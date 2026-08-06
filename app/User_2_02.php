<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
//use App\Profile;

class User extends Authenticatable implements MustVerifyEmail {
    use Notifiable;
    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn() {
        return 'App.User.' . $this->id;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dataid', 'name',
        'first_name',
        'last_name',
        'gender',
        'email',
        'contact_mobile_number',
        'height',
        'birthday',
        'mobile_country',
        'con_of_residence',
        'city',
        'religion',
        'caste',
        'sect',
        'profile_for',
        'mother_tongue',
        'marital_status',
        'education',
        'profession',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'age' => 'int'
    ];

    protected $profileObj = null;

    public static function retrieveUserObject($dataid = null, $refresh = null) {
        $user = null;
        if (Auth::guest())
            return null;
        if (empty($dataid))
            $dataid = Auth::user()->dataid;

        if (Cache::has("u_".$dataid) && !$refresh)
            $user = Cache::get("u_".$dataid);
        else {
            $start = time();
            $user = User::firstWhere('dataid', $dataid);
            Log::info("User retrieval took: ".(time()-$start)." secs");
            if (empty($user))
                Cache::forget("u_".$dataid);
            else {
                $start = time();
                $user->profile(true);
                Log::info("Profile retrieval took: ".(time()-$start)." secs");
                Cache::put("u_".$dataid, $user, 14400);
            }
        }
        return $user;
    }

    public function profile($refresh = null) {
        if ($this->profileObj == null || $refresh) {
            $this->profileObj = Profile::profiles("`u`.`dataid`='".$this->dataid."'")->first();
            $this->profileObj->getInterestLists();

            if (!empty($this->profileObj->images) && !is_array($this->profileObj->images))
                $this->profileObj->images = explode(',', $this->profileObj->images);
        }

        $this->profileObj->user = $this;

        return $this->profileObj;
    }

    public function isActive() {
        return $this->profile()->isActive();
    }

    public function isAdmin() {
        return $this->profile()->isAdmin();
    }

    public function getProfileImage($tiny = null) {
        return $this->profile()->getProfileImage($tiny);
    }

    public function getFilteredList() {
        return $this->profile()->getFilteredList();
    }

    public function getTypeFilteredList($type) {
        if (array_key_exists($type, $this->profile()->getFilteredList()))
            return $this->profile()->getFilteredList()[$type];
        return [];
    }

    public function updateFilteredList() {
        return $this->profile()->updateFilteredList();
    }

    public function getInterestLists() {
        return $this->profile()->getInterestLists();
    }

    public function updateInterestLists() {
        return $this->profile()->updateInterestLists();
    }

    public function inList($dataid, $listname) {
        return $this->profile()->inList($dataid, $listname);
    }

    public function getInterest($dataid) {
        return $this->profile()->getInterest($dataid);
    }

    public function getTotalCount() {
        return $this->profile()->getTotalCount();
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNormalizedPhoneNumber() {
        $pkCodes = ['300', '301', '302', '303', '304', '305', '306', '307', '308', '309', '310', '311', '312', '313', '314', '315', '316', '317', '318', '320', '321', '322', '323', '324', '330', '331', '332', '333', '334', '335', '336', '337', '340', '341', '342', '343', '344', '345', '346', '347', '348', '349', '355'];

        $n = $this->contact_mobile_number;

        foreach ($pkCodes as $code) {
            if (Str::startsWith($n, $code)) {
                return "92" . $n;
            }
        }

        if (Str::startsWith($n, "00")) {
            return Str::substr($n, 2);
        }

        if (Str::startsWith($n, "0")) {
            return "92". Str::substr($n, 1);
        }

        return $n;
    }

    public function getWhatsappLink() {
        return 'https://wa.me/' . $this->getNormalizedPhoneNumber();
    }
}
