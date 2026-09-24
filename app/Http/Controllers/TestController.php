<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Package;
use App\Models\Booking;
use App\Models\PosSale;
use App\Models\CustomerPackageBalance;
use App\Http\Controllers\POS\PosController;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function testPackageBug()
    {
        DB::beginTransaction();
        try {
            $salonId = 1; // Assuming salon ID 1 exists
            $user = User::where('salon_id', $salonId)->first();
            auth()->login($user);

            // 1. Setup Data
            $customer = Customer::create([
                'salon_id' => $salonId,
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '1234567890'
            ]);

            $service = Service::create([
                'salon_id' => $salonId,
                'name' => 'Test Service',
                'price' => 100,
                'duration' => 30
            ]);

            $package = Package::create([
                'salon_id' => $salonId,
                'name' => 'Test Package',
                'price' => 500
            ]);
            $package->services()->attach($service->id, ['quantity' => 1]);

            // 2. Create Booking (Simulate BookingController logic)
            // User has NO balance yet.
            $booking = Booking::create([
                'salon_id' => $salonId,
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'package_id' => $package->id,
                'staff_id' => $user->id,
                'start_time' => now(),
                'end_time' => now()->addMinutes(30),
                'amount' => 0,
                'status' => 'confirmed'
            ]);

            // Try to decrement (should fail/do nothing as balance is 0)
            $balance = CustomerPackageBalance::where('customer_id', $customer->id)
                ->where('package_id', $package->id)
                ->where('service_id', $service->id)
                ->where('quantity_remaining', '>', 0)
                ->first();

            if ($balance) {
                $balance->decrement('quantity_remaining');
                echo "BookingController: Decremented balance.\n";
            } else {
                echo "BookingController: No balance to decrement.\n";
            }

            // 3. Simulate POS Sale (Buy Package + Redeem Service)
            $posController = new PosController(
                app(\App\Services\SettingsService::class),
                app(\App\Services\NotificationService::class)
            );

            $reflector = new \ReflectionClass($posController);
            echo "PosController File: " . $reflector->getFileName() . "\n";

            $requestData = [
                'customer_id' => $customer->id,
                'payment_method' => 'cash',
                'items' => [
                    [
                        'type' => 'package',
                        'id' => $package->id,
                        'quantity' => 1,
                        'price' => 500
                    ],
                    [
                        'type' => 'service',
                        'id' => $service->id,
                        'package_id' => $package->id,
                        'quantity' => 1,
                        'price' => 0,
                        'booking_id' => $booking->id
                    ]
                ],
                'subtotal' => 500,
                'tax' => 0,
                'discount' => 0,
                'total' => 500,
                'cash_amount' => 500,
                'tendered_amount' => 500,
                'change_amount' => 0
            ];

            $request = new Request($requestData);

            // We can't easily call store() because it's complex and uses DB transaction.
            // But we can replicate the logic or call it if we mock everything.
            // Let's try calling store() directly.
            $response = $posController->store($request);

            echo "POS Store Response: " . $response->getStatusCode() . "\n";

            // 4. Check Balance
            $finalBalance = CustomerPackageBalance::where('customer_id', $customer->id)
                ->where('package_id', $package->id)
                ->where('service_id', $service->id)
                ->first();

            echo "Final Balance: " . ($finalBalance ? $finalBalance->quantity_remaining : 'null') . "\n";

            DB::rollBack(); // Always rollback
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString();
            DB::rollBack();
        }
    }
}
