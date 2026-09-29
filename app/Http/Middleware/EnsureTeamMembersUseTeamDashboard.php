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

        // "View file" on a Team Dashboard card (member-card.blade.php /
        // search-result-card.blade.php) links straight to
        // member/profile/{dataid} — ProfileController::profile() already
        // has its own logic letting a team member view any proposal/member
        // this way, but this middleware was catching the request first and
        // bouncing it back to the Team Dashboard before it ever got there.
        // Only the bare "view someone's profile by dataid" path is let
        // through; member/profile with no id (their own Member Dashboard
        // home) and its own-account sub-pages (pictures/preferences/
        // password/account) stay blocked below.
        $segments = explode('/', trim($request->path(), '/'));
        $isViewingAProfileByDataid = count($segments) === 3
            && $segments[0] === 'member'
            && $segments[1] === 'profile'
            && !in_array($segments[2], ['pictures', 'preferences', 'password', 'notifications', 'account'], true);
        if ($isViewingAProfileByDataid) {
            return $next($request);
        }

        return redirect()->route('team.dashboard');
    }
}
