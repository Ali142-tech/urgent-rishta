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
                            <h4 class="heading heading-4 strong-400 mb-4 font_light">Verify your Email Address</h4>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <form class="form-default" id="resend_form" role="form" method="post" action="{{ route('verification.resend') }}">
                                    @csrf
                                </form>
                                <p>Before proceeding, please check your email for a verification link. If you did not receive the email,
                                    <a href="#" onclick="javascript:$('#resend_form').submit();">click here to request another<a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        @if(session('resent'))
        successAlert('A fresh verification link has been sent to your email address.');
        @endif
    });
</script>
@endsection
