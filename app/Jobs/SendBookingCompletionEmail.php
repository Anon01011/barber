<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Mail\BookingCompletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingCompletionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The booking instance.
     */
    public $booking;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [60, 120, 300]; // 1 min, 2 min, 5 min

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send booking completion email
            if ($this->booking->customer && $this->booking->customer->email) {
                Mail::to($this->booking->customer->email)
                    ->send(new BookingCompletedMail($this->booking));

                Log::info('Booking completion email sent', [
                    'booking_id' => $this->booking->id,
                    'customer_email' => $this->booking->customer->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send booking completion email', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage()
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Booking completion email job failed after all retries', [
            'booking_id' => $this->booking->id,
            'error' => $exception->getMessage()
        ]);
    }
}
