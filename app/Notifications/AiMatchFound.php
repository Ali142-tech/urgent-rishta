<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AiMatchFound extends Notification implements ShouldQueue {
    use Queueable;

    private $proposal, $match, $percent;

    public function __construct($proposal, $match, $percent) {
        $this->proposal = $proposal;
        $this->match = $match;
        $this->percent = $percent;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable) {
        return [
            'proposalid' => $this->proposal->dataid,
            'matchid' => $this->match->dataid,
            'proposal' => $this->proposal->first_name,
            'percent' => $this->percent,
            'status' => 'New ' . $this->percent . '% AI Match Found',
        ];
    }
}
