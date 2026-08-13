@extends('layouts.master')
@section('main-content')
<link rel="stylesheet" href="/css/ur-hero.css?29">
{{-- Option A (1a) full homepage — pink theme --}}
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

    .ur-promise { display: grid; grid-template-columns: 1.05fr 1fr; gap: 40px 48px; align-items: center; }
    .ur-promise .ab-wel-lhs {
        position: relative;
        min-height: 560px;
        display: block !important;
        margin-bottom: 0;
    }
    .ur-promise .ab-wel-1 {
        position: absolute;
        width: 75%;
        height: 550px;
        object-fit: cover;
        left: 0;
        top: 0;
        border-radius: 15px;
    }
    .ur-promise .ab-wel-2 {
        width: 80%;
        height: 300px;
        object-fit: cover;
        z-index: 1;
        position: relative;
        margin: 47% 10% 5% 15%;
        border-width: 15px 0 0 15px;
        border-top-style: solid;
        border-left-style: solid;
        border-top-color: #fff;
        border-left-color: #fff;
        border-radius: 0 100px 15px;
    }
    .ur-promise .ab-wel-3 {
        width: 100px;
        height: 100px;
        border: 7px solid rgb(240, 168, 5);
        border-radius: 50%;
        left: -39px;
        top: -32px;
        z-index: 0;
        position: absolute;
    }
    .ur-promise .ab-wel-4 {
        width: 200px;
        height: 200px;
        border: 7px solid rgb(255, 226, 240);
        border-radius: 20px;
        right: 9px;
        bottom: -4px;
        position: absolute;
    }
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
    .ur-check {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 14.5px;
        line-height: 1.6;
        color: #33403A;
        margin-bottom: 12px;
        padding: 10px 12px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid rgba(233,30,99,.08);
    }
    .ur-check i {
        color: #fff;
        font-style: normal;
        font-weight: 700;
        width: 22px;
        height: 22px;
        min-width: 22px;
        border-radius: 50%;
        background: var(--pink);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        margin-top: 1px;
    }
    .ur-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 26px; }
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
    .ur-step { background: var(--cream); padding: 30px 24px; border-top: 3px solid var(--pink); }
    .ur-step--dark { background: var(--pink); }
    .ur-step--dark .ur-step__title, .ur-step--dark .ur-step__num { color: #fff; }
    .ur-step--dark .ur-step__text { color: #FFE0EC; }
    .ur-step__num { font-family: 'Playfair Display', Georgia, serif; font-size: 34px; color: var(--pink); line-height: 1; margin-bottom: 12px; }
    .ur-step__title { font-weight: 700; font-size: 16px; margin-bottom: 8px; color: var(--ink); }
    .ur-step__text { font-size: 13.5px; line-height: 1.65; color: var(--muted); }

    /* Restored classic How-it-works timeline */
    .ur-how-timeline {
        background: #fff;
        padding: 70px 0 40px;
        overflow: visible;
    }
    .ur-how-timeline .home-tit {
        width: 100%;
        text-align: center;
        margin-bottom: 40px;
        padding: 8px 16px 0;
        overflow: visible;
    }
    .ur-how-timeline .home-tit p {
        line-height: 1.3;
        margin-bottom: 6px;
    }
    .ur-how-timeline .wedd-tline .inn {
        float: none;
        margin: 0 auto;
        width: 70%;
    }
    .ur-how-timeline .wedd-tline .inn ul {
        position: relative;
        float: left; /* keeps height so the center line can stretch */
        width: 100%;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    /* Vertical link between step dots */
    .ur-how-timeline .wedd-tline .inn ul:before {
        content: '';
        background: #ddcebc !important;
        position: absolute !important;
        width: 2px !important;
        top: 12px !important;
        bottom: 40px !important;
        height: auto !important;
        left: 50% !important;
        margin-left: -1px;
        z-index: 0;
        display: block !important;
    }
    .ur-how-timeline .wedd-tline .inn ul li {
        position: relative;
        float: left;
        width: 100%;
        padding-bottom: 50px;
        list-style: none;
    }
    .ur-how-timeline .wedd-tline .inn ul li:before {
        content: '';
        position: absolute;
        width: 25px;
        height: 25px;
        background: #66451c;
        z-index: 2;
        border-radius: 50px;
        border: 5px solid #fff;
        box-sizing: border-box;
        margin-top: 2px;
        box-shadow: 0 0 10px 0.6px rgb(40 30 20 / 8%);
        left: calc(50% - 12px);
    }

    /* Tablet + phone: clean left-rail timeline (icon then text, always) */
    @media (max-width: 991px) {
        .ur-how-timeline {
            padding: 48px 0 28px;
            overflow: visible;
        }
        .ur-how-timeline .home-tit {
            margin-bottom: 28px;
            padding: 12px 12px 0;
        }
        .ur-how-timeline .home-tit h2 {
            font-size: 32px !important;
            line-height: 1.2 !important;
        }
        .ur-how-timeline .home-tit h2 span {
            font-size: inherit !important;
        }
        .ur-how-timeline .home-tit .leaf1 {
            margin-top: 6px;
            height: 44px;
        }
        .ur-how-timeline .wedd-tline .inn {
            width: 100% !important;
            float: none !important;
            padding: 8px 16px 0 !important;
            box-sizing: border-box;
        }
        .ur-how-timeline .wedd-tline .inn ul {
            float: none !important;
            display: block !important;
            overflow: visible;
            padding-left: 0 !important;
        }
        .ur-how-timeline .wedd-tline .inn ul:before {
            left: 31px !important;
            margin-left: 0 !important;
            top: 18px !important;
            bottom: 24px !important;
        }
        .ur-how-timeline .wedd-tline .inn ul li {
            float: none !important;
            width: 100% !important;
            padding: 0 0 28px !important;
            clear: both;
        }
        .ur-how-timeline .wedd-tline .inn ul li:before {
            left: 20px !important;
            top: 18px;
            margin-top: 0;
            width: 22px;
            height: 22px;
            border-width: 4px;
        }
        .ur-how-timeline .tline-inn,
        .ur-how-timeline .tline-inn-reve {
            display: flex !important;
            flex-direction: row !important;
            align-items: flex-start !important;
            float: none !important;
            width: 100% !important;
            gap: 14px;
            padding-left: 56px !important;
            box-sizing: border-box;
            position: relative !important;
        }
        /* Always: icon first, text second (fixes reversed steps) */
        .ur-how-timeline .tline-im,
        .ur-how-timeline .tline-inn-reve .tline-im {
            order: 1 !important;
            float: none !important;
            width: 58px !important;
            flex: 0 0 58px !important;
            padding: 0 !important;
            margin: 0 !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
        }
        .ur-how-timeline .tline-im img,
        .ur-how-timeline .tline-inn-reve .tline-im img {
            float: none !important;
            display: block !important;
            width: 58px !important;
            height: auto !important;
            max-width: 100%;
            margin: 0 auto;
        }
        .ur-how-timeline .tline-con,
        .ur-how-timeline .tline-inn-reve .tline-con {
            order: 2 !important;
            float: none !important;
            width: 100% !important;
            flex: 1 1 auto !important;
            padding: 4px 0 0 !important;
            margin: 0 !important;
            text-align: left !important;
            min-width: 0;
        }
        .ur-how-timeline .tline-inn div h5,
        .ur-how-timeline .tline-con h5 {
            font-size: 22px !important;
            line-height: 1.25 !important;
            margin: 0 0 6px !important;
            padding-right: 4px;
        }
        .ur-how-timeline .tline-inn div p,
        .ur-how-timeline .tline-con p {
            font-size: 14px !important;
            line-height: 1.55 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .ur-how-timeline .animate {
            opacity: 1 !important;
            transform: none !important;
        }
    }

    @media (max-width: 575px) {
        .ur-how-timeline .wedd-tline .inn {
            padding: 4px 12px 0 !important;
        }
        .ur-how-timeline .tline-inn,
        .ur-how-timeline .tline-inn-reve {
            padding-left: 52px !important;
            gap: 10px;
        }
        .ur-how-timeline .wedd-tline .inn ul:before {
            left: 28px !important;
        }
        .ur-how-timeline .wedd-tline .inn ul li:before {
            left: 17px !important;
            width: 20px;
            height: 20px;
        }
        .ur-how-timeline .tline-im,
        .ur-how-timeline .tline-inn-reve .tline-im {
            width: 52px !important;
            flex-basis: 52px !important;
        }
        .ur-how-timeline .tline-im img,
        .ur-how-timeline .tline-inn-reve .tline-im img {
            width: 52px !important;
        }
        .ur-how-timeline .tline-inn div h5,
        .ur-how-timeline .tline-con h5 {
            font-size: 20px !important;
        }
        .ur-how-timeline .tline-inn div p,
        .ur-how-timeline .tline-con p {
            font-size: 13.5px !important;
        }
    }

    /* Full-width photo gallery — outside ur-page / ur-wrap */
    .ur-photo-gallery {
        width: 100%;
        max-width: none;
        background: #fff;
        padding: 60px 0 24px;
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
    .ur-photo-gallery .home-tit {
        width: 100%;
        text-align: center;
        margin: 0 auto 36px;
        float: none;
        padding: 0 12px;
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
    .ur-stories-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 22px; }
    .ur-story { position: relative; border-radius: 6px; overflow: hidden; }
    .ur-story--lg { height: 360px; }
    .ur-story--sm { height: 169px; }
    .ur-story img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ur-story__overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 45%, rgba(26,6,16,.9)); }
    .ur-story__meta { position: absolute; bottom: 18px; left: 18px; right: 18px; color: #fff; }
    .ur-story__meta h4 { font-family: 'Playfair Display', Georgia, serif; font-size: 22px; font-weight: 600; margin: 0; }
    .ur-story__meta span { font-size: 12.5px; color: #F8D7E5; }
    .ur-story-col { display: flex; flex-direction: column; gap: 22px; }
    .ur-link { font-size: 14px; font-weight: 700; color: var(--pink); text-decoration: none !important; }
    .ur-link:hover { color: var(--pink-dark); }

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

    .ur-team { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 70px; }
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
        gap: 10px 22px;
        list-style: none;
        margin: 0;
        padding: 0;
        color: rgba(255, 232, 240, 0.88);
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .04em;
    }
    .ur-cta__trust li {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ur-cta__trust li::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #E8C48A;
        box-shadow: 0 0 8px rgba(232, 196, 138, 0.7);
    }
    @media (prefers-reduced-motion: reduce) {
        .ur-cta { background-attachment: scroll; }
    }

    .ur-center { text-align: center; max-width: 560px; margin: 0 auto 44px; }
    .ur-sec--deep .ur-eyebrow { color: #FFB6CE; }
    .ur-sec--deep .ur-h2 { color: #fff; }
    .ur-sec--pink .ur-trust-note { color: #FFF5F8; }

    @media (max-width: 991px) {
        .ur-promise, .ur-stories-grid, .ur-team { grid-template-columns: 1fr; }
        .ur-stats { grid-template-columns: 1fr 1fr; }
        .ur-stats__card:nth-child(2) { border-right: none; }
        .ur-stats__card:nth-child(1),
        .ur-stats__card:nth-child(2) { border-bottom: 1px solid var(--line); padding-bottom: 18px; margin-bottom: 8px; }
        .ur-steps { grid-template-columns: 1fr 1fr; }
        .ur-promise .ab-wel-lhs {
            min-height: 480px;
        }
        .ur-promise .ab-wel-1 {
            height: 460px;
        }
        .ur-gallery { grid-template-columns: repeat(3, 1fr); }
        .ur-contact-row { grid-template-columns: 1fr; }
        .ur-team { grid-template-columns: 1fr 1fr; }
        .ur-story--lg { height: 280px; }
        .ur-story--sm { height: 160px; }
        .ur-wrap { padding-left: 16px; padding-right: 16px; box-sizing: border-box; }
    }
    @media (max-width: 767px) {
        .ur-promise, .ur-stories-grid, .ur-team, .ur-steps, .ur-stats, .ur-gallery { grid-template-columns: 1fr; }
        .ur-stats__card { border-right: none !important; border-bottom: 1px solid var(--line); padding: 16px 8px; }
        .ur-stats__card:last-child { border-bottom: none; }
        .ur-story--lg,
        .ur-story--sm { height: 220px; }
        .ur-story-col { gap: 14px; }
        .ur-team__card img { height: 200px; }
        .ur-quote { min-height: 0; padding: 22px 18px; }
        .ur-gallery { grid-auto-rows: 160px; }
        .ur-gallery img.span2 { grid-row: auto; }
        .ur-sec { padding: 56px 16px; }
        .ur-promise .ab-wel-lhs {
            min-height: 420px;
            margin-bottom: 20px;
        }
        .ur-promise .ab-wel-1 {
            height: 380px;
        }
        .ur-promise .ab-wel-2 {
            height: 220px;
        }
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
    .ur-actions .ur-btn {
        max-width: 100%;
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

        .ur-actions {
            gap: 10px;
        }
        .ur-actions .ur-btn {
            width: 100%;
            justify-content: center;
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

<section class="ur-hero-a" aria-label="Home hero">
    <div class="ur-hero-a__overlay"></div>

    <div class="ur-hero-a__inner">
        <div class="ur-hero-a__main">
            <div class="ur-hero-a__content">
                <div class="ur-hero-a__badge">Est. 2018 • 5,000+ Marriages</div>
                <h1 class="ur-hero-a__title">
                    A Respectful Path to
                    <span class="ur-hero-line2">
                        <em>Your Rishta</em><span class="ur-hero-heart" aria-hidden="true"><i class="fa fa-heart-o"></i><span class="ur-hero-spark">✦</span></span>
                    </span>
                </h1>
                <p class="ur-hero-a__subtitle">
                    Verified profiles and dedicated matchmakers serving Pakistani &amp; Muslim families in Pakistan, the UK, USA, Canada and the Gulf.
                </p>

                <ul class="ur-hero-a__features">
                    <li>
                        <i class="fa fa-shield" aria-hidden="true"></i>
                        <span><b>100% Verified</b><em>Profiles</em></span>
                    </li>
                    <li>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span><b>Dedicated</b><em>Matchmakers</em></span>
                    </li>
                    <li>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <span><b>Privacy</b><em>Guaranteed</em></span>
                    </li>
                    <li>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span><b>Successful</b><em>Matches</em></span>
                    </li>
                </ul>
            </div>

            <div class="ur-hero-a__visual">
                <div class="ur-hero-a__photo-wrap">
                    <img class="ur-hero-a__photo" src="/images/slider_images/slider_image_1.png" alt="Happy couple" loading="eager">
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
                                <option value="female">Bride</option>
                                <option value="male">Groom</option>
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

<section class="ur-feat-row" aria-label="Why Urgent Rishta">
    <div class="ur-feat-row__inner">
        <div class="ur-feat-row__item">
            <div class="ur-feat-row__icon" aria-hidden="true"><i class="fa fa-users"></i></div>
            <h3>Serious Matches</h3>
            <p>Connect with people who are genuinely looking for a life partner.</p>
        </div>
        <div class="ur-feat-row__item">
            <div class="ur-feat-row__icon" aria-hidden="true"><i class="fa fa-heart"></i></div>
            <h3>Family Involved</h3>
            <p>We believe in families playing a key role in finding the perfect match.</p>
        </div>
        <div class="ur-feat-row__item">
            <div class="ur-feat-row__icon" aria-hidden="true"><i class="fa fa-shield"></i></div>
            <h3>Safe &amp; Secure</h3>
            <p>Your privacy is our priority with verified profiles and secure communication.</p>
        </div>
        <div class="ur-feat-row__item">
            <div class="ur-feat-row__icon" aria-hidden="true"><i class="fa fa-headphones"></i></div>
            <h3>Expert Support</h3>
            <p>Our matchmakers are here to guide you at every step.</p>
        </div>
    </div>
</section>


<div class="ur-page">
    <!-- WELCOME / PROMISE -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap ur-promise">
            <div class="ab-wel-lhs">
                <span class="ab-wel-3"></span>
                <img src="images/about/1.jpg" alt="" loading="lazy" class="ab-wel-1">
                <img src="images/couples/20.jpg" alt="" loading="lazy" class="ab-wel-2">
                <span class="ab-wel-4"></span>
            </div>
            <div>
                <div class="ur-eyebrow">Our Promise</div>
                <p class="ur-welcome-kicker">Welcome to</p>
                <h2 class="ur-welcome-brand">Urgent Rishta</h2>
                <h3 class="ur-h2" style="margin-bottom:14px;font-size:clamp(24px,2.6vw,30px);">Matchmaking with dignity and discretion</h3>
                <p class="ur-lead" style="max-width:480px;margin-bottom:18px;">Every profile is personally reviewed before approval. Every introduction is made with your family's values in mind — never an algorithm alone.</p>
                <div style="max-width:480px;">
                    <div class="ur-check"><i>✓</i><div><b>Verified members only</b> — mobile and ID checked before a profile goes live</div></div>
                    <div class="ur-check"><i>✓</i><div><b>A named matchmaker</b> guides your search from first search to nikah</div></div>
                    <div class="ur-check"><i>✓</i><div><b>Privacy controls</b> — you decide who sees your photos and contact details</div></div>
                </div>
                <div class="ur-actions">
                    <a href="/register" class="ur-btn ur-btn--solid">Start Your Journey</a>
                    <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-btn--outline">Book a Consultation</a>
                </div>
                <div class="ur-contact-row">
                    <a class="ur-contact" href="tel:+923040227000">
                        <span class="ur-contact__icon"><i class="fa fa-phone"></i></span>
                        <span>
                            <span class="ur-contact__label">Enquiry</span>
                            <span class="ur-contact__value">+92 304 0227000</span>
                        </span>
                    </a>
                    <a class="ur-contact" href="mailto:urgentrishta.co@gmail.com">
                        <span class="ur-contact__icon"><i class="fa fa-envelope"></i></span>
                        <span>
                            <span class="ur-contact__label">Get Support</span>
                            <span class="ur-contact__value">urgentrishta.co@gmail.com</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- COUNTS -->
    <section class="ur-sec ur-sec--cream" style="padding-top:0;">
        <div class="ur-wrap">
            <div class="ur-stats">
                <div class="ur-stats__card">
                    <div class="ur-stats__icon"><i class="fa fa-heart"></i></div>
                    <div class="ur-stats__num">5K</div>
                    <div class="ur-stats__sub">Couples Paired</div>
                </div>
                <div class="ur-stats__card">
                    <div class="ur-stats__icon"><i class="fa fa-users"></i></div>
                    <div class="ur-stats__num">15,000+</div>
                    <div class="ur-stats__sub">Registrants</div>
                </div>
                <div class="ur-stats__card">
                    <div class="ur-stats__icon"><i class="fa fa-male"></i></div>
                    <div class="ur-stats__num">8,000+</div>
                    <div class="ur-stats__sub">Men</div>
                </div>
                <div class="ur-stats__card">
                    <div class="ur-stats__icon"><i class="fa fa-female"></i></div>
                    <div class="ur-stats__num">7,000+</div>
                    <div class="ur-stats__sub">Women</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS — original vertical timeline (preferred) -->
    <section class="ur-how-timeline">
        <div class="wedd-tline">
            <div class="container">
                <div class="row">
                    <div class="home-tit">
                        <p>Moments</p>
                        <h2><span>How it works</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                    <div class="inn">
                        <ul>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <img src="images/icon/rings.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <h5>Register</h5>
                                        <p>Create your account by providing essential details and preferences. A complete profile increases your chances of finding the perfect match.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <h5>Find your Match</h5>
                                        <p>Explore a wide range of verified profiles based on your desired criteria, such as age, education, background, and personal values.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <img src="images/icon/wedding-2.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <img src="images/icon/love-birds.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <h5>Send Interest</h5>
                                        <p>Show your interest in a potential match by sending a request. If they accept, you can take the next step toward meaningful communication.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <h5>Get Profile Information</h5>
                                        <p>Once your interest is accepted, gain access to detailed profile information to ensure compatibility before proceeding further.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <img src="images/icon/network.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <img src="images/icon/chat.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <h5>Start Meetups</h5>
                                        <p>Engage in conversations, build a connection, and arrange meetups with mutual consent to understand each other better.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower" data-ani="animate__fadeInUp">
                                        <h5>Getting Married</h5>
                                        <p>When you find the right person, take the next step toward a lifelong commitment and begin your journey toward marriage.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow" data-ani="animate__fadeInUp">
                                        <img src="images/icon/wedding-couple.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUCCESS STORIES -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap">
            <div class="ur-stories-head">
                <div>
                    <div class="ur-eyebrow">Happy Endings</div>
                    <h2 class="ur-h2">Recent success stories</h2>
                </div>
                <a href="{{ url('stories') }}" class="ur-link">View all stories →</a>
            </div>
            <div class="ur-stories-grid">
                <div class="ur-story ur-story--lg">
                    <img src="images/couples/6.jpg" alt="" loading="lazy">
                    <div class="ur-story__overlay"></div>
                    <div class="ur-story__meta">
                        <h4>Bilal &amp; Fatima</h4>
                        <span>Married 2025 · Lahore, Pakistan</span>
                    </div>
                </div>
                <div class="ur-story-col">
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/7.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Zain &amp; Ayesha</h4><span>London, UK</span></div>
                    </div>
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/8.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Hamza &amp; Zara</h4><span>Toronto, Canada</span></div>
                    </div>
                </div>
                <div class="ur-story-col">
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/9.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Ali &amp; Mahnoor</h4><span>Dubai, UAE</span></div>
                    </div>
                    <div class="ur-story ur-story--sm">
                        <img src="images/couples/10.jpg" alt="" loading="lazy">
                        <div class="ur-story__overlay"></div>
                        <div class="ur-story__meta"><h4 style="font-size:18px;">Omar &amp; Hira</h4><span>Houston, USA</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="ur-sec ur-sec--deep">
        <div class="ur-wrap">
            <div class="ur-quotes-head">
                <div class="ur-center">
                    <div class="ur-eyebrow">Real Words, Real Trust</div>
                    <h2 class="ur-h2">Trusted by 5,000+ families</h2>
                </div>
                <div class="ur-quotes-nav">
                    <button type="button" class="ur-quotes-prev" aria-label="Previous review">‹</button>
                    <button type="button" class="ur-quotes-next" aria-label="Next review">›</button>
                </div>
            </div>
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
                        <div class="ur-quote__text">“A name of total trust and true professionalism with kind behaviour — dedicated and organised. Highly recommended.”</div>
                        <div class="ur-quote__person">
                            <img src="images/user/3.jpg" alt="" loading="lazy">
                            <div><strong>Usman</strong><span>Saudi Arabia</span></div>
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
            </div>
            </div>
        </div>
    </section>

    <!-- TEAM -->
    <section class="ur-sec ur-sec--cream">
        <div class="ur-wrap">
            <div class="ur-center">
                <div class="ur-eyebrow">Our Professionals</div>
                <h2 class="ur-h2">Meet our matchmaking team</h2>
            </div>
            <div class="ur-team">
                <div class="ur-team__card">
                    <img src="images/profiles/6.jpg" alt="Usman Zaheer" loading="lazy">
                    <h4>Usman Zaheer</h4>
                    <p>CEO &amp; Founder</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
                <div class="ur-team__card">
                    <img src="images/profiles/7.jpg" alt="Qanita Sundas" loading="lazy">
                    <h4>Qanita Sundas</h4>
                    <p>Co-Founder</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/923331623144" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
                <div class="ur-team__card">
                    <img src="images/profiles/8.jpg" alt="Minahil Malik" loading="lazy">
                    <h4>Minahil Malik</h4>
                    <p>Relationship Manager</p>
                    <ul class="ur-team__social">
                        <li><a href="https://wa.me/447445723296" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
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
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>{{-- /ur-page — gallery + CTA sit outside wrappers for full page width --}}

    <!-- PHOTO GALLERY — full page width -->
    <section class="ur-photo-gallery">
        <div class="container-fluid ur-photo-gallery__fluid">
            <div class="home-tit">
                <p>Events</p>
                <h2><span>Photo gallery</span></h2>
                <span class="leaf1"></span>
                <span class="tit-ani-"></span>
            </div>
            <div class="row no-gutters gall-inn">
                <div class="col-6 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/1.jpg" class="gal-siz-1" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/2.jpg" class="gal-siz-2" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/3.jpg" class="gal-siz-2" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/4.jpg" class="gal-siz-1" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/5.jpg" class="gal-siz-1" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/6.jpg" class="gal-siz-2" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/7.jpg" class="gal-siz-2" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/8.jpg" class="gal-siz-1" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/9.jpg" class="gal-siz-2" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                    <div class="gal-im animate" data-ani="urGalReveal">
                        <img src="images/gallery/10.jpg" class="gal-siz-1" alt="" loading="lazy">
                        <div class="txt">
                            <span>Find your match with us</span>
                            <h4>Urgent Rishta</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="ur-cta">
        <div class="ur-cta__overlay"></div>
        <div class="ur-cta__glow" aria-hidden="true"></div>
        <div class="ur-cta__inner">
            <span class="ur-cta__eyebrow">Start your journey</span>
            <h2>Find your perfect match, <em>today</em></h2>
            <p>Join 15,000+ members who trust Urgent Rishta for a respectful, halal path to marriage.</p>
            <div class="ur-cta__actions">
                <a href="/register" class="ur-btn ur-cta__btn ur-cta__btn--primary">Register Now</a>
                <a href="javascript:void(0);" onclick="openPopup()" class="ur-btn ur-cta__btn ur-cta__btn--ghost">Book a Consultation</a>
            </div>
            <ul class="ur-cta__trust">
                <li>15,000+ members</li>
                <li>5,000+ marriages</li>
                <li>Halal &amp; respectful</li>
            </ul>
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
