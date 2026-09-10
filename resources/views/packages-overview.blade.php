@extends('layouts.master')
@section('main-content')
{{-- Uses the site-wide Font Awesome 4 already loaded by layouts.master — do NOT add
     a second Font Awesome (e.g. v6) stylesheet here, see packages.blade.php for why. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    body.page-packages #main-content { background: #FBF7EF; overflow-x: hidden; }

    .pko-page {
        /* Same palette as packages.blade.php's .pk-page, kept in its own
           namespace since this is a separate template with its own scoped
           <style> block (not shared across pages). */
        --pko-green: #123A2E;
        --pko-gold: #C9974D;
        --pko-gold-light: #E8C27A;
        --pko-cream: #FBF7EF;
        --pko-sand: #EFE7D6;
        --pko-line: #F0EADD;
        --pko-terracotta: #B5674A;
        --pko-text: #5B6560;
        --pko-ink: #1C2321;
        --pko-cream-text: #EFE3C8;
        --pko-cream-text-2: #D7E4DC;
        --pko-mint: #EEF3F0;
        --pko-teal: #2F6D68;
        --pko-teal-line: #CFE3E0;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--pko-cream);
        color: var(--pko-ink);
    }
    .pko-page * { box-sizing: border-box; }
    .pko-page a { text-decoration: none; }

    .pko-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--pko-terracotta);
        text-transform: uppercase;
    }
    .pko-eyebrow::before {
        content: "";
        width: 22px;
        height: 2px;
        background: var(--pko-gold);
        border-radius: 2px;
        display: inline-block;
    }

    /* ============ HERO ============ */
    .pko-hero {
        background: linear-gradient(135deg, var(--pko-green) 0%, #1F5C46 55%, var(--pko-green) 100%);
        padding: 64px 20px 52px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .pko-hero::before {
        content: "";
        position: absolute;
        top: -180px;
        left: 50%;
        transform: translateX(-50%);
        width: 720px;
        height: 480px;
        background: radial-gradient(closest-side, rgba(201,151,77,0.22), transparent 70%);
        pointer-events: none;
    }
    .pko-hero > * { position: relative; z-index: 1; }
    .pko-hero-eyebrow {
        justify-content: center;
        color: var(--pko-gold-light);
        margin-bottom: 16px;
    }
    .pko-hero-eyebrow::before { background: var(--pko-gold-light); }
    .pko-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 14px;
        line-height: 1.2;
    }
    .pko-hero p {
        max-width: 620px;
        margin: 0 auto;
        font-size: 15px;
        line-height: 1.75;
        color: var(--pko-cream-text-2);
    }
    .pko-hero-trust {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 22px;
        margin-top: 26px;
        font-size: 12.5px;
        color: var(--pko-cream-text);
    }
    .pko-hero-trust > div { display: flex; gap: 7px; align-items: center; }
    .pko-hero-trust > div span { color: var(--pko-gold); }

    /* ============ PLANS ============ */
    .pko-plans {
        /* Section itself is full-bleed (container-fluid) — but the tab
           selector + detail panel below are wrapped in .pko-plans-container,
           a capped, centered "container" so the two-column panel doesn't
           stretch into unreadably long lines on an ultra-wide monitor. */
        padding: 64px 40px 20px;
    }
    .pko-plans-container { max-width: 1240px; margin: 0 auto; }

    /* Tab selector row — three equal pill buttons; the active one switches
       to the dark/gold treatment and its matching panel below is revealed. */
    .pko-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .pko-tab {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid var(--pko-line);
        border-radius: 14px;
        padding: 16px 18px;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
        position: relative;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
        box-shadow: 0 2px 10px rgba(28,35,33,0.04);
    }
    .pko-tab:hover { border-color: var(--pko-gold-light); }
    .pko-tab-num {
        flex-shrink: 0;
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 700;
        font-size: 17px;
        color: var(--pko-gold);
    }
    .pko-tab-title { display: block; font-size: 13.5px; font-weight: 700; color: var(--pko-ink); }
    .pko-tab-sub { display: block; font-size: 11px; color: var(--pko-text); margin-top: 1px; }
    .pko-tab-badge {
        position: absolute;
        top: -9px;
        right: 12px;
        background: var(--pko-gold);
        color: var(--pko-green);
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 3px 9px;
        border-radius: 999px;
    }
    .pko-tab--active {
        background: var(--pko-green);
        border-color: var(--pko-green);
        box-shadow: 0 12px 26px rgba(18,58,46,0.25);
    }
    .pko-tab--active .pko-tab-num { color: var(--pko-gold-light); }
    .pko-tab--active .pko-tab-title { color: #fff; }
    .pko-tab--active .pko-tab-sub { color: var(--pko-cream-text-2); }

    /* Detail panel — only the plan matching the active tab is shown. Each
       panel holds a white .pko-panel-card (the description/steps/CTA +
       highlight-card grid) and, for Signature only, an extra
       .pko-signature-categories card stacked beneath it. */
    .pko-panel { display: none; }
    .pko-panel--active { display: block; }
    .pko-panel-card {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 36px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(28,35,33,0.06);
        padding: 40px;
    }
    .pko-panel-badge {
        display: inline-block;
        margin-left: 10px;
        background: var(--pko-sand);
        color: var(--pko-terracotta);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 3px 10px;
        border-radius: 999px;
        vertical-align: middle;
    }
    /* Personalized/Diamond tier uses a light teal accent instead of the
       gold/sand used for Signature's badges. */
    .pko-panel-badge--diamond { background: var(--pko-mint); color: var(--pko-teal); }
    .pko-panel-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 27px;
        font-weight: 600;
        margin: 10px 0 12px;
        color: var(--pko-ink);
    }
    .pko-panel-desc { font-size: 14px; line-height: 1.75; color: var(--pko-text); margin: 0 0 20px; }
    .pko-panel-bestfor {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: var(--pko-cream);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 22px;
    }
    .pko-panel-bestfor .icon {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--pko-gold);
        color: var(--pko-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .pko-panel-bestfor strong { display: block; font-size: 13px; color: var(--pko-ink); }
    .pko-panel-bestfor span { display: block; font-size: 12px; color: var(--pko-text); margin-top: 2px; }
    .pko-panel-steps { margin-bottom: 26px; }
    .pko-panel-step {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: var(--pko-ink);
        margin-bottom: 10px;
    }
    .pko-panel-step span {
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--pko-sand);
        color: var(--pko-green);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pko-panel-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--pko-green);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        padding: 13px 22px;
        border-radius: 999px;
        transition: background .2s ease, gap .2s ease;
    }
    .pko-panel-cta:hover { background: var(--pko-terracotta); gap: 12px; color: #fff; }

    /* Dark highlight card on the right of every panel. */
    .pko-panel-side {
        background: linear-gradient(160deg, var(--pko-green) 0%, #0F2E24 100%);
        border-radius: 16px;
        padding: 28px;
        color: #fff;
    }
    .pko-panel-side-tag {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--pko-gold-light);
        margin-bottom: 10px;
    }
    .pko-panel-side-title { font-family: 'Playfair Display', Georgia, serif; font-size: 19px; font-weight: 600; margin: 0 0 20px; }
    .pko-panel-side ul { list-style: none; margin: 0 0 4px; padding: 0; }
    .pko-panel-side li { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 14px; }
    .pko-panel-side li i { color: var(--pko-gold); margin-top: 3px; flex-shrink: 0; }
    .pko-panel-side li b { display: block; font-size: 12.5px; color: #fff; }
    .pko-panel-side li span { display: block; font-size: 11.5px; color: var(--pko-cream-text-2); margin-top: 1px; }
    /* Reference marks some items as NOT included (e.g. "No matchmaker
       suggestions" on the Online plan) with a muted dash instead of a
       checkmark, distinguishing them from what the plan does include. */
    .pko-panel-side li.pko-panel-side-li--off { opacity: .6; }
    .pko-panel-side li.pko-panel-side-li--off i { color: var(--pko-cream-text-2); }
    .pko-panel-side-foot {
        display: flex;
        align-items: center;
        gap: 10px;
        border-top: 1px solid rgba(255,255,255,0.15);
        padding-top: 16px;
        margin-top: 18px;
    }
    .pko-panel-side-foot .avatar {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        color: var(--pko-gold-light);
        font-size: 10.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pko-panel-side-foot b { display: block; font-size: 11.5px; color: #fff; }
    .pko-panel-side-foot span { display: block; font-size: 11px; color: var(--pko-cream-text-2); }

    /* Signature-only: the "Selected Profile Categories" card stacked below
       the main panel card. */
    .pko-signature-categories {
        margin-top: 24px;
        background: linear-gradient(160deg, var(--pko-green) 0%, #0F2E24 100%);
        border-radius: 20px;
        padding: 36px 40px;
        color: #fff;
    }
    .pko-signature-categories .pko-eyebrow { color: var(--pko-gold-light); }
    .pko-signature-categories .pko-eyebrow::before { background: var(--pko-gold-light); }
    .pko-signature-categories h4 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 21px;
        font-weight: 600;
        margin: 10px 0 8px;
        color: #fff;
    }
    .pko-signature-categories > p { font-size: 13px; line-height: 1.7; color: var(--pko-cream-text-2); margin: 0 0 24px; max-width: 640px; }
    .pko-cat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .pko-cat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #fff;
        line-height: 1.35;
    }
    .pko-cat-item .icon {
        flex-shrink: 0;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--pko-gold);
        color: var(--pko-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .pko-signature-categories .pko-cat-note {
        font-size: 11.5px;
        line-height: 1.6;
        color: var(--pko-cream-text-2);
        border-top: 1px solid rgba(255,255,255,0.12);
        padding-top: 16px;
        margin: 0;
    }
    .pko-signature-categories .pko-cat-note strong { color: #fff; }

    /* Personalized-only: the "Diamond Network" card — same layout as the
       Signature categories card, but a light teal theme instead of dark
       green/gold, to visually separate the two tiers. */
    .pko-diamond-network {
        margin-top: 24px;
        background: var(--pko-mint);
        border: 1px solid var(--pko-teal-line);
        border-radius: 20px;
        padding: 36px 40px;
        color: var(--pko-ink);
    }
    .pko-diamond-network .pko-eyebrow { color: var(--pko-teal); }
    .pko-diamond-network .pko-eyebrow::before { background: var(--pko-teal); }
    .pko-diamond-network h4 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 21px;
        font-weight: 600;
        margin: 10px 0 8px;
        color: var(--pko-ink);
    }
    .pko-diamond-network > p { font-size: 13px; line-height: 1.7; color: var(--pko-text); margin: 0 0 24px; max-width: 640px; }
    .pko-diamond-network .pko-cat-item {
        background: #fff;
        border: 1px solid var(--pko-teal-line);
        color: var(--pko-ink);
    }
    .pko-diamond-network .pko-cat-item .icon { background: var(--pko-teal); color: #fff; }
    .pko-diamond-network .pko-cat-note {
        font-size: 11.5px;
        line-height: 1.6;
        color: var(--pko-text);
        border-top: 1px solid var(--pko-teal-line);
        padding-top: 16px;
        margin: 0;
    }
    .pko-diamond-network .pko-cat-note strong { color: var(--pko-ink); }

    /* ============ EXECUTIVE / CEO ============ */
    /* This section is full-bleed / "container-fluid" — a sand-tinted band
       edge to edge — with its card content capped and centered inside
       (.pko-exec-inner), so the page alternates flat and full-bleed
       sections instead of feeling uniformly boxed or uniformly stretched. */
    .pko-exec {
        margin: 20px 0 0;
        padding: 56px 40px;
        background: var(--pko-sand);
    }
    .pko-exec-inner { max-width: 1100px; margin: 0 auto; }
    .pko-exec-card {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 32px;
        align-items: center;
        box-shadow: 0 20px 50px rgba(28,35,33,0.08);
    }
    .pko-exec-photo {
        width: 108px;
        height: 108px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--pko-sand);
        box-shadow: 0 8px 20px rgba(28,35,33,0.12);
        flex-shrink: 0;
    }
    .pko-exec-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .pko-exec-name { font-family: 'Playfair Display', Georgia, serif; font-size: 19px; font-weight: 600; margin: 0 0 2px; }
    .pko-exec-role { font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--pko-terracotta); margin-bottom: 12px; }
    .pko-exec-text { font-size: 13.5px; line-height: 1.75; color: var(--pko-text); margin: 0 0 10px; }
    .pko-exec-quote { font-style: italic; font-size: 13px; color: var(--pko-ink); border-left: 3px solid var(--pko-gold); padding-left: 14px; margin: 0 0 16px; }
    .pko-exec-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--pko-green);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        padding: 11px 20px;
        border-radius: 999px;
    }
    .pko-exec-cta:hover { background: var(--pko-terracotta); color: #fff; }

    /* ============ FINAL CTA ============ */
    /* Also full-bleed / "container-fluid" — a soft mint band bookending the
       page (echoing the green hero) — with its content capped and centered
       via .pko-cta-inner. */
    .pko-cta {
        margin: 40px 0 0;
        padding: 56px 40px 64px;
        background: var(--pko-mint);
    }
    .pko-cta-inner { max-width: 1240px; margin: 0 auto; }
    .pko-cta-card {
        background: linear-gradient(135deg, var(--pko-green) 0%, #1F5C46 100%);
        border-radius: 20px;
        padding: 44px;
        text-align: center;
    }
    .pko-cta-card h2 { font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 600; color: #fff; margin: 0 0 10px; }
    .pko-cta-card p { max-width: 560px; margin: 0 auto 26px; font-size: 13.5px; line-height: 1.75; color: var(--pko-cream-text-2); }
    .pko-cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .pko-btn-appt, .pko-btn-wa {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 22px;
        border-radius: 999px;
    }
    .pko-btn-appt { background: var(--pko-gold); color: var(--pko-green); }
    .pko-btn-appt:hover { background: var(--pko-gold-light); color: var(--pko-green); }
    .pko-btn-wa { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3); }
    .pko-btn-wa:hover { background: rgba(255,255,255,0.18); color: #fff; }

    .pko-cta-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 18px; align-items: stretch; }
    .pko-cta-grid .pko-cta-card { text-align: left; display: flex; flex-direction: column; justify-content: center; }
    .pko-cta-grid .pko-cta-card p { margin: 0 0 20px; }
    .pko-cta-grid .pko-cta-actions { justify-content: flex-start; }
    .pko-office-card {
        background: #fff;
        border: 1px solid var(--pko-line);
        border-radius: 20px;
        padding: 28px 24px;
    }
    .pko-office-eyebrow { font-size: 10.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--pko-terracotta); margin-bottom: 8px; }
    .pko-office-name { font-family: 'Playfair Display', Georgia, serif; font-size: 16px; font-weight: 600; margin: 0 0 8px; display: flex; align-items: center; gap: 8px; }
    .pko-office-name i { color: var(--pko-gold); }
    .pko-office-addr { font-size: 12.5px; line-height: 1.6; color: var(--pko-text); margin: 0; }
    .pko-cta-note { text-align: center; font-size: 12px; color: var(--pko-text); margin-top: 16px; }
    .pko-cta-note strong { color: var(--pko-ink); }

    @media (max-width: 980px) {
        .pko-panel-card { grid-template-columns: 1fr; }
        .pko-cat-grid { grid-template-columns: repeat(2, 1fr); }
        .pko-exec-card { grid-template-columns: 1fr; text-align: center; }
        .pko-exec-photo { margin: 0 auto; }
        .pko-exec-quote { border-left: none; padding-left: 0; }
        .pko-cta-grid { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 700px) {
        .pko-tabs { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .pko-plans, .pko-exec, .pko-cta { padding-left: 20px; padding-right: 20px; }
        .pko-exec-card, .pko-cta-card, .pko-panel-card, .pko-signature-categories, .pko-diamond-network { padding: 28px 22px; }
        .pko-cat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="pko-page">

    <!-- HERO -->
    <section class="pko-hero">
        <div class="pko-eyebrow pko-hero-eyebrow">Our Services</div>
        <h1>Choose the right way to<br>find your <em>match</em>.</h1>
        <p>From an independent online search to private CEO-led matchmaking, select the level of support that suits your journey.</p>
        <div class="pko-hero-trust">
            <div><span>✓</span> 16+ Years of Trust</div>
        </div>
    </section>

    <!-- THREE PLANS -->
    <section class="pko-plans">
        <div class="pko-plans-container">
            <!-- TAB SELECTOR -->
            <div class="pko-tabs">
                <button type="button" class="pko-tab pko-tab--active" data-plan="personalized" onclick="pkoShowPlan('personalized')">
                    <span class="pko-tab-num">01</span>
                    <span><span class="pko-tab-title">Personalized Plan</span><span class="pko-tab-sub">Manager-led matching</span></span>
                </button>
                <button type="button" class="pko-tab" data-plan="signature" onclick="pkoShowPlan('signature')">
                    <span class="pko-tab-badge">✦ Most Exclusive</span>
                    <span class="pko-tab-num">02</span>
                    <span><span class="pko-tab-title">Signature Plan</span><span class="pko-tab-sub">Personally CEO managed</span></span>
                </button>
                <button type="button" class="pko-tab" data-plan="online" onclick="pkoShowPlan('online')">
                    <span class="pko-tab-num">03</span>
                    <span><span class="pko-tab-title">Online Plan</span><span class="pko-tab-sub">Self-managed search</span></span>
                </button>
            </div>

            <!-- 01. PERSONALIZED PANEL -->
            <div class="pko-panel pko-panel--active" id="pko-panel-personalized">
              <div class="pko-panel-card">
                <div>
                    <div class="pko-eyebrow">Plan 01 &middot; Guided<span class="pko-panel-badge pko-panel-badge--diamond">&#128142; Diamond Category</span></div>
                    <h3 class="pko-panel-title">Premium Personalized Plan</h3>
                    <p class="pko-panel-desc">A confidential matchmaking service for local Pakistani professional and business families, managed by our experienced relationship managers.</p>
                    <div class="pko-panel-bestfor">
                        <span class="icon"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
                        <div>
                            <strong>For families residing in Pakistan</strong>
                            <span>A professionally managed search with suitable, curated proposals.</span>
                        </div>
                    </div>
                    <div class="pko-panel-steps">
                        <div class="pko-panel-step"><span>1</span> Your profile and preferences are reviewed</div>
                        <div class="pko-panel-step"><span>2</span> A relationship manager curates suitable proposals</div>
                        <div class="pko-panel-step"><span>3</span> Introductions and family meetings are coordinated privately</div>
                    </div>
                    <a href="{{ url('packages') }}?type=personalized" class="pko-panel-cta">View Personalized Plan <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="pko-panel-side">
                    <div class="pko-panel-side-tag">At A Glance</div>
                    <div class="pko-panel-side-title">Expert guidance, every step.</div>
                    <ul>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Dedicated relationship manager</b><span>A real person manages your search</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Curated Pakistani proposals</b><span>Options selected around your preferences</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Private profile sharing</b><span>Your information is shared with approval</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Family meeting support</b><span>Our team helps coordinate the next step</span></div></li>
                    </ul>
                </div>
              </div>

              <!-- Personalized-only: Diamond Network professional/business categories -->
              <div class="pko-diamond-network">
                <div class="pko-eyebrow">&#128142; Diamond Network</div>
                <h4>Professional &amp; business profiles in Pakistan</h4>
                <p>For local Pakistani families seeking professionally managed introductions within respected career and business circles.</p>
                <div class="pko-cat-grid">
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Doctors &amp; Medical Professionals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Engineers &amp; Technology Professionals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Corporate &amp; Senior Job Holders</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Bankers &amp; Finance Professionals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Chartered Accountants (CA)</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> ACCA Professionals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Pakistani Families</div>
                </div>
                <p class="pko-cat-note"><strong>Relationship-manager led:</strong> Proposals are shared according to profile verification, mutual consent, suitability and current availability.</p>
              </div>
            </div>

            <!-- 02. SIGNATURE PANEL -->
            <div class="pko-panel" id="pko-panel-signature">
              <div class="pko-panel-card">
                <div>
                    <div class="pko-eyebrow">Plan 02 &middot; Exclusive<span class="pko-panel-badge">✦ Premier Executive Matchmaking</span></div>
                    <h3 class="pko-panel-title">Signature Plan</h3>
                    <p class="pko-panel-desc">Our most discreet, executive-level matchmaking service for overseas families and reputed business-class families in Pakistan.</p>
                    <div class="pko-panel-bestfor">
                        <span class="icon"><i class="fa fa-star" aria-hidden="true"></i></span>
                        <div>
                            <strong>For overseas and reputed business-class families</strong>
                            <span>Selective, confidential matchmaking personally overseen by our CEO.</span>
                        </div>
                    </div>
                    <div class="pko-panel-steps">
                        <div class="pko-panel-step"><span>1</span> Private consultation and detailed profile assessment</div>
                        <div class="pko-panel-step"><span>2</span> Discreet cross-border and elite-family search</div>
                        <div class="pko-panel-step"><span>3</span> Personal CEO oversight from selection to introduction</div>
                    </div>
                    <a href="{{ url('packages') }}?type=signature" class="pko-panel-cta">Explore Signature Plan <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="pko-panel-side">
                    <div class="pko-panel-side-tag">CEO Executive Service</div>
                    <div class="pko-panel-side-title">Private. Selective. Personal.</div>
                    <ul>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Overseas family network</b><span>Focused support across international markets</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Reputed business-class families</b><span>Selective proposals handled with discretion</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Executive-level confidentiality</b><span>Sensitive profiles stay private</span></div></li>
                    </ul>
                    <div class="pko-panel-side-foot">
                        <div class="avatar">UZ</div>
                        <div>
                            <b>Personally handled by Usman Zaheer</b>
                            <span>CEO, Urgent Rishta</span>
                        </div>
                    </div>
                </div>
              </div>

              <!-- Signature-only: search network / eligible profile categories -->
              <div class="pko-signature-categories">
                <div class="pko-eyebrow">Signature Search Network</div>
                <h4>Selected profile categories</h4>
                <p>A discreet search for distinguished profiles in Pakistan and across international communities.</p>
                <div class="pko-cat-grid">
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Overseas Proposals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Senior Professionals &amp; Executives</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Doctors &amp; Medical Professionals</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Citizens &amp; Permanent Residents</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Well-Settled Profiles</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Affluent Family Profiles</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> High-Net-Worth Business Families</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Celebrities &amp; Public Figures</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Second Marriage Profiles</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Luxury Lifestyle Profiles</div>
                    <div class="pko-cat-item"><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> Political &amp; Influential Families</div>
                </div>
                <p class="pko-cat-note"><strong>Private &amp; selective:</strong> Every introduction is subject to profile verification, mutual consent, suitability and current availability.</p>
              </div>
            </div>

            <!-- 03. ONLINE PANEL -->
            <div class="pko-panel" id="pko-panel-online">
              <div class="pko-panel-card">
                <div>
                    <div class="pko-eyebrow">Plan 03 &middot; Independent</div>
                    <h3 class="pko-panel-title">Online Plan</h3>
                    <p class="pko-panel-desc">A flexible, self-managed option for clients who prefer to search and explore profiles independently.</p>
                    <div class="pko-panel-bestfor">
                        <span class="icon"><i class="fa fa-desktop" aria-hidden="true"></i></span>
                        <div>
                            <strong>Best for independent clients</strong>
                            <span>You control your own search, interests and connections.</span>
                        </div>
                    </div>
                    <div class="pko-panel-steps">
                        <div class="pko-panel-step"><span>1</span> Create and complete your profile</div>
                        <div class="pko-panel-step"><span>2</span> Search suitable profiles yourself</div>
                        <div class="pko-panel-step"><span>3</span> Send interest and connect after acceptance</div>
                    </div>
                    <a href="{{ url('packages') }}?type=online" class="pko-panel-cta">View Online Plan <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="pko-panel-side">
                    <div class="pko-panel-side-tag">At A Glance</div>
                    <div class="pko-panel-side-title">Your search, your pace.</div>
                    <ul>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Self-service profile search</b><span>Browse using your own preferences</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Direct interest requests</b><span>Connect when interest is mutually accepted</span></div></li>
                        <li><i class="fa fa-check" aria-hidden="true"></i><div><b>Flexible package options</b><span>Choose access based on your requirements</span></div></li>
                        <li class="pko-panel-side-li--off"><i class="fa fa-minus" aria-hidden="true"></i><div><b>No matchmaker suggestions</b><span>Our team does not personally propose matches</span></div></li>
                    </ul>
                </div>
              </div>
            </div>
        </div>
    </section>

    <script>
        function pkoShowPlan(plan) {
            document.querySelectorAll('.pko-tab').forEach(function (t) {
                t.classList.toggle('pko-tab--active', t.getAttribute('data-plan') === plan);
            });
            document.querySelectorAll('.pko-panel').forEach(function (p) {
                p.classList.toggle('pko-panel--active', p.id === 'pko-panel-' + plan);
            });
        }
    </script>

    <!-- EXECUTIVE / CEO -->
    <section class="pko-exec">
        <div class="pko-exec-inner">
            <div class="pko-exec-card">
                <div class="pko-exec-photo"><img src="/images/profiles/5.jpeg" alt="Usman Zaheer" loading="lazy"></div>
                <div>
                    <div class="pko-exec-name">Usman Zaheer</div>
                    <div class="pko-exec-role">Founder &amp; CEO — Urgent Rishta</div>
                    <p class="pko-exec-text">The Signature Plan is personally handled by our CEO — every match is reviewed against your family's values and vision before it's ever suggested.</p>
                    <p class="pko-exec-quote">&ldquo;Great matches are not based on status or education — they are built on understanding and compatibility.&rdquo;</p>
                    <a href="{{ url('appointments') }}" class="pko-exec-cta"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Book a Private Consultation</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CTA + OFFICES -->
    <section class="pko-cta">
        <div class="pko-cta-inner">
            <div class="pko-cta-grid">
                <div class="pko-cta-card">
                    <h2>Not sure which service is right?</h2>
                    <p>Book a private consultation and our team will guide you to the most suitable plan.</p>
                    <div class="pko-cta-actions">
                        <a href="{{ url('appointments') }}" class="pko-btn-appt"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Book Appointment</a>
                        <a href="https://wa.me/923040227000" target="_blank" rel="noopener" class="pko-btn-wa"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp Us</a>
                    </div>
                </div>
                <div class="pko-office-card">
                    <div class="pko-office-eyebrow">Pakistan</div>
                    <div class="pko-office-name"><i class="fa fa-map-marker" aria-hidden="true"></i> Lahore Office</div>
                    <p class="pko-office-addr">114 A, B Block, River View Housing Society, Near Abdul Sattar Edhi Road, Lahore</p>
                </div>
                <div class="pko-office-card">
                    <div class="pko-office-eyebrow">United Kingdom</div>
                    <div class="pko-office-name"><i class="fa fa-map-marker" aria-hidden="true"></i> Manchester Office</div>
                    <p class="pko-office-addr">Universal Square, Devonshire St N, Manchester M12 6JH</p>
                </div>
            </div>
            <div class="pko-cta-note"><strong>Appointment required</strong> before every office visit.</div>
        </div>
    </section>

</div>
@endsection
