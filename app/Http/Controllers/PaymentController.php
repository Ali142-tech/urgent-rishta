<?php

namespace App\Http\Controllers;

use App\OnlinePackage;
use App\Payment;
use App\Services\StripeService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['stripeWebhook']);
    }

    /**
     * Start checkout for an online package (Stripe Checkout).
     * User cannot subscribe to another online package until the current one expires.
     */
    public function startOnlinePackageCheckout($packageId)
    {
        /** @var User $user */
        $user = User::retrieveUserObject();

        if ($user->hasActiveOnlinePackage()) {
            $exp = $user->online_package_expires_at;
            $expFormatted = $exp ? (\Illuminate\Support\Carbon::parse($exp)->format('j M Y')) : '';
            Session::flash('message', 'warning|You already have an active online subscription.'
                . ($expFormatted ? ' It is valid until ' . $expFormatted . '. You can subscribe again after it expires.' : ' You can subscribe again after it expires.'));
            return redirect('packages');
        }

        $package = OnlinePackage::where('is_active', true)->findOrFail($packageId);
        $meta = $package->meta();

        $amount = isset($meta['price']) ? (float) $meta['price'] : 0.0;
        if ($amount <= 0) {
            abort(500, 'Package price is not configured.');
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'online_package_id' => $package->id,
            'gateway' => 'stripe',
            'reference' => (string) Str::uuid(),
            'currency' => (string) ($meta['currency'] ?? 'USD'),
            'amount' => $amount,
            'status' => 'initiated',
        ]);

        $stripe = new StripeService();
        $session = $stripe->createCheckoutSession($payment, $package);

        $payment->gateway_txid = $session->id;
        $payment->save();

        return redirect()->away($session->url);
    }

    public function stripeSuccess(Request $request, $reference)
    {
        $payment = Payment::where('reference', $reference)->first();
        if (! $payment) {
            Session::flash('message', 'danger|Payment not found.');
            return redirect('home');
        }

        if ($payment->status !== 'paid') {
            $this->tryActivateFromStripeSession($payment, $request->query('session_id'));
        }

        $payment->refresh();
        if ($payment->status === 'paid') {
            Session::flash('message', 'success|Payment successful. Your package is now active. You can search Soul Mates.');
        } else {
            Session::flash('message', 'info|Payment received. Your package will be activated shortly.');
        }

        return redirect('home');
    }

    /**
     * When user returns from Stripe, activate package from Checkout Session if payment succeeded.
     */
    private function tryActivateFromStripeSession(Payment $payment, ?string $sessionId): void
    {
        if (empty($sessionId) || empty(Config::get('services.stripe.secret'))) {
            return;
        }
        try {
            $stripe = new \Stripe\StripeClient(Config::get('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent']]);
            if (($session->payment_status ?? '') !== 'paid') {
                return;
            }
            $payment->status = 'paid';
            $payment->paid_at = now();
            $payment->save();
            $this->activatePaidOnlinePackage($payment);
        } catch (\Throwable $e) {
            Log::warning('Stripe success-page activation failed', ['reference' => $payment->reference, 'error' => $e->getMessage()]);
        }
    }

    public function stripeCancel(Request $request, $reference)
    {
        $payment = Payment::where('reference', $reference)->first();
        if ($payment && $payment->status === 'initiated') {
            $payment->status = 'cancelled';
            $payment->save();
        }

        Session::flash('message', 'warning|Payment cancelled.');
        return redirect('packages');
    }

    /**
     * Stripe webhook endpoint.
     */
    public function stripeWebhook(Request $request)
    {
        $webhookSecret = (string) Config::get('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature', '');

        try {
            $event = ! empty($webhookSecret)
                ? Webhook::constructEvent($payload, $sigHeader, $webhookSecret)
                : json_decode($payload, false);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if (($event->type ?? '') === 'checkout.session.completed') {
            $session = $event->data->object ?? null;
            $reference = (string) ($session->client_reference_id ?? ($session->metadata->payment_reference ?? ''));

            if (! empty($reference)) {
                $payment = Payment::where('reference', $reference)->first();
                if ($payment && $payment->status !== 'paid') {
                    $payment->status = 'paid';
                    $payment->paid_at = now();
                    $payment->gateway_payload = $payload;
                    $payment->gateway_txid = (string) ($session->payment_intent ?? $session->id ?? $payment->gateway_txid);
                    $payment->save();

                    $this->activatePaidOnlinePackage($payment);
                }
            }
        }

        return response('ok', 200);
    }

    /**
     * Activate the purchased online package for the user.
     */
    private function activatePaidOnlinePackage(Payment $payment): void
    {
        $user = User::find($payment->user_id);
        $package = OnlinePackage::find($payment->online_package_id);
        if (! $user || ! $package) {
            return;
        }

        $user->activateOnlinePackage($package);
        User::retrieveUserObject($user->dataid, true);
    }
}
