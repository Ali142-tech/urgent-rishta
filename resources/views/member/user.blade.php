@extends('layouts.dashboard')
@section('dashboard-title', 'My Profile')
@push('styles')
<link rel="stylesheet" href="/css/ur-profile.css?v={{ filemtime(public_path('css/ur-profile.css')) }}">
@endpush
@section('main-content')
@php
    // Same "Match %"-style scorer used on member/profile.blade.php's
    // Compatibility tab (viewing OTHER members) — not a separate algorithm.
    $isVerified = $profile->photo_verification_status === 'verified';
    $age = $profile->birthday ? date_diff(date_create($profile->birthday), date_create('now'))->y : null;
@endphp
<style>
    .ur-profile-page {
        --ur-green: #123A2E;
        --ur-green-dark: #0F2E24;
        --ur-gold: #C9974D;
        --ur-red: #B5674A;
        --ur-bg: #F6F4EF;
        --ur-border: #E7E2D6;
        --ur-text: #1C2321;
        --ur-text-muted: #6B7570;
    }

    /* Bootstrap's plain .container caps out around ~1140px and centers
       itself — fine on the old 2-column layout, but with the sidebar
       column added the page now reads as squeezed into the middle with
       huge dead space left/right, since the dashboard shell already gives
       this page much more width to work with. Same fix already applied on
       the Search Profiles page. Capped (not 100%) so the 3 columns don't
       stretch absurdly wide on ultra-wide monitors. */
    .ur-profile-page .container {
        max-width: 1760px;
    }

    /* ---- Tabs (grouping the existing sections below without moving them
       in the DOM — see data-mp-group on each) ---- */
    .ur-mp-tabs {
        border-bottom: 1px solid var(--ur-border);
        margin: 4px 0 20px;
        padding-left: 12px;
        flex-wrap: nowrap;
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .ur-mp-tabs::-webkit-scrollbar {
        display: none;
    }

    /* !important here because the site also loads global-style-pink.css,
       whose default link color otherwise bleeds through onto these plain
       <a> tabs instead of the site's actual green/gold brand colors. */
    .ur-mp-tabs .nav-link {
        white-space: nowrap;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--ur-text-muted) !important;
        font-size: 13.5px;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 0;
        cursor: pointer;
    }

    .ur-mp-tabs .nav-link.active {
        color: var(--ur-green) !important;
        border-bottom-color: var(--ur-gold);
    }

    .ur-mp-tabs .nav-link:hover {
        color: var(--ur-green) !important;
    }

    /* ---- Ring (same technique as member/profile.blade.php's Compatibility
       tab, kept under this page's own class names) ---- */
    .ur-mp-ring {
        flex: 0 0 auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: conic-gradient(var(--ur-gold) calc(var(--pct) * 1%), var(--ur-border) 0);
    }

    .ur-mp-ring__hole {
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--ur-green);
    }

    .ur-mp-ring--lg {
        width: 96px;
        height: 96px;
    }

    .ur-mp-ring--lg .ur-mp-ring__hole {
        width: 76px;
        height: 76px;
        font-size: 19px;
    }

    .ur-mp-compat-full {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px dashed var(--ur-border);
    }

    .ur-mp-compat-full__text b {
        display: block;
        font-size: 16px;
        color: var(--ur-text);
    }

    .ur-mp-compat-full__text span {
        display: block;
        font-size: 13px;
        color: var(--ur-text-muted);
        margin-top: 3px;
    }

    .ur-mp-checklist {
        list-style: none;
        padding: 0;
        margin: 0;
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

    /* ---- Reused "detail card" wrapper for the new Looking For /
       Compatibility cards, matching member/profile.blade.php ---- */
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

    .ur-mp-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ur-green);
        text-decoration: none;
    }

    /* Compact label/value grid replacing the old wide <table> display for
       each section's read-only view (the #info_X divs) — same pattern as
       member/profile.blade.php. The #edit_X forms next to each are
       untouched. */
    /* auto-fit + minmax (not a fixed repeat(3,1fr)) so the number of
       columns adapts to whatever width this card actually has — it's now
       often one of 2-3 side-by-side cards (see .ur-mp-tab-grid), not
       always full-width, so a hard-coded 3 columns was forcing values
       like "Gender" to overflow past the card edge instead of wrapping. */
    .ur-mp-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 14px 18px;
    }

    .ur-mp-detail-grid.ur-mp-detail-grid--2 {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }

    .ur-mp-detail-grid > div {
        /* Grid items default to min-width:auto, which stops them shrinking
           below their content's intrinsic width — the actual cause of the
           overflow. This lets long values wrap/truncate inside their own
           column instead of pushing the card wider than its container. */
        min-width: 0;
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
        overflow-wrap: break-word;
    }

    .ur-mp-detail-empty {
        color: #b9b2a2 !important;
        font-style: italic;
        font-weight: 500 !important;
    }

    /* Lays related sections (About: Basic Info/Education/Physical Attrs;
       More Details: Residency/Spiritual/Permanent Address) side by side as
       compact cards, matching the mockup, instead of stacking each one
       full-width. Each .feature card inside keeps toggling independently
       via data-mp-group — this is just a wrapper around already-adjacent
       elements. */
    .ur-mp-tab-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 0 16px;
        align-items: start;
    }

    .ur-mp-tab-grid .feature.feature--boxed-border {
        margin-bottom: 16px !important;
        height: 100%;
    }

    /* The edit-mode forms (#edit_X, revealed by the pencil icon) still use
       the old Bootstrap .row/.col-md-6 two-column layout that was built
       for a full-width table row. Bootstrap's col-*-N sizes as a
       percentage of its OWN container, not the viewport — so col-md-6
       here computes to 50% of one of these narrow ~240-350px tab-grid
       cards, squashing every field into ~100px. That's what made editing
       look broken/cramped. Stack every field full-width inside these
       narrower cards instead. */
    .ur-mp-tab-grid .ur-mp-tabgroup {
        min-width: 0;
    }
    .ur-mp-tab-grid form.form-default .row > [class*="col-"] {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .ur-mp-tab-grid form.form-default .form-group {
        margin-bottom: 12px;
    }
    /* Date-of-birth's day/month/year selects are the one spot that's
       meant to stay 3-across on one line — give them an even split
       instead of each shrinking to its own text width. */
    .ur-mp-tab-grid form.form-default select[style*="width: auto"] {
        width: 32% !important;
    }
    .ur-mp-tab-grid form.form-default select[style*="width: auto"] + select[style*="width: auto"] {
        margin-left: 2%;
    }

    @media (max-width: 767.98px) {
        .ur-mp-tab-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .ur-mp-detail-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .ur-mp-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ---- Own-profile photo/headline (left column) — matches
       member/profile.blade.php's gallery/headline treatment: rectangular
       photo, plain background, dark text, no colored card wrapper. Only
       the photo box itself (#show_img) is unique to this page since it's
       also the upload target; everything else reuses the same classes. ---- */
    /* Header row: photo (fixed width) + name/details, side by side — matches
       the mockup's actual structure (one row spanning the full main-content
       width, tabs below it), not a persistent side column running the full
       page height. */
    .ur-mp-header {
        display: flex;
        gap: 24px;
        align-items: flex-start;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
    }

    .ur-mp-header__photo {
        flex: 0 0 200px;
        position: relative;
        aspect-ratio: 4 / 5;
        border-radius: 14px;
        overflow: hidden;
        background: #e9e6de;
        box-shadow: 0 6px 18px rgba(15, 46, 36, .1);
    }

    .ur-mp-header__info {
        flex: 1 1 auto;
        min-width: 0;
        padding-top: 4px;
    }

    .ur-mp-header__badges {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .ur-mp-id-chip {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .3px;
        color: var(--ur-text-muted);
    }

    .ur-mp-badge-inline {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .ur-mp-badge-inline--verified {
        background: #E7F5EC;
        color: #1E7A46;
    }

    .ur-mp-match-score {
        margin-left: auto;
        font-size: 15px;
        font-weight: 700;
        color: var(--ur-gold);
    }

    .ur-mp-header__name {
        font-size: 22px;
        font-weight: 700;
        color: var(--ur-text);
        margin: 0 0 10px;
    }

    .ur-mp-facts-stacked {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ur-mp-facts-stacked li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--ur-text-muted);
        padding: 4px 0;
    }

    .ur-mp-facts-stacked i {
        color: var(--ur-gold);
        width: 14px;
        text-align: center;
    }

    @media (max-width: 575.98px) {
        .ur-mp-header {
            flex-direction: column;
        }

        .ur-mp-header__photo {
            flex-basis: auto;
            width: 100%;
            max-width: 220px;
            margin: 0 auto;
        }
    }


    .ur-mp-own-photo {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
    }

    .ur-mp-photo-edit {
        position: absolute;
        right: 12px;
        bottom: 12px;
        z-index: 2;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--ur-gold);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .18);
    }

    .ur-mp-photo-edit:hover {
        background: #B07C3D;
    }


    /* ---- Right sidebar (Matchmaker/Profile Status/Privacy/Report) —
       matches member/profile.blade.php's sidebar; no relationship-manager
       assignment or report/block feature exists yet, so those two cards
       are static, same as there. ---- */
    .ur-mp-side-card {
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 18px 18px 20px;
        margin-bottom: 16px;
    }

    .ur-mp-side-card h4 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: var(--ur-text);
        margin: 0 0 12px;
    }

    .ur-mp-side-card p {
        font-size: 13px;
        line-height: 1.6;
        color: var(--ur-text-muted);
        margin: 0 0 10px;
    }

    .ur-mp-matchmaker {
        background: var(--ur-green);
        color: #fff;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
    }

    .ur-mp-matchmaker h4 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: rgba(255, 255, 255, .8);
        margin: 0 0 14px;
    }

    .ur-mp-matchmaker__who {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ur-mp-matchmaker__avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: var(--ur-gold);
        color: var(--ur-green-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex: 0 0 auto;
    }

    .ur-mp-matchmaker__who b {
        display: block;
        font-size: 14px;
    }

    .ur-mp-matchmaker__who span {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, .65);
        margin-top: 1px;
    }

    .ur-mp-matchmaker p {
        font-style: italic;
        font-size: 13px;
        color: rgba(255, 255, 255, .85);
        margin: 14px 0;
        line-height: 1.5;
    }

    .ur-mp-matchmaker button {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        width: 100%;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid #fff;
        background: #fff;
        color: var(--ur-green);
        cursor: pointer;
    }

    .ur-mp-matchmaker button:hover {
        background: var(--ur-bg);
    }

    .ur-mp-report-card {
        border-color: var(--ur-red);
    }

    .ur-mp-report-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 40px;
        width: 100%;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid var(--ur-red);
        background: #fff;
        color: var(--ur-red);
        cursor: pointer;
    }

    .ur-mp-report-btn:hover {
        background: var(--ur-red);
        color: #fff;
    }
