<?php

namespace App;

use App\User;
use App\Images;
use App\PartnerPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Profile extends Model {

    /**
     * Public URL segment and filesystem folder for member photos (see public/users).
     * Uploads must use this path so getProfileImage() / getLightGalleryImages() stay in sync.
     */
    public const MEMBER_IMAGES_PATH = '/users';

    protected $table="profile";

    protected $interestLists = null;
    protected $photoAccessLists = null;
    protected $followerCount = null;
    protected $userObj = null;

    public function user($refresh = false) {
        if ($this->userObj == null || $refresh) {
            $this->userObj = User::where('dataid', $this->dataid)->first();
        }

        return $this->userObj;
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'age' => 'int'
    ];

    public function isActive() {
        return $this->active;
    }

    public function getActiveLabel() {
        return $this->active ? "Active" : "Pending";
    }

    public function isAdmin() {
        return $this->admin;
    }

    public function getUserlabel() {
        return $this->admin ? "Administrator" : "Member";
    }

    private function showBlur() {
        return Auth::guest();
    }

    private function getBlurName($name) {
        $image = Images::where('name', $name)->first();
        if (!empty($image)) {
            $blurName = explode("_",$name);
            return $blurName[0].$image->salt.$blurName[1];
        }
        return strtolower($this->gender?$this->gender:'male').'_large.jpg';
    }

    public function getProfileImage($tiny = null) {
        $path = null;
        if (!empty($this->displaypic)) {
            $path = self::MEMBER_IMAGES_PATH.'/'.($this->showBlur()?$this->getBlurName(explode("/",$this->displaypic)[2]):"thumbnail_".($tiny?"sm_":"").explode("/",$this->displaypic)[2]);
        } else if (!empty($this->images)) {
            if (!is_array($this->images))
                $this->images=explode(',', $this->images);
            if (!empty($this->images[0]))
                $path = self::MEMBER_IMAGES_PATH.'/'.($this->showBlur()?$this->getBlurName(explode("/",$this->images[0])[2]):"thumbnail_".($tiny?"sm_":"").explode("/",$this->images[0])[2]);
        }

        // Fall back to the gender default avatar both when there's no photo
        // on record, and when the DB points at a file that isn't actually on
        // disk (e.g. a live DB dump imported locally without also copying
        // public/users) — otherwise the background-image div just renders
        // blank with no visible fallback.
        if ($path && file_exists(public_path($path)))
            return $path;
        return self::defaultImage($this->gender);
    }

    /**
     * Default fallback avatar for a gender when no profile photo exists.
     * Appends the file's mtime as a version query so browsers/CDNs pick up
     * a new default image immediately instead of serving a stale cached copy
     * of the same filename.
     */
    public static function defaultImage($gender) {
        $file = strtolower($gender ? $gender : 'male').'_large.jpg';
        $full = public_path('images/'.$file);
        return '/images/'.$file.(file_exists($full) ? '?v='.filemtime($full) : '');
    }

    /**
     * URL of a small flag image for a 2-letter ISO 3166-1 country code
     * (masterdata.abbreviation for COUNTRY rows), e.g. "PK" -> a Pakistan
     * flag PNG. We first tried rendering the Unicode flag emoji directly
     * (regional indicator symbols, no assets needed) but Windows Chrome/
     * Edge has no flag glyphs in its emoji font and falls back to showing
     * the raw two letters ("AE", "PK"...) instead of a flag — so an actual
     * image is the only way to get a flag everywhere. Uses flagcdn.com
     * (the same free public CDN pattern this app already uses for other
     * external assets); returns null for anything that isn't exactly 2
     * letters.
     *
     * flagcdn.com only serves a fixed set of width buckets (w20/w40/w80/
     * w160/...), not arbitrary pixel widths — requesting anything else
     * (e.g. "w24") 404s. $width here is one of those bucket numbers, not
     * a literal pixel size; we request w40 and let CSS scale it down for
     * a crisper (retina-friendly) result at the small display size.
     */
    public static function countryFlagUrl($code, $width = 40) {
        $code = strtolower((string) $code);
        if (!preg_match('/^[a-z]{2}$/', $code)) {
            return null;
        }
        return "https://flagcdn.com/w{$width}/{$code}.png";
    }

    public function getLightGalleryImages() {
        $lightgallery = array();
        if (!empty($this->images)) {
            if (!is_array($this->images))
                $this->images = explode(',', $this->images);
            $images = $this->images;

            for ($i=0; $i<sizeof($images); $i++) {
                $lightgallery[] = [
                    "src" => self::MEMBER_IMAGES_PATH.'/'.($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):explode("/",$images[$i])[2]),
                    "thumb" => !empty($images[$i])?self::MEMBER_IMAGES_PATH.'/'.($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):"thumbnail_".explode("/",$images[$i])[2]):"",
                    "mobileSrc" => self::MEMBER_IMAGES_PATH.'/'.($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):explode("/",$images[$i])[2])
                ];
            }
        }
        return json_encode($lightgallery);
    }

    public function getImageCount() {
        if (!empty($this->images)) {
            if (!is_array($this->images))
                $this->images =explode(',', $this->images);
            return sizeof($this->images);
        } else return 0;
    }

    /**
     * "Profile Completeness" meter shown at the top of the My Profile page.
     * One point per editable section on that page (matching the section
     * names ProfileController::updateProfile() switches on) plus Photo and
     * Partner Preferences — a section counts as filled if any one of its
     * fields is set, same "any field present" convention already used to
     * decide whether to show a section on the public profile view
     * (resources/views/member/profile.blade.php). Legacy/frozen fields
     * (users.r*, brother/sister) are intentionally left out.
     */
    public function profileCompleteness(): array {
        $sections = [
            'Photo' => $this->getImageCount() > 0 || !empty($this->displaypic),
            'Introduction' => !empty($this->intro),
            'Basic Info' => !empty($this->first_name) && !empty($this->last_name) && !empty($this->gender) && !empty($this->birthday),
            'Education & Career' => !empty($this->education) || !empty($this->profession) || !empty($this->salary),
            'Physical Attributes' => !empty($this->height) || !empty($this->weight),
            'Language' => !empty($this->mother_tongue) || !empty($this->language),
            'Residency Information' => !empty($this->con_of_birth) || !empty($this->con_of_residence) || !empty($this->con_of_citizenship) || !empty($this->con_grew_up) || !empty($this->immigration_status),
            'Spiritual & Social Background' => !empty($this->religion) || !empty($this->caste) || !empty($this->sect),
            'Permanent Address' => !empty($this->state) || !empty($this->city) || !empty($this->society),
            'Family Info' => !empty($this->father) || !empty($this->mother) || !empty($this->brothers_count) || !empty($this->sisters_count) || !empty($this->siblings),
            'Additional Personal Details' => !empty($this->district) || !empty($this->family_residence) || !empty($this->father_profession) || !empty($this->mother_profession) || !empty($this->special_circumstances) || !empty($this->family_values),
            'Partner Preferences' => !empty($this->rage_min) || !empty($this->rgen_req) || !empty($this->rheight) || !empty($this->rmarital_status)
                || !empty($this->rcon_of_residence) || !empty($this->rcity) || !empty($this->rreligion) || !empty($this->rcaste)
                || !empty($this->reducation) || !empty($this->rprofession) || !empty($this->rmother_tongue) || !empty($this->rlanguages) || !empty($this->rcon_pref),
        ];

        $filled = count(array_filter($sections));
        $total = count($sections);

        return [
            'percent' => $total > 0 ? (int) round($filled / $total * 100) : 0,
            'filled' => $filled,
            'total' => $total,
            'sections' => $sections,
        ];
    }

    /**
     * Real, per-viewer Compatibility Score — how well THIS profile matches
     * the VIEWER's own saved Partner Preferences (not how filled-out this
     * profile is; that's profileCompleteness() above). Only checks
     * criteria the viewer actually set a preference for — an unset
     * preference (e.g. no religion preference chosen) isn't counted for or
     * against the match, since there's nothing to compare.
     *
     * $preferenceLabels is the same resolved-label array
     * ProfileController::profile() builds for the "Looking For" tab
     * (['city' => ..., 'country' => ..., ...]) — reused here purely for
     * display text like "UK location matched", not for the matching logic
     * itself (which compares raw masterdata ids).
     *
     * Deliberately does NOT attempt Height/Weight/Sect (free-text fields on
     * both sides — format varies too much to compare reliably) or "Family
     * background" / "Lifestyle" as blanket checks (Partner Preferences
     * doesn't store a desired family type or lifestyle — nothing real to
     * compare against, so no fabricated check for those).
     *
     * Returns null if the viewer has no usable preference data at all —
     * the caller shows a "set your preferences" prompt in that case
     * instead of a meaningless 0%/100%.
     */
    public function compatibilityWith(?PartnerPreference $preference, array $preferenceLabels = []): ?array {
        if (empty($preference)) {
            return null;
        }

        $checks = [];

        if (!empty($preference->age_min) || !empty($preference->age_max)) {
            $age = !empty($this->birthday) ? Carbon::parse($this->birthday)->age : null;
            $matched = $age !== null
                && (empty($preference->age_min) || $age >= $preference->age_min)
                && (empty($preference->age_max) || $age <= $preference->age_max);
            $checks[] = ['label' => 'Preferred age range matched', 'matched' => (bool) $matched];
        }

        // City is the most specific ask; country and "preferred country (if
        // different from residence)" are both acceptable alternatives to it.
        if (!empty($preference->city_id) || !empty($preference->country_id) || !empty($preference->preferred_country_id)) {
            $matched = (!empty($preference->city_id) && $this->city == $preference->city_id)
                || (!empty($preference->country_id) && $this->con_of_residence == $preference->country_id)
                || (!empty($preference->preferred_country_id) && $this->con_of_residence == $preference->preferred_country_id);
            $locationLabel = $preferenceLabels['city'] ?? $preferenceLabels['country'] ?? $preferenceLabels['preferred_country'] ?? null;
            $checks[] = ['label' => $locationLabel ? "{$locationLabel} location matched" : 'Location preference matched', 'matched' => (bool) $matched];
        }

        if (!empty($preference->religion_id)) {
            $checks[] = ['label' => 'Religion preference matched', 'matched' => $this->religion == $preference->religion_id];
        }

        if (!empty($preference->caste_id)) {
            $checks[] = ['label' => 'Caste preference matched', 'matched' => $this->caste == $preference->caste_id];
        }

        if (!empty($preference->marital_status)) {
            $checks[] = ['label' => 'Marital status matched', 'matched' => $this->marital_status == $preference->marital_status];
        }

        if (!empty($preference->education_id)) {
            $checks[] = ['label' => 'Education preference matched', 'matched' => $this->education == $preference->education_id];
        }

        if (!empty($preference->mother_tongue_id)) {
            $checks[] = ['label' => 'Mother tongue matched', 'matched' => $this->mother_tongue == $preference->mother_tongue_id];
        }

        // "Does not matter" means the viewer explicitly has no preference
        // here — same as leaving it unset, so no check either way.
        if (!empty($preference->with_children) && $preference->with_children !== 'Does not matter') {
            $hasChildren = !empty($this->children) && $this->children > 0;
            $matched = $preference->with_children === 'Yes' ? $hasChildren : !$hasChildren;
            $checks[] = ['label' => 'Children preference matched', 'matched' => $matched];
        }

        if (empty($checks)) {
            return null; // a preference row exists but nothing on it is usable for matching
        }

        $matchedCount = count(array_filter($checks, fn ($c) => $c['matched']));
        $total = count($checks);

        return [
            'percent' => (int) round($matchedCount / $total * 100),
            'matched' => $matchedCount,
            'total' => $total,
            'checks' => $checks,
        ];
    }

    public function getFilteredCount($type) {
        return 0;
    }

    public function getInterestLists($refresh = null) {
        if ($refresh || $this->interestLists == null)
            $this->updateInterestLists();
        return $this->interestLists;
    }

    public function updateInterestLists() {
        $dataRow = null;
        try {
            $sentList = array();
            $result = DB::table("interest as i")
                ->select("u.dataid", "u.first_name", "u.last_name", "u.email", "u.gender", "u.birthday", "u.height",
                    "mr.name as lbl_religion", "mc.name as lbl_caste", "mmt.name as lbl_mother_tongue", "mms.name as lbl_marital_status", "mcor.name as lbl_con_of_residence",
                    "i.interest_back")
                ->leftJoin("users as u", "u.id", "=", "i.receiver")
                ->leftJoin("masterdata as mr", function($join) {
                    $join->on("u.religion", "=", "mr.dataid");
                    $join->where("mr.type","=","RELIGION");
                })
                ->leftJoin("masterdata as mc", function($join) {
                    $join->on("u.caste", "=", "mc.dataid");
                    $join->where("mc.type","=","CASTE");
                })
                ->leftJoin("masterdata as mmt", function($join) {
                    $join->on("u.mother_tongue", "=", "mmt.dataid");
                    $join->where("mmt.type","=","MOTHER_TONGUE");
                })
                ->leftJoin("masterdata as mms", function($join) {
                    $join->on("u.marital_status", "=", "mms.dataid");
                    $join->where("mms.type","=","MARITAL_STATUS");
                })
                ->leftJoin("masterdata as mcor", function($join) {
                    $join->on("u.con_of_residence", "=", "mcor.dataid");
                    $join->where("mcor.type","=","COUNTRY");
                })
                ->where("i.sender", $this->id)
                ->orderBy("i.updated_at", "DESC")->get();
            foreach($result as $row) {
                $sentList[$row->dataid] = $row;
            }

            $receivedList = array();
            $result = DB::table("interest as i")
                ->select("u.dataid", "u.first_name", "u.last_name", "u.email", "u.gender", "u.birthday", "u.height",
                    "mr.name as lbl_religion", "mc.name as lbl_caste", "mmt.name as lbl_mother_tongue", "mms.name as lbl_marital_status", "mcor.name as lbl_con_of_residence",
                    "i.interest_back")
                ->leftJoin("users as u", "u.id", "=", "i.sender")
                ->leftJoin("masterdata as mr", function($join) {
                    $join->on("u.religion", "=", "mr.dataid");
                    $join->where("mr.type","=","RELIGION");
                })
                ->leftJoin("masterdata as mc", function($join) {
                    $join->on("u.caste", "=", "mc.dataid");
                    $join->where("mc.type","=","CASTE");
                })
                ->leftJoin("masterdata as mmt", function($join) {
                    $join->on("u.mother_tongue", "=", "mmt.dataid");
                    $join->where("mmt.type","=","MOTHER_TONGUE");
                })
                ->leftJoin("masterdata as mms", function($join) {
                    $join->on("u.marital_status", "=", "mms.dataid");
                    $join->where("mms.type","=","MARITAL_STATUS");
                })
                ->leftJoin("masterdata as mcor", function($join) {
                    $join->on("u.con_of_residence", "=", "mcor.dataid");
                    $join->where("mcor.type","=","COUNTRY");
                })
                ->where("i.receiver", $this->id)
                ->orderBy("i.updated_at", "DESC")->get();
            foreach($result as $row) {
                $receivedList[$row->dataid] = $row;
            }

            $this->interestLists = [
                'sent' => $sentList,
                'received' => $receivedList
            ];
            return true;
        } catch (\Exception $e) {
            Log::error("Error encountered while updating interest list for ".$this->dataid." - ".$e->getMessage());
            return false;
        }
    }

    /**
     * "Request to view hidden photos" — same sent/received shape as
     * getInterestLists()/updateInterestLists() above, backed by
     * photo_access_requests (uid=requester, pid=owner, allowed=0/1/-1)
     * instead of `interest`. Powers member.photoaccessdata (My Photo
     * Access Requests) and admin.dashboard.photoaccess*.
     */
    public function getPhotoAccessLists($refresh = null) {
        if ($refresh || $this->photoAccessLists == null)
            $this->updatePhotoAccessLists();
        return $this->photoAccessLists;
    }

    public function updatePhotoAccessLists() {
        try {
            $sentList = array();
            $result = DB::table("photo_access_requests as p")
                ->select("u.dataid", "u.first_name", "u.last_name", "u.email", "u.gender", "u.birthday", "u.height",
                    "mr.name as lbl_religion", "mc.name as lbl_caste", "mmt.name as lbl_mother_tongue", "mms.name as lbl_marital_status", "mcor.name as lbl_con_of_residence",
                    "p.allowed")
                ->leftJoin("users as u", "u.id", "=", "p.pid")
                ->leftJoin("masterdata as mr", function($join) {
                    $join->on("u.religion", "=", "mr.dataid");
                    $join->where("mr.type","=","RELIGION");
                })
                ->leftJoin("masterdata as mc", function($join) {
                    $join->on("u.caste", "=", "mc.dataid");
                    $join->where("mc.type","=","CASTE");
                })
                ->leftJoin("masterdata as mmt", function($join) {
                    $join->on("u.mother_tongue", "=", "mmt.dataid");
                    $join->where("mmt.type","=","MOTHER_TONGUE");
                })
                ->leftJoin("masterdata as mms", function($join) {
                    $join->on("u.marital_status", "=", "mms.dataid");
                    $join->where("mms.type","=","MARITAL_STATUS");
                })
                ->leftJoin("masterdata as mcor", function($join) {
                    $join->on("u.con_of_residence", "=", "mcor.dataid");
                    $join->where("mcor.type","=","COUNTRY");
                })
                ->where("p.uid", $this->id)
                ->orderBy("p.updated_at", "DESC")->get();
            foreach($result as $row) {
                $sentList[$row->dataid] = $row;
            }

            $receivedList = array();
            $result = DB::table("photo_access_requests as p")
                ->select("u.dataid", "u.first_name", "u.last_name", "u.email", "u.gender", "u.birthday", "u.height",
                    "mr.name as lbl_religion", "mc.name as lbl_caste", "mmt.name as lbl_mother_tongue", "mms.name as lbl_marital_status", "mcor.name as lbl_con_of_residence",
                    "p.allowed")
                ->leftJoin("users as u", "u.id", "=", "p.uid")
                ->leftJoin("masterdata as mr", function($join) {
                    $join->on("u.religion", "=", "mr.dataid");
                    $join->where("mr.type","=","RELIGION");
                })
                ->leftJoin("masterdata as mc", function($join) {
                    $join->on("u.caste", "=", "mc.dataid");
                    $join->where("mc.type","=","CASTE");
                })
                ->leftJoin("masterdata as mmt", function($join) {
                    $join->on("u.mother_tongue", "=", "mmt.dataid");
                    $join->where("mmt.type","=","MOTHER_TONGUE");
                })
                ->leftJoin("masterdata as mms", function($join) {
                    $join->on("u.marital_status", "=", "mms.dataid");
                    $join->where("mms.type","=","MARITAL_STATUS");
                })
                ->leftJoin("masterdata as mcor", function($join) {
                    $join->on("u.con_of_residence", "=", "mcor.dataid");
                    $join->where("mcor.type","=","COUNTRY");
                })
                ->where("p.pid", $this->id)
                ->orderBy("p.updated_at", "DESC")->get();
            foreach($result as $row) {
                $receivedList[$row->dataid] = $row;
            }

            $this->photoAccessLists = [
                'sent' => $sentList,
                'received' => $receivedList
            ];
            return true;
        } catch (\Exception $e) {
            Log::error("Error encountered while updating photo access list for ".$this->dataid." - ".$e->getMessage());
            return false;
        }
    }

    public function inList($dataid, $listname) {
        if ($listname && $dataid) {
            $list = null;
            if ($listname=="interest") {
                $list = $this->getInterestLists();
                if ($list != null && array_key_exists('sent', $list))
                    $list = $list['sent'];
                else return false;
            } else if ($listname=="photoaccess") {
                $list = $this->getPhotoAccessLists();
                if ($list != null && array_key_exists('sent', $list))
                    $list = $list['sent'];
                else return false;
            } else {
                $list = $this->getFilteredList();
                if ($list != null && array_key_exists($listname, $list))
                    $list = $list[$listname];
                else return false;
            }

            if ($list != null && array_key_exists($dataid, $list))
                return true;
        }
        return false;
    }

    public function getInterest($dataid) {
        if ($this->inList($dataid, "interest")) {
            return $this->getInterestLists()["sent"][$dataid]->interest_back;
        }
        return -1;
    }

    /**
     * This member's own request to view $dataid's hidden photos — null if
     * never requested, else 0 (requested/pending), 1 (granted) or -1
     * (declined). Used both for the "My Photo Access Requests" page and to
     * gate a hidden photo on someone else's profile (see
     * ProfileController::profile()).
     */
    public function getPhotoAccess($dataid) {
        if ($this->inList($dataid, "photoaccess")) {
            return (int) $this->getPhotoAccessLists()["sent"][$dataid]->allowed;
        }
        return null;
    }

    public static function getTotalCount() {
        return User::select('id')->count();
    }

    public static function profiles($where = null, $having = null, $orderBy = null, $limit = null, $offset = null, $count = null) {
        // For count queries, use a simpler approach without all the joins
        if (!empty($count)) {
            // If WHERE references joined tables, we need to do a proper count with joins
            $hasJoinedTableRefs = !empty($where) && preg_match('/`(mr|mc|mmt|mms|me|mcor|mcob|mcoc|mcgu|ms|mcst|rmms|rmcor|rmc|rmr|rmcst|rme|rmmt|rmcp|mp|ml|dp|i)`\./', $where);
            
            if ($hasJoinedTableRefs) {
                // For complex WHERE with joined table references, use a subquery count
                $countQuery = "select count(distinct `u`.`id`) as total from `users` `u`
                    left join `masterdata` `mp` on((`u`.`package` = `mp`.`dataid`) and (`mp`.`type` = 'PACKAGE'))
                    left join `masterdata` `mms` on((`u`.`marital_status` = `mms`.`dataid`) and (`mms`.`type` = 'MARITAL_STATUS'))
                    left join `masterdata` `mr` on((`u`.`religion` = `mr`.`dataid`) and (`mr`.`type` = 'RELIGION'))
                    left join `masterdata` `mmt` on((`u`.`mother_tongue` = `mmt`.`dataid`) and (`mmt`.`type` = 'MOTHER_TONGUE'))
                    left join `masterdata` `ml` on((`u`.`language` = `ml`.`dataid`) and (`ml`.`type` = 'MOTHER_TONGUE'))
                    left join `masterdata` `mcor` on((`u`.`con_of_residence` = `mcor`.`dataid`) and (`mcor`.`type` = 'COUNTRY'))
                    left join `masterdata` `mcob` on((`u`.`con_of_birth` = `mcob`.`dataid`) and (`mcob`.`type` = 'COUNTRY'))
                    left join `masterdata` `mcoc` on((`u`.`con_of_citizenship` = `mcoc`.`dataid`) and (`mcoc`.`type` = 'COUNTRY'))
                    left join `masterdata` `mcgu` on((`u`.`con_grew_up` = `mcgu`.`dataid`) and (`mcgu`.`type` = 'COUNTRY'))
                    left join `masterdata` `ms` on((`u`.`state` = `ms`.`dataid`) and (`ms`.`type` = 'STATE'))
                    left join `masterdata` `mc` on((`u`.`city` = `mc`.`dataid`) and (`mc`.`type` = 'CITY'))
                    left join `masterdata` `mcst` on((`u`.`caste` = `mcst`.`dataid`) and (`mcst`.`type` = 'CASTE'))
                    left join `masterdata` `me` on((`u`.`education` = `me`.`dataid`) and (`me`.`type` = 'EDUCATION'))
                    where (" . $where . ")";
            } else {
                $countQuery = "select count(*) as total from `users` `u`";
                if (!empty($where)) {
                    $countQuery .= " where (" . $where . ")";
                }
            }
            $countResult = DB::select($countQuery);
            return !empty($countResult) ? (int)$countResult[0]->total : 0;
        }

        // Check if WHERE clause references joined tables (optimization only works for simple WHERE clauses)
        $hasJoinedTableRefs = !empty($where) && preg_match('/`(mr|mc|mmt|mms|me|mcor|mcob|mcoc|mcgu|ms|mcst|rmms|rmcor|rmc|rmr|rmcst|rme|rmmt|rmcp|mp|ml|dp|i)`\./', $where);
        
        $orderByClause = !empty($orderBy) ? $orderBy : "`u`.`updated_at` DESC";
        $limitClause = !empty($limit) ? (empty($offset) ? $limit : $offset . ", " . $limit) : "";

        // Optimized query: First get limited user IDs, then join (only if WHERE doesn't reference joined tables)
        if (!$hasJoinedTableRefs && !empty($limitClause)) {
            // Build the subquery to get limited user IDs first
            $subQuery = "select `u`.`id` from `users` `u`";
            if (!empty($where)) {
                $subQuery .= " where (" . $where . ")";
            }
            $subQuery .= " order by " . $orderByClause;
            // $subQuery .= " limit " . $limitClause;

            // Main query: Join only the limited users with masterdata
            $query = "select `u`.*, CONCAT(`u`.`first_name`,' ',`u`.`last_name`) AS `name`,
                COALESCE(`op`.`name`, `mp`.`name`) AS `lbl_package`,
                `mms`.`name` AS `lbl_marital_status`,
                `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`,
                `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`,
                `mcor`.`abbreviation` AS `con_of_residence_code`,
                `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`,
                `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`,
                `mc`.`name` AS `lbl_city`,
                `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `pp`.`age_min` AS `rage_min`,
                `pp`.`age_max` AS `rage_max`,
                `pp`.`height` AS `rheight`,
                `pp`.`weight` AS `rweight`,
                `pp`.`marital_status` AS `rmarital_status`,
                `pp`.`with_children` AS `rwith_children`,
                `pp`.`country_id` AS `rcon_of_residence`,
                `pp`.`city_id` AS `rcity`,
                `pp`.`religion_id` AS `rreligion`,
                `pp`.`caste_id` AS `rcaste`,
                `pp`.`sect` AS `rsect`,
                `pp`.`education_id` AS `reducation`,
                `pp`.`profession` AS `rprofession`,
                `pp`.`mother_tongue_id` AS `rmother_tongue`,
                `pp`.`languages` AS `rlanguages`,
                `pp`.`preferred_country_id` AS `rcon_pref`,
                `rmms`.`name` AS `lbl_rmarital_status`,
                `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`,
                `rmr`.`name` AS `lbl_rreligion`,
                `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`,
                `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`,
                `rms`.`name` AS `lbl_rstate`,
                `dp`.`img_url` AS `displaypic` ,
                group_concat(`i`.`img_url` separator ',') AS `images`
                from (" . $subQuery . ") as `limited_users`
                inner join `users` `u` on `limited_users`.`id` = `u`.`id`
                left join `masterdata` `mp` on((`u`.`package` = `mp`.`dataid`) and (`mp`.`type` = 'PACKAGE'))
                left join `online_packages` `op` on((`u`.`online_package` = `op`.`dataid`) and (`op`.`is_active` = 1))
                left join `masterdata` `mms` on((`u`.`marital_status` = `mms`.`dataid`) and (`mms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `mr` on((`u`.`religion` = `mr`.`dataid`) and (`mr`.`type` = 'RELIGION'))
                left join `masterdata` `mmt` on((`u`.`mother_tongue` = `mmt`.`dataid`) and (`mmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `ml` on((`u`.`language` = `ml`.`dataid`) and (`ml`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `mcor` on((`u`.`con_of_residence` = `mcor`.`dataid`) and (`mcor`.`type` = 'COUNTRY'))
                left join `masterdata` `mcob` on((`u`.`con_of_birth` = `mcob`.`dataid`) and (`mcob`.`type` = 'COUNTRY'))
                left join `masterdata` `mcoc` on((`u`.`con_of_citizenship` = `mcoc`.`dataid`) and (`mcoc`.`type` = 'COUNTRY'))
                left join `masterdata` `mcgu` on((`u`.`con_grew_up` = `mcgu`.`dataid`) and (`mcgu`.`type` = 'COUNTRY'))
                left join `masterdata` `ms` on((`u`.`state` = `ms`.`dataid`) and (`ms`.`type` = 'STATE'))
                left join `masterdata` `mc` on((`u`.`city` = `mc`.`dataid`) and (`mc`.`type` = 'CITY'))
                left join `masterdata` `mcst` on((`u`.`caste` = `mcst`.`dataid`) and (`mcst`.`type` = 'CASTE'))
                left join `masterdata` `me` on((`u`.`education` = `me`.`dataid`) and (`me`.`type` = 'EDUCATION'))
                left join `partner_preferences` `pp` on(`pp`.`user_id` = `u`.`id`)
                left join `masterdata` `rmms` on((`pp`.`marital_status` = `rmms`.`dataid`) and (`rmms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `rmcor` on((`pp`.`country_id` = `rmcor`.`dataid`) and (`rmcor`.`type` = 'COUNTRY'))
                left join `masterdata` `rmc` on((`pp`.`city_id` = `rmc`.`dataid`) and (`rmc`.`type` = 'CITY'))
                left join `masterdata` `rmr` on((`pp`.`religion_id` = `rmr`.`dataid`) and (`rmr`.`type` = 'RELIGION'))
                left join `masterdata` `rmcst` on((`pp`.`caste_id` = `rmcst`.`dataid`) and (`rmcst`.`type` = 'CASTE'))
                left join `masterdata` `rme` on((`pp`.`education_id` = `rme`.`dataid`) and (`rme`.`type` = 'EDUCATION'))
                left join `masterdata` `rmmt` on((`pp`.`mother_tongue_id` = `rmmt`.`dataid`) and (`rmmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `rmcp` on((`pp`.`preferred_country_id` = `rmcp`.`dataid`) and (`rmcp`.`type` = 'COUNTRY'))
                left join `masterdata` `rms` on((`pp`.`state_id` = `rms`.`dataid`) and (`rms`.`type` = 'STATE'))
                left join `images` `dp` on(`u`.`id` = `dp`.`user_id` and `dp`.`displaypic` = '1' and `dp`.`visibility` = 'Public')
                left join `images` `i` on(`u`.`id` = `i`.`user_id` and `i`.`visibility` = 'Public')
                group by `u`.`id`
                " . (empty($having) ? "" : " having " . $having) . "
                order by " . $orderByClause . "
                limit " . $limitClause;
        } else {
            // Fallback to original query structure for complex WHERE clauses
            $query = "select `u`.*, CONCAT(`u`.`first_name`,' ',`u`.`last_name`) AS `name`,
                COALESCE(`op`.`name`, `mp`.`name`) AS `lbl_package`,
                `mms`.`name` AS `lbl_marital_status`,
                `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`,
                `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`,
                `mcor`.`abbreviation` AS `con_of_residence_code`,
                `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`,
                `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`,
                `mc`.`name` AS `lbl_city`,
                `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `pp`.`age_min` AS `rage_min`,
                `pp`.`age_max` AS `rage_max`,
                `pp`.`height` AS `rheight`,
                `pp`.`weight` AS `rweight`,
                `pp`.`marital_status` AS `rmarital_status`,
                `pp`.`with_children` AS `rwith_children`,
                `pp`.`country_id` AS `rcon_of_residence`,
                `pp`.`city_id` AS `rcity`,
                `pp`.`religion_id` AS `rreligion`,
                `pp`.`caste_id` AS `rcaste`,
                `pp`.`sect` AS `rsect`,
                `pp`.`education_id` AS `reducation`,
                `pp`.`profession` AS `rprofession`,
                `pp`.`mother_tongue_id` AS `rmother_tongue`,
                `pp`.`languages` AS `rlanguages`,
                `pp`.`preferred_country_id` AS `rcon_pref`,
                `rmms`.`name` AS `lbl_rmarital_status`,
                `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`,
                `rmr`.`name` AS `lbl_rreligion`,
                `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`,
                `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`,
                `rms`.`name` AS `lbl_rstate`,
                `dp`.`img_url` AS `displaypic` ,
                group_concat(`i`.`img_url` separator ',') AS `images`
                from `users` `u`
                left join `masterdata` `mp` on((`u`.`package` = `mp`.`dataid`) and (`mp`.`type` = 'PACKAGE'))
                left join `online_packages` `op` on((`u`.`online_package` = `op`.`dataid`) and (`op`.`is_active` = 1))
                left join `masterdata` `mms` on((`u`.`marital_status` = `mms`.`dataid`) and (`mms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `mr` on((`u`.`religion` = `mr`.`dataid`) and (`mr`.`type` = 'RELIGION'))
                left join `masterdata` `mmt` on((`u`.`mother_tongue` = `mmt`.`dataid`) and (`mmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `ml` on((`u`.`language` = `ml`.`dataid`) and (`ml`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `mcor` on((`u`.`con_of_residence` = `mcor`.`dataid`) and (`mcor`.`type` = 'COUNTRY'))
                left join `masterdata` `mcob` on((`u`.`con_of_birth` = `mcob`.`dataid`) and (`mcob`.`type` = 'COUNTRY'))
                left join `masterdata` `mcoc` on((`u`.`con_of_citizenship` = `mcoc`.`dataid`) and (`mcoc`.`type` = 'COUNTRY'))
                left join `masterdata` `mcgu` on((`u`.`con_grew_up` = `mcgu`.`dataid`) and (`mcgu`.`type` = 'COUNTRY'))
                left join `masterdata` `ms` on((`u`.`state` = `ms`.`dataid`) and (`ms`.`type` = 'STATE'))
                left join `masterdata` `mc` on((`u`.`city` = `mc`.`dataid`) and (`mc`.`type` = 'CITY'))
                left join `masterdata` `mcst` on((`u`.`caste` = `mcst`.`dataid`) and (`mcst`.`type` = 'CASTE'))
                left join `masterdata` `me` on((`u`.`education` = `me`.`dataid`) and (`me`.`type` = 'EDUCATION'))
                left join `partner_preferences` `pp` on(`pp`.`user_id` = `u`.`id`)
                left join `masterdata` `rmms` on((`pp`.`marital_status` = `rmms`.`dataid`) and (`rmms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `rmcor` on((`pp`.`country_id` = `rmcor`.`dataid`) and (`rmcor`.`type` = 'COUNTRY'))
                left join `masterdata` `rmc` on((`pp`.`city_id` = `rmc`.`dataid`) and (`rmc`.`type` = 'CITY'))
                left join `masterdata` `rmr` on((`pp`.`religion_id` = `rmr`.`dataid`) and (`rmr`.`type` = 'RELIGION'))
                left join `masterdata` `rmcst` on((`pp`.`caste_id` = `rmcst`.`dataid`) and (`rmcst`.`type` = 'CASTE'))
                left join `masterdata` `rme` on((`pp`.`education_id` = `rme`.`dataid`) and (`rme`.`type` = 'EDUCATION'))
                left join `masterdata` `rmmt` on((`pp`.`mother_tongue_id` = `rmmt`.`dataid`) and (`rmmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `rmcp` on((`pp`.`preferred_country_id` = `rmcp`.`dataid`) and (`rmcp`.`type` = 'COUNTRY'))
                left join `masterdata` `rms` on((`pp`.`state_id` = `rms`.`dataid`) and (`rms`.`type` = 'STATE'))
                left join `images` `dp` on(`u`.`id` = `dp`.`user_id` and `dp`.`displaypic` = '1' and `dp`.`visibility` = 'Public')
                left join `images` `i` on(`u`.`id` = `i`.`user_id` and `i`.`visibility` = 'Public')"
                .(empty($where)?"":" where (".$where.") ")
                ." group by `u`.`id`"
                .(empty($having)?"":" having ".$having)
                .(empty($orderBy)?"":" order by ".$orderByClause)
                .(empty($limitClause)?"":" limit ".$limitClause);
        }
        
        $results = DB::select($query);
        return Profile::hydrate( $results );
    }
}
