@extends('layouts.master')
@section('main-content')
{{-- Uses the site-wide Font Awesome 4 already loaded by layouts.master — do NOT add
     a second Font Awesome (e.g. v6) stylesheet here, it conflicts with the FA4 icon
     classes used across the rest of the site (footer social icons, etc.) and breaks them. --}}
<?php use App\User; ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    body.page-packages #main-content { background: #FBF7EF; }

    .pk-page {
        --pk-green: #123A2E;
        --pk-green-deep: #0F2E24;
        --pk-gold: #C9974D;
        --pk-gold-light: #E8C27A;
        --pk-cream: #FBF7EF;
        --pk-sand: #EFE7D6;
        --pk-line: #F0EADD;
        --pk-terracotta: #B5674A;
        --pk-text: #5B6560;
        --pk-ink: #1C2321;
        --pk-ink-2: #33403A;
        --pk-cream-text: #EFE3C8;
        --pk-cream-text-2: #D7E4DC;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--pk-cream);
        color: var(--pk-ink);
    }
    .pk-page * { box-sizing: border-box; }
    .pk-page a { text-decoration: none; }
    .pk-eyebrow {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--pk-terracotta);
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .pk-h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 30px;
        font-weight: 600;
        margin: 0 0 14px;
        color: var(--pk-ink);
    }
    .pk-lead {
        font-size: 14.5px;
        line-height: 1.75;
        color: var(--pk-text);
        margin: 0 0 20px;
    }

    /* ============ 1. HERO ============ */
    .pk-hero {
        background: linear-gradient(135deg, var(--pk-green) 0%, #1F5C46 55%, var(--pk-green) 100%);
        padding: 64px 20px 46px;
        text-align: center;
    }
    .pk-hero-badge {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(239,227,200,0.3);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--pk-cream-text);
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .pk-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 14px;
        line-height: 1.2;
    }
    .pk-hero h1 em {
        color: var(--pk-gold);
        font-style: italic;
    }
    .pk-hero p {
        font-size: 15.5px;
        color: var(--pk-cream-text-2);
        max-width: 520px;
        margin: 0 auto 22px;
        line-height: 1.6;
    }
    .pk-hero-trust {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 28px;
        justify-content: center;
        font-size: 13px;
        color: var(--pk-cream-text);
    }
    .pk-hero-trust > div { display: flex; gap: 7px; align-items: center; }
    .pk-hero-trust > div span { color: var(--pk-gold); }

    /* ============ 2. SIDE-BY-SIDE SERVICES ============ */
    .pk-services-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: start;
        border-top: 1px solid var(--pk-line);
    }
    .pk-service-col {
        min-width: 0;
    }
    .pk-service-col--online {
        border-right: 1px solid var(--pk-line);
    }
    .pk-service-col-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 22px 20px;
        font-weight: 800;
        font-size: 13.5px;
        letter-spacing: .04em;
        text-align: center;
        color: var(--pk-green);
        background: var(--pk-sand);
    }
    .pk-service-col--premium .pk-service-col-label {
        background: var(--pk-green);
        color: var(--pk-cream-text);
    }
    .pk-service-col .pk-intro {
        grid-template-columns: 1fr;
        padding: 32px 32px 4px;
        gap: 24px;
    }
    .pk-service-col .pk-pkg-section {
        padding: 24px 32px 44px;
    }

    /* ============ Two-col intro sections ============ */
    .pk-intro {
        padding: 44px 56px 8px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 56px;
        align-items: start;
    }
    .pk-note {
        background: var(--pk-sand);
        border-left: 3px solid var(--pk-gold);
        border-radius: 6px;
        padding: 16px 18px;
        font-size: 13px;
        line-height: 1.65;
        color: var(--pk-text);
    }
    .pk-check-cards { display: flex; flex-direction: column; gap: 12px; }
    .pk-check-cards .pk-cc {
        display: flex;
        gap: 12px;
        background: #fff;
        border-radius: 12px;
        padding: 14px 16px;
    }
    .pk-check-cards .pk-cc > i, .pk-check-cards .pk-cc > .em { color: var(--pk-gold); flex-shrink: 0; }
    .pk-check-cards .pk-cc div.txt { font-size: 13.5px; line-height: 1.6; color: var(--pk-ink-2); }
    .pk-icon-lines { display: flex; flex-direction: column; gap: 12px; }
    .pk-icon-lines > div { display: flex; gap: 12px; }
    .pk-icon-lines > div > .em { color: var(--pk-gold); flex-shrink: 0; }
    .pk-icon-lines > div > .txt { font-size: 13.5px; line-height: 1.6; color: var(--pk-ink-2); }
    .pk-side-title { font-weight: 700; font-size: 14.5px; margin-bottom: 14px; }

    .pk-dark-box {
        background: var(--pk-green);
        border-radius: 16px;
        padding: 30px;
        color: #fff;
    }
    .pk-dark-box .pk-db-title { font-weight: 700; font-size: 15px; margin-bottom: 10px; }
    .pk-dark-box p { font-size: 13.5px; line-height: 1.75; color: var(--pk-cream-text-2); margin: 0 0 14px; }
    .pk-dark-box p:last-child { margin-bottom: 0; }
    .pk-dark-box strong { color: var(--pk-gold); }
    .pk-dark-box .pk-quote { font-style: italic; }

    /* ============ Package grids ============ */
    .pk-pkg-section { padding: 36px 56px 70px; }
    .pk-pkg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 22px;
    }
    .pk-card {
        background: #fff;
        border: 1px solid var(--pk-line);
        border-radius: 18px;
        padding: 28px 24px;
        text-align: center;
        position: relative;
    }
    .pk-card.pk-card--dark {
        background: var(--pk-green);
        border: none;
        box-shadow: 0 16px 36px rgba(18,58,46,0.25);
    }
    .pk-card-ribbon {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--pk-gold);
        color: var(--pk-green);
        font-size: 11px;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 99px;
        white-space: nowrap;
    }
    .pk-card-eyebrow {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        color: var(--pk-terracotta);
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .pk-card--dark .pk-card-eyebrow { color: var(--pk-gold); }
    .pk-card-price {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--pk-ink);
    }
    .pk-card--dark .pk-card-price { color: #fff; }
    .pk-card-sub { font-size: 12.5px; color: var(--pk-text); margin-bottom: 20px; }
    .pk-card--dark .pk-card-sub { color: var(--pk-cream-text-2); }
    .pk-card-divider { height: 1px; background: var(--pk-line); margin-bottom: 20px; }
    .pk-card--dark .pk-card-divider { background: rgba(255,255,255,0.14); }
    .pk-card-badge {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: var(--pk-cream);
        color: var(--pk-gold);
        border: 1px solid var(--pk-line);
    }
    .pk-card--dark .pk-card-badge { background: rgba(255,255,255,0.12); color: var(--pk-gold-light); border-color: transparent; }
    .pk-card-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 19px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--pk-ink);
    }
    .pk-card--dark .pk-card-name { color: #fff; }
    .pk-card-tag { font-size: 12.5px; line-height: 1.6; color: var(--pk-text); margin-bottom: 18px; }
    .pk-card--dark .pk-card-tag { color: var(--pk-cream-text-2); }
    .pk-btn-outline, .pk-btn-solid, .pk-btn-solid-gold {
        display: block;
        width: 100%;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        padding: 11px;
        text-align: center;
        cursor: pointer;
        font-family: 'Manrope', system-ui, sans-serif;
    }
    .pk-btn-outline {
        border: 1.5px solid var(--pk-green);
        color: var(--pk-green);
        background: transparent;
        margin-bottom: 10px;
    }
    .pk-card--dark .pk-btn-outline { border-color: var(--pk-cream-text); color: var(--pk-cream-text); }
    .pk-btn-solid { background: var(--pk-green); color: var(--pk-cream-text); border: none; }
    .pk-btn-solid-gold { background: var(--pk-gold); color: var(--pk-green); border: none; }
    .pk-card-active-badge {
        display: inline-block;
        background: var(--pk-gold);
        color: var(--pk-green);
        font-size: 11px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 99px;
        margin-bottom: 10px;
    }
    .pk-card-expiry { font-size: 12px; color: var(--pk-terracotta); margin-top: 8px; }
    .pk-empty { text-align: center; color: var(--pk-text); padding: 30px 0; }

    /* ============ Consultation + payment ============ */
    .pk-consult {
        padding: 64px 56px;
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 40px;
    }
    .pk-consult-card {
        background: #fff;
        border-radius: 18px;
        padding: 32px;
        box-shadow: 0 2px 14px rgba(18,58,46,0.06);
    }
    .pk-video-row {
        background: var(--pk-sand);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pk-video-row .vt { font-weight: 700; font-size: 14.5px; }
    .pk-video-row .vs { font-size: 12.5px; color: var(--pk-text); }
    .pk-video-row .price { font-family: 'Playfair Display', Georgia, serif; font-size: 22px; color: var(--pk-green); }
    .pk-consult-actions { display: flex; gap: 14px; flex-wrap: wrap; }
    .pk-btn-appt { background: var(--pk-green); color: var(--pk-cream-text); font-weight: 700; font-size: 13.5px; padding: 13px 24px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; }
    .pk-btn-wa { background: #25D366; color: #fff; font-weight: 700; font-size: 13.5px; padding: 13px 24px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; }
    .pk-payment-card {
        background: var(--pk-green);
        border-radius: 18px;
        padding: 32px;
        color: #fff;
    }
    .pk-payment-card .pt { font-weight: 700; font-size: 15px; margin-bottom: 18px; }
    .pk-payment-card .pl { font-size: 11.5px; font-weight: 700; letter-spacing: .08em; color: var(--pk-gold); text-transform: uppercase; margin-bottom: 8px; }
    .pk-payment-card .pd { font-size: 13px; line-height: 1.9; color: var(--pk-cream-text-2); margin-bottom: 16px; }
    .pk-payment-card .pd:last-child { margin-bottom: 0; }

    /* ============ Service comparison ============ */
    .pk-compare { padding: 64px 56px; }
    .pk-compare-head { text-align: center; max-width: 560px; margin: 0 auto 40px; }
    .pk-compare-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 1080px; margin: 0 auto; }
    .pk-compare-card { border-radius: 18px; padding: 32px; }
    .pk-compare-card.light { background: #fff; }
    .pk-compare-card.dark { background: var(--pk-green); color: #fff; }
    .pk-compare-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 16px; }
    .pk-compare-card.light .pk-compare-icon { background: var(--pk-cream-text); }
    .pk-compare-card.dark .pk-compare-icon { background: rgba(255,255,255,0.12); }
    .pk-compare-title { font-family: 'Playfair Display', Georgia, serif; font-size: 21px; font-weight: 600; margin-bottom: 10px; }
    .pk-compare-title span { font-size: 12px; font-family: 'Manrope', sans-serif; font-weight: 700; color: var(--pk-text); }
    .pk-compare-card.dark .pk-compare-title span { color: var(--pk-cream-text-2); }
    .pk-compare-card p { font-size: 13.5px; line-height: 1.7; margin: 0 0 14px; }
    .pk-compare-card.light p { color: var(--pk-text); }
    .pk-compare-card.dark p { color: var(--pk-cream-text-2); }
    .pk-discount-pill { background: var(--pk-sand); border-radius: 8px; padding: 10px 14px; font-size: 12.5px; font-weight: 700; color: #7A5A22; margin-bottom: 18px; display: inline-block; }
    .pk-compare-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; font-size: 12.5px; color: var(--pk-cream-text); }
    .pk-cta-outline, .pk-cta-solid {
        display: block; text-align: center; font-weight: 700; font-size: 13.5px; padding: 12px; border-radius: 10px;
    }
    .pk-cta-outline { border: 1.5px solid var(--pk-green); color: var(--pk-green); }
    .pk-compare-card.dark .pk-cta-solid { background: var(--pk-gold); color: var(--pk-green); }

    /* ============ Why choose us + Final CTA ============ */
    .pk-why { padding: 64px 56px; text-align: center; }
    .pk-why p { font-size: 14.5px; line-height: 1.75; color: var(--pk-text); max-width: 640px; margin: 0 auto; }
    .pk-final-cta { background: var(--pk-green); padding: 56px 56px; text-align: center; }
    .pk-final-cta h2 { font-family: 'Playfair Display', Georgia, serif; font-size: 30px; font-weight: 600; color: #fff; margin: 0 0 12px; }
    .pk-final-cta p { font-size: 14.5px; color: var(--pk-cream-text-2); max-width: 440px; margin: 0 auto 24px; }
    .pk-final-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .pk-final-actions .solid { background: var(--pk-gold); color: var(--pk-green); font-weight: 700; font-size: 14px; padding: 14px 28px; border-radius: 99px; }
    .pk-final-actions .outline { border: 1.5px solid var(--pk-cream-text); color: var(--pk-cream-text); font-weight: 700; font-size: 14px; padding: 14px 28px; border-radius: 99px; }

    @media (max-width: 900px) {
        .pk-wrap, .pk-intro, .pk-pkg-section, .pk-consult, .pk-compare { padding-left: 24px; padding-right: 24px; }
        .pk-intro, .pk-consult, .pk-compare-grid { grid-template-columns: 1fr; }
        .pk-hero { padding: 48px 20px 36px; }
        .pk-final-cta { padding: 48px 24px; }
        .pk-services-split { grid-template-columns: 1fr; }
        .pk-service-col--online { border-right: none; border-bottom: 1px solid var(--pk-line); }
        .pk-service-col .pk-intro { padding-left: 24px; padding-right: 24px; }
        .pk-service-col .pk-pkg-section { padding-left: 24px; padding-right: 24px; }
    }

    /* Footer intentionally NOT overridden here — uses the single shared footer
       styling from layouts/master.blade.php, same as every other page on the site. */
</style>

<div class="pk-page">

<!-- 1. HERO -->
<section class="pk-hero">
    <div class="pk-hero-badge">Pricing</div>
    <h1>Pick the Plan That Fits<br>Your <em>Journey to Forever</em></h1>
    <p>Two ways to search — go at your own pace with Online Services, or let our matchmakers lead with Personalized Service. No credit card required to get started.</p>
    <div class="pk-hero-trust">
        <div><span>✓</span> No credit card required</div>
        <div><span>✓</span> Personalised service available</div>
        <div><span>✓</span> Cancel anytime</div>
    </div>
</section>

@php
    $standardPackages = $standardPackages ?? collect();
    $premiumPackages = $premiumPackages ?? collect();
    $userOnlinePackageDataid = $userOnlinePackageDataid ?? null;
    $userOnlineExpiresAtFormatted = $userOnlineExpiresAtFormatted ?? null;
    $userHasActiveOnlinePackage = $userHasActiveOnlinePackage ?? false;
    if ($standardPackages->isEmpty() && $premiumPackages->isEmpty()) {
        $standardPackages = collect($packages ?? []);
    }
    $planTaglines = [
        'Platinum' => 'Personal matchmaking support for clients seeking a professionally managed search.',
        'Diamond' => 'Priority matchmaking with broader search support and dedicated assistance.',
        'Royal' => 'A highly personalized and confidential search with priority handling and senior-level oversight.',
        'Imperial' => 'Our most exclusive service for individuals and families with highly specific requirements who expect maximum discretion and individually managed search.',
    ];
    $planIcons = [
        'Platinum' => 'fa-shield',
        'Diamond' => 'fa-diamond',
        'Royal' => 'fa-star',
        'Imperial' => 'fa-trophy',
    ];
    $planFullNames = [
        'Royal' => 'Royal - Executive Matchmaking',
        'Imperial' => 'Imperial - Bespoke Private Matchmaking',
    ];
@endphp

<!-- 2 & 3/5. ONLINE SERVICES (left) + PERSONALIZED SERVICE (right), side by side -->
<div class="pk-services-split">

<div class="pk-service-col pk-service-col--online">
    <div class="pk-service-col-label">📱 Online Services</div>

    <!-- 3. ONLINE SERVICES INTRO -->
    <div class="pk-intro">
        <div>
            <div class="pk-eyebrow">Take Control of Your Journey</div>
            <h2 class="pk-h2">Online Services: search at your own pace</h2>
            <p class="pk-lead">A self-managed platform for those who prefer to explore and connect independently. Search our full database, send interests, and connect directly once matched.</p>
            <div class="pk-note">This is a DIY plan — our matchmaking team doesn't personally suggest matches here. From signup to contact, you manage your own search.</div>
        </div>
        <div>
            <div class="pk-side-title">How it works</div>
            <div class="pk-check-cards">
                <div class="pk-cc"><span class="em">✅</span><div class="txt"><b>Self-exploration</b> — full access to search by education, caste, city &amp; more</div></div>
                <div class="pk-cc"><span class="em">✅</span><div class="txt"><b>Direct interaction</b> — send interests, chat once accepted</div></div>
                <div class="pk-cc"><span class="em">✅</span><div class="txt"><b>Tiered access</b> — limits set by your chosen package</div></div>
                <div class="pk-cc"><span class="em">✅</span><div class="txt"><b>Premium profiles</b> — reserved for top-tier package holders</div></div>
            </div>
        </div>
    </div>

    <!-- ONLINE PACKAGES -->
    <div class="pk-pkg-section">
        @if(!$standardPackages->isEmpty())
        <div class="pk-pkg-grid">
            @foreach ($standardPackages as $package)
            @if($package->dataid!="99")
            @php
                $meta = method_exists($package, 'meta') ? $package->meta() : [];
                $isCurrent = $userHasActiveOnlinePackage && $userOnlinePackageDataid === $package->dataid;
            @endphp
            <div class="pk-card">
                @if($isCurrent)
                <div class="pk-card-active-badge">Current Plan</div>
                @endif
                @if(!empty($meta) && isset($meta['duration_label']))
                <div class="pk-card-eyebrow">{{ $meta['duration_label'] }}</div>
                @endif
                @if(!empty($meta) && isset($meta['price']))
                <div class="pk-card-price">{{ $meta['currency'] ?? 'USD' }} {{ number_format((float)$meta['price'], 2) }}</div>
                <div class="pk-card-sub">{{ $package->name }}</div>
                @else
                <div class="pk-card-price">{{ $package->name }}</div>
                @endif
                <div class="pk-card-divider"></div>
                <a href="{{ url('package-details/'.$package->id) }}" class="pk-btn-outline">View Details</a>
                @auth
                    @if($userHasActiveOnlinePackage)
                        <div class="pk-card-expiry">{{ $isCurrent ? 'Expires: '.$userOnlineExpiresAtFormatted : 'Subscribe again after '.$userOnlineExpiresAtFormatted }}</div>
                    @else
                        <a href="{{ route('packages.checkout', ['id' => $package->id]) }}" class="pk-btn-solid">Buy Now</a>
                    @endif
                @else
                    <a href="{{ url('login') }}" class="pk-btn-solid">Log In to Buy</a>
                @endauth
            </div>
            @endif
            @endforeach
        </div>
        @else
        <p class="pk-empty">No online packages available at the moment.</p>
        @endif
    </div>
</div><!-- /.pk-service-col--online -->

<div class="pk-service-col pk-service-col--premium">
    <div class="pk-service-col-label">🤝 Personalized Service</div>

    <!-- 4. PERSONALIZED SERVICE INTRO -->
    <div class="pk-intro" style="background:#EFE7D6;">
        <div>
            <div class="pk-eyebrow">Expert Matchmaking</div>
            <h2 class="pk-h2">Personalized (Confidential) Service</h2>
            <p class="pk-lead">A premium, high-touch experience for those who value privacy, accuracy and expert guidance — with a dedicated matchmaking partner, not just a platform.</p>
            <div class="pk-icon-lines">
                <div><span class="em">🔒</span><div class="txt"><b>Complete confidentiality</b> — shared only after your approval</div></div>
                <div><span class="em">🔍</span><div class="txt"><b>Profile assessment</b> — honest, transparent feedback before we proceed</div></div>
                <div><span class="em">💍</span><div class="txt"><b>Curated matching</b> — guided from introduction to family meetings</div></div>
                <div><span class="em">📋</span><div class="txt"><b>Tailored plans</b> — designed around your demands, lifestyle &amp; preferences</div></div>
            </div>
        </div>
        <div class="pk-dark-box">
            <div class="pk-db-title">Private &amp; Targeted Search</div>
            <p>For families who prefer discretion without public advertisement, our <strong>Special Executive Plans</strong> are personally overseen by our CEO — matched to your family's values and vision.</p>
            <p class="pk-quote">&ldquo;Great matches are not based on status or education — they are built on understanding and compatibility.&rdquo;</p>
        </div>
    </div>

    <!-- PERSONALIZED PACKAGES -->
    <div class="pk-pkg-section" style="background:#EFE7D6;">
        @if(!$premiumPackages->isEmpty())
        <div class="pk-pkg-grid">
            @foreach ($premiumPackages as $package)
            @if($package->dataid!="99")
            @php
                $isExecutive = trim($package->name) === 'Royal';
                $tagline = $planTaglines[trim($package->name)] ?? 'Dedicated matchmaker & priority introductions';
                $icon = $planIcons[trim($package->name)] ?? 'fa-crown';
                $displayName = $planFullNames[trim($package->name)] ?? $package->name;
            @endphp
            <div class="pk-card {{ $isExecutive ? 'pk-card--dark' : '' }}">
                @if($isExecutive)
                <div class="pk-card-ribbon">EXECUTIVE PICK</div>
                @endif
                <div class="pk-card-badge"><i class="fa {{ $icon }}" aria-hidden="true"></i></div>
                <div class="pk-card-name">{{ $displayName }}</div>
                <div class="pk-card-tag">{{ $tagline }}</div>
                <a href="{{ url('package-details/'.$package->id) }}" class="{{ $isExecutive ? 'pk-btn-solid-gold' : 'pk-btn-outline' }}" style="margin-bottom:0;">View Package Details</a>
            </div>
            @endif
            @endforeach
        </div>
        <div style="text-align:center;font-size:12.5px;color:#5B6560;margin-top:16px;">Pricing for Personalized packages is shared during your consultation.</div>
        @else
        <p class="pk-empty">No premium packages available at the moment.</p>
        @endif
    </div>
</div><!-- /.pk-service-col--premium -->

</div><!-- /.pk-services-split -->

<!-- 6. CONSULTATION + PAYMENT -->
<div class="pk-consult">
    <div class="pk-consult-card">
        <div class="pk-eyebrow">Consultation Services</div>
        <div class="pk-h2" style="font-size:24px;">Speak with a senior marriage consultant</div>
        <p class="pk-lead">Available for both office appointments and scheduled calls. Calls are only accepted with prior booking — after booking, our team contacts you within 24&ndash;48 hours.</p>
        <div class="pk-video-row">
            <div>
                <div class="vt">Video Session Booking</div>
                <div class="vs">View suitable profiles &amp; connect directly</div>
            </div>
            <div class="price">Rs. 2,000</div>
        </div>
        <div class="pk-consult-actions">
            <a href="{{ url('appointments') }}" class="pk-btn-appt"><i class="fa fa-calendar-check-o"></i> Book Appointment</a>
            <a href="https://wa.me/923040227000" target="_blank" rel="noopener" class="pk-btn-wa"><i class="fa fa-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
    <div class="pk-payment-card">
        <div class="pt">Payment Details</div>
        <div class="pl">Bank Transfer</div>
        <div class="pd">Account Title: Urgent Rishta<br>Bank: UBL<br>IBAN: PK98UNIL0109000343139629</div>
        <div class="pl">Easypaisa / JazzCash</div>
        <div class="pd">Mobile: 0304 0227000<br>Account Name: Usman Zaheer</div>
    </div>
</div>

<!-- 7. SERVICE COMPARISON -->
<div class="pk-compare" style="background:#EFE7D6;">
    <div class="pk-compare-head">
        <div class="pk-eyebrow">Our Services</div>
        <h2 class="pk-h2" style="font-size:32px;">Digital Match vs. Personal Match</h2>
    </div>
    <div class="pk-compare-grid">
        <div class="pk-compare-card light">
            <div class="pk-compare-icon">📱</div>
            <div class="pk-compare-title">Digital Match <span>(Online)</span></div>
            <p>Create your profile, choose a plan, and see matches based on your preferences. Express interest — if accepted, connect directly.</p>
            <div class="pk-discount-pill">🎉 50% launch discount available</div>
            <a href="https://urgentrishta.co/packages" class="pk-cta-outline">I'm Interested</a>
        </div>
        <div class="pk-compare-card dark">
            <div class="pk-compare-icon">🤝</div>
            <div class="pk-compare-title">Personal Match <span>(Offline)</span></div>
            <p>Four exclusive services: private database access, weekly curated matches, a daily broadcast list, and video consultations.</p>
            <div class="pk-compare-list">
                <div>✓ Exclusive private-database access</div>
                <div>✓ Personalized weekly matches</div>
                <div>✓ Daily broadcast list</div>
                <div>✓ Video consultation sessions</div>
            </div>
            <a href="http://urgentrishta.wedlock204.com" class="pk-cta-solid">I'm Interested</a>
        </div>
    </div>
</div>

<!-- 8. WHY CHOOSE US -->
<div class="pk-why">
    <div class="pk-eyebrow">Why Choose Us</div>
    <h2 class="pk-h2">We stay with you until you succeed</h2>
    <p>We never disappoint our clients! Unlike other services that show only a couple of proposals and disappear, we stay in touch with our clients and work continuously to find the perfect match based on their expectations. This level of commitment and service is unmatched — you won't find it anywhere else.</p>
</div>

<!-- 9. FINAL CTA -->
<div class="pk-final-cta">
    <h2>Ready to begin your search?</h2>
    <p>Choose Online Services to start today, or book a consultation for a fully guided experience.</p>
    <div class="pk-final-actions">
        @guest
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="solid">Create your profile free</a>
        @endif
        @endguest
        @auth
        <a href="{{ url('member/profile') }}" class="solid">My Profile</a>
        @endauth
        <a href="{{ url('appointments') }}" class="outline">Book a Private Consultation</a>
    </div>
</div>

</div><!-- /.pk-page -->

@auth
    @if(empty(User::retrieveUserObject()->online_package))
        <script type="text/javascript">
            $(document).ready(function() {
                swalAlert("info", "Select a Package", "Review packages available and contact Usman at 0304-0227000 for package activation.", null);
            });
        </script>
    @endif
@endauth
@endsection
