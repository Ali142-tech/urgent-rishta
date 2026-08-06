<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PhotoAccessRequested extends Notification implements ShouldQueue {
    use Queueable;

    private $user, $member;

    public function __construct($user, $member) {
        $this->user = $user;
        $this->member = $member;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable) {
        return [
            'userid' => $this->user->dataid,
            'memberid' => $this->member->dataid,
            'user' => $this->user->first_name,
            'member' => $this->member->first_name,
            'status' => 'Requested'
        ];
    }
}
