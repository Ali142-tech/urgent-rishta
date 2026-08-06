<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data object instance.
     *
     * @var data
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->data->sender_email)
                    ->subject($this->data->subject)
                    ->view('mail.contactus-html')
                    ->text('mail.contactus-plain')
                    ->with(
                      [
                            // 'testVarOne' => '1',
                            // 'testVarTwo' => '2',
                      ])
                      ->attach(public_path('/images').'/header_logo.png', [
                              'as' => 'header_logo.png',
                              'mime' => 'image/png',
                      ]);
    }
}
