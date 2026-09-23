<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Fires on every successful login regardless of method (password,
        // OTP, Google OAuth) — used to detect "inactive 7+ days" for the
        // automatic re-engagement reminder (see App\Services\InactivityReminderService).
        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            // last_login_at only ever holds the MOST RECENT login (overwritten
            // every time) — this is what turns that into real login history,
            // queryable in the admin Audit Log without touching any of the
            // three separate login controllers (password/OTP/Google).
            \App\AuditLog::record($event->user, 'login');
        });
    }
}
