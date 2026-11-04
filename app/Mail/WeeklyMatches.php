<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Weekly personalized recommendation email — 5 real opposite-gender
 * profiles from the recipient's own Recommended Matches (see
 * App\Jobs\SendWeeklyMatchEmailJob for how the list is built and sent).
 *
 * Deliberately NOT ShouldQueue — same reasoning as the other campaign
 * Mailables (MatchPreview, etc.): sent from inside an already-queued,
 * rate-limited job, so queuing it a second time would be redundant.
 */
class WeeklyMatches extends Mailable
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
                    ->subject('Your Weekly Matches')
                    ->view('mail.weekly-matches');
    }
}
