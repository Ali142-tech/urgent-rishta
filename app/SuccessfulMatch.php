<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * See the create_successful_matches_table migration for the full shape.
 */
class SuccessfulMatch extends Model
{
    protected $table = 'successful_matches';

    protected $fillable = [
        'proposal_id', 'counterpart_proposal_id',
        'partner_a_id', 'partner_b_id', 'matched_at', 'share_partner_a', 'share_partner_b',
    ];

    public function proposal()
    {
        return $this->belongsTo(User::class, 'proposal_id');
    }

    public function counterpartProposal()
    {
        return $this->belongsTo(User::class, 'counterpart_proposal_id');
    }

    public function partnerA()
    {
        return $this->belongsTo(User::class, 'partner_a_id');
    }

    public function partnerB()
    {
        return $this->belongsTo(User::class, 'partner_b_id');
    }
}
