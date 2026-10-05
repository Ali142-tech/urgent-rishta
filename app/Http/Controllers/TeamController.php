<?php

namespace App\Http\Controllers;

use App\AuditLog;
use App\Images;
use App\MasterData;
use App\Notifications\AiMatchFound;
use App\Notifications\NewProposalAdded;
use App\Profile;
use App\Proposal;
use App\ProposalCaste;
use App\TeamMember;
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
        $this->middleware('team_member');
    }

    /**
     * Shared by myProposals()/searchProposals(): runs the (already filtered)
     * Proposal query, paginates it, and decorates each row with its owner's
     * name/ID/experience for the card byline plus the 'own'/'other' role.
     */
    private function buildProposalGrid($query, int $pageRequested, int $pageSize = 12): array
    {
        $resultCount = (int) (clone $query)->count();
        $numPages = max(1, (int) ceil($resultCount / $pageSize));
        $pageRequested = min(max(1, $pageRequested), $numPages);
        $members = $query->with('photos')->orderByDesc('created_at')
            ->skip($pageSize * ($pageRequested - 1))->take($pageSize)->get();

        $owners = $members->isEmpty()
            ? collect()
            : TeamMember::whereIn('id', $members->pluck('added_by')->unique()->values())
                ->get(['id', 'first_name', 'last_name', 'dataid'])->keyBy('id');

        // 'own' vs 'other' — the whole proposal pool is open to every team
        // member, so this only decides whether the card shows Edit (own) or
        // just View/Share (anyone else's).
        $viewerId = auth()->id();
        foreach ($members as $member) {
            $owner = $owners->get($member->added_by);
            $member->added_by_name = $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
            $member->added_by_dataid = $owner->dataid ?? null;
            $member->added_by_experience = $owner->experience ?? null;
            $member->team_card_role = $member->added_by == $viewerId ? 'own' : 'other';
            $member->has_partner_preference = $member->hasPartnerPreferences();
        }

        return [
            'members' => $members,
            'resultCount' => $resultCount,
            'currentPage' => $pageRequested,
            'numPages' => $numPages,
        ];
    }

    /**
     * Dashboard overview — stat cards, a short AI Match preview, live
     * notifications panel and the Quick Add Proposal form.
     */
    public function dashboard(Request $request)
    {
        // An admin's team account gets the whole-team overview, not a matchmaker's own client dashboard.
        if (auth()->user()->isAdmin()) {
            return app(TeamAdminController::class)->overview();
        }

        $viewerId = auth()->id();

        $myProposalsCount = Proposal::where('added_by', $viewerId)->count();
        $teamProposalsCount = Proposal::count();
        $successfulMatchesCount = \App\SuccessfulMatch::where('partner_a_id', $viewerId)->orWhere('partner_b_id', $viewerId)->count();
        // Grouped in a closure — chaining ->orWhere() then ->whereMonth()/
        // ->whereYear() directly would bind the month/year filters only to
        // the OR's second clause, not both (a real precedence bug).
        $successfulMatchesThisMonth = \App\SuccessfulMatch::where(function ($q) use ($viewerId) {
            $q->where('partner_a_id', $viewerId)->orWhere('partner_b_id', $viewerId);
        })->whereMonth('matched_at', now()->month)->whereYear('matched_at', now()->year)->count();

        // AI Matches Found + a short preview — across this team member's own proposals only.
        $aiMatchesCount = 0;
        $previewMatches = collect();
        foreach (Proposal::where('added_by', $viewerId)->with('photos')->get() as $proposal) {
            $aiMatchesCount += $proposal->getProposalMatchesCount();
            if ($previewMatches->count() < 3) {
                foreach ($proposal->getProposalMatches(3) as $match) {
                    if ($previewMatches->count() >= 3) break;
                    $match->for_proposal_dataid = $proposal->dataid;
                    $match->for_proposal_profile = $proposal;   // the client this match is for
                    $previewMatches->push($match);
                }
            }
        }

        $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();

        // "Pending Requests" stand-in: unread notifications (the Collaboration
        // Request feature that name used to mean was removed).
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
     * "My Clients" — this team member's own proposals as a searchable,
     * status-filterable table.
     */
    public function myProposals(Request $request)
    {
        $query = Proposal::where('added_by', auth()->id());

        if ($request->filled('search')) {
            $keyword = '%' . $request->search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('reference', 'like', $keyword)->orWhere('profession', 'like', $keyword)
                    ->orWhere('city', 'like', $keyword)->orWhere('current_city', 'like', $keyword);
            });
        }
        if ($request->filled('status')) {
            $query->where('profile_status', $request->status);
        }

        // 15/page (table rows are far denser than the old card grid).
        $grid = $this->buildProposalGrid($query, (int) $request->query('page', 1), 15);

        // Match count is null (rendered as "—") for a client with no Partner
        // Requirements yet: nothing to compute against, not "0 candidates".
        foreach ($grid['members'] as $member) {
            $member->matchesCount = $member->hasPartnerPreferences() ? $member->getProposalMatchesCount() : null;
        }

        return view('team.proposals-mine', $grid);
    }

    /**
     * "My Matches" — every one of the viewer's OWN clients, each paired with
     * its single best AI match, on one page.
     */
    public function myMatches()
    {
        $viewerId = auth()->id();
        $myProposals = Proposal::where('added_by', $viewerId)->with('photos')->get();

        $pairs = collect();
        foreach ($myProposals as $proposal) {
            if (!$proposal->hasPartnerPreferences()) {
                continue;
            }

            [$best, $bestCompat] = $this->bestMatchFor($proposal);
            if (!$best) {
                continue;
            }

            $owner = TeamMember::find($best->added_by);

            $pairs->push([
                'client' => $proposal,
                'match' => $best,
                'compat' => $bestCompat,
                'ownerName' => $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown',
                'ownerLocation' => $owner->city ?? null,
                'ownerPhone' => $owner->contact_mobile_number ?? null,
                'ownerExperience' => $owner->experience ?? null,
                // "Verified owner" = an admin-approved, active team member account.
                'ownerVerified' => $owner && $owner->isActiveMember(),
            ]);
        }

        $pairs = $pairs->sortByDesc(fn ($p) => $p['compat']['percent'] ?? 0)->values();

        return view('team.my-matches', [
            'pairs' => $pairs,
            'activeClientProfiles' => $myProposals->count(),
            'bestMatchesReady' => $pairs->count(),
            'highestCompatibility' => $pairs->isNotEmpty() ? ($pairs->first()['compat']['percent'] ?? 0) : 0,
        ]);
    }

    /**
     * Shared by myMatches() and matchmakers() — the single highest-scoring
     * candidate for one proposal. Returns [null, null] if it has no Partner
     * Requirements or no candidates score at all.
     */
    private function bestMatchFor(Proposal $proposal): array
    {
        $best = null;
        $bestCompat = null;
        foreach ($proposal->getProposalMatches(20) as $candidate) {
            $compat = $candidate->compatibilityWith($proposal);
            $percent = $compat['percent'] ?? 0;
            if ($best === null || $percent > ($bestCompat['percent'] ?? -1)) {
                $best = $candidate;
                $bestCompat = $compat;
            }
        }

        return [$best, $bestCompat];
    }

    /**
     * "Search Proposals" — the whole proposal pool (everyone's) with quick
     * keyword/profession/country tabs and the "More filters" set.
     */
    public function searchProposals(Request $request)
    {
        // Default landing state: with no filters submitted at all, show a
        // "Doctor" keyword search rather than the whole unfiltered pool.
        // ?view=all (the sidebar's "Team Proposals" link) opts out of that.
        $filterKeys = ['keyword', 'height_from', 'height_to', 'profession', 'country', 'gender', 'aged_from', 'aged_to', 'caste', 'marital_status', 'education', 'city', 'current_city', 'matchmaker'];
        $hasAnyFilter = collect($filterKeys)->contains(fn ($key) => $request->filled($key));
        if (!$hasAnyFilter && $request->query('view') !== 'all') {
            $request->merge(['keyword' => 'Doctor']);
        }

        $query = Proposal::query();

        if (!empty($request->aged_from)) {
            $query->whereNotNull('birthday')->whereRaw(
                'TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN ? AND ?',
                [(int) $request->aged_from, $request->aged_to ? (int) $request->aged_to : 75]
            );
        }
        if (!empty($request->gender)) {
            $query->where('gender', $request->gender);
        }
        if (!empty($request->profession)) {
            $query->where('profession', 'like', '%' . $request->profession . '%');
        }
        if (!empty($request->city)) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if (!empty($request->country)) {
            $query->where('con_of_residence', $request->country);
        }
        if (!empty($request->marital_status)) {
            $query->where('marital_status', $request->marital_status);
        }
        if (!empty($request->caste)) {
            $query->where('caste_id', $request->caste);
        }
        if (!empty($request->education)) {
            $query->where('education', $request->education);
        }
        if (!empty($request->current_city)) {
            $query->where('current_city', 'like', '%' . $request->current_city . '%');
        }
        // Height is free text ("5'6", "5.6", ...), so the range is checked in PHP on the already-filtered rows.
        if ($request->filled('height_from') || $request->filled('height_to')) {
            $from = $request->filled('height_from') ? (int) $request->height_from : 0;
            $to = $request->filled('height_to') ? (int) $request->height_to : 999;
            if ($from > $to) {
                [$from, $to] = [$to, $from];
            }
            $ids = (clone $query)->whereNotNull('height')->pluck('height', 'id')
                ->filter(function ($height) use ($from, $to) {
                    $inches = Proposal::heightToInches($height);
                    return $inches !== null && $inches >= $from && $inches <= $to;
                })->keys();
            $query->whereIn('id', $ids);
        }
        // Set by "View proposals" on the Matchmakers Directory.
        if (!empty($request->matchmaker)) {
            $query->where('added_by', (int) $request->matchmaker);
        }
        // Quick "keyword" tab: a loose OR across the free-text fields.
        if (!empty($request->keyword)) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('reference', 'like', $keyword)->orWhere('profession', 'like', $keyword)
                    ->orWhere('city', 'like', $keyword)->orWhere('current_city', 'like', $keyword);
            });
        }

        $grid = $this->buildProposalGrid($query, (int) $request->query('page', 1), 10);

        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = ProposalCaste::options();
        $education = $this->educationOptions();

        // "X profiles in the shared network" badge — the whole pool, unfiltered.
        $totalNetworkCount = Proposal::count();

        return view('team.proposals-search', array_merge($grid, compact(
            'maritalstatuses', 'countries', 'caste', 'education', 'totalNetworkCount'
        )));
    }

    /**
     * "AI Match Center" — pick any of THIS team member's own proposals that
     * has Partner Requirements set, optionally narrow by minimum score /
     * country, and see every compatible candidate with a per-factor "why".
     * The dropdown only lists the viewer's own proposals; the matched
     * CANDIDATES are drawn from the whole shared pool.
     */
    public function matches(Request $request)
    {
        $selected = $request->query('proposal');
        if (empty($selected)) {
            $default = Proposal::where('added_by', auth()->id())->withPreferences()
                ->orderByDesc('updated_at')->first();

            if (!$default) {
                return view('team.matches', ['noProposals' => true]);
            }
            $selected = $default->reference;
        }

        return view('team.matches', $this->buildMatchCenterData($selected, $request));
    }

    /**
     * Deep-link variant of matches() — same page, forcing which proposal is
     * pre-selected (linked from the cards' "AI Match" buttons, which only
     * show on the viewer's own proposals).
     */
    public function matchesForProposal($dataid)
    {
        return view('team.matches', $this->buildMatchCenterData($dataid, request()));
    }

    private function buildMatchCenterData(string $selectedReference, Request $request): array
    {
        $proposal = $this->authorizeProposalOwner($selectedReference);
        $proposalProfile = $proposal;
        $preference = $proposal->preference;

        // Dropdown options — the viewer's OWN proposals that have requirements.
        // Admins see every proposal, since they may open any of them.
        $candidateProposals = (auth()->user()->isAdmin() ? Proposal::query() : Proposal::where('added_by', auth()->id()))
            ->withPreferences()->orderByDesc('updated_at')->get();

        $minScore = (int) $request->query('min_score', 0);
        $locationFilter = $request->query('location');

        $rawMatches = $proposal->getProposalMatches(50);

        $ownerIds = collect($rawMatches)->pluck('added_by')->push($proposal->added_by)->filter()->unique()->values();
        $owners = $ownerIds->isEmpty()
            ? collect()
            : TeamMember::whereIn('id', $ownerIds)->get(['id', 'first_name', 'last_name', 'dataid', 'contact_mobile_number'])->keyBy('id');

        $existingSuccessfulIds = \App\SuccessfulMatch::where('proposal_id', $proposal->id)
            ->pluck('counterpart_proposal_id')
            ->merge(\App\SuccessfulMatch::where('counterpart_proposal_id', $proposal->id)->pluck('proposal_id'));

        $matches = collect();
        foreach ($rawMatches as $match) {
            $compat = $match->compatibilityWith($proposal);
            $percent = $compat['percent'] ?? 0;
            if ($percent < $minScore) {
                continue;
            }
            if (!empty($locationFilter) && $match->con_of_residence != $locationFilter) {
                continue;
            }

            $owner = $owners->get($match->added_by);
            $match->added_by_name = $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
            $match->added_by_dataid = $owner->dataid ?? null;
            $match->added_by_experience = $owner->experience ?? null;
            $match->added_by_whatsapp = $owner->contact_mobile_number ?? null;
            $match->compat = $compat;
            $match->already_successful = $existingSuccessfulIds->contains($match->id);
            $matches->push($match);
        }
        $matches = $matches->sortByDesc(fn ($m) => $m->compat['percent'] ?? 0)->values();

        $proposalOwner = $owners->get($proposal->added_by);
        $proposalProfile->added_by_name = $proposalOwner ? trim($proposalOwner->first_name . ' ' . $proposalOwner->last_name) : 'Unknown';
        $proposalProfile->added_by_experience = $proposalOwner->experience ?? null;
        $proposalProfile->team_card_role = $proposal->added_by == auth()->id() ? 'own' : 'other';

        $locations = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();

        return compact('proposal', 'proposalProfile', 'preference', 'candidateProposals', 'matches', 'minScore', 'locationFilter', 'locations');
    }

    /**
     * "Forward Both" (AI Match Center) — sends the RECOMMENDED proposal's
     * owner a WhatsApp message with both proposals' links so they can review
     * the pairing together.
     */
    public function forwardBothWhatsapp($proposalDataid, $matchDataid)
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $proposalDataid)->first();
        $match = Proposal::where('reference', $matchDataid)->first();
        if (!$proposal || !$match) {
            abort(404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $match);
        Log::info('Team member (' . $loggedInUser->dataid . ') forwarded proposals ' . $proposal->reference . ' + ' . $match->reference . ' via WhatsApp');

        // route('share.proposal', ...) — a public link the WhatsApp preview crawler can open.
        $text = 'Potential match — ' . $proposal->reference . ': ' . route('share.proposal', $proposal->reference)
            . "\n" . $match->reference . ': ' . route('share.proposal', $match->reference);

        $matchOwner = TeamMember::find($match->added_by);
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

    /** Unread notifications for the bell (polled by public/js/app.js). */
    public function notificationsRefresh()
    {
        return auth()->user()->unreadNotifications()->limit(5)->get()->toArray();
    }

    public function profile()
    {
        return view('team.profile', ['member' => auth()->user()]);
    }

    public function profileUpdate(Request $request)
    {
        $member = auth()->user();
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_mobile_number' => 'required|string|max:30|unique:team_members,contact_mobile_number,' . $member->id,
            'city' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:255',
            'about_me' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:5120',
            'watermark_text' => 'nullable|string|max:100',
            'watermark_style' => 'nullable|in:diagonal,corner',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:3072',
        ]);

        $member->fill(collect($data)->except(['image', 'logo'])->all());
        $member->watermark_text = trim((string) $request->input('watermark_text')) ?: null;
        $member->watermark_style = $request->input('watermark_style') ?: 'diagonal';

        $logoDir = public_path(\App\TeamMember::PHOTO_PATH . '/logos');
        if ($request->boolean('remove_logo') && $member->logo) {
            @unlink($logoDir . '/' . $member->logo);
            $member->logo = null;
        }
        if ($request->hasFile('logo')) {
            \Illuminate\Support\Facades\File::ensureDirectoryExists($logoDir);
            if ($member->logo) {
                @unlink($logoDir . '/' . $member->logo);
            }
            $logoName = time() . '_' . $member->id . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move($logoDir, $logoName);
            $member->logo = $logoName;
        }
        $member->save();
        if ($request->hasFile('image')) {
            (new \App\Services\TeamMemberPhotoService())->store($member, $request->file('image'));
        }

        Session::flash('message', 'success|Profile updated.');
        return redirect()->route('team.profile');
    }

    public function passwordForm()
    {
        return view('team.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $member = auth()->user();
        if (!Hash::check($request->current_password, $member->password)) {
            return back()->withErrors(['current_password' => 'Your current password is not correct.']);
        }

        $member->password = Hash::make($request->password);
        $member->save();

        Session::flash('message', 'success|Password updated.');
        return redirect()->route('team.password');
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
     * Read-only team roster ("Matchmakers"): every active team member with
     * their profile info and two stats — proposals they currently own that
     * are still active, and successful matches credited to them — plus
     * "Recommended for your clients". No management actions here.
     */
    public function matchmakers()
    {
        $viewerId = auth()->id();
        $members = TeamMember::active()
            ->orderBy('first_name')
            ->get();

        foreach ($members as $member) {
            $member->activeProposalsCount = Proposal::where('added_by', $member->id)->where('profile_status', 'active')->count();
            $member->successfulMatchesCount = \App\SuccessfulMatch::where('partner_a_id', $member->id)
                ->orWhere('partner_b_id', $member->id)
                ->count();
            // "Senior Partner" ribbon — experience is a single free-text field.
            $member->isSenior = stripos((string) $member->experience, 'senior') !== false;
            $member->hasRealPhoto = !empty($member->photo);
        }

        // "Recommended for your clients": for each of MY clients with requirements
        // set, every OTHER matchmaker's candidate that scores above 0, best first,
        // capped to the top 3 shown.
        $recommendations = collect();
        foreach (Proposal::where('added_by', $viewerId)->with('photos')->get() as $client) {
            if (!$client->hasPartnerPreferences()) {
                continue;
            }
            [$overallBest] = $this->bestMatchFor($client);

            foreach ($client->getProposalMatches(10) as $candidate) {
                if ($candidate->added_by == $viewerId) {
                    continue;
                }
                $compat = $candidate->compatibilityWith($client);
                $percent = $compat['percent'] ?? 0;
                if ($percent <= 0) {
                    continue;
                }
                $owner = TeamMember::find($candidate->added_by);
                $reasons = collect($compat['checks'] ?? [])->filter(fn ($c) => $c['matched'])->pluck('label')->take(3)->implode(' • ');
                $recommendations->push([
                    'client' => $client,
                    'candidate' => $candidate,
                    'percent' => $percent,
                    'reasons' => $reasons,
                    'ownerName' => $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown',
                    'isTopMatch' => $overallBest && $overallBest->id === $candidate->id,
                ]);
            }
        }
        $recommendations = $recommendations->sortByDesc('percent')->take(3)->values();

        return view('team.matchmakers', compact('members', 'recommendations'));
    }

    /** "Upload my photo" / "Add photo" on the Matchmakers Directory — sets the team member's OWN profile photo. */
    public function uploadMyPhoto(Request $request)
    {
        if (!(new \App\Services\TeamMemberPhotoService())->store(auth()->user(), $request->file('image'))) {
            Session::flash('message', 'danger|Please upload a valid image (jpeg, png, webp or gif, max 5MB).');
            return redirect()->back();
        }

        Session::flash('message', 'success|Photo uploaded.');
        return redirect()->back();
    }

    public function create()
    {
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $education = $this->educationOptions();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = ProposalCaste::options();      // the short proposal caste list
        $religions = collect();                  // proposals no longer carry a religion
        $sendTemplate = $this->clientIntakeTemplate();

        $professionOptions = $this->professionOptions();

        return view('team.proposal-create', compact('religions', 'maritalstatuses', 'education', 'countries', 'caste', 'sendTemplate', 'professionOptions'));
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
Assalam o Alaikum! To create your profile, please fill in the details after each colon (:) and send this form back as it is. Please do not change the headings.

1. PERSONAL INFORMATION:
Name:
Gender:
Age:
Marital Status:
Height:

2. EDUCATION DETAILS:
Qualification:

3. OCCUPATION DETAIL:
Job/Business:

4. RELIGION DETAILS:
Sect:
Caste:

5. RESIDENCE DETAILS:
Country:
City:
Current City:
Nationality:

6. YOUR REQUIREMENTS:
Marital Status:
Age Limit:
Height:
Qualification:
Profession:
Caste:
City:
Nationality:
Other Requirements:

Please also send 1-2 recent photos along with this form.
(You may write more than one answer for the requirements, for example: Caste: Rajput, Arain)
TEMPLATE;
    }

    /**
     * Saves a new proposal: one row in `proposals` (reference P-001, P-002 ...),
     * its partner requirements, and up to two photos.
     */
    /** The client's education pick-list as objects (dataid = name) so the form and filter loops need no change. */
    private function educationOptions()
    {
        return collect(config('proposal_options.education'))->map(fn ($n) => (object) ['dataid' => $n, 'name' => $n]);
    }

    private function professionOptions()
    {
        return collect(config('proposal_options.profession'))->map(fn ($n) => (object) ['dataid' => $n, 'name' => $n]);
    }

    /**
     * "Other — add manually" for education, profession and the partner profession / educations:
     * the typed text replaces the '__other' marker before validation and saving.
     */
    private function resolveManualOptions(Request $request): void
    {
        foreach (['education' => 'education_other', 'profession' => 'profession_other'] as $field => $otherField) {
            if ($request->input($field) === '__other') {
                $request->validate([$otherField => 'required|string|max:150'], [$otherField . '.required' => 'Please type the ' . str_replace('_', ' ', $field) . '.']);
                $request->merge([$field => trim(preg_replace('/\s+/', ' ', $request->input($otherField)))]);
            }
        }

        $professions = (array) $request->input('pref_professions', []);
        if (in_array('__other', $professions, true)) {
            $request->validate(['pref_profession_other' => 'required|string|max:200'], ['pref_profession_other.required' => 'Please type the partner profession.']);
            $typed = array_filter(array_map('trim', explode(',', $request->input('pref_profession_other'))));
            $request->merge(['pref_professions' => array_values(array_unique(array_merge(array_diff($professions, ['__other']), $typed)))]);
        }

        $educations = (array) $request->input('pref_educations', []);
        if (in_array('__other', $educations, true)) {
            $request->validate(['pref_education_other' => 'required|string|max:200'], ['pref_education_other.required' => 'Please type the partner education.']);
            $typed = array_filter(array_map('trim', explode(',', $request->input('pref_education_other'))));
            $request->merge(['pref_educations' => array_values(array_unique(array_merge(array_diff($educations, ['__other']), $typed)))]);
        }
    }

    /** A multi-select's picked values as a clean list, or null when nothing was picked. */
    private function multiValues(Request $request, string $field): ?array
    {
        $values = array_values(array_unique(array_filter((array) $request->input($field, []), fn ($v) => $v !== null && $v !== '')));
        // "Any" means no preference and replaces every other pick.
        if (in_array('__any', $values, true)) {
            return ['__any'];
        }
        return $values ? $values : null;
    }

    /** "Not in the list" caste: the typed name is reused if it already exists, otherwise added, and the request then carries its id. */
    private function resolveManualCaste(Request $request): void
    {
        if ($request->caste !== '__new') {
            return;
        }
        $request->validate(['caste_other' => 'required|string|max:80'], ['caste_other.required' => 'Please type the caste name.']);
        $name = trim(preg_replace('/\s+/', ' ', $request->caste_other));
        $caste = ProposalCaste::whereRaw('lower(name) = ?', [mb_strtolower($name)])->first()
            ?: ProposalCaste::create(['name' => $name, 'sort_order' => 0, 'is_active' => true]);
        $request->merge(['caste' => (string) $caste->id]);
    }

    public function store(Request $request)
    {
        $this->resolveManualCaste($request);
        $this->resolveManualOptions($request);
        $request->validate([
            'gender' => 'required|string|in:male,female',
            // The raw pasted text, preserved verbatim.
            'raw_intake_text' => 'required|string|min:100|max:5000',
            'day' => 'required|string|size:2',
            'month' => 'required|string|size:2',
            'year' => 'required|digits:4',
            // The client's core fields — all required.
            'height' => 'required|string|max:20',
            'city' => 'required|string|max:150',
            'caste' => 'required|exists:proposal_castes,id',
            'sect' => 'required|string|max:100',
            'marital_status' => 'required|string',
            'education' => 'required|string',
            'profession' => 'required|string|max:150',
            'con_of_citizenship' => 'required|string',
            'country' => 'required|string',
            'current_city' => 'required|string|max:150',
            // Classification the team picks by hand (never filled by the parser).
            'family_status' => 'nullable|string|max:30',
            'looking_from' => 'nullable|string|max:20',
            'presentation_highlight' => 'nullable|string|max:30',
            'image1' => 'nullable|image|max:5120',
            'image2' => 'nullable|image|max:5120',
            // Partner requirements.
            'pref_age_min' => 'required|integer|min:18|max:99',
            'pref_age_max' => 'required|integer|min:18|max:99',
            'pref_height' => 'required|string|max:100',
            'pref_city' => 'required|string|max:150',
            'pref_professions' => 'required|array|min:1', 'pref_professions.*' => 'string|max:150',
            'pref_nationalities' => 'nullable|array', 'pref_nationalities.*' => 'string|max:20',
            'pref_castes' => 'required|array|min:1', 'pref_castes.*' => ['string', fn ($attr, $value, $fail) => ($value === '__any' || ProposalCaste::whereKey($value)->exists()) ?: $fail('Please pick a valid caste.')],
            'pref_educations' => 'required|array|min:1', 'pref_educations.*' => 'string|max:50',
            'pref_marital_statuses' => 'required|array|min:1', 'pref_marital_statuses.*' => 'string|max:50',
            'partner_requirements' => 'nullable|string|max:2000',
        ]);

        if (!checkdate((int) $request->month, (int) $request->day, (int) $request->year)) {
            return back()->withInput()->withErrors(['day' => 'That date of birth is not a real date.']);
        }

        $proposal = Proposal::create([
            'added_by' => auth()->id(),
            'gender' => $request->gender,
            'birthday' => $request->year . '-' . $request->month . '-' . $request->day,
            'height' => $request->height,
            'marital_status' => $request->marital_status,
            'education' => $request->education,
            'profession' => $request->profession,
            'caste_id' => $request->caste,
            'sect' => $request->sect,
            'con_of_residence' => $request->country,
            'con_of_citizenship' => $request->con_of_citizenship,
            'current_city' => $request->current_city,
            'city' => $request->city,
            'family_status' => $request->family_status,
            'looking_from' => $request->looking_from,
            'presentation_highlight' => $request->presentation_highlight,
            'raw_intake_text' => $request->raw_intake_text,
            'pref_age_min' => $request->pref_age_min,
            'pref_age_max' => $request->pref_age_max,
            'pref_height' => $request->pref_height,
            'pref_city' => $request->pref_city,
            'pref_profession' => implode(', ', array_diff($this->multiValues($request, 'pref_professions') ?? [], ['__any'])) ?: null,
            'pref_professions' => $this->multiValues($request, 'pref_professions'),
            'pref_nationalities' => $this->multiValues($request, 'pref_nationalities'),
            'pref_castes' => $this->multiValues($request, 'pref_castes'),
            'pref_educations' => $this->multiValues($request, 'pref_educations'),
            'pref_marital_statuses' => $this->multiValues($request, 'pref_marital_statuses'),
            'pref_note' => $request->partner_requirements,
        ]);
        $proposal = $proposal->fresh();   // picks up the generated reference

        // Up to two photos straight from the Add form (both optional).
        $photos = new \App\Services\ProposalPhotoService();
        foreach (['image1', 'image2'] as $field) {
            if ($request->hasFile($field)) {
                $photos->store($proposal, $request->file($field));
            }
        }

        $this->alertOnStrongMatch($proposal);

        $loggedInUser = auth()->user();
        TeamMember::approved()->where('id', '!=', $loggedInUser->id)
            ->get()->each(fn ($teamMember) => $teamMember->notify(new NewProposalAdded($loggedInUser, $proposal)));

        Log::info('Team member (' . $loggedInUser->dataid . ') added proposal ' . $proposal->reference);

        Session::flash('message', 'success|Proposal ' . $proposal->reference . ' added successfully.');
        return redirect()->route('team.proposals.mine');
    }

    /**
     * Shared by edit/update/photos/uploadPhoto/deletePhoto — only the
     * proposal's own team member or an admin may manage it.
     */
    private function authorizeProposalOwner($reference): Proposal
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $reference)->first();

        if (!$proposal) {
            abort(404);
        }
        if ($proposal->added_by != $loggedInUser->id && !$loggedInUser->isAdmin()) {
            abort(403);
        }

        return $proposal;
    }

    /**
     * After a proposal is saved: if its best candidate scores 70% or more, tell
     * the owner (one "AI Match Found" notification). Computed on demand — no
     * background job.
     */
    private function alertOnStrongMatch(Proposal $proposal): void
    {
        $best = $proposal->getProposalMatches(1)->first();
        if (!$best) {
            return;
        }

        $compatibility = $best->compatibilityWith($proposal);
        if ($compatibility && $compatibility['percent'] >= 70) {
            $owner = TeamMember::find($proposal->added_by);
            if ($owner) {
                $owner->notify(new AiMatchFound($proposal, $best, $compatibility['percent']));
            }
        }
    }

    /**
     * What the Complete Client File (popup and full page) shows for a proposal:
     * photos, the original/rebuilt form text, and match counts.
     */
    private function clientFileData(Proposal $member, $viewer): array
    {
        $owner = $member->added_by ? TeamMember::find($member->added_by) : null;
        $member->added_by_name = $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
        $member->added_by_dataid = $owner->dataid ?? null;
        $member->added_by_experience = $owner->experience ?? null;
        $member->team_card_role = $member->added_by == $viewer->id ? 'own' : 'other';
        $member->has_partner_preference = $member->hasPartnerPreferences();

        $matchCount = $member->getProposalMatchesCount();
        $canRunAiMatch = $member->added_by == $viewer->id || $viewer->isAdmin();
        $hasRealPhotos = $member->photos->isNotEmpty();
        $photos = $hasRealPhotos ? $member->getCardImages(null, true) : [];

        // "Original pasted form": the real text when there is one, otherwise
        // rebuilt from the saved fields (labelled honestly in the view).
        $intakeIsSynthesized = empty(trim((string) ($member->raw_intake_text ?? '')));
        $intakeText = $intakeIsSynthesized ? $this->buildSyntheticIntakeText($member) : $member->raw_intake_text;

        return compact('matchCount', 'canRunAiMatch', 'hasRealPhotos', 'photos', 'intakeText', 'intakeIsSynthesized');
    }

    /**
     * Full-detail read view for a proposal inside the Team Dashboard shell.
     * Any team member or admin can view any proposal (the pool is shared);
     * only Edit stays owner-gated.
     */
    public function viewProposal($dataid)
    {
        $member = Proposal::with('photos')->where('reference', $dataid)->first();
        if (!$member) {
            abort(404);
        }

        $data = $this->clientFileData($member, auth()->user());
        $preference = $member->preference;
        $preferenceLabels = [];

        return view('team.proposal-view', array_merge($data, compact('member', 'preference', 'preferenceLabels')));
    }

    /**
     * "Complete Client File" popup — an AJAX-loaded fragment shown as an
     * overlay by the "View file" actions. Same visibility rule as
     * viewProposal(): any team member/admin can open any proposal's file.
     */
    public function proposalFilePanel($dataid)
    {
        $member = Proposal::with('photos')->where('reference', $dataid)->first();
        if (!$member) {
            return response()->json(['code' => '404', 'message' => 'Proposal not found.'], 404);
        }

        $data = $this->clientFileData($member, auth()->user());

        return [
            'code' => '200',
            'html' => view('team.partials.proposal-file-modal', array_merge($data, compact('member')))->render(),
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

        // Edit = the Add Proposal ("Paste Profile") page, opened in edit mode with the
        // saved values, so the pasted original form and every field can be updated there.
        $religions = collect();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $education = $this->educationOptions();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = ProposalCaste::options();
        $sendTemplate = $this->clientIntakeTemplate();

        $professionOptions = $this->professionOptions();
        $editAge = $proposal->birthday ? $proposal->birthday->age : null;
        $editValues = [
            'gender' => $proposal->gender,
            'height' => $proposal->height,
            'country' => $proposal->con_of_residence,
            'current_city' => $proposal->current_city,
            'city' => $proposal->city,
            'caste' => $proposal->caste_id ? (string) $proposal->caste_id : null,
            'sect' => $proposal->sect,
            'marital_status' => $proposal->marital_status,
            'education' => $proposal->education,
            'profession' => $proposal->profession,
            'con_of_citizenship' => $proposal->con_of_citizenship,
            'day' => $proposal->birthday ? $proposal->birthday->format('d') : null,
            'month' => $proposal->birthday ? $proposal->birthday->format('m') : null,
            'year' => $proposal->birthday ? $proposal->birthday->format('Y') : null,
            'family_status' => $proposal->family_status,
            'looking_from' => $proposal->looking_from,
            'presentation_highlight' => $proposal->presentation_highlight,
            'pref_age_min' => $proposal->pref_age_min,
            'pref_age_max' => $proposal->pref_age_max,
            'pref_height' => $proposal->pref_height,
            'pref_city' => $proposal->pref_city,
            'partner_requirements' => $proposal->pref_note,
        ];
        // Partner educations that are not in the pick-list were typed in: show them under "Other".
        $knownEducation = config('proposal_options.education');
        $savedEducations = array_map('strval', $proposal->pref_educations ?: []);
        $typedEducations = array_values(array_diff($savedEducations, $knownEducation, ['__any']));
        $savedProfessions = $proposal->prefProfessionList();
        $savedProfessions = array_map('strval', $proposal->pref_professions ?: $proposal->prefProfessionList());
        $typedProfessions = array_values(array_diff($savedProfessions, config('proposal_options.profession'), ['__any']));
        $editExtra = ['pref_education_other' => implode(', ', $typedEducations), 'pref_profession_other' => implode(', ', $typedProfessions)];
        $editMulti = [
            'pref_nationalities[]' => array_map('strval', $proposal->pref_nationalities ?: []),
            'pref_professions[]' => $typedProfessions ? array_values(array_merge(array_intersect($savedProfessions, config('proposal_options.profession')), ['__other'])) : $savedProfessions,
            'pref_castes[]' => array_map('strval', $proposal->pref_castes ?: []),
            'pref_educations[]' => $typedEducations ? array_values(array_merge(array_intersect($savedEducations, $knownEducation), ['__other'])) : $savedEducations,
            'pref_marital_statuses[]' => array_map('strval', $proposal->pref_marital_statuses ?: []),
        ];

        // Current real photos (never the gender placeholder) for the two photo boxes.
        $existingPhotos = $proposal->photos->isNotEmpty() ? $proposal->getCardImages(null, false) : [];

        return view('team.proposal-create', compact(
            'proposal', 'editMulti', 'religions', 'maritalstatuses', 'education', 'countries', 'caste', 'sendTemplate', 'professionOptions', 'editExtra',
            'editValues', 'editAge', 'existingPhotos'
        ));
    }

    public function update(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = $this->authorizeProposalOwner($dataid);
        $this->resolveManualCaste($request);
        $this->resolveManualOptions($request);

        $request->validate([
            'gender' => 'required|string|in:male,female',
            'day' => 'required|string|size:2',
            'month' => 'required|string|size:2',
            'year' => 'required|digits:4',
            'height' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:150',
            'current_city' => 'nullable|string|max:150',
            'caste' => 'nullable|exists:proposal_castes,id',
            'sect' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string',
            'education' => 'nullable|string',
            'profession' => 'nullable|string|max:150',
            'con_of_citizenship' => 'nullable|string',
            'country' => 'nullable|string',
            'family_status' => 'nullable|string|max:30',
            'looking_from' => 'nullable|string|max:20',
            'presentation_highlight' => 'nullable|string|max:30',
            'raw_intake_text' => 'nullable|string|max:5000',
            'image1' => 'nullable|image|max:5120',
            'image2' => 'nullable|image|max:5120',
            'pref_age_min' => 'nullable|integer|min:18|max:99',
            'pref_age_max' => 'nullable|integer|min:18|max:99',
            'pref_height' => 'nullable|string|max:100',
            'pref_city' => 'nullable|string|max:150',
            'pref_professions' => 'nullable|array', 'pref_professions.*' => 'string|max:150',
            'pref_nationalities' => 'nullable|array', 'pref_nationalities.*' => 'string|max:20',
            'pref_castes' => 'nullable|array', 'pref_castes.*' => ['string', fn ($attr, $value, $fail) => ($value === '__any' || ProposalCaste::whereKey($value)->exists()) ?: $fail('Please pick a valid caste.')],
            'pref_educations' => 'nullable|array', 'pref_educations.*' => 'string|max:50',
            'pref_marital_statuses' => 'nullable|array', 'pref_marital_statuses.*' => 'string|max:50',
            'partner_requirements' => 'nullable|string|max:2000',
        ]);

        if (!checkdate((int) $request->month, (int) $request->day, (int) $request->year)) {
            return back()->withInput()->withErrors(['day' => 'That date of birth is not a real date.']);
        }

        // The page posts only the fields it shows, so write ONLY what was sent.
        $columnFromInput = [
            'gender' => 'gender', 'height' => 'height', 'marital_status' => 'marital_status',
            'education' => 'education', 'profession' => 'profession', 'caste_id' => 'caste', 'sect' => 'sect',
            'con_of_residence' => 'country', 'con_of_citizenship' => 'con_of_citizenship',
            'current_city' => 'current_city', 'city' => 'city',
            'family_status' => 'family_status', 'looking_from' => 'looking_from',
            'presentation_highlight' => 'presentation_highlight',
            'pref_age_min' => 'pref_age_min', 'pref_age_max' => 'pref_age_max', 'pref_height' => 'pref_height',
            'pref_city' => 'pref_city', 'pref_note' => 'partner_requirements',
        ];
        $changes = [];
        foreach ($columnFromInput as $column => $inputKey) {
            if ($request->has($inputKey)) {
                $changes[$column] = $request->input($inputKey);
            }
        }
        // Multi-selects send nothing when emptied, so they are always written (the edit page always has them).
        foreach (['pref_castes', 'pref_educations', 'pref_marital_statuses', 'pref_professions', 'pref_nationalities'] as $multi) {
            $changes[$multi] = $this->multiValues($request, $multi);
        }
        $changes['pref_profession'] = implode(', ', array_diff($changes['pref_professions'] ?? [], ['__any'])) ?: null;
        $changes['birthday'] = $request->year . '-' . $request->month . '-' . $request->day;
        // The pasted original form: replaced only when text was actually provided.
        if ($request->filled('raw_intake_text')) {
            $changes['raw_intake_text'] = $request->raw_intake_text;
        }
        $proposal->fill($changes)->save();

        // New images are ADDED to the existing ones (delete or reorder on the Manage photos page).
        $photos = new \App\Services\ProposalPhotoService();
        foreach (['image1', 'image2'] as $field) {
            if ($request->hasFile($field)) {
                $photos->store($proposal, $request->file($field));
            }
        }

        $this->alertOnStrongMatch($proposal->fresh());

        Log::info('Team member (' . $loggedInUser->dataid . ') updated proposal ' . $proposal->reference);

        Session::flash('message', 'success|Proposal ' . $proposal->reference . ' updated successfully.');
        return redirect()->route('team.proposals.mine');
    }

    public function photos($dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $images = $proposal->photos;

        return view('team.proposal-photos', compact('proposal', 'images'));
    }

    public function uploadPhoto(Request $request, $dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);

        if (!(new \App\Services\ProposalPhotoService())->store($proposal, $request->file('image'))) {
            Session::flash('message', 'danger|Please upload a valid image (jpeg, png, webp or gif, max 5MB).');
            return redirect()->back();
        }

        Log::info('Team member (' . auth()->user()->dataid . ') uploaded a photo for proposal ' . $proposal->reference);

        Session::flash('message', 'success|Photo uploaded.');
        return redirect()->route('team.proposals.photos', $proposal->reference);
    }

    /** Deletes a proposal (owner or admin). Soft delete: the row and photo files stay recoverable; it disappears from every list. */
    public function destroy($dataid)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $reference = $proposal->reference;
        $proposal->delete();

        Log::info('Team account (' . auth()->user()->dataid . ') deleted proposal ' . $reference);
        Session::flash('message', 'success|Proposal ' . $reference . ' deleted.');
        return redirect()->route('team.proposals.mine');
    }

    public function deletePhoto($dataid, $imageId)
    {
        $proposal = $this->authorizeProposalOwner($dataid);
        $photo = \App\ProposalPhoto::where('proposal_id', $proposal->id)->where('id', $imageId)->first();

        if ($photo) {
            (new \App\Services\ProposalPhotoService())->delete($photo);
            Log::info('Team member (' . auth()->user()->dataid . ') deleted a photo for proposal ' . $proposal->reference);
        }

        Session::flash('message', 'success|Photo deleted.');
        return redirect()->route('team.proposals.photos', $proposal->reference);
    }

    /**
     * Routes the WhatsApp click through the server so it can be audit-logged,
     * and so the message text is built server-side from the proposal's form
     * plus its public link (never phone/email/address). Opens a chat with the
     * matchmaker who owns the proposal when they have a number on file,
     * otherwise WhatsApp's own contact picker.
     */
    public function shareWhatsapp(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $dataid)->first();
        if (!$proposal) {
            abort(404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $proposal);
        Log::info('Team member (' . $loggedInUser->dataid . ') shared proposal ' . $proposal->reference . ' via WhatsApp');

        $addedByUser = TeamMember::find($proposal->added_by);
        $proposal->added_by_name = $addedByUser ? trim($addedByUser->first_name . ' ' . $addedByUser->last_name) : 'Unknown';
        $proposal->added_by_dataid = $addedByUser->dataid ?? null;

        $intakeText = !empty(trim((string) ($proposal->raw_intake_text ?? '')))
            ? $proposal->raw_intake_text
            : $this->buildSyntheticIntakeText($proposal);

        $text = "*CLIENT PROFILE*\n\n" . $intakeText . "\n\n" . route('share.proposal', $proposal->reference);
        if ($request->boolean('with_photos')) {
            $text .= "\n(client photos are viewable at this link)";
        }

        $link = (!empty($addedByUser) && !empty($addedByUser->contact_mobile_number))
            ? User::whatsappLinkForNumber($addedByUser->contact_mobile_number, $text)
            : User::whatsappShareLink($text);

        return redirect()->away($link);
    }

    /**
     * One proposal's shareable content: the client form text (the real pasted
     * form when there is one, otherwise rebuilt from the fields) plus its
     * public link, and the paths of its REAL photos (full-size when the file
     * exists; never the gender placeholder).
     */
    private function shareContentFor(Proposal $proposal): array
    {
        $proposal->loadMissing('photos');

        $intakeText = !empty(trim((string) ($proposal->raw_intake_text ?? '')))
            ? $proposal->raw_intake_text
            : $this->buildSyntheticIntakeText($proposal);
        $link = route('share.proposal', $proposal->reference);

        $photos = [];
        $branding = new \App\Services\PhotoBrandingService();
        $owner = TeamMember::find($proposal->added_by);   // the proposal's owner: their branding stays on the picture wherever it is shared
        foreach (json_decode($proposal->getLightGalleryImages(false), true) ?: [] as $g) {
            if (!empty($g['src'])) {
                // The owner's logo / watermark / name + ID go on the copy that leaves; the stored photo stays clean.
                $branded = $branding->brandedPath($owner, basename(parse_url($g['src'], PHP_URL_PATH)), $proposal->reference);
                $photos[] = $branded ?: $g['src'];
            }
        }

        return ['intake' => $intakeText, 'link' => $link, 'text' => "*CLIENT PROFILE*\n\n" . $intakeText . "\n\n" . $link, 'photos' => array_values($photos)];
    }

    /**
     * JSON behind "Forward Both" (AI Match / My Matches): BOTH proposals' forms
     * and photos in one share, instead of just two links. Same JSON shape as
     * sharePayload(), so the same click handler drives it.
     */
    public function forwardPayload($proposalDataid, $matchDataid)
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $proposalDataid)->first();
        $match = Proposal::where('reference', $matchDataid)->first();
        if (!$proposal || !$match) {
            return response()->json(['code' => '404', 'message' => 'Proposal was not found.'], 404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $match);
        Log::info('Team member (' . $loggedInUser->dataid . ') forwarded proposals ' . $proposal->reference . ' + ' . $match->reference . ' (native share)');

        $a = $this->shareContentFor($proposal);
        $b = $this->shareContentFor($match);

        $text = "*POTENTIAL MATCH*\n\n"
            . "*CLIENT — " . $proposal->reference . "*\n\n" . $a['intake'] . "\n\n" . $a['link']
            . "\n\n━━━━━━━━━━━━━━━\n\n"
            . "*MATCHED PROFILE — " . $match->reference . "*\n\n" . $b['intake'] . "\n\n" . $b['link'];

        return response()->json([
            'code' => '200',
            'title' => $proposal->reference . ' + ' . $match->reference,
            'text' => $text,
            'photos' => array_values(array_merge($a['photos'], $b['photos'])),
            // Two long forms don't fit comfortably as a photo caption: photos first, forms second.
            'two_step' => mb_strlen($text) > 3000,
            'fallback' => route('team.matches.forward', [$proposal->reference, $match->reference]),
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
    }
    /**
     * JSON behind the Share button on Advanced Search / Team Proposals cards:
     * the same "Form + photos" content the Complete Client File shares (the
     * client profile text + the real photo files), so the browser can hand
     * the actual images — not just a link — to WhatsApp via the native share
     * sheet. Built on click, rather than embedded in every card.
     */
    public function sharePayload($dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $dataid)->first();
        if (!$proposal) {
            return response()->json(['code' => '404', 'message' => 'Proposal was not found.'], 404);
        }

        AuditLog::record($loggedInUser, 'profile.shared', $proposal);
        Log::info('Team member (' . $loggedInUser->dataid . ') shared proposal ' . $proposal->reference . ' (native share)');

        $pieces = $this->shareContentFor($proposal);

        return response()->json([
            'code' => '200',
            'title' => $proposal->reference,
            'text' => $pieces['text'],
            'photos' => $pieces['photos'],
            // wa.me link used when the browser can't attach files.
            'fallback' => route('team.proposals.share.whatsapp', ['dataid' => $proposal->reference, 'with_photos' => $pieces['photos'] ? 1 : 0]),
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
    }

    public function updateProfileStatus(Request $request, $dataid)
    {
        $loggedInUser = auth()->user();
        $proposal = Proposal::where('reference', $dataid)->first();

        if (!$proposal) {
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

        Log::info('Team member (' . $loggedInUser->dataid . ') set proposal ' . $proposal->reference . ' status to ' . $request->profile_status);

        return ['code' => '200', 'message' => 'Profile status updated.', 'profile_status' => $proposal->profile_status];
    }

    /**
     * Any team member can mark any two proposals as a successful match
     * directly — the pool is shared, there's no accept-first gate.
     */
    public function markSuccessfulMatch(Request $request)
    {
        $loggedInUser = auth()->user();

        $request->validate([
            'proposal_dataid' => 'required|string|different:counterpart_dataid',
            'counterpart_dataid' => 'required|string',
        ]);

        $proposal = Proposal::where('reference', $request->proposal_dataid)->first();
        $counterpart = Proposal::where('reference', $request->counterpart_dataid)->first();
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
        Log::info('Team member (' . $loggedInUser->dataid . ') marked ' . $proposal->reference . ' and ' . $counterpart->reference . ' as a successful match');

        Session::flash('message', 'success|Marked as a successful match.');
        return redirect()->route('team.successful-matches');
    }
}
