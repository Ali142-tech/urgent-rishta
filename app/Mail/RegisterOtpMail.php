<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $email;

    public function __construct(string $otpCode, string $email)
    {
        $this->otpCode = $otpCode;
        $this->email = $email;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Your Urgent Rishta signup OTP')
            ->view('mail.register-otp');
    }
}
