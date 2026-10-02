@extends('layouts.team.dashboard')
@section('dashboard-title', 'Change Password')
@section('main-content')
<style>
    .ur-pw-card { max-width: 460px; background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 24px; }
    .ur-pw-card label { display: block; font-size: 13px; font-weight: 700; color: #1C2321; margin: 0 0 6px; }
    .ur-pw-card input { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; font-size: 14px; margin-bottom: 16px; }
    .ur-pw-card button { height: 44px; padding: 0 26px; border-radius: 999px; background: #C9974D; color: #1C2321; border: none; font-weight: 700; cursor: pointer; }
    .ur-pw-err { color: #B5674A; font-size: 12.5px; margin: -10px 0 14px; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 18px;">Change Password</h1>

<form method="POST" action="{{ route('team.password.update') }}" class="ur-pw-card">
    @csrf
    <label for="current_password">Current password</label>
    <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
    @error('current_password')<div class="ur-pw-err">{{ $message }}</div>@enderror

    <label for="password">New password</label>
    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
    @error('password')<div class="ur-pw-err">{{ $message }}</div>@enderror

    <label for="password_confirmation">Confirm new password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">

    <button type="submit">Update Password</button>
</form>
@endsection
