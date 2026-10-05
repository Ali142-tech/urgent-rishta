{{--
    Website Upgrade Brief §13 "Manage notifications" — send a message to
    one team member or broadcast to all (App\Notifications\AdminMessage).
--}}
@extends('layouts.team.dashboard')
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
    .ur-tm-search { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 16px 18px; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
    .ur-tm-search .form-group { flex: 1 1 260px; }
    .ur-tm-search label { display: block; font-size: 12.5px; font-weight: 700; color: #123A2E; margin-bottom: 6px; }
    .ur-tm-search input[type=search] { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; height: 36px; padding: 0 12px; font-size: 13.5px; }
    .ur-tm-search .ur-tm-search-btn { background: #123A2E; color: #fff; border: none; border-radius: 8px; height: 36px; padding: 0 18px; font-size: 13px; font-weight: 700; }
    .ur-tm-search .ur-tm-clear-btn { display: inline-flex; align-items: center; height: 36px; padding: 0 16px; border: 1px solid #E7E2D6; border-radius: 8px; font-size: 13px; color: #5B6560; text-decoration: none; }
</style>

<div class="ur-tm-search">
    <form method="GET" action="{{ route('team.manage.members') }}" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; flex:1;">
        <div class="form-group">
            <label for="search">Search by name or email</label>
            <input type="search" name="search" id="search" placeholder="Search team members..." value="{{ $search }}" autocomplete="off">
        </div>
        <button type="submit" class="ur-tm-search-btn">Search</button>
        @if($search !== '')
        <a href="{{ route('team.manage.members') }}" class="ur-tm-clear-btn">Clear</a>
        @endif
    </form>
</div>

<div class="ur-tm-broadcast">
    <h3>Message All Team Members</h3>
    <form method="POST" action="{{ route('team.manage.members.message') }}">
        @csrf
        <input type="hidden" name="recipient" value="all">
        <textarea name="message" placeholder="Message to send to every team member..." required></textarea>
        <button type="submit" class="ur-tm-send">Send to All</button>
    </form>
</div>

<table class="ur-tm-table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Mobile</th><th>Status</th><th>Message</th><th>Action</th></tr>
    </thead>
    <tbody>
        @forelse($members as $member)
        <tr id="tm_row_{{ $member->dataid }}">
            <td>{{ $member->first_name }} {{ $member->last_name }}<br><small style="color:#6B7570;">{{ $member->dataid }}</small></td>
            <td>{{ $member->email }}</td>
            <td>{{ $member->contact_mobile_number }}</td>
            <td id="tm_status_{{ $member->dataid }}">{{ ucfirst($member->status ?? 'active') }}</td>
            <td>
                <form method="POST" action="{{ route('team.manage.members.message') }}" style="display:flex; gap:6px;">
                    @csrf
                    <input type="hidden" name="recipient" value="{{ $member->dataid }}">
                    <input type="text" name="message" placeholder="Message..." required>
                    <button type="submit" class="ur-tm-send">Send</button>
                </form>
            </td>
            <td style="white-space:nowrap;">
                <button type="button" class="ur-tm-send" style="background:#123A2E;" onclick="return sendResetLink(this, '{{ $member->dataid }}');">Reset password</button>
                @if($member->id === auth()->id())
                    <small style="color:#6B7570;">This is you</small>
                @elseif(($member->status ?? 'active') === 'active')
                    <button type="button" class="ur-tm-send" style="background:#5B6560;" onclick="return setTeamMemberStatus(this, 'suspend', '{{ $member->dataid }}');">Suspend</button>
                    <button type="button" class="ur-tm-send" style="background:#B5674A;" onclick="return setTeamMemberStatus(this, 'deactivate', '{{ $member->dataid }}');">Deactivate</button>
                @else
                    <button type="button" class="ur-tm-send" style="background:#123A2E;" onclick="return setTeamMemberStatus(this, 'reactivate', '{{ $member->dataid }}');">Reactivate</button>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6">{{ $search !== '' ? 'No team members matching "'.$search.'".' : 'No team members yet.' }}</td></tr>
        @endforelse
    </tbody>
</table>

<script>
    // E-mails the member a link to set a new password.
    function sendResetLink(elem, id) {
        swalConfirm('Send reset link?', 'The member will get an e-mail with a link to set a new password.', () => {
            elem.disabled = true;
            $.ajax({
                type: 'post',
                url: "{{ url('team/manage/members') }}/" + id + '/reset-link',
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) { elem.disabled = false; swalAlert(result.code == '200' ? 'success' : 'error', result.code == '200' ? 'Sent' : 'Error', result.message, () => {}); },
                error: function () { elem.disabled = false; swalAlert('error', 'Error', 'Something went wrong. Please try again.', () => {}); }
            });
        });
        return false;
    }

    // Suspend / deactivate / reactivate — TeamAdminController::updateStatus().
    function setTeamMemberStatus(elem, action, id) {
        var labels = {suspend: 'Suspend', deactivate: 'Deactivate', reactivate: 'Reactivate'};
        swalConfirm(labels[action] + " team member?", action === 'reactivate'
            ? "They will be able to sign in to the Team Dashboard again."
            : "They will be signed out of the Team Dashboard and unable to sign in until reactivated.", () => {
            elem.disabled = true;
            $.ajax({
                type: "post",
                url: "{{ url('team/manage/members') }}/" + action + "/" + id,
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') {
                        swalAlert("success", "Done", result.message, () => { window.location.reload(); });
                    } else {
                        elem.disabled = false;
                        swalAlert("error", "Error", result.message, () => {});
                    }
                },
                error: function () {
                    elem.disabled = false;
                    swalAlert("error", "Error", "Something went wrong. Please try again.", () => {});
                }
            });
        });
        return false;
    }
</script>
@endsection
