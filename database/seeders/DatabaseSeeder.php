<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Global Settings
            $this->call(SettingSeeder::class);
            Log::info('Global settings seeded successfully');

            // 2. Plans
            // $this->call(PlanSeeder::class);
            // Log::info('Plans seeded successfully');

            // 3. Global Roles and Permissions
            $this->call(RoleSeeder::class);
            Log::info('Global roles and permissions seeded successfully');

            // 4. Salon Creation (Demo Salon)
            // $this->call(SalonSeeder::class);
            // Log::info('Salon seeded successfully');

            // 5. Users (Super Admin and Salon Users)
            $this->call(UserSeeder::class);
            Log::info('Users seeded successfully');

            // 6. System Templates
            $this->call(SystemEmailTemplateSeeder::class);
            Log::info('System templates seeded successfully');

            // 7. Email Templates
            $this->call(EmailTemplateSeeder::class);
            Log::info('Templates seeded successfully');

            // 8. Salon Data (Employees, Services, Products, etc.)
            // $this->call(EmployeeSeeder::class);
            // Log::info('Employee seeded successfully');

            //$this->call(ServiceSeeder::class);
            //Log::info('Services seeded successfully');

            //$this->call(ProductSeeder::class);
            //Log::info('Products seeded successfully');

            //$this->call(BookingSeeder::class);
            //Log::info('Bookings seeded successfully');

            DB::commit();
            Log::info('Database seeding completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
