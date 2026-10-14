<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

/** The standard reset e-mail, pointing at the Team Dashboard's reset page instead of the member one. */
class TeamPasswordReset extends ResetPassword
{
    protected function resetUrl($notifiable)
    {
        return url(route('team.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
