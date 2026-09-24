<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create ONLY super_admin role globally (no salon_id)
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // Get all permissions from PermissionService
        $permissions = \App\Services\PermissionService::getFlatPermissions();

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign all permissions to super_admin
        $superAdmin->syncPermissions($permissions);

        // Assign super_admin role to the first user (if exists)
        $user = User::first();
        if ($user && !$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        // Note: Salon-specific roles (salon_admin, manager, employee, customer)
        // are now created per-salon via SalonRoleSeederService
    }
}