<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddProductImportExportPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'products.import',
            'products.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign to Super Admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Assign to Salon Admins
        $salonAdmins = Role::where('name', 'salon_admin')->get();
        foreach ($salonAdmins as $role) {
            $role->givePermissionTo($permissions);
        }

        // Assign to Managers
        $managers = Role::where('name', 'manager')->get();
        foreach ($managers as $role) {
            $role->givePermissionTo($permissions);
        }
        
        $this->command->info('Product import/export permissions added successfully!');
    }
}
