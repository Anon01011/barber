<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateEmployeePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Get the employee role
        $employeeRole = Role::where('name', 'employee')->firstOrFail();

        // Define the permissions to assign to employee (matches PermissionService template)
        $permissions = [
            'system.view_dashboard',
            'bookings.view',
            'appointments.view',
            'services.view',
            'pos.view',
            'pos.create_sale',
            'pos.manage_payment',
            'customers.view',
            'customers.create',
        ];

        // Sync the permissions with the employee role
        $employeeRole->syncPermissions($permissions);

        $this->command->info('Employee permissions updated successfully!');
    }
}
