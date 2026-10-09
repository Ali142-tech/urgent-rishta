<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminMessage extends Notification implements ShouldQueue {
    use Queueable;

    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable) {
        return [
            'message' => $this->message,
            'status' => 'Admin Message',
        ];
    }
}
