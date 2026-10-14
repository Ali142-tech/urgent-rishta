@extends('layouts.master')

@section('main-content')
<style>
    /* Keep below sticky site header, same treatment as the login page */
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
        background: linear-gradient(180deg, #FBF7EF 0%, #ffffff 45%);
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
        border: 1px solid rgba(201, 151, 77, 0.08);
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
    .shaadi-login-card .form-control {
        height: 46px;
        border-radius: 10px;
        border: 1px solid #ddd;
        font-size: 15px;
        width: 100%;
        box-shadow: none;
        margin-bottom: 14px;
    }
    .shaadi-login-card .form-control:focus {
        border-color: #123A2E;
        box-shadow: 0 0 0 3px rgba(201, 151, 77, 0.12);
    }
    .shaadi-btn-primary {
        width: 100%;
        height: 48px;
        border: 0;
        border-radius: 999px;
        background: #123A2E;
        color: #fff !important;
        font-weight: 600;
        font-size: 16px;
        margin-top: 6px;
        transition: background .2s ease, opacity .2s ease;
    }
    .shaadi-btn-primary:hover,
    .shaadi-btn-primary:focus {
        background: #0F2E24;
        color: #fff !important;
        outline: none;
    }
    .login-password-wrap { position: relative; margin-bottom: 14px; }
    .login-password-wrap .form-control { padding-right: 42px; margin-bottom: 0; }
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
    .login-toggle-password:hover { color: #123A2E; }
    .back-link {
        display: inline-block;
        margin-bottom: 12px;
        color: #888;
        font-size: 13px;
        text-decoration: none;
    }
    .back-link:hover { color: #123A2E; text-decoration: none; }

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
        <a href="{{ route('login') }}" class="back-link"><i class="fa fa-angle-left"></i> Back to login</a>

        @if ($errors->any())
            <div class="login-flash danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <h2>Reset password</h2>
        <p class="subtitle">Choose a new password for your account.</p>

        <form id="reset_password_form" role="form" method="post" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="email" class="form-control" name="email" placeholder="Email address" value="{{ old('email', $email ?? '') }}" autofocus required="required" />

            <div class="login-password-wrap">
                <input type="password" class="form-control" name="password" id="password" placeholder="New password" required="required" minlength="8" />
                <button type="button" class="login-toggle-password" data-target="password"><i class="fa fa-eye"></i></button>
            </div>

            <div class="login-password-wrap">
                <input type="password" class="form-control" name="password_confirmation" id="password1" placeholder="Confirm new password" required="required" minlength="8" />
                <button type="button" class="login-toggle-password" data-target="password1"><i class="fa fa-eye"></i></button>
            </div>

            <button type="submit" id="btnSubmit" class="shaadi-btn-primary">Reset Password</button>
        </form>
    </div>
</div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $("#reset_password_form").on('submit', (e) => {
            checkPassword();
            $("#btnSubmit").prop('disabled', true).html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        });

        $("#password1").on('input', function() {
            checkPassword();
        });

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
    });

    function checkPassword() {
        var pass = $("#password");
        var pass1 = $("#password1");
        if (pass.val() != pass1.val())
            pass1[0].setCustomValidity("Passwords do not match");
        else pass1[0].setCustomValidity('');
    }
</script>
@endsection
