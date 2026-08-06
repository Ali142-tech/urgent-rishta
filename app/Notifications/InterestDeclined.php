<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InterestDeclined extends Notification implements ShouldQueue {
    use Queueable;

    private $sender, $receiver;

    public function __construct($sender, $receiver) {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable) {
        return [
            'senderid' => $this->sender->dataid,
            'receiverid' => $this->receiver->dataid,
            'sender' => $this->sender->first_name,
            'receiver' => $this->receiver->first_name,
            'status' => 'Declined'
        ];
    }
}
