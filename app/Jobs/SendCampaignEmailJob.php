<?php

namespace App\Jobs;

use App\CampaignSend;
use App\Mail\GenderCampaign;
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
 * Sends one campaign email. Dispatched in bulk by SendGenderCampaign (one job
 * per recipient) and throttled centrally by the "campaign-mail" rate limiter
 * registered in AppServiceProvider — that's what keeps this under Hostinger's
 * outbound limit, not sleep() or manual pacing. When the limiter says "no
 * capacity right now", Laravel automatically re-releases this job onto the
 * queue with a delay; the worker just moves on to whatever else is ready.
 *
 * Retry behaviour: a temporary SMTP rejection (4xx, e.g. Hostinger's 451
 * ratelimit) is left to propagate — Laravel's own $tries/backoff() schedule
 * then retries it later with an increasing delay. A permanent rejection (5xx,
 * e.g. an address that doesn't exist) calls $this->fail() immediately instead
 * of burning through retries against something that will never succeed.
 */
class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 6;

    /** @var int Give a real SMTP conversation (or a slow rate-limit rejection) plenty of room before the worker assumes this job died and reissues it. */
    public $timeout = 60;

    public function __construct(
        public int $campaignSendId,
        public string $email,
        public string $firstName,
        public string $heading,
        public string $body,
        public string $ctaText,
        public string $ctaUrl,
        public string $subject
    ) {
    }

    public function middleware(): array
    {
        return [new RateLimited('campaign-mail')];
    }

    /**
     * 2m, 10m, 30m, 1h, 2h — genuinely exponential, not a fixed sleep. Only
     * reached for temporary failures; permanent ones fail() immediately and
     * never get here.
     */
    public function backoff(): array
    {
        return [120, 600, 1800, 3600, 7200];
    }

    public function handle(): void
    {
        $row = CampaignSend::find($this->campaignSendId);
        if (!$row) {
            // Tracking row is gone for some reason — nothing sensible to update, skip.
            return;
        }

        $row->increment('attempts');

        try {
            Mail::to($this->email)->send(new GenderCampaign(
                $this->firstName,
                $this->heading,
                $this->body,
                $this->ctaText,
                $this->ctaUrl,
                $this->subject
            ));

            $row->update(['status' => 'sent', 'sent_at' => now(), 'last_error' => null]);
            Log::info("Campaign mail sent to {$row->dataid} ({$row->gender})");
        } catch (Throwable $e) {
            $row->update(['last_error' => $e->getMessage()]);

            if ($this->isPermanentSmtpFailure($e)) {
                Log::warning("Campaign mail permanently failed for {$row->dataid}: " . $e->getMessage());
                $this->fail($e);
                return;
            }

            Log::info("Campaign mail temporarily failed for {$row->dataid} (attempt {$row->attempts}): " . $e->getMessage());
            // Rethrow so Laravel's own retry/backoff schedule takes over.
            throw $e;
        }
    }

    /**
     * failed() runs once retries are exhausted (or fail() was called directly
     * above for a permanent rejection). Either way, record it so --status
     * and the failed_jobs table both reflect the truth.
     */
    public function failed(Throwable $e): void
    {
        CampaignSend::where('id', $this->campaignSendId)->update([
            'status' => 'failed',
            'last_error' => $e->getMessage(),
        ]);
    }

    /**
     * SMTP 5xx = permanent (bad/rejected address, will never succeed).
     * SMTP 4xx (incl. Hostinger's 451 ratelimit) = temporary, worth retrying.
     * Anything we can't classify (connection errors, timeouts, etc.) is
     * treated as temporary too — safer to retry a few times than to silently
     * drop a recipient over an ambiguous error.
     */
    private function isPermanentSmtpFailure(Throwable $e): bool
    {
        $message = $e->getMessage();

        if (preg_match('/got code "(\d{3})"/', $message, $m)) {
            return $m[1][0] === '5';
        }

        return (bool) preg_match('/\b5\d{2}\b/', $message) && !preg_match('/\b4\d{2}\b/', $message);
    }
}
