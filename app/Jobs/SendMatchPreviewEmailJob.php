<?php

namespace App\Jobs;

use App\Mail\MatchPreview;
use App\Services\MatchPreviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the weekly e-mail (the three newest profiles) to ONE member. The command queues one of these per
 * member, a few hundred milliseconds apart, so the send rate stays under the SES limit without any rate-limiter
 * middleware — a rate limiter that puts jobs back on the queue used up their attempts and made thousands fail.
 * A failure of one member (bad address, SES hiccup) is retried a few times and never affects the others.
 */
class SendMatchPreviewEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    public function __construct(
        public int $userId,
        public string $email,
        public string $firstName,
        public string $recipientGender
    ) {
    }

    /** Wait 5 minutes, then 30, between retries. */
    public function backoff(): array
    {
        return [300, 1800];
    }

    public function handle(MatchPreviewService $service): void
    {
        // Built once per gender and cached for the run (the same three profiles go to everyone of a gender).
        $profiles = $service->curatedProfilesCached($this->recipientGender);
        if ($profiles->isEmpty()) {
            return;
        }

        Mail::to($this->email)->send(new MatchPreview($this->firstName, $profiles));
        Log::info("Weekly e-mail sent to user #{$this->userId}");
    }

    public function failed(\Throwable $e): void
    {
        Log::warning("Weekly e-mail failed for user #{$this->userId} after retries: " . $e->getMessage());
    }
}