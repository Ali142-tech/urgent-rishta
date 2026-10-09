<?php use App\User; ?>
{{--
    Admin, read-only view of one member's interests (received + sent).
    Reuses the .ur-interest-card component/CSS from member/interestdata.blade.php
    (loaded admin-wide via ur-interests.css, see layouts/admin/dashboard.blade.php)
    but deliberately drops the Accept/Decline/Withdraw/Express Interest actions —
    those act on the *currently logged-in* account, which here is the admin, not
    the member being reviewed, so showing them would let an admin accidentally
    fire an interest action as themselves instead of just observing.
--}}
@extends('layouts.admin.master')
@section('dashboard-title', 'Admin Dashboard')
@section('admin-content')
<div class="ur-admin-header">
    <p class="ur-admin-back"><a href="{{ url('admin/profiles') }}"><i class="fa fa-arrow-left"></i> Back to Profiles</a></p>
    <h2>Interests &mdash; {{ $member->first_name }} {{ $member->last_name }} <span class="ur-admin-header__muted">({{ $member->dataid }})</span></h2>
</div>

<div class="ur-admin-panel">
    <div class="ur-interests-page">
        <div class="ur-interests-tabs">
            <button type="button" class="ur-interests-tab is-active" id="ur_tab_received" onclick="urInterestsShowTab('received')">
                <i class="fa fa-inbox"></i> Received Interests
                <span class="ur-interests-tab__badge">{{ !empty($members['received']) ? count($members['received']) : 0 }}</span>
            </button>
            <button type="button" class="ur-interests-tab" id="ur_tab_sent" onclick="urInterestsShowTab('sent')">
                <i class="fa fa-paper-plane"></i> Sent Interests
                <span class="ur-interests-tab__badge">{{ !empty($members['sent']) ? count($members['sent']) : 0 }}</span>
            </button>
        </div>

        {{-- ================= RECEIVED ================= --}}
        <div class="ur-interests-panel is-active" id="ur_panel_received">
            @if(!empty($members['received']) && sizeof($members['received'])>0)
                @foreach($members['received'] as $dataid => $other)
                @php
                    $interest = $other->interest_back;
                    if ($interest==1) { $statusClass = "ur-interest-card__status--accepted"; $label = "ACCEPTED"; }
                    elseif ($interest==-1) { $statusClass = "ur-interest-card__status--declined"; $label = "DECLINED"; }
                    else { $statusClass = "ur-interest-card__status--pending"; $label = "PENDING"; }
                    $age = date_diff(date_create($other->birthday), date_create('now'))->y;
                @endphp
                <div class="ur-interest-card">
                    <a class="ur-interest-card__avatar" href="{{ url('/member/profile/'.$dataid) }}" target="_blank"
                       style="display:block; background-image: url('{{ User::retrieveUserObject($other->dataid)->getProfileImage() }}')"></a>
                    <div class="ur-interest-card__body">
                        <div class="ur-interest-card__head">
                            <h5 class="ur-interest-card__name">
                                <a href="{{ url('/member/profile/'.$dataid) }}" target="_blank">{{ $other->first_name }}</a>
                                <div class="ur-interest-card__id">Member ID: <a href="{{ url('/member/profile/'.$dataid) }}" target="_blank">{{ $dataid }}</a></div>
                            </h5>
                            <span class="ur-interest-card__status {{ $statusClass }}">{{ $label }}</span>
                        </div>
                        <div class="ur-interest-card__tags">
                            <span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>
                            <span class="ur-interest-card__tag">Height <b>{{ $other->height }}</b></span>
                            <span class="ur-interest-card__tag">Religion <b>{{ $other->lbl_religion ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Caste / Sect <b>{{ $other->lbl_caste ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Mother Tongue <b>{{ $other->lbl_mother_tongue ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Marital Status <b>{{ $other->lbl_marital_status ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Location <b>{{ $other->lbl_con_of_residence ?: 'N/A' }}</b></span>
                        </div>
                        <div class="ur-interest-card__actions">
                            <a class="ur-interest-card__btn ur-interest-card__btn--ghost" href="{{ url('/member/profile/'.$dataid) }}" target="_blank">
                                <i class="fa fa-id-card"></i> Full Profile
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ur-interests-empty"><i class="fa fa-inbox"></i> No one has expressed interest in this profile yet.</div>
            @endif
        </div>

        {{-- ================= SENT ================= --}}
        <div class="ur-interests-panel" id="ur_panel_sent">
            @if(!empty($members['sent']) && sizeof($members['sent'])>0)
                @foreach($members['sent'] as $dataid => $other)
                @php
                    $interest = $other->interest_back;
                    if ($interest==1) { $statusClass = "ur-interest-card__status--accepted"; $label = "ACCEPTED"; }
                    elseif ($interest==-1) { $statusClass = "ur-interest-card__status--declined"; $label = "DECLINED"; }
                    else { $statusClass = "ur-interest-card__status--pending"; $label = "PENDING"; }
                    $age = date_diff(date_create($other->birthday), date_create('now'))->y;
                @endphp
                <div class="ur-interest-card">
                    <a class="ur-interest-card__avatar" href="{{ url('/member/profile/'.$dataid) }}" target="_blank"
                       style="display:block; background-image: url('{{ User::retrieveUserObject($other->dataid)->getProfileImage() }}')"></a>
                    <div class="ur-interest-card__body">
                        <div class="ur-interest-card__head">
                            <h5 class="ur-interest-card__name">
                                <a href="{{ url('/member/profile/'.$dataid) }}" target="_blank">{{ $other->first_name }}</a>
                                <div class="ur-interest-card__id">Member ID: <a href="{{ url('/member/profile/'.$dataid) }}" target="_blank">{{ $dataid }}</a></div>
                            </h5>
                            <span class="ur-interest-card__status {{ $statusClass }}">{{ $label }}</span>
                        </div>
                        <div class="ur-interest-card__tags">
                            <span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>
                            <span class="ur-interest-card__tag">Height <b>{{ $other->height }}</b></span>
                            <span class="ur-interest-card__tag">Religion <b>{{ $other->lbl_religion ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Caste / Sect <b>{{ $other->lbl_caste ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Mother Tongue <b>{{ $other->lbl_mother_tongue ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Marital Status <b>{{ $other->lbl_marital_status ?: 'N/A' }}</b></span>
                            <span class="ur-interest-card__tag">Location <b>{{ $other->lbl_con_of_residence ?: 'N/A' }}</b></span>
                        </div>
                        <div class="ur-interest-card__actions">
                            <a class="ur-interest-card__btn ur-interest-card__btn--ghost" href="{{ url('/member/profile/'.$dataid) }}" target="_blank">
                                <i class="fa fa-id-card"></i> Full Profile
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ur-interests-empty"><i class="fa fa-paper-plane"></i> This member hasn't expressed interest in anyone yet.</div>
            @endif
        </div>
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
