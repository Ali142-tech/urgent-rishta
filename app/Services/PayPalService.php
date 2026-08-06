<?php

namespace App\Services;

use App\OnlinePackage;
use App\Payment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private function baseUrl(): string
    {
        return Config::get('services.paypal.sandbox', true)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * Get OAuth2 access token. Returns null and logs reason if credentials missing or API fails.
     */
    private function getAccessToken(): ?string
    {
        $clientId = Config::get('services.paypal.client_id');
        $secret = Config::get('services.paypal.secret');
        if (empty($clientId) || empty($secret)) {
            Log::warning('PayPal: Missing credentials. Set PAYPAL_CLIENT_ID and PAYPAL_SECRET in .env');
            return null;
        }

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            Log::warning('PayPal OAuth failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'base_url' => $this->baseUrl(),
            ]);
            return null;
        }

        $data = $response->json();
        return $data['access_token'] ?? null;
    }

    /**
     * Whether PayPal is configured (has client_id and secret).
     */
    public function isConfigured(): bool
    {
        $clientId = Config::get('services.paypal.client_id');
        $secret = Config::get('services.paypal.secret');
        return !empty($clientId) && !empty($secret);
    }

    /**
     * Create a PayPal order and return order ID and approval URL for redirect.
     *
     * @return array{order_id: string, approval_url: string}|null
     */
    public function createOrder(Payment $payment, OnlinePackage $package): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $currency = strtoupper((string)Config::get('services.paypal.currency', 'USD'));
        $value = number_format((float)$payment->amount, 2, '.', '');

        // PayPal redirects to return_url and appends token=ORDER_ID
        $successUrl = route('paypal.success', ['reference' => $payment->reference]);
        $cancelUrl = route('paypal.cancel', ['reference' => $payment->reference]);

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $payment->reference,
                    'description' => $package->name,
                    'custom_id' => $payment->reference,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $value,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'brand_name' => Config::get('app.name', 'Urgent Rishta'),
                'user_action' => 'PAY_NOW',
            ],
        ];

        $response = Http::withToken($token)
            ->post($this->baseUrl() . '/v2/checkout/orders', $payload);

        if (!$response->successful()) {
            Log::warning('PayPal create order failed', [
                'reference' => $payment->reference,
                'status' => $response->status(),
                'body' => $response->body(),
                'payload_amount' => $value,
                'currency' => $currency,
            ]);
            return null;
        }

        $data = $response->json();
        $orderId = $data['id'] ?? null;
        $approvalUrl = null;
        foreach ($data['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approvalUrl = $link['href'] ?? null;
                break;
            }
        }

        if (!$orderId || !$approvalUrl) {
            return null;
        }

        return [
            'order_id' => $orderId,
            'approval_url' => $approvalUrl,
        ];
    }

    /**
     * Capture a PayPal order after user approval. Returns true if captured successfully.
     */
    public function captureOrder(string $orderId): bool
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $response = Http::withToken($token)
            ->post($this->baseUrl() . '/v2/checkout/orders/' . $orderId . '/capture', []);

        if (!$response->successful()) {
            Log::warning('PayPal capture failed', ['order_id' => $orderId, 'status' => $response->status(), 'body' => $response->body()]);
            return false;
        }

        $data = $response->json();
        $status = $data['status'] ?? '';
        return strtoupper($status) === 'COMPLETED';
    }
}
