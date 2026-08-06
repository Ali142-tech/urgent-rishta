<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterestAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $receiver;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
                    ->subject("Your rishta interest has been accepted")
                    ->view('mail.interest-accepted');
    }
}
