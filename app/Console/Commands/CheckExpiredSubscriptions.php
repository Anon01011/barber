<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\SubscriptionExpiringSoonMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired and expiring subscriptions and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $settingsService = app(\App\Services\SettingsService::class);

        $expiryCheckEnabled = $settingsService->get('email_subscription_expired_enabled', true);
        if (!$expiryCheckEnabled) {
            $this->info('Subscription expiry email notifications are disabled.');
        }

        // Find subscriptions that expired today or earlier
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->whereNotNull('ends_at')
            ->get();

        $expiredCount = 0;
        foreach ($expiredSubscriptions as $subscription) {
            try {
                // Update subscription status
                $subscription->update(['status' => 'expired']);

                // Deactivate salon
                $subscription->salon->update(['is_active' => false]);

                // Send expiry notification only if enabled
                if ($expiryCheckEnabled && $subscription->salon->owner) {
                    Mail::to($subscription->salon->owner->email)
                        ->send(new SubscriptionExpiredMail($subscription));
                }

                Log::info('Subscription expired', [
                    'subscription_id' => $subscription->id,
                    'salon_id' => $subscription->salon_id,
                    'salon_name' => $subscription->salon->name,
                    'email_sent' => $expiryCheckEnabled
                ]);

                $expiredCount++;
            } catch (\Exception $e) {
                Log::error('Failed to process expired subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Marked {$expiredCount} subscriptions as expired.");

        // Check availability of "Expiring Soon" notifications
        $reminderEnabled = $settingsService->get('email_subscription_expiring_soon_enabled', true);

        if (!$reminderEnabled) {
            $this->info('Subscription reminder email notifications are disabled.');
            $this->info('Subscription check completed!');
            return Command::SUCCESS;
        }

        $days1 = (int) $settingsService->get('email_subscription_expiring_soon_days_1', 7);
        $days2 = (int) $settingsService->get('email_subscription_expiring_soon_days_2', 3);

        // First Reminder (Default: 7 days)
        $this->info("Checking for subscriptions expiring in {$days1} days...");

        $expiringSoon = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->addDays($days1)->toDateString())
            ->whereNotNull('ends_at')
            ->get();

        $reminderCount = 0;
        foreach ($expiringSoon as $subscription) {
            try {
                if ($subscription->salon->owner) {
                    Mail::to($subscription->salon->owner->email)
                        ->send(new SubscriptionExpiringSoonMail($subscription, $days1));

                    Log::info("Sent {$days1}-day expiry reminder", [
                        'subscription_id' => $subscription->id,
                        'salon_id' => $subscription->salon_id,
                    ]);

                    $reminderCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send expiry reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$reminderCount} expiry reminder emails.");

        // Second Reminder (Default: 3 days)
        $this->info("Checking for subscriptions expiring in {$days2} days...");
        $expiringVerySoon = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->addDays($days2)->toDateString())
            ->whereNotNull('ends_at')
            ->get();

        $urgentReminderCount = 0;
        foreach ($expiringVerySoon as $subscription) {
            try {
                if ($subscription->salon->owner) {
                    Mail::to($subscription->salon->owner->email)
                        ->send(new SubscriptionExpiringSoonMail($subscription, $days2));

                    Log::info("Sent {$days2}-day expiry reminder", [
                        'subscription_id' => $subscription->id,
                        'salon_id' => $subscription->salon_id,
                    ]);

                    $urgentReminderCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send urgent expiry reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$urgentReminderCount} urgent expiry reminder emails.");
        $this->info('Subscription check completed!');

        return Command::SUCCESS;
    }
}
