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
    .ur-mm-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 6px; }
    .ur-mm-head p { color: #6B7570; font-size: 13.5px; margin: 0; max-width: 640px; }
    .ur-mm-live { background: #E7F3EC; color: #2E7D5B; font-size: 11.5px; font-weight: 700; padding: 4px 12px; border-radius: 999px; white-space: nowrap; }

    .ur-mm-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin: 18px 0 24px; }
    .ur-mm-stats div { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 16px 18px; }
    .ur-mm-stats span { display: block; font-size: 12.5px; color: #6B7570; margin-bottom: 6px; }
    .ur-mm-stats b { display: block; font-family: 'Playfair Display', serif; font-size: 26px; color: #123A2E; }

    .ur-mm-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .ur-mm-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 18px; }

    .ur-mm-pair { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: stretch; margin-bottom: 14px; }
    .ur-mm-side { background: #F6F4EF; border-radius: 12px; padding: 12px; min-width: 0; }
    .ur-mm-label { font-size: 9.5px; font-weight: 700; letter-spacing: .06em; color: #9AA5A0; margin-bottom: 6px; }
    .ur-mm-id { font-weight: 700; color: #123A2E; font-size: 14px; margin-bottom: 3px; }
    .ur-mm-meta { font-size: 11.5px; color: #6B7570; margin-bottom: 8px; }
    .ur-badge-pill { display: inline-block; font-size: 9.5px; font-weight: 700; padding: 3px 9px; border-radius: 999px; margin: 0 5px 5px 0; }
    .ur-badge-pill--premium { background: #FBF0DA; color: #8A6218; }
    .ur-badge-pill--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-badge-pill--abroad { background: #E4EEF7; color: #2C5F8A; }
    .ur-badge-pill--highlight { background: #FBE4E9; color: #A23B57; }
    .ur-mm-score { width: 64px; height: 64px; border-radius: 50%; background: #123A2E; color: #fff; display: flex; align-items: center; justify-content: center; align-self: center; flex-shrink: 0; }
    .ur-mm-score b { font-size: 17px; font-weight: 700; }

    .ur-mm-owner { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 10px; border-top: 1px dashed #E7E2D6; padding-top: 12px; margin-bottom: 14px; }
    .ur-mm-owner__avatar { width: 34px; height: 34px; border-radius: 50%; background: #123A2E; color: #fff; font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-mm-owner__body { flex: 1 1 200px; min-width: 0; }
    .ur-mm-owner__name { font-size: 13px; font-weight: 700; color: #1C2321; }
    .ur-mm-owner__meta { font-size: 11.5px; color: #6B7570; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-mm-owner__verified { margin-left: auto; background: #E7F3EC; color: #2E7D5B; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; white-space: nowrap; }

    .ur-mm-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .ur-mm-actions a { flex: 1 1 auto; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 12px; border-radius: 999px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; white-space: nowrap; }
    .ur-mm-actions a:hover { background: #F6F4EF; }
    .ur-mm-actions a.is-whatsapp { background: #25D366; border-color: #25D366; color: #fff; }
    .ur-mm-actions a.is-whatsapp:hover { background: #1DA851; color: #fff; }

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
                    <div class="ur-mm-owner__name">{{ $pair['ownerName'] }}</div>
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
                <a class="is-whatsapp" href="{{ route('team.matches.forward', [$client->dataid, $match->dataid]) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> Forward both forms</a>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
