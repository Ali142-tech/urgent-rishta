{{--
    Team member sign-up (/become-a-partner) — same split layout as the Partner Sign In page: a dark "Partner Portal" panel on
    the left (what you get + the three application steps) and the application form on the right. Saving: MatchmakerApplicationController.
--}}
@extends('layouts.master')

@section('main-content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    .mk-page {
        --mk-green: #123A2E;
        --mk-gold: #C9974D;
        --mk-cream: #F8F5EE;
        --mk-line: #E7E2D6;
        --mk-text: #5B6560;
        --mk-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--mk-cream);
        color: var(--mk-ink);
        padding: 64px 24px 90px;
    }
    .mk-page * { box-sizing: border-box; }
    .mk-shell { max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 420px) minmax(0, 1fr); gap: 54px; align-items: start; }

    /* ---- left: portal panel (stays in view while the form scrolls) ---- */
    .mk-side { position: sticky; top: 110px; overflow: hidden; border-radius: 28px; padding: 42px 40px 40px; color: #fff; display: flex; flex-direction: column;
        background: radial-gradient(520px 320px at 100% 0%, rgba(201,151,77,.14), rgba(201,151,77,0) 70%), linear-gradient(160deg, #0F3429 0%, #0D2E25 55%, #0B271F 100%); }
    .mk-side__logo { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
    .mk-side__logo i { color: var(--mk-gold); font-size: 22px; }
    .mk-side__logo span { display: block; line-height: 1; }
    .mk-side__logo em { display: block; font-family: 'Playfair Display', serif; font-style: italic; font-weight: 600; font-size: 19px; color: #E3C58B; margin-bottom: 3px; }
    .mk-side__logo b { display: block; font-size: 13px; font-weight: 800; letter-spacing: .22em; color: #fff; }
    .mk-side h2 { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 33px; line-height: 1.22; margin: 0 0 18px; color: #fff; }
    .mk-side h2 em { color: var(--mk-gold); font-style: italic; }
    .mk-side p { font-size: 15px; line-height: 1.75; color: rgba(231,240,235,.78); margin: 0; }
    .mk-side__label { font-size: 11.5px; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; color: var(--mk-gold); margin: 34px 0 16px; }
    .mk-side__steps { list-style: none; margin: 0; padding: 0; display: grid; gap: 0; }
    .mk-side__steps li { position: relative; display: flex; gap: 16px; padding-bottom: 22px; }
    .mk-side__steps li:last-child { padding-bottom: 0; }
    .mk-side__steps li::before { content: ''; position: absolute; left: 16px; top: 34px; bottom: 2px; width: 1px; background: rgba(201,151,77,.35); }
    .mk-side__steps li:last-child::before { display: none; }
    .mk-side__steps b { flex: 0 0 33px; width: 33px; height: 33px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13.5px; font-weight: 800; border: 1.5px solid rgba(201,151,77,.6); color: var(--mk-gold); }
    .mk-side__steps li.is-active b { background: var(--mk-gold); border-color: var(--mk-gold); color: #0F2E24; }
    .mk-side__steps strong { display: block; font-size: 14.5px; color: #fff; margin: 2px 0 2px; }
    .mk-side__steps span { display: block; font-size: 13px; line-height: 1.5; color: rgba(231,240,235,.66); }
    .mk-side__list { list-style: none; margin: 34px 0 0; padding: 22px 0 0; border-top: 1px solid rgba(255,255,255,.1); display: grid; gap: 14px; }
    .mk-side__list li { display: flex; align-items: center; gap: 14px; font-size: 14px; color: #E7EFEA; }
    .mk-side__list i { width: 30px; height: 30px; border-radius: 9px; background: rgba(255,255,255,.08); color: var(--mk-gold); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }

    /* ---- right: heading + form ---- */
    .mk-main { min-width: 0; max-width: 700px; }
    .mk-eyebrow { display: block; font-size: 12px; font-weight: 800; letter-spacing: .26em; text-transform: uppercase; color: var(--mk-gold); margin-bottom: 14px; }
    .mk-title { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 38px; line-height: 1.15; color: var(--mk-green); margin: 0 0 14px; }
    .mk-title em { color: var(--mk-gold); font-style: italic; }
    .mk-sub { font-size: 15px; color: var(--mk-text); line-height: 1.7; margin: 0 0 26px; max-width: 560px; }
    .mk-card { background: #fff; border-radius: 24px; padding: 34px 36px 30px; box-shadow: 0 24px 56px rgba(15,46,36,.09); }
    .mk-errors { background: #FCEBE8; border: 1px solid #E6968C; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-size: 13.5px; color: #B5674A; }
    .mk-errors ul { margin: 0; padding-left: 18px; }
    .mk-section { font-size: 12px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--mk-gold); margin: 28px 0 16px; }
    .mk-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }
    .mk-row--one { grid-template-columns: 1fr; }
    .mk-field label { display: block; font-size: 13.5px; font-weight: 700; color: var(--mk-green); margin-bottom: 8px; }
    .mk-input { position: relative; }
    .mk-input > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--mk-gold); font-size: 15px; pointer-events: none; }
    .mk-input input { width: 100%; height: 48px; border: 1px solid var(--mk-line); border-radius: 12px; padding: 0 14px 0 44px; font-size: 14.5px; font-family: inherit; color: var(--mk-ink); background: #fff; transition: border-color .15s, box-shadow .15s; }
    .mk-field textarea { width: 100%; border: 1px solid var(--mk-line); border-radius: 12px; padding: 14px 16px; font-size: 14.5px; font-family: inherit; color: var(--mk-ink); background: #fff; resize: vertical; min-height: 100px; transition: border-color .15s, box-shadow .15s; }
    .mk-input input:focus, .mk-field textarea:focus { outline: none; border-color: var(--mk-gold); box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .mk-input input::placeholder, .mk-field textarea::placeholder { color: #9AA5A0; }
    .mk-submit { width: 100%; height: 54px; border-radius: 999px; background: linear-gradient(180deg, #D2A35C, #C59652); color: #1C2321; border: 0; font-size: 15.5px; font-weight: 800; cursor: pointer; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 12px 26px rgba(201,151,77,.32); transition: transform .15s, filter .15s; }
    .mk-submit:hover { filter: brightness(1.05); transform: translateY(-1px); }
    .mk-terms { text-align: center; font-size: 13px; color: #8A938E; margin: 18px 0 0; }
    .mk-terms a { color: #C2185B; font-weight: 600; }
    .mk-foot { text-align: center; font-size: 13.5px; color: var(--mk-text); margin-top: 22px; }
    .mk-foot a { color: var(--mk-green); font-weight: 700; text-decoration: none; }
    .mk-foot a:hover { text-decoration: underline; }

    /* ---- photo ---- */
    .mk-photo { position: relative; display: flex; align-items: center; gap: 20px; border: 1.5px dashed #DCC9A0; border-radius: 16px; background: #FAF7F0; padding: 20px 22px; cursor: pointer; transition: border-color .15s, background .15s; margin: 0; }
    .mk-photo:hover, .mk-photo.is-over { border-color: var(--mk-gold); background: #F8F1E2; }
    .mk-photo.has-file { border-style: solid; border-color: var(--mk-green); background: #F4F8F5; }
    .mk-photo__preview { flex: 0 0 78px; width: 78px; height: 78px; border-radius: 50%; background: #F1ECDF center / cover no-repeat; border: 1px dashed #CFC4A8; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: var(--mk-green); overflow: hidden; }
    .mk-photo__ph b { display: block; font-size: 13px; font-weight: 600; margin-bottom: 2px; }
    .mk-photo__ph u { display: block; font-size: 10.5px; line-height: 1.25; color: var(--mk-text); }
    .mk-photo.has-file .mk-photo__ph { display: none; }
    .mk-photo.has-file .mk-photo__preview { border-style: solid; border-color: #fff; box-shadow: 0 0 0 1px var(--mk-line); }
    .mk-photo__title { display: block; font-size: 15.5px; font-weight: 800; color: var(--mk-green); margin-bottom: 4px; }
    .mk-photo__hint { display: block; font-size: 13px; color: var(--mk-text); line-height: 1.5; }
    .mk-photo__name { display: none; font-size: 12.5px; color: var(--mk-green); font-weight: 600; margin-top: 8px; word-break: break-all; }
    .mk-photo.has-file .mk-photo__name { display: block; }
    .mk-photo__error { display: none; color: #B5674A; font-size: 12.5px; margin-top: 8px; font-weight: 600; }
    .mk-photo input[type=file] { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }

    @media (max-width: 1020px) {
        .mk-shell { grid-template-columns: minmax(0, 640px); gap: 30px; justify-content: center; }
        .mk-side { position: static; padding: 32px 28px; }
        .mk-main { max-width: none; }
        .mk-side__label, .mk-side__list { margin-top: 26px; }
    }
    @media (max-width: 620px) {
        .mk-page { padding: 36px 14px 70px; }
        .mk-side { border-radius: 22px; padding: 26px 22px; }
        .mk-side h2 { font-size: 26px; }
        .mk-side__list { display: none; }
        .mk-title { font-size: 30px; }
        .mk-card { padding: 24px 18px 22px; border-radius: 20px; }
        .mk-row { grid-template-columns: 1fr; gap: 16px; }
        .mk-photo { flex-direction: column; text-align: center; padding: 18px 14px; }
    }
</style>

<div class="mk-page">
    <div class="mk-shell">
        <aside class="mk-side">
            <div class="mk-side__logo"><i class="fa fa-heart"></i><span><em>Urgent</em><b>RISHTA</b></span></div>
            <h2>Join the <em>Partner Portal</em></h2>
            <p>Apply to become a trusted Urgent Rishta matchmaker and manage your own clients, AI matches and proposals from one dashboard.</p>

            <div class="mk-side__label">How it works</div>
            <ol class="mk-side__steps">
                <li class="is-active"><b>1</b><div><strong>Your Details</strong><span>Fill in the short application on this page.</span></div></li>
                <li><b>2</b><div><strong>Review</strong><span>Our team reviews your application.</span></div></li>
                <li><b>3</b><div><strong>Approved</strong><span>Sign in to the Partner Dashboard and start adding proposals.</span></div></li>
            </ol>

            <ul class="mk-side__list">
                <li><i class="fa fa-check-circle-o"></i> Track client profiles and AI-matched suggestions</li>
                <li><i class="fa fa-paper-plane-o"></i> Forward proposals in one click</li>
                <li><i class="fa fa-user-o"></i> Stay in sync with your matchmaking team</li>
            </ul>
        </aside>

        <div class="mk-main">
            <span class="mk-eyebrow">Become a Partner</span>
            <h1 class="mk-title">Join Us as a <em>Matchmaker</em></h1>
            <p class="mk-sub">Tell us a bit about yourself. Once your application is reviewed and approved, you'll get access to the Partner Dashboard to start adding and managing proposals.</p>

            @if($errors->any())
            <div class="mk-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mk-card">
                <form method="POST" action="{{ route('matchmaker.apply.store') }}" enctype="multipart/form-data">
                    @csrf
                    <label class="mk-photo" id="mkPhoto" for="mkPhotoInput">
                        <span class="mk-photo__preview" id="mkPhotoPreview"><span class="mk-photo__ph"><b>Photo</b><u>or browse<br>files</u></span></span>
                        <span>
                            <span class="mk-photo__title">Add a clear photo of yourself</span>
                            <span class="mk-photo__hint">JPG, PNG or WEBP, up to 5 MB. Drag a picture or drop it above.</span>
                            <span class="mk-photo__name" id="mkPhotoName"></span>
                            <span class="mk-photo__error" id="mkPhotoError"></span>
                            <span id="mkPhotoBtnText" hidden>Choose photo</span>
                        </span>
                        <input type="file" id="mkPhotoInput" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
                    </label>

                    <div class="mk-section" style="margin-top:26px;">Personal Details</div>
                    <div class="mk-row">
                        <div class="mk-field">
                            <label for="mk_first">First Name</label>
                            <div class="mk-input"><i class="fa fa-user-o"></i><input type="text" id="mk_first" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Ayesha" required></div>
                        </div>
                        <div class="mk-field">
                            <label for="mk_last">Last Name</label>
                            <div class="mk-input"><i class="fa fa-user-o"></i><input type="text" id="mk_last" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Malik" required></div>
                        </div>
                    </div>
                    <div class="mk-row">
                        <div class="mk-field">
                            <label for="mk_email">Email</label>
                            <div class="mk-input"><i class="fa fa-envelope-o"></i><input type="email" id="mk_email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required></div>
                        </div>
                        <div class="mk-field">
                            <label for="mk_phone">Contact Number</label>
                            <div class="mk-input"><i class="fa fa-phone"></i><input type="text" id="mk_phone" name="contact_mobile_number" value="{{ old('contact_mobile_number') }}" placeholder="+92 300 0000000" required></div>
                        </div>
                    </div>
                    <div class="mk-row">
                        <div class="mk-field">
                            <label for="mk_city">City</label>
                            <div class="mk-input"><i class="fa fa-map-marker"></i><input type="text" id="mk_city" name="city" value="{{ old('city') }}" placeholder="e.g. Lahore" required></div>
                        </div>
                        <div class="mk-field">
                            <label for="mk_exp">Experience</label>
                            <div class="mk-input"><i class="fa fa-clock-o"></i><input type="text" id="mk_exp" name="experience" value="{{ old('experience') }}" placeholder="e.g. 3 years as a matchmaker" required></div>
                        </div>
                    </div>

                    <div class="mk-section">Your Experience</div>
                    <div class="mk-row mk-row--one">
                        <div class="mk-field">
                            <label for="mk_about">About Me</label>
                            <textarea id="mk_about" name="about_me" rows="4" placeholder="Share a little about your background and why you'd like to join as a matchmaker..." required>{{ old('about_me') }}</textarea>
                        </div>
                    </div>

                    <div class="mk-section">Secure Your Account</div>
                    <div class="mk-row">
                        <div class="mk-field">
                            <label for="mk_pw">Password</label>
                            <div class="mk-input"><i class="fa fa-lock"></i><input type="password" id="mk_pw" name="password" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password"></div>
                        </div>
                        <div class="mk-field">
                            <label for="mk_pw2">Confirm Password</label>
                            <div class="mk-input"><i class="fa fa-lock"></i><input type="password" id="mk_pw2" name="password_confirmation" placeholder="Repeat your password" required minlength="8" autocomplete="new-password"></div>
                        </div>
                    </div>

                    <button type="submit" class="mk-submit">Submit Application <i class="fa fa-arrow-right"></i></button>
                    <p class="mk-terms">By applying you agree to our <a href="{{ url('tandc') }}" target="_blank" rel="noopener">Terms</a> and <a href="{{ url('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</p>
                </form>
            </div>
            <p class="mk-foot">Already a partner? <a href="{{ route('team.login') }}">Team Member Login</a> &nbsp;&middot;&nbsp; <a href="{{ route('login') }}">Member sign in</a></p>
        </div>
    </div>
</div><script>
(function () {
    var box = document.getElementById('mkPhoto'), input = document.getElementById('mkPhotoInput');
    var preview = document.getElementById('mkPhotoPreview'), nameEl = document.getElementById('mkPhotoName');
    var err = document.getElementById('mkPhotoError'), btnText = document.getElementById('mkPhotoBtnText');
    if (!box || !input) return;

    function reset(message) {
        input.value = '';
        box.classList.remove('has-file');
        preview.style.backgroundImage = '';
        btnText.textContent = 'Choose photo';
        err.textContent = message || '';
        err.style.display = message ? 'block' : 'none';
    }
    function show(file) {
        if (!file) return reset();
        if (!/^image\/(jpeg|png|webp|gif)$/.test(file.type)) return reset('Please choose a JPG, PNG, WEBP or GIF picture.');
        if (file.size > 5 * 1024 * 1024) return reset('That picture is larger than 5 MB. Please choose a smaller one.');
        err.style.display = 'none';
        var reader = new FileReader();
        reader.onload = function (e) { preview.style.backgroundImage = 'url(' + e.target.result + ')'; };
        reader.readAsDataURL(file);
        nameEl.textContent = file.name;
        btnText.textContent = 'Change photo';
        box.classList.add('has-file');
    }
    input.addEventListener('change', function () { show(input.files[0]); });
    ['dragenter', 'dragover'].forEach(function (t) { box.addEventListener(t, function (e) { e.preventDefault(); box.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (t) { box.addEventListener(t, function (e) { e.preventDefault(); box.classList.remove('is-over'); }); });
    box.addEventListener('drop', function (e) {
        if (e.dataTransfer && e.dataTransfer.files.length) { input.files = e.dataTransfer.files; show(input.files[0]); }
    });
})();
</script>
@endsection
