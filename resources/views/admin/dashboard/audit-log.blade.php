{{--
    Website Upgrade Brief §16 — queryable audit trail (see App\AuditLog).
    Every Log::info(...) call elsewhere in this app only reaches
    storage/logs/laravel.log; this is the one admin-visible history.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Audit Log')
@section('main-content')
<style>
    .ur-audit-filter { margin-bottom: 16px; display: flex; gap: 10px; }
    .ur-audit-filter select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; }
    .ur-audit-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
    .ur-audit-table th, .ur-audit-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #E7E2D6; font-size: 13px; }
    .ur-audit-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; }
</style>

<form method="GET" class="ur-audit-filter">
    <select name="action" onchange="this.form.submit()">
        <option value="">All actions</option>
        @foreach(['profile.viewed','profile.shared','match.marked_successful','login'] as $a)
            <option value="{{ $a }}" {{ $action === $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
    </select>
</form>

<table class="ur-audit-table">
    <thead>
        <tr>
            <th>When</th>
            <th>Actor</th>
            <th>Action</th>
            <th>Subject</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ optional($log->actor)->first_name }} {{ optional($log->actor)->last_name }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4">No audit log entries yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:20px;">{{ $logs->links() }}</div>
@endsection
