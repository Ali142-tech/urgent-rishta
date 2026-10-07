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
    /* Team members table — the global "table * { padding:10px !important }" rule needs explicit overrides here. */
    .ur-tm-wrap { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; overflow-x: auto; box-shadow: 0 8px 24px rgba(15,46,36,.04); }
    .ur-tm-table { width: 100%; min-width: 1180px; border-collapse: collapse; background: #fff; border-radius: 0; }
    .ur-tm-table th { padding: 14px 16px !important; font-size: 12px !important; letter-spacing: .04em; text-transform: uppercase; background: #F6F4EF; color: #123A2E; white-space: nowrap; }
    .ur-tm-table td { padding: 16px !important; vertical-align: middle !important; border-bottom: 1px solid #F0ECE2; line-height: 1.35 !important; }
    .ur-tm-table tbody tr:last-child td { border-bottom: none; }
    .ur-tm-table tbody tr:hover td { background: #FCFAF5; }
    .ur-tm-table td small, .ur-tm-table td br, .ur-tm-table td div, .ur-tm-table td form, .ur-tm-table td span { padding: 0 !important; }
    .ur-tm-table td span.ur-tm-you { padding: 6px 12px !important; }
    .ur-tm-table td small { display: block; margin-top: 2px; color: #8A938E; font-size: 11.5px; }
    .ur-tm-table td br { display: none; }
    .ur-tm-table select { height: 40px !important; min-width: 135px; padding: 0 34px 0 12px !important; line-height: normal !important; font-size: 13px !important; color: #1C2321; border: 1px solid #DDD5C2 !important; border-radius: 10px !important; background-color: #fff; }
    .ur-tm-table select:focus { outline: none; border-color: #C9974D !important; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .ur-tm-table input[type=text] { height: 40px !important; padding: 0 12px !important; border: 1px solid #DDD5C2 !important; border-radius: 10px !important; font-size: 13px; min-width: 120px; width: 100%; }
    .ur-tm-table .ur-tm-send, .ur-tm-table a.ur-tm-send { display: inline-flex !important; align-items: center; justify-content: center; height: 40px !important; padding: 0 16px !important; line-height: 1 !important; border: none; border-radius: 10px; font-size: 12.5px !important; font-weight: 700; white-space: nowrap; text-decoration: none !important; cursor: pointer; color: #fff; }
    .ur-tm-table a.ur-tm-send { color: #1C2321; }
    .ur-tm-actions { display: flex; gap: 8px; flex-wrap: wrap; min-width: 150px; }
    /* column widths: name, email, mobile, status, photos, message, action */
    .ur-tm-table th:nth-child(1), .ur-tm-table td:nth-child(1) { min-width: 150px; }
    .ur-tm-table th:nth-child(2), .ur-tm-table td:nth-child(2) { min-width: 210px; word-break: normal; overflow-wrap: anywhere; }
    .ur-tm-table th:nth-child(3), .ur-tm-table td:nth-child(3) { min-width: 125px; }
    .ur-tm-table th:nth-child(4), .ur-tm-table td:nth-child(4) { min-width: 150px; }
    .ur-tm-table th:nth-child(5), .ur-tm-table td:nth-child(5) { min-width: 190px; }
    .ur-tm-table th:nth-child(6), .ur-tm-table td:nth-child(6) { min-width: 230px; }
    .ur-tm-table th:nth-child(7), .ur-tm-table td:nth-child(7) { min-width: 150px; }
    .ur-tm-msg { display: flex; gap: 8px; align-items: center; }
    .ur-tm-you { display: inline-block; font-size: 12px; font-weight: 700; color: #123A2E; background: #E6F1EA; border-radius: 999px; padding: 6px 12px; }
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

<div class="ur-tm-wrap">
<table class="ur-tm-table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Mobile</th><th>Status</th><th>Photos</th><th>Message</th><th>Action</th></tr>
    </thead>
    <tbody>
        @forelse($members as $member)
        <tr id="tm_row_{{ $member->dataid }}">
            <td>{{ $member->first_name }} {{ $member->last_name }}@if($member->isPremium()) @include('team.partials.premium-badge')@endif<br><small style="color:#6B7570;">{{ $member->dataid }}</small></td>
            <td>{{ $member->email }}</td>
            <td>{{ $member->contact_mobile_number }}</td>
            <td id="tm_status_{{ $member->dataid }}">
                @if($member->id === auth()->id())
                    <span class="ur-tm-you">{{ ucfirst($member->status ?? 'active') }} &middot; you</span>
                @else
                    <select class="js-member-status" data-id="{{ $member->dataid }}" data-current="{{ $member->status ?? 'active' }}" style="height:34px; border:1px solid #E7E2D6; border-radius:8px; padding:0 8px; font-size:13px; background:#fff;">
                        @foreach(['active' => 'Active', 'suspended' => 'Suspended', 'deactivated' => 'Deactivated'] as $v => $l)
                            <option value="{{ $v }}" {{ ($member->status ?? 'active') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td>
                @if($member->isAdmin())
                    <span class="ur-tm-you">Originals (admin)</span>
                @else
                    <select class="js-photo-access" data-id="{{ $member->dataid }}" data-current="{{ $member->can_view_originals ? '1' : '0' }}" style="height:34px; border:1px solid #E7E2D6; border-radius:8px; padding:0 8px; font-size:13px; background:#fff;">
                        <option value="0" {{ !$member->can_view_originals ? 'selected' : '' }}>With watermark</option>
                        <option value="1" {{ $member->can_view_originals ? 'selected' : '' }}>Original (no watermark)</option>
                    </select>
                @endif
            </td>
            <td>
                <form method="POST" action="{{ route('team.manage.members.message') }}" class="ur-tm-msg">
                    @csrf
                    <input type="hidden" name="recipient" value="{{ $member->dataid }}">
                    <input type="text" name="message" placeholder="Message..." required>
                    <button type="submit" class="ur-tm-send">Send</button>
                </form>
            </td>
            <td>
              <div class="ur-tm-actions">
                <button type="button" class="ur-tm-send" style="background:#123A2E;" onclick="return sendResetLink(this, '{{ $member->dataid }}');">Reset password</button>
                <a class="ur-tm-send" style="background:#C9974D;" href="{{ route('team.manage.proposals', ['matchmaker' => $member->id]) }}">View proposals</a>
              </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7">{{ $search !== '' ? 'No team members matching "'.$search.'".' : 'No team members yet.' }}</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<script>
    // Photo access: lets this member see every proposal's photos without the watermark.
    document.querySelectorAll('select.js-photo-access').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var previous = sel.dataset.current, wanted = sel.value;
            sel.disabled = true;
            $.ajax({
                type: 'post',
                url: "{{ url('team/manage/members') }}/" + sel.dataset.id + '/photo-access',
                data: { '_token': '{{ csrf_token() }}', originals: wanted },
                success: function (result) {
                    sel.disabled = false;
                    if (result.code == '200') { sel.dataset.current = wanted; swalAlert('success', 'Done', result.message, function () {}); }
                    else { sel.value = previous; swalAlert('error', 'Error', result.message, function () {}); }
                },
                error: function () { sel.disabled = false; sel.value = previous; swalAlert('error', 'Error', 'Something went wrong. Please try again.', function () {}); }
            });
        });
    });

    // Status dropdown: Active / Suspended / Deactivated — TeamAdminController::updateStatus().
    document.querySelectorAll('select.js-member-status').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var map = { active: 'reactivate', suspended: 'suspend', deactivated: 'deactivate' };
            var labels = { active: 'Active', suspended: 'Suspended', deactivated: 'Deactivated' };
            var previous = sel.dataset.current, wanted = sel.value;
            swalConfirm('Change status to ' + labels[wanted] + '?', wanted === 'active'
                ? 'They will be able to sign in to the Team Dashboard again.'
                : 'They will be signed out of the Team Dashboard and unable to sign in until set back to Active.', function () {
                sel.disabled = true;
                $.ajax({
                    type: 'post',
                    url: "{{ url('team/manage/members') }}/" + map[wanted] + '/' + sel.dataset.id,
                    data: { '_token': '{{ csrf_token() }}' },
                    success: function (result) {
                        sel.disabled = false;
                        if (result.code == '200') { sel.dataset.current = wanted; sel.value = wanted; swalAlert('success', 'Done', result.message, function () {}); }
                        else { sel.value = previous; swalAlert('error', 'Error', result.message, function () {}); }
                    },
                    error: function () { sel.disabled = false; sel.value = previous; swalAlert('error', 'Error', 'Something went wrong. Please try again.', function () {}); }
                });
            });
            // cancelled confirmation: put the dropdown back
            sel.value = previous;
            setTimeout(function () { if (!sel.disabled) sel.value = sel.dataset.current; }, 0);
        });
    });

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
