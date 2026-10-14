<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Website Upgrade Brief §5 "Mandatory photo policy" — a brand new account is
 * signed in (RegisterController@complete) *before* its required photos are
 * uploaded, then sent to the photos-required gate. Nothing was stopping that
 * already-authenticated session from simply navigating to any other page
 * (nav menu, back button, a typed URL, ...) and skipping the gate entirely.
 *
 * This runs on every web request and bounces anyone whose
 * photo_verification_status is still 'pending' (freshly registered, photos
 * not uploaded yet — ProfileController@mustUploadPhotos flips this to a
 * logged-out "awaiting review" state the moment the requirement is actually
 * satisfied) or 'resubmit' (previously rejected, reopened for one more try)
 * back to the gate, no matter what URL they were trying to reach.
 */
class EnsurePhotosUploaded {
    /**
     * Routes that must stay reachable even while the gate is active: the
     * gate page itself and the endpoints it depends on, logging out, and
     * Laravel's own email-verification routes (so that flow — which can
     * also land an authenticated-but-unverified user on shared pages —
     * never deadlocks against this one).
     */
    private const EXEMPT_ROUTE_NAMES = [
        'member.photos.required',
        'member.photos.required.status',
        'member.photos.selfie',
        'logout',
        'verification.notice',
        'verification.verify',
        'verification.resend',
    ];

    public function handle(Request $request, Closure $next): Response {
        if (Auth::guest()) {
            return $next($request);
        }

        // Admin accounts are unrelated to this member-facing gate.
        if ($request->is('admin*')) {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();
        if ($routeName && in_array($routeName, self::EXEMPT_ROUTE_NAMES, true)) {
            return $next($request);
        }

        // Shared upload endpoint (photo edit page and the gate page both
        // post here) — not named, so match by path.
        if ($request->is('member/profile/update/images/upload')) {
            return $next($request);
        }

        $user = User::retrieveUserObject();
        if ($user && in_array($user->photo_verification_status, ['pending', 'resubmit'], true)) {
            Session::put('photos_gate_redirect', $request->fullUrl());
            return redirect()->route('member.photos.required');
        }

        return $next($request);
    }
}
