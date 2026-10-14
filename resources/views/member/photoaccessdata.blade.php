<?php use App\User; ?>
@extends('member.listing')
@push('styles')
<link rel="stylesheet" href="/css/ur-interests.css?1">
@endpush
@section('photoaccess-data')

<div class="ur-interests-page">
    <div class="ur-interests-tabs">
        <button type="button" class="ur-interests-tab is-active" id="ur_tab_received" onclick="urInterestsShowTab('received')">
            <i class="fa fa-inbox"></i> Received Requests
            <span class="ur-interests-tab__badge">{{ !empty($members['received']) ? count($members['received']) : 0 }}</span>
        </button>
        <button type="button" class="ur-interests-tab" id="ur_tab_sent" onclick="urInterestsShowTab('sent')">
            <i class="fa fa-paper-plane"></i> Sent Requests
            <span class="ur-interests-tab__badge">{{ !empty($members['sent']) ? count($members['sent']) : 0 }}</span>
        </button>
    </div>

    {{-- ============= RECEIVED: other members asking to see MY hidden photos ============= --}}
    <div class="ur-interests-panel is-active" id="ur_panel_received">
        @if(!empty($members) && !empty($members['received']) && sizeof($members['received'])>0)
            @foreach($members['received'] as $dataid => $member)
            @php
                $allowed = $member->allowed;
                if ($allowed==1) { $class = "btn-green"; $label = "GRANTED"; $statusClass = "ur-interest-card__status--accepted"; }
                else if ($allowed==-1) { $class = "btn-red"; $label = "DECLINED"; $statusClass = "ur-interest-card__status--declined"; }
                else { $class = "btn-base-1"; $label = "PENDING"; $statusClass = "ur-interest-card__status--pending"; }
                $age = !empty($member->birthday) ? date_diff(date_create($member->birthday), date_create('now'))->y : null;
            @endphp
            <div class="ur-interest-card" id="block_rec_{{$dataid}}">
                <div class="ur-interest-card__avatar"
                     style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"
                     onclick="javascript:window.open('{{url('/member/profile/'.$dataid)}}');"></div>
                <div class="ur-interest-card__body">
                    <div class="ur-interest-card__head">
                        <h5 class="ur-interest-card__name">
                            <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $member->first_name }}</a>
                            <div class="ur-interest-card__id">Member ID: <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $dataid }}</a></div>
                        </h5>
                        <span id="photoaccess_status_{{ $dataid }}" class="ur-interest-card__status {{ $statusClass }} {{ $class }}">{{ $label }}</span>
                    </div>
                    <div class="ur-interest-card__tags">
                        @if($age)<span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>@endif
                        <span class="ur-interest-card__tag">Height <b>{{ $member->height ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Religion <b>{{ $member->lbl_religion ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Caste / Sect <b>{{ $member->lbl_caste ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Marital Status <b>{{ $member->lbl_marital_status ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Location <b>{{ $member->lbl_con_of_residence ?: 'N/A' }}</b></span>
                    </div>
                    <div class="ur-interest-card__actions">
                        <a class="ur-interest-card__btn ur-interest-card__btn--ghost" onclick="javascript:window.open('{{url('/member/profile/'.$dataid)}}');">
                            <i class="fa fa-id-card"></i> Full Profile
                        </a>
                        @if($allowed==0)
                        <a class="ur-interest-card__btn ur-interest-card__btn--accept" id="photoaccess_{{$dataid}}_g" title="Grant Photo Access" onclick="return grantPhotoAccess($(this));">
                            <i class="fa fa-check"></i> Grant Access
                        </a>
                        <a class="ur-interest-card__btn ur-interest-card__btn--decline" id="photoaccess_{{$dataid}}_d" title="Decline Photo Access" onclick="return declinePhotoAccess($(this));">
                            <i class="fa fa-times"></i> Decline
                        </a>
                        @elseif($allowed==1)
                        <a class="ur-interest-card__btn ur-interest-card__btn--withdraw" id="photoaccess_{{$dataid}}_w" title="Revoke Photo Access" onclick="return withdrawPhotoAccess($(this), 'o');">
                            <i class="fa fa-times"></i> Revoke Access
                        </a>
                        @else
                        <a class="ur-interest-card__btn ur-interest-card__btn--withdraw" id="photoaccess_{{$dataid}}_w" title="Remove Declined Request" onclick="return withdrawPhotoAccess($(this), 'o');">
                            <i class="fa fa-times"></i> Remove
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="ur-interests-empty"><i class="fa fa-inbox"></i>No one has asked to see your hidden photos yet.</div>
        @endif
    </div>

    {{-- ============= SENT: hidden photos I've asked to see ============= --}}
    <div class="ur-interests-panel" id="ur_panel_sent">
        @if(!empty($members) && !empty($members['sent']) && sizeof($members['sent'])>0)
            @foreach($members['sent'] as $dataid => $member)
            @php
                $allowed = $member->allowed;
                if ($allowed==1) { $class = "btn-green"; $label = "GRANTED"; $statusClass = "ur-interest-card__status--accepted"; }
                else if ($allowed==-1) { $class = "btn-red"; $label = "DECLINED"; $statusClass = "ur-interest-card__status--declined"; }
                else { $class = "btn-base-1"; $label = "PENDING"; $statusClass = "ur-interest-card__status--pending"; }
                $age = !empty($member->birthday) ? date_diff(date_create($member->birthday), date_create('now'))->y : null;
            @endphp
            <div class="ur-interest-card" id="block_sent_{{$dataid}}">
                <div class="ur-interest-card__avatar"
                     style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"
                     onclick="javascript:window.open('{{url('/member/profile/'.$dataid)}}');"></div>
                <div class="ur-interest-card__body">
                    <div class="ur-interest-card__head">
                        <h5 class="ur-interest-card__name">
                            <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $member->first_name }}</a>
                            <div class="ur-interest-card__id">Member ID: <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $dataid }}</a></div>
                        </h5>
                        <span id="photoaccess_status_{{ $dataid }}" class="ur-interest-card__status {{ $statusClass }} {{ $class }}">{{ $label }}</span>
                    </div>
                    <div class="ur-interest-card__tags">
                        @if($age)<span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>@endif
                        <span class="ur-interest-card__tag">Height <b>{{ $member->height ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Religion <b>{{ $member->lbl_religion ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Caste / Sect <b>{{ $member->lbl_caste ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Marital Status <b>{{ $member->lbl_marital_status ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Location <b>{{ $member->lbl_con_of_residence ?: 'N/A' }}</b></span>
                    </div>
                    <div class="ur-interest-card__actions">
                        <a class="ur-interest-card__btn ur-interest-card__btn--ghost" onclick="javascript:window.open('{{url('/member/profile/'.$dataid)}}');">
                            <i class="fa fa-id-card"></i> Full Profile
                        </a>
                        <a class="ur-interest-card__btn ur-interest-card__btn--withdraw" id="photoaccess_{{$dataid}}_w" title="Withdraw Request" onclick="return withdrawPhotoAccess($(this), 's');">
                            <i class="fa fa-times"></i> Withdraw
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="ur-interests-empty"><i class="fa fa-paper-plane"></i>You haven't requested to see anyone's hidden photos yet.</div>
        @endif
    </div>
</div>

<script type="text/javascript">
    function urInterestsShowTab(tab) {
        document.getElementById('ur_panel_received').classList.toggle('is-active', tab === 'received');
        document.getElementById('ur_panel_sent').classList.toggle('is-active', tab === 'sent');
        document.getElementById('ur_tab_received').classList.toggle('is-active', tab === 'received');
        document.getElementById('ur_tab_sent').classList.toggle('is-active', tab === 'sent');
    }
</script>
@endsection
