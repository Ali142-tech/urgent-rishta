<div class="modal fade" id="active_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="max-width: 400px; margin-top: 30vh;">
        <div class="modal-content">
            <div class="modal-header text-center" style="display: block; border-bottom: 1px solid transparent">
                <span class="modal-title" id="modal_title">{{ $title }}</span>
                <button type="button" class="close" id="modal_close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal_body">
                {!! $body !!}
            </div>
            <div class="modal-footer text-center" id="modal_buttons">
                {!! $buttons !!}
            </div>
        </div>
    </div>
</div>
