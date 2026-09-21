@extends('layouts.admin.master')
@section('dashboard-title', 'Profile Preview')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('admin-content')

<div class="ur-profile-preview">
    <div class="ur-profile-preview__crumbs">
        <a href="{{ url('admin/profiles') }}?page={{ $returnPage }}">Profiles</a> / {{ $member->first_name }} {{ $member->last_name }} / Profile Preview
    </div>

    <div class="ur-profile-preview__header">
        <div>
            <h2>Profile Preview</h2>
            <p>See exactly how this profile appears to members</p>
        </div>
        <div class="ur-profile-preview__header-actions">
            <a href="{{ url('admin/profiles') }}?page={{ $returnPage }}" class="ur-btn ur-btn--ghost-light">&larr; Back to Profiles</a>
            <div class="ur-viewport-toggle">
                <button type="button" class="is-active" onclick="setPreviewViewport(this, 'mobile')">Mobile</button>
                <button type="button" onclick="setPreviewViewport(this, 'desktop')">Desktop</button>
            </div>
            <a href="{{ url('/member/profile/'.$member->dataid) }}" target="_blank" class="ur-btn ur-btn--gold">View as Member &nearr;</a>
        </div>
    </div>

    <div class="ur-profile-preview__body">
        <div class="ur-profile-preview__panel">
            <h3><span class="ur-dot"></span> Live Website Preview</h3>
            <div class="ur-profile-preview__frame" id="preview-frame">
                @include('member.partials.member-card', ['member' => $member])
            </div>
        </div>

        <div class="ur-profile-preview__panel">
            <h3><span class="ur-dot"></span> Admin Controls</h3>

            <div class="ur-admin-controls-grid">
                <div>
                    <label>Profile Status</label>
                    <span id="active_label_{{ $member->dataid }}" class="ur-admin-badge {{ $member->active==0 ? 'ur-admin-badge--warning' : 'ur-admin-badge--success' }}">{{ $member->getActiveLabel() }}</span>
                </div>
                <div>
                    <label>Verification</label>
                    @php
                        $photoBadgeClass = match ($member->photo_verification_status) {
                            'verified' => 'ur-admin-badge--success',
                            'resubmit' => 'ur-admin-badge--warning',
                            default => 'ur-admin-badge--danger',
                        };
                    @endphp
                    <span class="ur-admin-badge {{ $photoBadgeClass }}">{{ ucfirst($member->photo_verification_status ?: 'pending') }}</span>
                </div>
                <div>
                    <label>Member ID</label>
                    <span>{{ $member->dataid }}</span>
                </div>
            </div>

            <label class="ur-section-label">Current Package</label>
            <div class="ur-current-package">
                {{ !empty($member->lbl_package) ? $member->lbl_package : 'Unassigned' }}
            </div>
            <div class="ur-profile-preview__actions-row">
                <a href="{{ url('admin/profile/package/'.$member->dataid) }}?page={{ $returnPage }}" class="ur-btn ur-btn--dark">&#8644; Change Package</a>
                <a href="{{ url('/member/profile/'.$member->dataid) }}" target="_blank" class="ur-btn ur-btn--outline">&#8599; Edit Profile</a>
                <a href="{{ url('admin/profile/listing/interests/'.$member->dataid) }}" class="ur-btn ur-btn--outline"><i class="fa fa-heart"></i> View Interests</a>
            </div>

            <label class="ur-section-label">Photo Privacy</label>
            <div class="ur-segmented" id="photo-visibility-toggle" data-dataid="{{ $member->dataid }}">
                <button type="button" data-value="visible" class="{{ ($member->photo_visibility ?: 'visible') === 'visible' ? 'is-active' : '' }}" onclick="setPhotoVisibility(this)">Visible</button>
                <button type="button" data-value="blurred" class="{{ $member->photo_visibility === 'blurred' ? 'is-active' : '' }}" onclick="setPhotoVisibility(this)">Blurred</button>
                <button type="button" data-value="hidden" class="{{ $member->photo_visibility === 'hidden' ? 'is-active' : '' }}" onclick="setPhotoVisibility(this)">Hidden</button>
            </div>
            <p class="ur-hint">Controls how this photo appears to members, overriding their own visibility.</p>

            <label class="ur-section-label">Email &amp; Security</label>
            <div class="ur-profile-preview__actions-row">
                <a class="ur-btn ur-btn--outline" onclick="return resendVerificationEmail($(this), '{{ $member->dataid }}');"><i class="fa fa-envelope"></i> Resend Verification</a>
                <a class="ur-btn ur-btn--outline" onclick="return sendPasswordResetEmail($(this), '{{ $member->dataid }}');"><i class="fa fa-unlock"></i> Send Password Reset</a>
            </div>

            <label class="ur-section-label">Account Actions <span class="ur-hint">sensitive actions are logged</span></label>
            <div class="ur-profile-preview__actions-row">
                <a id="active_{{ $member->dataid }}" class="ur-btn ur-btn--danger-outline" onclick="return toggleActive($(this), '{{ $member->dataid }}');">
                    {{ $member->active==1 ? 'Suspend Account' : 'Activate Account' }}
                </a>
                <a class="ur-btn ur-btn--danger-outline" onclick="return deleteProfileAndRedirect('{{ $member->dataid }}');"><i class="fa fa-trash"></i> Delete Profile</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function setPreviewViewport(btn, mode) {
        $(btn).siblings().removeClass('is-active');
        $(btn).addClass('is-active');
        $('#preview-frame').toggleClass('is-mobile', mode === 'mobile');
    }

    function setPhotoVisibility(btn) {
        var container = $('#photo-visibility-toggle');
        var dataid = container.data('dataid');
        var value = $(btn).data('value');

        $.ajax({
            type: 'post',
            url: "{{ url('admin/profile') }}/" + dataid + "/photo-visibility",
            data: { visibility: value, _token: '{{ csrf_token() }}' },
            success: function (result) {
                if (result.code == '200') {
                    container.find('button').removeClass('is-active');
                    $(btn).addClass('is-active');
                    showAlert('success', result.message, 3000);
                    location.reload(); // refresh so the live preview reflects the new visibility
                } else {
                    showAlert('danger', result.message, 5000);
                }
            }
        });
    }

    function deleteProfileAndRedirect(id) {
        swalConfirm("Delete Profile?", "Are you sure you want to delete this profile? You will not be able to revert this!", () => {
            $.ajax({
                type: "delete",
                url: "{{ url('admin/profile') }}" + "/" + id,
                data: { 'id': id, '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') {
                        swalAlert("success", "Success", "Profile deleted successfully.", () => {
                            window.location.href = "{{ url('admin/profiles') }}";
                        });
                    } else {
                        swal("error", "Error", "An error was encountered - " + result.message + ". Please contact admin of this website.");
                    }
                }
            });
        });
        return false;
    }

    function resendVerificationEmail(elem, id) {
        swalConfirm("Resend Verification Email?", "Are you sure you want to resend verification email to this profile? This will override any previous verification email sent.", () => {
            $.ajax({
                type: "post",
                url: "{{ url('admin/profile/resendemail') }}" + "/" + id,
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') showAlert('success', result.message, 3000);
                    else showAlert('danger', result.message, 5000);
                }
            });
        });
        return false;
    }

    function sendPasswordResetEmail(elem, id) {
        swalConfirm("Reset Password?", "Are you sure you want to request password reset for this profile?", () => {
            $.ajax({
                type: "post",
                url: "{{ url('admin/profile/requestreset') }}" + "/" + id,
                data: { '_token': '{{ csrf_token() }}' },
                success: function (result) {
                    if (result.code == '200') showAlert('success', result.message, 3000);
                    else showAlert('danger', result.message, 5000);
                }
            });
        });
        return false;
    }

    function toggleActive(elem, id) {
        $.ajax({
            type: "post",
            url: "{{ url('admin/profile/toggle') }}" + "/" + id,
            data: { '_token': '{{ csrf_token() }}' },
            success: function (result) {
                if (result.code == "200") {
                    var active = result.active;
                    elem.text(active == 1 ? 'Suspend Account' : 'Activate Account');
                    $("#active_label_" + id).text(active == 1 ? 'Active' : 'Pending')
                        .toggleClass('ur-admin-badge--success', active == 1)
                        .toggleClass('ur-admin-badge--warning', active != 1);
                    showAlert('success', result.message, 3000);
                } else showAlert('danger', result.message, 5000);
            }
        });
        return false;
    }
</script>
@endsection
