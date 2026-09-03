@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Photo &amp; Identity Verification</h2>
    <p>Members with {{ $required }}+ uploaded photos, awaiting review before they show as "Verified".
        <a href="{{ url('admin/photo-verification/logs') }}" class="ur-admin-header__muted"><i class="fa fa-history"></i> View decision history</a>
    </p>
</div>

<div class="ur-admin-panel">

    @if($pending->isEmpty())
        <div class="ur-admin-empty">
            <i class="fa fa-check-circle"></i> Nothing waiting for review right now.
        </div>
    @endif

    @foreach($pending as $member)
        <div class="ur-admin-card ur-admin-pv-card" id="pv_card_{{ $member->dataid }}">
            <div class="ur-admin-pv-card__info">
                <div class="ur-admin-pv-card__name">
                    {{ $member->first_name }} {{ $member->last_name }}
                    <span>({{ $member->dataid }})</span>
                </div>
                <div class="ur-admin-pv-card__contact">
                    <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                    @if($member->contact_mobile_number) | {{ $member->contact_mobile_number }} @endif
                </div>
                <div class="ur-admin-pv-card__meta">
                    Registered {{ \Carbon\Carbon::parse($member->created_at)->diffForHumans() }}
                </div>

                <textarea id="pv_reason_{{ $member->dataid }}" class="ur-admin-pv-card__reason" rows="2" placeholder="Reason if rejecting (required to reject)"></textarea>

                <div class="ur-admin-pv-card__actions">
                    <button type="button" class="ur-admin-pill-btn ur-admin-pill-btn--approve" onclick="pvApprove('{{ $member->dataid }}')">
                        <i class="fa fa-check"></i> Approve
                    </button>
                    <button type="button" class="ur-admin-pill-btn ur-admin-pill-btn--reject" onclick="pvReject('{{ $member->dataid }}')">
                        <i class="fa fa-times"></i> Reject
                    </button>
                </div>
            </div>

            <div class="ur-admin-pv-card__photos">
                @foreach($member->regularImages as $img)
                    <div class="ur-admin-pv-photo" onclick="pvZoom('{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/' . $img->name) }}', 'Uploaded photo — {{ $member->dataid }}')">
                        <img src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $img->name) }}">
                        <div class="ur-admin-pv-photo__label">Uploaded photo</div>
                    </div>
                @endforeach

                @if($member->selfieImage)
                    <div class="ur-admin-pv-photo" onclick="pvZoom('{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/' . $member->selfieImage->name) }}', 'Live selfie — {{ $member->dataid }}')">
                        <img class="is-selfie" src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $member->selfieImage->name) }}">
                        <div class="ur-admin-pv-photo__label ur-admin-pv-photo__label--selfie">Live selfie</div>
                    </div>
                @else
                    <div class="ur-admin-pv-photo--empty">
                        No selfie provided
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <div style="margin-top:10px;">
        {{ $pending->links() }}
    </div>

</div>

<div class="ur-admin-header">
    <h2>Rejected — Locked Out of Login ({{ $rejected->total() }})</h2>
    <p>These accounts cannot log in until reopened here. Reopening clears their rejected photos and lets them log in once to resubmit.</p>
</div>

<div class="ur-admin-panel">
    @if($rejected->isEmpty())
        <div class="ur-admin-empty">
            No rejected accounts.
        </div>
    @endif

    @foreach($rejected as $member)
        <div class="ur-admin-card ur-admin-rejected-card" id="pv_rejected_card_{{ $member->dataid }}">
            <div>
                <div class="ur-admin-rejected-card__name">
                    {{ $member->first_name }} {{ $member->last_name }}
                    <span>({{ $member->dataid }})</span>
                </div>
                <div class="ur-admin-pv-card__contact">
                    <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                </div>
                <div class="ur-admin-rejected-card__reason">
                    <i class="fa fa-times-circle"></i> {{ $member->photo_rejection_reason }}
                </div>
            </div>
            <button type="button" class="ur-admin-pill-btn ur-admin-pill-btn--reopen" onclick="pvReopen('{{ $member->dataid }}')">
                <i class="fa fa-unlock"></i> Reopen for Resubmission
            </button>
        </div>
    @endforeach

    <div style="margin-top:10px;">
        {{ $rejected->links() }}
    </div>
</div>

<div id="pv_zoom_overlay" class="ur-pv-zoom" onclick="pvZoomClose(event)">
    <button type="button" class="ur-pv-zoom__close" onclick="pvZoomClose(event)"><i class="fa fa-times"></i></button>
    <div class="ur-pv-zoom__caption" id="pv_zoom_caption"></div>
    <img id="pv_zoom_img" src="" alt="">
</div>

<style>
    .ur-pv-zoom {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 35, 33, .92);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 30px;
        cursor: zoom-out;
    }
    .ur-pv-zoom.is-open { display: flex; }
    .ur-pv-zoom__caption {
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .3px;
        margin-bottom: 14px;
        text-align: center;
    }
    .ur-pv-zoom img {
        max-width: min(90vw, 720px);
        max-height: 80vh;
        border-radius: 10px;
        box-shadow: 0 20px 60px rgba(0,0,0,.4);
        cursor: default;
    }
    .ur-pv-zoom__close {
        position: absolute;
        top: 20px;
        right: 24px;
        background: rgba(255,255,255,.12);
        border: 0;
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
    }
    .ur-pv-zoom__close:hover { background: rgba(255,255,255,.22); }
</style>

<script>
    function pvZoom(fullUrl, caption) {
        $('#pv_zoom_img').attr('src', fullUrl);
        $('#pv_zoom_caption').text(caption || '');
        $('#pv_zoom_overlay').addClass('is-open');
    }
    function pvZoomClose(e) {
        // Ignore clicks on the image itself so it doesn't close when you're
        // just looking at it — only the backdrop / close button / Esc do.
        if (e && e.target && e.target.id === 'pv_zoom_img') return;
        $('#pv_zoom_overlay').removeClass('is-open');
    }
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') pvZoomClose();
    });
</script>

<script>
    function pvApprove(dataid) {
        pvSubmit(dataid, 'approve', null, '#pv_card_' + dataid);
    }
    function pvReject(dataid) {
        var reason = $('#pv_reason_' + dataid).val();
        if (!reason || !reason.trim()) {
            showAlert('danger', 'Please enter a reason before rejecting.');
            return;
        }
        pvSubmit(dataid, 'reject', reason, '#pv_card_' + dataid);
    }
    function pvReopen(dataid) {
        pvSubmit(dataid, 'reopen', null, '#pv_rejected_card_' + dataid);
    }
    function pvSubmit(dataid, action, reason, cardSelector) {
        $.ajax({
            type: 'POST',
            url: "{{ url('admin/photo-verification') }}/" + dataid + "/" + action,
            data: { reason: reason, _token: "{{ csrf_token() }}" },
            success: function(result) {
                var message = (result.message || '').split('|');
                if (message[1]) showAlert(message[0], message[1]);
                if (result.code == '200' && cardSelector) {
                    $(cardSelector).fadeOut(300, function() { $(this).remove(); });
                }
            },
            error: function() {
                showAlert('danger', 'Something went wrong. Please try again.');
            }
        });
    }
</script>
@endsection
