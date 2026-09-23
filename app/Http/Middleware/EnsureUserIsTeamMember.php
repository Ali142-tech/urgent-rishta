<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the Team Dashboard / manual proposal-entry routes to users an admin
 * has promoted via AdminController::toggleTeamMember(). Structural mirror of
 * EnsureUserIsAdmin — same "run after 'auth' so a guest hits login, not a
 * raw 403" reasoning applies here too.
 *
 * Admins also pass through unconditionally: TeamController's per-proposal
 * authorization (authorizeProposalOwner(), etc.) already treats admins as
 * allowed on every proposal regardless of who added it, so admins get full
 * Team Dashboard access too, without needing to also be flagged as a team
 * member.
 */
class EnsureUserIsTeamMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $isTeamMember = $user && (int) $user->is_team_member === 1 && $user->team_member_status === 'active';
        $isAdmin = $user && $user->isAdmin();

        if (!$isTeamMember && !$isAdmin) {
            Log::warning('Blocked non-team-member access attempt to ' . $request->path() . ' by ' .
                ($user ? $user->dataid . ' (' . $user->email . ')' : 'a guest'));
            abort(403, 'Team member access required.');
        }

        return $next($request);
    }
}
