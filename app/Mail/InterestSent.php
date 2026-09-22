<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterestSent extends Mailable implements ShouldQueue
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
        // Client request (Sep 2026): when a Royal+ member sends interest to a
        // Diamond-package member, nudge the recipient to upgrade — they've
        // just caught the attention of a premium member.
        $diamond = \App\MasterData::where('type', 'PACKAGE')->where('name', 'Diamond')->first();
        $showUpgradeNote = \App\User::packageIsRoyalOrHigher($this->sender->package)
            && $diamond
            && $this->receiver->package === $diamond->dataid;

        return $this->from(config('mail.from.address'))
                    ->subject("You've received a new rishta interest")
                    ->view('mail.interest-sent')
                    ->with('showUpgradeNote', $showUpgradeNote);
    }
}
