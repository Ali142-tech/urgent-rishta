@extends('admin.dashboard.appointments')
@section('appointments-data')
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mb-3">
    @if($currentPage != 1)
        <li>
            <a onclick="javascript:refreshAppointments(true, {{ $currentPage - 1 }});">Previous</a>
        </li>
    @endif

    @for ($i = ($currentPage-3>1 ? $currentPage-3 : 1); $i <= ($currentPage+4 <= $numPages ? $currentPage+4 : $numPages); $i++)
        <li class="{{ $i == $currentPage ? 'active' : '' }}">
            <a {{ $currentPage == $i ? 'disabled' : '' }} onclick="javascript:refreshAppointments(true, {{ $i }});">{{ $i }}</a>
        </li>
    @endfor

    @if($currentPage < $numPages)
        <li>
            <a onclick="javascript:refreshAppointments(true, {{ $currentPage + 1 }});">Next</a>
        </li>
    @endif
</ul>
@endif
<div class="ur-admin-summary">@if(!empty($pageSize)) Showing
    <span>{{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}</span>
    to <span>{{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}</span>
    @if (isset($resultCount)) from <span>{{ $resultCount }}</span> filtered appointments @endif
    out of <span>{{ $total }}</span> total appointments @endif</div>

@if(!empty($appointments) && sizeof($appointments) > 0)
    <div class="ur-admin-table-wrap">
        <table class="ur-admin-table">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $apt)
                    <tr>
                        <td><a href="{{ url('/member/profile/'.$apt->member_id) }}" target="_blank" class="c-base-1">{{ $apt->member_id }}</a></td>
                        <td>{{ $apt->member_name }}</td>
                        <td><a href="mailto:{{ $apt->member_email }}">{{ $apt->member_email }}</a></td>
                        <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d/m/Y') }}</td>
                        <td>{{ $apt->appointment_time ?? '—' }}</td>
                        <td>{{ $apt->subject ?: '—' }}</td>
                        <td>
                            @if($apt->status === 'pending')
                                <span class="ur-admin-badge ur-admin-badge--warning">Pending</span>
                            @elseif($apt->status === 'confirmed')
                                <span class="ur-admin-badge ur-admin-badge--success">Confirmed</span>
                            @elseif($apt->status === 'cancelled')
                                <span class="ur-admin-badge ur-admin-badge--neutral">Cancelled</span>
                            @else
                                <span class="ur-admin-badge ur-admin-badge--info">{{ ucfirst($apt->status) }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($apt->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ur-admin-empty"><i class="fa fa-calendar"></i> No appointments found.</div>
@endif
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mt-3">
    @if($currentPage != 1)
        <li>
            <a onclick="javascript:refreshAppointments(true, {{ $currentPage - 1 }});">Previous</a>
        </li>
    @endif

    @for ($i = ($currentPage-3>1 ? $currentPage-3 : 1); $i <= ($currentPage+4 <= $numPages ? $currentPage+4 : $numPages); $i++)
        <li class="{{ $i == $currentPage ? 'active' : '' }}">
            <a {{ $currentPage == $i ? 'disabled' : '' }} onclick="javascript:refreshAppointments(true, {{ $i }});">{{ $i }}</a>
        </li>
    @endfor

    @if($currentPage < $numPages)
        <li>
            <a onclick="javascript:refreshAppointments(true, {{ $currentPage + 1 }});">Next</a>
        </li>
    @endif
</ul>
@endif
@endsection
