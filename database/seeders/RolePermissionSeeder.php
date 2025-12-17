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
            
            // Contract Approval
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            
            // Customer Management
            'view customers',
            'delete customers',
            
            // Lead Management
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'convert contract',
            
            // Proforma Invoice Management
            'view proforma invoices',
            'create proforma invoices',
            'edit proforma invoices',
            'delete proforma invoices',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except role management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view reports',
            'export reports',
            'view settings',
            'edit settings',
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            'view customers',
        ]);

        // Manager - View and edit permissions
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view users',
            'view reports',
            'export reports',
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            'view customers',
        ]);

        // Staff - Limited permissions
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        // No specific permissions for Staff

        // User - Basic permissions
        $user = Role::firstOrCreate(['name' => 'User']);
        // No specific permissions for User
        
        // Department Lead Manager - Role for department users with lead management permissions
        $departmentLeadManager = Role::firstOrCreate(['name' => 'Department Lead Manager']);
        $departmentLeadManager->givePermissionTo([
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'convert contract',
            'view contract approvals', // Allow viewing contracts they created
            'view customers', // Allow viewing customers from approved contracts
        ]);
    }
}
