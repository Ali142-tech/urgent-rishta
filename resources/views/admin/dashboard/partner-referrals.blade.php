{{--
    Admin ▸ Partner Referrals — clicks from UrgentRishta to the partner websites (Gallery ▸ Our Partners ▸ /go/{partner}).
    See AdminController::partnerReferrals(), PartnerRedirectController, config/partners.php.
    These are visits/clicks only — NOT purchases or commission (that needs the partner's own affiliate tracking, webhook or
    verified order reports).
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Partner Referrals')
@section('main-content')
<style>
    .ur-pr-note { background: #FBF0DA; border: 1px solid #F0DDB0; color: #6B4A12; border-radius: 12px; padding: 12px 16px; font-size: 13px; line-height: 1.55; margin-bottom: 18px; }
    .ur-pr-filter { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .ur-pr-filter label { display: block; font-size: 12px; font-weight: 700; color: #123A2E; margin: 0 0 5px; }
    .ur-pr-filter input, .ur-pr-filter select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; font-size: 13.5px; min-width: 150px; background: #fff; }
    .ur-pr-btn { height: 38px; padding: 0 18px; border: 0; border-radius: 8px; background: #123A2E; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; }
    .ur-pr-clear { display: inline-flex; align-items: center; height: 38px; padding: 0 14px; border: 1px solid #E7E2D6; border-radius: 8px; color: #5B6560; font-size: 13px; text-decoration: none; }
    .ur-pr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 16px; margin-bottom: 22px; }
    .ur-pr-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px 20px; }
    .ur-pr-card h3 { font-size: 15px; font-weight: 800; color: #123A2E; margin: 0 0 12px; }
    .ur-pr-big { font-size: 34px; font-weight: 800; color: #123A2E; line-height: 1; }
    .ur-pr-big small { display: block; font-size: 12px; font-weight: 600; color: #6B7570; margin-top: 6px; }
    .ur-pr-kv { display: grid; grid-template-columns: 1fr auto; gap: 6px 12px; margin-top: 14px; font-size: 13px; color: #4B5651; }
    .ur-pr-kv b { color: #1C2321; text-align: right; }
    .ur-pr-kv .sep { grid-column: 1 / -1; border-top: 1px dashed #E7E2D6; margin: 4px 0; }
    .ur-pr-table-wrap { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; overflow-x: auto; }
    .ur-pr-table { width: 100%; border-collapse: collapse; min-width: 640px; }
    .ur-pr-table th, .ur-pr-table td { padding: 11px 16px; text-align: left; border-bottom: 1px solid #F0ECE1; font-size: 13px; }
    .ur-pr-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
    .ur-pr-table tbody tr:last-child td { border-bottom: 0; }
    .ur-pr-tag { display: inline-block; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #F3EFE6; color: #4B5651; }
    .ur-pr-tag--user { background: #E7F3EC; color: #1C7A58; }
    .ur-pr-h2 { font-size: 15px; font-weight: 800; color: #123A2E; margin: 6px 0 12px; }
</style>

<div class="ur-pr-note">
    <b>Clicks, not sales.</b> This report counts visits from UrgentRishta to each partner's website. It does not track purchases or
    commission — that needs the partner's own affiliate tracking, a webhook or verified order reports.
    <b>Total clicks</b> counts every click; <b>unique users</b> counts each signed-in member once, and <b>unique visitors</b> counts
    each anonymous browser session once (approximate — no IP address or tracking cookie is stored).
</div>

<form method="GET" class="ur-pr-filter">
    <div><label for="pr_from">From</label><input type="date" id="pr_from" name="from" value="{{ $from->format('Y-m-d') }}"></div>
    <div><label for="pr_to">To</label><input type="date" id="pr_to" name="to" value="{{ $to->format('Y-m-d') }}"></div>
    <div>
        <label for="pr_partner">Partner</label>
        <select id="pr_partner" name="partner">
            <option value="">All partners</option>
            @foreach($partners as $key => $partner)
                <option value="{{ $key }}" {{ $partnerKey === $key ? 'selected' : '' }}>{{ $partner['name'] }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="ur-pr-btn">Apply</button>
    <a href="{{ route('admin.partner-referrals') }}" class="ur-pr-clear">Reset (last 30 days)</a>
</form>

<div class="ur-pr-h2">Overview</div>
<div class="ur-pr-grid">
    <div class="ur-pr-card">
        <h3>All shown partners</h3>
        <div class="ur-pr-big">{{ number_format($totals['total']) }}<small>total clicks, {{ $from->format('j M Y') }} – {{ $to->format('j M Y') }}</small></div>
        <div class="ur-pr-kv">
            <span>Signed-in members</span><b>{{ number_format($totals['logged_in']) }}</b>
            <span>Anonymous visitors</span><b>{{ number_format($totals['anonymous']) }}</b>
            <span class="sep"></span>
            <span>Today</span><b>{{ number_format($totals['today']) }}</b>
            <span>Last 7 days</span><b>{{ number_format($totals['d7']) }}</b>
            <span>Last 30 days</span><b>{{ number_format($totals['d30']) }}</b>
        </div>
    </div>
    @foreach($stats as $key => $st)
    <div class="ur-pr-card">
        <h3>{{ $st['name'] }}</h3>
        <div class="ur-pr-big">{{ number_format($st['total']) }}<small>total clicks in range</small></div>
        <div class="ur-pr-kv">
            <span>Unique signed-in users</span><b>{{ number_format($st['unique_users']) }}</b>
            <span>Signed-in clicks</span><b>{{ number_format($st['logged_in']) }}</b>
            <span>Anonymous clicks</span><b>{{ number_format($st['anonymous']) }}</b>
            <span>Unique anonymous visitors</span><b>{{ number_format($st['unique_visitors']) }}</b>
            <span class="sep"></span>
            <span>Today</span><b>{{ number_format($st['today']) }}</b>
            <span>Last 7 days</span><b>{{ number_format($st['d7']) }}</b>
            <span>Last 30 days</span><b>{{ number_format($st['d30']) }}</b>
        </div>
    </div>
    @endforeach
</div>

<div class="ur-pr-h2">Recent clicks <span style="font-weight:500; color:#6B7570; font-size:12.5px;">({{ number_format($clicks->total()) }} in range)</span></div>
<div class="ur-pr-table-wrap">
    <table class="ur-pr-table">
        <thead><tr><th>When (UTC)</th><th>Partner</th><th>Who</th><th>UTM</th></tr></thead>
        <tbody>
            @forelse($clicks as $click)
            <tr>
                <td>{{ $click->created_at->format('d M Y, H:i') }}</td>
                <td>{{ $click->partner_name }}</td>
                <td>
                    @if($click->user)
                        <a href="{{ url('admin/profile/preview/' . $click->user->dataid) }}" target="_blank" rel="noopener" title="Open this member's full profile" style="color:inherit; text-decoration:none;">
                            <span class="ur-pr-tag ur-pr-tag--user">Member</span> {{ $click->user->dataid }} &mdash; <span style="text-decoration:underline;">{{ trim($click->user->first_name . ' ' . $click->user->last_name) }}</span>
                        </a>
                    @elseif($click->teamMember)
                        <span class="ur-pr-tag ur-pr-tag--user">Team</span> {{ $click->teamMember->dataid }} &mdash; {{ trim($click->teamMember->first_name . ' ' . $click->teamMember->last_name) }}
                    @elseif($click->user_id || $click->team_member_id)
                        <span class="ur-pr-tag ur-pr-tag--user">Signed in</span> (account removed)
                    @else
                        <span class="ur-pr-tag">Visitor</span> {{ $click->visitor_hash ? substr($click->visitor_hash, 0, 6) : '—' }}
                    @endif
                </td>
                <td>{{ $click->utm_source }} / {{ $click->utm_medium }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:26px; text-align:center; color:#6B7570;">No partner clicks in this range yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- .ur-admin-panel carries the admin pagination reskin (green/gold); the reset keeps it from drawing a card around the links --}}
<div class="ur-admin-panel" style="margin-top:18px; padding:0; border:0; background:none; box-shadow:none;">{{ $clicks->links() }}</div>
@endsection