@extends('layouts.master')

@section('main-content')
<style>
    /* Keep login below sticky site header */
    body.normalpage .shaadi-login-page {
        position: relative;
        z-index: 1;
        clear: both;
    }
    .shaadi-login-wrap {
        min-height: calc(100vh - 220px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 48px 16px 64px;
        margin-top: 8px;
        background: linear-gradient(180deg, #fff5f8 0%, #ffffff 45%);
        box-sizing: border-box;
    }
    .shaadi-login-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,.10);
        padding: 36px 28px 28px;
        position: relative;
        z-index: 2;
        border: 1px solid rgba(233, 30, 99, 0.08);
    }
    .shaadi-login-card .login-flash {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border-radius: 10px;
        padding: 10px 12px;
        margin: 0 0 16px;
        font-size: 13px;
        line-height: 1.45;
        text-align: left;
        word-wrap: break-word;
        overflow-wrap: anywhere;
    }
    .shaadi-login-card .login-flash.danger,
    .shaadi-login-card .login-flash.error {
        background: #fde8ee;
        color: #9d174d;
        border: 1px solid #f5c2d1;
    }
    .shaadi-login-card .login-flash.success {
        background: #e8f8ef;
        color: #146c43;
        border: 1px solid #b7ebc6;
    }
    .shaadi-login-card .login-flash.warning {
        background: #fff8e6;
        color: #9a6700;
        border: 1px solid #ffe08a;
    }
    .shaadi-login-card .login-flash.info {
        background: #eef5ff;
        color: #1e4b8e;
        border: 1px solid #c9dcff;
    }

    .shaadi-login-card h2 {
        text-align: center;
        font-size: 26px;
        font-weight: 700;
        color: #222;
        margin: 0 0 6px;
        font-family: inherit;
    }
    .shaadi-login-card .subtitle {
        text-align: center;
        color: #777;
        font-size: 14px;
        margin-bottom: 24px;
    }
    .phone-row {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
    }
    .phone-row .cc {
        flex: 0 0 96px;
    }
    .phone-row .mob {
        flex: 1;
        min-width: 0;
    }
    .shaadi-login-card .iti {
        width: 100%;
        margin-bottom: 14px;
        display: block;
    }
    .shaadi-login-card .iti--separate-dial-code .iti__selected-flag {
        background: #fafafa;
        border-radius: 10px 0 0 10px;
    }
    .shaadi-login-card .iti__country-list {
        z-index: 20;
        max-height: 220px;
        width: 320px;
        white-space: normal;
    }
    .shaadi-login-card .form-control {
        height: 46px;
        border-radius: 10px;
        border: 1px solid #ddd;
        font-size: 15px;
        width: 100%;
        box-shadow: none;
    }
    .shaadi-login-card .form-control:focus {
        border-color: #E91E63;
        box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
    }
    .shaadi-btn-primary {
        width: 100%;
        height: 48px;
        border: 0;
        border-radius: 999px;
        background: #E91E63;
        color: #fff !important;
        font-weight: 600;
        font-size: 16px;
        margin-top: 6px;
        transition: background .2s ease, opacity .2s ease;
    }
    .shaadi-btn-primary:hover,
    .shaadi-btn-primary:focus {
        background: #c2185b;
        color: #fff !important;
        outline: none;
    }
    .shaadi-btn-primary:disabled,
    .shaadi-btn-primary[disabled] {
        background: #f3d0dd !important;
        color: #fff !important;
        cursor: not-allowed;
        opacity: 1;
    }
    .shaadi-link {
        display: block;
        text-align: center;
        margin-top: 16px;
        color: #888;
        font-size: 14px;
        text-decoration: none;
    }
    .shaadi-link:hover { color: #E91E63; text-decoration: none; }
    .shaadi-or {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 22px 0 16px;
        color: #999;
        font-size: 13px;
    }
    .shaadi-or:before,
    .shaadi-or:after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e6e6e6;
    }
    .shaadi-btn-outline {
        width: 100%;
        height: 48px;
        border-radius: 999px;
        border: 1px solid #ddd;
        background: #fff;
        color: #333;
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none !important;
    }
    .shaadi-btn-outline:hover {
        border-color: #E91E63;
        color: #E91E63;
        text-decoration: none !important;
    }
    .shaadi-btn-outline.disabled {
        opacity: .5;
        pointer-events: none;
    }
    .login-password-wrap { position: relative; margin-bottom: 12px; }
    .login-password-wrap .form-control { padding-right: 42px; }
    .login-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #888;
        cursor: pointer;
        padding: 4px;
    }
    .login-toggle-password:hover { color: #E91E63; }
    .otp-hint {
        text-align: center;
        font-size: 13px;
        color: #666;
        margin-bottom: 16px;
        line-height: 1.5;
    }
    .back-link {
        display: inline-block;
        margin-bottom: 12px;
        color: #888;
        font-size: 13px;
        text-decoration: none;
    }
    .back-link:hover { color: #E91E63; text-decoration: none; }
    .shaadi-login-card .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 12px;
    }
    .shaadi-login-card .meta-row a { color: #888; }
    .shaadi-login-card .meta-row a:hover { color: #E91E63; }
    .shaadi-login-card .footer-note {
        text-align: center;
        margin-top: 18px;
        margin-bottom: 0;
        font-size: 12px;
        color: #999;
    }
    .shaadi-login-card .footer-note a {
        color: #E91E63;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .shaadi-login-wrap {
            padding-top: 28px;
            min-height: auto;
        }
    }
</style>

<section class="shaadi-login-page">
<div class="shaadi-login-wrap">
    <div class="shaadi-login-card">
        <div class="brand-mark">U</div>

        @php
            $flashType = null;
            $flashText = null;
            if (session('message')) {
                $parts = explode('|', session('message'), 3);
                $flashType = strtolower($parts[0] ?? 'info');
                $flashText = $parts[1] ?? session('message');
                if ($flashType === 'error') $flashType = 'danger';
            }
        @endphp

        @if($flashText)
            <div class="login-flash {{ $flashType }}">{{ $flashText }}</div>
        @endif

        @if ($errors->any())
            <div class="login-flash danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($mode === 'phone')
            <h2>Welcome back</h2>
            <p class="subtitle">Enter your details to continue</p>

            <form method="post" action="{{ route('login.otp.send') }}" id="otp_send_form">
                @csrf
                <input type="hidden" name="country_code" id="otp_country_code" value="{{ $countryCode ?: '92' }}">
                <input type="hidden" name="mobile" id="otp_mobile_hidden" value="{{ old('mobile', $mobileLocal) }}">
                <input
                    type="tel"
                    id="mobile_input"
                    class="form-control"
                    placeholder="Mobile no."
                    value="{{ old('mobile', $mobileLocal) }}"
                    required
                    autocomplete="tel"
                >
                <button type="submit" class="shaadi-btn-primary" id="get_otp_btn" disabled>Get OTP</button>
            </form>

            <a class="shaadi-link" href="{{ route('login', ['mode' => 'password']) }}">Login with Password</a>

            <div class="shaadi-or">Or</div>

            <a href="{{ route('login.google') }}" class="shaadi-btn-outline">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" height="18" alt="">
                Continue with Google
            </a>
            <a href="{{ route('login', ['mode' => 'email']) }}" class="shaadi-btn-outline">
                <i class="fa fa-envelope-o"></i>
                Continue with Email
            </a>

            <p class="footer-note">
                New here? <a href="{{ url('register') }}">Create an account</a>
            </p>
        @endif

        @if($mode === 'otp')
            <a class="back-link" href="{{ route('login') }}"><i class="fa fa-arrow-left"></i> Back</a>
            <h2>Enter OTP</h2>
            <p class="otp-hint">
                We sent a 6-digit code to your email<br>
                <strong>{{ $otpEmailMasked }}</strong><br>
                <span style="color:#999;">(SMS OTP will replace this when provider is ready)</span>
            </p>

            <form method="post" action="{{ route('login.otp.verify') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="otp" class="form-control text-center" placeholder="Enter OTP" maxlength="6" inputmode="numeric" pattern="[0-9]*" required autofocus style="letter-spacing:6px;font-size:20px;">
                </div>
                <button type="submit" class="shaadi-btn-primary">Verify &amp; Login</button>
            </form>

            <form method="post" action="{{ route('login.otp.send') }}" class="mt-2">
                @csrf
                <input type="hidden" name="country_code" value="{{ $countryCode }}">
                <input type="hidden" name="mobile" value="{{ $mobileLocal }}">
                <button type="submit" class="shaadi-link" style="background:none;border:0;width:100%;">Resend OTP</button>
            </form>
            <a class="shaadi-link" href="{{ route('login', ['mode' => 'password']) }}">Login with Password instead</a>
        @endif

        @if($mode === 'password')
            <a class="back-link" href="{{ route('login') }}"><i class="fa fa-arrow-left"></i> Back</a>
            <h2>Login with Password</h2>
            <p class="subtitle">Use your mobile number and password</p>

            <form method="post" action="{{ route('login.password') }}" id="password_login_form">
                @csrf
                <input type="hidden" name="country_code" id="pwd_country_code" value="92">
                <input type="hidden" name="mobile" id="pwd_mobile_hidden" value="{{ old('mobile') }}">
                <input
                    type="tel"
                    id="password_mobile_input"
                    class="form-control"
                    placeholder="Mobile no."
                    value="{{ old('mobile') }}"
                    required
                    autocomplete="tel"
                >
                <div class="login-password-wrap">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <button type="button" class="login-toggle-password" data-target="password"><i class="fa fa-eye"></i></button>
                </div>
                <div class="meta-row">
                    <label class="mb-0"><input type="checkbox" name="remember" value="checked"> Remember Me</label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <button type="submit" class="shaadi-btn-primary">Login</button>
            </form>
            <a class="shaadi-link" href="{{ route('login', ['mode' => 'email']) }}">Continue with Email</a>
        @endif

        @if($mode === 'email')
            <a class="back-link" href="{{ route('login') }}"><i class="fa fa-arrow-left"></i> Back</a>
            <h2>Continue with Email</h2>
            <p class="subtitle">Login using email and password</p>

            <form method="post" action="{{ route('login.email') }}">
                @csrf
                <div class="form-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="login-password-wrap">
                    <input type="password" name="password" id="password_email" class="form-control" placeholder="Password" required>
                    <button type="button" class="login-toggle-password" data-target="password_email"><i class="fa fa-eye"></i></button>
                </div>
                <div class="meta-row">
                    <label class="mb-0"><input type="checkbox" name="remember" value="checked"> Remember Me</label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <button type="submit" class="shaadi-btn-primary">Login</button>
            </form>
            <a class="shaadi-link" href="{{ route('login') }}">Login with Mobile OTP</a>
        @endif
    </div>
</div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>
<script>
(function () {
    function initIti(inputId, formId, countryHiddenId, mobileHiddenId, enableBtnId) {
        var input = document.getElementById(inputId);
        var form = document.getElementById(formId);
        if (!input || !window.intlTelInput) return null;
        if (input.dataset.itiReady === '1') return null;
        input.dataset.itiReady = '1';

        var initial = @json((string)($countryCode ?: '92'));
        var isoGuess = { '92': 'pk', '971': 'ae', '966': 'sa', '44': 'gb', '1': 'us', '91': 'in', '61': 'au', '64': 'nz', '965': 'kw', '27': 'za' };

        var iti = window.intlTelInput(input, {
            initialCountry: isoGuess[String(initial)] || 'pk',
            separateDialCode: true,
            nationalMode: true,
            autoPlaceholder: 'polite',
            preferredCountries: ['pk', 'in', 'ae', 'sa', 'gb', 'us', 'ca', 'au'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js'
        });

        function syncHidden() {
            var data = iti.getSelectedCountryData() || {};
            var dial = data.dialCode || '';
            var national = (input.value || '').replace(/\D+/g, '');
            var cc = document.getElementById(countryHiddenId);
            var mob = document.getElementById(mobileHiddenId);
            if (cc) cc.value = dial;
            if (mob) mob.value = national;
            if (enableBtnId) {
                var btn = document.getElementById(enableBtnId);
                if (btn) btn.disabled = national.length < 7;
            }
        }

        input.addEventListener('input', syncHidden);
        input.addEventListener('countrychange', syncHidden);
        if (form) form.addEventListener('submit', syncHidden);
        syncHidden();
        return iti;
    }

    function boot() {
        initIti('mobile_input', 'otp_send_form', 'otp_country_code', 'otp_mobile_hidden', 'get_otp_btn');
        initIti('password_mobile_input', 'password_login_form', 'pwd_country_code', 'pwd_mobile_hidden', null);

        document.querySelectorAll('.login-toggle-password').forEach(function (el) {
            el.addEventListener('click', function () {
                var id = el.getAttribute('data-target');
                var input = document.getElementById(id);
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                var icon = el.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                }
            });
        });
    }

    boot();
})();
</script>
@endsection
