<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAccessController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('userAccessControl', compact('users', 'roles', 'permissions'));
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles($request->role);
        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    public function removeRole(User $user, Role $role)
    {
        $user->removeRole($role);
        return redirect()->back()->with('success', 'Role removed successfully.');
    }

    public function assignPermission(Request $request, User $user)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name'
        ]);

        $user->givePermissionTo($request->permission);
        return redirect()->back()->with('success', 'Permission assigned successfully.');
    }

    public function removePermission(User $user, Permission $permission)
    {
        $user->revokePermissionTo($permission);
        return redirect()->back()->with('success', 'Permission removed successfully.');
    }
    public function updateRolePermissions(Request $request)
    {
        foreach ($request->input('permissions', []) as $roleId => $perms) {
            $role = \Spatie\Permission\Models\Role::find($roleId);
            $permissionIds = array_keys($perms);
            $role->syncPermissions($permissionIds);
        }
        return back()->with('success', 'Role permissions updated!');
    }
}