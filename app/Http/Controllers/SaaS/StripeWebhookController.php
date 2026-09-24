<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\SaasPayment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        // Validate webhook secret is configured
        if (!config('services.stripe.webhook_secret')) {
            Log::critical('Stripe webhook secret not configured!');
            abort(500, 'Webhook not properly configured');
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Validate signature header exists
        if (!$sigHeader) {
            Log::warning('Webhook request missing signature', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            abort(403, 'Missing signature');
        }

        try {
            // Verify webhook signature using StripeService
            $stripeService = app(\App\Services\PaymentGateway\StripeService::class);
            $event = $stripeService->verifyWebhookSignature($payload, $sigHeader);

            Log::info('Stripe webhook received', [
                'type' => $event->type,
                'id' => $event->id,
            ]);

            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                default:
                    Log::info('Unhandled webhook event type', ['type' => $event->type]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Invalid webhook signature', [
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);
            abort(403, 'Invalid signature');
        } catch (\Exception $e) {
            Log::error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle successful payment
     */
    protected function handlePaymentSucceeded($paymentIntent)
    {
        Log::info('Payment succeeded', ['payment_intent' => $paymentIntent->id]);

        // Update payment record
        $payment = SaasPayment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Activate subscription if it exists
            if ($payment->subscription) {
                $payment->subscription->update(['status' => 'active']);

                // Update salon status and current subscription ID
                $payment->salon->update([
                    'subscription_id' => $payment->subscription_id,
                    'subscription_status' => 'active',
                    'is_active' => true
                ]);

                // Sync permissions for the plan
                \App\Services\SalonRoleSeederService::seedDefaultRoles($payment->salon_id, $payment->subscription->plan);
            }
        }
    }

    /**
     * Handle failed payment
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        Log::warning('Payment failed', ['payment_intent' => $paymentIntent->id]);

        // Update payment record
        $payment = SaasPayment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
            ]);
        }
    }

    /**
     * Handle subscription update
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        Log::info('Subscription updated', ['subscription' => $stripeSubscription->id]);

        // Try to find subscription by Stripe subscription ID first
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        // Fallback to finding by salon_id from metadata
        if (!$subscription) {
            $salonId = $stripeSubscription->metadata->salon_id ?? null;

            if ($salonId) {
                $salon = \App\Models\Salon::find($salonId);
                if ($salon && $salon->subscription) {
                    $subscription = $salon->subscription;

                    // Store Stripe subscription ID for future lookups
                    $subscription->update(['stripe_subscription_id' => $stripeSubscription->id]);
                }
            }
        }

        if ($subscription) {
            // Update status based on Stripe status
            $status = $stripeSubscription->status;
            $isActive = in_array($status, ['active', 'trialing']);

            $subscription->update([
                'status' => $isActive ? 'active' : 'cancelled',
                'ends_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);

            Log::info('Local subscription updated', [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'stripe_status' => $status,
            ]);
        } else {
            Log::warning('Could not find subscription for Stripe update', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
        }
    }

    /**
     * Handle subscription deletion
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        Log::info('Subscription deleted', ['subscription' => $stripeSubscription->id]);

        // Try to find subscription by Stripe subscription ID first
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        // Fallback to finding by salon_id from metadata
        if (!$subscription) {
            $salonId = $stripeSubscription->metadata->salon_id ?? null;

            if ($salonId) {
                $salon = \App\Models\Salon::find($salonId);
                if ($salon && $salon->subscription) {
                    $subscription = $salon->subscription;
                }
            }
        }

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Log::info('Local subscription cancelled via webhook', ['id' => $subscription->id]);
        } else {
            Log::warning('Could not find subscription for Stripe deletion', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
        }
    }
}
