<?php use App\User; ?>
@extends('member.listing')
@section('filtered-data')
    @if(!empty($members) && sizeof($members)>0)
        @foreach($members as $dataid => $member)
        <div class="block block--style-3 list z-depth-1-top" id="block_{{$dataid}}">
            <div class="block-image">
                <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest">
                    <div class="listing-image" style="background-image: url('{{ User::retrieveUserObject($member->dataid)->getProfileImage() }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper">
                <h3 class="heading heading-5 strong-500 mt-4">
                    <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest" class="c-base-1">{{$member['first_name']}}</a>
                </h3>
                <h4 class="heading heading-xs c-gray-light text-uppercase strong-400"></h4>
                <table id="datatable" class="table-striped table-bordered mb-2" style="font-size: 12px;">
                    <tbody>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="font-dark"><b>Member ID</b></td>
                            <td height="30" style="padding-left: 5px;" class="font-dark" colspan="3"><a onclick="javascript:@auth window.open('{{url('/member/profile/'.$dataid)}}'); @endauth @guest return register_request(); @endguest" class="c-base-1"><b>@auth {{$dataid}} @endauth @guest <img style="width:83px; height:18px" src="/images/blur_id.png" /> @endguest</b></a></td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Age </td>
                            <td class="table-cell w50 font-dark">{{ date_diff(date_create($member['birthday']), date_create('now'))->y }} </td>
                            <td class="table-cell label font-dark"> Height </td>
                            <td class="table-cell w50 font-dark">{{ $member['height'] }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Religion </td>
                            <td class="table-cell w50 font-dark">{{ $member['lbl_religion'] }}</td>
                            <td class="table-cell label font-dark"> Caste / Sect </td>
                            <td class="table-cell w50 font-dark">{{ $member['lbl_caste'] }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Mother Tongue </td>
                            <td class="table-cell font-dark">{{ $member['lbl_mother_tongue'] }}</td>
                            <td class="table-cell label font-dark"> Marital Status </td>
                            <td class="table-cell w50 font-dark">{{ $member['lbl_marital_status'] }}</td>
                        </tr>
                        <tr>
                            <td class="table-cell label font-dark"> Location </td>
                            <td class="table-cell w100 font-dark" colspan="3">{{ $member['lbl_con_of_residence'] }}</td>
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
                                <a id="interest_{{$dataid}}" onclick="return register_request();">
                                    <span><i class="fa fa-heart"></i> Express Interest </span>
                                </a>
                                @endguest
                                @auth
                                @if (User::retrieveUserObject()->inList($dataid, 'interest'))
                                <a id="interest_{{$dataid}}" onclick="return withdrawInterest($(this), 's');">
                                    <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed </span>
                                </a>
                                @else
                                <a id="interest_{{$dataid}}" onclick="return sendInterest($(this));">
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
