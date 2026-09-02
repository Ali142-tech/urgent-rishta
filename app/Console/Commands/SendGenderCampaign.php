<?php

namespace App\Console\Commands;

use App\CampaignSend;
use App\Jobs\SendCampaignEmailJob;
use App\User;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Gender email campaign — queue-based, rate-limit-safe for 17,000+ recipients
 * on a shared-hosting SMTP account (Hostinger).
 *
 *   php artisan campaign:send female --dry-run   # preview, nothing written/sent
 *   php artisan campaign:send female --status    # progress report
 *   php artisan campaign:send female             # dispatch jobs for anyone not yet sent/queued/failed
 *   php artisan campaign:send female --retry-failed   # re-queue rows that failed permanently
 *   php artisan campaign:send female --limit=100 # dispatch for a test batch only
 *
 * This command only DISPATCHES jobs — it does not send any mail itself and
 * finishes in seconds even for the full 17,000. The actual sending happens
 * in the queue worker (see App\Jobs\SendCampaignEmailJob), throttled by the
 * "campaign-mail" rate limiter registered in AppServiceProvider. You must
 * have a queue worker running (see the class docblock on the Job, or just
 * `php artisan queue:work`) for anything to actually go out.
 *
 * Resumable by design: every recipient gets a row in campaign_sends the
 * first time a job is dispatched for them. Re-running this command only
 * ever dispatches for users with NO row yet — anyone already sent, still
 * queued, or previously failed is skipped automatically, so restarting
 * after a partial run (or after the server was rebooted) never re-emails
 * someone who already got it. Rows that end up 'failed' (permanently
 * rejected, or a temporary error that exhausted all retries) are excluded
 * from normal runs on purpose — use --retry-failed to explicitly give them
 * another attempt rather than silently retrying them forever.
 */
class SendGenderCampaign extends Command
{
    protected $signature = 'campaign:send {gender : female or male}
                            {--dry-run : Show recipient counts without writing or sending anything}
                            {--status : Show a progress report (total/sent/failed/queued/remaining) and exit}
                            {--limit= : Only dispatch this many new jobs (for a test batch)}
                            {--retry-failed : Re-queue rows currently marked failed for this gender}';

    protected $description = 'Queue the gendered campaign email for sending by the queue worker';

    public function handle()
    {
        $gender = strtolower($this->argument('gender'));
        if (!in_array($gender, ['female', 'male'], true)) {
            $this->error('Gender must be "female" or "male".');
            return 1;
        }

        if ($this->option('status')) {
            return $this->showStatus($gender);
        }

        [$heading, $body, $ctaText, $ctaUrl, $subject] = $this->contentFor($gender);

        if ($this->option('retry-failed')) {
            return $this->retryFailed($gender, $heading, $body, $ctaText, $ctaUrl, $subject);
        }

        $query = $this->eligibleUsersQuery($gender);

        $this->info("Subject: {$subject}");
        $this->info("Heading: {$heading}");
        $this->info('Not yet sent/queued/failed for this gender: ' . (clone $query)->count());

        if ($this->option('dry-run')) {
            $this->line('--- DRY RUN — nothing was written or sent. Sample recipients: ---');
            foreach ((clone $query)->take(5)->get() as $u) {
                $this->line(" - {$u->dataid}  {$u->first_name} {$u->last_name}  <{$u->email}>");
            }
            return 0;
        }

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $total = $limit ? min($limit, (clone $query)->count()) : (clone $query)->count();

        if ($total === 0) {
            $this->info('Nothing to dispatch — everyone matching already has a campaign_sends row.');
            return 0;
        }

        if (!$this->confirm("This will queue {$total} emails for sending. Continue?")) {
            $this->line('Cancelled.');
            return 0;
        }

        $dispatched = 0;
        $remaining = $limit;

        $query->orderBy('users.id')->chunkById(500, function ($users) use (&$dispatched, &$remaining, $gender, $heading, $body, $ctaText, $ctaUrl, $subject) {
            foreach ($users as $user) {
                if ($remaining !== null && $remaining <= 0) {
                    return false; // stop chunking once the requested limit is hit
                }

                $row = CampaignSend::create([
                    'dataid' => $user->dataid,
                    'gender' => $gender,
                    'email' => $user->email,
                    'status' => 'queued',
                ]);

                SendCampaignEmailJob::dispatch(
                    $row->id,
                    $user->email,
                    $user->first_name ?: 'there',
                    $heading,
                    $body,
                    $ctaText,
                    $ctaUrl,
                    $subject
                );

                $dispatched++;
                if ($remaining !== null) {
                    $remaining--;
                }
            }
        }, 'users.id');

        $this->info("Dispatched {$dispatched} jobs. Run 'php artisan queue:work' (if it isn't already running) to actually send them.");
        $this->info("Check progress anytime with: php artisan campaign:send {$gender} --status");
        return 0;
    }

