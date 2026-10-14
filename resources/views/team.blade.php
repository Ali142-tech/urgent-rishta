@extends('layouts.master')
@section('main-content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    body.page-team #main-content { background: #FBF7EF; }

    .tm-page {
        --tm-green: #123A2E;
        --tm-green-deep: #0F2E24;
        --tm-gold: #C9974D;
        --tm-cream: #FBF7EF;
        --tm-sand: #EFE7D6;
        --tm-line: #F0EADD;
        --tm-terracotta: #B5674A;
        --tm-text: #5B6560;
        --tm-ink: #1C2321;
        --tm-cream-text: #EFE3C8;
        --tm-cream-text-2: #D7E4DC;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--tm-cream);
        color: var(--tm-ink);
    }
    .tm-page * { box-sizing: border-box; }
    .tm-page a { text-decoration: none; }

    /* Videos (Welcome + Company Highlight) — cinematic dark section */
    .tm-videos { position: relative; overflow: hidden; padding: 84px 20px 92px; color: #fff;
        background: radial-gradient(1100px 520px at 12% -10%, rgba(201,151,77,.20), rgba(201,151,77,0) 60%),
                    radial-gradient(900px 480px at 100% 110%, rgba(201,151,77,.14), rgba(201,151,77,0) 60%),
                    linear-gradient(135deg, #0B241C 0%, #123A2E 52%, #0F2E24 100%); }
    .tm-videos::after { content: ''; position: absolute; inset: 0; pointer-events: none; opacity: .5;
        background-image: radial-gradient(rgba(239,227,200,.10) 1px, transparent 1.4px); background-size: 26px 26px;
        -webkit-mask-image: linear-gradient(180deg, transparent, #000 25%, #000 75%, transparent); mask-image: linear-gradient(180deg, transparent, #000 25%, #000 75%, transparent); }
    .tm-videos__inner { position: relative; z-index: 1; max-width: 1240px; margin: 0 auto; }
    .tm-videos__head { text-align: center; margin-bottom: 46px; }
    .tm-videos__eyebrow { display: inline-flex; align-items: center; gap: 12px; font-size: 12px; font-weight: 800; letter-spacing: .22em; text-transform: uppercase; color: var(--tm-gold); margin-bottom: 14px; }
    .tm-videos__eyebrow::before, .tm-videos__eyebrow::after { content: ''; width: 34px; height: 1px; background: linear-gradient(90deg, transparent, var(--tm-gold)); }
    .tm-videos__eyebrow::after { transform: scaleX(-1); }
    .tm-videos__head h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 44px; line-height: 1.15; color: #fff; margin: 0 0 14px; }
    .tm-videos__head h2 em { color: var(--tm-gold); font-style: italic; }
    .tm-videos__head p { margin: 0 auto; max-width: 620px; font-size: 16px; line-height: 1.75; color: var(--tm-cream-text-2); }

    .tm-videos__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 34px; }
    .tm-video { position: relative; border-radius: 26px; padding: 8px; background: linear-gradient(145deg, rgba(239,227,200,.34), rgba(201,151,77,.10) 40%, rgba(239,227,200,.22));
        box-shadow: 0 30px 70px rgba(0,0,0,.45), 0 0 0 1px rgba(239,227,200,.10); transition: transform .3s ease, box-shadow .3s ease; }
    .tm-video:hover { transform: translateY(-6px); box-shadow: 0 40px 84px rgba(0,0,0,.55), 0 0 0 1px rgba(201,151,77,.35); }
    .tm-video__frame { position: relative; aspect-ratio: 16 / 9; border-radius: 19px; overflow: hidden; background: linear-gradient(135deg, #123A2E, #1F5C46 60%, #123A2E); isolation: isolate; }
    .tm-video__frame video { position: absolute; inset: 0; width: 100%; height: 100%; display: block; object-fit: cover; background: transparent; z-index: 0; }
    .tm-video__shade { position: absolute; inset: 0; z-index: 1; pointer-events: none; background: linear-gradient(180deg, rgba(8,24,19,.35) 0%, rgba(8,24,19,0) 32%, rgba(8,24,19,.55) 62%, rgba(8,24,19,.92) 100%); transition: opacity .35s ease; }
    .tm-video__tag { position: absolute; left: 18px; top: 18px; z-index: 3; display: inline-flex; align-items: center; gap: 7px; background: rgba(8,24,19,.62); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); color: #EFE3C8; font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; padding: 7px 14px; border-radius: 999px; border: 1px solid rgba(239,227,200,.30); pointer-events: none; transition: opacity .3s; }
    .tm-video__tag::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--tm-gold); }
    .tm-video__caption { position: absolute; left: 0; right: 0; bottom: 0; z-index: 3; padding: 26px 26px 24px; pointer-events: none; transition: opacity .3s, transform .3s; }
    .tm-video__caption b { display: block; font-family: 'Playfair Display', serif; font-size: 25px; font-weight: 700; line-height: 1.2; color: #fff; margin-bottom: 6px; text-shadow: 0 2px 14px rgba(0,0,0,.4); }
    .tm-video__caption span { display: block; max-width: 460px; font-size: 14.5px; line-height: 1.6; color: rgba(239,227,200,.92); }
    .tm-video__play { position: absolute; inset: 0; z-index: 2; width: 100%; border: 0; padding: 0; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: opacity .3s ease; }
    .tm-video__play > span { position: relative; width: 88px; height: 88px; border-radius: 50%; background: linear-gradient(145deg, #E3B369, #C9974D); display: flex; align-items: center; justify-content: center; box-shadow: 0 14px 34px rgba(0,0,0,.45); transition: transform .25s ease; margin-bottom: 34px; }
    .tm-video__play > span::before, .tm-video__play > span::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 2px solid rgba(227,179,105,.65); animation: tmPulse 2.4s ease-out infinite; }
    .tm-video__play > span::after { animation-delay: 1.2s; }
    .tm-video__play:hover > span { transform: scale(1.1); }
    .tm-video__play svg { width: 32px; height: 32px; margin-left: 5px; fill: #0F2E24; }
    @keyframes tmPulse { 0% { transform: scale(1); opacity: .9; } 100% { transform: scale(1.75); opacity: 0; } }
    .tm-video.is-playing .tm-video__play, .tm-video.is-playing .tm-video__tag, .tm-video.is-playing .tm-video__caption, .tm-video.is-playing .tm-video__shade { opacity: 0; pointer-events: none; }
    @media (prefers-reduced-motion: reduce) { .tm-video__play > span::before, .tm-video__play > span::after { animation: none; } .tm-video { transition: none; } }
    @media (max-width: 900px) {
        .tm-videos { padding: 56px 16px 62px; }
        .tm-videos__head { margin-bottom: 30px; }
        .tm-videos__head h2 { font-size: 30px; }
        .tm-videos__head p { font-size: 14.5px; }
        .tm-videos__grid { grid-template-columns: 1fr; gap: 24px; }
        .tm-video { border-radius: 22px; padding: 6px; }
        .tm-video__frame { border-radius: 17px; }
        .tm-video__play > span { width: 68px; height: 68px; margin-bottom: 26px; }
        .tm-video__play svg { width: 26px; height: 26px; }
        .tm-video__caption { padding: 18px 18px 16px; }
        .tm-video__caption b { font-size: 19px; }
        .tm-video__caption span { font-size: 13px; }
        .tm-video__tag { left: 12px; top: 12px; font-size: 10px; padding: 6px 11px; }
    }
    /* Hero */
    .tm-hero {
        background: linear-gradient(135deg, var(--tm-green) 0%, #1F5C46 55%, var(--tm-green) 100%);
        padding: 64px 20px 50px;
        text-align: center;
    }
    .tm-hero__badge {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(239,227,200,0.3);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--tm-cream-text);
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .tm-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 600;
        color: #fff;
        margin: 0 0 14px;
        line-height: 1.25;
    }
    .tm-hero h1 em { color: var(--tm-gold); font-style: italic; }
    .tm-hero p {
        font-size: 15.5px;
        color: var(--tm-cream-text-2);
        max-width: 580px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .tm-wrap { max-width: 1180px; margin: 0 auto; padding: 72px 24px; }
    .tm-section-head { text-align: center; max-width: 640px; margin: 0 auto 44px; }
    .tm-eyebrow {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--tm-terracotta);
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .tm-h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(26px, 3.2vw, 32px);
        font-weight: 600;
        margin: 0 0 12px;
        color: var(--tm-ink);
    }
    .tm-lead {
        font-size: 14.5px;
        line-height: 1.75;
        color: var(--tm-text);
        margin: 0;
    }

    /* Founder — prominent */
    .tm-founder {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 48px;
        align-items: center;
        background: #fff;
        border: 1px solid var(--tm-line);
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 18px 44px rgba(15,46,36,.08);
    }
    .tm-founder__photo { border-radius: 14px; overflow: hidden; }
    .tm-founder__photo img { width: 100%; height: 380px; object-fit: cover; object-position: center 20%; display: block; }
    .tm-founder__name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px;
        font-weight: 600;
        color: var(--tm-ink);
        margin: 0 0 6px;
    }
    .tm-founder__role {
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--tm-gold);
        margin-bottom: 18px;
    }
    .tm-founder__desc { font-size: 14.5px; line-height: 1.75; color: var(--tm-text); margin: 0 0 14px; max-width: 560px; }
    .tm-founder__actions { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; margin-top: 18px; }

    /* Team grids */
    .tm-team-label {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 0 0 28px;
    }
    .tm-team-label .tm-h2 { margin: 0; white-space: nowrap; }
    .tm-team-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--tm-line);
    }
    .tm-team-note { font-size: 13.5px; color: var(--tm-text); margin: -20px 0 28px; max-width: 620px; }

    .tm-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 8px; }
    .tm-card { text-align: center; }
    .tm-card img { width: 100%; height: 260px; object-fit: cover; border-radius: 10px; display: block; margin-bottom: 16px; box-shadow: 0 10px 24px rgba(15,46,36,.08); }
    .tm-card-img--top { object-position: center 12%; }
    .tm-card h4 { font-family: 'Playfair Display', Georgia, serif; font-weight: 600; font-size: 17px; margin: 0 0 4px; color: var(--tm-ink); }
    /* min-height (2 lines) so a longer title (e.g. "Co-Founder — National &
       International Client Handler") doesn't push that card's social icons
       lower than the rest of the row — every card reserves the same space
       for its role line whether it wraps or not. */
    .tm-card p { font-size: 12.5px; line-height: 1.4; color: var(--tm-text); margin: 0 0 10px; min-height: 35px; }

    .tm-social { display: flex; gap: 8px; justify-content: center; list-style: none; padding: 0; margin: 0; }
    .tm-social a { width: 30px; height: 30px; border-radius: 50%; border: 1px solid var(--tm-line); color: var(--tm-green); font-size: 12.5px; display: flex; align-items: center; justify-content: center; }
    .tm-social a:hover { background: var(--tm-green); color: #fff; border-color: var(--tm-green); }

    /* UK team — group/event photo gallery */
    .tm-uk-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .tm-uk-gallery img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
        box-shadow: 0 10px 24px rgba(15,46,36,.08);
        transition: transform .3s ease;
    }
    .tm-uk-gallery img:hover { transform: translateY(-4px); }

    .tm-cta { text-align: center; margin-top: 12px; }
    .tm-btn {
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
    .tm-btn--solid { background: var(--tm-green); color: var(--tm-cream-text) !important; }
    .tm-btn--solid:hover { background: var(--tm-green-deep); }

    @media (max-width: 900px) {
        .tm-founder { grid-template-columns: 1fr; text-align: center; padding: 32px 28px; }
        .tm-founder__photo img { height: 320px; }
        .tm-founder__desc { max-width: none; }
        .tm-founder__actions { justify-content: center; }
        .tm-grid { grid-template-columns: 1fr 1fr; }
        .tm-uk-gallery { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .tm-wrap { padding: 48px 18px 60px; }
        .tm-hero { padding: 48px 18px 40px; }
        .tm-grid { grid-template-columns: 1fr; }
        .tm-founder__photo img { height: 260px; }
        .tm-uk-gallery { grid-template-columns: 1fr; }
        .tm-uk-gallery img { height: 260px; }
    }
</style>

<div class="tm-page">
    <section class="tm-hero">
        <div class="tm-hero__badge">Meet The Team</div>
        <h1>The People Behind <em>Every Introduction</em></h1>
        <p>A dedicated matchmaking team across Pakistan and the UK, personally managing every step of your search.</p>
    </section>

    <section class="tm-videos" id="videos">
        <div class="tm-videos__inner">
            <div class="tm-videos__head">
                <span class="tm-videos__eyebrow">Our Story</span>
                <h2>Get to Know <em>Urgent Rishta</em></h2>
                <p>A short welcome and a look at who we are and how we work with families.</p>
            </div>
            <div class="tm-videos__grid">
                <div class="tm-video">
                    <div class="tm-video__frame">
                        <video playsinline preload="metadata" aria-label="Welcome to Urgent Rishta">
                            <source src="/Videos/1%20Welcome%20Animation.mp4#t=0.5" type="video/mp4">
                        </video>
                        <div class="tm-video__shade"></div>
                        <span class="tm-video__tag">Welcome</span>
                        <div class="tm-video__caption"><b>Welcome to Urgent Rishta</b><span>A quick introduction to our private, professional matchmaking service.</span></div>
                        <button type="button" class="tm-video__play" aria-label="Play: Welcome to Urgent Rishta"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></span></button>
                    </div>
                </div>
                <div class="tm-video">
                    <div class="tm-video__frame">
                        <video playsinline preload="metadata" aria-label="Company Highlight">
                            <source src="/Videos/Company%20Highlight.mp4#t=0.5" type="video/mp4">
                        </video>
                        <div class="tm-video__shade"></div>
                        <span class="tm-video__tag">Highlights</span>
                        <div class="tm-video__caption"><b>Company Highlight</b><span>The people, the process and the care behind every introduction.</span></div>
                        <button type="button" class="tm-video__play" aria-label="Play: Company Highlight"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></span></button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Big play button over each video; native controls appear once it plays, and only one video plays at a time.
            (function () {
                var cards = document.querySelectorAll('.tm-video');
                cards.forEach(function (card) {
                    var video = card.querySelector('video'), btn = card.querySelector('.tm-video__play');
                    btn.addEventListener('click', function () {
                        cards.forEach(function (other) { var v = other.querySelector('video'); if (v !== video) v.pause(); });
                        video.controls = true;
                        video.play();
                    });
                    video.addEventListener('play', function () { card.classList.add('is-playing'); });
                    video.addEventListener('ended', function () { video.controls = false; card.classList.remove('is-playing'); });
                });
            })();
        </script>
    </section>

    <div class="tm-wrap">
        <!-- FOUNDER -->
        <div class="tm-founder">
            <div class="tm-founder__photo">
                <img src="/images/profiles/5.jpeg" alt="Usman Zaheer" loading="lazy">
            </div>
            <div>
                <h3 class="tm-founder__name">Usman Zaheer</h3>
                <div class="tm-founder__role">Founder &amp; CEO — Urgent Rishta</div>
                <p class="tm-founder__desc">Urgent Rishta was created to provide individuals and families with a more professional, private and respectful way to search for a life partner.</p>
                <p class="tm-founder__desc">Our approach combines a carefully managed database with experienced human matchmakers, ensuring that every introduction is handled with discretion and personal attention.</p>
                <div class="tm-founder__actions">
                    <a href="{{ url('appointments') }}" class="tm-btn tm-btn--solid">Book a Private Consultation</a>
                    <ul class="tm-social">
                        <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://x.com/overseasrishta?s=09" target="_blank" rel="noopener"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- PAKISTAN TEAM -->
    <div class="tm-wrap" style="padding-top:0;">
        <div class="tm-team-label"><h2 class="tm-h2">Pakistan Team</h2></div>
        <div class="tm-grid">
            <div class="tm-card">
                <img src="/images/profiles/Qanita.jpeg" alt="Qanita Sundas" loading="lazy">
                <h4>Qanita Sundas</h4>
                <p>Co-Founder</p>
                <ul class="tm-social">
                    <li><a href="https://wa.me/923331623144" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                </ul>
            </div>
            <div class="tm-card">
                <img src="/images/profiles/minahil.jpeg" alt="Minahil Malik" loading="lazy">
                <h4>Minahil Malik</h4>
                <p>Relationship Manager</p>
                <ul class="tm-social">
                    <li><a href="https://wa.me/447445723296" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                </ul>
            </div>
            <div class="tm-card">
                <img src="/images/profiles/usman-idrees.jpg" alt="Usman Idrees" loading="lazy" class="tm-card-img--top">
                <h4>Usman Idrees</h4>
                <p>Client Coordinator</p>
                <ul class="tm-social">
                    <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                </ul>
            </div>
            <div class="tm-card">
                <img src="/images/profiles/niamat-ali.jpg" alt="Niamat Ali" loading="lazy" class="tm-card-img--top">
                <h4>Niamat Ali</h4>
                <p>Co-Founder &mdash; Director, Overseas Operations</p>
                <ul class="tm-social">
                    <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i></a></li>
                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- UK TEAM — group/event photos (individual profile cards pending named headshots) -->
    <div class="tm-wrap" style="padding-top:0;">
        <div class="tm-team-label"><h2 class="tm-h2">UK Team</h2></div>
        <p class="tm-team-note">Supporting overseas families across the United Kingdom with the same personal, private matchmaking approach.</p>
        <div class="tm-uk-gallery">
            <img src="/images/team/uk/uk-team-1.jpg" alt="Urgent Rishta UK team" loading="lazy">
            <img src="/images/team/uk/uk-team-2.jpg" alt="Urgent Rishta UK team" loading="lazy">
            <img src="/images/team/uk/uk-team-3.jpg" alt="Urgent Rishta UK team" loading="lazy">
            <img src="/images/team/uk/uk-team-4.jpg" alt="Urgent Rishta UK team" loading="lazy">
        </div>
    </div>

    <!-- FINAL CTA -->
    <div class="tm-wrap" style="padding-top:0;">
        <div class="tm-cta">
            <a href="{{ url('appointments') }}" class="tm-btn tm-btn--solid">Book a Private Consultation</a>
        </div>
    </div>
</div>
@endsection
