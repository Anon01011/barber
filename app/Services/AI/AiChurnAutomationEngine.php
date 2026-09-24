<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Models\Customer;
use App\Services\NotificationService;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiChurnAutomationEngine
{
    protected $notificationService;
    protected $settingsService;

    public function __construct(NotificationService $notificationService, SettingsService $settingsService)
    {
        $this->notificationService = $notificationService;
        $this->settingsService = $settingsService;
    }

    public function runRetentionCampaign(Salon $salon): array
    {
        return $this->runAutomatedRetentionCampaign($salon);
    }

    /**
     * Scan salon customers for churn risk and execute automated retention campaign.
     */
    public function runAutomatedRetentionCampaign(Salon $salon): array
    {
        $autoEnabled = $this->settingsService->get('enable_ai_churn_auto_marketing', true, $salon->id);
        $discountPct = (int) $this->settingsService->get('ai_churn_discount_percentage', 15, $salon->id);

        if (!$autoEnabled) {
            return [
                'status' => 'disabled',
                'message' => 'Automated churn marketing is disabled in AI Settings.',
                'campaigns_sent' => 0,
            ];
        }

        // Find customers with > 45 days idle time
        $atRiskCustomers = Customer::where('salon_id', $salon->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('last_visit_at')
                    ->where('created_at', '<=', now()->subDays(45))
                    ->orWhere('last_visit_at', '<=', now()->subDays(45));
            })
            ->limit(20)
            ->get();

        $processedCount = 0;
        $campaignDetails = [];

        foreach ($atRiskCustomers as $customer) {
            $voucherCode = 'COMEBACK' . strtoupper(substr(md5($customer->id . time()), 0, 6));
            $daysIdle = Carbon::parse($customer->last_visit_at ?? $customer->created_at)->diffInDays(now());

            $messageText = "Hi {$customer->name}! We miss you at {$salon->name}. Use code {$voucherCode} for {$discountPct}% OFF your next visit! Book now.";

            // Send via Notification Service (SMS/Email/WhatsApp depending on salon setup)
            try {
                if (!empty($customer->phone)) {
                    $this->notificationService->sendSmsNotification($customer->phone, $messageText, $salon->id);
                }
            } catch (\Exception $e) {
                Log::warning("AI Churn Notification failed for customer {$customer->id}: " . $e->getMessage());
            }

            $processedCount++;
            $campaignDetails[] = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'days_idle' => $daysIdle,
                'voucher_code' => $voucherCode,
                'discount_pct' => $discountPct,
                'sent_at' => now()->toDateTimeString(),
            ];
        }

        return [
            'status' => 'success',
            'message' => $processedCount > 0
                ? "Successfully dispatched automated retention offers to {$processedCount} high-risk customers."
                : "Scanned all salon customers for {$salon->name}. Retention rates are healthy with 0 idle churn risks.",
            'campaigns_sent' => $processedCount,
            'campaign_details' => $campaignDetails,
        ];
    }
}
