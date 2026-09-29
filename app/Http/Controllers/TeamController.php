<?php

namespace App\Http\Controllers;

use App\AuditLog;
use App\Images;
use App\MasterData;
use App\Notifications\AiMatchFound;
use App\Notifications\NewProposalAdded;
use App\PartnerPreference;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    use \App\Traits\HandlesImageUploads;

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'team_member']);
    }

    /**
     * Team Dashboard: the global pool of proposals manually added by any
     * team member (`added_by IS NOT NULL`) — never the regular self-
     * registered pool, which is excluded from here by definition since
     * those rows have `added_by = NULL`.
     */
    /**
     * Shared by myProposals()/searchProposals() (previously duplicated
     * inline as the old single dashboard() method — split into separate
     * "My Proposals" vs "Search Proposals" pages to match the client's
     * dashboard redesign, see the AI Matching Engine + Dashboard Redesign
     * plan). $where is caller-supplied (own proposals vs the whole global
     * pool + filters); everything else (added_by name lookup) is
     * identical either way.
     */
    private function buildProposalGrid(string $where, int $pageRequested, int $pageSize = 12): array
    {
        $resultCount = (int) Profile::profiles($where, null, null, null, null, true);
        $numPages = max(1, (int) ceil($resultCount / $pageSize));
        $pageRequested = min(max(1, $pageRequested), $numPages);
        $members = Profile::profiles($where, null, "`u`.`created_at` DESC", $pageSize, $pageSize * ($pageRequested - 1));

        // "Added by" display name — a small batched lookup kept separate from
        // Profile::profiles()'s shared raw-SQL query rather than joining it
        // into that query, since that function is relied on by many other
        // unrelated callers. Also grabs dataid/experience so cards can show
        // an "Owner: Name • ID • Experience" byline (Advanced Search redesign,
        // Sep 2026 — matches the Matchmakers roster's own name+experience line).
        $addedByIds = collect($members)->pluck('added_by')->filter()->unique()->values();
        $addedByNames = $addedByIds->isEmpty()
            ? collect()
            : User::whereIn('id', $addedByIds)->get(['id', 'first_name', 'last_name', 'dataid', 'experience'])->keyBy('id');

        // Whether each proposal has a partner preference saved — gates the
        // "AI Match" card action, since team.matches.show 404s without one
        // (see authorizeProposalOwner()/matchesForProposal()).
        $memberIds = collect($members)->pluck('id');
        $idsWithPreference = $memberIds->isEmpty()
            ? collect()
            : User::whereIn('id', $memberIds)->whereHas('partnerPreference')->pluck('id')->flip();

        // 'own' vs 'other' — the whole team-added pool is open to every team
        // member, so this only decides whether member-card.blade.php shows
        // the Edit action (own proposal) or just View/Share (anyone else's).
        $viewerId = auth()->id();
        foreach ($members as $member) {
            $addedByUser = $addedByNames->get($member->added_by);
            $member->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
            $member->added_by_dataid = $addedByUser->dataid ?? null;
            $member->added_by_experience = $addedByUser->experience ?? null;
            $member->team_card_role = $member->added_by == $viewerId ? 'own' : 'other';
            $member->has_partner_preference = $idsWithPreference->has($member->id);
        }

        return [
            'members' => $members,
            'resultCount' => $resultCount,
            'currentPage' => $pageRequested,
            'numPages' => $numPages,
        ];
    }

    /**
     * Dashboard overview — stat cards, a short AI Match Recommendations
     * preview, live notifications panel, and the Quick Add Proposal form.
     * This used to BE the proposal grid (now split out to myProposals()/
     * searchProposals() to match the client's redesign).
     */
    public function dashboard(Request $request)
    {
        $viewerId = auth()->id();

        $myProposalsCount = (int) Profile::profiles("`u`.`added_by` = " . (int) $viewerId, null, null, null, null, true);
        $teamProposalsCount = (int) Profile::profiles("`u`.`added_by` IS NOT NULL", null, null, null, null, true);
        $successfulMatchesCount = \App\SuccessfulMatch::where('partner_a_id', $viewerId)->orWhere('partner_b_id', $viewerId)->count();
        // Grouped in a closure — chaining ->orWhere() then ->whereMonth()/
        // ->whereYear() directly would bind the month/year filters only to
        // the OR's second clause, not both (a real precedence bug, not a
        // style choice).
        $successfulMatchesThisMonth = \App\SuccessfulMatch::where(function ($q) use ($viewerId) {
            $q->where('partner_a_id', $viewerId)->orWhere('partner_b_id', $viewerId);
        })->whereMonth('matched_at', now()->month)->whereYear('matched_at', now()->year)->count();

        // AI Matches Found + a short recommendations preview — aggregated
        // across this team member's own proposals only (see
        // User::getProposalMatches()/getProposalMatchesCount()).
        $myProposalIds = User::where('added_by', $viewerId)->pluck('id');
        $myProposalUsers = User::whereIn('id', $myProposalIds)->get();
        $aiMatchesCount = 0;
        $previewMatches = collect();
        foreach ($myProposalUsers as $proposal) {
            $aiMatchesCount += $proposal->getProposalMatchesCount();
            if ($previewMatches->count() < 3) {
                $preference = $proposal->partnerPreference;
                $proposalProfile = $proposal->profile();
                foreach ($proposal->getProposalMatches(3) as $match) {
                    if ($previewMatches->count() >= 3) break;
                    $match->viewer_preference_for_card = $preference;
                    $match->for_proposal_dataid = $proposal->dataid;
                    $match->for_proposal_profile = $proposalProfile;
                    $previewMatches->push($match);
                }
            }
        }

        $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();

        // "AI Partner Portal" mockup dashboard (Sep 2026) — the 4th stat
        // card. Stands in for the mockup's "Pending Requests" — the actual
        // Collaboration Request feature that name used to mean was
        // deliberately removed (see
        // 2026_09_23_000001_drop_collaboration_requests_table.php); this
        // uses unread notifications as a real, honest substitute rather
        // than fabricating a number with nothing behind it.
        $pendingRequestsCount = auth()->user()->unreadNotifications()->count();

        return view('team.overview', [
            'myProposalsCount' => $myProposalsCount,
            'aiMatchesCount' => $aiMatchesCount,
            'teamProposalsCount' => $teamProposalsCount,
            'successfulMatchesCount' => $successfulMatchesCount,
            'successfulMatchesThisMonth' => $successfulMatchesThisMonth,
            'pendingRequestsCount' => $pendingRequestsCount,
            'previewMatches' => $previewMatches,
            'recentNotifications' => $recentNotifications,
        ]);
    }

    /**
     * "My Proposals" — this team member's own added proposals only.
     */
    public function myProposals(Request $request)
    {
        $where = "`u`.`added_by` = " . (int) auth()->id();
        $grid = $this->buildProposalGrid($where, (int) $request->query('page', 1));

        return view('team.proposals-mine', $grid);
    }

    /**
     * "Search Proposals" — the global team-added pool (everyone's), with
     * the same filter fields as HomeController::search() (just
     * `added_by IS NOT NULL` instead of `IS NULL`).
     */
    public function searchProposals(Request $request)
    {
        // Default landing state (client request, Sep 2026): with no filters
        // submitted at all, show a "Doctor" keyword search rather than the
        // whole unfiltered pool — both applied to $where below and reflected
        // back into the keyword box via request('keyword') in the view.
        // "Clear filters" (a plain link back to this same route with no
        // query string) lands here too, so it resets to this same default
        // rather than an empty results-head/photo-less page.
        $filterKeys = ['keyword', 'profession', 'country', 'gender', 'aged_from', 'aged_to', 'religion', 'caste', 'marital_status', 'mother_tongue', 'education', 'city', 'current_city'];
        $hasAnyFilter = collect($filterKeys)->contains(fn ($key) => $request->filled($key));
        if (!$hasAnyFilter) {
            $request->merge(['keyword' => 'Doctor']);
        }

        $where = "`u`.`added_by` IS NOT NULL";

        if (!empty($request->aged_from)) {
            $where .= " and FLOOR(DATEDIFF(NOW(), `u`.`birthday`)/365.25) between " . (int) $request->aged_from . " and " . ($request->aged_to ? (int) $request->aged_to : 75);
        }
        if (!empty($request->gender)) {
            $where .= " and `u`.`gender`='" . addslashes($request->gender) . "'";
        }
        if (!empty($request->profession)) {
            $where .= " and `u`.`profession` LIKE '%" . addslashes($request->profession) . "%'";
        }
        if (!empty($request->religion)) {
            $where .= " and `u`.`religion`='" . addslashes($request->religion) . "'";
        }
        if (!empty($request->city)) {
            $where .= " and `u`.`city` LIKE '%" . addslashes($request->city) . "%'";
        }
        if (!empty($request->country)) {
            $where .= " and `u`.`con_of_residence`='" . addslashes($request->country) . "'";
        }
        if (!empty($request->marital_status)) {
            $where .= " and `u`.`marital_status`='" . addslashes($request->marital_status) . "'";
        }
        if (!empty($request->mother_tongue)) {
            $where .= " and `u`.`mother_tongue`='" . addslashes($request->mother_tongue) . "'";
        }
        if (!empty($request->caste)) {
            $where .= " and `u`.`caste`='" . addslashes($request->caste) . "'";
        }
        if (!empty($request->education)) {
            $where .= " and `u`.`education`='" . addslashes($request->education) . "'";
        }
        if (!empty($request->current_city)) {
            $where .= " and `u`.`current_city` LIKE '%" . addslashes($request->current_city) . "%'";
        }
        // "Search by keyword" quick-search tab (Advanced Search redesign, Sep
        // 2026) — a loose OR across the handful of free-text fields, rather
        // than a single-column match, since a matchmaker typing a name or a
        // job title into "keyword" has no way to know which column it's in.
        if (!empty($request->keyword)) {
            $keyword = addslashes($request->keyword);
            $where .= " and (`u`.`first_name` LIKE '%{$keyword}%' or `u`.`last_name` LIKE '%{$keyword}%'"
                . " or `u`.`profession` LIKE '%{$keyword}%' or `u`.`city` LIKE '%{$keyword}%'"
                . " or `u`.`current_city` LIKE '%{$keyword}%')";
        }

        // 10/page (client request, Sep 2026) — buildProposalGrid()'s other
        // caller (myProposals()) keeps its own default of 12.
        $grid = $this->buildProposalGrid($where, (int) $request->query('page', 1), 10);

        $religions = MasterData::where('type', 'RELIGION')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();

        // "X profiles in the shared network" badge on the search panel — the
        // whole team-added pool, unfiltered (not $grid['resultCount'], which
        // is post-filter).
        $totalNetworkCount = (int) Profile::profiles("`u`.`added_by` IS NOT NULL", null, null, null, null, true);

        return view('team.proposals-search', array_merge($grid, compact(
            'religions', 'maritalstatuses', 'mothertongues', 'countries', 'caste', 'education', 'totalNetworkCount'
        )));
    }

    /**
     * "AI Match Center" (client mockup, Sep 2026) — one page: pick any of
     * THIS team member's own proposals that has Partner Requirements set
     * from a dropdown, optionally narrow by minimum score/location, and see
     * every compatible candidate with a per-factor "why" breakdown.
     * "Select any team proposal" only ever lists the logged-in team
     * member's own proposals — client confirmed 2026-09-29 this must NOT
     * browse other matchmakers' clients, only the matched CANDIDATES (the
     * right-hand side of each pair) are drawn from the whole shared pool,
     * same as every other AI matching flow in this app. Replaces the old
     * list-of-proposals-then-drill-down flow (team.matches + team.matches.show
     * used to render two different views — matches.blade.php and
     * matches-detail.blade.php — now both render this same
     * team/matches.blade.php, just with a different proposal pre-selected).
     */
    public function matches(Request $request)
    {
        $selectedDataid = $request->query('proposal');
        if (empty($selectedDataid)) {
            $default = User::where('added_by', auth()->id())->whereHas('partnerPreference')
                ->orderByDesc('updated_at')->first();

            if (!$default) {
                return view('team.matches', ['noProposals' => true]);
            }
            $selectedDataid = $default->dataid;
        }

        return view('team.matches', $this->buildMatchCenterData($selectedDataid, $request));
    }

    /**
     * Deep-link variant of matches() — same page/view, just forcing which
     * proposal is pre-selected (linked from search-result-card.blade.php's
     * "AI Match" button, member-card.blade.php, proposal-view.blade.php —
     * all three already only show that button on the viewer's own
     * proposals, so authorizeProposalOwner() below never blocks a
     * legitimate click, only a guessed/shared URL to someone else's).
     */
    public function matchesForProposal($dataid)
    {
        return view('team.matches', $this->buildMatchCenterData($dataid, request()));
    }

    private function buildMatchCenterData(string $selectedDataid, Request $request): array
    {
        $proposal = $this->authorizeProposalOwner($selectedDataid);
        $proposalProfile = $proposal->profile();
        $preference = $proposal->partnerPreference;

        // Dropdown options — only the logged-in team member's OWN
        // proposals that have Partner Requirements set (i.e. actually
        // usable for matching). Admins fall back to every proposal in the
        // pool, since authorizeProposalOwner() already lets them view any
        // of them and an admin has no "own" proposals of their own.
        $candidateProposals = auth()->user()->isAdmin()
            ? User::whereNotNull('added_by')->where('admin', 0)->whereHas('partnerPreference')
                ->orderByDesc('updated_at')
                ->get(['id', 'dataid', 'first_name', 'last_name', 'gender', 'birthday', 'profession'])
            : User::where('added_by', auth()->id())->whereHas('partnerPreference')
                ->orderByDesc('updated_at')
                ->get(['id', 'dataid', 'first_name', 'last_name', 'gender', 'birthday', 'profession']);

        $minScore = (int) $request->query('min_score', 0);
        $locationFilter = $request->query('location');

        $rawMatches = $proposal->getProposalMatches(50);

        $addedByIds = collect($rawMatches)->pluck('added_by')->push($proposal->added_by)->filter()->unique()->values();
        $addedByUsers = $addedByIds->isEmpty()
            ? collect()
            : User::whereIn('id', $addedByIds)->get(['id', 'first_name', 'last_name', 'dataid', 'experience', 'contact_mobile_number'])->keyBy('id');

        $existingSuccessfulIds = \App\SuccessfulMatch::where('proposal_id', $proposal->id)
            ->pluck('counterpart_proposal_id')
            ->merge(\App\SuccessfulMatch::where('counterpart_proposal_id', $proposal->id)->pluck('proposal_id'));

        $matches = collect();
        foreach ($rawMatches as $match) {
            $compat = $match->compatibilityWith($preference, [], $proposalProfile);
            $percent = $compat['percent'] ?? 0;
            if ($percent < $minScore) {
                continue;
            }
            if (!empty($locationFilter) && $match->con_of_residence != $locationFilter) {
                continue;
            }

            $owner = $addedByUsers->get($match->added_by);
            $match->added_by_name = $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
            $match->added_by_dataid = $owner->dataid ?? null;
            $match->added_by_experience = $owner->experience ?? null;
            $match->added_by_whatsapp = $owner->contact_mobile_number ?? null;
            $match->compat = $compat;
            $match->already_successful = $existingSuccessfulIds->contains($match->id);
            $matches->push($match);
        }
        $matches = $matches->sortByDesc(fn ($m) => $m->compat['percent'] ?? 0)->values();

        $proposalOwner = $addedByUsers->get($proposal->added_by);
        $proposalProfile->added_by_name = $proposalOwner ? trim($proposalOwner->first_name . ' ' . $proposalOwner->last_name) : 'Unknown';
        $proposalProfile->added_by_experience = $proposalOwner->experience ?? null;
        $proposalProfile->team_card_role = $proposal->added_by == auth()->id() ? 'own' : 'other';

        $locations = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();

        return compact('proposal', 'proposalProfile', 'preference', 'candidateProposals', 'matches', 'minScore', 'locationFilter', 'locations');
    }

    /**
     * "Forward Both" (AI Match Center) — sends the RECOMMENDED profile's
     * owner a WhatsApp message with both proposals' portal links, so they
     * can review the pairing together. Separate from shareWhatsapp()
     * (which forwards a single proposal to its own owner).
     */
    public function forwardBothWhatsapp($proposalDataid, $matchDataid)
    {
        $loggedInUser = auth()->user();
        $proposal = User::where('dataid', $proposalDataid)->whereNotNull('added_by')->first();
        $match = User::where('dataid', $matchDataid)->whereNotNull('added_by')->first();
        if (!$proposal || !$match) {
            abort(404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $match);
        Log::info('Team member (' . $loggedInUser->dataid . ') forwarded proposals ' . $proposal->dataid . ' + ' . $match->dataid . ' via WhatsApp');

        // route('share.proposal', ...) rather than member/profile/{dataid}
        // — that route sits behind a login wall, so WhatsApp's link-preview
        // crawler could never reach it or show the client's photo. See
        // HomeController::sharePreview().
        $text = 'Potential match — ' . $proposal->dataid . ': ' . route('share.proposal', $proposal->dataid)
            . "\n" . $match->dataid . ': ' . route('share.proposal', $match->dataid);

        $matchOwner = User::find($match->added_by);
        $link = (!empty($matchOwner) && !empty($matchOwner->contact_mobile_number))
            ? User::whatsappLinkForNumber($matchOwner->contact_mobile_number, $text)
            : User::whatsappShareLink($text);

        return redirect()->away($link);
    }

    /**
     * Full notifications list (the bell dropdown only shows a handful) —
     * marks read via the existing global `?read=<id>` convention
     * (MarkNotificationAsRead middleware, already applied to every web
     * request, see bootstrap/app.php).
     */
    public function notificationsList()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);

        return view('team.notifications', compact('notifications'));
    }

    /**
     * Team-member-facing Successful Matches — same data as AdminController::
     * successfulMatches(), scoped to just this team member's own.
     */
    public function successfulMatchesMine()
    {
        $viewerId = auth()->id();
        $matches = \App\SuccessfulMatch::with('proposal', 'counterpartProposal', 'partnerA', 'partnerB')
            ->where('partner_a_id', $viewerId)->orWhere('partner_b_id', $viewerId)
            ->orderByDesc('matched_at')->paginate(25);

        return view('team.successful-matches', ['matches' => $matches]);
    }

    /**
     * Read-only team roster ("Matchmakers" — client's Partner Portal
     * mockup, Sep 2026): every active team member with their profile info
     * and two stats — proposals they currently own that are still
     * profile_status='active' (client confirmed this should mean
     * "currently active," not "ever added" — the dashboard stat cards'
     * looser convention), and successful matches credited to them via
     * SuccessfulMatch.partner_a_id/partner_b_id (see markSuccessfulMatch()
     * — a match can legitimately credit two different team members, both
     * get +1). No management actions here (suspend/deactivate/delete stay
     * admin-only at admin/team-members) — this is peer visibility only.
     *
     * Deliberately does NOT invent a "login ID" style code (the mockup
     * shows e.g. "MM-UK-001") — nothing in this app tracks or assigns one,
     * and fabricating a label with no real backing data would be
     * misleading rather than useful.
     */
    public function matchmakers()
    {
        $members = User::where('is_team_member', 1)
            ->where('team_member_status', 'active')
            ->orderBy('first_name')
            ->get();

        foreach ($members as $member) {
            $member->activeProposalsCount = (int) Profile::profiles(
                "`u`.`added_by` = " . (int) $member->id . " and `u`.`profile_status` = 'active'",
                null, null, null, null, true
            );
            $member->successfulMatchesCount = \App\SuccessfulMatch::where('partner_a_id', $member->id)
                ->orWhere('partner_b_id', $member->id)
                ->count();
        }

        return view('team.matchmakers', ['members' => $members]);
    }

    public function create()
    {
        // $religions is only used for the partner's REQUIRED religion
        // (pref_religion, in "Partner Requirements") — the client's own
        // religion select was dropped along with the rest of the "extra
        // fields" form (Sep 2026); this query came back for that reason.
        $religions = MasterData::where('type', 'RELIGION')->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $sendTemplate = $this->clientIntakeTemplate();

        return view('team.proposal-create', compact('religions', 'maritalstatuses', 'education', 'countries', 'caste', 'sendTemplate'));
    }

    /**
     * The blank WhatsApp-style intake template a team member sends to a
     * prospective client — its section headers/field labels are exactly
     * what the Add Proposal page's paste-box parser (proposal-create.
     * blade.php) recognizes, so whatever comes back pastes in cleanly.
     * Keep the two in sync if either one changes.
     */
    private function clientIntakeTemplate(): string
    {
        return <<<'TEMPLATE'
Hi! To create your profile, please fill in the details below and send it back exactly in this format:

1. PERSONAL INFORMATION:
Gender:
Age:
Marital Status:
Height:

2. EDUCATION DETAILS:
Qualification:
College/University:

3. OCCUPATION DETAIL:
Job/Business:
Income:

4. RELIGION DETAILS:
Religion:
Sect:
Caste:

5. RESIDENCE DETAILS:
Home Own/On Rent:
Size:
City:
Nationality:
Current City:

6. FAMILY DETAILS:
Father's Occupation:
Mother's Occupation:
Brothers:
Sisters:
Married:

7. YOUR REQUIREMENTS:
Age Limit:
Height:
City:
Caste:
Qualification:

Please also send 1-2 recent photos along with this.
TEMPLATE;
    }

    /**
     * A team-added proposal is a "shell" record: real client data curated
     * by staff, but never meant to be logged into directly (client
     * confirmed this — no password is ever handed out, no verification
     * email is sent). It follows the same `users` row shape self-
     * registration builds (see RegisterController::create()), just entered
     * through one direct-entry form instead of the OTP-gated signup wizard.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Optional (client request) — a name-less shell record is odd
            // but harmless everywhere it's displayed (just renders blank),
            // same reasoning as email/contact below.
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|string',
            // The raw pasted text, preserved verbatim (mockup, Sep 2026 —
            // see add_raw_intake_text_to_users_table's docblock).
            'raw_intake_text' => 'nullable|string|max:5000',
            // Optional (client's WhatsApp intake template doesn't collect
            // these) — checked directly against the DB (SHOW INDEX): no
            // real unique constraint on either column, so a blank one is
            // safe to leave null rather than needing a placeholder value.
            'email' => 'nullable|email|unique:users,email',
            'contact_mobile_number' => 'nullable|string|max:30',
            'day' => 'required|string|size:2',
            'month' => 'required|string|size:2',
            'year' => 'required|digits:4',
            'state' => 'nullable|string',
            'religion' => 'nullable|string',
            'profile_for' => 'nullable|string|max:100',
            'mother_tongue' => 'nullable|string',
            'immigration_status' => 'nullable|string|max:100',
            'family_residence' => 'nullable|string|max:20',
            'family_values' => 'nullable|string|max:20',
            'property_financial_status' => 'nullable|string|max:255',
            'profile_description' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:255',
            'college_university' => 'nullable|string|max:2000',
            'residence_size' => 'nullable|string|max:100',
            // The 11 fields below (+ gender/day/month/year above) are the
            // compact "AI structured profile" panel's core set — client
            // confirmed "All fields required" (Sep 2026), enforced here
            // too, not just via the panel's HTML `required` attributes
            // (which a direct/non-browser POST could bypass).
            'height' => 'required|string|max:20',
            'city' => 'required|string',
            'caste' => 'required|string',
            'sect' => 'required|string|max:100',
            'marital_status' => 'required|string',
            'education' => 'required|string',
            'profession' => 'required|string|max:150',
            'con_of_citizenship' => 'required|string',
            'country' => 'required|string',
            'current_city' => 'required|string|max:150',
            'income' => 'required|string|max:50',
            'father_occupation' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'siblings_brothers' => 'nullable|string|max:2000',
            'siblings_sisters' => 'nullable|string|max:2000',
            'siblings_married_note' => 'nullable|string|max:255',
            // "AI Partner Portal" mockup fields (Sep 2026) — see the
            // add_partner_portal_classification_fields_to_users_table
            // migration. presentation_highlight is deliberately always
            // manual (never written by the paste parser) — see the
            // migration's docblock for why.
            'family_status' => 'nullable|string|max:30',
            'looking_from' => 'nullable|string|max:20',
            'profile_category' => 'nullable|string|max:20',
            'presentation_highlight' => 'nullable|string|max:30',
            'image1' => 'nullable|image|max:5120',
            'image2' => 'nullable|image|max:5120',
            // "Partner Requirements" structured fields — also required now
            // (client request, Sep 2026), same enforcement reasoning as
            // the client's own core fields above.
            'pref_age_min' => 'required|integer|min:18|max:99',
            'pref_age_max' => 'required|integer|min:18|max:99',
            'pref_height' => 'required|string|max:100',
            'pref_city' => 'required|string|max:150',
            'pref_caste_note' => 'required|string|max:255',
            'pref_qualification_note' => 'required|string|max:2000',
            'pref_profession' => 'required|string|max:150',
            'pref_religion' => 'required|string',
            'partner_requirements' => 'nullable|string|max:2000',
        ]);

        $payload = [
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
            'contact_mobile_number' => $request->contact_mobile_number,
            'height' => $request->height,
            'birthday' => $request->year . '-' . $request->month . '-' . $request->day,
            'mobile_country' => $request->country,
            'con_of_residence' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'religion' => $request->religion,
            'caste' => $request->caste,
            'sect' => $request->sect,
            'profile_for' => $request->profile_for,
            'mother_tongue' => $request->mother_tongue,
            'marital_status' => $request->marital_status,
            'education' => $request->education,
            'profession' => $request->profession,
            'con_of_citizenship' => $request->con_of_citizenship,
            'immigration_status' => $request->immigration_status,
            'family_residence' => $request->family_residence,
            'family_values' => $request->family_values,
            'income' => $request->income,
            'property_financial_status' => $request->property_financial_status,
            'profile_description' => $request->profile_description,
            'address' => $request->address,
            'college_university' => $request->college_university,
            'residence_size' => $request->residence_size,
            'current_city' => $request->current_city,
            'father_occupation' => $request->father_occupation,
            'mother_occupation' => $request->mother_occupation,
            'siblings_brothers' => $request->siblings_brothers,
            'siblings_sisters' => $request->siblings_sisters,
            'siblings_married_note' => $request->siblings_married_note,
            'family_status' => $request->family_status,
            'looking_from' => $request->looking_from,
            'profile_category' => $request->profile_category,
            'presentation_highlight' => $request->presentation_highlight,
            'raw_intake_text' => $request->raw_intake_text,
            // Shell record — never intended to log in, so the password is
            // random and never surfaced anywhere.
            'password' => Hash::make(Str::random(40)),
        ];

        $user = User::create($payload);
        $user->added_by = auth()->id();
        $user->active = 1;
        $user->save();

        $this->savePreferenceAndCheckMatches($request, $user);

        // Add Proposal form's own 2-image upload (client asked for this to
        // be part of the same form, not a separate step) — same storage
        // path as the dedicated proposal-photos page, see
        // storeProposalPhoto(). Silently skipped if a slot is empty or the
        // file fails validation — image1/image2 are optional either way.
        if ($request->hasFile('image1')) {
            $this->storeProposalPhoto($user, $request->file('image1'));
        }
        if ($request->hasFile('image2')) {
            $this->storeProposalPhoto($user, $request->file('image2'));
        }

        $loggedInUser = auth()->user();
        User::where('is_team_member', 1)->where('id', '!=', $loggedInUser->id)
            ->get()->each(fn ($teamMember) => $teamMember->notify(new NewProposalAdded($loggedInUser, $user)));

        Log::info('Team member (' . $loggedInUser->dataid . ') added proposal ' . $user->dataid . ' (' . $user->first_name . ' ' . $user->last_name . ')');

        Session::flash('message', 'success|Proposal ' . $user->dataid . ' added successfully.');
        return redirect()->route('team.proposals.mine');
    }

    /**
     * Shared by edit/update/photos/uploadPhoto/deletePhoto — only the
     * proposal's own team member or an admin may manage it.
     */
    private function authorizeProposalOwner($dataid): User
    {
        $loggedInUser = auth()->user();
        $proposal = User::where('dataid', $dataid)->first();

        if (!$proposal || empty($proposal->added_by)) {
            abort(404);
        }
        if ($proposal->added_by != $loggedInUser->id && !$loggedInUser->isAdmin()) {
            abort(403);
        }

        return $proposal;
    }

    /**
     * Website Upgrade Brief §3 "Partner Requirements / Preferred *" fields
     * — stored as this proposal's OWN PartnerPreference row (schema
     * already supports any `users.id` as `user_id`, not just self-
     * registered members). After saving, checks for a strong (>=70%) AI
     * match and fires one AiMatchFound alert if found — Website Upgrade
     * Brief §7 "AI Match Alerts". On-demand only (no background job/
     * scheduler), matching this app's existing "compute at the moment
     * it's needed" convention (see getRecommendedMatches()).
     */
    private function savePreferenceAndCheckMatches(Request $request, User $proposal): void
    {
        // store() (the redesigned Add Proposal form) and update() (the
        // still-unchanged Edit Proposal form) submit two different sets of
        // "partner requirements" inputs — only write a column when its
        // request key is actually PRESENT, so submitting from one form
        // never wipes out data the other form saved (e.g. editing a
        // proposal for an unrelated reason must not null out pref_height/
        // pref_city, which only the Add Proposal form collects).
        $columnToRequestKey = [
            'age_min' => 'pref_age_min',
            'age_max' => 'pref_age_max',
            'marital_status' => 'pref_marital_status',
            'country_id' => 'pref_country',
            'education_id' => 'pref_education',
            'profession' => 'pref_profession',
            'religion_id' => 'pref_religion',
            'general_requirement' => 'partner_requirements',
            'pref_height' => 'pref_height',
            'pref_city' => 'pref_city',
            'pref_caste_note' => 'pref_caste_note',
            'pref_qualification_note' => 'pref_qualification_note',
        ];
        $preferenceData = [];
        foreach ($columnToRequestKey as $column => $requestKey) {
            if ($request->has($requestKey)) {
                $preferenceData[$column] = $request->input($requestKey);
            }
        }

        PartnerPreference::updateOrCreate(['user_id' => $proposal->id], $preferenceData);

        $proposal = $proposal->fresh();
        $bestMatch = $proposal->getProposalMatches(1)->first();
        if (!$bestMatch) {
            return;
        }

        $preference = $proposal->partnerPreference()->first();
        $compatibility = $bestMatch->compatibilityWith($preference, [], $proposal->profile());
        if ($compatibility && $compatibility['percent'] >= 70) {
            $owner = User::find($proposal->added_by);
            if ($owner) {
                $owner->notify(new AiMatchFound($proposal, $bestMatch, $compatibility['percent']));
            }
        }
    }

    /**
     * Full-detail read view for a proposal, rendered inside the Team
     * Dashboard shell (layouts.team.dashboard) — a separate route from
     * member/profile/{dataid} (ProfileController::profile(), which renders
     * layouts.dashboard, the MEMBER Dashboard shell) so that "View file" on
     * a Team Dashboard card no longer drops a team member into the Member
     * Dashboard's own navigation/branding. Any team member or admin can
     * view any team-added proposal — the whole pool is shared, same as
     * every other read action here; only Edit stays owner-gated (see
     * authorizeProposalOwner(), used by edit()/update() below).
     */
    public function viewProposal($dataid)
    {
        $quoted = "'" . addslashes($dataid) . "'";
        $member = Profile::profiles("`u`.`dataid` = $quoted and `u`.`added_by` IS NOT NULL", null, null, null, null, null, true)->first();
        if (!$member) {
            abort(404);
        }

        $loggedInUser = auth()->user();
        $addedByUser = $member->added_by ? User::find($member->added_by) : null;
        $member->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
        $member->added_by_dataid = $addedByUser->dataid ?? null;
        $member->added_by_experience = $addedByUser->experience ?? null;
        $member->team_card_role = $member->added_by == $loggedInUser->id ? 'own' : 'other';

        $preference = PartnerPreference::where('user_id', $member->id)->first();
        $member->has_partner_preference = (bool) $preference;
        $preferenceLabels = $this->resolvePreferenceLabels($preference);

        $canViewContactInfo = $loggedInUser->canViewContactInfoOf(User::find($member->id));
        $contactUnlockSettings = \App\ContactUnlockSetting::current();

        return view('team.proposal-view', compact('member', 'preference', 'preferenceLabels', 'canViewContactInfo', 'contactUnlockSettings'));
    }

    /**
     * Shared by viewProposal() and proposalFilePanel() — resolves a
     * PartnerPreference's masterdata-id fields (marital_status/country_id/
     * etc.) to display names.
     */
    private function resolvePreferenceLabels(?PartnerPreference $preference): array
    {
        if (!$preference) {
            return [];
        }

        $lookup = fn ($type, $id) => $id ? optional(MasterData::where('type', $type)->where('dataid', $id)->first())->name : null;

        return [
            'marital_status' => $lookup('MARITAL_STATUS', $preference->marital_status),
            'country' => $lookup('COUNTRY', $preference->country_id),
            'city' => $lookup('CITY', $preference->city_id),
            'religion' => $lookup('RELIGION', $preference->religion_id),
            'caste' => $lookup('CASTE', $preference->caste_id),
            'education' => $lookup('EDUCATION', $preference->education_id),
            'mother_tongue' => $lookup('MOTHER_TONGUE', $preference->mother_tongue_id),
            'preferred_country' => $lookup('COUNTRY', $preference->preferred_country_id),
        ];
    }

    /**
     * "Complete Client File" modal (client mockup, Sep 2026) — an
     * AJAX-loaded fragment (same pattern as AdminController::profilePanel())
     * shown as an overlay by the "View Match"/"View file" actions instead
     * of opening a new tab. Same visibility rule as viewProposal(): any
     * team member/admin can open any team-added proposal's file.
     */
    public function proposalFilePanel($dataid)
    {
        $quoted = "'" . addslashes($dataid) . "'";
        $member = Profile::profiles("`u`.`dataid` = $quoted and `u`.`added_by` IS NOT NULL", null, null, null, null, null, true)->first();
        if (!$member) {
            return response()->json(['code' => '404', 'message' => 'Proposal not found.'], 404);
        }

        $loggedInUser = auth()->user();
        $addedByUser = $member->added_by ? User::find($member->added_by) : null;
        $member->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
        $member->added_by_dataid = $addedByUser->dataid ?? null;
        $member->added_by_experience = $addedByUser->experience ?? null;

        // Only used for the header's "N top AI matches" pill and gating
        // "Run AI Match" — getProposalMatchesCount() already returns 0
        // gracefully when there's no Partner Requirements row at all, so
        // no separate preference fetch is needed just for this.
        $matchCount = User::find($member->id)->getProposalMatchesCount();
        $canRunAiMatch = $member->added_by == $loggedInUser->id || $loggedInUser->isAdmin();
        $hasRealPhotos = !empty($member->images);
        $photos = $hasRealPhotos ? $member->getCardImages(null, false) : [];

        // "Original pasted form" tab (client request, Sep 2026: always show
        // it — when there's no real raw_intake_text on file, build a
        // form-shaped stand-in from the structured fields that WERE
        // entered, rather than hiding the tab). $intakeIsSynthesized lets
        // the view label it honestly as reconstructed, not the client's
        // actual original text.
        $intakeIsSynthesized = empty(trim((string) ($member->raw_intake_text ?? '')));
        $intakeText = $intakeIsSynthesized ? $this->buildSyntheticIntakeText($member) : $member->raw_intake_text;

        return [
            'code' => '200',
            'html' => view('team.partials.proposal-file-modal', compact(
                'member', 'matchCount', 'canRunAiMatch', 'hasRealPhotos', 'photos', 'intakeText', 'intakeIsSynthesized'
            ))->render(),
        ];
    }

    /**
     * Fallback for proposalFilePanel()'s "Original pasted form" tab when a
     * proposal has no raw_intake_text (added via the manual form fields,
     * not the paste-and-fill workflow) — reformats whatever structured
     * fields WERE entered into the same section headings the paste parser
     * itself recognizes (see proposals-search.blade.php's parseAndFill()),
     * skipping anything left blank rather than padding it out with
     * "Not provided" placeholders.
     */
    private function buildSyntheticIntakeText($member): string
    {
        $lines = [];
        $section = function ($title, array $fields) use (&$lines) {
            $body = [];
            foreach ($fields as $label => $value) {
                if (!empty($value)) {
                    $body[] = "{$label}: {$value}";
                }
            }
            if (!empty($body)) {
                $lines[] = $title . ':';
                $lines = array_merge($lines, $body);
                $lines[] = '';
            }
        };

        $age = !empty($member->birthday) ? date_diff(date_create($member->birthday), date_create('now'))->y : null;

        $section('Personal Information', [
            'Gender' => $member->gender ? ucfirst($member->gender) : null,
            'Age' => $age,
            'Marital Status' => $member->lbl_marital_status,
            'Height' => $member->height,
            'Profile For' => $member->profile_for,
        ]);

        $section('Religion Details', [
            'Religion' => $member->lbl_religion,
            'Caste' => $member->lbl_caste,
            'Sect' => $member->sect,
            'Mother Tongue' => $member->lbl_mother_tongue,
        ]);

        $section('Education Details', [
            'Qualification' => $member->lbl_education,
        ]);

        $section('Occupation Details', [
            'Job/Business' => $member->profession,
            'Income' => $member->income,
        ]);

        $section('Residence Details', [
            'City' => $member->lbl_city,
            'Current City' => $member->current_city,
            'Country' => $member->lbl_con_of_residence,
            'Nationality' => $member->lbl_con_of_citizenship,
        ]);

        $section('Family Details', [
            'Family Status' => $member->family_status,
        ]);

        $section('Classification', [
            'Looking From' => $member->looking_from,
            'Profile Category' => $member->profile_category,
            'Presentation Highlight' => $member->presentation_highlight,
        ]);

        $section('About', [
            'Description' => $member->profile_description,
        ]);

        $section('Team Notes', [
            'Profile Owner' => $member->added_by_name ?? null,
            'Owner Login ID' => $member->added_by_dataid ?? null,
        ]);

        return trim(implode("\n", $lines));
    }

    public function edit($dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $preference = $proposal->partnerPreference()->first();

        $religions = MasterData::where('type', 'RELIGION')->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderByRaw("name = 'Other' ASC")->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();

        return view('team.proposal-edit', compact('proposal', 'preference', 'religions', 'maritalstatuses', 'mothertongues', 'education', 'countries', 'caste'));
    }

    public function update(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = $this->authorizeProposalOwner($dataid);

        $request->validate([
            // Matches store()'s relaxed rules — a proposal saved without a
            // name/email/phone (the paste-and-fill workflow doesn't
            // collect the latter two, and name is now optional per client
            // request) must still be editable afterward for unrelated
            // reasons without being forced to backfill these first.
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|string',
            'email' => 'nullable|email|unique:users,email,' . $proposal->id,
            'contact_mobile_number' => 'nullable|string|max:30',
            'height' => 'nullable|string|max:20',
            'day' => 'required|string|size:2',
            'month' => 'required|string|size:2',
            'year' => 'required|digits:4',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'religion' => 'nullable|string',
            'caste' => 'nullable|string',
            'sect' => 'nullable|string|max:100',
            'profile_for' => 'nullable|string|max:100',
            'mother_tongue' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'education' => 'nullable|string',
            'profession' => 'nullable|string|max:150',
            'con_of_citizenship' => 'nullable|string',
            'immigration_status' => 'nullable|string|max:100',
            'family_residence' => 'nullable|string|max:20',
            'family_values' => 'nullable|string|max:20',
            'income' => 'nullable|string|max:50',
            'property_financial_status' => 'nullable|string|max:255',
            'profile_description' => 'nullable|string|max:2000',
            'pref_age_min' => 'nullable|integer|min:18|max:99',
            'pref_age_max' => 'nullable|integer|min:18|max:99',
            'pref_country' => 'nullable|string',
            'pref_education' => 'nullable|string',
            'pref_marital_status' => 'nullable|string',
            'pref_profession' => 'nullable|string|max:150',
            'partner_requirements' => 'nullable|string|max:2000',
        ]);

        $proposal->fill([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
            'contact_mobile_number' => $request->contact_mobile_number,
            'height' => $request->height,
            'birthday' => $request->year . '-' . $request->month . '-' . $request->day,
            'mobile_country' => $request->country,
            'con_of_residence' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'religion' => $request->religion,
            'caste' => $request->caste,
            'sect' => $request->sect,
            'profile_for' => $request->profile_for,
            'mother_tongue' => $request->mother_tongue,
            'marital_status' => $request->marital_status,
            'education' => $request->education,
            'profession' => $request->profession,
            'con_of_citizenship' => $request->con_of_citizenship,
            'immigration_status' => $request->immigration_status,
            'family_residence' => $request->family_residence,
            'family_values' => $request->family_values,
            'income' => $request->income,
            'property_financial_status' => $request->property_financial_status,
            'profile_description' => $request->profile_description,
        ]);
        $proposal->save();

        $this->savePreferenceAndCheckMatches($request, $proposal);

        Log::info('Team member (' . $loggedInUser->dataid . ') updated proposal ' . $proposal->dataid);

        Session::flash('message', 'success|Proposal ' . $proposal->dataid . ' updated successfully.');
        return redirect()->route('team.proposals.mine');
    }

    public function photos($dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $images = Images::where('user_id', $proposal->id)->get();

        return view('team.proposal-photos', compact('proposal', 'images'));
    }

    /**
     * Same storage convention as ProfileController::uploadImages() (see
     * app/Traits/HandlesImageUploads.php) — public/users, salted blur
     * filename, thumbnail_/thumbnail_md_/thumbnail_sm_ sizes — but keyed to
     * the PROPOSAL's id, not the uploader's. Also bakes a watermark_
     * derivative (see App\Services\WatermarkService) — currently unused
     * (any team member can see the sharp photo directly, see
     * User::canViewContactInfoOf()), kept in case per-proposal photo
     * gating is reintroduced later. Shared by uploadPhoto() (the
     * proposal-photos page, one at a time) and store() (up to 2 images
     * directly on the Add Proposal form) — returns false on a missing/
     * invalid file rather than throwing, so both callers decide how to
     * report that themselves.
     */
    private function storeProposalPhoto(User $proposal, $image): bool
    {
        $imageExtension = $image ? $this->validateUploadedImage($image) : null;
        if (!$imageExtension) {
            return false;
        }

        $name = time() . '_' . $proposal->id . '_' . random_int(1000, 9999) . '.' . $imageExtension;
        $rootImgPath = Profile::MEMBER_IMAGES_PATH;
        $path = $rootImgPath . '/' . $name;
        $publicPath = public_path($rootImgPath);
        $image->move($publicPath, $name);

        $salt = random_int(111, 99999);
        $manager = $this->createImageManager();
        if ($manager) {
            $this->generateBlurAndThumbnails($manager, $publicPath, $name, $salt);
        } else {
            $this->copyDerivativesWithoutProcessing($publicPath, $name, $salt);
        }

        $watermarkService = new \App\Services\WatermarkService();
        $watermarked = $watermarkService->watermark(
            $publicPath . '/thumbnail_' . $name,
            $publicPath . '/watermark_' . $name,
            'Urgent Rishta - ' . $proposal->dataid
        );
        if (!$watermarked) {
            // Never fall back to showing the sharp thumbnail unwatermarked —
            // copy the blur derivative instead so an un-collaborated viewer
            // still sees something obscured rather than the real photo.
            $blurName = explode('_', $name);
            $blurFile = $publicPath . '/' . $blurName[0] . $salt . $blurName[1];
            if (file_exists($blurFile)) {
                copy($blurFile, $publicPath . '/watermark_' . $name);
            }
        }

        $img = new Images();
        $img->dataid = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
        $img->name = $name;
        $img->user_id = $proposal->id;
        $img->img_url = $path;
        $img->salt = $salt;
        $img->visibility = 'Public';
        $img->displaypic = Images::where('user_id', $proposal->id)->count() === 0 ? 1 : 0;
        $img->save();

        return true;
    }

    public function uploadPhoto(Request $request, $dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);

        if (!$this->storeProposalPhoto($proposal, $request->file('image'))) {
            Session::flash('message', 'danger|Please upload a valid image (jpeg, png, webp or gif, max 5MB).');
            return redirect()->back();
        }

        Log::info('Team member (' . auth()->user()->dataid . ') uploaded a photo for proposal ' . $proposal->dataid);

        Session::flash('message', 'success|Photo uploaded.');
        return redirect()->route('team.proposals.photos', $proposal->dataid);
    }

    public function deletePhoto($dataid, $imageId)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $image = Images::where('user_id', $proposal->id)->where('id', $imageId)->first();

        if ($image) {
            $publicPath = public_path(Profile::MEMBER_IMAGES_PATH);
            $this->deleteUploadDerivatives($publicPath, $image->name, $image->salt);
            @unlink($publicPath . '/watermark_' . $image->name);
            $image->delete();
            Log::info('Team member (' . auth()->user()->dataid . ') deleted a photo for proposal ' . $proposal->dataid);
        }

        Session::flash('message', 'success|Photo deleted.');
        return redirect()->route('team.proposals.photos', $proposal->dataid);
    }

    /**
     * Routes the WhatsApp click through the server (rather than a plain
     * <a href="wa.me/...">) so it can be audit-logged, and so the message
     * text is built server-side where we can guarantee it only ever
     * contains the proposal ID + a secure portal link — never phone/
     * email/address (Website Upgrade Brief §10). Opens a chat straight
     * with the matchmaker who added the proposal (their contact_mobile_
     * number — same field the Matchmakers roster already shows/links),
     * so another team member can reach them about that specific client,
     * rather than WhatsApp's own "pick anyone" contact picker. Falls back
     * to that generic picker only if the adder has no number on file.
     */
    public function shareWhatsapp(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = User::where('dataid', $dataid)->first();

        if (!$proposal || empty($proposal->added_by)) {
            abort(404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $proposal);
        Log::info('Team member (' . $loggedInUser->dataid . ') shared proposal ' . $proposal->dataid . ' via WhatsApp');

        // "Form only" / "Form + N photos" ("Complete Client File" modal,
        // Sep 2026) — the whole client profile as one WhatsApp-formatted
        // block (same field set as buildSyntheticIntakeText()'s "Original
        // pasted form" tab: the real raw_intake_text if there is one,
        // otherwise reconstructed from whatever structured fields were
        // entered), not just a bare link. WhatsApp links can't actually
        // attach files, so ?with_photos=1 just adds a line saying photos
        // are viewable at the portal link, rather than faking an
        // attachment.
        $quoted = "'" . addslashes($dataid) . "'";
        $member = Profile::profiles("`u`.`dataid` = $quoted", null, null, null, null, null, true)->first();
        $addedByUser = User::find($proposal->added_by);
        $member->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
        $member->added_by_dataid = $addedByUser->dataid ?? null;

        $intakeText = !empty(trim((string) ($member->raw_intake_text ?? '')))
            ? $member->raw_intake_text
            : $this->buildSyntheticIntakeText($member);

        // route('share.proposal', ...), not member/profile/{dataid} — see
        // the comment on forwardBothWhatsapp()'s own $text above.
        $text = "*CLIENT PROFILE*\n\n" . $intakeText . "\n\n" . route('share.proposal', $proposal->dataid);
        if ($request->boolean('with_photos')) {
            $text .= "\n(client photos are viewable at this link)";
        }

        $link = (!empty($addedByUser) && !empty($addedByUser->contact_mobile_number))
            ? User::whatsappLinkForNumber($addedByUser->contact_mobile_number, $text)
            : User::whatsappShareLink($text);

        return redirect()->away($link);
    }

    /**
     * Set only by the proposal's own team member or an admin. Direct
     * property assignment (not mass-assignment) — same reasoning as
     * is_team_member/added_by in User::$fillable's exclusion comment.
     */
    public function updateProfileStatus(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = User::where('dataid', $dataid)->first();

        if (!$proposal || empty($proposal->added_by)) {
            return ['code' => '404', 'message' => 'Proposal was not found.'];
        }
        if ($proposal->added_by != $loggedInUser->id && !$loggedInUser->isAdmin()) {
            return ['code' => '403', 'message' => 'You are not allowed to change this proposal\'s status.'];
        }

        $request->validate([
            'profile_status' => 'required|in:active,on_hold,matched,engaged,married,closed',
        ]);

        $proposal->profile_status = $request->profile_status;
        $proposal->save();

        Log::info('Team member (' . $loggedInUser->dataid . ') set proposal ' . $proposal->dataid . ' status to ' . $request->profile_status);

        return ['code' => '200', 'message' => 'Profile status updated.', 'profile_status' => $proposal->profile_status];
    }

    /**
     * Any team member can mark any two team-added proposals as a
     * successful match directly — the whole proposal pool is shared/open
     * across the team, there's no accept-first gate to satisfy.
     */
    public function markSuccessfulMatch(Request $request)
    {
        $loggedInUser = auth()->user();

        $request->validate([
            'proposal_dataid' => 'required|string|different:counterpart_dataid',
            'counterpart_dataid' => 'required|string',
        ]);

        $proposal = User::where('dataid', $request->proposal_dataid)->whereNotNull('added_by')->first();
        $counterpart = User::where('dataid', $request->counterpart_dataid)->whereNotNull('added_by')->first();
        if (!$proposal || !$counterpart) {
            Session::flash('message', 'danger|One of the proposals was not found.');
            return redirect()->back();
        }

        $alreadyExists = \App\SuccessfulMatch::where(function ($q) use ($proposal, $counterpart) {
            $q->where('proposal_id', $proposal->id)->where('counterpart_proposal_id', $counterpart->id);
        })->orWhere(function ($q) use ($proposal, $counterpart) {
            $q->where('proposal_id', $counterpart->id)->where('counterpart_proposal_id', $proposal->id);
        })->exists();
        if ($alreadyExists) {
            Session::flash('message', 'warning|These two proposals are already marked as a successful match.');
            return redirect()->back();
        }

        \App\SuccessfulMatch::create([
            'proposal_id' => $proposal->id,
            'counterpart_proposal_id' => $counterpart->id,
            'partner_a_id' => $proposal->added_by,
            'partner_b_id' => $counterpart->added_by,
            'matched_at' => now()->toDateString(),
        ]);

        AuditLog::record($loggedInUser, 'match.marked_successful', $proposal);
        Log::info('Team member (' . $loggedInUser->dataid . ') marked ' . $proposal->dataid . ' and ' . $counterpart->dataid . ' as a successful match');

        Session::flash('message', 'success|Marked as a successful match.');
        return redirect()->route('team.successful-matches');
    }
}
