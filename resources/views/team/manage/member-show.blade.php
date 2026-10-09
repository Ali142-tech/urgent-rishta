{{--
    Admin ▸ Team Members ▸ one member's complete profile (TeamAdminController::showMember()): who they are, contact,
    branding, access / privacy settings (read-only here — changed on the Team Members list), workload numbers and
    their latest proposals.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Team Member')
@section('main-content')
<style>
    .ur-mp { max-width: 1200px; }
    .ur-mp-back { display: inline-flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 700; color: #5B6560 !important; text-decoration: none !important; margin-bottom: 14px; }
    .ur-mp-hero { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; background: #fff; border: 1px solid #E7E2D6; border-radius: 20px; padding: 24px; margin-bottom: 18px; box-shadow: 0 2px 12px rgba(15,46,36,.05); }
    .ur-mp-photo { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 0 2px #E7E2D6; background: #F3EFE6; flex-shrink: 0; }
    .ur-mp-who { flex: 1; min-width: 220px; }
    .ur-mp-who h1 { font-family: 'Manrope', system-ui, sans-serif; font-size: 26px; font-weight: 800; color: #123A2E; margin: 0 0 4px; line-height: 1.2 !important; }
    .ur-mp-id { font-size: 13px; color: #8A938E; letter-spacing: .02em; }
    .ur-mp-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
    .ur-mp-chip { display: inline-flex; align-items: center; gap: 6px; padding: 5px 13px; border-radius: 999px; font-size: 12px; font-weight: 700; background: #F3EFE6; color: #4B5651; }
    .ur-mp-chip--ok { background: #E3F3EA; color: #1C7A58; }
    .ur-mp-chip--warn { background: #FCEBD2; color: #A8650F; }
    .ur-mp-chip--bad { background: #FBE0E5; color: #B02A47; }
    .ur-mp-chip--dark { background: #3B2A4F; color: #fff; }
    .ur-mp-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ur-mp-btn { display: inline-flex; align-items: center; gap: 7px; height: 42px; padding: 0 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none !important; border: 0; cursor: pointer; white-space: nowrap; }
    .ur-mp-btn--gold { background: #C9974D; color: #fff !important; }
    .ur-mp-btn--dark { background: #123A2E; color: #fff !important; }
    .ur-mp-btn--ghost { background: #fff; color: #123A2E !important; border: 1px solid #DDD5C2; }

    .ur-mp-stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
    .ur-mp-stat { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 16px 18px; }
    .ur-mp-stat span { display: block; font-size: 12px; color: #6B7570; margin-bottom: 8px; }
    .ur-mp-stat b { display: block; font-family: 'Manrope', system-ui, sans-serif; font-size: 26px; font-weight: 800; color: #123A2E; line-height: 1 !important; }
    .ur-mp-stat small { display: block; margin-top: 6px; font-size: 11.5px; color: #8A938E; }

    .ur-mp-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 18px; margin-bottom: 18px; }
    .ur-mp-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 20px 22px; }
    .ur-mp-card h2 { font-size: 15px; font-weight: 800; color: #123A2E; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
    .ur-mp-card h2 i { color: #C9974D; }
    .ur-mp-dl { display: grid; grid-template-columns: 150px minmax(0, 1fr); gap: 10px 14px; margin: 0; }
    .ur-mp-dl dt { font-size: 12.5px; font-weight: 600; color: #8A938E; margin: 0; }
    .ur-mp-dl dd { font-size: 14px; color: #1C2321; margin: 0; overflow-wrap: anywhere; }
    .ur-mp-about { font-size: 14px; color: #3C4642; line-height: 1.6; white-space: pre-line; margin: 0; }
    .ur-mp-logo { max-width: 120px; max-height: 70px; border-radius: 10px; border: 1px solid #E7E2D6; background: #fff; padding: 6px; }

    .ur-mp-list { display: grid; gap: 0; }
    .ur-mp-prop { display: grid; grid-template-columns: 54px minmax(0, 1fr) auto; gap: 14px; align-items: center; padding: 12px 0; border-bottom: 1px solid #F0ECE1; text-decoration: none !important; color: inherit !important; }
    .ur-mp-prop:last-child { border-bottom: 0; }
    .ur-mp-prop:hover b { color: #C9974D; }
    .ur-mp-prop img { width: 54px; height: 54px; border-radius: 12px; object-fit: cover; background: #F3EFE6; }
    .ur-mp-prop b { display: block; font-size: 14px; color: #123A2E; }
    .ur-mp-prop small { display: block; font-size: 12.5px; color: #6B7570; line-height: 1.4; }
    .ur-mp-prop em { font-style: normal; font-size: 12px; color: #8A938E; white-space: nowrap; text-align: right; }
    .ur-mp-empty { color: #6B7570; font-size: 13.5px; margin: 0; }

    @media (max-width: 1100px) { .ur-mp-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 860px) { .ur-mp-grid { grid-template-columns: 1fr; } }
    @media (max-width: 560px) {
        .ur-mp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ur-mp-hero { padding: 18px; }
        .ur-mp-dl { grid-template-columns: 1fr; gap: 2px 0; }
        .ur-mp-dl dd { margin-bottom: 10px; }
        .ur-mp-actions { width: 100%; } .ur-mp-actions .ur-mp-btn { flex: 1; justify-content: center; }
        .ur-mp-prop { grid-template-columns: 48px minmax(0, 1fr); } .ur-mp-prop em { grid-column: 2; text-align: left; }
    }
</style>

@php
    $status = $member->status ?? 'active';
    $statusClass = ['active' => 'ur-mp-chip--ok', 'suspended' => 'ur-mp-chip--warn', 'deactivated' => 'ur-mp-chip--bad'][$status] ?? '';
    $logoUrl = $member->logo ? '/' . \App\TeamMember::PHOTO_PATH . '/logos/' . $member->logo : null;
    $waDigits = preg_replace('/\D+/', '', \App\User::normalizePhoneNumberValue($member->contact_mobile_number));
    $dash = '—';
@endphp

<div class="ur-mp">
    <a href="{{ route('team.manage.members') }}" class="ur-mp-back"><i class="fa fa-arrow-left"></i> All team members</a>

    <div class="ur-mp-hero">
        <img class="ur-mp-photo" src="{{ $member->getProfileImage() }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage('male') }}';">
        <div class="ur-mp-who">
            <h1>{{ $member->first_name }} {{ $member->last_name }}@if($member->isPremium()) @include('team.partials.premium-badge') @endif</h1>
            <div class="ur-mp-id">{{ $member->dataid }}@if($member->experience) &middot; {{ $member->experience }}@endif</div>
            <div class="ur-mp-chips">
                <span class="ur-mp-chip {{ $statusClass }}"><i class="fa fa-circle" style="font-size:7px;"></i> {{ ucfirst($status) }}</span>
                @if($member->isAdmin())<span class="ur-mp-chip ur-mp-chip--dark"><i class="fa fa-shield"></i> Admin</span>@endif
                @if($member->is_private_member)<span class="ur-mp-chip ur-mp-chip--dark"><i class="fa fa-lock"></i> Private member</span>@endif
                @if($member->can_view_private)<span class="ur-mp-chip"><i class="fa fa-eye"></i> Can see private</span>@endif
                @if($member->can_view_originals)<span class="ur-mp-chip"><i class="fa fa-picture-o"></i> Original photos</span>@endif
            </div>
        </div>
        <div class="ur-mp-actions">
            <a class="ur-mp-btn ur-mp-btn--gold" href="{{ route('team.manage.proposals', ['matchmaker' => $member->id]) }}"><i class="fa fa-clipboard"></i> All proposals</a>
            @if($waDigits)<a class="ur-mp-btn ur-mp-btn--ghost" href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> WhatsApp</a>@endif
            <a class="ur-mp-btn ur-mp-btn--dark" href="{{ route('team.manage.members') }}#tm_row_{{ $member->dataid }}"><i class="fa fa-sliders"></i> Manage access</a>
        </div>
    </div>

    <div class="ur-mp-stats">
        <div class="ur-mp-stat"><span>Proposals added</span><b>{{ number_format($stats['total']) }}</b></div>
        <div class="ur-mp-stat"><span>Active proposals</span><b>{{ number_format($stats['active']) }}</b></div>
        <div class="ur-mp-stat"><span>Private proposals</span><b>{{ number_format($stats['private']) }}</b></div>
        <div class="ur-mp-stat"><span>Added this month</span><b>{{ number_format($stats['month']) }}</b></div>
        <div class="ur-mp-stat"><span>Successful matches</span><b>{{ number_format($stats['matches']) }}</b></div>
        <div class="ur-mp-stat"><span>Last proposal</span><b style="font-size:16px; line-height:1.3 !important;">{{ $stats['last'] ? \Carbon\Carbon::parse($stats['last'])->diffForHumans() : $dash }}</b>@if($stats['first'])<small>First: {{ \Carbon\Carbon::parse($stats['first'])->format('j M Y') }}</small>@endif</div>
    </div>

    <div class="ur-mp-grid">
        <div class="ur-mp-card">
            <h2><i class="fa fa-address-card-o"></i> Profile &amp; contact</h2>
            <dl class="ur-mp-dl">
                <dt>Full name</dt><dd>{{ $member->first_name }} {{ $member->last_name }}</dd>
                <dt>Member ID</dt><dd>{{ $member->dataid }}</dd>
                <dt>Email</dt><dd>{{ $member->email }}</dd>
                <dt>Mobile / WhatsApp</dt><dd>{{ $member->contact_mobile_number ?: $dash }}</dd>
                <dt>City</dt><dd>{{ $member->city ?: $dash }}</dd>
                <dt>Experience</dt><dd>{{ $member->experience ?: $dash }}</dd>
                <dt>Joined</dt><dd>{{ $member->created_at ? $member->created_at->format('j M Y') : $dash }}</dd>
                <dt>Approved</dt><dd>{{ $member->approved_at ? \Carbon\Carbon::parse($member->approved_at)->format('j M Y') : $dash }}</dd>
                <dt>About</dt><dd>@if($member->about_me)<p class="ur-mp-about">{{ $member->about_me }}</p>@else {{ $dash }} @endif</dd>
            </dl>
        </div>

        <div class="ur-mp-card">
            <h2><i class="fa fa-sliders"></i> Access, photos &amp; privacy</h2>
            <dl class="ur-mp-dl">
                <dt>Account status</dt><dd>{{ ucfirst($status) }}</dd>
                <dt>Role</dt><dd>{{ $member->isAdmin() ? 'Admin (sees everything)' : 'Team member' }}</dd>
                <dt>Photos</dt><dd>{{ $member->isAdmin() || $member->can_view_originals ? 'Original (no watermark)' : 'With watermark' }}</dd>
                <dt>Their proposals</dt><dd>{{ $member->is_private_member ? 'Private — hidden from other members' : 'Public' }}</dd>
                <dt>Private proposals</dt><dd>{{ $member->isAdmin() || $member->can_view_private ? 'Can see them' : 'Cannot see them' }}</dd>
                <dt>Watermark text</dt><dd>{{ $member->watermark_text ?: $dash }}</dd>
                <dt>Watermark style</dt><dd>{{ $member->watermark_style === 'corner' ? 'Corner' : 'Diagonal' }}</dd>
                <dt>Logo</dt><dd>@if($logoUrl)<img class="ur-mp-logo" src="{{ $logoUrl }}" alt="">@else {{ $dash }} @endif</dd>
            </dl>
            @unless($member->isAdmin())
                <p class="ur-mp-empty" style="margin-top:14px;">Change any of these from the <a href="{{ route('team.manage.members') }}#tm_row_{{ $member->dataid }}" style="font-weight:700;">Team Members</a> list.</p>
            @endunless
        </div>
    </div>

    <div class="ur-mp-card">
        <h2><i class="fa fa-clipboard"></i> Latest proposals <span style="margin-left:auto; font-size:12.5px; font-weight:600;"><a href="{{ route('team.manage.proposals', ['matchmaker' => $member->id]) }}">View all {{ number_format($stats['total']) }} &rarr;</a></span></h2>
        @if($recent->isEmpty())
            <p class="ur-mp-empty">This member has not added any proposals yet.</p>
        @else
            <div class="ur-mp-list">
                @foreach($recent as $proposal)
                    @php $age = !empty($proposal->birthday) ? date_diff(date_create($proposal->birthday), date_create('now'))->y : null; @endphp
                    <a class="ur-mp-prop" href="{{ route('team.manage.proposals', ['keyword' => $proposal->reference]) }}">
                        <img src="{{ $proposal->getProfileImage(true) }}" alt="" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
                        <span>
                            <b>{{ $proposal->reference }}@if($proposal->is_private) <span class="ur-mp-chip ur-mp-chip--dark" style="padding:2px 9px; font-size:10.5px;"><i class="fa fa-lock"></i> Private</span>@endif</b>
                            <small>{{ collect([ucfirst($proposal->gender), $age ? $age . ' yrs' : null, $proposal->profession, $proposal->lbl_city])->filter()->implode(' • ') }}</small>
                        </span>
                        <em>{{ ucfirst(str_replace('_', ' ', $proposal->profile_status ?? 'active')) }}<br>{{ $proposal->created_at->format('j M Y') }}</em>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
