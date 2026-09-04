<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\MasterData;
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
        'service_type',
        'email',
        'google_id',
        'contact_mobile_number',
        'height',
        'birthday',
        'mobile_country',
        'con_of_residence',
        'state',
        'city',
        'religion',
        'caste',
        'sect',
        'profile_for',
        'mother_tongue',
        'marital_status',
        'education',
        'profession',
        'password',
        'package',
        'package_started_at',
        'package_expires_at',
        'online_package',
        'online_package_started_at',
        'online_package_expires_at',
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
        'age' => 'int',
        'package_started_at' => 'datetime',
        'package_expires_at' => 'datetime',
        'online_package_started_at' => 'datetime',
        'online_package_expires_at' => 'datetime',
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

    public function partnerPreference() {
        return $this->hasOne(PartnerPreference::class);
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

    /**
     * User has an active admin-assigned package (e.g. Platinum, Diamond, Royal, Sovereign Matchmaking).
     * Package must exist in masterdata type PACKAGE and not be expired.
     */
    public function hasActivePackage(): bool
    {
        if (empty($this->package)) {
            return false;
        }

        $isAdminPackage = MasterData::where('type', 'PACKAGE')->where('dataid', $this->package)->exists();
        if (!$isAdminPackage) {
            return false;
        }

        if (empty($this->package_expires_at)) {
            return true;
        }

        return $this->package_expires_at instanceof Carbon
            ? $this->package_expires_at->isFuture()
            : Carbon::parse($this->package_expires_at)->isFuture();
    }

    /**
     * True only if the user has an active online package (pay-per-period) and it is not expired.
     * Uses only online_package, online_package_expires_at (no fallback to admin package column).
     */
    public function hasActiveOnlinePackage(): bool
    {
        $dataid = $this->online_package;
        $expiresAt = $this->online_package_expires_at;

        if (empty($dataid)) {
            return false;
        }

        $isOnlinePackage = OnlinePackage::where('dataid', $dataid)->where('is_active', true)->exists();
        if (!$isOnlinePackage) {
            return false;
        }

        if (empty($expiresAt)) {
            return true;
        }

        return $expiresAt instanceof Carbon
            ? $expiresAt->isFuture()
            : Carbon::parse($expiresAt)->isFuture();
    }

    /**
     * Search Soul Mates is allowed when:
     * - User is admin (can search all profiles without any package), OR
     * - User has an admin-assigned package (e.g. Platinum, Diamond, Royal) and it is active, OR
     * - User has an active (subscribed and not expired) online package (days-based access).
     */
    public function canSearchSoulMates(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->hasActivePackage() || $this->hasActiveOnlinePackage();
    }

    /**
     * Returns package dataids that this user (as searcher) is allowed to see.
     * - If user is admin: can see all profiles (all package tiers).
     * - If user has admin package: higher tier can see same + lower tiers; lower cannot see higher.
     * - If user has no admin package but has active online package: can see all package tiers.
     * - If user has neither: returns empty (no search access).
     */
    public function getVisiblePackageDataidsForSearch(): array
    {
        $packages = MasterData::where('type', 'PACKAGE')->orderBy('id')->get();
        if ($packages->isEmpty()) {
            return [];
        }
        if ($this->isAdmin()) {
            return $packages->pluck('dataid')->values()->all();
        }
        // No admin package: if they have active online package, allow seeing all tiers; otherwise none.
        if (empty($this->package)) {
            return $this->hasActiveOnlinePackage()
                ? $packages->pluck('dataid')->values()->all()
                : [];
        }
        $searcherPackage = $packages->firstWhere('dataid', $this->package);
        if (!$searcherPackage) {
            return [];
        }
        return $packages->where('id', '<=', $searcherPackage->id)->pluck('dataid')->values()->all();
    }

    /**
     * Activate an ONLINE package (sets online_package columns only; does not change admin package).
     * If the user already has an active (non-expired) online subscription, does nothing:
     * they must wait until expiry before subscribing again.
     */
    public function activateOnlinePackage(OnlinePackage $package): void
    {
        if ($this->hasActiveOnlinePackage()) {
            return;
        }

        $meta = $package->meta();
        $durationDays = isset($meta['duration_days']) ? (int)$meta['duration_days'] : 30;
        if ($durationDays <= 0) {
            $durationDays = 30;
        }

        $now = Carbon::now();
        $this->online_package = $package->dataid;
        $this->online_package_started_at = $now;
        $this->online_package_expires_at = $now->copy()->addDays($durationDays);
        $this->save();
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

    /**
     * Website Upgrade Brief §5/§9 — photo verification. Split into the two
     * regular uploads vs. the live-captured selfie (Images::is_selfie) so
     * the admin verification queue can show them separately.
     */
    public function images() {
        return $this->hasMany(Images::class, 'user_id');
    }

    public function regularImages() {
        return $this->images()->where('is_selfie', 0);
    }

    public function selfieImage() {
        return $this->hasOne(Images::class, 'user_id')->where('is_selfie', 1)->latestOfMany();
    }

    /**
     * Single source of truth for "can this account actually log in yet?" —
     * used by every login path (password, OTP, Google — none of which share
     * a common base method, so this stays here instead of being duplicated
     * per controller). Null means fine, proceed; otherwise a "type|text"
     * flash string ready for Session::flash('message', ...).
     *
     * Note 'resubmit' (set by AdminController@reopenPhotoVerification) is
     * intentionally NOT blocked here — that status means "let this
     * previously-rejected account log in one time specifically to redo the
     * photo gate", handled in LoginController@finishLogin /
     * GoogleAuthController@callback.
     */
    public function photoVerificationBlockMessage(): ?string {
        if ($this->photo_verification_status === 'rejected') {
            $reason = $this->photo_rejection_reason;
            return 'danger|Your account was not approved' . (!empty($reason) ? ': ' . $reason : '') . '. Please contact support.';
        }
        if ($this->photo_verification_status === 'pending') {
            return 'warning|Your account is pending admin approval. We will notify you by email once your photos are verified.';
        }
        return null;
    }
}
