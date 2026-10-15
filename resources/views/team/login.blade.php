{{--
    Partner (team member) sign in — a dark "Partner Portal" panel next to the sign-in form. Posting: Auth\TeamLoginController.
    Sign-up lives at /become-a-partner and links back here; members of the site use the normal sign in.
--}}
@extends('layouts.master')

@section('main-content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .mk-page {
        --mk-green: #123A2E; --mk-green-2: #0F2E24; --mk-gold: #C9974D; --mk-cream: #F8F5EE; --mk-line: #E7E2D6; --mk-text: #5B6560; --mk-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif; background: var(--mk-cream); color: var(--mk-ink); padding: 70px 24px 90px;
    }
    .mk-page * { box-sizing: border-box; }
    .mk-shell { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 485px) minmax(0, 485px); gap: 70px; align-items: center; justify-content: center; }

    /* ---- left: portal panel ---- */
    .mk-side { position: relative; overflow: hidden; border-radius: 28px; padding: 46px 44px 44px; min-height: 664px; color: #fff; display: flex; flex-direction: column;
        background: radial-gradient(520px 320px at 100% 0%, rgba(201,151,77,.14), rgba(201,151,77,0) 70%), linear-gradient(160deg, #0F3429 0%, #0D2E25 55%, #0B271F 100%); }
    .mk-side__logo { display: flex; align-items: center; gap: 12px; margin-bottom: 44px; }
    .mk-side__logo i { color: var(--mk-gold); font-size: 22px; }
    .mk-side__logo span { display: block; line-height: 1; }
    .mk-side__logo em { display: block; font-family: 'Playfair Display', serif; font-style: italic; font-weight: 600; font-size: 19px; color: #E3C58B; margin-bottom: 3px; }
    .mk-side__logo b { display: block; font-size: 13px; font-weight: 800; letter-spacing: .22em; color: #fff; }
    .mk-side h2 { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 35px; line-height: 1.22; margin: 0 0 22px; color: #fff; }
    .mk-side h2 em { color: var(--mk-gold); font-style: italic; }
    .mk-side p { font-size: 15.5px; line-height: 1.75; color: rgba(231,240,235,.78); margin: 0; max-width: 390px; }
    .mk-side__list { list-style: none; margin: auto 0 0; padding: 0; display: grid; gap: 18px; }
    .mk-side__list li { display: flex; align-items: center; gap: 14px; font-size: 14.5px; color: #E7EFEA; }
    .mk-side__list i { width: 32px; height: 32px; border-radius: 9px; background: rgba(255,255,255,.08); color: var(--mk-gold); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }

    /* ---- right: form ---- */
    .mk-main { min-width: 0; }
    .mk-eyebrow { display: block; font-size: 12px; font-weight: 800; letter-spacing: .26em; text-transform: uppercase; color: var(--mk-gold); margin-bottom: 14px; }
    .mk-title { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 38px; line-height: 1.15; color: var(--mk-green); margin: 0 0 14px; }
    .mk-sub { font-size: 15px; color: var(--mk-text); line-height: 1.65; margin: 0 0 28px; max-width: 470px; }
    .mk-sub a { color: var(--mk-green); font-weight: 700; text-decoration: none; border-bottom: 1.5px solid var(--mk-gold); }
    .mk-card { background: #fff; border-radius: 24px; padding: 36px 36px 30px; box-shadow: 0 24px 56px rgba(15,46,36,.09); }
    .mk-errors { background: #FCEBE8; border: 1px solid #E6968C; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; font-size: 13.5px; color: #B5674A; }
    .mk-errors ul { margin: 0; padding-left: 18px; }
    .mk-field { margin-bottom: 20px; }
    .mk-field label { display: block; font-size: 13.5px; font-weight: 700; color: var(--mk-green); margin-bottom: 9px; }
    .mk-input { position: relative; }
    .mk-input > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--mk-gold); font-size: 15px; pointer-events: none; }
    .mk-input input { width: 100%; height: 48px; border: 1px solid var(--mk-line); border-radius: 12px; padding: 0 14px 0 44px; font-size: 14.5px; font-family: inherit; color: var(--mk-ink); background: #fff; transition: border-color .15s, box-shadow .15s; }
    .mk-input input:focus { outline: none; border-color: var(--mk-gold); box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .mk-input input::placeholder { color: #9AA5A0; }
    .mk-remember { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--mk-text); margin: 4px 0 22px 4px; cursor: pointer; }
    .mk-remember input { width: 17px; height: 17px; accent-color: var(--mk-green); margin: 0; }
    .mk-submit { width: 100%; height: 54px; border-radius: 999px; background: linear-gradient(180deg, #D2A35C, #C59652); color: #1C2321; border: 0; font-size: 15.5px; font-weight: 800; cursor: pointer; box-shadow: 0 12px 26px rgba(201,151,77,.32); transition: transform .15s, filter .15s; }
    .mk-submit:hover { filter: brightness(1.05); transform: translateY(-1px); }
    .mk-forgot { text-align: center; margin: 18px 0 0; font-size: 13.5px; }
    .mk-forgot a { color: var(--mk-green); font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(201,151,77,.5); padding-bottom: 1px; }
    .mk-foot { text-align: center; font-size: 13.5px; color: var(--mk-text); margin-top: 22px; }
    .mk-foot a { color: var(--mk-green); font-weight: 700; text-decoration: none; }
    .mk-foot a:hover { text-decoration: underline; }

    @media (max-width: 980px) {
        .mk-shell { grid-template-columns: minmax(0, 560px); gap: 34px; }
        .mk-side { min-height: 0; padding: 34px 30px; }
        .mk-side__list { margin-top: 30px; }
        .mk-side__logo { margin-bottom: 28px; }
    }
    @media (max-width: 560px) {
        .mk-page { padding: 36px 14px 60px; }
        .mk-side { border-radius: 22px; padding: 28px 22px; }
        .mk-side h2 { font-size: 27px; }
        .mk-side p { font-size: 14.5px; }
        .mk-title { font-size: 30px; }
        .mk-card { padding: 26px 20px 24px; border-radius: 20px; }
    }
</style>

<div class="mk-page">
    <div class="mk-shell">
        <aside class="mk-side">
            <div class="mk-side__logo"><i class="fa fa-heart"></i><span><em>Urgent</em><b>RISHTA</b></span></div>
            <h2>Welcome back to the <em>Partner Portal</em></h2>
            <p>Manage your clients, track AI matches, and forward proposals &mdash; all from one dashboard built for our trusted matchmakers.</p>
            <ul class="mk-side__list">
                <li><i class="fa fa-check-circle-o"></i> Track client profiles and AI-matched suggestions</li>
                <li><i class="fa fa-paper-plane-o"></i> Forward proposals in one click</li>
                <li><i class="fa fa-user-o"></i> Stay in sync with your matchmaking team</li>
            </ul>
        </aside>

        <div class="mk-main">
            <span class="mk-eyebrow">Partner Portal</span>
            <h1 class="mk-title">Partner Sign In</h1>
            <p class="mk-sub">For matchmakers working in the Partner Dashboard. Looking for your member account? <a href="{{ route('login') }}">Use the normal sign in instead.</a></p>

            @if($errors->any())
            <div class="mk-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="mk-card">
                <form method="POST" action="{{ route('team.login.submit') }}">
                    @csrf
                    <div class="mk-field">
                        <label for="login">Email or mobile number</label>
                        <div class="mk-input"><i class="fa fa-envelope-o"></i><input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="you@example.com" required autofocus autocomplete="username"></div>
                    </div>
                    <div class="mk-field">
                        <label for="password">Password</label>
                        <div class="mk-input"><i class="fa fa-lock"></i><input type="password" id="password" name="password" placeholder="Your password" required autocomplete="current-password"></div>
                    </div>
                    <label class="mk-remember"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
                    <button type="submit" class="mk-submit">Sign In</button>
                    <p class="mk-forgot"><a href="{{ route('team.password.forgot') }}">Forgot your password?</a></p>
                </form>
            </div>
            <p class="mk-foot">New here? <a href="{{ route('matchmaker.apply') }}">Become a Partner</a> &nbsp;&middot;&nbsp; <a href="{{ route('login') }}">Member sign in</a></p>
        </div>
    </div>
</div>
@endsection
