@extends('layouts.master')

@section('main-content')
{{-- Uses the site-wide Font Awesome 4 already loaded by layouts.master — do NOT add
     a second Font Awesome (e.g. v6) stylesheet here, it conflicts with the FA4 icon
     classes used across the rest of the site (footer social icons, etc.) and breaks them. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .cu-page {
        --cu-green: #123A2E;
        --cu-green-deep: #0F2E24;
        --cu-gold: #C9974D;
        --cu-cream: #FBF7EF;
        --cu-sand: #EFE7D6;
        --cu-line: #F0EADD;
        --cu-terracotta: #B5674A;
        --cu-text: #5B6560;
        --cu-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--cu-cream);
        color: var(--cu-ink);
    }
    .cu-page * { box-sizing: border-box; }
    .cu-page a { text-decoration: none; }
    .cu-wrap { max-width: 1180px; margin: 0 auto; padding: 0 24px; }

    /* Hero */
    .cu-hero { padding: 64px 0 60px; }
    .cu-hero__grid {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 48px;
        align-items: center;
    }
    .cu-eyebrow {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--cu-terracotta);
        margin-bottom: 14px;
    }
    .cu-h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600;
        font-size: clamp(32px, 4.2vw, 46px);
        line-height: 1.18;
        color: var(--cu-green-deep);
        margin: 0 0 18px;
    }
    .cu-h1 em { color: var(--cu-gold); font-style: italic; }
    .cu-lead {
        font-size: 16px;
        line-height: 1.75;
        color: var(--cu-text);
        max-width: 480px;
        margin: 0 0 30px;
    }
    .cu-hero__actions { display: flex; flex-wrap: wrap; gap: 14px; }
    .cu-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        font-weight: 700;
        font-size: 14.5px;
        padding: 15px 28px;
        border-radius: 8px;
        border: 1.5px solid transparent;
        transition: .2s ease;
        white-space: nowrap;
    }
    .cu-btn--solid { background: var(--cu-green); color: var(--cu-cream) !important; }
    .cu-btn--solid:hover { background: var(--cu-green-deep); }
    .cu-btn--outline { border-color: var(--cu-green); color: var(--cu-green) !important; background: transparent; }
    .cu-btn--outline:hover { background: var(--cu-green); color: var(--cu-cream) !important; }

    .cu-hero__photo { position: relative; }
    .cu-hero__photo img {
        width: 100%;
        height: 380px;
        object-fit: cover;
        object-position: center 20%;
        border-radius: 20px;
        display: block;
        box-shadow: 0 24px 48px rgba(15,46,36,.14);
    }
    .cu-hero__badge {
        position: absolute;
        left: 24px;
        bottom: -22px;
        background: var(--cu-green-deep);
        color: var(--cu-cream);
        padding: 16px 22px;
        border-radius: 12px;
        box-shadow: 0 14px 30px rgba(15,46,36,.25);
    }
    .cu-hero__badge strong {
        display: block;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px;
        color: var(--cu-gold);
        line-height: 1;
        margin-bottom: 4px;
    }
    .cu-hero__badge span { font-size: 11.5px; letter-spacing: .03em; opacity: .9; }

    /* Info cards */
    .cu-info { padding: 44px 0 8px; }
    .cu-info__grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .cu-info__card {
        background: #fff;
        border: 1px solid var(--cu-line);
        border-radius: 14px;
        padding: 26px 22px;
        box-shadow: 0 8px 22px rgba(15,46,36,.05);
    }
    .cu-info__icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--cu-sand);
        color: var(--cu-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        margin-bottom: 16px;
    }
    .cu-info__label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--cu-terracotta);
        margin-bottom: 8px;
    }
    .cu-info__value {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: var(--cu-ink) !important;
        margin-bottom: 6px;
        line-height: 1.4;
    }
    a.cu-info__value:hover { color: var(--cu-green) !important; }
    .cu-info__note { font-size: 12px; color: var(--cu-text); }

    /* Main: form + offices */
    .cu-main { padding: 56px 0 84px; }
    .cu-main__grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 32px;
        align-items: start;
    }

    .cu-form-card {
        background: #fff;
        border: 1px solid var(--cu-line);
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 12px 30px rgba(15,46,36,.06);
    }
    .cu-form-card__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 24px;
        font-weight: 600;
        color: var(--cu-green-deep);
        margin: 0 0 8px;
    }
    .cu-form-card__sub { font-size: 14px; color: var(--cu-text); margin: 0 0 28px; }
    .cu-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .cu-field { display: flex; flex-direction: column; }
    .cu-field label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        color: var(--cu-ink);
        margin-bottom: 8px;
    }
    .cu-field input,
    .cu-field select,
    .cu-field textarea {
        width: 100%;
        border: 1.5px solid var(--cu-line);
        background: var(--cu-cream);
        border-radius: 9px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: inherit;
        color: var(--cu-ink);
        transition: border-color .2s ease;
    }
    .cu-field input:focus,
    .cu-field select:focus,
    .cu-field textarea:focus {
        outline: none;
        border-color: var(--cu-gold);
        background: #fff;
    }
    .cu-field textarea { resize: vertical; min-height: 120px; }
    .cu-field--full { grid-column: 1 / -1; }
    .cu-form-submit {
        width: 100%;
        justify-content: center;
        margin-top: 4px;
    }

    .cu-offices {
        background: var(--cu-green-deep);
        color: var(--cu-cream);
        border-radius: 18px;
        padding: 34px;
    }
    .cu-offices__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 22px;
        color: #fff;
    }
    .cu-office__label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: var(--cu-gold);
        margin-bottom: 8px;
    }
    .cu-office__text { font-size: 14px; line-height: 1.6; color: var(--cu-cream); margin: 0; }
    .cu-office-divider { border-top: 1px dotted rgba(201,151,77,0.4); margin: 22px 0; }

    .cu-follow {
        background: #fff;
        border: 1px solid var(--cu-line);
        border-radius: 18px;
        padding: 28px 34px;
        margin-top: 24px;
    }
    .cu-follow__label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: var(--cu-terracotta);
        margin-bottom: 14px;
    }
    .cu-follow__social { display: flex; gap: 10px; list-style: none; margin: 0; padding: 0; }
    .cu-follow__social a {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(18,58,46,0.18);
        color: var(--cu-green);
        font-size: 14px;
        transition: .2s ease;
    }
    .cu-follow__social a:hover {
        background: var(--cu-green);
        border-color: var(--cu-green);
        color: #fff !important;
        transform: translateY(-2px);
    }

    .cu-response-note {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--cu-sand);
        color: var(--cu-green-deep);
        font-size: 13px;
        font-weight: 600;
        padding: 14px 18px;
        border-radius: 12px;
        margin-top: 20px;
    }
    .cu-response-note i { color: var(--cu-terracotta); }

    @media (max-width: 991px) {
        .cu-hero__grid { grid-template-columns: 1fr; }
        .cu-hero__content { order: 2; text-align: center; }
        .cu-lead { margin-left: auto; margin-right: auto; }
        .cu-hero__actions { justify-content: center; }
        .cu-hero__photo { order: 1; margin-bottom: 30px; }
        .cu-info__grid { grid-template-columns: 1fr 1fr; }
        .cu-main__grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 575px) {
        .cu-info__grid { grid-template-columns: 1fr; }
        .cu-form-row { grid-template-columns: 1fr; }
        .cu-form-card { padding: 28px 22px; }
        .cu-offices { padding: 26px 22px; }
        .cu-hero__badge { position: static; margin-top: -40px; margin-left: 24px; display: inline-block; }
    }
