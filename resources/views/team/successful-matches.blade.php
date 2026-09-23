{{--
    Team-member-facing Successful Matches — same data as
    AdminController::successfulMatches(), scoped to this team member's own
    (partner_a_id/partner_b_id). Read-only here — the collaboration-share
    split stays admin-editable only. See TeamController::successfulMatchesMine().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Successful Matches')
@section('main-content')
<style>
    .ur-sm-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
    .ur-sm-table th, .ur-sm-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #E7E2D6; font-size: 13.5px; }
    .ur-sm-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 18px;">Successful Matches</h1>

<table class="ur-sm-table">
    <thead>
        <tr><th>Proposal</th><th>Counterpart</th><th>Partner</th><th>Matched On</th><th>Share</th></tr>
    </thead>
    <tbody>
        @forelse($matches as $match)
        <tr>
            <td>{{ optional($match->proposal)->dataid }} ({{ optional($match->proposal)->first_name }})</td>
            <td>{{ optional($match->counterpartProposal)->dataid }} ({{ optional($match->counterpartProposal)->first_name }})</td>
            <td>{{ optional($match->partnerA)->first_name }} {{ optional($match->partnerA)->last_name }} &harr; {{ optional($match->partnerB)->first_name }} {{ optional($match->partnerB)->last_name }}</td>
            <td>{{ \Carbon\Carbon::parse($match->matched_at)->format('d/m/Y') }}</td>
            <td>{{ $match->share_partner_a !== null ? $match->share_partner_a.'% / '.$match->share_partner_b.'%' : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5">No successful matches yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:20px;">{{ $matches->links() }}</div>
@endsection
