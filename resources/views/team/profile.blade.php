{{--
    Team member "My Profile" — two cards side by side: Basic info (photo, name, contact, experience, about) and the
    watermark that goes on this member's proposal photos (logo, text, style). One form (#pf_form); Save Changes sits in
    the page header. See TeamController::profile() / profileUpdate().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Profile')
@section('main-content')
<style>
    .ur-pf-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-pf-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 30px; margin: 0; line-height: 1.2 !important; }
    .ur-pf-head__right { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
    .ur-pf-save { height: 44px; padding: 0 26px; border-radius: 12px; background: #C9974D; color: #fff; border: 0; font-size: 14px; font-weight: 700; cursor: pointer; }
    .ur-pf-save:hover { background: #B88640; }
    .ur-pf-head__link { font-size: 14px; font-weight: 700; color: #A8731F !important; text-decoration: underline; }

    .ur-pf-grid { display: grid; grid-template-columns: minmax(0, 1.12fr) minmax(0, 1fr); gap: 22px; align-items: start; }
    .ur-pf-card { background: #fff; border-radius: 22px; padding: 24px; box-shadow: 0 2px 14px rgba(15,46,36,.06); }
    .ur-pf-card__label { font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #8A938E; margin: 0 0 16px; }
    .ur-pf-card label { display: block; font-size: 13.5px; font-weight: 700; color: #1C2321; margin: 0 0 7px; }
    .ur-pf-card label small { font-weight: 400; color: #6B7570; font-size: 12px; }
    .ur-pf-card input[type=text], .ur-pf-card textarea, .ur-pf-card select { width: 100%; border: 1px solid #DDD5C2; border-radius: 12px; padding: 0 14px; font-size: 14.5px; color: #1C2321; background: #fff; font-family: inherit; outline: none; transition: border-color .15s, box-shadow .15s; }
    .ur-pf-card input[type=text], .ur-pf-card select { height: 46px; }
    .ur-pf-card textarea { padding: 12px 14px; min-height: 96px; resize: vertical; }
    .ur-pf-card input[type=text]:focus, .ur-pf-card textarea:focus, .ur-pf-card select:focus { border-color: #C9974D; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .ur-pf-card input[disabled] { background: #F1EEE6; color: #6B7570; }
    .ur-pf-field { margin-bottom: 16px; }
    .ur-pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ur-pf-err { color: #B5674A; font-size: 12.5px; margin-top: 5px; }

    .ur-pf-photo { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
    .ur-pf-avatar { width: 58px; height: 58px; border-radius: 50%; background: #123A2E; color: #fff; font-weight: 800; font-size: 17px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; object-fit: cover; }
    .ur-pf-file { font-size: 13px; color: #6B7570; }
    .ur-pf-file input[type=file], .ur-pf-card input[type=file] { font-size: 13px; color: #6B7570; max-width: 100%; }
    .ur-pf-card input[type=file]::file-selector-button { height: 36px; padding: 0 16px; margin-right: 10px; border: 1px solid #DDD5C2; border-radius: 9px; background: #F3EFE6; color: #1C2321; font-size: 13.5px; font-weight: 600; cursor: pointer; }
    .ur-pf-card input[type=file]::file-selector-button:hover { background: #EAE4D5; }

    .ur-pf-col { display: grid; gap: 22px; align-content: start; min-width: 0; }
    .ur-pf-prefix-preview { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #F6F4EF; border-radius: 14px; padding: 14px 16px; }
    .ur-pf-prefix-preview span { font-size: 13px; color: #4B5651; }
    .ur-pf-prefix-preview b { font-family: 'Manrope', system-ui, sans-serif; font-size: 22px; font-weight: 800; color: #123A2E; letter-spacing: .03em; }
    .ur-pf-wm-title { display: flex; align-items: center; gap: 10px; font-size: 15.5px; font-weight: 800; color: #123A2E; margin: 0 0 6px; }
    .ur-pf-wm-title i { color: #E4572E; }
    .ur-pf-wm-sub { font-size: 13px; color: #6B7570; line-height: 1.55; margin: 0 0 18px; }
    .ur-pf-logo { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
    .ur-pf-logo__box { width: 58px; height: 58px; border-radius: 12px; background: #F6F4EF; border: 1px dashed #D9CFB8; display: flex; align-items: center; justify-content: center; color: #B9A988; font-size: 22px; flex-shrink: 0; overflow: hidden; }
    .ur-pf-logo__box img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
    .ur-pf-hint { font-size: 12.5px; color: #6B7570; line-height: 1.55; margin: 2px 0 0; }
    .ur-pf-remove { display: flex !important; align-items: center; gap: 7px; font-weight: 500 !important; font-size: 13px !important; margin: 8px 0 0 !important; }
    .ur-pf-remove input { width: auto; }

    @media (max-width: 991px) { .ur-pf-grid { grid-template-columns: 1fr; } }
    @media (max-width: 560px) {
        .ur-pf-head h1 { font-size: 24px; }
        .ur-pf-head__right { width: 100%; justify-content: space-between; }
        .ur-pf-card { padding: 18px; border-radius: 18px; }
        .ur-pf-row { grid-template-columns: 1fr; gap: 0; }
    }
</style>

@php
    $initials = strtoupper(substr($member->first_name ?? '', 0, 1) . substr($member->last_name ?? '', 0, 1)) ?: 'TM';
    $hasPhoto = $member->photo && file_exists(public_path(\App\TeamMember::PHOTO_PATH . '/thumbnail_' . $member->photo));
    $hasLogo = $member->logo && file_exists(public_path(\App\TeamMember::PHOTO_PATH . '/logos/' . $member->logo));
@endphp

<form method="POST" action="{{ route('team.profile.update') }}" enctype="multipart/form-data" id="pf_form">
    @csrf
    <div class="ur-pf-head">
        <h1>My Profile</h1>
        <div class="ur-pf-head__right">
            <button type="submit" class="ur-pf-save">Save Changes</button>
            <a href="{{ route('team.password') }}" class="ur-pf-head__link">Change password</a>
        </div>
    </div>

    <div class="ur-pf-grid">
        <div class="ur-pf-card">
            <div class="ur-pf-card__label">Basic info</div>

            <div class="ur-pf-photo">
                @if($hasPhoto)
                    <img class="ur-pf-avatar" src="{{ $member->getProfileImage() }}" alt="">
                @else
                    <span class="ur-pf-avatar">{{ $initials }}</span>
                @endif
                <div class="ur-pf-file">
                    <input type="file" id="image" name="image" accept="image/*">
                    @error('image')<div class="ur-pf-err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ur-pf-row">
                <div class="ur-pf-field">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
                    @error('first_name')<div class="ur-pf-err">{{ $message }}</div>@enderror
                </div>
                <div class="ur-pf-field">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required>
                    @error('last_name')<div class="ur-pf-err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ur-pf-row">
                <div class="ur-pf-field">
                    <label for="contact_mobile_number">Mobile number</label>
                    <input type="text" id="contact_mobile_number" name="contact_mobile_number" value="{{ old('contact_mobile_number', $member->contact_mobile_number) }}" required>
                    @error('contact_mobile_number')<div class="ur-pf-err">{{ $message }}</div>@enderror
                </div>
                <div class="ur-pf-field">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $member->city) }}">
                    @error('city')<div class="ur-pf-err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ur-pf-field">
                <label>Email <small>(contact an admin to change)</small></label>
                <input type="text" value="{{ $member->email }}" disabled>
            </div>

            <div class="ur-pf-field">
                <label for="experience">Experience</label>
                <input type="text" id="experience" name="experience" value="{{ old('experience', $member->experience) }}" placeholder="e.g. 3 years as a matchmaker">
                @error('experience')<div class="ur-pf-err">{{ $message }}</div>@enderror
            </div>

            <div class="ur-pf-field" style="margin-bottom:0;">
                <label for="about_me">About me</label>
                <textarea id="about_me" name="about_me">{{ old('about_me', $member->about_me) }}</textarea>
                @error('about_me')<div class="ur-pf-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="ur-pf-col">
        <div class="ur-pf-card">
            <div class="ur-pf-wm-title"><i class="fa fa-tint"></i> Watermark on proposal photos</div>
            <p class="ur-pf-wm-sub">Shown on every shared photo: your name, proposal ID, and optional logo/text. Stored photos are never changed.</p>

            <div class="ur-pf-logo">
                <span class="ur-pf-logo__box">
                    @if($hasLogo)<img src="/{{ \App\TeamMember::PHOTO_PATH }}/logos/{{ $member->logo }}" alt="Logo">@else<i class="fa fa-picture-o"></i>@endif
                </span>
                <div class="ur-pf-file">
                    <label for="logo" style="margin-bottom:4px;">Logo <small>(PNG, up to 3 MB)</small></label>
                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp">
                    @error('logo')<div class="ur-pf-err">{{ $message }}</div>@enderror
                    @if($member->logo)
                        <label class="ur-pf-remove"><input type="checkbox" name="remove_logo" value="1"> Remove my logo</label>
                    @endif
                </div>
            </div>

            <div class="ur-pf-field">
                <label for="watermark_text">Watermark text</label>
                <input type="text" id="watermark_text" name="watermark_text" maxlength="100" value="{{ old('watermark_text', $member->watermark_text) }}" placeholder="e.g. Ali Matchmaking · 0300 1234567">
                @error('watermark_text')<div class="ur-pf-err">{{ $message }}</div>@enderror
            </div>

            <div class="ur-pf-field" style="margin-bottom:0;">
                <label for="watermark_style">Style</label>
                <select id="watermark_style" name="watermark_style">
                    @foreach(\App\Services\PhotoBrandingService::STYLES as $value => $label)
                        <option value="{{ $value }}" {{ old('watermark_style', $member->watermark_style ?: 'diagonal') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="ur-pf-hint" style="margin-top:10px;">Diagonal pattern repeats your logo, text and "Name · Proposal ID" faintly across the photo. Centre mark + corner badge puts one large faint logo and text in the middle and a small badge with your name and the proposal ID in the corner. With no logo or text, only your name and the proposal ID are shown.</p>
            </div>
        </div>

        <div class="ur-pf-card">
            <div class="ur-pf-wm-title"><i class="fa fa-hashtag" style="color:#C9974D;"></i> Proposal ID</div>
            <p class="ur-pf-wm-sub">Choose the letters your proposals start with. The number after them continues the shared sequence automatically.</p>
            <div class="ur-pf-field" style="margin-bottom:12px;">
                <label for="proposal_prefix">Letters <small>(1 to 3 letters, e.g. P, UR, CV)</small></label>
                <input type="text" id="proposal_prefix" name="proposal_prefix" maxlength="3" value="{{ old('proposal_prefix', $member->proposalPrefix()) }}" placeholder="P" autocomplete="off" style="text-transform:uppercase; letter-spacing:.06em; font-weight:700;">
                @error('proposal_prefix')<div class="ur-pf-err">{{ $message }}</div>@enderror
            </div>
            <div class="ur-pf-prefix-preview">
                <span>Your next proposal will be</span>
                <b id="pf_prefix_preview">{{ old('proposal_prefix', $member->proposalPrefix()) }}-{{ str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT) }}</b>
            </div>
            <p class="ur-pf-hint" style="margin-top:10px;">Only proposals you add from now on use the new letters. The IDs of proposals you already added stay the same, so shared links and photo watermarks keep working.</p>
        </div>
        </div>
    </div>
</form>

<script>
    // live preview of the next proposal ID while typing the letters
    (function () {
        var input = document.getElementById('proposal_prefix'), out = document.getElementById('pf_prefix_preview');
        if (!input || !out) return;
        var number = "{{ str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT) }}";
        input.addEventListener('input', function () {
            input.value = input.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 3);
            out.textContent = (input.value || 'P') + '-' + number;
        });
    })();
</script>
@endsection
