<?php

namespace App\Services;

use App\Jobs\SendInactivityReminderEmailJob;
use App\User;

/**
 * Eligibility: active, non-admin members with an email, who last logged in
 * 7+ days ago (or NEVER logged in and registered 7+ days ago), and who
 * haven't already been sent this reminder for their CURRENT inactive streak.
 *
 * "Current streak" is what last_inactivity_reminder_sent_at vs last_login_at
 * captures: if we already reminded them and they haven't logged in since,
 * don't remind again every day forever — but if they DID log in again after
 * that reminder and have since gone quiet for another 7 days, it's a new
 * streak and they're eligible again.
 */
class InactivityReminderService
{
    public const INACTIVE_AFTER_DAYS = 7;

    public function eligibleUsersQuery()
    {
        $cutoff = now()->subDays(self::INACTIVE_AFTER_DAYS);

        return User::where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function ($q) use ($cutoff) {
                $q->where('last_login_at', '<=', $cutoff)
                  ->orWhere(function ($q2) use ($cutoff) {
                      $q2->whereNull('last_login_at')->where('created_at', '<=', $cutoff);
                  });
            })
            ->where(function ($q) {
                $q->whereNull('last_inactivity_reminder_sent_at')
                  ->orWhereColumn('last_inactivity_reminder_sent_at', '<', 'last_login_at');
            });
    }

    /** Dispatches jobs for every currently-eligible user, chunked so the whole audience is never loaded into memory at once. Returns how many were dispatched. */
    public function dispatchEligible(): int
    {
        $dispatched = 0;

        $this->eligibleUsersQuery()->orderBy('users.id')->chunkById(500, function ($users) use (&$dispatched) {
            foreach ($users as $user) {
                $user->forceFill(['last_inactivity_reminder_sent_at' => now()])->saveQuietly();

                SendInactivityReminderEmailJob::dispatch(
                    $user->id,
                    $user->email,
                    $user->first_name ?: 'there'
                );

                $dispatched++;
            }
        }, 'users.id', 'id');

        return $dispatched;
    }
}
