<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // System
            'system.view_dashboard',
            'system.manage_roles',
            'system.manage_settings',

            // Appointments
            'bookings.view',
            'bookings.create',
            'bookings.edit',
            'bookings.delete',
            'bookings.view_stats',
            'bookings.manage_guest',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',

            // Branches
            'salon.manage_branches',

            // Staff
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'staff.manage_schedule',
            'staff.view_performance',

            // Services
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'services.manage_categories',
            'packages.view',
            'packages.create',
            'packages.edit',
            'packages.delete',
            'memberships.view',
            'memberships.create',
            'memberships.edit',
            'memberships.delete',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.view_history',

            // POS
            'pos.view',
            'pos.create_sale',
            'pos.edit_sale',
            'pos.delete_sale',
            'pos.process_refunds',
            'pos.view_reports',
            'pos.manage_payment',
            'pos.manage_discounts',

            // Inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.manage_stock',
            'inventory.manage_suppliers',
            'inventory.view_reports',
            'inventory.manage_categories',
            'inventory.categories.view',
            'inventory.categories.create',
            'inventory.categories.edit',
            'inventory.categories.delete',
            'products.import',
            'products.export',

            // Reports
            'reports.view',
            'reports.sales',
            'reports.appointments',
            'reports.staff',
            'reports.customers',

            // Salon
            'salon.view',
            'salon.edit',
            'salon.manage_settings',
            'salon.manage_working_hours',
            'salon.manage_holidays',

            // Commissions
            'commissions.view',
            'commissions.manage',
            'commissions.view_reports',

            // Email
            'salon.manage_email_templates',
            'salon.manage_email_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to delete permissions in down() as they might be used by other things
        // But strictly speaking, a down method should reverse the up method.
        // For safety in production, we usually leave this empty or comment it out.
    }
};
