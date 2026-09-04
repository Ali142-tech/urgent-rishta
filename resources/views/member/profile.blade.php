{{-- Guests can view a member's profile too (public browsing), so this can't
     assume the authenticated dashboard shell — only logged-in members get it,
     guests still get the marketing site layout. Whichever shell is used, its
     header/sidebar/topbar is untouched here — only the main-content below is. --}}
@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.master')
@section('dashboard-title', ($profile->first_name ?? 'Member') . "'s Profile")
@section('main-content')
<?php use App\User; ?>
@php
    $isVerified = $profile->photo_verification_status === 'verified';
    $galleryImages = json_decode($profile->getLightGalleryImages(), true) ?: [];
    $imageCount = $profile->getImageCount();
    $age = $profile->birthday ? date_diff(date_create($profile->birthday), date_create('now'))->y : null;
    $pronounTitle = $profile->gender === 'female' ? 'Her' : ($profile->gender === 'male' ? 'Him' : ($profile->first_name ?: 'Them'));
@endphp
<style>
    .ur-mp-page {
        --ur-green: #123A2E;
        --ur-green-dark: #0F2E24;
        --ur-gold: #C9974D;
        --ur-red: #B5674A;
        --ur-bg: #F6F4EF;
        --ur-border: #E7E2D6;
        --ur-text: #1C2321;
        --ur-text-muted: #6B7570;
        padding: 28px 0 60px;
        background: var(--ur-bg);
    }

    .ur-mp-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .ur-mp-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--ur-text-muted);
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .ur-mp-back:hover {
        color: var(--ur-green);
    }

    /* ---- Photo gallery ---- */
    .ur-mp-gallery__main {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 5;
        border-radius: 14px;
        overflow: hidden;
        background: #e9e6de;
        box-shadow: 0 10px 30px rgba(15, 46, 36, .1);
    }

    .ur-mp-gallery__bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(22px) saturate(1.1) brightness(.92);
        transform: scale(1.15);
    }

    .ur-mp-gallery__main img {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .ur-mp-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: var(--ur-green);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .2px;
        padding: 6px 12px;
        border-radius: 999px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
    }

    .ur-mp-badge--verified i {
        color: var(--ur-green);
    }

    .ur-mp-viewall {
        position: absolute;
        right: 14px;
        bottom: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 35, 33, .78);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 999px;
        border: 0;
        cursor: pointer;
    }

    .ur-mp-thumbs {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 8px;
    }

    .ur-mp-thumb {
        aspect-ratio: 1 / 1;
        border-radius: 8px;
        border: 2px solid transparent;
        background-color: var(--ur-bg);
        background-size: cover;
        background-position: center;
        cursor: pointer;
        padding: 0;
        transition: border-color .15s ease;
    }

    .ur-mp-thumb:hover {
        border-color: var(--ur-gold);
    }

    .ur-mp-thumb--more {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--ur-green);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    /* ---- Headline ---- */
    .ur-mp-headline {
        margin-top: 20px;
    }

    .ur-mp-headline h2 {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        color: var(--ur-text);
        margin: 0;
    }

    .ur-mp-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #FBF3E6;
        color: #8a6a2f;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid #EBD7AE;
    }

    .ur-mp-headline h3 {
        font-size: 15px;
        font-weight: 600;
        color: var(--ur-green);
        margin: 6px 0 0;
    }

    .ur-mp-facts {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        padding: 0;
        margin: 10px 0 0;
        font-size: 13px;
        color: var(--ur-text-muted);
    }

    .ur-mp-facts i {
        color: var(--ur-gold);
        margin-right: 5px;
    }

    /* ---- Verified box / meta / actions / privacy ---- */
    .ur-mp-verified-box {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 16px;
    }

    .ur-mp-verified-box i {
        color: var(--ur-green);
        font-size: 18px;
        margin-top: 2px;
    }

    .ur-mp-verified-box b {
        display: block;
        color: var(--ur-text);
        font-size: 13.5px;
    }

    .ur-mp-verified-box span {
        display: block;
        color: var(--ur-text-muted);
        font-size: 12.5px;
        margin-top: 2px;
    }

    .ur-mp-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 20px;
        margin-top: 16px;
        font-size: 13px;
        color: var(--ur-text);
        font-weight: 600;
    }

    .ur-mp-meta i {
        color: var(--ur-gold);
        margin-right: 6px;
    }

    .ur-mp-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 16px;
    }

    .ur-mp-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 46px;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 700;
        border: 1px solid var(--ur-green);
        cursor: pointer;
        text-decoration: none !important;
        transition: background-color .2s ease, color .2s ease;
    }

    .ur-mp-btn--solid {
        background: var(--ur-green);
        color: #fff !important;
    }

    .ur-mp-btn--solid:hover {
        background: var(--ur-green-dark);
    }

    .ur-mp-btn--outline {
        background: #fff;
        color: var(--ur-green) !important;
    }

    .ur-mp-btn--outline:hover {
        background: var(--ur-bg);
    }

    .ur-mp-privacy-note {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 10px;
        padding: 10px 14px;
        margin-top: 14px;
        font-size: 12px;
        color: var(--ur-text-muted);
    }

    .ur-mp-privacy-note i {
        color: var(--ur-gold);
    }

    /* ---- Generic left-column card ---- */
    .ur-mp-card {
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 18px 18px 20px;
        margin-top: 16px;
    }

    .ur-mp-card h4 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: var(--ur-text);
        margin: 0 0 12px;
    }

    .ur-mp-card p {
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--ur-text-muted);
        margin: 0;
    }

    .ur-mp-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 10px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ur-green);
        text-decoration: none;
    }

    .ur-mp-quicklist {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ur-mp-quicklist li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px dashed var(--ur-border);
        font-size: 13px;
    }

    .ur-mp-quicklist li:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .ur-mp-quicklist li span:first-child {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--ur-text-muted);
    }

    .ur-mp-quicklist li span:first-child i {
        color: var(--ur-gold);
        width: 16px;
        text-align: center;
    }

    .ur-mp-quicklist li b {
        color: var(--ur-text);
        font-weight: 600;
        text-align: right;
    }

    .ur-mp-checklist {
        list-style: none;
        padding: 0;
        margin: 0 0 14px;
    }

    .ur-mp-checklist li {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 0;
        font-size: 13px;
        color: #b9b2a2;
    }

    .ur-mp-checklist li.is-done {
        color: var(--ur-text);
    }

    .ur-mp-checklist li i {
        font-size: 15px;
    }

    .ur-mp-checklist li.is-done i {
        color: var(--ur-green);
    }

    .ur-mp-confidential {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--ur-bg);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        color: var(--ur-text);
    }

    .ur-mp-confidential i {
        color: var(--ur-gold);
    }

    /* ---- Right column detail cards ---- */
    .ur-mp-detail-card {
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 20px 22px 22px;
        margin-bottom: 20px;
    }

    .ur-mp-detail-card__title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 700;
        color: var(--ur-text);
        margin: 0 0 16px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .ur-mp-detail-card__title i {
        color: var(--ur-gold);
        font-size: 16px;
    }

    .ur-mp-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 18px;
    }

    .ur-mp-detail-grid.ur-mp-detail-grid--2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .ur-mp-detail-grid div span {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: var(--ur-text-muted);
        margin-bottom: 4px;
    }

    .ur-mp-detail-grid div b {
        font-size: 13.5px;
        color: var(--ur-text);
        font-weight: 600;
    }

    .ur-mp-note {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--ur-bg);
        border-radius: 10px;
        padding: 10px 14px;
        margin-top: 18px;
        font-size: 12px;
        color: var(--ur-text-muted);
    }

    .ur-mp-note i {
        color: var(--ur-gold);
    }

    /* ---- Bottom CTA bar ---- */
    .ur-mp-cta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 18px 22px;
        margin-top: 4px;
    }

    .ur-mp-cta__msg {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ur-mp-cta__msg i {
        color: var(--ur-green);
        font-size: 22px;
    }

    .ur-mp-cta__msg b {
        display: block;
        color: var(--ur-text);
        font-size: 14px;
    }

    .ur-mp-cta__msg span {
        display: block;
        color: var(--ur-text-muted);
        font-size: 12.5px;
        margin-top: 2px;
    }

    .ur-mp-cta__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ur-mp-cta__actions .ur-mp-btn {
        padding: 0 18px;
        width: auto;
    }

    @media (max-width: 991px) {
        .ur-mp-detail-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575px) {
        .ur-mp-detail-grid {
            grid-template-columns: 1fr;
        }

        .ur-mp-cta {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<section class="ur-mp-page">
    <div class="container">
        <div class="ur-mp-topbar">
            <a class="ur-mp-back" href="{{ url()->previous(route('searchresults')) }}"><i class="fa fa-angle-left"></i> Back to Results</a>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="ur-mp-gallery">
                    <div class="ur-mp-gallery__main">
                        <span class="ur-mp-gallery__bg" style="background-image:url('{{ $profile->getProfileImage() }}')"></span>
                        <img id="mp_main_photo" src="{{ $profile->getProfileImage() }}" alt="{{ $profile->first_name }}" />
                        @if($isVerified)
                            <span class="ur-mp-badge ur-mp-badge--verified"><i class="fa fa-check-circle"></i> Verified Profile</span>
                        @endif
                        @if(!empty($galleryImages))
                            <button type="button" class="ur-mp-viewall" onclick="showLightGallery($('#mp_main_photo'))"><i class="fa fa-th"></i> View All Photos ({{ $imageCount }})</button>
                        @endif
                    </div>
                    @if(count($galleryImages) > 1)
                        <div class="ur-mp-thumbs">
                            @foreach(array_slice($galleryImages, 0, 5) as $i => $img)
                                @if($i == 4 && count($galleryImages) > 5)
                                    <button type="button" class="ur-mp-thumb ur-mp-thumb--more" onclick="showLightGallery($('#mp_main_photo'))">+{{ count($galleryImages) - 4 }}</button>
                                @else
                                    <button type="button" class="ur-mp-thumb" onclick="mpSetMainPhoto('{{ $img['src'] }}')" style="background-image:url('{{ $img['thumb'] }}')"></button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="ur-mp-headline">
                    <h2>
                        @if($age){{ $age }} &bull; @endif{{ ucfirst($profile->gender ?: 'Member') }}@if(!empty($profile->height)) &bull; {{ $profile->height }}@endif
                        @if(!empty($profile->lbl_package))<span class="ur-mp-chip"><i class="fa fa-star"></i> {{ $profile->lbl_package }}</span>@endif
                    </h2>
                    @if(!empty($profile->profession))<h3>{{ $profile->profession }}</h3>@endif
                    <ul class="ur-mp-facts">
                        @if(!empty($profile->lbl_con_of_residence))<li><i class="fa fa-map-marker"></i>{{ $profile->lbl_city ? $profile->lbl_city.', ' : '' }}{{ $profile->lbl_con_of_residence }}</li>@endif
                        @if(!empty($profile->sect))<li><i class="fa fa-moon-o"></i>{{ $profile->sect }}</li>@endif
                        @if(!empty($profile->lbl_con_of_citizenship))<li><i class="fa fa-globe"></i>{{ $profile->lbl_con_of_citizenship }}</li>@endif
                    </ul>
                </div>

                @if($isVerified)
                    <div class="ur-mp-verified-box">
                        <i class="fa fa-shield"></i>
                        <div>
                            <b>This profile is verified</b>
                            <span>We have manually verified this profile for your safety and privacy.</span>
                        </div>
                    </div>
                @endif

                <div class="ur-mp-meta">
                    <span><i class="fa fa-id-badge"></i>ID: {{ $profile->dataid }}</span>
                    @if(!empty($profile->created_at))<span><i class="fa fa-calendar"></i>Joined: {{ date('M Y', strtotime($profile->created_at)) }}</span>@endif
                </div>

                <div class="ur-mp-actions" id="ur_mp_actions">
                    @guest
                        <a class="ur-mp-btn ur-mp-btn--solid" id="interest_{{$profile->dataid}}" onclick="return register_request();">
                            <i class="fa fa-heart"></i> <span>Send Interest</span>
                        </a>
                    @endguest
                    @auth
                        @if (User::retrieveUserObject()->inList($profile->dataid, 'interest'))
                            @php
                                $interest = User::retrieveUserObject()->getInterest($profile->dataid);
                            @endphp
                            <a class="ur-mp-btn ur-mp-btn--solid" id="interest_{{$profile->dataid}}" onclick="return {{$interest==-1? "false":"withdrawInterest($(this), 's')"}};">
                                @if ($interest==1)
                                    <span><i class="fa fa-heart"></i> Interest Accepted</span>
                                @elseif ($interest==-1)
                                    <span><i class="fa fa-heart"></i> Interest Declined</span>
                                @else
                                    <span><i class="fa fa-heart"></i> Interest Expressed</span>
                                @endif
                            </a>
                        @else
                            <a class="ur-mp-btn ur-mp-btn--solid" id="interest_{{$profile->dataid}}" onclick="return sendInterest($(this));">
                                <span><i class="fa fa-heart"></i> Send Interest</span>
                            </a>
                        @endif
                    @endauth

                    {{-- No relationship-manager chat exists yet — kept as a
                         static, clearly-labelled action for now rather than a
                         dead/misleading control. --}}
                    <a class="ur-mp-btn ur-mp-btn--outline" onclick="chatComingSoon();"><i class="fa fa-headphones"></i> Chat with Relationship Manager</a>

                    {{-- "Save profile" has no backing feature yet either (the
                         generic saved/followed-list read path is unfinished
                         elsewhere in the codebase) — same static treatment. --}}
                    <a class="ur-mp-btn ur-mp-btn--outline" onclick="saveProfileComingSoon();"><i class="fa fa-bookmark-o"></i> Save Profile</a>
                </div>

                <div class="ur-mp-privacy-note"><i class="fa fa-lock"></i> Contact information is private and will be shared only after mutual interest.</div>

                <div class="ur-mp-card">
                    <h4>Quick Overview</h4>
                    <ul class="ur-mp-quicklist">
                        @if($age)<li><span><i class="fa fa-birthday-cake"></i>Age</span><b>{{ $age }} Years</b></li>@endif
                        @if(!empty($profile->lbl_marital_status))<li><span><i class="fa fa-heart-o"></i>Marital Status</span><b>{{ $profile->lbl_marital_status }}</b></li>@endif
                        @if(!empty($profile->height))<li><span><i class="fa fa-arrows-v"></i>Height</span><b>{{ $profile->height }}</b></li>@endif
                        @if(!empty($profile->lbl_education))<li><span><i class="fa fa-graduation-cap"></i>Education</span><b>{{ $profile->lbl_education }}</b></li>@endif
                        @if(!empty($profile->profession))<li><span><i class="fa fa-briefcase"></i>Profession</span><b>{{ $profile->profession }}</b></li>@endif
                        @if(!empty($profile->lbl_con_of_residence))<li><span><i class="fa fa-map-marker"></i>Location</span><b>{{ $profile->lbl_con_of_residence }}</b></li>@endif
                        @if(!empty($profile->sect))<li><span><i class="fa fa-moon-o"></i>Sect</span><b>{{ $profile->sect }}</b></li>@endif
                        @if(!empty($profile->lbl_con_of_citizenship))<li><span><i class="fa fa-globe"></i>Nationality</span><b>{{ $profile->lbl_con_of_citizenship }}</b></li>@endif
                    </ul>
                </div>

                @if(!empty($profile->intro))
                    <div class="ur-mp-card">
                        <h4>About {{ $pronounTitle }}</h4>
                        <p>{{ $profile->intro }}</p>
                    </div>
                @endif

                @if(!empty($profile->rgen_req))
                    <div class="ur-mp-card">
                        <h4>Looking For</h4>
                        <p>{{ $profile->rgen_req }}</p>
                        <a href="#ur_mp_partner_pref" class="ur-mp-link">View Partner Preferences <i class="fa fa-angle-right"></i></a>
                    </div>
                @endif

                <div class="ur-mp-card">
                    <h4>Verification &amp; Privacy</h4>
                    <ul class="ur-mp-checklist">
                        <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Profile Manually Verified</li>
                        <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Photo Verified</li>
                        <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Information Verified</li>
                    </ul>
                    <div class="ur-mp-confidential"><i class="fa fa-lock"></i> 100% Confidential</div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="ur-mp-detail-card">
                    <h3 class="ur-mp-detail-card__title"><i class="fa fa-user"></i> Basic Details</h3>
                    <div class="ur-mp-detail-grid">
                        @if($age)<div><span>Age</span><b>{{ $age }} Years</b></div>@endif
                        @if(!empty($profile->height))<div><span>Height</span><b>{{ $profile->height }}</b></div>@endif
                        @if(!empty($profile->lbl_marital_status))<div><span>Marital Status</span><b>{{ $profile->lbl_marital_status }}</b></div>@endif
                        @if(!empty($profile->lbl_religion))<div><span>Religion</span><b>{{ $profile->lbl_religion }}</b></div>@endif
                        @if(!empty($profile->sect))<div><span>Sect</span><b>{{ $profile->sect }}</b></div>@endif
                        @if(!empty($profile->lbl_con_of_citizenship))<div><span>Nationality</span><b>{{ $profile->lbl_con_of_citizenship }}</b></div>@endif
                        @if(!empty($profile->lbl_con_of_residence))<div><span>Location</span><b>{{ $profile->lbl_con_of_residence }}</b></div>@endif
                        @if(!empty($profile->lbl_mother_tongue))<div><span>Mother Tongue</span><b>{{ $profile->lbl_mother_tongue }}</b></div>@endif
                    </div>
                </div>

                @if(!empty($profile->lbl_education) || !empty($profile->profession) || !empty($profile->salary))
                    <div class="ur-mp-detail-card">
                        <h3 class="ur-mp-detail-card__title"><i class="fa fa-graduation-cap"></i> Education &amp; Career</h3>
                        <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                            @if(!empty($profile->lbl_education))<div><span>Qualification</span><b>{{ $profile->lbl_education }}</b></div>@endif
                            @if(!empty($profile->profession))<div><span>Profession</span><b>{{ $profile->profession }}</b></div>@endif
                            @if(!empty($profile->salary))<div><span>Annual Income</span><b>{{ $profile->salary }}</b></div>@endif
                        </div>
                    </div>
                @endif

                @if(!empty($profile->father) || !empty($profile->mother) || !empty($profile->brothers_count) || !empty($profile->sisters_count) || !empty($profile->father_profession))
                    <div class="ur-mp-detail-card">
                        <h3 class="ur-mp-detail-card__title"><i class="fa fa-users"></i> Family Background</h3>
                        <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                            @if(!empty($profile->father))<div><span>Father</span><b>{{ $profile->father }}</b></div>@endif
                            @if(!empty($profile->father_profession))<div><span>Father's Occupation</span><b>{{ $profile->father_profession }}</b></div>@endif
                            @if(!empty($profile->mother))<div><span>Mother</span><b>{{ $profile->mother }}</b></div>@endif
                            @if(!empty($profile->brothers_count))<div><span>Brother(s)</span><b>{{ $profile->brothers_count }}</b></div>@endif
                            @if(!empty($profile->sisters_count))<div><span>Sister(s)</span><b>{{ $profile->sisters_count }}</b></div>@endif
                        </div>
                        <div class="ur-mp-note"><i class="fa fa-lock"></i> We respect your privacy. Detailed family information will be shared after mutual interest.</div>
                    </div>
                @endif

                <div class="ur-mp-detail-card" id="ur_mp_partner_pref">
                    <h3 class="ur-mp-detail-card__title"><i class="fa fa-users"></i> Partner Preference</h3>
                    <div class="ur-mp-detail-grid">
                        @if(!empty($profile->rage_min) || !empty($profile->rage_max))<div><span>Preferred Age</span><b>{{ $profile->rage_min }}{{ !empty($profile->rage_max) ? ' - '.$profile->rage_max : ($profile->rage_min ? '+' : '') }}</b></div>@endif
                        @if(!empty($profile->rheight))<div><span>Preferred Height</span><b>{{ $profile->rheight }}</b></div>@endif
                        @if(!empty($profile->lbl_rmarital_status))<div><span>Marital Status</span><b>{{ $profile->lbl_rmarital_status }}</b></div>@endif
                        @if(!empty($profile->lbl_reducation))<div><span>Education</span><b>{{ $profile->lbl_reducation }}</b></div>@endif
                        @if(!empty($profile->rprofession))<div><span>Profession</span><b>{{ $profile->rprofession }}</b></div>@endif
                        @if(!empty($profile->lbl_rreligion))<div><span>Religious Preference</span><b>{{ $profile->lbl_rreligion }}</b></div>@endif
                        @if(!empty($profile->lbl_rcaste) || !empty($profile->rsect))<div><span>Caste / Sect</span><b>{{ $profile->lbl_rcaste }}{{ !empty($profile->rsect) ? ' / '.$profile->rsect : '' }}</b></div>@endif
                        @if(!empty($profile->lbl_rmother_tongue))<div><span>Mother Tongue</span><b>{{ $profile->lbl_rmother_tongue }}</b></div>@endif
                        @if(!empty($profile->lbl_rcon_pref) || !empty($profile->lbl_rcon_of_residence))<div><span>Location Preference</span><b>{{ $profile->lbl_rcon_pref ?: $profile->lbl_rcon_of_residence }}</b></div>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="ur-mp-cta">
            <div class="ur-mp-cta__msg">
                <i class="fa fa-shield"></i>
                <div>
                    <b>Only serious individuals and families.</b>
                    <span>We are committed to meaningful and successful matches.</span>
                </div>
            </div>
            <div class="ur-mp-cta__actions">
                <a class="ur-mp-btn ur-mp-btn--solid" onclick="document.getElementById('ur_mp_actions').scrollIntoView({behavior:'smooth', block:'center'});"><i class="fa fa-heart"></i> Send Interest</a>
                <a class="ur-mp-btn ur-mp-btn--outline" onclick="chatComingSoon();"><i class="fa fa-headphones"></i> Chat with Manager</a>
                <a class="ur-mp-btn ur-mp-btn--outline" onclick="saveProfileComingSoon();"><i class="fa fa-bookmark-o"></i> Save Profile</a>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function mpSetMainPhoto(src) {
        var mainImg = document.getElementById('mp_main_photo');
        if (!mainImg) return;
        mainImg.src = src;
        var bg = mainImg.previousElementSibling;
        if (bg && bg.classList.contains('ur-mp-gallery__bg')) {
            bg.style.backgroundImage = "url('" + src + "')";
        }
    }

    function chatComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Live chat with a relationship manager isn\'t available yet. Please reach out via our Contact page in the meantime and we\'ll get back to you personally.',
            'icon': 'info',
        });
    }

    function saveProfileComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Saving profiles for later is on its way. For now, use Send Interest to keep in touch with this profile.',
            'icon': 'info',
        });
    }

    $(document).ready(function() {
        $("#mp_main_photo").on('click', function() {
            showLightGallery($(this));
        });
    });
</script>
@endsection
