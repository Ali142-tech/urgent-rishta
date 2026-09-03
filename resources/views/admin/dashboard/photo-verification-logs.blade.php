@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <div class="ur-admin-back">
        <a href="{{ url('admin/photo-verification') }}"><i class="fa fa-angle-left"></i> Back to Photo Verification</a>
    </div>
    <h2>Photo Verification History</h2>
    <p>Every approve / reject / reopen decision, who made it, and when — the users table only ever keeps the latest state, this is the permanent record.</p>
</div>

<div class="ur-admin-panel">
    <form method="get" action="{{ url('admin/photo-verification/logs') }}" class="ur-admin-filters">
        <div class="form-group form-group--grow">
            <label for="dataid">Search by Member ID or Admin ID</label>
            <input type="search" id="dataid" name="dataid" class="form-control form-control-sm" placeholder="e.g. QT853J5X7" value="{{ $search }}" />
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-base-1 btn-sm btn-block">Search</button>
        </div>
        @if($search !== '')
        <div class="form-group">
            <label>&nbsp;</label>
            <a href="{{ url('admin/photo-verification/logs') }}" class="btn btn-outline-secondary btn-sm btn-block">Clear</a>
        </div>
        @endif
    </form>

    @if($logs->isEmpty())
        <div class="ur-admin-empty">
            <i class="fa fa-id-badge"></i>
            {{ $search !== '' ? 'No decisions found for "' . $search . '".' : 'No verification decisions have been made yet.' }}
        </div>
    @else
        <div class="ur-admin-table-wrap">
            <table class="ur-admin-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Member</th>
                        <th>Decided By (Admin)</th>
                        <th>Reason</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                @if($log->action === 'approved')
                                    <span class="ur-admin-badge ur-admin-badge--success">Approved</span>
                                @elseif($log->action === 'rejected')
                                    <span class="ur-admin-badge ur-admin-badge--neutral" style="background:rgba(181,103,74,.1); color:#B5674A;">Rejected</span>
                                @else
                                    <span class="ur-admin-badge ur-admin-badge--warning">Reopened</span>
                                @endif
                            </td>
                            <td><a href="{{ url('/member/profile/'.$log->user_dataid) }}" target="_blank" class="c-base-1">{{ $log->user_dataid }}</a></td>
                            <td>{{ $log->admin_dataid ?: '—' }}</td>
                            <td>{{ $log->reason ?: '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:10px;">
            {{ $logs->appends(['dataid' => $search])->links() }}
        </div>
    @endif
</div>
@endsection
