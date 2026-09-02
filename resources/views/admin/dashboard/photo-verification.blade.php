@extends('layouts.admin.master')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Photo &amp; Identity Verification</h2>
    <p>Members with {{ $required }}+ uploaded photos, awaiting review before they show as "Verified".</p>
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
                    <div class="ur-admin-pv-photo">
                        <img src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $img->name) }}">
                        <div class="ur-admin-pv-photo__label">Uploaded photo</div>
                    </div>
                @endforeach

                @if($member->selfieImage)
                    <div class="ur-admin-pv-photo">
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
