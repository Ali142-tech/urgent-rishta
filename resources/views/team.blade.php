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
    .tm-founder__photo img { width: 100%; height: 380px; object-fit: cover; display: block; }
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

    .tm-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 8px; }
    .tm-card { text-align: center; }
    .tm-card img { width: 100%; height: 260px; object-fit: cover; border-radius: 10px; display: block; margin-bottom: 16px; box-shadow: 0 10px 24px rgba(15,46,36,.08); }
    .tm-card h4 { font-family: 'Playfair Display', Georgia, serif; font-weight: 600; font-size: 17px; margin: 0 0 4px; color: var(--tm-ink); }
    .tm-card p { font-size: 12.5px; color: var(--tm-text); margin: 0 0 10px; }

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
                <img src="/images/profiles/9.jpg" alt="Usman Idrees" loading="lazy">
                <h4>Usman Idrees</h4>
                <p>Client Coordinator</p>
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
        <p class="tm-team-note">Supporting Pakistani and Muslim families across the United Kingdom with the same personal, private matchmaking approach.</p>
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
