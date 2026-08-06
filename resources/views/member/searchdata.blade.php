<?php use App\User; ?>
@extends('member.searchresults')
@section('search-data')

<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
            @endif
        </ul>
    </div>
</div>
<div class="block-footer b-xs-top" style="margin: 20px 0;">@if(!empty($pageSize)) Showing
    {{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}
    to {{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}
    <span>@if (isset($resultCount)) from {{ $resultCount }} filtered members @endif</span>
    out of {{ $total }} total members @endif</div>
@if(!empty($members) && sizeof($members)>0)
@foreach ($members as $member)
    <div class="block block--style-3 list z-depth-1-top" id="block_{{$member->dataid}}">
        <div class="block-image">
            <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <div class="listing-image" style="background-image: url('{{ $member->getProfileImage() }}')"></div>
            </a>
        </div>
        <div class="block-title-wrapper">
            <h3 class="heading heading-5 strong-500 mt-4">
                <span><a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest" class="c-base-1">{{$member->first_name}}</a></span>
                @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))<span class="float-right"><img src="/images/new.png" style="width:auto; height:30px" /></span>
                @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))<span class="float-right"><img src="/images/updated.png" style="width:auto; height:30px" /></span> @endif
            </h3>
            <h4 class="heading heading-xs c-gray-light text-uppercase strong-400"></h4>
            <table class="table-striped table-bordered mb-2" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <td height="30" style="padding-left: 5px;" class="font-dark"><b>Member ID</b></td>
                        <td height="30" style="padding-left: 5px;" class="font-dark" colspan="3"><a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest" class="c-base-1"><b>@auth {{$member->dataid}} @endauth @guest <img style="width:83px; height:18px" src="/images/blur_id.png" /> @endguest</b></a></td>
                    </tr>
                    <tr>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Age</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{date_diff(date_create($member->birthday), date_create('now'))->y}}</td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Height</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->height}}</td>
                    </tr>
                    <tr>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Religion</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_religion}}</td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Caste / Sect</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_caste}} / {{$member->sect}}</td>
                    </tr>
                    <tr>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Mother Tongue</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_mother_tongue}}</td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Marital Status</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_marital_status}}</td>
                    </tr>
                    <tr>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Education</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_education}}</td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Profession</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->profession}}</td>
                    </tr>
                    <tr>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>City</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_city}}</td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark"><b>Location</b></td>
                        <td width="120" height="30" style="padding-left: 5px;" class="font-dark">{{$member->lbl_con_of_residence}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="block-footer b-xs-top">
            <div class="row align-items-center">
                <div class="col-sm-12 text-center">
                    <ul class="inline-links inline-links--style-3">
                        <li class="listing-hover">
                            <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                                <i class="fa fa-id-card"></i>Full Profile </a>
                        </li>
                        <li class="listing-hover">
                            @guest
                                <a id="interest_{{$member->dataid}}" onclick="return register_request();">
                                    <span><i class="fa fa-heart"></i> Express Interest </span>
                                </a>
                            @endguest
                            @auth
                                @if (User::retrieveUserObject()->inList($member->dataid, 'interest'))
                                    @php
                                        $interest = User::retrieveUserObject()->getInterest($member->dataid);
                                    @endphp
                                    <a id="interest_{{$member->dataid}}" onclick="return {{$interest==-1? "false":"withdrawInterest($(this), 's')"}};">
                                        @if ($interest==1)
                                            <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                                        @elseif ($interest==-1)
                                            <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                                        @else
                                            <span class="c-base-1"><i class="fa fa-heart"></i> Interest Expressed</span>
                                        @endif
                                    </a>
                                @else
                                    <a id="interest_{{$member->dataid}}" onclick="return sendInterest($(this));">
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
<div class="row">
    <div class="col-sm-12 col-md-6">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=($currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
            @endif
        </ul>
    </div>
</div>
@endsection
