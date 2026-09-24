<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     *
     * @param Booking $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Broadcast on a private channel for employees
        return new PrivateChannel('employee.bookings');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->booking->id,
            'customer_name' => $this->booking->customer->name,
            'service_name' => $this->booking->service->name,
            'start_time' => $this->booking->start_time->toDateTimeString(),
            'status' => $this->booking->status,
            'staff_assignment_status' => $this->booking->staff_assignment_status,
        ];
    }
}
