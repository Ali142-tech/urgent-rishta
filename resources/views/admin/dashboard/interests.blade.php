@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Member Interests</h2>
</div>

<div class="ur-admin-panel">
    <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
        @csrf
        <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>

        <div class="form-group form-group--tight">
            <label for="pagesize">Entries</label>
            <select name="pagesize" class="form-control form-control-sm" onchange="javascript:refreshInterests(true);">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="form-group form-group--grow">
            <label for="term">Search</label>
            <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshInterests(true);" value="" />
        </div>
        <div class="form-group">
            <label for="status">Filter Status</label>
            <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose a status" data-hide-disabled="true" onchange="javascript:refreshInterests(true);">
                <option value="">Select status...</option>
                <option value="1">Accepted</option>
                <option value="0">Pending</option>
                <option value="-1">Declined</option>
            </select>
        </div>
    </form>

    <div id="interest-data">
    @yield('interest-data')
    </div>
</div>
<script type="text/javascript">
    function refreshInterests(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/interests/refresh')}}", "post", $("#controls-form").serialize(), $("#interest-data"));
    }
</script>
@endsection
