<!doctype html>
<!--[if lt IE 7]>      <html class="no-js ie lt-ie10" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if IE 7]>         <html class="no-js ie ie7 lt-ie10" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if IE 8]>         <html class="no-js ie ie8 lt-ie10" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if IE 9 ]>    <html dir="ltr" lang="en-US" class="ie ie9 lt-ie10"> <![endif]-->
<!--[if gte IE 9]><!-->
<head>
    <meta charset="UTF-8">
    <meta name="fragment" content="!">
    <title>Right Rishta </title>
    <meta name="description" content="right rishta" />
    <meta name="keywords" content="matrimony, pakistan matrimonial, matrimonial sites, marriage, wedding, matrimonial services, right rishta" />
    <meta name="referrer" content="origin">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/fontawesome/css/font-awesome.min.css" media="screen" type="text/css" />
    <link rel="icon" href="https://www.lovevivah.com/assets/images/icons/favicon_medium.png" type="image/png">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
    <script src="https://www.lovevivah.com/assets/js/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/bootstrap/css/bootstrap.min.css?v=4" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/bootstrap/css/bootstrap-select.min.css?v=4" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="screen" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="https://kenwheeler.github.io/slick/slick/slick.css" rel="stylesheet" />
    <link href="https://kenwheeler.github.io/slick/slick/slick-theme.css" rel="stylesheet" />
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KHCRXG');
    </script>
    <!-- End Google Tag Manager -->
    <!--<meta name="google-site-verification" content="oo7O9UcsOzKK9kFQKobRs5-HN5i5ThzaA3y_RlRy8rE" /> -->
    <link rel="canonical" href="search/search-result" />
    <link href="https://fonts.googleapis.com/css?family=Tangerine:700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300|Roboto:300,400,400i,500,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Squada+One" rel="stylesheet">
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/override.css?v=47" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/custom.css?v=116" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/reg-style.css?v=100" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/owl-carousel/owl.carousel.min.css?v=26" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/owl-carousel/owl.theme.default.min.css?v=26" type="text/css" />
    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/animate2.min.css" media="screen" type="text/css" />
    <script src="https://www.lovevivah.com/assets/js/angular.min.js"></script>
    <script src="https://www.lovevivah.com/assets/js/angular-sanitize.js"></script>
    <script src="https://www.lovevivah.com/assets/js/baby-button.js"></script>
