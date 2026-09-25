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
                        <input type="file" name="image" accept="image/*" required>
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
@endsection
