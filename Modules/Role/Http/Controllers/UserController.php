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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $users = User::with(['roles', 'employee', 'employee.position', 'employee.position.department'])->get();
        return view('role::users.index', compact('users'));
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

        // Split name into first_name and last_name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        DB::beginTransaction();
        
        try {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->status === 'active' ? 1 : 0,
                'phone_number' => $request->phone,
            ]);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // Create employee record
            Employee::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);

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
        $user = User::with(['roles', 'roles.permissions'])->findOrFail($id);
        $employee = Employee::with(['position', 'position.department', 'reportsTo'])->where('user_id', $id)->first();
        
        return view('role::users.show', compact('user', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('guard_name', 'tenant')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $positions = Position::with('department')->get();
        $departments = Department::all();
        $employee = Employee::where('user_id', $id)->first();
        
        return view('role::users.edit', compact('user', 'roles', 'userRoles', 'positions', 'departments', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'roles' => 'nullable|array',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }
        
        $request->validate($rules);
        
        // Split name into first_name and last_name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        
        DB::beginTransaction();
        
        try {
            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'is_active' => $request->status === 'active' ? 1 : 0,
                'phone_number' => $request->phone,
            ];
            
            // Only update password if it's provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $user->update($userData);
            
            // Sync roles
            if ($request->has('roles')) {
                $roleNames = Role::whereIn('id', $request->roles)
                ->pluck('name')
                ->toArray();
        
            $user->syncRoles($roleNames);
            } else {
                $user->syncRoles([]);
            }
            
            // Update or create employee record
            $employee = Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'position_id' => $request->position_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'status' => $request->status,
                ]
            );
            
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





