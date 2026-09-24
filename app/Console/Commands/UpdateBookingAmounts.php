<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class UpdateBookingAmounts extends Command
{
    protected $signature = 'bookings:update-amounts';

    protected $description = 'Update booking amounts from related service price where amount is null or zero';

    public function handle()
    {
        $this->info('Starting to update booking amounts...');

        $bookings = Booking::whereNull('amount')
            ->orWhere('amount', 0)
            ->with('service')
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            if ($booking->service && $booking->service->price > 0) {
                $booking->amount = $booking->service->price;
                $booking->save();
                $count++;
                $this->info("Updated booking ID {$booking->id} with amount {$booking->amount}");
            }
        }

        $this->info("Updated {$count} bookings.");

        return 0;
    }
}
