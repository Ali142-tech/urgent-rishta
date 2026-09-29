{{--
    Read-only team roster ("Matchmakers" — client's Partner Portal mockup,
    Sep 2026). See TeamController::matchmakers(). Peer visibility only — no
    management actions here (suspend/deactivate/delete stay admin-only at
    admin/team-members).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Matchmakers')
@section('main-content')
<style>
    .ur-mm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; }
    .ur-mm-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; }
    .ur-mm-card__head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .ur-mm-card__photo { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; background: #F6F4EF; border: 1px solid #E7E2D6; }
    .ur-mm-card__name { font-family: 'Playfair Display', serif; font-weight: 700; color: #123A2E; font-size: 15.5px; }
    .ur-mm-card__meta { font-size: 12px; color: #6B7570; }
    .ur-mm-card__field { font-size: 12.5px; margin-bottom: 8px; }
    .ur-mm-card__field span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #9AA5A0; }
    .ur-mm-card__field a { color: #123A2E; }
    .ur-mm-card__stats { display: flex; gap: 10px; margin-top: 14px; }
    .ur-mm-card__stat { flex: 1; text-align: center; background: #F6F4EF; border-radius: 10px; padding: 10px 6px; }
    .ur-mm-card__stat b { display: block; font-family: 'Playfair Display', serif; font-size: 18px; color: #123A2E; }
    .ur-mm-card__stat span { font-size: 10.5px; color: #6B7570; }
    .ur-mm-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 30px; text-align: center; color: #6B7570; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 4px;">Matchmakers</h1>
<p style="color:#6B7570; font-size:13.5px; margin:0 0 18px;">{{ $members->count() }} verified {{ Str::plural('teammate', $members->count()) }}.</p>

@if($members->isEmpty())
<div class="ur-mm-empty">No active team members yet.</div>
@else
<div class="ur-mm-grid">
    @foreach($members as $member)
    <div class="ur-mm-card">
        <div class="ur-mm-card__head">
            <img class="ur-mm-card__photo" src="{{ $member->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
            <div>
                <div class="ur-mm-card__name">{{ $member->first_name }} {{ $member->last_name }}</div>
                @if(!empty($member->experience))<div class="ur-mm-card__meta">{{ $member->experience }}</div>@endif
            </div>
        </div>
        @if(!empty($member->city))<div class="ur-mm-card__field"><span>City</span>{{ $member->city }}</div>@endif
        @if(!empty($member->contact_mobile_number))
        <div class="ur-mm-card__field">
            <span>WhatsApp</span>
            <a href="{{ \App\User::whatsappLinkForNumber($member->contact_mobile_number) }}" target="_blank" rel="noopener">
                {{ $member->contact_mobile_number }} <i class="fa fa-whatsapp" style="color:#25D366;"></i>
            </a>
        </div>
        @endif
        @if(!empty($member->about_me))<div class="ur-mm-card__field"><span>About</span>{{ $member->about_me }}</div>@endif

        <div class="ur-mm-card__stats">
            <div class="ur-mm-card__stat"><b>{{ $member->activeProposalsCount }}</b><span>Active Profiles</span></div>
            <div class="ur-mm-card__stat"><b>{{ $member->successfulMatchesCount }}</b><span>Successful Matches</span></div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
