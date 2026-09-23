{{--
    Dashboard overview — stat cards, a short AI Match Recommendations
    preview, a Live Notifications panel, and the Quick Add Proposal form.
    This is what "Team Dashboard" (team/dashboard route) now shows — the
    old single-page proposal grid was split into My Proposals / Search
    Proposals (see team/proposals-mine.blade.php, team/proposals-search.blade.php).
    Keeps this site's real brand palette (green/gold, --ur-dash-*) rather
    than the client mockup's literal maroon — see ur-member-card.css:279-283
    for the prior precedent of that same call.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Dashboard')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-ov-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
    .ur-ov-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0; }
    .ur-ov-head p { color: #6B7570; font-size: 13.5px; margin: 4px 0 0; }

    .ur-ov-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
    @media (max-width: 991px) { .ur-ov-layout { grid-template-columns: 1fr; } }

    .ur-ov-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 26px; }
    @media (max-width: 900px) { .ur-ov-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .ur-ov-stats { grid-template-columns: 1fr; } }
    .ur-ov-stat { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px; }
    .ur-ov-stat__icon { width: 38px; height: 38px; border-radius: 10px; background: #F6F4EF; color: #C9974D; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 10px; }
    .ur-ov-stat__label { display: block; font-size: 12.5px; color: #6B7570; }
    .ur-ov-stat__value { display: block; font-size: 26px; font-weight: 700; color: #123A2E; font-family: 'Playfair Display', serif; }
    .ur-ov-stat__delta { display: inline-block; margin-top: 6px; font-size: 11.5px; font-weight: 700; color: #C9974D; text-decoration: none; }

    .ur-ov-section { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; margin-bottom: 24px; }
    .ur-ov-section__head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px; }
    .ur-ov-section__head h2 { font-size: 16px; color: #123A2E; font-weight: 700; margin: 0; }
    .ur-ov-section__head a { font-size: 12.5px; color: #C9974D; font-weight: 700; text-decoration: none; }

    .ur-ov-panel { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
    .ur-ov-panel h3 { font-size: 14px; color: #123A2E; font-weight: 700; margin: 0 0 12px; }
    .ur-notif-mini { display: flex; gap: 10px; padding: 8px 0; border-bottom: 1px solid #F0EEE7; font-size: 12.5px; }
    .ur-notif-mini:last-child { border-bottom: none; }
    .ur-notif-mini i { color: #C9974D; margin-top: 2px; }
    .ur-notif-mini__time { color: #9AA5A0; font-size: 11px; }

    .ur-quick-form .row { margin-bottom: 10px; }
    .ur-quick-form label { font-size: 11.5px; font-weight: 600; color: #1C2321; margin-bottom: 3px; display: block; }
    .ur-quick-form .form-control, .ur-quick-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 36px; padding: 0 10px; width: 100%; font-size: 12.5px; }
    .ur-quick-form button { width: 100%; height: 42px; border-radius: 999px; background: #C9974D; color: #fff; border: none; font-size: 13px; font-weight: 700; margin-top: 6px; }
    .ur-quick-form button:hover { background: #B07C3D; }

    .ur-trust-bar { display: flex; align-items: center; justify-content: center; gap: 24px; flex-wrap: wrap; margin-top: 30px; padding: 16px; background: #F6F4EF; border-radius: 12px; font-size: 12.5px; color: #123A2E; font-weight: 600; }
    .ur-trust-bar i { color: #C9974D; margin-right: 6px; }
</style>

<div class="ur-ov-head">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->first_name }}!</h1>
        <p>{{ now()->format('l, j F Y') }} &bull; here's how your proposals are doing</p>
    </div>
</div>

<div class="ur-ov-layout">
    <div>
        <div class="ur-ov-stats">
            <div class="ur-ov-stat">
                <div class="ur-ov-stat__icon"><i class="fa fa-file-text-o"></i></div>
                <span class="ur-ov-stat__label">My Proposals</span>
                <span class="ur-ov-stat__value">{{ number_format($myProposalsCount) }}</span>
                <a href="{{ route('team.proposals.mine') }}" class="ur-ov-stat__delta">View all &rarr;</a>
            </div>
            <div class="ur-ov-stat">
                <div class="ur-ov-stat__icon"><i class="fa fa-magic"></i></div>
                <span class="ur-ov-stat__label">AI Matches Found</span>
                <span class="ur-ov-stat__value">{{ number_format($aiMatchesCount) }}</span>
                <a href="{{ route('team.matches') }}" class="ur-ov-stat__delta">View all &rarr;</a>
            </div>
            <div class="ur-ov-stat">
                <div class="ur-ov-stat__icon"><i class="fa fa-users"></i></div>
                <span class="ur-ov-stat__label">Team Proposals</span>
                <span class="ur-ov-stat__value">{{ number_format($teamProposalsCount) }}</span>
                <a href="{{ route('team.proposals.search') }}" class="ur-ov-stat__delta">View all &rarr;</a>
            </div>
            <div class="ur-ov-stat">
                <div class="ur-ov-stat__icon"><i class="fa fa-trophy"></i></div>
                <span class="ur-ov-stat__label">Successful Matches</span>
                <span class="ur-ov-stat__value">{{ number_format($successfulMatchesCount) }}</span>
                <a href="{{ route('team.successful-matches') }}" class="ur-ov-stat__delta">View all &rarr;</a>
            </div>
        </div>

        <div class="ur-ov-section">
            <div class="ur-ov-section__head">
                <h2><i class="fa fa-magic" style="color:#C9974D;"></i> AI Match Recommendations</h2>
                <a href="{{ route('team.matches') }}">View All Matches &rarr;</a>
            </div>
            @if($previewMatches->isEmpty())
            <p style="color:#6B7570; font-size:13px; margin:0;">No AI matches yet — add Partner Requirements/Preferred fields on a proposal to start finding matches for it.</p>
            @else
            <div class="member-results">
                @foreach($previewMatches as $match)
                    @php
                        $compat = $match->compatibilityWith($match->viewer_preference_for_card);
                        $explanation = $compat ? \App\Profile::compatibilityExplanation($compat) : null;
                    @endphp
                    @include('member.partials.member-card', ['member' => $match, 'viewerPreference' => $match->viewer_preference_for_card, 'matchExplanation' => $explanation])
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div>
        <div class="ur-ov-panel">
            <h3><i class="fa fa-bell" style="color:#C9974D;"></i> Live Notifications</h3>
            @forelse($recentNotifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="ur-notif-mini">
                    <i class="fa fa-circle" style="font-size:8px;"></i>
                    <div>
                        <div><b>{{ $data['status'] ?? class_basename($notification->type) }}</b></div>
                        @if(!empty($data['message']))<div>{{ $data['message'] }}</div>@endif
                        <div class="ur-notif-mini__time">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p style="color:#6B7570; font-size:12.5px; margin:0;">No notifications yet.</p>
            @endforelse
            <a href="{{ route('team.notifications') }}" style="display:block; margin-top:10px; font-size:12px; color:#C9974D; font-weight:700; text-decoration:none;">View All Notifications &rarr;</a>
        </div>

        <div class="ur-ov-panel">
            <h3><i class="fa fa-plus-circle" style="color:#C9974D;"></i> Add New Proposal</h3>
            <form class="ur-quick-form" method="POST" action="{{ route('team.proposals.store') }}">
                @csrf
                <div class="row">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="row">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="row">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="" disabled selected>Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="row">
                    <label>Date of Birth</label>
                    <div style="display:flex; gap:4px;">
                        <input type="text" class="form-control" name="day" placeholder="DD" maxlength="2" required>
                        <input type="text" class="form-control" name="month" placeholder="MM" maxlength="2" required>
                        <input type="text" class="form-control" name="year" placeholder="YYYY" maxlength="4" required>
                    </div>
                </div>
                <div class="row">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="row">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" name="contact_mobile_number" required>
                </div>
                <div class="row">
                    <label>Country</label>
                    <select name="country" class="form-control">
                        <option value="">Select</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->dataid }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <label>Profession</label>
                    <input type="text" class="form-control" name="profession">
                </div>
                <div class="row">
                    <label>Marital Status</label>
                    <select name="marital_status" class="form-control">
                        <option value="">Select</option>
                        @foreach($maritalstatuses as $maritalstatus)
                            <option value="{{ $maritalstatus->dataid }}">{{ $maritalstatus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"><i class="fa fa-plus"></i> Submit Proposal</button>
            </form>
        </div>
    </div>
</div>

<div class="ur-trust-bar">
    <span><i class="fa fa-lock"></i> Private</span>
    <span><i class="fa fa-check-circle"></i> Verified</span>
    <span><i class="fa fa-briefcase"></i> Professional</span>
</div>
@endsection
