@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Package Subscribers</h2>
                <p class="mt-2 text-muted">Users with an admin-assigned package or an online subscription</p>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="block-wrapper">
                    <form id="controls-form" action="javascript:void(0);">
                        @csrf
                        <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
                        <div class="row mb-3">
                            <div class="col-md-3 col-sm-6">
                                <label for="term">Search</label>
                                <input type="search" name="term" class="form-control form-control-sm" placeholder="Name, email, Member ID, mobile..." autocomplete="off" value="" />
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="pagesize">Per page</label>
                                <select name="pagesize" class="form-control form-control-sm" onchange="refreshPackageSubscribers(true);">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="package_type">Package type</label>
                                <select name="package_type" id="package_type" class="form-control form-control-sm" onchange="togglePackageFilter(); refreshPackageSubscribers(true);">
                                    <option value="">All</option>
                                    <option value="admin_only">Admin / Offline only</option>
                                    <option value="online_only">Online only</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6" id="wrap_specific_package">
                                <label for="package">Specific package</label>
                                <select name="package" id="package_select" class="form-control form-control-sm" onchange="refreshPackageSubscribers(true);">
                                    <option value="">Any</option>
                                    <optgroup label="Admin packages" id="opt_admin">
                                        @foreach($adminPackages as $p)
                                            <option value="{{ $p->dataid }}" data-filter-type="admin">{{ $p->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Online packages" id="opt_online">
                                        @foreach($onlinePackages as $p)
                                            <option value="{{ $p->dataid }}" data-filter-type="online">{{ $p->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <input type="hidden" name="package_filter_type" id="package_filter_type" value="admin" />
                            </div>
                            <div class="col-md-2 col-sm-6 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-base-1" onclick="refreshPackageSubscribers(true);">Apply</button>
                            </div>
                        </div>
                    </form>

                    <div id="package-subscribers-data">
                        @include('admin.dashboard.package-subscribers-data', [
                            'subscribers' => $subscribers,
                            'currentPage' => $currentPage,
                            'numPages' => $numPages,
                            'total' => $total,
                            'pageSize' => $pageSize,
                            'adminPackages' => $adminPackages,
                            'onlinePackages' => $onlinePackages,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function refreshPackageSubscribers(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            document.getElementById('pagerequested').value = newPage ? newPage : 1;
        $.ajax({
            type: 'post',
            url: "{{ url('admin/package-subscribers/refresh') }}",
            data: $('#controls-form').serialize(),
            success: function(result) {
                if (result.code === '200' && result.html)
                    $('#package-subscribers-data').html(result.html);
            }
        });
    }

    function togglePackageFilter() {
        var type = document.getElementById('package_type').value;
        var sel = document.getElementById('package_select');
        sel.value = '';
        var optAdmin = document.getElementById('opt_admin');
        var optOnline = document.getElementById('opt_online');
        optAdmin.style.display = type === 'online_only' ? 'none' : '';
        optOnline.style.display = type === 'admin_only' ? 'none' : '';
    }

    $('#package_select').on('change', function() {
        var opt = this.options[this.selectedIndex];
        var typeInput = document.getElementById('package_filter_type');
        if (typeInput && opt && opt.getAttribute('data-filter-type'))
            typeInput.value = opt.getAttribute('data-filter-type');
    });
</script>
@endsection
