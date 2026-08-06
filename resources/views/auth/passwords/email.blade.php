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
                        <h4 class="heading heading-4 strong-400 mb-4 font_light">Reset Password</h4>
                    </div>
                    <form class="form-default" id="reset_link_form" role="form" method="post" action="{{ route('password.email') }}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="text-uppercase font_light">Email</label>
                                    <input type="email" class="form-control input-sm" name="email" autofocus required="required">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" id="btnSubmit" class="btn btn-styled btn-sm btn-block btn-base-1 z-depth-2-bottom mt-4">SEND RESET PASSWORD LINK</button>
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

        $("#reset_link_form").on('submit', (e) => {
            var parent = $("#btnSubmit").parent();
            parent.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        });
    });
</script>
@endsection
