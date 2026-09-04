<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A member's "what I'm looking for" preferences — one row per user,
 * edited on its own dashboard page (see ProfileController::preferencesPage()
 * / updatePreferences()). Previously lived as 14 `r*`-prefixed columns
 * directly on `users` (still there, unused/frozen — see the
 * create_partner_preferences_table migration for why they weren't dropped).
 *
 * `country_id`/`state_id`/`city_id`/`religion_id`/`caste_id`/`education_id`/
 * `mother_tongue_id`/`preferred_country_id` all store a `masterdata.dataid`
 * code (same convention as the matching fields on User), not a free-form
 * string — resolve them via MasterData::where('type', '...') for display,
 * same as everywhere else in the app.
 */
class PartnerPreference extends Model
{
    protected $table = 'partner_preferences';

    protected $fillable = [
        'user_id',
        'age_min',
        'age_max',
        'height',
        'weight',
        'marital_status',
        'with_children',
        'country_id',
        'state_id',
        'city_id',
        'religion_id',
        'caste_id',
        'sect',
        'education_id',
        'profession',
        'mother_tongue_id',
        'languages',
        'preferred_country_id',
        'general_requirement',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 'languages' is stored as a comma-separated list of masterdata codes (see migration). */
    public function getLanguagesArrayAttribute()
    {
        return empty($this->languages) ? [] : explode(',', $this->languages);
    }
}
