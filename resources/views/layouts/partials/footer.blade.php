{{--
    Shared site footer component.
    Included once from layouts/master.blade.php so every page renders the exact
    same footer. Do NOT copy/paste this markup into individual pages or override
    its styling with page-scoped CSS (e.g. body.page-xyz .footer {...}) — that is
    what caused the footer to look different across pages before this was split
    out into its own component. If the footer needs to change, edit this file only.
--}}
<style>
    /* Deep green brand footer (matches the premium rebrand — packages page dark
       sections, homepage dark CTA) instead of the old theme's plain black default. */
    .footer,
    .footer-top,
    .footer-bottom {
        background: #0F2E24 !important;
        color: #fff;
    }
    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.08) !important;
    }
    .footer-bottom .copyright,
    .footer-bottom .copyright a { color: #fff !important; }

    /* ---------- Layout grid ---------- */
    /* text-align:left overrides the theme's ".footer { text-align:center }" mobile
       rule (custom-style.css, max-width:767px) — without this, headings/labels
       (block-level text) get centered while the flex-based icon rows/dividers stay
       left-aligned, producing a mismatched look on small screens. */
    .ur-footer-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr 1fr 1.5fr;
        gap: 36px;
        padding: 56px 0 36px;
        text-align: left;
    }
    .ur-footer-col { position: relative; }
    .ur-footer-col--bordered {
        border-left: 1px dotted rgba(201,151,77,0.35);
        padding-left: 36px;
    }

    /* ---------- Brand column ---------- */
    .ur-footer-brand .navbar-brand { display: inline-block; width: 150px; margin-bottom: 18px; }
    .ur-footer-brand .navbar-brand img { width: 100%; }
    .ur-footer-tagline {
        color: #fff;
        font-family: 'Playfair Display', Georgia, serif;
        font-style: italic;
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 12px;
    }
    .ur-footer-desc {
        color: #fff;
        font-size: 13.5px;
        line-height: 1.75;
        margin: 0 0 20px;
        max-width: 260px;
    }
    .ur-footer-heart-divider {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ur-footer-heart-divider span {
        flex: 0 0 46px;
        height: 0;
        border-top: 1px dotted rgba(201,151,77,0.55);
    }
    .ur-footer-heart-divider i { color: #C9974D; font-size: 12px; }

    /* ---------- Headings + decorative underline ---------- */
    .ur-footer-heading {
        color: #fff !important;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin: 0 0 10px;
    }
    .ur-footer-underline {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 24px;
    }
    .ur-footer-underline span {
        flex: 0 0 34px;
        height: 0;
        border-top: 1px dotted rgba(201,151,77,0.55);
    }
    .ur-footer-underline i { color: #C9974D; font-size: 6px; }

    /* ---------- Icon lists (Main Menu / Useful Links) ---------- */
    .ur-footer-iconlist { list-style: none; margin: 0; padding: 0; }
    .ur-footer-iconlist li { margin-bottom: 16px; }
    .ur-footer-iconlist li:last-child { margin-bottom: 0; }
    .ur-footer-iconlist a {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: color .2s ease, transform .2s ease;
    }
    .ur-footer-iconlist a:hover { color: #C9974D; transform: translateX(3px); }
    .ur-footer-iconlist a i {
        color: #C9974D;
        font-size: 13px;
        width: 16px;
        text-align: center;
        flex-shrink: 0;
    }

    /* ---------- Get in Touch ---------- */
    .ur-footer-office__label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #fff;
        margin-bottom: 8px;
    }
    .ur-footer-office-divider {
        border-top: 1px dotted rgba(201,151,77,0.4);
        margin: 18px 0;
    }
    .ur-footer-contact { display: flex; flex-direction: column; }
    .ur-footer-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        line-height: 1.6;
        color: #fff !important;
        font-weight: 600;
        margin-bottom: 14px;
    }
    .ur-footer-row:last-child { margin-bottom: 0; }
    .ur-footer-row i {
        margin-top: 3px;
        color: #C9974D;
        font-size: 12px;
        width: 14px;
        text-align: center;
        flex-shrink: 0;
    }
    a.ur-footer-row:hover { color: #C9974D !important; }
    .ur-footer-contact-extra { margin-top: 18px; display: flex; flex-direction: column; }

    /* ---------- Social row (centered, flanked by decorative lines) ---------- */
    .ur-footer-social-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 8px 0 44px;
    }
    .ur-footer-social-line {
        flex: 0 1 90px;
        height: 0;
        border-top: 1px dotted rgba(201,151,77,0.55);
    }
    .ur-footer-social {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .ur-footer-social a,
    .ur-footer-social__disabled {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .ur-footer-social a {
        border: 1.5px solid rgba(153,111,42,0.65);
        background: rgba(153,111,42,0.18);
        color: #C9974D;
        text-decoration: none;
        transition: background .2s ease, border-color .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .ur-footer-social a:hover,
    .ur-footer-social a:focus-visible {
        background: #C9974D;
        border-color: #C9974D;
        color: #0F2E24;
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(153,111,42,0.35);
    }
    .ur-footer-social__disabled {
        border: 1.5px solid rgba(255,255,255,0.16);
        color: rgba(255,255,255,0.35);
        opacity: .55;
        cursor: default;
    }

    @media (max-width: 991px) {
        .ur-footer-grid { grid-template-columns: 1fr 1fr; row-gap: 40px; }
        .ur-footer-col--bordered { border-left: 0; padding-left: 0; }
    }
    @media (max-width: 575px) {
        .ur-footer-grid { grid-template-columns: 1fr; text-align: center; }
        .ur-footer-desc { max-width: 320px; margin-left: auto; margin-right: auto; }
        .ur-footer-social-line { flex-basis: 40px; }
        /* Center the flex rows themselves (they ignore text-align since they're
           block-level flex containers) so icon + label read as one centered unit. */
        .ur-footer-underline,
        .ur-footer-heart-divider,
        .ur-footer-iconlist a,
        .ur-footer-row {
            justify-content: center;
        }
    }
</style>

<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="ur-footer-grid">
                <div class="ur-footer-col ur-footer-brand">
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img src="/images/header_logo2.png" class="img-responsive" width="100%">
                    </a>
                    <p class="ur-footer-tagline">Bringing Hearts Together</p>
                    <p class="ur-footer-desc">We believe in meaningful connections built on trust, respect, and understanding.</p>
                    <div class="ur-footer-heart-divider" aria-hidden="true"><span></span><i class="fa fa-heart"></i><span></span></div>
                </div>

                <div class="ur-footer-col">
                    <h4 class="ur-footer-heading">Main Menu</h4>
                    <div class="ur-footer-underline" aria-hidden="true"><span></span><i class="fa fa-diamond"></i><span></span></div>
                    <ul class="ur-footer-iconlist">
                        <li><a href="{{url('/')}}" title="Home"><i class="fa fa-home" aria-hidden="true"></i><span class="text-white">Home</span></a></li>
                        <li><a href="{{url('packages')}}" title="Premium Plans"><i class="fa fa-diamond" aria-hidden="true"></i><span class="text-white">Premium Plans</span></a></li>
                        <li><a href="{{url('team')}}" title="Our Team"><i class="fa fa-users" aria-hidden="true"></i><span class="text-white">Our Team</span></a></li>
                        <li><a href="{{url('stories')}}" title="Success Stories"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="text-white">Success Stories</span></a></li>
                        <li><a href="{{url('contact-us')}}" title="Contact Us"><i class="fa fa-phone" aria-hidden="true"></i><span class="text-white">Contact Us</span></a></li>
                    </ul>
                </div>

                <div class="ur-footer-col ur-footer-col--bordered">
                    <h4 class="ur-footer-heading">Useful Links</h4>
                    <div class="ur-footer-underline" aria-hidden="true"><span></span><i class="fa fa-diamond"></i><span></span></div>
                    <ul class="ur-footer-iconlist">
                        <li><a href="{{url('faqs')}}" title="FAQ"><i class="fa fa-question-circle" aria-hidden="true"></i><span class="text-white">FAQ</span></a></li>
                        <li><a href="{{url('tandc')}}" title="Terms &amp; Conditions"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="text-white">Terms &amp; Conditions</span></a></li>
                        <li><a href="{{url('privacy')}}" title="Privacy Policy"><i class="fa fa-shield" aria-hidden="true"></i><span class="text-white">Privacy Policy</span></a></li>
                    </ul>
                </div>

                <div class="ur-footer-col ur-footer-col--bordered">
                    <h4 class="ur-footer-heading">Get in Touch</h4>
                    <div class="ur-footer-underline" aria-hidden="true"><span></span><i class="fa fa-diamond"></i><span></span></div>
                    <div class="ur-footer-contact">
                        <div class="ur-footer-office__label">UK Office</div>
                        <a class="ur-footer-row" href="https://maps.google.com/?q=Universal+Square,+Devonshire+St+N,+Manchester+M12+6JH" target="_blank" rel="noopener">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                            <span class="text-white">Universal Square, Devonshire St N, Manchester M12 6JH</span>
                        </a>

                        <div class="ur-footer-office-divider"></div>

                        <div class="ur-footer-office__label">Pakistan Office</div>
                        <a class="ur-footer-row" href="https://maps.google.com/?q=114+A+B+Block+River+View+Housing+Society+Near+Abdul+Sattar+Edhi+Road+Lahore" target="_blank" rel="noopener">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                            <span class="text-white">114 A B Block River View Housing Society, Near Abdul Sattar Edhi Road, Lahore</span>
                        </a>

                        <div class="ur-footer-contact-extra">
                            <a class="ur-footer-row" href="tel:+923040227000">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                                <span class="text-white">+92 304 0227000</span>
                            </a>
                            <a class="ur-footer-row" href="mailto:urgentrishta.co@gmail.com">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <span class="text-white">urgentrishta.co@gmail.com</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ur-footer-social-row">
                <span class="ur-footer-social-line" aria-hidden="true"></span>
                <ul class="ur-footer-social" aria-label="Follow Urgent Rishta">
                    <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener" title="LinkedIn" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                    <li><span class="ur-footer-social__disabled" title="YouTube channel not yet available" aria-label="YouTube (coming soon)"><i class="fa fa-youtube"></i></span></li>
                    <li><span class="ur-footer-social__disabled" title="TikTok not yet available" aria-label="TikTok (coming soon)"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.4 2.2 1.9 3.9 4.5 4.1v3.1c-1.6 0-3.1-.5-4.4-1.4v6.7c0 3.4-2.8 6-6.1 6-3.4 0-6.1-2.7-6.1-6s2.8-6 6.1-6c.4 0 .8 0 1.2.1v3.2c-.4-.1-.8-.2-1.2-.2-1.6 0-2.9 1.3-2.9 2.9s1.3 2.9 2.9 2.9 3-1.3 3-3V3h3z"/></svg></span></li>
                    <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener" title="WhatsApp" aria-label="WhatsApp"><i class="fa fa-whatsapp"></i></a></li>
                </ul>
                <span class="ur-footer-social-line" aria-hidden="true"></span>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-1">
        <div class="container">
            <div class="row row-cols-xs-spaced flex flex-items-xs-middle">
                <div class="col col-md-7">
                    <div class="copyright text-center text-sm-left mt-2">
                        © {{ date('Y') }} <a href="{{url('/')}}" class="c-base-1" target="_blank" title="Urgent Rishta - Official Website">
                            <strong class="strong-400">Urgent Rishta (Pvt.) Ltd.</strong>
                        </a> All Rights Reserved. </div>
                </div>
            </div>
        </div>
    </div>
</footer>
