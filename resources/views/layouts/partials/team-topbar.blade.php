<?php use App\User; ?>
{{--
    Team Dashboard topbar. Mirrors layouts/partials/dashboard-topbar.blade.php
    (bell markup copied verbatim — same ids/classes public/app.js already
    polls: #notifications, #notificationsMenu, .noti_counter — so it works
    unmodified here) plus a search box and mail/WhatsApp shortcut icons
    added for the Dashboard redesign. Included by layouts/team/dashboard.blade.php only.
--}}
<header class="ur-dash-topbar">
    <div class="ur-dash-topbar__left">
        <button type="button" class="ur-dash-toggler" id="ur_dash_toggler" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <div class="ur-dash-topbar__title">@yield('dashboard-title', 'Dashboard')</div>
    </div>

    <div class="ur-dash-topbar__right">
        <a href="{{ route('team.notifications') }}" class="ur-dash-bell" title="Messages" style="text-decoration:none;">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a href="{{ route('team.notifications') }}" class="ur-dash-bell" title="WhatsApp" style="text-decoration:none;">
            <i class="fa fa-whatsapp"></i>
        </a>
        <div class="dropdown dropdown--style-2 dropdown--animated">
            <button type="button" class="ur-dash-bell" id="notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Notifications">
                <i class="fa fa-bell-o"></i>
                <span class="noti_counter"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right ur-dash-bell-menu">
                <h6 class="dropdown-header">Notifications</h6>
                <ul class="notifications" id="notificationsMenu" style="list-style:none;margin:0;padding:0;">
                    <li class="dropdown-header">No notifications</li>
                </ul>
            </div>
        </div>

        <div class="dropdown dropdown--style-2 dropdown--animated">
            <button type="button" class="ur-dash-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="ur-dash-user__avatar" id="top_nav_img" style="background-image:url('{{ User::retrieveUserObject()->getProfileImage(true) }}')"></div>
                <span class="ur-dash-user__name">{{ User::retrieveUserObject()->first_name }} {{ User::retrieveUserObject()->last_name }}<br><small style="font-weight:400; color:#6B7570;">Verified Partner</small></span>
                <i class="fa fa-chevron-down ur-dash-user__caret"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right ur-dash-user-menu">
                <div class="ur-dash-user-menu__head">
                    <div class="name">{{ User::retrieveUserObject()->first_name }} {{ User::retrieveUserObject()->last_name }}</div>
                    <div class="id">Member ID: {{ User::retrieveUserObject()->dataid }}</div>
                </div>
                <a href="{{ url('member/profile') }}"><i class="fa fa-user"></i> My Profile</a>
                <a href="{{ url('member/profile/password/update') }}"><i class="fa fa-key"></i> Change Password</a>
                @if(User::retrieveUserObject()->admin == 1)
                <a href="{{ url('admin/dashboard') }}"><i class="fa fa-cogs"></i> Admin Dashboard</a>
                @endif
                <button type="button" class="is-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-power-off"></i> Log Out
                </button>
            </div>
        </div>
    </div>
</header>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
