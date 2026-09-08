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
    // Compatibility ($compatibility) is computed in ProfileController::profile()
    // — it needs the logged-in VIEWER's own Partner Preferences compared
    // against $profile (this member), which isn't available from $profile
    // alone.
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

    /* Bootstrap's plain .container caps out around ~1140px and centers
       itself — with the 3-column layout (photo/tabs/sidebar) that reads as
       squeezed into the middle with dead space left/right, since the
       dashboard shell already gives this page much more width. Capped (not
       100%) so it doesn't stretch absurdly wide on ultra-wide monitors. */
    .ur-mp-page .container {
        max-width: 1760px;
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

    .ur-mp-save-icon {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid var(--ur-border);
        color: var(--ur-text);
        font-size: 13px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 999px;
        cursor: pointer;
    }

    .ur-mp-save-icon:hover {
        border-color: var(--ur-gold);
        color: var(--ur-green);
    }

    .ur-mp-save-icon i {
        color: var(--ur-gold);
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

    /* ---- Hidden-photos notice (member/profile.blade.php only — the
       viewer isn't the owner, isn't admin, and hasn't been granted access
       to this member's Private photos; see ProfileController::profile()) ---- */
    .ur-mp-locked-photos {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        padding: 12px 14px;
        border-radius: 10px;
        background: var(--ur-bg);
        border: 1px dashed var(--ur-border);
        font-size: 12.5px;
        color: var(--ur-text-muted);
    }

    .ur-mp-locked-photos__icon {
        color: var(--ur-red);
        font-size: 15px;
    }

    .ur-mp-locked-photos__text {
        flex: 1;
    }

    .ur-mp-locked-photos__btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--ur-green);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 999px;
        border: 0;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
    }

    .ur-mp-locked-photos__btn:hover {
        background: var(--ur-green-dark);
        color: #fff;
    }

    .ur-mp-locked-photos__status {
        font-weight: 600;
        white-space: nowrap;
    }

    .ur-mp-locked-photos__status--declined {
        color: var(--ur-red);
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

    /* ---- Generic card (left column + right sidebar both reuse this) ---- */
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
        cursor: pointer;
        border: 0;
        background: none;
        padding: 0;
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

    /* ---- Matchmaker sidebar card (static — no relationship-manager
       assignment feature exists yet, same as the Chat/Ask buttons below) ---- */
    .ur-mp-matchmaker {
        background: var(--ur-green);
        color: #fff;
        border-radius: 14px;
        padding: 20px;
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

    .ur-mp-matchmaker .ur-mp-btn {
        background: #fff;
        color: var(--ur-green) !important;
        border-color: #fff;
        height: 42px;
        font-size: 12.5px;
    }

    .ur-mp-matchmaker .ur-mp-btn:hover {
        background: var(--ur-bg);
    }

    .ur-mp-report-card {
        border-color: var(--ur-red);
    }

    .ur-mp-report-card p {
        margin-bottom: 12px;
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

    /* ---- Tabs ---- */
    .ur-mp-tabs {
        border-bottom: 1px solid var(--ur-border);
        margin-bottom: 20px;
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
    }

    .ur-mp-tabs .nav-link.active {
        color: var(--ur-green) !important;
        border-bottom-color: var(--ur-gold);
        background: transparent;
    }

    .ur-mp-tabs .nav-link:hover {
        color: var(--ur-green) !important;
    }

    /* ---- Ring (reuses the same conic-gradient technique as the Search
       Profiles completeness ring, kept under this page's own class names) ---- */
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

    .ur-mp-ring--sm {
        width: 68px;
        height: 68px;
    }

    .ur-mp-ring--sm .ur-mp-ring__hole {
        width: 54px;
        height: 54px;
        font-size: 13px;
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

    .ur-mp-compat-summary {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ur-mp-compat-summary__text b {
        display: block;
        font-size: 13.5px;
        color: var(--ur-text);
    }

    .ur-mp-compat-summary__text span {
        display: block;
        font-size: 12px;
        color: var(--ur-text-muted);
        margin-top: 2px;
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

    .ur-mp-not-specified {
        color: #b9b2a2 !important;
        font-style: italic;
        font-weight: 500 !important;
    }

    /* ---- Right column detail cards (About/Lifestyle/Family/Looking
       For/Compatibility/More tab content) ---- */
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

    /* auto-fit + minmax (not a fixed repeat(3,1fr)) so the number of
       columns adapts to whatever width this card actually has, instead of
       forcing 3 equal columns that overflow when the card is narrower. */
    .ur-mp-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 16px 18px;
    }

    .ur-mp-detail-grid.ur-mp-detail-grid--2 {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }

    .ur-mp-detail-grid > div {
        /* Grid items default to min-width:auto, which stops them shrinking
           below their content's intrinsic width — the actual overflow
           cause. This lets long values wrap inside their own column. */
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

    .ur-mp-empty-tab {
        text-align: center;
        padding: 30px 20px;
        color: var(--ur-text-muted);
        font-size: 13.5px;
    }

    /* ---- "Verified & Trusted" trust banner (bottom of tab content) ---- */
    .ur-mp-trust-banner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    .ur-mp-trust-banner__msg {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1 1 260px;
    }

    .ur-mp-trust-banner__msg i {
        color: var(--ur-green);
        font-size: 24px;
    }

    .ur-mp-trust-banner__msg b {
        display: block;
        color: var(--ur-text);
        font-size: 13.5px;
    }

    .ur-mp-trust-banner__msg span {
        display: block;
        color: var(--ur-text-muted);
        font-size: 12px;
        margin-top: 2px;
    }

    .ur-mp-trust-banner__items {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .ur-mp-trust-banner__item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #b9b2a2;
    }

    .ur-mp-trust-banner__item.is-done {
        color: var(--ur-text);
    }

    .ur-mp-trust-banner__item.is-done i {
        color: var(--ur-green);
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
            <a class="ur-mp-back" href="{{ url()->previous(route('searchresults')) }}"><i class="fa fa-angle-left"></i> Back to Matches</a>
            <button type="button" class="ur-mp-save-icon" onclick="shortlistComingSoon();"><i class="fa fa-bookmark-o"></i> Save</button>
        </div>

        <div class="row">
            <div class="col-lg-3">
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

                @if(!empty($hiddenPhotos) && $hiddenPhotos['count'] > 0 && !$hiddenPhotos['canView'])
                <div class="ur-mp-locked-photos">
                    <span class="ur-mp-locked-photos__icon"><i class="fa fa-lock"></i></span>
                    <span class="ur-mp-locked-photos__text">{{ $hiddenPhotos['count'] }} {{ $hiddenPhotos['count'] == 1 ? 'photo is' : 'photos are' }} hidden by this member.</span>
                    @guest
                        <a class="ur-mp-locked-photos__btn" href="javascript:void(0);" onclick="return register_request();">
                            <i class="fa fa-lock"></i> Request to View
                        </a>
                    @endguest
                    @auth
                        @if($hiddenPhotos['status'] === null)
                        <a class="ur-mp-locked-photos__btn" id="photoaccess_{{ $profile->dataid }}" href="javascript:void(0);" title="Request Photo Access" onclick="return requestPhotoAccess($(this));">
                            <i class="fa fa-lock"></i> Request to View
                        </a>
                        @elseif($hiddenPhotos['status'] === 0)
                        <span class="ur-mp-locked-photos__status">Request sent — waiting for approval</span>
                        @elseif($hiddenPhotos['status'] === -1)
                        <span class="ur-mp-locked-photos__status ur-mp-locked-photos__status--declined">Access request declined</span>
                        @endif
                    @endauth
                </div>
                @endif

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

                    {{-- No shortlist/saved-profiles feature exists yet (the
                         generic Filtered list read path is unfinished on the
                         Profile model) — static, clearly-labelled for now. --}}
                    <a class="ur-mp-btn ur-mp-btn--outline" onclick="shortlistComingSoon();"><i class="fa fa-bookmark-o"></i> Shortlist</a>

                    {{-- No relationship-manager chat exists yet — same
                         static, clearly-labelled treatment. --}}
                    <a class="ur-mp-btn ur-mp-btn--outline" onclick="chatComingSoon();"><i class="fa fa-headphones"></i> Ask Matchmaker</a>
                </div>

                <div class="ur-mp-privacy-note"><i class="fa fa-lock"></i> Contact information is private and will be shared only after mutual interest.</div>
            </div>

            <div class="col-lg-6">
                <ul class="nav ur-mp-tabs" id="mp_tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#mp_tab_about" role="tab">About</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mp_tab_lifestyle" role="tab">Lifestyle</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mp_tab_family" role="tab">Family</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mp_tab_looking_for" role="tab">Looking For</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mp_tab_compatibility" role="tab">Compatibility</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mp_tab_more" role="tab">More About Them</a></li>
                </ul>

                <div class="tab-content" id="mp_tab_content">
                    {{-- ===== About ===== --}}
                    <div class="tab-pane fade show active" id="mp_tab_about" role="tabpanel">
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-user"></i> About</h3>
                            <div class="ur-mp-detail-grid">
                                @if($age)<div><span>Age</span><b>{{ $age }} Years</b></div>@endif
                                @if(!empty($profile->height))<div><span>Height</span><b>{{ $profile->height }}</b></div>@endif
                                @if(!empty($profile->lbl_religion))<div><span>Religion / Sect</span><b>{{ $profile->lbl_religion }}{{ !empty($profile->sect) ? ' / '.$profile->sect : '' }}</b></div>@endif
                                @if(!empty($profile->lbl_caste))<div><span>Caste</span><b>{{ $profile->lbl_caste }}</b></div>@endif
                                @if(!empty($profile->lbl_marital_status))<div><span>Marital Status</span><b>{{ $profile->lbl_marital_status }}</b></div>@endif
                                @if(!empty($profile->lbl_mother_tongue))<div><span>Mother Tongue</span><b>{{ $profile->lbl_mother_tongue }}</b></div>@endif
                            </div>
                        </div>

                        @if(!empty($profile->lbl_education) || !empty($profile->profession) || !empty($profile->designation) || !empty($profile->salary) || !empty($profile->companyname))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-graduation-cap"></i> Education &amp; Career</h3>
                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                @if(!empty($profile->lbl_education))<div><span>Qualification</span><b>{{ $profile->lbl_education }}</b></div>@endif
                                @if(!empty($profile->profession))<div><span>Profession</span><b>{{ $profile->profession }}</b></div>@endif
                                @if(!empty($profile->designation))<div><span>Designation</span><b>{{ $profile->designation }}</b></div>@endif
                                @if(!empty($profile->companyname))<div><span>Company</span><b>{{ $profile->companyname }}</b></div>@endif
                                @if(!empty($profile->salary))<div><span>Income Range</span><b>{{ $profile->salary }}</b></div>@endif
                            </div>
                        </div>
                        @endif

                        @if(!empty($profile->lbl_city) || !empty($profile->lbl_con_of_residence) || !empty($profile->lbl_con_of_citizenship) || !empty($profile->immigration_status))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-map-marker"></i> Location</h3>
                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                @if(!empty($profile->lbl_city))<div><span>City</span><b>{{ $profile->lbl_city }}</b></div>@endif
                                @if(!empty($profile->lbl_con_of_residence))<div><span>Country</span><b>{{ $profile->lbl_con_of_residence }}</b></div>@endif
                                @if(!empty($profile->lbl_con_of_citizenship))<div><span>Nationality</span><b>{{ $profile->lbl_con_of_citizenship }}</b></div>@endif
                                @if(!empty($profile->immigration_status))<div><span>Residency Status</span><b>{{ $profile->immigration_status }}</b></div>@endif
                                @if(!empty($profile->lbl_city) || !empty($profile->lbl_con_of_residence))<div><span>Living In</span><b>{{ collect([$profile->lbl_city ?? null, $profile->lbl_con_of_residence ?? null])->filter()->implode(', ') }}</b></div>@endif
                            </div>
                        </div>
                        @endif

                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-pie-chart"></i> Compatibility</h3>
                            @if(!empty($compatibility))
                            <div class="ur-mp-compat-summary">
                                <div class="ur-mp-ring ur-mp-ring--sm" style="--pct: {{ $compatibility['percent'] }};">
                                    <div class="ur-mp-ring__hole">{{ $compatibility['percent'] }}%</div>
                                </div>
                                <div class="ur-mp-compat-summary__text">
                                    <b>{{ $compatibility['percent'] }}% Compatibility Score</b>
                                    <span>{{ $compatibility['matched'] }} of {{ $compatibility['total'] }} of your preferences matched</span>
                                </div>
                            </div>
                            <button type="button" class="ur-mp-link" onclick="mpGoToTab('mp_tab_compatibility');">View full compatibility report <i class="fa fa-angle-right"></i></button>
                            @else
                            <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;margin:0 0 10px;">Set your Partner Preferences to see how well {{ $profile->first_name }} matches what you're looking for.</p>
                            <a class="ur-mp-link" href="{{ url('member/profile/preferences') }}">Set Partner Preferences <i class="fa fa-angle-right"></i></a>
                            @endif
                        </div>
                    </div>

                    {{-- ===== Lifestyle — real fields now (see
                         add_designation_and_lifestyle_fields_to_users_table
                         migration); still shown as "Not specified" per-field
                         when a member hasn't filled a particular one in,
                         rather than hiding the row or inventing data. ===== --}}
                    <div class="tab-pane fade" id="mp_tab_lifestyle" role="tabpanel">
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-leaf"></i> Lifestyle</h3>
                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                <div><span>Prayer</span><b class="{{ empty($profile->prayer) ? 'ur-mp-not-specified' : '' }}">{{ $profile->prayer ?: 'Not specified' }}</b></div>
                                <div><span>Smoking</span><b class="{{ empty($profile->smoking) ? 'ur-mp-not-specified' : '' }}">{{ $profile->smoking ?: 'Not specified' }}</b></div>
                                <div><span>Diet</span><b class="{{ empty($profile->diet) ? 'ur-mp-not-specified' : '' }}">{{ $profile->diet ?: 'Not specified' }}</b></div>
                                <div><span>Exercise</span><b class="{{ empty($profile->exercise) ? 'ur-mp-not-specified' : '' }}">{{ $profile->exercise ?: 'Not specified' }}</b></div>
                                <div><span>Hobbies</span><b class="{{ empty($profile->hobbies) ? 'ur-mp-not-specified' : '' }}">{{ $profile->hobbies ?: 'Not specified' }}</b></div>
                                <div><span>Living Arrangement</span><b class="{{ empty($profile->living_arrangement) ? 'ur-mp-not-specified' : '' }}">{{ $profile->living_arrangement ?: 'Not specified' }}</b></div>
                                @if(!empty($profile->family_values))<div><span>Family Values</span><b>{{ $profile->family_values }}</b></div>@endif
                            </div>
                            @if(empty($profile->prayer) && empty($profile->smoking) && empty($profile->diet) && empty($profile->exercise) && empty($profile->hobbies) && empty($profile->living_arrangement))
                            <div class="ur-mp-note"><i class="fa fa-info-circle"></i> This member hasn't shared detailed lifestyle preferences yet.</div>
                            @endif
                        </div>
                    </div>

                    {{-- ===== Family ===== --}}
                    <div class="tab-pane fade" id="mp_tab_family" role="tabpanel">
                        @if(!empty($profile->father) || !empty($profile->mother) || !empty($profile->brothers_count) || !empty($profile->sisters_count) || !empty($profile->father_profession) || !empty($profile->mother_profession) || !empty($profile->family_residence))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-users"></i> Family Background</h3>
                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                @if(!empty($profile->father))<div><span>Father</span><b>{{ $profile->father }}</b></div>@endif
                                @if(!empty($profile->father_profession))<div><span>Father's Occupation</span><b>{{ $profile->father_profession }}</b></div>@endif
                                @if(!empty($profile->mother))<div><span>Mother</span><b>{{ $profile->mother }}</b></div>@endif
                                @if(!empty($profile->mother_profession))<div><span>Mother's Occupation</span><b>{{ $profile->mother_profession }}</b></div>@endif
                                @if(!empty($profile->brothers_count))<div><span>Brother(s)</span><b>{{ $profile->brothers_count }}</b></div>@endif
                                @if(!empty($profile->sisters_count))<div><span>Sister(s)</span><b>{{ $profile->sisters_count }}</b></div>@endif
                                @if(!empty($profile->family_residence))<div><span>Family Type</span><b>{{ $profile->family_residence }}</b></div>@endif
                            </div>
                            <div class="ur-mp-note"><i class="fa fa-lock"></i> We respect your privacy. Detailed family information will be shared after mutual interest.</div>
                        </div>
                        @else
                        <div class="ur-mp-empty-tab">This member hasn't added family background details yet.</div>
                        @endif
                    </div>

                    {{-- ===== Looking For (Partner Preference) ===== --}}
                    <div class="tab-pane fade" id="mp_tab_looking_for" role="tabpanel">
                        @if(!empty($profile->rgen_req))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-quote-left"></i> In Their Own Words</h3>
                            <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;margin:0;">{{ $profile->rgen_req }}</p>
                        </div>
                        @endif
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-search"></i> Partner Preference</h3>
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
                            @if(empty($profile->rage_min) && empty($profile->rage_max) && empty($profile->rheight) && empty($profile->lbl_rmarital_status) && empty($profile->lbl_reducation) && empty($profile->rprofession) && empty($profile->lbl_rreligion) && empty($profile->lbl_rcaste) && empty($profile->rsect) && empty($profile->lbl_rmother_tongue) && empty($profile->lbl_rcon_pref) && empty($profile->lbl_rcon_of_residence))
                                <div class="ur-mp-empty-tab" style="padding:10px 0 0;">This member hasn't set their partner preferences yet.</div>
                            @endif
                        </div>
                    </div>

                    {{-- ===== Compatibility (full — reuses Profile
                         Completeness, per product decision: same scorer as
                         the My Profile dashboard, not a new per-pair
                         algorithm) ===== --}}
                    <div class="tab-pane fade" id="mp_tab_compatibility" role="tabpanel">
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-pie-chart"></i> Compatibility Report</h3>
                            @if(!empty($compatibility))
                            <div class="ur-mp-compat-full">
                                <div class="ur-mp-ring ur-mp-ring--lg" style="--pct: {{ $compatibility['percent'] }};">
                                    <div class="ur-mp-ring__hole">{{ $compatibility['percent'] }}%</div>
                                </div>
                                <div class="ur-mp-compat-full__text">
                                    <b>{{ $compatibility['percent'] }}% Compatibility Score</b>
                                    <span>Based on {{ $compatibility['matched'] }} of {{ $compatibility['total'] }} of your saved Partner Preferences matching {{ $profile->first_name }}'s profile.</span>
                                </div>
                            </div>
                            <ul class="ur-mp-checklist">
                                @foreach($compatibility['checks'] as $check)
                                    <li class="{{ $check['matched'] ? 'is-done' : '' }}"><i class="fa {{ $check['matched'] ? 'fa-check-circle' : 'fa-circle-o' }}"></i> {{ $check['label'] }}</li>
                                @endforeach
                            </ul>
                            <div class="ur-mp-note"><i class="fa fa-info-circle"></i> Only preferences you've actually set are checked here — anything left blank on your Partner Preferences isn't counted for or against a match.</div>
                            @else
                            <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;">You haven't set any Partner Preferences yet, so we can't calculate a compatibility score against {{ $profile->first_name }}'s profile.</p>
                            <a class="ur-mp-link" href="{{ url('member/profile/preferences') }}">Set Partner Preferences <i class="fa fa-angle-right"></i></a>
                            @endif
                        </div>
                    </div>

                    {{-- ===== More About Them ===== --}}
                    <div class="tab-pane fade" id="mp_tab_more" role="tabpanel">
                        @if(!empty($profile->intro))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-quote-left"></i> About {{ $pronounTitle }}</h3>
                            <p style="font-size:13.5px;color:var(--ur-text-muted);line-height:1.6;margin:0;">{{ $profile->intro }}</p>
                        </div>
                        @endif
                        @if(!empty($profile->special_circumstances) || !empty($profile->district))
                        <div class="ur-mp-detail-card">
                            <h3 class="ur-mp-detail-card__title"><i class="fa fa-info-circle"></i> Additional Details</h3>
                            <div class="ur-mp-detail-grid ur-mp-detail-grid--2">
                                @if(!empty($profile->district))<div><span>District</span><b>{{ $profile->district }}</b></div>@endif
                                @if(!empty($profile->special_circumstances))<div><span>Special Circumstances</span><b>{{ $profile->special_circumstances }}</b></div>@endif
                            </div>
                        </div>
                        @endif
                        @if(empty($profile->intro) && empty($profile->special_circumstances) && empty($profile->district))
                        <div class="ur-mp-empty-tab">This member hasn't added any more details yet.</div>
                        @endif
                    </div>
                </div>

                <div class="ur-mp-trust-banner">
                    <div class="ur-mp-trust-banner__msg">
                        <i class="fa fa-shield"></i>
                        <div>
                            <b>Verified &amp; Trusted</b>
                            <span>{{ $isVerified ? 'This profile is verified and personally reviewed by our team.' : 'Verification for this profile is still pending.' }}</span>
                        </div>
                    </div>
                    <div class="ur-mp-trust-banner__items">
                        <span class="ur-mp-trust-banner__item {{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Identity Verified</span>
                        <span class="ur-mp-trust-banner__item {{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Details Reviewed</span>
                        <span class="ur-mp-trust-banner__item {{ $profile->isActive() ? 'is-done' : '' }}"><i class="fa {{ $profile->isActive() ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Profile Monitored</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                {{-- No relationship-manager assignment feature exists yet —
                     static content, per product decision (same treatment as
                     Ask Matchmaker/Chat above). --}}
                <div class="ur-mp-matchmaker">
                    <h4>Your Matchmaker</h4>
                    <div class="ur-mp-matchmaker__who">
                        <div class="ur-mp-matchmaker__avatar">SF</div>
                        <div>
                            <b>Sana Farooq</b>
                            <span>Relationship Manager</span>
                        </div>
                    </div>
                    <p>&ldquo;Need guidance about this profile?&rdquo;</p>
                    <button type="button" class="ur-mp-btn" onclick="chatComingSoon();">Ask About This Profile</button>
                </div>

                <div class="ur-mp-card">
                    <h4>Profile Status</h4>
                    <ul class="ur-mp-checklist">
                        <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Identity Reviewed</li>
                        <li class="{{ $isVerified ? 'is-done' : '' }}"><i class="fa {{ $isVerified ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Details Reviewed</li>
                        <li class="{{ $profile->isActive() ? 'is-done' : '' }}"><i class="fa {{ $profile->isActive() ? 'fa-check-circle' : 'fa-circle-o' }}"></i> Available for Introduction</li>
                    </ul>
                </div>

                <div class="ur-mp-card">
                    <h4>Why Matches Are Private?</h4>
                    <p>We respect your privacy. Personal information is shared only after mutual interest and appropriate approval.</p>
                    <a class="ur-mp-link" href="{{ url('privacy') }}">Learn more about our privacy <i class="fa fa-angle-right"></i></a>
                </div>

                {{-- No report/block feature exists yet — static, per product
                     decision. --}}
                <div class="ur-mp-card ur-mp-report-card">
                    <h4>Report / Block</h4>
                    <p>Something not right? You can report or block this profile.</p>
                    <button type="button" class="ur-mp-report-btn" onclick="reportProfileComingSoon();"><i class="fa fa-flag"></i> Report Profile</button>
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
                <a class="ur-mp-btn ur-mp-btn--outline" onclick="chatComingSoon();"><i class="fa fa-headphones"></i> Ask Matchmaker</a>
                <a class="ur-mp-btn ur-mp-btn--outline" onclick="shortlistComingSoon();"><i class="fa fa-bookmark-o"></i> Shortlist</a>
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

    // Switches to a tab pane programmatically (used by the "View full
    // compatibility report" link) and scrolls the tab strip into view.
    function mpGoToTab(id) {
        $('#mp_tabs a[href="#' + id + '"]').tab('show');
        document.getElementById('mp_tabs').scrollIntoView({behavior: 'smooth', block: 'start'});
    }

    function chatComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Live chat with a relationship manager isn\'t available yet. Please reach out via our Contact page in the meantime and we\'ll get back to you personally.',
            'icon': 'info',
        });
    }

    function shortlistComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Shortlisting profiles for later is on its way. For now, use Send Interest to keep in touch with this profile.',
            'icon': 'info',
        });
    }

    function reportProfileComingSoon() {
        swal({
            'title': 'Coming Soon',
            'text': 'Reporting/blocking profiles directly isn\'t available yet. If something looks wrong with this profile, please contact our support team and we\'ll look into it right away.',
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
