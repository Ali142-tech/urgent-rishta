<?php use App\User; ?>
@extends('member.listing')
@section('interest-data')

<style>
    * {
    padding: 0;
    margin: 0;
}

img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.client {
    display: flex;
    justify-content: space-between;
}
.client {
    border: 1px solid #d15e72;
    padding: 15px;
    margin: 10px 30px;
    border-radius: 10px;
}
.client-img {
    width: 18%;
    position: relative;
}

.img-div img{
    border-radius: 15px;
}
span.selected-packge {
    position: absolute;
    bottom: 20px;
    left: 10px;
    background: #e32b4b;
    padding: 4px 8px;
    border-radius: 10px;
    color: white;
}

.client-details {
    width: 58%;
    position: relative;
}
.client-details h5 {
    font-size: 20px;
    margin-bottom: 8px;
}
ol.poi {
    margin-left: 25px;
    margin-bottom: 10px;
    list-style: square;
    display: flex;
    flex-wrap: wrap;
    column-gap: 40px;
    row-gap: 8px;
}
.client-actions {
    width: 18%;
}
.client-actions ul{
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.client-actions ul li {
    list-style: none;
}
a.accept {
    background-color: green;
    border: 2px solid green;
    color: white;
    padding: 8px;
    border-radius: 20px;
}
a.accept:hover{
    background-color: white;
    color: green;
}
a.denay {
    border: 2px solid red;
    color: red;
    padding: 8px;
    border-radius: 20px;
}
a.denay:hover {
    color: white;
    background-color: red;
}
a.cta-5 {

    bottom: 0;
    position: absolute;
    padding: 8px;
    border: 1px solid black;
    text-decoration: none;
    color: black;
    border-radius: 6px;
    gap: 12px;
    display: flex;
    align-items: center;
}
a.cta-5:hover {
    color: white;
    background-color: black;
}
@media screen and (min-width: 540px) and (max-width: 780px) {
    a.accept, a.denay {
        font-size: x-small;
    }
    a.cta-5 {
        right: -115px;
        font-size: x-small;
        bottom: 12px;
    }
}
@media screen and (max-width: 540px) {
    .client {
    flex-direction: column;
            align-items: center;
                    gap: 20px;
    }
    .client-img {
    width: 90%;

}
.client-details {
    width: 100%;
}
.client-actions {
    width: 100%;
    /* display: flex; */
}
a.accept, a.denay {
        font-size: x-small;
    }
    a.cta-5 {
    position: relative;
}
}
</style>
    <div class="col-12 text-center">
        <h2 class="heading heading-4 strong-400 mb-0">Received Interests</h2>
    </div>
    @if(!empty($members) && !empty($members['received']) && sizeof($members['received'])>0)
        @foreach($members['received'] as $dataid => $member)
        <div class="main-all-intrestes" id="block_rec_{{$dataid}}">
        <div class="client" >
            <div class="client-img">
                <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                    <div class="listing-image" style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"></div>
                </a>
                <div class="heading heading-xs strong-500 mt-4 text-uppercase padding-lr20 float-right center">
                    @php
                        $interest = $member->interest_back;
                        $class = null;
                        $label = null;
                        if ($interest==1) {
                            $class = "btn-green";
                            $label = "ACCEPTED";
                        } else if ($interest==-1) {
                            $class = "btn-red";
                            $label = "DECLINED";
                        } else {
                            $class = "btn-base-1";
                            $label = "PENDING";
                        }
                    @endphp
                    <span id="status_{{ $dataid }}" class="selected-packge center btn btn-styled btn-shadow padding-lr20 {{$class}}">{{$label}}</span>
                </div>
            </div>
            <div class="client-details">
                <h5>{{ $member->first_name}}</h5>
                <ol class="poi">
                    <li>Member Id: <strong><a href="{{url('/member/profile/'.$dataid)}}" target="_blank"
                                class="c-base-1"> {{ $dataid }} </a></strong></li>
                    <li>Age: <strong>{{ date_diff(date_create($member->birthday), date_create('now'))->y }}</strong>
                    </li>
                    <li>Height: <strong>{{ $member->height }}</strong></li>
                    <li>Religion: <strong>{{ $member->lbl_religion }}</strong></li>
                    <li>Caste / Sect: <strong>{{ $member->lbl_caste }}</strong></li>
                    <li>Mother Tongue: <strong>{{ $member->lbl_mother_tongue }}</strong></li>
                    <li>Marital Status: <strong>{{ $member->lbl_marital_status }}</strong></li>
                    
                </ol>
                <ol class="poi poi-date">
                    <li>Location: <strong>{{ $member->lbl_con_of_residence }}</strong></li>
                </ol>
                <a class="cta-5"
                    onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                    <i class="fa fa-id-card"></i>Full Profile </a>
            </div>
            <div class="client-actions">
                <ul>
                    <li>
                        @guest
                        <a id="interest_{{$dataid}}" title="Register to Express Interest"
                            onclick="return register_request();">
                            <span><i class="fa fa-heart"></i> Express Interest </span>
                        </a>
                        @endguest
                        @auth
                        @if($member->interest_back==0)
                        <a class="accept" id="interest_{{$dataid}}_a" title="Accept Interest"
                            onclick="return acceptInterest($(this));">
                            <span><i class="fa fa-check"></i> Accept Interest </span>
                        </a>
                    </li>
                    <li>
                        <a class="denay" id="interest_{{$dataid}}_d" title="Decline Interest" onclick="return declineInterest($(this));">
                            <span><i class="fa fa-times"></i> Decline Interest </span>
                        </a>
                    </li>
                    <li>
                        <a id="interest_{{$dataid}}_w" title="Withdraw Interest" style="display:none" onclick="return withdrawInterest($(this), 'withdraw', 'r');">
                            <span><i class="fa fa-times"></i> Withdraw Interest </span>
                        </a>
                        @elseif($member->interest_back==1)
                        <a id="interest_{{$dataid}}_w" title="Withdraw Interest" onclick="return withdrawInterest($(this), 'r');">
                            <span><i class="fa fa-times"></i> Withdraw Interest </span>
                        </a>
                    </li>
                    <li>
                        <a id="interest_{{$dataid}}_w" title="Withdraw Interest" onclick="return withdrawInterest($(this), 'withdraw', 'r');">
                            <span><i class="fa fa-times"></i> Withdraw Interest </span>
                        </a>
                        @endif
                        @endauth
                    </li>
                </ul>
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
        <h2 class="heading heading-4 strong-400 mb-0">Sent Interests</h2>
    </div>
    @if(!empty($members) && !empty($members['sent']) && sizeof($members['sent'])>0)
        @foreach($members['sent'] as $dataid => $member)
        <div class="block block--style-3 list z-depth-1-top" id="block_sent_{{$dataid}}">
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
                        $interest = $member->interest_back;
                        $class = null;
                        $label = null;
                        if ($interest==1) {
                            $class = "btn-green";
                            $label = "ACCEPTED";
                        } else if ($interest==-1) {
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
                                @guest
                                <a id="interest_{{$dataid}}" title="Register to Express Interest" onclick="return register_request();">
                                    <span><i class="fa fa-heart"></i> Express Interest </span>
                                </a>
                                @endguest
                                @auth
                                @if (User::retrieveUserObject()->inList($dataid, 'interest'))
                                    @php
                                        $interest_back = User::retrieveUserObject()->getInterest($member->dataid);
                                    @endphp
                                    <a id="interest_{{$member->dataid}}_w" title="Withdraw Interest" onclick="return {{$interest_back==-1? "false":"withdrawInterest($(this), 's')"}};">
                                        @if ($interest_back==1)
                                            <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                                        @elseif ($interest_back==-1)
                                            <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                                        @else
                                            <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed</span>
                                        @endif
                                    </a>
                                @else
                                <a id="interest_{{$dataid}}" title="Express Interest" onclick="return sendInterest($(this));">
                                    <span><i class="fa fa-heart"></i> Express Interest </span>
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
