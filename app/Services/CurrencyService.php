<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shows prices in the visitor's local currency based on their IP's country.
 *   - Pakistan (PK): shown as-is in PKR.
 *   - Everywhere else (GB/AE get their own currency, everyone else falls
 *     back to USD): a flat, client-specified foreign amount — NOT a
 *     converted PKR figure — see displayFlatOutsidePakistan().
 * The geo-IP lookup is a free, keyless third-party API — if it's slow,
 * down, or the visitor's IP can't be resolved (e.g. localhost during
 * testing), this must never break the price display, so every failure
 * path falls back to plain PKR.
 */
class CurrencyService
{
    /** @var array<string, array{code:string, symbol:string}> */
    protected const CURRENCIES = [
        'PK' => ['code' => 'PKR', 'symbol' => 'Rs. '],
        'GB' => ['code' => 'GBP', 'symbol' => '£'],
        'AE' => ['code' => 'AED', 'symbol' => 'AED '],
    ];

    /** "Any other country" default, per client instruction (US included). */
    protected const DEFAULT_CURRENCY = ['code' => 'USD', 'symbol' => '$'];

    /**
     * Strip "Rs.", commas, spaces etc. from an admin-typed fee string (old
     * free-text like "Rs. 50,000" or plain-numeric "50000") and return the
     * underlying PKR amount. Returns null if nothing numeric was found.
     */
    public static function parseAmount(?string $raw): ?float
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $clean = str_ireplace(['rs.', 'rs', 'pkr', '₨', ','], '', $raw);
        if (preg_match('/\d+(\.\d+)?/', $clean, $matches)) {
            return (float) $matches[0];
        }
        return null;
    }

    /**
     * Detect the visitor's ISO-3166 country code from their IP, cached per
     * IP for a day so repeat visits don't re-hit the external API. Falls
     * back to 'PK' for local/private IPs or any lookup failure.
     */
    public function detectCountry(Request $request): string
    {
        $ip = $request->ip();

        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'PK';
        }

        return Cache::remember('geoip_country_' . $ip, now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,countryCode',
                ]);
                if ($response->ok()) {
                    $data = $response->json();
                    if (($data['status'] ?? null) === 'success' && !empty($data['countryCode'])) {
                        return strtoupper($data['countryCode']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CurrencyService: geo-IP lookup failed for ' . $ip . ': ' . $e->getMessage());
            }
            return 'PK';
        });
    }

    /** The currency config (code/symbol) for a given country code. */
    protected function currencyFor(string $countryCode): array
    {
        return self::CURRENCIES[$countryCode] ?? self::DEFAULT_CURRENCY;
    }

    /**
     * ISO-3166 country => ISO-4217 currency, covering our visitor base
     * (Pakistan, India, the Gulf, UK/US/EU, and other common regions).
     * Anything not listed falls back to USD, same default philosophy as
     * the flat-rate helper above.
     */
    protected const COUNTRY_CURRENCY = [
        'PK' => 'PKR', 'IN' => 'INR', 'BD' => 'BDT', 'GB' => 'GBP', 'US' => 'USD',
        'CA' => 'CAD', 'AU' => 'AUD', 'NZ' => 'NZD',
        'AE' => 'AED', 'SA' => 'SAR', 'QA' => 'QAR', 'KW' => 'KWD', 'OM' => 'OMR', 'BH' => 'BHD',
        'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR', 'IE' => 'EUR',
        'PT' => 'EUR', 'BE' => 'EUR', 'AT' => 'EUR', 'GR' => 'EUR', 'FI' => 'EUR', 'LU' => 'EUR',
        'MT' => 'EUR', 'CY' => 'EUR', 'SK' => 'EUR', 'SI' => 'EUR', 'EE' => 'EUR', 'LV' => 'EUR', 'LT' => 'EUR',
        'ZA' => 'ZAR', 'SG' => 'SGD', 'MY' => 'MYR', 'TR' => 'TRY', 'CH' => 'CHF',
        'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK', 'JP' => 'JPY', 'CN' => 'CNY', 'HK' => 'HKD',
        'TH' => 'THB', 'PH' => 'PHP', 'ID' => 'IDR', 'VN' => 'VND', 'KR' => 'KRW',
        'BR' => 'BRL', 'MX' => 'MXN', 'RU' => 'RUB', 'EG' => 'EGP', 'NG' => 'NGN', 'KE' => 'KES',
    ];

    /** "Any other country" default target currency for live conversion. */
    protected const DEFAULT_TARGET_CURRENCY = 'USD';

    /** The visitor's local currency code, based on their detected country. */
    public function localCurrencyFor(Request $request): string
    {
        $country = $this->detectCountry($request);
        return self::COUNTRY_CURRENCY[$country] ?? self::DEFAULT_TARGET_CURRENCY;
    }

    /**
     * Convert a real listed amount (e.g. a GBP package price) into the
     * visitor's local currency using a live exchange rate — meant to be
     * shown as a secondary "≈ ..." line under the actual price, never
     * replacing it. This is a genuine proportional conversion (unlike
     * displayFlatOutsidePakistan() above, which is deliberately flat).
     *
     * Returns null when the visitor's local currency already matches
     * $fromCurrency (nothing to convert) or the live-rate lookup fails —
     * a broken/slow rate API must never break the price display.
     *
     * @return array{currency:string, rate:float, converted:float}|null
     */
    public function convert(float $amount, string $fromCurrency, Request $request): ?array
    {
        $fromCurrency = strtoupper($fromCurrency);
        $target = $this->localCurrencyFor($request);

        if ($target === $fromCurrency) {
            return null;
        }

        $rate = $this->exchangeRate($fromCurrency, $target);
        if ($rate === null) {
            return null;
        }

        return [
            'currency' => $target,
            'rate' => $rate,
            'converted' => $amount * $rate,
        ];
    }

    /**
     * Live exchange rate table for $from, cached 12h on success. A failed
     * lookup is cached for only 5 minutes so a temporary outage doesn't
     * hammer the free rate API on every page view, but also self-heals fast.
     */
    protected function exchangeRate(string $from, string $to): ?float
    {
        $cacheKey = 'fx_rates_' . $from;
        $rates = Cache::get($cacheKey);

        if ($rates === null) {
            $rates = [];
            try {
                $response = Http::timeout(3)->get("https://open.er-api.com/v6/latest/{$from}");
                if ($response->ok()) {
                    $data = $response->json();
                    if (($data['result'] ?? null) === 'success' && !empty($data['rates'])) {
                        $rates = $data['rates'];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CurrencyService: exchange-rate lookup failed for ' . $from . ': ' . $e->getMessage());
            }
            Cache::put($cacheKey, $rates, now()->addMinutes(empty($rates) ? 5 : 720));
        }

        return isset($rates[$to]) ? (float) $rates[$to] : null;
    }

    /**
     * Pakistan sees the real PKR amount as-is; every other country sees a
     * flat, client-set amount in their own currency symbol (GBP for UK, AED
     * for UAE, USD for everyone else) — this is deliberately NOT a
     * converted/proportional figure, per client instruction (e.g. a
     * Rs. 2,000 consultation fee shows flat as "$10" / "£10" abroad).
     */
    public function displayFlatOutsidePakistan(float $pakistanAmount, float $flatForeignAmount, Request $request): string
    {
        $country = $this->detectCountry($request);
        if ($country === 'PK') {
            return self::CURRENCIES['PK']['symbol'] . number_format((int) round($pakistanAmount));
        }
        $currency = $this->currencyFor($country);
        return $currency['symbol'] . number_format((int) round($flatForeignAmount));
    }
}
