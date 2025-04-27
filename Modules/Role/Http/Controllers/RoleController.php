<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Role\Entities\Role;
use Modules\Role\Entities\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $roles = Role::all();
        return view('role::roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
   
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('role::roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'module' => 'required|string',
        ]);

        $name             = $request['name'];
        $role             = new Role();
        $role->name       = $name;
        $role->guard_name ="tenant";
        $role->module     = $request['module'];
        $role->created_by = auth('tenant')->user()->id;
        $permissions      = $request['permissions'];
        $role->save();

        foreach($permissions as $permission)
        {
            $p    = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }
        return redirect()->route('tenant.role.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('role::roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('name', 'id')->toArray();

        return view('role::roles.edit', compact('role', 'permissions', 'rolePermissions'));
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
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);
        $input       = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role = Role::findOrFail($id);
        $role->fill($input)->save();
        $p_all = Permission::all();

        foreach($p_all as $p)
        {
            $role->revokePermissionTo($p);
        }

        foreach($permissions as $permission)
        {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }

        return redirect()->route('tenant.role.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('tenant.role.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
