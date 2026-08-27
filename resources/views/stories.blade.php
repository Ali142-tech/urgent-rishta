@extends('layouts.master')
@section('main-content')
{{-- Uses the site-wide Font Awesome 4 already loaded by layouts.master — do NOT add
     a second Font Awesome (e.g. v6) stylesheet here, it conflicts with the FA4 icon
     classes used across the rest of the site (footer social icons, etc.) and breaks them. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .ss-page {
        --ss-green: #123A2E;
        --ss-green-deep: #0F2E24;
        --ss-gold: #C9974D;
        --ss-cream: #FBF7EF;
        --ss-sand: #EFE7D6;
        --ss-line: #F0EADD;
        --ss-terracotta: #B5674A;
        --ss-text: #5B6560;
        --ss-ink: #1C2321;
        --ss-cream-text: #EFE3C8;
        --ss-cream-text-2: #D7E4DC;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--ss-cream);
        color: var(--ss-ink);
    }
    .ss-page * { box-sizing: border-box; }
    .ss-page a { text-decoration: none; }

    /* Hero */
    .ss-hero {
        background: linear-gradient(135deg, var(--ss-green) 0%, #1F5C46 55%, var(--ss-green) 100%);
        padding: 64px 20px 50px;
        text-align: center;
    }
    .ss-hero__badge {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(239,227,200,0.3);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--ss-cream-text);
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .ss-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 14px;
        line-height: 1.25;
    }
    .ss-hero h1 em { color: var(--ss-gold); font-style: italic; }
    .ss-hero p {
        font-size: 15.5px;
        color: var(--ss-cream-text-2);
        max-width: 560px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Stories grid */
    .ss-wrap { max-width: 1180px; margin: 0 auto; padding: 64px 24px 80px; }
    .ss-grid {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }
    .ss-card {
        display: flex;
        align-items: stretch;
        background: #fff;
        border: 1px solid var(--ss-line);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15,46,36,.06);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ss-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(15,46,36,.12); }
    /* Alternate: odd cards -> image left, text right. even cards -> image right, text left. */
    .ss-card:nth-child(even) { flex-direction: row-reverse; }
    .ss-card__image { flex: 0 0 50%; max-width: 50%; overflow: hidden; }
    .ss-card__image img { width: 100%; height: 100%; min-height: 340px; object-fit: cover; display: block; object-position: center 15%; }
    .ss-card__body { flex: 0 0 50%; max-width: 50%; padding: 40px 44px; display: flex; flex-direction: column; justify-content: center; }
    .ss-card__names {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px;
        font-weight: 600;
        color: var(--ss-ink);
        margin: 0 0 8px;
    }
    .ss-card__meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        color: var(--ss-terracotta);
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .ss-card__meta i { font-size: 11px; }
    .ss-card__headline {
        font-family: 'Playfair Display', Georgia, serif;
        font-style: italic;
        font-size: 16px;
        font-weight: 600;
        color: var(--ss-green);
        margin: 0 0 10px;
    }
    .ss-card__text {
        font-size: 14px;
        line-height: 1.75;
        color: var(--ss-text);
        margin: 0 0 16px;
    }
    .ss-card__status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        color: var(--ss-green);
        background: var(--ss-sand);
        padding: 7px 14px;
        border-radius: 99px;
    }
    .ss-card__status i { color: var(--ss-gold); font-size: 11px; }

    .ss-disclaimer {
        text-align: center;
        font-size: 12.5px;
        font-style: italic;
        color: var(--ss-text);
        max-width: 560px;
        margin: 40px auto 0;
    }

    .ss-cta { text-align: center; margin-top: 40px; }
    .ss-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14.5px;
        padding: 15px 32px;
        border-radius: 8px;
        border: 1.5px solid transparent;
        transition: .2s ease;
    }
    .ss-btn--solid { background: var(--ss-green); color: var(--ss-cream-text) !important; }
    .ss-btn--solid:hover { background: var(--ss-green-deep); }

    @media (max-width: 767px) {
        .ss-card,
        .ss-card:nth-child(even) { flex-direction: column; }
        .ss-card__image,
        .ss-card__body { flex: 0 0 100%; max-width: 100%; }
        .ss-card__image img { min-height: 230px; }
        .ss-card__body { padding: 28px 28px 30px; }
        .ss-wrap { padding: 48px 18px 60px; }
        .ss-hero { padding: 48px 18px 40px; }
    }
