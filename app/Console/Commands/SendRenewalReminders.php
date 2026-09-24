<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Mail\SubscriptionRenewingSoonMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal reminder emails to salons before their subscription renews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending renewal reminders...');

        // Find subscriptions renewing in 7 days
        $renewingSoon = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->addDays(7)->toDateString())
            ->whereNotNull('ends_at')
            ->get();

        $reminderCount = 0;
        foreach ($renewingSoon as $subscription) {
            try {
                if ($subscription->salon->owner) {
                    Mail::to($subscription->salon->owner->email)
                        ->send(new SubscriptionRenewingSoonMail($subscription, 7));
                    
                    Log::info('Sent 7-day renewal reminder', [
                        'subscription_id' => $subscription->id,
                        'salon_id' => $subscription->salon_id,
                        'plan' => $subscription->plan->name,
                        'amount' => $subscription->plan->price,
                    ]);
                    
                    $reminderCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send renewal reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$reminderCount} renewal reminder emails (7 days).");

        // Find subscriptions renewing tomorrow
        $renewingTomorrow = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->addDay()->toDateString())
            ->whereNotNull('ends_at')
            ->get();

        $urgentCount = 0;
        foreach ($renewingTomorrow as $subscription) {
            try {
                if ($subscription->salon->owner) {
                    Mail::to($subscription->salon->owner->email)
                        ->send(new SubscriptionRenewingSoonMail($subscription, 1));
                    
                    Log::info('Sent 1-day renewal reminder', [
                        'subscription_id' => $subscription->id,
                        'salon_id' => $subscription->salon_id,
                    ]);
                    
                    $urgentCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send urgent renewal reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$urgentCount} urgent renewal reminder emails (1 day).");
        $this->info('Renewal reminders completed!');

        return Command::SUCCESS;
    }
}
