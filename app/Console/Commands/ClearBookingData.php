<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearBookingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:booking-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all booking related data including bookings, booking rejections, and related payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to clear booking related data...');

        DB::beginTransaction();

        try {
            // Delete booking rejections
            DB::table('booking_rejections')->truncate();
            $this->info('Cleared booking_rejections table.');

            // Delete bookings
            DB::table('bookings')->truncate();
            $this->info('Cleared bookings table.');

            // Delete payments related to bookings (types: income, tip)
            $deletedPayments = DB::table('payments')
                ->whereIn('type', ['income', 'tip'])
                ->delete();
            $this->info("Deleted {$deletedPayments} payments related to bookings.");

            DB::commit();

            $this->info('Booking related data cleared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to clear booking related data: ' . $e->getMessage());
        }
    }
}
