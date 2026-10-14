<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * The base Laravel middleware calls $request->host() here purely to auto-detect
     * Forge/Vapor hosting (str_ends_with($request->host(), '.on-forge.com'/'.on-vapor.com'))
     * — this app is on regular shared hosting, so that check is never relevant. On at
     * least one shared host running this app, that host() call trips a PCRE compatibility
     * bug in the server's PHP build ("preg_match(): Compilation failed: quantifier does
     * not follow a repeatable item at offset 0"), which crashes EVERY request before
     * routing even runs. Skipping the irrelevant Forge/Vapor branch avoids that call.
     * This is a server/PHP-build issue, not an application bug — if similar
     * "preg_match(): Compilation failed" errors show up elsewhere, ask the host to
     * check the PCRE extension/library on that PHP version.
     */
    protected function setTrustedProxyIpAddresses(Request $request)
    {
        $trustedIps = $this->proxies() ?: config('trustedproxy.proxies');

        if ($trustedIps === '*' || $trustedIps === '**') {
            $this->setTrustedProxyIpAddressesToTheCallingIp($request);
            return;
        }

        $trustedIps = is_string($trustedIps)
            ? array_map('trim', explode(',', $trustedIps))
            : $trustedIps;

        if (is_array($trustedIps)) {
            $this->setTrustedProxyIpAddressesToSpecificIps($request, $trustedIps);
        }
    }
}
