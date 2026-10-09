@extends('layouts.master')
@section('main-content')
{{-- Uses the site-wide Font Awesome 4 already loaded by layouts.master — do NOT add
     a second Font Awesome (e.g. v6) stylesheet here, it conflicts with the FA4 icon
     classes used across the rest of the site (footer social icons, etc.) and breaks them. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .gl-page {
        --gl-green: #123A2E;
        --gl-green-deep: #0F2E24;
        --gl-gold: #C9974D;
        --gl-cream: #FBF7EF;
        --gl-terracotta: #B5674A;
        --gl-text: #5B6560;
        --gl-cream-text: #EFE3C8;
        --gl-cream-text-2: #D7E4DC;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--gl-cream);
    }
    .gl-page * { box-sizing: border-box; }

    .gl-hero {
        background: linear-gradient(135deg, var(--gl-green) 0%, #1F5C46 55%, var(--gl-green) 100%);
        padding: 64px 20px 50px;
        text-align: center;
    }
    .gl-hero__badge {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(239,227,200,0.3);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--gl-cream-text);
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .gl-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 14px;
        line-height: 1.25;
    }
    .gl-hero p {
        font-size: 15.5px;
        color: var(--gl-cream-text-2);
        max-width: 560px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .gl-wrap { max-width: 1240px; margin: 0 auto; padding: 56px 16px 56px; }
    .gl-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: 220px;
        grid-auto-flow: dense;
        gap: 12px;
    }
    .gl-grid__item {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15,46,36,.08);
    }
    .gl-grid__item--tall { grid-row: span 2; }
    .gl-grid__item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .5s cubic-bezier(.22,1,.36,1);
    }
    .gl-grid__item:hover img { transform: scale(1.08); }
    .gl-grid__item__caption {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 14px 14px 12px;
        background: linear-gradient(180deg, transparent 0%, rgba(15,46,36,.85) 100%);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
        opacity: 0;
        transition: opacity .25s ease;
    }
    .gl-grid__item:hover .gl-grid__item__caption { opacity: 1; }

    @media (max-width: 991px) {
        .gl-grid { grid-template-columns: repeat(3, 1fr); grid-auto-rows: 180px; }
    }
    @media (max-width: 700px) {
        .gl-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 160px; }
        .gl-grid__item--tall { grid-row: span 1; }
    }
</style>

<div class="gl-page">
    <section class="gl-hero">
        <div class="gl-hero__badge">Behind The Scenes</div>
        <h1>Inside Urgent Rishta</h1>
        <p>A look at our team at industry seminars, matchmaking events and moments we're proud to share.</p>
    </section>

    <div class="gl-wrap">
        <div class="gl-grid">
            <div class="gl-grid__item gl-grid__item--tall">
                <img src="/images/gallery/1.jpg" alt="Urgent Rishta team at an industry matchmaking seminar" loading="lazy">
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/2.jpg" alt="Urgent Rishta founder in conversation at an industry event" loading="lazy">
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/3.jpg" alt="Urgent Rishta representatives at a professional marriage consultants seminar" loading="lazy">
            </div>
            <div class="gl-grid__item gl-grid__item--tall">
                <img src="/images/gallery/4.jpg" alt="Certificate presentation at an industry seminar" loading="lazy">
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/5.jpg" alt="Certificate presentation at an industry seminar" loading="lazy">
            </div>
            <div class="gl-grid__item gl-grid__item--tall">
                <img src="/images/gallery/6.jpg" alt="Certificate presentation at an industry seminar" loading="lazy">
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/7.jpg" alt="Urgent Rishta Services recognised at an industry seminar" loading="lazy">
                <div class="gl-grid__item__caption">Recognised for Urgent Rishta Services</div>
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/8.jpg" alt="Urgent Rishta representatives with fellow delegates at an industry seminar" loading="lazy">
            </div>
            <div class="gl-grid__item gl-grid__item--tall">
                <img src="/images/gallery/9.jpg" alt="Urgent Rishta founder speaking with the media" loading="lazy">
            </div>
            <div class="gl-grid__item">
                <img src="/images/gallery/10.jpg" alt="Urgent Rishta team and guests at an industry gathering" loading="lazy">
            </div>
        </div>
    </div>
</div>

@include('partials.partners-section')
@endsection
