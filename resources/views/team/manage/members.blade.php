{{--
    Admin ▸ Team Members — one card per team member: who they are, contact, account status, photo access, privacy and
    the admin's actions. The dropdown
    classes (js-member-status, js-photo-access, js-private-member, js-private-access) are wired by the script below.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Team Members')
@section('main-content')
<style>
    .ur-tmx { max-width: 1400px; }
    .ur-tmx-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .ur-tmx-eyebrow { font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #C9974D; margin: 0 0 6px; }
    .ur-tmx-head h1 { font-family: 'Manrope', system-ui, sans-serif; font-size: 30px; font-weight: 800; color: #123A2E; margin: 0 0 6px; line-height: 1.15 !important; }
    .ur-tmx-head p { margin: 0; font-size: 14px; color: #5B6560; }
    .ur-tmx-pill { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #E7E2D6; border-radius: 999px; padding: 9px 18px; font-size: 13.5px; font-weight: 600; color: #123A2E; }
    .ur-tmx-pill i { width: 9px; height: 9px; border-radius: 50%; background: #1C9A6C; display: block; }

    .ur-tmx-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-tmx-search { position: relative; flex: 1 1 320px; max-width: 360px; margin: 0; }
    .ur-tmx-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #7B8781; }
    .ur-tmx-search input { width: 100%; height: 44px; border: 1px solid #E7E2D6; border-radius: 12px; background: #fff; padding: 0 14px 0 40px; font-size: 14px; outline: none; }
    .ur-tmx-bar select { height: 44px; min-width: 160px; border: 1px solid #E7E2D6; border-radius: 12px; background-color: #fff; padding: 0 14px; font-size: 14px; color: #1C2321; }
    .ur-tmx-count { font-size: 13.5px; color: #6B7570; }
    .ur-tmx-more { text-align: center; margin-top: 18px; }
    .ur-tmx-more .ur-tmx-btn { min-width: 180px; height: 44px; }
    .ur-tmx-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; height: 40px; padding: 0 16px; border: 0; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none !important; white-space: nowrap; }
    .ur-tmx-btn--dark { background: #123A2E; color: #fff !important; }
    .ur-tmx-btn--dark:hover { background: #0D2C23; }
    .ur-tmx-btn--gold { background: #C9974D; color: #fff !important; }
    .ur-tmx-btn--gold:hover { background: #B88640; }
    .ur-tmx-btn--ghost { background: #fff; color: #123A2E !important; border: 1px solid #DDD5C2; }
    .ur-tmx-btn--ghost:hover { background: #F6F4EF; }


    .ur-tmx-list { display: grid; gap: 14px; }
    .ur-tmx-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 20px 22px; box-shadow: 0 2px 10px rgba(15,46,36,.04); }
    .ur-tmx-card.is-me { background: #FAF8F2; }
    .ur-tmx-card[hidden] { display: none; }
    .ur-tmx-row { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1.25fr) 150px 170px 200px 160px; gap: 18px; align-items: start; }
    .ur-tmx-person { display: flex; gap: 14px; align-items: center; min-width: 0; }
    .ur-tmx-avatar { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 14px; flex-shrink: 0; }
    .ur-tmx-name { font-size: 14.5px; font-weight: 800; color: #1C2321; line-height: 1.3; overflow-wrap: anywhere; }
    .ur-tmx-id { font-size: 12.5px; color: #8A938E; margin-top: 2px; letter-spacing: .02em; }
    .ur-tmx-contact { font-size: 13.5px; color: #4B5651; line-height: 1.5; min-width: 0; overflow-wrap: anywhere; padding-top: 2px; }
    .ur-tmx-contact span { display: block; color: #8A938E; }
    .ur-tmx-field { display: grid; gap: 8px; align-content: start; }
    .ur-tmx-field select { width: 100%; height: 40px; border: 1px solid #DDD5C2; border-radius: 10px; background-color: #fff; padding: 0 30px 0 12px; font-size: 13px; color: #1C2321; }
    .ur-tmx-field select:focus { outline: none; border-color: #C9974D; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .ur-tmx-tag { display: inline-block; justify-self: start; background: #E3F3EA; color: #1C7A58; border-radius: 999px; padding: 6px 14px; font-size: 12px; font-weight: 700; }
    .ur-tmx-actions { display: grid; gap: 8px; align-content: start; }
    .ur-tmx-actions .ur-tmx-btn { width: 100%; }
    .ur-tmx-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 36px; text-align: center; color: #6B7570; }

    @media (max-width: 1280px) {
        .ur-tmx-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .ur-tmx-person { grid-column: 1 / 3; }
        .ur-tmx-actions { grid-column: 1 / -1; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    }
    @media (max-width: 760px) {
        .ur-tmx-row { grid-template-columns: 1fr 1fr; gap: 14px; }
        .ur-tmx-person, .ur-tmx-contact { grid-column: 1 / -1; }
        .ur-tmx-field { grid-column: auto; }
        .ur-tmx-head h1 { font-size: 24px; }
        .ur-tmx-search { max-width: none; flex-basis: 100%; }
    }
</style>

@php
    $palette = ['#123A2E', '#C9974D', '#8B6BA8', '#3F8F68', '#B5574A', '#4A6FA5', '#A65E8B'];
@endphp

<div class="ur-tmx">
    <div class="ur-tmx-head">
        <div>
            <div class="ur-tmx-eyebrow">Admin &middot; Team</div>
            <h1>Team Members</h1>
            <p>Manage access, visibility, and communication for every team member.</p>
        </div>
        <span class="ur-tmx-pill"><i></i> {{ $activeTotal }} active of {{ $total }}</span>
    </div>

    <form method="GET" action="{{ route('team.manage.members') }}" class="ur-tmx-bar" id="tmx_filters">
        <label class="ur-tmx-search">
            <i class="fa fa-search"></i>
            <input type="search" name="search" placeholder="Search name, email, ID…" value="{{ $search }}" autocomplete="off">
        </label>
        <select name="status" aria-label="Filter by status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['active' => 'Active', 'suspended' => 'Suspended', 'deactivated' => 'Deactivated'] as $v => $l)
                <option value="{{ $v }}" {{ $statusFilter === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <span class="ur-tmx-count" id="tmx_count">Showing {{ $members->count() }} of {{ $members->total() }} member{{ $members->total() === 1 ? '' : 's' }}</span>
        @if($search !== '' || $statusFilter !== '')<a href="{{ route('team.manage.members') }}" class="ur-tmx-btn ur-tmx-btn--ghost">Clear</a>@endif
    </form>
    <div class="ur-tmx-list" id="tmx_list">
        @include('team.manage.partials.member-cards', ['members' => $members])
    </div>

    @if($members->isEmpty())
        <div class="ur-tmx-empty">{{ $search !== '' ? 'No team members matching "' . $search . '".' : 'No team members found.' }}</div>
    @endif

    @if($members->hasMorePages())
        <div class="ur-tmx-more">
            <button type="button" class="ur-tmx-btn ur-tmx-btn--ghost" id="tmx_more" data-next="{{ $members->nextPageUrl() }}">Load more</button>
        </div>
    @endif
</div>

<script>
    // "Load more": fetches the next page of cards (10 at a time) and appends them; the button goes away on the last page.
    (function () {
        var btn = document.getElementById('tmx_more');
        if (!btn) return;
        var list = document.getElementById('tmx_list');
        btn.addEventListener('click', function () {
            btn.disabled = true;
            var label = btn.textContent;
            btn.textContent = 'Loading…';
            fetch(btn.dataset.next, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (res) {
                    list.insertAdjacentHTML('beforeend', res.html);
                    if (typeof tmxBind === 'function') tmxBind(list);
                    document.getElementById('tmx_count').textContent = 'Showing ' + list.querySelectorAll('.ur-tmx-card').length + ' of ' + res.total + ' member' + (res.total === 1 ? '' : 's');
                    if (res.next) { btn.dataset.next = res.next; btn.disabled = false; btn.textContent = label; }
                    else { btn.parentNode.remove(); }
                })
                .catch(function () { btn.disabled = false; btn.textContent = label; swalAlert('error', 'Error', 'Could not load more members. Please try again.', function () {}); });
        });
    })();
</script>
<script>
    // Wires the dropdowns inside 'root' (the page at first, then every batch added by "Load more").
    function tmxBind(root) {
    // Photo access: lets this member see every proposal's photos without the watermark.
    root.querySelectorAll('select.js-photo-access').forEach(function (sel) {
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

    // Privacy: "private member" (their proposals are private) and "can see private proposals".
    [['select.js-private-member', 'private-member', 'private'], ['select.js-private-access', 'private-access', 'access']].forEach(function (cfg) {
        root.querySelectorAll(cfg[0]).forEach(function (sel) {
            sel.addEventListener('change', function () {
                var previous = sel.dataset.current, wanted = sel.value, data = { '_token': '{{ csrf_token() }}' };
                data[cfg[2]] = wanted;
                sel.disabled = true;
                $.ajax({
                    type: 'post',
                    url: "{{ url('team/manage/members') }}/" + sel.dataset.id + '/' + cfg[1],
                    data: data,
                    success: function (result) {
                        sel.disabled = false;
                        if (result.code == '200') { sel.dataset.current = wanted; swalAlert('success', 'Done', result.message, function () {}); }
                        else { sel.value = previous; swalAlert('error', 'Error', result.message, function () {}); }
                    },
                    error: function () { sel.disabled = false; sel.value = previous; swalAlert('error', 'Error', 'Something went wrong. Please try again.', function () {}); }
                });
            });
        });
    });

    // Status dropdown: Active / Suspended / Deactivated — TeamAdminController::updateStatus().
    root.querySelectorAll('select.js-member-status').forEach(function (sel) {
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
                        if (result.code == '200') { sel.dataset.current = wanted; sel.value = wanted; var card = sel.closest('.ur-tmx-card'); if (card) card.dataset.status = wanted; swalAlert('success', 'Done', result.message, function () {}); }
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

    }
    tmxBind(document);

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
