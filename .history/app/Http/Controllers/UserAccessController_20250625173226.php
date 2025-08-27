<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view user access control')->only(['index']);
        $this->middleware('permission:manage user access')->only(['assignRole', 'removeRole', 'updateRolePermissions']);
        $this->middleware('permission:create users')->only(['assignPermission']);
        $this->middleware('permission:delete users')->only(['removePermission']);
    }
    
    public function index()
    {
        $users = User::with('roles')->paginate(10);
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

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'position' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'position']);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('userAccessControl')->with('success', 'User updated successfully.');
    }

    public function updateRolePermissions(Request $request)
    {
        if ($request->has('restore_default')) {
            // Restore default permissions for each role
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            $defaults = [
                'administrative' => Permission::all()->pluck('name')->toArray(),
                'managerial' => [
                    'view projects', 'create projects', 'edit projects', 'delete projects', 'archive projects', 'restore projects',
                    'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
                    'comment on tasks', 'upload files to tasks',
                    'view dashboard', 'view reports', 'manage settings'
                ],
                'viewer' => [
                    'view projects', 'view tasks', 'comment on tasks', 'upload files to tasks', 'view dashboard'
                ],
                'editor' => [
                    'view projects', 'create projects', 'edit projects',
                    'view tasks', 'create tasks', 'edit tasks',
                    'comment on tasks', 'upload files to tasks', 'view dashboard'
                ],
                'commentator' => [
                    'view projects', 'view tasks', 'comment on tasks', 'upload files to tasks', 'view dashboard'
                ],
            ];

            foreach ($defaults as $roleName => $permissions) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $role->syncPermissions($permissions);
                }
            }
            return back()->with('success', 'Role permissions restored to default!');
        }

        // Otherwise, update permissions as checked in the matrix
        foreach ($request->input('permissions', []) as $roleId => $perms) {
            $role = Role::find($roleId);
            if ($role) {
                $permissionIds = array_keys($perms);
                $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            }
        }
        return back()->with('success', 'Role permissions updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('userAccessControl')->with('success', 'User deleted successfully.');
    }
}