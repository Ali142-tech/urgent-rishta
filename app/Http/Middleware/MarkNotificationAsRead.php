<?php

namespace App\Http\Middleware;

use App\Notifications;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationAsRead {
    public function handle(Request $request, Closure $next): Response {
        if ($request->has('read')) {
            $notification = Notifications::where('id', $request->read)->first();
            if (!empty($notification)) {
                $notification->read_at=now();
                $notification->save();
            }
        }
        return $next($request);
    }
}
