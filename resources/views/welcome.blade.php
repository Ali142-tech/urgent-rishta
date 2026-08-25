@extends('layouts.master')
@section('main-content')
<link rel="stylesheet" href="/css/ur-hero.css?29">
{{-- Variation 1a — Editorial Luxe (emerald + gold). ur-1a.css is loaded after the page styles. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,600;1,700&family=DM+Sans:wght@400;500;600;700&display=swap');

    /* Hero styles moved to /css/ur-hero.css */

    a.btn.btn-styled.btn-xs.btn-base-1.btn-shadow {
        font-size: 12px !important;
    }

    .hom-couples-all {
        background: wheat;
    }
    .wedd-gall.home-wedd-gall, .ab-team {
        background: wheat;
    }
    /* Overlay Background */
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
    }
    .popup-content {
        background: white;
        padding: 25px;
        border-radius: 12px;
        max-width: 450px;
        width: 90%;
        text-align: left;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
    }
    .popup-content h2 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .package-price {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 15px;
    }
    .bank-details {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        color: #444;
        margin-top: 10px;
    }
    .bank-details p {
        margin: 8px 0;
        font-size: 14px;
    }
    .bank-details strong {
        color: #333;
    }
    .copy-icon {
        cursor: pointer;
        font-size: 14px;
        margin-left: 8px;
        color: #d63384;
    }
    .note-box {
        background: #ffe5e5;
        padding: 10px;
        border-radius: 8px;
        margin-top: 15px;
        font-size: 14px;
        color: #d63384;
        font-weight: bold;
    }
    .whatsapp-btn {
        background: #25d366;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
        transition: 0.3s;
        border: none;
        width: 100%;
        text-align: center;
    }
    .whatsapp-btn:hover {
        background: #1ebe57;
    }
    .whatsapp-btn img {
        width: 18px;
        margin-right: 8px;
    }
    .close-btn {
        background: #d63384;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .close-btn:hover {
        background: #b02a70;
    }

    /* ===== Option A full page sections (pink theme) ===== */
    .ur-page { --pink:#E91E63; --pink-dark:#C2185B; --pink-deep:#8E0E3E; --cream:#FFF8FB; --cream2:#FCEEF3; --ink:#1C2321; --muted:#5B6560; --line:#F0D3DF; }
    .ur-sec { padding: 76px 24px; }
    .ur-sec--cream { background: var(--cream); }
    .ur-sec--soft { background: var(--cream2); }
    .ur-sec--pink { background: var(--pink); }
    .ur-sec--deep { background: #1a0610; }
    .ur-wrap { max-width: 1180px; margin: 0 auto; }
    .ur-eyebrow { font-size: 11.5px; font-weight: 700; letter-spacing: .14em; color: var(--pink-dark); text-transform: uppercase; margin-bottom: 10px; }
    .ur-h2 { font-family: 'Playfair Display', Georgia, serif; font-size: clamp(28px, 3.5vw, 36px); font-weight: 600; margin: 0; color: var(--ink); line-height: 1.2; }
    .ur-lead { font-size: 15.5px; line-height: 1.8; color: var(--muted); margin: 0; }
    .ur-btn { display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; padding: 14px 28px; border-radius: 8px; text-decoration: none !important; transition: .2s ease; border: none; cursor: pointer; }
    .ur-btn--solid { background: var(--pink); color: #fff !important; }
    .ur-btn--solid:hover { background: var(--pink-dark); color: #fff !important; }
    .ur-btn--outline { background: transparent; border: 1.5px solid var(--pink); color: var(--pink) !important; }
    .ur-btn--outline:hover { background: var(--pink); color: #fff !important; }
    .ur-btn--light { background: transparent; border: 1.5px solid #FFE0EC; color: #FFE0EC !important; }
    .ur-btn--light:hover { background: #fff; color: var(--pink) !important; }

    .ur-promise2__top { display: grid; grid-template-columns: 1fr 1.15fr; gap: 40px 48px; align-items: center; margin-bottom: 56px; }
    .ur-promise2__top .ur-eyebrow,
    .ur-promise2__top .ur-h2,
    .ur-promise2__top .ur-lead { text-align: left; }
    .ur-promise2__top .ab-wel-lhs {
        position: relative;
        min-height: 430px;
        height: 430px;
    }
    .ur-promise2__top .ab-wel-1 {
        position: absolute;
        width: 74%;
        height: 80%;
        object-fit: cover;
        left: 0;
        top: 0;
        border-radius: 4px;
    }
    .ur-promise2__top .ab-wel-2 {
        width: 56%;
        height: 56%;
        object-fit: cover;
        position: absolute;
        bottom: 0;
        right: 0;
        border: 8px solid var(--cream);
        border-radius: 4px;
    }
    .ur-promise2__top .ur-years {
        position: absolute;
        bottom: 18%;
        left: 0;
        z-index: 3;
        background: var(--pink);
        color: #fff;
        padding: 14px 20px;
        border-radius: 4px;
    }
    .ur-promise2__top .ur-years b {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 26px;
        font-weight: 700;
        display: block;
        line-height: 1;
    }
    .ur-promise2__top .ur-years span {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .06em;
    }
    .ur-promise2__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
    .ur-promise2__card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 38px 30px;
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-promise2__card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(0,0,0,.07);
    }
    .ur-promise2__icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--cream2);
        color: var(--pink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 20px;
    }
    .ur-promise2__card-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 19px;
        font-weight: 600;
        color: var(--ink);
        margin: 0 0 10px;
    }
    .ur-promise2__card-text {
        font-size: 14px;
        line-height: 1.7;
        color: var(--muted);
        margin: 0;
    }
    .ur-promise2__cta { text-align: center; margin-top: 44px; }
    .ur-welcome-kicker {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px;
        color: #8A6A4A;
        font-weight: 600;
        margin: 0 0 2px;
        line-height: 1.2;
    }
    .ur-welcome-brand {
        font-size: clamp(30px, 3.4vw, 40px);
        font-weight: 800;
        color: var(--pink);
        letter-spacing: .02em;
        margin: 0 0 14px;
        line-height: 1.15;
        text-transform: uppercase;
    }
    .ur-contact-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid var(--line);
    }
    .ur-contact {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none !important;
        color: inherit;
    }
    .ur-contact__icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 50%;
        background: #1a0610;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .ur-contact__label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pink-dark);
        margin-bottom: 2px;
    }
    .ur-contact__value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 16px;
        font-weight: 600;
        color: var(--ink);
        word-break: break-word;
    }

    .ur-stats {
        background: #fff;
        border-radius: 14px;
        padding: 28px 20px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        align-items: stretch;
        border: 1px solid var(--line);
        box-shadow: 0 16px 40px rgba(26,6,16,.06);
    }
    .ur-stats__card {
        text-align: center;
        padding: 10px 16px;
        border-right: 1px solid var(--line);
    }
    .ur-stats__card:last-child { border-right: none; }
    .ur-stats__icon {
        width: 44px;
        height: 44px;
        margin: 0 auto 12px;
        border: 1px solid rgba(233,30,99,.25);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--pink);
        font-size: 18px;
        background: var(--cream);
    }
    .ur-stats__num {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 30px;
        color: var(--pink);
        line-height: 1;
        margin-bottom: 6px;
    }
    .ur-stats__sub {
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .ur-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
    .ur-step {
        position: relative;
        background: var(--cream);
        padding: 34px 26px;
        border-top: 3px solid var(--pink);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-step:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px rgba(0,0,0,.06);
    }
    .ur-step--dark { background: var(--pink); }
    .ur-step--dark .ur-step__title, .ur-step--dark .ur-step__num { color: #fff; }
    .ur-step--dark .ur-step__text { color: #FFE0EC; }
    .ur-step__icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--cream2);
        color: var(--pink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-bottom: 18px;
    }
    .ur-step--dark .ur-step__icon { background: rgba(255,255,255,.12); color: #fff; }
    .ur-step__num { font-family: 'Playfair Display', Georgia, serif; font-size: 34px; color: var(--pink); line-height: 1; margin-bottom: 12px; }
    .ur-step__title { font-family: 'Playfair Display', Georgia, serif; font-weight: 600; font-size: 18px; margin-bottom: 8px; color: var(--ink); }
    .ur-step__text { font-size: 13.5px; line-height: 1.65; color: var(--muted); }
    .ur-how-1a__cta { text-align: center; margin-top: 48px; }

    /* Premium Matchmaking */
    .ur-premium__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-top: 8px; }
    .ur-premium__card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 42px 26px;
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-premium__card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(0,0,0,.08);
    }
    .ur-premium__card--dark {
        background: var(--pink);
        border-color: var(--pink);
    }
    .ur-premium__icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--cream2);
        color: var(--pink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 22px;
    }
    .ur-premium__card--dark .ur-premium__icon { background: rgba(255,255,255,.12); color: #C9974D; }
    .ur-premium__tier {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: .02em;
        margin-bottom: 10px;
    }
    .ur-premium__card--dark .ur-premium__tier { color: #fff; }
    .ur-premium__desc { font-size: 13.5px; line-height: 1.6; color: var(--muted); margin: 0; }
    .ur-premium__card--dark .ur-premium__desc { color: #D7E4DC; }
    .ur-premium__cta {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 46px;
    }

    /* Two Ways to Find Your Match */
    .ur-choice__grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-top: 8px; }
    .ur-choice__card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 40px 34px;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-choice__card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(0,0,0,.07);
    }
    .ur-choice__card--dark {
        background: var(--pink);
        border-color: var(--pink);
    }
    .ur-choice__icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--cream2);
        color: var(--pink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 22px;
    }
    .ur-choice__card--dark .ur-choice__icon { background: rgba(255,255,255,.14); color: #fff; }
    .ur-choice__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px;
        font-weight: 600;
        color: var(--ink);
        margin: 0 0 10px;
    }
    .ur-choice__card--dark .ur-choice__title { color: #fff; }
    .ur-choice__desc { font-size: 14px; line-height: 1.7; color: var(--muted); margin: 0 0 20px; }
    .ur-choice__card--dark .ur-choice__desc { color: #FFE0EC; }
    .ur-choice__list { list-style: none; margin: 0 0 26px; padding: 0; }
    .ur-choice__list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        line-height: 1.5;
        color: var(--muted);
        margin-bottom: 10px;
    }
    .ur-choice__card--dark .ur-choice__list li { color: #FFE0EC; }
    .ur-choice__list li i { color: var(--pink); margin-top: 2px; font-size: 12px; }
    .ur-choice__card--dark .ur-choice__list li i { color: #fff; }

    /* International Matchmaking */
    .ur-intl__note {
        font-size: 14px;
        line-height: 1.7;
        color: var(--muted);
        max-width: 560px;
        margin: 14px auto 0;
    }
    .ur-intl__regions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
        margin: 44px 0 8px;
    }
    .ur-intl__region {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 99px;
        padding: 14px 26px;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .ur-intl__region:hover { transform: translateY(-3px); }
    .ur-intl__flag { font-size: 22px; line-height: 1; }
    .ur-intl__name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
        letter-spacing: .01em;
    }
    .ur-intl__cta { text-align: center; margin-top: 40px; }

    /* Full-width photo gallery — outside ur-page / ur-wrap */
    .ur-photo-gallery {
        width: 100%;
        max-width: none;
        background: #FBF7EF;
        padding: 24px 0 40px;
        overflow: hidden;
        clear: both;
    }
    .ur-photo-gallery__fluid.container-fluid {
        width: 100%;
        max-width: 100%;
        padding-left: 8px;
        padding-right: 8px;
        margin-left: 0;
        margin-right: 0;
    }
    .ur-photo-gallery__head {
        padding: 0 12px;
        margin-bottom: 36px;
    }
    .ur-photo-gallery .gal-im .gal-label {
        font-family: 'Manrope', system-ui, sans-serif;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #fff;
    }
    .ur-photo-gallery .gall-inn {
        width: 100%;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
    }
    .ur-photo-gallery .gall-inn > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
    }
    .ur-photo-gallery .gal-im {
        margin-bottom: 12px;
        border-radius: 14px;
        overflow: hidden;
        opacity: 0;
        box-shadow: 0 8px 24px rgba(26, 6, 16, 0.08);
    }
    .ur-photo-gallery .gal-im.anistart {
        opacity: 1;
    }
    .ur-photo-gallery .gal-im:before {
        background: linear-gradient(180deg, transparent 20%, rgba(26, 6, 16, 0.78) 100%);
        opacity: 0.35;
        transition: opacity .45s ease;
    }
    .ur-photo-gallery .gal-im:hover:before {
        opacity: 1;
    }
    .ur-photo-gallery .gal-im::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.32) 48%, transparent 66%);
        transform: translateX(-130%);
        transition: transform .75s ease;
    }
    .ur-photo-gallery .gal-im:hover::after {
        transform: translateX(130%);
    }
    .ur-photo-gallery .gal-im img {
        transform: scale(1.04);
        transition: transform 1.15s cubic-bezier(.22,1,.36,1), filter .45s ease;
        will-change: transform;
    }
    .ur-photo-gallery .gal-im:hover img {
        transform: scale(1.16);
        filter: saturate(1.1) brightness(1.04);
    }
    .ur-photo-gallery .gal-im img.gal-siz-1 {
        height: 280px;
    }
    .ur-photo-gallery .gal-im img.gal-siz-2 {
        height: 42vh;
        min-height: 280px;
    }
    .ur-photo-gallery .gal-im .txt span,
    .ur-photo-gallery .gal-im .txt h4 {
        color: #fff;
    }
    .ur-photo-gallery .gal-im .txt h4 {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600;
    }

    @keyframes urGalReveal {
        from {
            opacity: 0;
            transform: translateY(36px) scale(.94);
            filter: blur(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
    }
    .urGalReveal {
        animation-name: urGalReveal;
        animation-duration: .85s;
        animation-timing-function: cubic-bezier(.22, 1, .36, 1);
        animation-fill-mode: both;
    }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(1) .gal-im:nth-child(1) { animation-delay: .04s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(1) .gal-im:nth-child(2) { animation-delay: .16s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(2) .gal-im:nth-child(1) { animation-delay: .10s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(2) .gal-im:nth-child(2) { animation-delay: .22s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(3) .gal-im:nth-child(1) { animation-delay: .16s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(3) .gal-im:nth-child(2) { animation-delay: .28s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(4) .gal-im:nth-child(1) { animation-delay: .22s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(4) .gal-im:nth-child(2) { animation-delay: .34s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(5) .gal-im:nth-child(1) { animation-delay: .28s; }
    .ur-photo-gallery .gall-inn > [class*="col-"]:nth-child(5) .gal-im:nth-child(2) { animation-delay: .40s; }

    @media (prefers-reduced-motion: reduce) {
        .ur-photo-gallery .gal-im.animate,
        .ur-photo-gallery .gal-im.anistart {
            opacity: 1;
            animation: none !important;
            transform: none;
            filter: none;
        }
        .ur-photo-gallery .gal-im img,
        .ur-photo-gallery .gal-im:hover img,
        .ur-photo-gallery .gal-im::after {
            transform: none !important;
            transition: none !important;
        }
    }
    @media (max-width: 767px) {
        .ur-photo-gallery .gal-im img.gal-siz-1,
        .ur-photo-gallery .gal-im img.gal-siz-2 {
            height: 220px;
            min-height: 0;
        }
    }

    .ur-stories-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 36px; flex-wrap: wrap; }
    .ur-stories-sub { font-size: 15.5px; line-height: 1.8; color: var(--muted); max-width: 640px; margin: -22px 0 30px; }
    .ur-stories-disclaimer { text-align: center; font-size: 12.5px; font-style: italic; color: var(--muted); margin: 28px 0 0; }
    .ur-stories-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 22px; }
    .ur-story { position: relative; border-radius: 6px; overflow: hidden; }
    .ur-story--lg { height: 360px; }
    .ur-story--sm { height: 169px; }
    .ur-story img { width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 20%; }
    .ur-story--sm img { object-position: center 12%; }
    .ur-story__overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 45%, rgba(26,6,16,.9)); }
    .ur-story__meta { position: absolute; bottom: 18px; left: 18px; right: 18px; color: #fff; }
    .ur-story__meta h4 { font-family: 'Playfair Display', Georgia, serif; font-size: 22px; font-weight: 600; margin: 0; }
    .ur-story__meta span { font-size: 12.5px; color: #F8D7E5; }
    .ur-story-col { display: flex; flex-direction: column; gap: 22px; }
    .ur-stories__cta { text-align: center; margin-top: 40px; }

    .ur-quotes-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 36px;
        flex-wrap: wrap;
    }
    .ur-quotes-head .ur-center {
        text-align: left;
        max-width: none;
        margin: 0;
    }
    .ur-quotes-sub {
        font-size: 15px;
        line-height: 1.7;
        color: #D7E4DC;
        max-width: 640px;
        margin: -20px 0 32px;
    }
    .ur-quotes__cta { text-align: center; margin-top: 40px; }
    .ur-quotes-nav {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .ur-quotes-nav button {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid rgba(255,224,236,.35);
        background: transparent;
        color: #FFE0EC;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        transition: .2s ease;
        padding: 0;
    }
    .ur-quotes-nav button:hover {
        background: #E91E63;
        border-color: #E91E63;
        color: #fff;
    }
    .ur-quotes-wrap {
        position: relative;
        overflow: hidden;
    }
    .ur-quotes {
        display: block;
        margin: 0 -10px;
    }
    .ur-quotes .slick-list {
        overflow: hidden;
        padding: 4px 0 8px !important;
    }
    .ur-quotes .slick-slide {
        padding: 0 10px;
        height: auto;
        opacity: .45;
        transition: opacity .25s ease;
    }
    .ur-quotes .slick-slide.slick-active {
        opacity: 1;
    }
    .ur-quotes .slick-track {
        display: flex !important;
    }
    .ur-quotes .slick-slide > div,
    .ur-quotes .slick-slide .ur-quote {
        height: 100%;
    }
    .ur-quote {
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,224,236,.16);
        border-radius: 14px;
        padding: 28px 26px;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 12px 30px rgba(0,0,0,.18);
    }
    .ur-quote__stars { color: #FFB6CE; margin-bottom: 14px; letter-spacing: 2px; font-size: 14px; }
    .ur-quote__text {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 17px;
        line-height: 1.65;
        color: #FFF5F8;
        margin-bottom: 22px;
        flex: 1;
    }
    .ur-quote__person { display: flex; gap: 12px; align-items: center; margin-top: auto; }
    .ur-quote__person img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(233,30,99,.45);
    }
    .ur-quote__person strong { display: block; color: #fff; font-size: 14px; margin-bottom: 2px; }
    .ur-quote__person span { font-size: 12px; color: #F3A8C3; }
    .ur-quotes .slick-arrow { display: none !important; }
    .ur-quotes .slick-dots {
        display: flex !important;
        justify-content: center;
        align-items: center;
        gap: 8px;
        list-style: none;
        padding: 26px 0 0;
        margin: 0;
    }
    .ur-quotes .slick-dots li {
        margin: 0;
        width: auto;
        height: auto;
    }
    .ur-quotes .slick-dots li button {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,.28);
        font-size: 0;
        padding: 0;
        cursor: pointer;
        transition: .2s ease;
    }
    .ur-quotes .slick-dots li.slick-active button {
        background: #E91E63;
        width: 24px;
        border-radius: 999px;
    }
    @media (max-width: 767px) {
        .ur-quotes-head {
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .ur-quotes-head .ur-center {
            text-align: center;
            width: 100%;
        }
        .ur-quotes-nav {
            width: 100%;
            justify-content: center;
        }
    }

    /* Founder — prominent presentation */
    .ur-founder {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 48px;
        align-items: center;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 40px;
        margin-bottom: 56px;
        box-shadow: 0 18px 44px rgba(0,0,0,.06);
    }
    .ur-founder__photo { border-radius: 14px; overflow: hidden; }
    .ur-founder__photo img { width: 100%; height: 380px; object-fit: cover; display: block; }
    .ur-founder__name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px;
        font-weight: 600;
        color: var(--ink);
        margin: 0 0 6px;
    }
    .ur-founder__role {
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #C9974D;
        margin-bottom: 18px;
    }
    .ur-founder__desc { font-size: 14.5px; line-height: 1.75; color: var(--muted); margin: 0 0 14px; max-width: 560px; }
    .ur-founder__actions { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; margin-top: 18px; }
    .ur-founder__actions .ur-team__social { justify-content: flex-start; margin: 0; }

    .ur-team__label {
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        text-align: center;
        margin: 0 0 24px;
    }

    .ur-team { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 70px; }
    .ur-team--rest { grid-template-columns: repeat(3, 1fr); }
    .ur-team__card { text-align: center; }
    .ur-team__card img { width: 100%; height: 230px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 14px; }
    .ur-team__card h4 { font-weight: 700; font-size: 15px; margin: 0 0 4px; color: var(--ink); }
    .ur-team__card p { font-size: 12.5px; color: var(--muted); margin: 0 0 10px; }
    .ur-team__social { display: flex; gap: 8px; justify-content: center; list-style: none; padding: 0; margin: 0; }
    .ur-team__social a { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--line); color: var(--pink); font-size: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
    .ur-team__social a:hover { background: var(--pink); color: #fff; border-color: var(--pink); }

    .ur-gallery { display: grid; grid-template-columns: repeat(5, 1fr); grid-auto-rows: 130px; gap: 12px; }
    .ur-gallery img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
    .ur-gallery img.span2 { grid-row: span 2; }

    /* Privacy & Safety */
    .ur-privacy__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-top: 8px; }
    .ur-privacy__card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 34px 26px;
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-privacy__card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(0,0,0,.07);
    }
    .ur-privacy__icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--cream2);
        color: var(--pink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin: 0 auto 16px;
    }
    .ur-privacy__num {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .06em;
        color: #C9974D;
        margin-bottom: 10px;
    }
    .ur-privacy__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 17px;
        font-weight: 600;
        color: var(--ink);
        margin: 0 0 8px;
    }
    .ur-privacy__text { font-size: 13.5px; line-height: 1.65; color: var(--muted); margin: 0; }
    .ur-privacy__cta { text-align: center; margin-top: 40px; }

    .ur-cta {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        isolation: isolate;
        background-color: #1a0610;
        background-image: url(/images/couples/10.jpg);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .ur-cta__overlay {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 80% at 50% 45%, rgba(90, 8, 40, 0.28) 0%, rgba(26, 4, 16, 0.72) 100%),
            linear-gradient(180deg, rgba(18, 4, 12, 0.55) 0%, rgba(142, 14, 62, 0.42) 42%, rgba(18, 4, 12, 0.82) 100%);
    }
    .ur-cta__glow {
        position: absolute;
        width: 520px;
        height: 220px;
        left: 50%;
        top: 42%;
        transform: translate(-50%, -50%);
        background: radial-gradient(ellipse at center, rgba(233, 30, 99, 0.28), transparent 68%);
        filter: blur(28px);
        pointer-events: none;
        z-index: 1;
    }
    .ur-cta::before,
    .ur-cta::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: min(280px, 46%);
        height: 1px;
        background: linear-gradient(90deg, transparent, #D4AF6A, transparent);
        z-index: 3;
        pointer-events: none;
        opacity: .7;
    }
    .ur-cta::before { top: 22px; }
    .ur-cta::after { bottom: 22px; }
    .ur-cta__inner {
        position: relative;
        z-index: 2;
        padding: 72px 24px 68px;
        max-width: 720px;
    }
    .ur-cta__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #E8C48A;
        margin-bottom: 18px;
    }
    .ur-cta__eyebrow::before,
    .ur-cta__eyebrow::after {
        content: '';
        width: 28px;
        height: 1px;
        background: #E8C48A;
        opacity: .7;
    }
    .ur-cta__inner h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4.4vw, 46px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 16px;
        line-height: 1.18;
        text-shadow: 0 8px 28px rgba(0,0,0,.35);
    }
    .ur-cta__inner h2 em {
        font-style: italic;
        font-weight: 700;
        color: #E8C48A;
        -webkit-text-fill-color: #E8C48A;
    }
    .ur-cta__inner p {
        font-size: 16px;
        line-height: 1.7;
        color: rgba(255, 232, 240, 0.92);
        margin: 0 auto 28px;
        max-width: 34em;
    }
    .ur-cta__actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .ur-cta__btn {
        min-width: 200px;
        padding: 15px 30px;
        border-radius: 999px;
        font-size: 14.5px;
        letter-spacing: .02em;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    }
    .ur-cta__btn--primary {
        background: linear-gradient(135deg, #FF6B9D 0%, #E91E63 48%, #C2185B 100%) !important;
        color: #fff !important;
        border: none;
        box-shadow:
            0 12px 28px rgba(233, 30, 99, 0.45),
            0 0 0 4px rgba(233, 30, 99, 0.12);
    }
    .ur-cta__btn--primary:hover {
        transform: translateY(-2px);
        filter: brightness(1.04);
        box-shadow:
            0 16px 32px rgba(233, 30, 99, 0.52),
            0 0 0 5px rgba(233, 30, 99, 0.16);
        color: #fff !important;
    }
    .ur-cta__btn--ghost {
        background: rgba(255,255,255,0.08) !important;
        border: 1.5px solid rgba(255,255,255,0.72) !important;
        color: #fff !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .ur-cta__btn--ghost:hover {
        background: #fff !important;
        color: #C2185B !important;
        border-color: #fff !important;
        transform: translateY(-2px);
    }
    .ur-cta__trust {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px 14px;
        list-style: none;
        margin: 0;
        padding: 0;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .ur-cta__trust li {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(232, 196, 138, 0.45);
        border-radius: 999px;
        padding: 7px 16px;
        color: #FFEFD9;
    }
    .ur-cta__trust li::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #E8C48A;
        box-shadow: 0 0 8px rgba(232, 196, 138, 0.7);
    }
    .ur-cta__disclaimer {
        margin: 22px 0 0;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: .03em;
        color: rgba(255, 232, 240, 0.62);
    }
    @media (prefers-reduced-motion: reduce) {
        .ur-cta { background-attachment: scroll; }
    }

    .ur-center { text-align: center; max-width: 560px; margin: 0 auto 44px; }
    .ur-sec--deep .ur-eyebrow { color: #FFB6CE; }
    .ur-sec--deep .ur-h2 { color: #fff; }
    .ur-sec--pink .ur-trust-note { color: #FFF5F8; }

    @media (max-width: 991px) {
        .ur-stories-grid { grid-template-columns: 1fr; }
        .ur-promise2__top { grid-template-columns: 1fr; margin-bottom: 40px; }
        .ur-promise2__top .ab-wel-lhs { min-height: 380px; height: 380px; margin-bottom: 8px; }
        .ur-promise2__top .ur-eyebrow,
        .ur-promise2__top .ur-h2,
        .ur-promise2__top .ur-lead { text-align: center; }
        .ur-promise2__grid { grid-template-columns: 1fr 1fr; }
        .ur-stats { grid-template-columns: 1fr 1fr; }
        .ur-stats__card:nth-child(2) { border-right: none; }
        .ur-stats__card:nth-child(1),
        .ur-stats__card:nth-child(2) { border-bottom: 1px solid var(--line); padding-bottom: 18px; margin-bottom: 8px; }
        .ur-steps { grid-template-columns: 1fr 1fr; }
        .ur-choice__grid { grid-template-columns: 1fr; }
        .ur-premium__grid { grid-template-columns: 1fr 1fr; }
        .ur-privacy__grid { grid-template-columns: 1fr 1fr; }
        .ur-gallery { grid-template-columns: repeat(3, 1fr); }
        .ur-contact-row { grid-template-columns: 1fr; }
        .ur-team { grid-template-columns: 1fr 1fr; }
        .ur-team--rest { grid-template-columns: 1fr 1fr 1fr; }
        .ur-founder { grid-template-columns: 1fr; text-align: center; padding: 32px 28px; }
        .ur-founder__photo img { height: 320px; }
        .ur-founder__desc { max-width: none; margin-left: auto; margin-right: auto; }
        .ur-founder__actions { justify-content: center; }
        .ur-founder__actions .ur-team__social { justify-content: center; }
        .ur-story--lg { height: 280px; }
        .ur-story--sm { height: 160px; }
        .ur-wrap { padding-left: 16px; padding-right: 16px; box-sizing: border-box; }
    }
    @media (max-width: 767px) {
        .ur-stories-grid, .ur-team, .ur-steps, .ur-stats, .ur-gallery { grid-template-columns: 1fr; }
        .ur-promise2__grid { grid-template-columns: 1fr; }
        .ur-premium__grid { grid-template-columns: 1fr; }
        .ur-premium__cta { flex-direction: column; align-items: stretch; }
        .ur-privacy__grid { grid-template-columns: 1fr; }
        .ur-intl__region { padding: 12px 18px; gap: 8px; }
        .ur-intl__name { font-size: 14px; }
        .ur-stats__card { border-right: none !important; border-bottom: 1px solid var(--line); padding: 16px 8px; }
        .ur-stats__card:last-child { border-bottom: none; }
        .ur-story--lg,
        .ur-story--sm { height: 220px; }
        .ur-story-col { gap: 14px; }
        .ur-team--rest { grid-template-columns: 1fr; }
        .ur-team__card img { height: 200px; }
        .ur-founder { padding: 28px 20px; }
        .ur-founder__photo img { height: 260px; }
        .ur-quote { min-height: 0; padding: 22px 18px; }
        .ur-gallery { grid-auto-rows: 160px; }
        .ur-gallery img.span2 { grid-row: auto; }
        .ur-sec { padding: 56px 16px; }
        .ur-promise2__top .ab-wel-lhs { min-height: 320px; height: 320px; }
        .ur-promise2__card { padding: 30px 22px; }
        .ur-welcome-kicker { font-size: 24px; }
    }

    /* ========== Full responsive pass (all devices) ========== */
    body.homepage,
    body.homepage #main-content,
    body.homepage .ur-page {
        overflow-x: hidden;
        max-width: 100%;
    }
    .ur-hero-a,
    .ur-feat-row,
    .ur-photo-gallery,
    .ur-cta {
        max-width: 100%;
    }
    .ur-hero-a__search-wrap,
    .ur-wrap {
        width: 100%;
        box-sizing: border-box;
    }

    /* Phones + small tablets (non-hero page sections) */
    @media (max-width: 767px) {
        /* Gallery */
        .ur-photo-gallery {
            padding: 40px 0 12px;
        }
        .ur-photo-gallery__fluid.container-fluid {
            padding-left: 6px;
            padding-right: 6px;
        }
        .ur-photo-gallery .gall-inn > [class*="col-"] {
            padding-left: 4px;
            padding-right: 4px;
        }
        .ur-photo-gallery .gal-im {
            margin-bottom: 8px;
        }
        .ur-photo-gallery .gal-im .txt {
            padding: 14px 12px 16px;
        }
        .ur-photo-gallery .gal-im .txt h4 {
            font-size: 15px;
        }
        .ur-photo-gallery .gal-im .txt span {
            font-size: 11px;
        }
        .ur-cta {
            min-height: 0;
            background-attachment: scroll;
        }
        .ur-cta::before,
        .ur-cta::after {
            width: min(160px, 40%);
        }
        .ur-cta__inner {
            padding: 56px 20px 52px;
        }
        .ur-cta__inner h2 {
            font-size: clamp(26px, 8vw, 34px);
        }
        .ur-cta__inner p {
            font-size: 14.5px;
            margin-bottom: 22px;
        }
        .ur-cta__actions {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            margin-bottom: 22px;
        }
        .ur-cta__btn {
            width: 100%;
            min-width: 0;
        }
        .ur-cta__trust {
            gap: 8px 14px;
            font-size: 11.5px;
        }
        .ur-team__card img {
            height: 260px;
        }
        .ur-contact-row {
            gap: 10px;
        }
    }


</style>
<link rel="stylesheet" href="/css/ur-1a.css?13">

<section class="ur-hero-a" aria-label="Home hero">
    <div class="ur-hero-a__overlay"></div>

    <div class="ur-hero-a__inner">
        <div class="ur-hero-a__main">
            <div class="ur-hero-a__content">
                <div class="ur-hero-a__badge">Private &bull; Verified &bull; Professional Matchmaking</div>
                <h1 class="ur-hero-a__title">
                    Where <em>Meaningful Matches</em> Begin
                </h1>
                <p class="ur-hero-a__subtitle">
                    Pakistan&rsquo;s trusted premium matchmaking service for serious individuals and families in Pakistan, UK, USA, Canada, Australia, Europe &amp; the Gulf.
                </p>
                <p class="ur-hero-a__subtitle ur-hero-a__subtitle--sm">
                    Every profile is carefully reviewed, with privacy, discretion and personal matchmaking support at every stage.
                </p>
                <div class="ur-hero-a__cta-row">
                    <a href="javascript:void(0);" onclick="openPopup()" class="ur-hero-a__btn ur-hero-a__btn--primary">Book a Private Consultation</a>
                    <a href="/register" class="ur-hero-a__btn ur-hero-a__btn--secondary">Create Your Profile</a>
                </div>
                <div class="ur-hero-a__trust" aria-label="Trust points">
                    <span>15,000+ Verified Profiles</span>
                    <span class="ur-hero-a__trust-dot" aria-hidden="true">&bull;</span>
                    <span>5,000+ Successful Matches</span>
                    <span class="ur-hero-a__trust-dot" aria-hidden="true">&bull;</span>
                    <span>16+ Years of Trust</span>
                </div>
            </div>
        </div>

        <div class="ur-hero-a__search-wrap">
            <div class="ur-hero-a__search-card s-search">
                <form name="search_form" id="search_form" data-toggle="validator" role="form" action="{{route('searchresults')}}" method="POST">
                    @csrf
                    <div class="ur-hero-a__search-grid">
                        <div class="form-group has-feedback" data-field="gender">
                            <label for="gender">Looking for</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-user"></i></span>
                            <select name="gender" id="gender" class="form-control form-control-sm selectpicker" required="required">
                                <option value="">Select one...</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>
                        <div class="form-group has-feedback" data-field="aged_from">
                            <label for="aged_from">Age From</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-calendar"></i></span>
                            <select name="aged_from" id="aged_from" class="form-control form-control-sm selectpicker">
                                <option value="">From</option>
                                @for ($i=18; $i<=75; $i++)
                                <option>{{$i<10?"0".$i:$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group has-feedback" data-field="aged_to">
                            <label for="aged_to">Age To</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-calendar"></i></span>
                            <select name="aged_to" id="aged_to" class="form-control form-control-sm selectpicker">
                                <option value="">To</option>
                                @for ($i=18; $i<=75; $i++)
                                <option>{{$i<10?"0".$i:$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group has-feedback" data-field="marital_status">
                            <label for="marital_status">Marital Status</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-heart"></i></span>
                            <select name="marital_status" id="marital_status" class="form-control form-control-sm selectpicker">
                                <option value="">Select one...</option>
                                @foreach($maritalstatuses as $maritalstatus)
                                <option value="{{$maritalstatus->dataid}}">{{$maritalstatus->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group has-feedback" data-field="country">
                            <label for="country">Country</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-globe"></i></span>
                            <select name="country" id="country" class="form-control form-control-sm selectpicker">
                                <option value="">Select one...</option>
                                @foreach($countries as $country)
                                <option value="{{$country->dataid}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group has-feedback" data-field="language">
                            <label for="mother_tongue">Language</label>
                            <span class="ur-field-ico" aria-hidden="true"><i class="fa fa-comments"></i></span>
                            <select name="mother_tongue" id="mother_tongue" class="form-control form-control-sm selectpicker">
                                <option value="">Select one...</option>
                                @foreach($mothertongues as $mothertongue)
                                <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ur-hero-a__search-actions">
                            <button id="search_button" type="submit" class="btn btn-styled btn-sm btn-base-1 btn-search"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


<div class="ur-page">
    <!-- WELCOME / PROMISE -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap">
            <div class="ur-promise2__top">
                <div class="ab-wel-lhs">
                    <img src="images/about/1.jpg" alt="" loading="lazy" class="ab-wel-1">
                    <img src="images/couples/20.jpg" alt="" loading="lazy" class="ab-wel-2">
                    <div class="ur-years"><b>16+</b><span>YEARS OF SERVICE</span></div>
                </div>
                <div>
                    <div class="ur-eyebrow">Private Matchmaking, Personally Managed</div>
                    <h2 class="ur-h2" style="margin-bottom:16px;">More Than a Matrimonial Website</h2>
                    <p class="ur-lead">
                        Finding the right life partner requires more than browsing profiles. Urgent Rishta combines technology with
experienced human matchmaking to create a private, structured and respectful experience</p>
                </div>
            </div>

            <div class="ur-promise2__grid">
                <div class="ur-promise2__card">
                    <div class="ur-promise2__icon"><i class="fa fa-check-circle" aria-hidden="true"></i></div>
                    <h3 class="ur-promise2__card-title">Verified &amp; Genuine</h3>
                    <p class="ur-promise2__card-text">Profiles and key information are reviewed before approval.</p>
                </div>
                <div class="ur-promise2__card">
                    <div class="ur-promise2__icon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                    <h3 class="ur-promise2__card-title">Private &amp; Confidential</h3>
                    <p class="ur-promise2__card-text">Your personal information and photographs are handled with discretion.</p>
                </div>
                <div class="ur-promise2__card">
                    <div class="ur-promise2__icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                    <h3 class="ur-promise2__card-title">Human Matchmaking Support</h3>
                    <p class="ur-promise2__card-text">Our team personally assists you from profile creation to family introductions.</p>
                </div>
            </div>

            <div class="ur-promise2__cta">
                <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-btn--solid">Speak to a Matchmaker</a>
            </div>
        </div>
    </section>

    <!-- TWO WAYS TO FIND YOUR MATCH -->
    <section class="ur-sec ur-sec--soft">
        <div class="ur-wrap">
            <div class="ur-center">
            
                <h2 class="ur-h2">Choose the Service That Suits You</h2>
            </div>
            <div class="ur-choice__grid">
                <div class="ur-choice__card">
                    <div class="ur-choice__icon"><i class="fa fa-desktop" aria-hidden="true"></i></div>
                    <h3 class="ur-choice__title">Online Matchmaking</h3>
                    <p class="ur-choice__desc">For members who prefer to explore suitable profiles through our online platform.</p>
                    <ul class="ur-choice__list">
                        <li><i class="fa fa-check" aria-hidden="true"></i> Access to suitable profiles</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Search based on preferences</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Interest requests</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Privacy controls</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Matchmaking support</li>
                    </ul>
                    <a href="/register" class="ur-btn ur-btn--outline">Explore Online Plans</a>
                </div>
                <div class="ur-choice__card ur-choice__card--dark">
                    <div class="ur-choice__icon"><i class="fa fa-user-circle" aria-hidden="true"></i></div>
                    <h3 class="ur-choice__title">Personalized Confidential Matchmaking</h3>
                    <p class="ur-choice__desc">For individuals and families who prefer their search to be personally managed by our matchmaking team.</p>
                    <ul class="ur-choice__list">
                        <li><i class="fa fa-check" aria-hidden="true"></i> We understand your requirements</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> We review suitable prospects</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> We coordinate introductions privately</li>
                    </ul>
                    <a href="{{ url('packages') }}" class="ur-btn ur-btn--light">Explore Personalized Services</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ENQUIRY + COUNTS -->
    <section class="ur-sec ur-sec--cream" style="padding-top:0;">
        <div class="ur-wrap">
            <div class="ur-enquiry-bar">
                <div class="ur-enquiry-bar__contact">
                    <div class="ur-enquiry-bar__item">
                        <div class="ur-enquiry-bar__label">Enquiry</div>
                        <a class="ur-enquiry-bar__value" href="tel:+923040227000">+92 304 0227000</a>
                    </div>
                    <div class="ur-enquiry-bar__item">
                        <div class="ur-enquiry-bar__label">Get Support</div>
                        <a class="ur-enquiry-bar__value" href="mailto:urgentrishta.co@gmail.com" style="font-size:17px;">urgentrishta.co@gmail.com</a>
                    </div>
                </div>
                <div class="ur-enquiry-bar__stats">
                    <div class="ur-enquiry-bar__stat">
                        <div class="ur-enquiry-bar__num"><span class="ur-counter" data-target="15000">0</span>+</div>
                        <div class="ur-enquiry-bar__sub">Verified Profiles</div>
                    </div>
                    <div class="ur-enquiry-bar__stat">
                        <div class="ur-enquiry-bar__num"><span class="ur-counter" data-target="5000">0</span>+</div>
                        <div class="ur-enquiry-bar__sub">Successful Matches</div>
                    </div>
                    <div class="ur-enquiry-bar__stat">
                        <div class="ur-enquiry-bar__num"><span class="ur-counter" data-target="16">0</span>+ Years</div>
                        <div class="ur-enquiry-bar__sub">Matchmaking Experience</div>
                    </div>
                    <div class="ur-enquiry-bar__stat">
                        <div class="ur-enquiry-bar__num">Worldwide</div>
                        <div class="ur-enquiry-bar__sub">Pakistani &amp; Global Community</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="ur-how-1a" id="how-it-works">
        <div class="ur-wrap">
            <div class="ur-center">
                <div class="ur-eyebrow">A MORE PRIVATE WAY TO FIND THE RIGHT PERSON</div>
                <h2 class="ur-h2">Your Search. Personally Managed</h2>
                <p class="ur-lead" style="margin-top:14px;">Some searches require more than access to a database. Our personalized matchmaking service is designed for
clients who value privacy, discretion and individual attention.</p>
            </div>
            <div class="ur-steps">
                <div class="ur-step">
                    <div class="ur-step__icon"><i class="fa fa-user-plus" aria-hidden="true"></i></div>
                    <div class="ur-step__num">01</div>
                    <div class="ur-step__title"> Private Consultation</div>
                    <div class="ur-step__text">We understand your background, expectations and preferences.</div>
                </div>
                <div class="ur-step">
                    <div class="ur-step__icon"><i class="fa fa-search" aria-hidden="true"></i></div>
                    <div class="ur-step__num">02</div>
                    <div class="ur-step__title">Profile Review</div>
                    <div class="ur-step__text">Your information and requirements are carefully reviewed. </div>
                </div>
                <div class="ur-step">
                    <div class="ur-step__icon"><i class="fa fa-star" aria-hidden="true"></i></div>
                    <div class="ur-step__num">03</div>
                    <div class="ur-step__title">Curated Search</div>
                    <div class="ur-step__text">Our team identifies potentially compatible profiles.</div>
                </div>
                <div class="ur-step">
                    <div class="ur-step__icon"><i class="fa fa-comments" aria-hidden="true"></i></div>
                    <div class="ur-step__num">04</div>
                    <div class="ur-step__title"> Private Introductions</div>
                    <div class="ur-step__text">Suitable profiles are presented discreetly.</div>
                </div>
                <div class="ur-step">
                    <div class="ur-step__icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                    <div class="ur-step__num">05</div>
                    <div class="ur-step__title"> Mutual Interest</div>
                    <div class="ur-step__text">Personal contact details are shared only after appropriate approval.</div>
                </div>
                <div class="ur-step ur-step--dark">
                    <div class="ur-step__icon"><i class="fa fa-heart" aria-hidden="true"></i></div>
                    <div class="ur-step__num">06</div>
                    <div class="ur-step__title"> Family Connection</div>
                    <div class="ur-step__text">Where appropriate, our team helps facilitate communication between families.</div>
                </div>
            </div>
            <div class="ur-how-1a__cta">
                <a href="/register" class="ur-btn ur-btn--solid">Start Your Journey</a>
            </div>
        </div>
    </section>

    <!-- PREMIUM MATCHMAKING -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap ur-wrap--full">
            <div class="ur-center">
                <div class="ur-eyebrow">For Clients Who Value Privacy</div>
                <h2 class="ur-h2">A Level of Service for Every Search</h2>
                {{-- <p class="ur-lead" style="margin-top:14px;">For professionals, entrepreneurs, overseas Pakistanis and families seeking a more personalised experience, our premium matchmaking plans provide dedicated support, greater privacy and carefully curated introductions.</p> --}}
            </div>

            <div class="ur-premium__grid">
                <div class="ur-premium__card">
                    <div class="ur-premium__icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
                    <div class="ur-premium__tier">Platinum</div>
                    <p class="ur-premium__desc">Personal matchmaking support for clients seeking a professionally managed search.</p>
                </div>
                <div class="ur-premium__card">
                    <div class="ur-premium__icon"><i class="fa fa-diamond" aria-hidden="true"></i></div>
                    <div class="ur-premium__tier">Diamond</div>
                    <p class="ur-premium__desc">Priority matchmaking with broader search support and dedicated assistance.</p>
                </div>
                <div class="ur-premium__card">
                    <div class="ur-premium__icon"><i class="fa fa-star" aria-hidden="true"></i></div>
                    <div class="ur-premium__tier">Royal - Executive Matchmaking</div>
                    <p class="ur-premium__desc">A highly personalized and confidential search with priority handling and senior-level oversight.</p>
                </div>
                <div class="ur-premium__card ur-premium__card--dark">
                    <div class="ur-premium__icon"><i class="fa fa-trophy" aria-hidden="true"></i></div>
                    <div class="ur-premium__tier">Imperial - Bespoke Private Matchmaking</div>
                    <p class="ur-premium__desc">Our most exclusive service for individuals and families with highly specific requirements who expect maximum
discretion and individually managed search.</p>
                </div>
            </div>

            <div class="ur-premium__cta">
                <a href="{{ url('packages') }}" class="ur-btn ur-btn--solid">Compare Premium Plans</a>
                <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-btn--outline">Book a Private Consultation</a>
            </div>
        </div>
    </section>

    <!-- PRIVACY & SAFETY -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap">
            <div class="ur-center" style="max-width:640px;">
                <div class="ur-eyebrow">Privacy &amp; Safety</div>
                <h2 class="ur-h2">Your Privacy Comes First</h2>
                <p class="ur-lead" style="margin-top:14px;">Marriage is personal. Your search should be too.</p>
                <p class="ur-lead" style="margin-top:10px;">Urgent Rishta is designed around confidentiality. Personal information is handled carefully, and sensitive details are not intended for unrestricted public disclosure.</p>
            </div>

            <div class="ur-privacy__grid">
                <div class="ur-privacy__card">
                    <div class="ur-privacy__icon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <div class="ur-privacy__num">01</div>
                    <h3 class="ur-privacy__title">Your Phone Number</h3>
                    <p class="ur-privacy__text">Not displayed publicly.</p>
                </div>
                <div class="ur-privacy__card">
                    <div class="ur-privacy__icon"><i class="fa fa-picture-o" aria-hidden="true"></i></div>
                    <div class="ur-privacy__num">02</div>
                    <h3 class="ur-privacy__title">Your Photographs</h3>
                    <p class="ur-privacy__text">Shared according to applicable privacy settings and matchmaking procedures.</p>
                </div>
                <div class="ur-privacy__card">
                    <div class="ur-privacy__icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
                    <div class="ur-privacy__num">03</div>
                    <h3 class="ur-privacy__title">Your Personal Information</h3>
                    <p class="ur-privacy__text">Used for matchmaking, verification and service delivery in accordance with our Privacy Policy.</p>
                </div>
                <div class="ur-privacy__card">
                    <div class="ur-privacy__icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                    <div class="ur-privacy__num">04</div>
                    <h3 class="ur-privacy__title">Family Introductions</h3>
                    <p class="ur-privacy__text">Facilitated after appropriate interest and approval.</p>
                </div>
            </div>

            <div class="ur-privacy__cta">
                <a href="{{ url('privacy') }}" class="ur-btn ur-btn--outline">Read Our Privacy Policy</a>
            </div>
        </div>
    </section>

    <!-- INTERNATIONAL MATCHMAKING -->
    <section class="ur-sec ur-sec--soft">
        <div class="ur-wrap">
            <div class="ur-center" style="max-width:640px;">
                <div class="ur-eyebrow">GLOBAL REACH • PERSONAL SERVICE</div>
                <h2 class="ur-h2">Matchmaking Beyond Borders</h2>
                <p class="ur-lead" style="margin-top:14px;">Whether you are living in Pakistan or overseas, our team helps connect serious individuals and families across</p>
                {{-- <p class="ur-intl__note">Whether you are looking locally or overseas, our team helps identify suitable introductions while maintaining privacy and family values.</p> --}}
            </div>

            <div class="ur-intl__regions">
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇵🇰</span><span class="ur-intl__name">Pakistan</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇬🇧</span><span class="ur-intl__name">United Kingdom</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇺🇸</span><span class="ur-intl__name">United States</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇨🇦</span><span class="ur-intl__name">Canada</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇦🇺</span><span class="ur-intl__name">Australia</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇦🇪</span><span class="ur-intl__name">UAE &amp; Gulf</span></div>
                <div class="ur-intl__region"><span class="ur-intl__flag" aria-hidden="true">🇪🇺</span><span class="ur-intl__name">Europe</span></div>
            </div>

            <div class="ur-center" style="max-width:640px;">
            <p class="ur-lead" style="margin-top:14px;">Our overseas matchmaking service is particularly suited to professionals, business families and individuals seeking compatible Pakistani matches internationally.</p>
            </div>


            <div class="ur-intl__cta">
                <a href="/register" class="ur-btn ur-btn--solid">Explore Overseas Matchmaking</a>
            </div>
        </div>
    </section>

    <!-- SUCCESS STORIES -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap ur-wrap--full">
            <div class="ur-stories-head">
                <div>
                    <div class="ur-eyebrow">Happy Endings</div>
                    <h2 class="ur-h2">Real People. Meaningful Connections. Successful Matches.</h2>
                </div>
            </div>
            <p class="ur-stories-sub">Behind every successful match are two individuals and two families who found the confidence to take the next
step.</p>
            <div class="ur-stories-grid">
                <div class="ur-story ur-story--lg">
                    <img src="images/couples/arabic-couple.jpg" alt="" loading="lazy">
                    <div class="ur-story__overlay"></div>
                    <div class="ur-story__meta">
                        <h4>Ayesha &amp; Rayed</h4>
                        <span>Successfully Matched · Dubai, UAE</span>
                    </div>
                </div>
                <div class="ur-story-col">
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/pakistani-couple.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Dr. Usman &amp; Dr. Rabia</h4><span>United Kingdom</span></div>
                    </div>
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/second-marriage-couple.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Abdullah &amp; Sarah</h4><span>Saudi Arabia</span></div>
                    </div>
                </div>
                <div class="ur-story ur-story--lg">
                    <img src="images/couples/usa-couple.jpg" alt="" loading="lazy">
                    <div class="ur-story__overlay"></div>
                    <div class="ur-story__meta">
                        <h4>Adam &amp; Sophia</h4>
                        <span>Successfully Matched · United States</span>
                    </div>
                </div>
            </div>
            {{-- <p class="ur-stories-disclaimer">Names and identifying details may be changed to protect our clients' privacy.</p> --}}
            <div class="ur-stories__cta">
                <a href="{{ url('stories') }}" class="ur-btn ur-btn--solid">View More Success Stories</a>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="ur-sec ur-sec--deep">
        <div class="ur-wrap">
            <div class="ur-quotes-head">
                <div class="ur-center">
                    <div class="ur-eyebrow">Real Words, Real Trust</div>
                    <h2 class="ur-h2">Trusted by Families Worldwide</h2>
                </div>
                <div class="ur-quotes-nav">
                    <button type="button" class="ur-quotes-prev" aria-label="Previous review">‹</button>
                    <button type="button" class="ur-quotes-next" aria-label="Next review">›</button>
                </div>
            </div>
            <p class="ur-quotes-sub">Real experiences from individuals and families who trusted Urgent Rishta with one of life's most important decisions.</p>
            <div class="ur-quotes-wrap">
            <div class="ur-quotes">
                <div>
                    <div class="ur-quote">
                        <div class="ur-quote__stars">★★★★★</div>
                        <div class="ur-quote__text">“Decent, professional and completely trustworthy from start to finish. Wishing them continued success.”</div>
                        <div class="ur-quote__person">
                            <img src="images/user/1.jpg" alt="" loading="lazy">
                            <div><strong>Ayesha</strong><span>Dubai, UAE</span></div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="ur-quote">
                        <div class="ur-quote__stars">★★★★★</div>
                        <div class="ur-quote__text">“One of the most reliable matrimony services in Pakistan — organised, dedicated and genuinely caring.”</div>
                        <div class="ur-quote__person">
                            <img src="images/user/2.jpg" alt="" loading="lazy">
                            <div><strong>Dr. Sana Ullah</strong><span>Pakistan</span></div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="ur-quote">
                        <div class="ur-quote__stars">★★★★★</div>
                        <div class="ur-quote__text">“They treated us like family, not clients. Outclass service throughout the whole process.”</div>
                        <div class="ur-quote__person">
                            <img src="images/user/5.jpg" alt="" loading="lazy">
                            <div><strong>Javeria</strong><span>Manchester, UK</span></div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="ur-quote">
                        <div class="ur-quote__stars">★★★★★</div>
                        <div class="ur-quote__text">“A name of total trust and true professionalism with kind behaviour — dedicated and organised. Highly recommended.”</div>
                        <div class="ur-quote__person">
                            <img src="images/user/3.jpg" alt="" loading="lazy">
                            <div><strong>Usman</strong><span>Saudi Arabia</span></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="ur-quotes__cta">
                <a href="{{ url('stories') }}" class="ur-btn ur-btn--light">Read Our Client Reviews</a>
            </div>
        </div>
    </section>

    <!-- TEAM -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap ur-wrap--full">
            <div class="ur-center">
                <div class="ur-eyebrow">FOUNDER-LED MATCHMAKING</div>
                <h2 class="ur-h2">Matchmaking With Accountability</h2>
            </div>

            <div class="ur-founder">
                <div class="ur-founder__photo">
                    <img src="images/profiles/5.jpeg" alt="Usman Zaheer" loading="lazy">
                </div>
                <div class="ur-founder__body">
                    <h3 class="ur-founder__name">Usman Zaheer</h3>
                    <div class="ur-founder__role">Founder &amp; CEO — Urgent Rishta</div>
                    <p class="ur-founder__desc"> Urgent Rishta was built around a simple principle: finding a life partner should be handled with seriousness,
privacy and respect.</p>
                    <p class="ur-founder__desc"> Our team combines years of matchmaking experience with a structured process to help individuals and families
navigate one of life's most important decisions.</p>
                    <p class="ur-founder__desc">For clients choosing our premium services, the search receives personalized attention from our matchmaking
team, with senior-level involvement where applicable.</p>
                    <div class="ur-founder__actions">
                        <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-btn--solid">Book a Private Consultation</a>
                        <ul class="ur-team__social">
                            <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                            <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                            <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="ur-team__label">Your Matchmaking Team</div>
            <div class="ur-team ur-team--rest">
                <div class="ur-team__card">
                    <img src="images/profiles/Qanita.jpeg" alt="Qanita Sundas" loading="lazy">
                    <h4>Qanita Sundas</h4>
                    <p>Co-Founder</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/923331623144" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
                <div class="ur-team__card">
                    <img src="images/profiles/minahil.jpeg" alt="Minahil Malik" loading="lazy">
                    <h4>Minahil Malik</h4>
                    <p>Relationship Manager</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/447445723296" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
                <div class="ur-team__card">
                    <img src="images/profiles/9.jpg" alt="Usman Idrees" loading="lazy">
                    <h4>Usman Idrees</h4>
                    <p>Client Coordinator</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>{{-- /ur-page — gallery + CTA sit full width --}}

    @include('partials.partners-section')

    <!-- PHOTO GALLERY — masonry -->
    <section class="ur-photo-gallery">
        <div class="container-fluid ur-photo-gallery__fluid">
            <div class="ur-center ur-photo-gallery__head">
                <div class="ur-eyebrow">Behind The Scenes</div>
                <h2 class="ur-h2">Inside Urgent Rishta</h2>
            </div>
            <div class="row no-gutters gall-inn">
                <div class="col-6 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/1.jpg" class="gal-siz-1" alt="Urgent Rishta team at an industry matchmaking seminar" loading="lazy">
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/2.jpg" class="gal-siz-2" alt="Urgent Rishta founder in conversation at an industry event" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/3.jpg" class="gal-siz-2" alt="Urgent Rishta representatives at a professional marriage consultants seminar" loading="lazy">
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/4.jpg" class="gal-siz-1" alt="Certificate presentation at an industry seminar" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/5.jpg" class="gal-siz-1" alt="Certificate presentation at an industry seminar" loading="lazy">
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/6.jpg" class="gal-siz-2" alt="Certificate presentation at an industry seminar" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/7.jpg" class="gal-siz-2" alt="Urgent Rishta Services recognised at an industry seminar" loading="lazy">
                        <div class="txt">
                            <span class="gal-label">Recognised for Urgent Rishta Services</span>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/8.jpg" class="gal-siz-1" alt="Urgent Rishta representatives with fellow delegates at an industry seminar" loading="lazy">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/9.jpg" class="gal-siz-2" alt="Urgent Rishta founder speaking with the media" loading="lazy">
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/10.jpg" class="gal-siz-1" alt="Urgent Rishta team and guests at an industry gathering" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="ur-cta">
        <div class="ur-cta__overlay"></div>
        <div class="ur-cta__inner">
            <h2>Your Right Match Could Be One Introduction Away</h2>
            <p>Start your search with a matchmaking service built around privacy, professionalism and personal attention.</p>
            <div class="ur-cta__actions">
                <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-cta__btn ur-cta__btn--primary">Book a Private Consultation</a>
                <a href="/register" class="ur-btn ur-cta__btn ur-cta__btn--ghost">Create Your Profile</a>
            </div>
            <ul class="ur-cta__trust">
                <li>Confidential</li>
                <li>Verified</li>
                <li>Family-Friendly</li>
            </ul>
            <p class="ur-cta__disclaimer">Strictly for genuine marriage proposals. No dating or casual relationships.</p>
        </div>
    </section>


    <!-- Popup Modal -->
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <button class="close-btn" onclick="closePopup()">×</button>
            
            <!-- Title Added -->
            <h2>Consultation Fee</h2>
            <p class="package-price">Fee: 2000 PKR</p>

            <!-- Bank Details -->
            <div class="bank-details">
                <p><strong>Account Title:</strong> Urgent Rishta</p>
                <p>
                    <strong>Account Number:</strong> 07900010047772550026 
                    <span class="copy-icon" onclick="copyToClipboard('07900010047772550026')">📋</span>
                </p>
                <p><strong>Bank Name:</strong> Allied Bank Limited</p>
                <p>
                    <strong>IBAN:</strong> PK12ABPA0010047772550026 
                    <span class="copy-icon" onclick="copyToClipboard('PK12ABPA0010047772550026')">📋</span>
                </p>
                <p><strong>SWIFT Code:</strong> ABPAPKKA</p>
            </div>

            <!-- Note Box -->
            <div class="note-box">
                Please provide a screenshot of your payment on our WhatsApp after completing the transaction.
            </div>

            <!-- WhatsApp Button -->
            <a href="https://wa.me/923040227000?text=I%20have%20made%20the%20payment.%20Here%20is%20the%20screenshot."
               target="_blank" class="whatsapp-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
                Contact Us on WhatsApp
            </a>
        </div>
    </div>

    <script>
        // Open the popup
        function openPopup() {
            document.getElementById("popup").style.display = "block";
        }

        // Close the popup
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            alert("Copied to clipboard: " + text);
        }
    </script>
    <!-- END -->
    
<script type="text/javascript">
    function resetSearchButton() {
        var elem = $("#search_button");
        elem.html("Search");
        elem.prop('disabled', false);
    }

    $(document).ready(function() {
        resetSearchButton();

        // Animated count-up for the enquiry bar stats (Verified Profiles, Successful Matches, Years)
        function initStatCounters() {
            var counters = document.querySelectorAll('.ur-counter');
            if (!counters.length) return;

            function runCounter(el) {
                var target = parseInt(el.getAttribute('data-target'), 10) || 0;
                var duration = 1600;
                var start = null;

                function step(timestamp) {
                    if (!start) start = timestamp;
                    var progress = Math.min((timestamp - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                    var value = Math.floor(eased * target);
                    el.textContent = value.toLocaleString('en-US');
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        el.textContent = target.toLocaleString('en-US');
                    }
                }
                window.requestAnimationFrame(step);
            }

            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            runCounter(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.4 });
                counters.forEach(function(counter) { observer.observe(counter); });
            } else {
                counters.forEach(runCounter);
            }
        }
        initStatCounters();

        // Re-init AFTER master.blade's $(".selectpicker").select2() so age lists stay styled/capped
        function initHeroSearchSelects() {
            if (typeof $.fn.select2 !== 'function') return;

            var countryFlags = {
                pakistan: '🇵🇰', uae: '🇦🇪', 'united arab emirates': '🇦🇪',
                uk: '🇬🇧', 'united kingdom': '🇬🇧', england: '🇬🇧',
                usa: '🇺🇸', 'united states': '🇺🇸', 'united states of america': '🇺🇸',
                canada: '🇨🇦', 'saudi arabia': '🇸🇦', qatar: '🇶🇦',
                oman: '🇴🇲', kuwait: '🇰🇼', bahrain: '🇧🇭',
                australia: '🇦🇺', germany: '🇩🇪', france: '🇫🇷',
                india: '🇮🇳', bangladesh: '🇧🇩', turkey: '🇹🇷',
                malaysia: '🇲🇾', italy: '🇮🇹', spain: '🇪🇸'
            };

            function optionIcon(field, text) {
                var t = (text || '').toLowerCase();
                var $ico = $('<span class="ur-opt__ico"></span>');
                if (field === 'gender') {
                    if (t.indexOf('bride') !== -1 || t === 'female') {
                        $ico.addClass('ur-opt__ico--pink').html('<i class="fa fa-female"></i>');
                    } else {
                        $ico.addClass('ur-opt__ico--teal').html('<i class="fa fa-male"></i>');
                    }
                    return $ico;
                }
                if (field === 'aged_from' || field === 'aged_to') {
                    return $ico.html('<i class="fa fa-calendar"></i>');
                }
                if (field === 'marital_status') {
                    if (t.indexOf('never') !== -1) $ico.html('<i class="fa fa-circle-o"></i>');
                    else if (t.indexOf('divorced') !== -1) $ico.addClass('ur-opt__ico--gold').html('<i class="fa fa-heartbeat"></i>');
                    else if (t.indexOf('widow') !== -1) $ico.addClass('ur-opt__ico--gold').html('<i class="fa fa-star-o"></i>');
                    else if (t.indexOf('separat') !== -1) $ico.addClass('ur-opt__ico--blue').html('<i class="fa fa-pause"></i>');
                    else if (t.indexOf('await') !== -1) $ico.addClass('ur-opt__ico--gold').html('<i class="fa fa-hourglass-half"></i>');
                    else $ico.html('<i class="fa fa-heart"></i>');
                    return $ico;
                }
                if (field === 'country') {
                    var flag = countryFlags[t];
                    if (flag) return $('<span class="ur-opt__ico ur-opt__ico--flag"></span>').text(flag);
                    return $ico.html('<i class="fa fa-globe"></i>');
                }
                if (field === 'language') {
                    return $ico.addClass('ur-opt__ico--blue').html('<i class="fa fa-comment"></i>');
                }
                return $ico.html('<i class="fa fa-circle"></i>');
            }

            function optionRow(data) {
                if (data.id === '' || data.id == null) return null;
                var $opt = $(data.element);
                var field = $opt.closest('select').attr('id') || '';
                var $row = $('<span class="ur-opt"></span>');
                $row.append(optionIcon(field, data.text));
                $row.append($('<span class="ur-opt__txt"></span>').text(data.text));
                return $row;
            }

            $('.ur-hero-a__search-card select.selectpicker').each(function() {
                var $el = $(this);
                var placeholder = $el.find('option[value=""]').text() || 'Select one...';
                var $icon = $el.closest('.form-group').find('.ur-field-ico').html() || '<i class="fa fa-chevron-down"></i>';
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    width: '100%',
                    minimumResultsForSearch: 12,
                    dropdownCssClass: 'ur-hero-select-drop',
                    dropdownParent: $('.ur-hero-a__search-card'),
                    placeholder: placeholder,
                    templateResult: optionRow,
                    templateSelection: function(data) {
                        return data.text || placeholder;
                    }
                });
                $el.off('select2:open.urHero').on('select2:open.urHero', function() {
                    setTimeout(function() {
                        var $drop = $('.select2-container--open .select2-dropdown');
                        $drop.addClass('ur-hero-select-drop');
                        $drop.find('.ur-select-head').remove();
                        var $head = $('<div class="ur-select-head"></div>');
                        $head.append($icon);
                        $head.append($('<span></span>').text(placeholder));
                        $drop.prepend($head);
                    }, 0);
                });
            });
        }
        setTimeout(initHeroSearchSelects, 80);

        if ($('.ur-quotes').length && typeof $.fn.slick === 'function') {
            var $quotes = $('.ur-quotes');
            $quotes.slick({
                infinite: true,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
                speed: 450,
                arrows: false,
                dots: true,
                adaptiveHeight: false,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

            $('.ur-quotes-prev').on('click', function() {
                $quotes.slick('slickPrev');
            });
            $('.ur-quotes-next').on('click', function() {
                $quotes.slick('slickNext');
            });
        }
    });

    // Browser Back restores page from cache with "Processing..." state — reset it
    window.addEventListener('pageshow', function(event) {
        resetSearchButton();
    });

    $("#search_form").on("submit", function() {
        var elem = $("#search_button");
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
    });

</script>
@endsection
