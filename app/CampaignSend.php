<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignSend extends Model
{
    protected $fillable = ['dataid', 'gender', 'email', 'status', 'attempts', 'last_error', 'sent_at'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
