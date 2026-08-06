@extends('layouts.master')

@section('main-content')
<section class="slice-lg has-bg-cover bg-size-cover" style="background-image: url('/images/parallax-23860.jpg'); background-position: bottom bottom;">
    <span class="mask mask-dark--style-2"></span>
    <div class="container">
        <div class="row cols-xs-space align-items-center text-center text-md-left">
            <div class="col-lg-4 col-md-6 ml-auto mr-auto">
                <div class="form-card form-card--style-2 z-depth-3-top">
                    <div class="form-body">
                        <div class="text-center px-2">
                            <h4 class="heading heading-4 strong-400 mb-4 font_light">Log In To Your Account</h4>
                        </div>
                        <form class="form-default" role="form" method="post" action="{{route('login')}}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="text-uppercase font_light">Email</label>
                                        <input type="email" class="form-control input-sm" name="email" autofocus required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group has-feedback">
                                        <label class="text-uppercase font_light">Password</label>
                                        <input type="password" class="form-control input-sm" name="password" required>
                                    </div>
                                    <p style="color: red">
                                    </p>
                                    <p style="color: green">
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-styled btn-sm btn-block btn-base-1 z-depth-2-bottom mt-4">LOG IN</button>
                                </div>
                            </div>
                            <div class="row pt-3">
                                <div class="col-6" style="font-size: 12px;">
                                    <div class="checkbox">
                                        <input type="checkbox" name="remember" id="remember" value="checked">
                                        <label for="remember"><span class="c-gray-light">Remember Me</span></label>
                                    </div>
                                </div>
                                <div class="col-6 text-right" style="font-size: 12px;">
                                    <a href="{{ route('password.request') }}" class="c-gray-light">Recover Password</a>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12 text-center" style="font-size: 12px;">
                                <span class="c-gray-light">New Here?</span><a class="c-gray-light" href="{{url('register')}}"> <u>Create An Account From Here!</u></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@if(session('verified'))
    <h1>You've successfully verified your email!</h1>
@endif
<!-- <h1>@if (Session::getId()) {{ Session::getId()}} @else thenga @endif</h1>
<-?php echo '<pre>';var_dump(session()->all());echo '</pre>'; ?> -->
@endsection
