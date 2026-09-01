{{--
    Member dashboard sidebar. Included by layouts/dashboard.blade.php only.
    Active-state highlighting is done with request()->is(...) so no JS is
    needed just to mark the current page.
--}}
<aside class="ur-dash-sidebar" id="ur_dash_sidebar">
    <button type="button" class="ur-dash-sidebar__close" id="ur_dash_sidebar_close" aria-label="Close menu">
        <i class="fa fa-times"></i>
    </button>

    <a href="{{ url('/') }}" class="ur-dash-sidebar__brand">
        <img src="/images/header_logo2.png" alt="Urgent Rishta">
        <span>Urgent Rishta</span>
    </a>

    <ul class="ur-dash-nav">
        <li>
            <a href="{{ url('member/profile') }}" class="{{ request()->is('member/profile') ? 'is-active' : '' }}">
                <i class="fa fa-user"></i> My Profile
            </a>
        </li>
        <li>
            <a href="{{ url('member/profile/listing/interests') }}" class="{{ request()->is('member/profile/listing/interests') ? 'is-active' : '' }}">
                <i class="fa fa-heart"></i> My Interests
            </a>
        </li>

        <div class="ur-dash-nav__divider"></div>

        <li>
            <a href="{{ url('member/profile/pictures') }}" class="{{ request()->is('member/profile/pictures') ? 'is-active' : '' }}">
                <i class="fa fa-photo"></i> Manage Pictures
            </a>
        </li>
        <li>
            <a href="{{ url('member/profile/password/update') }}" class="{{ request()->is('member/profile/password/update') ? 'is-active' : '' }}">
                <i class="fa fa-key"></i> Change Password
            </a>
        </li>
    </ul>

    <div class="ur-dash-sidebar__footer">
        <button type="button" class="ur-dash-nav__link is-danger" onclick="javascript:deleteAccount($(this));">
            <i class="fa fa-close"></i> Close Account
        </button>
        <button type="button" class="ur-dash-nav__link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Log Out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
