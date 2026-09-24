<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        // Set Stripe API key from settings or env
        $stripeKey = config('services.stripe.secret') ?? env('STRIPE_SECRET_KEY');
        if ($stripeKey) {
            Stripe::setApiKey($stripeKey);
        }
    }

    /**
     * Create a payment intent for subscription payment
     */
    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = []): ?PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => (int)($amount * 100), // Convert to cents
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Confirm a payment intent
     */
    public function confirmPayment(string $paymentIntentId): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Confirmation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create or retrieve a Stripe customer
     */
    public function createCustomer(string $email, string $name, array $metadata = []): ?Customer
    {
        try {
            // Check if customer already exists
            $customers = Customer::all(['email' => $email, 'limit' => 1]);
            
            if (count($customers->data) > 0) {
                return $customers->data[0];
            }

            // Create new customer
            return Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Customer Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if Stripe is configured
     */
    public function isConfigured(): bool
    {
        $stripeKey = config('services.stripe.secret') ?? env('STRIPE_SECRET_KEY');
        return !empty($stripeKey);
    }
}
