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

    Mapping note: "AI Match" (team.matches — pick any of your own proposals,
    filter, see every candidate) and "My Matches" (team.my-matches — every
    client you own, each paired with its single best match, no controls)
    are now separate pages (client mockup, Sep 2026), sharing only the
    same sidebarAiMatchesCount badge number; "Requests" uses unread
    notifications as a real stand-in for the
    mockup's removed Collaboration Request feature (see TeamController::
    dashboard()'s own comment) — flagged to the client, not yet confirmed.
--}}
<?php use App\User; ?>
<aside class="ur-dash-sidebar ur-dash-sidebar--team" id="ur_dash_sidebar">
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
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.create') }}" class="{{ request()->is('team/proposals/create') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/></svg> Paste Profile
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.search') }}" class="{{ request()->is('team/proposals/search') && request('view') !== 'all' ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7.5"/><path d="m21 21-4.3-4.3"/></svg> Advanced Search
            </a>
        </li>
        <li>
            <a href="{{ route('team.matches') }}" class="{{ request()->is('team/matches') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.94 14.06 8 20l-1.94-5.94L0.12 12.12 6.06 10.2 8 4.26l1.94 5.94 5.94 1.92z" transform="translate(3 0)"/><path d="M19 3v4M17 5h4M19 17v4M17 19h4"/></svg> AI Match
                @isset($sidebarAiMatchesCount)<span class="ur-dash-nav__badge">{{ $sidebarAiMatchesCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.my-matches') }}" class="{{ request()->is('team/my-matches') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7z"/></svg> My Matches
                @isset($sidebarAiMatchesCount)<span class="ur-dash-nav__badge">{{ $sidebarAiMatchesCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.search', ['view' => 'all']) }}" class="{{ request()->is('team/proposals/search') && request('view') === 'all' ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg> Team Proposals
                @isset($sidebarTeamProposalsCount)<span class="ur-dash-nav__badge">{{ $sidebarTeamProposalsCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.matchmakers') }}" class="{{ request()->is('team/matchmakers') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg> Matchmakers
                @isset($sidebarMatchmakersCount)<span class="ur-dash-nav__badge">{{ $sidebarMatchmakersCount }}</span>@endisset
            </a>
        </li>
        <li>
            <a href="{{ route('team.proposals.mine') }}" class="{{ request()->is('team/proposals/mine') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/></svg> My Clients
            </a>
        </li>
       

        @if($__sidebarUser->admin == 1)
        <div class="ur-dash-nav__section-label">Management</div>
        <li>
            <a href="{{ url('admin/dashboard') }}">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg> Admin Panel
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
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v10M18.4 6.6a9 9 0 1 1-12.8 0"/></svg> Sign out
        </button>
    </div>
</aside>
<div class="ur-dash-backdrop" id="ur_dash_backdrop"></div>
