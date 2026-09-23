{{--
    Full notifications list — the topbar bell only shows a handful. Marks
    read via the existing global `?read=<id>` convention
    (MarkNotificationAsRead middleware, bootstrap/app.php). See
    TeamController::notificationsList().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Notifications')
@section('main-content')
<style>
    .ur-notif-list { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #E7E2D6; }
    .ur-notif-row { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-bottom: 1px solid #E7E2D6; font-size: 13.5px; }
    .ur-notif-row:last-child { border-bottom: none; }
    .ur-notif-row.is-unread { background: #FCF3E3; }
    .ur-notif-row__icon { color: #C9974D; font-size: 16px; margin-top: 2px; }
    .ur-notif-row__body b { color: #123A2E; }
    .ur-notif-row__time { font-size: 11px; color: #6B7570; margin-top: 2px; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 18px;">Notifications</h1>

<div class="ur-notif-list">
    @forelse($notifications as $notification)
        @php
            $data = $notification->data;
            $status = $data['status'] ?? class_basename($notification->type);
        @endphp
        <a href="{{ url()->current() . '?read=' . $notification->id }}" class="ur-notif-row {{ $notification->read_at ? '' : 'is-unread' }}" style="text-decoration:none; color:inherit; display:flex;">
            <i class="fa fa-bell-o ur-notif-row__icon"></i>
            <div class="ur-notif-row__body">
                <div><b>{{ $status }}</b></div>
                @if(!empty($data['message']))<div>{{ $data['message'] }}</div>@endif
                @if(!empty($data['proposal']) && empty($data['message']))<div>Regarding proposal: {{ $data['proposal'] }}{{ !empty($data['proposalid']) ? ' ('.$data['proposalid'].')' : '' }}</div>@endif
                <div class="ur-notif-row__time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
        </a>
    @empty
        <div class="ur-notif-row"><i>No notifications yet.</i></div>
    @endforelse
</div>

<div style="margin-top:20px;">{{ $notifications->links() }}</div>
@endsection
