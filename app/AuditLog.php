<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Generic, queryable audit trail — see the create_audit_logs_table
 * migration. Write points are scattered across ProfileController (profile
 * views), TeamController (proposal sharing, successful matches), and
 * EventServiceProvider (logins) — see AuditLog::record() call sites.
 */
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = ['actor_id', 'action', 'subject_type', 'subject_id', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * $actor may be null (system action, or no logged-in user). $subject
     * may be any Eloquent model or null.
     */
    public static function record($actor, string $action, $subject = null, array $meta = []): self
    {
        // Team members live in their own table (audit_logs.actor_id points at users),
        // so they are recorded by their id in the meta instead of as the actor.
        if ($actor instanceof TeamMember) {
            $meta['team_member'] = $actor->dataid;
            $actor = null;
        }

        return self::create([
            'actor_id' => $actor ? $actor->id : null,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'meta' => $meta ?: null,
        ]);
    }
}
