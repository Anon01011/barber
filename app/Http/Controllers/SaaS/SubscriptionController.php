<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\SaasPayment;
use App\Mail\SubscriptionUpgradeRequest;
use App\Mail\SubscriptionRenewalRequest;
use App\Mail\SubscriptionCancelled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SettingsService;

class SubscriptionController extends Controller
{
    public function index()
    {
        $salon = Auth::user()->salon;
        $subscription = $salon->activeSubscription;
        $plans = Plan::where('is_active', true)->get();

        $pendingPayment = SaasPayment::where('salon_id', $salon->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $showNav = $subscription && $subscription->is_active;

        return view('saas.subscription.index', compact('salon', 'subscription', 'plans', 'pendingPayment', 'showNav'));
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:manual,stripe'
        ]);

        $salon = auth()->user()->salon;
        $currentSubscription = $salon->activeSubscription;
        $newPlan = Plan::findOrFail($request->plan_id);

        // Check if already on this plan
        if ($currentSubscription && $currentSubscription->plan_id == $newPlan->id) {
            return back()->with('error', 'You are already on this plan!');
        }

        // Check for pending payments
        $pendingPayment = SaasPayment::where('salon_id', $salon->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingPayment) {
            return back()->with('error', 'You already have a pending payment request. Please wait for admin approval.');
        }

        try {
            if ($request->payment_method === 'manual') {
                // Calculate proration if upgrading
                $proration = $currentSubscription ? $currentSubscription->calculateProration($newPlan) : null;
                $amount = $proration ? $proration['prorated_charge'] : $newPlan->price;

                // Create Pending Payment
                $payment = SaasPayment::create([
                    'salon_id' => $salon->id,
                    'subscription_id' => $currentSubscription ? $currentSubscription->id : null,
                    'amount' => $amount,
                    'currency' => system_currency(),
                    'payment_method' => 'manual',
                    'transaction_id' => 'MANUAL-REQ-' . uniqid(),
                    'status' => 'pending',
                    'metadata' => [
                        'type' => 'upgrade',
                        'new_plan_id' => $newPlan->id,
                        'proration' => $proration,
                    ],
                ]);

                // Send Email to Admin
                try {
                    $adminEmail = app(SettingsService::class)->get('admin_email', config('mail.from.address'));
                    Mail::to($adminEmail)->send(new SubscriptionUpgradeRequest($salon, $newPlan, $payment));
                } catch (\Exception $e) {
                    Log::error('Failed to send upgrade request email: ' . $e->getMessage());
                }

                return back()->with('success', 'Upgrade request submitted successfully. Please wait for admin approval.');
            }

            // Stripe Logic Placeholder
            // In a full implementation, this would redirect to a Stripe Checkout session or handling page.
            return back()->with('error', 'Online payment via Stripe is currently being configured. Please use Manual Payment for now.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit upgrade request: ' . $e->getMessage());
        }
    }

    public function renew(Request $request)
    {
        $salon = auth()->user()->salon;
        $subscription = $salon->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found!');
        }

        $request->validate([
            'payment_method' => 'required|in:manual,stripe'
        ]);

        // Check for pending payments
        $pendingPayment = SaasPayment::where('salon_id', $salon->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingPayment) {
            return back()->with('error', 'You already have a pending payment request. Please wait for admin approval.');
        }

        try {
            if ($request->payment_method === 'manual') {
                // Create Pending Payment
                $payment = SaasPayment::create([
                    'salon_id' => $salon->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $subscription->plan->price,
                    'currency' => system_currency(),
                    'payment_method' => 'manual',
                    'transaction_id' => 'MANUAL-RENEW-' . uniqid(),
                    'status' => 'pending',
                    'metadata' => [
                        'type' => 'renewal',
                        'plan_id' => $subscription->plan_id,
                    ],
                ]);

                // Send Email to Admin
                try {
                    $adminEmail = app(SettingsService::class)->get('admin_email', config('mail.from.address'));
                    Mail::to($adminEmail)->send(new SubscriptionRenewalRequest($salon, $subscription->plan, $payment));
                } catch (\Exception $e) {
                    Log::error('Failed to send renewal request email: ' . $e->getMessage());
                }

                return back()->with('success', 'Renewal request submitted successfully. Please wait for admin approval.');
            }

            // Stripe Logic Placeholder
            return back()->with('error', 'Online payment via Stripe is currently being configured. Please use Manual Payment for now.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit renewal request: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $salon = auth()->user()->salon;
        $subscriptions = $salon->subscriptions()->with('plan')->orderBy('created_at', 'desc')->get();

        return view('saas.subscription.history', compact('salon', 'subscriptions'));
    }

    public function invoice($id)
    {
        $salon = auth()->user()->salon;
        $subscription = $salon->subscriptions()->with('plan')->findOrFail($id);

        return view('saas.subscription.invoice', compact('salon', 'subscription'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $salon = auth()->user()->salon;
        $subscription = $salon->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found!');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $salon->update([
                'subscription_status' => 'cancelled'
            ]);

            // Log cancellation reason if provided
            if ($request->reason) {
                Log::info('Subscription cancelled', [
                    'salon_id' => $salon->id,
                    'subscription_id' => $subscription->id,
                    'reason' => $request->reason,
                ]);
            }

            // Send Cancellation Email
            try {
                Mail::to(auth()->user()->email)->send(new SubscriptionCancelled($salon, $subscription));
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email: ' . $e->getMessage());
            }

            return redirect()->route('admin.saas.subscription.index', ['salon_slug' => $salon->slug])
                ->with('success', 'Subscription cancelled successfully. You can continue using the service until ' . $subscription->ends_at->format('M d, Y'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }
}
