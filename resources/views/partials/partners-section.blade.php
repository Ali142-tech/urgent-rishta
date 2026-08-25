{{--
    "Our Partners" section component.
    Self-contained (markup + its own scoped CSS) so it can be @include'd on any page
    without depending on that page's own <style> block. Currently used on the
    homepage (welcome.blade.php). To add/remove a partner, edit the list below —
    no other file needs to change.

    Logos: each partner below has a `logo` key for an actual logo image path
    (public/images/partners/*). Leave `logo` as null to fall back to a styled text
    wordmark instead of a broken image.
--}}
<style>
    .ur-partners {
        padding: 80px 24px;
        background: linear-gradient(135deg, #123A2E 0%, #0F2E24 100%);
    }
    .ur-partners__wrap {
        max-width: 1180px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 0.85fr 1.15fr;
        gap: 56px;
        align-items: center;
    }
    .ur-partners__eyebrow {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(22px, 2.6vw, 28px);
        font-style: italic;
        font-weight: 400;
        color: #C9974D;
        margin: 0 0 2px;
    }
    .ur-partners__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(38px, 5vw, 52px);
        font-weight: 700;
        color: #fff;
        margin: 0 0 20px;
        line-height: 1.1;
    }
    .ur-partners__sub {
        font-size: 15px;
        line-height: 1.75;
        color: #B9C7BF;
        margin: 0 0 30px;
        max-width: 420px;
    }
    .ur-partners__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        padding: 14px 30px;
        border-radius: 999px;
        background: #C9974D;
        color: #123A2E !important;
        text-decoration: none !important;
        transition: transform .2s ease, filter .2s ease;
    }
    .ur-partners__cta:hover { transform: translateY(-2px); filter: brightness(1.06); }

    .ur-partners__grid {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }
    .ur-partner-card {
        position: relative;
        width: 210px;
        background: #fff;
        border-radius: 16px;
        padding: 30px 22px 26px;
        text-align: center;
        text-decoration: none !important;
        overflow: hidden;
        box-shadow: 0 14px 30px rgba(0,0,0,.18);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .ur-partner-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #C9974D, #E8C27A);
    }
    .ur-partner-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,.26);
    }
    .ur-partner-card__logo {
        width: 84px;
        height: 84px;
        margin: 4px auto 16px;
        border-radius: 50%;
        background: #FBF7EF;
        border: 1px solid #F0EADD;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .ur-partner-card__logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        box-sizing: border-box;
        display: block;
    }
    .ur-partner-card__wordmark {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 700;
        font-size: 22px;
        color: #123A2E;
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
        font-weight: 600;
        font-size: 10.5px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #B5674A;
        margin-bottom: 14px;
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
        padding-top: 12px;
        border-top: 1px solid #F0EADD;
        width: 100%;
        justify-content: center;
        transition: color .2s ease, gap .2s ease;
    }
    .ur-partner-card:hover .ur-partner-card__link {
        color: #B5674A;
        gap: 8px;
    }

    @media (max-width: 900px) {
        .ur-partners__wrap { grid-template-columns: 1fr; text-align: center; }
        .ur-partners__sub { max-width: none; margin-left: auto; margin-right: auto; }
        .ur-partners__grid { justify-content: center; }
    }
    @media (max-width: 560px) {
        .ur-partners { padding: 56px 16px; }
        .ur-partner-card { width: 100%; max-width: 240px; }
    }
</style>

<section class="ur-partners" aria-label="Our partners">
    <div class="ur-partners__wrap">
        <div>
            <div class="ur-partners__eyebrow">Trusted</div>
            <h2 class="ur-partners__title">Partners</h2>
            <p class="ur-partners__sub">Proud to work alongside trusted local brands who share our commitment to quality and care.</p>
            <a href="{{ url('contact-us') }}" class="ur-partners__cta">Contact Our Team</a>
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
