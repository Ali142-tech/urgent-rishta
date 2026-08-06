@extends('layouts.master')
@section('main-content')
<section class="sct-color-1">
    <div class="container-fluid no-padding">
        <div class="row row-no-padding">
            <style>
            .navbar-light .navbar-nav .nav-link{
                color: white !important;
            }
            .custom-nav > a:before{
                background:white;
            }
            a.btn.btn-styled.btn-xs.btn-base-1.btn-shadow {
                font-size: 12px !important;
            }
            @media (max-width: 1000px) {
                .hamburger:hover:not(.is-active) .hamburger-inner, .hamburger:hover:not(.is-active) .hamburger-inner:after, .hamburger:hover:not(.is-active) .hamburger-inner:before, .hamburger-inner, .hamburger-inner:after, .hamburger-inner:before{
                    background-color: rgb(255 255 255 / 56%);
                }
                a.btn.btn-styled.btn-xs.btn-base-1.btn-shadow {
                    background: white;
                    color: #E91E63;
                    border:1px solid white;
                }
                nav.navbar.navbar-expand-lg.navbar-light.bg-default.navbar--link-arrow.navbar--uppercase{
                    background: #E91E63;
                    border: 0;
                }
                .btn-styled:hover {
                    color:white;
                }
                
            }
             @media (max-width: 700px) {
                 .mobileImage{
                        background-image: url(images/slider_images/slider_image_1_1.png) !important;
                    }
             }
                @media (max-width: 576px) {
                    .outer-search {
                        bottom: 50px;
                        margin: 0px 20px 100px 20px !important;
                        top: auto !important;
                    }

                    .btn-search {
                        margin-top: 0px !important;
                    }
                    nav.navbar .navbar-brand img {
                        max-height: 100%;
                        max-width: 100%;
                    }
                    .outer-search {
                        bottom: auto;
                        margin: 0px 20px 100px 20px !important;
                        top: 35% !important;
                    }
                    
                }

                @media (min-width: 567px) and (max-width: 991px) {
                    .outer-search {
                        position: absolute;
                        z-index: 1;
                        margin: -100px 160px 150px 100px !important;
                        /* margin-left: -25px !important; */
                    }
                }

                @media (min-width: 992px) and (max-width: 1199px) {
                    .outer-search {
                        position: absolute;
                        top: 35% !important;
                        z-index: 1;
                        margin: 0 0 0 60px;
                    }
                }

                .s-search label {
                    white-space: nowrap;
                }

                .outer-search {
                    position: absolute;
                    top: 65%;
                    z-index: 1;
                    margin: 0 0 0 100px;
                }

                .btn-search {
                    border-radius: 3px !important;
                }
                .hom-couples-all {
                    background: wheat;
                }      
                /* Overlay Background */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }

        /* Popup Modal */
        .popup-content {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 450px;
            width: 90%;
            text-align: left;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Title Styling */
        .popup-content h2 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* Price Styling */
        .package-price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }

        /* Bank Details Section */
        .bank-details {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            color: #444;
            margin-top: 10px;
        }

        .bank-details p {
            margin: 8px 0;
            font-size: 14px;
        }

        .bank-details strong {
            color: #333;
        }

        /* Copy Icon */
        .copy-icon {
            cursor: pointer;
            font-size: 14px;
            margin-left: 8px;
            color: #d63384;
        }

        /* Red Note Box */
        .note-box {
            background: #ffe5e5;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
            color: #d63384;
            font-weight: bold;
        }

        /* WhatsApp Button */
        .whatsapp-btn {
            background: #25d366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
            transition: 0.3s;
            border: none;
            width: 100%;
            text-align: center;
        }

        .whatsapp-btn:hover {
            background: #1ebe57;
        }

        .whatsapp-btn img {
            width: 18px;
            margin-right: 8px;
        }

        /* Close Button */
        .close-btn {
            background: #d63384;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .close-btn:hover {
            background: #b02a70;
        }
        .wedd-gall.home-wedd-gall, .ab-team {
    background: wheat;
}
            </style>
            
            <div class="col-lg-12">
                <div style="position: relative;">
                    <!-- <div class="swiper-js-container background-image-holder">
                        <div class="swiper-container swiper-container-horizontal swiper-container-3d swiper-container-coverflow" data-swiper-autoplay="true" data-swiper-effect="coverflow" data-swiper-items="1" data-swiper-space-between="0">
                            <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px);">
                                <!-- Slide ->
                                <div class="swiper-slide swiper-slide-active" data-swiper-autoplay="10000" style="width: 1915px; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg); z-index: 1;">
                                    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 650px; background-size: cover; background-position: center; background-image: url(images/slider_images/slider_image_1.jpg); background-position: bottom bottom;">
                                    </div>
                                    <div class="swiper-slide-shadow-left" style="opacity: 0;"></div>
                                    <div class="swiper-slide-shadow-right" style="opacity: 0;"></div>
                                </div>
                            </div>
                            <!-- Add Arrows ->
                            <div class="swiper-button swiper-button-next">
                            </div>
                            <div class="swiper-button swiper-button-prev">
                            </div>
                        </div>
                    </div> -->
                    <div id="home-carousel" class="carousel slide" data-ride="carousel">
                        <div style="height: 580px;" class="carousel-inner">
                            <!--<div class="carousel-item">-->
                            <!--    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_1.jpg); background-position: bottom bottom;"></div>-->
                            <!--</div>-->
                            <!--<div class="carousel-item">-->
                            <!--    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_2.jpg); background-position: bottom bottom;"></div>-->
                            <!--</div>-->
                            <!--<div class="carousel-item">-->
                            <!--    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_3.jpg); background-position: bottom bottom;"></div>-->
                            <!--</div>-->
                            <!--<div class="carousel-item">-->
                            <!--    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_4.jpg); background-position: bottom bottom;"></div>-->
                            <!--</div>-->
                            <!--<div class="carousel-item">-->
                            <!--    <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_5.jpg); background-position: bottom bottom;"></div>-->
                            <!--</div>-->
                            <div class="carousel-item active">
                                <div class="slice px-3 holder-item holder-item-light has-bg-cover bg-size-cover same-height mobileImage" data-same-height="#div_properties_search" style="height: 580px; background-size: cover; background-image: url(images/slider_images/slider_image_1.png); background-position: bottom bottom;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="container pl-0">
                        <div class="outer-search">
                            <h4 class="text-white text-center mb-4">
                                <span style="text-shadow: 4px 3px 6px #000;">Search Your Soul Mates</span>
                            </h4>
                            <div class="feature feature--boxed-border feature--bg-1 z-depth-2-bottom px-3 py-4 animated animation-ended s-search" data-animation-in="zoomIn" data-animation-delay="400" style="background: #1b1e23b3;">
                                <form name="search_form" id="search_form" class="mt-4" data-toggle="validator" role="form" action="{{route('searchresults')}}" method="POST" style="margin-top: 0px !important;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="gender" class="text-uppercase text-white">I'm Looking For A</label><br/>
                                                <select name="gender" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a gender" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    <option value="female">Female</option>
                                                    <option value="male">Male</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="aged_from" class="text-uppercase text-white">Age From</label><br/>
                                                <select name="aged_from" class="form-control form-control-sm selectpicker" data-placeholder="From age" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @for ($i=18; $i<=75; $i++)
                                                    <option>{{$i<10?"0".$i:$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="aged_to" class="text-uppercase text-white">To</label><br/>
                                                <select name="aged_to" class="form-control form-control-sm selectpicker" data-placeholder="To age" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @for ($i=18; $i<=75; $i++)
                                                    <option>{{$i<10?"0".$i:$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="marital_status" class="text-uppercase text-white">Marital Status</label><br/>
                                                <select name="marital_status" class="form-control form-control-sm selectpicker" data-placeholder="Choose marital status" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @foreach($maritalstatuses as $maritalstatus)
                                                    <option value="{{$maritalstatus->dataid}}">{{$maritalstatus->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="country" class="text-uppercase text-white">Country</label><br/>
                                                <select name="country" class="form-control form-control-sm selectpicker" data-placeholder="Choose country" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @foreach($countries as $country)
                                                    <option value="{{$country->dataid}}">{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="mother_tongue" class="text-uppercase text-white">Language</label><br/>
                                                <select name="mother_tongue" class="form-control form-control-sm selectpicker" data-placeholder="Choose language" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @foreach($mothertongues as $mothertongue)
                                                    <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="caste" class="text-uppercase text-white">Caste</label><br/>
                                                <select name="caste" class="form-control form-control-sm selectpicker" data-placeholder="Choose caste" data-hide-disabled="true">
                                                    <option value="">Select one...</option>
                                                    @foreach($caste as $cst)
                                                    <option value="{{$cst->dataid}}">{{$cst->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                                            <div class="form-group has-feedback">
                                                <label for="profession" class="text-uppercase text-white">Profession</label><br/>
                                                <input type="text" class="form-control form-control-sm" name="profession" id="filter_profession" placeholder="e.g. Doctor">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-12 mr-auto">
                                            <button id="search_button" type="submit" class="btn btn-styled btn-sm btn-block btn-base-1 btn-search" style="padding: 6.5px 5px !important;margin-top: 29px;">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- BANNER SLIDER -->
    <section>
        <div class="hom-ban-sli">
            <div>
                <ul class="ban-sli">
                    <li>
                        <div class="image">
                            <img src="images/ban-bg.jpg" alt="" loading="lazy">
                        </div>
                    </li>
                    <li>
                        <div class="image">
                            <img src="images/banner.jpg" alt="" loading="lazy">
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- QUICK ACCESS -->
    <section>
        <div class="str home-acces-main">
            <div class="container">
                <div class="row">
                    <!-- BACKGROUND SHAPE -->
                    <div class="wedd-shap">
                        <span class="abo-shap-1"></span>
                        <span class="abo-shap-4"></span>
                    </div>
                    <!-- END BACKGROUND SHAPE -->

                    <div class="home-tit">
                        <p>Quick Access</p>
                        <h2><span>Our Services</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                    <div class="home-acces">
                        <ul class="hom-qui-acc-sli">
                            <li>
                                <div class="wow fadeInUp hacc hacc1" data-wow-delay="0.1s">
                                    <div class="con">
                                        <img src="images/icon/user.png" alt="" loading="lazy">
                                        <h4>Browse Profiles</h4>
                                        <p>15K+ Profiles</p>
                                        <a href="/register">View more</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="wow fadeInUp hacc hacc2" data-wow-delay="0.2s">
                                    <div class="con">
                                        <img src="images/icon/gate.png" alt="" loading="lazy">
                                        <h4>Wedding</h4>
                                        <p>15K+ Profiles</p>
                                        <a href="/register">View more</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="wow fadeInUp hacc hacc3" data-wow-delay="0.3s">
                                    <div class="con">
                                        <img src="images/icon/couple.png" alt="" loading="lazy">
                                        <h4>All Services</h4>
                                        <p>15K+ Profiles</p>
                                        <a href="/register">View more</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="wow fadeInUp hacc hacc4" data-wow-delay="0.4s">
                                    <div class="con">
                                        <img src="images/icon/hall.png" alt="" loading="lazy">
                                        <h4>Join Now</h4>
                                        <p>Register with us</p>
                                        <a href="/register">Get started</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="wow fadeInUp hacc hacc3" data-wow-delay="0.3s">
                                    <div class="con">
                                        <img src="images/icon/photo-camera.png" alt="" loading="lazy">
                                        <h4>Photo gallery</h4>
                                        <p>15K+ Profiles</p>
                                        <a href="/register">View more</a>
                                    </div>
                                </div>
                            </li>
                            <!--<li>-->
                            <!--    <div class="wow fadeInUp hacc hacc4" data-wow-delay="0.4s">-->
                            <!--        <div class="con">-->
                            <!--            <img src="images/icon/cake.png" alt="" loading="lazy">-->
                            <!--            <h4>Blog & Articles</h4>-->
                            <!--            <p>Start for free</p>-->
                            <!--            <a href="blog.html">Get started</a>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</li>-->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- TRUST BRANDS -->
    <section>
        <div class="hom-cus-revi">
            <div class="container">
                <div class="row">
                    <div class="home-tit">
                        <p>trusted brand</p>
                        <h2><span>Trust by <b class="num">5000</b>+ Couples</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                    <div class="slid-inn cus-revi">
                        <ul class="slider3">
                            <li>
                                <div class="cus-revi-box">
                                    <div class="revi-im">
                                        <img src="images/user/1.jpg" alt="" loading="lazy">
                                        <i class="cir-com cir-1"></i>
                                        <i class="cir-com cir-2"></i>
                                        <i class="cir-com cir-3"></i>
                                    </div>
                                    <p>Dear brother and sisters.
                                        First of all there is no fraud anyone can do if you are by yourself responsible for your things.
                                        This mane ,Name of USMAN , is very decent and also very professional.
                                        And I am asking  that keep going on.<br>
                                        Best of luck for this good services</p>
                                    <h5>Ayesha</h5>
                                    <span>Dubai</span>
                                </div>
                            </li>
                            <li>
                                <div class="cus-revi-box">
                                    <div class="revi-im">
                                        <img src="images/user/2.jpg" alt="" loading="lazy">
                                        <i class="cir-com cir-1"></i>
                                        <i class="cir-com cir-2"></i>
                                        <i class="cir-com cir-3"></i>
                                    </div>
                                    <p>One of the most reliable Matrimony Websites of Pakistan. Usman Bhai is very dedicated & works in a systematic & organized way to provide suitable match as per client's requirements. I would definitely suggest Urgent Rishta to everyone. Thanks Usman Bhai</p>
                                    <h5>Dr. Sana Ullah</h5>
                                    <span>Pakistan</span>
                                </div>
                            </li>
                            <li>
                                <div class="cus-revi-box">
                                    <div class="revi-im">
                                        <img src="images/user/3.jpg" alt="" loading="lazy">
                                        <i class="cir-com cir-1"></i>
                                        <i class="cir-com cir-2"></i>
                                        <i class="cir-com cir-3"></i>
                                    </div>
                                    <p>A name of total trust and true professionalism with kind behaviour dedicated & works in a systematic & organized way. Highly recommended Urgent Rishta service in the field of match making</p>
                                    <h5>Usman</h5>
                                    <span>Saudia</span>
                                </div>
                            </li>
                            <li>
                                <div class="cus-revi-box">
                                    <div class="revi-im">
                                        <img src="images/user/5.jpg" alt="" loading="lazy">
                                        <i class="cir-com cir-1"></i>
                                        <i class="cir-com cir-2"></i>
                                        <i class="cir-com cir-3"></i>
                                    </div>
                                    <p>Found the best marrige beuro.. usman bhai is the kindest n sweetest person i ever meet .. outclass services .. They treated like a family not like others , fully satisfied 🌺</p>
                                    <h5>Javeria</h5>
                                    <span>Manchester</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="cta-full-wid">
                        <a href="https://maps.app.goo.gl/Gtnsx74wN1JS2p1T7" class="cta-dark">More customer reviews</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- BANNER -->
    <section>
        <div class="str">
            <div class="ban-inn ban-home">
                <div class="container">
                    <div class="row">
                        <div class="hom-ban">
                            <div class="ban-tit">
                                <span><i class="no1">#1</i> Wedding Website</span>
                                <h2>Why choose us</h2>
                                <p>Most Trusted and premium Matrimony Service in the World.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- START -->
    <section>
        <div class="ab-sec2">
            <div class="container">
                <div class="row">
                    <ul>
                        <li>
                            <div class="animate animate__animated animate__slower" data-ani="animate__flipInX"
                                data-dely="0.1">
                                <img src="images/icon/prize.png" alt="" loading="lazy">
                                <h4>Genuine profiles</h4>
                                <p>Contact genuine profiles with 100% verified mobile</p>
                            </div>
                        </li>
                        <li>
                            <div class="animate animate__animated animate__slower" data-ani="animate__flipInX"
                                data-dely="0.3">
                                <img src="images/icon/trust.png" alt="" loading="lazy">
                                <h4>Most trusted</h4>
                                <p>The most trusted wedding matrimony brand lorem</p>
                            </div>
                        </li>
                        <li>
                            <div class="animate animate__animated animate__slower" data-ani="animate__flipInX"
                                data-dely="0.6">
                                <img src="images/icon/rings.png" alt="" loading="lazy">
                                <h4>5000+ weddings</h4>
                                <p>Lakhs of peoples have found their life partner</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- ABOUT START -->
    <section>
        <div class="ab-wel">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="ab-wel-lhs">
                            <span class="ab-wel-3"></span>
                            <img src="images/about/1.jpg" alt="" loading="lazy" class="ab-wel-1">
                            <img src="images/couples/20.jpg" alt="" loading="lazy" class="ab-wel-2">
                            <span class="ab-wel-4"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ab-wel-rhs">
                            <div class="ab-wel-tit">
                                <h2>Welcome to <em>Urgent Rishta</em></h2>
                                <p>Best wedding matrimony It is a long established fact that a reader will be distracted
                                    by the readable content of a page when looking at its layout. </p>
                                <p> <a href="/register">Click here to</a> Start you matrimony service now.</p>
                            </div>
                            <div class="ab-wel-tit-1">
                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have
                                    suffered alteration in some form, by injected humour, or randomised words which
                                    don't look even slightly believable.</p>
                            </div>
                            <div class="ab-wel-tit-2">
                                <ul>
                                    <li>
                                        <div>
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                            <h4>Enquiry <em>+92 304 0227000</em></h4>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            <h4>Get Support <em>urgentrishta.co@gmail.com</em></h4>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- COUNTS START -->
    <section>
        <div class="ab-cont">
            <div class="container">
                <div class="row">
                    <ul>
                        <li>
                            <div class="ab-cont-po">
                                <i class="fa fa-heart-o" aria-hidden="true"></i>
                                <div>
                                    <h4>5K</h4>
                                    <span>Couples pared</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ab-cont-po">
                                <i class="fa fa-users" aria-hidden="true"></i>
                                <div>
                                    <h4>15000+</h4>
                                    <span>Registerents</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ab-cont-po">
                                <i class="fa fa-male" aria-hidden="true"></i>
                                <div>
                                    <h4>8000+</h4>
                                    <span>Mens</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ab-cont-po">
                                <i class="fa fa-female" aria-hidden="true"></i>
                                <div>
                                    <h4>7000+</h4>
                                    <span>Womens</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- MOMENTS START -->
    <section>
        <div class="wedd-tline">
            <div class="container">
                <div class="row">
                    <div class="home-tit">
                        <p>Moments</p>
                        <h2><span>How it works</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                    <div class="inn">
                        <ul>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/rings.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <h5>Register</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>Create your account by providing essential details and preferences. A complete profile increases your chances of finding the perfect match.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <h5>Find your Match</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>Explore a wide range of verified profiles based on your desired criteria, such as age, education, background, and personal values.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/wedding-2.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/love-birds.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <h5>Send Interest</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>Show your interest in a potential match by sending a request. If they accept, you can take the next step toward meaningful communication.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <h5>Get Profile Information</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>Once your interest is accepted, gain access to detailed profile information to ensure compatibility before proceeding further.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/network.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn">
                                    <div class="tline-im animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/chat.png" alt="" loading="lazy">
                                    </div>
                                    <div class="tline-con animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <h5>Start Meetups</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>Engage in conversations, build a connection, and arrange meetups with mutual consent to understand each other better.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="tline-inn tline-inn-reve">
                                    <div class="tline-con animate animate__animated animate__slower"
                                        data-ani="animate__fadeInUp">
                                        <h5>Getting Marriage</h5>
                                        <!--<span>Timing: 7:00 PM</span>-->
                                        <p>When you find the right person, take the next step toward a lifelong commitment and begin your journey toward marriage.</p>
                                    </div>
                                    <div class="tline-im animate animate__animated animate__slow"
                                        data-ani="animate__fadeInUp">
                                        <img src="images/icon/wedding-couple.png" alt="" loading="lazy">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->

    <!-- RECENT COUPLES -->
    <section>
        <div class="hom-couples-all">
            <div class="container">
                <div class="row">
                    <div class="home-tit">
                        <p>trusted brand</p>
                        <h2><span>Recent Couples</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                </div>
            </div>
            <div class="hom-coup-test">
                <ul class="couple-sli">
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/6.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/7.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/8.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/9.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/10.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/3.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/4.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding-video.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="hom-coup-box">
                            <span class="leaf"></span>
                            <img src="images/couples/5.jpg" alt="" loading="lazy">
                            <div class="bx">
                                <h4>Dany & July <span>New York</span></h4>
                                <!--<a href="wedding.html" class="sml-cta cta-dark">View more</a>-->
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <!-- END -->
    
    <!-- TEAM START -->
    <section>
        <div class="ab-team">
            <div class="container">
                <div class="row">
                    <div class="home-tit">
                        <p>OUR PROFESSIONALS</p>
                        <h2><span>Meet Our Team</span></h2>
                        <span class="leaf1"></span>
                    </div>
                    <ul>
                        <li>
                            <div>
                                <img src="images/profiles/6.jpg" alt="" loading="lazy">
                                <h4>Usman Zaheer</h4>
                                <p>CEO</p>
                                <ul class="social-light">
                                    <li><a href="https://www.facebook.com/usman.zaheer.710?mibextid=ZbWKwL "><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="https://x.com/overseasrishta?s=09"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="https://wa.me/923040227000"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw== "><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="images/profiles/7.jpg" alt="" loading="lazy">
                                <h4>Qanita Sundas</h4>
                                <p>Co-Founder</p>
                                <ul class="social-light">
                                    <li><a href="https://www.facebook.com/usman.zaheer.710?mibextid=ZbWKwL "><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="https://x.com/overseasrishta?s=09"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="https://wa.me/923331623144"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw== "><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="images/profiles/8.jpg" alt="" loading="lazy">
                                <h4>Minahil Malik</h4>
                                <p>Relationship Manager</p>
                                <ul class="social-light">
                                    <li><a href="https://www.facebook.com/usman.zaheer.710?mibextid=ZbWKwL "><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="https://x.com/overseasrishta?s=09"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="https://wa.me/447445723296"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw== "><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="images/profiles/9.jpg" alt="" loading="lazy">
                                <h4>Usman Idrees</h4>
                                <p>Client Coordinator</p>
                                <ul class="social-light">
                                    <li><a href="https://www.facebook.com/usman.zaheer.710?mibextid=ZbWKwL "><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="https://x.com/overseasrishta?s=09"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="https://wa.me/923040227000"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.linkedin.com/in/usman-zaheer-3028ab204"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.instagram.com/overseas_rishta?igsh=MXhldzY0ZTlidTU2Yw== "><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->
<!-- GALLERY START -->
    <section>
        <div class="wedd-gall home-wedd-gall">
            <div class="">
                <div class="gall-inn">
                    <div class="home-tit">
                        <p>Events</p>
                        <h2><span>Photo gallery</span></h2>
                        <span class="leaf1"></span>
                        <span class="tit-ani-"></span>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="gal-im animate animate__animated animate__slow" data-ani="animate__flipInX">
                            <img src="images/gallery/1.jpg" class="gal-siz-1" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/2.jpg" class="gal-siz-2" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/3.jpg" class="gal-siz-2" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/4.jpg" class="gal-siz-1" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/5.jpg" class="gal-siz-1" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/6.jpg" class="gal-siz-2" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/7.jpg" class="gal-siz-2" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/8.jpg" class="gal-siz-1" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/9.jpg" class="gal-siz-2" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                        <div class="gal-im animate animate__animated animate__slower" data-ani="animate__flipInX">
                            <img src="images/gallery/10.jpg" class="gal-siz-1" alt="" loading="lazy">
                            <div class="txt">
                                <span>Find your match with us</span>
                                <h4>Urgent Rishta</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END -->
    <!-- FIND YOUR MATCH BANNER -->
    <section>
        <div class="str count">
            <div class="container">
                <div class="row">
                    <div class="fot-ban-inn">
                        <div class="lhs">
                            <h2>Find your perfect Match now</h2>
                            <p>lacinia viverra lectus. Fusce imperdiet ullamcorper metus eu fringilla.Lorem Ipsum is
                                simply dummy text of the printing and typesetting industry.</p>
                            <a href="/register" class="cta-3">Register Now</a>
                            <a href="javascript:void(0);" class="cta-4" onclick="openPopup()">Help & Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
    <!-- Popup Modal -->
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <button class="close-btn" onclick="closePopup()">×</button>
            
            <!-- Title Added -->
            <h2>Consultation Fee</h2>
            <p class="package-price">Fee: 2000 PKR</p>

            <!-- Bank Details -->
            <div class="bank-details">
                <p><strong>Account Title:</strong> Urgent Rishta</p>
                <p>
                    <strong>Account Number:</strong> 07900010047772550026 
                    <span class="copy-icon" onclick="copyToClipboard('07900010047772550026')">📋</span>
                </p>
                <p><strong>Bank Name:</strong> Allied Bank Limited</p>
                <p>
                    <strong>IBAN:</strong> PK12ABPA0010047772550026 
                    <span class="copy-icon" onclick="copyToClipboard('PK12ABPA0010047772550026')">📋</span>
                </p>
                <p><strong>SWIFT Code:</strong> ABPAPKKA</p>
            </div>

            <!-- Note Box -->
            <div class="note-box">
                Please provide a screenshot of your payment on our WhatsApp after completing the transaction.
            </div>

            <!-- WhatsApp Button -->
            <a href="https://wa.me/923040227000?text=I%20have%20made%20the%20payment.%20Here%20is%20the%20screenshot."
               target="_blank" class="whatsapp-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
                Contact Us on WhatsApp
            </a>
        </div>
    </div>

    <script>
        // Open the popup
        function openPopup() {
            document.getElementById("popup").style.display = "block";
        }

        // Close the popup
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            alert("Copied to clipboard: " + text);
        }
    </script>
    <!-- END -->
    
<script type="text/javascript">
    function resetSearchButton() {
        var elem = $("#search_button");
        elem.html("Search");
        elem.prop('disabled', false);
    }

    $(document).ready(function() {
        $('#home-carousel').carousel();
        resetSearchButton();
    });

    // Browser Back restores page from cache with "Processing..." state — reset it
    window.addEventListener('pageshow', function(event) {
        resetSearchButton();
    });

    $("#search_form").on("submit", function() {
        var elem = $("#search_button");
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
    });
    
</script>
@endsection
