{{--
    The Team Dashboard an ADMIN sees (team_members.is_admin): an overview of the
    whole partner team instead of one matchmaker's own clients. Ordinary team
    members keep team/overview.blade.php. See TeamAdminController::overview().
    Redesigned (Nov 2026) to match the Temporary Access page: hero banner, icon stat cards, avatar rows.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Team Overview')
@section('main-content')
@php
    $__tones = [['#123A2E', '#1E5A46'], ['#1F3A5F', '#2F5A8F'], ['#5B2C4A', '#8A3F6E'], ['#7A4E10', '#B07C3D'], ['#0F4C5C', '#1B7A8F'], ['#4A2C5B', '#6F4A86'], ['#7A2E2A', '#A8443E'], ['#2D4A3E', '#4F7F6B']];
    $__tone = fn ($seed) => $__tones[crc32((string) $seed) % count($__tones)];
    $__ini = function ($name) {
        $p = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        if (!$p) return '?';
        return strtoupper(mb_substr($p[0], 0, 1) . (count($p) > 1 ? mb_substr($p[count($p) - 1], 0, 1) : mb_substr($p[0], 1, 1)));
    };
@endphp
<style>
    .ur-ov { width: 100%; }
    .ur-ov-hero { position: relative; overflow: hidden; border-radius: 22px; padding: 28px 30px 26px; margin-bottom: 18px; background: linear-gradient(135deg, #123A2E 0%, #0F2E24 60%, #174536 100%); color: #F6F4EF; box-shadow: 0 12px 30px rgba(15, 46, 36, .18); }
    .ur-ov-hero::after { content: ''; position: absolute; right: -60px; top: -60px; width: 220px; height: 220px; border-radius: 50%; border: 2px solid rgba(201, 151, 77, .35); box-shadow: 0 0 0 26px rgba(201, 151, 77, .07), 0 0 0 54px rgba(201, 151, 77, .04); pointer-events: none; }
    .ur-ov-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #E2B66E; margin: 0 0 8px; }
    .ur-ov-hero h1 { font-family: 'Playfair Display', Georgia, serif; font-variant-numeric: lining-nums; font-size: 32px; font-weight: 700; color: #fff; margin: 0 0 8px; position: relative; z-index: 1; }
    .ur-ov-sub { color: #C9DAD2; font-size: 14px; margin: 0; max-width: 700px; line-height: 1.65; position: relative; z-index: 1; }

    .ur-ov-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .ur-ov-stat { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 16px 18px; text-decoration: none !important; transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease; }
    a.ur-ov-stat:hover { box-shadow: 0 10px 24px rgba(15, 46, 36, .1); transform: translateY(-2px); border-color: #C9974D; }
    .ur-ov-stat__ico { flex: none; width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 19px; }
    .ur-ov-stat__ico i { color: inherit !important; }
    .ur-ov-stat--members .ur-ov-stat__ico { background: #E3F4EA; color: #17734B; }
    .ur-ov-stat--apps .ur-ov-stat__ico { background: #FBF0DA; color: #B07C3D; }
    .ur-ov-stat--props .ur-ov-stat__ico { background: #EAF0FB; color: #2F4F8F; }
    .ur-ov-stat--week .ur-ov-stat__ico { background: #F1E8F6; color: #6B3F86; }
    .ur-ov-stat--match .ur-ov-stat__ico { background: #FDECEA; color: #A3281E; }
    .ur-ov-stat__num { display: block; font-family: 'Playfair Display', Georgia, serif; font-variant-numeric: lining-nums; font-size: 28px; font-weight: 700; line-height: 1; color: #123A2E; }
    .ur-ov-stat__lbl { display: block; margin-top: 5px; font-size: 12px; font-weight: 700; letter-spacing: .03em; color: #6B7570; }
    .ur-ov-stat__go { display: block; margin-top: 3px; font-size: 11.5px; font-weight: 700; color: #B07C3D; }

    .ur-ov-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 20px; padding: 22px 22px 18px; margin-bottom: 20px; box-shadow: 0 4px 14px rgba(15, 46, 36, .04); }
    .ur-ov-card__head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
    .ur-ov-card__title { display: flex; align-items: center; gap: 12px; }
    .ur-ov-card__ico { flex: none; width: 40px; height: 40px; border-radius: 12px; background: #FBF0DA; color: #B07C3D; display: flex; align-items: center; justify-content: center; }
    .ur-ov-card__ico i { color: inherit !important; }
    .ur-ov-card h2 { font-family: 'Playfair Display', Georgia, serif; font-variant-numeric: lining-nums; font-size: 19px; font-weight: 700; color: #123A2E; margin: 0; padding: 0; border: 0; }
    .ur-ov-card p.hint { margin: 2px 0 0; font-size: 13px; color: #6B7570; }
    .ur-ov-link { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 14px; border-radius: 999px; border: 1.5px solid #D9C79B; color: #123A2E !important; font-size: 12.5px; font-weight: 700; text-decoration: none !important; background: #fff; }
    .ur-ov-link:hover { background: #FBF0DA; }
    .ur-ov-link i { color: inherit !important; }

    .ur-ov-head, .ur-ov-row { display: grid; gap: 14px; align-items: center; }
    .ur-ov-head { padding: 0 16px 8px; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #9AA5A0; }
    .ur-ov-list { display: grid; gap: 10px; }
    .ur-ov-row { padding: 13px 16px; border: 1px solid #EFEADD; border-radius: 16px; background: #fff; transition: box-shadow .2s ease, border-color .2s ease; }
    .ur-ov-row:hover { border-color: #D9C79B; box-shadow: 0 8px 20px rgba(15, 46, 36, .07); }
    .ur-ov-members .ur-ov-head, .ur-ov-members .ur-ov-row { grid-template-columns: minmax(0, 2.4fr) minmax(0, 1.3fr) 80px 70px 80px minmax(0, 1.1fr); }
    .ur-ov-props .ur-ov-head, .ur-ov-props .ur-ov-row { grid-template-columns: 90px minmax(0, 2.2fr) minmax(0, 1.1fr) minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1fr) 84px; }

    .ur-ov-who { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ur-ov-av { flex: none; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #123A2E, #1E5A46); color: #fff; font-family: Poppins, Arial, sans-serif; font-weight: 700; font-size: 16px; letter-spacing: .5px; box-shadow: 0 0 0 3px #fff, 0 0 0 4px rgba(15, 46, 36, .15); }
    .ur-ov-av--sm { width: 30px; height: 30px; font-size: 11px; box-shadow: none; }
    .ur-ov-row--person { border-left: 4px solid var(--tone, #123A2E); }
    .ur-ov-name { font-weight: 700; color: #0F2E24; font-size: 15.5px; line-height: 1.25; }
    .ur-ov-id { display: inline-block; margin-right: 6px; padding: 1px 8px; border-radius: 6px; background: #F1EEE6; color: #123A2E; font-size: 11.5px; font-weight: 700; letter-spacing: .04em; }
    .ur-ov-meta { font-size: 12.5px; color: #6B7570; margin-top: 2px; overflow-wrap: anywhere; }
    .ur-ov-num { font-family: 'Playfair Display', Georgia, serif; font-variant-numeric: lining-nums; font-size: 19px; font-weight: 700; color: #123A2E; }
    .ur-ov-num small { display: none; }
    .ur-ov-time { font-size: 12.5px; color: #1C2321; }
    .ur-ov-ref { font-family: 'Playfair Display', Georgia, serif; font-variant-numeric: lining-nums; font-weight: 700; font-size: 16px; color: #123A2E; }
    .ur-ov-owner { display: flex; align-items: center; gap: 9px; min-width: 0; font-size: 13px; color: #1C2321; }
    .ur-ov-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 5px 12px; font-size: 11.5px; font-weight: 700; background: #E3F4EA; color: #17734B; white-space: nowrap; }
    .ur-ov-pill--warn { background: #FCF3E3; color: #8A5A12; }
    .ur-ov-pill--admin { background: #FBF0DA; color: #8A6218; padding: 2px 9px; font-size: 10.5px; margin-left: 4px; }
    .ur-ov-change { margin-left: 6px; font-size: 11.5px; font-weight: 700; color: #B07C3D !important; }
    .ur-ov-view { display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 14px; border-radius: 999px; background: #123A2E; color: #fff !important; font-size: 12px; font-weight: 700; text-decoration: none !important; }
    .ur-ov-view:hover { background: #0D2C23; }
    .ur-ov-empty { text-align: center; padding: 34px 16px; color: #6B7570; font-size: 14px; border: 1px dashed #DDD5C2; border-radius: 16px; background: #FDFBF6; }

    .ur-ov-foot { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
    .ur-ov-count { font-size: 13px; color: #6B7570; }
    .ur-ov-count b { color: #123A2E; }
    .ur-ov-pager nav { overflow-x: auto; }
    .ur-ov-pager .pagination { margin: 0; display: flex; flex-wrap: wrap; gap: 6px; padding: 0; list-style: none; }
    .ur-ov-pager .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px !important; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-size: 13px; font-weight: 700; box-shadow: none; }
    .ur-ov-pager .page-item .page-link:hover { background: #FBF0DA; border-color: #C9974D; color: #123A2E; }
    .ur-ov-pager .page-item.active .page-link { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-ov-pager .page-item.disabled .page-link { background: #F6F4EF; color: #B8B2A2; }

    .ur-ov-p-ico { display: none; }
    @media (max-width: 1100px) {
        .ur-ov-head { display: none; }
        /* members: name + status on top, the three counts in a tinted band, last-proposal line underneath */
        .ur-ov-members .ur-ov-row { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px 8px; }
        .ur-ov-members .ur-ov-who, .ur-ov-members .ur-ov-status, .ur-ov-members .ur-ov-last { grid-column: 1 / -1; }
        .ur-ov-members .ur-ov-num { background: #FBF7EF; border: 1px solid #EFEADD; border-radius: 12px; padding: 9px 4px; text-align: center; }
        .ur-ov-num small { display: block; font-family: Poppins, Arial, sans-serif; font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #9AA5A0; margin-top: 3px; }
        .ur-ov-members .ur-ov-last { font-size: 12px; color: #6B7570; padding-top: 2px; }
        .ur-ov-members .ur-ov-last::before { content: attr(data-label) ': '; font-weight: 700; color: #123A2E; }
        /* proposals: ref + View on one line, then client, city, owner, status + time */
        .ur-ov-props .ur-ov-row { grid-template-columns: minmax(0, 1fr) auto; gap: 8px 10px; grid-template-areas: "ref view" "client client" "city city" "owner owner" "status time"; }
        .ur-ov-p-ref { grid-area: ref; font-size: 18px; }
        .ur-ov-p-view { grid-area: view; }
        .ur-ov-props .ur-ov-client { grid-area: client; }
        .ur-ov-p-city { grid-area: city; color: #6B7570; }
        .ur-ov-p-ico { display: inline-block; color: #C9974D; margin-right: 6px; }
        .ur-ov-props .ur-ov-owner { grid-area: owner; }
        .ur-ov-p-status { grid-area: status; }
        .ur-ov-p-time { grid-area: time; text-align: right; color: #6B7570; }
        .ur-ov-props .ur-ov-row { padding: 14px 15px; }
    }
    @media (max-width: 560px) {
        .ur-ov-hero { padding: 22px 20px; border-radius: 18px; }
        .ur-ov-hero h1 { font-size: 26px; }
        .ur-ov-card { padding: 18px 14px 14px; }
        .ur-ov-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ur-ov-stats > :last-child:nth-child(odd) { grid-column: 1 / -1; }
        .ur-ov-stat { padding: 13px 14px; gap: 11px; }
        .ur-ov-stat__ico { width: 38px; height: 38px; font-size: 16px; }
        .ur-ov-stat__num { font-size: 22px; }
        .ur-ov-foot { justify-content: center; }
    }
</style>

<div class="ur-ov">
    <div class="ur-ov-hero">
        <div class="ur-ov-eyebrow">Admin &middot; Team</div>
        <h1>Team Overview</h1>
        <p class="ur-ov-sub">How the partner team is doing &mdash; members, proposals and matches in one place.</p>
    </div>

    <div class="ur-ov-stats">
        <a class="ur-ov-stat ur-ov-stat--members" href="{{ route('team.manage.members') }}">
            <span class="ur-ov-stat__ico"><i class="fa fa-users"></i></span>
            <span><span class="ur-ov-stat__num">{{ $stats['active_members'] }}</span><span class="ur-ov-stat__lbl">Active team members</span><span class="ur-ov-stat__go">Manage &rarr;</span></span>
        </a>
        <a class="ur-ov-stat ur-ov-stat--apps" href="{{ route('team.manage.applications') }}">
            <span class="ur-ov-stat__ico"><i class="fa fa-user-plus"></i></span>
            <span><span class="ur-ov-stat__num">{{ $stats['pending_applications'] }}</span><span class="ur-ov-stat__lbl">Pending applications</span><span class="ur-ov-stat__go">Review &rarr;</span></span>
        </a>
        <a class="ur-ov-stat ur-ov-stat--props" href="{{ route('team.manage.proposals') }}">
            <span class="ur-ov-stat__ico"><i class="fa fa-clipboard"></i></span>
            <span><span class="ur-ov-stat__num">{{ $stats['proposals_total'] }}</span><span class="ur-ov-stat__lbl">Proposals ({{ $stats['proposals_active'] }} active)</span><span class="ur-ov-stat__go">View all &rarr;</span></span>
        </a>
        <div class="ur-ov-stat ur-ov-stat--week">
            <span class="ur-ov-stat__ico"><i class="fa fa-line-chart"></i></span>
            <span><span class="ur-ov-stat__num">{{ $stats['proposals_week'] }}</span><span class="ur-ov-stat__lbl">Added in the last 7 days</span></span>
        </div>
        <a class="ur-ov-stat ur-ov-stat--match" href="{{ route('team.manage.successful-matches') }}">
            <span class="ur-ov-stat__ico"><i class="fa fa-heart"></i></span>
            <span><span class="ur-ov-stat__num">{{ $stats['matches_total'] }}</span><span class="ur-ov-stat__lbl">Successful matches</span><span class="ur-ov-stat__go">View &rarr;</span></span>
        </a>
    </div>

    <div class="ur-ov-card ur-ov-members">
        <div class="ur-ov-card__head">
            <div class="ur-ov-card__title">
                <span class="ur-ov-card__ico"><i class="fa fa-users"></i></span>
                <div>
                    <h2>Team members</h2>
                    <p class="hint">{{ $members->total() }} approved member{{ $members->total() === 1 ? '' : 's' }}</p>
                </div>
            </div>
            <a class="ur-ov-link" href="{{ route('team.manage.members') }}"><i class="fa fa-cog"></i> Manage members</a>
        </div>
        @if($members->isEmpty())
            <div class="ur-ov-empty"><i class="fa fa-users" style="font-size:28px; color:#C9974D; display:block; margin-bottom:8px;"></i>No approved team members yet.</div>
        @else
            <div class="ur-ov-head"><span>Member</span><span>Status</span><span>Proposals</span><span>Active</span><span>Matches</span><span>Last proposal</span></div>
            <div class="ur-ov-list">
                @foreach($members as $member)
                    @php $full = trim($member->first_name . ' ' . $member->last_name); [$c1, $c2] = $__tone($member->dataid ?: $full); @endphp
                    <div class="ur-ov-row ur-ov-row--person" style="--tone: {{ $c1 }};">
                        <div class="ur-ov-who">
                            <span class="ur-ov-av" style="background: linear-gradient(135deg, {{ $c1 }}, {{ $c2 }});">{{ $__ini($full) }}</span>
                            <div style="min-width:0;">
                                <div class="ur-ov-name">{{ $full }}@if($member->isPremium()) @include('team.partials.premium-badge') @endif @if($member->is_admin)<span class="ur-ov-pill ur-ov-pill--admin">Admin</span>@endif</div>
                                <div class="ur-ov-meta"><span class="ur-ov-id">{{ $member->dataid }}</span>@if($member->experience) <span>{{ $member->experience }}</span>@endif</div>
                            </div>
                        </div>
                        <div class="ur-ov-status">
                            <span class="ur-ov-pill {{ $member->status === 'active' ? '' : 'ur-ov-pill--warn' }}"><i class="fa fa-circle" style="font-size:7px;"></i> {{ ucfirst($member->status) }}</span>
                            <a class="ur-ov-change" href="{{ route('team.manage.members') }}">change</a>
                        </div>
                        <div class="ur-ov-num">{{ $member->proposals_count }}<small>Proposals</small></div>
                        <div class="ur-ov-num">{{ $member->active_proposals_count }}<small>Active</small></div>
                        <div class="ur-ov-num">{{ $member->matches_count }}<small>Matches</small></div>
                        <div class="ur-ov-time ur-ov-last" data-label="Last proposal">{{ $member->last_proposal_at ? \Carbon\Carbon::parse($member->last_proposal_at)->diffForHumans() : '—' }}</div>
                    </div>
                @endforeach
            </div>
            <div class="ur-ov-foot">
                <div class="ur-ov-count">Showing <b>{{ $members->firstItem() }}&ndash;{{ $members->lastItem() }}</b> of <b>{{ $members->total() }}</b> members</div>
                @if($members->hasPages())
                    <div class="ur-ov-pager">{{ $members->links() }}</div>
                @endif
            </div>
        @endif
    </div>

    <div class="ur-ov-card ur-ov-props">
        <div class="ur-ov-card__head">
            <div class="ur-ov-card__title">
                <span class="ur-ov-card__ico"><i class="fa fa-clipboard"></i></span>
                <div>
                    <h2>Latest proposals</h2>
                    <p class="hint">The 8 most recently added</p>
                </div>
            </div>
            <a class="ur-ov-link" href="{{ route('team.manage.proposals') }}"><i class="fa fa-list"></i> All proposals</a>
        </div>
        @if($recent->isEmpty())
            <div class="ur-ov-empty"><i class="fa fa-clipboard" style="font-size:28px; color:#C9974D; display:block; margin-bottom:8px;"></i>No proposals have been added yet.</div>
        @else
            <div class="ur-ov-head"><span>Ref</span><span>Client</span><span>City</span><span>Added by</span><span>Status</span><span>Added</span><span></span></div>
            <div class="ur-ov-list">
                @foreach($recent as $proposal)
                    @php $ownerName = $proposal->owner ? trim($proposal->owner->first_name . ' ' . $proposal->owner->last_name) : 'Unknown'; [$o1, $o2] = $__tone($ownerName); @endphp
                    <div class="ur-ov-row">
                        <div class="ur-ov-ref ur-ov-p-ref">{{ $proposal->reference }}</div>
                        <div class="ur-ov-client">
                            <div class="ur-ov-name" style="font-size:13.5px;">{{ ucfirst($proposal->gender) }}{{ $proposal->birthday ? ', ' . \Carbon\Carbon::parse($proposal->birthday)->age : '' }}</div>
                            @if($proposal->profession)<div class="ur-ov-meta">{{ $proposal->profession }}</div>@endif
                        </div>
                        <div class="ur-ov-time ur-ov-p-city"><i class="fa fa-map-marker ur-ov-p-ico"></i>{{ $proposal->current_city ?: $proposal->city ?: '—' }}</div>
                        <div class="ur-ov-owner"><span class="ur-ov-av ur-ov-av--sm" style="background: linear-gradient(135deg, {{ $o1 }}, {{ $o2 }});">{{ $__ini($ownerName) }}</span><span style="min-width:0; overflow-wrap:anywhere;">{{ $ownerName }}</span></div>
                        <div class="ur-ov-p-status"><span class="ur-ov-pill">{{ ucfirst(str_replace('_', ' ', $proposal->profile_status)) }}</span></div>
                        <div class="ur-ov-time ur-ov-p-time">{{ $proposal->created_at->diffForHumans() }}</div>
                        <div class="ur-ov-p-view"><a class="ur-ov-view" href="{{ route('team.proposals.view', $proposal->reference) }}">View</a></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
