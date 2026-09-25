<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client requirement (Sep 2026): the Member Dashboard and Team Dashboard
 * must stay fully separate — a regular self-registered member must never
 * reach the Team Dashboard (already enforced the other direction by
 * EnsureUserIsTeamMember via TeamController::__construct()'s 'team_member'
 * middleware), and a team member must never reach the Member Dashboard.
 * This is that second half.
 *
 * Runs globally (like EnsurePhotosUploaded) since member/* routes aren't
 * behind a single controller-level middleware the way TeamController's are.
 * Admins pass through unconditionally — same reasoning as
 * EnsureUserIsTeamMember, an admin previewing the member area isn't the
 * scenario either gate exists for.
 */
class EnsureTeamMembersUseTeamDashboard
{
    /**
     * public/app.js polls this unconditionally for the notification bell on
     * every dashboard, including the Team Dashboard — must stay reachable
     * or team members lose notifications entirely.
     */
    private const EXEMPT_PATHS = [
        'member/profile/notifications/refresh',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        $isTeamMember = (int) $user->is_team_member === 1 && $user->team_member_status === 'active';
        if (!$isTeamMember || !$request->is('member/*')) {
            return $next($request);
        }

        foreach (self::EXEMPT_PATHS as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        return redirect()->route('team.dashboard');
    }
}
