@extends('layouts.master')

@section('main-content')
<style>
    .mk-page {
        --mk-green: #123A2E; --mk-gold: #C9974D; --mk-cream: #FBF7EF; --mk-line: #E7E2D6; --mk-text: #5B6560; --mk-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif; background: var(--mk-cream); color: var(--mk-ink); padding: 60px 20px 80px;
    }
    .mk-wrap { max-width: 460px; margin: 0 auto; }
    .mk-eyebrow { font-size: 11.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--mk-gold); display: block; margin-bottom: 10px; }
    .mk-title { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 30px; color: var(--mk-green); margin: 0 0 10px; }
    .mk-sub { font-size: 14.5px; color: var(--mk-text); line-height: 1.6; margin: 0 0 28px; }
    .mk-card { background: #fff; border: 1px solid var(--mk-line); border-radius: 16px; padding: 30px; }
    .mk-field { margin-bottom: 18px; }
    .mk-field label { display: block; font-size: 13px; font-weight: 700; color: var(--mk-ink); margin-bottom: 6px; }
    .mk-field input[type=text], .mk-field input[type=password] { width: 100%; border: 1px solid var(--mk-line); border-radius: 8px; height: 44px; padding: 0 14px; font-size: 14px; font-family: inherit; }
    .mk-remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--mk-text); margin-bottom: 18px; }
    .mk-submit { width: 100%; height: 50px; border-radius: 999px; background: var(--mk-gold); color: #1C2321; border: none; font-size: 15px; font-weight: 700; cursor: pointer; }
    .mk-submit:hover { background: #B07C3D; }
    .mk-errors { background: #FCEBE8; border: 1px solid #E6968C; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: 13.5px; color: #B5674A; }
    .mk-errors ul { margin: 0; padding-left: 18px; }
    .mk-foot { text-align: center; font-size: 13.5px; color: var(--mk-text); margin-top: 20px; }
    .mk-foot a { color: var(--mk-green); font-weight: 700; }
</style>
<div class="mk-page">
    <div class="mk-wrap">
        <span class="mk-eyebrow">Partner Portal</span>
        <h1 class="mk-title">Partner Sign In</h1>
        <p class="mk-sub">For matchmakers working in the Partner Dashboard. Looking for your member account? Use the normal sign in instead.</p>

        @if($errors->any())
        <div class="mk-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="mk-card">
            <form method="POST" action="{{ route('team.login.submit') }}">
                @csrf
                <div class="mk-field">
                    <label for="login">Email or mobile number</label>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus autocomplete="username">
                </div>
                <div class="mk-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <label class="mk-remember"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
                <button type="submit" class="mk-submit">Sign In</button>
            </form>
        </div>
        <p class="mk-foot">New here? <a href="{{ route('matchmaker.apply') }}">Become a Partner</a> &nbsp;&bull;&nbsp; <a href="{{ route('login') }}">Member sign in</a></p>
    </div>
</div>
@endsection
