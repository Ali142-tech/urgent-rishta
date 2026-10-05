@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Profile')
@section('main-content')
<style>
    .ur-pf-card { max-width: 620px; background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 24px; }
    .ur-pf-card label { display: block; font-size: 13px; font-weight: 700; color: #1C2321; margin: 0 0 6px; }
    .ur-pf-card input[type=text], .ur-pf-card textarea { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; padding: 0 12px; font-size: 14px; margin-bottom: 16px; font-family: inherit; }
    .ur-pf-card input[type=text] { height: 42px; }
    .ur-pf-card textarea { padding: 10px 12px; min-height: 90px; }
    .ur-pf-card input[disabled] { background: #F6F4EF; color: #6B7570; }
    .ur-pf-row { display: flex; gap: 14px; flex-wrap: wrap; }
    .ur-pf-row > div { flex: 1 1 220px; }
    .ur-pf-photo { display: flex; align-items: center; gap: 16px; margin-bottom: 18px; }
    .ur-pf-photo img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 1px solid #E7E2D6; }
    .ur-pf-card button { height: 44px; padding: 0 26px; border-radius: 999px; background: #C9974D; color: #1C2321; border: none; font-weight: 700; cursor: pointer; }
    .ur-pf-err { color: #B5674A; font-size: 12.5px; margin: -10px 0 14px; }
    .ur-pf-links { margin-top: 14px; font-size: 13.5px; }
    .ur-pf-brand { border-top: 1px dashed #E7E2D6; margin: 6px 0 20px; padding-top: 18px; }
    .ur-pf-brand__title { font-size: 15px; font-weight: 800; color: #123A2E; margin-bottom: 4px; }
    .ur-pf-brand__title i { color: #C9974D; margin-right: 6px; }
    .ur-pf-brand__sub, .ur-pf-brand__hint { font-size: 12.5px; color: #6B7570; line-height: 1.5; margin: 0 0 14px; }
    .ur-pf-brand__hint { margin: -6px 0 6px; }
    .ur-pf-logo-empty { width: 72px; height: 72px; border-radius: 12px; background: #F6F4EF; border: 1px dashed #D9CFB8; display: flex; align-items: center; justify-content: center; color: #B9A988; font-size: 26px; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 18px;">My Profile</h1>

<form method="POST" action="{{ route('team.profile.update') }}" enctype="multipart/form-data" class="ur-pf-card">
    @csrf
    <div class="ur-pf-photo">
        <img src="{{ $member->getProfileImage() }}" alt="">
        <div>
            <label for="image">Profile photo</label>
            <input type="file" id="image" name="image" accept="image/*">
            @error('image')<div class="ur-pf-err">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="ur-pf-row">
        <div>
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
            @error('first_name')<div class="ur-pf-err">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required>
            @error('last_name')<div class="ur-pf-err">{{ $message }}</div>@enderror
        </div>
    </div>

    <label>Email <small style="font-weight:400; color:#6B7570;">(used to sign in &mdash; contact an admin to change it)</small></label>
    <input type="text" value="{{ $member->email }}" disabled>

    <div class="ur-pf-row">
        <div>
            <label for="contact_mobile_number">Mobile number</label>
            <input type="text" id="contact_mobile_number" name="contact_mobile_number" value="{{ old('contact_mobile_number', $member->contact_mobile_number) }}" required>
            @error('contact_mobile_number')<div class="ur-pf-err">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="city">City</label>
            <input type="text" id="city" name="city" value="{{ old('city', $member->city) }}">
            @error('city')<div class="ur-pf-err">{{ $message }}</div>@enderror
        </div>
    </div>

    <label for="experience">Experience</label>
    <input type="text" id="experience" name="experience" value="{{ old('experience', $member->experience) }}" placeholder="e.g. 3 years as a matchmaker">
    @error('experience')<div class="ur-pf-err">{{ $message }}</div>@enderror

    <label for="about_me">About me</label>
    <textarea id="about_me" name="about_me">{{ old('about_me', $member->about_me) }}</textarea>
    @error('about_me')<div class="ur-pf-err">{{ $message }}</div>@enderror

    <div class="ur-pf-brand">
        <div class="ur-pf-brand__title"><i class="fa fa-tint"></i> Watermark on your proposals' photos</div>
        <p class="ur-pf-brand__sub">Every photo of your proposals carries your watermark when it is shared or forwarded, so it stays on the picture wherever it goes. It always shows your name and the proposal ID (for example "Ali Butt · P-112"), plus your logo and text if you add them. The stored photos are never changed.</p>

        <div class="ur-pf-photo" style="margin-bottom:14px;">
            @if($member->logo && file_exists(public_path(\App\TeamMember::PHOTO_PATH . '/logos/' . $member->logo)))
                <img src="/{{ \App\TeamMember::PHOTO_PATH }}/logos/{{ $member->logo }}" alt="Logo" style="border-radius:12px; object-fit:contain; background:#F6F4EF; padding:6px;">
            @else
                <span class="ur-pf-logo-empty"><i class="fa fa-picture-o"></i></span>
            @endif
            <div>
                <label for="logo">Logo <small style="font-weight:400; color:#6B7570;">(PNG with a clear background works best, up to 3 MB)</small></label>
                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp">
                @error('logo')<div class="ur-pf-err">{{ $message }}</div>@enderror
                @if($member->logo)
                    <label style="font-weight:400; margin-top:6px;"><input type="checkbox" name="remove_logo" value="1"> Remove my logo</label>
                @endif
            </div>
        </div>

        <label for="watermark_text">Watermark text</label>
        <input type="text" id="watermark_text" name="watermark_text" maxlength="100" value="{{ old('watermark_text', $member->watermark_text) }}" placeholder="e.g. Ali Matchmaking · 0300 1234567">
        @error('watermark_text')<div class="ur-pf-err">{{ $message }}</div>@enderror
                <label for="watermark_style" style="margin-top:12px;">Style</label>
        <select id="watermark_style" name="watermark_style" style="width:100%; height:42px; border:1px solid #E7E2D6; border-radius:8px; padding:0 12px; margin-bottom:10px;">
            @foreach(\App\Services\PhotoBrandingService::STYLES as $value => $label)
                <option value="{{ $value }}" {{ old('watermark_style', $member->watermark_style ?: 'diagonal') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="ur-pf-brand__hint">Diagonal pattern repeats your logo, text and "Name · Proposal ID" faintly across the photo. Centre mark + corner badge puts one large faint logo and text in the middle and a small badge with your name and the proposal ID in the corner. If you add no logo or text, only your name and the proposal ID are shown.</p>
    </div>
    <button type="submit">Save Changes</button>
    <div class="ur-pf-links"><a href="{{ route('team.password') }}">Change password</a></div>
</form>
@endsection
