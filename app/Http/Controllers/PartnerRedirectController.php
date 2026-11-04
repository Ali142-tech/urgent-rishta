<?php

namespace App\Http\Controllers;

use App\PartnerClick;
use App\Services\PartnerLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * /go/{partner} — the link behind every "Our Partners" card. It records the click (one row per real click) and then
 * redirects to the partner's website with the UrgentRishta referral tags (utm_source / utm_medium) added.
 *
 * The destination is never taken from the request: {partner} is only a key into the server-side allowlist
 * (config/partners.php), so this cannot be used as an open redirect. This tracks visits from this site to the partner —
 * not purchases or commission.
 */
class PartnerRedirectController extends Controller
{
    public function go(Request $request, string $partner)
    {
        $link = PartnerLinkService::find($partner);
        abort_unless($link, 404);

        $destination = PartnerLinkService::withUtm($link['url']);

        if ($this->isRealClick($request)) {
            $this->record($request, $partner, $link['name'], $destination);
        }

        // The click must never fail the visit: a recording problem is only logged.
        return redirect()->away($destination);
    }

    /** One real click per request: not HEAD requests, link prefetchers/previews or crawlers (they would inflate the numbers). */
    private function isRealClick(Request $request): bool
    {
        if ($request->isMethod('HEAD')) {
            return false;
        }
        foreach (['Purpose', 'Sec-Purpose', 'X-Moz', 'X-Purpose'] as $header) {
            if (preg_match('/prefetch|prerender|preview/i', (string) $request->header($header))) {
                return false;
            }
        }
        $agent = (string) $request->userAgent();

        return $agent !== '' && !preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|headless|monitor|curl|wget|python-requests/i', $agent);
    }

    private function record(Request $request, string $partner, string $partnerName, string $destination): void
    {
        try {
            $user = Auth::guard('web')->user();
            $teamMember = Auth::guard('team')->user();

            // Anonymous identifier: an HMAC of the session id (no IP address, no raw session id, no extra long-lived cookie).
            // It groups the clicks of one browser session, so "unique visitors" are approximate by design.
            $visitor = substr(hash_hmac('sha256', $request->session()->getId(), (string) config('app.key')), 0, 16);

            // A double-click or a repeated handler must not count twice: the same visitor + partner within 3 seconds is one click.
            if (!Cache::add('partner_click:' . $visitor . ':' . $partner, 1, 3)) {
                return;
            }

            PartnerClick::create([
                'partner_key' => $partner,
                'partner_name' => $partnerName,
                'user_id' => $user?->id,
                'team_member_id' => $teamMember?->id,
                'visitor_hash' => $visitor,
                'destination_url' => mb_substr($destination, 0, 500),
                'referral_source' => config('partners.referral.source', 'urgentrishta'),
                'utm_source' => config('partners.referral.source', 'urgentrishta'),
                'utm_medium' => config('partners.referral.medium', 'partner'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Partner click not recorded (' . $partner . '): ' . $e->getMessage());
        }
    }
}
