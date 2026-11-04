<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * The short caste list proposals use (the site-wide masterdata CASTE list has
 * ~420 entries; the client wants only a few for proposals).
 */
class ProposalCaste extends Model
{
    protected $table = 'proposal_castes';

    protected $fillable = ['name', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Screens loop over caste options and read ->dataid like they did for masterdata rows. */
    public function getDataidAttribute()
    {
        return (string) $this->id;
    }

    /** Active castes in display order — what the Add/Edit Proposal dropdown shows. */
    public static function options()
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }
}
