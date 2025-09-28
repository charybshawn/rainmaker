<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Check if user has admin permission
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        return Inertia::render('Admin/Dashboard', [
            'users_count' => User::count(),
            'roles_count' => Role::count(),
        ]);
    }

    public function users()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::render('Admin/Users', [
            'users' => User::with('roles')->get(),
            'roles' => Role::with('permissions')->get(),
        ]);
    }

    public function roles()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage roles')) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::render('Admin/Roles', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
        ]);
    }

    public function permissions()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage permissions')) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::render('Admin/Permissions', [
            'permissions' => Permission::with('roles')->get(),
        ]);
    }

    // User management
    public function updateUserRoles(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->back()->with('success', 'User roles updated successfully.');
    }


    // Role management
    public function storeRole(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage roles')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, Role $role)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage roles')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function deleteRole(Role $role)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage roles')) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return redirect()->back()->withErrors(['error' => 'Cannot delete admin role.']);
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    // Permission management
    public function storePermission(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage permissions')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return redirect()->back()->with('success', 'Permission created successfully.');
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage permissions')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    public function deletePermission(Permission $permission)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage permissions')) {
            abort(403, 'Unauthorized access.');
        }

        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}