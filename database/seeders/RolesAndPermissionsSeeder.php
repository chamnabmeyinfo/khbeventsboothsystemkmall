<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Booths Module
            ['name' => 'View Booths', 'slug' => 'booths.view', 'module' => 'booths', 'description' => 'View booth listings'],
            ['name' => 'Create Booths', 'slug' => 'booths.create', 'module' => 'booths', 'description' => 'Create new booths'],
            ['name' => 'Edit Booths', 'slug' => 'booths.edit', 'module' => 'booths', 'description' => 'Edit existing booths'],
            ['name' => 'Delete Booths', 'slug' => 'booths.delete', 'module' => 'booths', 'description' => 'Delete booths'],
            ['name' => 'Manage Floor Plan', 'slug' => 'booths.floor-plan', 'module' => 'booths', 'description' => 'Manage floor plan layout'],
            
            // Clients Module
            ['name' => 'View Clients', 'slug' => 'clients.view', 'module' => 'clients', 'description' => 'View client listings'],
            ['name' => 'Create Clients', 'slug' => 'clients.create', 'module' => 'clients', 'description' => 'Create new clients'],
            ['name' => 'Edit Clients', 'slug' => 'clients.edit', 'module' => 'clients', 'description' => 'Edit existing clients'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'module' => 'clients', 'description' => 'Delete clients'],
            
            // Bookings Module
            ['name' => 'View Bookings', 'slug' => 'bookings.view', 'module' => 'bookings', 'description' => 'View booking listings'],
            ['name' => 'Create Bookings', 'slug' => 'bookings.create', 'module' => 'bookings', 'description' => 'Create new bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'bookings.edit', 'module' => 'bookings', 'description' => 'Edit existing bookings'],
            ['name' => 'Delete Bookings', 'slug' => 'bookings.delete', 'module' => 'bookings', 'description' => 'Delete bookings'],
            ['name' => 'Confirm Bookings', 'slug' => 'bookings.confirm', 'module' => 'bookings', 'description' => 'Confirm bookings'],
            
            // Payments Module
            ['name' => 'View Payments', 'slug' => 'payments.view', 'module' => 'payments', 'description' => 'View payment records'],
            ['name' => 'Create Payments', 'slug' => 'payments.create', 'module' => 'payments', 'description' => 'Record payments'],
            ['name' => 'Edit Payments', 'slug' => 'payments.edit', 'module' => 'payments', 'description' => 'Edit payment records'],
            ['name' => 'View Invoices', 'slug' => 'payments.invoice', 'module' => 'payments', 'description' => 'View and print invoices'],
            
            // Reports Module
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'View reports and analytics'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Export reports'],
            
            // Users Module
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'View user listings'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Delete users'],
            
            // Roles & Permissions Module
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'View role listings'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'roles', 'description' => 'Create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'roles', 'description' => 'Edit existing roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'roles', 'description' => 'Delete roles'],
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'module' => 'roles', 'description' => 'View permission listings'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'roles', 'description' => 'Manage permissions'],
            
            // Categories Module
            ['name' => 'View Categories', 'slug' => 'categories.view', 'module' => 'categories', 'description' => 'View category listings'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'module' => 'categories', 'description' => 'Manage categories'],
            
            // Settings Module
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings', 'description' => 'Manage system settings'],
            
            // Communications Module
            ['name' => 'View Messages', 'slug' => 'communications.view', 'module' => 'communications', 'description' => 'View messages'],
            ['name' => 'Send Messages', 'slug' => 'communications.send', 'module' => 'communications', 'description' => 'Send messages'],
            
            // Export/Import Module
            ['name' => 'Export Data', 'slug' => 'export.data', 'module' => 'export', 'description' => 'Export data'],
            ['name' => 'Import Data', 'slug' => 'import.data', 'module' => 'export', 'description' => 'Import data'],
        ];

        foreach ($permissions as $index => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'module' => $permission['module'],
                'description' => $permission['description'],
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        // Create Roles
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'description' => 'Full system access',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $salesManagerRole = Role::create([
            'name' => 'Sales Manager',
            'slug' => 'sales-manager',
            'description' => 'Manage sales team and bookings',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $salesStaffRole = Role::create([
            'name' => 'Sales Staff',
            'slug' => 'sales-staff',
            'description' => 'Basic sales operations',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Assign all permissions to Administrator
        $adminRole->assignPermissions(Permission::pluck('id')->toArray());

        // Assign permissions to Sales Manager
        $salesManagerPermissions = Permission::whereIn('slug', [
            'booths.view', 'booths.create', 'booths.edit', 'booths.floor-plan',
            'clients.view', 'clients.create', 'clients.edit',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.confirm',
            'payments.view', 'payments.create', 'payments.invoice',
            'reports.view', 'reports.export',
            'communications.view', 'communications.send',
            'export.data',
        ])->pluck('id')->toArray();
        $salesManagerRole->assignPermissions($salesManagerPermissions);

        // Assign permissions to Sales Staff
        $salesStaffPermissions = Permission::whereIn('slug', [
            'booths.view',
            'clients.view', 'clients.create', 'clients.edit',
            'bookings.view', 'bookings.create',
            'payments.view', 'payments.create',
            'communications.view', 'communications.send',
        ])->pluck('id')->toArray();
        $salesStaffRole->assignPermissions($salesStaffPermissions);
    }
}