</head>
<body ng-controller="mainController">
    <noscript>
        <div class="noscript colrw boxshadow">You have not enabled Javascript on your browser, please enable it to use the website</div>
    </noscript>
    <!--Header Start-->
    <script type="text/javascript">
        $(document).ready(function() {
            $("#registerBTN").removeAttr('onclick');
            $("#registerBTN").attr('href', 'registration');
        });
    </script>
    <style>
        .mob-search-pop {
            display: none;
        }
    </style>
    <style type="text/css">
        .container-fluid {
            position: relative;
        }
        .special-offer999 {
            position: absolute;
            top: 0px;
            left: 200px;
            z-index: 10000;
        }
        .specialmodal .close {
            padding: 10px 15px;
            opacity: .7;
        }
        .SpecialOffer-sec {
            padding: 30px;
            text-align: center;
        }
        .SpecialOffer-sec h3 {
            line-height: 33px;
            font-size: 20px;
            margin: 0px;
            padding: 0px;
        }
        .SpecialOffer-sec h3 strong span {
            font-size: 35px;
            font-weight: 600;
            color: #090;
        }
        @media (max-width: 767px) {
            .specialmodal {
                width: 95%;
            }
            .special-offer999 {
                position: absolute;
                top: 0px;
                left: 65px;
                z-index: 10000;
            }
        }
        .specialbtn {
            font-size: 20px;
            border-radius: 3px;
            padding: 10px 0px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0px 0px 34px rgba(205, 49, 98, 0.53);
        }
        .blur {
            filter: blur(8px);
            -webkit-filter: blur(8x);
        }
    </style>
    <!--Desktop Navbar -->
    <nav class="navbar navbar-default navbar-fixed-top lv-nav" ng-controller="headerController">
        <div class="container-fluid top-nav"><span class="top-trust pull-left"><label class="label label-warning">Right Rishta</label></span><span class="top-trust pull-right">Call Number Pakistan | Email <a href=""></a></div>
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed navbar-left" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{url('/')}}" class="navbar-brand">
                    <img style="max-width:200px; margin-top: -7px;" src="{{asset('images/logo.png')}}">
                </a>
            </div>
            <div class="mainmenu-lv">
                <div id="navbar" class="navbar-collapse collapse" aria-expanded="false">
                    <ul class="nav navbar-nav navbar-right">
                        @auth
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('contact-us')}}">Contact</a></li>
                        <li><a href="{{url('packages')}}">Packages</a></li>
                        <li><a href="{{url('search')}}">Search</a></li>
                        <li><a href="{{url('interest/show')}}">Interests</a></li>
                        <li><a href={{url('profile')}}>Profile</a></li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                        @endauth
                        @guest
                        <li><a href="{{route('register')}}">FREE REGISTER</a></li>
                        <li><a href="{{route('login')}}">LOGIN</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
        </span>
        </div>
    </nav>
    <section class="headline" style="padding:0px;">
        &nbsp;
    </section>
    <section id="feature" class="section-padding">
        <div class="container">
            <div class="row" style="margin-top:100px">
                <div class="autoplay col-md-12" style="height:200px">
                    @foreach($rec as $r)
                    <div><img src="{{asset('').'/'.$r}}" class="blur" alt="Cinque Terre" height="150px" width="150px"></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="header-section text-center">
                    <i><img src="https://www.lovevivah.com/assets/images/leaf.png" alt="" /></i>
                    <h2>Find Your Match With Right Rishta</h2>
                    <!--Header End-->
                    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/ion.rangeSlider.css" />
                    <link rel="stylesheet" href="https://www.lovevivah.com/assets/css/new-listing.css?v=2" media="screen" type="text/css" />
                    <style>
                        .overlay-load {
                            position: fixed;
                            width: 100%;
                            height: 100%;
                            top: 0;
                            left: 0;
                            background-color: rgba(0, 0, 0, 0.33);
                            z-index: 1000;
                            display: none;
                        }
                        .bg-r {
                            /*background:#fff;*/
                            border-radius: 50px;
                            padding: 5px 0px 0px 0px;
                        }
                        .search-criteria {
                            padding: 5px;
                        }
                        /*.lvwordellips{
                            max-width:100%;
                        }*/
                        .no-package {
                            border: none;
                        }
                        .light-pink {
                            color: none;
                        }
                        .fa-sheild {
                            display: none;
                        }
                        .spinner-list {
                            display: none;
                        }
                    </style>
                    <script type="text/javascript">
                        function sendinterest(obj, id) {
                            $.ajax({
                                type: "get",
                                url: "{{ url('interest/add')}}" + '/' + id,
                                // data: {id:id,},
                                success: function(result) {
                                    if (result.code == '200') {
                                        obj.disabled = true;
                                        swal("Success", "Interest Sent", "success");
                                    }
                                }
                            });
                        }
                    </script>
                    <div class="overlay-load">
                        <div style="margin-top:30%;">
                            <div class="spinner bg-r">
                                <div class="bounce1" style="background:#cd3162"></div>
                                <div class="bounce2" style="background:#fbbb38"></div>
                                <div class="bounce3" style="background:#cd3162"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="startval" id="startvalue" value="12">
                    <div class="main page-bg">
                        <div class="container container-full">
                            <div class="row searchpage">
                                <div class="col-md-12 col-sm-12 col-xs-12 no-padding-sm">
                                    <div class="col-md-12 col-sm-12 col-xs-12 no-padding-sm pro-m10">
                                        <div class="result-listing" id="profile-listing" style="padding-bottom:30px;">
                                            <input type="hidden" name="totalpagecount" id="totalpagecount" value="2939">
                                            <div class="col-md-12 col-sm-12 no-padding" id="topfilter">
                                                <div class="result-number-wrapper">
                                                    <h2><span>{{sizeof($users)}} </span> Profiles</h2>
                                                    {{-- <button type="submit" class="btn btn-theme-new btn-sm ripplelink pull-right modify-btn desktop-hide" onclick="$('#filter-collapse').slideToggle('slow');"><i class="fa fa-filter"></i></button> --}}
                                                    <a href="{{url('search')}}" class="btn btn-theme-new btn-sm ripplelink pull-right modify-btn hidden-xs" style="margin-right:5px;">Modify Search</a>
                                                    {{-- <p>Female, 20 - 30&nbsp;Years --}}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 no-padding">
                                                <ul class="profile-list-new">
                                                    @foreach($users as $user)
                                                    <li class="pro-listing-n">
                                                        <div class="pro-container-n ">
                                                            <div class="pro-pic" id="parentid1277654">
                                                                @guest
                                                                <a href="{{asset('')}}/images/notvisible.jpg" target="_blank">
                                                                    <img src="{{asset('')}}/images/notvisible.jpg" class="img-responsive" alt="Profilepic">
                                                                    {{-- <span class="pics-count">7</span>  --}}
                                                                </a>
                                                                @else
                                                                <a href="{{url('profile').'/'.$user->id}}" target="_blank">
                                                                    <img src="{{asset('').$user->img_url}}" class="img-responsive" alt="Profilepic">
                                                                    {{-- <span class="pics-count">7</span>  --}}
                                                                </a>
                                                                @endguest
                                                            </div>
                                                            <div class="pro-detail padd-15">
                                                                <div class="pro-detail-sum">
                                                                    <span class="applozic-launcher" id="applozic-launcher-1277654" data-mck-id="1277654" data-mck-name="Gunjan Bharadwaj" style="display:none;"></span>
                                                                    <div class="bor-bot">
                                                                        <!--<span class="memberbadge"><img src="img/member.jpg" width="29" height="48" alt="" data-toggle="tooltip" title="Premium Member"/></span>-->
                                                                        <h2>
                                                                            {{-- <a href="javascript:void(0);" data-toggle="modal" data-target="#loginModal" class="opencometchat cometchat_ccmobiletab_redirect_custom direct_chat"><i class="fa fa-commenting"></i> Chat Now</a> --}}
                                                                            {{-- <a href="https://www.lovevivah.com/userprofile_controller/view_profile/1277654?src_trck=search" target="_blank">  --}}
                                                                            {{$user->first_name}} {{$user->last_name}}
                                                                            {{-- <span class="pro-id-num">LV-1277654</span> </a> --}}
                                                                        </h2>
                                                                    </div>
                                                                    <div class="pro-detail-all">
                                                                        <ul>
                                                                            <li>Name: {{$user->first_name}} {{$user->last_name}}</li>
                                                                            <li>Height: {{$user->height}} ft</li>
                                                                            <li>Religion: {{$user->religion}}</li>
                                                                            <li>Education: {{$user->education}}</li>
                                                                            <li>{{$user->marital_stauts}}</li>
                                                                            <li>Age: {{$user->age}} year old</li>
                                                                            <li>{{$user->country}} </li>
                                                                            <li>{{$user->city}} | Pakistan</li>
                                                                            <li>Profession: {{$user->profession}}</li>
                                                                            <li>Mother Language: {{$user->mother_toungh}}</li>
                                                                            <li>Salary: {{$user->salary}}</li>
                                                                        </ul>
                                                                        <div class="trust-score-n">
                                                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#loginModal">
                                                                                <p>Trust Score</p> <span class="trust-colored"> 70%</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="more-option-n">
                                                                    {{-- <a href="#" class=" dropdown-toggle" id="drop1" data-toggle="dropdown" role="button">
                                                                        <i class="fa fa-circle"></i> <i class="fa fa-circle"></i> <i class="fa fa-circle"></i>
                                                                    </a>             --}}
                                                                    <ul role="menu" class="dropdown-menu option-drop" aria-labelledby="drop1">
                                                                        <li role="presentation"><a href="javascript:void(0);" role="menuitem" tabindex="-1" data-toggle="modal" data-target="#loginModal">View Contact</a></li>
                                                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="https://www.lovevivah.com/userprofile_controller/view_profile/1277654?src_trck=search" target="_blank">View Profile</a></li>
                                                                    </ul>
                                                                </div>
                                                                <div class="pro-buttons">
                                                                    <div class="pabrsli">
                                                                        {{-- <button type="button" class="shortlist-button" data-toggle="modal" data-target="#loginModal" ><i class="fa fa-heart"></i></button>
																<p>Shortlist</p> --}}
                                                                    </div>
                                                                    <div class="pabrsli">
                                                                        @guest
                                                                        <a type="button" class="sendinterest-button btn-sendint_pro" href="{{route('login')}}"><i class="fa fa-send"></i></a>
                                                                        @else
                                                                        <button type="button" class="sendinterest-button btn-sendint_pro" onclick="sendinterest(this, {{$user->id}})"><i class="fa fa-send"></i></button>
                                                                        @endguest
                                                                        <p>Send Interest</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                    <script src="https://kenwheeler.github.io/slick/slick/slick.js"></script>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('.autoplay').slick({
                                                                slidesToShow: 5,
                                                                slidesToScroll: 1,
                                                                autoplay: true,
                                                                autoplaySpeed: 2000,
                                                            });
                                                        })
                                                    </script>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
