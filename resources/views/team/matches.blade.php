{{--
    "AI Match Center" (client mockup, Sep 2026) — pick any team proposal
    with Partner Requirements set, optionally narrow by score/location, and
    review every compatible candidate with a per-factor "why" breakdown.
    See TeamController::matches()/matchesForProposal()/buildMatchCenterData()
    — both routes render this same view, just pre-selecting a different
    proposal. Replaces the old two-step "list of proposals, then drill
    down" flow (this used to be two different views).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'AI Match')
@section('main-content')
<style>
    .ur-am-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; flex-wrap: wrap; margin-bottom: 20px; }
    .ur-am-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .1em; color: #C9974D; }
    .ur-am-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 26px; margin: 4px 0 6px; }
    .ur-am-head p { color: #6B7570; font-size: 13.5px; margin: 0; max-width: 520px; }
    .ur-am-privacy { display: flex; align-items: flex-start; gap: 8px; background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 12px 16px; font-size: 12px; color: #6B7570; max-width: min(300px, 100%); box-sizing: border-box; }
    .ur-am-privacy i { color: #123A2E; margin-top: 2px; }

    .ur-am-top { display: grid; grid-template-columns: minmax(260px, 340px) 1fr; gap: 18px; margin-bottom: 22px; align-items: stretch; }

    /* ---- Matching For ---- */
    .ur-am-matching-for { background: #123A2E; border-radius: 16px; padding: 20px; color: #fff; }
    .ur-am-matching-for__label { font-size: 10.5px; font-weight: 700; letter-spacing: .08em; color: #C9974D; margin-bottom: 12px; }
    .ur-am-matching-for__head { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .ur-am-matching-for__photo { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 2px solid #C9974D; flex-shrink: 0; }
    .ur-am-matching-for__id { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 17px; }
    .ur-am-matching-for__meta { font-size: 12px; color: rgba(255,255,255,.7); }
    .ur-badge-pill { display: inline-block; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin: 0 6px 6px 0; }
    .ur-badge-pill--premium { background: rgba(201,151,77,.25); color: #F0D9AE; }
    .ur-badge-pill--royal { background: rgba(155,89,182,.35); color: #E6D3F0; }
    .ur-badge-pill--abroad { background: rgba(255,255,255,.16); color: #fff; }
    .ur-badge-pill--highlight { background: rgba(178,60,90,.3); color: #F3C7D2; }
    .ur-am-matching-for__tiles { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 14px 0; }
    .ur-am-matching-for__tiles div { background: rgba(255,255,255,.08); border-radius: 10px; padding: 9px 12px; }
    .ur-am-matching-for__tiles span { display: block; font-size: 9.5px; font-weight: 700; letter-spacing: .04em; color: rgba(255,255,255,.55); }
    .ur-am-matching-for__tiles b { display: block; font-size: 12.5px; margin-top: 2px; }
    .ur-am-matching-for__edit { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #C9974D; text-decoration: none; }
    .ur-am-matching-for__edit:hover { color: #fff; }

    /* ---- Match controls ---- */
    .ur-am-controls { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 20px; }
    .ur-am-controls__head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px; }
    .ur-am-controls__head h2 { font-size: 16px; font-weight: 700; color: #123A2E; margin: 0; }
    .ur-am-ready-pill { background: #E7F3EC; color: #123A2E; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
    .ur-am-controls > p, .ur-am-controls__head + p { color: #6B7570; font-size: 12.5px; margin: 0 0 16px; }
    .ur-am-controls__row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 14px; }
    .ur-am-controls__row > div { flex: 1 1 220px; }
    .ur-am-controls label { display: block; font-size: 11.5px; font-weight: 700; color: #1C2321; margin-bottom: 6px; }
    .ur-am-controls select { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; font-size: 13px; background: #fff; }
    .ur-am-generate { flex-shrink: 0; display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 22px; border-radius: 999px; border: none; background: #C9974D; color: #1C2321; font-size: 13px; font-weight: 700; cursor: pointer; align-self: flex-end; }
    .ur-am-generate:hover { background: #B07C3D; }

    /* ---- Results head ---- */
    .ur-am-results-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; font-size: 13.5px; color: #1C2321; }
    .ur-am-results-head strong { font-weight: 700; }
    .ur-am-tags span { font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 999px; margin-left: 8px; }
    .ur-am-tags span:first-child { background: #FCF3E3; color: #C9974D; }
    .ur-am-tags span:last-child { background: #E7F3EC; color: #2E7D5B; }

    /* ---- Match card ---- */
    .ur-am-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .ur-am-match-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 18px; }
    .ur-am-match-card__head { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
    .ur-am-match-card__num { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; color: #9AA5A0; margin-bottom: 2px; }
    .ur-am-match-card__head h3 { font-family: 'Playfair Display', serif; font-size: 17px; color: #123A2E; margin: 0; }
    .ur-am-score { width: 58px; height: 58px; border-radius: 50%; background: #C9974D; color: #1C2321; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-am-score b { font-size: 16px; line-height: 1.1; }
    .ur-am-score small { font-size: 8px; font-weight: 700; letter-spacing: .04em; }

    .ur-am-pair { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: stretch; margin-bottom: 16px; }
    .ur-am-pair__side { border-radius: 12px; padding: 12px; border: 1px solid #E7E2D6; }
    .ur-am-pair__side--client { background: #123A2E; color: #fff; border-color: #123A2E; }
    .ur-am-pair__side--match { background: #F1F7F3; }
    .ur-am-pair__label { font-size: 9.5px; font-weight: 700; letter-spacing: .06em; margin-bottom: 8px; opacity: .7; }
    .ur-am-pair__head { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .ur-am-pair__photo { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
    .ur-am-pair__id { font-weight: 700; font-size: 13px; }
    .ur-am-pair__meta { font-size: 11px; opacity: .8; margin-bottom: 6px; }
    .ur-am-pair .ur-badge-pill { font-size: 9px; padding: 2px 8px; margin: 0 4px 4px 0; }
    .ur-am-pair__side--match .ur-badge-pill--abroad { background: #E4EEF7; color: #2C5F8A; }
    .ur-am-pair__field { font-size: 11px; margin-top: 6px; }
    .ur-am-pair__field span { display: block; opacity: .65; font-size: 9.5px; text-transform: uppercase; letter-spacing: .03em; }
    .ur-am-pair__field b { font-weight: 600; }
    .ur-am-pair__connector { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; color: #C9974D; width: 22px; }
    .ur-am-pair__connector i { width: 30px; height: 30px; border-radius: 50%; border: 1px solid #E7E2D6; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .ur-am-pair__connector span { writing-mode: vertical-rl; font-size: 9px; font-weight: 700; letter-spacing: .06em; color: #9AA5A0; }

    .ur-am-why { border-top: 1px dashed #E7E2D6; padding-top: 14px; margin-bottom: 14px; }
    .ur-am-why__head { display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 700; color: #1C2321; margin-bottom: 10px; }
    .ur-am-why__head span { font-weight: 400; color: #9AA5A0; }
    .ur-am-why__grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .ur-am-why-pill { display: flex; align-items: center; gap: 7px; font-size: 11.5px; padding: 8px 10px; border-radius: 8px; }
    .ur-am-why-pill--ok { background: #E7F3EC; color: #205C3F; }
    .ur-am-why-pill--warn { background: #FCF0DE; color: #8A5A15; }
    .ur-am-why-pill i { flex-shrink: 0; }

    .ur-am-owner { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 10px; border-top: 1px dashed #E7E2D6; padding-top: 12px; margin-bottom: 12px; }
    .ur-am-owner__avatar { width: 32px; height: 32px; border-radius: 50%; background: #F6F4EF; color: #123A2E; font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-am-owner__body { flex: 1 1 140px; min-width: 0; }
    .ur-am-owner__label { font-size: 9.5px; font-weight: 700; letter-spacing: .04em; color: #9AA5A0; }
    .ur-am-owner__name { font-size: 12.5px; font-weight: 700; color: #1C2321; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-am-owner__meta { font-size: 11px; color: #6B7570; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-am-owner__role { margin-left: auto; background: #E7F3EC; color: #123A2E; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; white-space: nowrap; }
    .ur-am-owner__whatsapp { width: 30px; height: 30px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-am-owner__whatsapp:hover { background: #1DA851; color: #fff; }

    .ur-am-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .ur-am-actions a, .ur-am-actions button { flex: 1 1 auto; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 12px; border-radius: 999px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; white-space: nowrap; }
    .ur-am-actions a:hover, .ur-am-actions button:hover { background: #F6F4EF; }
    .ur-am-btn--dark { background: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
    .ur-am-btn--dark:hover { background: #0F2E24 !important; }
    .ur-am-btn--whatsapp { background: #25D366 !important; border-color: #25D366 !important; color: #fff !important; }
    .ur-am-btn--whatsapp:hover { background: #1DA851 !important; }
    .ur-am-btn--done { background: #E7F3EC !important; border-color: #2E7D5B !important; color: #2E7D5B !important; cursor: default; }

    .ur-am-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 34px; text-align: center; color: #6B7570; }

    @media (max-width: 991px) {
        .ur-am-top { grid-template-columns: 1fr; }
    }
    @media (max-width: 860px) {
        .ur-am-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .ur-am-pair { grid-template-columns: 1fr; }
        .ur-am-pair__connector { flex-direction: row; width: 100%; }
        .ur-am-pair__connector span { writing-mode: horizontal-tb; }
        .ur-am-why__grid { grid-template-columns: 1fr; }
        .ur-am-controls__row { flex-direction: column; }
        .ur-am-controls__row > div { flex-basis: auto; }
        .ur-am-generate { width: 100%; justify-content: center; align-self: stretch; }
    }
</style>

@if(!empty($noProposals))
<div class="ur-am-empty">
    No team proposals have Partner Requirements set yet — add them under "My Proposals &rarr; Edit" to start finding AI matches.
</div>
@else
@php
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
    // The paste-and-fill workflow doesn't always capture a name — falls
    // back to the dataid only when there's genuinely nothing to show.
    $nameOf = fn ($p) => trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: $p->dataid;
    $proposalAge = $ageOf($proposalProfile);
    $proposalName = $nameOf($proposal);
    $bestScore = $matches->isNotEmpty() ? ($matches->first()->compat['percent'] ?? 0) : 0;
    $tierLabel = fn ($pct) => $pct >= 85 ? 'Excellent Match' : ($pct >= 70 ? 'Good Match' : ($pct >= 50 ? 'Fair Match' : 'Needs Review'));
@endphp

<div class="ur-am-head">
    <div>
        <span class="ur-am-eyebrow">— AI MATCH CENTER</span>
        <h1>Best compatible proposals</h1>
        <p>Compare both clients, review every match reason, then contact the profile owner directly.</p>
    </div>
    <div class="ur-am-privacy"><i class="fa fa-lock"></i> Sensitive details stay protected until the profile owner approves the introduction.</div>
</div>

<div class="ur-am-top">
    <div class="ur-am-matching-for">
        <div class="ur-am-matching-for__label">MATCHING FOR</div>
        <div class="ur-am-matching-for__head">
            <img class="ur-am-matching-for__photo" src="{{ $proposalProfile->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
            <div>
                <div class="ur-am-matching-for__id">{{ $proposalName }}</div>
                <div class="ur-am-matching-for__meta">
                    {{ $proposal->dataid }}
                    &bull; {{ ucfirst($proposal->gender) }}
                    @if($proposalAge) &bull; {{ $proposalAge }} @endif
                    @if(!empty($proposalProfile->lbl_marital_status)) &bull; {{ $proposalProfile->lbl_marital_status }} @endif
                </div>
            </div>
        </div>
        <div>@include('team.partials.classification-badges', ['profile' => $proposalProfile])</div>
        <div class="ur-am-matching-for__tiles">
            <div><span>PROFESSION</span><b>{{ $proposal->profession ?: '—' }}</b></div>
            <div><span>LOCATION</span><b>{{ $proposalProfile->lbl_city ?: $proposalProfile->lbl_con_of_residence ?: '—' }}</b></div>
            <div><span>NATIONALITY</span><b>{{ $proposalProfile->lbl_con_of_citizenship ?: $proposalProfile->lbl_con_of_residence ?: '—' }}</b></div>
            <div><span>PROFILE OWNER</span><b>{{ $proposalProfile->added_by_name }}</b></div>
        </div>
        @if($proposalProfile->team_card_role === 'own')
            <a href="{{ route('team.proposals.edit', $proposal->dataid) }}" class="ur-am-matching-for__edit"><i class="fa fa-pencil"></i> Edit requirements</a>
        @endif
    </div>

    <div class="ur-am-controls">
        <div class="ur-am-controls__head">
            <h2>Match controls</h2>
            <span class="ur-am-ready-pill">AI ready</span>
        </div>
        <p>Choose a client and narrow the results.</p>
        <form method="GET" action="{{ route('team.matches') }}">
            <div class="ur-am-controls__row">
                <div>
                    <label>Select any team proposal</label>
                    <select name="proposal" onchange="this.form.submit()">
                        @foreach($candidateProposals as $cand)
                            @php $candAge = $ageOf($cand); $candName = $nameOf($cand); @endphp
                            <option value="{{ $cand->dataid }}" {{ $cand->dataid === $proposal->dataid ? 'selected' : '' }}>
                                {{ $candName }}{{ $candName !== $cand->dataid ? ' ('.$cand->dataid.')' : '' }} — {{ ucfirst($cand->gender) }}{{ $candAge ? ', '.$candAge : '' }}{{ $cand->profession ? ', '.$cand->profession : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Minimum score</label>
                    <select name="min_score" onchange="this.form.submit()">
                        <option value="0" {{ $minScore == 0 ? 'selected' : '' }}>All matches</option>
                        <option value="70" {{ $minScore == 70 ? 'selected' : '' }}>70%+ match</option>
                        <option value="80" {{ $minScore == 80 ? 'selected' : '' }}>80%+ match</option>
                        <option value="90" {{ $minScore == 90 ? 'selected' : '' }}>90%+ match</option>
                    </select>
                </div>
            </div>
            <div class="ur-am-controls__row">
                <div>
                    <label>Location</label>
                    <select name="location" onchange="this.form.submit()">
                        <option value="">Any location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->dataid }}" {{ $locationFilter == $loc->dataid ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="ur-am-generate"><i class="fa fa-magic"></i> Generate AI matches</button>
            </div>
        </form>
    </div>
</div>

<div class="ur-am-results-head">
    <div>
        <strong>{{ count($matches) }}</strong> compatible proposal{{ count($matches) == 1 ? '' : 's' }} for {{ $proposalName }}
        @if($matches->isNotEmpty()) &bull; best score {{ $bestScore }}% @endif
    </div>
    <div class="ur-am-tags"><span>Premium scoring active</span><span>Privacy-safe results</span></div>
</div>

@if($matches->isEmpty())
<div class="ur-am-empty">No compatible proposals found for these filters — try lowering the minimum score or clearing the location filter.</div>
@else
<div class="ur-am-grid">
    @foreach($matches as $i => $match)
        @php
            $pct = $match->compat['percent'] ?? 0;
            $matchAge = $ageOf($match);
            $matchName = $nameOf($match);
            $bothAbroad = strtolower($proposalProfile->looking_from ?? '') === 'abroad' && strtolower($match->looking_from ?? '') === 'abroad';
        @endphp
        <div class="ur-am-match-card">
            <div class="ur-am-match-card__head">
                <div>
                    <span class="ur-am-match-card__num">MATCH {{ sprintf('%02d', $i + 1) }}</span>
                    <h3>{{ $tierLabel($pct) }}</h3>
                </div>
                <div class="ur-am-score"><b>{{ $pct }}%</b><small>AI SCORE</small></div>
            </div>

            <div class="ur-am-pair">
                <div class="ur-am-pair__side ur-am-pair__side--client">
                    <div class="ur-am-pair__label">YOUR CLIENT</div>
                    <div class="ur-am-pair__head">
                        <img class="ur-am-pair__photo" src="{{ $proposalProfile->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
                        <div>
                            <div class="ur-am-pair__id">{{ $proposalName }}</div>
                            <div class="ur-am-pair__meta">{{ $proposal->dataid }} &bull; {{ ucfirst($proposal->gender) }}@if($proposalAge) &bull; {{ $proposalAge }}@endif @if(!empty($proposalProfile->lbl_marital_status)) &bull; {{ $proposalProfile->lbl_marital_status }}@endif</div>
                        </div>
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $proposalProfile])
                    <div class="ur-am-pair__field"><span>Profession</span><b>{{ $proposal->profession ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Location</span><b>{{ $proposalProfile->lbl_city ?: $proposalProfile->lbl_con_of_residence ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Education</span><b>{{ $proposalProfile->lbl_education ?: '—' }}</b></div>
                </div>
                <div class="ur-am-pair__connector"><i class="fa fa-magic"></i><span>AI MATCH</span></div>
                <div class="ur-am-pair__side ur-am-pair__side--match">
                    <div class="ur-am-pair__label">RECOMMENDED PROFILE</div>
                    <div class="ur-am-pair__head">
                        <img class="ur-am-pair__photo" src="{{ $match->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender) }}';">
                        <div>
                            <div class="ur-am-pair__id">{{ $matchName }}</div>
                            <div class="ur-am-pair__meta">{{ $match->dataid }} &bull; {{ ucfirst($match->gender) }}@if($matchAge) &bull; {{ $matchAge }}@endif @if(!empty($match->lbl_marital_status)) &bull; {{ $match->lbl_marital_status }}@endif</div>
                        </div>
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $match])
                    <div class="ur-am-pair__field"><span>Profession</span><b>{{ $match->profession ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Location</span><b>{{ $match->lbl_city ?: $match->lbl_con_of_residence ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Education</span><b>{{ $match->lbl_education ?: '—' }}</b></div>
                </div>
            </div>

            <div class="ur-am-why">
                <div class="ur-am-why__head">Why AI selected this match <span>{{ count($match->compat['checks'] ?? []) }} checks</span></div>
                <div class="ur-am-why__grid">
                    @foreach(($match->compat['checks'] ?? []) as $check)
                        <div class="ur-am-why-pill {{ $check['matched'] ? 'ur-am-why-pill--ok' : 'ur-am-why-pill--warn' }}">
                            <i class="fa {{ $check['matched'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                            {{ $check['matched'] ? $check['factor'].' preference matched' : $check['factor'].' needs review' }}
                        </div>
                    @endforeach
                    @if($bothAbroad)
                        <div class="ur-am-why-pill ur-am-why-pill--ok"><i class="fa fa-check-circle"></i> Both profiles are abroad-based</div>
                    @endif
                </div>
            </div>

            <div class="ur-am-owner">
                <div class="ur-am-owner__avatar">{{ strtoupper(substr($match->added_by_name, 0, 1)) }}</div>
                <div class="ur-am-owner__body">
                    <div class="ur-am-owner__label">MATCHED PROFILE OWNER</div>
                    <div class="ur-am-owner__name">{{ $match->added_by_name }}</div>
                    <div class="ur-am-owner__meta">{{ $match->added_by_dataid }}{{ $match->added_by_experience ? ' &bull; '.$match->added_by_experience : '' }}</div>
                </div>
                @if($match->added_by_experience)<span class="ur-am-owner__role">{{ $match->added_by_experience }}</span>@endif
                <a class="ur-am-owner__whatsapp" href="{{ route('team.proposals.share.whatsapp', $match->dataid) }}" target="_blank" rel="noopener" title="Message {{ $match->added_by_name }}"><i class="fa fa-whatsapp"></i></a>
            </div>

            <div class="ur-am-actions">
                <a href="{{ route('team.proposals.view', $match->dataid) }}" onclick="event.preventDefault(); openClientFile('{{ $match->dataid }}');"><i class="fa fa-eye"></i> View Match</a>
                @if($match->already_successful)
                    <button type="button" class="ur-am-btn--done" disabled><i class="fa fa-trophy"></i> Matched</button>
                @else
                    <form method="POST" action="{{ route('team.successful-matches.store') }}" onsubmit="return false;" style="flex:1 1 auto;">
                        @csrf
                        <input type="hidden" name="proposal_dataid" value="{{ $proposal->dataid }}">
                        <input type="hidden" name="counterpart_dataid" value="{{ $match->dataid }}">
                        <button type="button" class="ur-am-btn--dark" style="width:100%;" onclick="swalConfirm('Mark as Successful Match', 'Mark {{ $proposalName }} and {{ $matchName }} as a successful match?', () => this.closest('form').submit());"><i class="fa fa-trophy"></i> Mark Successful</button>
                    </form>
                @endif
                <a href="{{ route('team.matches.forward', [$proposal->dataid, $match->dataid]) }}" target="_blank" rel="noopener" class="ur-am-btn--whatsapp"><i class="fa fa-whatsapp"></i> Forward Both</a>
            </div>
        </div>
    @endforeach
</div>
@endif
@endif
@endsection
