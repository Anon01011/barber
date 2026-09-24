<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

// MODELS (adjust if namespace differs)
use App\Models\User;
use App\Models\Salon;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use Carbon\Carbon;

class LoadTestSeeder extends Seeder
{
    public function run(): void
    {
        echo "Starting Load Test Seeding...\n";

        DB::beginTransaction();

        try {

            /* ---------------- ROLE ---------------- */
            $role = Role::firstOrCreate(
                ['name' => 'salon_admin', 'guard_name' => 'web']
            );

            /* -------------- CONFIG -------------- */
            $SALONS = 2;
            $PRODUCT_CATEGORIES = 5;
            $PRODUCTS_PER_CATEGORY = 20;
            $SERVICE_CATEGORIES = 5;
            $SERVICES_PER_CATEGORY = 10;

            /* -------------- LOOP SALONS -------------- */
            for ($i = 1; $i <= $SALONS; $i++) {

                echo "Processing Salon {$i} / {$SALONS}\n";

                /* -------- SALON -------- */
                $salon = Salon::firstOrCreate(
                    ['email' => "salon{$i}@example.com"],
                    [
                        'name' => "Load Test Salon {$i}",
                        'slug' => 'load-test-salon-' . Str::uuid(),
                        'is_active' => 1,
                    ]
                );

                /* -------- USER -------- */
                $user = User::firstOrCreate(
                    ['email' => "admin{$i}@example.com"],
                    [
                        'name' => "Salon Admin {$i}",
                        'password' => Hash::make('password'),
                    ]
                );

                if (!$user->hasRole($role)) {
                    $user->assignRole($role);
                }

                /* -------- PRODUCT CATEGORIES -------- */
                $productCategories = [];

                for ($c = 1; $c <= $PRODUCT_CATEGORIES; $c++) {

                    $productCategories[] = InventoryCategory::create([
                        'salon_id' => $salon->id,
                        'name' => "Product Category {$c}",
                        'slug' => 'product-category-' . Str::uuid(), // ✅ UNIQUE
                        'color' => '#6c757d',
                        'icon' => 'fas fa-box',
                        'order' => $c,
                        'is_active' => 1,
                    ]);
                }

                /* -------- PRODUCTS -------- */
                foreach ($productCategories as $category) {
                    for ($p = 1; $p <= $PRODUCTS_PER_CATEGORY; $p++) {
                        InventoryItem::create([
                            'salon_id' => $salon->id,
                            'category_id' => $category->id,
                            'name' => "Product {$p}",
                            'sku' => 'SKU-' . Str::uuid(),
                            'selling_price' => rand(100, 1000),
                            'quantity_in_stock' => rand(10, 200),
                            'is_active' => 1,
                            'is_taxable' => 1,
                            'tax_rate' => 0,
                        ]);
                    }
                }

                /* -------- SERVICE CATEGORIES -------- */
                $serviceCategories = [];

                for ($sc = 1; $sc <= $SERVICE_CATEGORIES; $sc++) {
                    $serviceCategories[] = ServiceCategory::create([
                        'salon_id' => $salon->id,
                        'name' => "Service Category {$sc}",
                        'status' => 'active',
                    ]);
                }

                /* -------- SERVICES -------- */
                foreach ($serviceCategories as $category) {
                    for ($s = 1; $s <= $SERVICES_PER_CATEGORY; $s++) {
                        Service::create([
                            'salon_id' => $salon->id,
                            'category_id' => $category->id,
                            'name' => "Service {$s}",
                            'price' => rand(300, 3000),
                            'duration' => rand(15, 90),
                            'status' => 'active',
                        ]);
                    }
                }

                /* -------- CUSTOMERS -------- */
                $CUSTOMERS_PER_SALON = 500;
                $customerData = [];
                for ($c = 1; $c <= $CUSTOMERS_PER_SALON; $c++) {
                    $customerData[] = [
                        'salon_id' => $salon->id,
                        'name' => "Customer $c Salon $i",
                        'email' => "cust{$c}_s{$i}@example.com",
                        'phone' => '555' . str_pad($i, 3, '0', STR_PAD_LEFT) . str_pad($c, 4, '0', STR_PAD_LEFT),
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                Customer::insert($customerData);
                $customerIds = Customer::where('salon_id', $salon->id)->pluck('id')->toArray();

                /* -------- BOOKINGS -------- */
                $BOOKINGS_PER_SALON = 1000;
                $bookingData = [];
                $startDate = Carbon::now()->subMonths(6);

                // Fetch service IDs for random selection
                $serviceIds = Service::where('salon_id', $salon->id)->pluck('id')->toArray();

                if (!empty($serviceIds) && !empty($customerIds)) {
                    for ($b = 0; $b < $BOOKINGS_PER_SALON; $b++) {
                        $bookingDate = $startDate->copy()->addMinutes(rand(0, 525600));
                        $serviceId = $serviceIds[array_rand($serviceIds)];
                        $customerId = $customerIds[array_rand($customerIds)];

                        // Simple duration/price lookup (optimization: could map beforehand)
                        $service = Service::find($serviceId);

                        $bookingData[] = [
                            'salon_id' => $salon->id,
                            'customer_id' => $customerId,
                            'service_id' => $serviceId,
                            'staff_id' => $user->id, // Assign to admin for simplicity
                            'start_time' => $bookingDate,
                            'end_time' => $bookingDate->copy()->addMinutes($service->duration),
                            'amount' => $service->price,
                            'status' => ['completed', 'confirmed', 'cancelled'][rand(0, 2)],
                            'created_at' => $bookingDate,
                            'updated_at' => $bookingDate,
                        ];
                    }

                    foreach (array_chunk($bookingData, 500) as $chunk) {
                        Booking::insert($chunk);
                    }
                }

                /* -------- POS SALES -------- */
                $SALES_PER_SALON = 500;
                $productIds = InventoryItem::where('salon_id', $salon->id)->pluck('id')->toArray();

                if (!empty($productIds) && !empty($customerIds)) {
                    for ($s = 0; $s < $SALES_PER_SALON; $s++) {
                        $saleDate = $startDate->copy()->addMinutes(rand(0, 525600));
                        $customerId = $customerIds[array_rand($customerIds)];

                        $sale = PosSale::create([
                            'salon_id' => $salon->id,
                            'customer_id' => $customerId,
                            'employee_id' => $user->id,
                            'invoice_number' => "INV-S{$i}-{$s}",
                            'subtotal' => 0,
                            'tax' => 0,
                            'total' => 0,
                            'payment_status' => 'paid',
                            'payment_method' => 'cash',
                            'sale_date' => $saleDate,
                            'created_at' => $saleDate,
                        ]);

                        $itemCount = rand(1, 5);
                        $subtotal = 0;

                        for ($it = 0; $it < $itemCount; $it++) {
                            $productId = $productIds[array_rand($productIds)];
                            $product = InventoryItem::find($productId);
                            $qty = rand(1, 3);
                            $lineTotal = $product->selling_price * $qty;

                            PosSaleItem::create([
                                'sale_id' => $sale->id,
                                'item_id' => $product->id,
                                'item_type' => get_class($product),
                                'item_name' => $product->name,
                                'quantity' => $qty,
                                'unit_price' => $product->selling_price,
                                'total' => $lineTotal,
                                'subtotal' => $lineTotal,
                            ]);

                            $subtotal += $lineTotal;
                        }

                        $sale->update([
                            'subtotal' => $subtotal,
                            'total' => $subtotal,
                        ]);
                    }
                }
            }

            DB::commit();
            echo "✅ Load Test Seeding Completed Successfully\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
