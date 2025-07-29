<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index() {

        return Inertia::render('Admin/Roles/Index' , [
            'roles' => Role::with("permissions")->get(),
            'permissions' => Permission::pluck('name')->all(),
        ]);

    }


    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return Inertia::render('Admin/Roles/Show', [
            'role' => $role,
        ]);
    }


      public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->back()->with('success', 'Role created successfully.');
    }


    public function update(Request $request, Role $role)
    {
        // Validate input
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        // Update role name
        $role->update([
            'name' => $data['name'],
        ]);

        // Update permissions (sync)
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        } else {
            // If permissions array is empty, remove all permissions
            $role->syncPermissions([]);
        }

        return redirect()->back()->with('success', 'Role updated successfully.');
    }


    
     public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->back();
    }

    public function selectDelete(Request $request)
    {
        Role::whereIn('id', $request->input('role_ids', []))->delete();
        return redirect()->back();
    }

}
