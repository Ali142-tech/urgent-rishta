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
            'packagesView','storiesView', 'teamView', 'galleryView', 'faqsView', 'termsAndConditionsView', 'privacyPolicyView',
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
        // The bare /packages URL (no ?type=) used to show a combined
        // side-by-side Online + Personalized comparison — client no longer
        // wants that page shown at all. The three filtered variants
        // (?type=online|personalized|signature, used by the nav dropdown's
        // submenu items) are unaffected and keep working exactly as before;
        // this only catches someone hitting the bare URL directly (typed in,
        // bookmarked, or an old link) and sends them to a sensible default
        // instead of a dead end.
        if (empty(request('type'))) {
            return redirect('packages?type=personalized');
        }

        // Standard (ONLINE) packages: stored in separate table and paid online
        $standardPackages = OnlinePackage::where('is_active', true)->get();

        // Premium/offline packages: existing data kept in masterdata
        $allPremiumPackages = MasterData::where('type', 'PACKAGE')->get();

        // Royal and Imperial moved out of "Personalized Plan" into their own
        // "Signature Plan" nav tab/section per client request — everything else
        // (Platinum, Diamond, and the "99"/All Profiles admin-only row) stays on
        // the regular Personalized tab exactly as before.
        $signaturePackages = $allPremiumPackages->filter(fn ($p) => in_array(trim($p->name), ['Royal', 'Imperial']))->values();
        $premiumPackages = $allPremiumPackages->reject(fn ($p) => in_array(trim($p->name), ['Royal', 'Imperial']))->values();

        $packages = $standardPackages->concat($allPremiumPackages);

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
            'packages', 'standardPackages', 'premiumPackages', 'signaturePackages',
            'userOnlinePackageDataid', 'userOnlineExpiresAtFormatted', 'userHasActiveOnlinePackage'
        ));
    }

    public function storiesView() {
        return view('stories');
    }

    public function teamView() {
        return view('team');
    }

    public function galleryView() {
        return view('gallery');
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
            Session::flash('message', 'danger|Profile not active. Search disabled. Please contact UrgentRishta Team at 0304-0227000 for profile activation.<br><a href="https://wa.me/923040227000" target="_blank" rel="noopener" class="ur-toast__wa-btn"><i class="fa fa-whatsapp"></i> Chat on WhatsApp</a>|20000');
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

        // Default to the opposite gender of the viewer when the "Bride/Groom"
        // toggle hasn't been submitted yet (first page load) — otherwise the
        // query below became `u`.`gender`='' , which matches nothing and the
        // page always opened on "No members found" until the user manually
        // picked one.
        $selectedGender = $request->gender;
        if (empty($selectedGender)) {
            $ownGender = strtolower($loggedInUser->gender ?? '');
            $selectedGender = $ownGender === 'male' ? 'female' : ($ownGender === 'female' ? 'male' : null);
        }

        // Dashboard-home summary shown at the top of this page for logged-in
        // members only (Welcome banner, completeness ring, "Recommended
        // Matches" preview) — null/empty for guests.
        $completeness = null;
        $recommendedMatches = collect();
        if (!empty($loggedInUser)) {
            $completeness = $loggedInUser->profile()->profileCompleteness();
            $recommendedMatches = $loggedInUser->getRecommendedMatches(4);
        }

        // Only run the real search once the member has actually submitted
        // one — either via the "Search Profiles"/"Apply Filters" popup
        // (always a POST, see the JS below) or a resumed search redirected
        // back with query params after login (see the route comment above).
        // A bare first-visit GET has neither, so the results area below the
        // "Recommended Matches" preview simply isn't rendered — no
        // "No members found", no stale/duplicate recommended-again list.
        $hasSearched = $request->isMethod('post') || $request->query->count() > 0;

        $where = $having = "";
        $members = collect();

        if ($hasSearched) {
            $where = $selectedGender ? "`u`.`gender`='".$selectedGender."'" : "1=1";

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

            // Nothing matched the *broad* search (just the Bride/Groom toggle,
            // no other filters) — rather than an empty "No members found"
            // page in that specific case, fall back to a small guaranteed set
            // of recommended profiles (opposite gender, same city prioritized
            // — see User::getRecommendedMatches()) for logged-in members.
            // Crucially, this must NOT fire once the member has actually
            // applied specific filters (country, religion, age range, etc.):
            // silently swapping in unrelated recommended profiles there would
            // make a real 0-result filter combination look like the filters
            // are being ignored, rather than honestly reporting "no matches".
            // Guests never get the fallback either — there's no viewer
            // profile/city to base a recommendation on for them.
            $hasNarrowFilters = $request->filled('member_id') || $request->filled('aged_from') || $request->filled('aged_to')
                || $request->filled('first_name') || $request->filled('profession') || $request->filled('religion')
                || $request->filled('city') || $request->filled('state') || $request->filled('country')
                || $request->filled('marital_status') || $request->filled('mother_tongue') || $request->filled('caste')
                || $request->filled('withpics');

            if ($resultCount == 0 && !empty($loggedInUser) && !$hasNarrowFilters) {
                $members = $loggedInUser->getRecommendedMatches($pageSize);
                $resultCount = $members->count();
            }
        }

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
                'caste' => $caste,
                'selectedGender' => $selectedGender,
                'completeness' => $completeness,
                'recommendedMatches' => $recommendedMatches,
                'viewerUser' => $loggedInUser,
                'hasSearched' => $hasSearched
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

    /**
     * Dedicated destination for the "View Recommended Matches"/"View All"
     * links on the Search Profiles page — same profiles as that page's
     * 3-card preview (see User::getRecommendedMatches()), just a full,
     * paginated listing instead of capped at 3. Requires auth (not in
     * this controller's guest-accessible $except list).
     */
    public function recommendedMatches(Request $request) {
        $loggedInUser = User::retrieveUserObject(null, true);

        if (!$loggedInUser->isActive()) {
            Session::flash('message', 'danger|Profile not active. Please contact UrgentRishta Team at 0304-0227000 for profile activation.<br><a href="https://wa.me/923040227000" target="_blank" rel="noopener" class="ur-toast__wa-btn"><i class="fa fa-whatsapp"></i> Chat on WhatsApp</a>|20000');
            Log::info("Recommended matches disabled. User profile not activated for " . $loggedInUser->email);
            return redirect('home');
        }
        if (!$loggedInUser->canSearchSoulMates()) {
            Session::flash('message', 'warning|To see recommended matches you need either an admin-assigned package (e.g. Platinum, Diamond, Royal—contact admin) or an active online package (see Packages page).');
            return redirect('home');
        }

        $pageSize = 12;
        $resultCount = $loggedInUser->getRecommendedMatchesCount();
        $numPages = max(1, (int) ceil($resultCount / $pageSize));
        $pageRequested = min(max(1, (int) $request->query('page', 1)), $numPages);
        $members = $loggedInUser->getRecommendedMatches($pageSize, $pageSize * ($pageRequested - 1));

        return view('member.recommended-matches', [
            'members' => $members,
            'resultCount' => $resultCount,
            'currentPage' => $pageRequested,
            'numPages' => $numPages,
        ]);
    }

    public function contactUsEmail(Request $request) {

        $obj = new \stdClass();
        $obj->sender = $request->get('name');
        $obj->sender_email = $request->get('email');
        $obj->sender_phone = $request->get('phone');
        $obj->sender_city = $request->get('city');
        $obj->subject = $request->get('subject');
        $obj->message = $request->get('message');

        Mail::to("admin@urgentrishta.co")->send(new ContactUsEmail($obj));

        Log::info($obj->sender."(".$obj->sender_email.") sent an email through contact-us form.");
        Session::flash('message','success|Thank you for contacting us. Your message has been received. Someone from our team will get in touch.');
        return view("contactus");
    }
}
