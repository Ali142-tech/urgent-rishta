<?php

namespace App\Http\Middleware;

use App\Services\ProfileWizard;
use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * A member who registered through the site must fill in the "Complete your profile" wizard (100%) before reaching the
 * dashboard or anything else. Only accounts flagged at registration (users.must_complete_profile) are held; the flag clears
 * itself, for good, the moment the profile is complete. Runs after the photo gate, so photos come first.
 */
class EnsureProfileComplete
{
    /** Pages the wizard itself needs (and logging out) must stay reachable while it is in force. */
    private const EXEMPT_ROUTE_NAMES = [
        'member.complete', 'member.complete.save', 'member.complete.finish', 'member.complete.areas',
        'member.photos.required', 'member.photos.required.status', 'member.photos.selfie',
        'logout', 'verification.notice', 'verification.verify', 'verification.resend',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // the public marketing pages stay open to everyone; the wizard guards the member area
        if (Auth::guest() || $request->is('/', 'home', 'admin*', 'team*', 'states/*', 'cities/*', 'castes/*', 'communities/*', 'member/profile/update/images/upload',
            'packages*', 'stories', 'gallery', 'partners', 'faqs', 'tandc', 'privacy', 'p/*', 'contact-us*', 'go/*', 'about*')) {
            return $next($request);
        }
        $routeName = optional($request->route())->getName();
        if ($routeName && in_array($routeName, self::EXEMPT_ROUTE_NAMES, true)) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user || empty($user->must_complete_profile) || $user->admin == 1) {
            return $next($request);
        }

        $wizard = app(ProfileWizard::class);
        if ($wizard->complete($user)) {
            // finished (possibly through the dashboard's own forms): release the hold for good
            $user->forceFill(['must_complete_profile' => false, 'profile_completed_at' => now()])->save();
            User::retrieveUserObject($user->dataid, true);
            return $next($request);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['code' => '403', 'message' => 'danger|Please complete your profile first.', 'redirect' => route('member.complete')], 403);
        }
        return redirect()->route('member.complete');
    }
}
