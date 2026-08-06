<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'appointment_date',
        'appointment_time',
        'subject',
        'notes',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isUpcoming(): bool
    {
        if ($this->isCancelled()) {
            return false;
        }
        $date = $this->appointment_date;
        if ($date->isPast() && ! $date->isToday()) {
            return false;
        }
        return true;
    }
}
