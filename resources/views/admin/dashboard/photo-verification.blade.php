@extends('layouts.admin.master')
@section('admin-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Photo &amp; Identity Verification</h2>
                <p class="mb-0">Members with {{ $required }}+ uploaded photos, awaiting review before they show as "Verified".</p>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="block-wrapper">

                    @if($pending->isEmpty())
                        <div class="block block--style-3 z-depth-1-top" style="padding: 30px; text-align: center;">
                            <i class="fa fa-check-circle" style="font-size: 32px; color: #123A2E;"></i>
                            <p style="margin-top:12px;">Nothing waiting for review right now.</p>
                        </div>
                    @endif

                    @foreach($pending as $member)
                        <div class="block block--style-3 z-depth-1-top pv-card" id="pv_card_{{ $member->dataid }}" style="padding: 20px; margin-bottom: 18px;">
                            <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:flex-start;">
                                <div style="flex: 1 1 260px; min-width: 220px;">
                                    <div style="font-weight:700; font-size:16px;">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        <span style="font-weight:400; font-size:12px; color:#777;">({{ $member->dataid }})</span>
                                    </div>
                                    <div style="font-size:12.5px; color:#777;">
                                        <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                        @if($member->contact_mobile_number) | {{ $member->contact_mobile_number }} @endif
                                    </div>
                                    <div style="font-size:12px; color:#999; margin-top:4px;">
                                        Registered {{ \Carbon\Carbon::parse($member->created_at)->diffForHumans() }}
                                    </div>

                                    <div style="margin-top:14px;">
                                        <textarea id="pv_reason_{{ $member->dataid }}" class="form-control form-control-sm" rows="2" placeholder="Reason if rejecting (required to reject)"></textarea>
                                    </div>
                                    <div style="margin-top:10px; display:flex; gap:8px;">
                                        <button type="button" class="btn btn-success btn-sm" onclick="pvApprove('{{ $member->dataid }}')">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="pvReject('{{ $member->dataid }}')">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>

                                <div style="flex: 2 1 360px; display:flex; gap:14px; flex-wrap:wrap;">
                                    @foreach($member->regularImages as $img)
                                        <div style="text-align:center;">
                                            <img src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $img->name) }}"
                                                 style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:1px solid #e6e6e6;">
                                            <div style="font-size:11px; color:#999; margin-top:4px;">Uploaded photo</div>
                                        </div>
                                    @endforeach

                                    <div style="text-align:center;">
                                        @if($member->selfieImage)
                                            <img src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $member->selfieImage->name) }}"
                                                 style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:2px solid #C9974D;">
                                            <div style="font-size:11px; color:#C9974D; font-weight:600; margin-top:4px;">Live selfie</div>
                                        @else
                                            <div style="width:120px; height:120px; border-radius:10px; border:1.5px dashed #ddd; display:flex; align-items:center; justify-content:center; color:#aaa; font-size:11px; text-align:center; padding:6px;">
                                                No selfie provided
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div style="margin-top:10px;">
                        {{ $pending->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1" style="padding-top:0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="heading heading-5 strong-500" style="margin-bottom:14px;">
                    Rejected — Locked Out of Login ({{ $rejected->total() }})
                </h3>
                <p style="font-size:13px; color:#777; margin-bottom:16px;">
                    These accounts cannot log in until reopened here. Reopening clears their rejected
                    photos and lets them log in once to resubmit.
                </p>

                <div class="block-wrapper">
                    @if($rejected->isEmpty())
                        <div class="block block--style-3 z-depth-1-top" style="padding: 20px; text-align: center; color:#999;">
                            No rejected accounts.
                        </div>
                    @endif

                    @foreach($rejected as $member)
                        <div class="block block--style-3 z-depth-1-top pv-rejected-card" id="pv_rejected_card_{{ $member->dataid }}" style="padding: 16px 20px; margin-bottom: 14px;">
                            <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between;">
                                <div>
                                    <div style="font-weight:700; font-size:15px;">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        <span style="font-weight:400; font-size:12px; color:#777;">({{ $member->dataid }})</span>
                                    </div>
                                    <div style="font-size:12.5px; color:#777;">
                                        <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                    </div>
                                    <div style="font-size:12.5px; color:#B5674A; margin-top:4px;">
                                        <i class="fa fa-times-circle"></i> {{ $member->photo_rejection_reason }}
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning btn-sm" onclick="pvReopen('{{ $member->dataid }}')">
                                    <i class="fa fa-unlock"></i> Reopen for Resubmission
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <div style="margin-top:10px;">
                        {{ $rejected->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
