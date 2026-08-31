<?php use App\User; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="urgentrishta.com is a Global matrimonial website.">
    <meta name="keywords" content="matrimonial,urgentrishta.com">
    <meta name="author" content="urgentrishta.com">
    <meta name="revisit-after" content="2 day(s)">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="/images/header_logo2.png?1">
    <link rel="apple-touch-icon" href="/images/header_logo2.png?1">

    <link rel="stylesheet" href="/css/app.css?2"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/green/pace-theme-minimal.min.css" type="text/css" />
<!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" type="text/css" />
    <!-- Plugins -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.1.3/hamburgers.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.9.0/css/lightgallery.min.css" type="text/css" />
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" type="text/css" />
    <!-- Global style (main) -->
    <link id="stylesheet" type="text/css" href="/css/global-style-pink.css?3" rel="stylesheet" media="screen" />
    <!-- Custom style - Remove if not necessary -->
    <link type="text/css" href="/css/custom-style.css?2" rel="stylesheet" />
    <link type="text/css" href="/css/new-theme.css?2" rel="stylesheet" />
    <link type="text/css" href="/css/new-animate.min.css?2" rel="stylesheet" />
    <link type="text/css" href="/css/ur-navbar.css?14" rel="stylesheet" />
    <!-- SCRIPTS -->
    <!-- Core -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
    <!-- Plugins -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-viewport-checker/1.8.8/jquery.viewportchecker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>
    <!-- Light Gallery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.9.0/js/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-thumbnail/1.2.1/lg-thumbnail.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-video/1.3.0/lg-video.min.js"></script>


    <!-- Google Analytics -->
    <script async="" src="/js/analytics.js"></script>
    
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '/js/analytics.js', 'ga');
        ga('create', " ", 'auto');
        ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->
    <title>Urgent Rishta</title>
</head>

