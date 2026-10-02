<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the Team Dashboard. Team members sign in on their own guard
 * (`team`, /team/login); this makes that the default guard for the request
 * (so auth()->user() is the TeamMember everywhere in the team screens) and
 * turns away anyone who is not an approved, active team member — including
 * one an admin has suspended since they signed in.
 */
class EnsureUserIsTeamMember
{
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('team');
        $member = Auth::guard('team')->user();

        // A signed-in admin has a team account too (created on demand) — no second login needed.
        if (!$member) {
            $admin = Auth::guard('web')->user();
            if ($admin && ($admin->admin ?? 0) == 1) {
                $member = \App\TeamMember::provisionForAdmin($admin);
                Auth::guard('team')->login($member);
            }
        }

        if (!$member) {
            return redirect()->guest(route('team.login'));
        }

        if (!$member->isActiveMember()) {
            Log::warning('Blocked inactive team member ' . $member->dataid . ' from ' . $request->path());
            Auth::guard('team')->logout();
            session()->flash('message', 'warning|Your account is not active. Please contact support at 0304-0227000.');
            return redirect()->route('team.login');
        }

        return $next($request);
    }
}
