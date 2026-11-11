<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure system.manage_employees permission exists
        $manageEmployees = Permission::firstOrCreate([
            'name' => 'system.manage_employees',
            'guard_name' => 'web',
        ]);

        // 2. Ensure view_unmasked_customer_data permission exists
        $viewUnmasked = Permission::firstOrCreate([
            'name' => 'view_unmasked_customer_data',
            'guard_name' => 'web',
        ]);

        // 3. Grant system.manage_employees to super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($manageEmployees);
            $superAdmin->givePermissionTo($viewUnmasked);
        }

        // 4. Grant view_unmasked_customer_data to salon_admin
        foreach (Role::where('name', 'salon_admin')->get() as $salonAdminRole) {
            $salonAdminRole->givePermissionTo($viewUnmasked);
        }

        // 5. Grant pos.manage_payment to pos_staff and staff
        $managePayment = Permission::where('name', 'pos.manage_payment')->where('guard_name', 'web')->first();
        if ($managePayment) {
            foreach (Role::whereIn('name', ['pos_staff', 'staff'])->get() as $role) {
                if (!$role->hasPermissionTo('pos.manage_payment')) {
                    $role->givePermissionTo($managePayment);
                }
            }
        }

        // 6. Clear Spatie cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
