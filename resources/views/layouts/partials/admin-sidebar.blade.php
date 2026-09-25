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
            <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'is-active' : '' }}">
                <i class="fa fa-tachometer"></i> Dashboard
            </a>
        </li>

        <div class="ur-dash-nav__section-label">Members</div>
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
            <a href="{{ url('admin/photoaccess') }}" class="{{ request()->is('admin/photoaccess') ? 'is-active' : '' }}">
                <i class="fa fa-lock"></i> Photo Access Requests
            </a>
        </li>
        <li>
            <a href="{{ url('admin/photo-verification') }}" class="{{ request()->is('admin/photo-verification') ? 'is-active' : '' }}">
                <i class="fa fa-id-badge"></i> Photo Verification
            </a>
        </li>
        <li>
            <a href="{{ url('admin/profiles/deleted') }}" class="{{ request()->is('admin/profiles/deleted') ? 'is-active' : '' }}">
                <i class="fa fa-trash"></i> Deleted Profiles
            </a>
        </li>

        <div class="ur-dash-nav__section-label">Memberships</div>
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

        <div class="ur-dash-nav__section-label">AI &amp; Matching</div>
        <li>
            <a href="{{ route('admin.match-weights') }}" class="{{ request()->is('admin/match-weights') ? 'is-active' : '' }}">
                <i class="fa fa-sliders"></i> AI Match Weights
            </a>
        </li>
        <li>
            <a href="{{ route('admin.successful-matches') }}" class="{{ request()->is('admin/successful-matches') ? 'is-active' : '' }}">
                <i class="fa fa-check-circle"></i> Successful Matches
            </a>
        </li>
        <li>
            <a href="{{ route('admin.contact-unlock-settings') }}" class="{{ request()->is('admin/contact-unlock-settings') ? 'is-active' : '' }}">
                <i class="fa fa-unlock-alt"></i> Contact Unlock Settings
            </a>
        </li>

        <div class="ur-dash-nav__section-label">Team Management</div>
        <li>
            <a href="{{ route('admin.team-members') }}" class="{{ request()->is('admin/team-members') ? 'is-active' : '' }}">
                <i class="fa fa-users"></i> Team Members
            </a>
        </li>
        <li>
            <a href="{{ route('admin.matchmaker-applications') }}" class="{{ request()->is('admin/matchmaker-applications') ? 'is-active' : '' }}">
                <i class="fa fa-user-plus"></i> Matchmaker Applications
            </a>
        </li>

        <div class="ur-dash-nav__section-label">System</div>
        <li>
            <a href="{{ route('admin.audit-log') }}" class="{{ request()->is('admin/audit-log') ? 'is-active' : '' }}">
                <i class="fa fa-history"></i> Audit Log
            </a>
        </li>
        {{-- Hidden from the admin nav for now (client request, Sep 2026) — the
             route/controller/service/view are untouched, just not linked to
             here, so it's a one-line revert to bring back. --}}
        {{--
        <li>
            <a href="{{ url('admin/campaigns/profile-completion') }}" class="{{ request()->is('admin/campaigns/profile-completion') ? 'is-active' : '' }}">
                <i class="fa fa-envelope"></i> Profile Completion Campaign
            </a>
        </li>
        --}}
    </ul>

    <div class="ur-dash-sidebar__footer">
        <div class="ur-dash-nav__section-label" style="padding-top:0;">Switch Dashboard</div>
        <a href="{{ url('member/profile') }}" class="ur-dash-nav__link">
            <i class="fa fa-user"></i> Member Dashboard
        </a>
        <a href="{{ route('team.dashboard') }}" class="ur-dash-nav__link">
            <i class="fa fa-briefcase"></i> Team Dashboard
        </a>
        <div class="ur-dash-nav__divider"></div>
        <button type="button" class="ur-dash-nav__link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Log Out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
