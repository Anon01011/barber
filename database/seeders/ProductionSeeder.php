<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Create global roles and permissions
            $this->call(RoleSeeder::class);
            Log::info('Global roles and permissions seeded successfully');

            // 2. Create plans
            $this->call(PlanSeeder::class);
            Log::info('Plans seeded successfully');

            // 3. Create System Email Templates
            $this->call(SystemEmailTemplateSeeder::class);
            Log::info('System email templates seeded successfully');

            // 4. Create Super Admin User
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@salonpro.com'],
                [
                    'name' => 'Super Admin',
                    'password' => bcrypt('admin123'), // Should be changed immediately
                    'phone' => '1234567890',
                ]
            );

            if (!$superAdmin->hasRole('super_admin')) {
                $superAdmin->assignRole('super_admin');
            }
            Log::info('Super Admin user created successfully');

            DB::commit();
            Log::info('Production seeding completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
