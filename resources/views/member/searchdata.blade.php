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
<div class="member-results">
@foreach ($members as $member)
    <div class="member-card" id="block_{{$member->dataid}}">
        <div class="member-card__photo">
            <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <img src="{{ $member->getProfileImage() }}" alt="{{ $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='/images/{{ strtolower($member->gender ? $member->gender : 'male') }}_large.jpg';" />
            </a>
            @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))
                <span class="member-card__ribbon member-card__ribbon--new">New</span>
            @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))
                <span class="member-card__ribbon member-card__ribbon--updated">Updated</span>
            @endif
            <span class="member-card__id-badge">
                @auth
                    ID: {{$member->dataid}}
                @endauth
                @guest
                    <i class="fa fa-lock"></i> ID Hidden
                @endguest
            </span>
        </div>
        <div class="member-card__body">
            <h3 class="member-card__name">
                <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">{{$member->first_name}}</a>
            </h3>
            <ul class="member-card__quick">
                <li><i class="fa fa-birthday-cake"></i>{{date_diff(date_create($member->birthday), date_create('now'))->y}} yrs</li>
                <li><i class="fa fa-arrows-v"></i>{{$member->height}}</li>
                <li><i class="fa fa-map-marker"></i>{{$member->lbl_city}}</li>
            </ul>
            <ul class="member-card__details">
                <li><span>Religion</span><b>{{$member->lbl_religion}}</b></li>
                <li><span>Caste / Sect</span><b>{{$member->lbl_caste}} / {{$member->sect}}</b></li>
                <li><span>Mother Tongue</span><b>{{$member->lbl_mother_tongue}}</b></li>
                <li><span>Marital Status</span><b>{{$member->lbl_marital_status}}</b></li>
                <li><span>Education</span><b>{{$member->lbl_education}}</b></li>
                <li><span>Profession</span><b>{{$member->profession}}</b></li>
                <li><span>Location</span><b>{{$member->lbl_con_of_residence}}</b></li>
            </ul>
        </div>
        <div class="member-card__footer">
            <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <i class="fa fa-id-card"></i> Full Profile
            </a>
            @guest
                <a id="interest_{{$member->dataid}}" class="is-interest" onclick="return register_request();">
                    <i class="fa fa-heart"></i> <span>Express Interest</span>
                </a>
            @endguest
            @auth
                @if (User::retrieveUserObject()->inList($member->dataid, 'interest'))
                    @php
                        $interest = User::retrieveUserObject()->getInterest($member->dataid);
                    @endphp
                    <a id="interest_{{$member->dataid}}" class="is-interest" onclick="return {{$interest==-1? "false":"withdrawInterest($(this), 's')"}};">
                        @if ($interest==1)
                            <span class="c-green"><i class="fa fa-heart"></i> Interest Accepted</span>
                        @elseif ($interest==-1)
                            <span class="c-red"><i class="fa fa-heart"></i> Interest Declined</span>
                        @else
                            <span><i class="fa fa-heart"></i> Interest Expressed</span>
                        @endif
                    </a>
                @else
                    <a id="interest_{{$member->dataid}}" class="is-interest" onclick="return sendInterest($(this));">
                        <span><i class="fa fa-heart"></i> Express Interest</span>
                    </a>
                @endif
            @endauth
        </div>
    </div>
@endforeach
</div>
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
