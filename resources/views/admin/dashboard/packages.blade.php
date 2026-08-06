@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Packages</h2>
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
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a onclick="return renderPackageModal();"><i class="fa fa-plus"></i> Add new</a>
                                </h5>

                                <div class="card-text">
                                    <table id="example" class="table table-striped table-bordered">
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
                                                <td class="nowrap">
                                                    @if($package->dataid!="99")
                                                    <img src="/images/package_{{$package->dataid}}.png" alt="{{$package->name}}" title="{{$package->name}}" style="height:70px; width: 70px" />
                                                    @endif
                                                    {{$package->name}}</td>
                                                <td>{{$package->description}}</td>
                                                <td>
                                                    <a onclick="return renderPackageModal('{{$package->dataid}}');"><i class="fa fa-pencil"></i></a>
                                                    @if($package->dataid!="99")
                                                    <a onclick="return deletePackage('{{$package->dataid}}');"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div><!-- /.card -->
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</section>
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
                    $("#modal_dialog").html(result.html);
                    $("#active_modal").modal("toggle");

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
