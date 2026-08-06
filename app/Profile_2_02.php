<?php

namespace App;

use App\User;
use App\Images;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Profile extends Model {

    protected $table="profile";

    protected $interestLists = null;
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
        if (!empty($this->displaypic))
            return "/users/".($this->showBlur()?$this->getBlurName(explode("/",$this->displaypic)[2]):"thumbnail_".($tiny?"sm_":"").explode("/",$this->displaypic)[2]);
        else if (!empty($this->images)) {
            if (!is_array($this->images))
                $this->images=explode(',', $this->images);
            if (!empty($this->images[0]))
                return "/users/".($this->showBlur()?$this->getBlurName(explode("/",$this->images[0])[2]):"thumbnail_".($tiny?"sm_":"").explode("/",$this->images[0])[2]);
        } else return '/images/'.strtolower($this->gender?$this->gender:'male').'_large.jpg';
    }

    public function getLightGalleryImages() {
        $lightgallery = array();
        if (!empty($this->images)) {
            if (!is_array($this->images))
                $this->images = explode(',', $this->images);
            $images = $this->images;

            for ($i=0; $i<sizeof($images); $i++) {
                $lightgallery[] = [
                    "src" => "/users/".($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):explode("/",$images[$i])[2]),
                    "thumb" => !empty($images[$i])?"/users/".($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):"thumbnail_".explode("/",$images[$i])[2]):"",
                    "mobileSrc" => "/users/".($this->showBlur()?$this->getBlurName(explode("/",$images[$i])[2]):explode("/",$images[$i])[2])
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

    public function inList($dataid, $listname) {
        if ($listname && $dataid) {
            $list = null;
            if ($listname=="interest") {
                $list = $this->getInterestLists();
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
            $subQuery .= " limit " . $limitClause;

            // Main query: Join only the limited users with masterdata
            $query = "select `u`.*, CONCAT(`u`.`first_name`,' ',`u`.`last_name`) AS `name`,
                `mp`.`name` AS `lbl_package`,
                `mms`.`name` AS `lbl_marital_status`,
                `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`,
                `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`,
                `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`,
                `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`,
                `mc`.`name` AS `lbl_city`,
                `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `rmms`.`name` AS `lbl_rmarital_status`,
                `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`,
                `rmr`.`name` AS `lbl_rreligion`,
                `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`,
                `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`,
                `dp`.`img_url` AS `displaypic` ,
                group_concat(`i`.`img_url` separator ',') AS `images`
                from (" . $subQuery . ") as `limited_users`
                inner join `users` `u` on `limited_users`.`id` = `u`.`id`
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
                left join `masterdata` `rmms` on((`u`.`rmarital_status` = `rmms`.`dataid`) and (`rmms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `rmcor` on((`u`.`rcon_of_residence` = `rmcor`.`dataid`) and (`rmcor`.`type` = 'COUNTRY'))
                left join `masterdata` `rmc` on((`u`.`rcity` = `rmc`.`dataid`) and (`rmc`.`type` = 'CITY'))
                left join `masterdata` `rmr` on((`u`.`rreligion` = `rmr`.`dataid`) and (`rmr`.`type` = 'RELIGION'))
                left join `masterdata` `rmcst` on((`u`.`rcaste` = `rmcst`.`dataid`) and (`rmcst`.`type` = 'CASTE'))
                left join `masterdata` `rme` on((`u`.`reducation` = `rme`.`dataid`) and (`rme`.`type` = 'EDUCATION'))
                left join `masterdata` `rmmt` on((`u`.`rmother_tongue` = `rmmt`.`dataid`) and (`rmmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `rmcp` on((`u`.`rcon_pref` = `rmcp`.`dataid`) and (`rmcp`.`type` = 'COUNTRY'))
                left join `images` `dp` on(`u`.`id` = `dp`.`user_id` and `dp`.`displaypic` = '1')
                left join `images` `i` on(`u`.`id` = `i`.`user_id`)
                group by `u`.`id`
                " . (empty($having) ? "" : " having " . $having) . "
                order by " . $orderByClause;
        } else {
            // Fallback to original query structure for complex WHERE clauses
            $query = "select `u`.*, CONCAT(`u`.`first_name`,' ',`u`.`last_name`) AS `name`,
                `mp`.`name` AS `lbl_package`,
                `mms`.`name` AS `lbl_marital_status`,
                `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`,
                `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`,
                `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`,
                `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`,
                `mc`.`name` AS `lbl_city`,
                `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `rmms`.`name` AS `lbl_rmarital_status`,
                `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`,
                `rmr`.`name` AS `lbl_rreligion`,
                `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`,
                `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`,
                `dp`.`img_url` AS `displaypic` ,
                group_concat(`i`.`img_url` separator ',') AS `images`
                from `users` `u`
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
                left join `masterdata` `rmms` on((`u`.`rmarital_status` = `rmms`.`dataid`) and (`rmms`.`type` = 'MARITAL_STATUS'))
                left join `masterdata` `rmcor` on((`u`.`rcon_of_residence` = `rmcor`.`dataid`) and (`rmcor`.`type` = 'COUNTRY'))
                left join `masterdata` `rmc` on((`u`.`rcity` = `rmc`.`dataid`) and (`rmc`.`type` = 'CITY'))
                left join `masterdata` `rmr` on((`u`.`rreligion` = `rmr`.`dataid`) and (`rmr`.`type` = 'RELIGION'))
                left join `masterdata` `rmcst` on((`u`.`rcaste` = `rmcst`.`dataid`) and (`rmcst`.`type` = 'CASTE'))
                left join `masterdata` `rme` on((`u`.`reducation` = `rme`.`dataid`) and (`rme`.`type` = 'EDUCATION'))
                left join `masterdata` `rmmt` on((`u`.`rmother_tongue` = `rmmt`.`dataid`) and (`rmmt`.`type` = 'MOTHER_TONGUE'))
                left join `masterdata` `rmcp` on((`u`.`rcon_pref` = `rmcp`.`dataid`) and (`rmcp`.`type` = 'COUNTRY'))
                left join `images` `dp` on(`u`.`id` = `dp`.`user_id` and `dp`.`displaypic` = '1')
                left join `images` `i` on(`u`.`id` = `i`.`user_id`)"
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
