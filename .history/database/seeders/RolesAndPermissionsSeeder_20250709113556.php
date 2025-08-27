<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Project Management
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            'archive projects',
            'restore projects',
            
            // Task Management
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'comment on tasks',
            'upload files to tasks',
            
            // User Management
            'view user access control',
            'manage user access',
            'create users',
            'edit users',
            'delete users',
            
            // Other Features
            'view dashboard',
            'view reports',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'administrative', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'managerial', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'archive projects', 'restore projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'comment on tasks', 'upload files to tasks',
            'view dashboard', 'view reports', 'manage settings'
        ]);

        // Combine viewer, editor, commentator permissions for staff
        $staffPermissions = [
            'view projects', 'create projects', 'edit projects',
            'view tasks', 'create tasks', 'edit tasks',
            'comment on tasks', 'upload files to tasks',
            'view dashboard'
        ];
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->givePermissionTo($staffPermissions);
    }
}
