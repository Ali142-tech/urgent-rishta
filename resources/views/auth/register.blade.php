@extends('layouts.master')

@section('main-content')
<style>
    .shaadi-reg-page { position: relative; z-index: 1; clear: both; }
    .shaadi-reg-wrap {
        min-height: calc(100vh - 220px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 48px 16px 64px;
        background: linear-gradient(180deg, #fff5f8 0%, #ffffff 45%);
        box-sizing: border-box;
    }
    .shaadi-reg-card {
        width: 100%;
        max-width: {{ $mode === 'profile' ? '720px' : '420px' }};
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,.10);
        padding: 36px 28px 28px;
        border: 1px solid rgba(233, 30, 99, 0.08);
    }
    .shaadi-reg-card .brand-mark {
        width: 56px; height: 56px; border-radius: 14px; background: #E91E63; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 700; margin: 0 auto 18px;
        box-shadow: 0 6px 16px rgba(233, 30, 99, 0.35);
    }
    .shaadi-reg-card h2 { text-align: center; font-size: 26px; font-weight: 700; color: #222; margin: 0 0 6px; }
    .shaadi-reg-card .subtitle { text-align: center; color: #777; font-size: 14px; margin-bottom: 24px; }
    .shaadi-reg-card .form-control {
        height: 46px; border-radius: 10px; border: 1px solid #ddd; font-size: 15px; width: 100%; box-shadow: none;
    }
    .shaadi-reg-card .form-control:focus {
        border-color: #E91E63; box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
    }
    .shaadi-reg-card .form-control-sm { height: 38px; font-size: 14px; }
    .shaadi-reg-card .iti { width: 100%; margin-bottom: 14px; display: block; }
    .shaadi-reg-card .iti__country-list { z-index: 20; max-height: 220px; width: 320px; }
    .shaadi-btn-primary {
        width: 100%; height: 48px; border: 0; border-radius: 999px; background: #E91E63;
        color: #fff !important; font-weight: 600; font-size: 16px; margin-top: 6px;
    }
    .shaadi-btn-primary:hover { background: #c2185b; color: #fff !important; }
    .shaadi-btn-primary:disabled { background: #f3d0dd !important; cursor: not-allowed; }
    .shaadi-link { display: block; text-align: center; margin-top: 16px; color: #888; font-size: 14px; }
    .shaadi-link:hover { color: #E91E63; text-decoration: none; }
    .shaadi-or { display: flex; align-items: center; gap: 12px; margin: 22px 0 16px; color: #999; font-size: 13px; }
    .shaadi-or:before, .shaadi-or:after { content: ""; flex: 1; height: 1px; background: #e6e6e6; }
    .shaadi-btn-outline {
        width: 100%; height: 48px; border-radius: 999px; border: 1px solid #ddd; background: #fff;
        color: #333; font-weight: 600; font-size: 15px; margin-bottom: 10px;
        display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none !important;
    }
    .shaadi-btn-outline:hover { border-color: #E91E63; color: #E91E63; text-decoration: none !important; }
    .login-flash {
        width: 100%; box-sizing: border-box; border-radius: 10px; padding: 10px 12px;
        margin: 0 0 16px; font-size: 13px; line-height: 1.45; text-align: left; word-wrap: break-word;
    }
    .login-flash.danger, .login-flash.error { background: #fde8ee; color: #9d174d; border: 1px solid #f5c2d1; }
    .login-flash.success { background: #e8f8ef; color: #146c43; border: 1px solid #b7ebc6; }
    .login-flash.warning { background: #fff8e6; color: #9a6700; border: 1px solid #ffe08a; }
    .back-link { display: inline-block; margin-bottom: 12px; color: #888; font-size: 13px; text-decoration: none; }
    .back-link:hover { color: #E91E63; text-decoration: none; }
    .step-pill {
        display: inline-block; background: #fce4ec; color: #E91E63; border-radius: 999px;
        padding: 4px 12px; font-size: 12px; font-weight: 600; margin-bottom: 12px;
    }
    .profile-locked {
        background: #f8f8f8 !important;
    }
    .reg-field { margin-bottom: 22px; }
    .reg-field > .reg-label {
        display: block; font-size: 15px; font-weight: 600; color: #333; margin-bottom: 8px;
    }
    .reg-field-outline {
        position: relative;
        border: 1px solid #cfcfcf;
        border-radius: 8px;
        padding: 14px 12px 8px;
        background: #fff;
        transition: border-color .15s ease;
    }
    .reg-field-outline:focus-within {
        border-color: #E91E63;
        box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
    }
    .reg-field-outline .float-label {
        position: absolute; top: -8px; left: 10px; padding: 0 6px;
        background: #fff; font-size: 11px; color: #888; line-height: 1;
    }
    .reg-field-outline:focus-within .float-label { color: #E91E63; }
    .reg-field-outline select {
        width: 100%; border: 0; outline: none; background: transparent;
        font-size: 15px; color: #222; height: 28px; padding: 0;
        -webkit-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23888' d='M1.4 0L6 4.6 10.6 0 12 1.4 6 7.4 0 1.4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 4px center;
        padding-right: 20px;
    }
    .reg-field .form-control {
        background: #f3f6f8;
        border: 1px solid #e2e8ee;
    }
    .reg-field .form-control.is-invalid-email,
    .reg-field .form-control.email-taken {
        border-color: #e53935 !important;
        background: #fff5f5;
    }
    .reg-field-error {
        color: #e53935;
        font-size: 13px;
        margin-top: 8px;
        line-height: 1.4;
    }
    .reg-field-error a { color: #1976d2; font-weight: 600; }
    .shaadi-reg-card.hide-mark .brand-mark.top-mark { display: none; }
    .reg-progress {
        display: flex; gap: 6px; margin: 0 0 22px; padding: 0 4px;
    }
    .reg-progress span {
        flex: 1; height: 4px; border-radius: 999px; background: #e8e8e8;
    }
    .reg-progress span.on { background: #E91E63; }
    .reg-build-icon {
        width: 48px; height: 48px; border-radius: 50%; margin: 0 auto 14px;
        background: #fce4ec; color: #E91E63; display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .reg-build-title {
        text-align: center; font-size: 22px; font-weight: 700; color: #333; margin: 0 0 28px;
    }
    .reg-required-note {
        text-align: right; font-size: 11px; color: #999; margin-top: 14px;
    }
    .otp-boxes {
        display: flex; gap: 12px; justify-content: center; margin: 28px 0 24px;
    }
    .otp-boxes input {
        width: 42px; height: 48px; border: 0; border-bottom: 2px solid #ddd;
        text-align: center; font-size: 22px; font-weight: 600; outline: none; background: transparent;
    }
    .otp-boxes input:focus { border-bottom-color: #E91E63; }
    .verify-mobile-sub {
        text-align: center; color: #666; font-size: 14px; line-height: 1.5; margin: 0 0 8px;
    }
    .verify-mobile-sub a { color: #1976d2; font-weight: 600; }
    .resend-row { text-align: center; color: #999; font-size: 13px; margin-top: 14px; }
    .resend-row button {
        background: none; border: 0; color: #E91E63; font-weight: 600; padding: 0; cursor: pointer;
    }
    .resend-row button:disabled { color: #aaa; cursor: default; }
</style>

<section class="shaadi-reg-page">
<div class="shaadi-reg-wrap">
    <div class="shaadi-reg-card {{ in_array($mode, ['community', 'contact', 'build', 'build2', 'build3', 'build4'], true) ? 'hide-mark' : '' }}">
        <div class="brand-mark top-mark">U</div>

        @php
            $flashType = null; $flashText = null;
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
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        {{-- STEP 1: name + DOB (Shaadi-style) --}}
        @if($mode === 'start')
            <h2>Create account</h2>
            <p class="subtitle">Let's start with your name and date of birth</p>

            <form method="post" action="{{ route('register.basics') }}" id="reg_basics_form">
                @csrf
                <div class="form-group mb-3">
                    <input type="text" name="first_name" id="reg_first_name" class="form-control" placeholder="First name"
                           value="{{ old('first_name', $registerFirstName) }}" required autocomplete="given-name">
                </div>
                <div class="form-group mb-3">
                    <input type="text" name="last_name" id="reg_last_name" class="form-control" placeholder="Last name"
                           value="{{ old('last_name', $registerLastName) }}" required autocomplete="family-name">
                </div>
                <label class="control-label font_light" style="font-size:13px;color:#666;margin-bottom:6px;display:block;">Date of birth</label>
                <div class="row mb-3" style="margin-left:-6px;margin-right:-6px;">
                    <div class="col-xs-4" style="padding:0 6px;width:33.33%;float:left;">
                        <select name="day" id="reg_day" class="form-control" required>
                            <option value="" disabled {{ old('day', $registerDay) ? '' : 'selected' }}>DD</option>
                            @for ($i = 1; $i <= 31; $i++)
                                @php $d = $i < 10 ? '0'.$i : (string)$i; @endphp
                                <option value="{{ $d }}" @if(old('day', $registerDay)==$d) selected @endif>{{ $d }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-xs-4" style="padding:0 6px;width:33.33%;float:left;">
                        <select name="month" id="reg_month" class="form-control" required>
                            <option value="" disabled {{ old('month', $registerMonth) ? '' : 'selected' }}>MM</option>
                            @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $val=>$label)
                                <option value="{{ $val }}" @if(old('month', $registerMonth)==$val) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-4" style="padding:0 6px;width:33.33%;float:left;">
                        <select name="year" id="reg_year" class="form-control" required>
                            <option value="" disabled {{ old('year', $registerYear) ? '' : 'selected' }}>YYYY</option>
                            @for ($i = ((int)date('Y') - 18); $i >= 1927; $i--)
                                <option value="{{ $i }}" @if(old('year', $registerYear)==$i) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div style="clear:both;"></div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_basics" disabled>Continue</button>
            </form>

            <div class="shaadi-or">Or</div>
            <a href="{{ route('login.google') }}" class="shaadi-btn-outline">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" height="18" alt="">
                Continue with Google
            </a>
            <a class="shaadi-link" href="{{ route('login') }}">Already have an account? Log in</a>
        @endif

        {{-- STEP 2: religion + community + country (Shaadi-style) --}}
        @if($mode === 'community')
            <a class="back-link" href="{{ route('register', ['mode' => 'start']) }}"><i class="fa fa-arrow-left"></i></a>
            <form method="post" action="{{ route('register.community') }}" id="reg_community_form" style="margin-top:8px;">
                @csrf
                <div class="reg-field">
                    <span class="reg-label">Your religion</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Religion</span>
                        <select name="religion" id="reg_religion" required>
                            <option value="" disabled {{ old('religion', $registerReligion) ? '' : 'selected' }}>Select</option>
                            @foreach($religions as $religion)
                                <option value="{{ $religion->dataid }}" @if(old('religion', $registerReligion)==$religion->dataid) selected @endif>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Community</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Community</span>
                        <select name="caste" id="reg_caste" required>
                            <option value="" disabled {{ old('caste', $registerCaste) ? '' : 'selected' }}>Select</option>
                            @foreach($caste as $cst)
                                <option value="{{ $cst->dataid }}" @if(old('caste', $registerCaste)==$cst->dataid) selected @endif>{{ $cst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Living in</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Country</span>
                        <select name="country" id="reg_country" required>
                            <option value="" disabled {{ old('country', $registerCountry) ? '' : 'selected' }}>Select</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->dataid }}" @if(old('country', $registerCountry)==$country->dataid) selected @endif>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_community" disabled>Continue</button>
            </form>
        @endif

        {{-- STEP 3: email + mobile (Submit only — no OTP) --}}
        @if($mode === 'contact')
            <a class="back-link" href="{{ route('register', ['mode' => 'community']) }}"><i class="fa fa-arrow-left"></i></a>
            <div class="text-center" style="margin:8px 0 16px;">
                <div class="brand-mark" style="width:64px;height:64px;border-radius:50%;font-size:26px;box-shadow:0 0 0 8px rgba(233,30,99,.12);">
                    <i class="fa fa-shield"></i>
                </div>
            </div>
            <p class="subtitle" style="font-size:15px;color:#444;font-weight:500;margin-bottom:28px;">
                An active email ID &amp; phone no. are required to secure your Profile
            </p>

            <form method="post" action="{{ route('register.contact') }}" id="reg_contact_form">
                @csrf
                <div class="reg-field">
                    <span class="reg-label">Email ID</span>
                    @if(!empty($googleOAuth['email']))
                        <input type="email" name="email" id="reg_start_email" class="form-control profile-locked" value="{{ $googleOAuth['email'] }}" readonly required>
                    @else
                        <input type="email" name="email" id="reg_start_email" class="form-control {{ session('email_taken') || old('email_taken') ? 'is-invalid-email' : '' }}"
                               placeholder="Email ID" value="{{ old('email', $registerEmail) }}" required autocomplete="email">
                        <div id="reg_email_error" class="reg-field-error" style="{{ session('email_taken') ? '' : 'display:none;' }}">
                            This email ID is already registered.
                            <div><a href="{{ route('login') }}">Already a Member? Login</a></div>
                        </div>
                    @endif
                </div>
                <div class="reg-field">
                    <span class="reg-label">Mobile no.</span>
                    <input type="hidden" name="country_code" id="reg_country_code" value="{{ old('country_code', $registerCountryCode) }}">
                    <input type="hidden" name="mobile" id="reg_mobile_hidden" value="{{ old('mobile', $registerMobileLocal) }}">
                    <input type="tel" id="reg_mobile_input" class="form-control" placeholder="Mobile no."
                           value="{{ old('mobile', $registerMobileLocal) }}" required autocomplete="tel">
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_contact_submit" disabled>Submit</button>
            </form>
            <p style="text-align:center;font-size:12px;color:#888;margin-top:18px;line-height:1.5;">
                By creating account, you agree to our
                <a href="{{ url('privacy') }}" target="_blank" style="color:#E91E63;">Privacy Policy</a>
                and
                <a href="{{ url('tandc') }}" target="_blank" style="color:#E91E63;">T&amp;C</a>.
            </p>
        @endif

        {{-- Later step: OTP (not part of the first 3 Shaadi screens) --}}
        @if($mode === 'otp')
            <a class="back-link" href="{{ route('register', ['mode' => 'contact']) }}"><i class="fa fa-arrow-left"></i> Back</a>
            <h2>Enter OTP</h2>
            @if(empty($registerEmailMasked))
                <p class="subtitle">We'll email a 6-digit code to verify <strong>{{ $registerEmail }}</strong></p>
                <form method="post" action="{{ route('register.otp.send') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $registerEmail }}">
                    <input type="hidden" name="country_code" value="{{ $registerCountryCode }}">
                    <input type="hidden" name="mobile" value="{{ $registerMobileLocal }}">
                    <button type="submit" class="shaadi-btn-primary">Send OTP</button>
                </form>
            @else
                <p class="subtitle">
                    We sent a 6-digit code to<br><strong>{{ $registerEmailMasked }}</strong>
                </p>
                <form method="post" action="{{ route('register.otp.verify') }}">
                    @csrf
                    <input type="text" name="otp" class="form-control text-center mb-3" placeholder="Enter OTP" maxlength="6" inputmode="numeric" required autofocus style="letter-spacing:6px;font-size:20px;">
                    <button type="submit" class="shaadi-btn-primary">Verify &amp; Continue</button>
                </form>
                <form method="post" action="{{ route('register.otp.send') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="email" value="{{ $registerEmail }}">
                    <input type="hidden" name="country_code" value="{{ $registerCountryCode }}">
                    <input type="hidden" name="mobile" value="{{ $registerMobileLocal }}">
                    <button type="submit" class="shaadi-link" style="background:none;border:0;width:100%;">Resend OTP</button>
                </form>
            @endif
        @endif

        {{-- After OTP: build profile — State / City / Sub-community --}}
        @if($mode === 'build')
            <div class="reg-progress">
                <span class="on"></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="reg-build-icon"><i class="fa fa-map-marker"></i></div>
            <h2 class="reg-build-title">Now let's build your Profile</h2>

            <form method="post" action="{{ route('register.build') }}" id="reg_build_form">
                @csrf
                @if(($states ?? collect())->isEmpty())
                    <div class="login-flash warning">No states found for your selected country. Go back and pick another country, or contact support.</div>
                    <a class="shaadi-btn-outline" href="{{ route('register', ['mode' => 'community']) }}">Change country</a>
                @else
                <div class="reg-field">
                    <span class="reg-label">State</span>
                    <div class="reg-field-outline">
                        <span class="float-label">State you live in</span>
                        <select name="state" id="reg_build_state" required>
                            <option value="" disabled {{ old('state', $registerState) ? '' : 'selected' }}>Select</option>
                            @foreach(($states ?? collect()) as $st)
                                <option value="{{ $st->dataid }}" @if(old('state', $registerState)==$st->dataid) selected @endif>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">City</span>
                    <div class="reg-field-outline">
                        <span class="float-label">City you live in</span>
                        <select name="city" id="reg_build_city" required>
                            <option value="" disabled {{ old('city', $registerCity) ? '' : 'selected' }}>Select</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_build" disabled>Continue</button>
                @endif
            </form>
            <p class="reg-required-note">* Required fields</p>
        @endif

        {{-- Build step 2: marital status + height (no diet) --}}
        @if($mode === 'build2')
            <a class="back-link" href="{{ route('register', ['mode' => 'build']) }}"><i class="fa fa-arrow-left"></i></a>
            <div class="reg-progress">
                <span class="on"></span>
                <span class="on"></span>
                <span></span>
                <span></span>
            </div>
            <div class="reg-build-icon"><i class="fa fa-user"></i></div>

            <form method="post" action="{{ route('register.build2') }}" id="reg_build2_form" style="margin-top:8px;">
                @csrf
                <div class="reg-field">
                    <span class="reg-label">Gender</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Your Gender *</span>
                        <select name="gender" id="reg_build_gender" required>
                            <option value="" disabled {{ old('gender', $registerGender) ? '' : 'selected' }}>Select</option>
                            <option value="male" @if(old('gender', $registerGender)=='male') selected @endif>Male</option>
                            <option value="female" @if(old('gender', $registerGender)=='female') selected @endif>Female</option>
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Marital status</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Your Marital status *</span>
                        <select name="marital_status" id="reg_build_marital" required>
                            <option value="" disabled {{ old('marital_status', $registerMaritalStatus) ? '' : 'selected' }}>Select</option>
                            @foreach($maritalstatuses as $maritalstatus)
                                <option value="{{ $maritalstatus->dataid }}" @if(old('marital_status', $registerMaritalStatus)==$maritalstatus->dataid) selected @endif>{{ $maritalstatus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Height</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Your Height *</span>
                        <select name="height" id="reg_build_height" required>
                            <option value="" disabled {{ old('height', $registerHeight) ? '' : 'selected' }}>Select</option>
                            @php
                                $heights = [];
                                for ($ft = 4; $ft <= 7; $ft++) {
                                    for ($inch = 0; $inch <= 11; $inch++) {
                                        if ($ft === 4 && $inch < 5) continue;
                                        if ($ft === 7 && $inch > 0) break;
                                        $heights[] = $ft . "' " . $inch . '"';
                                    }
                                }
                            @endphp
                            @foreach($heights as $h)
                                <option value="{{ $h }}" @if(old('height', $registerHeight)==$h) selected @endif>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_build2" disabled>Continue</button>
            </form>
            <p class="reg-required-note">* Required fields</p>
        @endif

        {{-- Build step 3: education + profession --}}
        @if($mode === 'build3')
            <a class="back-link" href="{{ route('register', ['mode' => 'build2']) }}"><i class="fa fa-arrow-left"></i></a>
            <div class="reg-progress">
                <span class="on"></span>
                <span class="on"></span>
                <span class="on"></span>
                <span></span>
            </div>
            <div class="reg-build-icon"><i class="fa fa-graduation-cap"></i></div>
            <p class="subtitle" style="margin-bottom:8px;">Great! Few more details</p>
            <h2 class="reg-build-title" style="margin-bottom:24px;">Highest qualification</h2>

            <form method="post" action="{{ route('register.build3') }}" id="reg_build3_form">
                @csrf
                <div class="reg-field">
                    <div class="reg-field-outline">
                        <span class="float-label">Your highest qualification *</span>
                        <select name="education" id="reg_build_education" required>
                            <option value="" disabled {{ old('education', $registerEducation) ? '' : 'selected' }}>Select</option>
                            @foreach($education as $degree)
                                <option value="{{ $degree->dataid }}" @if(old('education', $registerEducation)==$degree->dataid) selected @endif>{{ $degree->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Profession</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Your profession *</span>
                        <input type="text" name="profession" id="reg_build_profession" value="{{ old('profession', $registerProfession) }}" required
                               style="width:100%;border:0;outline:none;background:transparent;font-size:15px;height:28px;padding:0;">
                    </div>
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_build3" disabled>Continue</button>
            </form>
            <p class="reg-required-note">* Required fields</p>
        @endif

        {{-- Build step 4: remaining old-form fields --}}
        @if($mode === 'build4')
            <a class="back-link" href="{{ route('register', ['mode' => 'build3']) }}"><i class="fa fa-arrow-left"></i></a>
            <div class="reg-progress">
                <span class="on"></span>
                <span class="on"></span>
                <span class="on"></span>
                <span class="on"></span>
            </div>
            <div class="reg-build-icon"><i class="fa fa-lock"></i></div>
            <h2 class="reg-build-title">Almost there</h2>
            <p class="subtitle">Sect, mother tongue &amp; password</p>

            <form method="post" action="{{ route('register.build4') }}" id="reg_build4_form">
                @csrf
                <div class="reg-field">
                    <span class="reg-label">Sect</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Your Sect *</span>
                        <input type="text" name="sect" id="reg_build_sect" value="{{ old('sect', $registerSect ?? '') }}" required
                               style="width:100%;border:0;outline:none;background:transparent;font-size:15px;height:28px;padding:0;">
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">On Behalf</span>
                    <div class="reg-field-outline">
                        <span class="float-label">On Behalf *</span>
                        <select name="on_behalf" id="reg_build_on_behalf" required>
                            <option value="" disabled {{ old('on_behalf', $registerOnBehalf ?? null) ? '' : 'selected' }}>Select</option>
                            @foreach(['Self','Son','Daughter','Brother','Sister','Relative','Friend'] as $ob)
                                <option value="{{ $ob }}" @if(old('on_behalf', $registerOnBehalf ?? null)==$ob) selected @endif>{{ $ob }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Mother Tongue</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Mother Tongue *</span>
                        <select name="mother_tongue" id="reg_build_mother_tongue" required>
                            <option value="" disabled {{ old('mother_tongue', $registerMotherTongue ?? null) ? '' : 'selected' }}>Select</option>
                            @foreach($mothertongues as $mothertongue)
                                <option value="{{ $mothertongue->dataid }}" @if(old('mother_tongue', $registerMotherTongue)==$mothertongue->dataid) selected @endif>{{ $mothertongue->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Password</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Password * (min 8)</span>
                        <input type="password" name="password" id="reg_build_password" required minlength="8" autocomplete="new-password"
                               style="width:100%;border:0;outline:none;background:transparent;font-size:15px;height:28px;padding:0;">
                    </div>
                </div>
                <div class="reg-field">
                    <span class="reg-label">Confirm Password</span>
                    <div class="reg-field-outline">
                        <span class="float-label">Confirm Password *</span>
                        <input type="password" name="password_confirmation" id="reg_build_password_confirmation" required minlength="8" autocomplete="new-password"
                               style="width:100%;border:0;outline:none;background:transparent;font-size:15px;height:28px;padding:0;">
                    </div>
                </div>
                <button type="submit" class="shaadi-btn-primary" id="reg_continue_build4" disabled>Create Profile</button>
            </form>
            <p class="reg-required-note">* Required fields</p>
        @endif
    </div>
</div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>
<script>
(function () {
    // Step 1: enable Continue when name + DOB filled
    (function () {
        var btn = document.getElementById('reg_continue_basics');
        if (!btn) return;
        var fields = ['reg_first_name', 'reg_last_name', 'reg_day', 'reg_month', 'reg_year'];
        function syncBasics() {
            var ok = fields.every(function (id) {
                var el = document.getElementById(id);
                return el && String(el.value || '').trim().length > 0;
            });
            btn.disabled = !ok;
        }
        fields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', syncBasics);
                el.addEventListener('change', syncBasics);
            }
        });
        syncBasics();
    })();

    // Step 2: enable Continue when religion + community + country selected
    (function () {
        var btn = document.getElementById('reg_continue_community');
        if (!btn) return;
        var fields = ['reg_religion', 'reg_caste', 'reg_country'];
        function syncCommunity() {
            var ok = fields.every(function (id) {
                var el = document.getElementById(id);
                return el && String(el.value || '').trim().length > 0;
            });
            btn.disabled = !ok;
        }
        fields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('change', syncCommunity);
        });
        syncCommunity();
    })();

    // Profile build: cities load when state changes (states rendered server-side)
    (function () {
        var stateSel = document.getElementById('reg_build_state');
        var citySel = document.getElementById('reg_build_city');
        var btn = document.getElementById('reg_continue_build');
        if (!stateSel || !citySel) return;

        var prefCity = @json(old('city', $registerCity ?? null));
        var citiesUrl = @json(url('cities'));
        var csrf = @json(csrf_token());

        function syncBuild() {
            if (!btn) return;
            btn.disabled = !(stateSel.value && citySel.value);
        }

        function fillCities(options, selectedId) {
            citySel.innerHTML = '';
            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.selected = !selectedId;
            placeholder.textContent = 'Select';
            citySel.appendChild(placeholder);
            (options || []).forEach(function (opt) {
                var o = document.createElement('option');
                o.value = opt.dataid;
                o.textContent = opt.name;
                if (selectedId && String(selectedId) === String(opt.dataid)) o.selected = true;
                citySel.appendChild(o);
            });
            syncBuild();
        }

        function loadCities(stateId, selectedCity) {
            if (!stateId || !window.jQuery) return;
            citySel.innerHTML = '<option value="">Loading…</option>';
            $.ajax({
                type: 'get',
                url: citiesUrl + '/' + stateId + '/0',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: { _token: csrf },
                cache: false
            }).done(function (result) {
                if (result && result.code == '200') {
                    fillCities(result.options || [], selectedCity || null);
                } else {
                    fillCities([], null);
                }
            }).fail(function () {
                fillCities([], null);
            });
        }

        stateSel.addEventListener('change', function () {
            loadCities(stateSel.value, null);
            syncBuild();
        });
        citySel.addEventListener('change', syncBuild);

        if (stateSel.value) {
            loadCities(stateSel.value, prefCity);
        }
        syncBuild();
    })();

    // Build step 2: marital + height
    (function () {
        var btn = document.getElementById('reg_continue_build2');
        if (!btn) return;
        var fields = ['reg_build_gender', 'reg_build_marital', 'reg_build_height'];
        function sync() {
            var ok = fields.every(function (id) {
                var el = document.getElementById(id);
                return el && String(el.value || '').trim().length > 0;
            });
            btn.disabled = !ok;
        }
        fields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('change', sync);
        });
        sync();
    })();

    // Build step 3: education + profession
    (function () {
        var btn = document.getElementById('reg_continue_build3');
        if (!btn) return;
        var edu = document.getElementById('reg_build_education');
        var prof = document.getElementById('reg_build_profession');
        function sync() {
            var ok = edu && edu.value && prof && String(prof.value || '').trim().length > 0;
            btn.disabled = !ok;
        }
        if (edu) edu.addEventListener('change', sync);
        if (prof) {
            prof.addEventListener('input', sync);
            prof.addEventListener('change', sync);
        }
        sync();
    })();

    // Build step 4: sect, on behalf, mother tongue, password
    (function () {
        var btn = document.getElementById('reg_continue_build4');
        if (!btn) return;
        var sect = document.getElementById('reg_build_sect');
        var onBehalf = document.getElementById('reg_build_on_behalf');
        var tongue = document.getElementById('reg_build_mother_tongue');
        var pass = document.getElementById('reg_build_password');
        var pass2 = document.getElementById('reg_build_password_confirmation');
        function sync() {
            var ok = sect && String(sect.value || '').trim().length > 0
                && onBehalf && onBehalf.value
                && tongue && tongue.value
                && pass && String(pass.value || '').length >= 8
                && pass2 && pass2.value === pass.value;
            btn.disabled = !ok;
        }
        [sect, onBehalf, tongue, pass, pass2].forEach(function (el) {
            if (!el) return;
            el.addEventListener('input', sync);
            el.addEventListener('change', sync);
        });
        sync();
    })();

    function initIti(inputId, formId, countryHiddenId, mobileHiddenId, enableBtnId) {
        var input = document.getElementById(inputId);
        var form = document.getElementById(formId);
        if (!input || !window.intlTelInput || input.dataset.itiReady === '1') return;
        input.dataset.itiReady = '1';
        var iti = window.intlTelInput(input, {
            initialCountry: 'pk',
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ['pk', 'in', 'ae', 'sa', 'gb', 'us'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js'
        });
        function sync() {
            var data = iti.getSelectedCountryData() || {};
            var dial = data.dialCode || '';
            var national = (input.value || '').replace(/\D+/g, '');
            var cc = document.getElementById(countryHiddenId);
            var mob = document.getElementById(mobileHiddenId);
            if (cc) cc.value = dial;
            if (mob) mob.value = national;
            if (enableBtnId) {
                var btn = document.getElementById(enableBtnId);
                var email = document.getElementById('reg_start_email');
                var emailOk = email ? /.+@.+\..+/.test(email.value) : true;
                var taken = email && (email.classList.contains('email-taken') || email.classList.contains('is-invalid-email'));
                if (btn) btn.disabled = national.length < 7 || !emailOk || !!taken;
            }
        }
        input.addEventListener('input', sync);
        input.addEventListener('countrychange', sync);
        var email = document.getElementById('reg_start_email');
        if (email) email.addEventListener('input', sync);
        if (form) form.addEventListener('submit', sync);
        sync();
    }

    initIti('reg_mobile_input', 'reg_contact_form', 'reg_country_code', 'reg_mobile_hidden', 'reg_contact_submit');

    // Step 3: email-in-use check + Submit enable
    (function () {
        var email = document.getElementById('reg_start_email');
        var err = document.getElementById('reg_email_error');
        var btn = document.getElementById('reg_contact_submit');
        var mobile = document.getElementById('reg_mobile_input');
        if (!email || !btn || email.readOnly) {
            // Google locked email — still need mobile sync via iti
            return;
        }
        var emailTaken = {{ session('email_taken') ? 'true' : 'false' }};
        var timer = null;

        function emailLooksValid() {
            return /.+@.+\..+/.test(String(email.value || '').trim());
        }
        function mobileOk() {
            return mobile && String(mobile.value || '').replace(/\D+/g, '').length >= 7;
        }
        function syncSubmit() {
            btn.disabled = !emailLooksValid() || emailTaken || !mobileOk();
        }
        function checkEmail() {
            if (!emailLooksValid()) {
                emailTaken = false;
                email.classList.remove('email-taken', 'is-invalid-email');
                if (err) err.style.display = 'none';
                syncSubmit();
                return;
            }
            if (!window.jQuery) { syncSubmit(); return; }
            $.post('{{ url('eiu') }}', { e: email.value.trim(), _token: '{{ csrf_token() }}' })
                .done(function (res) {
                    var count = (res && res.data != null) ? parseInt(res.data, 10) : 0;
                    emailTaken = count > 0;
                    if (emailTaken) {
                        email.classList.add('email-taken', 'is-invalid-email');
                        if (err) err.style.display = '';
                    } else {
                        email.classList.remove('email-taken', 'is-invalid-email');
                        if (err) err.style.display = 'none';
                    }
                    syncSubmit();
                })
                .fail(function () { syncSubmit(); });
        }
        email.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(checkEmail, 400);
            syncSubmit();
        });
        email.addEventListener('blur', checkEmail);
        if (mobile) mobile.addEventListener('input', syncSubmit);
        if (emailTaken) {
            email.classList.add('email-taken', 'is-invalid-email');
        }
        syncSubmit();
    })();


    if (window.jQuery) {
        $(".selectpicker").select2();
    }
})();
</script>
@endsection