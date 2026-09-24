<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the permission
        $permission = Permission::firstOrCreate([
            'name' => 'view_unmasked_customer_data',
            'guard_name' => 'web'
        ]);

        // Assign permission to salon_admin role for all salons
        $salonAdminRoles = Role::where('name', 'salon_admin')->get();
        
        foreach ($salonAdminRoles as $role) {
            $role->givePermissionTo($permission);
        }

        // Also assign to super_admin if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'view_unmasked_customer_data')->first();
        
        if ($permission) {
            // Remove permission from all roles
            $permission->roles()->detach();
            
            // Delete the permission
            $permission->delete();
        }
    }
};
