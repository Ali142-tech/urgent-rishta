@extends('layouts.master')
@section('main-content')
<?php use App\User; ?>
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
                        <a href="{{ url('/member/profile') }}" class="nav-link p_nav">
                            <i class="fa fa-user"></i>
                            Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link interests p_nav {{ $type=='interests' ? 'active' : '' }}" href="{{ url('/member/profile/listing/interests') }}">
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
<style>
    @media (max-width: 576px) {
        .listing-image {
            height: 330px !important;
        }
    }
</style>
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">
                @if ($type=="interests")
                    Interests
                @else
                    {{ (strtoupper(substr($type, 0, 1)).substr($type, 1).(substr($type, strlen($type)-1)=="e"?"d":"ed"))." Members" }}
                @endif
            </div>
        </div>
    </div>
</section>
<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="visible_xs align-items-center">
                <div class="container mb-4 text-center">
                    <ul class="inline-links inline-links--style-3">
                        <li>
                            <a href="/home/profile" class="c-base-1 xs_nav_item m_profile m_nav">
                                <i class="fa fa-user"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="c-base-1 xs_nav_item m_interests m_nav {{ $type=='interests' ? ' m_nav_active' : '' }}" href="{{ url('/member/profile/listing/interests') }}">
                                <i class="fa fa-heart"></i>
                                Interests
                            </a>
                        </li>
                        <!-- <li>
                            <a class="c-base-1 xs_nav_item m_messaging m_nav" onclick="profile_load('messaging', 'no')">
                                <i class="fa fa-comments-o"></i>
                                Messaging
                            </a>
                        </li> -->
                    </ul>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="block-wrapper" id="result">
                    @if($type=="interests")
                        @yield('interest-data')
                    @else
                        @yield('filtered-data')
                    @endif
                </div>
                <!-- {-{- $profiles->links() -}-} -->
                <script type="text/javascript">
                    $(document).ready(function() {
                        //$('#datatable').DataTable();
                    });
                </script>
            </div>
        </div>
    </div>
</section>
<style>
    /* xs */
    .size-sm {
        display: none;
    }

    .size-sm-btn {
        display: block;
    }

    /* sm */
    @media (min-width: 768px) {
        .size-sm {
            display: none;
        }

        .size-sm-btn {
            display: block;
        }
    }

    /* md */
    @media (min-width: 992px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }

    /* lg */
    @media (min-width: 1200px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }
</style>
@endsection
