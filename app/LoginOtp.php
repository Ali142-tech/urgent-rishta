<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class LoginOtp extends Model
{
    protected $table = 'login_otps';

    protected $fillable = [
        'identifier',
        'channel',
        'code_hash',
        'delivery_target',
        'attempts',
        'expires_at',
        'consumed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public static function issue(string $identifier, string $plainCode, string $channel, ?string $deliveryTarget): self
    {
        // Invalidate previous unused OTPs for this identifier
        static::where('identifier', $identifier)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        return static::create([
            'identifier' => $identifier,
            'channel' => $channel,
            'code_hash' => Hash::make($plainCode),
            'delivery_target' => $deliveryTarget,
            'attempts' => 0,
            'expires_at' => now()->addMinutes((int) config('otp.expires_minutes', 10)),
        ]);
    }

    public function isValid(): bool
    {
        if ($this->consumed_at !== null) {
            return false;
        }

        if ($this->expires_at === null) {
            return false;
        }

        $expiresAt = $this->expires_at instanceof Carbon
            ? $this->expires_at
            : Carbon::parse($this->expires_at);

        return $expiresAt->isFuture()
            && (int) $this->attempts < (int) config('otp.max_attempts', 5);
    }

    public function matches(string $plainCode): bool
    {
        return Hash::check($plainCode, $this->code_hash);
    }

    public function markConsumed(): void
    {
        $this->consumed_at = now();
        $this->save();
    }

    public function incrementAttempts(): void
    {
        $this->attempts = (int) $this->attempts + 1;
        $this->save();
    }
}
