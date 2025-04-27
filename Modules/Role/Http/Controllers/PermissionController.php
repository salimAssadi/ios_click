<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Role\Entities\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('role::permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
       
        $modules = Permission::select('module')->distinct()->pluck('module')->filter()->all();
        return view('role::permissions.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'guard_name' => 'nullable|string|max:255',
            'module' => 'required|string|max:255',
        ]);
    
        Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
            'module' => 'tenant',
        ]);
    
        if ($request->has('add_new')) {
            return redirect()->route('tenant.role.permissions.create')
                ->with('success', 'Permission created successfully. You can add another one.');
        }
    
        return redirect()->route('tenant.role.permissions.index')
            ->with('success', 'Permission created successfully.');
    }
    

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return view('role::permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $modules = Permission::select('module')->distinct()->pluck('module')->filter()->all();
        return view('role::permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string|max:255',
            'module' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
            'module' => $request->module,
        ]);

       

        return redirect()->route('tenant.role.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('tenant.role.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
