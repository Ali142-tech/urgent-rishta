<?php use App\User; ?>
@extends('member.listing')
@section('photoaccess-data')
    <div class="col-12 text-center">
        <h2 class="heading heading-4 strong-400 mb-0">Received Photo Access Requests</h2>
    </div>
    @if(!empty($members) && !empty($members['received']) && sizeof($members['received'])>0)
        @foreach($members['received'] as $dataid => $member)
        <div class="block block--style-3 list z-depth-1-top" id="block_rec_{{$dataid}}">
            <div class="block-image">
                <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                    <div class="listing-image" style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper">
                <div class="heading heading-5 strong-500 mt-4 float-left">
                    <a href="{{url('/member/profile/'.$dataid)}}" target="_blank" class="c-base-1">
                        <div>{{ $member->first_name}}</div>
                    </a>
                </div>
                <div class="heading heading-xs strong-500 mt-4 text-uppercase padding-lr20 float-right center">
                    @php
                        $allowed = $member->allowed;
                        $class = null;
                        $label = null;
                        if ($allowed==1) {
                            $class = "btn-green";
                            $label = "GRANTED";
                        } else if ($allowed==-1) {
                            $class = "btn-red";
                            $label = "DECLINED";
                        } else {
                            $class = "btn-base-1";
                            $label = "PENDING";
                        }
                    @endphp
                    <span id="status_{{ $dataid }}" class="center btn btn-styled btn-shadow padding-lr20 {{$class}}">{{$label}}</span>
                </div>
                <table class="table-striped table-bordered mb-2 w100" style="font-size: 12px;">
                    <tbody>
                        <tr>
                            <td class="table-cell label font-dark"> Member ID </td>
                            <td class="table-cell w100 font-dark" colspan="3"><a href="{{url('/member/profile/'.$dataid)}}" target="_blank" class="c-base-1"> {{ $dataid }} </a></td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Age </td>
                            <td class="table-cell w50 font-dark">{{ date_diff(date_create($member->birthday), date_create('now'))->y }} </td>
                            <td class="table-cell label font-dark"> Height </td>
                            <td class="table-cell w50 font-dark">{{ $member->height }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Religion </td>
                            <td class="table-cell w50 font-dark">{{ $member->lbl_religion }}</td>
                            <td class="table-cell label font-dark"> Caste / Sect </td>
                            <td class="table-cell w50 font-dark">{{ $member->lbl_caste }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Mother Tongue </td>
                            <td class="table-cell font-dark">{{ $member->lbl_mother_tongue }}</td>
                            <td class="table-cell label font-dark"> Marital Status </td>
                            <td class="table-cell w50 font-dark">{{ $member->lbl_marital_status }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Location </td>
                            <td class="table-cell w100 font-dark" colspan="3">{{ $member->lbl_con_of_residence }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="block-footer b-xs-top">
                <div class="row align-items-center">
                    <div class="col-sm-12 text-center">
                        <ul class="inline-links inline-links--style-3">
                            <li class="listing-hover">
                                <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                                    <i class="fa fa-id-card"></i>Full Profile </a>
                            </li>
                            <li class="listing-hover">
                                @auth
                                @if (User::retrieveUserObject()->inList($dataid, 'interest'))
                                    @php
                                        $interest = User::retrieveUserObject()->getInterest($member->dataid);
                                    @endphp
                                    <a id="interest_{{$member->dataid}}" onclick="return false;">
                                        @if ($interest==1)
                                            <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                                        @elseif ($interest==-1)
                                            <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                                        @else
                                            <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed</span>
                                        @endif
                                    </a>
                                @endif
                                @endauth
                            </li>
                            <li class="listing-hover">
                                @guest
                                <a id="photoaccess_{{$dataid}}" title="Register to Request Photo Access" onclick="return register_request();">
                                    <span><i class="fa fa-lock"></i> Request Photo Access </span>
                                </a>
                                @endguest
                                @auth
                                @if($member->allowed==0)
                                <a id="photoaccess_{{$dataid}}_g" title="Grant Photo Access Request" onclick="return grantPhotoAccess($(this));">
                                    <span><i class="fa fa-check"></i> Grant Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_d" title="Decline Photo Access Request" onclick="return declinePhotoAccess($(this));">
                                    <span><i class="fa fa-times"></i> Decline Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_w" title="Withdraw Photo Access Request" style="display:none" onclick="return withdrawPhotoAccess($(this), 'withdraw', 'r');">
                                    <span><i class="fa fa-times"></i> Withdraw Photo Access Request </span>
                                </a>
                                @elseif($member->allowed==1)
                                <a id="photoaccess_{{$dataid}}_w" title="Withdraw Photo Access Request" onclick="return withdrawPhotoAccess($(this), 'r');">
                                    <span><i class="fa fa-times"></i> Withdraw Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_g" title="Grant Photo Access Request" style="display:none" onclick="return grantPhotoAccess($(this));">
                                    <span><i class="fa fa-check"></i> Grant Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_d" title="Decline Photo Access Request" style="display:none" onclick="return declinePhotoAccess($(this));">
                                    <span><i class="fa fa-times"></i> Decline Photo Access Request </span>
                                </a>
                                @else
                                <a id="photoaccess_{{$dataid}}_g" title="Grant Photo Access Request" style="display:none" onclick="return grantPhotoAccess($(this));">
                                    <span><i class="fa fa-check"></i> Grant Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_d" title="Decline Photo Access Request" style="display:none" onclick="return declinePhotoAccess($(this));">
                                    <span><i class="fa fa-times"></i> Decline Photo Access Request </span>
                                </a>
                            </li>
                            <li class="listing-hover">
                                <a id="photoaccess_{{$dataid}}_w" title="Withdraw Photo Access Request" onclick="return withdrawPhotoAccess($(this), 'withdraw', 'r');">
                                    <span><i class="fa fa-times"></i> Withdraw Photo Access Request </span>
                                </a>
                                @endif
                                @endauth
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
    <div class="block block--style-3 list z-depth-1-top">
        <i>No members found!!!</i>
    </div>
    @endif
    <div class="col-12 text-center">
        <h2 class="heading heading-4 strong-400 mb-0">Sent Photo Access Request</h2>
    </div>
    @if(!empty($members) && !empty($members['requested']) && sizeof($members['requested'])>0)
        @foreach($members['requested'] as $dataid => $member)
            <div class="block block--style-3 list z-depth-1-top" id="block_req_{{$dataid}}">
                <div class="block-image">
                    <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                        <div class="listing-image" style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"></div>
                    </a>
                </div>
                <div class="block-title-wrapper">
                    <div class="heading heading-5 strong-500 mt-4 float-left">
                        <a href="{{url('/member/profile/'.$dataid)}}" target="_blank" class="c-base-1">
                            <div>{{ $member->first_name}}</div>
                        </a>
                    </div>
                    <div class="heading heading-xs strong-500 mt-4 text-uppercase padding-lr20 float-right center">
                        @php
                            $allowed = $member->allowed;
                            $class = null;
                            $label = null;
                            if ($allowed==1) {
                                $class = "btn-green";
                                $label = "GRANTED";
                            } else if ($allowed==-1) {
                                $class = "btn-red";
                                $label = "DECLINED";
                            } else {
                                $class = "btn-base-1";
                                $label = "PENDING";
                            }
                        @endphp
                        <span class="center btn btn-styled btn-shadow padding-lr20 {{$class}}">{{$label}}</span>
                    </div>
                    <table class="table-striped table-bordered mb-2 w100" style="font-size: 12px;">
                        <tbody>
                            <tr>
                                <td class="table-cell label font-dark"> Member ID </td>
                                <td class="table-cell w100 font-dark" colspan="3"><a href="{{url('/member/profile/'.$dataid)}}" target="_blank" class="c-base-1"> {{ $dataid }} </a></td>
                            </tr>
                            <tr>
                                <td class="table-cell label font-dark"> Age </td>
                                <td class="table-cell w50 font-dark">{{ date_diff(date_create($member->birthday), date_create('now'))->y }} </td>
                                <td class="table-cell label font-dark"> Height </td>
                                <td class="table-cell w50 font-dark">{{ $member->height }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell label font-dark"> Religion </td>
                                <td class="table-cell w50 font-dark">{{ $member->lbl_religion }}</td>
                                <td class="table-cell label font-dark"> Caste / Sect </td>
                                <td class="table-cell w50 font-dark">{{ $member->lbl_caste }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell label font-dark"> Mother Tongue </td>
                                <td class="table-cell font-dark">{{ $member->lbl_mother_tongue }}</td>
                                <td class="table-cell label font-dark"> Marital Status </td>
                                <td class="table-cell w50 font-dark">{{ $member->lbl_marital_status }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell label font-dark"> Location </td>
                                <td class="table-cell w100 font-dark" colspan="3">{{ $member->lbl_con_of_residence }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="block-footer b-xs-top">
                    <div class="row align-items-center">
                        <div class="col-sm-12 text-center">
                            <ul class="inline-links inline-links--style-3">
                                <li class="listing-hover">
                                    <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                                        <i class="fa fa-id-card"></i>Full Profile </a>
                                </li>
                                <li class="listing-hover">
                                    @auth
                                    @if (User::retrieveUserObject()->inList($dataid, 'interest'))
                                        @php
                                            $interest = User::retrieveUserObject()->getInterest($member->dataid);
                                        @endphp
                                        <a id="interest_{{$member->dataid}}" onclick="return false;">
                                            @if ($interest==1)
                                                <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                                            @elseif ($interest==-1)
                                                <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                                            @else
                                                <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed</span>
                                            @endif
                                        </a>
                                    @endif
                                    @endauth
                                </li>
                                <li class="listing-hover">
                                    @guest
                                    <a id="photoaccess_{{$dataid}}" title="Register to Request Photo Access" onclick="return register_request();">
                                        <span><i class="fa fa-lock"></i> Request Photo Access </span>
                                    </a>
                                    @endguest
                                    @auth
                                    @if (User::retrieveUserObject()->inList($dataid, 'photoaccess'))
                                        @php
                                            $allowed = User::retrieveUserObject()->getPhotoAccess($member->dataid);
                                        @endphp
                                        <a id="photoaccess_{{$member->dataid}}_w" title="Withdraw Photo Access" onclick="return {{$allowed==-1? "false":"withdrawPhotoAccess($(this), 'u')"}};">
                                            @if ($allowed==1)
                                                <span class="c-green"><i class="fa fa-lock"></i> Photo Access Granted</span>
                                            @elseif ($allowed==-1)
                                                <span class="c-red"><i class="fa fa-lock"></i> Photo Access Declined</span>
                                            @else
                                                <span class="c-base-1"><i class="fa fa-lock"></i> Photo Access Requested</span>
                                            @endif
                                        </a>
                                    @else
                                    <a id="photoaccess_{{$dataid}}" title="Request Photo Access" onclick="return requestPhotoAccess($(this));">
                                        <span><i class="fa fa-lock"></i> Request Photo Access </span>
                                    </a>
                                    @endif
                                    @endauth
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
    <div class="block block--style-3 list z-depth-1-top">
        <i>No members found!!!</i>
    </div>
    @endif
@endsection
