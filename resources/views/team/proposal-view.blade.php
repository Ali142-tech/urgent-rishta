{{--
    Full-detail read view for a single proposal — rendered inside the Team
    Dashboard shell (layouts.team.dashboard), unlike member/profile.blade.php
    (layouts.dashboard, the Member Dashboard shell). See TeamController::
    viewProposal(). Linked from every Team Dashboard card's "View Full
    Profile"/"View file" action (member-card.blade.php, search-result-
    card.blade.php) instead of member/profile/{dataid}, so a team member
    stays inside the Team Dashboard instead of being dropped into the
    Member Dashboard's own navigation.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', trim($member->first_name . ' ' . $member->last_name) ?: $member->dataid)
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-pv-back { display: inline-flex; align-items: center; gap: 6px; color: #6B7570; font-size: 13px; font-weight: 600; text-decoration: none; margin-bottom: 16px; }
    .ur-pv-back:hover { color: #123A2E; }

    .ur-pv-head { display: flex; gap: 20px; background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
    .ur-pv-head__photo { flex: 0 0 160px; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; background: #F6F4EF; }
    .ur-pv-head__photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ur-pv-head__body { flex: 1; min-width: 0; }
    .ur-pv-head__top { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px; }
    .ur-pv-head__name { font-family: 'Playfair Display', serif; font-weight: 700; color: #123A2E; font-size: 22px; margin: 0; }
    .ur-pv-head__id { color: #6B7570; font-size: 13px; margin: 0 0 8px; }
    .ur-pv-head__verified { display: inline-flex; align-items: center; gap: 4px; background: #1E7A46; color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .ur-pv-head__meta { font-size: 13.5px; color: #6B7570; margin-bottom: 10px; }
    .ur-pv-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
    .ur-pv-badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: .2px; }
    .ur-pv-badge--premium { background: #FBF0DA; color: #8A6218; }
    .ur-pv-badge--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-pv-badge--abroad { background: #E4EEF7; color: #2C5F8A; }
    .ur-pv-badge--highlight { background: #FBE4E9; color: #A23B57; }
    .ur-pv-head__owner { font-size: 12.5px; color: #6B7570; margin-bottom: 14px; }
    .ur-pv-head__owner i { color: #C9974D; margin-right: 4px; }
    .ur-pv-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .ur-pv-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; border-radius: 999px; padding: 8px 16px; font-size: 12.5px; font-weight: 700; cursor: pointer; text-decoration: none; }
    .ur-pv-btn:hover { background: #F6F4EF; border-color: #C9974D; color: #123A2E; text-decoration: none; }
    .ur-pv-btn--dark { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-pv-btn--dark:hover { background: #0F2E24; color: #fff; }
    .ur-pv-btn--whatsapp { background: #25D366; border-color: #25D366; color: #fff; }
    .ur-pv-btn--whatsapp:hover { background: #1DA851; color: #fff; }

    .ur-pv-section { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; margin-bottom: 18px; }
    .ur-pv-section h2 { font-family: 'Playfair Display', serif; font-weight: 700; color: #123A2E; font-size: 16px; margin: 0 0 14px; }
    .ur-pv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px 20px; }
    .ur-pv-grid div span { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #9AA5A0; margin-bottom: 3px; }
    .ur-pv-grid div b { color: #1C2321; font-size: 13.5px; font-weight: 600; }
    .ur-pv-description { font-size: 13.5px; color: #1C2321; line-height: 1.6; white-space: pre-line; }
    .ur-pv-gallery { display: flex; flex-wrap: wrap; gap: 10px; }
    .ur-pv-gallery img { width: 110px; height: 110px; object-fit: cover; border-radius: 10px; border: 1px solid #E7E2D6; }

    @media (max-width: 560px) {
        .ur-pv-head { flex-direction: column; }
        .ur-pv-head__photo { width: 100%; height: 220px; }
    }
</style>

@php
    $category = strtolower($member->profile_category ?? '');
    $highlight = strtolower($member->presentation_highlight ?? '');
    $isAbroad = strtolower($member->looking_from ?? '') === 'abroad';
@endphp

<a href="{{ url()->previous() }}" class="ur-pv-back"><i class="fa fa-arrow-left"></i> Back to results</a>

<div class="ur-pv-head">
    <div class="ur-pv-head__photo">
        <img src="{{ $member->getProfileImage() }}" alt="{{ $member->first_name }}" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
    </div>
    <div class="ur-pv-head__body">
        <div class="ur-pv-head__top">
            <h1 class="ur-pv-head__name">{{ trim($member->first_name . ' ' . $member->last_name) ?: $member->dataid }}</h1>
            @if($member->photo_verification_status === 'verified')
                <span class="ur-pv-head__verified"><i class="fa fa-check-circle"></i> Verified</span>
            @endif
        </div>
        <p class="ur-pv-head__id">ID: {{ $member->dataid }}</p>
        <div class="ur-pv-head__meta">
            {{ ucfirst($member->gender) }}
            @if(!empty($member->birthday)) &bull; {{ date_diff(date_create($member->birthday), date_create('now'))->y }} yrs @endif
            @if(!empty($member->height)) &bull; {{ $member->height }} @endif
            @if(!empty($member->lbl_marital_status)) &bull; {{ $member->lbl_marital_status }} @endif
            @if(!empty($member->lbl_city) || !empty($member->lbl_con_of_residence)) &bull; {{ $member->lbl_city ?: $member->lbl_con_of_residence }} @endif
        </div>

        @if($category === 'premium' || $category === 'royal' || $isAbroad || $highlight === 'highly attractive')
        <div class="ur-pv-badges">
            @if($category === 'premium')<span class="ur-pv-badge ur-pv-badge--premium">Premium Profile</span>@endif
            @if($category === 'royal')<span class="ur-pv-badge ur-pv-badge--royal">Royal Profile</span>@endif
            @if($isAbroad)<span class="ur-pv-badge ur-pv-badge--abroad">Abroad</span>@endif
            @if($highlight === 'highly attractive')<span class="ur-pv-badge ur-pv-badge--highlight">Highly Attractive</span>@endif
        </div>
        @endif

        <div class="ur-pv-head__owner">
            <i class="fa fa-user-circle"></i> Owner: {{ $member->added_by_name }}
            @if($member->added_by_dataid) &bull; {{ $member->added_by_dataid }} @endif
            @if($member->added_by_experience) &bull; {{ $member->added_by_experience }} @endif
        </div>

        <div class="ur-pv-actions">
            @if($member->team_card_role === 'own')
                @if($member->has_partner_preference)
                    <a class="ur-pv-btn ur-pv-btn--dark" href="{{ route('team.matches.show', $member->dataid) }}">
                        <i class="fa fa-magic"></i> AI Match
                    </a>
                @endif
                <a class="ur-pv-btn" href="{{ route('team.proposals.edit', $member->dataid) }}">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a class="ur-pv-btn" href="{{ route('team.proposals.photos', $member->dataid) }}">
                    <i class="fa fa-camera"></i> Manage Photos
                </a>
            @endif
            <a class="ur-pv-btn ur-pv-btn--whatsapp" href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener">
                <i class="fa fa-whatsapp"></i> Share
            </a>
        </div>
    </div>
</div>

<div class="ur-pv-section">
    <h2>Personal Information</h2>
    <div class="ur-pv-grid">
        @if(!empty($member->profile_for))<div><span>Profile For</span><b>{{ $member->profile_for }}</b></div>@endif
        <div><span>Gender</span><b>{{ ucfirst($member->gender) }}</b></div>
        @if(!empty($member->lbl_marital_status))<div><span>Marital Status</span><b>{{ $member->lbl_marital_status }}</b></div>@endif
        @if(!empty($member->family_status))<div><span>Family Status</span><b>{{ $member->family_status }}</b></div>@endif
        @if(!empty($member->family_residence))<div><span>Family Residence</span><b>{{ $member->family_residence }}</b></div>@endif
        @if(!empty($member->family_values))<div><span>Family Values</span><b>{{ $member->family_values }}</b></div>@endif
        @if(!empty($member->income))<div><span>Income</span><b>{{ $member->income }}</b></div>@endif
        @if(!empty($member->property_financial_status))<div><span>Property / Financial Status</span><b>{{ $member->property_financial_status }}</b></div>@endif
    </div>
</div>

<div class="ur-pv-section">
    <h2>Religion &amp; Education</h2>
    <div class="ur-pv-grid">
        @if(!empty($member->lbl_religion))<div><span>Religion</span><b>{{ $member->lbl_religion }}</b></div>@endif
        @if(!empty($member->lbl_caste) || !empty($member->sect))<div><span>Caste / Sect</span><b>{{ $member->lbl_caste }} @if(!empty($member->sect)) / {{ $member->sect }} @endif</b></div>@endif
        @if(!empty($member->lbl_mother_tongue))<div><span>Mother Tongue</span><b>{{ $member->lbl_mother_tongue }}</b></div>@endif
        @if(!empty($member->lbl_education))<div><span>Qualification</span><b>{{ $member->lbl_education }}</b></div>@endif
        @if(!empty($member->profession))<div><span>Profession</span><b>{{ $member->profession }}</b></div>@endif
    </div>
</div>

<div class="ur-pv-section">
    <h2>Residence</h2>
    <div class="ur-pv-grid">
        @if(!empty($member->lbl_city))<div><span>City</span><b>{{ $member->lbl_city }}</b></div>@endif
        @if(!empty($member->current_city))<div><span>Current City</span><b>{{ $member->current_city }}</b></div>@endif
        @if(!empty($member->lbl_con_of_residence))<div><span>Country of Residence</span><b>{{ $member->lbl_con_of_residence }}</b></div>@endif
        @if(!empty($member->lbl_con_of_citizenship))<div><span>Citizenship</span><b>{{ $member->lbl_con_of_citizenship }}</b></div>@endif
        @if(!empty($member->immigration_status))<div><span>Immigration Status</span><b>{{ $member->immigration_status }}</b></div>@endif
    </div>
</div>

@if(!empty($canViewContactInfo))
<div class="ur-pv-section">
    <h2>Contact Information</h2>
    <div class="ur-pv-grid">
        @if($contactUnlockSettings['unlock_name'] ?? true)<div><span>Full Name</span><b>{{ $member->first_name }} {{ $member->last_name }}</b></div>@endif
        @if(($contactUnlockSettings['unlock_email'] ?? true) && !empty($member->email))<div><span>Email</span><b>{{ $member->email }}</b></div>@endif
        @if(($contactUnlockSettings['unlock_phone'] ?? true) && !empty($member->contact_mobile_number))<div><span>Phone</span><b>{{ $member->contact_mobile_number }}</b></div>@endif
        @if(!empty($member->address) && ($contactUnlockSettings['unlock_address'] ?? true))<div><span>Address</span><b>{{ $member->address }}</b></div>@endif
    </div>
</div>
@endif

@if(!empty($member->profile_description))
<div class="ur-pv-section">
    <h2>About</h2>
    <p class="ur-pv-description">{{ $member->profile_description }}</p>
</div>
@endif

@if($preference)
<div class="ur-pv-section">
    <h2>Partner Requirements</h2>
    <div class="ur-pv-grid">
        @if(!empty($preference->age_min) || !empty($preference->age_max))<div><span>Age Range</span><b>{{ $preference->age_min }} - {{ $preference->age_max }}</b></div>@endif
        @if(!empty($preference->height))<div><span>Height</span><b>{{ $preference->height }}</b></div>@endif
        @if(!empty($preferenceLabels['marital_status']))<div><span>Marital Status</span><b>{{ $preferenceLabels['marital_status'] }}</b></div>@endif
        @if(!empty($preferenceLabels['preferred_country']))<div><span>Preferred Country</span><b>{{ $preferenceLabels['preferred_country'] }}</b></div>@endif
        @if(!empty($preferenceLabels['country']))<div><span>Country</span><b>{{ $preferenceLabels['country'] }}</b></div>@endif
        @if(!empty($preferenceLabels['city']))<div><span>City</span><b>{{ $preferenceLabels['city'] }}</b></div>@endif
        @if(!empty($preferenceLabels['religion']))<div><span>Religion</span><b>{{ $preferenceLabels['religion'] }}</b></div>@endif
        @if(!empty($preferenceLabels['caste']))<div><span>Caste</span><b>{{ $preferenceLabels['caste'] }}</b></div>@endif
        @if(!empty($preferenceLabels['education']))<div><span>Education</span><b>{{ $preferenceLabels['education'] }}</b></div>@endif
        @if(!empty($preference->profession))<div><span>Profession</span><b>{{ $preference->profession }}</b></div>@endif
        @if(!empty($preferenceLabels['mother_tongue']))<div><span>Mother Tongue</span><b>{{ $preferenceLabels['mother_tongue'] }}</b></div>@endif
    </div>
    @if(!empty($preference->general_requirement))
        <p class="ur-pv-description" style="margin-top:14px;">{{ $preference->general_requirement }}</p>
    @endif
</div>
@endif

@php
    // Full-size sources for the lightbox (thumbnails only for the strip).
    $galleryFull = json_decode($member->getLightGalleryImages(false), true) ?: [];
    $galleryFull = array_values(array_filter($galleryFull, fn ($g) => file_exists(public_path($g['src']))));
@endphp
@if(count($galleryFull) > 0)
<div class="ur-pv-section">
    <h2>Photos</h2>
    <div class="ur-pv-gallery">
        @foreach($galleryFull as $gi => $g)
            <img src="{{ file_exists(public_path($g['thumb'])) ? $g['thumb'] : $g['src'] }}" alt="" loading="lazy" data-full="{{ $g['src'] }}" data-index="{{ $gi }}" style="cursor:zoom-in;">
        @endforeach
    </div>
</div>
@endif

<div id="urPvLightbox" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.88); align-items:center; justify-content:center;">
    <button type="button" id="urPvLbClose" aria-label="Close" style="position:absolute; top:16px; right:22px; background:none; border:0; color:#fff; font-size:34px; line-height:1; cursor:pointer;">&times;</button>
    <button type="button" id="urPvLbPrev" aria-label="Previous" style="position:absolute; left:18px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:0; color:#fff; font-size:26px; width:44px; height:44px; border-radius:50%; cursor:pointer;">&#8249;</button>
    <img id="urPvLbImg" src="" alt="" style="max-width:92vw; max-height:90vh; object-fit:contain; border-radius:8px;">
    <button type="button" id="urPvLbNext" aria-label="Next" style="position:absolute; right:18px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:0; color:#fff; font-size:26px; width:44px; height:44px; border-radius:50%; cursor:pointer;">&#8250;</button>
</div>
<script>
(function () {
    var box = document.getElementById('urPvLightbox');
    var img = document.getElementById('urPvLbImg');
    var srcs = [];
    document.querySelectorAll('.ur-pv-gallery img').forEach(function (el) { srcs.push(el.getAttribute('data-full')); });
    var head = document.querySelector('.ur-pv-head__photo img');
    var cur = 0;
    function show(i) {
        if (!srcs.length) return;
        cur = (i + srcs.length) % srcs.length;
        img.src = srcs[cur];
        box.style.display = 'flex';
    }
    function hide() { box.style.display = 'none'; }
    document.querySelectorAll('.ur-pv-gallery img').forEach(function (el, i) { el.addEventListener('click', function () { show(i); }); });
    if (head && srcs.length) { head.style.cursor = 'zoom-in'; head.addEventListener('click', function () { show(0); }); }
    document.getElementById('urPvLbClose').onclick = hide;
    document.getElementById('urPvLbPrev').onclick = function (e) { e.stopPropagation(); show(cur - 1); };
    document.getElementById('urPvLbNext').onclick = function (e) { e.stopPropagation(); show(cur + 1); };
    box.addEventListener('click', function (e) { if (e.target === box) hide(); });
    document.addEventListener('keydown', function (e) {
        if (box.style.display === 'none') return;
        if (e.key === 'Escape') hide();
        if (e.key === 'ArrowLeft') show(cur - 1);
        if (e.key === 'ArrowRight') show(cur + 1);
    });
})();
</script>
@endsection
