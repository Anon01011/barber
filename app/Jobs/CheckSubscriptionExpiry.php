<?php

namespace App\Jobs;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\SubscriptionExpiringSoonMail;

class CheckSubscriptionExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Checking for expired subscriptions...');

        // 1. Handle Expired Subscriptions (Active or Cancelled but reached end date)
        $expiredSubscriptions = Subscription::whereIn('status', ['active', 'cancelled'])
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            // Only update status if it's not already expired
            if ($subscription->status !== 'expired') {
                $subscription->update(['status' => 'expired']);
                
                Log::info("Subscription {$subscription->id} for Salon {$subscription->salon_id} has expired.");

                // Send email notification to salon owner
                try {
                    Mail::to($subscription->salon->owner->email)->send(new SubscriptionExpiredMail($subscription));
                    Log::info("Expiry email sent to {$subscription->salon->owner->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send expiry email: " . $e->getMessage());
                }
            }
        }

        // 2. Handle Expiring Soon Subscriptions (Only Active ones, not cancelled)
        // We don't remind cancelled users because they already know it's ending.
        $expiringSoonSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays(7)) // Changed from 3 to 7 days to match system standard
            ->get();

        foreach ($expiringSoonSubscriptions as $subscription) {
            $daysLeft = now()->diffInDays($subscription->ends_at);
            
            // Avoid sending duplicate emails every day if needed, but for now we send daily reminders in the last week
            try {
                Mail::to($subscription->salon->owner->email)->send(new SubscriptionExpiringSoonMail($subscription, $daysLeft));
                Log::info("Expiring soon email sent to {$subscription->salon->owner->email} ({$daysLeft} days left)");
            } catch (\Exception $e) {
                Log::error("Failed to send expiring soon email: " . $e->getMessage());
            }
        }

        Log::info('Subscription expiry check completed.');
    }
}
