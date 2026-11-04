<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Weekly "profiles waiting for you" email — shows a handful of opposite-
 * gender curated profiles with blurred photos (see App\Services\MatchPreviewService
 * for how the list is built, App\Jobs\SendMatchPreviewEmailJob for the
 * queued/rate-limited send).
 *
 * Deliberately NOT ShouldQueue — same reasoning as the other campaign
 * Mailables: sent from inside an already-queued, rate-limited job.
 */
class MatchPreview extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $profiles;

    public function __construct(string $firstName, Collection $profiles)
    {
        $this->firstName = $firstName;
        $this->profiles = $profiles;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('New Profiles Are Waiting to Connect With You')
                    ->view('mail.match-preview');
    }
}
