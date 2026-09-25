{{--
    Team Dashboard sidebar — its own space, separate from the member
    sidebar (layouts/partials/dashboard-sidebar.blade.php). Mirrors
    layouts/partials/admin-sidebar.blade.php's structure/shell so all three
    dashboards (member/admin/team) share the same look. Included by
    layouts/team/dashboard.blade.php only.
--}}
<?php use App\User; ?>
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
            <a href="{{ route('team.dashboard') }}" class="{{ request()->is('team/dashboard') ? 'is-active' : '' }}">
                <i class="fa fa-tachometer"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.mine') }}" class="{{ request()->is('team/proposals/mine') ? 'is-active' : '' }}">
                <i class="fa fa-file-text-o"></i> My Proposals
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.create') }}" class="{{ request()->is('team/proposals/create') ? 'is-active' : '' }}">
                <i class="fa fa-plus"></i> Add Proposal
            </a>
        </li>
        <li>
            <a href="{{ route('team.matches') }}" class="{{ request()->is('team/matches') ? 'is-active' : '' }}">
                <i class="fa fa-magic"></i> AI Matches
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.search') }}" class="{{ request()->is('team/proposals/search') ? 'is-active' : '' }}">
                <i class="fa fa-search"></i> Search Proposals
            </a>
        </li>
        <li>
            <a href="{{ route('team.successful-matches') }}" class="{{ request()->is('team/successful-matches') ? 'is-active' : '' }}">
                <i class="fa fa-trophy"></i> Successful Matches
            </a>
        </li>
        <li>
            <a href="{{ route('team.notifications') }}" class="{{ request()->is('team/notifications') ? 'is-active' : '' }}">
                <i class="fa fa-bell-o"></i> Notifications
            </a>
        </li>
        <li>
            {{-- No real messaging feature in this app — links to Notifications. --}}
            <a href="{{ route('team.notifications') }}">
                <i class="fa fa-envelope-o"></i> Messages
            </a>
        </li>
        @unless(User::retrieveUserObject()->isMatchmakerOnly())
        <li>
            <a href="{{ url('member/profile') }}">
                <i class="fa fa-user-o"></i> Partner Account
            </a>
        </li>
        @endunless
        <li>
            <a href="{{ url('member/profile/password/update') }}">
                <i class="fa fa-cog"></i> Settings
            </a>
        </li>
    </ul>

    <div class="ur-dash-sidebar__footer">
        @unless(User::retrieveUserObject()->isMatchmakerOnly())
        <div class="ur-dash-nav__section-label" style="padding-top:0;">Switch Dashboard</div>
        <a href="{{ url('member/profile') }}" class="ur-dash-nav__link">
            <i class="fa fa-user"></i> Member Dashboard
        </a>
        @endunless
        @if(User::retrieveUserObject()->admin == 1)
        <a href="{{ url('admin/dashboard') }}" class="ur-dash-nav__link">
            <i class="fa fa-cogs"></i> Admin Dashboard
        </a>
        @endif
        <div class="ur-dash-nav__divider"></div>
        <button type="button" class="ur-dash-nav__link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Log Out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
