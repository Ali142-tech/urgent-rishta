@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Appointments</h2>
            </div>
        </div>
    </div>
</section>
<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="row">
                <div class="col-lg-12">
                    <div class="block-wrapper">
                        <div class="row">
                            <form id="controls-form" action="javascript:void();">
                                @csrf
                                <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
                                <div class="col col-sm-12 col-md-12">
                                    <span><label for="pagesize">Number of entries:
                                        <select name="pagesize" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshAppointments(true);">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label></span>
                                    <span><label for="term">Search:
                                        <input type="search" name="term" class="form-control form-control-sm" placeholder="Search by Member ID, name, email..." autocomplete="off" onkeyup="javascript:refreshAppointments(true);" value="" />
                                    </label></span>
                                    <span><label for="status">Status:
                                        <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose status" data-hide-disabled="true" onchange="javascript:refreshAppointments(true);">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </label></span>
                                </div>
                            </form>
                        </div>
                        <div id="appointments-data">
                            @yield('appointments-data')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function refreshAppointments(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage ? newPage : 1);
        renderPage("{{ url('admin/appointments/refresh') }}", "post", $("#controls-form").serialize(), $("#appointments-data"));
    }
</script>
@endsection

