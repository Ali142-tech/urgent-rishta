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

        // Temporary access: finished -> out for good; and only the shared proposals can be browsed (no photos / contacts / sharing / editing)
        if ($member->is_temporary) {
            if ($reason = $member->tempExpiredReason()) {
                Auth::guard('team')->logout();
                $request->session()->invalidate();
                session()->flash('message', 'danger|' . $reason . ' Please ask the admin for new access.');
                return $request->expectsJson() ? response()->json(['message' => 'expired'], 401) : redirect()->route('team.login');
            }
            $allowed = ['team.proposals.search', 'team.proposals.view', 'team.proposals.file', 'team.logout'];
            if (!in_array($request->route() ? $request->route()->getName() : null, $allowed, true)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Not available with temporary access.'], 403);
                }
                session()->flash('message', 'warning|With temporary access you can only look through the shared proposals.');
                return redirect()->route('team.proposals.search');
            }
            view()->share('tempSecondsLeft', $member->tempSecondsLeft());
        }

        return $next($request);
    }
}
