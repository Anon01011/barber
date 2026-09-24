<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class TestConflict extends Command
{
    protected $signature = 'test:conflict';
    protected $description = 'Test cross-table conflict checking';

    public function handle()
    {
        $this->info("Starting Conflict Check Test...");

        try {
            // 1. Setup Data
            // Use existing or create new if needed. Using factories is safer if DB is set up.
            // Check if we have any user first to avoid factory issues if DB is empty/weird
            $staff = User::first();
            if (!$staff) {
                $staff = User::factory()->create();
            }
            
            $customer = Customer::first();
            if (!$customer) {
                $customer = Customer::factory()->create();
            }

            $service = Service::first();
            if (!$service) {
                $service = Service::factory()->create();
            }

            $startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
            $endTime = $startTime->copy()->addHour();

            $this->info("Test Data:");
            $this->info("Staff ID: {$staff->id}");
            $this->info("Time Slot: {$startTime} to {$endTime}");

            // 2. Create a Booking
            $this->info("\nCreating Booking...");
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'staff_id' => $staff->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed', // Use string literal to be safe
                'amount' => 100,
                'salon_id' => $staff->salon_id ?? 1
            ]);

            $this->info("Booking Created (ID: {$booking->id})");

            // 3. Check Conflict from Appointment Model
            $this->info("\nChecking Conflict from Appointment Model (should be TRUE)...");
            $hasConflict = Appointment::hasConflict($staff->id, $startTime, $endTime);

            if ($hasConflict) {
                $this->info("PASS: Appointment model detected conflict with Booking.");
            } else {
                $this->error("FAIL: Appointment model FAILED to detect conflict with Booking.");
            }

            // 4. Create an Appointment to test the other way
            $appStartTime = $startTime->copy()->addHours(2);
            $appEndTime = $endTime->copy()->addHours(2);
            
            $appointment = new Appointment();
            $appointment->staff_id = $staff->id;
            $appointment->start_time = $appStartTime;
            $appointment->end_time = $appEndTime;
            $appointment->salon_id = $staff->salon_id ?? 1;
            $appointment->save();

            $this->info("\nCreated Appointment at {$appStartTime}");

            // 5. Check Conflict from Booking Model
            $this->info("Checking if Booking model detects this Appointment...");
            $bookingDetectsAppointment = Booking::hasConflict($staff->id, $appStartTime, $appEndTime);

            if ($bookingDetectsAppointment) {
                $this->info("PASS: Booking model detected conflict with Appointment.");
            } else {
                $this->error("FAIL: Booking model FAILED to detect conflict with Appointment.");
            }

            // Cleanup
            $booking->delete();
            $appointment->delete();
            
            $this->info("\nTest Completed.");

        } catch (\Exception $e) {
            $this->error("CRITICAL ERROR: " . $e->getMessage());
            // $this->error($e->getTraceAsString()); // Comment out trace to avoid truncation for now
        }
    }
}