</style>

<div class="cu-page">
    <section class="cu-hero">
        <div class="cu-wrap cu-hero__grid">
            <div class="cu-hero__content">
                <div class="cu-eyebrow">Get In Touch</div>
                <h1 class="cu-h1">We're Here to Help You Say <em>Yes</em></h1>
                <p class="cu-lead">Questions about a profile, a package, or how our matchmaking process works? Our team responds personally — usually within a few hours.</p>
                <div class="cu-hero__actions">
                    <a href="#contact-form" class="cu-btn cu-btn--solid"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send a Message</a>
                    <a href="tel:+923040227000" class="cu-btn cu-btn--outline"><i class="fa fa-phone" aria-hidden="true"></i> Call Us Now</a>
                </div>
            </div>
            <div class="cu-hero__photo">
                <img src="/images/about/elegant-couple.jpg" alt="A happy couple matched through Urgent Rishta" loading="lazy">
                <div class="cu-hero__badge"><strong>5,000+</strong><span class="text-white">Successful Matches</span></div>
            </div>
        </div>
    </section>

    <section class="cu-info">
        <div class="cu-wrap cu-info__grid">
            <div class="cu-info__card">
                <div class="cu-info__icon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                <div class="cu-info__label">Call Us</div>
                <a href="tel:+923040227000" class="cu-info__value">+92 304 0227000</a>
                <div class="cu-info__note">Standard rates apply</div>
            </div>
            <div class="cu-info__card">
                <div class="cu-info__icon"><i class="fa fa-whatsapp" aria-hidden="true"></i></div>
                <div class="cu-info__label">WhatsApp</div>
                <a href="https://wa.me/923040227000" target="_blank" rel="noopener" class="cu-info__value">+92 304 0227000</a>
                <div class="cu-info__note">Fastest response</div>
            </div>
            <div class="cu-info__card">
                <div class="cu-info__icon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                <div class="cu-info__label">Email</div>
                <a href="mailto:urgentrishta.co@gmail.com" class="cu-info__value">urgentrishta.co@gmail.com</a>
                <div class="cu-info__note">Replies within a day</div>
            </div>
            <div class="cu-info__card">
                <div class="cu-info__icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                <div class="cu-info__label">Visit Us</div>
                <div class="cu-info__value">Lahore &amp; Manchester</div>
                <div class="cu-info__note">By appointment</div>
            </div>
        </div>
    </section>

    <section class="cu-main">
        <div class="cu-wrap cu-main__grid">
            <div class="cu-form-card" id="contact-form">
                <h2 class="cu-form-card__title">Send Us a Message</h2>
                <p class="cu-form-card__sub">Fill in your details below and our team will reach out shortly.</p>

                <form class="form-default" role="form" method="POST" action="{{ url('contact-us') }}">
                    @csrf
                    <div class="cu-form-row">
                        <div class="cu-field">
                            <label for="cu-name">Full Name</label>
                            <input type="text" id="cu-name" name="name" placeholder="Your name" required>
                        </div>
                        <div class="cu-field">
                            <label for="cu-phone">Phone / WhatsApp</label>
                            <input type="text" id="cu-phone" name="phone" placeholder="03xx-xxxxxxx">
                        </div>
                    </div>
                    <div class="cu-form-row">
                        <div class="cu-field">
                            <label for="cu-email">Email</label>
                            <input type="email" id="cu-email" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="cu-field">
                            <label for="cu-city">City</label>
                            <input type="text" id="cu-city" name="city" placeholder="Lahore, Karachi...">
                        </div>
                    </div>
                    <div class="cu-form-row">
                        <div class="cu-field cu-field--full">
                            <label for="cu-subject">I'm Interested In</label>
                            <select id="cu-subject" name="subject">
                                <option>General Inquiry</option>
                                <option>Premium Packages</option>
                                <option>Digital Match (Online)</option>
                                <option>Personal Match (Offline)</option>
                                <option>Existing Profile Support</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="cu-form-row">
                        <div class="cu-field cu-field--full">
                            <label for="cu-message">Your Message <small style="font-weight:500;color:var(--cu-text);">(Max 300 characters)</small></label>
                            <textarea id="cu-message" name="message" rows="5" maxlength="300" placeholder="Tell us how we can help..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="cu-btn cu-btn--solid cu-form-submit">Send Message</button>
                </form>
            </div>

            <div>
                <div class="cu-offices">
                    <h3 class="cu-offices__title">Our Offices</h3>
                    <div class="cu-office__label">Pakistan Office</div>
                    <p class="cu-office__text">114 A/B Block, River View Housing Society, Near Abdul Sattar Edhi Road, Lahore, Pakistan</p>

                    <div class="cu-office-divider"></div>

                    <div class="cu-office__label">UK Office</div>
                    <p class="cu-office__text">Universal Square, Devonshire St N, Manchester M12 6JH, UK</p>

                    <div class="cu-office-divider"></div>

                    <div class="cu-office__label">Office Hours</div>
                    <p class="cu-office__text">Monday – Saturday, 10:00 AM – 7:00 PM (PKT)</p>
                </div>

                <div class="cu-follow">
                    <div class="cu-follow__label">Follow Along</div>
                    <ul class="cu-follow__social">
                        <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener" title="LinkedIn" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener" title="WhatsApp" aria-label="WhatsApp"><i class="fa fa-whatsapp"></i></a></li>
                    </ul>
                </div>

                <div class="cu-response-note"><i class="fa fa-clock-o" aria-hidden="true"></i> We typically respond within 2–4 hours</div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-success').fadeOut('fast');
        }, 5000);
    });
</script>
@endsection
