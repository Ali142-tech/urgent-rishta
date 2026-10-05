{{--
    Team Dashboard topbar. Mirrors layouts/partials/dashboard-topbar.blade.php
    (bell markup copied verbatim — same ids/classes public/app.js already
    polls: #notifications, #notificationsMenu, .noti_counter — so it works
    unmodified here) plus an "Add profile" button. Included by
    layouts/team/dashboard.blade.php only. At phone widths (see
    public/css/ur-dashboard.css's ≤420px rule) "Add profile" drops its text
    label since this topbar has one more element than every other dashboard
    topbar and there isn't room for both that and a readable title.
--}}
<header class="ur-dash-topbar">
    <div class="ur-dash-topbar__left">
        <button type="button" class="ur-dash-toggler" id="ur_dash_toggler" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <div class="ur-dash-topbar__title">@yield('dashboard-title', 'Dashboard')</div>
    </div>

    <div class="ur-dash-topbar__right">
        <a href="{{ route('team.proposals.create') }}" class="ur-dash-topbar__add-btn"><i class="fa fa-plus"></i> <span class="ur-dash-topbar__add-btn-label text-white">Add profile</span></a>
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
                <div class="ur-dash-user__avatar" id="top_nav_img" style="background-image:url('{{ auth()->user()->getProfileImage(true) }}')"></div>
                <span class="ur-dash-user__name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}<br><small style="font-weight:400; color:#6B7570;">Verified Partner</small></span>
                <i class="fa fa-chevron-down ur-dash-user__caret"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right ur-dash-user-menu">
                <div class="ur-dash-user-menu__head">
                    <div class="name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="id">Partner ID: {{ auth()->user()->dataid }}</div>
                </div>
                <a href="{{ route('team.profile') }}"><i class="fa fa-user"></i> My Profile</a>
                <a href="{{ route('team.password') }}"><i class="fa fa-key"></i> Change Password</a>
                <button type="button" class="is-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-power-off"></i> Log Out
                </button>
            </div>
        </div>
    </div>
</header>
<form id="logout-form" action="{{ route('team.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
