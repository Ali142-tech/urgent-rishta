{{--
    "My Matches" (client mockup, Sep 2026) — every client THIS team member
    owns, each paired with its single best AI match, all on one page with
    no controls (that's what AI Match Center — team.matches — is for; see
    TeamController::myMatches()). Uses the same classification-badges
    partial and Complete Client File modal (openClientFile()) as AI Match
    Center for consistency, and the same team.matches.forward route for
    "Forward both forms".
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Matches')
@section('main-content')
<style>
    .ur-mm-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }
    .ur-mm-head h1 { font-family: 'Manrope', system-ui, sans-serif; color: #123A2E; font-weight: 800; font-size: 26px; line-height: 1.2 !important; margin: 0 0 8px; }
    .ur-mm-head p { color: #4B5651; font-size: 14.5px; line-height: 1.6 !important; margin: 0; max-width: 700px; }
    .ur-mm-live { background: #E7F3EC; color: #2E7D5B; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 999px; white-space: nowrap; }

    .ur-mm-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin: 20px 0 24px; }
    .ur-mm-stats div { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 18px 20px; }
    .ur-mm-stats span { display: block; font-size: 13px; color: #6B7570; line-height: 1.3 !important; margin: 0 0 10px; }
    .ur-mm-stats b { display: block; font-family: 'Manrope', system-ui, sans-serif; font-size: 30px; font-weight: 800; line-height: 1 !important; color: #123A2E; }

    .ur-mm-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: start; }
    .ur-mm-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 22px; padding: 18px 18px 18px; overflow: hidden; }

    .ur-mm-pair { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); gap: 12px; align-items: center; margin-bottom: 16px; }
    .ur-mm-side { background: #fff; border: 1px solid #ECE8DD; border-radius: 16px; padding: 14px 16px; min-width: 0; }
    .ur-mm-label { font-size: 10px; font-weight: 800; letter-spacing: .08em; line-height: 1.2 !important; color: #7B8781; margin: 0 0 8px; text-transform: uppercase; }
    .ur-mm-id { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; color: #123A2E; font-size: 17px; line-height: 1.25 !important; margin: 0 0 6px; }
    .ur-mm-meta { font-size: 12px; color: #6B7570; line-height: 1.45 !important; margin: 0 0 10px; }
    .ur-badge-pill { display: inline-block; font-size: 10.5px; font-weight: 700; line-height: 1.2 !important; padding: 4px 10px; border-radius: 999px; margin: 0 6px 6px 0; border: 1px solid transparent; }
    .ur-badge-pill--premium { background: #FBF0DA; color: #8A6218; border-color: #F0DDB0; }
    .ur-badge-pill--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-badge-pill--abroad { background: #EAF2FB; color: #2C5F8A; border-color: #C9DDF0; }
    .ur-badge-pill--highlight { background: #FBE4E9; color: #A23B57; border-color: #F3C6D1; }
    .ur-mm-score { width: 66px; height: 66px; border-radius: 50%; background: linear-gradient(135deg, #1C6B52, #0F3F30); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 0 5px #fff, 0 0 0 6px #E7E2D6; margin: 0 4px; }
    .ur-mm-score b { font-family: 'Manrope', system-ui, sans-serif; font-size: 17px; font-weight: 800; line-height: 1 !important; }

    .ur-mm-owner { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 12px; background: #F8F7F3; margin: 0 -18px 16px; padding: 14px 18px; }
    .ur-mm-owner__avatar { width: 42px; height: 42px; border-radius: 12px; background: #0F3F30; color: #fff; font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-mm-owner__body { flex: 1 1 200px; min-width: 0; }
    .ur-mm-owner__name { font-size: 14px; font-weight: 800; line-height: 1.3 !important; color: #1C2321; margin: 0 0 2px; }
    .ur-mm-owner__meta { font-size: 12px; line-height: 1.4 !important; color: #6B7570; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-mm-owner__verified { margin-left: auto; background: #E7F3EC; color: #2E7D5B; font-size: 11px; font-weight: 700; line-height: 1.2 !important; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }

    .ur-mm-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ur-mm-actions a { flex: 1 1 auto; display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 42px; padding: 0 14px; border-radius: 12px; font-size: 12.5px; font-weight: 700; line-height: 1 !important; cursor: pointer; text-decoration: none; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; white-space: nowrap; }
    .ur-mm-actions a:hover { background: #F6F4EF; }
    .ur-mm-actions a.is-whatsapp { flex: 1.4 1 auto; background: #16A05A; border-color: #16A05A; color: #fff; }
    .ur-mm-actions a.is-whatsapp:hover { background: #128A4C; color: #fff; }


    .ur-mm-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 34px; text-align: center; color: #6B7570; }

    @media (max-width: 860px) {
        .ur-mm-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .ur-mm-pair { grid-template-columns: 1fr; }
        .ur-mm-score { width: 100%; height: auto; border-radius: 10px; padding: 8px; flex-direction: row; gap: 6px; }
    }
</style>

@php
    $viewerName = trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) ?: auth()->user()->dataid;
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
@endphp

<div class="ur-mm-head">
    <div>
        <h1>{{ $viewerName }} &mdash; My Matches</h1>
        <p>Automatic AI matches for the clients owned by your current login. Forward sends both original forms to the matched profile owner.</p>
    </div>
    <span class="ur-mm-live">Updated live</span>
</div>

<div class="ur-mm-stats">
    <div><span>Your active client profiles</span><b>{{ $activeClientProfiles }}</b></div>
    <div><span>Best matches ready</span><b>{{ $bestMatchesReady }}</b></div>
    <div><span>Highest AI compatibility</span><b>{{ $highestCompatibility }}%</b></div>
</div>

@if($pairs->isEmpty())
<div class="ur-mm-empty">
    No matches ready yet — add Partner Requirements on a client (under "My Clients" &rarr; Edit) to start finding AI matches for them.
</div>
@else
<div class="ur-mm-grid">
    @foreach($pairs as $i => $pair)
        @php
            $client = $pair['client'];
            $match = $pair['match'];
            $pct = $pair['compat']['percent'] ?? 0;
            $clientAge = $ageOf($client);
            $matchAge = $ageOf($match);
        @endphp
        <div class="ur-mm-card">
            <div class="ur-mm-pair">
                <div class="ur-mm-side">
                    <div class="ur-mm-label">YOUR CLIENT {{ sprintf('%02d', $i + 1) }}</div>
                    <div class="ur-mm-id">{{ $client->dataid }}</div>
                    <div class="ur-mm-meta">
                        {{ ucfirst($client->gender) }}
                        @if($clientAge) &bull; {{ $clientAge }} @endif
                        @if(!empty($client->profession)) &bull; {{ $client->profession }} @endif
                        @if($client->lbl_city || $client->lbl_con_of_residence) <br>{{ $client->lbl_city ?: $client->lbl_con_of_residence }} @endif
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $client])
                </div>
                <div class="ur-mm-score"><b>{{ $pct }}%</b></div>
                <div class="ur-mm-side">
                    <div class="ur-mm-label">SUGGESTED MATCH</div>
                    <div class="ur-mm-id">{{ $match->dataid }}</div>
                    <div class="ur-mm-meta">
                        {{ ucfirst($match->gender) }}
                        @if($matchAge) &bull; {{ $matchAge }} @endif
                        @if(!empty($match->profession)) &bull; {{ $match->profession }} @endif
                        @if($match->lbl_city || $match->lbl_con_of_residence) <br>{{ $match->lbl_city ?: $match->lbl_con_of_residence }} @endif
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $match])
                </div>
            </div>

            <div class="ur-mm-owner">
                <div class="ur-mm-owner__avatar">{{ strtoupper(substr($pair['ownerName'], 0, 1)) }}</div>
                <div class="ur-mm-owner__body">
                    <div class="ur-mm-owner__name">{{ $pair['ownerName'] }}@if(!empty($pair['ownerPremium'])) @include('team.partials.premium-badge')@endif</div>
                    <div class="ur-mm-owner__meta">
                        {{ $pair['ownerLocation'] }}
                        @if($pair['ownerPhone']) &bull; {{ $pair['ownerPhone'] }} @endif
                        @if($pair['ownerExperience']) &bull; {{ $pair['ownerExperience'] }} @endif
                    </div>
                </div>
                @if($pair['ownerVerified'])<span class="ur-mm-owner__verified">Verified owner</span>@endif
            </div>

            <div class="ur-mm-actions">
                <a onclick="openClientFile('{{ $client->dataid }}')"><i class="fa fa-eye"></i> Your file</a>
                <a onclick="openClientFile('{{ $match->dataid }}')"><i class="fa fa-eye"></i> Match file</a>
                <a class="is-whatsapp js-share-proposal" data-payload-url="{{ route('team.matches.forward.payload', [$client->dataid, $match->dataid]) }}" href="{{ route('team.matches.forward', [$client->dataid, $match->dataid]) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> Forward both forms</a>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
