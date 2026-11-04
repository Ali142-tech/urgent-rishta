{{--
    Partners page (/partners) — "UR Privilege": hero, category filter, a card per partner and a "Partner With Urgent Rishta"
    banner. The partners and their text come from config/partners.php; every "Visit Website" button goes through /go/{partner},
    which records the click and redirects with the UrgentRishta referral tags (PartnerRedirectController). Header and footer are
    the shared site layout.
--}}
@extends('layouts.master')
@section('main-content')
@php
    $ppPartners = collect(\App\Services\PartnerLinkService::all());
    $ppCategories = $ppPartners->pluck('category')->filter()->unique()->values();
    $ppIcons = ['Fashion & Clothing' => 'fa-diamond', 'Health & Beauty' => 'fa-heartbeat', 'Technology & Services' => 'fa-cogs', 'Wedding & Groom Wear' => 'fa-black-tie', 'Umrah & Travel' => 'fa-plane'];
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .pp { --pp-green: #0F2E24; --pp-green-2: #123A2E; --pp-gold: #C9974D; --pp-gold-2: #E3B369; --pp-cream: #FBF7EF; --pp-ink: #1C2321; --pp-text: #5B6560;
        font-family: 'Manrope', system-ui, sans-serif; color: var(--pp-ink); background: #fff; }
    .pp * { box-sizing: border-box; }
    .pp a { text-decoration: none; }

    /* ---- hero ---- */
    .pp-hero { position: relative; overflow: hidden; color: #fff; padding: 64px 24px 58px;
        background: radial-gradient(900px 420px at 18% 0%, rgba(201,151,77,.20), rgba(201,151,77,0) 60%), linear-gradient(135deg, #0A2219 0%, #123A2E 55%, #0F2E24 100%); }
    .pp-hero::after { content: ''; position: absolute; inset: 0; pointer-events: none; opacity: .5;
        background-image: radial-gradient(rgba(239,227,200,.10) 1px, transparent 1.4px); background-size: 26px 26px; }
    .pp-hero__inner { position: relative; z-index: 1; max-width: 1240px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(0, .75fr); gap: 40px; align-items: center; }
    .pp-eyebrow { display: block; font-size: 13px; font-weight: 800; letter-spacing: .42em; text-transform: uppercase; color: var(--pp-gold); margin-bottom: 14px; }
    .pp-hero h1 { font-family: 'Playfair Display', serif; font-size: 62px; font-weight: 700; line-height: 1.08; margin: 0 0 16px; color: #F3E3BF; }
    .pp-hero p.pp-sub { font-family: 'Playfair Display', serif; font-size: 22px; line-height: 1.5; color: #EFE3C8; margin: 0 0 30px; max-width: 640px; }
    .pp-perks { display: flex; flex-wrap: wrap; gap: 22px 34px; }
    .pp-perk { display: flex; align-items: center; gap: 12px; font-size: 14.5px; line-height: 1.35; color: #F3E3BF; font-weight: 600; }
    .pp .pp-perk span { color: #F3E3BF !important; opacity: 1 !important; }
    .pp-perk i { font-size: 30px; color: var(--pp-gold-2); width: 34px; text-align: center; }
    .pp-arch { position: relative; justify-self: end; width: min(100%, 360px); aspect-ratio: 3 / 3.6; border-radius: 180px 180px 18px 18px; padding: 9px; background: linear-gradient(160deg, #E8C98A, #B07C3D 60%, #E8C98A); box-shadow: 0 26px 60px rgba(0,0,0,.5); }
    .pp-arch img { width: 100%; height: 100%; object-fit: cover; object-position: center 22%; border-radius: 172px 172px 12px 12px; display: block; }

    /* ---- category chips ---- */
    .pp-wrap { max-width: 1240px; margin: 0 auto; padding: 0 24px; }
    .pp-chips { display: flex; gap: 14px; flex-wrap: wrap; padding: 26px 0 8px; }
    .pp-chip { display: inline-flex; align-items: center; gap: 10px; padding: 12px 22px; border-radius: 999px; border: 1px solid #ECE5D5; background: #F7F2E8; color: var(--pp-ink); font-size: 14.5px; font-weight: 600; cursor: pointer; transition: all .2s ease; font-family: inherit; }
    .pp-chip i { font-size: 16px; color: var(--pp-gold); }
    .pp-chip:hover { border-color: var(--pp-gold); }
    .pp-chip.is-active { background: var(--pp-green-2); border-color: var(--pp-green-2); color: #fff; box-shadow: 0 6px 16px rgba(15,46,36,.25); }
    .pp-chip.is-active i { color: #fff; }

    /* ---- featured partners ---- */
    .pp-featured { padding: 22px 0 60px; }
    .pp-featured__head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }
    .pp-featured__head h2 { font-family: 'Playfair Display', serif; font-size: 40px; font-weight: 700; margin: 0; color: var(--pp-ink); }
    .pp-featured__all { font-size: 15px; font-weight: 700; color: var(--pp-ink); cursor: pointer; background: none; border: 0; padding: 0; font-family: inherit; }
    .pp-featured__all i { margin-left: 8px; }
    .pp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(262px, 320px)); gap: 26px; justify-content: center; }
    .pp-card { display: flex; flex-direction: column; background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #F0EADD; box-shadow: 0 10px 30px rgba(15,46,36,.10); transition: transform .25s ease, box-shadow .25s ease; }
    .pp-card:hover { transform: translateY(-5px); box-shadow: 0 18px 42px rgba(15,46,36,.16); }
    .pp-card[hidden] { display: none; }
    .pp-cover { position: relative; height: 215px; padding: 26px 24px; color: #fff; overflow: hidden; }
    .pp-cover::before { content: ''; position: absolute; inset: 0; opacity: .5; background-image: radial-gradient(rgba(255,255,255,.18) 1px, transparent 1.4px); background-size: 20px 20px; }
    .pp-cover::after { content: ''; position: absolute; width: 230px; height: 230px; right: -70px; top: -80px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.22), rgba(255,255,255,0) 70%); }
    .pp-cover--rose { background: linear-gradient(145deg, #7A2E45, #B5566F 60%, #D98BA0); }
    .pp-cover--gold { background: linear-gradient(145deg, #6B4A1C, #B07C3D 60%, #E3B369); }
    .pp-cover--blue { background: linear-gradient(145deg, #0B2A63, #1D5BD6 62%, #5C8FF0); }
    .pp-cover b { position: relative; z-index: 1; display: block; font-family: 'Playfair Display', serif; font-size: 25px; line-height: 1.15; font-weight: 700; margin-bottom: 8px; text-shadow: 0 2px 12px rgba(0,0,0,.25); }
    .pp-cover span { position: relative; z-index: 1; display: block; font-size: 14px; line-height: 1.5; max-width: 160px; color: rgba(255,255,255,.92); }
    .pp-logo { position: relative; z-index: 2; width: 104px; height: 104px; border-radius: 50%; background: #fff; border: 4px solid #fff; box-shadow: 0 8px 22px rgba(0,0,0,.2); margin: -52px auto 0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .pp-logo img { width: 100%; height: 100%; object-fit: contain; padding: 8px; display: block; }
    .pp-logo--cover img { object-fit: cover; padding: 0; }
    .pp-logo__initial { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 40px; color: var(--pp-gold-2); }
    .pp-logo--dark { background: var(--pp-green-2); }
    .pp-body { display: flex; flex-direction: column; flex: 1; padding: 14px 20px 22px; text-align: center; }
    .pp-name { font-family: 'Playfair Display', serif; font-size: 23px; font-weight: 700; color: var(--pp-ink); margin: 0 0 8px; }
    .pp-pill { align-self: center; display: inline-block; background: #F6E8C9; color: #8A6218; font-size: 12.5px; font-weight: 700; padding: 5px 16px; border-radius: 6px; margin-bottom: 12px; }
    .pp-desc { font-size: 14.5px; line-height: 1.6; color: var(--pp-text); margin: 0 0 14px; flex: 1; }
    .pp-benefit { display: flex; align-items: center; gap: 12px; text-align: left; border-radius: 10px; padding: 11px 14px; margin-bottom: 14px; }
    .pp-benefit i { font-size: 26px; flex-shrink: 0; }
    .pp-benefit small { display: block; font-size: 12px; color: #6B7570; line-height: 1.3; }
    .pp-benefit b { display: block; font-size: 15.5px; font-weight: 700; line-height: 1.3; }
    .pp-benefit--rose { background: #FDE9EE; } .pp-benefit--rose i, .pp-benefit--rose b { color: #B5324F; }
    .pp-benefit--gold { background: #FBF0DA; } .pp-benefit--gold i, .pp-benefit--gold b { color: #A8731F; }
    .pp-benefit--blue { background: #E8EFFD; } .pp-benefit--blue i, .pp-benefit--blue b { color: #1D4FB5; }
    .pp-visit { display: flex; align-items: center; justify-content: center; gap: 10px; height: 48px; border-radius: 10px; background: linear-gradient(180deg, #143F31, #0F2E24); color: #fff !important; font-size: 16px; font-weight: 700; transition: filter .2s; margin-top: auto; }
    .pp-visit:hover { filter: brightness(1.2); }
    .pp-empty { display: none; padding: 40px 0; text-align: center; color: var(--pp-text); }

    /* ---- Partner with us ---- */
    .pp-join { position: relative; overflow: hidden; color: #fff; padding: 46px 24px;
        background: radial-gradient(700px 300px at 85% 120%, rgba(201,151,77,.22), rgba(201,151,77,0) 60%), linear-gradient(135deg, #0B241C 0%, #123A2E 60%, #0F2E24 100%); }
    .pp-join__inner { max-width: 1240px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); gap: 36px; align-items: center; }
    .pp-join h2 { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 700; color: #F3E3BF; margin: 0 0 10px; }
    .pp-join p { font-size: 17px; line-height: 1.6; color: #EFE3C8; margin: 0 0 22px; max-width: 520px; }
    .pp-join__btn { display: inline-flex; align-items: center; gap: 12px; height: 52px; padding: 0 30px; border-radius: 8px; background: linear-gradient(180deg, #F0C98A, #D9A85A); color: #0F2E24 !important; font-size: 17px; font-weight: 800; box-shadow: 0 8px 20px rgba(0,0,0,.3); transition: transform .2s; }
    .pp-join__btn:hover { transform: translateY(-2px); }
    .pp-join__points { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .pp-join__point { text-align: center; padding: 6px 12px; border-left: 1px solid rgba(239,227,200,.22); }
    .pp-join__point:first-child { border-left: 0; }
    .pp-join__point i { font-size: 38px; color: var(--pp-gold-2); display: block; margin-bottom: 12px; }
    .pp-join__point span { display: block; font-size: 15.5px; line-height: 1.4; color: #EFE3C8; }

    @media (max-width: 1000px) {
        .pp-hero__inner { grid-template-columns: 1fr; }
        .pp-arch { display: none; }
        .pp-hero h1 { font-size: 46px; }
        .pp-join__inner { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .pp-hero { padding: 44px 18px 40px; }
        .pp-hero h1 { font-size: 34px; }
        .pp-hero p.pp-sub { font-size: 17px; margin-bottom: 22px; }
        .pp-eyebrow { letter-spacing: .3em; font-size: 12px; }
        .pp-perks { gap: 16px 22px; }
        .pp-perk { font-size: 13.5px; } .pp-perk i { font-size: 24px; width: 28px; }
        .pp-wrap { padding: 0 16px; }
        .pp-chips { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 12px; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .pp-chips::-webkit-scrollbar { display: none; }
        .pp-chip { flex-shrink: 0; padding: 10px 16px; font-size: 13.5px; }
        .pp-featured__head h2 { font-size: 28px; }
        .pp-grid { grid-template-columns: 1fr; }
        .pp-join { padding: 38px 18px; }
        .pp-join h2 { font-size: 27px; }
        .pp-join__points { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px 0; }
        .pp-join__point:nth-child(3) { border-left: 0; }
    }
</style>

<div class="pp">
    <section class="pp-hero">
        <div class="pp-hero__inner">
            <div>
                <span class="pp-eyebrow">UR Privilege</span>
                <h1>Our Trusted Partners</h1>
                <p class="pp-sub">Exclusive services &amp; benefits for the Urgent Rishta community.</p>
                <div class="pp-perks">
                    <div class="pp-perk"><i class="fa fa-diamond"></i><span>Verified<br>Brands</span></div>
                    <div class="pp-perk"><i class="fa fa-shield"></i><span>Exclusive<br>Member Benefits</span></div>
                    <div class="pp-perk"><i class="fa fa-gift"></i><span>Special<br>Discounts</span></div>
                    <div class="pp-perk"><i class="fa fa-handshake-o"></i><span>Trusted for<br>Your New Journey</span></div>
                </div>
            </div>
            <div class="pp-arch"><img src="/images/about/elegant-couple.jpg" alt="" loading="eager"></div>
        </div>
    </section>

    <div class="pp-wrap">
        <div class="pp-chips" id="pp_chips" role="tablist" aria-label="Partner categories">
            <button type="button" class="pp-chip is-active" data-cat=""><i class="fa fa-th-large"></i> All Partners</button>
            @foreach($ppCategories as $cat)
                <button type="button" class="pp-chip" data-cat="{{ $cat }}"><i class="fa {{ $ppIcons[$cat] ?? 'fa-star' }}"></i> {{ $cat }}</button>
            @endforeach
        </div>

        <section class="pp-featured">
            <div class="pp-featured__head">
                <h2>Featured Partners</h2>
                <button type="button" class="pp-featured__all" id="pp_all">View All Partners <i class="fa fa-long-arrow-right"></i></button>
            </div>

            <div class="pp-grid" id="pp_grid">
                @foreach($ppPartners as $key => $partner)
                    @php
                        $theme = $partner['theme'] ?? 'gold';
                        $logo = !empty($partner['logo']) && file_exists(public_path($partner['logo'])) ? '/' . ltrim($partner['logo'], '/') : null;
                    @endphp
                    <article class="pp-card" data-cat="{{ $partner['category'] ?? '' }}">
                        <div class="pp-cover pp-cover--{{ $theme }}">
                            <b>{{ $partner['name'] }}</b>
                            @if(!empty($partner['tagline']))<span>{{ $partner['tagline'] }}</span>@endif
                        </div>
                        <div class="pp-logo {{ $logo ? '' : 'pp-logo--dark' }}">
                            @if($logo)<img src="{{ $logo }}" alt="{{ $partner['name'] }} logo" loading="lazy">@else<span class="pp-logo__initial">{{ strtoupper(mb_substr($partner['name'], 0, 1)) }}</span>@endif
                        </div>
                        <div class="pp-body">
                            <h3 class="pp-name">{{ $partner['name'] }}</h3>
                            <span class="pp-pill">UR Privilege Partner</span>
                            @if(!empty($partner['description']))<p class="pp-desc">{{ $partner['description'] }}</p>@else<p class="pp-desc"></p>@endif
                            @if(!empty($partner['benefit']))
                                <div class="pp-benefit pp-benefit--{{ $theme }}">
                                    <i class="fa fa-gift"></i>
                                    <div><small>Exclusive Benefit for UR Members</small><b>{{ $partner['benefit'] }}</b></div>
                                </div>
                            @endif
                            <a class="pp-visit" href="{{ route('partner.go', $key) }}" target="_blank" rel="nofollow noopener">Visit Website <i class="fa fa-external-link"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="pp-empty" id="pp_empty">No partners in this category yet.</p>
        </section>
    </div>

    <section class="pp-join">
        <div class="pp-join__inner">
            <div>
                <h2>Partner With Urgent Rishta</h2>
                <p>Grow your business with Pakistan's trusted matchmaking platform and connect with a serious, family-oriented audience.</p>
                <a class="pp-join__btn" href="{{ url('become-a-partner') }}">Become a Partner <i class="fa fa-arrow-right"></i></a>
            </div>
            <div class="pp-join__points">
                <div class="pp-join__point"><i class="fa fa-users"></i><span>Targeted<br>Audience</span></div>
                <div class="pp-join__point"><i class="fa fa-bar-chart"></i><span>Increase<br>Brand Visibility</span></div>
                <div class="pp-join__point"><i class="fa fa-handshake-o"></i><span>Long-term<br>Partnership</span></div>
                <div class="pp-join__point"><i class="fa fa-star-o"></i><span>Be Part of a<br>Trusted Network</span></div>
            </div>
        </div>
    </section>
</div>

<script>
    // Category chips filter the cards in the browser; "View All Partners" resets the filter.
    (function () {
        var chips = document.querySelectorAll('#pp_chips .pp-chip'), cards = document.querySelectorAll('#pp_grid .pp-card'), empty = document.getElementById('pp_empty');
        function show(cat) {
            var shown = 0;
            chips.forEach(function (c) { c.classList.toggle('is-active', c.dataset.cat === cat); });
            cards.forEach(function (card) { var ok = !cat || card.dataset.cat === cat; card.hidden = !ok; if (ok) shown++; });
            empty.style.display = shown ? 'none' : 'block';
        }
        chips.forEach(function (c) { c.addEventListener('click', function () { show(c.dataset.cat); }); });
        document.getElementById('pp_all').addEventListener('click', function () { show(''); });
    })();
</script>
@endsection
