<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Salon;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SingleSalonSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            $slug = env('SINGLE_SALON_SLUG', 'intranet');

            // 1. Create Lifetime License Plan
            $plan = Plan::updateOrCreate(
                ['slug' => 'lifetime-license'],
                [
                    'name' => 'Intranet License',
                    'price' => 0.00,
                    'duration_in_days' => 36500, // 100 years
                    'trial_days' => 0,
                    'description' => 'Lifetime license for single salon intranet',
                    'features' => [
                        'All Premium Features',
                        'Unlimited Users',
                        'Unlimited Branches',
                        'Unlimited Customers',
                        'Unlimited Services',
                        'Unlimited Products',
                        'Priority Support'
                    ],
                    'limits' => [
                        'max_users' => -1,
                        'max_branches' => -1,
                        'max_customers' => -1,
                        'max_services' => -1,
                        'max_products' => -1,
                        'max_packages' => -1,
                        'max_memberships' => -1,
                        'max_bookings_per_month' => -1,
                        'max_guest_bookings_per_month' => -1
                    ],
                    'max_users' => -1,
                    'max_branches' => -1,
                    'is_active' => true,
                    'is_popular' => true,
                    'sort_order' => 0
                ]
            );
            Log::info('Lifetime License plan created');

            // 2. Create Single Salon
            $salon = Salon::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => 'Intranet Salon',
                    'email' => 'admin@intranet.local',
                    'phone' => '0000000000',
                    'address' => 'Local Intranet',
                    'city' => 'Local',
                    'state' => 'Local',
                    'country' => 'Local',
                    'zip_code' => '00000',
                    'status' => true,
                ]
            );
            Log::info('Single Salon created: ' . $salon->name);

            // 3. Create Subscription
            if (!$salon->subscription) {
                Subscription::create([
                    'salon_id' => $salon->id,
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_in_days),
                    'status' => 'active',
                ]);
                Log::info('Subscription created for single salon');
            }

            // 4. Seed Salon Roles
            \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id);
            Log::info('Salon roles seeded');

            // 5. Create Admin User
            $adminEmail = 'admin@intranet.local';
            $user = User::where('email', $adminEmail)->first();

            if (!$user) {
                $user = User::create([
                    'name' => 'Intranet Admin',
                    'email' => $adminEmail,
                    'password' => Hash::make('password'),
                    'salon_id' => $salon->id,
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update(['salon_id' => $salon->id]);
            }

            // Assign salon_admin role
            // We need to find the role specifically for this salon
            $role = \Spatie\Permission\Models\Role::where('name', 'salon_admin')
                ->where('salon_id', $salon->id)
                ->first();

            if ($role) {
                $user->assignRole($role);
                Log::info('Admin role assigned to user');
            } else {
                Log::error('Salon admin role not found for salon ' . $salon->id);
            }

            DB::commit();
            Log::info('Single Salon Seeder completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Single Salon Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
