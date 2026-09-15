@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Profile Completion Campaign</h2>
</div>

<div class="ur-admin-panel">
    <p>
        Sends the "Complete Your Profile" email to every active member with an email address.
        Each member only ever receives this campaign once — re-running "Start" only dispatches to
        members not yet sent/queued/failed.
    </p>

    <table class="table table-bordered" style="max-width:900px;">
        <thead>
            <tr>
                <th>Total eligible audience</th>
                <th>Sent</th>
                <th>Queued (in progress)</th>
                <th>Failed</th>
                <th>Not yet dispatched</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $stats['total_audience'] }}</td>
                <td>{{ $stats['sent'] }}</td>
                <td>{{ $stats['queued'] }}</td>
                <td>{{ $stats['failed'] }}</td>
                <td>{{ $stats['not_yet_dispatched'] }}</td>
                <td>
                    @if($stats['paused'])
                        <span class="badge badge-warning">Paused</span>
                    @else
                        <span class="badge badge-success">Running</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <p class="text-muted">
        This page only <em>dispatches</em> emails — actual sending happens via the queue worker at the rate
        configured on the server. Refresh this page to see updated counts.
    </p>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">
        <form method="POST" action="{{ route('admin.campaigns.profile-completion.start') }}"
              onsubmit="return confirm('This will queue {{ $stats['not_yet_dispatched'] }} emails for sending. Continue?');">
            @csrf
            <div class="form-group" style="margin-bottom:8px;">
                <label for="limit" style="font-size:12px;">Limit (optional &mdash; leave blank to dispatch all {{ $stats['not_yet_dispatched'] }} remaining)</label>
                <input type="number" name="limit" id="limit" class="form-control form-control-sm" placeholder="e.g. 300 for a first batch" min="1">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Start / Dispatch Batch</button>
        </form>

        <form method="POST" action="{{ route('admin.campaigns.profile-completion.retry-failed') }}"
              onsubmit="return confirm('Re-queue all {{ $stats['failed'] }} failed emails for another attempt?');">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm" {{ $stats['failed'] === 0 ? 'disabled' : '' }}>
                Retry Failed ({{ $stats['failed'] }})
            </button>
        </form>

        @if($stats['paused'])
            <form method="POST" action="{{ route('admin.campaigns.profile-completion.resume') }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Resume Sending</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.campaigns.profile-completion.pause') }}"
                  onsubmit="return confirm('Pause sending? Already-queued emails will hold instead of sending until resumed.');">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">Pause Sending</button>
            </form>
        @endif

        <a href="{{ route('admin.campaigns.profile-completion') }}" class="btn btn-link btn-sm">Refresh</a>
    </div>
</div>
@endsection
