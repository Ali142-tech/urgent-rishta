<div class="ur-admin-table-wrap">
    <table class="ur-admin-table">
        <thead>
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
                        <span class="ur-admin-badge ur-admin-badge--neutral">{{ $s->admin_package_name }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if(!empty($s->online_package_name))
                        <span class="ur-admin-badge ur-admin-badge--info">{{ $s->online_package_name }}</span>
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
                <td><a href="{{ url('/member/profile/'.$s->dataid) }}" target="_blank" class="ur-admin-btn ur-admin-btn--outline ur-admin-btn--sm">View</a></td>
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
        <ul class="ur-admin-pagination">
            @if($currentPage > 1)
                <li><a href="javascript:refreshPackageSubscribers(true, {{ $currentPage - 1 }});">Previous</a></li>
            @endif
            @for($i = max(1, $currentPage - 2); $i <= min($numPages, $currentPage + 2); $i++)
                <li class="{{ $i == $currentPage ? 'active' : '' }}"><a href="javascript:refreshPackageSubscribers(true, {{ $i }});">{{ $i }}</a></li>
            @endfor
            @if($currentPage < $numPages)
                <li><a href="javascript:refreshPackageSubscribers(true, {{ $currentPage + 1 }});">Next</a></li>
            @endif
        </ul>
    </div>
    <div class="col-md-6 text-right">
        @php $n = $subscribers->count(); $start = $n ? (($currentPage - 1) * $pageSize + 1) : 0; $end = $n ? (($currentPage - 1) * $pageSize + $n) : 0; @endphp
        <div class="ur-admin-summary">Showing {{ $start }}–{{ $end }} of {{ $total }}</div>
    </div>
</div>
@endif
