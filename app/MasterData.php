<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    Protected $table="masterdata";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dataid', 'type', 'subtype', 'name', 'description'
    ];
}
