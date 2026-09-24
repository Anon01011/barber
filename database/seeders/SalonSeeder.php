<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salon;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SalonRoleSeederService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SalonSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Ensure a plan exists (Standard Plan as default for demo)
            $plan = Plan::where('slug', 'standard-plan')->first();
            if (!$plan) {
                $this->call(PlanSeeder::class);
                $plan = Plan::where('slug', 'standard-plan')->first();
            }

            // 2. Create Demo Salon
            $salon = Salon::updateOrCreate(
                ['slug' => 'demo-salon'],
                [
                    'name' => 'Demo Salon',
                    'email' => 'demo@salonpro.com',
                    'phone' => '1234567890',
                    'address' => '456 Demo Street, City, Country',
                    'city' => 'Demo City',
                    'state' => 'Demo State',
                    'country' => 'Demo Country',
                    'zip_code' => '12345',
                    'status' => true,
                ]
            );
            Log::info('Demo Salon created/updated: ' . $salon->name);

            // 3. Create/Update Subscription
            Subscription::updateOrCreate(
                ['salon_id' => $salon->id],
                [
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays(30),
                    'status' => 'active',
                ]
            );
            Log::info('Subscription updated for demo salon');

            // 4. Seed Salon Roles
            SalonRoleSeederService::seedDefaultRoles($salon->id);
            Log::info('Salon roles seeded for demo salon');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalonSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
