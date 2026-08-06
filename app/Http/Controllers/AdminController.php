<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\MasterData;
use App\Images;
use App\Interest;
use App\Filtered;
use App\OnlinePackage;
use App\Appointment;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['phpInfo', 'artisanAllClear', 'registerPublishImageVendor']);
    }

    public function phpInfo()
    {
        return phpinfo();
    }

    public function artisanOptimize()
    {
        Artisan::call('optimize');
        return 'All optimized.';
    }

    public function artisanAllClear()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        //Artisan::call('route:cache');
        Artisan::call('view:clear');
        return 'All cleared.';
    }

    public function registerPublishImageVendor()
    {
        Artisan::call('vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"');
        return 'Published.';
    }

    function profiles()
    {
        $pageSize = 10;
        $total = Profile::getTotalCount();
        $view = view('admin.dashboard.memberdata')->with([
            'currentPage' => 1,
            'pageSize' => $pageSize,
            'total' => $total,
            'numPages' => ceil($total / $pageSize),
            'members' => Profile::profiles(null, null, "`u`.`updated_at` DESC", "10"),
            'packages' => MasterData::where('type', '=', 'PACKAGE')->get()
        ]);

        if (request()->ajax()) {
            return [
                'code' => '200',
                'html' => $view->renderSections()['admin-content']
            ]; // only return whats in the main-content section
        } else return $view;
    }

    function refreshProfiles(Request $request)
    {
        if ($request->ajax()) {
            $searchTerm = $request->term;
            $pageSize = $request->pagesize;
            $pageRequested = $request->pagerequested;
            $showOnly = $request->showonly;
            $showWithin = $request->showwithin;
            $where = "";
            $resultCount = null;
            $members = null;

            if (!empty($searchTerm)) {
                $searchTerm = strtolower($searchTerm);
                $where = "(lower(`u`.`first_name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`last_name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`dataid`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`email`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`contact_mobile_number`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`age`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`height`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mr`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mc`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mmt`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mms`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`me`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`u`.`profession`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mc`.`name`) LIKE '%" . $searchTerm . "%'" .
                    " or lower(`mcor`.`name`) LIKE '%" . $searchTerm . "%')";
            }

            $gender = $request->gender;
            if (!empty($gender)) {
                $where = $where . ((empty($where) ? "" : " and ") . "`u`.`gender`='" . $gender . "'");
            }

            $status = $request->status;
            if (!empty($status)) {
                $where = $where . ((empty($where) ? "" : " and ") . "`u`.`active`='" . $status . "'");
            }

            $package = $request->package;
            if (!empty($package)) {
                $where = $where . ((empty($where) ? "" : " and ") . ($package == "null" ? "`u`.`package` IS NULL" : "`u`.`package`='" . $package . "'"));
            }

            if (!empty($showOnly) && $showOnly != 'all' && !empty($showWithin)) {
                $days = 7 * intval($showWithin);
                $where = $where . ((empty($where) ? "" : " and ") . "DATEDIFF(NOW(), `u`.`" . ($showOnly == 'created' ? "created_at" : "updated_at") . "`) between 0 and " . $days);
            }

            $orderBy = "`u`.`updated_at` DESC";

            $members = Profile::profiles($where, null, $orderBy, $pageSize, $pageSize * ($pageRequested - 1));

            $resultCount = Profile::profiles($where, null, null, null, null, true);
            $total = empty($searchTerm) && empty($gender) ? Profile::getTotalCount() : $resultCount;

            return [
                'code' => '200',
                'html' => view('admin.dashboard.memberdata')->with([
                    'currentPage' => $pageRequested,
                    'pageSize' => $pageSize,
                    'total' => $total,
                    'numPages' => ceil($resultCount / $pageSize),
                    'resultCount' => !empty($request->term) ? $resultCount : null,
                    'members' => $members,
                    'packages' => MasterData::where('type', '=', 'PACKAGE')->get()
                ])->renderSections()['member-data']
            ]; // only return whats in the main-content section
        } else return [
            'code' => '200'
        ];
    }

    function interests()
    {

        $pageSize = 10;
        $query = DB::table('interest', 'i')->select(
            DB::raw('us.dataid as sid, CONCAT(us.first_name, " ",us.last_name)
            as sender, ur.dataid as rid, CONCAT(ur.first_name, " ",ur.last_name) as receiver,
            us.email as sender_email,
            ur.email as receiver_email,
            (select group_concat(img_url separator ",") from images where user_id=i.sender) as sender_images,
            (select group_concat(img_url separator ",") from images where user_id=i.receiver) as receiver_images,
            i.interest_back as interest_back, i.created_at as created_at, i.updated_at as updated_at')
        )
            ->leftJoin('users as us', 'i.sender', '=', 'us.id')
            ->leftJoin('users as ur', 'i.receiver', '=', 'ur.id');
        $total = $query->count();
        $view = view('admin.dashboard.interestdata')->with([
            'currentPage' => 1,
            'pageSize' => $pageSize,
            'total' => $total,
            'numPages' => ceil($total / $pageSize),
            'interests' => $query->orderBy('i.updated_at', 'DESC')->limit(10)->get()
        ]);

        if (request()->ajax()) {
            return [
                'code' => '200',
                'html' => $view->renderSections()['admin-content']
            ]; // only return whats in the main-content section
        } else return $view;
    }

    function refreshInterests(Request $request)
    {
        //DB::enableQueryLog(); // Enable query log
        if ($request->ajax()) {
            $searchTerm = $request->term;
            $pageSize = $request->pagesize;
            $pageRequested = $request->pagerequested;
            $query = DB::table('interest', 'i')
                ->select(DB::raw('us.dataid as sid, CONCAT(us.first_name, " ", us.last_name) as sender, ur.dataid as rid,
            CONCAT(ur.first_name, " ", ur.last_name) as receiver,
            us.email as sender_email, ur.email as receiver_email,
            (select group_concat(img_url separator ",") from images where user_id=i.sender) as sender_images,
            (select group_concat(img_url separator ",") from images where user_id=i.receiver) as receiver_images,
            i.interest_back as interest_back, i.created_at as created_at, i.updated_at as updated_at'))
                ->leftJoin('users as us', 'i.sender', '=', 'us.id')
                ->leftJoin('users as ur', 'i.receiver', '=', 'ur.id');
            $resultCount = null;
            $interests = null;

            $total = $query->count();

            $status = $request->status;
            if ($status != null) {
                $query->where('i.interest_back', '=', $status);
            }
            if (!empty($searchTerm)) {
                $searchTerm = strtolower($searchTerm);
                $query = $query->where(function ($query) use ($searchTerm) {
                    $query->orWhereRaw('lower(us.dataid) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(ur.dataid) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(us.first_name) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(us.last_name) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(ur.first_name) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(ur.last_name) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(us.email) LIKE "%' . $searchTerm . '%"')
                        ->orWhereRaw('lower(ur.email) LIKE "%' . $searchTerm . '%"');
                });
            }

            $resultCount = $query->count();
            $interests = $query->orderBy('i.updated_at', 'DESC')->offset($pageSize * ($pageRequested - 1))->limit($pageSize)->get();
            //Log::info(DB::getQueryLog()); // Show results of log

            return [
                'code' => '200',
                'html' => view('admin.dashboard.interestdata')->with([
                    'currentPage' => $pageRequested,
                    'pageSize' => $pageSize,
                    'total' => $total,
                    'numPages' => ceil($resultCount / $pageSize),
                    'resultCount' => !empty($request->term) ? $resultCount : null,
                    'interests' => $interests
                ])->renderSections()['interest-data']
            ]; // only return whats in the main-content section
        } else return [
            'code' => '200'
        ];
    }

    function packages()
    {
        if (request()->ajax()) {
            return [
                'code' => '200',
                'html' => view('admin.dashboard.packages')->with(['packages' => MasterData::where('type', 'PACKAGE')->get()])->renderSections()['admin-content']
            ]; // only return whats in the main-content section
        } else return [
            'code' => '200'
        ];
    }

    /**
     * Admin view: all user appointments.
     */
    function appointments()
    {
        $pageSize = 10;
        $query = DB::table('appointments as a')
            ->select(DB::raw('a.id, a.appointment_date, a.appointment_time, a.subject, a.notes, a.status, a.created_at, a.updated_at, u.dataid as member_id, CONCAT(u.first_name, " ", u.last_name) as member_name, u.email as member_email'))
            ->leftJoin('users as u', 'a.user_id', '=', 'u.id');
        $total = $query->count();
        $view = view('admin.dashboard.appointmentsdata')->with([
            'currentPage' => 1,
            'pageSize' => $pageSize,
            'total' => $total,
            'numPages' => ceil(max(1, $total) / $pageSize),
            'resultCount' => null,
            'appointments' => $query->orderBy('a.appointment_date', 'DESC')->orderBy('a.appointment_time', 'DESC')->limit($pageSize)->get(),
        ]);

        if (request()->ajax()) {
            return [
                'code' => '200',
                'html' => $view->renderSections()['admin-content'],
            ];
        }

        return $view;
    }

    /**
     * AJAX refresh for appointments list.
     */
    function refreshAppointments(Request $request)
    {
        if ($request->ajax()) {
            $searchTerm = $request->term;
            $status = $request->status;
            $pageSize = (int) ($request->pagesize ?? 10);
            $pageRequested = (int) ($request->pagerequested ?? 1);

            $query = DB::table('appointments as a')
                ->select(DB::raw('a.id, a.appointment_date, a.appointment_time, a.subject, a.notes, a.status, a.created_at, a.updated_at, u.dataid as member_id, CONCAT(u.first_name, " ", u.last_name) as member_name, u.email as member_email'))
                ->leftJoin('users as u', 'a.user_id', '=', 'u.id');

            if (!empty($status)) {
                $query->where('a.status', '=', $status);
            }

            if (!empty($searchTerm)) {
                $searchTerm = strtolower($searchTerm);
                $like = '%' . $searchTerm . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->whereRaw('LOWER(u.dataid) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(u.first_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(u.last_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(u.email) LIKE ?', [$like]);
                });
            }

            $resultCount = $query->count();
            $appointments = $query
                ->orderBy('a.appointment_date', 'DESC')
                ->orderBy('a.appointment_time', 'DESC')
                ->offset($pageSize * ($pageRequested - 1))
                ->limit($pageSize)
                ->get();

            return [
                'code' => '200',
                'html' => view('admin.dashboard.appointmentsdata')->with([
                    'currentPage' => $pageRequested,
                    'pageSize' => $pageSize,
                    'total' => $resultCount,
                    'numPages' => ceil(max(1, $resultCount) / $pageSize),
                    'resultCount' => $resultCount,
                    'appointments' => $appointments,
                ])->renderSections()['appointments-data'],
            ];
        }

        return [
            'code' => '200',
        ];
    }

    /**
     * List users who have any package: admin-assigned (offline) or online subscription.
     * Admin only. Filters: search (name/email/dataid), package type (all / admin_only / online_only), specific package.
     */
    public function packageSubscribers()
    {
        $pageSize = 10;
        $adminPackages = MasterData::where('type', 'PACKAGE')->orderBy('id')->get();
        $onlinePackages = OnlinePackage::where('is_active', true)->orderBy('id')->get();
        $subscribers = $this->queryPackageSubscribers(null, null, 1, $pageSize);
        $total = $this->queryPackageSubscribersCount(null, null);
        $view = view('admin.dashboard.package-subscribers')->with([
            'currentPage' => 1,
            'pageSize' => $pageSize,
            'total' => $total,
            'numPages' => (int)max(1, ceil($total / $pageSize)),
            'subscribers' => $subscribers,
            'adminPackages' => $adminPackages,
            'onlinePackages' => $onlinePackages,
        ]);
        if (request()->ajax()) {
            return ['code' => '200', 'html' => $view->renderSections()['admin-content']];
        }
        return $view;
    }

    /**
     * AJAX refresh for package subscribers list (filters + pagination).
     */
    public function refreshPackageSubscribers(Request $request)
    {
        if (!$request->ajax()) {
            return ['code' => '400'];
        }
        $pageSize = (int)($request->pagesize ?? 10);
        $pageRequested = (int)($request->pagerequested ?? 1);
        $pageSize = $pageSize < 1 ? 10 : min($pageSize, 100);
        $subscribers = $this->queryPackageSubscribers($request, null, $pageRequested, $pageSize);
        $total = $this->queryPackageSubscribersCount($request, null);
        $numPages = (int)max(1, ceil($total / $pageSize));
        $adminPackages = MasterData::where('type', 'PACKAGE')->orderBy('id')->get();
        $onlinePackages = OnlinePackage::where('is_active', true)->orderBy('id')->get();
        return [
            'code' => '200',
            'html' => view('admin.dashboard.package-subscribers-data')->with([
                'currentPage' => $pageRequested,
                'pageSize' => $pageSize,
                'total' => $total,
                'numPages' => $numPages,
                'subscribers' => $subscribers,
                'adminPackages' => $adminPackages,
                'onlinePackages' => $onlinePackages,
            ])->render(),
        ];
    }

    private function queryPackageSubscribers(Request $request = null, $whereExtra = null, $page = 1, $pageSize = 10)
    {
        $query = DB::table('users as u')
            ->select(
                'u.id',
                'u.dataid',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.contact_mobile_number',
                'u.package as admin_package_dataid',
                'u.online_package as online_package_dataid',
                'u.online_package_started_at',
                'u.online_package_expires_at',
                'mp.name as admin_package_name',
                'op.name as online_package_name'
            )
            ->leftJoin('masterdata as mp', function ($j) {
                $j->on('u.package', '=', 'mp.dataid')->where('mp.type', '=', 'PACKAGE');
            })
            ->leftJoin('online_packages as op', function ($j) {
                $j->on('u.online_package', '=', 'op.dataid')->where('op.is_active', '=', 1);
            })
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('u.package')->where('u.package', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('u.online_package')->where('u.online_package', '!=', '');
                });
            });
        // Apply filters from request
        if ($request) {
            $term = $request->input('term');
            if (!empty($term)) {
                $t = '%' . strtolower($term) . '%';
                $query->where(function ($q) use ($t) {
                    $q->whereRaw('LOWER(u.first_name) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.last_name) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.email) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.dataid) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.contact_mobile_number) LIKE ?', [$t]);
                });
            }
            $packageType = $request->input('package_type');
            if ($packageType === 'admin_only') {
                $query->whereNotNull('u.package')->where('u.package', '!=', '');
            } elseif ($packageType === 'online_only') {
                $query->whereNotNull('u.online_package')->where('u.online_package', '!=', '');
            }
            $package = $request->input('package');
            if (!empty($package)) {
                $filterBy = $request->input('package_filter_type', 'admin');
                if ($filterBy === 'online') {
                    $query->where('u.online_package', '=', $package);
                } else {
                    $query->where('u.package', '=', $package);
                }
            }
        }
        if ($whereExtra) {
            $query->whereRaw($whereExtra);
        }
        $offset = ($page - 1) * $pageSize;
        return $query->orderBy('u.updated_at', 'desc')->offset($offset)->limit($pageSize)->get();
    }

    private function queryPackageSubscribersCount(Request $request = null, $whereExtra = null)
    {
        $query = DB::table('users as u')
            ->leftJoin('masterdata as mp', function ($j) {
                $j->on('u.package', '=', 'mp.dataid')->where('mp.type', '=', 'PACKAGE');
            })
            ->leftJoin('online_packages as op', function ($j) {
                $j->on('u.online_package', '=', 'op.dataid')->where('op.is_active', '=', 1);
            })
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('u.package')->where('u.package', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('u.online_package')->where('u.online_package', '!=', '');
                });
            });
        if ($request) {
            $term = $request->input('term');
            if (!empty($term)) {
                $t = '%' . strtolower($term) . '%';
                $query->where(function ($q) use ($t) {
                    $q->whereRaw('LOWER(u.first_name) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.last_name) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.email) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.dataid) LIKE ?', [$t])
                        ->orWhereRaw('LOWER(u.contact_mobile_number) LIKE ?', [$t]);
                });
            }
            $packageType = $request->input('package_type');
            if ($packageType === 'admin_only') {
                $query->whereNotNull('u.package')->where('u.package', '!=', '');
            } elseif ($packageType === 'online_only') {
                $query->whereNotNull('u.online_package')->where('u.online_package', '!=', '');
            }
            $package = $request->input('package');
            if (!empty($package)) {
                $filterBy = $request->input('package_filter_type', 'admin');
                if ($filterBy === 'online') {
                    $query->where('u.online_package', '=', $package);
                } else {
                    $query->where('u.package', '=', $package);
                }
            }
        }
        if ($whereExtra) {
            $query->whereRaw($whereExtra);
        }
        return $query->count();
    }

    function renderUpdatePackageModal($dataid)
    {
        if (request()->ajax()) {
            $user = User::retrieveUserObject($dataid);
            $body =             "<form id='package_form' name='package_form' class='form-horizontal' novalidate=''>" .
                "    <input name='_token' value='" . csrf_token() . "' type='hidden'/>" .
                "    <div class='form-group'>" .
                "        <label class='control-label font_dark text-uppercase strong-400'>Package</label>" .
                "        <select name='package' onChange='(this.value,this)' class='form-control form-control-sm' required='required' data-placeholder='Choose a package' data-hide-disabled='true'>" .
                "            <option value=''>Choose one</option>";
            foreach (MasterData::where('type', 'PACKAGE')->get() as $package)
                $body = $body . "            <option value='" . $package->dataid . "'" . ($user->package == $package->dataid ? " selected" : "") . ">" . $package->name . "</option>";

            $body = $body .     "        </select>" .
                "    </div>" .
                "</form>";
            $buttons =          "<button type='button' class='btn btn-primary' id='btn_submit' data-dismiss='modal' onclick='javascript:updatePackage(\"" . $dataid . "\");'>Update Package</button>";
            return [
                'code' => '200',
                'html' => $this->renderModal("Update Package", $body, $buttons)
            ];
        } else return [
            'code' => '200'
        ];
    }

    function fixDataId($table)
    {
        $msg_array = array();

        $objs = null;
        if ($table == "users") {
            $objs = User::all();
        } else if ($table == "images") {
            $objs = Images::all();
        } else {
            return [
                'code' => '200',
                'message' => "No table specified"
            ];
        }

        foreach ($objs as $obj) {
            if (empty($obj->dataid) || $obj->dataid == 'NA') {
                $obj->dataid = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
                $obj->save();
                $message = "Updated " . $table . " dataid for " . $obj->id;
                array_push($msg_array, $message);
                Log::info($message);
            } else {
                $message = "Skipping " . $table . " dataid update for " . $obj->id;
                array_push($msg_array, $message);
                Log::info($message);
            }
        }

        return [
            'code' => '200',
            'message' => $msg_array
        ];
    }

    public function generateThumbnails()
    {
        $msg_array = array();
        $publicPath = public_path('/users');
        $images = Images::all();
        foreach ($images as $image) {
            if (empty($image->name)) {
                $image->name = explode('/', $image->img_url)[2];
                $image->save();
            }
            $name = $image->name;
            Log::info("Processing " . $name);
            // generate thumbnails
            try {
                $thumbnail = Image::make($publicPath . '/' . $name);
                $height = $thumbnail->height();
                $width = $thumbnail->width();
                if ($width > $height) {
                    $thumbnail->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_" . $name);

                    $thumbnail->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_md_" . $name);

                    $thumbnail->resize(100, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_sm_" . $name);
                } else {
                    $thumbnail->resize(null, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_" . $name);

                    $thumbnail->resize(null, 200, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_md_" . $name);

                    $thumbnail->resize(null, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumbnail->save($publicPath . "/thumbnail_sm_" . $name);
                }
                $message = "Generated thumbnails for " . $name;
                array_push($msg_array, $message);
                Log::info($message);
            } catch (\Exception $e) {
                Log::error("Error processing " . $name . ": " . $e->getMessage());
            }
        }
        return [
            'code' => '200',
            'message' => $msg_array
        ];
    }

    public function generateBlurs()
    {
        $msg_array = array();
        $publicPath = public_path('/users');
        $images = Images::all();
        foreach ($images as $image) {
            if (empty($image->name)) {
                $image->name = explode('/', $image->img_url)[2];
                $image->save();
            }

            $name = $image->name;
            Log::info("Processing " . $name);
            // generate blur
            try {
                $blur = Image::make($publicPath . '/' . $name);
                $height = $blur->height();
                $width = $blur->width();
                $blurAmt = 30;
                if ($width > $height) {
                    $blur->resize(210, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $blur->resize(null, 210, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $blur->blur($blurAmt);
                $blurName = explode("_", $name);
                $salt = rand(111, 99999);
                $blur->save($publicPath . '/' . $blurName[0] . $salt . $blurName[1]);
                $image->salt = $salt;
                $image->save();


                $message = "Generated blur for " . $name;
                array_push($msg_array, $message);
                Log::info($message);
            } catch (\Exception $e) {
                Log::error("Error processing " . $name . ": " . $e->getMessage());
            }
        }
        return [
            'code' => '200',
            'message' => $msg_array
        ];
    }

    function toggleActive($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $user = User::retrieveUserObject($dataid);
        if ($user) {
            $user->active = $user->active ? 0 : 1;
            $user->save();

            $displayName = $user->first_name . ' ' . $user->last_name;
            $status = $user->active == 1 ? 'activated' : 'deactivated';
            Log::info("User (" . $loggedInUser->dataid . ") successfully " . $status . " profile of " . $displayName . " (" . $user->email . ")");
            User::retrieveUserObject($dataid, true);
            return [
                'code' => '200',
                'message' => 'User ' . $displayName . ' (' . $user->email . ') successfully ' . $status,
                'active' => $user->active
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not update status of " . $dataid);
            return [
                'code' => '404',
                'message' => 'User was not found (id: ' . $dataid . ')'
            ];
        }
    }

    function deleteProfile($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        DB::beginTransaction();
        try {
            if (!empty($dataid)) {
                $user = User::retrieveUserObject($dataid);
                Log::info("Initiating profile delete for " . $dataid);
                $id = $user->id;
                Images::where('user_id', $id)->delete();
                Log::info("Deleted images");
                Interest::where('sender', $id)->delete();
                Interest::where('receiver', $id)->delete();
                Log::info("Deleted interests");
                Filtered::where('user', $id)->delete();
                Log::info("Deleted Filtered");
                $displayName = $user->first_name . " " . $user->last_name;
                $user->delete();
                DB::commit();
                Log::info("User (" . $loggedInUser->dataid . ") deleted profile of " . $displayName . " (" . $user->email . ")");
            }
            if (request()->ajax()) {
                return [
                    'code' => '200'
                ]; // only return whats in the main-content section
            } else return [
                'code' => '200'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return ['code' => '500', 'message' => $e->getMessage()];
        }
    }

    function resendVerificationEmail($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $user = User::retrieveUserObject($dataid);
        if ($user) {
            $displayName = $user->first_name . " " . $user->last_name;
            $user->sendEmailVerificationNotification();
            Log::info("User (" . $loggedInUser->dataid . ") resent verification email to " . $displayName . " (" . $user->email . ")");
            return [
                'code' => '200',
                'message' => 'Verification email sent to ' . $displayName . ' (' . $user->email . ') successfully.'
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not resend verification email to " . $dataid);
            return [
                'code' => '404',
                'message' => 'User was not found (id: ' . $dataid . ')'
            ];
        }
    }

    function requestPasswordReset($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $user = User::retrieveUserObject($dataid);
        if ($user) {
            $displayName = $user->first_name . " " . $user->last_name;
            $credentials = ['email' => $user->email];
            $response = Password::sendResetLink($credentials, function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    Log::info("User (" . $loggedInUser->dataid . ") sent password reset email for " . $displayName . " (" . $user->email . ")");
                    return [
                        'code' => '200',
                        'message' => 'Password reset request email sent to ' . $displayName . ' (' . $user->email . ') successfully.'
                    ];
                case Password::INVALID_USER:
                    Log::info("User (" . $loggedInUser->dataid . ") could not send password reset email to " . $dataid);
                    return [
                        'code' => '404',
                        'message' => 'Invalid user (' . $user->email . ')'
                    ];
            }
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not send password reset email to " . $dataid);
            return [
                'code' => '404',
                'message' => 'User was not found (id: ' . $dataid . ')'
            ];
        }
    }

    function updateProfilePackage(Request $request, $dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $user = User::retrieveUserObject($dataid);
        if ($user) {
            $displayName = $user->first_name . " " . $user->last_name;
            $package = $request['package'];
            $user->package = $package;
            $user->save();
            Log::info("User (" . $loggedInUser->dataid . ") update package for " . $displayName . " (" . $user->email . ") to " . $package);
            $user = User::retrieveUserObject($dataid, true);
            $profile = $user->profile();
            return [
                'code' => '200',
                'message' => 'Package updated for ' . $displayName . ' (' . $user->email . ') successfully.',
                'response' => $profile->package . "|" . $profile->lbl_package
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not update package for " . $dataid);
            return [
                'code' => '404',
                'message' => 'User was not found (id: ' . $dataid . ')'
            ];
        }
    }

    function addPackage(Request $request)
    {
        $loggedInUser = User::retrieveUserObject();

        $name = $request['package'];
        $package = MasterData::create([
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'type' => "PACKAGE",
            'subtype' => $request['subtype'],
            'name' => $name,
            'description' => $request['description'],
        ]);
        Log::info("User (" . $loggedInUser->dataid . ") created new package " . $name);
        return [
            'code' => '200',
            'message' => 'Package created with name: ' . $name,
            'response' => $this->packages()
        ];
    }

    function updatePackage(Request $request, $dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $package = MasterData::where(['dataid' => $dataid, 'type' => 'PACKAGE'])->first();
        if ($package) {
            $oldName = $package->name;
            $name = $request['package'];
            $package->subtype = $request['subtype'];
            $package->name = $name;
            $package->description = $request['description'];
            $package->save();
            Log::info("User (" . $loggedInUser->dataid . ") updated package details for " . $oldName);
            return [
                'code' => '200',
                'message' => 'Package details updated for ' . $oldName,
                'response' => $this->packages()
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not update package " . $dataid);
            return [
                'code' => '404',
                'message' => 'Package was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function deletePackage($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        DB::beginTransaction();
        try {
            if (!empty($dataid)) {
                $package = MasterData::where(['dataid' => $dataid, 'type' => 'PACKAGE'])->first();
                Log::info("Initiating package delete for " . $dataid);
                $id = $package->id;
                User::where('package', $dataid)->update(['package' => '-1']);
                Log::info("Updated package to -1 for user records that had this package");
                $name = $package->name;
                $package->delete();
                DB::commit();
                Log::info("User (" . $loggedInUser->dataid . ") deleted package " . $name);
            }
            if (request()->ajax()) {
                return [
                    'code' => '200',
                    'response' => $this->packages()
                ]; // only return whats in the main-content section
            } else return [
                'code' => '200'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return ['code' => '500', 'message' => $e->getMessage()];
        }
    }

    public function showListingModal($type, $dataid)
    {
        if (request()->ajax()) {
            $user = User::retrieveUserObject($dataid);
            $members = null;
            if ($type != "interests") {
                $members = $user->getTypeFilteredList($type);
            } else {
                $members = $user->getInterestLists();
            }
            $body = view('member.listing', compact('type', 'members'))->renderSections()['main-content'];
            $buttons = "";
            return [
                'code' => '200',
                'html' => $this->renderModal("", $body, $buttons)
            ];
        } else return [
            'code' => '200'
        ];
    }

    public function renderPackagesModal($dataid = null)
    {
        if (request()->ajax()) {
            $package = null;
            if (!empty($dataid))
                $package = MasterData::firstWhere(['type' => 'PACKAGE', 'dataid' => $dataid]);
            $body = "<form id='package_form' name='package_form' class='form-horizontal' novalidate=''>" .
                "    <input name='_token' value='" . csrf_token() . "' type='hidden'/>" .
                "    <div class='form-group'>" .
                "        <label class='control-label font_dark text-uppercase strong-400'>Name</label>" .
                "        <input type='text' name='package' class='form-control form-control-sm' required='required' data-placeholder='Add a package name' value='" . (!empty($package) ? $package->name : "") . "' />" .
                "    </div>" .
                "    <div class='form-group'>" .
                "        <label class='control-label font_dark text-uppercase strong-400'>Sub Type</label>" .
                "        <input type='text' name='subtype' class='form-control form-control-sm' required='required' data-placeholder='Add a package name' value='" . (!empty($package) ? $package->subtype : "") . "' />" .
                "    </div>" .
                "    <div class='form-group'>" .
                "        <label class='control-label font_dark text-uppercase strong-400'>Description</label>" .
                "        <input type='text' name='description' class='form-control form-control-sm' required='required' data-placeholder='Add a package name' value='" . (!empty($package) ? $package->description : "") . "' />" .
                "    </div>" .
                "</form>";
            $buttons = "<button type='button' class='btn btn-primary' id='btn_submit' data-dismiss='modal' onclick='javascript:$(\"#package_form\").submit();'>" . (!empty($package) ? "Update" : "Add") . " Package</button>";
            return [
                'code' => '200',
                'html' => $this->renderModal((!empty($package) ? "Update" : "Create new") . " Package", $body, $buttons)
            ];
        } else return [
            'code' => '200'
        ];
    }
}
