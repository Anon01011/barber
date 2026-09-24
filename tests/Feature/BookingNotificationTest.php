<?php

namespace Tests\Feature;

use App\Mail\BookingRescheduledMail;
use App\Mail\BookingStatusUpdatedMail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Salon;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $salon;
    protected $customer;
    protected $service;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup basic data
        $this->salon = Salon::factory()->create();
        $this->customer = Customer::factory()->create(['salon_id' => $this->salon->id, 'email' => 'customer@example.com']);
        $this->staff = User::factory()->create(['salon_id' => $this->salon->id]);
        $this->service = Service::factory()->create(['salon_id' => $this->salon->id]);
    }

    /** @test */
    public function it_sends_email_when_booking_status_is_updated()
    {
        Mail::fake();

        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'amount' => 100,
            'status' => 'pending'
        ]);

        // Update status
        $booking->update(['status' => 'confirmed']);

        Mail::assertSent(BookingStatusUpdatedMail::class, function ($mail) use ($booking) {
            return $mail->booking->id === $booking->id &&
                $mail->hasTo('customer@example.com');
        });
    }

    /** @test */
    public function it_sends_email_when_booking_is_rescheduled()
    {
        Mail::fake();

        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'amount' => 100,
            'status' => 'confirmed'
        ]);

        // Reschedule
        $booking->update([
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHour()
        ]);

        Mail::assertSent(BookingRescheduledMail::class, function ($mail) use ($booking) {
            return $mail->booking->id === $booking->id &&
                $mail->hasTo('customer@example.com');
        });
    }

    /** @test */
    public function it_does_not_send_reschedule_email_if_status_is_cancelled()
    {
        Mail::fake();

        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'amount' => 100,
            'status' => 'cancelled'
        ]);

        // Update time (should not trigger reschedule email for cancelled booking)
        $booking->update([
            'start_time' => now()->addDays(2)
        ]);

        Mail::assertNotSent(BookingRescheduledMail::class);
    }
}
