<?php use App\User; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="urgentrishta.co is a Pakistani matrimonial website.">
    <meta name="keywords" content="matrimonial,urgentrishta.co">
    <meta name="author" content="urgentrishta.co">
    <meta name="revisit-after" content="2 day(s)">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/app.css?1"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/pink/pace-theme-minimal.min.css" type="text/css" />
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
    <link id="stylesheet" type="text/css" href="/css/global-style-pink.css?1" rel="stylesheet" media="screen" />
    <!-- Custom style - Remove if not necessary -->
    <link type="text/css" href="/css/custom-style.css?1" rel="stylesheet" />
    <link type="text/css" href="/css/new-theme.css?1" rel="stylesheet" />
    <link type="text/css" href="/css/new-animate.min.css?1" rel="stylesheet" />
    <link type="text/css" href="/css/ur-navbar.css?10" rel="stylesheet" />
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

<body class="pace-done">
    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <style>
    @media screen and (max-width: 500px) {
    ul.navbar-nav {
        background-color: #E91E63;
        
    }
    ul.navbar-nav a.nav-link.admin-link.p_nav.active{
        color: white !important;
    }
    }
    @media (min-width: 1000px) {
        .homepage #myHeader{
            position: absolute;
            top:0;
            width: 100%;
        }
        .normalpage img.img-responsive {
            filter: drop-shadow(0px 0px) drop-shadow(2px 4px 6px #E91E63);
        }   
    }
    .navbar-light .navbar-nav .nav-link {
  color: white;
  background: transparent; }
    .c-base-1 {
  color: white; }
  .navbar.bg-default {
  background: #E91E63;
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
            background-color: #E91E63;
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: 1050;
            margin-top: 0px;
            top: 0px;
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
                                    <div class="d-inline-block ur-nav-toggler-wrap">
                                        <!-- Navbar toggler  -->
                                        <button class="navbar-toggler hamburger hamburger-js hamburger--spring" type="button" data-toggle="collapse" data-target="#navbar_main" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                                            <span class="hamburger-box">
                                                <span class="hamburger-inner"></span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="collapse navbar-collapse align-items-center justify-content-end" id="navbar_main">
                                        <!-- Navbar links -->
                                        <ul class="navbar-nav" data-hover="dropdown">
                                            <li class="custom-nav">
                                                <a class="nav-link" href="{{url('/')}}" aria-haspopup="true" aria-expanded="false">
                                                    Home</a>
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
                                                <a class="nav-link " href="{{url('stories')}}" aria-haspopup="true" aria-expanded="false">
                                                    Happy Stories</a>
                                            </li>
                                            <li class="custom-nav">
                                                <a class="nav-link " href="{{url('contact-us')}}" aria-haspopup="true" aria-expanded="false">
                                                    Contact Us</a>
                                            </li>
                                            {{-- Mobile menu only; desktop uses .ur-header-actions copy --}}
                                            <li class="custom-nav ur-appt-in-nav">
                                                <a class="appointment-btn" href="{{ url('appointments') }}" aria-haspopup="true" aria-expanded="false">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM480 496C488.8 496 496 488.8 496 480L496 416L408 416L408 496L480 496zM496 368L496 288L408 288L408 368L496 368zM360 368L360 288L280 288L280 368L360 368zM232 368L232 288L144 288L144 368L232 368zM144 416L144 480C144 488.8 151.2 496 160 496L232 496L232 416L144 416zM280 416L280 496L360 496L360 416L280 416zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176z"/></svg>
                                                Book An Appointment</a>
                                            </li>
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
                                                <a href="{{ route('login') }}" class="btn btn-styled btn-xs btn-base-1 btn-shadow"><i class="fa fa-power-off"></i> Log In</a>
                                            </li>
                                            @if (Route::has('register'))
                                            <li>
                                                <a href="{{ route('register') }}" class="btn btn-styled btn-xs btn-base-1 btn-shadow"><i class="fa fa-user"></i> Register</a>
                                            </li>
                                            @endif
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
                                        <a class="appointment-btn ur-appt-in-actions d-none d-lg-inline-flex" href="{{ url('appointments') }}" aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM480 496C488.8 496 496 488.8 496 480L496 416L408 416L408 496L480 496zM496 368L496 288L408 288L408 368L496 368zM360 368L360 288L280 288L280 368L360 368zM232 368L232 288L144 288L144 368L232 368zM144 416L144 480C144 488.8 151.2 496 160 496L232 496L232 416L144 416zM280 416L280 496L360 496L360 416L280 416zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176z"/></svg>
                                            Book An Appointment</a>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="sticky-content">
                            <div class="container">
                                <div class="row">
                                    <!-- Alerts for actions -->
                                    <div id="message_alert" style="display: none; position: fixed; top: 25%; margin: auto; z-index: 9999"></div>
                                    <!-- Alerts for actions -->
                                </div>
                            </div>
                            <div id="main-content">
                            @yield('main-content')
                            </div>
                            <footer id="footer" class="footer">
                                <div class="footer-top">
                                    <div class="container">
                                        <div class="row cols-xs-space cols-sm-space cols-md-space">
                                            <div class="col-md-3 col-lg-3">
                                                <div class="col">
                                                    <a class="navbar-brand" href="{{url('/')}}">
                                                        <img src="/images/header_logo2.png" class="img-responsive" width="100%">
                                                    </a>
                                                    <div class="text-center"><small></small></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-lg-3 d-none d-lg-block d-md-block">
                                                <div class="col">
                                                    <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                                                        Main Menu</h4>
                                                    <ul class="footer-links">
                                                        <li>
                                                            <a href="{{url('/')}}" title="Home">
                                                                Home</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('packages')}}" title="Premium Plans">
                                                                Premium Plans</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('stories')}}" title="Happy Stories">
                                                                Happy Stories</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('contact-us')}}" title="Contact Us">
                                                                Contact Us</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-3 col-lg-3 d-none d-lg-block d-md-block">
                                                <div class="col">
                                                    <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                                                        Quick Search</h4>
                                                    <ul class="footer-links">
                                                        <li>
                                                            <a href="/home/listing" title="All Members">
                                                                All Members</a>
                                                        </li>
                                                        <li>
                                                            <a href="/home/listing/premium_members" title="Premium Members">
                                                                Premium Members</a>
                                                        </li>
                                                        <li>
                                                            <a href="/home/listing/free_members" title="Free Members">
                                                                Free Members</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div> -->
                                            <div class="col-md-3 col-lg-3">
                                                <div class="col">
                                                    <h4 class="heading heading-xs strong-600 text-uppercase mb-1">
                                                        Useful Links</h4>
                                                    <ul class="footer-links">
                                                        <li>
                                                            <a href="{{url('faqs')}}" title="FAQ">
                                                                FAQ </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('tandc')}}" title="Terms &amp; Conditions">
                                                                Terms &amp; Conditions</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('privacy')}}" title="Prvacy Policy">
                                                                Privacy Policy</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer-bottom py-1">
                                    <div class="container">
                                        <div class="row row-cols-xs-spaced flex flex-items-xs-middle">
                                            <div class="col col-md-7">
                                                <div class="copyright text-center text-sm-left mt-2">
                                                    Copyright © 2021 <a href="{{url('/')}}" class="c-base-1" target="_blank" title="Urgent Rishta - Official Website">
                                                        <strong class="strong-400">Urgent Rishta (pvt) Ltd.</strong>
                                                    </a> - All Rights Reserved </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </footer>
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
            var alertDiv = $("#message_alert");
            var width = screen.width;
            var alertWidth = width*0.5;
            alertDiv.css({'text-align': 'center', 'width': alertWidth, 'left': (width-alertWidth)/2});
            alertDiv.show();
            alertDiv.html("<div class=\"alert alert-" + type + " fade show\" role=\"alert\">" + message + "</div>");
            setTimeout(function() {
                alertDiv.fadeOut("slow", "swing");
                if (code) eval(code);
            }, timeout ? timeout : 10000);
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
        
        if (window.location.pathname === "/" || window.location.pathname === "/home" || window.location.pathname === "/packages" || window.location.pathname.indexOf("/package-details") === 0) {
            document.body.classList.add("homepage");
        }else{
            document.body.classList.add("normalpage");
        }

    </script>
    
    <!-- Bootstrap Modal -->
    <script src="/js/app.js?1"></script>
    <script src="/js/ur-navbar.js?2"></script>
    <script src="/js/new-slick.js?1"></script>
    <script src="/js/new-custom.js?1"></script>
</body>
</html>
