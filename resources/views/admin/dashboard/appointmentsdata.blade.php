@extends('admin.dashboard.appointments')
@section('appointments-data')
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage != 1)
                <li class="paginate_button page-item previous">
                    <a onclick="javascript:refreshAppointments(true, {{ $currentPage - 1 }});" class="page-link">Previous</a>
                </li>
            @endif

            @for ($i = ($currentPage-3>1 ? $currentPage-3 : 1); $i <= ($currentPage+4 <= $numPages ? $currentPage+4 : $numPages); $i++)
                <li class="paginate_button page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a {{ $currentPage == $i ? 'disabled' : '' }} onclick="javascript:refreshAppointments(true, {{ $i }});" class="page-link">{{ $i }}</a>
                </li>
            @endfor

            @if($currentPage < $numPages)
                <li class="paginate_button page-item next">
                    <a onclick="javascript:refreshAppointments(true, {{ $currentPage + 1 }});" class="page-link">Next</a>
                </li>
            @endif
        </ul>
    </div>
</div>
<div class="block-footer b-xs-top" style="margin: 20px 0;">@if(!empty($pageSize)) Showing
    {{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}
    to {{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}
    <span>@if (isset($resultCount)) from {{ $resultCount }} filtered appointments @endif</span>
    out of {{ $total }} total appointments @endif</div>

@if(!empty($appointments) && sizeof($appointments) > 0)
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover mb-0" style="font-size: 12px;">
            <thead class="thead-light">
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
                                <span class="badge badge-warning">Pending</span>
                            @elseif($apt->status === 'confirmed')
                                <span class="badge badge-success">Confirmed</span>
                            @elseif($apt->status === 'cancelled')
                                <span class="badge badge-secondary">Cancelled</span>
                            @else
                                <span class="badge badge-info">{{ ucfirst($apt->status) }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($apt->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info mt-3 mb-0">No appointments found.</div>
@endif
@endsection

