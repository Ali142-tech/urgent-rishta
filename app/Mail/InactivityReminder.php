<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Re-engagement email for members inactive 7+ days (see
 * App\Services\InactivityReminderService for the eligibility rule and
 * App\Jobs\SendInactivityReminderEmailJob for the queued send).
 *
 * Deliberately NOT ShouldQueue — same reasoning as GenderCampaign/
 * ProfileUpdateReminder: sent from inside an already-queued, rate-limited
 * job, so this must send synchronously right there rather than re-queuing
 * itself as a second unthrottled job.
 */
class InactivityReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;

    public function __construct($firstName)
    {
        $this->firstName = $firstName;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('New Members Are Waiting to Connect With You')
                    ->view('mail.inactivity-reminder');
    }
}
