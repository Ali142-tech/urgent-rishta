<?php

namespace App\Http\Controllers;

use App\Proposal;
use App\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Team management, inside the Team Dashboard — only for an admin's own team
 * account (team_members.is_admin, see AdminTeamMemberSeeder). Shows the
 * whole-team overview, lists and reviews the team (members, applications)
 * and shows every team proposal. An admin does everything an ordinary team
 * member does through TeamController as well; these are the extra pages.
 */
class TeamAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('team_member');
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::guard('team')->user()->isAdmin(), 403, 'Admin access required.');
            return $next($request);
        });
    }

    /** The admin's Team Dashboard: headline numbers, each member's workload, latest proposals. */
    public function overview()
    {
        $stats = [
            'active_members' => TeamMember::active()->count(),
            'pending_applications' => TeamMember::pending()->count(),
            'proposals_total' => Proposal::count(),
            'proposals_active' => Proposal::where('profile_status', 'active')->count(),
            'proposals_week' => Proposal::where('created_at', '>=', now()->subDays(7))->count(),
            'matches_total' => \App\SuccessfulMatch::count(),
        ];

        $members = TeamMember::approved()->orderBy('first_name')->get();
        foreach ($members as $member) {
            $member->proposals_count = Proposal::where('added_by', $member->id)->count();
            $member->active_proposals_count = Proposal::where('added_by', $member->id)->where('profile_status', 'active')->count();
            $member->matches_count = \App\SuccessfulMatch::where('partner_a_id', $member->id)->orWhere('partner_b_id', $member->id)->count();
            $member->last_proposal_at = Proposal::where('added_by', $member->id)->max('created_at');
        }

        $recent = Proposal::with('owner')->orderByDesc('created_at')->take(8)->get();

        return view('team.manage.overview', compact('stats', 'members', 'recent'));
    }

    /** Every proposal on the platform, with search and a "added by" filter. */
    public function proposals(Request $request)
    {
        $pageSize = 12;
        $query = Proposal::query()->with('photos');

        if (!empty($request->keyword)) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('reference', 'like', $keyword)->orWhere('profession', 'like', $keyword)
                    ->orWhere('city', 'like', $keyword)->orWhere('current_city', 'like', $keyword);
            });
        }
        if (!empty($request->matchmaker)) {
            $query->where('added_by', (int) $request->matchmaker);
        }

        $resultCount = (int) (clone $query)->count();
        $numPages = max(1, (int) ceil($resultCount / $pageSize));
        $currentPage = min(max(1, (int) $request->query('page', 1)), $numPages);

        $members = $query->orderByDesc('created_at')->skip($pageSize * ($currentPage - 1))->take($pageSize)->get();

        $owners = $members->isEmpty()
            ? collect()
            : TeamMember::whereIn('id', $members->pluck('added_by')->unique()->values())
                ->get(['id', 'first_name', 'last_name', 'dataid', 'experience'])->keyBy('id');
        foreach ($members as $member) {
            $owner = $owners->get($member->added_by);
            $member->added_by_name = $owner ? trim($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
            $member->added_by_dataid = $owner->dataid ?? null;
            $member->added_by_experience = $owner->experience ?? null;
            $member->has_partner_preference = $member->hasPartnerPreferences();
            // An admin may edit any proposal (TeamController::authorizeProposalOwner()).
            $member->team_card_role = 'own';
        }

        $matchmakers = TeamMember::approved()->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('team.manage.proposals', compact('members', 'resultCount', 'currentPage', 'numPages', 'matchmakers'));
    }

    /** Approved team members, with a message box each and suspend / deactivate / reactivate. */
    public function members()
    {
        $search = trim((string) request()->query('search', ''));

        $query = TeamMember::approved();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $members = $query->orderBy('first_name')->get();

        return view('team.manage.members', ['members' => $members, 'search' => $search]);
    }

    /** One message to a team member, or to all of them. */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $recipients = $request->recipient === 'all'
            ? TeamMember::approved()->get()
            : TeamMember::approved()->where('dataid', $request->recipient)->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\AdminMessage($request->message));
        }

        Log::info('Admin team account (' . Auth::guard('team')->user()->dataid . ') sent a message to ' . $recipients->count() . ' team member(s)');

        Session::flash('message', 'success|Message sent to ' . $recipients->count() . ' team member(s).');
        return redirect()->route('team.manage.members');
    }

    /** Suspend / deactivate / reactivate an approved team member. */
    public function updateStatus($action, $dataid)
    {
        $admin = Auth::guard('team')->user();

        $statusMap = ['suspend' => 'suspended', 'deactivate' => 'deactivated', 'reactivate' => 'active'];
        if (!isset($statusMap[$action])) {
            return ['code' => '404', 'message' => 'Action not permitted!!!'];
        }

        $member = TeamMember::approved()->where('dataid', $dataid)->first();
        if (!$member) {
            return ['code' => '404', 'message' => 'Team member was not found (id: ' . $dataid . ')'];
        }
        if ($member->id === $admin->id) {
            return ['code' => '404', 'message' => 'You cannot change the status of your own account.'];
        }

        $member->status = $statusMap[$action];
        $member->save();

        $name = $member->first_name . ' ' . $member->last_name;
        Log::info('Admin team account (' . $admin->dataid . ') set team member status of ' . $name . ' (' . $member->email . ') to ' . $statusMap[$action]);

        return ['code' => '200', 'message' => $name . ' is now ' . $statusMap[$action] . '.', 'team_member_status' => $member->status];
    }

    /** "Become a Partner" applications awaiting a decision. */
    public function applications()
    {
        $applications = TeamMember::pending()->orderBy('created_at')->get();
        return view('team.manage.applications', ['applications' => $applications]);
    }

    /** Approving lets the applicant sign in at /team/login and use the Team Dashboard. */
    public function approve($dataid)
    {
        $admin = Auth::guard('team')->user();
        $member = TeamMember::pending()->where('dataid', $dataid)->first();
        if (!$member) {
            return ['code' => '404', 'message' => 'Application was not found or already reviewed.'];
        }

        $member->update([
            'application_status' => 'approved',
            'is_approved' => true,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        Log::info('Admin team account (' . $admin->dataid . ') approved matchmaker application ' . $member->dataid . ' (' . $member->email . ')');

        return ['code' => '200', 'message' => $member->first_name . ' ' . $member->last_name . ' has been approved as a team member.'];
    }

    public function reject($dataid)
    {
        $admin = Auth::guard('team')->user();
        $member = TeamMember::pending()->where('dataid', $dataid)->first();
        if (!$member) {
            return ['code' => '404', 'message' => 'Application was not found or already reviewed.'];
        }

        $member->update(['application_status' => 'rejected']);

        Log::info('Admin team account (' . $admin->dataid . ') rejected matchmaker application ' . $member->dataid . ' (' . $member->email . ')');

        return ['code' => '200', 'message' => $member->first_name . ' ' . $member->last_name . '\'s application has been rejected.'];
    }
}
