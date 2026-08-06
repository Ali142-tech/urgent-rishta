<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllowedProfiles extends Model
{
    Protected $table="allowed_profiles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'profile', 'allowed'
    ];
}
