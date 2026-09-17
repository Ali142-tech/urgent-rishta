<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
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

    /** Display name regardless of whether this came from a logged-in member or a guest. */
    public function getContactNameAttribute(): ?string
    {
        return $this->user ? trim($this->user->first_name . ' ' . $this->user->last_name) : $this->guest_name;
    }

    /**
     * The consultation form always captures email/phone at submission time
     * now (even for logged-in members, who might want a different contact
     * for this specific request) — prefer that over the account's own
     * info, falling back to the account only for older rows created before
     * this was always captured.
     */
    public function getContactEmailAttribute(): ?string
    {
        return $this->guest_email ?: ($this->user->email ?? null);
    }

    public function getContactPhoneAttribute(): ?string
    {
        return $this->guest_phone ?: ($this->user->contact_mobile_number ?? null);
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
