<?php use App\User; ?>
@extends('member.listing')
@push('styles')
<link rel="stylesheet" href="/css/ur-interests.css?1">
@endpush
@section('interest-data')

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
        @if(!empty($members) && !empty($members['received']) && sizeof($members['received'])>0)
            @foreach($members['received'] as $dataid => $member)
            @php
                $interest = $member->interest_back;
                if ($interest==1) { $class = "btn-green"; $label = "ACCEPTED"; $statusClass = "ur-interest-card__status--accepted"; }
                else if ($interest==-1) { $class = "btn-red"; $label = "DECLINED"; $statusClass = "ur-interest-card__status--declined"; }
                else { $class = "btn-base-1"; $label = "PENDING"; $statusClass = "ur-interest-card__status--pending"; }
                $age = date_diff(date_create($member->birthday), date_create('now'))->y;
            @endphp
            <div class="ur-interest-card" id="block_rec_{{$dataid}}">
                <div class="ur-interest-card__avatar"
                     style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"
                     onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest"></div>
                <div class="ur-interest-card__body">
                    <div class="ur-interest-card__head">
                        <h5 class="ur-interest-card__name">
                            <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $member->first_name }}</a>
                            <div class="ur-interest-card__id">Member ID: <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $dataid }}</a></div>
                        </h5>
                        <span id="status_{{ $dataid }}" class="ur-interest-card__status {{ $statusClass }} {{ $class }}">{{ $label }}</span>
                    </div>
                    <div class="ur-interest-card__tags">
                        <span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>
                        <span class="ur-interest-card__tag">Height <b>{{ $member->height }}</b></span>
                        <span class="ur-interest-card__tag">Religion <b>{{ $member->lbl_religion ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Caste / Sect <b>{{ $member->lbl_caste ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Mother Tongue <b>{{ $member->lbl_mother_tongue ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Marital Status <b>{{ $member->lbl_marital_status ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Location <b>{{ $member->lbl_con_of_residence ?: 'N/A' }}</b></span>
                    </div>
                    <div class="ur-interest-card__actions">
                        <a class="ur-interest-card__btn ur-interest-card__btn--ghost"
                           onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                            <i class="fa fa-id-card"></i> Full Profile
                        </a>
                        @guest
                        <a class="ur-interest-card__btn ur-interest-card__btn--accept" id="interest_{{$dataid}}" title="Register to Express Interest" onclick="return register_request();">
                            <i class="fa fa-heart"></i> Express Interest
                        </a>
                        @endguest
                        @auth
                        @if($member->interest_back==0)
                        <a class="ur-interest-card__btn ur-interest-card__btn--accept" id="interest_{{$dataid}}_a" title="Accept Interest" onclick="return acceptInterest($(this));">
                            <i class="fa fa-check"></i> Accept
                        </a>
                        <a class="ur-interest-card__btn ur-interest-card__btn--decline" id="interest_{{$dataid}}_d" title="Decline Interest" onclick="return declineInterest($(this));">
                            <i class="fa fa-times"></i> Decline
                        </a>
                        <a class="ur-interest-card__btn ur-interest-card__btn--withdraw" id="interest_{{$dataid}}_w" title="Withdraw Interest" style="display:none" onclick="return withdrawInterest($(this), 'r');">
                            <i class="fa fa-times"></i> Withdraw
                        </a>
                        @elseif($member->interest_back==1)
                        <a class="ur-interest-card__btn ur-interest-card__btn--withdraw" id="interest_{{$dataid}}_w" title="Withdraw Interest" onclick="return withdrawInterest($(this), 'r');">
                            <i class="fa fa-times"></i> Withdraw
                        </a>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="ur-interests-empty"><i class="fa fa-inbox"></i>No one has expressed interest in your profile yet.</div>
        @endif
    </div>

    {{-- ================= SENT ================= --}}
    <div class="ur-interests-panel" id="ur_panel_sent">
        @if(!empty($members) && !empty($members['sent']) && sizeof($members['sent'])>0)
            @foreach($members['sent'] as $dataid => $member)
            @php
                $interest = $member->interest_back;
                if ($interest==1) { $class = "btn-green"; $label = "ACCEPTED"; $statusClass = "ur-interest-card__status--accepted"; }
                else if ($interest==-1) { $class = "btn-red"; $label = "DECLINED"; $statusClass = "ur-interest-card__status--declined"; }
                else { $class = "btn-base-1"; $label = "PENDING"; $statusClass = "ur-interest-card__status--pending"; }
                $age = date_diff(date_create($member->birthday), date_create('now'))->y;
            @endphp
            <div class="ur-interest-card" id="block_sent_{{$dataid}}">
                <div class="ur-interest-card__avatar"
                     style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"
                     onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest"></div>
                <div class="ur-interest-card__body">
                    <div class="ur-interest-card__head">
                        <h5 class="ur-interest-card__name">
                            <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $member->first_name }}</a>
                            <div class="ur-interest-card__id">Member ID: <a href="{{url('/member/profile/'.$dataid)}}" target="_blank">{{ $dataid }}</a></div>
                        </h5>
                        <span id="status_{{ $dataid }}" class="ur-interest-card__status {{ $statusClass }} {{ $class }}">{{ $label }}</span>
                    </div>
                    <div class="ur-interest-card__tags">
                        <span class="ur-interest-card__tag">Age <b>{{ $age }}</b></span>
                        <span class="ur-interest-card__tag">Height <b>{{ $member->height }}</b></span>
                        <span class="ur-interest-card__tag">Religion <b>{{ $member->lbl_religion ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Caste / Sect <b>{{ $member->lbl_caste ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Mother Tongue <b>{{ $member->lbl_mother_tongue ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Marital Status <b>{{ $member->lbl_marital_status ?: 'N/A' }}</b></span>
                        <span class="ur-interest-card__tag">Location <b>{{ $member->lbl_con_of_residence ?: 'N/A' }}</b></span>
                    </div>
                    <div class="ur-interest-card__actions">
                        <a class="ur-interest-card__btn ur-interest-card__btn--ghost"
                           onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                            <i class="fa fa-id-card"></i> Full Profile
                        </a>
                        @guest
                        <a class="ur-interest-card__btn ur-interest-card__btn--accept" id="interest_{{$dataid}}" title="Register to Express Interest" onclick="return register_request();">
                            <i class="fa fa-heart"></i> Express Interest
                        </a>
                        @endguest
                        @auth
                        @if (User::retrieveUserObject()->inList($dataid, 'interest'))
                            @php $interest_back = User::retrieveUserObject()->getInterest($member->dataid); @endphp
                            <a class="ur-interest-card__btn {{ $interest_back==-1 ? 'ur-interest-card__btn--expressed' : 'ur-interest-card__btn--withdraw' }}"
                               id="interest_{{$member->dataid}}_w" title="Withdraw Interest"
                               onclick="return {{$interest_back==-1? "false":"withdrawInterest($(this), 's')"}};">
                                @if ($interest_back==1)
                                    <i class="fa fa-heart"></i> Interest Accepted
                                @elseif ($interest_back==-1)
                                    <i class="fa fa-heart"></i> Interest Declined
                                @else
                                    <i class="fa fa-times"></i> Withdraw
                                @endif
                            </a>
                        @else
                        <a class="ur-interest-card__btn ur-interest-card__btn--accept" id="interest_{{$dataid}}" title="Express Interest" onclick="return sendInterest($(this));">
                            <i class="fa fa-heart"></i> Express Interest
                        </a>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="ur-interests-empty"><i class="fa fa-paper-plane"></i>You haven't expressed interest in anyone yet.</div>
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
