<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Profile;
use App\User;
use App\MasterData;

class APIController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | API Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles all API functions.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    // public function getToken() {
    //     return response()->json([
    //         'token' => csrf_token(),
    //     ]);
    // }

    public function login(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }

        $email = property_exists($data, "email")?$data->email:"";
        $password = property_exists($data, "password")?$data->password:"";
        $remember = !empty($data->remember);

        if (empty($email) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Email and password are required for login and cannot be empty.',
                'next-screen' => 'login'
            ]);
        }

        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $loggedInUser = User::retrieveUserObject(null, true);
            $loggedInUserProfile = $loggedInUser->profile(true);
            unset($loggedInUserProfile->password);
            unset($loggedInUserProfile->remember_token);

            if (!$loggedInUser->hasVerifiedEmail()) {
                try {
                    $loggedInUser->sendEmailVerificationNotification();
                    Log::info("User email not verified for " . $request->email);
                    Auth::logout();
                } catch (\Exception $e) {
                    if ($loggedInUser) Auth::logout();
                }
                return response()->json([
                    'status' => 'error',
                    'error-type' => 'danger',
                    'error' => 'Email has not been verified. Please check your email (junk/spam also) for a verification link. If you did not receive the email, contact Nimrah at 0307-0227000 for a new email.',
                    'next-screen' => 'login'
                ]);

            } else {
                Log::info("User (".$loggedInUser->dataid.") logged in using " . $request->email);
                if (empty($loggedInUser->package)) {
                    return response()->json([
                        'status' => 'warning',
                        'user' => $loggedInUserProfile,
                        'error-type' => 'warning',
                        'error' => 'No package selected',
                        'next-screen' => 'packages'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'user' => $loggedInUserProfile,
                        'next-screen' => 'home'
                    ]);
                }
            }
        } else {
            Log::info("Failed login attempt detected using " . $request->email);
            return response()->json([
                'status' => 'error',
                'error-type' => 'danger',
                'error' => 'Unable to authenticate. Please check your password and try again.',
                'next-screen' => 'login'
            ]);
        }
    }

    public function emailInUse(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }
        $email = property_exists($data, "email")?$data->email:"";
        return response()->json([
            'status' => 'success',
            'email-in-use' => User::where('email', $email)->count()>0
        ]);
    }

    public function registerUser(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }
        $email = property_exists($data, "email")?$data->email:"";

        $existing = User::where('email',$email)->get();
        if (empty($existing) || $existing->count()==0) {
            $dataid = null;
            try {
                $user = $this->createUser($data);
                event(new Registered($user));
                $user->sendEmailVerificationNotification();
                Log::info("New user registered (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ")");

                $dataid = $user->dataid;

                return response()->json([
                    'status' => 'success',
                    'user' => $user
                ]);
            } catch (\Exception $e) {
                Log::info("User (" . $dataid.") could not be registered. Error: ".$e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'error-type' => 'api error',
                    'error' => 'There was an error creating the profile. '.$e->getMessage()
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'User already registered with this email'
            ]);
        }
    }

    private function createUser($data) {
        return User::create([
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'first_name' => property_exists($data, "first_name")?$data->first_name:null,
            'last_name' => property_exists($data, "last_name")?$data->last_name:null,
            'gender' => property_exists($data, "gender")?$data->gender:null,
            'email' => property_exists($data, "email")?$data->email:null,
            'contact_mobile_number' => property_exists($data, "mobile")?$data->mobile:null,
            'height' => property_exists($data, "height")?$data->height:null,
            'birthday' => property_exists($data, "year") && property_exists($data, "month")
                            && property_exists($data, "day") ?
                            ($data->year . '-' . $data->month . '-' . $data->day)
                            : "",
            'mobile_country' => property_exists($data, "country")?$data->country:null,
            'con_of_residence' => property_exists($data, "country")?$data->country:null,
            'city' => property_exists($data, "city")?$data->city:null,
            'religion' => property_exists($data, "religion")?$data->religion:null,
            'caste' => property_exists($data, "caste")?$data->caste:null,
            'sect' => property_exists($data, "sect")?$data->sect:null,
            'profile_for' => property_exists($data, "on_behalf")?$data->on_behalf:null,
            'mother_tongue' => property_exists($data, "mother_tongue")?$data->mother_tongue:null,
            'marital_status' => property_exists($data, "marital_status")?$data->marital_status:null,
            'education' => property_exists($data, "education")?$data->education:null,
            'profession' => property_exists($data, "profession")?$data->profession:null,
            'password' => Hash::make($data->password)
        ]);
    }

    public function updateUserProfile(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }

        $dataid = $data->dataid;
        $section = $data->section;
        $user = User::retrieveUserObject($dataid);
        try {
            if ($section=="introduction") {
                $user->intro = $data->introduction;
            } else if ($section=="basic_info") {
                $user->first_name = $data->first_name;
                $user->last_name = $data->last_name;
                $user->gender = $data->gender;
                $user->birthday = $data->year . '-' . $data->month . '-' . $data->day;
                $user->marital_status = $data->marital_status;
                $user->children = $data->children;
                $user->area = $data->area;
                $user->profile_for = $data->on_behalf;
                $user->contact_mobile_number = $data->contact_mobile_number;
            } else if ($section=="education_and_career") {
                $user->education = $data->education;
                $user->profession = $data->profession;
                $user->salary = $data->salary;
            } else if ($section=="physical_attributes") {
                $user->height = $data->height;
                $user->weight = $data->weight;
            } else if ($section=="language") {
                $user->mother_tongue = $data->mother_tongue;
                $user->language = $data->language;
            } else if ($section=="residency_information") {
                $user->con_of_birth = $data->con_of_birth;
                $user->con_of_residence = $data->con_of_residence;
                $user->con_of_citizenship = $data->con_of_citizenship;
                $user->con_grew_up = $data->con_grew_up;
                $user->immigration_status = $data->immigration_status;
            } else if ($section=="spiritual_and_social_background") {
                $user->religion = $data->religion;
                $user->caste = $data->caste;
                $user->sect = $data->sect;
            } else if ($section=="permanent_address") {
                $user->con_of_residence = $data->con_of_residence;
                $user->state = $data->state;
                $user->city = $data->city;
                $user->society = $data->society;
            } else if ($section=="family_info") {
                $user->father = $data->father;
                $user->mother = $data->mother;
                $user->brother = $data->brother;
                $user->sister = $data->sister;
            } else if ($section=="additional_personal_details") {
                $user->district = $data->district;
                $user->family_residence = $data->family_residence;
                $user->father_profession = $data->father_profession;
                $user->special_circumstances = $data->special_circumstances;
            } else if ($section=="partner_expectation") {
                $user->rgen_req = $data->rgen_req;
                $user->rage = $data->rage;
                $user->rheight = $data->rheight;
                $user->rmarital_status = $data->rmarital_status;
                $user->rwith_children = $data->rwith_children;
                $user->rcon_of_residence = $data->rcon_of_residence;
                $user->rcity = $data->rcity;
                $user->rreligion = $data->rreligion;
                $user->rcaste = $data->rcaste;
                $user->rsect = $data->rsect;
                $user->reducation = $data->reducation;
                $user->rprofession = $data->rprofession;
                $user->rmother_tongue = $data->rmother_tongue;
                $user->rcon_pref = $data->rcon_pref;
            }
            $user->save();
            Log::info("User (".$dataid.") profile updated. Section: ".$section);

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::info("User (" . $dataid.") profile could not be updated. Error: ".$e->getMessage());
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'There was an error updating the profile. '.$e->getMessage()
            ]);
        }
    }

    public function getDataTypes(Request $request) {
        $types = MasterData::select("type")->distinct()->get();
        return response()->json([
            'status' => 'success',
            'types' => $types
        ]);
    }

    public function getData(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }

        $type = property_exists($data, "type")?$data->type:"";
        $dataid = property_exists($data, "dataid")?$data->dataid:"";
        $list = null;

        if (empty($type)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Type is required for lists.'
            ]);
        }

        if (!empty($dataid)) {
            $list = MasterData::where(['type' => strtoupper($type), 'dataid'=>$dataid])->get();
            return response()->json([
                'status' => 'success',
                'list' => $list
            ]);
        } else {
            if (strtoupper($type)=="STATE") {
                $country = property_exists($data, "country")?$data->country:"";

                if (empty($country)) {
                    return response()->json([
                        'status' => 'error',
                        'error-type' => 'api error',
                        'error' => 'Country code is required for state listing.'
                    ]);
                }
                $list = MasterData::where(['type' => strtoupper($type), 'subtype'=>$country])->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
            } else if (strtoupper($type)=="CITY") {
                $state = property_exists($data, "state")?$data->state:"";
                $country = property_exists($data, "country")?$data->country:"";
                if (empty($state) && empty($country)) {
                    return response()->json([
                        'status' => 'error',
                        'error-type' => 'api error',
                        'error' => 'Country code or State code is required for city listing.'
                    ]);
                }
                $list = empty($state) ?
                        MasterData::where('type', strtoupper($type))->whereIn('subtype', MasterData::where(['type' => 'STATE', 'subtype'=>$country])->select('dataid')->get())->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get()
                        : MasterData::where(['type' => strtoupper($type), 'subtype'=>$state])->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
            } else {
                $list = MasterData::where('type', strtoupper($type))->orderBy('name', 'ASC')->get();
            }

            return response()->json([
                'status' => 'success',
                'list' => $list
            ]);
        }
    }

    public function searchProfiles(Request $request) {
        $data = json_decode($request->getContent());
        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Request data missing'
            ]);
        }

        $pageSize = property_exists($data, "pagesize") ? $data->pagesize:10;
        $pageRequested = property_exists($data, "pagerequested") ? $data->pagerequested : 1;
        $resultCount = null;
        $total = Profile::getTotalCount();

        $gender = property_exists($data, "gender") ? $data->gender : "";
        if (empty($gender)) {
            return response()->json([
                'status' => 'error',
                'error-type' => 'api error',
                'error' => 'Gender is required for searches.'
            ]);
        }

        $where = "`u`.`gender`='".$gender."'";
        $having = "";

        $package = property_exists($data, "package") ? $data->package : 1;
        $member_id = property_exists($data, "member_id") ? $data->member_id : "";

        $where = $where.((empty($where) ? "" : " and ")."`u`.`package`<=".$package);
        if (!empty($member_id)) { // if dataid only search on dataid
            $where = $where.((empty($where) ? "" : " and ")."`u`.`dataid`='".$request->member_id."'");
        } else {
            $aged_from = property_exists($data, "aged_from") ? $data->aged_from : "";
            if (!empty($aged_from)) {
                $aged_to = property_exists($data, "aged_to") ? $data->aged_to : "";
                $where = $where.((empty($where) ? "" : " and ")."FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/ 365.25) between ".$aged_from." and  ".(!empty($aged_to)?$aged_to:75));
            }

            $first_name = property_exists($data, "first_name") ? $data->first_name : "";
            if (!empty($first_name)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`first_name`='".$first_name."'");
            }

            $profession = property_exists($data, "profession") ? $data->profession : "";
            if (!empty($profession)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`profession`='".$profession."'");
            }

            $religion = property_exists($data, "religion") ? $data->religion : "";
            if (!empty($religion)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`religion`='".$religion."'");
            }

            $city = property_exists($data, "city") ? $data->city : "";
            if (!empty($city)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`city`='".$city."'");
            }

            $state = property_exists($data, "state") ? $data->state : "";
            if (!empty($state)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`state`='".$state."'");
            }

            $country = property_exists($data, "country") ? $data->country : "";
            if (!empty($country)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`con_of_residence`='".$country."'");
            }

            $marital_status = property_exists($data, "marital_status") ? $data->marital_status : "";
            if (!empty($marital_status)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`marital_status`='".$marital_status."'");
            }

            $mother_tongue = property_exists($data, "mother_tongue") ? $data->mother_tongue : "";
            if (!empty($mother_tongue)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`mother_tongue`='".$mother_tongue."'");
            }

            $withpics = property_exists($data, "withpics") ? $data->withpics : "";
            if (!empty($withpics)) {
                $having = "`images`<>''";
            }
        }
        $where = $where.((empty($where) ? "" : " and ")."`u`.`active`=1");
        $members = Profile::profiles($where, $having, "`u`.`updated_at` DESC", $pageSize, $pageSize*($pageRequested-1));
        $resultCount = Profile::profiles($where, $having, null, null, null, true);
//dd($members);
        foreach($members as $member) {
            unset($member->password);
            unset($member->remember_token);
        }

        return response()->json([
            'status' => 'success',
            'page-requested' => $pageRequested,
            'page-size' => $pageSize,
            'result-count' => $resultCount,
            'total-count' => $total,
            'member' => $members
        ]);

    }
}
