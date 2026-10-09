<?php

namespace App\Jobs;

use App\Jobs\Concerns\ClassifiesMailFailures;
use App\Mail\InactivityReminder;
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
 * Sends one "we miss you" inactivity reminder. Mirrors
 * App\Jobs\SendProfileCompletionEmailJob (same rate limiter, same "campaigns"
 * queue, same failure classification) — no per-recipient status table here
 * though, since eligibility is re-derived fresh from users.last_login_at /
 * last_inactivity_reminder_sent_at each time the daily command runs, rather
 * than needing a separate queued/sent/failed row per send like the
 * one-off campaigns do.
 */
class SendInactivityReminderEmailJob implements ShouldQueue
{
    use ClassifiesMailFailures, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 6;

    public $timeout = 60;

    public function __construct(
        public int $userId,
        public string $email,
        public string $firstName
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

    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new InactivityReminder($this->firstName));
            Log::info("Inactivity reminder sent to user #{$this->userId}");
        } catch (Throwable $e) {
            if ($this->isPermanentMailFailure($e)) {
                Log::warning("Inactivity reminder permanently failed for user #{$this->userId}: " . $e->getMessage());
                $this->fail($e);
                return;
            }

            Log::info("Inactivity reminder temporarily failed for user #{$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }
}
