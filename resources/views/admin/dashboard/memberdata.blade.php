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
        {{-- Mobile only (see ur-admin.css responsive block) — reuses the
             member-card__* classes directly (NOT the outer <div class="member-card">
             wrapper, which is what carries its own border/shadow box) so this
             gets the same rich visual as the live site card without nesting
             a second box inside .ur-admin-card. Only "Full Profile" in the
             footer — no Express Interest, and it goes to the admin preview
             page, not the public one. --}}
        <div class="ur-admin-mobile-card">
            <div class="member-card__photo">
                <span class="member-card__photo-bg" style="background-image:url('{{ $member->getProfileImage() }}')"></span>
                <img src="{{ $member->getProfileImage() }}" alt="{{ $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';" />
                @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))
                    <span class="member-card__ribbon member-card__ribbon--new">New</span>
                @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))
                    <span class="member-card__ribbon member-card__ribbon--updated">Updated</span>
                @endif
                @if($member->photo_verification_status === 'verified')
                    <span class="member-card__badge--verified"><i class="fa fa-check-circle"></i> Verified <i class="fa fa-shield"></i></span>
                @endif
                @if(!empty($member->lbl_package))
                    @php
                        $packageSlug = \Illuminate\Support\Str::slug($member->lbl_package);
                        $packageIcon = match ($packageSlug) {
                            'platinum' => '<i class="fa fa-star"></i>',
                            'diamond' => '<i class="fa fa-diamond"></i>',
                            'royal', 'imperial' => '&#128081;',
                            default => '<i class="fa fa-diamond"></i>',
                        };
                    @endphp
                    <span class="member-card__badge--package member-card__badge--package-{{ $packageSlug }}">
                        {!! $packageIcon !!} {{ $member->lbl_package }}
                    </span>
                @endif
                <span class="member-card__id-badge">ID: {{ $member->dataid }}</span>
            </div>
            <div class="member-card__body">
                <h3 class="member-card__name">{{ $member->first_name }}</h3>
                <ul class="member-card__quick">
                    @if(!empty($member->birthday))<li><i class="fa fa-birthday-cake"></i>{{ date_diff(date_create($member->birthday), date_create('now'))->y }} yrs</li>@endif
                    @if(!empty($member->height))<li><i class="fa fa-arrows-v"></i>{{ $member->height }}</li>@endif
                    @if(!empty($member->lbl_city))
                    <li>
                        <i class="fa fa-map-marker"></i>{{ $member->lbl_city }}
                        @if(!empty($member->con_of_residence_code))
                            <img class="member-card__flag" src="{{ \App\Profile::countryFlagUrl($member->con_of_residence_code) }}" alt="" loading="lazy" onerror="this.style.display='none';">
                        @endif
                    </li>
                    @endif
                </ul>
                @if(!empty($member->profession))
                    <div class="member-card__designation"><i class="fa fa-briefcase"></i> {{ $member->profession }}</div>
                @endif
                <ul class="member-card__details">
                    <li><span>Religion</span><b>{{ $member->lbl_religion }}</b></li>
                    <li><span>Caste / Sect</span><b>{{ $member->lbl_caste }} / {{ $member->sect }}</b></li>
                    <li><span>Marital Status</span><b>{{ $member->lbl_marital_status }}</b></li>
                </ul>
            </div>
            <div class="member-card__footer">
                <a href="{{ url('admin/profile/preview/'.$member->dataid) }}?page={{ $currentPage }}">
                    <i class="fa fa-id-card"></i> Full Profile
                </a>
            </div>
        </div>

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
                    <a id="package_{{$member->dataid}}" href="{{ url('admin/profile/package/'.$member->dataid) }}?page={{ $currentPage }}" class="ur-admin-profile-card__package">
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
                            <span id="active_label_{{$member->dataid}}" class="ur-admin-badge {{ $member->active==0 ? 'ur-admin-badge--warning' : 'ur-admin-badge--success' }}">{{ $member->getActiveLabel() }}</span>
                            @php
                                // Whether the member has actually finished the
                                // AI selfie/photo verification gate — surfaced
                                // here because "Active" alone doesn't tell an
                                // admin that, e.g., a user is still stuck
                                // behind the photo-upload requirement.
                                $photoStatus = $member->photo_verification_status;
                                $photoBadgeClass = match ($photoStatus) {
                                    'verified' => 'ur-admin-badge--success',
                                    'resubmit' => 'ur-admin-badge--warning',
                                    'rejected' => 'ur-admin-badge--danger',
                                    default => 'ur-admin-badge--danger', // pending / not started — deliberately red, not the muted/neutral "info" style, so it's not mistaken for "verified" at a glance
                                };
                                $photoLabel = match ($photoStatus) {
                                    'verified' => 'Photo Verified',
                                    'resubmit' => 'Photo: Resubmit Needed',
                                    'rejected' => 'Photo Rejected',
                                    default => 'Photo Not Verified',
                                };
                            @endphp
                            <span class="ur-admin-badge {{ $photoBadgeClass }}"><i class="fa fa-camera"></i> {{ $photoLabel }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Member ID</b></td>
                        <td colspan="3"><a href="{{url('/member/profile/'.$member->dataid)}}" target="_blank">{{$member->dataid}}</a></td>
                    </tr>
                    <tr>
                        <td><b>Gender</b></td>
                        <td>{{ ucfirst($member->gender) }}</td>
                        <td><b>Age</b></td>
                        <td>{{date_diff(date_create($member->birthday), date_create('now'))->y}}</td>
                    </tr>
                    <tr>
                        <td><b>Height</b></td>
                        <td>{{$member->height}}</td>
                        <td><b>Religion</b></td>
                        <td>{{$member->lbl_religion}}</td>
                    </tr>
                    <tr>
                        <td><b>Caste / Sect</b></td>
                        <td colspan="3">{{$member->lbl_caste}}</td>
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
                <a href="{{ url('admin/profile/preview/'.$member->dataid) }}?page={{ $currentPage }}">
                    <i class="fa fa-id-card"></i> Full Profile
                </a>
                <a id="interest_a_'{{$member->dataid}}'" href="{{ url('admin/profile/listing/interests/'.$member->dataid) }}">
                    <span id="interest_'{{$member->dataid}}'"><i class="fa fa-heart"></i> View Interests</span>
                </a>
                <a href="{{ url('admin/profile/package/'.$member->dataid) }}?page={{ $currentPage }}">
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
