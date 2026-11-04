<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * One click from UrgentRishta to a partner website (see PartnerRedirectController, partner_clicks migration). Records the
 * visit only — not purchases or commission.
 */
class PartnerClick extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'partner_clicks';

    protected $fillable = [
        'partner_key', 'partner_name', 'user_id', 'team_member_id', 'visitor_hash',
        'destination_url', 'referral_source', 'utm_source', 'utm_medium',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }
}
