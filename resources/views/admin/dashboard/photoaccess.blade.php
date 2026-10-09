@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Photo Access Requests</h2>
</div>

<div class="ur-admin-panel">
    <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
        @csrf
        <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>

        <div class="form-group form-group--tight">
            <label for="pagesize">Entries</label>
            <select name="pagesize" class="form-control form-control-sm" onchange="javascript:refreshPhotoAccessRequests(true);">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="form-group form-group--grow">
            <label for="term">Search</label>
            <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshPhotoAccessRequests(true);" value="" />
        </div>
        <div class="form-group">
            <label for="status">Filter Status</label>
            <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose a status" data-hide-disabled="true" onchange="javascript:refreshPhotoAccessRequests(true);">
                <option value="">Select status...</option>
                <option value="1">Granted</option>
                <option value="0">Pending</option>
                <option value="-1">Declined</option>
            </select>
        </div>
    </form>

    <div id="photoaccess-data">
    @yield('photoaccess-data')
    </div>
</div>
<script type="text/javascript">
    function refreshPhotoAccessRequests(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/photoaccess/refresh')}}", "post", $("#controls-form").serialize(), $("#photoaccess-data"));
    }
</script>
@endsection
