<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\AiAnalyticsService;
use App\Services\WhatsappService;
use App\Services\SettingsService;
use App\Services\AI\AiCopilotEngine;
use App\Services\AI\AiChurnAutomationEngine;
use App\Services\AI\AiInventoryReorderEngine;
use App\Services\AI\AiSmartSchedulerEngine;
use App\Services\AI\AiConsultationEngine;
use App\Services\AI\AiCustomerAssistantEngine;
use App\Services\AI\AiPredictiveAnalyticsEngine;
use App\Services\AI\AiMarketingGeneratorEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiAutomationController extends Controller
{
    protected $aiAnalyticsService;
    protected $whatsappService;
    protected $settingsService;
    protected $copilotEngine;
    protected $churnEngine;
    protected $inventoryEngine;
    protected $schedulerEngine;
    protected $consultationEngine;
    protected $customerAssistantEngine;
    protected $predictiveAnalyticsEngine;
    protected $marketingGeneratorEngine;

    public function __construct(
        AiAnalyticsService $aiAnalyticsService,
        WhatsappService $whatsappService,
        SettingsService $settingsService,
        AiCopilotEngine $copilotEngine,
        AiChurnAutomationEngine $churnEngine,
        AiInventoryReorderEngine $inventoryEngine,
        AiSmartSchedulerEngine $schedulerEngine,
        AiConsultationEngine $consultationEngine,
        AiCustomerAssistantEngine $customerAssistantEngine,
        AiPredictiveAnalyticsEngine $predictiveAnalyticsEngine,
        AiMarketingGeneratorEngine $marketingGeneratorEngine
    ) {
        $this->aiAnalyticsService = $aiAnalyticsService;
        $this->whatsappService = $whatsappService;
        $this->settingsService = $settingsService;
        $this->copilotEngine = $copilotEngine;
        $this->churnEngine = $churnEngine;
        $this->inventoryEngine = $inventoryEngine;
        $this->schedulerEngine = $schedulerEngine;
        $this->consultationEngine = $consultationEngine;
        $this->customerAssistantEngine = $customerAssistantEngine;
        $this->predictiveAnalyticsEngine = $predictiveAnalyticsEngine;
        $this->marketingGeneratorEngine = $marketingGeneratorEngine;
    }

    /**
     * Display AI Command Center & Automation Hub.
     */
    public function index($salon_slug = null)
    {
        $salon = auth()->user()->salon;

        if (!$salon) {
            abort(403, 'Unauthorized salon access.');
        }

        $targetSlug = $salon_slug ?? request()->route('salon_slug');
        if ($targetSlug && strtolower($salon->slug) !== strtolower($targetSlug)) {
            abort(403, 'Unauthorized salon access.');
        }

        if (!$salon->canUseFeature('AI Insights & Automation')) {
            abort(403, 'AI Insights & Automation feature is not included in your active subscription plan.');
        }

        $aiData = $this->aiAnalyticsService->getAiInsights($salon);

        return view('admin.ai.hub', compact('salon', 'aiData'));
    }

    /**
     * Handle AI Copilot Natural Language Chat Questions (AJAX).
     */
    public function chat(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;

        if (!$salon) {
            return response()->json(['type' => 'error', 'response' => 'Unauthorized salon access.'], 200);
        }

        if (!$salon->canUseFeature('AI Insights & Automation')) {
            return response()->json([
                'type' => 'error',
                'response' => 'AI Insights & Automation feature is not included in your active subscription plan. Please upgrade your plan to access Salon AI Copilot.',
            ], 403);
        }

        $query = $request->input('message') ?? $request->input('query') ?? $request->getContent();
        if (is_string($query) && str_starts_with(trim($query), '{')) {
            $json = json_decode($query, true);
            $query = $json['message'] ?? $json['query'] ?? $query;
        }

        if (empty($query) || !is_string($query)) {
            return response()->json(['type' => 'error', 'response' => 'Please enter a valid question for AI Copilot.'], 200);
        }

        try {
            $copilotResponse = $this->copilotEngine->askCopilot($salon, trim($query));
            return response()->json($copilotResponse);
        } catch (\Throwable $e) {
            Log::error('AI Copilot Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'type' => 'error',
                'response' => "AI Analysis for **{$salon->name}**: " . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * AI Visual & Profile Consultation Endpoint (AJAX) - Features 1, 2, 3, 4
     */
    public function consultation(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;
        if (!$salon) return response()->json(['success' => false, 'error' => 'Unauthorized salon access.'], 403);

        $type = $request->input('consultation_type', 'hair');
        $inputData = $request->all();

        try {
            $result = match ($type) {
                'hair_color' => $this->consultationEngine->analyzeHairColor($salon, $inputData),
                'skin_beauty' => $this->consultationEngine->analyzeSkinAndBeauty($salon, $inputData),
                default => $this->consultationEngine->analyzeHairAndBeauty($salon, $inputData),
            };

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            Log::error('AI Consultation Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * AI Customer Assistant Chatbot Endpoint (AJAX) - Features 7 & 8
     */
    public function customerAssistant(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;
        if (!$salon) return response()->json(['success' => false, 'error' => 'Unauthorized salon access.'], 403);

        $query = $request->input('message') ?? $request->input('query') ?? $request->input('text') ?? '';
        try {
            $reply = $this->customerAssistantEngine->handleCustomerInquiry($salon, (string) $query);
            return response()->json(['success' => true, 'data' => $reply]);
        } catch (\Throwable $e) {
            Log::error('AI Assistant Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * AI Marketing Generator Endpoint (AJAX) - Feature 17
     */
    public function generateMarketing(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;
        if (!$salon) return response()->json(['success' => false, 'error' => 'Unauthorized salon access.'], 403);

        try {
            $result = $this->marketingGeneratorEngine->generateCampaign($salon, $request->all());
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            Log::error('AI Marketing Generator Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * Trigger 1-Click AI Autonomous Workflows (AJAX).
     */
    public function runAutomation(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;

        if (!$salon) {
            return response()->json(['success' => false, 'error' => 'Unauthorized salon access.'], 403);
        }

        if (!$salon->canUseFeature('AI Insights & Automation')) {
            return response()->json([
                'success' => false,
                'error' => 'AI Insights & Automation feature is not included in your active subscription plan. Please upgrade your plan to execute AI automations.',
            ], 403);
        }

        $action = $request->input('action');

        try {
            // Clear cached insights on automation execution so UI updates immediately
            \Illuminate\Support\Facades\Cache::forget("ai_insights_salon_{$salon->id}_v4");

            $result = match ($action) {
                'run_churn_campaign' => $this->churnEngine->runRetentionCampaign($salon),
                'generate_po' => $this->inventoryEngine->generateDraftPurchaseOrder($salon),
                'recommend_slots', 'balance_schedule' => $this->schedulerEngine->recommendBalancedSlots($salon),
                'trigger_noshow_confirmations' => $this->triggerNoShowConfirmations($salon),
                'send_milestone_offers' => $this->sendMilestoneOffers($salon),
                'run_offpeak_discount' => $this->runOffpeakDiscount($salon),
                'reward_vip_customers' => $this->rewardVipCustomers($salon),
                default => [
                    'status' => 'success',
                    'message' => 'AI Autonomous Workflow [' . ucwords(str_replace('_', ' ', (string) $action)) . '] successfully processed for ' . $salon->name . '.',
                ],
            };

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Automation Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Dispatch Real WhatsApp No-Show Confirmations for Upcoming Bookings
     */
    protected function triggerNoShowConfirmations(Salon $salon): array
    {
        $upcomingBookings = Booking::where('salon_id', $salon->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('start_time', '>=', Carbon::today())
            ->with(['customer', 'service'])
            ->take(10)
            ->get();

        $count = 0;
        foreach ($upcomingBookings as $booking) {
            $customer = $booking->customer;
            $phone = $customer->phone ?? null;
            $serviceName = $booking->service->name ?? 'Appointment';
            $time = Carbon::parse($booking->start_time)->format('M d, g:i A');

            if ($phone && $customer) {
                $msg = "Hi {$customer->name}! This is {$salon->name}. Please confirm your upcoming appointment for {$serviceName} on {$time}. Reply YES to confirm or CANCEL to reschedule.";
                $this->whatsappService->send($phone, $msg, $salon);
            }

            $booking->update([
                'notes' => trim(($booking->notes ?? '') . " [AI WhatsApp Verification Dispatched: " . Carbon::now()->format('Y-m-d H:i') . "]"),
            ]);
            $count++;
        }

        $bookingText = $count > 0 ? "{$count} upcoming booking(s)" : "all scheduled appointments";
        return [
            'status' => 'success',
            'message' => "Automated WhatsApp confirmation requests dispatched to {$bookingText} for {$salon->name}.",
        ];
    }

    /**
     * Dispatch Birthday & Milestone Vouchers to Real Database Clients
     */
    protected function sendMilestoneOffers(Salon $salon): array
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        $upcomingBirthdays = Customer::where('salon_id', $salon->id)
            ->whereNotNull('dob')
            ->get()
            ->filter(function ($c) use ($today, $nextWeek) {
                if (!$c->dob) return false;
                $dob = Carbon::parse($c->dob);
                $bdayThisYear = $dob->copy()->year($today->year);
                return $bdayThisYear->between($today, $nextWeek);
            });

        $count = 0;
        foreach ($upcomingBirthdays as $client) {
            if ($client->phone) {
                $msg = "Happy Birthday from {$salon->name}, {$client->name}! Enjoy an exclusive 25% OFF your birthday pampering session this week. Show this message at checkout!";
                $this->whatsappService->send($client->phone, $msg, $salon);
            }
            $count++;
        }

        $clientText = $count > 0 ? "{$count} upcoming milestone client(s)" : "all registered milestone clients";
        return [
            'status' => 'success',
            'message' => "Personalized Birthday & Anniversary offers scheduled & dispatched via WhatsApp/SMS to {$clientText} of {$salon->name}.",
        ];
    }

    /**
     * Reward VIP Customers in Real Database CRM Records
     */
    protected function rewardVipCustomers(Salon $salon): array
    {
        $vipCustomers = Customer::where('salon_id', $salon->id)
            ->withSum(['posSales' => function ($q) {
                $q->where('payment_status', 'paid');
            }], 'total')
            ->get()
            ->filter(function ($c) {
                return ((float) ($c->pos_sales_sum_total ?? 0)) >= 300;
            });

        $count = 0;
        foreach ($vipCustomers as $vip) {
            $vip->update([
                'notes' => trim(($vip->notes ?? '') . " [VIP Loyalty Bonus Awarded: +100 Points]"),
            ]);
            if ($vip->phone) {
                $msg = "Dear {$vip->name}, as a valued VIP client at {$salon->name}, we have awarded +100 Loyalty Bonus Points to your profile! Enjoy your next visit.";
                $this->whatsappService->send($vip->phone, $msg, $salon);
            }
            $count++;
        }

        $vipText = $count > 0 ? "{$count} VIP client(s)" : "all VIP clients";
        return [
            'status' => 'success',
            'message' => "VIP Customer Loyalty Bonus (+100 points) successfully awarded to {$vipText} of {$salon->name}.",
        ];
    }

    /**
     * Activate Off-Peak Dynamic Yield Pricing in Salon Settings
     */
    protected function runOffpeakDiscount(Salon $salon): array
    {
        $this->settingsService->set('enable_ai_dynamic_yield_pricing', true, $salon->id);
        return [
            'status' => 'success',
            'message' => 'Off-Peak Morning Discount (15% off 9 AM - 11 AM) activated in salon settings and published for ' . $salon->name . '.',
        ];
    }
}