</style>
<section class="slice sct-color-2 ur-profile-page">
    <div class="profile">
        <div class="container">
            <div class="row cols-md-space cols-sm-space cols-xs-space">
                <div class="col-lg-9">
                    {{-- Matches the mockup's actual structure: photo + name/details
                         form ONE header row spanning the main content width (not a
                         separate persistent side column) — the tabs below span that
                         same width, with the sidebar running alongside both. The
                         upload mechanism itself (#show_img, #btn_image_edit,
                         #images_form, #images, uploadImages()) is unchanged, just
                         placed differently. --}}
                    <div class="ur-mp-header">
                        <div class="ur-mp-header__photo">
                            <div class="ur-mp-own-photo" id="show_img" style="background-image: url('{{ $profile->getProfileImage() }}')"></div>
                            <label id="btn_image_edit" class="ur-mp-photo-edit" for="images"><i class="fa fa-edit"></i></label>
                            <form id="images_form" enctype="multipart/form-data">
                                @csrf
                                <input type="file" accept="image/png,image/x-png,image/gif,image/jpeg" style="display: none;" id="images" name="images[]" multiple onchange="javascript:uploadImages($('#btn_image_edit'));" />
                            </form>
                        </div>

                        <div class="ur-mp-header__info">
                            <div class="ur-mp-header__badges">
                                <span class="ur-mp-id-chip">{{ $profile->dataid }}</span>
                                @if($isVerified)
                                    <span class="ur-mp-badge-inline ur-mp-badge-inline--verified"><i class="fa fa-check-circle"></i> Verified Profile</span>
                                @endif
                                @if(!empty($completeness))
                                    <span class="ur-mp-match-score">{{ $completeness['percent'] }}% Complete</span>
                                @endif
                            </div>

                            <h2 class="ur-mp-header__name">{{$profile->first_name}} {{$profile->last_name}}{{ $age ? ', '.$age : '' }}</h2>

                            <ul class="ur-mp-facts-stacked">
                                @if(!empty($profile->lbl_con_of_residence))<li><i class="fa fa-map-marker"></i> {{ $profile->lbl_city ? $profile->lbl_city.', ' : '' }}{{ $profile->lbl_con_of_residence }}</li>@endif
                                @if(!empty($profile->profession))<li><i class="fa fa-briefcase"></i> {{ $profile->profession }}</li>@endif
                                @if(!empty($profile->lbl_education))<li><i class="fa fa-graduation-cap"></i> {{ $profile->lbl_education }}</li>@endif
                                @if(!empty($profile->lbl_marital_status))<li><i class="fa fa-heart-o"></i> {{ $profile->lbl_marital_status }}</li>@endif
                            </ul>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="card z-depth-2-top" id="profile_load">
                            <div class="card-title">
                                <h3 class="text-uppercase heading heading-6 strong-500 pull-left">
                                    <b>Profile Information</b>
                                </h3>
                                <!-- <div class="pull-right">
                                    <a href="/home/profile/edit_full_profile" class="btn btn-base-1 btn-sm btn-shadow">
                                        <i class="fa fa-edit"></i>
                                        Edit All
                                    </a>
                                </div> -->
                            </div>
                            <div class="card-body pt-2" style="padding: 1rem 0.5rem;">
                                @if(!empty($completeness))
                                <div class="ur-profile-completeness">
                                    <div class="ur-profile-completeness__label">
                                        <span>Profile Completeness</span>
                                        <span class="ur-profile-completeness__pct">{{ $completeness['percent'] }}%</span>
                                    </div>
                                    <div class="ur-profile-completeness__track">
                                        <div class="ur-profile-completeness__fill" style="width: {{ $completeness['percent'] }}%;"></div>
                                    </div>
                                    @if($completeness['percent'] < 100)
                                    <div class="ur-profile-completeness__hint">{{ $completeness['filled'] }} of {{ $completeness['total'] }} sections complete &mdash; fill in the rest to help us find better matches for you.</div>
                                    @endif
                                </div>
                                @endif

                                <ul class="nav ur-mp-tabs" id="mp_own_tabs" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" onclick="mpOwnShowGroup('about', this);">About</a></li>
                                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="mpOwnShowGroup('lifestyle', this);">Lifestyle</a></li>
                                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="mpOwnShowGroup('family', this);">Family</a></li>
                                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="mpOwnShowGroup('looking_for', this);">Looking For</a></li>
                                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="mpOwnShowGroup('profile_scorer', this);">Profile Scorer</a></li>
                                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="mpOwnShowGroup('more', this);">More Details</a></li>
                                </ul>

                                {{-- No in-page Partner Preferences EDITOR exists — that's still a
                                     separate page (member/profile/preferences) — but if the member
                                     already saved preferences there, show what was actually saved
                                     here instead of just repeating the same "go set this up" teaser
                                     every time regardless of whether they'd already done it. --}}
                                <div class="ur-mp-tabgroup" data-mp-group="looking_for" style="display:none;">
                                    <div class="ur-mp-detail-card">
                                        <div class="card-inner-title-wrapper pt-0">
                                            <h3 class="ur-mp-detail-card__title mb-0"><i class="fa fa-search"></i> Partner Preferences</h3>
                                            <a class="ur-mp-link" href="{{ url('member/profile/preferences') }}">{{ $hasPreferenceData ? 'Edit Preferences' : 'Manage Partner Preferences' }} <i class="fa fa-angle-right"></i></a>
                                        </div>

                                        @if($hasPreferenceData)
                                        <div class="ur-mp-detail-grid">
                                            @if(!empty($preference->age_min) || !empty($preference->age_max))
                                            <div><span>Age Range</span><b>{{ $preference->age_min ?: 'Any' }} - {{ $preference->age_max ?: 'Any' }}</b></div>
                                            @endif
                                            @if(!empty($preference->height))
                                            <div><span>Height</span><b>{{ $preference->height }}</b></div>
                                            @endif
                                            @if(!empty($preference->weight))
                                            <div><span>Weight</span><b>{{ $preference->weight }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['marital_status']))
                                            <div><span>Marital Status</span><b>{{ $preferenceLabels['marital_status'] }}</b></div>
                                            @endif
                                            @if(!empty($preference->with_children))
                                            <div><span>With Children</span><b>{{ $preference->with_children }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['country']) || !empty($preferenceLabels['state']) || !empty($preferenceLabels['city']))
                                            <div><span>Location</span><b>{{ collect([$preferenceLabels['city'] ?? null, $preferenceLabels['state'] ?? null, $preferenceLabels['country'] ?? null])->filter()->implode(', ') }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['religion']))
                                            <div><span>Religion</span><b>{{ $preferenceLabels['religion'] }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['caste']) || !empty($preference->sect))
                                            <div><span>Caste / Sect</span><b>{{ $preferenceLabels['caste'] ?? '—' }}{{ !empty($preference->sect) ? ' / '.$preference->sect : '' }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['mother_tongue']))
                                            <div><span>Mother Tongue</span><b>{{ $preferenceLabels['mother_tongue'] }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['languages']))
                                            <div><span>Languages Spoken</span><b>{{ $preferenceLabels['languages'] }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['education']))
                                            <div><span>Minimum Education</span><b>{{ $preferenceLabels['education'] }}</b></div>
                                            @endif
                                            @if(!empty($preference->profession))
                                            <div><span>Profession</span><b>{{ $preference->profession }}</b></div>
                                            @endif
                                            @if(!empty($preferenceLabels['preferred_country']))
                                            <div><span>Preferred Country</span><b>{{ $preferenceLabels['preferred_country'] }}</b></div>
                                            @endif
                                        </div>
                                        @if(!empty($preference->general_requirement))
                                        <div style="margin-top:14px;">
                                            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.3px;color:var(--ur-text-muted);margin-bottom:4px;">Other Requirements</span>
                                            <p style="font-size:13.5px;color:var(--ur-text);margin:0;">{{ $preference->general_requirement }}</p>
                                        </div>
                                        @endif
                                        @else
                                        <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;">Tell us what you're looking for in a partner — age range, location, education, religion and more — so we can find better matches for you.</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Reuses the same Profile Completeness scorer as the widget
                                     above. On your OWN profile this is just about how filled-out
                                     YOUR profile is — there's no second person to be "compatible"
                                     with here, unlike member/profile.blade.php's Compatibility tab
                                     (viewing another member), so this tab is labelled "Profile
                                     Scorer" instead of "Compatibility". Same underlying data either
                                     way — not a separate algorithm. --}}
                                @if(!empty($completeness))
                                <div class="ur-mp-tabgroup" data-mp-group="profile_scorer" style="display:none;">
                                    <div class="ur-mp-detail-card">
                                        <h3 class="ur-mp-detail-card__title"><i class="fa fa-pie-chart"></i> Profile Scorer</h3>
                                        <div class="ur-mp-compat-full">
                                            <div class="ur-mp-ring ur-mp-ring--lg" style="--pct: {{ $completeness['percent'] }};">
                                                <div class="ur-mp-ring__hole">{{ $completeness['percent'] }}%</div>
                                            </div>
                                            <div class="ur-mp-compat-full__text">
                                                <b>{{ $completeness['percent'] }}% Profile Match Score</b>
                                                <span>Based on {{ $completeness['filled'] }} of {{ $completeness['total'] }} profile sections you've completed. Other members see this same score on your profile.</span>
                                            </div>
                                        </div>
                                        <ul class="ur-mp-checklist">
                                            @foreach($completeness['sections'] as $label => $done)
                                                <li class="{{ $done ? 'is-done' : '' }}"><i class="fa {{ $done ? 'fa-check-circle' : 'fa-circle-o' }}"></i> {{ $label }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                <!-- Contact information -->
                                <div id="section_introduction" class="ur-mp-tabgroup" data-mp-group="about">
                                    <div class="mb-2 pl-3">
                                        <b>Member ID - </b>
                                        <b class="c-base-1">{{$profile->dataid}}</b>
                                    </div>
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_introduction">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Introduction </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('introduction')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if(!empty($profile->intro))
                                                <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;margin:0;">{{ $profile->intro }}</p>
                                            @else
                                                <p style="font-size:13px;color:#b9b2a2;font-style:italic;margin:0;">You haven't added an introduction yet — click the edit icon above to add one.</p>
                                            @endif
                                        </div>

                                        <div id="edit_introduction" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Introduction </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow mb-1" onclick="save_section($(this), 'introduction')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow mb-1" onclick="load_section('introduction')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_introduction" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <textarea name="introduction" class="form-control no-resize" rows="5" required="required">{{ $profile->intro }} </textarea>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Basic Info / Education & Career / Physical Attributes sit
                                     side by side in a grid (matching the mockup's compact card
                                     row) instead of stacked full-width — Introduction stays full
                                     width above since it's a free-text blurb, not a label/value
                                     card. Each still toggles independently via data-mp-group;
                                     wrapping them here doesn't change that. --}}
                                <div class="ur-mp-tab-grid">
                                <div id="section_basic_info" class="ur-mp-tabgroup" data-mp-group="about">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_basic_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Basic Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('basic_info')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid">
                                                <div><span>First Name</span><b>{{$profile->first_name}}</b></div>
                                                <div><span>Last Name</span><b>{{$profile->last_name}}</b></div>
                                                <div><span>Gender</span><b>{{ucfirst($profile->gender ?: '')}}</b></div>
                                                <div><span>Email</span><b>{{$profile->email}}</b></div>
                                                <div><span>Date Of Birth</span><b>{{$profile->birthday}}</b></div>
                                                <div><span>Age</span><b>{{ $profile->birthday ? date_diff(date_create($profile->birthday), date_create('now'))->y : '—' }} Years</b></div>
                                                <div><span>Marital Status</span><b>{{$profile->lbl_marital_status ?: '—'}}</b></div>
                                                <div><span>Number Of Children</span><b>{{$profile->children ?: '0'}}</b></div>
                                                <div><span>Area</span><b>{{$profile->area ?: '—'}}</b></div>
                                                <div><span>On Behalf</span><b>{{$profile->profile_for ?: '—'}}</b></div>
                                                <div><span>Mobile</span><b>{{$profile->contact_mobile_number ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_basic_info" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Basic Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'basic_info')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('basic_info')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_basic_info" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="first_name" class="text-uppercase c-gray-light">First Name</label>
                                                            <input type="text" class="form-control no-resize" name="first_name" value="{{$profile->first_name}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="last_name" class="text-uppercase c-gray-light">Last Name</label>
                                                            <input type="text" class="form-control no-resize" name="last_name" value="{{$profile->last_name}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="first_name" class="text-uppercase c-gray-light">Gender</label>
                                                            <select name="gender" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a gender" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option value="male" {{$profile->gender=="male"?"selected":""}}>Male</option>
                                                                <option value="female" {{$profile->gender=="female"?"selected":""}}>Female</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="email" class="text-uppercase c-gray-light">Email</label>
                                                            <input type="email" class="form-control no-resize" name="email" value="{{$profile->email}}" disabled="disabled">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="date_of_birth" class="text-uppercase c-gray-light">Date Of Birth </label>
                                                            <select name="day" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a day for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                @for ($i=1; $i<=31; $i++)
                                                                <option {{substr($profile->birthday, 8, 2)==($i<10?"0".$i:$i)?"selected":""}}>{{$i<10?"0".$i:$i}}</option>
                                                                @endfor
                                                            </select>

                                                            <select name="month" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a month for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                <option value="01" {{substr($profile->birthday, 5, 2)=="01"?"selected":""}}>January</option>
                                                                <option value="02" {{substr($profile->birthday, 5, 2)=="02"?"selected":""}}>February</option>
                                                                <option value="03" {{substr($profile->birthday, 5, 2)=="03"?"selected":""}}>March</option>
                                                                <option value="04" {{substr($profile->birthday, 5, 2)=="04"?"selected":""}}>April</option>
                                                                <option value="05" {{substr($profile->birthday, 5, 2)=="05"?"selected":""}}>May</option>
                                                                <option value="06" {{substr($profile->birthday, 5, 2)=="06"?"selected":""}}>June</option>
                                                                <option value="07" {{substr($profile->birthday, 5, 2)=="07"?"selected":""}}>July</option>
                                                                <option value="08" {{substr($profile->birthday, 5, 2)=="08"?"selected":""}}>August</option>
                                                                <option value="09" {{substr($profile->birthday, 5, 2)=="09"?"selected":""}}>September</option>
                                                                <option value="10" {{substr($profile->birthday, 5, 2)=="10"?"selected":""}}>October</option>
                                                                <option value="11" {{substr($profile->birthday, 5, 2)=="11"?"selected":""}}>November</option>
                                                                <option value="12" {{substr($profile->birthday, 5, 2)=="12"?"selected":""}}>December</option>
                                                            </select>

                                                            <select name="year" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a year for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                @for ($i=2002; $i>=1927; $i--)
                                                                <option {{substr($profile->birthday, 0, 4)==$i?"selected":""}}>{{$i}}</option>
                                                                @endfor
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <!-- <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="age" class="text-uppercase c-gray-light">Age</label>
                                                            <input type="number" class="form-control no-resize" name="age" value="" min="0" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div> -->
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="marital_status" class="text-uppercase c-gray-light">Marital Status</label>
                                                            <select name="marital_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($maritalstatuses as $maritalstatus)
                                                                <option value="{{$maritalstatus->dataid}}" {{$profile->marital_status==$maritalstatus->dataid?"selected":""}}>{{$maritalstatus->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="children" class="text-uppercase c-gray-light">Number Of Children</label>
                                                            <input type="number" class="form-control no-resize" name="children" value="{{$profile->children}}" min="0" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="area" class="text-uppercase c-gray-light">Area</label>
                                                            <input type="text" class="form-control no-resize" name="area" value="{{$profile->area}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="on_behalf" class="text-uppercase c-gray-light">On Behalf</label>
                                                            <select name="on_behalf" onChange="(this.value,this)" class="form-control form-control-sm selectpicker present_on_behalf_edit" data-placeholder="Choose a on behalf" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->profile_for=="Self"?"selected":""}}>Self</option>
                                                                <option {{$profile->profile_for=="Son"?"selected":""}}>Son</option>
                                                                <option {{$profile->profile_for=="Daughter"?"selected":""}}>Daughter</option>
                                                                <option {{$profile->profile_for=="Brother"?"selected":""}}>Brother</option>
                                                                <option {{$profile->profile_for=="Sister"?"selected":""}}>Sister</option>
                                                                <option {{$profile->profile_for=="Relative"?"selected":""}}>Relative</option>
                                                                <option{{$profile->profile_for=="Friend"?"selected":""}}>Friend</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="contact_mobile_number" class="text-uppercase c-gray-light">Mobile</label>
                                                            <input type="number" class="form-control no-resize" name="contact_mobile_number" value="{{$profile->contact_mobile_number}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_education_and_career" class="ur-mp-tabgroup" data-mp-group="about">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_education_and_career">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Education And Career </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_education_and_career" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('education_and_career')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_education_and_career" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('education_and_career')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('education_and_career')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                                <div><span>Highest Education</span><b>{{$profile->lbl_education ?: '—'}}</b></div>
                                                <div><span>Occupation</span><b>{{$profile->profession ?: '—'}}</b></div>
                                                <div><span>Designation</span><b>{{$profile->designation ?: '—'}}</b></div>
                                                <div><span>Company</span><b>{{$profile->companyname ?: '—'}}</b></div>
                                                <div><span>Annual Income</span><b>{{$profile->salary ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_education_and_career" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Education And Career </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'education_and_career')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('education_and_career')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_education_and_career" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="education" class="text-uppercase c-gray-light">Highest Education</label>
                                                            <select name="education" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a degree" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($education as $degree)
                                                                <option value="{{$degree->dataid}}" {{$profile->education==$degree->dataid?"selected":""}}>{{$degree->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="profession" class="text-uppercase c-gray-light">Occupation</label>
                                                            <input type="text" class="form-control no-resize" name="profession" value="{{$profile->profession}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="designation" class="text-uppercase c-gray-light">Designation</label>
                                                            <input type="text" class="form-control no-resize" name="designation" value="{{$profile->designation}}" placeholder="e.g. Senior Software Engineer">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="companyname" class="text-uppercase c-gray-light">Company</label>
                                                            <input type="text" class="form-control no-resize" name="companyname" value="{{$profile->companyname}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="salary" class="text-uppercase c-gray-light">Annual Income</label>
                                                            <input type="text" class="form-control no-resize" name="salary" value="{{$profile->salary}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_physical_attributes" class="ur-mp-tabgroup" data-mp-group="about">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_physical_attributes">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Physical Attributes </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_physical_attributes" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('physical_attributes')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_physical_attributes" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('physical_attributes')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('physical_attributes')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid">
                                                <div><span>Height</span><b>{{$profile->height ?: '—'}}</b></div>
                                                <div><span>Weight</span><b>{{$profile->weight ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_physical_attributes" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Physical Attributes </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'physical_attributes')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('physical_attributes')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_physical_attributes" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="height" class="text-uppercase c-gray-light">Height</label>
                                                            <input type="text" class="form-control no-resize" name="height" value="{{$profile->height}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="weight" class="text-uppercase c-gray-light">Weight</label>
                                                            <input type="text" class="form-control no-resize" name="weight" value="{{$profile->weight}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="eye_color" class="text-uppercase c-gray-light">Eye Color</label>
                                                            <input type="text" class="form-control no-resize" name="eye_color" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="hair_color" class="text-uppercase c-gray-light">Hair Color</label>
                                                            <input type="text" class="form-control no-resize" name="hair_color" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="complexion" class="text-uppercase c-gray-light">Complexion</label>
                                                            <input type="text" class="form-control no-resize" name="complexion" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="blood_group" class="text-uppercase c-gray-light">Blood Group</label>
                                                            <input type="text" class="form-control no-resize" name="blood_group" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="body_type" class="text-uppercase c-gray-light">Body Type</label>
                                                            <input type="text" class="form-control no-resize" name="body_type" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="body_art" class="text-uppercase c-gray-light">Body Art</label>
                                                            <input type="text" class="form-control no-resize" name="body_art" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="any_disability" class="text-uppercase c-gray-light">Any Disability</label>
                                                            <input type="text" class="form-control no-resize" name="any_disability" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Read-only summary card — the same fields are
                                     actually editable via Residency Information /
                                     Permanent Address further down in More Details;
                                     this just surfaces them here too so About
                                     matches member/profile.blade.php's layout,
                                     without duplicating the edit forms themselves. --}}
                                <div class="ur-mp-tabgroup" data-mp-group="about">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div class="card-inner-title-wrapper pt-0">
                                            <h3 class="text-uppercase card-inner-title pull-left">Location</h3>
                                        </div>
                                        <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                            <div><span>City</span><b>{{ $profile->lbl_city ?: '—' }}</b></div>
                                            <div><span>Country</span><b>{{ $profile->lbl_con_of_residence ?: '—' }}</b></div>
                                            <div><span>Nationality</span><b>{{ $profile->lbl_con_of_citizenship ?: '—' }}</b></div>
                                            <div><span>Residency Status</span><b>{{ $profile->immigration_status ?: '—' }}</b></div>
                                            <div><span>Living In</span><b>{{ collect([$profile->lbl_city ?? null, $profile->lbl_con_of_residence ?? null])->filter()->implode(', ') ?: '—' }}</b></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div id="section_language" class="ur-mp-tabgroup" data-mp-group="lifestyle" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_language">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Language </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_language" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('language')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_language" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('language')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('language')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                                <div><span>Mother Tongue</span><b>{{$profile->lbl_mother_tongue ?: '—'}}</b></div>
                                                <div><span>Language</span><b>{{$profile->lbl_language ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_language" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Language </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'language')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('language')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_language" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="mother_tongue" class="text-uppercase c-gray-light">Mother Tongue</label>
                                                            <select name="mother_tongue" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a mother tongue" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}" {{$profile->mother_tongue==$mothertongue->dataid?"selected":""}}>{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="language" class="text-uppercase c-gray-light">Language</label>
                                                            <select name="language" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a language" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}" {{$profile->language==$mothertongue->dataid?"selected":""}}>{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="speak" class="text-uppercase c-gray-light">Speak</label>
                                                            <select name="speak" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a speak" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="read" class="text-uppercase c-gray-light">Read</label>
                                                            <select name="read" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a read" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_lifestyle_details" class="ur-mp-tabgroup" data-mp-group="lifestyle" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_lifestyle_details">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Lifestyle </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('lifestyle_details')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                                <div><span>Prayer</span><b>{{$profile->prayer ?: '—'}}</b></div>
                                                <div><span>Smoking</span><b>{{$profile->smoking ?: '—'}}</b></div>
                                                <div><span>Diet</span><b>{{$profile->diet ?: '—'}}</b></div>
                                                <div><span>Exercise</span><b>{{$profile->exercise ?: '—'}}</b></div>
                                                <div><span>Hobbies</span><b>{{$profile->hobbies ?: '—'}}</b></div>
                                                <div><span>Living Arrangement</span><b>{{$profile->living_arrangement ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_lifestyle_details" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Lifestyle </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'lifestyle_details')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('lifestyle_details')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_lifestyle_details" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="prayer" class="text-uppercase c-gray-light">Prayer</label>
                                                            <select name="prayer" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose an option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->prayer=="Always"?"selected":""}}>Always</option>
                                                                <option {{$profile->prayer=="Sometimes"?"selected":""}}>Sometimes</option>
                                                                <option {{$profile->prayer=="Rarely"?"selected":""}}>Rarely</option>
                                                                <option {{$profile->prayer=="Never"?"selected":""}}>Never</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="smoking" class="text-uppercase c-gray-light">Smoking</label>
                                                            <select name="smoking" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose an option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->smoking=="Yes"?"selected":""}}>Yes</option>
                                                                <option {{$profile->smoking=="No"?"selected":""}}>No</option>
                                                                <option {{$profile->smoking=="Occasionally"?"selected":""}}>Occasionally</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="diet" class="text-uppercase c-gray-light">Diet</label>
                                                            <select name="diet" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose an option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->diet=="Vegetarian"?"selected":""}}>Vegetarian</option>
                                                                <option {{$profile->diet=="Non-Vegetarian"?"selected":""}}>Non-Vegetarian</option>
                                                                <option {{$profile->diet=="Eggetarian"?"selected":""}}>Eggetarian</option>
                                                                <option {{$profile->diet=="Vegan"?"selected":""}}>Vegan</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="exercise" class="text-uppercase c-gray-light">Exercise</label>
                                                            <select name="exercise" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose an option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->exercise=="Regularly"?"selected":""}}>Regularly</option>
                                                                <option {{$profile->exercise=="Occasionally"?"selected":""}}>Occasionally</option>
                                                                <option {{$profile->exercise=="Never"?"selected":""}}>Never</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="hobbies" class="text-uppercase c-gray-light">Hobbies</label>
                                                            <input type="text" class="form-control no-resize" name="hobbies" value="{{$profile->hobbies}}" placeholder="e.g. Travel, Reading, Football">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="living_arrangement" class="text-uppercase c-gray-light">Living Arrangement</label>
                                                            <select name="living_arrangement" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose an option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->living_arrangement=="With Family"?"selected":""}}>With Family</option>
                                                                <option {{$profile->living_arrangement=="Alone"?"selected":""}}>Alone</option>
                                                                <option {{$profile->living_arrangement=="With Roommates"?"selected":""}}>With Roommates</option>
                                                                <option {{$profile->living_arrangement=="Other"?"selected":""}}>Other</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div id="section_hobbies_and_interest">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_hobbies_and_interest">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Hobbies And Interests </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_hobbies_and_interest" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('hobbies_and_interest')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_hobbies_and_interest" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('hobbies_and_interest')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('hobbies_and_interest')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Hobby</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Interest</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Music</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Books</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Movie</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>TV Show</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Sports Show</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Fitness Activity</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Cuisine</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Dress Style</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_hobbies_and_interest" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Hobbies And Interests </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'hobbies_and_interest')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('hobbies_and_interest')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_hobbies_and_interest" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="hobby" class="text-uppercase c-gray-light">Hobby</label>
                                                            <input type="text" class="form-control no-resize" name="hobby" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="interest" class="text-uppercase c-gray-light">Interest</label>
                                                            <input type="text" class="form-control no-resize" name="interest" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="music" class="text-uppercase c-gray-light">Music</label>
                                                            <input type="text" class="form-control no-resize" name="music" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="books" class="text-uppercase c-gray-light">Books</label>
                                                            <input type="text" class="form-control no-resize" name="books" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="movie" class="text-uppercase c-gray-light">Movie</label>
                                                            <input type="text" class="form-control no-resize" name="movie" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="tv_show" class="text-uppercase c-gray-light">TV Show</label>
                                                            <input type="text" class="form-control no-resize" name="tv_show" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sports_show" class="text-uppercase c-gray-light">Sports Show</label>
                                                            <input type="text" class="form-control no-resize" name="sports_show" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="fitness_activity" class="text-uppercase c-gray-light">Fitness Activity</label>
                                                            <input type="text" class="form-control no-resize" name="fitness_activity" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="cuisine" class="text-uppercase c-gray-light">Cuisine</label>
                                                            <input type="text" class="form-control no-resize" name="cuisine" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="dress_style" class="text-uppercase c-gray-light">Dress Style</label>
                                                            <input type="text" class="form-control no-resize" name="dress_style" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div id="section_personal_attitude_and_behavior">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_personal_attitude_and_behavior">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Personal Attitude And Behavior </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_personal_attitude_and_behavior" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_personal_attitude_and_behavior" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Affection</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Humor</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Political View</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Religious Service</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_personal_attitude_and_behavior" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Personal Attitude And Behavior </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'personal_attitude_and_behavior')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_personal_attitude_and_behavior" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="affection" class="text-uppercase c-gray-light">Affection</label>
                                                            <input type="text" class="form-control no-resize" name="affection" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="humor" class="text-uppercase c-gray-light">Humor</label>
                                                            <input type="text" class="form-control no-resize" name="humor" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="political_view" class="text-uppercase c-gray-light">Political View</label>
                                                            <input type="text" class="form-control no-resize" name="political_view" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="religious_service" class="text-uppercase c-gray-light">Religious Service</label>
                                                            <input type="text" class="form-control no-resize" name="religious_service" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="ur-mp-tab-grid">
                                <div id="section_residency_information" class="ur-mp-tabgroup" data-mp-group="more" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_residency_information">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Residency Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('residency_information')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid">
                                                <div><span>Birth Country</span><b>{{ $profile->lbl_con_of_birth ?: '—' }}</b></div>
                                                <div><span>Residency Country</span><b>{{ $profile->lbl_con_of_residence ?: '—' }}</b></div>
                                                <div><span>Citizenship Country</span><b>{{ $profile->lbl_con_of_citizenship ?: '—' }}</b></div>
                                                <div><span>Grow Up Country</span><b>{{ $profile->lbl_con_grew_up ?: '—' }}</b></div>
                                                <div><span>Immigration Status</span><b>{{ $profile->immigration_status ?: '—' }}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_residency_information" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Residency Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'residency_information')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('residency_information')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_residency_information" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_birth" class="text-uppercase c-gray-light">Birth Country</label>
                                                            <select name="con_of_birth" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_birth==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_residence" class="text-uppercase c-gray-light">Residency Country</label>
                                                            <select name="con_of_residence" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_residence==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_citizenship" class="text-uppercase c-gray-light">Citizenship Country</label>
                                                            <select name="con_of_citizenship" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a citizenship_country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_citizenship==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_grew_up" class="text-uppercase c-gray-light">Grow Up Country</label>
                                                            <select name="con_grew_up" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_grew_up==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="immigration_status" class="text-uppercase c-gray-light">Immigration Status</label>
                                                            <input type="text" class="form-control no-resize" name="immigration_status" value="{{$profile->immigrarion_status}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_spiritual_and_social_background" class="ur-mp-tabgroup" data-mp-group="more" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_spiritual_and_social_background">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Spiritual And Social Background </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_spiritual_and_social_background" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('spiritual_and_social_background')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_spiritual_and_social_background" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('spiritual_and_social_background')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('spiritual_and_social_background')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                                <div><span>Religion</span><b>{{$profile->lbl_religion ?: '—'}}</b></div>
                                                <div><span>Caste / Sect</span><b>{{$profile->lbl_caste ?: '—'}} / {{$profile->sect ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_spiritual_and_social_background" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Spiritual And Social Background </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'spiritual_and_social_background')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('spiritual_and_social_background')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_spiritual_and_social_background" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="religion" class="text-uppercase c-gray-light">Religion</label>
                                                            <select name="religion" onChange="(this.value,this)" class="form-control form-control-sm selectpicker present_religion_edit" data-placeholder="Choose a religion" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($religions as $religion)
                                                                <option value="{{$religion->dataid}}" {{$profile->religion==$religion->dataid?"selected":""}}>{{$religion->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="caste" class="text-uppercase c-gray-light">Caste</label>
                                                            <select class="form-control form-control-sm selectpicker present_caste_edit" name="caste" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($caste as $cst)
                                                                <option value="{{$cst->dataid}}" {{$profile->caste==$cst->dataid?"selected":""}}>{{$cst->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="sect" class="text-uppercase c-gray-light">Sect</label>
                                                            <input type="text" class="form-control no-resize" name="sect" value="{{$profile->sect}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sub_caste" class="text-uppercase c-gray-light">Sub Caste</label>
                                                            <select class="form-control form-control-sm selectpicker present_sub_caste_edit" name="sub_caste">
                                                                <option value="">Choose A Caste First</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="ethnicity" class="text-uppercase c-gray-light">Ethnicity</label>
                                                            <input type="text" class="form-control no-resize" name="ethnicity" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="personal_value" class="text-uppercase c-gray-light">Personal Value</label>
                                                            <input type="text" class="form-control no-resize" name="personal_value" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_value" class="text-uppercase c-gray-light">Family Value</label>
                                                            <select name="family_value" onChange="(this.value,this)" class="form-control form-control-sm selectpicker family_value_edit" data-placeholder="Choose a family_value" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Traditional</option>
                                                                <option value="2">Moderate</option>
                                                                <option value="3">Liberal</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="community_value" class="text-uppercase c-gray-light">Community Value</label>
                                                            <input type="text" class="form-control no-resize" name="community_value" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_value" class="text-uppercase c-gray-light">Family Status</label>
                                                            <select name="family_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker family_status_edit" data-placeholder="Choose a family_status" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">High Class</option>
                                                                <option value="2">Middle Class</option>
                                                                <option value="3">Low Class</option>
                                                                <option value="4">Upper Middle Class </option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="u_manglik" class="text-uppercase c-gray-light">Manglik</label>

                                                            <select name="u_manglik" class="form-control form-control-sm selectpicker" data-placeholder="Choose a manglik" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">I don't know</option>
                                                            </select>
                                                            <!- <select name="manglik" onChange="(this.value,this)" class="form-control form-control-sm selectpicker"   data-placeholder="Choose a manglik" tabindex="2" data-hide-disabled="true" ><option value="">Choose one</option><option value="1" >Yes</option><option value="2" >No</option><option value="3" >Doesn't Matter</option></select> ->
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <!-- <div id="section_life_style">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_life_style">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Life Style </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_life_style" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('life_style')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_life_style" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('life_style')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('life_style')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Diet</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Drink</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Smoke</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Living With</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_life_style" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Life Style </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'life_style')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('life_style')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_life_style" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="diet" class="text-uppercase c-gray-light">Diet</label>
                                                            <input type="text" class="form-control no-resize" name="diet" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="drink" class="text-uppercase c-gray-light">Drink</label>
                                                            <select name="drink" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a drink" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Doesn't Matter</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="smoke" class="text-uppercase c-gray-light">Smoke</label>
                                                            <select name="smoke" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a smoke" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Doesn't Matter</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="living_with" class="text-uppercase c-gray-light">Living With</label>
                                                            <input type="text" class="form-control no-resize" name="living_with" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <div id="section_permanent_address" class="ur-mp-tabgroup" data-mp-group="more" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_permanent_address">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Permanent Address </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_permanent_address" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('permanent_address')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_permanent_address" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('permanent_address')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('permanent_address')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid">
                                                <div><span>Country</span><b>{{ $profile->lbl_con_of_residence ?: '—' }}</b></div>
                                                <div><span>State</span><b>{{ $profile->lbl_state ?: '—' }}</b></div>
                                                <div><span>City</span><b>{{ $profile->lbl_city ?: '—' }}</b></div>
                                                <div><span>Society</span><b>{{ $profile->society ?: '—' }}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_permanent_address" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Permanent Address </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'permanent_address')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('permanent_address')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_permanent_address" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_residence" class="text-uppercase c-gray-light">Country</label>
                                                            <select name="con_of_residence" onchange="javascript:loadSelect('{{url('states')}}', this.value, $('#state'), '{{$profile->state}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_residence==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="state" class="text-uppercase c-gray-light">State</label>
                                                            <select id="state" name="state" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/0', $('#city'), '{{$profile->city}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a state" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose a country first</option>

                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="city" class="text-uppercase c-gray-light">City</label>
                                                            <select id="city" name="city" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose a state first</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="society" class="text-uppercase c-gray-light">Society</label>
                                                            <input type="text" class="form-control no-resize" name="society" value="{{$profile->society}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div id="section_family_info" class="ur-mp-tabgroup" data-mp-group="family" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_family_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Family Information </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_family_info" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('family_info')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_family_info" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('family_info')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('family_info')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid">
                                                <div><span>Father</span><b>{{ $profile->father ?: '—' }}</b></div>
                                                <div><span>Mother</span><b>{{ $profile->mother ?: '—' }}</b></div>
                                                <div><span>Number Of Brothers</span><b>{{ $profile->brothers_count ?: '0' }}</b></div>
                                                <div><span>Number Of Sisters</span><b>{{ $profile->sisters_count ?: '0' }}</b></div>
                                                <div><span>Married Siblings</span><b>{{ $profile->siblings ?: '—' }}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_family_info" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Family Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'family_info')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('family_info')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_family_info" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="father" class="text-uppercase c-gray-light">Father</label>
                                                            <input type="text" class="form-control no-resize" name="father" value="{{$profile->father}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="mother" class="text-uppercase c-gray-light">Mother</label>
                                                            <input type="text" class="form-control no-resize" name="mother" value="{{$profile->mother}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="brothers_count" class="text-uppercase c-gray-light">Number Of Brothers</label>
                                                            <input type="number" min="0" max="20" class="form-control no-resize" name="brothers_count" value="{{$profile->brothers_count}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sisters_count" class="text-uppercase c-gray-light">Number Of Sisters</label>
                                                            <input type="number" min="0" max="20" class="form-control no-resize" name="sisters_count" value="{{$profile->sisters_count}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="siblings" class="text-uppercase c-gray-light">Married Siblings</label>
                                                            <input type="text" class="form-control no-resize" name="siblings" value="{{$profile->siblings}}" placeholder="e.g. 1 brother married">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_additional_personal_details" class="ur-mp-tabgroup" data-mp-group="more" style="display:none;">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_additional_personal_details">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Additional Personal Details </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_additional_personal_details" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('additional_personal_details')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_additional_personal_details" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('additional_personal_details')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('additional_personal_details')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                                <div><span>Home District</span><b>{{$profile->district ?: '—'}}</b></div>
                                                <div><span>Family Residence</span><b>{{$profile->family_residence ?: '—'}}</b></div>
                                                <div><span>Father's Occupation</span><b>{{$profile->father_profession ?: '—'}}</b></div>
                                                <div><span>Mother's Occupation</span><b>{{$profile->mother_profession ?: '—'}}</b></div>
                                                <div><span>Special Circumstances</span><b>{{$profile->special_circumstances ?: '—'}}</b></div>
                                                <div><span>Family Values</span><b>{{$profile->family_values ?: '—'}}</b></div>
                                            </div>
                                        </div>

                                        <div id="edit_additional_personal_details" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Additional Personal Details </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'additional_personal_details')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('additional_personal_details')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_additional_personal_details" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="district" class="text-uppercase c-gray-light">Home District</label>
                                                            <input type="text" class="form-control no-resize" name="district" value="{{$profile->district}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_residence" class="text-uppercase c-gray-light">Family Residence</label>
                                                            <select name="family_residence" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a family residence option" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->family_residence=="Rent"?"selected":""}}>Rent</option>
                                                                <option {{$profile->family_residence=="Own"?"selected":""}}>Own</option>
                                                                <option {{$profile->family_residence=="Do not know"?"selected":""}}>Do not know</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="father_profession" class="text-uppercase c-gray-light">Father's Occupation</label>
                                                            <input type="text" class="form-control no-resize" name="father_profession" value="{{$profile->father_profession}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="mother_profession" class="text-uppercase c-gray-light">Mother's Occupation</label>
                                                            <input type="text" class="form-control no-resize" name="mother_profession" value="{{$profile->mother_profession}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="special_circumstances" class="text-uppercase c-gray-light">Special Circumstances</label>
                                                            <input type="text" class="form-control no-resize" name="special_circumstances" value="{{$profile->special_circumstances}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_values" class="text-uppercase c-gray-light">Family Values</label>
                                                            <select name="family_values" class="form-control form-control-sm selectpicker" data-placeholder="Choose a family values option" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="Traditional" {{$profile->family_values=="Traditional"?"selected":""}}>Traditional</option>
                                                                <option value="Moderate" {{$profile->family_values=="Moderate"?"selected":""}}>Moderate</option>
                                                                <option value="Liberal" {{$profile->family_values=="Liberal"?"selected":""}}>Liberal</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    {{-- No relationship-manager assignment feature exists yet
                         — static content, same treatment as member/profile
                         .blade.php's Matchmaker card. --}}
                    <div class="ur-mp-matchmaker">
                        <h4>Your Matchmaker</h4>
                        <div class="ur-mp-matchmaker__who">
                            <div class="ur-mp-matchmaker__avatar">SF</div>
                            <div>
                                <b>Sana Farooq</b>
                                <span>Relationship Manager</span>
                            </div>
                        </div>
                        <p>&ldquo;Need help completing your profile?&rdquo;</p>
                        <button type="button" onclick="chatComingSoon();">Ask My Matchmaker</button>
                    </div>

                    <div class="ur-mp-side-card">
                        <h4>Package Information</h4>
                        <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                            <div><span>Current Package</span><b>{{$profile->lbl_package ?: '—'}}</b></div>
                            <div id="images_stats" style="cursor:pointer;"><span>Photos</span><b>{{ $profile->getImageCount() }}</b></div>
                        </div>
                    </div>

                    <div class="ur-mp-side-card">
                        <h4>Profile Status</h4>
                        <ul class="ur-mp-checklist">
                            <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Identity Reviewed</li>
                            <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Details Reviewed</li>
                            <li class="{{ $profile->isActive() ? 'is-done' : '' }}"><i class="fa {{ $profile->isActive() ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Available for Introduction</li>
                        </ul>
                    </div>

                    <div class="ur-mp-side-card">
                        <h4>Why Matches Are Private?</h4>
                        <p>We respect your privacy. Your personal information is only shared with other members after mutual interest and approval.</p>
                        <a class="ur-mp-link" href="{{ url('privacy') }}">Learn more about our privacy <i class="fa fa-angle-right"></i></a>
                    </div>

                    {{-- No report/block feature exists yet — static, same
                         treatment as member/profile.blade.php. --}}
                    <div class="ur-mp-side-card ur-mp-report-card">
                        <h4>Need Help?</h4>
                        <p>Something wrong with your account, or need support?</p>
                        <button type="button" class="ur-mp-report-btn" onclick="chatComingSoon();"><i class="fa fa-life-ring"></i> Contact Support</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function chatComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Live chat with a relationship manager isn\'t available yet. Please reach out via our Contact page in the meantime and we\'ll get back to you personally.',
            'icon': 'info',
        });
    }

    $(document).ready(function() {
        $("#show_img").on('click', function() {
            showLightGallery($(this));
        });

        $("#images_stats").on('click', function() {
            showLightGallery($(this));
        });

        // preload city select
        @if(!empty($profile->con_of_residence))
        loadSelect('{{url('states')}}', '{{$profile->con_of_residence}}', $('#state'), '{{$profile->state}}');
        loadSelect('{{url('cities')}}', '{{$profile->con_of_residence}}/1', $('#city'), '{{$profile->city}}');
        @endif

    });

    // $("#active_modal").on('hidden.bs.modal', function (e) {
    //     window.location.href = "{{ url('member/profile') }}";
    // });

    function uploadImages(elem) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "{{ url('member/profile/update/images/upload') }}",
            cache: false,
            contentType: false,
            processData: false,
            data: new FormData($("#images_form")[0]),
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);
                var message = result.message.split("|");
                if (result.code == '200') {
                    if (result.html) {
                        $("#top_nav_img").css("background-image", "url('" + result.nav_img + "')");;
                        $("#main-content").html(result.html);
                        // Same reasoning as save_section() above — the
                        // freshly-injected HTML defaults back to "About".
                        mpOwnShowGroup(mpActiveGroup);
                    }
                    if (message) showAlert(message[0], message[1], message[2]);
                } else {
                    if (message) showAlert('danger', message);
                }
            }
        });
    }

    //var isloggedin = "116";
    // Script for Editing Profile with Ajax
    // Groups the existing sections above into tabs (About/Lifestyle/Family/
    // Looking For/Profile Scorer/More Details) without moving any of them in
    // the DOM — each section just carries a data-mp-group attribute (see the
    // blade markup) that this shows/hides. Tracks the active group in
    // mpActiveGroup so save_section() below can restore it after replacing
    // #main-content wholesale (which would otherwise silently reset the view
    // back to the "About" tab's baked-in default every time something is
    // saved).
    var mpActiveGroup = 'about';
    function mpOwnShowGroup(group, linkElem) {
        mpActiveGroup = group;
        document.querySelectorAll('.ur-mp-tabgroup').forEach(function(el) {
            el.style.display = (el.getAttribute('data-mp-group') === group) ? '' : 'none';
        });
        var tabsNav = document.getElementById('mp_own_tabs');
        if (tabsNav) {
            tabsNav.querySelectorAll('.nav-link').forEach(function(a) {
                a.classList.remove('active');
            });
        }
        if (!linkElem && tabsNav) {
            linkElem = tabsNav.querySelector('a[onclick*="\'' + group + '\'"]');
        }
        if (linkElem) linkElem.classList.add('active');
    }

    function edit_section(section) {
        $('#info_' + section).hide();
        $('#edit_' + section).show();
    }

    function load_section(section) {

        $('#info_' + section).show();
        $('#edit_' + section).hide();
    }

    function save_section(elem, section) {
        // For Safety Disabling Section Elements for Slow Internet Connections
        $('#section_' + section).find('.form-control').prop('readonly', true);
        $('#section_' + section).find('.btn').prop('disabled', true);
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "/member/profile/update/" + section,
            cache: false,
            data: $('#form_' + section).serialize(),
            success: function(result) {
                elem.html(oldHtml);
                var message = result.message.split("|");
                if (result.code == '200') {
                    if (result.html) {
                        $("#main-content").html(result.html);
                        // The freshly-injected HTML defaults back to the
                        // "About" tab (that's its baked-in default display
                        // state) — restore whichever tab was actually open.
                        mpOwnShowGroup(mpActiveGroup);
                    }
                    if (message)
                        showAlert(message[0], message[1], 3000);
                } else {
                    if (message)
                        showAlert('danger', message, 5000);
                }
            }
        });
    }

    // function unhide_section(section) {
    //     $('#section_' + section).find('.form-control').prop('readonly', true);
    //     $('#section_' + section).find('.btn').prop('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/profile/unhide_section/" + section,
    //         cache: false,
    //         success: function(response) {
    //             $('#ajax_danger_alert').fadeOut('fast');
    //             $('#ajax_success_alert').show();
    //             $('.ajax_success_alert').html("This Section Is Successfully Showed");
    //             setTimeout(function() {
    //                 $('#ajax_success_alert').fadeOut('fast');
    //             }, 3000); // <-- time in milliseconds
    //             $('#section_' + section).find('.form-control').prop('readonly', false);
    //             $('#section_' + section).find('.btn').prop('disabled', false);
    //             $('#unhide_' + section).hide();
    //             $('#hide_' + section).show();
    //         },
    //         fail: function(error) {
    //             alert(error);
    //         }
    //     });
    // }

    // function hide_section(section) {
    //     $('#section_' + section).find('.form-control').prop('readonly', true);
    //     $('#section_' + section).find('.btn').prop('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/profile/hide_section/" + section,
    //         cache: false,
    //         success: function(response) {
    //             $('#ajax_success_alert').fadeOut('fast');
    //             $('#ajax_danger_alert').show();
    //             $('.ajax_danger_alert').html("This Section Is Successfully Hidden");
    //             setTimeout(function() {
    //                 $('#ajax_danger_alert').fadeOut('fast');
    //             }, 3000); // <-- time in milliseconds
    //             $('#section_' + section).find('.form-control').prop('readonly', false);
    //             $('#section_' + section).find('.btn').prop('disabled', false);
    //             $('#unhide_' + section).show();
    //             $('#hide_' + section).hide();
    //         },
    //         fail: function(error) {
    //             alert(error);
    //         }
    //     });
    // }

    // function IsJsonString(str) {
    //     try {
    //         JSON.parse(str);
    //     } catch (e) {
    //         return false;
    //     }
    //     return true;
    // }


    // function open_message_box(thread_id, now) {

    //     $("#msg_body").html("<div class='text-center' id='payment_loader'><i class='fa fa-refresh fa-5x fa-spin'></i></div>");
    //     $("#msg_box_header").html("<a class='c-base-1' target='_blank' href='/home/member_profile/" + $(now).find('.contacts-list-name').data('member') + "'>" + $(now).find('.contacts-list-name').html() + "</a>");
    //     $("#msg_refresh").html("<a onclick='refresh_msg(" + thread_id + ")'><i class='fa fa-refresh'></i> Refresh</a>");
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/get_messages/" + thread_id,
    //         cache: false,
    //         success: function(response) {
    //             /*clearInterval(message_interval);
    //             var message_interval =  setInterval(function(){
    //                                         $("#msg_body").load('/home/get_messages/'+thread_id);
    //                                     }, 4000);*/
    //             $("#msg_body").removeAttr("style");
    //             $("#message_text").removeAttr('disabled');
    //             $("#message_text").val('');
    //             $("#msg_body").html(response);
    //         }
    //     });
    // }

    // function refresh_msg(thread_id) {
    //     $(".contacts-list").find("#thread_" + thread_id).click();
    // }

    // function load_all_msg(thread_id) {
    //     $("#msg_body").html("<div class='text-center' id='payment_loader'><i class='fa fa-refresh fa-5x fa-spin'></i></div>");
    //     $("#message_text").attr('disabled', true);
    //     $("#msg_send_btn").attr('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/get_messages/" + thread_id + "/all_msg",
    //         cache: false,
    //         success: function(response) {
    //             $("#message_text").removeAttr('disabled');
    //             $("#msg_send_btn").removeAttr('disabled');
    //             $("#msg_body").html(response);
    //         }
    //     });
    // }

    // function msg_send(thread, from, to) {
    //     if ($("#message_text").val().length != 0) {
    //         var form_data = ($("#message_form").serialize());
    //         $("#message_text").attr('disabled', 'disabled');
    //         $("#msg_send_btn").attr('disabled', 'disabled');
    //         $("#msg_send_btn").html("<i class='fa fa-refresh fa-spin'></i>");

    //         $.ajax({
    //             type: "POST",
    //             url: "/home/send_message/" + thread + "/" + from + "/" + to,
    //             data: form_data,
    //             success: function(response) {
    //                 // alert('done');
    //                 $("#message_text").removeAttr('disabled');
    //                 $("#message_text").val('');
    //                 $("#msg_send_btn").html("Send");
    //                 $.ajax({
    //                     type: "POST",
    //                     url: "/home/get_messages/" + thread,
    //                     cache: false,
    //                     success: function(response) {
    //                         $("#msg_body").html(response);
    //                     }
    //                 });
    //             }
    //         });
    //     }
    // }
</script>
<style type="text/css">
    .xs_nav_item {
        text-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
    }
</style>
@endsection
