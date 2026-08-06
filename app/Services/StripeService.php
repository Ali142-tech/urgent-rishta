<?php

namespace App\Services;

use App\OnlinePackage;
use App\Payment;
use Illuminate\Support\Facades\Config;
use Stripe\StripeClient;

class StripeService
{
    private function client(): StripeClient
    {
        $secret = (string) Config::get('services.stripe.secret');
        return new StripeClient($secret);
    }

    public function createCheckoutSession(Payment $payment, OnlinePackage $package): \Stripe\Checkout\Session
    {
        $currency = (string) Config::get('services.stripe.currency', 'usd');

        // Stripe amounts are in the smallest currency unit (cents).
        $amountCents = (int) round(((float) $payment->amount) * 100);

        $successUrl = route('stripe.success', ['reference' => $payment->reference]) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('stripe.cancel', ['reference' => $payment->reference]);

        return $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'client_reference_id' => $payment->reference,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'payment_method_types' => ['card'],
            'metadata' => [
                'payment_reference' => $payment->reference,
                'user_id' => (string) $payment->user_id,
                'online_package_id' => (string) $payment->online_package_id,
                'package_dataid' => (string) $package->dataid,
            ],
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' => $package->name,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
