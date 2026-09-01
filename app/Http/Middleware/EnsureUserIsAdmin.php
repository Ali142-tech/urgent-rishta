<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Website Upgrade Brief §12 P0: "Separate admin authentication/authorization
 * from users." Until now, every admin/* route only required the 'auth'
 * middleware — any logged-in member could reach admin actions server-side
 * (the admin nav link was merely hidden client-side for non-admins). This
 * middleware must run AFTER 'auth' on any admin-only route so a guest is
 * redirected to login first, rather than seeing a raw 403.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || (int) $user->admin !== 1) {
            Log::warning('Blocked non-admin access attempt to ' . $request->path() . ' by ' .
                ($user ? $user->dataid . ' (' . $user->email . ')' : 'a guest'));
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
