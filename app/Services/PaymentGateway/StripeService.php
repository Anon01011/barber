<?php

namespace App\Services\PaymentGateway;

use Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Refund;

class StripeService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.stripe.secret');
        
        if ($this->apiKey) {
            Stripe::setApiKey($this->apiKey);
        }
    }

    /**
     * Create a payment intent for subscription
     */
    public function createPaymentIntent($amount, $currency = 'USD', $metadata = [])
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured. Please add STRIPE_SECRET to your .env file.');
        }

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => strtolower($currency),
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a payment
     */
    public function confirmPayment($paymentIntentId)
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount_received' => $paymentIntent->amount_received / 100, // Convert from cents
                'currency' => $paymentIntent->currency,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Create a customer in Stripe
     */
    public function createCustomer($email, $name, $metadata = [])
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $customer = Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);

            return [
                'id' => $customer->id,
                'email' => $customer->email,
                'name' => $customer->name,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Create a subscription in Stripe
     */
    public function createSubscription($customerId, $priceId, $metadata = [])
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $subscription = StripeSubscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'metadata' => $metadata,
            ]);

            return [
                'id' => $subscription->id,
                'customer' => $subscription->customer,
                'status' => $subscription->status,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a subscription in Stripe
     */
    public function cancelSubscription($subscriptionId)
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            $subscription->cancel();

            return [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'canceled_at' => $subscription->canceled_at,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve payment details
     */
    public function retrievePayment($paymentIntentId)
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'created' => $paymentIntent->created,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Process refund
     */
    public function refundPayment($paymentIntentId, $amount = null)
    {
        if (!$this->apiKey) {
            throw new Exception('Stripe API key not configured');
        }

        try {
            $refundData = ['payment_intent' => $paymentIntentId];
            
            if ($amount !== null) {
                $refundData['amount'] = $amount * 100; // Convert to cents
            }

            $refund = Refund::create($refundData);

            return [
                'id' => $refund->id,
                'payment_intent' => $refund->payment_intent,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        
        if (!$webhookSecret) {
            throw new Exception('Stripe webhook secret not configured');
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );

            return $event;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception('Webhook signature verification failed: ' . $e->getMessage());
        }
    }
}
