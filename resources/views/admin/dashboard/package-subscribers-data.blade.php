<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Admin package</th>
                <th>Online package</th>
                <th>Online expires</th>
                <th>Profile</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $s)
            <tr>
                <td><a href="{{ url('/member/profile/'.$s->dataid) }}" target="_blank" class="c-base-1">{{ $s->dataid }}</a></td>
                <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                <td><a href="mailto:{{ $s->email }}">{{ $s->email }}</a></td>
                <td>{{ $s->contact_mobile_number ?? '—' }}</td>
                <td>
                    @if(!empty($s->admin_package_name))
                        <span class="badge badge-secondary">{{ $s->admin_package_name }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if(!empty($s->online_package_name))
                        <span class="badge badge-info">{{ $s->online_package_name }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if(!empty($s->online_package_expires_at))
                        {{ \Carbon\Carbon::parse($s->online_package_expires_at)->format('j M Y') }}
                    @else
                        —
                    @endif
                </td>
                <td><a href="{{ url('/member/profile/'.$s->dataid) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">No subscribers match your filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($numPages > 1)
<div class="row mt-2">
    <div class="col-md-6">
        <ul class="pagination pagination-sm mb-0">
            @if($currentPage > 1)
                <li class="page-item"><a class="page-link" href="javascript:refreshPackageSubscribers(true, {{ $currentPage - 1 }});">Previous</a></li>
            @endif
            @for($i = max(1, $currentPage - 2); $i <= min($numPages, $currentPage + 2); $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a class="page-link" href="javascript:refreshPackageSubscribers(true, {{ $i }});">{{ $i }}</a></li>
            @endfor
            @if($currentPage < $numPages)
                <li class="page-item"><a class="page-link" href="javascript:refreshPackageSubscribers(true, {{ $currentPage + 1 }});">Next</a></li>
            @endif
        </ul>
    </div>
    <div class="col-md-6 text-right text-muted small">
        @php $n = $subscribers->count(); $start = $n ? (($currentPage - 1) * $pageSize + 1) : 0; $end = $n ? (($currentPage - 1) * $pageSize + $n) : 0; @endphp
        Showing {{ $start }}–{{ $end }} of {{ $total }}
    </div>
</div>
@endif
