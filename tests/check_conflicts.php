<?php

use App\Models\Booking;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Conflict Check Test...\n";

// 1. Setup Data
$staff = User::factory()->create();
$customer = Customer::factory()->create();
$service = Service::factory()->create();

$startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
$endTime = $startTime->copy()->addHour();

echo "Test Data Created:\n";
echo "Staff ID: {$staff->id}\n";
echo "Time Slot: {$startTime} to {$endTime}\n";

// 2. Create a Booking
echo "\nCreating Booking...\n";
$booking = Booking::create([
    'customer_id' => $customer->id,
    'service_id' => $service->id,
    'staff_id' => $staff->id,
    'start_time' => $startTime,
    'end_time' => $endTime,
    'status' => Booking::STATUS_CONFIRMED,
    'amount' => 100,
    'salon_id' => 1 // Assuming salon ID 1 exists or is not strictly checked in this context
]);

echo "Booking Created (ID: {$booking->id})\n";

// 3. Check Conflict from Appointment Model
echo "\nChecking Conflict from Appointment Model (should be TRUE)...\n";
$hasConflict = Appointment::hasConflict($staff->id, $startTime, $endTime);

if ($hasConflict) {
    echo "PASS: Appointment model detected conflict with Booking.\n";
} else {
    echo "FAIL: Appointment model FAILED to detect conflict with Booking.\n";
}

// 4. Check Conflict from Booking Model (should be TRUE because of the existing booking)
echo "\nChecking Conflict from Booking Model (should be TRUE)...\n";
$hasConflictBooking = Booking::hasConflict($staff->id, $startTime, $endTime, $booking->id); 
// Note: We exclude the booking itself to simulate trying to create *another* booking/appointment
// But wait, hasConflict checks if *any* record exists.
// If we want to check if we can create a NEW appointment, we just check.
// If we want to check if we can create a NEW booking, we check.

// Let's try to create an Appointment record now to test the other way around
$appointment = new Appointment();
$appointment->staff_id = $staff->id;
$appointment->start_time = $startTime->addHours(2); // Different time
$appointment->end_time = $endTime->addHours(2);
$appointment->save();

echo "\nCreated Appointment at {$appointment->start_time}\n";

// Now check if Booking detects this appointment
echo "Checking if Booking model detects this Appointment...\n";
$bookingDetectsAppointment = Booking::hasConflict($staff->id, $appointment->start_time, $appointment->end_time);

if ($bookingDetectsAppointment) {
    echo "PASS: Booking model detected conflict with Appointment.\n";
} else {
    echo "FAIL: Booking model FAILED to detect conflict with Appointment.\n";
}

// Cleanup
$booking->delete();
$appointment->delete();
$staff->delete();
$customer->delete();
$service->delete();

echo "\nTest Completed.\n";
