@extends('layouts.admin.master')
@section('dashboard-title', 'Deleted Profiles')
@section('admin-content')
<style>
    /* the gold Pace.js page bar also fires on every live-search request; this page has its own "Searching..." loader */
    .pace, .pace .pace-progress { display: none !important; }
</style>
<div class="ur-admin-header">
    <h2>Deleted Profiles</h2>
    <p>Profiles removed via "Delete Profile" — restoring brings back the account and all its data (photos, interests), but leaves it inactive until you activate it again. Permanently deleting cannot be undone.</p>
</div>

<div class="ur-admin-panel">
    <form method="GET" action="{{ url('admin/profiles/deleted') }}" class="ur-admin-filters js-del-search">
        <div class="form-group form-group--grow">
            <label for="search">Search by name, email or member ID</label>
            <input type="search" name="search" id="search" class="form-control form-control-sm" placeholder="Name, member@example.com or member ID" value="{{ $search }}" autocomplete="off" />
        </div>
        <div class="form-group" style="align-self:flex-end;">
            <button type="submit" class="ur-btn ur-btn--dark">Search</button>
            <a href="{{ url('admin/profiles/deleted') }}" class="ur-btn ur-btn--outline js-del-clear" @if($search === '') style="display:none;" @endif>Clear</a>
        </div>
    </form>

    <div id="del-results">
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
</div>

<script type="text/javascript">
    // Live search: the list updates as you type (no need to press Search); results are fetched from the same page and swapped in.
    (function () {
        var form = document.querySelector('.js-del-search');
        if (!form) return;
        var input = form.querySelector('input[name=search]');
        var clearBtn = form.querySelector('.js-del-clear');
        var base = "{{ url('admin/profiles/deleted') }}";
        var timer = null, xhr = null, last = input.value.trim();

        function run() {
            var term = input.value.trim().replace(/\s+/g, ' ');
            var url = term ? base + '?search=' + encodeURIComponent(term) : base;
            var box = document.getElementById('del-results');
            if (clearBtn) clearBtn.style.display = term ? '' : 'none';
            last = term;
            box.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-spinner fa-spin"></i> Searching deleted profiles...</div>';
            if (xhr) xhr.abort();
            xhr = new XMLHttpRequest();
            xhr.open('GET', url);
            xhr.onload = function () {
                if (xhr.status !== 200) return fail();
                var fresh = new DOMParser().parseFromString(xhr.responseText, 'text/html').getElementById('del-results');
                if (!fresh) return fail();
                box.innerHTML = fresh.innerHTML;
                window.history.replaceState(null, '', url);
            };
            xhr.onerror = fail;
            xhr.send();
        }
        function fail() {
            document.getElementById('del-results').innerHTML = '<div class="ur-admin-empty"><i class="fa fa-exclamation-triangle"></i> Could not load the profiles. Please try again.</div>';
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { if (input.value.trim() !== last) run(); }, 350);
        });
        form.addEventListener('submit', function (e) { e.preventDefault(); clearTimeout(timer); run(); });
        if (clearBtn) clearBtn.addEventListener('click', function (e) { e.preventDefault(); input.value = ''; clearTimeout(timer); run(); input.focus(); });
    })();

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
