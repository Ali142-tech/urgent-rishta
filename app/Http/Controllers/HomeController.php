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
use App\Interest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class HomeController extends Controller {
    /**
     * Client-curated, fixed list of profiles for the homepage "Meet Our
     * Members" slider (sent over WhatsApp Sep 11 2026), shown in this exact
     * order rather than randomly sampled. Shared with
     * GenerateHomepageFaceBlur (php artisan homepage:blur-faces) — re-run
     * that command after updating this list so new entries get a face-blur
     * file generated before they can appear on the slider.
     */
    // const CURATED_PROFILE_DATAIDS = [
    //     'I2M2LNKVV', 'NZHV8PEFM', '24L0ZK1T1', '285ABV24J', '9L7X195LS', '321US8KXU',
    //     '6VVVCIJV2', 'R2QSYGYF9', 'I8RBBI61E', '3NY5HQ7AU', '7FN3BYRUL', 'RAD86075Z',
    //     'TRQAEO4F7', 'JIU4JDXE1', 'H0BNEOCJI', 'KD7D6P5N5', '8L5L4JVAQ', 'G8IWL9735',
    //     'ESTC23MRV', 'M9D7J1LAY', '7SUEWATKP',
    // ];

    /** Where GenerateHomepageFaceBlur saves its output — public/homepage-blurred/<dataid>.jpg */
    const FACE_BLUR_DIR = 'homepage-blurred';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth', 'verified'])->except(['index', 'contactUsEmail',
            'packagesView','storiesView', 'teamView', 'galleryView', 'partnersView', 'cmsPage', 'faqsView', 'termsAndConditionsView', 'privacyPolicyView',
            'contactUsView', 'states', 'cities', 'castes', 'communities', 'sharePreview']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home() {
        return $this->index();
    }

    /**
     * Guest-facing photo for the homepage slider: the member's real picture, blurred just enough that the face cannot be
     * recognised but the person, outfit and setting still show (the stored copy used before is blurred so hard it is only a
     * colour wash). Made once per photo from the original and kept in public/homepage-blurred; null when the member has no
     * photo, hides it, or the picture cannot be read — the card then uses its normal blurred image.
     */
    private function homepageLightBlur($profile): ?string
    {
        if (($profile->photo_visibility ?? null) === 'hidden') {
            return null;
        }
        $first = !empty($profile->displaypic) ? $profile->displaypic : (is_array($profile->images) ? ($profile->images[0] ?? '') : explode(',', (string) $profile->images)[0]);
        $name = basename((string) $first);
        $source = public_path(trim(Profile::MEMBER_IMAGES_PATH, '/') . '/' . $name);
        if ($name === '' || !is_file($source)) {
            return null;
        }

        $relative = self::FACE_BLUR_DIR . '/' . $profile->dataid . '_' . substr(sha1($name . filemtime($source) . 'light2'), 0, 10) . '.jpg';
        $target = public_path($relative);
        if (!is_file($target)) {
            try {
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }
                $manager = new \Intervention\Image\ImageManager(['driver' => extension_loaded('imagick') ? 'imagick' : 'gd']);
                $image = $manager->make($source)->orientate();
                // fit in 480 x 600 (never enlarged), then blur: strong enough to hide the face, light enough to read the photo
                $image->resize(480, 600, function ($c) { $c->aspectRatio(); $c->upsize(); });
                $image->blur(22);
                $image->save($target, 82, 'jpg');
            } catch (\Throwable $e) {
                Log::warning('Homepage photo blur failed for ' . $profile->dataid . ': ' . $e->getMessage());
                return null;
            }
        }

        return '/' . $relative;
    }

    public Function index() {

    
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderByRaw("name = 'Other' ASC")->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();

       
        $verifiedProfilesCount = User::where('active', 1)->count();
        $successfulMatchesCount = Interest::where('interest_back', 1)->count();

        

        $femalesProfiles=Profile::where('active', 1)->where('gender','female')->whereNotNull('images')->where('images', '!=', '')->orderBy('created_at','desc')->take(11)->get();
        $malesProfiles=Profile::where('active', 1)->where('gender','male')->whereNotNull('images')->where('images', '!=', '')->orderBy('created_at','desc')->take(10)->get();
        // country flag on each card: the residence country's 2-letter code (masterdata abbreviation), one lookup for all of them
        $countryCodes = MasterData::where('type', 'COUNTRY')->pluck('abbreviation', 'dataid');
        $sampleProfiles=$femalesProfiles->merge($malesProfiles)->shuffle()->map(function ($profile) use ($countryCodes) {
            $profile->con_of_residence_code = $countryCodes[$profile->con_of_residence] ?? null;
            // a lightly blurred copy of the real photo (the card falls back to the old heavy blur when there is none)
            $profile->homepageFaceBlurredImage = $this->homepageLightBlur($profile);
            return $profile;
        });

           

        return view('welcome', compact('maritalstatuses', 'countries', 'mothertongues', 'caste', 'verifiedProfilesCount', 'successfulMatchesCount', 'sampleProfiles'));
    }

    public function packagesView() {
        // The bare /packages URL (no ?type=) shows a short overview page —
        // three plan "teaser" cards (Online / Personalized / Signature) that
        // each link into the matching filtered view below. The three
        // filtered variants (?type=online|personalized|signature, used by
        // the nav dropdown's submenu items and by the teaser cards' own
        // buttons) are unaffected and keep working exactly as before.
        if (empty(request('type'))) {
            return view('packages-overview');
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

    public function partnersView() {
        return view('partners');
    }

    public function faqsView () {
        return view('faqs', ['faqs' => \App\Faq::visible()->get()]);
    }

    public function termsAndConditionsView() {
        return view('tandc', ['page' => \App\CmsPage::active()->where('slug', 'terms')->firstOrFail()]);
    }

    public function privacyPolicyView() {
        return view('privacy', ['page' => \App\CmsPage::active()->where('slug', 'privacy')->firstOrFail()]);
    }

    /** Any page an admin created in Admin ▸ Content (shown at /p/{slug}); a hidden page is "not found". */
    public function cmsPage($slug) {
        return view('cms-page', ['page' => \App\CmsPage::active()->where('slug', $slug)->firstOrFail()]);
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
        $hasPartnerPreferences = false;
        // Fetched once here (not per-card) — member-card.blade.php calls
        // $member->compatibilityWith($viewerPreference) for each card, which
        // is a pure in-memory comparison against this single row, no extra
        // queries per card. Client request (Sep 2026): show the match % on
        // every card, not just the single profile page.
        $viewerPreference = null;
        if (!empty($loggedInUser)) {
            $completeness = $loggedInUser->profile()->profileCompleteness();
            $hasPartnerPreferences = $loggedInUser->hasPartnerPreferences();
            $recommendedMatches = $loggedInUser->getRecommendedMatches(4);
            if ($hasPartnerPreferences) {
                $viewerPreference = $loggedInUser->partnerPreference()->first();
            }
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
            // Every value below is user-supplied and goes straight into a raw
            // SQL string (Profile::profiles() has no parameter binding), so
            // each one MUST be escaped with addslashes() before concatenation
            // — this was previously unescaped (SQL injection via any of these
            // search fields, reachable by any logged-in member). Numeric
            // fields are additionally cast to int since they sit in an
            // unquoted context where addslashes() alone wouldn't help.
            $where = $selectedGender ? "`u`.`gender`='".addslashes($selectedGender)."'" : "1=1";

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
                $where = $where.((empty($where) ? "" : " and ")."`u`.`dataid`='".addslashes($request->member_id)."'");
            } else {
                if (!empty($request->aged_from)) {
                    $where = $where.((empty($where) ? "" : " and ")."FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/ 365.25) between ".(int) $request->aged_from." and  ".($request->aged_to ? (int) $request->aged_to : 75));
                }
                if (!empty($request->first_name)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`first_name`='".addslashes($request->first_name)."'");
                }
                if (!empty($request->profession)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`profession`='".addslashes($request->profession)."'");
                }
                if (!empty($request->religion)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`religion`='".addslashes($request->religion)."'");
                }
                if (!empty($request->city)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`city`='".addslashes($request->city)."'");
                }
                if (!empty($request->state)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`state`='".addslashes($request->state)."'");
                }
                if (!empty($request->country)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`con_of_residence`='".addslashes($request->country)."'");
                }
                if (!empty($request->marital_status)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`marital_status`='".addslashes($request->marital_status)."'");
                }
                if (!empty($request->mother_tongue)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`mother_tongue`='".addslashes($request->mother_tongue)."'");
                }
                if (!empty($request->caste)) {
                    $where = $where.((empty($where) ? "" : " and ")."`u`.`caste`='".addslashes($request->caste)."'");
                }
                if (!empty($request->withpics)) {
                    $having = "`images`<>''";
                }
            }
            $where = $where.((empty($where) ? "" : " and ")."`u`.`active`=1");
            // Team-added proposals (see TeamController::store()) are team-exclusive
            // — never surfaced in the regular search results.
            $where .= " and `u`.`added_by` IS NULL";
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

        $religions = MasterData::where('type', 'RELIGION')->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderByRaw("name = 'Other' ASC")->orderBy('name', 'ASC')->get();
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
                'hasPartnerPreferences' => $hasPartnerPreferences,
                'viewerUser' => $loggedInUser,
                'viewerPreference' => $viewerPreference,
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
        $hasPartnerPreferences = $loggedInUser->hasPartnerPreferences();

        return view('member.recommended-matches', [
            'members' => $members,
            'resultCount' => $resultCount,
            'currentPage' => $pageRequested,
            'numPages' => $numPages,
            'hasPartnerPreferences' => $hasPartnerPreferences,
            'viewerPreference' => $hasPartnerPreferences ? $loggedInUser->partnerPreference()->first() : null,
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

    /**
     * Public, unauthenticated WhatsApp/link-preview card for a shared
     * proposal — TeamController::shareWhatsapp()/forwardBothWhatsapp() link
     * here instead of straight to member/profile/{dataid}, which requires
     * login (member/profile/{dataid} sits behind ProfileController's own
     * blanket 'auth' middleware), so a logged-out crawler like WhatsApp's
     * link-preview bot could never reach it or read its og:image tag.
     *
     * Deliberately public per the client's explicit choice (Sep 2026): the
     * real photo shows in the WhatsApp link preview, which means it's
     * reachable by anyone holding the link — not just logged-in
     * matchmakers. Everything else (contact info, the full field grid,
     * Partner Requirements) stays behind the login wall this page's own
     * "View Full Profile" button leads to; this card only ever shows
     * photo + the same handful of non-sensitive fields the public
     * homepage slider already exposes to guests.
     */
    public function sharePreview(\Illuminate\Http\Request $request, $dataid) {
        // A private proposal stays hidden from visitors (PrivateProposalScope) — except through the signed link a team member
        // deliberately shared (TeamController::shareLinkFor()), which is checked here.
        $signed = $request->hasValidSignature();
        $member = \App\Proposal::with('photos')
            ->when($signed, fn ($q) => $q->withoutGlobalScope(\App\Scopes\PrivateProposalScope::class))
            ->where('reference', $dataid)->first();
        if (!$member) {
            abort(404);
        }

        $age = $member->age;
        $photoPath = $member->getCardImages(null, false)[0] ?? null;
        // The link preview carries the owner's watermark too — except on the signed link an admin shares, which shows the original.
        $original = $request->boolean('orig') && $signed;
        if (!$original && $photoPath && $member->photos->isNotEmpty()) {
            $photoPath = (new \App\Services\PhotoBrandingService())->brandedPath(\App\TeamMember::find($member->added_by), basename(parse_url($photoPath, PHP_URL_PATH)), $member->reference) ?: $photoPath;
        }
        $photoUrl = url($photoPath ?: Profile::defaultImage($member->gender));
        $location = $member->lbl_city ?: $member->lbl_con_of_residence;

        return view('share.proposal-preview', compact('member', 'age', 'photoUrl', 'location'));
    }
}
