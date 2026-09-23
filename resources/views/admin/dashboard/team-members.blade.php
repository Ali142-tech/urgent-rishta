{{--
    Website Upgrade Brief §13 "Manage notifications" — send a message to
    one team member or broadcast to all (App\Notifications\AdminMessage).
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Team Members')
@section('main-content')
<style>
    .ur-tm-broadcast { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 18px; margin-bottom: 20px; }
    .ur-tm-broadcast textarea { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; padding: 10px; min-height: 70px; margin-bottom: 10px; }
    .ur-tm-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
    .ur-tm-table th, .ur-tm-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #E7E2D6; font-size: 13.5px; }
    .ur-tm-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; }
    .ur-tm-table input[type=text] { width: 100%; border: 1px solid #E7E2D6; border-radius: 6px; height: 32px; padding: 0 8px; }
    .ur-tm-send { background: #C9974D; color: #fff; border: none; border-radius: 6px; height: 32px; padding: 0 12px; font-size: 12px; }
</style>

<div class="ur-tm-broadcast">
    <h3>Message All Team Members</h3>
    <form method="POST" action="{{ route('admin.team-members.message') }}">
        @csrf
        <input type="hidden" name="recipient" value="all">
        <textarea name="message" placeholder="Message to send to every team member..." required></textarea>
        <button type="submit" class="ur-tm-send">Send to All</button>
    </form>
</div>

<table class="ur-tm-table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Status</th><th>Message</th></tr>
    </thead>
    <tbody>
        @forelse($members as $member)
        <tr>
            <td>{{ $member->first_name }} {{ $member->last_name }}</td>
            <td>{{ $member->email }}</td>
            <td>{{ ucfirst($member->team_member_status ?? 'active') }}</td>
            <td>
                <form method="POST" action="{{ route('admin.team-members.message') }}" style="display:flex; gap:6px;">
                    @csrf
                    <input type="hidden" name="recipient" value="{{ $member->dataid }}">
                    <input type="text" name="message" placeholder="Message..." required>
                    <button type="submit" class="ur-tm-send">Send</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No team members yet.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
