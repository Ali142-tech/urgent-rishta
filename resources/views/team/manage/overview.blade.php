{{--
    The Team Dashboard an ADMIN sees (team_members.is_admin): an overview of the
    whole partner team instead of one matchmaker's own clients. Ordinary team
    members keep team/overview.blade.php. See TeamAdminController::overview().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Team Overview')
@section('main-content')
<style>
    .ur-td-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-td-head p { color: #6B7570; font-size: 13.5px; margin: 0 0 20px; }
    .ur-td-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
    .ur-td-stat { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 16px 18px; }
    .ur-td-stat b { display: block; font-family: 'Playfair Display', serif; font-size: 28px; color: #123A2E; line-height: 1.1; }
    .ur-td-stat span { font-size: 12px; color: #6B7570; }
    .ur-td-stat a { font-size: 11.5px; font-weight: 700; color: #C9974D; }
    .ur-td-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; margin-bottom: 24px; overflow-x: auto; }
    .ur-td-card h2 { font-size: 15px; font-weight: 700; color: #123A2E; margin: 0; padding: 14px 18px; border-bottom: 1px solid #E7E2D6; }
    .ur-td-card table { width: 100%; border-collapse: collapse; }
    .ur-td-card th, .ur-td-card td { padding: 10px 18px !important; text-align: left; border-bottom: 1px solid #F0ECE2; font-size: 13.5px; }
    .ur-td-card th { background: #F6F4EF; color: #123A2E; font-weight: 700; font-size: 12.5px; }
    .ur-td-card tr:last-child td { border-bottom: none; }
    .ur-td-pill { display: inline-block; border-radius: 999px; padding: 2px 10px; font-size: 11.5px; font-weight: 700; background: #E6F1EA; color: #123A2E; }
    .ur-td-pill--warn { background: #FCF3E3; color: #8A5A12; }
    .ur-td-empty { padding: 22px 18px; color: #6B7570; font-size: 13.5px; }
</style>

<div class="ur-td-head">
    <h1>Team Overview</h1>
    <p>How the partner team is doing &mdash; members, proposals and matches in one place.</p>
</div>

<div class="ur-td-stats">
    <div class="ur-td-stat"><b>{{ $stats['active_members'] }}</b><span>Active team members</span><br><a href="{{ route('team.manage.members') }}">Manage &rarr;</a></div>
    <div class="ur-td-stat"><b>{{ $stats['pending_applications'] }}</b><span>Pending applications</span><br><a href="{{ route('team.manage.applications') }}">Review &rarr;</a></div>
    <div class="ur-td-stat"><b>{{ $stats['proposals_total'] }}</b><span>Proposals ({{ $stats['proposals_active'] }} active)</span><br><a href="{{ route('team.manage.proposals') }}">View all &rarr;</a></div>
    <div class="ur-td-stat"><b>{{ $stats['proposals_week'] }}</b><span>Added in the last 7 days</span></div>
    <div class="ur-td-stat"><b>{{ $stats['matches_total'] }}</b><span>Successful matches</span></div>
</div>

<div class="ur-td-card">
    <h2>Team members</h2>
    @if($members->isEmpty())
        <div class="ur-td-empty">No approved team members yet.</div>
    @else
    <table>
        <thead><tr><th>Name</th><th>Status</th><th>Proposals</th><th>Active</th><th>Matches</th><th>Last proposal</th></tr></thead>
        <tbody>
        @foreach($members as $member)
            <tr>
                <td>{{ $member->first_name }} {{ $member->last_name }}@if($member->isPremium()) @include('team.partials.premium-badge') @endif @if($member->is_admin) <span class="ur-td-pill ur-td-pill--warn">Admin</span>@endif<br><small style="color:#6B7570;">{{ $member->dataid }}@if($member->experience) &bull; {{ $member->experience }}@endif</small></td>
                <td><span class="ur-td-pill {{ $member->status === 'active' ? '' : 'ur-td-pill--warn' }}">{{ ucfirst($member->status) }}</span> <a href="{{ route('team.manage.members') }}" style="font-size:11.5px; font-weight:700; color:#C9974D;">change</a></td>
                <td>{{ $member->proposals_count }}</td>
                <td>{{ $member->active_proposals_count }}</td>
                <td>{{ $member->matches_count }}</td>
                <td>{{ $member->last_proposal_at ? \Carbon\Carbon::parse($member->last_proposal_at)->diffForHumans() : '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

<div class="ur-td-card">
    <h2>Latest proposals</h2>
    @if($recent->isEmpty())
        <div class="ur-td-empty">No proposals have been added yet.</div>
    @else
    <table>
        <thead><tr><th>Ref</th><th>Client</th><th>City</th><th>Added by</th><th>Status</th><th>Added</th><th></th></tr></thead>
        <tbody>
        @foreach($recent as $proposal)
            <tr>
                <td><b>{{ $proposal->reference }}</b></td>
                <td>{{ ucfirst($proposal->gender) }}{{ $proposal->birthday ? ', ' . \Carbon\Carbon::parse($proposal->birthday)->age : '' }}{{ $proposal->profession ? ' · ' . $proposal->profession : '' }}</td>
                <td>{{ $proposal->current_city ?: $proposal->city ?: '—' }}</td>
                <td>{{ $proposal->owner ? $proposal->owner->first_name . ' ' . $proposal->owner->last_name : 'Unknown' }}</td>
                <td><span class="ur-td-pill">{{ ucfirst(str_replace('_', ' ', $proposal->profile_status)) }}</span></td>
                <td>{{ $proposal->created_at->diffForHumans() }}</td>
                <td><a href="{{ route('team.proposals.view', $proposal->reference) }}">View</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
