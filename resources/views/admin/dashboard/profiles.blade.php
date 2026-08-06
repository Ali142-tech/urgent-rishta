@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Member Profiles</h2>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="block-wrapper">
                        <form id="controls-form" action="javascript:void();">
                            @csrf
                            <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
                            <div class="row">
                                <div class="col col-sm-4 col-md-4">
                                    <span><label for="pagesize">Number of entries: <select name="pagesize" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshProfiles(true);">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select></label></span>
                                </div>
                                <div class="col col-sm-8 col-md-8">
                                    <span><label for="term">Search: <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshProfiles(true);" value="" /></label></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-sm-12 col-md-4">
                                    <span><label for="gender">Gender: <select name="gender" class="form-control form-control-sm selectpicker" data-placeholder="Choose a gender" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                                        <option value="">Select gender...</option>
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                    </select></label></span>
                                </div>
                                <div class="col col-sm-12 col-md-4">
                                    <span><label for="package">Package:
                                        <select name="package" class="form-control form-control-sm selectpicker" data-placeholder="Choose a package" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                                            <option value="">Select package...</option>
                                            <option value="null">Unassigned</option>
                                            @foreach($packages as $package)
                                            <option value="{{$package->dataid}}">{{$package->name}}</option>
                                            @endforeach
                                        </select></label>
                                    </span>
                                </div>
                                <div class="col col-sm-12 col-md-4">
                                    <span><label for="status">Status:
                                        <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Choose a status" data-hide-disabled="true" onchange="javascript:refreshProfiles(true);">
                                            <option value="">Select status...</option>
                                            <option value="1">Active</option>
                                            <option value="0">Pending</option>
                                        </select></label>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-sm-12 col-md-12">
                                    <span>
                                        <label>Show Only:
                                            <div>
                                                <div class="form-check form-check-inline"><input type="radio" checked="checked" name="showonly" value="all" class="form-check-input" onchange="javascript:$('#within').hide();refreshProfiles(true);" /><label class="form-check-label"> All Profiles </label></div>
                                                <div class="form-check form-check-inline"><input type="radio" name="showonly" value="updated" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Updated </label></div>
                                                <div class="form-check form-check-inline"><input type="radio" name="showonly" value="created" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Created </label></div>
                                                <div class="justify-content-center" id="within" style="display: none"> within last
                                                    <select name="showwithin" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshProfiles(true);">
                                                        <option value="">Select...</option>
                                                        <option>1</option>
                                                        <option>2</option>
                                                        <option>4</option>
                                                        <option>6</option>
                                                        <option>8</option>
                                                        <option>10</option>
                                                    </select> week(s)
                                                </div>
                                            </div>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="member-data">
                    @yield('member-data')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function refreshProfiles(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/profiles/refresh')}}", "post", $("#controls-form").serialize(), $("#member-data"));
    }

    function renderListingModal(type, dataid) {
        $.ajax({
            type: "get",
            url: "{{ url('admin/profile/listing')}}"+"/"+type+"/"+dataid,
            success: function(result) {
                if (result.code == '200') {
                    $("#modal_dialog").html(result.html);
                    $("div.modal-dialog").css("max-width", "900px");
                    $("#modal_body").find("div.hidden_xs").css("display", "none");
                    $("#modal_body").find("div.block-footer").css("display", "none");
                    $("#active_modal").modal("toggle");
                }
            }
        });
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

    function renderUpdatePackageModal(dataid) {
        $.ajax({
            type: "get",
            url: "{{ url('admin/profile/package/modal')}}" + "/" + dataid,
            success: function(result) {
                if (result.code == '200') {
                    $("#modal_dialog").html(result.html);
                    $("#active_modal").modal("toggle");
                }
            }
        });
    }

    function updatePackage(dataid) {
        //var form = elem.parent('form');
        $.ajax({
            url: "{{url('admin/profile/updatepackage')}}" + "/" + dataid,
            type: "post",
            data: $('#package_form').serialize(),
            success: function(result) {
                var message = result.message;
                if (result.code == '200') {
                    showAlert('success', message, 3000);
                    if (result.response) {
                        var package = result.response.split("|");
                        var aTag = $("#package_"+dataid);
                        if (package[0]==99)
                            aTag.text("All Profiles");
                        else {
                            aTag.html('<img src="/images/package_'+package[0]+'.png" alt="'+package[1]+'" title="'+package[1]+'" style="height:70px; width: 70px" />');
                        }
                    }

                } else showAlert('danger', message, 5000);
            }
        });
    }
</script>
@endsection
