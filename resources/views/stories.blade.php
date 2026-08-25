@extends('layouts.master')
@section('main-content')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
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
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }
    .ss-card {
        background: #fff;
        border: 1px solid var(--ss-line);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15,46,36,.06);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ss-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(15,46,36,.12); }
    .ss-card__image { height: 230px; overflow: hidden; }
    .ss-card__image img { width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 15%; }
    .ss-card__body { padding: 26px 24px 28px; }
    .ss-card__names {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 21px;
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
        margin-bottom: 14px;
    }
    .ss-card__meta i { font-size: 11px; }
    .ss-card__text {
        font-size: 14px;
        line-height: 1.7;
        color: var(--ss-text);
        margin: 0;
    }

    .ss-cta { text-align: center; margin-top: 56px; }
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

    @media (max-width: 991px) {
        .ss-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .ss-grid { grid-template-columns: 1fr; }
        .ss-wrap { padding: 48px 18px 60px; }
        .ss-hero { padding: 48px 18px 40px; }
    }
</style>

<div class="ss-page">
    <section class="ss-hero">
        <div class="ss-hero__badge">Happy Endings</div>
        <h1>Real People. <em>Meaningful Beginnings.</em></h1>
        <p>Every successful introduction reminds us that the right match can change two families forever.</p>
    </section>

    <div class="ss-wrap">
        <div class="ss-grid">
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/pakistani-couple.jpg" alt="Bilal and Fatima" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Bilal &amp; Fatima</h3>
                    <div class="ss-card__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Lahore, Pakistan &nbsp;&middot;&nbsp; Married 2025</div>
                    <p class="ss-card__text">Introduced through Urgent Rishta's personal matchmaking process, their families connected quickly over shared values — leading to a wedding in 2025.</p>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/arabic-couple.jpg" alt="Ali and Mahnoor" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Ali &amp; Mahnoor</h3>
                    <div class="ss-card__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Dubai, UAE</div>
                    <p class="ss-card__text">A discreet, family-first introduction that grew into a lasting partnership.</p>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/second-marriage-couple.jpg" alt="Omar and Hira" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Omar &amp; Hira</h3>
                    <div class="ss-card__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Abu Dhabi, UAE</div>
                    <p class="ss-card__text">Distance was never a barrier — a well-matched introduction brought two families together across continents.</p>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/usa-couple.jpg" alt="Zain and Ayesha" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Zain &amp; Ayesha</h3>
                    <div class="ss-card__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> New York, United States</div>
                    <p class="ss-card__text">A thoughtful introduction between two overseas Pakistani families, built on trust and mutual respect.</p>
                </div>
            </div>
            <div class="ss-card">
                <div class="ss-card__image"><img src="/images/couples/indian-couple.jpg" alt="Hamza and Zara" loading="lazy"></div>
                <div class="ss-card__body">
                    <h3 class="ss-card__names">Hamza &amp; Zara</h3>
                    <div class="ss-card__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Udaipur, India</div>
                    <p class="ss-card__text">Matched through careful profile review, their journey from introduction to marriage was guided every step of the way.</p>
                </div>
            </div>
        </div>

        <div class="ss-cta">
            <a href="/register" class="ss-btn ss-btn--solid">Start Your Journey</a>
        </div>
    </div>
</div>
@endsection
