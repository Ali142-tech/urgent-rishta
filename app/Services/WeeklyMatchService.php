<?php

namespace App\Services;

use App\Jobs\SendWeeklyMatchEmailJob;
use App\User;

/**
 * Eligibility: active, non-admin, self-registered members (never team-added
 * proposals — those are login-less shell records, often without even an
 * email address, see TeamController::store()) with an email, who either
 * never got a weekly match email or last got one 7+ days ago.
 *
 * Whether they actually HAVE any matches to show is decided later, inside
 * SendWeeklyMatchEmailJob (via User::getRecommendedMatches()) — this class
 * only decides who's due to be CHECKED, not who has real matches, since
 * that requires the per-user Partner Preferences query this class has no
 * reason to run twice.
 */
class WeeklyMatchService
{
    public const RESEND_AFTER_DAYS = 7;

    public function dueUsersQuery()
    {
        $cutoff = now()->subDays(self::RESEND_AFTER_DAYS);

        return User::where('active', 1)
            ->where('admin', 0)
            ->whereNull('added_by')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereIn('gender', ['male', 'female'])
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_weekly_match_email_sent_at')
                  ->orWhere('last_weekly_match_email_sent_at', '<=', $cutoff);
            });
    }

    /** Dispatches jobs for every currently-due user, chunked so the whole audience is never loaded into memory at once. Returns how many were dispatched. */
    public function dispatchDue(): int
    {
        $dispatched = 0;

        $this->dueUsersQuery()->orderBy('users.id')->chunkById(500, function ($users) use (&$dispatched) {
            foreach ($users as $user) {
                SendWeeklyMatchEmailJob::dispatch($user->id);
                $dispatched++;
            }
        }, 'users.id', 'id');

        return $dispatched;
    }
}
