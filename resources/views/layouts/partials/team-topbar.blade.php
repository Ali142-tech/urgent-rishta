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
                <a href="{{ route('team.notifications') }}" class="ur-bn-all">View all notifications</a>
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

<style>
    .ur-dash-bell-menu { width: 360px; max-width: calc(100vw - 24px); }
    .ur-dash-bell-menu li a { padding: 0 !important; }
    .ur-bn { display: flex; align-items: center; gap: 12px; padding: 11px 16px; border-bottom: 1px solid #F0ECE1; }
    .ur-bn__pic { position: relative; width: 42px; height: 42px; flex-shrink: 0; }
    .ur-bn__pic img { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; display: block; background: #F3EFE6; }
    .ur-bn__icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #F3EFE6; color: #C9974D; font-size: 16px; }
    .ur-bn__icon--match { background: #E7F3EC; color: #1C6B52; }
    .ur-bn__icon--admin { background: #EEF0FB; color: #4A4FA3; }
    .ur-bn__txt { flex: 1; min-width: 0; line-height: 1.35; }
    .ur-bn__txt b { display: block; font-size: 13px; color: #123A2E; }
    .ur-bn__txt span { display: block; font-size: 12px; color: #4B5651; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .ur-bn__txt em { display: block; font-style: normal; font-size: 11px; color: #8A938E; margin-top: 2px; }
    .ur-bn__dot { width: 8px; height: 8px; border-radius: 50%; background: #1C9A6C; flex-shrink: 0; }
    .ur-bn-all { display: block; text-align: center; padding: 11px 16px; font-size: 12.5px; font-weight: 700; color: #1C6B52 !important; border-top: 1px solid #F0ECE1; text-decoration: none !important; }
    .ur-bn-all:hover { background: #F2F8F5; }
</style>
<script>
    /* One row of the bell dropdown (picture or icon, title, line of text, time) — called by public/js/app.js for team notifications. */
    window.urNotifHtml = function (u) {
        var esc = function (v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]; }); };
        var pic = u.image
            ? '<img src="' + esc(u.image) + '" alt="" onerror="this.outerHTML=\'<span class=&quot;ur-bn__icon&quot;><i class=&quot;fa ' + esc(u.icon) + '&quot;></i></span>\'">'
            : '<span class="ur-bn__icon ur-bn__icon--' + esc(u.tone || 'plain') + '"><i class="fa ' + esc(u.icon) + '"></i></span>';
        return '<span class="ur-bn"><span class="ur-bn__pic">' + pic + '</span><span class="ur-bn__txt"><b>' + esc(u.title) + '</b>'
            + (u.text ? '<span>' + esc(u.text) + '</span>' : '') + '<em>' + esc(u.time) + '</em></span><i class="ur-bn__dot"></i></span>';
    };
</script>