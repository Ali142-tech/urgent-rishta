<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    Protected $table="interest";

    public static function getInterestList($dataid) {
        $user = User::retrieveUserObject($dataid);
        if ($user) {
            $id = $user->id;
            $query = DB::table('filtered as f')->where('f.user', $id)
                ->leftJoin('users as u', 'u.id', '=', 'f.member')
                ->groupby('f.type')->select('f.user','f.type')
                ->selectRaw('GROUP_CONCAT(u.dataid) as memberids')->get();
                return $query;
        } else return null;
    }
}
