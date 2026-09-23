<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Single-row settings table — see the create_match_weight_settings_table
 * migration. Profile::compatibilityWith() reads this to weight each
 * factor's score instead of a flat average.
 */
class MatchWeightSetting extends Model
{
    protected $table = 'match_weight_settings';

    protected $fillable = [
        'age_weight', 'location_weight', 'religion_weight', 'caste_weight',
        'marital_status_weight', 'education_weight', 'mother_tongue_weight', 'children_weight',
    ];

    /**
     * The one settings row, keyed by factor name (matching each check's
     * 'factor' value in Profile::compatibilityWith()) — created with
     * defaults on first access rather than needing a seeder.
     */
    public static function current(): array
    {
        $row = self::first();
        if (!$row) {
            $row = self::create([]);
        }

        return [
            'Age' => $row->age_weight,
            'Location' => $row->location_weight,
            'Religion' => $row->religion_weight,
            'Caste' => $row->caste_weight,
            'Marital Status' => $row->marital_status_weight,
            'Education' => $row->education_weight,
            'Mother Tongue' => $row->mother_tongue_weight,
            'Children' => $row->children_weight,
        ];
    }
}
