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
        $this->middleware('permission:create users')->only(['register']);
    }
    
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $pendingUsers = User::where('is_approved', false)->orderBy('created_at')->get();

        // Explicitly order roles
        $roles = Role::whereIn('name', [
            'administrative',
            'managerial',
            'staff'
        ])->orderByRaw("FIELD(name, 'administrative', 'managerial', 'staff')")->get();

        // Permission groups
        $permissionGroups = [
            'Project Management' => [
                'view activities',
                'create activities',
                'edit activities',
                'delete activities',
                'archive activities',
                'restore activities',
            ],
            'Task Management' => [
                'create tasks',
                'view tasks',
                'edit tasks',
                'delete tasks',
                'comment on tasks',
                'upload files to tasks',
            ],
            'User Management' => [
                'view user access control',
                'manage user access',
                'create users',
                'edit users',
                'delete users',
            ],
        ];

        // Flatten permission order for query
        $permissionOrder = array_merge(
            $permissionGroups['Project Management'],
            $permissionGroups['Task Management'],
            $permissionGroups['User Management']
        );

        $permissions = Permission::whereIn('name', $permissionOrder)
            ->orderByRaw("FIELD(name, '".implode("','", $permissionOrder)."')")
            ->get();

        return view('userAccessControl', compact('users', 'roles', 'permissions', 'permissionGroups', 'pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->save();
        // Optionally, notify the user here
        return back()->with('success', 'User approved successfully!');
    }
    public function reject(User $user)
    {
        $user->delete();
        return back()->with('success', 'User rejected and removed.');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'position' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'],
            'password' => bcrypt($validated['password']),
        ]);

        // Assign role based on position
        switch ($validated['position']) {
            case 'City Engineer':
            case 'Assistant City Engineer':
            case 'Supervising Administrative Officer':
            case 'Division Head':
                $user->assignRole('administrative');
                break;
            case 'Group Leaders':
                $user->assignRole('managerial');
                break;
            case 'Technical Personnel':
                $user->assignRole('staff');
                break;
            default:
                $user->assignRole('staff');
                break;
        }

        return redirect()->route('userAccessControl')->with('success', 'User registered successfully!');
    }

    public function assignRole(Request $request, User $user)
    {
        // Prevent administrative users from changing their own role
        if ($user->hasRole('administrative')) {
            return redirect()->back()->with([
                'error' => 'Administrative users cannot change their own role.',
                'role_change_attempt' => true,
                'user_name' => $user->name,
            ]);
        }

        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles($request->role);
        return redirect()->back()->with([
            'success' => 'Role assigned successfully.',
            'role_changed' => true,
            'user_name' => $user->name,
            'new_role' => $request->role,
        ]);
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

        // Automatically sync role based on position
        $positionRoleMap = [
            'City Engineer' => 'administrative',
            'Assistant City Engineer' => 'administrative',
            'Supervising Administrative Officer' => 'administrative',
            'Division Head' => 'administrative',
            'Group Leaders' => 'managerial',
            'Technical Personnel' => 'staff',
            'Engineering Assistant' => 'staff',
        ];

        if (isset($positionRoleMap[$request->position])) {
            $user->syncRoles($positionRoleMap[$request->position]);
        }

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
                    'view activities', 'create activities', 'edit activities', 'delete activities',
                    'archive activities', 'restore activities',
                    'view tasks','create tasks', 'edit tasks', 'delete tasks',
                    'comment on tasks', 'upload files to tasks',
                ],
                'staff' => [
                    'view activities',
                    'view tasks','create tasks', 'edit tasks',
                    'comment on tasks', 'upload files to tasks',
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