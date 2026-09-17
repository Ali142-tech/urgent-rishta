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
                    <th>Contact</th>
                    <th>Requested Date/Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Reschedule &amp; Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $apt)
                    <tr>
                        <td>
                            @if($apt->member_id)
                                <a href="{{ url('/member/profile/'.$apt->member_id) }}" target="_blank" class="c-base-1">{{ $apt->member_id }}</a>
                            @else
                                <span class="ur-admin-badge ur-admin-badge--neutral">Guest</span>
                            @endif
                        </td>
                        <td>{{ $apt->member_name ?: '—' }}</td>
                        <td>
                            @if($apt->member_email)<a href="mailto:{{ $apt->member_email }}">{{ $apt->member_email }}</a><br>@endif
                            @if($apt->member_phone)<a href="https://wa.me/{{ preg_replace('/\D/', '', $apt->member_phone) }}" target="_blank">{{ $apt->member_phone }}</a>@endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d/m/Y') }} &bull; {{ $apt->appointment_time ?? '—' }}</td>
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
                        <td>
                            <form class="ur-admin-appointment-form" onsubmit="return updateAppointment(event, {{ $apt->id }});">
                                <input type="date" name="appointment_date" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d') }}">
                                <input type="text" name="appointment_time" class="form-control form-control-sm" value="{{ $apt->appointment_time }}" placeholder="Time">
                                <select name="status" class="form-control form-control-sm">
                                    <option value="pending" {{ $apt->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $apt->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ $apt->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ $apt->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="ur-btn ur-btn--dark">Save</button>
                            </form>
                        </td>
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
