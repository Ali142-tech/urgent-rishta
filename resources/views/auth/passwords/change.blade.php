@extends('layouts.master')
@section('main-content')
<section class="slice-lg has-bg-cover bg-size-cover" style="background-image: url(/images/Elite-bg1.jpg); background-position: bottom bottom;">
    <span class="mask mask-dark--style-2"></span>
    <div class="container">
        <div class="row cols-xs-space align-items-center text-center text-md-left">
            <div class="col-lg-4 col-md-6 ml-auto mr-auto">
                <div class="form-card form-card--style-2 z-depth-3-top">
                    <div class="form-body">
                        <div class="text-center px-2">
                            <h4 class="heading heading-4 strong-400 mb-4 font_light">Change Password</h4>
                        </div>
                        <form class="form-default" id="change_password_form" role="form" method="post" action="{{ route('change.password') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="text-uppercase font_light">Current Password</label>
                                        <input type="password" class="form-control input-sm" name="current_password" autofocus required="required" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label font_light">Password</label>
                                        <input type="password" class="form-control form-control-sm" name="password" id="password" required="required" minlength="8" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group has-feedback">
                                        <label class="control-label font_light">Confirm Password </label>
                                        <input type="password" class="form-control form-control-sm" name="password_confirmation" id="password1" required="required" minlength="8" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" id="btnSubmit" class="btn btn-styled btn-sm btn-block btn-base-1 z-depth-2-bottom mt-4">CHANGE PASSWORD</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {

        $("#change_password_form").on('submit', (e) => {
            var parent = $("#btnSubmit").parent();
            parent.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
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
