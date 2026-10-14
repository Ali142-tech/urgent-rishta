<?php

namespace App\Services;

/** Partner allowlist (config/partners.php) and the outgoing referral URL (existing query and #fragment kept, UTM tags set once). */
class PartnerLinkService
{
    /** @return array<string, array{name:string,url:string}> */
    public static function all(): array
    {
        return (array) config('partners.links', []);
    }

    /** One allowlisted partner by key, or null — the only way a destination is ever chosen. */
    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    public static function name(string $key): string
    {
        return self::find($key)['name'] ?? $key;
    }

    /**
     * Adds utm_source / utm_medium to $url. Other query parameters and the #fragment stay, "?" or "&" is chosen correctly,
     * the two UTM tags are replaced rather than repeated (so running a URL through this again gives the same URL), and an
     * empty path becomes "/" (https://example.com -> https://example.com/?utm_source=...).
     */
    public static function withUtm(string $url): string
    {
        $fragment = '';
        if (($hash = strpos($url, '#')) !== false) {
            $fragment = substr($url, $hash);
            $url = substr($url, 0, $hash);
        }
        $query = '';
        if (($mark = strpos($url, '?')) !== false) {
            $query = substr($url, $mark + 1);
            $url = substr($url, 0, $mark);
        }
        if ((parse_url($url, PHP_URL_PATH) ?? '') === '') {
            $url .= '/';
        }
        parse_str($query, $params);
        $params['utm_source'] = config('partners.referral.source', 'urgentrishta');
        $params['utm_medium'] = config('partners.referral.medium', 'partner');

        return $url . '?' . http_build_query($params) . $fragment;
    }
}
