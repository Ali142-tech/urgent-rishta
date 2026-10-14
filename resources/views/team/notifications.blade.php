{{--
    Team notifications page — every notification of the logged-in team member, grouped by day, with an All / Unread
    filter and "Mark all as read". Clicking a row opens what it is about (the new proposal, the AI match, ...) and
    marks it read through the global `?read=<id>` convention (MarkNotificationAsRead middleware). See
    TeamController::notificationsList() / notificationsMarkAllRead().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Notifications')
@section('main-content')
<style>
    .ur-nt-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-nt-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 26px; line-height: 1.2 !important; margin: 0 0 4px; }
    .ur-nt-head p { margin: 0; color: #6B7570; font-size: 13.5px; }
    .ur-nt-readall { display: inline-flex; align-items: center; gap: 7px; height: 40px; padding: 0 16px; border-radius: 10px; border: 1px solid #1C6B52; background: #fff; color: #1C6B52; font-size: 13px; font-weight: 700; cursor: pointer; }
    .ur-nt-readall:hover { background: #F2F8F5; }

    .ur-nt-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
    .ur-nt-tabs a { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #4B5651 !important; font-size: 13px; font-weight: 700; text-decoration: none; }
    .ur-nt-tabs a.is-active { background: #123A2E; border-color: #123A2E; color: #fff !important; }
    .ur-nt-tabs a span { background: #F3EFE6; color: #123A2E; border-radius: 999px; min-width: 22px; padding: 1px 7px; font-size: 11px; text-align: center; }
    .ur-nt-tabs a.is-active span { background: rgba(255,255,255,.18); color: #fff; }

    .ur-nt-day { font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #7B8781; margin: 22px 0 8px; }
    .ur-nt-list { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; overflow: hidden; }
    .ur-nt-row { position: relative; display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-bottom: 1px solid #F0ECE1; text-decoration: none !important; color: inherit !important; background: #fff; }
    .ur-nt-row:last-child { border-bottom: 0; }
    .ur-nt-row:hover { background: #FAF8F3; }
    .ur-nt-row.is-unread { background: #F6FAF7; }
    .ur-nt-row.is-unread::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: #1C9A6C; }
    .ur-nt-avatar { position: relative; width: 48px; height: 48px; flex-shrink: 0; }
    .ur-nt-avatar > img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; display: block; background: #F3EFE6; }
    .ur-nt-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; background: #F3EFE6; color: #C9974D; }
    .ur-nt-icon--match { background: #E7F3EC; color: #1C6B52; }
    .ur-nt-icon--admin { background: #EEF0FB; color: #4A4FA3; }
    .ur-nt-badge { position: absolute; right: -3px; bottom: -3px; width: 20px; height: 20px; border-radius: 50%; border: 2px solid #fff; display: flex !important; align-items: center; justify-content: center; font-size: 9px; background: #C9974D; color: #fff; }
    .ur-nt-badge--match { background: #1C9A6C; }
    .ur-nt-badge--admin { background: #4A4FA3; }
    .ur-nt-avatar > span.ur-nt-icon + .ur-nt-badge { display: none !important; }
    .ur-nt-body { flex: 1; min-width: 0; }
    .ur-nt-title { font-size: 14px; font-weight: 700; color: #123A2E; line-height: 1.35 !important; margin: 0 0 2px; }
    .ur-nt-text { font-size: 13px; color: #4B5651; line-height: 1.45 !important; margin: 0; overflow-wrap: anywhere; }
    .ur-nt-time { font-size: 11.5px; color: #8A938E; margin: 4px 0 0; }
    .ur-nt-go { flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #1C6B52; white-space: nowrap; }
    .ur-nt-new { flex-shrink: 0; font-size: 10px; font-weight: 800; letter-spacing: .04em; background: #1C9A6C; color: #fff; border-radius: 999px; padding: 2px 8px; margin-left: 8px; vertical-align: middle; }

    .ur-nt-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 48px 20px; text-align: center; color: #6B7570; }
    .ur-nt-empty i { display: block; font-size: 34px; color: #C9D6CF; margin-bottom: 10px; }
    .ur-nt-pager { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 20px; font-size: 13px; color: #6B7570; }
    .ur-nt-pager a, .ur-nt-pager span.is-off { display: inline-flex; align-items: center; height: 38px; padding: 0 16px; border-radius: 10px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E !important; font-weight: 700; text-decoration: none; }
    .ur-nt-pager span.is-off { opacity: .4; }
    .ur-nt-flash { background: #E7F3EC; color: #1C6B52; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; margin-bottom: 14px; }

    @media (max-width: 560px) {
        .ur-nt-row { padding: 12px 14px; gap: 11px; align-items: flex-start; }
        .ur-nt-avatar, .ur-nt-avatar > img, .ur-nt-icon { width: 40px; height: 40px; }
        .ur-nt-icon { font-size: 16px; }
        .ur-nt-go { display: none; }
        .ur-nt-readall { width: 100%; justify-content: center; }
    }
</style>

@php
    $dayLabel = function ($date) {
        if ($date->isToday()) return 'Today';
        if ($date->isYesterday()) return 'Yesterday';
        return $date->isSameYear(now()) ? $date->format('l, j F') : $date->format('j F Y');
    };
@endphp

<div class="ur-nt-head">
    <div>
        <h1>Notifications</h1>
        <p>{{ $unreadCount ? $unreadCount . ' unread' : 'You are all caught up' }} &middot; {{ $totalCount }} in total</p>
    </div>
    @if($unreadCount)
    <form method="POST" action="{{ route('team.notifications.read-all') }}">
        @csrf
        <button type="submit" class="ur-nt-readall"><i class="fa fa-check"></i> Mark all as read</button>
    </form>
    @endif
</div>

@if(session('status'))<div class="ur-nt-flash">{{ session('status') }}</div>@endif

<div class="ur-nt-tabs">
    <a href="{{ route('team.notifications') }}" class="{{ $filter === 'all' ? 'is-active' : '' }}">All <span>{{ $totalCount }}</span></a>
    <a href="{{ route('team.notifications', ['filter' => 'unread']) }}" class="{{ $filter === 'unread' ? 'is-active' : '' }}">Unread <span>{{ $unreadCount }}</span></a>
</div>

@forelse($notifications->groupBy(fn ($n) => $dayLabel($n->created_at)) as $day => $group)
    <div class="ur-nt-day">{{ $day }}</div>
    <div class="ur-nt-list">
        @foreach($group as $notification)
            @php($u = $ui[$notification->id])
            @php($unread = is_null($notification->read_at))
            <a href="{{ $u['url'] }}" class="ur-nt-row {{ $unread ? 'is-unread' : '' }}">
                <div class="ur-nt-avatar">
                    @if($u['image'])
                        <img src="{{ $u['image'] }}" alt="" loading="lazy" onerror="this.remove()">
                    @else
                        <span class="ur-nt-icon ur-nt-icon--{{ $u['tone'] ?: 'plain' }}"><i class="fa {{ $u['icon'] }}"></i></span>
                    @endif
                    <i class="ur-nt-badge ur-nt-badge--{{ $u['tone'] ?: 'plain' }} fa {{ $u['icon'] }}"></i>
                </div>
                <div class="ur-nt-body">
                    <div class="ur-nt-title">{{ $u['title'] }}@if($unread)<span class="ur-nt-new">NEW</span>@endif</div>
                    @if($u['text'])<p class="ur-nt-text">{{ $u['text'] }}</p>@endif
                    <div class="ur-nt-time">{{ $u['clock'] }} &middot; {{ $u['time'] }}</div>
                </div>
                @if($u['go'])<span class="ur-nt-go">{{ $u['go'] }} <i class="fa fa-angle-right"></i></span>@endif
            </a>
        @endforeach
    </div>
@empty
    <div class="ur-nt-empty">
        <i class="fa fa-bell-o"></i>
        {{ $filter === 'unread' ? 'No unread notifications.' : 'No notifications yet.' }}
    </div>
@endforelse

@if($notifications->hasPages())
<div class="ur-nt-pager">
    @if($notifications->onFirstPage())<span class="is-off">&lsaquo; Newer</span>@else<a href="{{ $notifications->previousPageUrl() }}">&lsaquo; Newer</a>@endif
    <span>Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}</span>
    @if($notifications->hasMorePages())<a href="{{ $notifications->nextPageUrl() }}">Older &rsaquo;</a>@else<span class="is-off">Older &rsaquo;</span>@endif
</div>
@endif
@endsection
