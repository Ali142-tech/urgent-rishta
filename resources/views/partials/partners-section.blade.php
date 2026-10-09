{{--
    "Our Partners" section component.
    Self-contained (markup + its own scoped CSS) so it can be @include'd on any page
    without depending on that page's own <style> block. Currently used on the
    photo-gallery page (gallery.blade.php). To add/remove a partner, edit the list
    below — no other file needs to change.

    Logos: each partner below has a `logo` key for an actual logo image path
    (public/images/partners/*). Leave `logo` as null to fall back to a styled text
    wordmark instead of a broken image.

    Deliberately a LIGHT section (not dark green) — it sits directly above the
    footer, which is also dark green; two dark blocks back to back with no
    border between them just blend into one undifferentiated slab. Light here
    gives a clean, visible handoff into the dark footer instead.
--}}
<style>
    .ur-partners {
        padding: 64px 24px 80px;
        background: #FBF7EF;
        position: relative;
    }
    .ur-partners__wrap {
        max-width: 1180px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 0.85fr 1.15fr;
        gap: 56px;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    .ur-partners__eyebrow {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #B5674A;
        margin-bottom: 14px;
    }
    .ur-partners__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(32px, 4.2vw, 44px);
        font-weight: 600;
        color: #1C2321;
        margin: 0 0 16px;
        line-height: 1.15;
    }
    .ur-partners__title em { color: #C9974D; font-style: italic; }
    .ur-partners__sub {
        font-size: 15px;
        line-height: 1.75;
        color: #5B6560;
        margin: 0 0 30px;
        max-width: 420px;
    }
    .ur-partners__divider {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }
    .ur-partners__divider span {
        flex: 0 0 40px;
        height: 0;
        border-top: 1.5px dotted rgba(201,151,77,.6);
    }
    .ur-partners__divider i { color: #C9974D; font-size: 8px; }
    .ur-partners__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 700;
        font-size: 14px;
        padding: 15px 32px;
        border-radius: 8px;
        background: #123A2E;
        color: #FBF7EF !important;
        text-decoration: none !important;
        transition: .2s ease;
        box-shadow: 0 12px 26px rgba(18,58,46,.18);
    }
    .ur-partners__cta:hover { background: #0F2E24; transform: translateY(-2px); }

    .ur-partners__grid {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }
    .ur-partner-card {
        position: relative;
        width: 210px;
        background: #fff;
        border: 1px solid #F0EADD;
        border-radius: 18px;
        padding: 32px 22px 24px;
        text-align: center;
        text-decoration: none !important;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15,46,36,.07);
        transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s ease, border-color .3s ease;
    }
    .ur-partner-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #C9974D, #E8C27A);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .35s ease;
    }
    .ur-partner-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 22px 44px rgba(15,46,36,.16);
        border-color: rgba(201,151,77,.35);
    }
    .ur-partner-card:hover::before { transform: scaleX(1); }
    .ur-partner-card__logo {
        width: 86px;
        height: 86px;
        margin: 4px auto 18px;
        border-radius: 50%;
        background: #FBF7EF;
        border: 1px solid #F0EADD;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        transition: border-color .3s ease;
    }
    .ur-partner-card:hover .ur-partner-card__logo { border-color: rgba(201,151,77,.5); }
    .ur-partner-card__logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        box-sizing: border-box;
        display: block;
    }
    .ur-partner-card__wordmark {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #123A2E 0%, #0F2E24 100%);
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 700;
        font-size: 26px;
        color: #C9974D;
    }
    .ur-partner-card__name {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600;
        font-size: 15px;
        color: #123A2E;
        line-height: 1.3;
        margin-bottom: 6px;
    }
    .ur-partner-card__tag {
        font-family: 'Manrope', system-ui, sans-serif;
        font-weight: 700;
        font-size: 10.5px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #B5674A;
        margin-bottom: 16px;
    }
    .ur-partner-card__link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'Manrope', system-ui, sans-serif;
        font-weight: 700;
        font-size: 11.5px;
        letter-spacing: .03em;
        color: #123A2E;
        padding-top: 14px;
        border-top: 1px dotted #E4D9C2;
        width: 100%;
        justify-content: center;
        transition: color .2s ease, gap .2s ease;
    }
    .ur-partner-card:hover .ur-partner-card__link {
        color: #C9974D;
        gap: 8px;
    }

    @media (max-width: 900px) {
        .ur-partners__wrap { grid-template-columns: 1fr; text-align: center; }
        .ur-partners__sub { max-width: none; margin-left: auto; margin-right: auto; }
        .ur-partners__divider { justify-content: center; }
        .ur-partners__grid { justify-content: center; }
    }
    @media (max-width: 560px) {
        .ur-partners { padding: 64px 16px 72px; }
        .ur-partner-card { width: 100%; max-width: 240px; }
    }
</style>

<section class="ur-partners" aria-label="Our partners">
    <div class="ur-partners__wrap">
        <div>
            <div class="ur-partners__eyebrow">Trusted Alongside Us</div>
            <h2 class="ur-partners__title">Our <em>Partners</em></h2>
            <div class="ur-partners__divider" aria-hidden="true"><span></span><i class="fa fa-diamond"></i><span></span></div>
            <p class="ur-partners__sub">Proud to work alongside trusted brands who share our commitment to quality and care.</p>
            <a href="{{ url('contact-us') }}" class="ur-partners__cta">Contact Our Team <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
        </div>

        <div class="ur-partners__grid">
            @php
                $urPartners = [
                    [
                        'name' => 'Lumina Skin & Laser Clinic',
                        'tag' => 'Skin & Laser Clinic',
                        'url' => 'https://www.instagram.com/lumina_clinic.pk',
                        'logo' => 'images/partners/lumina-clinic.jpg',
                    ],
                    [
                        'name' => "Mishi's Collection",
                        'tag' => 'Fashion & Apparel',
                        'url' => 'https://mishiscollection.com',
                        'logo' => 'images/partners/mishis-collection.webp',
                    ],
                    [
                        'name' => 'UK Security Hub',
                        'tag' => 'Security Services',
                        'url' => 'https://www.smartroutesystems.co.uk/',
                        'logo' => null,
                    ],
                ];
            @endphp
            @foreach ($urPartners as $urPartner)
            <a href="{{ $urPartner['url'] }}" target="_blank" rel="noopener" class="ur-partner-card">
                <div class="ur-partner-card__logo">
                    @if(!empty($urPartner['logo']))
                        <img src="{{ $urPartner['logo'] }}" alt="{{ $urPartner['name'] }}" loading="lazy">
                    @else
                        <span class="ur-partner-card__wordmark">{{ strtoupper(substr($urPartner['name'], 0, 1)) }}</span>
                    @endif
                </div>
                <div class="ur-partner-card__name">{{ $urPartner['name'] }}</div>
                <div class="ur-partner-card__tag">{{ $urPartner['tag'] }}</div>
                <span class="ur-partner-card__link">Visit &rarr;</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
