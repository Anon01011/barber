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

        // Define the permissions to assign to employee
        $permissions = [
            'bookings.view',
            'bookings.create',
            'bookings.edit',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'customers.view',
            'services.view',
            'staff.view',
            'system.view_dashboard',
        ];

        // Sync the permissions with the employee role
        $employeeRole->syncPermissions($permissions);

        $this->command->info('Employee permissions updated successfully!');
    }
}
