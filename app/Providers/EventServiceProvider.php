<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
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
    /** True while one login is mirroring itself onto the other guard (stops the two listeners calling each other). */
    private static bool $syncing = false;

    public function boot()
    {
        parent::boot();

        // Fires on every successful login regardless of method (password,
        // OTP, Google OAuth) — used to detect "inactive 7+ days" for the
        // automatic re-engagement reminder (see App\Services\InactivityReminderService).
        Event::listen(Login::class, function (Login $event) {
            $this->syncGuardsOnLogin($event);

            // Team members (own guard / table) have no last_login_at column and no audit actor.
            if (!($event->user instanceof \App\User)) {
                return;
            }
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            // last_login_at only ever holds the MOST RECENT login (overwritten
            // every time) — this is what turns that into real login history,
            // queryable in the admin Audit Log without touching any of the
            // three separate login controllers (password/OTP/Google).
            \App\AuditLog::record($event->user, 'login');
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (self::$syncing || !$event->user) {
                return;
            }
            // An admin is signed in on both sides (see syncGuardsOnLogin()), so signing out of one signs out of both.
            self::$syncing = true;
            try {
                if ($event->guard === 'team' && $event->user->isAdmin()) {
                    Auth::guard('web')->logout();
                } elseif ($event->guard === 'web' && ($event->user->admin ?? 0) == 1) {
                    Auth::guard('team')->logout();
                }
            } finally {
                self::$syncing = false;
            }
        });
    }

    /**
     * The member side (`users`, web guard) and the team side (`team_members`, team guard) are separate logins:
     *  - an ordinary person signing in on one is signed out of the other, so only the dashboard they chose is offered;
     *  - an ADMIN has an account on both sides (AdminTeamMemberSeeder), so signing in on either one opens the other
     *    too, and they can use the Member, Admin and Team dashboards without signing in again.
     */
    private function syncGuardsOnLogin(Login $event): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            if ($event->guard === 'team') {
                if ($event->user->isAdmin()) {
                    $admin = \App\User::where('email', $event->user->email)->where('admin', 1)->first();
                    if ($admin && !Auth::guard('web')->check()) {
                        Auth::guard('web')->login($admin);
                    }
                } else {
                    Auth::guard('web')->logout();
                }
            } elseif ($event->guard === 'web') {
                if (($event->user->admin ?? 0) == 1) {
                    if (!Auth::guard('team')->check()) {
                        Auth::guard('team')->login(\App\TeamMember::provisionForAdmin($event->user));
                    }
                } else {
                    Auth::guard('team')->logout();
                }
            }
        } finally {
            self::$syncing = false;
        }
    }
}