<body class="pace-done {{ request()->is('/') || request()->is('home') ? 'homepage' : 'normalpage' }}{{ request()->is('packages') ? ' page-packages' : '' }}{{ request()->is('package-details/*') ? ' page-package-details' : '' }}{{ request()->is('team') ? ' page-team' : '' }}">
    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <style>
    /* ---------- Toast notifications (#message_alert / showAlert()) ---------- */
    .ur-toast-stack {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: min(380px, calc(100vw - 40px));
        pointer-events: none;
    }
    .ur-toast {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fff;
        border-radius: 12px;
        padding: 16px 40px 18px 16px;
        box-shadow: 0 16px 36px rgba(15,46,36,.18), 0 2px 8px rgba(15,46,36,.08);
        border-left: 4px solid #C9974D;
        overflow: hidden;
        opacity: 0;
        transform: translateX(24px);
        transition: opacity .3s ease, transform .3s ease;
        pointer-events: auto;
    }
    .ur-toast--show { opacity: 1; transform: translateX(0); }
    .ur-toast--hide { opacity: 0; transform: translateX(24px); }
    .ur-toast--success { border-left-color: #123A2E; }
    .ur-toast--success .ur-toast__icon { color: #123A2E; }
    .ur-toast--danger { border-left-color: #B5674A; }
    .ur-toast--danger .ur-toast__icon { color: #B5674A; }
    .ur-toast--warning { border-left-color: #C9974D; }
    .ur-toast--warning .ur-toast__icon { color: #C9974D; }
    .ur-toast--info { border-left-color: #5B6560; }
    .ur-toast--info .ur-toast__icon { color: #5B6560; }
    .ur-toast__icon {
        font-size: 20px;
        line-height: 1.4;
        flex-shrink: 0;
    }
    .ur-toast__body {
        font-family: 'Manrope', system-ui, sans-serif;
        font-size: 13.5px;
        line-height: 1.55;
        color: #1C2321;
        word-break: break-word;
    }
    .ur-toast__body div { margin-bottom: 4px; }
    .ur-toast__body div:last-child { margin-bottom: 0; }
    .ur-toast__close {
        position: absolute;
        top: 8px;
        right: 10px;
        border: none;
        background: transparent;
        font-size: 18px;
        line-height: 1;
        color: #9AA5A0;
        cursor: pointer;
        padding: 4px;
    }
    .ur-toast__close:hover { color: #1C2321; }
    .ur-toast__bar {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 100%;
        background: currentColor;
        color: #C9974D;
        opacity: .35;
        transform-origin: left;
        animation: urToastShrink linear forwards;
    }
    .ur-toast--success .ur-toast__bar { color: #123A2E; }
    .ur-toast--danger .ur-toast__bar { color: #B5674A; }
    .ur-toast--info .ur-toast__bar { color: #5B6560; }
    @keyframes urToastShrink {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }
    @media (max-width: 500px) {
        .ur-toast-stack { top: 70px; right: 10px; left: 10px; max-width: none; }
    }

    @media screen and (max-width: 500px) {
    ul.navbar-nav {
        background-color: #0F2E24;
        
    }
    ul.navbar-nav a.nav-link.admin-link.p_nav.active{
        color: white !important;
    }
    }
    {{-- .ur-footer-touch / .ur-footer-social CSS now lives in layouts/partials/footer.blade.php --}}
        .normalpage img.img-responsive {
            filter: drop-shadow(0px 0px) drop-shadow(2px 4px 6px #C9974D);
        }   
    }
    .navbar-light .navbar-nav .nav-link {
  color: white;
  background: transparent; }
    .c-base-1 {
  color: white; }
  .navbar.bg-default {
  background: #0F2E24;
  /*border-bottom: 1px solid #f1f1f1;*/ }
    .navbar-brand{
        width:190px;
        height:114px;
    }
    .navbar-brand img{
        width:100%;
    }
        #loading-center {
            width: 100%;
            height: 100%;
            position: relative;
        }

        #loading-center-absolute {
            position: absolute;
            left: 50%;
            top: 50%;
            height: 50px;
            width: 150px;
            margin-top: -25px;
            margin-left: -75px;
        }

        .object {
            width: 8px;
            height: 50px;
            margin-right: 5px;
            background-color: white;
            -webkit-animation: animate 1s infinite;
            animation: animate 1s infinite;
            float: left;
        }

        .object:last-child {
            margin-right: 0px;
        }

        .object:nth-child(10) {
            -webkit-animation-delay: 0.9s;
            animation-delay: 0.9s;
        }

        .object:nth-child(9) {
            -webkit-animation-delay: 0.8s;
            animation-delay: 0.8s;
        }

        .object:nth-child(8) {
            -webkit-animation-delay: 0.7s;
            animation-delay: 0.7s;
        }

        .object:nth-child(7) {
            -webkit-animation-delay: 0.6s;
            animation-delay: 0.6s;
        }

        .object:nth-child(6) {
            -webkit-animation-delay: 0.5s;
            animation-delay: 0.5s;
        }

        .object:nth-child(5) {
            -webkit-animation-delay: 0.4s;
            animation-delay: 0.4s;
        }

        .object:nth-child(4) {
            -webkit-animation-delay: 0.3s;
            animation-delay: 0.3s;
        }

        .object:nth-child(3) {
            -webkit-animation-delay: 0.2s;
            animation-delay: 0.2s;
        }

        .object:nth-child(2) {
            -webkit-animation-delay: 0.1s;
            animation-delay: 0.1s;
        }


        .appointment-btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    font-size: 11px;
    font-weight: 600;
    color: #ffffff !important;
    text-decoration: none;
    border-radius: 60px;
    background: linear-gradient(
            to bottom,
            #e8c27a 0%,
            #c9903c 40%,
            #b87a22 60%,
            #a96815 100%
        );
    box-shadow: inset 0 6px 10px rgba(255,255,255,0.4),
            inset 0 -6px 10px rgba(0,0,0,0.2),
            0 10px 20px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    position: relative;
    margin-top: 10px;
}

/* Mobile: never show the top header appointment (menu already has one) */
@media (max-width: 999.98px) {
    #myHeader .ur-header-actions > a.appointment-btn,
    #myHeader .ur-appt-in-actions {
        display: none !important;
    }
}

a.appointment-btn::before{
    display: none;
}

    .appointment-btn:hover {
        transform: translateY(-3px);
        box-shadow:
            inset 0 6px 12px rgba(255,255,255,0.5),
            inset 0 -6px 12px rgba(0,0,0,0.25),
            0 15px 25px rgba(0,0,0,0.3);
    }

    .appointment-btn:active {
        transform: translateY(1px);
        box-shadow:
            inset 0 4px 8px rgba(255,255,255,0.4),
            inset 0 -4px 8px rgba(0,0,0,0.2);
    }

    .appointment-btn svg {
        width: 25px;
        height: 25px;
        fill: white;
    }




        @-webkit-keyframes animate {
            50% {
                -ms-transform: scaleY(0);
                -webkit-transform: scaleY(0);
                transform: scaleY(0);
            }
        }

        @keyframes animate {
            50% {
                -ms-transform: scaleY(0);
                -webkit-transform: scaleY(0);
                transform: scaleY(0);
            }
        }

        #loading {
            background-color: #0F2E24;
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: 1050;
            margin-top: 0px;
            top: 0px;
        }
        .pace .pace-progress {
            background: #C9974D !important;
        }
        .pace .pace-activity {
            border-top-color: #C9974D !important;
            border-left-color: #C9974D !important;
        }
        .pace .pace-progress-inner {
            box-shadow: 0 0 10px #C9974D, 0 0 5px #C9974D !important;
        }
        @media screen and (max-width:1000px){
    #member-data a.c-base-1,#interest-data a.c-base-1 {
    color: black !important;
    font-weight: 600;
    }
    /*div#interest-data .d-inline-block.w100 {*/
    /*    display: flex !important;*/
    /*    flex-direction: column;*/
    /*}*/
    /*div#interest-data .float-left {*/
    /*    display: grid;*/
    /*    grid-template-columns: 1fr 1fr;*/
    /*}*/
    /*div#interest-data .listing-image {*/
    /*    width: 100%;*/
    /*    min-width: 100px;*/
    /*}   */
}
    </style>
    <div id="loading" style="display: none;">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        //$(window).load(function() {
        $(document).ready(function(e) {

            $("#loading").delay(500).fadeOut(500);
            $("#loading-center").click(function() {
                $("#loading").fadeOut(500);
            });
        });
    </script>
    <!-- MAIN WRAPPER -->
    <div class="body-wrap">
        <div id="st-container" class="st-container">
            <div class="st-pusher">
                <div class="st-content">
                    <div class="st-content-inner">
                        <!-- Navbar -->
                        <div id="myHeader" class="ur-header-unified">
                            {{-- Auth lives in .ur-header-actions (final layout in HTML — no JS rearrange / no flash) --}}
                            <nav class="navbar navbar-expand-lg navbar-light bg-default navbar--link-arrow navbar--uppercase">
                                <div class="container navbar-container">
                                    <!-- Brand/Logo -->
                                    <a class="navbar-brand" href="{{url('/')}}">
                                        <img src="/images/header_logo2.png" class="img-responsive" height="100%">
                                    </a>
                                    <div class="ur-mobile-actions d-lg-none">
                                        @auth
                                        <a href="{{ url('member/profile') }}" class="ur-mobile-profile" aria-label="Profile">
                                            <i class="fa fa-user"></i>
                                        </a>
                                        @endauth
                                        <div class="d-inline-block ur-nav-toggler-wrap">
                                            <!-- Navbar toggler  -->
                                            <button class="navbar-toggler hamburger hamburger-js hamburger--spring" type="button" data-toggle="collapse" data-target="#navbar_main" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                                                <span class="hamburger-box">
                                                    <span class="hamburger-inner"></span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse navbar-collapse align-items-center justify-content-end" id="navbar_main">
                                        <!-- Navbar links -->
                                        <ul class="navbar-nav" data-hover="dropdown">
                                            <li class="custom-nav">
                                                <a class="nav-link" href="{{url('/')}}" aria-haspopup="true" aria-expanded="false">
                                                    Home</a>
                                            </li>
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{ url('/') }}#how-it-works" aria-haspopup="true" aria-expanded="false">
                                                    How It Works</a>
                                            </li>
                                            @auth
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('member/profile')}}" aria-haspopup="true" aria-expanded="false">
                                                    Profile</a>
                                            </li>
                                            @endauth
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('packages')}}" aria-haspopup="true" aria-expanded="false">
                                                    Premium Plans</a>
                                            </li>
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('team')}}" aria-haspopup="true" aria-expanded="false">
                                                    Our Team</a>
                                            </li>
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('stories')}}" aria-haspopup="true" aria-expanded="false">
                                                    Success Stories</a>
                                            </li>
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('contact-us')}}" aria-haspopup="true" aria-expanded="false">
                                                    Contact</a>
                                            </li>
                                            @guest
                                            <li class="custom-nav d-lg-none">
                                                <a class="nav-link" href="{{ route('login') }}" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-power-off mr-2"></i> Log In
                                                </a>
                                            </li>
                                            @endguest
                                            @auth
                                            <li class="custom-nav d-lg-none">
                                                <a class="nav-link" href="{{ url('member/profile') }}" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-user-circle mr-2"></i> Profile
                                                </a>
                                            </li>
                                            @if(User::retrieveUserObject()->admin==1)
                                            <li class="custom-nav d-lg-none">
                                                <a class="nav-link" href="{{ url('admin/profiles') }}" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cogs mr-2"></i> Dashboard
                                                </a>
                                            </li>
                                            @endif
                                            <li class="custom-nav d-lg-none">
                                                <a class="nav-link" href="{{ url('/member/profile/listing/interests') }}" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-heart mr-2"></i> Interests
                                                </a>
                                            </li>
                                            <li class="custom-nav d-lg-none">
                                                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="fa fa-power-off mr-2"></i> Log Out
                                                </a>
                                            </li>
                                            @endauth
                                            {{-- Mobile menu only; desktop uses .ur-header-actions copy. Logged-in users
                                                 don't need this — they already have Interests/Log Out actions, and it
                                                 was crowding the navbar. --}}
                                            @guest
                                            <li class="custom-nav ur-appt-in-nav">
                                                @if(request()->is('/') || request()->is('home'))
                                                <a class="appointment-btn" href="javascript:void(0);" onclick="openPopup()" aria-haspopup="true" aria-expanded="false">
                                                @else
                                                <a class="appointment-btn" href="{{ url('contact-us') }}" aria-haspopup="true" aria-expanded="false">
                                                @endif
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM480 496C488.8 496 496 488.8 496 480L496 416L408 416L408 496L480 496zM496 368L496 288L408 288L408 368L496 368zM360 368L360 288L280 288L280 368L360 368zM232 368L232 288L144 288L144 368L232 368zM144 416L144 480C144 488.8 151.2 496 160 496L232 496L232 416L144 416zM280 416L280 496L360 496L360 416L280 416zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176z"/></svg>
                                                Book a Private Consultation</a>
                                            </li>
                                            @endguest
                                        </ul>
                                    </div>
                                    <div class="ur-header-actions">
                                        <ul class="top_bar_right">
                                            @auth
                                            <li class="dropdown dropdown--style-2 dropdown--animated">
                                                <div class="notification-box" id="notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <span class="notification-count noti_counter"></span>
                                                    <div class="notification-bell">
                                                        <span class="bell-top"></span>
                                                        <span class="bell-middle"></span>
                                                        <span class="bell-bottom"></span>
                                                        <span class="bell-rad"></span>
                                                    </div>
                                                </div>
                                                <div class="dropdown-menu" style="max-height: 300px;overflow: auto;">
                                                    <h6 class="dropdown-header">Notifications</h6>
                                                    <div class="text-center">
                                                        <ul class="notifications" aria-labelledby="notificationsMenu" id="notificationsMenu">
                                                            <li class="sml_txt">No Notification To Show</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="dropdown dropdown--style-2 dropdown--animated">
                                                <a class="dropdown-toggle has-badge c-base-1" href="{{url('member/profile')}}">
                                                    <div id="top_nav_img" class="top_nav_img" style="background-image: url( '{{ User::retrieveUserObject()->getProfileImage(true) }}')"></div>
                                                    <span class="dropdown-text strong-500 d-lg-inline-block d-xl-inline-block" style="margin-top: 5px">{{User::retrieveUserObject()->first_name}} {{User::retrieveUserObject()->last_name}}</span>
                                                </a>
                                            </li>
                                            @endauth
                                            @guest
                                            <li>
                                                <a href="{{ route('login') }}" class="btn btn-styled btn-xs btn-base-1 btn-shadow" aria-label="Log In"><i class="fa fa-power-off"></i> Log In</a>
                                            </li>
                                            @endguest
                                            @auth
                                            <li>
                                                @if(User::retrieveUserObject()->admin==1)
                                                <a href="{{url('admin/profiles')}}" class="btn btn-styled btn-xs btn-base-1 btn-shadow"><i class="fa fa-cogs"></i> Dashboard</a>
                                                @endif
                                                <a href="{{ url('/member/profile/listing/interests') }}" class="btn btn-styled btn-xs btn-base-1 btn-shadow"><i class="fa fa-heart"></i> Interests</a>
                                                <a href="#" class="btn btn-styled btn-xs btn-base-1 btn-shadow" onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Log Out</a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </li>
                                            @endauth
                                            </li>
                                        </ul>
                                        {{-- Logged-in users don't need this — they already have Interests/Log Out
                                             actions, and it was crowding the navbar. --}}
                                        @guest
                                        @if(request()->is('/') || request()->is('home'))
                                        <a class="appointment-btn ur-appt-in-actions d-none d-lg-inline-flex" href="javascript:void(0);" onclick="openPopup()" aria-haspopup="true" aria-expanded="false">
                                        @else
                                        <a class="appointment-btn ur-appt-in-actions d-none d-lg-inline-flex" href="{{ url('contact-us') }}" aria-haspopup="true" aria-expanded="false">
                                        @endif
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM480 496C488.8 496 496 488.8 496 480L496 416L408 416L408 496L480 496zM496 368L496 288L408 288L408 368L496 368zM360 368L360 288L280 288L280 368L360 368zM232 368L232 288L144 288L144 368L232 368zM144 416L144 480C144 488.8 151.2 496 160 496L232 496L232 416L144 416zM280 416L280 496L360 496L360 416L280 416zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176z"/></svg>
                                            Book a Private Consultation</a>
                                        @endguest
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="sticky-content">
                            <div class="container">
                                <div class="row">
                                    <!-- Toast notifications for actions -->
                                    <div id="message_alert" class="ur-toast-stack"></div>
                                    <!-- Toast notifications for actions -->
                                </div>
                            </div>
                            <div id="main-content">
                            @yield('main-content')
                            </div>
                            @include('layouts.partials.footer')
                        </div>
                    </div>
                </div>
                <!-- END: st-pusher -->
            </div>
            <!-- END: st-pusher -->
        </div>
        <!-- END: st-container -->
    </div>
    <!-- END: body-wrap -->
    <a href="#" class="btn-shadow back-to-top btn-back-to-top"></a>
    <div id="modal_dialog"></div>
    <script type="text/javascript">
        window.Laravel = {'token': '{{ csrf_token() }}', 'root': '{{ url('/') }}'};
        @auth
        window.Laravel.userId='{{ Auth::user()->id }}';
        @endauth

        window.onscroll = function() {
            scrollFunction();
        };
        var header = document.getElementById("myHeader");
        var sticky = header.offsetTop;

        function scrollFunction() {
            if (window.pageYOffset > sticky) {
                header.classList.remove("sticky-header");
            } else {
                header.classList.remove("sticky-header");
            }
        }

        function register_request() {
            swal({
                'title': 'Register for Full Access',
                'text': 'Thanks for checking out our website. Kindly register to gain full access to the profiles and for complete interactions.',
                'icon': 'info',
            });
        }

        function swalConfirm(title, message, onConfirm) {
            swal({
                'title': title,
                'text': message,
                'icon': 'warning',
                'buttons': {
                    cancel: true,
                    confirm: true
                }
            }).then((isConfirm) => {
                if (isConfirm && onConfirm)
                    onConfirm();
            });
        }

        function swalAlert(type, title, message, callback) {
            swal(title, message, type).then( callback );
        }

        function showAlert(type, message, timeout, code) {
            var stack = document.getElementById('message_alert');
            if (!stack) return;

            // Normalize the many type spellings used across the codebase
            // ('success', 'danger', 'error', 'warning', 'info', ...) to one
            // of our four toast variants.
            var variant = 'info';
            if (/success/i.test(type)) variant = 'success';
            else if (/danger|error/i.test(type)) variant = 'danger';
            else if (/warning/i.test(type)) variant = 'warning';

            var icons = {
                success: 'fa-check-circle',
                danger: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            var duration = timeout ? timeout : 10000;

            var toast = document.createElement('div');
            toast.className = 'ur-toast ur-toast--' + variant;
            toast.innerHTML =
                '<i class="fa ' + icons[variant] + ' ur-toast__icon" aria-hidden="true"></i>' +
                '<div class="ur-toast__body">' + message + '</div>' +
                '<button type="button" class="ur-toast__close" aria-label="Dismiss">&times;</button>' +
                '<div class="ur-toast__bar" style="animation-duration:' + duration + 'ms"></div>';

            stack.appendChild(toast);
            // Force layout before adding the "show" class so the slide-in transition runs.
            void toast.offsetWidth;
            toast.classList.add('ur-toast--show');

            var dismissed = false;
            function dismiss() {
                if (dismissed) return;
                dismissed = true;
                toast.classList.remove('ur-toast--show');
                toast.classList.add('ur-toast--hide');
                setTimeout(function() {
                    toast.remove();
                    if (code) eval(code);
                }, 300);
            }

            toast.querySelector('.ur-toast__close').addEventListener('click', dismiss);
            var autoTimer = setTimeout(dismiss, duration);

            // Pause the countdown while the user is reading it.
            toast.addEventListener('mouseenter', function() {
                clearTimeout(autoTimer);
                toast.querySelector('.ur-toast__bar').style.animationPlayState = 'paused';
            });
            toast.addEventListener('mouseleave', function() {
                autoTimer = setTimeout(dismiss, 1500);
                toast.querySelector('.ur-toast__bar').style.animationPlayState = 'running';
            });
        }

        // highlight link with icon
        function clickHighlight(title_id, title, icon_tag, new_icon, new_label, isHighlight, updateAnchor, updatedOnClickCode, highlightClass) {

            if (!highlightClass) highlightClass = "c-base-1";

            if (title_id && title) // update title if needed
                title_id.html(title);

            if (icon_tag) { //  element containing the fa icon

                if (new_icon) // update if new icon and update to new label
                    icon_tag.html('<i class="fa fa-'+new_icon+'"></i> '+new_label+' ');
                else { // just reinsert existing icon with new label
                    var iconTag = icon_tag.children("i")[0];
                    icon_tag.html("");
                    icon_tag.append(iconTag, ' ' + new_label + ' ');
                }

                if (isHighlight) { // should highlight link
                    icon_tag.addClass(highlightClass);
                    icon_tag.siblings("span").addClass(highlightClass);
                } else {
                    icon_tag.removeClass(highlightClass);
                    icon_tag.siblings("span").removeClass(highlightClass);
                }

                if (updateAnchor) { // if anchor link should be updated
                    var anchor = icon_tag.prop("tagName")=="A" ? icon_tag : icon_tag.parent("a");
                    if (updatedOnClickCode) // new click code
                        anchor.attr("onclick", updatedOnClickCode);
                    else anchor.removeAttr("onclick"); // remove on click option so link does not work anymore
                }
            }
        }

        function loadSelect(url, querystr, selElem, selectedId) {
            $.ajax({
                type: "get",
                url: url + "/" + querystr,
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                success: function(result) {
                    if (result.code=="200") {
                        if (result.options) {
                            selElem.empty();
                            selElem.append($("<option />").val(this.dataid).text("Choose one..."));
                            $.each(result.options, function() {
                                selElem.append($("<option />").val(this.dataid).text(this.name));
                            });
                            selElem.val(selectedId);
                            if (selElem.selectpicker)
                                selElem.selectpicker('refresh');
                        }
                    }
                }
            });
        }

        function sendInterest(elem) {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            var elemId = elem.attr("id");
            var splitId = elemId.split("_");
            $.ajax({
                type: "post",
                url: "{{ url('member/profile/interest/send')}}" + "/" + splitId[1],
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message.split("|");
                    if (result.code=="200") {
                        $("#status_"+splitId[1]).removeClass("btn-green");
                        $("#status_"+splitId[1]).removeClass("btn-red");
                        $("#status_"+splitId[1]).addClass("btn-base-1");
                        $("#status_"+splitId[1]).html("PENDING");
                        clickHighlight(null, null,
                            $(elem.children("span")[0]), null, "Interest Expressed", true, true,  "return withdrawInterest($(this), 's');");
                        showAlert(message[0], message[1], 7000);
                    } else showAlert('danger', message, 5000);
                }
            });
        }

        function acceptInterest(elem) {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            var elemId = elem.attr("id");
            var splitId = elemId.split("_");
            $.ajax({
                type: "post",
                url: "{{ url('member/profile/interest/accept')}}" + '/' + splitId[1],
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message.split("|");
                    if (result.code=="200") {
                        $("#interest_"+splitId[1]+"_d").hide();
                        $("#interest_"+splitId[1]+"_a").hide();
                        $("#interest_"+splitId[1]+"_w").show();
                        $("#status_"+splitId[1]).removeClass("btn-base-1");
                        $("#status_"+splitId[1]).removeClass("btn-red");
                        $("#status_"+splitId[1]).addClass("btn-green");
                        $("#status_"+splitId[1]).html("GRANTED");
                        showAlert(message[0], message[1], 7000);
                    } else showAlert('danger', message, 5000);
                }
            });
        }

        function declineInterest(elem) {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            var elemId = elem.attr("id");
            var splitId = elemId.split("_");
            $.ajax({
                type: "post",
                url: "{{ url('member/profile/interest/decline')}}" + '/' + splitId[1],
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message.split("|");
                    if (result.code=="200") {
                        $("#interest_"+splitId[1]+"_d").hide();
                        $("#interest_"+splitId[1]+"_a").hide();
                        $("#interest_"+splitId[1]+"_w").show();
                        $("#status_"+splitId[1]).removeClass("btn-green");
                        $("#status_"+splitId[1]).removeClass("btn-base-1");
                        $("#status_"+splitId[1]).addClass("btn-red");
                        $("#status_"+splitId[1]).html("DECLINED");
                        showAlert(message[0], message[1], 7000);
                    } else showAlert('danger', message, 5000);
                }
            });
        }

        function withdrawInterest(elem, who) {
            swalConfirm("Withdraw Interest", "Are you sure you want to withdraw your interest?", ()=>{
                var oldHtml = elem.html();
                elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
                elem.prop('disabled', true);

                var elemId = elem.attr("id");
                var splitId = elemId.split("_");
                $.ajax({
                    type: "post",
                    url: "{{ url('member/profile/interest/withdraw')}}" + "/" + splitId[1] + "/" + who,
                    data : {
                        '_token': "{{ csrf_token() }}"
                    },
                    cache: false,
                    success: function(result) {
                        elem.html(oldHtml);
                        elem.prop('disabled', false);

                        var message = result.message.split("|");
                        if (result.code=="200") {
                            $("#interest_"+splitId[1]+"_w").hide();
                            if (who!="s") {
                                $("#interest_"+splitId[1]+"_a").show();
                                $("#interest_"+splitId[1]+"_d").show();
                                $("#status_"+splitId[1]).removeClass("btn-green");
                                $("#status_"+splitId[1]).removeClass("btn-red");
                                $("#status_"+splitId[1]).addClass("btn-base-1");
                                $("#status_"+splitId[1]).html("PENDING");
                            } else {
                                clickHighlight(null, null,
                                    $(elem.children("span")[0]), null, "Express Interest", false, true,  "return sendInterest($(this));");
                                $("#block_sent_"+splitId[1]).remove();
                            }
                            showAlert(message[0], message[1], 7000);
                        } else showAlert('danger', message, 5000);
                    }
                });
            });
        }

        function updateFiltered(elem, action) {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            var elemId = elem.attr("id");
            var splitId = elemId.split("_");
            var newLabel = null;
            if (action=="add") {
                 newLabel = splitId[0][0].toUpperCase()+splitId[0].slice(1)+(splitId[0].charAt(splitId[0].length-1)=="e"?"d":"ed"); // append ed if last char not e otherwise just d
            } else {
                newLabel = splitId[0][0].toUpperCase()+splitId[0].slice(1);
            }
            $.ajax({
                type: "post",
                url: "{{ url('member/profile/filtered')}}" + "/" + action + "/" + splitId[0] + "/" + splitId[1],
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message.split("|");
                    if (result.code=="200") {
                        clickHighlight(null, null,
                            $(elem.children("span")[0]), null, newLabel, (action=="add"), true, "return updateFiltered($(this), '"+(action=="add"?"remove":"add")+"');");
                        showAlert(message[0], message[1], 7000);
                    } else showAlert('danger', message, 5000);
                }
            });
        }

        function showLightGallery(elem) {
            elem.lightGallery({
                cssEasing: 'cubic-bezier(0.680, -0.550, 0.265, 1.550)',
                dynamic: true,
                html: true,
                mobileSrc: true,
                showThumbByDefault: true,
                dynamicEl: @if(!empty($profile)) {!! $profile->getLightGalleryImages() !!} @else '' @endif
            });
        }

        function renderPage(dataUrl, method, formFields, elem) {
            if (!elem) {
                showAlert('danger', "Rendering element is null. Cannot proceed.", 5000);
                return;
            }
            $.ajax({
                url: dataUrl,
                type: method,
                data: formFields?formFields:'',
                success: function(result){
                    elem.html("<i class='fa fa-refresh fa-spin'></i> Retrieving some awesome records for you..");
                    var message = result.message;
                    if (result.code == '200') {
                        if (message)
                            showAlert('success', message, 3000);
                        if (result.html) {
                            elem.html(result.html);
                            $("body, html, .body-wrap").animate({ scrollTop: 0 }, "slow");
                        }
                    } else {
                        if (message)
                            showAlert('danger', message, 5000);
                    }
                }
            });
        }

        $(document).ready(function() {

            @if($errors->any() && !(request()->is('login') || request()->is('login/*') || request()->is('register') || request()->is('register/*')))
            showAlert("error", "{!! implode('', $errors->all('<div>:message</div>')) !!}")
            @endif

            @if(session('message') && !(request()->is('login') || request()->is('login/*') || request()->is('register') || request()->is('register/*')))
            var message = "{!! session('message') !!}".split("|");
            showAlert(message[0], message[1], message[2]);
            @endif

            $(".selectpicker").select2();
        });
        
    </script>
    
    <!-- Bootstrap Modal -->
    <script src="/js/app.js?1"></script>
    <script src="/js/ur-navbar.js?2"></script>
    <script src="/js/new-slick.js?1"></script>
    <script src="/js/new-custom.js?1"></script>
</body>
</html>
