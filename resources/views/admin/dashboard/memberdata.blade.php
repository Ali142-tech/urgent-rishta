@extends('admin.dashboard.profiles')
@section('member-data')

@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mb-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
<div class="ur-admin-summary">@if(!empty($pageSize)) Showing
    <span>{{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}</span>
    to <span>{{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}</span>
    @if (isset($resultCount)) from <span>{{ $resultCount }}</span> filtered members @endif
    out of <span>{{ $total }}</span> total members @endif</div>
@if(!empty($members) && sizeof($members)>0)
@foreach ($members as $member)
    <div class="ur-admin-card ur-admin-profile-card" id="block_{{$member->dataid}}">
        <div class="ur-admin-profile-card__media">
            <a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">
                <span class="ur-admin-thumb" style="background-image: url('{{ $member->getProfileImage() }}')"></span>
            </a>
        </div>
        <div class="ur-admin-profile-card__body">
            <div class="ur-admin-profile-card__head">
                <div>
                    <a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank" class="ur-admin-profile-card__name">
                        {{ $member->name }}
                        @if ($member->isAdmin()) <img src="/images/admin.png" style="width:auto; height:22px" /> @endif
                    </a>
                    <div class="ur-admin-profile-card__sub"><a href="mailto:{{$member->email}}">{{$member->email}}</a></div>
                    <div class="ur-admin-profile-card__sub"><b>Mobile:</b> {{$member->contact_mobile_number}} | <a href="{{ $member->user()->getWhatsappLink() }}" target="_blank">Send WhatsApp</a></div>
                </div>
                <div class="ur-admin-profile-card__badges">
                    <a id="package_{{$member->dataid}}" href="{{ url('admin/profile/package/'.$member->dataid) }}" class="ur-admin-profile-card__package">
                        @if (!empty($member->package))
                        @if($member->package==99)
                        <span class="ur-admin-badge ur-admin-badge--neutral">All Profiles</span>
                        @else
                        <img src="/images/package_{{$member->package}}.png" alt="{{$member->lbl_package}}" title="{{$member->lbl_package}}" />
                        @endif
                        @else
                        <span class="ur-admin-badge ur-admin-badge--neutral">Unassigned</span>
                        @endif
                    </a>
                    @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))
                        <span class="ur-admin-profile-card__flag"><img src="/images/new.png" /></span>
                    @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))
                        <span class="ur-admin-profile-card__flag"><img src="/images/updated.png" /></span>
                    @endif
                </div>
            </div>

            <table class="ur-admin-mini-table">
                <tbody>
                    <tr>
                        <td colspan="2" class="ur-admin-mini-table__meta">Created: {{ $member->created_at->format('d/m/Y') }}</td>
                        <td colspan="2" class="ur-admin-mini-table__meta">Updated: {{ $member->updated_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="ur-admin-mini-table__status">
                            <a id="active_label_{{$member->dataid}}" class="ur-admin-badge {{ $member->active==0 ? 'ur-admin-badge--warning' : 'ur-admin-badge--success' }}" onclick="return toggleActive('{{$member->dataid}}');">{{ $member->getActiveLabel() }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Member ID</b></td>
                        <td colspan="3"><a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">{{$member->dataid}}</a></td>
                    </tr>
                    <tr>
                        <td><b>Age</b></td>
                        <td>{{date_diff(date_create($member->birthday), date_create('now'))->y}}</td>
                        <td><b>Height</b></td>
                        <td>{{$member->height}}</td>
                    </tr>
                    <tr>
                        <td><b>Religion</b></td>
                        <td>{{$member->lbl_religion}}</td>
                        <td><b>Caste / Sect</b></td>
                        <td>{{$member->lbl_caste}}</td>
                    </tr>
                    <tr>
                        <td><b>Mother Tongue</b></td>
                        <td>{{$member->lbl_mother_tongue}}</td>
                        <td><b>Marital Status</b></td>
                        <td>{{$member->lbl_marital_status}}</td>
                    </tr>
                    <tr>
                        <td><b>Education</b></td>
                        <td>{{$member->lbl_education}}</td>
                        <td><b>Profession</b></td>
                        <td>{{$member->profession}}</td>
                    </tr>
                    <tr>
                        <td><b>City</b></td>
                        <td>{{$member->lbl_city}}</td>
                        <td><b>Location</b></td>
                        <td>{{$member->lbl_city}} {{$member->lbl_con_of_residence}}</td>
                    </tr>
                </tbody>
            </table>

            <div class="ur-admin-card__actions">
                <a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">
                    <i class="fa fa-id-card"></i> Full Profile
                </a>
                <a id="interest_a_'{{$member->dataid}}'" href="{{ url('admin/profile/listing/interests/'.$member->dataid) }}">
                    <span id="interest_'{{$member->dataid}}'"><i class="fa fa-heart"></i> View Interests</span>
                </a>
                <a onclick="return toggleActive($(this), '{{$member->dataid}}');">
                    <span id="active_{{$member->dataid}}" class="{{$member->active==0 ? '':'is-active'}}">
                        <i class="fa fa-toggle-{{$member->active==0 ? 'off':'on'}}"></i> Make {{$member->active==0 ? 'Active':'Inactive'}}
                    </span>
                </a>
                <a href="{{ url('admin/profile/package/'.$member->dataid) }}">
                    <span id="package_'{{$member->dataid}}'"><i class="fa fa-archive"></i> Change Package</span>
                </a>
                <a onclick="return resendVerificationEmail($(this), '{{$member->dataid}}');">
                    <span id="email_'{{$member->dataid}}'"><i class="fa fa-envelope"></i> Resend Verification Email</span>
                </a>
                <a onclick="return sendPasswordResetEmail($(this), '{{$member->dataid}}');">
                    <span id="reset_'{{$member->dataid}}'"><i class="fa fa-unlock"></i> Password Reset</span>
                </a>
                <a class="is-danger" onclick="return deleteProfile($(this), '{{$member->dataid}}');">
                    <span id="delete_'{{$member->dataid}}'"><i class="fa fa-trash"></i> Delete Profile</span>
                </a>
                <a href="{{ url('admin/profile/pdf/'.$member->dataid) }}"><i class="fa fa-download"></i> Download User Data (PDF)</a>
            </div>
        </div>
    </div>
@endforeach
@else
<div class="ur-admin-empty"><i class="fa fa-users"></i> No members found.</div>
@endif
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mt-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=($currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
@endsection
