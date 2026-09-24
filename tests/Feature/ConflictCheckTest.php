<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class ConflictCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_detects_conflict_with_booking()
    {
        // 1. Setup Data
        $staff = User::factory()->create();
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        
        // Ensure salon_id is set if required (factories might handle it, but being safe)
        $salonId = $staff->salon_id ?? 1;

        $startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        // 2. Create a Booking
        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'staff_id' => $staff->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed',
            'amount' => 100,
            'salon_id' => $salonId
        ]);

        // 3. Check Conflict from Appointment Model
        $hasConflict = Appointment::hasConflict($staff->id, $startTime, $endTime);

        $this->assertTrue($hasConflict, 'Appointment model failed to detect conflict with existing Booking');
    }

    public function test_booking_detects_conflict_with_appointment()
    {
        // 1. Setup Data
        $staff = User::factory()->create();
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $salonId = $staff->salon_id ?? 1;

        $startTime = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        // 2. Create an Appointment
        $appointment = new Appointment();
        $appointment->staff_id = $staff->id;
        $appointment->start_time = $startTime;
        $appointment->end_time = $endTime;
        $appointment->salon_id = $salonId;
        $appointment->save();

        // 3. Check Conflict from Booking Model
        $hasConflict = Booking::hasConflict($staff->id, $startTime, $endTime);

        $this->assertTrue($hasConflict, 'Booking model failed to detect conflict with existing Appointment');
    }
    
    public function test_no_conflict_when_times_do_not_overlap()
    {
        $staff = User::factory()->create();
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $salonId = $staff->salon_id ?? 1;

        $startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'staff_id' => $staff->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed',
            'amount' => 100,
            'salon_id' => $salonId
        ]);

        // Check for a time slot AFTER the booking
        $checkStart = $endTime->copy()->addHour();
        $checkEnd = $checkStart->copy()->addHour();

        $hasConflict = Appointment::hasConflict($staff->id, $checkStart, $checkEnd);
        $this->assertFalse($hasConflict, 'Appointment model detected false positive conflict');
        
        $hasConflictBooking = Booking::hasConflict($staff->id, $checkStart, $checkEnd);
        $this->assertFalse($hasConflictBooking, 'Booking model detected false positive conflict');
    }
}
