<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Revoke staff.view and staff.view_performance from all employee roles
        $employeeRoles = Role::where('name', 'employee')->get();
        $permissionsToRemove = ['staff.view', 'staff.view_performance', 'staff.create', 'staff.edit', 'staff.delete', 'staff.manage_schedule'];

        foreach ($employeeRoles as $role) {
            foreach ($permissionsToRemove as $permissionName) {
                if ($role->hasPermissionTo($permissionName)) {
                    $role->revokePermissionTo($permissionName);
                }
            }
        }

        // Clear Spatie permissions cache
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
