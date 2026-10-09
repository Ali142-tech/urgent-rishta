{{--
    "My Matches" — every client THIS team member owns, each paired with its single best AI match, shown as a grid of
    match cards (photo, compatibility ring, details, WhatsApp + View profile). Search and paging run in the browser
    over the cards already on the page. See TeamController::myMatches(); the AI Match Center (team.matches) is the
    page with the controls. Uses the same classification-badges partial, Complete Client File modal (openClientFile())
    and team.matches.forward route ("Forward both" on WhatsApp) as AI Match Center.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Matches')
@section('main-content')
<style>
    .ur-mm-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }
    .ur-mm-head h1 { font-family: 'Manrope', system-ui, sans-serif; color: #123A2E; font-weight: 800; font-size: 26px; line-height: 1.2 !important; margin: 0 0 6px; }
    .ur-mm-head p { color: #2E7D5B; font-size: 14px; line-height: 1.6 !important; margin: 0; max-width: 640px; }
    .ur-mm-search { flex: 0 1 420px; position: relative; }
    .ur-mm-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #7B8781; font-size: 14px; }
    .ur-mm-search input { width: 100%; height: 44px; border: 1px solid #E7E2D6; border-radius: 12px; background: #fff; padding: 0 14px 0 38px; font-size: 13.5px; outline: none; }
    .ur-mm-search input:focus { border-color: #1F6B52; }

    .ur-mm-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 20px 0 24px; }
    .ur-mm-stat { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 16px 18px; }
    .ur-mm-stat:first-child { background: #E7F3EC; border-color: #D3E8DC; }
    .ur-mm-stat__icon { width: 46px; height: 46px; border-radius: 50%; background: #F3EFE6; color: #123A2E; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
    .ur-mm-stat:first-child .ur-mm-stat__icon { background: #1C6B52; color: #fff; }
    .ur-mm-stat span { display: block; font-size: 12.5px; color: #4B5651; line-height: 1.3 !important; margin: 0 0 6px; }
    .ur-mm-stat b { display: block; font-family: 'Manrope', system-ui, sans-serif; font-size: 28px; font-weight: 800; line-height: 1 !important; color: #123A2E; }

    .ur-mm-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; align-items: stretch; }
    .ur-mm-card { min-width: 0; display: flex; flex-direction: column; background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 16px; box-shadow: 0 1px 3px rgba(15,46,36,.05); }
    .ur-mm-card[hidden] { display: none; }
    .ur-mm-top { min-height: 112px; display: flex; gap: 14px; align-items: flex-start; }
    .ur-mm-photo { width: 92px; height: 112px; border-radius: 12px; object-fit: cover; background: #F3EFE6; flex-shrink: 0; display: block; }
    .ur-mm-info { flex: 1; min-width: 0; }
    .ur-mm-matchrow { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .ur-mm-ring { --p: 0; width: 44px; height: 44px; border-radius: 50%; background: conic-gradient(#1C9A6C calc(var(--p) * 1%), #E3EEE8 0); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-mm-ring b { width: 34px; height: 34px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-family: 'Manrope', system-ui, sans-serif; font-size: 11px; font-weight: 800; color: #123A2E; line-height: 1 !important; }
    .ur-mm-matchrow span { font-size: 13px; font-weight: 700; color: #123A2E; }
    .ur-mm-id { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; color: #123A2E; font-size: 17px; line-height: 1.25 !important; margin: 0 0 6px; }
    .ur-mm-line { font-size: 12.5px; color: #4B5651; line-height: 1.45 !important; margin: 0 0 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-mm-line i { width: 16px; color: #7B8781; }
    .ur-mm-tags { margin: 12px 0 0; flex: 1 1 auto; }
    .ur-badge-pill { display: inline-block; font-size: 10.5px; font-weight: 700; line-height: 1.2 !important; padding: 4px 10px; border-radius: 999px; margin: 0 6px 6px 0; border: 1px solid transparent; }
    .ur-badge-pill--premium { background: #FBF0DA; color: #8A6218; border-color: #F0DDB0; }
    .ur-badge-pill--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-badge-pill--abroad { background: #EAF2FB; color: #2C5F8A; border-color: #C9DDF0; }
    .ur-badge-pill--highlight { background: #FBE4E9; color: #A23B57; border-color: #F3C6D1; }
    .ur-badge-pill--client, .ur-badge-pill--client:hover, .ur-badge-pill--client:focus { background: #EEF0FB !important; color: #4A4FA3 !important; border-color: #D9DCF3; cursor: pointer; text-decoration: none; }
    .ur-badge-pill--client i { font-size: 9px; margin-left: 3px; }
    .ur-badge-pill--owner { background: #E7F3EC; color: #2E7D5B; border-color: #D3E8DC; }

    .ur-mm-owner { display: flex; align-items: center; gap: 8px; margin: 12px 0 14px; padding-top: 12px; border-top: 1px solid #F0ECE1; font-size: 12px; color: #6B7570; }
    .ur-mm-owner i { color: #7B8781; }
    .ur-mm-owner .ur-badge-pill--owner { margin: 0 0 0 auto; }
    .ur-mm-owner b { color: #1C2321; font-weight: 700; }

    .ur-mm-actions { display: flex; gap: 10px; }
    .ur-mm-actions a { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 7px; height: 42px; padding: 0 10px; border-radius: 10px; font-size: 12.5px; font-weight: 700; line-height: 1 !important; cursor: pointer; text-decoration: none; white-space: nowrap; }
    .ur-mm-actions a.is-whatsapp { background: #0F5B43; border: 1px solid #0F5B43; color: #fff; }
    .ur-mm-actions a.is-whatsapp:hover { background: #0B4734; color: #fff; }
    .ur-mm-actions a.is-view { background: #fff; border: 1px solid #1C6B52; color: #1C6B52; }
    .ur-mm-actions a.is-view:hover { background: #F2F8F5; }

    .ur-mm-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
    .ur-mm-foot > span { font-size: 13px; color: #2E7D5B; }
    .ur-mm-pager { display: flex; gap: 8px; }
    .ur-mm-pager button { min-width: 38px; height: 38px; border-radius: 10px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-weight: 700; font-size: 13px; cursor: pointer; }
    .ur-mm-pager button.is-active { background: #0F5B43; border-color: #0F5B43; color: #fff; }
    .ur-mm-pager button:disabled { opacity: .4; cursor: default; }
    .ur-mm-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 34px; text-align: center; color: #6B7570; }

    @media (max-width: 1180px) { .ur-mm-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .ur-mm-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 700px) { .ur-mm-grid { grid-template-columns: minmax(0, 1fr); } .ur-mm-search { flex: 1 1 100%; } .ur-mm-head h1 { font-size: 22px; } }
    @media (max-width: 480px) {
        .ur-mm-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .ur-mm-stat { flex-direction: column; align-items: flex-start; gap: 8px; padding: 12px; }
        .ur-mm-stat__icon { width: 36px; height: 36px; font-size: 15px; }
        .ur-mm-stat b { font-size: 22px; }
        .ur-mm-card { padding: 12px; }
        .ur-mm-photo { width: 76px; height: 96px; }
        .ur-mm-top { gap: 10px; min-height: 0; }
        .ur-mm-line { white-space: normal; }
        .ur-mm-owner { flex-wrap: wrap; row-gap: 6px; }
        .ur-mm-owner .ur-badge-pill--owner { margin-left: 0; }
        .ur-mm-actions { flex-direction: column; }
        .ur-mm-actions a { flex: 0 0 auto; width: 100%; height: 44px; box-sizing: border-box; }
        .ur-mm-foot { justify-content: center; }
    }
</style>

@php
    $viewerName = trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) ?: auth()->user()->dataid;
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
    $highCount = $pairs->filter(fn ($p) => ($p['compat']['percent'] ?? 0) >= 85)->count();
@endphp

<div class="ur-mm-head">
    <div>
        <h1>My Matches</h1>
        <p>{{ $viewerName }} &mdash; every AI match for your clients, best first. WhatsApp sends both forms to the match's owner.</p>
    </div>
    <div class="ur-mm-search">
        <i class="fa fa-search"></i>
        <input type="text" id="mm_search" placeholder="Search by profile ID, city, profession or owner…" autocomplete="off">
    </div>
</div>

<div class="ur-mm-stats">
    <div class="ur-mm-stat"><div class="ur-mm-stat__icon"><i class="fa fa-heart"></i></div><div><span>Matches ready</span><b>{{ $bestMatchesReady }}</b></div></div>
    <div class="ur-mm-stat"><div class="ur-mm-stat__icon"><i class="fa fa-star-o"></i></div><div><span>High compatibility (85%+)</span><b>{{ $highCount }}</b></div></div>
    <div class="ur-mm-stat"><div class="ur-mm-stat__icon"><i class="fa fa-users"></i></div><div><span>Your active clients</span><b>{{ $activeClientProfiles }}</b></div></div>
    <div class="ur-mm-stat"><div class="ur-mm-stat__icon"><i class="fa fa-line-chart"></i></div><div><span>Highest compatibility</span><b>{{ $highestCompatibility }}%</b></div></div>
</div>

@if($pairs->isEmpty())
<div class="ur-mm-empty">
    No matches ready yet — add Partner Requirements on a client (under "My Clients" &rarr; Edit) to start finding AI matches for them.
</div>
@else
<div class="ur-mm-grid" id="mm_grid">
    @foreach($pairs as $i => $pair)
        @php
            $client = $pair['client'];
            $match = $pair['match'];
            $pct = (int) ($pair['compat']['percent'] ?? 0);
            $matchAge = $ageOf($match);
            $casteText = $match->lbl_caste ? $match->lbl_caste . (!empty($match->sect) ? ' · ' . $match->sect : '') : null;
            $place = $match->lbl_city ?: $match->lbl_con_of_residence;
            $searchText = strtolower(implode(' ', array_filter([$match->dataid, $client->dataid, $match->profession, $casteText, $place, $match->lbl_con_of_residence, $pair['ownerName']])));
        @endphp
        <div class="ur-mm-card" data-search="{{ $searchText }}">
            <div class="ur-mm-top">
                <img class="ur-mm-photo" src="{{ $match->getProfileImage() }}" alt="{{ $match->dataid }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender) }}';">
                <div class="ur-mm-info">
                    <div class="ur-mm-matchrow">
                        <div class="ur-mm-ring" style="--p: {{ $pct }}"><b>{{ $pct }}%</b></div>
                        <span>Match</span>
                    </div>
                    <div class="ur-mm-id">{{ $match->dataid }}</div>
                    <div class="ur-mm-line"><i class="fa fa-user-o"></i>{{ ucfirst($match->gender) }}@if($matchAge) &bull; {{ $matchAge }} Years @endif @if(!empty($match->height)) &bull; {{ $match->height }} @endif</div>
                    @if(!empty($match->profession))<div class="ur-mm-line"><i class="fa fa-briefcase"></i>{{ $match->profession }}</div>@endif
                    @if($casteText)<div class="ur-mm-line"><i class="fa fa-users"></i>{{ $casteText }}</div>@endif
                    @if($place)<div class="ur-mm-line"><i class="fa fa-map-marker"></i>{{ $place }}@if($match->lbl_con_of_residence && $place !== $match->lbl_con_of_residence), {{ $match->lbl_con_of_residence }}@endif</div>@endif
                </div>
            </div>

            <div class="ur-mm-tags">
                <a class="ur-badge-pill ur-badge-pill--client" onclick="openClientFile('{{ $client->dataid }}')" title="Open your proposal {{ $client->dataid }}">For client {{ $client->dataid }} <i class="fa fa-external-link"></i></a>
                @include('team.partials.classification-badges', ['profile' => $match])
            </div>

            <div class="ur-mm-owner">
                <i class="fa fa-user-circle"></i> Owner: <b>{{ $pair['ownerName'] }}</b>@if(!empty($pair['ownerPremium'])) @include('team.partials.premium-badge') @endif
                @if($pair['ownerVerified'])<span class="ur-badge-pill ur-badge-pill--owner">Verified</span>@endif
            </div>

            <div class="ur-mm-actions">
                <a class="is-whatsapp js-share-proposal" data-payload-url="{{ route('team.matches.forward.payload', [$client->dataid, $match->dataid]) }}" href="{{ route('team.matches.forward', [$client->dataid, $match->dataid]) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> WhatsApp Message</a>
                <a class="is-view" onclick="openClientFile('{{ $match->dataid }}')"><i class="fa fa-eye"></i> View Profile</a>
            </div>
        </div>
    @endforeach
</div>

<p class="ur-mm-empty" id="mm_none" style="display:none;margin-top:16px;">No matches fit that search.</p>

<div class="ur-mm-foot">
    <span id="mm_count"></span>
    <div class="ur-mm-pager" id="mm_pager"></div>
</div>

<script>
(function () {
    var PER_PAGE = 6;
    var cards = Array.prototype.slice.call(document.querySelectorAll('#mm_grid .ur-mm-card'));
    var input = document.getElementById('mm_search');
    var pager = document.getElementById('mm_pager');
    var count = document.getElementById('mm_count');
    var none = document.getElementById('mm_none');
    var page = 1;

    function render() {
        var q = input.value.trim().toLowerCase();
        var hits = cards.filter(function (c) { return !q || c.getAttribute('data-search').indexOf(q) !== -1; });
        var pages = Math.max(1, Math.ceil(hits.length / PER_PAGE));
        if (page > pages) page = pages;
        var from = (page - 1) * PER_PAGE;
        cards.forEach(function (c) { c.hidden = true; });
        hits.slice(from, from + PER_PAGE).forEach(function (c) { c.hidden = false; });
        none.style.display = hits.length ? 'none' : '';
        count.textContent = hits.length ? 'Showing ' + (from + 1) + ' - ' + Math.min(from + PER_PAGE, hits.length) + ' of ' + hits.length + ' matches' : '';

        pager.innerHTML = '';
        if (pages < 2) return;
        function btn(label, target, opts) {
            var b = document.createElement('button');
            b.type = 'button'; b.innerHTML = label;
            if (opts && opts.active) b.className = 'is-active';
            if (opts && opts.disabled) b.disabled = true;
            b.addEventListener('click', function () { page = target; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
            pager.appendChild(b);
        }
        btn('&lsaquo;', page - 1, { disabled: page === 1 });
        for (var p = 1; p <= pages; p++) btn(p, p, { active: p === page });
        btn('&rsaquo;', page + 1, { disabled: page === pages });
    }

    input.addEventListener('input', function () { page = 1; render(); });
    render();
})();
</script>
@endif
@endsection
