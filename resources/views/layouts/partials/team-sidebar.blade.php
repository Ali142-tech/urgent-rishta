{{--
    Team Dashboard sidebar — its own space, separate from the member
    sidebar (layouts/partials/dashboard-sidebar.blade.php). Included by
    layouts/team/dashboard.blade.php only.

    Rebuilt to match the "AI Partner Portal" mockup screenshot exactly
    (Sep 2026, client's explicit "remove extra sections" instruction) —
    nav list is now ONLY the items the mockup shows, in its exact order.
    Items this removed that had no other real destination lost their only
    nav entry point: "Successful Matches" (team.successful-matches) is no
    longer linked from anywhere in this sidebar — still reachable by
    direct URL, just not from here, since the mockup doesn't have it.
    Everything else removed (Notifications/Messages/Partner Account/
    Settings/Switch Dashboard) already has an equivalent reachable from
    the topbar's user-avatar dropdown (My Profile, Change Password) or
    is folded into "Requests" (same team.notifications destination).

    Nav badge counts (sidebarTeamProposalsCount/sidebarAiMatchesCount/
    sidebarMatchmakersCount/sidebarRequestsCount) come from a View::composer
    in AppServiceProvider::boot(), not from each page's own controller —
    so every Team Dashboard page shows live numbers, not just team/dashboard.

    Mapping note: "AI Match" and "My Matches" both point at the same
    team.matches page/count (no second real dataset exists to split them
    into); "Requests" uses unread notifications as a real stand-in for the
    mockup's removed Collaboration Request feature (see TeamController::
    dashboard()'s own comment) — flagged to the client, not yet confirmed.
--}}
<?php use App\User; ?>
<aside class="ur-dash-sidebar" id="ur_dash_sidebar">
    <button type="button" class="ur-dash-sidebar__close" id="ur_dash_sidebar_close" aria-label="Close menu">
        <i class="fa fa-times"></i>
    </button>

    <div class="ur-dash-sidebar__brand-row">
        <div class="ur-dash-sidebar__brand-icon"><i class="fa fa-sliders"></i></div>
        <a href="{{ url('/') }}" class="ur-dash-sidebar__brand">
            <img src="/images/header_logo2.png" alt="Urgent Rishta">
            <div>
                <span>Urgent Rishta</span>
                <small>AI Partner Network</small>
            </div>
        </a>
    </div>

    @php($__sidebarUser = User::retrieveUserObject())
    @php($__sidebarInitials = strtoupper(substr($__sidebarUser->first_name ?: '', 0, 1) . substr($__sidebarUser->last_name ?: '', 0, 1)))
    <div class="ur-dash-sidebar__profile">
        <div class="ur-dash-sidebar__profile-avatar">{{ $__sidebarInitials }}</div>
        <div>
            <div class="ur-dash-sidebar__profile-name">{{ $__sidebarUser->first_name }} {{ $__sidebarUser->last_name }}</div>
            @if(!empty($__sidebarUser->experience))
                <div class="ur-dash-sidebar__profile-meta">{{ $__sidebarUser->experience }}</div>
            @endif
        </div>
    </div>

    <div class="ur-dash-nav__section-label" style="padding-top:14px;">Workspace</div>
    <ul class="ur-dash-nav">
        <li>
            <a href="{{ route('team.dashboard') }}" class="{{ request()->is('team/dashboard') ? 'is-active' : '' }}">
                <i class="fa fa-tachometer"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.create') }}" class="{{ request()->is('team/proposals/create') ? 'is-active' : '' }}">
                <i class="fa fa-clipboard"></i> Paste Profile
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.search') }}" class="{{ request()->is('team/proposals/search') ? 'is-active' : '' }}">
                <i class="fa fa-search"></i> Advanced Search
            </a>
        </li>
        <li>
            <a href="{{ route('team.matches') }}" class="{{ request()->is('team/matches') ? 'is-active' : '' }}">
                <i class="fa fa-magic"></i> AI Match
                @isset($sidebarAiMatchesCount)<span class="ur-dash-nav__badge">{{ $sidebarAiMatchesCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.matches') }}" class="{{ request()->is('team/matches') ? 'is-active' : '' }}">
                <i class="fa fa-heart-o"></i> My Matches
                @isset($sidebarAiMatchesCount)<span class="ur-dash-nav__badge">{{ $sidebarAiMatchesCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.search') }}" class="{{ request()->is('team/proposals/search') ? 'is-active' : '' }}">
                <i class="fa fa-users"></i> Team Proposals
                @isset($sidebarTeamProposalsCount)<span class="ur-dash-nav__badge">{{ $sidebarTeamProposalsCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.matchmakers') }}" class="{{ request()->is('team/matchmakers') ? 'is-active' : '' }}">
                <i class="fa fa-users"></i> Matchmakers
                @isset($sidebarMatchmakersCount)<span class="ur-dash-nav__badge">{{ $sidebarMatchmakersCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.mine') }}" class="{{ request()->is('team/proposals/mine') ? 'is-active' : '' }}">
                <i class="fa fa-file-text-o"></i> My Clients
            </a>
        </li>
        <li>
            <a href="{{ route('team.notifications') }}" class="{{ request()->is('team/notifications') ? 'is-active' : '' }}">
                <i class="fa fa-bell-o"></i> Requests
                @isset($sidebarRequestsCount)<span class="ur-dash-nav__badge">{{ $sidebarRequestsCount }}</span>@endisset
            </a>
        </li>

        @if($__sidebarUser->admin == 1)
        <div class="ur-dash-nav__section-label">Management</div>
        <li>
            <a href="{{ url('admin/dashboard') }}">
                <i class="fa fa-cogs"></i> Admin Panel
            </a>
        </li>
        @endif
    </ul>

    <div class="ur-dash-sidebar__status">
        <span class="ur-dash-sidebar__status-dot"></span> AI matching online
        <div>Last updated: just now</div>
    </div>
    <div class="ur-dash-sidebar__footer">
        <button type="button" class="ur-dash-nav__link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Sign out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
