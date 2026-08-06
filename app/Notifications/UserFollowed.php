<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserFollowed extends Notification implements ShouldQueue {
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
            'followerid' => $this->user->dataid,
            'follower' => $this->user->first_name,
            'member' => $this->member->first_name,
        ];
    }
}
