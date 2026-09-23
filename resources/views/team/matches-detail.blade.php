{{--
    Drill-down from team/matches.blade.php — every match for ONE proposal.
    See TeamController::matchesForProposal(). Redesigned (2026-09) to match
    the client's mockup: dark green header banner with the proposal's own
    Partner Requirements pills, a filter/sort toolbar, and compact portrait
    cards (rather than reusing member.partials.member-card.blade.php, whose
    layout is quite different) with inline "Why it's a match" bars.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'AI Matches')
@section('main-content')
<style>
    .ur-md-breadcrumb { font-size: 13px; color: #6B7570; margin-bottom: 14px; }
    .ur-md-breadcrumb a { color: #6B7570; text-decoration: none; }
    .ur-md-breadcrumb a:hover { color: #123A2E; }

    .ur-md-banner { background: #123A2E; border-radius: 16px; padding: 20px 22px; display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-bottom: 18px; color: #fff; }
    .ur-md-banner__photo { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid #C9974D; flex-shrink: 0; }
    .ur-md-banner__info { flex: 0 1 auto; }
    .ur-md-banner__label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; color: #C9974D; }
    .ur-md-banner__name { font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; margin: 2px 0; }
    .ur-md-banner__meta { font-size: 12.5px; color: rgba(255,255,255,.75); }
    .ur-md-banner__reqs { flex: 1 1 260px; }
    .ur-md-banner__reqs-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; color: rgba(255,255,255,.6); margin-bottom: 6px; }
    .ur-md-banner__pills { display: flex; gap: 8px; flex-wrap: wrap; }
    .ur-md-pill { background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.35); border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 700; white-space: nowrap; color: #fff !important; }
    .ur-md-edit-btn { background: #C9974D; color: #1C2321 !important; font-size: 12.5px; font-weight: 700; padding: 10px 18px; border-radius: 999px; text-decoration: none; white-space: nowrap; margin-left: auto; }
    .ur-md-edit-btn:hover { background: #B07C3D; }

    .ur-md-toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .ur-md-count { font-size: 14px; font-weight: 700; color: #123A2E; }
    .ur-md-tabs { display: inline-flex; background: #fff; border: 1px solid #E7E2D6; border-radius: 999px; padding: 3px; }
    .ur-md-tabs button { border: none; background: transparent; padding: 7px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; color: #6B7570; cursor: pointer; }
    .ur-md-tabs button.is-active { background: #123A2E; color: #fff; }
    .ur-md-sort { border: 1px solid #E7E2D6; border-radius: 999px; height: 36px; padding: 0 14px; font-size: 12.5px; font-weight: 600; color: #1C2321; background: #fff; }

    .ur-md-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; }
    .ur-md-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; }
    .ur-md-card__photo { position: relative; height: 190px; background-size: cover; background-position: center; background-color: #E7E2D6; }
    .ur-md-card__new { position: absolute; top: 10px; left: 10px; background: #C9974D; color: #1C2321; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
    .ur-md-card__score { position: absolute; top: 10px; right: 10px; width: 48px; height: 48px; border-radius: 50%; background: #123A2E; color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; line-height: 1.1; border: 2px solid #C9974D; }
    .ur-md-card__score small { font-size: 7px; font-weight: 700; letter-spacing: .04em; opacity: .8; }
    .ur-md-card__overlay { position: absolute; left: 0; right: 0; bottom: 0; padding: 28px 14px 10px; background: linear-gradient(to top, rgba(0,0,0,.75), rgba(0,0,0,0)); color: #fff; }
    .ur-md-card__name { font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; }
    .ur-md-card__meta { font-size: 12px; opacity: .9; }

    .ur-md-card__tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: #E7E2D6; }
    .ur-md-card__tiles div { background: #F6F4EF; padding: 8px 10px; }
    .ur-md-card__tiles span { display: block; font-size: 9.5px; font-weight: 700; letter-spacing: .04em; color: #9AA5A0; }
    .ur-md-card__tiles b { display: block; font-size: 12px; color: #1C2321; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .ur-md-card__why { padding: 12px 14px; flex: 1; }
    .ur-md-card__why-label { font-size: 10px; font-weight: 700; letter-spacing: .05em; color: #9AA5A0; margin-bottom: 8px; }
    .ur-md-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .ur-md-bar-label { font-size: 11.5px; color: #1C2321; width: 78px; flex-shrink: 0; }
    .ur-md-bar-track { flex: 1; height: 6px; border-radius: 4px; background: #E7E2D6; overflow: hidden; }
    .ur-md-bar-fill { display: block; height: 100%; }
    .ur-md-bar-fill--high { background: #2E7D5B; }
    .ur-md-bar-fill--mid { background: #C9974D; }
    .ur-md-bar-fill--low { background: #B5674A; }
    .ur-md-bar-pct { font-size: 11px; font-weight: 700; color: #1C2321; width: 32px; text-align: right; }
    .ur-md-explain { font-size: 12px; color: #6B7570; margin: 8px 0 0; padding-top: 8px; border-top: 1px dashed #E7E2D6; }

    .ur-md-card__actions { display: flex; gap: 8px; padding: 12px 14px; border-top: 1px solid #E7E2D6; }
    .ur-md-btn { flex: 1; text-align: center; border-radius: 999px; padding: 9px 0; font-size: 12.5px; font-weight: 700; text-decoration: none; border: 1px solid #E7E2D6; color: #123A2E; }
    .ur-md-btn:hover { background: #F6F4EF; }
    .ur-md-btn--gold { background: #C9974D; border-color: #C9974D; color: #1C2321; flex: 1; }
    .ur-md-btn--gold:hover { background: #B07C3D; }
    .ur-md-trophy-btn { flex: 0 0 auto; width: 38px; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #C9974D; cursor: pointer; }
    .ur-md-trophy-btn:hover { background: #FCF3E3; }
    .ur-md-trophy-btn--done { background: #E7F3EC; border-color: #2E7D5B; color: #2E7D5B; cursor: default; }
</style>

<div class="ur-md-breadcrumb">
    <a href="{{ route('team.matches') }}">AI Matches</a> / {{ $proposal->first_name }} {{ $proposal->last_name }}
</div>

@php
    $proposalProfile = $proposal->profile();
    $proposalAge = !empty($proposal->birthday) ? date_diff(date_create($proposal->birthday), date_create('now'))->y : null;
@endphp
<div class="ur-md-banner">
    <img class="ur-md-banner__photo" src="{{ $proposalProfile->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
    <div class="ur-md-banner__info">
        <div class="ur-md-banner__label">MATCHES FOR</div>
        <div class="ur-md-banner__name">{{ $proposal->first_name }} {{ $proposal->last_name }}</div>
        <div class="ur-md-banner__meta">ID {{ $proposal->dataid }} {{ $proposalAge ? '• '.$proposalAge.' yrs' : '' }} {{ $proposalProfile->lbl_city ? '• '.$proposalProfile->lbl_city : '' }} {{ $proposal->profession ? '• '.$proposal->profession : '' }}</div>
    </div>
    <div class="ur-md-banner__reqs">
        <div class="ur-md-banner__reqs-label">PARTNER REQUIREMENTS</div>
        <div class="ur-md-banner__pills">
            @forelse($requirementPills as $pill)
                <span class="ur-md-pill">{{ $pill }}</span>
            @empty
                <span class="ur-md-pill">Not set yet</span>
            @endforelse
        </div>
    </div>
    <a href="{{ route('team.proposals.edit', $proposal->dataid) }}" class="ur-md-edit-btn">Edit requirements</a>
</div>

@if($matches->isEmpty())
<div class="block block--style-3 list z-depth-1-top">
    <i>No matches found yet for this proposal's requirements.</i>
</div>
@else
<div class="ur-md-toolbar">
    <div class="ur-md-count" id="md_count">{{ count($matches) }} match{{ count($matches) == 1 ? '' : 'es' }} found</div>
    <div class="ur-md-tabs" id="md_tabs">
        <button type="button" data-filter="all" class="is-active">All</button>
        <button type="button" data-filter="interest">Interest sent</button>
        <button type="button" data-filter="successful">Successful</button>
    </div>
    <select class="ur-md-sort" id="md_sort">
        <option value="best">Best match first</option>
        <option value="newest">Newest first</option>
    </select>
</div>

<div class="ur-md-grid" id="md_grid">
    @foreach($matches as $match)
        @php
            $compat = $match->compatibilityWith($proposal->partnerPreference);
            $explanation = $compat ? \App\Profile::compatibilityExplanation($compat) : null;
            $matchAge = !empty($match->birthday) ? date_diff(date_create($match->birthday), date_create('now'))->y : null;
        @endphp
        <div class="ur-md-card" data-score="{{ $compat['percent'] ?? 0 }}" data-updated="{{ strtotime($match->updated_at ?? 'now') }}" data-interest="{{ $match->interest_sent ? 1 : 0 }}" data-successful="{{ $match->already_successful ? 1 : 0 }}">
            <div class="ur-md-card__photo" style="background-image:url('{{ $match->getProfileImage(true) }}');">
                @if(!empty($match->created_at) && $match->created_at->greaterThan(now()->subDays(7)))
                    <span class="ur-md-card__new">NEW</span>
                @endif
                @if($compat)
                    <span class="ur-md-card__score">{{ $compat['percent'] }}%<br><small>MATCH</small></span>
                @endif
                <div class="ur-md-card__overlay">
                    <div class="ur-md-card__name">{{ $match->first_name }}</div>
                    <div class="ur-md-card__meta">{{ $matchAge ? $matchAge.' yrs' : '' }} {{ $match->height ? '• '.$match->height : '' }} {{ $match->lbl_city ? '• '.$match->lbl_city : '' }}</div>
                </div>
            </div>
            <div class="ur-md-card__tiles">
                <div><span>PROFESSION</span><b>{{ $match->profession ?: '&mdash;' }}</b></div>
                <div><span>CASTE / SECT</span><b>{{ $match->lbl_caste ?: '&mdash;' }}</b></div>
                <div><span>MARITAL</span><b>{{ $match->lbl_marital_status ?: '&mdash;' }}</b></div>
            </div>
            @if($compat)
            <div class="ur-md-card__why">
                <div class="ur-md-card__why-label">WHY IT'S A MATCH</div>
                @foreach($compat['checks'] as $check)
                    @php $tier = $check['score'] >= 80 ? 'high' : ($check['score'] >= 45 ? 'mid' : 'low'); @endphp
                    <div class="ur-md-bar-row">
                        <span class="ur-md-bar-label">{{ $check['factor'] }}</span>
                        <span class="ur-md-bar-track"><span class="ur-md-bar-fill ur-md-bar-fill--{{ $tier }}" style="width:{{ $check['score'] }}%"></span></span>
                        <span class="ur-md-bar-pct">{{ $check['score'] }}%</span>
                    </div>
                @endforeach
                @if($explanation)<p class="ur-md-explain">{{ $explanation }}</p>@endif
            </div>
            @endif
            <div class="ur-md-card__actions">
                <a href="{{ url('member/profile/'.$match->dataid) }}" target="_blank" rel="noopener" class="ur-md-btn">View profile</a>
                <a href="{{ route('team.proposals.share.whatsapp', $match->dataid) }}" target="_blank" rel="noopener" class="ur-md-btn ur-md-btn--gold"><i class="fa fa-whatsapp"></i> Share</a>
                @if($match->already_successful)
                    <button type="button" class="ur-md-trophy-btn ur-md-trophy-btn--done" title="Already marked as a successful match" disabled><i class="fa fa-trophy"></i></button>
                @else
                    <form method="POST" action="{{ route('team.successful-matches.store') }}" onsubmit="return false;">
                        @csrf
                        <input type="hidden" name="proposal_dataid" value="{{ $proposal->dataid }}">
                        <input type="hidden" name="counterpart_dataid" value="{{ $match->dataid }}">
                        <button type="button" class="ur-md-trophy-btn" title="Mark as Successful Match" onclick="swalConfirm('Mark as Successful Match', 'Mark {{ $proposal->first_name }} and {{ $match->first_name }} as a successful match?', () => this.closest('form').submit());"><i class="fa fa-trophy"></i></button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>

<script>
    (function () {
        var grid = document.getElementById('md_grid');
        var tabs = document.getElementById('md_tabs');
        var sort = document.getElementById('md_sort');
        var activeFilter = 'all';

        function applyFilter() {
            var cards = grid.querySelectorAll('.ur-md-card');
            var visible = 0;
            cards.forEach(function (card) {
                var show = activeFilter === 'all'
                    || (activeFilter === 'interest' && card.dataset.interest === '1')
                    || (activeFilter === 'successful' && card.dataset.successful === '1');
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('md_count').textContent = visible + ' match' + (visible === 1 ? '' : 'es') + ' found';
        }

        tabs.addEventListener('click', function (e) {
            var btn = e.target.closest('button[data-filter]');
            if (!btn) return;
            tabs.querySelectorAll('button').forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            activeFilter = btn.dataset.filter;
            applyFilter();
        });

        sort.addEventListener('change', function () {
            var cards = Array.from(grid.querySelectorAll('.ur-md-card'));
            var mode = this.value;
            cards.sort(function (a, b) {
                if (mode === 'newest') return b.dataset.updated - a.dataset.updated;
                return b.dataset.score - a.dataset.score;
            });
            cards.forEach(function (c) { grid.appendChild(c); });
        });
    })();
</script>
@endif
@endsection
