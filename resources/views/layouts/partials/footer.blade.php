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
        color: #B9C7BF;
    }
    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.08) !important;
    }
    .footer .heading { color: #fff !important; }
    .footer .footer-links > li > a { color: #B9C7BF !important; }
    .footer-bottom .copyright,
    .footer-bottom .copyright a { color: #B9C7BF !important; }
    .ur-footer-touch,
    .ur-footer-touch a { color: #B9C7BF !important; }
    .ur-footer-social {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 18px 0 0;
        padding: 0;
        list-style: none;
    }
    .ur-footer-social a {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1.5px solid rgba(153,111,42,0.65);
        background: rgba(153,111,42,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #A9793B;
        text-decoration: none;
        transition: background .2s ease, border-color .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .ur-footer-social a:hover,
    .ur-footer-social a:focus-visible {
        background: #A9793B;
        border-color: #A9793B;
        color: #0F2E24;
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(153,111,42,0.35);
    }
    .ur-footer-social__disabled {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: rgba(255,255,255,0.35);
        opacity: .55;
        cursor: default;
    }
</style>

<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-md-3 col-lg-3">
                    <div class="col">
                        <a class="navbar-brand" href="{{url('/')}}">
                            <img src="/images/header_logo2.png" class="img-responsive" width="100%">
                        </a>
                        <div class="text-center"><small></small></div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 d-none d-lg-block d-md-block">
                    <div class="col">
                        <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                            Main Menu</h4>
                        <ul class="footer-links">
                            <li>
                                <a href="{{url('/')}}" title="Home">
                                    Home</a>
                            </li>
                            <li>
                                <a href="{{url('packages')}}" title="Premium Plans">
                                    Premium Plans</a>
                            </li>
                            <li>
                                <a href="{{url('stories')}}" title="Success Stories">
                                    Success Stories</a>
                            </li>
                            <li>
                                <a href="{{url('contact-us')}}" title="Contact Us">
                                    Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- <div class="col-md-3 col-lg-3 d-none d-lg-block d-md-block">
                    <div class="col">
                        <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                            Quick Search</h4>
                        <ul class="footer-links">
                            <li>
                                <a href="/home/listing" title="All Members">
                                    All Members</a>
                            </li>
                            <li>
                                <a href="/home/listing/premium_members" title="Premium Members">
                                    Premium Members</a>
                            </li>
                            <li>
                                <a href="/home/listing/free_members" title="Free Members">
                                    Free Members</a>
                            </li>
                        </ul>
                    </div>
                </div> -->
                <div class="col-md-3 col-lg-3">
                    <div class="col">
                        <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                            Useful Links</h4>
                        <ul class="footer-links">
                            <li>
                                <a href="{{url('faqs')}}" title="FAQ">
                                    FAQ </a>
                            </li>
                            <li>
                                <a href="{{url('tandc')}}" title="Terms &amp; Conditions">
                                    Terms &amp; Conditions</a>
                            </li>
                            <li>
                                <a href="{{url('privacy')}}" title="Prvacy Policy">
                                    Privacy Policy</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="col">
                        <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                            Get in Touch</h4>
                        <div class="ur-footer-touch">
                            <a href="tel:+923040227000">+92 304 0227000</a><br>
                            <a href="mailto:urgentrishta.co@gmail.com">urgentrishta.co@gmail.com</a>
                        </div>
                        <ul class="ur-footer-social" aria-label="Follow Urgent Rishta">
                            <li><a href="https://www.facebook.com/share/1EqwQvEXJh/" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204?utm_source=share_via&amp;utm_content=profile&amp;utm_medium=member_android" target="_blank" rel="noopener" title="LinkedIn" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw==" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                            <li><span class="ur-footer-social__disabled" title="YouTube channel not yet available" aria-label="YouTube (coming soon)"><i class="fa fa-youtube"></i></span></li>
                            <li><span class="ur-footer-social__disabled" title="TikTok not yet available" aria-label="TikTok (coming soon)"><svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.4 2.2 1.9 3.9 4.5 4.1v3.1c-1.6 0-3.1-.5-4.4-1.4v6.7c0 3.4-2.8 6-6.1 6-3.4 0-6.1-2.7-6.1-6s2.8-6 6.1-6c.4 0 .8 0 1.2.1v3.2c-.4-.1-.8-.2-1.2-.2-1.6 0-2.9 1.3-2.9 2.9s1.3 2.9 2.9 2.9 3-1.3 3-3V3h3z"/></svg></span></li>
                            <li><a href="https://wa.me/923040227000" target="_blank" rel="noopener" title="WhatsApp" aria-label="WhatsApp"><i class="fa fa-whatsapp"></i></a></li>
                        </ul>
                    </div>
                </div>
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
