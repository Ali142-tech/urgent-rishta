<?php

namespace App\Jobs;

use App\Jobs\Concerns\ClassifiesMailFailures;
use App\Mail\MatchPreview;
use App\Services\MatchPreviewService;
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
 * Sends one weekly "match preview" email. Mirrors SendInactivityReminderEmailJob
 * (same rate limiter, "campaigns" queue, failure classification). Takes only
 * the recipient's gender (not the profile list itself) and rebuilds the
 * curated opposite-gender list fresh via MatchPreviewService right before
 * sending — cheap (a handful of rows) and avoids passing Eloquent
 * models/collections through job serialization.
 */
class SendMatchPreviewEmailJob implements ShouldQueue
{
    use ClassifiesMailFailures, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 6;

    public $timeout = 60;

    public function __construct(
        public int $userId,
        public string $email,
        public string $firstName,
        public string $recipientGender
    ) {
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

    public function handle(MatchPreviewService $service): void
    {
        $profiles = $service->curatedProfilesFor($this->recipientGender);

        if ($profiles->isEmpty()) {
            // Nothing of the opposite gender currently curated/active —
            // skip rather than send an empty-looking email.
            return;
        }

        try {
            Mail::to($this->email)->send(new MatchPreview($this->firstName, $profiles));
            Log::info("Match preview sent to user #{$this->userId}");
        } catch (Throwable $e) {
            if ($this->isPermanentMailFailure($e)) {
                Log::warning("Match preview permanently failed for user #{$this->userId}: " . $e->getMessage());
                $this->fail($e);
                return;
            }

            Log::info("Match preview temporarily failed for user #{$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }
}
