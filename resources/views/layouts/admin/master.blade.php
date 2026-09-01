@extends('layouts.master')
@section('main-content')
<div class="hidden_xs">
    <nav class="navbar navbar-expand-lg  navbar--style-1 navbar-light bg-default navbar--shadow navbar--uppercase profile-nav">
        <div class="container navbar-container">
            <!-- Brand/Logo -->

            <div class="d-inline-block">
                <!-- Navbar toggler  -->
                <button class="navbar-toggler hamburger hamburger-js hamburger--spring" type="button" data-toggle="collapse" data-target="#admin_navbar_main" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"><span style="padding-left: 30px; vertical-align: top; white-space: nowrap; font-size: 14px;">ADMIN DASHBOARD</span></div>
                    </div>
                </button>
            </div>
            <div class="collapse navbar-collapse justify-content-between align-items-center" id="admin_navbar_main">
                <ul class="navbar-nav" data-hover="dropdown" data-animations="zoomIn zoomIn zoomIn zoomIn">
                    <li class="nav-item">
                        <a data-url="{{url('admin/profiles')}}" class="nav-link admin-link p_nav active">
                            <i class="fa fa-user"></i>
                            Profiles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-url="{{url('admin/interests')}}" class="nav-link admin-link p_nav">
                            <i class="fa fa-heart"></i>
                            Interests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a      data-url="{{url('admin/packages')}}" class="nav-link admin-link p_nav">
                            <i class="fa fa-list-ul"></i>
                            Packages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-url="{{url('admin/package-subscribers')}}" class="nav-link admin-link p_nav">
                            <i class="fa fa-users"></i>
                            Package Subscribers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-url="{{url('admin/appointments')}}" class="nav-link admin-link p_nav">
                            <i class="fa fa-calendar"></i>
                            Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-url="{{url('admin/photo-verification')}}" class="nav-link admin-link p_nav">
                            <i class="fa fa-id-badge"></i>
                            Photo Verification
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</div>
<div id="admin-content">
@yield('admin-content')
</div>
<script type="text/javascript">
    $(".admin-link").on("click", (e)=>{
        e.preventDefault();
        $(".admin-link").removeClass("active");
        $(e.target).addClass("active");
        renderPage($(e.target).data('url'), "get", null, $("#admin-content"));
    });
</script>
@endsection
