@extends('admin.dashboard.appointments')
@section('appointments-data')
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mb-3">
    @if($currentPage != 1)
        <li><a onclick="javascript:refreshAppointments(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif
    @for ($i = ($currentPage-3>1 ? $currentPage-3 : 1); $i <= ($currentPage+4 <= $numPages ? $currentPage+4 : $numPages); $i++)
        <li class="{{ $i == $currentPage ? 'active' : '' }}">
            <a {{ $currentPage == $i ? 'disabled' : '' }} onclick="javascript:refreshAppointments(true, {{ $i }});">{{ $i }}</a>
        </li>
    @endfor
    @if($currentPage < $numPages)
        <li><a onclick="javascript:refreshAppointments(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
<div class="ur-admin-summary">@if(!empty($pageSize)) Showing
    <span>{{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}</span>
    to <span>{{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}</span>
    @if (isset($resultCount)) from <span>{{ $resultCount }}</span> filtered requests @endif
    out of <span>{{ $total }}</span> total requests @endif</div>

@if(!empty($appointments) && sizeof($appointments) > 0)
    @foreach($appointments as $apt)
        @php
            $initials = strtoupper(substr(trim($apt->member_name ?: 'G'), 0, 1));
            $statusClass = match($apt->status) {
                'pending' => 'ur-admin-badge--warning',
                'confirmed' => 'ur-admin-badge--success',
                'completed' => 'ur-admin-badge--info',
                'cancelled' => 'ur-admin-badge--neutral',
                default => 'ur-admin-badge--neutral',
            };
        @endphp
        <div class="ur-apt-row {{ isset($selectedAppointment) && $selectedAppointment && $selectedAppointment->id == $apt->id ? 'is-active' : '' }}" onclick="loadAppointmentDetail('{{ $apt->id }}', this);">
            <span class="ur-apt-row__thumb">{{ $initials }}</span>
            <div class="ur-apt-row__body">
                <div class="ur-apt-row__name">{{ $apt->member_name ?: 'Guest' }}</div>
                <div class="ur-apt-row__sub"><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($apt->appointment_date)->format('d M') }} &bull; {{ $apt->appointment_time ?? '—' }}</div>
            </div>
            <div class="ur-apt-row__reason">{{ $apt->subject ?: '—' }}</div>
            <span class="ur-apt-row__status ur-admin-badge {{ $statusClass }}">{{ ucfirst($apt->status) }}</span>
        </div>
    @endforeach
@else
    <div class="ur-admin-empty"><i class="fa fa-calendar"></i> No appointments found.</div>
@endif

@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mt-3">
    @if($currentPage != 1)
        <li><a onclick="javascript:refreshAppointments(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif
    @for ($i = ($currentPage-3>1 ? $currentPage-3 : 1); $i <= ($currentPage+4 <= $numPages ? $currentPage+4 : $numPages); $i++)
        <li class="{{ $i == $currentPage ? 'active' : '' }}">
            <a {{ $currentPage == $i ? 'disabled' : '' }} onclick="javascript:refreshAppointments(true, {{ $i }});">{{ $i }}</a>
        </li>
    @endfor
    @if($currentPage < $numPages)
        <li><a onclick="javascript:refreshAppointments(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
@endsection
