@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Appointments</h2>
</div>

<div class="ur-admin-panel">
    <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
        @csrf
        <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>

        <div class="form-group form-group--tight">
            <label for="pagesize">Number of entries</label>
            <select name="pagesize" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshAppointments(true);">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="form-group form-group--grow">
            <label for="term">Search</label>
            <input type="search" name="term" class="form-control form-control-sm" placeholder="Search by Member ID, name, email..." autocomplete="off" onkeyup="javascript:refreshAppointments(true);" value="" />
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose status" data-hide-disabled="true" onchange="javascript:refreshAppointments(true);">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </form>

    <div id="appointments-data">
        @yield('appointments-data')
    </div>
</div>
<script type="text/javascript">
    function refreshAppointments(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage ? newPage : 1);
        renderPage("{{ url('admin/appointments/refresh') }}", "post", $("#controls-form").serialize(), $("#appointments-data"));
    }
</script>
@endsection

