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
    /** Symbols for the "≈" income line; any currency not listed is shown with its ISO code in front. */
    protected const SYMBOLS = [
        'USD' => '$', 'GBP' => '£', 'EUR' => '€', 'CAD' => 'CA$', 'AUD' => 'A$', 'NZD' => 'NZ$', 'INR' => '₹',
        'SAR' => 'SAR ', 'AED' => 'AED ', 'QAR' => 'QAR ', 'KWD' => 'KWD ', 'OMR' => 'OMR ', 'BHD' => 'BHD ',
    ];

    /** Currencies offered next to the "Monthly income range" dropdown: code => label. */
    public const INCOME_CURRENCIES = [
        'PKR' => 'Pakistan — PKR', 'GBP' => 'United Kingdom — GBP', 'USD' => 'USA — USD', 'AED' => 'UAE — AED',
        'SAR' => 'Saudi Arabia — SAR', 'CAD' => 'Canada — CAD', 'AUD' => 'Australia — AUD', 'EUR' => 'Europe — EUR',
    ];

    /**
     * Data for the income currency picker: the choices, the currency to start on (the visitor's own, if offered, else PKR
     * in Pakistan / USD elsewhere), and a live PKR→currency rate for each. A currency whose rate can't be fetched is left out.
     *
     * @return array{default:string, currencies:array<string,array{label:string,symbol:string,rate:float}>}
     */
    public function incomeCurrencyOptions(Request $request): array
    {
        $local = $this->localCurrencyFor($request);
        $out = [];
        foreach (self::INCOME_CURRENCIES as $code => $label) {
            $rate = $code === 'PKR' ? 1.0 : $this->exchangeRate('PKR', $code);
            if ($rate === null) {
                continue;
            }
            $out[$code] = ['label' => $label, 'symbol' => $code === 'PKR' ? 'PKR ' : (self::SYMBOLS[$code] ?? ($code . ' ')), 'rate' => $rate];
        }
        $default = isset($out[$local]) ? $local : (isset($out['USD']) ? 'USD' : 'PKR');
        return ['default' => $default, 'currencies' => $out];
    }

    /**
     * For the "Monthly income range" answers, which are saved as PKR text ("PKR 50,000 – 100,000", "PKR Below 50,000",
     * "PKR Above 5,000,000"): the same range in the visitor's own currency, e.g. "≈ $180 – $360". Display only: the saved
     * value never changes. Null in Pakistan, for answers that are not a PKR range ("No Income / Student"), or when the live
     * rate is unavailable (the page then simply shows the PKR text).
     */
    public function approxIncomeRange(?string $range, Request $request): ?string
    {
        if ($range === null || stripos($range, 'PKR') === false) {
            return null;
        }
        preg_match_all('/\d[\d,]*/', $range, $m);
        $nums = array_map(fn ($n) => (float) str_replace(',', '', $n), $m[0]);
        if (empty($nums)) {
            return null;
        }
        $lo = $nums[0];
        $hi = $nums[1] ?? null;

        $first = $this->convert($lo, 'PKR', $request);
        if ($first === null) {
            return null;
        }
        $code = $first['currency'];
        $symbol = self::SYMBOLS[$code] ?? ($code . ' ');
        $rate = $first['rate'];
        $fmt = function (float $pkr) use ($rate, $symbol) {
            $v = $pkr * $rate;
            $step = $v >= 100000 ? 1000 : ($v >= 1000 ? 100 : 10);
            return $symbol . number_format((int) (round($v / $step) * $step));
        };

        if ($hi !== null) {
            return '≈ ' . $fmt($lo) . ' – ' . $fmt($hi);
        }
        return stripos($range, 'below') !== false || stripos($range, 'under') !== false
            ? '≈ below ' . $fmt($lo)
            : '≈ above ' . $fmt($lo);
    }
}
