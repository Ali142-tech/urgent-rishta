<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Single-row settings table — see the create_contact_unlock_settings_table
 * migration and User::canViewContactInfoOf(). Mirrors MatchWeightSetting.
 */
class ContactUnlockSetting extends Model
{
    protected $table = 'contact_unlock_settings';

    protected $fillable = ['unlock_email', 'unlock_phone', 'unlock_name', 'unlock_address'];

    public static function current(): array
    {
        $row = self::first();
        if (!$row) {
            $row = self::create([]);
        }

        return [
            'unlock_email' => (bool) $row->unlock_email,
            'unlock_phone' => (bool) $row->unlock_phone,
            'unlock_name' => (bool) $row->unlock_name,
            'unlock_address' => (bool) $row->unlock_address,
        ];
    }
}
