{{--
    Which contact fields render once a viewer passes
    User::canViewContactInfoOf() — see AdminController::contactUnlockSettings().
    The gate itself stays all-or-nothing; these only prune which fields show.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Contact Unlock Settings')
@section('main-content')
<style>
    .ur-cu-form { max-width: 560px; }
    .ur-cu-form h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 22px; margin: 0 0 8px; }
    .ur-cu-form p.ur-cu-note { color: #6B7570; font-size: 13.5px; margin: 0 0 22px; }
    .ur-cu-form .row { margin-bottom: 14px; display: flex; align-items: center; gap: 12px; }
    .ur-cu-form label { font-size: 14px; font-weight: 600; color: #1C2321; }
    .ur-cu-form .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 28px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; margin-top: 10px; }
    .ur-cu-form .ur-submit-btn:hover { background: #B07C3D; }
</style>

<div class="ur-cu-form">
    <h1>Contact Unlock Settings</h1>
    <p class="ur-cu-note">Only the fields checked here are ever shown to a team member viewing another team-added proposal. Unchecking a field hides it for everyone.</p>

    <form method="POST" action="{{ route('admin.contact-unlock-settings.update') }}">
        @csrf
        @php
            $labels = ['unlock_email' => 'Email', 'unlock_phone' => 'Phone', 'unlock_name' => 'Full Name', 'unlock_address' => 'Address'];
        @endphp
        @foreach($labels as $field => $label)
        <div class="row">
            <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" {{ $settings[$field] ? 'checked' : '' }}>
            <label for="{{ $field }}">{{ $label }}</label>
        </div>
        @endforeach

        <button type="submit" class="ur-submit-btn"><i class="fa fa-check"></i> Save Settings</button>
    </form>
</div>
@endsection