</style>

<div class="ss-page">
    <section class="ss-hero">
        <div class="ss-hero__badge">Happy Endings</div>
        <h1>Real People. Meaningful Connections. <em>Successful Matches.</em></h1>
        <p>Behind every successful match are two individuals and two families who found the confidence to take the next step.</p>
    </section>

    <div class="ss-wrap">
        <div class="ss-grid">
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/dubai-couple.jpg" alt="Ayesha and Rayed" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Ayesha &amp; Rayed</h3>
                    <div class="ss-card__meta"><i class="fa fa-map-marker" aria-hidden="true"></i> Dubai, UAE &nbsp;🇦🇪</div>
                    <div class="ss-card__headline">A Match Built on Understanding</div>
                    <p class="ss-card__text">Ayesha and Rayed were introduced through Urgent Rishta after both families shared their preferences with our matchmaking team. Their values, expectations and personalities aligned naturally. After family discussions and mutual understanding, they decided to begin their journey together.</p>
                    <div class="ss-card__status"><i class="fa fa-check-circle" aria-hidden="true"></i> Successfully Matched in Dubai</div>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/uk-couple.jpg" alt="Dr. Usman and Dr. Rabia" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Dr. Usman &amp; Dr. Rabia</h3>
                    <div class="ss-card__meta"><i class="fa fa-map-marker" aria-hidden="true"></i> United Kingdom &nbsp;🇬🇧</div>
                    <div class="ss-card__headline">Two Doctors, One Beautiful Journey</div>
                    <p class="ss-card__text">Both Dr. Usman and Dr. Rabia were looking for an educated, professionally compatible partner with strong family values. Our team introduced their profiles after carefully reviewing their requirements. The families connected, the couple found compatibility, and the match successfully moved forward.</p>
                    <div class="ss-card__status"><i class="fa fa-check-circle" aria-hidden="true"></i> Successfully Matched in the UK</div>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/second-marriage-couple.jpg" alt="Abdullah and Sarah" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Abdullah &amp; Sarah</h3>
                    <div class="ss-card__meta"><i class="fa fa-map-marker" aria-hidden="true"></i> Saudi Arabia &nbsp;🇸🇦</div>
                    <div class="ss-card__headline">From Introduction to a Meaningful Relationship</div>
                    <p class="ss-card__text">Abdullah and Sarah were searching for a serious marriage proposal with compatible family backgrounds and shared values. After a carefully selected introduction through Urgent Rishta, both families developed mutual confidence and the couple found the understanding they were looking for.</p>
                    <div class="ss-card__status"><i class="fa fa-check-circle" aria-hidden="true"></i> Successfully Matched in Saudi Arabia</div>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/us-couple.jpg" alt="Adam and Sophia" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Adam &amp; Sophia</h3>
                    <div class="ss-card__meta"><i class="fa fa-map-marker" aria-hidden="true"></i> United States &nbsp;🇺🇸</div>
                    <div class="ss-card__headline">Distance Was Never a Barrier</div>
                    <p class="ss-card__text">Adam and Sophia were both looking for a genuine, family-oriented life partner in the United States. Their requirements were carefully reviewed before an introduction was arranged. With mutual interest and family involvement, their initial introduction developed into a successful match.</p>
                    <div class="ss-card__status"><i class="fa fa-check-circle" aria-hidden="true"></i> Successfully Matched in the USA</div>
                </div>
            </div>
        </div>

        {{-- <p class="ss-disclaimer">Names and identifying details may be changed to protect our clients' privacy.</p> --}}

        <div class="ss-cta">
            <a href="/register" class="ss-btn ss-btn--solid">Start Your Journey</a>
        </div>
    </div>
</div>
@endsection
