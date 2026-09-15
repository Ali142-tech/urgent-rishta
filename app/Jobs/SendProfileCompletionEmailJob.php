<?php

namespace App\Jobs;

use App\CampaignSend;
use App\Jobs\Concerns\ClassifiesMailFailures;
use App\Mail\ProfileUpdateReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends one "complete your profile" email. Mirrors App\Jobs\SendCampaignEmailJob
 * (same rate limiter, same retry/backoff, same failure classification via
 * ClassifiesMailFailures) but is its own job — rather than generalizing the
 * existing job to take a Mailable class name — so the already-working
 * gender-campaign path isn't touched by this feature.
 *
 * Shares the "campaign-mail" rate limiter with SendCampaignEmailJob on
 * purpose: if both campaigns ever run at once, their combined throughput
 * still stays under the account's real SES send rate instead of each job
 * type getting its own separate allowance.
 *
 * Runs on its own "campaigns" queue, not "default" — discovered while
 * testing that this app's "default" queue already had ~3,000 unrelated
 * transactional-email jobs backlogged. A plain queue:work drains queues in
 * FIFO order, so campaign jobs would sit stuck behind that entire backlog
 * (and, worse, running the worker to test 2 campaign emails would also
 * flush every one of those unrelated backlogged jobs). A dedicated queue
 * lets `queue:work --queue=campaigns` process only these, and production's
 * main worker still needs to cover "default" for everything else.
 */
class SendProfileCompletionEmailJob implements ShouldQueue
{
    use ClassifiesMailFailures, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CACHE_KEY_PAUSED = 'campaign:profile_completion:paused';

    public $tries = 6;

    /** @var int Give a real SES API call (or a slow rate-limit rejection) plenty of room before the worker assumes this job died and reissues it. */
    public $timeout = 60;

    public function __construct(
        public int $campaignSendId,
        public string $email,
        public string $firstName
    ) {
        $this->onQueue('campaigns');
    }

    public function middleware(): array
    {
        return [new RateLimited('campaign-mail')];
    }

    /** 2m, 10m, 30m, 1h, 2h — same schedule as the gender campaign job. */
    public function backoff(): array
    {
        return [120, 600, 1800, 3600, 7200];
    }

    public function handle(): void
    {
        if (Cache::get(self::CACHE_KEY_PAUSED)) {
            // Paused from the admin page — release without counting as an
            // attempt or touching the row, so pausing never eats into the
            // job's retry budget.
            $this->release(300);
            return;
        }

        $row = CampaignSend::find($this->campaignSendId);
        if (!$row) {
            return;
        }

        $row->increment('attempts');

        try {
            Mail::to($this->email)->send(new ProfileUpdateReminder($this->firstName));

            $row->update(['status' => 'sent', 'sent_at' => now(), 'last_error' => null]);
            Log::info("Profile-completion mail sent to {$row->dataid}");
        } catch (Throwable $e) {
            $row->update(['last_error' => $e->getMessage()]);

            if ($this->isPermanentMailFailure($e)) {
                Log::warning("Profile-completion mail permanently failed for {$row->dataid}: " . $e->getMessage());
                $this->fail($e);
                return;
            }

            Log::info("Profile-completion mail temporarily failed for {$row->dataid} (attempt {$row->attempts}): " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        CampaignSend::where('id', $this->campaignSendId)->update([
            'status' => 'failed',
            'last_error' => $e->getMessage(),
        ]);
    }
}
