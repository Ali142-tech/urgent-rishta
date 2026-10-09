<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Login OTP channel
    |--------------------------------------------------------------------------
    | email = send OTP to the account email (current)
    | sms   = send OTP via SMS provider (future)
    */
    'channel' => env('LOGIN_OTP_CHANNEL', 'email'),

    'length' => 6,
    'expires_minutes' => 10,
    'max_attempts' => 5,
    'resend_cooldown_seconds' => 60,
];
