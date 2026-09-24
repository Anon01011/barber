<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Salon;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected $salon;
    protected $admin;
    protected $employee;
    protected $customer;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salon = Salon::factory()->create(['slug' => 'test-salon']);

        $this->admin = User::factory()->create(['salon_id' => $this->salon->id]);
        $this->admin->assignRole('salon_admin');

        $this->employee = User::factory()->create(['salon_id' => $this->salon->id]);
        $this->employee->assignRole('employee');

        $this->customer = Customer::factory()->create(['salon_id' => $this->salon->id]);
        $this->service = Service::factory()->create(['salon_id' => $this->salon->id]);
    }

    /** @test */
    public function it_prevents_completing_a_future_booking_via_admin_update_status()
    {
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->employee->id,
            'start_time' => Carbon::now()->addDays(2),
            'end_time' => Carbon::now()->addDays(2)->addHour(),
            'amount' => 100,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.bookings.status.update', [
                'salon_slug' => $this->salon->slug,
                'booking' => $booking->id
            ]), [
                'status' => 'completed'
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Bookings scheduled for the future cannot be completed.'
        ]);

        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    /** @test */
    public function it_allows_completing_a_today_booking_via_admin_update_status()
    {
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->employee->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'amount' => 100,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.bookings.status.update', [
                'salon_slug' => $this->salon->slug,
                'booking' => $booking->id
            ]), [
                'status' => 'completed'
            ]);

        // Should return requires_payment: true (standard behavior for completion)
        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'requires_payment' => true
        ]);
    }

    /** @test */
    public function it_allows_staff_to_mark_as_staff_completed()
    {
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->employee->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'amount' => 100,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->employee)
            ->postJson(route('employee.appointments.complete', [
                'salon_slug' => $this->salon->slug,
                'id' => $booking->id
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Appointment marked as completed from your side. Final completion with payment will be handled by the admin/manager.'
        ]);

        $this->assertEquals('staff_completed', $booking->fresh()->status);
    }

    /** @test */
    public function it_allows_admin_to_finalize_staff_completed_booking_with_payment()
    {
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->employee->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'amount' => 100,
            'status' => 'staff_completed'
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.bookings.complete-payment', [
                'salon_slug' => $this->salon->slug,
                'booking' => $booking->id
            ]), [
                'payment_method' => 'cash',
                'tip_amount' => 10
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Booking completed successfully with payment.'
        ]);

        $this->assertEquals('completed', $booking->fresh()->status);
        $this->assertEquals('paid', $booking->fresh()->payment_status);
    }

    /** @test */
    public function it_prevents_staff_from_completing_future_booking()
    {
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->employee->id,
            'start_time' => Carbon::now()->addDays(2),
            'end_time' => Carbon::now()->addDays(2)->addHour(),
            'amount' => 100,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->employee)
            ->postJson(route('employee.appointments.complete', [
                'salon_slug' => $this->salon->slug,
                'id' => $booking->id
            ]));

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'message' => 'Bookings scheduled for the future cannot be completed.'
        ]);
    }
}
