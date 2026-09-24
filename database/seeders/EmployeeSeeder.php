<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Find the demo salon
            $salon = Salon::where('slug', 'demo-salon')->first();
            if (!$salon) {
                Log::warning('Demo salon not found. Skipping employee seeding.');
                return;
            }

            // Get or create employee user
            $user = User::where('email', 'employee@salonpro.com')->first();
            if (!$user) {
                Log::warning('Employee user not found in UserSeeder. Skipping employee record creation.');
                return;
            }

            // Create employee record
            Employee::updateOrCreate(
                ['email' => 'employee@salonpro.com'],
                [
                    'name' => 'Employee User',
                    'phone' => '4567890123',
                    'position' => 'Stylist',
                    'status' => 'active',
                    'salon_id' => $salon->id,
                    'user_id' => $user->id,
                ]
            );

            Log::info('Employee seeded successfully for demo salon');
        } catch (\Exception $e) {
            Log::error('Error seeding employee: ' . $e->getMessage());
            throw $e;
        }
    }
}