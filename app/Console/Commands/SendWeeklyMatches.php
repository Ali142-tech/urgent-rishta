<?php

namespace App\Console\Commands;

use App\Mail\WeeklyMatches;
use App\Services\WeeklyMatchService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Runs automatically once a day (see App\Console\Kernel::schedule) — finds
 * every member who is due (7+ days since their last "Weekly Matches"
 * email, or never sent one) and dispatches one queued job per member (see
 * App\Jobs\SendWeeklyMatchEmailJob). The command itself never sends mail
 * or touches thousands of rows in memory — see WeeklyMatchService::
 * dispatchDue()'s chunking.
 *
 *   php artisan matches:send-weekly --dry-run           # preview due count, nothing sent
 *   php artisan matches:send-weekly --test=SOMEDATAID   # real send to ONE real member, right now, ignoring the 7-day gate — does NOT touch last_weekly_match_email_sent_at, so it's safe to re-run repeatedly while testing
 *   php artisan matches:send-weekly                     # dispatch for everyone currently due
 */
class SendWeeklyMatches extends Command
{
    protected $signature = 'matches:send-weekly
                            {--dry-run : Show due-user count without sending anything}
                            {--test= : Send one real email to this user (by dataid) immediately, bypassing the 7-day gate. Does not update last_weekly_match_email_sent_at.}';

    protected $description = "Email every due member their personalized weekly match recommendations";

    public function handle(WeeklyMatchService $service)
    {
        if ($dataid = $this->option('test')) {
            return $this->sendTest($dataid);
        }

        $total = (clone $service->dueUsersQuery())->count();
        $this->info("Due for weekly match email: {$total}");
        Log::info("matches:send-weekly — {$total} user(s) due.");

        if ($this->option('dry-run')) {
            $this->line('--- DRY RUN — nothing sent. Sample recipients: ---');
            foreach ((clone $service->dueUsersQuery())->take(5)->get() as $u) {
                $this->line(" - {$u->dataid}  {$u->first_name} {$u->last_name}  <{$u->email}>  last sent: " . ($u->last_weekly_match_email_sent_at ?: 'never'));
            }
            return 0;
        }

        if ($total === 0) {
            $this->info('Nobody currently due.');
            return 0;
        }

        $dispatched = $service->dispatchDue();
        $this->info("Dispatched {$dispatched} weekly match email job(s).");
        Log::info("matches:send-weekly — dispatched {$dispatched} job(s).");
        return 0;
    }

    private function sendTest(string $dataid): int
    {
        $user = User::where('dataid', $dataid)->first();
        if (!$user) {
            $this->error("No user found with dataid: {$dataid}");
            return 1;
        }
        if (empty($user->email)) {
            $this->error("User {$dataid} ({$user->first_name}) has no email address.");
            return 1;
        }

        $this->info("Building matches for {$dataid} ({$user->first_name} {$user->last_name}, {$user->gender})...");
        $profiles = $user->getRecommendedMatches(\App\Jobs\SendWeeklyMatchEmailJob::MATCHES_PER_EMAIL);

        if ($profiles->isEmpty()) {
            $this->warn("No eligible matches found for this user right now — check they have an active package and saved Partner Preferences (User::hasPartnerPreferences()).");
            return 1;
        }

        $this->info("Found {$profiles->count()} eligible match(es): " . $profiles->pluck('dataid')->implode(', '));

        $cards = $profiles->map(function ($profile) {
            $age = !empty($profile->birthday) ? \Carbon\Carbon::parse($profile->birthday)->age : null;
            $cityCountry = implode(', ', array_filter([$profile->lbl_city ?? null, $profile->lbl_con_of_residence ?? null]));

            return (object) [
                'dataid' => $profile->dataid,
                'name_line' => $profile->first_name . ($age ? ", {$age} yrs" : ''),
                'location_line' => implode(' • ', array_filter([$profile->height ?? null, $cityCountry])),
                'profession' => $profile->profession ?? null,
                'image_url' => 'https://urgentrishta.co' . $profile->getProfileImage(true),
                'profile_url' => 'https://urgentrishta.co/member/profile/' . $profile->dataid,
            ];
        });

        try {
            Mail::to($user->email)->send(new WeeklyMatches($user->first_name ?: 'there', $cards));
            $this->info("Test email sent to {$user->email}. (last_weekly_match_email_sent_at was NOT updated — safe to re-run.)");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Test send failed: ' . $e->getMessage());
            return 1;
        }
    }
}
