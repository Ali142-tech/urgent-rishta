@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Packages</h2>
</div>

<div class="ur-admin-panel">
    <div class="ur-admin-toolbar">
        <span>All Packages</span>
        <a class="ur-admin-btn ur-admin-btn--gold" onclick="return renderPackageModal();"><i class="fa fa-plus"></i> Add New Package</a>
    </div>

    <div class="ur-admin-table-wrap">
        <table class="ur-admin-table ur-admin-table--packages">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packages as $package)
                <tr>
                    <td>
                        <div class="ur-admin-package-name">
                            @if($package->dataid!="99")
                            <img src="/images/package_{{$package->dataid}}.png" alt="{{$package->name}}" title="{{$package->name}}" class="ur-admin-package-thumb" />
                            @endif
                            <span>{{$package->name}}</span>
                        </div>
                    </td>
                    <td class="ur-admin-package-desc">{{$package->description}}</td>
                    <td>
                        <div class="ur-admin-row-actions">
                            <a class="ur-admin-icon-link" onclick="return renderPackageModal('{{$package->dataid}}');" title="Edit"><i class="fa fa-pencil"></i></a>
                            @if($package->dataid!="99")
                            <a class="ur-admin-icon-link" onclick="return deletePackage('{{$package->dataid}}');" title="Delete"><i class="fa fa-trash"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">

function deletePackage(id) {
        swalConfirm("Delete Package?", "Are you sure you want to delete this package? You will not be able to revert this!", ()=>{
            $.ajax({
                type: "delete",
                url: "{{ url('admin/packages')}}" + "/" + id,
                data:{
                    'id': id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(result) {
                    if (result.code == '200') {
                        swalAlert("success", "Success", "Package Deleted", ()=>{
                            if (result.response && result.response.html)
                                $("#admin-content").html(result.response.html);
                        });
                    } else {
                        swal("error", "Error", "An error was encountered - " + result.message + ". Please contact admin of this website.");
                    }
                }
            });
        });
    }

    function renderPackageModal(id) {
        $.ajax({
            type: "get",
            url: id ? "{{ url('admin/packages/modal')}}"+"/"+id : "{{ url('admin/packages/modal')}}",
            success: function(result) {
                if (result.code == '200') {
                    openAdminModal(result.html);

                    $("#package_form").on("submit", (e)=>{
                        e.preventDefault();
                        $.ajax({
                            url: id?"{{url('admin/packages/')}}"+"/"+id : "{{url('admin/packages/')}}",
                            type: "post",
                            data: $('#package_form').serialize(),
                            success: function(result){
                                var message = result.message;
                                if (result.code == '200') {
                                    showAlert('success', message, 3000);
                                    if (result.response && result.response.html)
                                        $("#admin-content").html(result.response.html);
                                } else showAlert('danger', message, 5000);
                            }
                        });
                    });
                }
            }
        });
    }
</script>
@endsection
