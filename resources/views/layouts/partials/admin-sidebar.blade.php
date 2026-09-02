{{--
    Admin dashboard sidebar. Included by layouts/admin/dashboard.blade.php only.
    Mirrors layouts/partials/dashboard-sidebar.blade.php (member area) so both
    dashboards share the same shell/look. Active-state highlighting uses
    request()->is(...), same as the member sidebar.
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
            <a href="{{ url('admin/profiles') }}" class="{{ request()->is('admin/profiles') ? 'is-active' : '' }}">
                <i class="fa fa-user"></i> Profiles
            </a>
        </li>
        <li>
            <a href="{{ url('admin/interests') }}" class="{{ request()->is('admin/interests') ? 'is-active' : '' }}">
                <i class="fa fa-heart"></i> Interests
            </a>
        </li>
        <li>
            <a href="{{ url('admin/packages') }}" class="{{ request()->is('admin/packages') ? 'is-active' : '' }}">
                <i class="fa fa-list-ul"></i> Packages
            </a>
        </li>
        <li>
            <a href="{{ url('admin/package-subscribers') }}" class="{{ request()->is('admin/package-subscribers') ? 'is-active' : '' }}">
                <i class="fa fa-users"></i> Package Subscribers
            </a>
        </li>
        <li>
            <a href="{{ url('admin/appointments') }}" class="{{ request()->is('admin/appointments') ? 'is-active' : '' }}">
                <i class="fa fa-calendar"></i> Appointments
            </a>
        </li>
        <li>
            <a href="{{ url('admin/photo-verification') }}" class="{{ request()->is('admin/photo-verification') ? 'is-active' : '' }}">
                <i class="fa fa-id-badge"></i> Photo Verification
            </a>
        </li>
    </ul>

    <div class="ur-dash-sidebar__footer">
        <a href="{{ url('member/profile') }}" class="ur-dash-nav__link">
            <i class="fa fa-arrow-left"></i> Back to My Dashboard
        </a>
        <button type="button" class="ur-dash-nav__link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Log Out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
