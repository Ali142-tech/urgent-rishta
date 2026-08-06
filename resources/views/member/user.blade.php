@extends('layouts.master')
@section('main-content')
<style type="text/css">
    @media (max-width: 991px) {
        .hidden_xs {
            display: none !important;
        }
    }

    @media (min-width: 992px) {
        .visible_xs {
            display: none !important;
        }
    }
</style>
<div class="hidden_xs">
    <nav class="navbar navbar-expand-lg  navbar--style-1 navbar-light bg-default navbar--shadow navbar--uppercase profile-nav">
        <div class="container navbar-container">
            <!-- Brand/Logo -->

            <div class="d-inline-block">
                <!-- Navbar toggler  -->
                <button class="navbar-toggler hamburger hamburger-js hamburger--spring" type="button" data-toggle="collapse" data-target="#navbar_main" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
            <div class="collapse navbar-collapse justify-content-between align-items-center" id="navbar_main">
                <ul class="navbar-nav " data-hover="dropdown" data-animations="zoomIn zoomIn zoomIn zoomIn">
                <li class="nav-item">
                        <a href="{{ url('/member/profile') }}" class="nav-link p_nav active">
                            <i class="fa fa-user"></i>
                            Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link interests p_nav" href="{{ url('/member/profile/listing/interests') }}">
                            <i class="fa fa-heart"></i>
                            Interests
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link messaging p_nav" onclick="profile_load('messaging')">
                            <i class="fa fa-comments-o"></i>
                            Messaging
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>
</div>
<!-- <script>
    function profile_load(page, sp) {
        // alert('here');
        if (typeof message_interval !== 'undefined') {
            clearInterval(message_interval);
        }
        if (page !== '') {
            $.ajax({
                url: "/home/profile/" + page,
                success: function(response) {
                    $("#profile_load").html(response);
                    if (page == 'messaging') {
                        $('body').find('#thread_' + sp).click();
                    }
                    // window.scrollTo(0, 0);
                    if ($(window).width() < 992 && sp == 'alt-sm') {
                        $("html, body").animate({
                            scrollTop: $('.sidebar.sidebar-inverse').offset().top + $('.sidebar.sidebar-inverse').outerHeight(true) - 100
                        }, 500);
                    } else if (sp != 'no') {
                        $(".btn-back-to-top").click();
                    }
                }
            });
            $('.p_nav').removeClass("active");
            $('.l_nav').removeClass("li_active");
            $('.m_nav').removeClass("m_nav_active");

            if (page != 'gallery' || page != 'happy_story' || page != 'my_packages' || page != 'payments' || page == 'change_pass' || page == 'picture_privacy') {
                $('.' + page).addClass("active");
                $('.m_' + page).addClass("m_nav_active");
            }
            if (page == 'gallery' || page == 'happy_story' || page == 'my_packages' || page == 'payments' || page == 'change_pass' || page == 'picture_privacy') {
                $('.' + page).addClass("li_active");
            }

        }
    }
