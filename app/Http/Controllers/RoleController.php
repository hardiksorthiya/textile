<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.create', compact('permissions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('roles.create')
            ->with('success', 'Role created successfully with permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        $roles = Role::with('permissions')->orderBy('name')->get();
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);

        // Sync permissions
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.create')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of Super Admin role
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.create')
                ->with('error', 'Cannot delete Super Admin role.');
        }

        $role->delete();

        return redirect()->route('roles.create')
            ->with('success', 'Role deleted successfully.');
    }
}
