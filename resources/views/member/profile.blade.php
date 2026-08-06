@extends('layouts.master')
@section('main-content')
<?php use App\User; ?>
<section class="slice sct-color-2">
    <div class="profile">
        <div class="container">
            <div class="row cols-md-space cols-sm-space cols-xs-space">
                <div class="col-lg-4">
                    <style>
                        .lg-outer #lg-download {
                            display: none !important;
                        }
                    </style>
                    <div class="sidebar sidebar-inverse sidebar--style-1 bg-base-1 z-depth-2-top">
                        <div class="sidebar-object mb-0">
                            <!-- Profile picture -->
                            <div class="profile-picture profile-picture--style-2">
                                <div style="border: 10px solid rgba(255, 255, 255, 0.1);width: 200px;border-radius: 50%;margin-top: 30px;">
                                    <div class="profile_img" id="show_img" style="background-image: url('{{ $profile->getProfileImage() }}')"></div>
                                </div>
                            </div>
                            <!-- Profile details -->
                            <div class="profile-details">
                                <h2 class="heading heading-3 strong-500 profile-name">{{ $profile->first_name }}</h2>
                                <h3 class="heading heading-6 strong-400 profile-occupation mt-3">{{ $profile->profession }}</h3><br/>
                            </div>
                            <!-- Profile connect -->
                            <div class="profile-connect mt-2">
                                <div class="row">
                                    <div class="col-sm-12 size-smtr">
                                        @guest
                                            <a class="btn btn-styled btn-block btn-sm btn-white z-depth-2-bottom" id="interest_{{$profile->dataid}}" onclick="return register_request();">
                                                <span><i class="fa fa-heart"></i> Express Interest </span>
                                            </a>
                                        @endguest
                                        @auth
                                            @if (User::retrieveUserObject()->inList($profile->dataid, 'interest'))
                                                @php
                                                    $interest = User::retrieveUserObject()->getInterest($profile->dataid);
                                                @endphp
                                                <a class="btn btn-styled btn-block btn-sm btn-white z-depth-2-bottom" id="interest_{{$profile->dataid}}" onclick="return {{$interest==-1? "false":"withdrawInterest($(this), 's')"}};">
                                                    @if ($interest==1)
                                                        <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                                                    @elseif ($interest==-1)
                                                        <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                                                    @else
                                                        <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed</span>
                                                    @endif
                                                </a>
                                            @else
                                                <a class="btn btn-styled btn-block btn-sm btn-white z-depth-2-bottom" id="interest_{{$profile->dataid}}" onclick="return sendInterest($(this));">
                                                    <span><i class="fa fa-heart"></i> Express Interest </span>
                                                </a>
                                            @endif
                                        @endauth
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-stats clearfix mt-2">
                                <div class="stats-entry">
                                    <span class="stats-count" id="follower">{{ $profile->getFilteredCount('follow') }}</span>
                                    <span class="stats-label text-uppercase">Followers</span>
                                </div>
                                <div class="stats-entry" id="images_stats">
                                    <span class="stats-count">{{ $profile->getImageCount() }}</span>
                                    <span class="stats-label text-uppercase">Photo in Gallery</span>
                                </div>
                            </div>
                            <!-- Profile stats -->
                            <div class="profile-stats clearfix mt-2">
                                <div class="stats-entry">
                                    <span class="stats-label text-uppercase text-left pl-2">Age</span>
                                    <span class="stats-label text-uppercase text-left pl-2">Mother Tongue</span>
                                    <span class="stats-label text-uppercase text-left pl-2">Religion</span>
                                    <span class="stats-label text-uppercase text-left pl-2">Caste / Sect</span>
                                    <span class="stats-label text-uppercase text-left pl-2">Height</span>
                                    <span class="stats-label text-uppercase text-left pl-2">Location</span>
                                </div>

                                <div class="stats-entry">
                                    <span class="stats-label text-uppercase text-left pl-2">{{ date_diff(date_create($profile->birthday), date_create('now'))->y }} </span>
                                    <span class="stats-label text-uppercase text-left pl-2">{{ $profile->lbl_mother_tongue?$profile->lbl_mother_tongue:'N/A' }} </span>
                                    <span class="stats-label text-uppercase text-left pl-2">{{ $profile->lbl_religion?$profile->lbl_religion:'N/A' }} </span>
                                    <span class="stats-label text-uppercase text-left pl-2">{{ $profile->lbl_caste?$profile->lbl_caste:'N/A' }} </span>
                                    <span class="stats-label text-uppercase text-left pl-2">{{ $profile->height?$profile->height:'N/A' }} </span>
                                    <span class="stats-label text-uppercase text-left pl-2">{{ $profile->lbl_con_of_residence?$profile->lbl_con_of_residence:'N/A' }} </span>
                                </div>
                            </div>
                            <!-- Profile connected accounts -->
                            <div class="profile-useful-links clearfix mb-5"></div>
                        </div>
                    </div>
                    <style>
                        /* xs */
                        .size-sm {
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                        }

                        .size-smtr {
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                            padding-top: .50rem !important;
                        }

                        .size-smtl {
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                            padding-top: .50rem !important;
                        }

                        .size-smr {
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                            padding-top: 0px !important;
                        }

                        .size-sml {
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                            padding-top: .50rem !important;
                        }

                        /* sm */

                        @media (min-width: 768px) {
                            .size-sm {
                                padding-left: 0px !important;
                                padding-right: 0px !important;
                            }

                            .size-smtr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: .50rem !important;
                            }

                            .size-smtl {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: .50rem !important;
                            }

                            .size-smr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: 0px !important;
                            }

                            .size-sml {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: 0px !important;
                            }
                        }

                        /* md */

                        @media (min-width: 992px) {
                            .size-sm {
                                padding-left: 0px !important;
                                padding-right: 0px !important;
                            }

                            .size-smtr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: .50rem !important;
                            }

                            .size-smtl {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: .50rem !important;
                            }

                            .size-smr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: 0px !important;
                            }

                            .size-sml {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: 0px !important;
                            }
                        }

                        /* lg */

                        @media (min-width: 1200px) {
                            .size-sm {
                                padding-left: 0px !important;
                                padding-right: 0px !important;
                            }

                            .size-smtr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: .50rem !important;
                            }

                            .size-smtl {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: .50rem !important;
                            }

                            .size-smr {
                                padding-left: 0px !important;
                                padding-right: .25rem !important;
                                padding-top: 0px !important;
                            }

                            .size-sml {
                                padding-left: .25rem !important;
                                padding-right: 0px !important;
                                padding-top: 0px !important;
                            }
                        }
                    </style>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="feature feature--boxed-border feature--bg-1 mb-4 z-depth-2-top" style="padding: 0.8rem 0.8rem;">
                                <div class="block-title">
                                    <h3 class="text-uppercase heading heading-6 strong-500 pull-left mb-2 pl-2">
                                        <b>Quick Information</b>
                                    </h3>
                                </div>
                                <div class="block-content">
                                    <div class="table-full-width">
                                        <div class="table-full-width">
                                            <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                <tbody>
                                                    <tr>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Member Id</b></td>
                                                        <td colspan="3"><b class="c-base-1">{{ $profile->dataid }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>First Name</b></td>
                                                        <td colspan="3">{{ $profile->first_name }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Gender</b></td>
                                                        <td>{{ $profile->gender }} </td>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Age</b></td>
                                                        <td>{{ date_diff(date_create($profile->birthday), date_create('now'))->y }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Marital Status</b></td>
                                                        <td {{$profile->lbl_marital_status=="Never Married"?'colspan=3':''}}>{{ $profile->lbl_marital_status }}</td>
                                                        @if($profile->lbl_marital_status!="Never Married")
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Number Of Children</b></td>
                                                        <td>{{ $profile->children }}</td>
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Area</b></td>
                                                        <td></td>
                                                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>On Behalf</b></td>
                                                        <td>{{ $profile->profile_for }} </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="widget">
                        <div class="card z-depth-2-top" id="profile_load">
                            <div class="card-title">
                                <h3 class="text-uppercase heading heading-6 strong-500 pull-left">
                                    <b>Profile Information</b>
                                </h3>
                            </div>
                            <div class="card-body" style="padding: 1.5rem 0.5rem;">
                                <!-- Contact information -->
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_introduction">
                                        <div id="info_introduction">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Introduction </h3>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td class=""></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_basic_info">
                                        <div id="info_basic_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Basic Information </h3>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>First Name</b></td>
                                                                <td colspan="3">{{ $profile->first_name }} </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Gender</b></td>
                                                                <td>{{ $profile->gender }} </td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Age</b></td>
                                                                <td>{{ date_diff(date_create($profile->birthday), date_create('now'))->y }} </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Marital Status</b></td>
                                                                <td {{$profile->lbl_marital_status=="Never Married"?'colspan=3':''}}>{{ $profile->lbl_marital_status }}</td>
                                                                @if($profile->lbl_marital_status!="Never Married")
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Number Of Children</b></td>
                                                                <td>{{ $profile->children }}</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Area</b></td>
                                                                <td>{{ $profile->area }}</td>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Date Of Birth</b></td>
                                                                <td>{{ $profile->birthday }} </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>On Behalf</b></td>
                                                                <td colspan="3">{{ $profile->profile_for }} </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_education_and_career">
                                        <div id="info_education_and_career">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Education And Career </h3>
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
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_physical_attributes">
                                        <div id="info_physical_attributes">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Physical Attributes </h3>
                                            </div>
                                            <div class="table-full-width">
                                                <div class="table-full-width">
                                                    <table class="table table-profile table-responsive table-striped table-bordered table-slick">
                                                        <tbody>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Height</b></td>
                                                                <td colspan="3">{{$profile->height}}</td>
                                                                <!-- <td height="30" style="padding-left: 5px;" class="font-dark"><b>Weight</b></td>
                                                                <td></td> -->
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_language">
                                        <div id="info_language">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Language </h3>
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
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_residency_information">
                                        <div id="info_residency_information">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Residency Information </h3>
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
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_spiritual_and_social_background">
                                        <div id="info_spiritual_and_social_background">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Spiritual And Social Background </h3>
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
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_permanent_address">
                                        <div id="info_permanent_address">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Permanent Address </h3>
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
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_family_info">
                                        <div id="info_family_info">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Family Information </h3>
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
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_additional_personal_details">
                                        <div id="info_additional_personal_details">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Additional Personal Details </h3>
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
                                    </div>
                                </div>
                                <div class="feature feature--boxed-border feature--bg-1 pt-3 pb-0 pl-3 pr-3 mb-3 border_top2x">
                                    <div id="section_partner_expectation">
                                        <div id="info_partner_expectation">
                                            <div class="card-inner-title-wrapper pt-0">
                                                <h3 class="text-uppercase card-inner-title pull-left">
                                                    Partner Expectation </h3>
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
                                                                <td colspan="3">{{$profile->lbl_rcaste}} / {{$profile->rsect}}</td>
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
                                                            </tr>
                                                            <tr>
                                                                <td height="30" style="padding-left: 5px;" class="font-dark"><b>Prefered Country</b></td>
                                                                <td colspan="3">{{$profile->lbl_rcon_pref}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
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
    });
</script>
@endsection
