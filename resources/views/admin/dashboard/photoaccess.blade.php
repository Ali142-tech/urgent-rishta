@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Photo Access Requests</h2>
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
                                    <span><label for="pagesize">Number of entries: <select name="pagesize" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshPhotoAccessRequests(true);">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select></label></span>
                                    <span><label for="term">Search: <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshPhotoAccessRequests(true);" value="" /></label></span>
                                    <span><label for="status">Filter Status: </label>
                                        <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose a status" data-hide-disabled="true" onchange="javascript:refreshPhotoAccessRequests(true);">
                                            <option value="">Select status...</option>
                                            <option value="1">Granted</option>
                                            <option value="0">Pending</option>
                                            <option value="-1">Declined</option>
                                        </select>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <div id="photoaccess-data">
                        @yield('photoaccess-data')
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
</section>
<script type="text/javascript">
    function refreshPhotoAccessRequests(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/photoaccess/refresh')}}", "post", $("#controls-form").serialize(), $("#photoaccess-data"));
    }
</script>
@endsection
