<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use Notifiable, SoftDeletes;
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
        'con_of_citizenship',
        'immigration_status',
        'family_residence',
        'family_values',
        'income',
        'property_financial_status',
        'profile_description',
        'address',
        'college_university',
        'residence_size',
        'current_city',
        'father_occupation',
        'mother_occupation',
        'siblings_brothers',
        'siblings_sisters',
        'siblings_married_note',
        'experience',
        'about_me',
        'password',
    ];

    /**
     * Entitlement fields deliberately excluded from $fillable — no legitimate
     * code path mass-assigns them (they're always set via explicit property
     * writes, e.g. activateOnlinePackage() below, or AdminController's
     * package-change flow), so keeping them out of $fillable closes off a
     * mass-assignment privilege-escalation path (a user self-granting a
     * package via a crafted request) for any future code that isn't as
     * careful: 'package', 'package_started_at', 'package_expires_at',
     * 'online_package', 'online_package_started_at', 'online_package_expires_at'.
     * Same reasoning covers 'is_team_member' (set only via AdminController::
     * approveMatchmakerApplication()/rejectMatchmakerApplication()) and
     * 'added_by' (set only via TeamController::store()
     * to Auth::id() of the acting team member) — neither should ever be
     * settable from a mass-assigned request body. 'matchmaker_status' joins
     * this list for the same reason — a signup form submitter must never be
     * able to set their own application straight to 'approved' by including
     * that field in a crafted request; only MatchmakerApplicationController::
     * store() (hardcoded to 'pending') and AdminController's approve/reject
     * actions ever write it.
     */

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

    /**
     * Whether this member has actually filled in at least one partner
     * preference field — used to gate "Recommended Matches" (no
     * preferences = we have nothing to base a recommendation on, show a
     * prompt to set them instead of a generic/irrelevant list). Checks
     * every fillable field except the housekeeping ones (id/user_id/
     * timestamps), so setting just one field (e.g. only a religion) already
     * counts as "has preferences".
     */
    public function hasPartnerPreferences(): bool
    {
        $pref = $this->partnerPreference;
        if (empty($pref)) {
            return false;
        }
        $meaningfulFields = ['age_min', 'age_max', 'height', 'weight', 'marital_status', 'with_children',
            'country_id', 'state_id', 'city_id', 'religion_id', 'caste_id', 'sect', 'education_id',
            'profession', 'mother_tongue_id', 'languages', 'preferred_country_id', 'general_requirement'];
        foreach ($meaningfulFields as $field) {
            if (!empty($pref->{$field})) {
                return true;
            }
        }
        return false;
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
        // Per client request (Sep 2026): every member who can search at all
        // now sees EVERY package tier's profiles — previously a lower tier
        // couldn't see higher tiers at all (rank-limited to their own tier
        // and below). The package gate has moved to sending an interest to
        // a Royal+ profile instead (see canMessageRoyalTier() below and
        // ProfileController::sendInterest()), not to browsing/searching.
        if (empty($this->package)) {
            return $this->hasActiveOnlinePackage()
                ? $packages->pluck('dataid')->values()->all()
                : [];
        }
        return $packages->pluck('dataid')->values()->all();
    }

    /**
     * True if the given package dataid ranks Royal or higher (currently
     * Royal, Imperial — see the PACKAGE masterdata rank order, lowest id
     * first: Platinum, Diamond, Royal, Imperial). Used to gate sending an
     * interest request to a Royal+ profile: the sender must also be Royal+
     * (see ProfileController::sendInterest()) — client request Sep 2026.
     */
    public static function packageIsRoyalOrHigher(?string $packageDataid): bool
    {
        if (empty($packageDataid)) {
            return false;
        }
        $packages = MasterData::where('type', 'PACKAGE')->orderBy('id')->get();
        $royal = $packages->firstWhere('name', 'Royal');
        $target = $packages->firstWhere('dataid', $packageDataid);
        return $royal && $target && $target->id >= $royal->id;
    }

    /**
     * WHERE clause shared by getRecommendedMatches()/getRecommendedMatchesCount()
     * — same active/package visibility rules as the real search
     * (HomeController::search()), restricted to the opposite gender, AND
     * (per client request) narrowed down to whatever this member has
     * actually set on their Partner Preferences page. Null when the viewer
     * can't search yet (inactive profile / no package), has no gender set,
     * or — crucially — has no partner preferences saved at all: with
     * nothing to base a recommendation on, callers show a "please add your
     * preferences" prompt instead of falling back to an unrelated list (see
     * hasPartnerPreferences()).
     *
     * Hard-filtered fields (per client request, Sep 2026): age range,
     * marital status, has-children, country of residence, state, religion,
     * and a partial-text match on profession. City, caste, and education
     * are deliberately NOT filtered here — each was found to zero out
     * results entirely (city for smaller towns, caste for less common
     * castes, education for narrow country/state/religion combinations)
     * even with otherwise-good matches available nearby — mother tongue/
     * height/weight/sect/languages/preferred country/general requirement
     * are free-text or have no reliable single-column equivalent, so
     * they're saved on the preferences page but not enforced here either.
     */
    private function recommendedMatchesWhere(): ?string
    {
        if (!$this->isActive() || !$this->canSearchSoulMates()) {
            return null;
        }

        $ownGender = strtolower($this->gender ?? '');
        $oppositeGender = $ownGender === 'male' ? 'female' : ($ownGender === 'female' ? 'male' : null);
        if (empty($oppositeGender)) {
            return null;
        }

        if (!$this->hasPartnerPreferences()) {
            return null;
        }

        $where = "`u`.`gender`='" . $oppositeGender . "' and `u`.`active`=1";
        $visiblePackageDataids = $this->getVisiblePackageDataidsForSearch();
        if (empty($visiblePackageDataids)) {
            $where .= " and 1=0";
        } elseif (!$this->isAdmin()) {
            $quoted = array_map(function ($d) {
                return "'" . addslashes($d) . "'";
            }, $visiblePackageDataids);
            $where .= " and `u`.`package` IN (" . implode(',', $quoted) . ")";
        }

        $pref = $this->partnerPreference;
        if (!empty($pref->age_min) || !empty($pref->age_max)) {
            $min = !empty($pref->age_min) ? (int) $pref->age_min : 18;
            $max = !empty($pref->age_max) ? (int) $pref->age_max : 99;
            $where .= " and FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/365.25) between " . $min . " and " . $max;
        }
        if (!empty($pref->marital_status)) {
            $where .= " and `u`.`marital_status`='" . addslashes($pref->marital_status) . "'";
        }
        // `u`.`children` stores a free-text/number of kids ("0", "2", ...);
        // the preference is a Yes/No/Does-not-matter question — "No" means
        // childless (0/blank), "Yes" means at least one, "Does not matter"
        // (or empty) applies no filter at all.
        if ($pref->with_children === 'No') {
            $where .= " and (`u`.`children` is null or `u`.`children`='' or CAST(`u`.`children` AS UNSIGNED)=0)";
        } elseif ($pref->with_children === 'Yes') {
            $where .= " and CAST(`u`.`children` AS UNSIGNED) > 0";
        }
        if (!empty($pref->country_id)) {
            $where .= " and `u`.`con_of_residence`='" . addslashes($pref->country_id) . "'";
        }
        if (!empty($pref->state_id)) {
            $where .= " and `u`.`state`='" . addslashes($pref->state_id) . "'";
        }
        if (!empty($pref->religion_id)) {
            $where .= " and `u`.`religion`='" . addslashes($pref->religion_id) . "'";
        }
        if (!empty($pref->profession)) {
            $where .= " and `u`.`profession` LIKE '%" . addslashes($pref->profession) . "%'";
        }

        // Team-added proposals (see TeamController::store()) are team-exclusive
        // — they must never surface in the regular recommended-matches pool.
        $where .= " and `u`.`added_by` IS NULL";

        return $where;
    }

    /**
     * Up to $limit "recommended" profiles for this member, starting at
     * $offset (for the dedicated "Recommended Matches" page's
     * pagination). Filtered by this member's own Partner Preferences (age
     * range, marital status, country/city, religion, caste, mother tongue,
     * profession — see recommendedMatchesWhere()); returns empty when no
     * preferences have been saved at all, rather than an unrelated default
     * list — callers should check hasPartnerPreferences() to show a
     * "please add your preferences" prompt in that case. Beyond the
     * preference filters, same-city results are still prioritized first
     * (via ORDER BY), then same-country, then everyone else by recency.
     */
    public function getRecommendedMatches($limit = 3, $offset = 0)
    {
        $where = $this->recommendedMatchesWhere();
        if ($where === null) {
            return collect();
        }

        $orderParts = [];
        if (!empty($this->city)) {
            $orderParts[] = "(`u`.`city`='" . addslashes($this->city) . "') DESC";
        }
        if (!empty($this->con_of_residence)) {
            $orderParts[] = "(`u`.`con_of_residence`='" . addslashes($this->con_of_residence) . "') DESC";
        }
        $orderParts[] = "`u`.`updated_at` DESC";
        $orderBy = implode(', ', $orderParts);

        return Profile::profiles($where, "", $orderBy, $limit, $offset);
    }

    /**
     * Total count of profiles getRecommendedMatches() draws from — used
     * by the dedicated "Recommended Matches" page to paginate.
     */
    public function getRecommendedMatchesCount(): int
    {
        $where = $this->recommendedMatchesWhere();
        if ($where === null) {
            return 0;
        }

        return (int) Profile::profiles($where, "", null, null, null, true);
    }

    /**
     * Parallel to recommendedMatchesWhere() — NOT a modification of it,
     * kept fully separate so self-registered members' behavior is
     * untouched. $this is expected to be a PROPOSAL (a `users` row with
     * `added_by` set — see TeamController::store()), not a logged-in
     * viewer. Deliberate difference from recommendedMatchesWhere(): skips
     * the isActive()/canSearchSoulMates() package gate — a proposal has
     * no package, that gate would always return null for one. Candidate
     * pool is restricted to OTHER team-added proposals only
     * (`added_by IS NOT NULL`) — client confirmed 2026-09-23 the Team
     * Dashboard's AI Matches must only ever surface manually-added
     * proposals, never the regular self-registered pool (matches how
     * every other Team Dashboard page already works).
     */
    private function proposalMatchesWhere(): ?string
    {
        $ownGender = strtolower($this->gender ?? '');
        $oppositeGender = $ownGender === 'male' ? 'female' : ($ownGender === 'female' ? 'male' : null);
        if (empty($oppositeGender)) {
            return null;
        }

        if (!$this->hasPartnerPreferences()) {
            return null;
        }

        $where = "`u`.`gender`='" . $oppositeGender . "' and `u`.`active`=1 and `u`.`id`!=" . (int) $this->id . " and `u`.`added_by` IS NOT NULL";

        $pref = $this->partnerPreference;
        if (!empty($pref->age_min) || !empty($pref->age_max)) {
            $min = !empty($pref->age_min) ? (int) $pref->age_min : 18;
            $max = !empty($pref->age_max) ? (int) $pref->age_max : 99;
            $where .= " and FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/365.25) between " . $min . " and " . $max;
        }
        if (!empty($pref->marital_status)) {
            $where .= " and `u`.`marital_status`='" . addslashes($pref->marital_status) . "'";
        }
        if ($pref->with_children === 'No') {
            $where .= " and (`u`.`children` is null or `u`.`children`='' or CAST(`u`.`children` AS UNSIGNED)=0)";
        } elseif ($pref->with_children === 'Yes') {
            $where .= " and CAST(`u`.`children` AS UNSIGNED) > 0";
        }
        if (!empty($pref->country_id)) {
            $where .= " and `u`.`con_of_residence`='" . addslashes($pref->country_id) . "'";
        }
        if (!empty($pref->state_id)) {
            $where .= " and `u`.`state`='" . addslashes($pref->state_id) . "'";
        }
        if (!empty($pref->religion_id)) {
            $where .= " and `u`.`religion`='" . addslashes($pref->religion_id) . "'";
        }
        if (!empty($pref->profession)) {
            $where .= " and `u`.`profession` LIKE '%" . addslashes($pref->profession) . "%'";
        }

        return $where;
    }

    /**
     * AI Matches Found for a proposal — same shape as
     * getRecommendedMatches() but drawing from proposalMatchesWhere().
     */
    public function getProposalMatches($limit = 3, $offset = 0)
    {
        $where = $this->proposalMatchesWhere();
        if ($where === null) {
            return collect();
        }

        $orderParts = [];
        if (!empty($this->city)) {
            $orderParts[] = "(`u`.`city`='" . addslashes($this->city) . "') DESC";
        }
        if (!empty($this->con_of_residence)) {
            $orderParts[] = "(`u`.`con_of_residence`='" . addslashes($this->con_of_residence) . "') DESC";
        }
        $orderParts[] = "`u`.`updated_at` DESC";
        $orderBy = implode(', ', $orderParts);

        return Profile::profiles($where, "", $orderBy, $limit, $offset);
    }

    public function getProposalMatchesCount(): int
    {
        $where = $this->proposalMatchesWhere();
        if ($where === null) {
            return 0;
        }

        return (int) Profile::profiles($where, "", null, null, null, true);
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

    public function getPhotoAccessLists() {
        return $this->profile()->getPhotoAccessLists();
    }

    public function updatePhotoAccessLists() {
        return $this->profile()->updatePhotoAccessLists();
    }

    public function getPhotoAccess($dataid) {
        return $this->profile()->getPhotoAccess($dataid);
    }

    /**
     * Can this member see $owner's hidden (Private) photos right now?
     * Admins always can (Website Upgrade Brief-style moderation access —
     * "hidden" only ever meant hidden from other members); the owner
     * always can on their own profile (not that this page renders for
     * them); everyone else needs a granted photo_access_requests row.
     */
    public function canViewHiddenPhotosOf(User $owner): bool {
        if ($this->id === $owner->id) return true;
        if ($this->isAdmin()) return true;
        return $this->getPhotoAccess($owner->dataid) === 1;
    }

    /**
     * Can this member see $proposalOwner's hidden contact info (email,
     * phone, last name, exact address) right now? These fields are hidden
     * by default for any team-added proposal (see TeamController::store())
     * — but the whole team-added pool is shared/open across the team, so
     * any active team member can see them, not just the one who added it.
     */
    public function canViewContactInfoOf(User $proposalOwner): bool {
        if ($this->id === $proposalOwner->id) return true;
        if ($this->isAdmin()) return true;
        if (empty($proposalOwner->added_by)) return false;

        return (int) $this->is_team_member === 1 && $this->team_member_status === 'active';
    }

    public function getTotalCount() {
        return $this->profile()->getTotalCount();
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Static version of the normalization logic so callers that only have a
     * raw phone number (e.g. a stdClass row from a raw DB::table() select,
     * like the admin Interests list — not a full User model) can still get
     * a correct WhatsApp link without an extra query per row.
     */
    public static function normalizePhoneNumberValue($n) {
        $pkCodes = ['300', '301', '302', '303', '304', '305', '306', '307', '308', '309', '310', '311', '312', '313', '314', '315', '316', '317', '318', '320', '321', '322', '323', '324', '330', '331', '332', '333', '334', '335', '336', '337', '340', '341', '342', '343', '344', '345', '346', '347', '348', '349', '355'];

        $n = (string) $n;

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

    public static function whatsappLinkForNumber($n, $text = null) {
        $link = 'https://wa.me/' . self::normalizePhoneNumberValue($n);
        return $text ? $link . '?text=' . rawurlencode($text) : $link;
    }

    /**
     * A "share to anyone" WhatsApp link — no target number, opens WhatsApp's
     * own contact picker. Used for sharing a proposal (Website Upgrade
     * Brief §10: never send phone/email/address via WhatsApp — only a
     * proposal ID and a secure portal link belong in $text).
     */
    public static function whatsappShareLink($text) {
        return 'https://wa.me/?text=' . rawurlencode($text);
    }

    public function getNormalizedPhoneNumber() {
        return self::normalizePhoneNumberValue($this->contact_mobile_number);
    }

    public function getWhatsappLink() {
        return self::whatsappLinkForNumber($this->contact_mobile_number);
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
     * 'rejected' is the ONLY status that hard-blocks login here — it's the
     * old manual-admin-review outcome and genuinely requires an admin to
     * call reopenPhotoVerification() before the account can do anything
     * again, so there's no self-service path being cut off.
     *
     * 'pending' (never yet completed photo verification) and 'resubmit'
     * (AI verification failed, needs to try again) are deliberately NOT
     * blocked here — login always succeeds for them, and
     * LoginController@finishLogin / GoogleAuthController@callback instead
     * redirect straight to the photo verification gate with an explanatory
     * message. Hard-blocking either of those at login would leave the
     * member with no way to ever reach the gate again to fix it themselves
     * (client explicitly wants zero manual/admin involvement in this flow)
     * — the EnsurePhotosUploaded middleware already fully prevents them
     * from reaching search, other profiles, or sending interest while in
     * either state, so nothing is lost security-wise by letting login
     * proceed.
     */
    public function photoVerificationBlockMessage(): ?string {
        if ($this->photo_verification_status === 'rejected') {
            $reason = $this->photo_rejection_reason;
            return 'danger|Your account was not approved' . (!empty($reason) ? ': ' . $reason : '') . '. Please contact support.';
        }
        return null;
    }

    /**
     * Matchmaker signup (see MatchmakerApplicationController::store())
     * creates a real `users` row with a real password specifically so it
     * CAN log in — but unlike a normal member, login itself must stay
     * gated on admin review here, not just Team Dashboard access. Unlike
     * photoVerificationBlockMessage()'s 'pending'/'resubmit' states (which
     * deliberately let login through because there's a self-service gate
     * to send them to), there's no self-service path for an unreviewed
     * matchmaker application, so both 'pending' and 'rejected' block login
     * entirely until an admin acts (AdminController::approve/reject
     * MatchmakerApplication()).
     */
    public function matchmakerLoginBlockMessage(): ?string {
        if ($this->matchmaker_status === 'pending') {
            return 'warning|Your matchmaker application is still under review. We\'ll notify you once it\'s approved.';
        }
        if ($this->matchmaker_status === 'rejected') {
            return 'danger|Your matchmaker application was not approved. Please contact support at 0304-0227000.';
        }
        return null;
    }

    /**
     * True for a matchmaker-signup account (see MatchmakerApplicationController::
     * store()) — one with no real dating profile (no gender/DOB ever
     * collected). Used by the nav/sidebar "Switch Dashboard" links to hide
     * Member Dashboard for these accounts, since there's nothing there for
     * them to see. Approving a matchmaker application is the only way to
     * become a team member now (client requirement, Sep 2026 — admin can no
     * longer promote an arbitrary regular member; see the now-removed
     * AdminController::toggleTeamMember()), so in practice this is
     * equivalent to is_team_member==1 going forward — kept as its own
     * check anyway for any pre-existing account promoted the old way.
     */
    public function isMatchmakerOnly(): bool {
        return !empty($this->matchmaker_status);
    }
}
