<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;

    /**
     * Create a new event instance.
     *
     * @param Booking $booking
     * @param string $oldStatus
     * @return void
     */
    public function __construct(Booking $booking, $oldStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
    }
}
