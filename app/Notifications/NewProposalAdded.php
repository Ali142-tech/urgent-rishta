<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewProposalAdded extends Notification implements ShouldQueue {
    use Queueable;

    private $addedBy, $proposal;

    public function __construct($addedBy, $proposal) {
        $this->addedBy = $addedBy;
        $this->proposal = $proposal;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable) {
        return [
            'addedbyid' => $this->addedBy->dataid,
            'proposalid' => $this->proposal->dataid,
            'addedby' => $this->addedBy->first_name,
            'proposal' => $this->proposal->first_name,
            'status' => 'New Proposal',
        ];
    }
}
