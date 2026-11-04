<?php

namespace App\Jobs;

use App\Jobs\Concerns\ClassifiesMailFailures;
use App\Mail\WeeklyMatches;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends one member their personalized "Weekly Matches" email — 5 real
 * opposite-gender profiles from User::getRecommendedMatches() (the same
 * engine behind the live "Recommended Matches" page), never a fabricated
 * or curated substitute. Mirrors SendMatchPreviewEmailJob/
 * SendInactivityReminderEmailJob (same "campaigns" queue, rate limiter,
 * failure classification) — only the recipient's id is passed in (not the
 * User model itself, to avoid serializing it through the queue), and the
 * user + their matches are loaded fresh right before sending.
 *
 * last_weekly_match_email_sent_at is only updated AFTER a confirmed
 * successful send (client's explicit requirement) — WeeklyMatchService's
 * due-query doesn't mark anything at dispatch time, unlike its inactivity-
 * reminder sibling. This is intentionally stricter: it means a job stuck
 * retrying for multiple days could in theory get double-dispatched by the
 * next day's due-check, but with a max backoff around 2 hours and a daily
 * cron, that's very unlikely in practice.
 */
class SendWeeklyMatchEmailJob implements ShouldQueue
{
    use ClassifiesMailFailures, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const MATCHES_PER_EMAIL = 5;

    public $tries = 6;

    public $timeout = 60;

    public function __construct(public int $userId)
    {
        $this->onQueue('campaigns');
    }

    public function middleware(): array
    {
        return [new RateLimited('campaign-mail')];
    }

    /** 2m, 10m, 30m, 1h, 2h — same schedule as the other campaign jobs. */
    public function backoff(): array
    {
        return [120, 600, 1800, 3600, 7200];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user || empty($user->email)) {
            Log::warning("Weekly match email skipped for user #{$this->userId}: user not found or has no email.");
            return;
        }

        $profiles = $user->getRecommendedMatches(self::MATCHES_PER_EMAIL);

        if ($profiles->isEmpty()) {
            // No preferences saved, no active package, or genuinely nobody
            // eligible right now — skip rather than send an empty-looking
            // email. Not marked as sent, so it's naturally retried by
            // tomorrow's due-check once/if the user becomes eligible.
            Log::info("Weekly match email skipped for user #{$this->userId} ({$user->dataid}): no eligible matches found.");
            return;
        }

        Log::info("Weekly match email: user #{$this->userId} ({$user->dataid}) has " . $profiles->count() . ' eligible match(es).');

        $cards = $profiles->map(function ($profile) {
            $age = !empty($profile->birthday) ? \Carbon\Carbon::parse($profile->birthday)->age : null;
            $cityCountry = implode(', ', array_filter([$profile->lbl_city ?? null, $profile->lbl_con_of_residence ?? null]));

            return (object) [
                'dataid' => $profile->dataid,
                'name_line' => $profile->first_name . ($age ? ", {$age} yrs" : ''),
                'location_line' => implode(' • ', array_filter([$profile->height ?? null, $cityCountry])),
                'profession' => $profile->profession ?? null,
                // Respects the profile's own photo_visibility setting (visible/
                // blurred/hidden) — same as the real Recommended Matches page,
                // not force-blurred like the guest-facing curated teaser email.
                'image_url' => 'https://urgentrishta.co' . $profile->getProfileImage(true),
                'profile_url' => 'https://urgentrishta.co/member/profile/' . $profile->dataid,
            ];
        });

        try {
            Mail::to($user->email)->send(new WeeklyMatches($user->first_name ?: 'there', $cards));
            $user->forceFill(['last_weekly_match_email_sent_at' => now()])->saveQuietly();
            Log::info("Weekly match email sent to user #{$this->userId} ({$user->dataid}).");
        } catch (Throwable $e) {
            if ($this->isPermanentMailFailure($e)) {
                Log::warning("Weekly match email permanently failed for user #{$this->userId} ({$user->dataid}): " . $e->getMessage());
                $this->fail($e);
                return;
            }

            Log::info("Weekly match email temporarily failed for user #{$this->userId} ({$user->dataid}): " . $e->getMessage());
            throw $e;
        }
    }
}
