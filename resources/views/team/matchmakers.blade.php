{{--
    "Matchmakers Directory" (client mockup, Sep 2026) — the team roster
    (peer visibility, still no suspend/deactivate/delete — those stay
    admin-only at admin/team-members), plus "Recommended for your clients"
    (cross-team AI suggestions — every OTHER matchmaker's candidate that
    scores against one of MY clients, see TeamController::matchmakers())
    and a self-service "Upload my photo" (reuses storeProposalPhoto() —
    see TeamController::uploadMyPhoto()'s own docblock for why that's safe
    to reuse as-is).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Matchmakers')
@section('main-content')
<style>
    .ur-mk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-mk-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-mk-head p { color: #6B7570; font-size: 13.5px; margin: 0; }
    .ur-mk-verified-tag { background: #E7F3EC; color: #2E7D5B; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }

    .ur-mk-grow { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 26px; }
    .ur-mk-grow h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 18px; color: #123A2E; margin: 0 0 4px; }
    .ur-mk-grow p { color: #6B7570; font-size: 13px; margin: 0; }
    .ur-mk-grow__actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ur-mk-grow__actions a { display: inline-flex; align-items: center; gap: 7px; height: 42px; padding: 0 18px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; white-space: nowrap; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; }
    .ur-mk-grow__actions a:hover { background: #F6F4EF; }
    .ur-mk-grow__actions a.is-dark { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-mk-grow__actions a.is-dark:hover { background: #0F2E24; }

    .ur-mk-section-head { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin: 0 0 14px; }
    .ur-mk-section-head h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 18px; color: #123A2E; margin: 0; }
    .ur-mk-section-head span { font-size: 12px; color: #9AA5A0; }

    .ur-mk-reco-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 30px; }
    .ur-mk-reco-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px; display: flex; flex-direction: column; }
    .ur-mk-reco-card__head { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px; }
    .ur-mk-reco-tier { font-size: 10.5px; font-weight: 700; letter-spacing: .05em; color: #C9974D; }
    .ur-mk-reco-pct { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 18px; color: #123A2E; }
    .ur-mk-reco-id { font-weight: 700; color: #123A2E; font-size: 15px; }
    .ur-mk-reco-meta { font-size: 12.5px; color: #6B7570; margin-bottom: 4px; }
    .ur-mk-reco-for { font-size: 12px; color: #9AA5A0; margin-bottom: 10px; }
    .ur-mk-reco-reasons { font-size: 12px; color: #1C2321; margin-bottom: 14px; }
    .ur-mk-reco-by { font-size: 12px; color: #6B7570; margin: auto 0 14px; }
    .ur-mk-reco-btn { display: block; text-align: center; border-radius: 999px; padding: 11px 0; font-size: 13px; font-weight: 700; text-decoration: none; background: #123A2E; color: #fff; border: 1px solid #123A2E; }
    .ur-mk-reco-btn:hover { background: #0F2E24; color: #fff; }
    .ur-mk-reco-btn.is-added { background: #fff; color: #9AA5A0; border-color: #E7E2D6; cursor: default; }

    .ur-mk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
    .ur-mk-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; overflow: hidden; position: relative; }
    .ur-mk-card__banner { height: 76px; position: relative; }
    .ur-mk-ribbon { position: absolute; top: 10px; left: 10px; background: #FCF3E3; color: #8A6218; font-size: 10px; font-weight: 700; letter-spacing: .04em; padding: 4px 10px; border-radius: 999px; z-index: 2; }
    .ur-mk-card__avatar { position: absolute; left: 50%; bottom: -32px; transform: translateX(-50%); width: 68px; height: 68px; border-radius: 50%; border: 4px solid #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; font-family: 'Playfair Display', serif; font-weight: 700; font-size: 18px; z-index: 2; overflow: hidden; }
    .ur-mk-card__avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ur-mk-card__avatar small { font-family: 'Manrope', sans-serif; font-size: 7px; font-weight: 700; letter-spacing: .04em; margin-top: 2px; }
    .ur-mk-card__body { padding: 42px 18px 18px; text-align: center; }
    .ur-mk-card__name { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 16px; color: #123A2E; }
    .ur-mk-card__sub { font-size: 12px; color: #6B7570; margin-bottom: 12px; }
    .ur-mk-upload-form { margin-bottom: 12px; }
    .ur-mk-upload-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #E7E2D6; border-radius: 999px; padding: 8px 16px; font-size: 12px; font-weight: 700; color: #123A2E; cursor: pointer; }
    .ur-mk-upload-btn:hover { background: #F6F4EF; }
    .ur-mk-card__stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; text-align: left; }
    .ur-mk-card__stats div { background: #F6F4EF; border-radius: 10px; padding: 8px 10px; min-width: 0; }
    .ur-mk-card__stats span { display: block; font-size: 9.5px; font-weight: 700; letter-spacing: .04em; color: #9AA5A0; text-transform: uppercase; }
    .ur-mk-card__stats b { display: block; font-size: 12px; color: #1C2321; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-mk-card__actions { display: flex; gap: 8px; }
    .ur-mk-card__actions a { flex: 1 1 auto; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 38px; border-radius: 999px; font-size: 12px; font-weight: 700; text-decoration: none; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; white-space: nowrap; }
    .ur-mk-card__actions a:hover { background: #F6F4EF; }
    .ur-mk-card__actions a.is-whatsapp { background: #25D366; border-color: #25D366; color: #fff; }
    .ur-mk-card__actions a.is-whatsapp:hover { background: #1DA851; color: #fff; }

    .ur-mk-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 30px; text-align: center; color: #6B7570; }
</style>

@php
    $avatarPalette = ['#17493A', '#8E44AD', '#2C5F8A', '#B36B2E', '#1E7A46', '#5B2C6F'];
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
@endphp

<div class="ur-mk-head">
    <div>
        <h1>Matchmakers Directory</h1>
        <p>Find your teammate, add a client profile and review cross-team suggestions.</p>
    </div>
    <span class="ur-mk-verified-tag">{{ $members->count() }} verified {{ Str::plural('teammate', $members->count()) }}</span>
</div>

<div class="ur-mk-grow">
    <div>
        <h2>Grow your matches</h2>
        <p>Add a client profile, then review compatible profiles from other matchmakers.</p>
    </div>
    <div class="ur-mk-grow__actions">
        <a href="{{ route('team.proposals.create') }}" class="is-dark"><i class="fa fa-plus"></i> Add my profile</a>
        <a href="#ur-mk-recommendations"><i class="fa fa-magic"></i> My recommendations</a>
    </div>
</div>

@if($recommendations->isNotEmpty())
<div class="ur-mk-section-head" id="ur-mk-recommendations">
    <h2>Recommended for your clients</h2>
    <span>Compatibility suggestions for your current login</span>
</div>
<div class="ur-mk-reco-grid">
    @foreach($recommendations as $reco)
        @php
            $candidate = $reco['candidate'];
            $candidateAge = $ageOf($candidate);
            $candidateName = trim($candidate->first_name . ' ' . $candidate->last_name) ?: $candidate->dataid;
        @endphp
        <div class="ur-mk-reco-card">
            <div class="ur-mk-reco-card__head">
                <span class="ur-mk-reco-tier"><i class="fa fa-star"></i> {{ $reco['percent'] >= 90 ? 'Top Compatibility' : 'Recommended' }}</span>
                <span class="ur-mk-reco-pct">{{ $reco['percent'] }}%</span>
            </div>
            <div class="ur-mk-reco-id">{{ $candidateName }}</div>
            <div class="ur-mk-reco-meta">
                {{ ucfirst($candidate->gender) }}
                @if($candidateAge) &bull; {{ $candidateAge }} @endif
                @if(!empty($candidate->profession)) &bull; {{ $candidate->profession }} @endif
                @if($candidate->lbl_city || $candidate->lbl_con_of_residence) &bull; {{ $candidate->lbl_city ?: $candidate->lbl_con_of_residence }} @endif
            </div>
            <div class="ur-mk-reco-for">For your client {{ $reco['client']->dataid }}</div>
            @if($reco['reasons'])<div class="ur-mk-reco-reasons">{{ $reco['reasons'] }}</div>@endif
            <div class="ur-mk-reco-by">By {{ $reco['ownerName'] }}</div>
            @if($reco['isTopMatch'])
                <button type="button" class="ur-mk-reco-btn is-added" disabled>Added to My Matches</button>
            @else
                <a href="{{ route('team.matches.forward', [$reco['client']->dataid, $candidate->dataid]) }}" target="_blank" rel="noopener" class="ur-mk-reco-btn">Add match in one click</a>
            @endif
        </div>
    @endforeach
</div>
@endif

<div class="ur-mk-section-head">
    <h2>Partner matchmakers</h2>
    <span>Senior partners are highlighted by experience</span>
</div>

@if($members->isEmpty())
<div class="ur-mk-empty">No active team members yet.</div>
@else
<div class="ur-mk-grid">
    @foreach($members as $member)
        @php $color = $avatarPalette[$member->id % count($avatarPalette)]; @endphp
        <div class="ur-mk-card">
            <div class="ur-mk-card__banner" style="background:linear-gradient(135deg, {{ $color }}, #0F2E24);">
                @if($member->isSenior)<span class="ur-mk-ribbon"><i class="fa fa-star"></i> Senior Partner</span>@endif
                <div class="ur-mk-card__avatar" style="background:{{ $color }};">
                    @if($member->hasRealPhoto)
                        <img src="{{ $member->getProfileImage() }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
                    @else
                        {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                        <small>Add photo</small>
                    @endif
                </div>
            </div>
            <div class="ur-mk-card__body">
                <div class="ur-mk-card__name">{{ $member->first_name }} {{ $member->last_name }}</div>
                <div class="ur-mk-card__sub">{{ $member->dataid }} &bull; Verified Partner</div>

                @if($member->id === auth()->id())
                <form method="POST" action="{{ route('team.my-profile.photo') }}" enctype="multipart/form-data" class="ur-mk-upload-form">
                    @csrf
                    <label class="ur-mk-upload-btn">
                        <i class="fa fa-camera"></i> Upload my photo
                        <input type="file" name="image" accept="image/*" onchange="this.form.submit()" hidden>
                    </label>
                </form>
                @endif

                <div class="ur-mk-card__stats">
                    <div><span>City</span><b>{{ $member->city ?: '—' }}</b></div>
                    <div><span>Experience</span><b>{{ $member->experience ?: '—' }}</b></div>
                    <div><span>WhatsApp</span><b>{{ $member->contact_mobile_number ?: '—' }}</b></div>
                    <div><span>Proposals</span><b>{{ $member->activeProposalsCount }} client profile{{ $member->activeProposalsCount == 1 ? '' : 's' }}</b></div>
                </div>

                <div class="ur-mk-card__actions">
                    <a href="{{ route('team.proposals.search', ['matchmaker' => $member->id]) }}">View proposals</a>
                    @if(!empty($member->contact_mobile_number))
                        <a class="is-whatsapp" href="{{ \App\User::whatsappLinkForNumber($member->contact_mobile_number) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
