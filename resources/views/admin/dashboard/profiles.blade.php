@extends('layouts.admin.master')
@section('dashboard-title', 'Member Profiles')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Member Profiles</h2>
</div>

<div class="ur-admin-panel">
    <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
        @csrf
        <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>

        <div class="form-group form-group--tight">
            <label for="pagesize">Entries</label>
            <select name="pagesize" class="form-control form-control-sm" onchange="javascript:refreshProfiles(true);">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="form-group form-group--grow">
            <label for="term">Search</label>
            <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshProfiles(true);" value="" />
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" class="form-control form-control-sm selectpicker" data-placeholder="Choose a gender" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                <option value="">Select gender...</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
            </select>
        </div>
        <div class="form-group">
            <label for="package">Package</label>
            <select name="package" class="form-control form-control-sm selectpicker" data-placeholder="Choose a package" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                <option value="">Select package...</option>
                <option value="null">Unassigned</option>
                @foreach($packages as $package)
                <option value="{{$package->dataid}}">{{$package->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose a status" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                <option value="">Select status...</option>
                <option value="1">Active</option>
                <option value="0">Pending</option>
            </select>
        </div>
        <div class="form-group form-group--grow">
            <label>Show Only</label>
            <div>
                <div class="form-check form-check-inline"><input type="radio" checked="checked" name="showonly" value="all" class="form-check-input" onchange="javascript:$('#within').hide();refreshProfiles(true);" /><label class="form-check-label"> All Profiles </label></div>
                <div class="form-check form-check-inline"><input type="radio" name="showonly" value="updated" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Updated </label></div>
                <div class="form-check form-check-inline"><input type="radio" name="showonly" value="created" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Created </label></div>
                <span id="within" style="display: none"> within last
                    <select name="showwithin" class="form-control form-control-sm d-inline-block" style="width:auto;" onchange="javascript:refreshProfiles(true);">
                        <option value="">Select...</option>
                        <option>1</option>
                        <option>2</option>
                        <option>4</option>
                        <option>6</option>
                        <option>8</option>
                        <option>10</option>
                    </select> week(s)
                </span>
            </div>
        </div>
    </form>

    <div id="member-data">
    @yield('member-data')
    </div>
</div>
<script type="text/javascript">
    function refreshProfiles(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/profiles/refresh')}}", "post", $("#controls-form").serialize(), $("#member-data"));
    }

    function toggleActive(elem, id) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "get",
            url: "{{url('admin/profile/toggle/')}}" + "/" + id,
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message;
                if (result.code == "200") {
                    var active = result.active;
                    clickHighlight($("#active_label_" + id), active == 1 ? "Active" : "Pending",
                        $("#active_" + id), active == 1 ? "toggle-on" : "toggle-off", "Make " + (active == 1 ? "Inactive" : "Active"), active == 1);
                    showAlert('success', message, 3000);
                } else showAlert('danger', message, 5000);
            }
        })
    }

    function deleteProfile(elem, id) {
        swalConfirm("Delete Profile?", "Are you sure you want to delete this profile? You will not be able to revert this!", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "delete",
                url: "{{ url('admin/profile')}}" + "/" + id,
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    if (result.code == '200') {
                        swalAlert("success", "Success", "Profile deleted successfully.", () => {
                            $("#block_"+id).remove();
                        });
                    } else {
                        swal("error", "Error", "An error was encountered - " + result.message + ". Please contact admin of this website.");
                    }
                }
            });
        });
    }

    function resendVerificationEmail(elem, id) {
        swalConfirm("Resend Verification Email?", "Are you sure you want to resend verification email to this profile? This will override any previous verification email sent.", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "get",
                url: "{{ url('admin/profile/resendemail')}}" + "/" + id,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message;
                    if (result.code == '200') {
                        showAlert('success', message, 3000);
                    } else showAlert('danger', message, 5000);
                }
            });
        });
    }

    function sendPasswordResetEmail(elem, id) {
        swalConfirm("Reset Password?", "Are you sure you want to request password reset for this profile?", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "get",
                url: "{{ url('admin/profile/requestreset')}}" + "/" + id,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message;
                    if (result.code == '200') {
                        showAlert('success', message, 3000);
                    } else showAlert('danger', message, 5000);
                }
            });

        });
    }

</script>
@endsection
