<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User; // Ensure this model exists
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LargeScaleSalonSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Configuration
        $totalSalons = 1000;
        $staffPerSalon = 2000;
        $customersPerSalon = 50000; // 10% Registered (User), 90% Guest
        $bookingsPerSalon = 50000;   // Mix of Past/Future

        $faker = fake();
        $password = Hash::make('password'); // Pre-hash for performance
        $now = now();

        $this->command->info("Starting Large Scale Authentic Seeder...");
        $this->command->info("Salons: $totalSalons | Staff: $staffPerSalon | Customers: $customersPerSalon | Bookings: $bookingsPerSalon");

        // 2. Ensure Roles Exist (for authenticity)
        $roles = ['super_admin', 'salon_owner', 'employee', 'customer'];
        foreach ($roles as $roleName) {
            // We use DB check purely for speed/safety, in valid app relying on Role::findOrCreate is better
            // Ideally we assume roles table exists and is populated or we populate it.
            // Let's create 'web' guard roles globally if they don't exist, linked to no salon (global) or per salon?
            // Salon Owner is per salon usually (or global role name?). Spatie roles can be scoped or global.
            // For simplicity in this stress test, we'll assume global role names or create them per salon if scoped.
            // Usually 'employee' role is global definition, applied to user.
        }

        // 3. Create Plans (Basic, Pro, Enterprise)
        $planIds = [];
        $plans = [
            ['name' => 'Basic', 'price' => 29.99, 'max_users' => 500, 'max_branches' => 1],
            ['name' => 'Pro', 'price' => 79.99, 'max_users' => 2000, 'max_branches' => 5],
            ['name' => 'Enterprise', 'price' => 199.99, 'max_users' => -1, 'max_branches' => -1],
        ];

        foreach ($plans as $plan) {
            $slug = Str::slug($plan['name']);
            // Upsert Plan
            $exists = DB::table('plans')->where('slug', $slug)->first();
            if (!$exists) {
                $planIds[] = DB::table('plans')->insertGetId(array_merge($plan, [
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]));
            } else {
                $planIds[] = $exists->id;
            }
        }

        // 4. Batch Operations
        DB::disableQueryLog(); // Memory optimization

        for ($s = 1; $s <= $totalSalons; $s++) {
            $salonName = $faker->company . ' Salon ' . $s;
            $salonSlug = Str::slug($salonName) . '-' . uniqid();

            // A. Create Salon
            $salonId = DB::table('salons')->insertGetId([
                'name' => $salonName,
                'slug' => $salonSlug,
                'email' => "salon{$s}." . uniqid() . "@example.com", // Unique email
                'phone' => $faker->phoneNumber,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // B. Create Owner User
            $ownerEmail = "owner{$s}@" . Str::slug($faker->domainName);
            $ownerId = DB::table('users')->insertGetId([
                'salon_id' => $salonId,
                'name' => $faker->name,
                'email' => $ownerEmail,
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Assign Owner Role (Spatie)
            // Assuming role 'salon_owner' id is constant or found. Let's find/create dynamically for safety without model overhead
            $ownerRoleId = DB::table('roles')->where('name', 'salon_owner')->value('id');
            if (!$ownerRoleId) {
                $ownerRoleId = DB::table('roles')->insertGetId(['name' => 'salon_owner', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
            }
            DB::table('model_has_roles')->insert([
                'role_id' => $ownerRoleId,
                'model_type' => 'App\Models\User',
                'model_id' => $ownerId
            ]);

            // Assign Owner to Salon
            DB::table('salons')->where('id', $salonId)->update(['owner_id' => $ownerId]);

            $planId = $faker->randomElement($planIds);
            $subId = DB::table('subscriptions')->insertGetId([
                'salon_id' => $salonId,
                'plan_id' => $planId,
                'starts_at' => $now->copy()->subDays(rand(1, 365)),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Create SaaS Payment for the subscription
            $planPrice = 0;
            foreach ($plans as $p) {
                if ($planId == DB::table('plans')->where('slug', Str::slug($p['name']))->value('id')) {
                    // Wait, we can't reliably match by ID easily without fetching.
                    // Better: fetch price from DB or use array.
                    // Since we have $planId, let's fetch current price.
                    $planPrice = $p['price'];
                    break;
                }
            }
            // Actually, querying DB for price is safer if ID mismatch in array.
            $planPrice = DB::table('plans')->where('id', $planId)->value('price');

            if ($planPrice > 0) {
                DB::table('saas_payments')->insert([
                    'salon_id' => $salonId,
                    'subscription_id' => $subId,
                    'amount' => $planPrice,
                    'currency' => 'USD',
                    'payment_method' => 'card',
                    'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                    'status' => 'completed',
                    'paid_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }

            // D. Services (10 Categories, 20 Services)
            $serviceIds = [];
            for ($c = 1; $c <= 1000; $c++) {
                $catId = DB::table('service_categories')->insertGetId([
                    'salon_id' => $salonId,
                    'name' => "Category $c",
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                for ($svc = 1; $svc <= 2; $svc++) {
                    $price = rand(2000, 20000);
                    $serviceIds[] = DB::table('services')->insertGetId([
                        'salon_id' => $salonId,
                        'category_id' => $catId,
                        'name' => "Service $c-$svc",
                        'price' => $price,
                        'duration' => 60,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }

            // E. Staff (100) -> User + Employee + Role
            $staffUserIds = [];
            $staffData = [];
            $employeeData = [];
            $roleData = [];

            $employeeRoleId = DB::table('roles')->where('name', 'employee')->value('id');
            if (!$employeeRoleId) {
                $employeeRoleId = DB::table('roles')->insertGetId(['name' => 'employee', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
            }

            for ($st = 0; $st < $staffPerSalon; $st++) {
                // We need ID for relation, so insert get ID or batch?
                // Batch insert users logic:
                // Since we need IDs to link expected employees and roles, batch insert doesn't return IDs easily in generic SQL helpers.
                // For 100 rows, individual loop is perfectly fine (100 ints ok).
                $uId = DB::table('users')->insertGetId([
                    'salon_id' => $salonId,
                    'name' => $faker->name,
                    'email' => "staff{$s}_{$st}_" . uniqid() . "@salon.com",
                    'password' => $password,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $staffUserIds[] = $uId;

                $employeeData[] = [
                    'user_id' => $uId,
                    'salon_id' => $salonId,
                    'name' => "Staff $st",
                    'email' => "staff{$s}_{$st}@salon.com", // Keeping sync with user email is good practice
                    'position' => 'Senior Stylist',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $roleData[] = [
                    'role_id' => $employeeRoleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $uId
                ];
            }
            DB::table('employees')->insert($employeeData);
            DB::table('model_has_roles')->insert($roleData);

            // F. Customers (1000) -> 100 Reg (User+Cust), 900 Guest (Cust only)
            $customerIds = []; // IDs from 'customers' table
            $registeredCustomerUserIds = []; // mapping customer_id => user_id

            $customerRoleId = DB::table('roles')->where('name', 'customer')->value('id');
            if (!$customerRoleId) {
                $customerRoleId = DB::table('roles')->insertGetId(['name' => 'customer', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
            }

            $custRoles = [];

            // Loop 1000
            $batchCustomers = [];
            for ($cu = 0; $cu < $customersPerSalon; $cu++) {
                $isRegistered = ($cu < 1000); // First 100 are registered
                $userId = null;

                if ($isRegistered) {
                    $userId = DB::table('users')->insertGetId([
                        'salon_id' => $salonId, // Some systems scope customers to salon, others global. Using salon_id here based on schema.
                        'name' => $faker->name,
                        'email' => "cust{$s}_{$cu}_" . uniqid() . "@mail.com",
                        'password' => $password,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    $custRoles[] = [
                        'role_id' => $customerRoleId,
                        'model_type' => 'App\Models\User',
                        'model_id' => $userId
                    ];
                }

                // We use insertGetId inside loop? For 1000, batch insert is better.
                // But we need IDs for bookings.
                // Let's Batch insert Customers, then pluck IDs.
                // BUT authentic data requires `user_id` map.
                // Optimization: Insert 100 registered individually (fast enough), insert 900 guests in batch.

                if ($isRegistered) {
                    $cId = DB::table('customers')->insertGetId([
                        'salon_id' => $salonId,
                        'user_id' => $userId,
                        'name' => "RegCustomer $cu",
                        'email' => "cust{$s}_{$cu}@mail.com",
                        'phone' => $faker->phoneNumber,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    $customerIds[] = $cId;
                    $registeredCustomerUserIds[$cId] = $userId;
                } else {
                    $batchCustomers[] = [
                        'salon_id' => $salonId,
                        'user_id' => null,
                        'name' => "GuestCustomer $cu",
                        'email' => "guest{$s}_{$cu}@mail.com",
                        'phone' => $faker->phoneNumber,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
            DB::table('model_has_roles')->insert($custRoles);

            // Insert Guests in chunks and fetch IDs
            if (!empty($batchCustomers)) {
                foreach (array_chunk($batchCustomers, 500) as $chunk) {
                    DB::table('customers')->insert($chunk);
                }
                // Fetch Guest IDs
                $guestIds = DB::table('customers')
                    ->where('salon_id', $salonId)
                    ->whereNull('user_id')
                    ->pluck('id')
                    ->toArray();
                $customerIds = array_merge($customerIds, $guestIds);
            }

            // G. Bookings (500)
            $bookingData = [];
            $posSales = [];
            $posItems = [];
            $payments = [];

            for ($b = 0; $b < $bookingsPerSalon; $b++) {
                $status = $faker->randomElement(['completed', 'completed', 'completed', 'confirmed', 'cancelled']); // 60% completed
                $staffId = $faker->randomElement($staffUserIds); // Must be a User ID
                $custId = $faker->randomElement($customerIds);
                $svcId = $faker->randomElement($serviceIds);
                $svcPrice = DB::table('services')->where('id', $svcId)->value('price') ?? 50;

                // Date logic
                $bDate = ($status == 'confirmed') ? $now->copy()->addDays(rand(1, 30)) : $now->copy()->subDays(rand(1, 90));

                $bookingId = DB::table('bookings')->insertGetId([
                    'salon_id' => $salonId,
                    'customer_id' => $custId,
                    'staff_id' => $staffId, // User ID reference
                    'service_id' => $svcId,
                    'status' => $status,
                    'amount' => $svcPrice,
                    'start_time' => $bDate,
                    'end_time' => $bDate->copy()->addHour(),
                    'created_at' => $bDate->subDays(rand(1, 5)),
                    'updated_at' => $bDate
                ]);

                if ($status == 'completed') {
                    // Create Sale
                    $custUserId = $registeredCustomerUserIds[$custId] ?? null; // Null if guest

                    $saleId = DB::table('pos_sales')->insertGetId([
                        'salon_id' => $salonId,
                        'customer_id' => $custUserId, // Nullable User ID
                        'employee_id' => $staffId,    // User ID (Staff)
                        'invoice_number' => "INV-{$s}-{$b}-" . Str::random(5),
                        'subtotal' => $svcPrice,
                        'tax' => 0,
                        'total' => $svcPrice,
                        'status' => 'completed',
                        'payment_status' => 'paid',
                        'payment_method' => 'cash',
                        'created_at' => $bDate,
                        'updated_at' => $bDate
                    ]);

                    // Sale Item
                    DB::table('pos_sale_items')->insert([
                        'salon_id' => $salonId,
                        'sale_id' => $saleId,
                        'item_type' => 'service', // Or 'App\Models\Service'
                        'item_id' => $svcId,
                        'item_name' => "Service #$svcId",
                        'quantity' => 1,
                        'unit_price' => $svcPrice,
                        'subtotal' => $svcPrice,
                        'total' => $svcPrice,
                    ]);

                    // Payment
                    DB::table('payments')->insert([
                        'salon_id' => $salonId,
                        'customer_id' => $custUserId,
                        'employee_id' => $staffId,
                        'amount' => $svcPrice,
                        'type' => 'sale',
                        'payment_method' => 'cash',
                        'created_at' => $bDate,
                        'updated_at' => $bDate
                    ]);
                }
            }

            $this->command->info("Completed Salon $s/$totalSalons");
        }

        // Clear Spatie Permission Cache to ensure new roles are visible
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("Large Scale Authentic Seeding Completed Successfully.");
    }
}
