@extends('layouts.dashboard')
@section('dashboard-title', 'Change Password')
@push('styles')
<link rel="stylesheet" href="/css/ur-change-password.css?1">
@endpush
@section('main-content')

<div class="ur-changepw">
    <div class="ur-changepw__card">
        <h2 class="ur-changepw__title">Change Password</h2>
        <p class="ur-changepw__sub">You'll be logged out and asked to sign in again with your new password.</p>

        <form class="ur-changepw__form" id="change_password_form" role="form" method="post" action="{{ route('change.password') }}">
            @csrf
            <div class="ur-changepw__field">
                <label>Current Password</label>
                <input type="password" class="ur-changepw__input" name="current_password" autofocus required value="" />
            </div>
            <div class="ur-changepw__field">
                <label>New Password</label>
                <input type="password" class="ur-changepw__input" name="password" id="password" required minlength="8" value="" />
            </div>
            <div class="ur-changepw__field">
                <label>Confirm New Password</label>
                <input type="password" class="ur-changepw__input" name="password_confirmation" id="password1" required minlength="8" value="" />
            </div>
            <button type="submit" id="btnSubmit" class="ur-changepw__submit">Change Password</button>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#change_password_form").on('submit', (e) => {
            $("#btnSubmit").html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            checkPassword();
        });

        $("#password1").on('input', function() {
            checkPassword();
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
