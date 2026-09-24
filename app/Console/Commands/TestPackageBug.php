<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Package;
use App\Models\Booking;
use App\Models\CustomerPackageBalance;
use App\Http\Controllers\POS\PosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestPackageBug extends Command
{
    protected $signature = 'test:package-bug';
    protected $description = 'Reproduce the package balance double decrement bug';

    public function handle()
    {
        $this->info('Starting reproduction test...');

        DB::beginTransaction();

        try {
            // 1. Setup User and Context
            $user = User::role('salon_admin')->first();
            if (!$user) {
                $user = User::first();
            }
            if (!$user) {
                $this->error('No user found.');
                return;
            }
            Auth::login($user);
            $salonId = $user->salon_id;
            $this->info("Using User: {$user->name} (Salon ID: {$salonId})");

            // 2. Create Test Data
            $customer = Customer::create([
                'salon_id' => $salonId,
                'name' => 'Test Customer ' . time(),
                'email' => 'test' . time() . '@example.com',
                'mobile' => '1234567890',
                'status' => 'active'
            ]);

            $service = Service::create([
                'salon_id' => $salonId,
                'name' => 'Test Service ' . time(),
                'price' => 100,
                'duration' => 60,
                'status' => 'active'
            ]);

            $package = Package::create([
                'salon_id' => $salonId,
                'name' => 'Test Package ' . time(),
                'price' => 500,
                'status' => 'active',
                'validity_value' => 30,
                'validity_unit' => 'days'
            ]);
            $package->services()->attach($service->id, ['quantity' => 10]);

            // 3. Setup Initial State: Customer has 1 session remaining
            $balance = CustomerPackageBalance::create([
                'salon_id' => $salonId,
                'customer_id' => $customer->id,
                'package_id' => $package->id,
                'service_id' => $service->id,
                'quantity_remaining' => 1,
                'expiry_date' => now()->addDays(30)
            ]);
            $this->info("Initial Balance: {$balance->quantity_remaining}");

            // 4. Create Booking (Simulate BookingController logic)
            // Booking consumes the last session
            $booking = Booking::create([
                'salon_id' => $salonId,
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'package_id' => $package->id,
                'staff_id' => $user->id, // Assign to current user as staff
                'start_time' => now(),
                'end_time' => now()->addHour(),
                'amount' => 0,
                'status' => 'confirmed'
            ]);

            // Manually decrement balance as BookingController would
            $balance->decrement('quantity_remaining');
            $balance->refresh();
            $this->info("Balance after Booking: {$balance->quantity_remaining} (Updated At: {$balance->updated_at})");

            // 5. Simulate POS Sale: Buy Package (10 sessions) + Pay for Booking
            $request = new Request();
            $request->merge([
                'customer_id' => $customer->id,
                'payment_method' => 'cash',
                'amount_paid' => 500,
                'items' => [
                    [
                        'type' => 'package',
                        'id' => $package->id,
                        'name' => $package->name,
                        'price' => 500,
                        'quantity' => 1, // Buying 1 package
                    ],
                    [
                        'type' => 'service',
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => 0,
                        'quantity' => 1,
                        'package_id' => $package->id,
                        'booking_id' => $booking->id // Linked to booking
                    ]
                ]
            ]);
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $this->info('Processing POS Sale...');
            $controller = new PosController(app(\App\Services\SettingsService::class));
            $controller->store($request);

            // 6. Verify Result
            $balance->refresh();
            $this->info("Final Balance: {$balance->quantity_remaining}");

            if ($balance->quantity_remaining == 9) {
                $this->error("BUG REPRODUCED: Balance is 9 (Should be 10). Double decrement occurred.");
            } elseif ($balance->quantity_remaining == 10) {
                $this->info("SUCCESS: Balance is 10. Logic is correct.");
            } else {
                $this->warn("Unexpected Balance: {$balance->quantity_remaining}");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        } finally {
            DB::rollBack(); // Always rollback test data
            $this->info('Test data rolled back.');
        }
    }
}
