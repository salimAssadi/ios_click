<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Tenant\Entities\User;
use Modules\Role\Entities\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\Employee;
use Modules\Setting\Entities\Position;
use Modules\Setting\Entities\Department;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {   
        $employees = Employee::with(['user', 'user.roles', 'position', 'position.department'])->get();
        return view('role::users.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'tenant')->get();
        $positions = Position::with('department')->get();
        $departments = Department::all();
        return view('role::users.create', compact('roles', 'positions', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            // Get role type first
            $userType = 'employee'; // default type
            if ($request->has('roles')) {
                $role = Role::find($request->roles[0]); // Get first role
                if ($role) {
                    $userType = strtolower($role->name);
                }
            }

            // Create user first
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->status === 'active' ? 1 : 0,
                'phone_number' => $request->phone,
                'type' => $userType
            ]);

            // Assign roles if any
            if ($request->has('roles')) {
                $roleNames = Role::whereIn('id', $request->roles)
                    ->pluck('name')
                    ->toArray();
                $user->syncRoles($roleNames);
            }
           
            // Create employee and link to user
            $employee=   Employee::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);
            if ($request->has('signature_pad_data')&& !empty($request->signature_pad_data)) {
                $employee->update([
                    'signature_pad_data' => $request->signature_pad_data
                ]);
            }
            DB::commit();

            return redirect()->route('tenant.role.users.index')
                ->with('success', 'User and employee records created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user and employee: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        
        $employee = Employee::with(['user', 'user.roles', 'position', 'position.department', 'reportsTo'])->findOrFail($id);
        $user = $employee->user;
        return view('role::users.show', compact('user', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {   
        $employee = Employee::with(['user', 'user.roles', 'position', 'position.department', 'reportsTo'])->findOrFail($id);
        $roles = Role::where('guard_name', 'tenant')->get();
        $user = $employee->user ? $employee->user : null;
        if ($user) {
            $userRoles = $user->roles->pluck('id')->toArray();
        } else {
            $userRoles = [];
        }
        $positions = Position::with('department')->get();
        $departments = Department::all();
        return view('role::users.edit', compact('user', 'employee', 'roles', 'userRoles', 'positions', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Find the employee by ID
        $employee = Employee::findOrFail($id);
        
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($employee->user ? $employee->user->id : ''),
            'roles' => 'nullable|array',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ];
        
        // Validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            // Get role type first
            $userType = 'employee'; // default type
            if ($request->has('roles')) {
                $role = Role::find($request->roles[0]); // Get first role
                if ($role) {
                    $userType = strtolower($role->name);
                }
            }

            // Prepare user data
            $userData = [
                'email' => $request->email,
                'is_active' => $request->status === 'active' ? 1 : 0,
                'phone_number' => $request->phone,
                'type' => $userType
            ];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            // Get or create user
            if ($employee->user) {
                $user = $employee->user;
                $user->update($userData);
            } else {
                // Create new user if not exists
                $userData['password'] = $userData['password'] ?? Hash::make('password');
                $user = User::create($userData);
                
                // Link user to employee
                $employee->user_id = $user->id;
                $employee->save();
            }
            
            // Sync roles
            if ($request->has('roles')) {
                $roleNames = Role::whereIn('id', $request->roles)
                    ->pluck('name')
                    ->toArray();
                $user->syncRoles($roleNames);
            } else {
                $user->syncRoles([]);
            }
            
            // Update employee record
            $employee->update([
                'position_id' => $request->position_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);
            
            if ($request->has('signature_pad_data') && !empty($request->signature_pad_data)) {
                $employee->update([
                    'signature_pad_data' => $request->signature_pad_data
                ]);
            }
            DB::commit();
            
            return redirect()->route('tenant.role.users.index')
                ->with('success', 'User and employee records updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating user and employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Delete associated employee record
            Employee::where('user_id', $id)->delete();
            
            // Delete user
            $user->delete();
            
            DB::commit();
            
            return redirect()->route('tenant.role.users.index')
                ->with('success', 'User and employee records deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting user and employee: ' . $e->getMessage());
        }
    }
}
