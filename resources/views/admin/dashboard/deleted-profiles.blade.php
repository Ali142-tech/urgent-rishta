@extends('layouts.admin.master')
@section('dashboard-title', 'Deleted Profiles')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Deleted Profiles</h2>
    <p>Profiles removed via "Delete Profile" — restoring brings back the account and all its data (photos, interests), but leaves it inactive until you activate it again. Permanently deleting cannot be undone.</p>
</div>

<div class="ur-admin-panel">
    <form method="GET" action="{{ url('admin/profiles/deleted') }}" class="ur-admin-filters">
        <div class="form-group form-group--grow">
            <label for="search">Search by email</label>
            <input type="search" name="search" id="search" class="form-control form-control-sm" placeholder="member@example.com" value="{{ $search }}" autocomplete="off" />
        </div>
        <div class="form-group" style="align-self:flex-end;">
            <button type="submit" class="ur-btn ur-btn--dark">Search</button>
            @if($search !== '')
            <a href="{{ url('admin/profiles/deleted') }}" class="ur-btn ur-btn--outline">Clear</a>
            @endif
        </div>
    </form>

    <div class="ur-admin-summary">Showing <span>{{ $total === 0 ? 0 : ($currentPage - 1) * $pageSize + 1 }}</span>
        to <span>{{ min($currentPage * $pageSize, $total) }}</span> out of <span>{{ $total }}</span> deleted profiles</div>

    @if($members->count() > 0)
    <div class="ur-admin-table-wrap">
        <table class="ur-admin-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Member ID</th>
                    <th>Deleted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr id="deleted_{{ $member->dataid }}">
                    <td><span class="ur-admin-table__thumb" style="background-image:url('{{ \App\Profile::defaultImage($member->gender) }}')"></span></td>
                    <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->dataid }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($member->deleted_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="ur-admin-table__actions">
                            <a class="ur-btn ur-btn--dark" onclick="return restoreProfile('{{ $member->dataid }}');">
                                <i class="fa fa-undo"></i> Restore
                            </a>
                            <a class="ur-btn ur-btn--danger-outline" onclick="return permanentlyDeleteProfile('{{ $member->dataid }}');">
                                <i class="fa fa-trash"></i> Delete Permanently
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

        @if($numPages > 1)
        <ul class="ur-admin-pagination mt-3">
            @if($currentPage != 1)
            <li><a href="?page={{ $currentPage - 1 }}{{ $search !== '' ? '&search='.urlencode($search) : '' }}">Previous</a></li>
            @endif
            @for ($i = 1; $i <= $numPages; $i++)
            <li class="{{ $i == $currentPage ? 'active' : '' }}"><a href="?page={{ $i }}{{ $search !== '' ? '&search='.urlencode($search) : '' }}">{{ $i }}</a></li>
            @endfor
            @if($currentPage < $numPages)
            <li><a href="?page={{ $currentPage + 1 }}{{ $search !== '' ? '&search='.urlencode($search) : '' }}">Next</a></li>
            @endif
        </ul>
        @endif
    @else
        <div class="ur-admin-empty"><i class="fa fa-trash"></i> No deleted profiles{{ $search !== '' ? ' matching "'.$search.'"' : '' }}.</div>
    @endif
</div>

<script type="text/javascript">
    function restoreProfile(dataid) {
        swalConfirm("Restore Profile?", "This brings back the account and all its data. It will stay inactive until you activate it.", () => {
            $.ajax({
                type: "post",
                url: "{{ url('admin/profile') }}/" + dataid + "/restore",
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') {
                        showAlert('success', result.message, 4000);
                        $("#deleted_" + dataid).fadeOut(200, function () { $(this).remove(); });
                    } else {
                        showAlert('danger', result.message, 5000);
                    }
                }
            });
        });
        return false;
    }

    function permanentlyDeleteProfile(dataid) {
        swalConfirm("Permanently Delete?", "This CANNOT be undone — the account, photos, and interests will be gone for good. Are you absolutely sure?", () => {
            $.ajax({
                type: "delete",
                url: "{{ url('admin/profile') }}/" + dataid + "/permanent",
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') {
                        showAlert('success', result.message, 4000);
                        $("#deleted_" + dataid).fadeOut(200, function () { $(this).remove(); });
                    } else {
                        showAlert('danger', result.message, 5000);
                    }
                }
            });
        });
        return false;
    }
</script>
@endsection
