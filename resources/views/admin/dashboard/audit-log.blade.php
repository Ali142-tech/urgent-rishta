{{--
    Website Upgrade Brief §16 — queryable audit trail (see App\AuditLog).
    Every Log::info(...) call elsewhere in this app only reaches
    storage/logs/laravel.log; this is the one admin-visible history.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Audit Log')
@section('main-content')
@php
    // action code => [label, icon, tone]
    $actionMeta = [
        'profile.viewed'          => ['Profile viewed', 'fa-eye', 'blue'],
        'profile.shared'          => ['Profile shared', 'fa-share-alt', 'gold'],
        'match.marked_successful' => ['Match marked successful', 'fa-heart', 'green'],
        'login'                   => ['Signed in', 'fa-sign-in', 'grey'],
    ];
    $tones = ['#17493A', '#2C5F8A', '#B36B2E', '#1E7A46', '#8A6218', '#3B5B52'];
@endphp
<style>
    .ur-au { width: 100%; font-family: 'Manrope', system-ui, sans-serif; color: #1C2321; }
    /* the site-wide stylesheet pads and spaces table cells / b / small generously — pin them so rows stay compact */
    .ur-au .ur-au-table td { padding: 10px 12px !important; height: auto !important; line-height: 1.3 !important; }
    .ur-au .ur-au-table th { line-height: 1.2 !important; }
    .ur-au b, .ur-au small, .ur-au p, .ur-au h1, .ur-au-tabs a, .ur-au-actor div, .ur-au-when div { padding: 0 !important; }
    .ur-au span:not(.ur-au-badge):not(.ur-au-subject):not(.ur-au-avatar):not(.ur-au-total) { padding: 0 !important; }
    .ur-au .ur-au-table tr { padding: 0; height: auto; }
    .ur-au b, .ur-au small, .ur-au span, .ur-au p, .ur-au h1 { margin: 0; }
    .ur-au .ur-au-actor b { margin: 0 0 2px; }
    .ur-au .ur-au-badge { line-height: 1.2; padding: 5px 12px !important; }
    .ur-au .ur-au-subject { line-height: 1.2; padding: 4px 10px !important; }
    .ur-au-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .ur-au-head h1 { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 28px; line-height: 1.2; color: #123A2E; margin: 0 0 6px; }
    .ur-au-head p { margin: 0; font-size: 14px; color: #6B7570; }
    .ur-au-total { background: #F3F0E8; color: #6B7570; font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 999px; white-space: nowrap; }

    .ur-au-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 22px; padding: 18px; box-shadow: 0 6px 18px rgba(15, 46, 36, .05); }
    .ur-au-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .ur-au .ur-au-tab { padding: 0 16px !important; }
    .ur-au-tab { display: inline-flex; align-items: center; gap: 7px; height: 38px; padding: 0 16px; border: 1px solid #E7E2D6; border-radius: 999px; background: #fff; color: #4B5651 !important; font-size: 13px; font-weight: 600; text-decoration: none !important; transition: all .15s ease; }
    .ur-au-tab i { color: #B88436 !important; font-size: 13px; }
    .ur-au-tab:hover { border-color: #C9974D; background: #FBF8F1; color: #123A2E !important; }
    .ur-au-tab.is-active:hover { background: #0F2E24; color: #fff !important; }
    .ur-au-tab.is-active { background: #123A2E; border-color: #123A2E; color: #fff !important; }
    .ur-au-tab.is-active i { color: #E2B66E !important; }

    .ur-au-table { width: 100%; border-collapse: collapse; }
    .ur-au-table th { text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #8A958F; padding: 0 12px 10px; border-bottom: 1px solid #E7E2D6; }
    .ur-au-table td { padding: 13px 12px; border-bottom: 1px solid #F0ECE0; font-size: 13.5px; vertical-align: middle; }
    .ur-au-table tr:last-child td { border-bottom: 0; }
    .ur-au-table tbody tr:hover td { background: #FBF9F3; }

    .ur-au-when b { display: block; font-weight: 700; color: #1C2321; font-size: 13.5px; }
    .ur-au-when span { display: block; font-size: 11.5px; color: #8A958F; margin-top: 2px; }
    .ur-au-actor { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .ur-au-avatar { flex: 0 0 34px; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; font-weight: 700; letter-spacing: .3px; }
    .ur-au-actor b { display: block; font-weight: 700; color: #123A2E; line-height: 1.25; }
    .ur-au-actor small { display: block; font-size: 11.5px; color: #8A958F; }

    .ur-au-badge { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }
    .ur-au-badge i { font-size: 12px; color: inherit !important; }
    .ur-au-badge--blue { background: #E6F0F8; color: #2C5F8A; }
    .ur-au-badge--gold { background: #FBF0DA; color: #8A6218; }
    .ur-au-badge--green { background: #E3F4EA; color: #1E7A46; }
    .ur-au-badge--grey { background: #F0EEE6; color: #5B6660; }
    .ur-au-subject { display: inline-block; font-size: 12px; font-weight: 600; color: #4B5651; background: #F6F4EF; border: 1px solid #E7E2D6; border-radius: 8px; padding: 4px 10px; }
    .ur-au-none { color: #B5BDB8; }

    .ur-au-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
    .ur-au-bar .ur-au-tabs { margin: 0; }
    .ur-au-acts { display: flex; align-items: center; gap: 8px; }
    .ur-au .ur-au-btn { display: inline-flex; align-items: center; gap: 7px; height: 38px; padding: 0 16px !important; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E !important; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .15s ease; }
    .ur-au .ur-au-btn i { color: inherit !important; }
    .ur-au .ur-au-btn:hover { background: #FBF8F1; border-color: #C9974D; }
    .ur-au .ur-au-btn--danger { color: #A33A2B !important; border-color: #EBC9C2; background: #FDF4F2; }
    .ur-au .ur-au-btn--danger:hover { background: #A33A2B; border-color: #A33A2B; color: #fff !important; }
    .ur-au .ur-au-btn[disabled] { opacity: .45; cursor: not-allowed; pointer-events: none; }
    .ur-au-chk { width: 17px; height: 17px; accent-color: #123A2E; cursor: pointer; margin: 0; }
    .ur-au .ur-au-c-chk { width: 44px; }
    .ur-au-rowdel { width: 34px; height: 34px; border-radius: 10px; border: 1px solid #E7E2D6; background: #fff; color: #A33A2B; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; transition: all .15s ease; }
    .ur-au-rowdel:hover { background: #A33A2B; border-color: #A33A2B; color: #fff; }
    .ur-au-rowdel i { color: inherit !important; }
    .ur-au-table tr.is-sel td { background: #FBF4E4 !important; }
    .ur-au-empty { text-align: center; padding: 56px 16px; color: #6B7570; }
    .ur-au-empty i { font-size: 34px; color: #C9974D; margin-bottom: 12px; display: block; }
    .ur-au-empty b { display: block; font-family: 'Playfair Display', Georgia, serif; font-size: 19px; color: #123A2E; margin-bottom: 4px; }

    .ur-au-foot { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 16px; padding-top: 14px; border-top: 1px dashed #E7E2D6; }
    .ur-au-foot__note { font-size: 13px; color: #6B7570; }
    .ur-au-foot .pagination { margin: 0; display: flex; flex-wrap: wrap; gap: 6px; padding: 0; list-style: none; }
    .ur-au-foot .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px !important; border: 1px solid #E7E2D6 !important; background: #fff !important; color: #123A2E !important; font-size: 13px; font-weight: 700; box-shadow: none !important; }
    .ur-au-foot .page-item .page-link:hover { background: #FBF0DA !important; border-color: #C9974D !important; color: #123A2E !important; }
    .ur-au-foot .page-item.active .page-link { background: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
    .ur-au-foot .page-item.disabled .page-link { background: #F6F4EF !important; color: #B8B2A2 !important; }

    @media (max-width: 700px) {
        .ur-au-head { margin-bottom: 14px; gap: 8px; }
        .ur-au-head h1 { font-size: 24px; }
        .ur-au-head p { font-size: 13px; }
        .ur-au-card { padding: 12px; border-radius: 16px; }
        /* filter chips scroll sideways in one line instead of wrapping into a tall block */
        .ur-au-bar { display: block; }
        .ur-au-tabs { flex-wrap: nowrap; overflow-x: auto; margin: 0 -12px 12px !important; padding: 0 12px 4px; scrollbar-width: none; }
        .ur-au-tabs::-webkit-scrollbar { display: none; }
        .ur-au-tab { flex: 0 0 auto; height: 36px; font-size: 12.5px; }
        .ur-au-acts { width: 100%; }
        .ur-au-acts .ur-au-btn { flex: 1; justify-content: center; }
        /* each entry becomes its own card */
        .ur-au-table, .ur-au-table tbody { display: block; width: 100%; }
        .ur-au-table thead { display: none; }
        .ur-au .ur-au-table tbody tr { display: grid; grid-template-columns: 26px minmax(0, 1fr) auto; align-items: center; column-gap: 10px; background: #FBF8F1; border: 1px solid #E3D9C3; border-left: 4px solid #C9974D; border-radius: 12px; padding: 12px !important; margin: 0 0 12px; box-shadow: 0 2px 8px rgba(15,46,36,.05); }
        .ur-au .ur-au-table tbody tr.is-sel { background: #F6ECD4; }
        .ur-au .ur-au-table tbody td { display: block; width: auto !important; padding: 0 !important; border: 0 !important; background: transparent !important; min-width: 0; }
        .ur-au .ur-au-table td.ur-au-c-chk { grid-column: 1; grid-row: 1; width: auto !important; }
        .ur-au .ur-au-table td.ur-au-c-actor { grid-column: 2; grid-row: 1; width: auto !important; }
        .ur-au .ur-au-table td.ur-au-c-del { grid-column: 3; grid-row: 1; margin: 0; }
        .ur-au .ur-au-table td.ur-au-c-action { grid-column: 1 / -1; grid-row: 2; margin-top: 10px; padding-top: 10px !important; border-top: 1px dashed #DCCFB4 !important; }
        .ur-au .ur-au-table td.ur-au-c-subject { grid-column: 1 / -1; grid-row: 3; margin-top: 8px; }
        .ur-au .ur-au-table td.ur-au-c-when { grid-column: 1 / -1; grid-row: 4; margin-top: 10px; padding-top: 10px !important; border-top: 1px dashed #DCCFB4 !important; text-align: left; white-space: normal; }
        .ur-au-when b { display: inline; }
        .ur-au-when span { display: inline; margin-left: 6px; font-size: 11.5px; }
        .ur-au-actor b { font-family: 'Playfair Display', Georgia, serif; font-size: 15px; }
        .ur-au .ur-au-subject { white-space: nowrap; }
        .ur-au-foot { flex-direction: column; align-items: center; text-align: center; }
        .ur-au-foot .pagination { justify-content: center; }
    }
</style>

<div class="ur-au">
    <div class="ur-au-head">
        <div>
            <h1>Audit Log</h1>
            <p>Who did what on the site: profile views, shares, successful matches and sign-ins.</p>
        </div>
        <span class="ur-au-total">{{ number_format($logs->total()) }} {{ $logs->total() == 1 ? 'entry' : 'entries' }}</span>
    </div>

    <div class="ur-au-card">
        <div class="ur-au-bar">
        <div class="ur-au-tabs">
            <a class="ur-au-tab {{ $action === '' ? 'is-active' : '' }}" href="{{ route('admin.audit-log') }}"><i class="fa fa-list"></i> All actions</a>
            @foreach($actionMeta as $code => [$label, $icon, $tone])
                <a class="ur-au-tab {{ $action === $code ? 'is-active' : '' }}" href="{{ route('admin.audit-log', ['action' => $code]) }}"><i class="fa {{ $icon }}"></i> {{ $label }}</a>
            @endforeach
        </div>
        @if($logs->count())
        <div class="ur-au-acts">
            <button type="button" class="ur-au-btn ur-au-btn--danger" id="ur_au_del_sel" disabled><i class="fa fa-trash"></i> <span>Delete selected</span></button>
            <button type="button" class="ur-au-btn" id="ur_au_clear"><i class="fa fa-eraser"></i> {{ $action !== '' ? 'Clear this action' : 'Clear all' }}</button>
        </div>
        @endif
        </div>

        <form method="POST" id="ur_au_form" action="{{ route('admin.audit-log.delete') }}">
            @csrf
            <input type="hidden" name="action" value="{{ $action }}">
        </form>
        <form method="POST" id="ur_au_clear_form" action="{{ route('admin.audit-log.clear') }}">
            @csrf
            <input type="hidden" name="action" value="{{ $action }}">
        </form>

        @if($logs->count())
        <table class="ur-au-table">
            <thead>
                <tr>
                    <th class="ur-au-c-chk"><input type="checkbox" class="ur-au-chk" id="ur_au_all" title="Select all on this page"></th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Subject</th>
                    <th>When</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                        $actor = $log->actor;
                        $teamRef = is_array($log->meta) ? ($log->meta['team_member'] ?? null) : null;
                        $actorName = $actor ? trim($actor->first_name . ' ' . $actor->last_name) : ($teamRef ? 'Team member ' . $teamRef : 'System');
                        $parts = preg_split('/\s+/', $actorName === '' ? '?' : $actorName);
                        $initials = strtoupper(mb_substr($parts[0], 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : mb_substr($parts[0], 1, 1)));
                        $tone = $tones[crc32($actorName) % count($tones)];
                        [$aLabel, $aIcon, $aTone] = $actionMeta[$log->action] ?? [$log->action, 'fa-circle-o', 'grey'];
                    @endphp
                    <tr>
                        <td class="ur-au-c-chk"><input type="checkbox" class="ur-au-chk ur-au-row" name="ids[]" value="{{ $log->id }}" form="ur_au_form"></td>
                        <td class="ur-au-c-actor">
                            <div class="ur-au-actor">
                                <span class="ur-au-avatar" style="background: {{ $tone }};">{{ $initials }}</span>
                                <div>
                                    <b>{{ $actorName === '' ? 'Unknown' : $actorName }}</b>
                                    <small>{{ $actor ? ($actor->dataid ?? 'Member') : ($teamRef ? 'Team dashboard' : 'Automatic') }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="ur-au-c-action"><span class="ur-au-badge ur-au-badge--{{ $aTone }}"><i class="fa {{ $aIcon }}"></i> {{ $aLabel }}</span></td>
                        <td class="ur-au-c-subject">
                            @if($log->subject_type)
                                <span class="ur-au-subject">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                            @else
                                <span class="ur-au-none">—</span>
                            @endif
                        </td>
                        <td class="ur-au-c-when ur-au-when">
                            <div class="ur-au-when">
                                <b>{{ $log->created_at->format('d M Y') }}</b>
                                <span>{{ $log->created_at->format('H:i') }} &middot; {{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td class="ur-au-c-del"><button type="button" class="ur-au-rowdel" title="Delete this entry" data-id="{{ $log->id }}"><i class="fa fa-trash"></i></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div class="ur-au-empty">
                <i class="fa fa-history"></i>
                <b>No entries found</b>
                {{ $action !== '' ? 'Nothing has been recorded for this action yet.' : 'No audit log entries yet.' }}
            </div>
        @endif

        @if($logs->count())
        <div class="ur-au-foot">
            <div class="ur-au-foot__note">Showing {{ $logs->firstItem() }}&ndash;{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries</div>
            @if($logs->hasPages()){{ $logs->links() }}@endif
        </div>
        @endif
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('ur_au_form'), clearForm = document.getElementById('ur_au_clear_form');
    if (!form) return;
    var all = document.getElementById('ur_au_all'), delBtn = document.getElementById('ur_au_del_sel'), clearBtn = document.getElementById('ur_au_clear');
    var rows = function () { return Array.prototype.slice.call(document.querySelectorAll('.ur-au-row')); };

    function ask(title, text, yes, go) {
        if (typeof swal === 'function') {
            swal({ title: title, text: text, icon: 'warning', buttons: { cancel: 'Cancel', confirm: yes }, dangerMode: true }).then(function (ok) { if (ok) go(); });
        } else if (confirm(title + '\n\n' + text)) { go(); }
    }
    function sync() {
        var n = rows().filter(function (c) { return c.checked; }).length;
        if (delBtn) { delBtn.disabled = n === 0; delBtn.querySelector('span').textContent = n ? 'Delete selected (' + n + ')' : 'Delete selected'; }
        if (all) { all.checked = n > 0 && n === rows().length; all.indeterminate = n > 0 && n < rows().length; }
        rows().forEach(function (c) { c.closest('tr').classList.toggle('is-sel', c.checked); });
    }
    rows().forEach(function (c) { c.addEventListener('change', sync); });
    if (all) all.addEventListener('change', function () { rows().forEach(function (c) { c.checked = all.checked; }); sync(); });

    if (delBtn) delBtn.addEventListener('click', function () {
        var n = rows().filter(function (c) { return c.checked; }).length;
        if (!n) return;
        ask('Delete ' + n + ' ' + (n === 1 ? 'entry' : 'entries') + '?', 'This cannot be undone.', 'Yes, delete', function () { form.submit(); });
    });
    document.querySelectorAll('.ur-au-rowdel').forEach(function (b) {
        b.addEventListener('click', function () {
            ask('Delete this entry?', 'This cannot be undone.', 'Yes, delete', function () {
                rows().forEach(function (c) { c.checked = c.value === b.getAttribute('data-id'); });
                form.submit();
            });
        });
    });
    if (clearBtn) clearBtn.addEventListener('click', function () {
        var filtered = {{ $action !== '' ? 'true' : 'false' }};
        ask(filtered ? 'Clear every entry of this action?' : 'Clear the whole audit log?', filtered ? 'All entries for this action (on every page) will be deleted. This cannot be undone.' : 'Every audit log entry (on every page) will be deleted. This cannot be undone.', 'Yes, clear', function () { clearForm.submit(); });
    });
    sync();
})();
</script>
@endsection
