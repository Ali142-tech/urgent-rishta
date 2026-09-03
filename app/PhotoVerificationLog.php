<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PhotoVerificationLog extends Model
{
    protected $table = "photo_verification_logs";

    protected $fillable = [
        'user_id', 'user_dataid',
        'admin_id', 'admin_dataid',
        'action', 'reason',
    ];

    /**
     * Record one verification decision. $admin may be null (shouldn't happen
     * in practice — these actions require an authenticated admin — but this
     * keeps the log itself from ever being the thing that breaks the action).
     */
    public static function record(User $user, ?User $admin, string $action, ?string $reason = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'user_dataid' => $user->dataid,
            'admin_id' => $admin ? $admin->id : null,
            'admin_dataid' => $admin ? $admin->dataid : null,
            'action' => $action,
            'reason' => $reason,
        ]);
    }
}
