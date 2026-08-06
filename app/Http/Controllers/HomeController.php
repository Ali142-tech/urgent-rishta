<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Mail\ContactUsEmail;
use App\MasterData;
use App\OnlinePackage;
use App\Profile;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth', 'verified'])->except(['index', 'contactUsEmail',
            'packagesView','storiesView', 'faqsView', 'termsAndConditionsView', 'privacyPolicyView',
            'contactUsView', 'states', 'cities']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home() {
        return $this->index();
    }

    public Function index() {
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        return view('welcome', compact('maritalstatuses', 'countries', 'mothertongues', 'caste'));
    }

    public function packagesView() {
        // Standard (ONLINE) packages: stored in separate table and paid online
        $standardPackages = OnlinePackage::where('is_active', true)->get();

        // Premium/offline packages: existing data kept in masterdata
        $premiumPackages = MasterData::where('type', 'PACKAGE')->get();

        $packages = $standardPackages->concat($premiumPackages);

        // Current user's active online subscription (for showing "Active" and expiry on packages page)
        $userOnlinePackageDataid = null;
        $userOnlineExpiresAtFormatted = null;
        $userHasActiveOnlinePackage = false;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasActiveOnlinePackage()) {
                $userHasActiveOnlinePackage = true;
                $userOnlinePackageDataid = $user->online_package;
                $exp = $user->online_package_expires_at;
                $userOnlineExpiresAtFormatted = $exp instanceof Carbon
                    ? $exp->format('j M Y')
                    : ($exp ? Carbon::parse($exp)->format('j M Y') : null);
            }
        }

        return view('packages', compact(
            'packages', 'standardPackages', 'premiumPackages',
            'userOnlinePackageDataid', 'userOnlineExpiresAtFormatted', 'userHasActiveOnlinePackage'
        ));
    }

    public function storiesView() {
        return view('stories');
    }

    public function faqsView () {
        return view('faqs');
    }

    public function termsAndConditionsView() {
        return view('tandc');
    }

    public function privacyPolicyView() {
        return view('privacy');
    }

    public function contactUsView() {
        return view('contactus');
    }

    public Function search(Request $request, $refresh = null) {
        // Force refresh from DB so package/online_package and expiry are up to date
        $loggedInUser = User::retrieveUserObject(null, true);
       
        if (!empty($loggedInUser) && !$loggedInUser->isActive()) {
            Session::flash('message', 'danger|Profile not active. Search disabled. Please contact Nimrah at 0307-0227000 for profle activation.');
            Log::info("Search disabled. User profile not activated for " . $loggedInUser->email);
            return redirect('home');
        }
        // Search allowed when EITHER admin-assigned package OR active online package (independent).
        if (!empty($loggedInUser) && !$loggedInUser->canSearchSoulMates()) {
            Session::flash('message', 'warning|To search Soul Mates you need either an admin-assigned package (e.g. Platinum, Diamond, Royal—contact admin) or an active online package (see Packages page).');
            return redirect('home');
        }

        $pageSize = !empty($request->pagesize) ? $request->pagesize : 10;
        $pageRequested = !empty($request->pagerequested) ? $request->pagerequested : 1;
        $resultCount = null;
        $total = Profile::getTotalCount();

        $where = "`u`.`gender`='".$request->gender."'";
        $having = "";

        // Restrict results by package tier (admin users can search all profiles without filter).
        $visiblePackageDataids = $loggedInUser->getVisiblePackageDataidsForSearch();
        if (empty($visiblePackageDataids)) {
            $where = $where . " and 1=0";
        } elseif (!$loggedInUser->isAdmin()) {
            $quoted = array_map(function ($d) {
                return "'" . addslashes($d) . "'";
            }, $visiblePackageDataids);
            $where = $where . " and `u`.`package` IN (" . implode(',', $quoted) . ")";
        }

        if (!empty($request->member_id)) { // if dataid only search on dataid
            $where = $where.((empty($where) ? "" : " and ")."`u`.`dataid`='".$request->member_id."'");
        } else {
            if (!empty($request->aged_from)) {
                $where = $where.((empty($where) ? "" : " and ")."FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/ 365.25) between ".$request->aged_from." and  ".($request->aged_to?$request->aged_to:75));
            }
            if (!empty($request->first_name)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`first_name`='".$request->first_name."'");
            }
            if (!empty($request->profession)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`profession`='".$request->profession."'");
            }
            if (!empty($request->religion)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`religion`='".$request->religion."'");
            }
            if (!empty($request->city)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`city`='".$request->city."'");
            }
            if (!empty($request->state)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`state`='".$request->state."'");
            }
            if (!empty($request->country)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`con_of_residence`='".$request->country."'");
            }
            if (!empty($request->marital_status)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`marital_status`='".$request->marital_status."'");
            }
            if (!empty($request->mother_tongue)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`mother_tongue`='".$request->mother_tongue."'");
            }
            if (!empty($request->caste)) {
                $where = $where.((empty($where) ? "" : " and ")."`u`.`caste`='".$request->caste."'");
            }
            if (!empty($request->withpics)) {
                $having = "`images`<>''";
            }
        }
        $where = $where.((empty($where) ? "" : " and ")."`u`.`active`=1");
        $members = Profile::profiles($where, $having, "`u`.`updated_at` DESC", $pageSize, $pageSize*($pageRequested-1));
        $resultCount = Profile::profiles($where, $having, null, null, null, true);

        $religions = MasterData::where('type', 'RELIGION')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();

        // $view = null;
        // if (!empty($refresh))
        //     $view = view('member.searchdata')->with([
        //         'currentPage' => 1,
        //         'pageSize' => $pageSize,
        //         'total' => $total,
        //         'resultCount' => $resultCount,
        //         'numPages' => ceil($resultCount / $pageSize),
        //         'members' => $members
        //     ]);
        // else
        $view = view('member.searchdata')->with([
                'currentPage' => $pageRequested,
                'pageSize' => $pageSize,
                'total' => $total,
                'resultCount' => $resultCount,
                'numPages' => ceil($resultCount / $pageSize),
                'members' => $members,
                'religions' => $religions,
                'mothertongues' => $mothertongues,
                'maritalstatuses' => $maritalstatuses,
                'countries' => $countries,
                'caste' => $caste
            ]);

        if (request()->ajax()) {
            return [
                'code' => '200',
                'html' => (!empty($refresh) ?
                    $view->renderSections()['search-data']
                        :
                    $view->renderSections()['main-content'] )
            ]; // only return whats in the main-content section
        } else return $view;
    }

    public function contactUsEmail(Request $request) {

        $obj = new \stdClass();
        $obj->sender = $request->get('name');
        $obj->sender_email = $request->get('email');
        $obj->subject = $request->get('subject');
        $obj->message = $request->get('message');

        Mail::to("admin@urgentrishta.co")->send(new ContactUsEmail($obj));

        Log::info($obj->sender."(".$obj->sender_email.") sent an email through contact-us form.");
        Session::flash('message','success|Thank you for contacting us. Your message has been received. Someone from our team will get in touch.');
        return view("contactus");
    }
}
