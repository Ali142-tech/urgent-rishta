<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * "Complete Your Profile" re-engagement email — announces the recent site
 * upgrade and walks the member through updating their profile (see
 * resources/views/mail/profile-update-reminder.blade.php for the design).
 *
 * Unlike App\Mail\GenderCampaign, this one's content is fixed (not passed in
 * per-send) since it's one specific announcement, not a reusable
 * male/female template — only $firstName varies per recipient.
 *
 * Deliberately NOT ShouldQueue — same reasoning as GenderCampaign: this is
 * meant to be sent from inside a rate-limited queue job (see
 * App\Jobs\SendCampaignEmailJob), and a Mailable that also implements
 * ShouldQueue gets silently re-queued as its own unthrottled second job
 * instead of actually sending inline.
 */
class ProfileUpdateReminder extends Mailable
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
                    ->subject('Complete Your Profile — Improve Your Match Results')
                    ->view('mail.profile-update-reminder');
    }
}
