<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $userName;
    public $mobile;

    public function __construct(string $otpCode, string $userName, string $mobile)
    {
        $this->otpCode = $otpCode;
        $this->userName = $userName;
        $this->mobile = $mobile;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Your Urgent Rishta login OTP')
            ->view('mail.login-otp');
    }
}
