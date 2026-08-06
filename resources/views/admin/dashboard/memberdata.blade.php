@extends('admin.dashboard.profiles')
@section('member-data')

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
    <span>@if (isset($resultCount)) from {{ $resultCount }} filtered members2 @endif</span>
    out of {{ $total }} total members @endif</div>
@if(!empty($members) && sizeof($members)>0)
@foreach ($members as $member)
    <div class="block block--style-3 list z-depth-1-top" id="block_{{$member->dataid}}">
        <div class="block-image">
            <a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">
                <div class="listing-image" style="background-image: url('{{ $member->getProfileImage() }}')"></div>
            </a>
        </div>
        <div class="block-title-wrapper" style="width: 80%">
            <div class="heading heading-5 strong-500 mt-4 d-inline-block w100">
                <div class="float-left" style="display: inline-block">
                    <div><a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank" class="c-base-1">{{ $member->name }}</a> @if ($member->isAdmin()) <img src="/images/admin.png" style="width:auto; height:30px" /> @endif</div>
                    <div style="font-size: 12px;"><a href="mailto:{{$member->email}}">{{$member->email}}</a></div>
                    <div style="font-size: 12px;"><b>Mobile:</b> {{$member->contact_mobile_number}} | <a href="{{ $member->user()->getWhatsappLink() }}" target="_blank">Send WhatsApp</a></div>
                </div>
                <div class="float-right" style="display: inline-block">
                    <div>
                    <a id="package_{{$member->dataid}}" onclick="return renderChangePackageModal('{{$member->dataid}}');" class="c-base-1">
                        @if (!empty($member->package))
                        @if($member->package==99)
                        All Profiles
                        @else
                        <img src="/images/package_{{$member->package}}.png" alt="{{$member->lbl_package}}" title="{{$member->lbl_package}}" style="height:70px; width: 70px" />
                        @endif
                        @else
                        Package Unassigned
                        @endif
                    </a></div>
                    <div>
                    @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))<span class="float-right"> <img src="/images/new.png" style="width:auto; height:30px" /></span>
                    @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))<span class="float-right"> <img src="/images/updated.png" style="width:auto; height:30px" /></span> @endif
                    </div>
                </div>
            </div>
            <div class="w100" style="display: inline-block">
                <table class="table-striped table-bordered mb-2 w100" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <td colspan="2" class="center"><i>Profile Created On:</i> {{ $member->created_at->format('d/m/Y') }}</td>
                            <td colspan="2" class="center"><i>Profile Last Updated:</i> {{ $member->updated_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr><td colspan="4"><span class="d-flex justify-content-center btn-base-1 c-light-grey text-uppercase strong-400"><a id="active_label_{{$member->dataid}}" onclick="return toggleActive('{{$member->dataid}}');">{{ $member->getActiveLabel() }}</a></span></td></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Member ID</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark" colspan="3"><a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank" class="c-base-1"> {{$member->dataid}} </a></td>
                        </tr>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Age</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{date_diff(date_create($member->birthday), date_create('now'))->y}}</td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Height</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->height}}</td>
                        </tr>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Religion</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_religion}}</td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Caste / Sect</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_caste}}</td>
                        </tr>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Mother Tongue</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_mother_tongue}}</td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Marital Status</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_marital_status}}</td>
                        </tr>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Education</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_education}}</td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Profession</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->profession}}</td>
                        </tr>
                        <tr>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>City</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_city}}</td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark"><b>Location</b></td>
                            <td height="30" style="padding-left: 5px;" class="nowrap font-dark">{{$member->lbl_city}} {{$member->lbl_con_of_residence}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="block-footer b-xs-top">
            <div class="align-items-center">
                <div class="col-sm-12 text-center">
                    <ul class="inline-links inline-links--style-3">
                        <li class="listing-hover">
                            <a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">
                                <i class="fa fa-id-card"></i>Full Profile </a>
                        </li>
                        <li class="listing-hover">
                            <a id="interest_a_'{{$member->dataid}}'" onclick="return renderListingModal('interests', '{{$member->dataid}}');">
                                <span id="interest_'{{$member->dataid}}'" class="">
                                    <i class="fa fa-heart"></i> View Interests </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                            <a onclick="return toggleActive($(this), '{{$member->dataid}}');">
                                <span id="active_{{$member->dataid}}" class="{{$member->active==0 ? '':'c-base-1'}}">
                                    <i class="fa fa-toggle-{{$member->active==0 ? 'off':'on'}}"></i> <span>Make {{$member->active==0 ? 'Active':'Inactive'}}</span> </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                            <a onclick="return renderUpdatePackageModal('{{$member->dataid}}');">
                                <span id="package_'{{$member->dataid}}'" class="">
                                    <i class="fa fa-archive"></i> Change Package </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                            <a onclick="return resendVerificationEmail($(this), '{{$member->dataid}}');">
                                <span id="email_'{{$member->dataid}}'" class="">
                                    <i class="fa fa-envelope"></i> Resend Verification Email </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                            <a onclick="return sendPasswordResetEmail($(this), '{{$member->dataid}}');">
                                <span id="reset_'{{$member->dataid}}'" class="">
                                    <i class="fa fa-unlock"></i> Password Reset </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                            <a onclick="return deleteProfile($(this), '{{$member->dataid}}');">
                                <span id="delete_'{{$member->dataid}}'" class="">
                                    <i class="fa fa-trash"></i> Delete Profile </span>
                            </a>
                        </li>
                        <li class="listing-hover">
                          <a >
                            <i class="fa fa-download"></i> Download User Data (PDF)
                        </a>

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
