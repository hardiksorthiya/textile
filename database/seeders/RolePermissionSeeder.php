<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            
            // Reports
            'view reports',
            'export reports',
            
            // Settings
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except role management
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view reports',
            'export reports',
            'view settings',
            'edit settings',
        ]);

        // Manager - View and edit permissions
        $manager = Role::create(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view users',
            'view reports',
            'export reports',
        ]);

        // Staff - Limited permissions
        $staff = Role::create(['name' => 'Staff']);
        $staff->givePermissionTo([
            // No specific permissions for Staff
        ]);

        // User - Basic permissions
        $user = Role::create(['name' => 'User']);
        $user->givePermissionTo([
            // No specific permissions for User
        ]);
    }
}
