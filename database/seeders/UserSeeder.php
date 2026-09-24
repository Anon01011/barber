<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Super Admin (global, no salon required)
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@fstqatar.com'],
                [
                    'name' => 'Super Admin',
                    'password' => bcrypt('admin123'),
                    'phone' => '1234567890',
                ]
            );
            if (!$superAdmin->hasRole('super_admin')) {
                $superAdmin->assignRole('super_admin');
            }
            Log::info('Super Admin user created/updated');

            // Salon Users for demo-salon
            $salon = \App\Models\Salon::where('slug', 'demo-salon')->first();
            if ($salon) {
                // Salon Admin
                $salonAdmin = User::updateOrCreate(
                    ['email' => 'salon@fstqatar.com'],
                    [
                        'name' => 'Salon Admin',
                        'password' => bcrypt('salon123'),
                        'phone' => '2345678901',
                        'salon_id' => $salon->id,
                    ]
                );
                $salonAdminRole = \App\Models\Role::where('name', 'salon_admin')->where('salon_id', $salon->id)->first();
                if ($salonAdminRole)
                    $salonAdmin->syncRoles([$salonAdminRole]);

                // Salon Manager
                $salonManager = User::updateOrCreate(
                    ['email' => 'manager@fstqatar.com'],
                    [
                        'name' => 'Salon Manager',
                        'password' => bcrypt('manager123'),
                        'phone' => '3456789012',
                        'salon_id' => $salon->id,
                    ]
                );
                $managerRole = \App\Models\Role::where('name', 'manager')->where('salon_id', $salon->id)->first();
                if ($managerRole)
                    $salonManager->syncRoles([$managerRole]);

                // Salon Employee (User record for employee)
                $employeeUser = User::updateOrCreate(
                    ['email' => 'employee@salonpro.com'],
                    [
                        'name' => 'Employee User',
                        'password' => bcrypt('employee123'),
                        'phone' => '4567890123',
                        'salon_id' => $salon->id,
                    ]
                );
                $employeeRole = \App\Models\Role::where('name', 'employee')->where('salon_id', $salon->id)->first();
                if ($employeeRole)
                    $employeeUser->syncRoles([$employeeRole]);

                // Salon Customer
                $customerUser = User::updateOrCreate(
                    ['email' => 'customer@fstqatar.com'],
                    [
                        'name' => 'Sample Customer',
                        'password' => bcrypt('customer123'),
                        'phone' => '5678901234',
                        'salon_id' => $salon->id,
                    ]
                );
                $customerRole = \App\Models\Role::where('name', 'customer')->where('salon_id', $salon->id)->first();
                if ($customerRole)
                    $customerUser->syncRoles([$customerRole]);

                // Ensure Customer profile exists
                \App\Models\Customer::firstOrCreate(
                    ['user_id' => $customerUser->id],
                    [
                        'salon_id' => $salon->id,
                        'first_name' => 'Sample',
                        'last_name' => 'Customer',
                        'email' => $customerUser->email,
                        'phone' => $customerUser->phone,
                    ]
                );

                Log::info('Salon users created/updated for demo salon');
            } else {
                Log::info('No demo salon found. Skipping salon-specific users.');
            }

            Log::info('Users seeded successfully');
        } catch (\Exception $e) {
            Log::error('Error seeding users: ' . $e->getMessage());
            throw $e;
        }
    }
}