<?php

namespace App\Console\Commands;

use App\Mail\ProfileUpdateReminder;
use App\Services\ProfileCompletionCampaignService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Profile-completion campaign — queue-based, same throttled/resumable
 * architecture as SendGenderCampaign (see that class's docblock), sent to
 * every active member (not split by gender). See
 * App\Services\ProfileCompletionCampaignService for the eligibility rule
 * and dispatch logic shared with the admin page.
 *
 *   php artisan campaign:send-profile-completion --test=you@example.com  # real send to ONE address, no DB/queue involved
 *   php artisan campaign:send-profile-completion --dry-run              # preview recipient count, nothing written/sent
 *   php artisan campaign:send-profile-completion --status               # progress report
 *   php artisan campaign:send-profile-completion                        # dispatch jobs for everyone not yet sent/queued/failed
 *   php artisan campaign:send-profile-completion --limit=300             # dispatch a batch only (recommended for a first live run)
 *   php artisan campaign:send-profile-completion --retry-failed          # re-queue rows that failed permanently
 *
 * This command only DISPATCHES jobs — it does not send any mail itself.
 * Actual sending happens in the queue worker (php artisan queue:work),
 * throttled by the "campaign-mail" rate limiter (AppServiceProvider).
 */
class SendProfileCompletionCampaign extends Command
{
    protected $signature = 'campaign:send-profile-completion
                            {--dry-run : Show recipient count without writing or sending anything}
                            {--status : Show a progress report (total/sent/failed/queued/remaining) and exit}
                            {--limit= : Only dispatch this many new jobs (recommended for a first live batch)}
                            {--retry-failed : Re-queue rows currently marked failed}
                            {--test= : Send one real email to this address only (no queue, no campaign_sends row) to preview the template}';

    protected $description = 'Queue the "complete your profile" campaign email for members with an unverified photo status';

    public function handle(ProfileCompletionCampaignService $service)
    {
        if ($testEmail = $this->option('test')) {
            return $this->sendTest($testEmail);
        }

        if ($this->option('status')) {
            return $this->showStatus($service);
        }

        if ($this->option('retry-failed')) {
            $requeued = $service->retryFailed();
            $this->info("Re-queued {$requeued} previously-failed jobs.");
            return 0;
        }

        $total = (clone $service->eligibleUsersQuery())->count();
        $this->info("Not yet sent/queued/failed for this campaign: {$total}");

        if ($this->option('dry-run')) {
            $this->line('--- DRY RUN — nothing was written or sent. Sample recipients: ---');
            foreach ((clone $service->eligibleUsersQuery())->take(5)->get() as $u) {
                $this->line(" - {$u->dataid}  {$u->first_name} {$u->last_name}  <{$u->email}>");
            }
            return 0;
        }

        if ($total === 0) {
            $this->info('Nothing to dispatch — everyone eligible already has a campaign_sends row.');
            return 0;
        }

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $willDispatch = $limit ? min($limit, $total) : $total;

        if (!$this->confirm("This will queue {$willDispatch} emails for sending. Continue?")) {
            $this->line('Cancelled.');
            return 0;
        }

        $dispatched = $service->dispatchEligible($limit);

        $this->info("Dispatched {$dispatched} jobs. Run 'php artisan queue:work' (if it isn't already running) to actually send them.");
        $this->info('Check progress anytime with: php artisan campaign:send-profile-completion --status');
        return 0;
    }

    /**
     * Sends the real ProfileUpdateReminder template to one address right
     * now, synchronously — bypasses campaign_sends and the queue entirely,
     * so it can never touch a real member or count against campaign state.
     * This is what requirement #10 ("test mode") maps to.
     */
    private function sendTest(string $email): int
    {
        try {
            Mail::to($email)->send(new ProfileUpdateReminder('there'));
            $this->info("Test email sent to {$email}.");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Test send failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function showStatus(ProfileCompletionCampaignService $service): int
    {
        $s = $service->status();

        $this->table(
            ['Total audience', 'Sent', 'Queued (in progress)', 'Failed', 'Not yet dispatched', 'Paused?'],
            [[$s['total_audience'], $s['sent'], $s['queued'], $s['failed'], $s['not_yet_dispatched'], $s['paused'] ? 'YES' : 'no']]
        );

        return 0;
    }
}
