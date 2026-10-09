{{--
    Team member "Change Password" — same look as My Profile: a form card (show / hide eyes, live strength meter, "passwords
    match" hint) next to a tips card. Validation and saving: TeamController::passwordUpdate().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Change Password')
@section('main-content')
<style>
    .ur-pw-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-pw-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 30px; margin: 0; line-height: 1.2 !important; }
    .ur-pw-head a { font-size: 14px; font-weight: 700; color: #A8731F !important; text-decoration: underline; }

    .ur-pw-grid { display: grid; grid-template-columns: minmax(0, 1.12fr) minmax(0, 1fr); gap: 22px; align-items: start; }
    .ur-pw-card { background: #fff; border-radius: 22px; padding: 26px; box-shadow: 0 2px 14px rgba(15,46,36,.06); }
    .ur-pw-card__label { font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #8A938E; margin: 0 0 18px; }
    .ur-pw-field { margin-bottom: 18px; }
    .ur-pw-field label { display: block; font-size: 13.5px; font-weight: 700; color: #1C2321; margin: 0 0 7px; }
    .ur-pw-input { position: relative; }
    .ur-pw-input > i.ur-pw-lock { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #A9B3AE; font-size: 15px; pointer-events: none; }
    .ur-pw-input input { width: 100%; height: 48px; border: 1px solid #DDD5C2; border-radius: 12px; padding: 0 46px 0 42px; font-size: 14.5px; color: #1C2321; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; }
    .ur-pw-input input:focus { border-color: #C9974D; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .ur-pw-input.is-bad input { border-color: #D97B8E; }
    .ur-pw-eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; background: transparent; color: #7B8781; cursor: pointer; border-radius: 8px; font-size: 16px; }
    .ur-pw-eye:hover { background: #F3EFE6; color: #123A2E; }
    .ur-pw-err { color: #B5674A; font-size: 12.5px; margin-top: 6px; }

    .ur-pw-meter { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-top: 10px; }
    .ur-pw-meter span { height: 5px; border-radius: 999px; background: #E9E5DA; transition: background .2s; }
    .ur-pw-meter[data-level="1"] span:nth-child(-n+1) { background: #D9534F; }
    .ur-pw-meter[data-level="2"] span:nth-child(-n+2) { background: #E8A33D; }
    .ur-pw-meter[data-level="3"] span:nth-child(-n+3) { background: #7BB661; }
    .ur-pw-meter[data-level="4"] span { background: #1C9A6C; }
    .ur-pw-meter-text { font-size: 12.5px; color: #6B7570; margin-top: 6px; min-height: 18px; }
    .ur-pw-match { font-size: 12.5px; margin-top: 6px; min-height: 18px; }
    .ur-pw-match.is-ok { color: #1C7A58; } .ur-pw-match.is-bad { color: #B02A47; }

    .ur-pw-save { height: 46px; padding: 0 30px; border-radius: 12px; background: #C9974D; color: #fff; border: 0; font-size: 14.5px; font-weight: 700; cursor: pointer; }
    .ur-pw-save:hover { background: #B88640; }

    .ur-pw-tips h2 { font-size: 15.5px; font-weight: 800; color: #123A2E; margin: 0 0 6px; display: flex; align-items: center; gap: 10px; }
    .ur-pw-tips h2 i { color: #1C9A6C; }
    .ur-pw-tips > p { font-size: 13px; color: #6B7570; line-height: 1.55; margin: 0 0 16px; }
    .ur-pw-tip { display: flex; gap: 12px; align-items: flex-start; padding: 12px 0; border-top: 1px solid #F0ECE1; }
    .ur-pw-tip i { width: 32px; height: 32px; border-radius: 50%; background: #E3F3EA; color: #1C7A58; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
    .ur-pw-tip b { display: block; font-size: 13.5px; color: #1C2321; margin-bottom: 2px; }
    .ur-pw-tip span { display: block; font-size: 12.5px; color: #6B7570; line-height: 1.5; }
    .ur-pw-account { margin-top: 6px; padding: 14px 16px; border-radius: 14px; background: #F6F4EF; font-size: 13px; color: #4B5651; }
    .ur-pw-account b { color: #123A2E; }

    @media (max-width: 991px) { .ur-pw-grid { grid-template-columns: 1fr; } }
    @media (max-width: 560px) {
        .ur-pw-head h1 { font-size: 24px; }
        .ur-pw-card { padding: 18px; border-radius: 18px; }
        .ur-pw-save { width: 100%; }
    }
</style>

<div class="ur-pw-head">
    <h1>Change Password</h1>
    <a href="{{ route('team.profile') }}">&larr; Back to My Profile</a>
</div>

<div class="ur-pw-grid">
    <form method="POST" action="{{ route('team.password.update') }}" class="ur-pw-card" id="pw_form" autocomplete="off">
        @csrf
        <div class="ur-pw-card__label">Update your password</div>

        <div class="ur-pw-field">
            <label for="current_password">Current password</label>
            <div class="ur-pw-input">
                <i class="fa fa-lock ur-pw-lock"></i>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                <button type="button" class="ur-pw-eye" data-target="current_password" aria-label="Show password"><i class="fa fa-eye"></i></button>
            </div>
            @error('current_password')<div class="ur-pw-err">{{ $message }}</div>@enderror
        </div>

        <div class="ur-pw-field">
            <label for="password">New password</label>
            <div class="ur-pw-input">
                <i class="fa fa-key ur-pw-lock"></i>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                <button type="button" class="ur-pw-eye" data-target="password" aria-label="Show password"><i class="fa fa-eye"></i></button>
            </div>
            <div class="ur-pw-meter" id="pw_meter" data-level="0"><span></span><span></span><span></span><span></span></div>
            <div class="ur-pw-meter-text" id="pw_meter_text">At least 8 characters.</div>
            @error('password')<div class="ur-pw-err">{{ $message }}</div>@enderror
        </div>

        <div class="ur-pw-field">
            <label for="password_confirmation">Confirm new password</label>
            <div class="ur-pw-input" id="pw_confirm_wrap">
                <i class="fa fa-check-circle-o ur-pw-lock"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
                <button type="button" class="ur-pw-eye" data-target="password_confirmation" aria-label="Show password"><i class="fa fa-eye"></i></button>
            </div>
            <div class="ur-pw-match" id="pw_match"></div>
        </div>

        <button type="submit" class="ur-pw-save">Update Password</button>
    </form>

    <div class="ur-pw-card ur-pw-tips">
        <h2><i class="fa fa-shield"></i> Keep your account safe</h2>
        <p>Your Team Dashboard holds your clients' private details, so choose a password only you know.</p>
        <div class="ur-pw-tip"><i class="fa fa-text-width"></i><div><b>Make it long</b><span>Use at least 8 characters — 12 or more is much harder to guess.</span></div></div>
        <div class="ur-pw-tip"><i class="fa fa-font"></i><div><b>Mix it up</b><span>Combine upper and lower case letters, numbers and a symbol.</span></div></div>
        <div class="ur-pw-tip"><i class="fa fa-user-secret"></i><div><b>Keep it personal-free</b><span>Avoid your name, phone number or birth year, and don't reuse a password from another site.</span></div></div>
        <div class="ur-pw-account">Signed in as <b>{{ auth()->user()->email }}</b>. Forgot your current password? Sign out and use <b>Forgot password</b> on the login page.</div>
    </div>
</div>

<script>
    (function () {
        // show / hide each password field
        document.querySelectorAll('.ur-pw-eye').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('i').className = 'fa ' + (show ? 'fa-eye-slash' : 'fa-eye');
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        // strength meter for the new password
        var pw = document.getElementById('password'), conf = document.getElementById('password_confirmation');
        var meter = document.getElementById('pw_meter'), meterText = document.getElementById('pw_meter_text');
        var labels = ['At least 8 characters.', 'Weak — add more characters.', 'Fair — add numbers or symbols.', 'Good — almost there.', 'Strong password.'];
        function score(v) {
            if (!v) return 0;
            var s = 0;
            if (v.length >= 8) s++;
            if (v.length >= 12) s++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
            if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) s++;
            return v.length < 8 ? Math.min(s, 1) : Math.max(s, 1);
        }
        function matchHint() {
            var el = document.getElementById('pw_match'), wrap = document.getElementById('pw_confirm_wrap');
            if (!conf.value) { el.textContent = ''; el.className = 'ur-pw-match'; wrap.classList.remove('is-bad'); return; }
            var ok = conf.value === pw.value;
            el.textContent = ok ? 'Passwords match.' : 'Passwords do not match yet.';
            el.className = 'ur-pw-match ' + (ok ? 'is-ok' : 'is-bad');
            wrap.classList.toggle('is-bad', !ok);
        }
        pw.addEventListener('input', function () {
            var s = score(pw.value);
            meter.dataset.level = s;
            meterText.textContent = labels[s];
            matchHint();
        });
        conf.addEventListener('input', matchHint);
        document.getElementById('pw_form').addEventListener('submit', function (e) {
            if (pw.value !== conf.value) { e.preventDefault(); matchHint(); conf.focus(); }
        });
    })();
</script>
@endsection
