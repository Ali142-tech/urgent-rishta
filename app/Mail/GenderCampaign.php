<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * One-off gendered marketing/reactivation email (see App\Console\Commands\SendGenderCampaign
 * and App\Jobs\SendCampaignEmailJob).
 * Content is passed in rather than hard-coded so both the "female" and "male"
 * variants share one Mailable + one branded template.
 *
 * Deliberately NOT ShouldQueue: this is sent from inside SendCampaignEmailJob,
 * which is itself the queued, rate-limited unit of work. If this Mailable also
 * implements ShouldQueue, Laravel's Mailer::send() auto-re-queues it as a
 * second, separate job (Illuminate\Mail\SendQueuedMailable) instead of
 * actually sending — and that inner job has no rate-limit middleware attached
 * at all, so the real SMTP send would happen completely unthrottled the
 * moment the queue worker got to it. Keeping this a plain synchronous
 * Mailable is what makes Mail::send() inside the job actually perform the
 * SMTP conversation right there, under the job's own rate limit.
 */
class GenderCampaign extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $heading;
    public $body;
    public $ctaText;
    public $ctaUrl;
    public $mailSubject;

    public function __construct($firstName, $heading, $body, $ctaText, $ctaUrl, $mailSubject)
    {
        $this->firstName = $firstName;
        $this->heading = $heading;
        $this->body = $body;
        $this->ctaText = $ctaText;
        $this->ctaUrl = $ctaUrl;
        $this->mailSubject = $mailSubject;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($this->mailSubject)
                    ->view('mail.campaign');
    }
}
