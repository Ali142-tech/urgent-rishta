<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignSend extends Model
{
    protected $fillable = ['dataid', 'gender', 'campaign_key', 'email', 'status', 'attempts', 'last_error', 'sent_at'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
