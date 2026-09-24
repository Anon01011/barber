<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SaasPayment;
use App\Services\PaymentGateway\StripeService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create payment intent for subscription
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $salon = auth()->user()->salon;

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $plan->price,
                system_currency(),
                [
                    'salon_id' => $salon->id,
                    'plan_id' => $plan->id,
                ]
            );

            return response()->json([
                'clientSecret' => $paymentIntent['client_secret'],
                'paymentIntentId' => $paymentIntent['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create payment intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment and create subscription
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $salon = auth()->user()->salon;
        $plan = Plan::findOrFail($request->plan_id);

        try {
            // Confirm payment with Stripe
            $payment = $this->stripeService->confirmPayment($request->payment_intent_id);

            if ($payment['status'] === 'succeeded') {
                // Create or upgrade subscription
                $currentSubscription = $salon->subscription;

                if ($currentSubscription) {
                    $subscription = $currentSubscription->upgradeTo($plan);
                } else {
                    $subscription = \App\Models\Subscription::create([
                        'salon_id' => $salon->id,
                        'plan_id' => $plan->id,
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($plan->duration_in_days),
                        'status' => 'active',
                    ]);
                }

                // Sync permissions for the new plan
                \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $plan);

                // Update salon status and current subscription ID
                $salon->update([
                    'subscription_status' => 'active',
                    'is_active' => true,
                    'subscription_id' => $subscription->id
                ]);

                // Create payment record
                SaasPayment::create([
                    'salon_id' => $salon->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $plan->price,
                    'currency' => 'USD',
                    'payment_method' => 'stripe',
                    'transaction_id' => $request->payment_intent_id,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription has been activated.',
                    'subscription' => $subscription,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Payment confirmation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function history()
    {
        $salon = auth()->user()->salon;
        $payments = SaasPayment::where('salon_id', $salon->id)
            ->with('subscription.plan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('saas.payments.history', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $salon = auth()->user()->salon;
        $payment = SaasPayment::where('salon_id', $salon->id)
            ->with('subscription.plan')
            ->findOrFail($id);

        return view('saas.payments.show', compact('payment'));
    }
}