</script> -->
<section class="slice sct-color-2">
    <div class="profile">
        <div class="container">
            <div class="row cols-md-space cols-sm-space cols-xs-space">
                <div class="col-lg-4">
                    <div class="sidebar sidebar-inverse sidebar--style-1 bg-base-1 z-depth-2-top">
                        <div class="sidebar-object mb-0">
                            <!-- Profile picture -->
                            <div class="profile-picture profile-picture--style-2">
                                <div style="border: 10px solid rgba(255, 255, 255, 0.1);width: 200px;border-radius: 50%;margin-top: 30px;">
                                    <div class="profile_img" id="show_img" style="background-image: url('{{ $profile->getProfileImage() }}')"></div>
                                </div>
                                <!-- <div class="profile-connect mt-1 mb-0" id="save_button_section" style="display: none">
                                    <button type="button" class="btn btn-styled btn-xs btn-base-2" id="save_image">Save Image</button>
                                </div> -->
                                <label id="btn_image_edit" class="btn-aux" for="images" style="cursor: pointer;"><i class="fa fa-edit"></i></label>
                                <form id="images_form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" accept="image/png,image/x-png,image/gif,image/jpeg" style="display: none;" id="images" name="images[]" multiple onchange="javascript:uploadImages($('#btn_image_edit'));" />
                                </form>
                            </div>
                            <!-- Profile details -->
                            <div class="profile-details">
                                <h2 class="heading heading-3 strong-500 profile-name">{{$profile->first_name}} {{$profile->last_name}}</h2>
                                <h3 class="heading heading-6 strong-400 profile-occupation mt-3">{{ $profile->profession }}</h3><br/>
                                <div class="profile-stats clearfix mt-2">
                                    <div class="stats-entry" style="width: 100%">
                                        <span class="stats-count">0</span>
                                        <span class="stats-label text-uppercase">Followers</span>
                                    </div>
                                </div>
                                <!-- Profile connect -->
                                <div class="profile-connect mt-5">
                                    <!-- <a href="#" class="btn btn-styled btn-block btn-circle btn-sm btn-base-5">Follow</a>
                                                                                <a href="#" class="btn btn-styled btn-block btn-circle btn-sm btn-base-2">Send message</a> -->
                                    <h2 class="heading heading-5 strong-400">Package Information</h2>
                                </div>
                                <div class="profile-stats clearfix mt-0">
                                    <div class="stats-entry">
                                        <span class="stats-count">{{$profile->lbl_package}}</span>
                                        <span class="stats-label text-uppercase">Current Package</span>
                                    </div>
                                    <!-- <div class="stats-entry">
                                        <span class="stats-count">$0.00</span>
                                        <span class="stats-label text-uppercase">Package Price</span>
                                    </div>
                                </div>
                                <div class="profile-stats clearfix mt-2">
                                    <div class="stats-entry">
                                        <span class="stats-count">None</span>
                                        <span class="stats-label text-uppercase">Payment Gateway</span>
                                    </div>
                                    <div class="stats-entry">
                                        <span class="stats-count">4</span>
                                        <span class="stats-label text-uppercase">Remaining Interest</span>
                                    </div>
                                </div>
                                <div class="profile-stats clearfix mt-2">
                                    <div class="stats-entry">
                                        <span class="stats-count">6</span>
                                        <span class="stats-label text-uppercase">Remaining Message</span>
                                    </div> -->
                                    <div class="stats-entry" id="images_stats">
                                        <span class="stats-count">{{ $profile->getImageCount() }}</span>
                                        <span class="stats-label text-uppercase">Photo in Gallery</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Profile stats -->
                            <div class="profile-useful-links clearfix">
                                <div class="useful-links">
                                    <a class="btn btn-styled btn-sm btn-white z-depth-2-bottom mb-3 interests l_nav" href="{{ url('/member/profile/listing/interests') }}">
                                        <b style="font-size: 12px"><i class="fa fa-heart"></i> Interests</b>
                                    </a>
                                    <a class="btn btn-styled btn-sm btn-white z-depth-2-bottom mb-3 gallery l_nav" id="gallery">
                                        <b style="font-size: 12px"><i class="fa fa-camera"></i> Gallery</b>
                                    </a>
                                    <a class="btn btn-styled btn-sm btn-white z-depth-2-bottom mb-3 picture_privacy l_nav" onclick="javascript:renderImagesModal();">
                                        <b style="font-size: 12px"><i class="fa fa-photo"></i> Manage Pictures</b>
                                    </a>
                                    <a class="btn btn-styled btn-sm btn-white z-depth-2-bottom mb-3 change_pass l_nav" href="{{ url('member/profile/password/update') }}">
                                        <b style="font-size: 12px"><i class="fa fa-key"></i> Change Password</b>
                                    </a>
                                    <a class="btn btn-styled btn-sm btn-white z-depth-2-bottom mb-3 change_pass l_nav" onclick="javascript:deleteAccount($(this));">
                                        <b style="font-size: 12px"><i class="fa fa-close"></i> Close Account</b>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="visible_xs align-items-center">
                    <div class="container mb-4 text-center">
                        <ul class="inline-links inline-links--style-3">
                            <li>
                                <a href="/home/profile" class="c-base-1 xs_nav_item m_profile m_nav m_nav_active">
                                    <i class="fa fa-user"></i>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="c-base-1 xs_nav_item m_my_interests m_nav" href="{{ url('/member/profile/listing/interests') }}">
                                    <i class="fa fa-heart"></i>
                                    My Interests
                                </a>
                            </li>
                            <li>
                                <a class="c-base-1 xs_nav_item m_short_list m_nav" href="{{ url('/member/profile/listing/shortlist') }}">
                                    <i class="fa fa-list-ul"></i>
                                    Shortlist
                                </a>
                            </li>
                            <li>
                                <a class="c-base-1 xs_nav_item m_followed_users m_nav" href="{{ url('/member/profile/listing/follow') }}">
                                    <i class="fa fa-star"></i>
                                    Followed Users
                                </a>
                            </li>
                            <!-- <li>
                                <a class="c-base-1 xs_nav_item m_messaging m_nav" onclick="profile_load('messaging', 'no')">
                                    <i class="fa fa-comments-o"></i>
                                    Messaging
                                </a>
                            </li> -->
                            <li>
                                <a class="c-base-1 xs_nav_item m_ignored_list m_nav" href="{{ url('/member/profile/listing/ignore') }}">
                                    <i class="fa fa-ban"></i>
                                    Ignored List
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="widget">
                        <div class="card z-depth-2-top" id="profile_load">
                            <div class="card-title">
                                <h3 class="text-uppercase heading heading-6 strong-500 pull-left">
                                    <b>Profile Information</b>
                                </h3>
                                <!-- <div class="pull-right">
                                    <a href="/home/profile/edit_full_profile" class="btn btn-base-1 btn-sm btn-shadow">
                                        <i class="fa fa-edit"></i>
                                        Edit All
                                    </a>
                                </div> -->
                            </div>
                            <div class="card-body pt-2" style="padding: 1rem 0.5rem;">
                                <!-- Contact information -->
                                <div id="section_introduction">
                                    <div class="mb-2 pl-3">
                                        <b>Member ID - </b>
                                        <b class="c-base-1">{{$profile->dataid}}</b>
                                    </div>
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_introduction">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Introduction </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('introduction')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-slick">
                                                        <tbody>
                                                            <tr><td class="">{{ $profile->intro }}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_introduction" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Introduction </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow mb-1" onclick="save_section($(this), 'introduction')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow mb-1" onclick="load_section('introduction')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_introduction" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <textarea name="introduction" class="form-control no-resize" rows="5" required="required">{{ $profile->intro }} </textarea>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_basic_info">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_basic_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Basic Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('basic_info')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>First Name</b></td>
                                                                <td>{{$profile->first_name}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Last Name</b></td>
                                                                <td>{{$profile->last_name}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Gender</b></td>
                                                                <td>{{$profile->gender}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Email</b></td>
                                                                <td>{{$profile->email}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Date Of Birth</b></td>
                                                                <td colspan="3">{{$profile->birthday}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Age</b></td>
                                                                <td>{{date_diff(date_create($profile->birthday), date_create('now'))->y}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Marital Status</b></td>
                                                                <td>{{$profile->lbl_marital_status}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Number Of Children</b></td>
                                                                <td>{{$profile->children}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Area</b></td>
                                                                <td>{{$profile->area}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>On Behalf</b></td>
                                                                <td>{{$profile->profile_for}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Mobile</b></td>
                                                                <td>{{$profile->contact_mobile_number}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_basic_info" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Basic Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'basic_info')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('basic_info')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_basic_info" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="first_name" class="text-uppercase c-gray-light">First Name</label>
                                                            <input type="text" class="form-control no-resize" name="first_name" value="{{$profile->first_name}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="last_name" class="text-uppercase c-gray-light">Last Name</label>
                                                            <input type="text" class="form-control no-resize" name="last_name" value="{{$profile->last_name}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="first_name" class="text-uppercase c-gray-light">Gender</label>
                                                            <select name="gender" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a gender" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option value="male" {{$profile->gender=="male"?"selected":""}}>Male</option>
                                                                <option value="female" {{$profile->gender=="female"?"selected":""}}>Female</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="email" class="text-uppercase c-gray-light">Email</label>
                                                            <input type="email" class="form-control no-resize" name="email" value="{{$profile->email}}" disabled="disabled">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="date_of_birth" class="text-uppercase c-gray-light">Date Of Birth </label>
                                                            <select name="day" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a day for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                @for ($i=1; $i<=31; $i++)
                                                                <option {{substr($profile->birthday, 8, 2)==($i<10?"0".$i:$i)?"selected":""}}>{{$i<10?"0".$i:$i}}</option>
                                                                @endfor
                                                            </select>

                                                            <select name="month" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a month for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                <option value="01" {{substr($profile->birthday, 5, 2)=="01"?"selected":""}}>January</option>
                                                                <option value="02" {{substr($profile->birthday, 5, 2)=="02"?"selected":""}}>February</option>
                                                                <option value="03" {{substr($profile->birthday, 5, 2)=="03"?"selected":""}}>March</option>
                                                                <option value="04" {{substr($profile->birthday, 5, 2)=="04"?"selected":""}}>April</option>
                                                                <option value="05" {{substr($profile->birthday, 5, 2)=="05"?"selected":""}}>May</option>
                                                                <option value="06" {{substr($profile->birthday, 5, 2)=="06"?"selected":""}}>June</option>
                                                                <option value="07" {{substr($profile->birthday, 5, 2)=="07"?"selected":""}}>July</option>
                                                                <option value="08" {{substr($profile->birthday, 5, 2)=="08"?"selected":""}}>August</option>
                                                                <option value="09" {{substr($profile->birthday, 5, 2)=="09"?"selected":""}}>September</option>
                                                                <option value="10" {{substr($profile->birthday, 5, 2)=="10"?"selected":""}}>October</option>
                                                                <option value="11" {{substr($profile->birthday, 5, 2)=="11"?"selected":""}}>November</option>
                                                                <option value="12" {{substr($profile->birthday, 5, 2)=="12"?"selected":""}}>December</option>
                                                            </select>

                                                            <select name="year" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a year for date of birth" data-hide-disabled="true" required="required">
                                                                <option value="" disabled>Choose one</option>
                                                                @for ($i=2002; $i>=1927; $i--)
                                                                <option {{substr($profile->birthday, 0, 4)==$i?"selected":""}}>{{$i}}</option>
                                                                @endfor
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <!-- <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="age" class="text-uppercase c-gray-light">Age</label>
                                                            <input type="number" class="form-control no-resize" name="age" value="" min="0" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div> -->
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="marital_status" class="text-uppercase c-gray-light">Marital Status</label>
                                                            <select name="marital_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($maritalstatuses as $maritalstatus)
                                                                <option value="{{$maritalstatus->dataid}}" {{$profile->marital_status==$maritalstatus->dataid?"selected":""}}>{{$maritalstatus->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="children" class="text-uppercase c-gray-light">Number Of Children</label>
                                                            <input type="number" class="form-control no-resize" name="children" value="{{$profile->children}}" min="0" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="area" class="text-uppercase c-gray-light">Area</label>
                                                            <input type="text" class="form-control no-resize" name="area" value="{{$profile->area}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="on_behalf" class="text-uppercase c-gray-light">On Behalf</label>
                                                            <select name="on_behalf" onChange="(this.value,this)" class="form-control form-control-sm selectpicker present_on_behalf_edit" data-placeholder="Choose a on behalf" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->profile_for=="Self"?"selected":""}}>Self</option>
                                                                <option {{$profile->profile_for=="Son"?"selected":""}}>Son</option>
                                                                <option {{$profile->profile_for=="Daughter"?"selected":""}}>Daughter</option>
                                                                <option {{$profile->profile_for=="Brother"?"selected":""}}>Brother</option>
                                                                <option {{$profile->profile_for=="Sister"?"selected":""}}>Sister</option>
                                                                <option {{$profile->profile_for=="Relative"?"selected":""}}>Relative</option>
                                                                <option{{$profile->profile_for=="Friend"?"selected":""}}>Friend</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="contact_mobile_number" class="text-uppercase c-gray-light">Mobile</label>
                                                            <input type="number" class="form-control no-resize" name="contact_mobile_number" value="{{$profile->contact_mobile_number}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_education_and_career">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_education_and_career">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Education And Career </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_education_and_career" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('education_and_career')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_education_and_career" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('education_and_career')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('education_and_career')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Highest Education</b></td>
                                                                <td>{{$profile->lbl_education}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Occupation</b></td>
                                                                <td>{{$profile->profession}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Annual Income</b></td>
                                                                <td colspan="3">{{$profile->salary}}</td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_education_and_career" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Education And Career </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'education_and_career')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('education_and_career')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_education_and_career" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="education" class="text-uppercase c-gray-light">Highest Education</label>
                                                            <select name="education" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a degree" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($education as $degree)
                                                                <option value="{{$degree->dataid}}" {{$profile->education==$degree->dataid?"selected":""}}>{{$degree->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="profession" class="text-uppercase c-gray-light">Occupation</label>
                                                            <input type="text" class="form-control no-resize" name="profession" value="{{$profile->profession}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="salary" class="text-uppercase c-gray-light">Annual Income</label>
                                                            <input type="text" class="form-control no-resize" name="salary" value="{{$profile->salary}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_physical_attributes">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_physical_attributes">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Physical Attributes </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_physical_attributes" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('physical_attributes')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_physical_attributes" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('physical_attributes')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('physical_attributes')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Height</b></td>
                                                                <td>{{$profile->height}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Weight</b></td>
                                                                <td>{{$profile->weight}}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Eye Color</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Hair Color</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Complexion</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Blood Group</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Body Type</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Body Art</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Any Disability</b></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_physical_attributes" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Physical Attributes </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'physical_attributes')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('physical_attributes')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_physical_attributes" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="height" class="text-uppercase c-gray-light">Height</label>
                                                            <input type="text" class="form-control no-resize" name="height" value="{{$profile->height}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="weight" class="text-uppercase c-gray-light">Weight</label>
                                                            <input type="text" class="form-control no-resize" name="weight" value="{{$profile->weight}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="eye_color" class="text-uppercase c-gray-light">Eye Color</label>
                                                            <input type="text" class="form-control no-resize" name="eye_color" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="hair_color" class="text-uppercase c-gray-light">Hair Color</label>
                                                            <input type="text" class="form-control no-resize" name="hair_color" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="complexion" class="text-uppercase c-gray-light">Complexion</label>
                                                            <input type="text" class="form-control no-resize" name="complexion" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="blood_group" class="text-uppercase c-gray-light">Blood Group</label>
                                                            <input type="text" class="form-control no-resize" name="blood_group" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="body_type" class="text-uppercase c-gray-light">Body Type</label>
                                                            <input type="text" class="form-control no-resize" name="body_type" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="body_art" class="text-uppercase c-gray-light">Body Art</label>
                                                            <input type="text" class="form-control no-resize" name="body_art" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="any_disability" class="text-uppercase c-gray-light">Any Disability</label>
                                                            <input type="text" class="form-control no-resize" name="any_disability" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_language">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_language">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Language </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_language" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('language')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_language" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('language')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('language')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Mother Tongue</b></td>
                                                                <td>{{$profile->lbl_mother_tongue}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Language</b></td>
                                                                <td>{{$profile->lbl_language}}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Speak</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Read</b></td>
                                                                <td></td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_language" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Language </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'language')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('language')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_language" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="mother_tongue" class="text-uppercase c-gray-light">Mother Tongue</label>
                                                            <select name="mother_tongue" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a mother tongue" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}" {{$profile->mother_tongue==$mothertongue->dataid?"selected":""}}>{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="language" class="text-uppercase c-gray-light">Language</label>
                                                            <select name="language" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a language" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}" {{$profile->language==$mothertongue->dataid?"selected":""}}>{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="speak" class="text-uppercase c-gray-light">Speak</label>
                                                            <select name="speak" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a speak" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="read" class="text-uppercase c-gray-light">Read</label>
                                                            <select name="read" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a read" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}">{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div id="section_hobbies_and_interest">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_hobbies_and_interest">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Hobbies And Interests </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_hobbies_and_interest" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('hobbies_and_interest')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_hobbies_and_interest" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('hobbies_and_interest')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('hobbies_and_interest')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Hobby</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Interest</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Music</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Books</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Movie</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>TV Show</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Sports Show</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Fitness Activity</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Cuisine</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Dress Style</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_hobbies_and_interest" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Hobbies And Interests </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'hobbies_and_interest')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('hobbies_and_interest')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_hobbies_and_interest" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="hobby" class="text-uppercase c-gray-light">Hobby</label>
                                                            <input type="text" class="form-control no-resize" name="hobby" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="interest" class="text-uppercase c-gray-light">Interest</label>
                                                            <input type="text" class="form-control no-resize" name="interest" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="music" class="text-uppercase c-gray-light">Music</label>
                                                            <input type="text" class="form-control no-resize" name="music" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="books" class="text-uppercase c-gray-light">Books</label>
                                                            <input type="text" class="form-control no-resize" name="books" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="movie" class="text-uppercase c-gray-light">Movie</label>
                                                            <input type="text" class="form-control no-resize" name="movie" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="tv_show" class="text-uppercase c-gray-light">TV Show</label>
                                                            <input type="text" class="form-control no-resize" name="tv_show" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sports_show" class="text-uppercase c-gray-light">Sports Show</label>
                                                            <input type="text" class="form-control no-resize" name="sports_show" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="fitness_activity" class="text-uppercase c-gray-light">Fitness Activity</label>
                                                            <input type="text" class="form-control no-resize" name="fitness_activity" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="cuisine" class="text-uppercase c-gray-light">Cuisine</label>
                                                            <input type="text" class="form-control no-resize" name="cuisine" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="dress_style" class="text-uppercase c-gray-light">Dress Style</label>
                                                            <input type="text" class="form-control no-resize" name="dress_style" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div id="section_personal_attitude_and_behavior">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_personal_attitude_and_behavior">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Personal Attitude And Behavior </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_personal_attitude_and_behavior" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_personal_attitude_and_behavior" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Affection</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Humor</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Political View</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Religious Service</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_personal_attitude_and_behavior" style="display: none">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Personal Attitude And Behavior </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'personal_attitude_and_behavior')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('personal_attitude_and_behavior')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_personal_attitude_and_behavior" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="affection" class="text-uppercase c-gray-light">Affection</label>
                                                            <input type="text" class="form-control no-resize" name="affection" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="humor" class="text-uppercase c-gray-light">Humor</label>
                                                            <input type="text" class="form-control no-resize" name="humor" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="political_view" class="text-uppercase c-gray-light">Political View</label>
                                                            <input type="text" class="form-control no-resize" name="political_view" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="religious_service" class="text-uppercase c-gray-light">Religious Service</label>
                                                            <input type="text" class="form-control no-resize" name="religious_service" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <div id="section_residency_information">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_residency_information">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Residency Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('residency_information')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Birth Country</b></td>
                                                                <td>{{ $profile->lbl_con_of_birth}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Residency Country</b></td>
                                                                <td>{{ $profile->lbl_con_of_residence}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Citizenship Country</b></td>
                                                                <td>{{ $profile->lbl_con_of_citizenship}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Grow Up Country</b></td>
                                                                <td>{{ $profile->lbl_con_grew_up}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Immigration Status</b></td>
                                                                <td colspan="3">{{ $profile->immigration_status}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_residency_information" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Residency Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'residency_information')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('residency_information')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_residency_information" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_birth" class="text-uppercase c-gray-light">Birth Country</label>
                                                            <select name="con_of_birth" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_birth==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_residence" class="text-uppercase c-gray-light">Residency Country</label>
                                                            <select name="con_of_residence" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_residence==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_citizenship" class="text-uppercase c-gray-light">Citizenship Country</label>
                                                            <select name="con_of_citizenship" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a citizenship_country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_citizenship==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_grew_up" class="text-uppercase c-gray-light">Grow Up Country</label>
                                                            <select name="con_grew_up" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_grew_up==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="immigration_status" class="text-uppercase c-gray-light">Immigration Status</label>
                                                            <input type="text" class="form-control no-resize" name="immigration_status" value="{{$profile->immigrarion_status}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_spiritual_and_social_background">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_spiritual_and_social_background">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Spiritual And Social Background </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_spiritual_and_social_background" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('spiritual_and_social_background')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_spiritual_and_social_background" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('spiritual_and_social_background')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('spiritual_and_social_background')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Religion</b></td>
                                                                <td>{{$profile->lbl_religion}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Caste / Sect</b></td>
                                                                <td>{{$profile->lbl_caste}} / {{$profile->sect}}</td>
                                                            </tr>

                                                            <!-- <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Sub-Caste</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Ethnicity</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Personal Value</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Family Value</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Community Value</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Family Status</b></td>
                                                                <td></td>
                                                            </tr> -->
                                                            <!-- <tr>
                                                                <td class="td-label">Manglik</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_spiritual_and_social_background" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Spiritual And Social Background </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'spiritual_and_social_background')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('spiritual_and_social_background')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_spiritual_and_social_background" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="religion" class="text-uppercase c-gray-light">Religion</label>
                                                            <select name="religion" onChange="(this.value,this)" class="form-control form-control-sm selectpicker present_religion_edit" data-placeholder="Choose a religion" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($religions as $religion)
                                                                <option value="{{$religion->dataid}}" {{$profile->religion==$religion->dataid?"selected":""}}>{{$religion->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="caste" class="text-uppercase c-gray-light">Caste</label>
                                                            <select class="form-control form-control-sm selectpicker present_caste_edit" name="caste" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($caste as $cst)
                                                                <option value="{{$cst->dataid}}" {{$profile->caste==$cst->dataid?"selected":""}}>{{$cst->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="sect" class="text-uppercase c-gray-light">Sect</label>
                                                            <input type="text" class="form-control no-resize" name="sect" value="{{$profile->sect}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sub_caste" class="text-uppercase c-gray-light">Sub Caste</label>
                                                            <select class="form-control form-control-sm selectpicker present_sub_caste_edit" name="sub_caste">
                                                                <option value="">Choose A Caste First</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="ethnicity" class="text-uppercase c-gray-light">Ethnicity</label>
                                                            <input type="text" class="form-control no-resize" name="ethnicity" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="personal_value" class="text-uppercase c-gray-light">Personal Value</label>
                                                            <input type="text" class="form-control no-resize" name="personal_value" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_value" class="text-uppercase c-gray-light">Family Value</label>
                                                            <select name="family_value" onChange="(this.value,this)" class="form-control form-control-sm selectpicker family_value_edit" data-placeholder="Choose a family_value" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Traditional</option>
                                                                <option value="2">Moderate</option>
                                                                <option value="3">Liberal</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="community_value" class="text-uppercase c-gray-light">Community Value</label>
                                                            <input type="text" class="form-control no-resize" name="community_value" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_value" class="text-uppercase c-gray-light">Family Status</label>
                                                            <select name="family_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker family_status_edit" data-placeholder="Choose a family_status" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">High Class</option>
                                                                <option value="2">Middle Class</option>
                                                                <option value="3">Low Class</option>
                                                                <option value="4">Upper Middle Class </option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="u_manglik" class="text-uppercase c-gray-light">Manglik</label>

                                                            <select name="u_manglik" class="form-control form-control-sm selectpicker" data-placeholder="Choose a manglik" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">I don't know</option>
                                                            </select>
                                                            <!- <select name="manglik" onChange="(this.value,this)" class="form-control form-control-sm selectpicker"   data-placeholder="Choose a manglik" tabindex="2" data-hide-disabled="true" ><option value="">Choose one</option><option value="1" >Yes</option><option value="2" >No</option><option value="3" >Doesn't Matter</option></select> ->
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <!-- <div id="section_life_style">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_life_style">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Life Style </h3>
                                                <div class="pull-right">
                                                    <button type="button" id="unhide_life_style" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('life_style')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_life_style" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('life_style')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button>
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('life_style')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Diet</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Drink</b></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Smoke</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Living With</b></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_life_style" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Life Style </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'life_style')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('life_style')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_life_style" class="form-default" role="form">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="diet" class="text-uppercase c-gray-light">Diet</label>
                                                            <input type="text" class="form-control no-resize" name="diet" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="drink" class="text-uppercase c-gray-light">Drink</label>
                                                            <select name="drink" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a drink" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Doesn't Matter</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="smoke" class="text-uppercase c-gray-light">Smoke</label>
                                                            <select name="smoke" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a smoke" tabindex="2" data-hide-disabled="true">
                                                                <option value="">Choose one</option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Doesn't Matter</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="living_with" class="text-uppercase c-gray-light">Living With</label>
                                                            <input type="text" class="form-control no-resize" name="living_with" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->
                                <div id="section_permanent_address">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_permanent_address">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Permanent Address </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_permanent_address" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('permanent_address')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_permanent_address" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('permanent_address')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('permanent_address')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Country</b></td>
                                                                <td>{{ $profile->lbl_con_of_residence }}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>State</b></td>
                                                                <td>{{ $profile->lbl_state }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>City</b></td>
                                                                <td>{{ $profile->lbl_city }}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Society</b></td>
                                                                <td>{{ $profile->society }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_permanent_address" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Permanent Address </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'permanent_address')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('permanent_address')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_permanent_address" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="con_of_residence" class="text-uppercase c-gray-light">Country</label>
                                                            <select name="con_of_residence" onchange="javascript:loadSelect('{{url('states')}}', this.value, $('#state'), '{{$profile->state}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->con_of_residence==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="state" class="text-uppercase c-gray-light">State</label>
                                                            <select id="state" name="state" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/0', $('#city'), '{{$profile->city}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a state" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose a country first</option>

                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="city" class="text-uppercase c-gray-light">City</label>
                                                            <select id="city" name="city" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose a state first</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="society" class="text-uppercase c-gray-light">Society</label>
                                                            <input type="text" class="form-control no-resize" name="society" value="{{$profile->society}}">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_family_info">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_family_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Family Information </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_family_info" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('family_info')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_family_info" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('family_info')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('family_info')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Father</b></td>
                                                                <td>{{ $profile->father }}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Mother</b></td>
                                                                <td>{{ $profile->mother }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Brother</b></td>
                                                                <td>{{ $profile->brother }}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Sister</b></td>
                                                                <td>{{ $profile->sister }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_family_info" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Family Information </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'family_info')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('family_info')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_family_info" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="father" class="text-uppercase c-gray-light">Father</label>
                                                            <input type="text" class="form-control no-resize" name="father" value="{{$profile->father}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="mother" class="text-uppercase c-gray-light">Mother</label>
                                                            <input type="text" class="form-control no-resize" name="mother" value="{{$profile->mother}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="brother" class="text-uppercase c-gray-light">Brother</label>
                                                            <input type="text" class="form-control no-resize" name="brother" value="{{$profile->brother}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="sister" class="text-uppercase c-gray-light">Sister</label>
                                                            <input type="text" class="form-control no-resize" name="sister" value="{{$profile->sister}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_additional_personal_details">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_additional_personal_details">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Additional Personal Details </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_additional_personal_details" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('additional_personal_details')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_additional_personal_details" style="display: none" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('additional_personal_details')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('additional_personal_details')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Home District</b></td>
                                                                <td>{{$profile->district}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Family Residence</b></td>
                                                                <td>{{$profile->family_residence}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Father's Occupation</b></td>
                                                                <td>{{$profile->father_profession}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Special Circumstances</b></td>
                                                                <td>{{$profile->special_circumstances}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_additional_personal_details" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Additional Personal Details </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'additional_personal_details')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('additional_personal_details')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_additional_personal_details" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="district" class="text-uppercase c-gray-light">Home District</label>
                                                            <input type="text" class="form-control no-resize" name="district" value="{{$profile->district}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="family_residence" class="text-uppercase c-gray-light">Family Residence</label>
                                                            <select name="family_residence" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a family residence option" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->family_residence=="Rent"?"selected":""}}>Rent</option>
                                                                <option {{$profile->family_residence=="Own"?"selected":""}}>Own</option>
                                                                <option {{$profile->family_residence=="Do not know"?"selected":""}}>Do not know</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="father_profession" class="text-uppercase c-gray-light">Father's Occupation</label>
                                                            <input type="text" class="form-control no-resize" name="father_profession" value="{{$profile->father_profession}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="special_circumstances" class="text-uppercase c-gray-light">Special Circumstances</label>
                                                            <input type="text" class="form-control no-resize" name="special_circumstances" value="{{$profile->special_circumstances}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="section_partner_expectation">
                                    <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                        <div id="info_partner_expectation">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Partner Expectation </h3>
                                                <div class="pull-right">
                                                    <!-- <button type="button" id="unhide_partner_expectation" style="display: none" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="unhide_section('partner_expectation')">
                                                        <i class="fa fa-unlock"></i>
                                                        Show
                                                    </button>
                                                    <button type="button" id="hide_partner_expectation" class="btn btn-dark btn-sm btn-icon-only btn-shadow mb-1" onclick="hide_section('partner_expectation')">
                                                        <i class="fa fa-lock"></i>
                                                        Hide
                                                    </button> -->
                                                    <button type="button" class="btn btn-base-1 btn-sm btn-icon-only btn-shadow mb-1" onclick="edit_section('partner_expectation')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>General Requirement</b></td>
                                                                <td colspan="3">{{$profile->rgen_req}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Age</b></td>
                                                                <td>{{$profile->rage}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Height</b></td>
                                                                <td>{{$profile->rheight}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Marital Status</b></td>
                                                                <td>{{$profile->lbl_rmarital_status}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>With Children Acceptables</b></td>
                                                                <td>{{$profile->rwith_children}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Country Of Residence</b></td>
                                                                <td>{{$profile->lbl_rcon_of_residence}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>City</b></td>
                                                                <td>{{$profile->lbl_rcity}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Religion</b></td>
                                                                <td>{{$profile->lbl_rreligion}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Caste / Sect</b></td>
                                                                <td>{{$profile->lbl_rcaste}} / {{$profile->rsect}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Education</b></td>
                                                                <td>{{$profile->lbl_reducation}}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Profession</b></td>
                                                                <td>{{$profile->rprofession}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Mother Tongue</b></td>
                                                                <td colspan="3">{{$profile->lbl_rmother_tongue}}</td>
                                                                <!-- <td height="30" style="padding-left: 5px;" class="font-dark"><b>Any Disability</b></td>
                                                                <td></td> -->
                                                            </tr>
                                                            <tr>
                                                                <!-- <td height="30" style="padding-left: 5px;" class="font-dark"><b>Family Value</b></td>
                                                                <td></td> -->
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Prefered Country</b></td>
                                                                <td colspan="3">{{$profile->lbl_rcon_pref}}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Prefered State</b></td>
                                                                <td></td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Prefered Status</b></td>
                                                                <td></td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="edit_partner_expectation" style="display: none;">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Partner Expectation </h3>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-success btn-sm btn-icon-only btn-shadow" onclick="save_section($(this), 'partner_expectation')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-icon-only btn-shadow" onclick="load_section('partner_expectation')">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class='clearfix'></div>
                                            <form id="form_partner_expectation" class="form-default" role="form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="rgen_req" class="text-uppercase c-gray-light">General Requirement</label>
                                                            <input type="text" class="form-control no-resize" name="rgen_req" value="{{$profile->rgen_req}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rage" class="text-uppercase c-gray-light">Age</label>
                                                            <input type="text" class="form-control no-resize" name="rage" value="{{$profile->rage}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rheight" class="text-uppercase c-gray-light">Height</label>
                                                            <input type="text" class="form-control no-resize" name="rheight" value="{{$profile->rheight}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rmarital_status" class="text-uppercase c-gray-light">Marital Status</label>
                                                            <select name="rmarital_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($maritalstatuses as $maritalstatus)
                                                                <option value="{{$maritalstatus->dataid}}" {{$profile->rmarital_status==$maritalstatus->dataid?"selected":""}}>{{$maritalstatus->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rwith_children" class="text-uppercase c-gray-light">With Children Acceptables</label>
                                                            <select name="rwith_children" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a with children acceptable option" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                <option {{$profile->rwith_children=="Yes"?"selected":""}}>Yes</option>
                                                                <option {{$profile->rwith_children=="No"?"selected":""}}>No</option>
                                                                <option {{$profile->rwith_children=="Does not matter"?"selected":""}}>Does not matter</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rcon_of_residence" class="text-uppercase c-gray-light">Country Of Residence</label>
                                                            <select name="rcon_of_residence" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/1', $('#rcity'), '{{$profile->rcity}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->rcon_of_residence==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rcity" class="text-uppercase c-gray-light">City</label>
                                                            <select id="rcity" name="rcity" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose a country first</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rreligion" class="text-uppercase c-gray-light">Religion</label>
                                                            <select name="rreligion" onChange="(this.value,this)" class="form-control form-control-sm selectpicker prefered_religion_edit" data-placeholder="Choose a religion" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($religions as $religion)
                                                                <option value="{{$religion->dataid}}" {{$profile->rreligion==$religion->dataid?"selected":""}}>{{$religion->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rcaste" class="text-uppercase c-gray-light">Caste</label>
                                                            <select class="form-control form-control-sm selectpicker prefered_caste_edit" name="rcaste" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($caste as $cst)
                                                                <option value="{{$cst->dataid}}" {{$profile->rcaste==$cst->dataid?"selected":""}}>{{$cst->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="rsect" class="text-uppercase c-gray-light">Sect</label>
                                                            <input type="text" class="form-control no-resize" name="rsect" value="{{$profile->rsect}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="reducation" class="text-uppercase c-gray-light">Education</label>
                                                            <select name="reducation" onChange="(this.value,this)" class="form-control form-control-sm selectpicker prefered_education_edit" data-placeholder="Choose an education" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($education as $degree)
                                                                <option value="{{$degree->dataid}}" {{$profile->reducation==$degree->dataid?"selected":""}}>{{$degree->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="rprofession" class="text-uppercase c-gray-light">Profession</label>
                                                            <input type="text" class="form-control no-resize" name="rprofession" value="{{$profile->rprofession}}" required="required">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="rmother_tongue" class="text-uppercase c-gray-light">Mother Tongue</label>
                                                            <select name="rmother_tongue" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a mother tongue" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($mothertongues as $mothertongue)
                                                                <option value="{{$mothertongue->dataid}}" {{$profile->rmother_tongue==$mothertongue->dataid?"selected":""}}>{{$mothertongue->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="partner_any_disability" class="text-uppercase c-gray-light">Any Disability</label>
                                                            <input type="text" class="form-control no-resize" name="partner_any_disability" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <div class="row">
                                                    <!-- <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="partner_family_value" class="text-uppercase c-gray-light">Family Value</label>
                                                            <input type="text" class="form-control no-resize" name="partner_family_value" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div> -->
                                                    <div class="col-md-12">
                                                        <div class="form-group has-feedback">
                                                            <label for="rcon_pref" class="text-uppercase c-gray-light">Prefered Country</label>
                                                            <select name="rcon_pref" onChange="(this.value,this)" class="form-control form-control-sm selectpicker prefered_country_edit" data-placeholder="Choose a country" tabindex="2" data-hide-disabled="true" required="required">
                                                                <option value="">Choose one</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{$country->dataid}}" {{$profile->rcon_pref==$country->dataid?"selected":""}}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="prefered_state" class="text-uppercase c-gray-light">Prefered State</label>
                                                            <select class="form-control form-control-sm selectpicker permanent_state_edit" name="prefered_state">
                                                                <option value="">Choose A Country First</option>
                                                            </select>
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="prefered_status" class="text-uppercase c-gray-light">Prefered Status</label>
                                                            <input type="text" class="form-control no-resize" name="prefered_status" value="">
                                                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                            </form>
                                        </div>
                                    </div>
                                    <script>
                                        $(".prefered_country_edit").change(function() {
                                            var country_id = $(".prefered_country_edit").val();
                                            if (country_id == "") {
                                                $(".prefered_state_edit").html("<option value=''>Choose A Country First</option>");
                                            } else {
                                                $.ajax({
                                                    url: "/home/get_dropdown_by_id/state/country_id/" + country_id,
                                                    // form action url
                                                    type: 'POST',
                                                    // form submit method get/post
                                                    dataType: 'html',
                                                    // request type html/json/xml
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function(data) {
                                                        $(".prefered_state_edit").html(data);
                                                    },
                                                    error: function(e) {
                                                        console.log(e)
                                                    }
                                                });
                                            }
                                        });
                                        $(".prefered_religion_edit").change(function() {
                                            var religion_id = $(".prefered_religion_edit").val();
                                            if (religion_id == "") {
                                                $(".prefered_caste_edit").html("<option value=''>Choose A Religion First</option>");
                                                $(".prefered_sub_caste_edit").html("<option value=''>Choose A Caste First</option>");
                                            } else {
                                                $.ajax({
                                                    url: "/home/get_dropdown_by_id_caste/caste/religion_id/" + religion_id,
                                                    // form action url
                                                    type: 'POST',
                                                    // form submit method get/post
                                                    dataType: 'html',
                                                    // request type html/json/xml
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function(data) {
                                                        $(".prefered_caste_edit").html(data);
                                                        $(".prefered_sub_caste_edit").html("<option value=''>Choose A Caste First</option>");
                                                    },
                                                    error: function(e) {
                                                        console.log(e)
                                                    }
                                                });
                                            }
                                        });
                                        $(".prefered_caste_edit").change(function() {
                                            var caste_id = $(".prefered_caste_edit").val();
                                            if (caste_id == "") {
                                                $(".prefered_sub_caste_edit").html("<option value=''>Choose A Caste First</option>");
                                            } else {
                                                $.ajax({
                                                    url: "/home/get_dropdown_by_id_caste/sub_caste/caste_id/" + caste_id,
                                                    // form action url
                                                    type: 'POST',
                                                    // form submit method get/post
                                                    dataType: 'html',
                                                    // request type html/json/xml
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function(data) {
                                                        $(".prefered_sub_caste_edit").html(data);
                                                    },
                                                    error: function(e) {
                                                        console.log(e)
                                                    }
                                                });
                                            }
                                        });
                                    </script>
                                </div>
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
        $("#show_img").on('click', function() {
            showLightGallery($(this));
        });

        $("#images_stats").on('click', function() {
            showLightGallery($(this));
        });

        $("#gallery").on('click', function() {
            showLightGallery($(this));
        });

        // preload city select
        @if(!empty($profile->con_of_residence))
        loadSelect('{{url('states')}}', '{{$profile->con_of_residence}}', $('#state'), '{{$profile->state}}');
        loadSelect('{{url('cities')}}', '{{$profile->con_of_residence}}/1', $('#city'), '{{$profile->city}}');
        @endif

    });

    // $("#active_modal").on('hidden.bs.modal', function (e) {
    //     window.location.href = "{{ url('member/profile') }}";
    // });

    function renderImagesModal() {
        $.ajax({
            type: "get",
            url: "{{ url('member/profile/images/modal')}}",
            success: function(result) {
                if (result.code == '200') {
                    $("#modal_dialog").html(result.html);
                    $("#active_modal").modal("toggle");
                }
            }
        });
    }

    function uploadImages(elem) { alert(elem.html());
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "{{ url('member/profile/update/images/upload') }}",
            cache: false,
            contentType: false,
            processData: false,
            data: new FormData($("#images_form")[0]),
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);
                var message = result.message.split("|");
                if (result.code == '200') {
                    if (result.html) {
                        $("#top_nav_img").css("background-image", "url('" + result.nav_img + "')");;
                        $("#main-content").html(result.html);
                    }
                    if (message) showAlert(message[0], message[1], message[2]);
                } else {
                    if (message) showAlert('danger', message);
                }
            }
        });
    }

    function deleteImage(elem, dataid) {
        swalConfirm("Delete Image?", "Are you sure you want to delete this image? You will not be able to revert this!", () => {
            updateImage(elem, 'd', dataid);
        });
    }

    function deleteAccount(elem) {
        swalConfirm("Delete Account?", "Are you sure you want to delete your account? You will not be able to revert this!", () => {
            // var oldHtml = elem.html();
            // $.ajax({
            //     type: "post",
            //     url: "{{ url('member/profile/account/terminate')}}",
            //     data: {
            //         '_token': '{{ csrf_token() }}',
            //     },
            //     success: function(result) {
            //         elem.html(oldHtml);
            //         elem.prop('disabled', false);
            //         var message = result.message.split("|");
            //         if (result.code == '200') {
            //             showAlert(message[0], message[1], 3000);
            //             location.href="/login";
            //         } else showAlert('danger', message, 5000);
            //     }
            // });
        });
    }

    function updateImage(elem, action, dataid) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/images/update')}}"+"/"+action+"/"+dataid,
            data: {
                '_token': '{{ csrf_token() }}',
            },
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);
                var message = result.message.split("|");
                if (result.code == '200') {
                    showAlert(message[0], message[1], 3000);
                    if (action=="d") {
                        $("#image_"+dataid).remove();
                    } else if (action=="dp") {
                        $(".displaypic").html("<i class='fa fa-user-times'></i>");
                        $("#displaypic_"+dataid).html("<i class='fa fa-user'></i>");
                        //clickHighlight(null, null, $("#displaypic_"+dataid),
                        //    $("#displaypic_"+dataid).children("i").hasClass("fa-user")?"user-slash":"user", "");
                        //$("#active_modal").hide();
                        //renderImagesModal();
                    }
                    if (result.html) {
                        $("#top_nav_img").css("background-image", "url('" + result.nav_img + "')");;
                        $('#main-content').html(result.html)
                    };
                } else showAlert('danger', message, 5000);
            }
        });
    }

    //var isloggedin = "116";
    // Script for Editing Profile with Ajax
    function edit_section(section) {
        $('#info_' + section).hide();
        $('#edit_' + section).show();
    }

    function load_section(section) {

        $('#info_' + section).show();
        $('#edit_' + section).hide();
    }

    function save_section(elem, section) {
        // For Safety Disabling Section Elements for Slow Internet Connections
        $('#section_' + section).find('.form-control').prop('readonly', true);
        $('#section_' + section).find('.btn').prop('disabled', true);
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "/member/profile/update/" + section,
            cache: false,
            data: $('#form_' + section).serialize(),
            success: function(result) {
                elem.html(oldHtml);
                var message = result.message.split("|");
                if (result.code == '200') {
                    if (result.html)
                        $("#main-content").html(result.html);
                        if (message)
                            showAlert(message[0], message[1], 3000);
                } else {
                    if (message)
                        showAlert('danger', message, 5000);
                }
            }
        });
    }

    // function unhide_section(section) {
    //     $('#section_' + section).find('.form-control').prop('readonly', true);
    //     $('#section_' + section).find('.btn').prop('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/profile/unhide_section/" + section,
    //         cache: false,
    //         success: function(response) {
    //             $('#ajax_danger_alert').fadeOut('fast');
    //             $('#ajax_success_alert').show();
    //             $('.ajax_success_alert').html("This Section Is Successfully Showed");
    //             setTimeout(function() {
    //                 $('#ajax_success_alert').fadeOut('fast');
    //             }, 3000); // <-- time in milliseconds
    //             $('#section_' + section).find('.form-control').prop('readonly', false);
    //             $('#section_' + section).find('.btn').prop('disabled', false);
    //             $('#unhide_' + section).hide();
    //             $('#hide_' + section).show();
    //         },
    //         fail: function(error) {
    //             alert(error);
    //         }
    //     });
    // }

    // function hide_section(section) {
    //     $('#section_' + section).find('.form-control').prop('readonly', true);
    //     $('#section_' + section).find('.btn').prop('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/profile/hide_section/" + section,
    //         cache: false,
    //         success: function(response) {
    //             $('#ajax_success_alert').fadeOut('fast');
    //             $('#ajax_danger_alert').show();
    //             $('.ajax_danger_alert').html("This Section Is Successfully Hidden");
    //             setTimeout(function() {
    //                 $('#ajax_danger_alert').fadeOut('fast');
    //             }, 3000); // <-- time in milliseconds
    //             $('#section_' + section).find('.form-control').prop('readonly', false);
    //             $('#section_' + section).find('.btn').prop('disabled', false);
    //             $('#unhide_' + section).show();
    //             $('#hide_' + section).hide();
    //         },
    //         fail: function(error) {
    //             alert(error);
    //         }
    //     });
    // }

    // function IsJsonString(str) {
    //     try {
    //         JSON.parse(str);
    //     } catch (e) {
    //         return false;
    //     }
    //     return true;
    // }


    // function open_message_box(thread_id, now) {

    //     $("#msg_body").html("<div class='text-center' id='payment_loader'><i class='fa fa-refresh fa-5x fa-spin'></i></div>");
    //     $("#msg_box_header").html("<a class='c-base-1' target='_blank' href='/home/member_profile/" + $(now).find('.contacts-list-name').data('member') + "'>" + $(now).find('.contacts-list-name').html() + "</a>");
    //     $("#msg_refresh").html("<a onclick='refresh_msg(" + thread_id + ")'><i class='fa fa-refresh'></i> Refresh</a>");
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/get_messages/" + thread_id,
    //         cache: false,
    //         success: function(response) {
    //             /*clearInterval(message_interval);
    //             var message_interval =  setInterval(function(){
    //                                         $("#msg_body").load('/home/get_messages/'+thread_id);
    //                                     }, 4000);*/
    //             $("#msg_body").removeAttr("style");
    //             $("#message_text").removeAttr('disabled');
    //             $("#message_text").val('');
    //             $("#msg_body").html(response);
    //         }
    //     });
    // }

    // function refresh_msg(thread_id) {
    //     $(".contacts-list").find("#thread_" + thread_id).click();
    // }

    // function load_all_msg(thread_id) {
    //     $("#msg_body").html("<div class='text-center' id='payment_loader'><i class='fa fa-refresh fa-5x fa-spin'></i></div>");
    //     $("#message_text").attr('disabled', true);
    //     $("#msg_send_btn").attr('disabled', true);
    //     $.ajax({
    //         type: "POST",
    //         url: "/home/get_messages/" + thread_id + "/all_msg",
    //         cache: false,
    //         success: function(response) {
    //             $("#message_text").removeAttr('disabled');
    //             $("#msg_send_btn").removeAttr('disabled');
    //             $("#msg_body").html(response);
    //         }
    //     });
    // }

    // function msg_send(thread, from, to) {
    //     if ($("#message_text").val().length != 0) {
    //         var form_data = ($("#message_form").serialize());
    //         $("#message_text").attr('disabled', 'disabled');
    //         $("#msg_send_btn").attr('disabled', 'disabled');
    //         $("#msg_send_btn").html("<i class='fa fa-refresh fa-spin'></i>");

    //         $.ajax({
    //             type: "POST",
    //             url: "/home/send_message/" + thread + "/" + from + "/" + to,
    //             data: form_data,
    //             success: function(response) {
    //                 // alert('done');
    //                 $("#message_text").removeAttr('disabled');
    //                 $("#message_text").val('');
    //                 $("#msg_send_btn").html("Send");
    //                 $.ajax({
    //                     type: "POST",
    //                     url: "/home/get_messages/" + thread,
    //                     cache: false,
    //                     success: function(response) {
    //                         $("#msg_body").html(response);
    //                     }
    //                 });
    //             }
    //         });
    //     }
    // }
</script>
<style type="text/css">
    .xs_nav_item {
        text-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
    }
</style>
@endsection
