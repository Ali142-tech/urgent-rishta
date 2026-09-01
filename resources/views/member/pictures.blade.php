<?php use App\Profile; ?>
@extends('layouts.dashboard')
@section('dashboard-title', 'Manage Pictures')
@push('styles')
<link rel="stylesheet" href="/css/ur-pictures.css?1">
@endpush
@section('main-content')

<div class="ur-pictures-page">
    <label id="pg_btn_image_edit" class="ur-pictures-page__add" for="pg_images">
        <i class="fa fa-plus-circle"></i> Add Pictures
    </label>
    <form id="pg_images_form" enctype="multipart/form-data">
        @csrf
        <input type="file" accept="image/png,image/x-png,image/gif,image/jpeg" style="display: none;" id="pg_images" name="images[]" multiple onchange="javascript:pageImagesUpload();" />
    </form>

    <div class="ur-pictures-page__grid">
        @forelse($images as $image)
        <div class="ur-pictures-page__card" id="image_{{ $image->dataid }}">
            @if($image->displaypic == 1)
            <span class="ur-pictures-page__badge">Display Pic</span>
            @endif
            <img src="{{ Profile::MEMBER_IMAGES_PATH }}/thumbnail_{{ $image->name }}" alt="">
            <div class="ur-pictures-page__overlay">
                <button type="button" class="ur-pictures-page__action" id="displaypic_{{ $image->dataid }}" title="Set as Display Picture" onclick="javascript:updateImage($(this), 'dp', '{{ $image->dataid }}');">
                    <i class="fa fa-{{ $image->displaypic == 1 ? 'user' : 'user-o' }}"></i>
                </button>
                <button type="button" class="ur-pictures-page__action ur-pictures-page__action--delete" title="Delete" onclick="javascript:deleteImage($(this), '{{ $image->dataid }}');">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="ur-pictures-page__empty">
            <i class="fa fa-camera"></i>
            No pictures yet — click "Add Pictures" above to upload your first one.
        </div>
        @endforelse
    </div>
</div>

<script type="text/javascript">
    function pageImagesUpload() {
        var label = $("#pg_btn_image_edit");
        var oldHtml = label.html();
        label.html("<i class='fa fa-refresh fa-spin'></i> Uploading...");

        $.ajax({
            type: "POST",
            url: "{{ url('member/profile/update/images/upload') }}",
            cache: false,
            contentType: false,
            processData: false,
            data: new FormData($("#pg_images_form")[0]),
            success: function(result) {
                if (result.message) {
                    var message = result.message.split("|");
                    showAlert(message[0], message[1], message[2]);
                }
                setTimeout(function() { location.reload(); }, 700);
            },
            error: function() {
                label.html(oldHtml);
                showAlert('danger', 'There was an error uploading your image(s). Please try again.', 5000);
            }
        });
    }
</script>
@endsection
