@extends('layouts.master')

@section('main-content')
<style>
    .mk-page {
        --mk-green: #123A2E;
        --mk-gold: #C9974D;
        --mk-cream: #FBF7EF;
        --mk-line: #E7E2D6;
        --mk-text: #5B6560;
        --mk-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--mk-cream);
        color: var(--mk-ink);
        padding: 60px 20px 80px;
    }
    .mk-wrap { max-width: 640px; margin: 0 auto; }
    .mk-eyebrow { font-size: 11.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--mk-gold); display: block; margin-bottom: 10px; }
    .mk-title { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 32px; color: var(--mk-green); margin: 0 0 10px; }
    .mk-sub { font-size: 14.5px; color: var(--mk-text); line-height: 1.6; margin: 0 0 32px; }
    .mk-card { background: #fff; border: 1px solid var(--mk-line); border-radius: 16px; padding: 32px; }
    .mk-row { display: flex; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
    .mk-field { flex: 1 1 200px; }
    .mk-field label { display: block; font-size: 13px; font-weight: 700; color: var(--mk-ink); margin-bottom: 6px; }
    .mk-field label .mk-opt { font-weight: 400; font-size: 11px; color: #9AA5A0; }
    .mk-field input, .mk-field textarea { width: 100%; border: 1px solid var(--mk-line); border-radius: 8px; height: 44px; padding: 0 14px; font-size: 14px; font-family: inherit; }
    .mk-field textarea { height: auto; padding: 12px 14px; resize: vertical; }
    .mk-submit { width: 100%; height: 50px; border-radius: 999px; background: var(--mk-gold); color: #1C2321; border: none; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 8px; }
    .mk-submit:hover { background: #B07C3D; }
    .mk-errors { background: #FCEBE8; border: 1px solid #E6968C; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: 13.5px; color: #B5674A; }
    .mk-errors ul { margin: 0; padding-left: 18px; }

    /* nicer fields */
    .mk-card { box-shadow: 0 18px 40px rgba(15,46,36,.07); }
    .mk-field input, .mk-field textarea { transition: border-color .15s ease, box-shadow .15s ease; background: #fff; }
    .mk-field input:focus, .mk-field textarea:focus { outline: none; border-color: var(--mk-gold); box-shadow: 0 0 0 3px rgba(201,151,77,.18); }

    /* photo upload */
    .mk-field label.mk-photo { display: flex; margin: 0; font-size: inherit; font-weight: 400; color: inherit; }
    .mk-photo { display: flex; align-items: center; gap: 20px; border: 2px dashed #D9CFB8; border-radius: 14px; background: #FDFBF6; padding: 18px 20px; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
    .mk-photo:hover, .mk-photo.is-over { border-color: var(--mk-gold); background: #FBF4E6; }
    .mk-photo.has-file { border-style: solid; border-color: var(--mk-green); background: #F4F8F5; }
    .mk-photo__preview { flex: 0 0 92px; width: 92px; height: 92px; border-radius: 50%; background: #EFE7D6 center / cover no-repeat; border: 3px solid #fff; box-shadow: 0 0 0 1px var(--mk-line); display: flex; align-items: center; justify-content: center; color: #B9A988; font-size: 34px; overflow: hidden; }
    .mk-photo.has-file .mk-photo__preview i { display: none; }
    .mk-photo__text { flex: 1; min-width: 0; }
    .mk-photo__title { font-size: 14.5px; font-weight: 700; color: var(--mk-green); margin: 0 0 3px; }
    .mk-photo__hint { font-size: 12.5px; color: var(--mk-text); margin: 0 0 10px; line-height: 1.5; }
    .mk-photo__btn { display: inline-flex; align-items: center; gap: 8px; background: var(--mk-green); color: #fff; border-radius: 999px; padding: 8px 18px; font-size: 13px; font-weight: 700; }
    .mk-photo__name { display: none; font-size: 12.5px; color: var(--mk-green); font-weight: 600; margin-top: 8px; word-break: break-all; }
    .mk-photo.has-file .mk-photo__name { display: block; }
    .mk-photo__error { display: none; color: #B5674A; font-size: 12.5px; margin-top: 8px; font-weight: 600; }
    .mk-photo input[type=file] { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
    @media (max-width: 480px) { .mk-photo { flex-direction: column; text-align: center; } }
</style>

<div class="mk-page">
    <div class="mk-wrap">
        <span class="mk-eyebrow">Become a Partner</span>
        <h1 class="mk-title">Join Us as a Matchmaker</h1>
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
                <div class="mk-row">
                    <div class="mk-field">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="mk-field">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                </div>
                <div class="mk-row">
                    <div class="mk-field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mk-field">
                        <label>Contact Number</label>
                        <input type="text" name="contact_mobile_number" value="{{ old('contact_mobile_number') }}" required>
                    </div>
                </div>
                <div class="mk-row">
                    <div class="mk-field">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city') }}" required>
                    </div>
                    <div class="mk-field">
                        <label>Experience</label>
                        <input type="text" name="experience" value="{{ old('experience') }}" placeholder="e.g. 3 years as a matchmaker" required>
                    </div>
                </div>
                <div class="mk-row">
                    <div class="mk-field" style="flex:1 1 100%;">
                        <label>About Me</label>
                        <textarea name="about_me" rows="4" required>{{ old('about_me') }}</textarea>
                    </div>
                </div>
                <div class="mk-row">
                    <div class="mk-field" style="flex:1 1 100%;">
                        <label>Your Photo</label>
                        <label class="mk-photo" id="mkPhoto" for="mkPhotoInput" style="margin:0; cursor:pointer;">
                            <span class="mk-photo__preview" id="mkPhotoPreview"><i class="fa fa-user"></i></span>
                            <span class="mk-photo__text">
                                <span class="mk-photo__title" style="display:block;">Add a clear photo of yourself</span>
                                <span class="mk-photo__hint" style="display:block;">JPG, PNG or WEBP, up to 5 MB. Drag a picture here or choose one from your device.</span>
                                <span class="mk-photo__btn"><i class="fa fa-camera"></i> <span id="mkPhotoBtnText" class="text-white">Choose photo</span></span>
                                <span class="mk-photo__name" id="mkPhotoName"></span>
                                <span class="mk-photo__error" id="mkPhotoError"></span>
                            </span>
                            <input type="file" id="mkPhotoInput" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
                        </label>
                    </div>
                </div>
                <div class="mk-row">
                    <div class="mk-field">
                        <label>Password</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div class="mk-field">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="mk-submit">Submit Application</button>
            </form>
        </div>
    </div>
</div>
<script>
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