    /**
     * Users who don't already have a campaign_sends row for this gender in
     * status sent/queued/failed. A correlated NOT EXISTS subquery rather than
     * whereNotIn(...) — stays index-friendly instead of building a
     * 17,000-item IN-list.
     */
    private function eligibleUsersQuery(string $gender)
    {
        return User::where('gender', $gender)
            ->where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotExists(function ($q) use ($gender) {
                $q->select(DB::raw(1))
                    ->from('campaign_sends')
                    ->whereColumn('campaign_sends.dataid', 'users.dataid')
                    ->where('campaign_sends.gender', $gender);
            });
    }

    private function retryFailed(string $gender, string $heading, string $body, string $ctaText, string $ctaUrl, string $subject): int
    {
        $failedRows = CampaignSend::where('gender', $gender)->where('status', 'failed')->get();

        if ($failedRows->isEmpty()) {
            $this->info('No failed rows to retry for this gender.');
            return 0;
        }

        if (!$this->confirm("This will re-queue {$failedRows->count()} previously-failed emails. Continue?")) {
            $this->line('Cancelled.');
            return 0;
        }

        $usersByDataid = User::whereIn('dataid', $failedRows->pluck('dataid'))->get()->keyBy('dataid');

        $requeued = 0;
        foreach ($failedRows as $row) {
            $user = $usersByDataid->get($row->dataid);
            if (!$user || empty($user->email)) {
                continue; // user no longer exists / has no email — nothing sensible to retry
            }

            $row->update(['status' => 'queued', 'attempts' => 0, 'last_error' => null]);

            SendCampaignEmailJob::dispatch(
                $row->id,
                $user->email,
                $user->first_name ?: 'there',
                $heading,
                $body,
                $ctaText,
                $ctaUrl,
                $subject
            );
            $requeued++;
        }

        $this->info("Re-queued {$requeued} jobs.");
        return 0;
    }

    private function showStatus(string $gender): int
    {
        $totalAudience = User::where('gender', $gender)
            ->where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

        $sent = CampaignSend::where('gender', $gender)->where('status', 'sent')->count();
        $failed = CampaignSend::where('gender', $gender)->where('status', 'failed')->count();
        $queued = CampaignSend::where('gender', $gender)->where('status', 'queued')->count();
        $notYetDispatched = $totalAudience - $sent - $failed - $queued;

        $this->table(
            ['Total audience', 'Sent', 'Queued (in progress)', 'Failed', 'Not yet dispatched'],
            [[$totalAudience, $sent, $queued, $failed, max(0, $notYetDispatched)]]
        );

        return 0;
    }

    private function contentFor(string $gender): array
    {
        // Static, not url() — this command is being run against a local DB copy
        // (not on the live Hostinger deployment), so url() would resolve against
        // the local APP_URL and produce broken links for real recipients.
        $liveSiteUrl = 'https://urgentrishta.com/';

        if ($gender === 'female') {
            return [
                'Your matches are waiting — activate your account',
                "We have a number of serious, established proposals — including businessmen — actively looking for a match right now. To be considered, please activate your account and upload your photos so families can view a complete profile.",
                'Upload Your Photos',
                $liveSiteUrl,
                'Your account is missing something — proposals are waiting',
            ];
        }

        return [
            'Proposals from the UK & USA are waiting for you',
            "We have proposals from families based in the UK and USA actively reviewing profiles this week. Please update your profile so you don't miss out on being considered.",
            'Update Your Profile',
            $liveSiteUrl,
            'UK & USA proposals are waiting — update your profile',
        ];
    }
}
