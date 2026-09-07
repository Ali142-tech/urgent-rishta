<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per (requester, owner) pair asking to see that owner's hidden
 * (visibility='Private') photos — see the create_photo_access_requests_table
 * migration for the full shape/status convention. Mirrors App\Interest;
 * most of the actual send/grant/decline/withdraw logic lives on
 * Profile/User (updatePhotoAccessLists() etc.), same as interest lists do.
 */
class PhotoAccessRequest extends Model
{
    protected $table = 'photo_access_requests';

    protected $fillable = ['uid', 'pid', 'allowed'];

    public function requester()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'pid');
    }
}
