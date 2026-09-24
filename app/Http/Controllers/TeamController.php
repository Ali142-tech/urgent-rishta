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
        // unrelated callers.
        $addedByIds = collect($members)->pluck('added_by')->filter()->unique()->values();
        $addedByNames = $addedByIds->isEmpty()
            ? collect()
            : User::whereIn('id', $addedByIds)->get(['id', 'first_name', 'last_name'])->keyBy('id');
        // 'own' vs 'other' — the whole team-added pool is open to every team
        // member, so this only decides whether member-card.blade.php shows
        // the Edit action (own proposal) or just View/Share (anyone else's).
        $viewerId = auth()->id();
        foreach ($members as $member) {
            $addedByUser = $addedByNames->get($member->added_by);
            $member->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
            $member->team_card_role = $member->added_by == $viewerId ? 'own' : 'other';
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
                foreach ($proposal->getProposalMatches(3) as $match) {
                    if ($previewMatches->count() >= 3) break;
                    $match->viewer_preference_for_card = $preference;
                    $match->for_proposal_dataid = $proposal->dataid;
                    $previewMatches->push($match);
                }
            }
        }

        $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();

        // Quick Add Proposal form dropdowns (right-hand panel).
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();

        return view('team.overview', [
            'myProposalsCount' => $myProposalsCount,
            'aiMatchesCount' => $aiMatchesCount,
            'teamProposalsCount' => $teamProposalsCount,
            'successfulMatchesCount' => $successfulMatchesCount,
            'previewMatches' => $previewMatches,
            'recentNotifications' => $recentNotifications,
            'countries' => $countries,
            'maritalstatuses' => $maritalstatuses,
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

        $grid = $this->buildProposalGrid($where, (int) $request->query('page', 1));

        $religions = MasterData::where('type', 'RELIGION')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();

        return view('team.proposals-search', array_merge($grid, compact('religions', 'maritalstatuses', 'mothertongues', 'countries', 'caste', 'education')));
    }

    /**
     * "AI Matches" — one row per proposal (paginated, 20/page — still
     * bounded even with dozens/hundreds of proposals), each showing a
     * COUNT plus a small preview strip (top 3, compact tiles, not the
     * full member-card component) so there's something useful to see
     * without an extra click, while staying far lighter than rendering
     * every match for every proposal on one page. "View all matches"
     * opens matchesForProposal() below for the full list.
     */
    public function matches(Request $request)
    {
        $viewerId = auth()->id();
        $pageSize = in_array((int) $request->query('per_page', 10), [5, 10, 20, 50], true)
            ? (int) $request->query('per_page', 10) : 10;

        $proposalsQuery = User::where('added_by', $viewerId)->whereHas('partnerPreference');
        $totalProposals = $proposalsQuery->count();
        $numPages = max(1, (int) ceil($totalProposals / $pageSize));
        $pageRequested = min(max(1, (int) $request->query('page', 1)), $numPages);

        $proposals = $proposalsQuery->orderByDesc('updated_at')
            ->skip($pageSize * ($pageRequested - 1))->take($pageSize)->get();

        // "New" is approximated as "the candidate proposal itself was added
        // in the last 7 days" — matches are computed live, not stored, so
        // there's no real "first seen" timestamp to check without a new
        // table. This only checks the top-3 preview per row (kept cheap),
        // not every match, so it undercounts rather than over-claims.
        $newCutoff = now()->subDays(7);

        $rows = $proposals->map(function ($proposal) use ($newCutoff) {
            $preference = $proposal->partnerPreference;
            $previewMatches = $proposal->getProposalMatches(3);
            $bestScore = 0;
            $newCount = 0;
            foreach ($previewMatches as $match) {
                $compat = $match->compatibilityWith($preference);
                $match->preview_compat = $compat;
                $bestScore = max($bestScore, $compat['percent'] ?? 0);
                if (!empty($match->created_at) && $match->created_at->greaterThan($newCutoff)) {
                    $newCount++;
                }
            }

            return [
                'proposal' => $proposal,
                'matchCount' => $proposal->getProposalMatchesCount(),
                'previewMatches' => $previewMatches,
                'bestScore' => $bestScore,
                'newCount' => $newCount,
            ];
        });

        return view('team.matches', [
            'rows' => $rows,
            'totalProposals' => $totalProposals,
            'totalMatchesThisPage' => $rows->sum('matchCount'),
            'newThisWeekCount' => $rows->sum('newCount'),
            'currentPage' => $pageRequested,
            'numPages' => $numPages,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * Drill-down from matches() — every match for ONE proposal.
     */
    public function matchesForProposal($dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $preference = $proposal->partnerPreference;
        if (!$preference) {
            abort(404);
        }

        $matches = $proposal->getProposalMatches(24);
        $existingSuccessfulIds = \App\SuccessfulMatch::where('proposal_id', $proposal->id)
            ->pluck('counterpart_proposal_id')
            ->merge(\App\SuccessfulMatch::where('counterpart_proposal_id', $proposal->id)->pluck('proposal_id'));
        $viewerId = auth()->id();
        foreach ($matches as $match) {
            $match->viewer_preference_for_card = $preference;
            $match->already_successful = $existingSuccessfulIds->contains($match->id);
            $match->interest_sent = auth()->user()->inList($match->dataid, 'interest');
        }

        // "Partner Requirements" pills on the header banner — only whatever
        // this proposal's preference actually has filled in (the Add/Edit
        // Proposal form only collects age/country/marital status/
        // education/profession — not religion/caste, unlike a self-
        // registered member's own preferences page).
        $requirementPills = [];
        if (!empty($preference->age_min) || !empty($preference->age_max)) {
            $requirementPills[] = 'Age ' . ($preference->age_min ?: '18') . '-' . ($preference->age_max ?: '99');
        }
        if (!empty($preference->country_id)) {
            $country = MasterData::where('type', 'COUNTRY')->where('dataid', $preference->country_id)->first();
            if ($country) {
                $requirementPills[] = $country->name;
            }
        }
        if (!empty($preference->marital_status)) {
            $maritalStatus = MasterData::where('type', 'MARITAL_STATUS')->where('dataid', $preference->marital_status)->first();
            if ($maritalStatus) {
                $requirementPills[] = $maritalStatus->name;
            }
        }
        if (!empty($preference->education_id)) {
            $education = MasterData::where('type', 'EDUCATION')->where('dataid', $preference->education_id)->first();
            if ($education) {
                $requirementPills[] = $education->name;
            }
        }
        if (!empty($preference->profession)) {
            $requirementPills[] = $preference->profession;
        }

        return view('team.matches-detail', compact('proposal', 'matches', 'requirementPills'));
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

    public function create()
    {
        $religions = MasterData::where('type', 'RELIGION')->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderByRaw("name = 'Other' ASC")->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $sendTemplate = $this->clientIntakeTemplate();

        return view('team.proposal-create', compact('religions', 'maritalstatuses', 'mothertongues', 'education', 'countries', 'caste', 'sendTemplate'));
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
            // Optional (client's WhatsApp intake template doesn't collect
            // these) — checked directly against the DB (SHOW INDEX): no
            // real unique constraint on either column, so a blank one is
            // safe to leave null rather than needing a placeholder value.
            'email' => 'nullable|email|unique:users,email',
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
            'address' => 'nullable|string|max:255',
            'college_university' => 'nullable|string|max:2000',
            'residence_size' => 'nullable|string|max:100',
            'current_city' => 'nullable|string|max:150',
            'father_occupation' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'siblings_brothers' => 'nullable|string|max:2000',
            'siblings_sisters' => 'nullable|string|max:2000',
            'siblings_married_note' => 'nullable|string|max:255',
            'image1' => 'nullable|image|max:5120',
            'image2' => 'nullable|image|max:5120',
            'pref_age_min' => 'nullable|integer|min:18|max:99',
            'pref_age_max' => 'nullable|integer|min:18|max:99',
            'pref_height' => 'nullable|string|max:100',
            'pref_city' => 'nullable|string|max:150',
            'pref_caste_note' => 'nullable|string|max:255',
            'pref_qualification_note' => 'nullable|string|max:2000',
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
        $compatibility = $bestMatch->compatibilityWith($preference);
        if ($compatibility && $compatibility['percent'] >= 70) {
            $owner = User::find($proposal->added_by);
            if ($owner) {
                $owner->notify(new AiMatchFound($proposal, $bestMatch, $compatibility['percent']));
            }
        }
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
     * email/address (Website Upgrade Brief §10). No target number is set
     * (see User::whatsappShareLink()) — it opens WhatsApp's own contact
     * picker instead of messaging the client directly.
     */
    public function shareWhatsapp($dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = User::where('dataid', $dataid)->first();

        if (!$proposal || empty($proposal->added_by)) {
            abort(404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $proposal);
        Log::info('Team member (' . $loggedInUser->dataid . ') shared proposal ' . $proposal->dataid . ' via WhatsApp');

        $text = 'Proposal ' . $proposal->dataid . ' — ' . url('member/profile/' . $proposal->dataid);
        return redirect()->away(User::whatsappShareLink($text));
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
