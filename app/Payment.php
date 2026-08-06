<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'online_package_id',
        'gateway',
        'reference',
        'currency',
        'amount',
        'status',
        'gateway_txid',
        'gateway_payload',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function onlinePackage()
    {
        return $this->belongsTo(OnlinePackage::class, 'online_package_id');
    }
}

