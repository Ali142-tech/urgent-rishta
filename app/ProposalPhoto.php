<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * One photo of a proposal. Files live in public/proposals: the original plus
 * thumbnail_<file> (300px) and thumbnail_sm_<file> (100px). Team members always
 * see the sharp image, so there is no blurred/watermarked copy.
 */
class ProposalPhoto extends Model
{
    protected $table = 'proposal_photos';

    protected $fillable = ['proposal_id', 'file_name', 'is_main', 'sort_order'];

    protected $casts = ['is_main' => 'boolean'];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /** Public URL path of the original, e.g. /proposals/1790..._1_4821.jpg */
    public function getPathAttribute(): string
    {
        return Proposal::PHOTO_PATH . '/' . $this->file_name;
    }

    public function getThumbPathAttribute(): string
    {
        return Proposal::PHOTO_PATH . '/thumbnail_' . $this->file_name;
    }

    public function getThumbSmPathAttribute(): string
    {
        return Proposal::PHOTO_PATH . '/thumbnail_sm_' . $this->file_name;
    }
}
