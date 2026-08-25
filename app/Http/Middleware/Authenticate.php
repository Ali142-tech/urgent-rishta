<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // A guest submitting the search form gets sent here to log in first. Stash the
        // submitted filters (own session key, not Laravel's url.intended — that gets
        // overwritten right after this by Redirector::guest(), which for a POST request
        // only remembers the previous page, not the form data) so the search can resume
        // automatically once they're signed in. See LoginController::finishLogin().
        if (!$request->expectsJson() && $request->routeIs('searchresults')) {
            session(['pending_search' => $request->except(['_token'])]);
        }

        return $request->expectsJson() ? null : route('login');
    }
}
