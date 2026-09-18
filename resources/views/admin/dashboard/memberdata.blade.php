@extends('admin.dashboard.profiles')
@section('member-data')

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
    <div class="ur-admin-card ur-admin-profile-card" id="block_{{$member->dataid}}" data-dataid="{{ $member->dataid }}">
        {{-- Desktop only (see ur-admin.css responsive block) — the compact
             row for the list+detail layout (admin/dashboard/profiles.blade.php).
             Clicking it AJAX-loads the full summary into the right-hand
             panel instead of navigating away; see loadMemberDetail(). --}}
        <div class="ur-admin-list-item {{ isset($selectedMember) && $selectedMember && $selectedMember->dataid === $member->dataid ? 'is-active' : '' }}" onclick="loadMemberDetail('{{ $member->dataid }}', this);">
            <span class="ur-avatar-wrap">
                <span class="ur-admin-list-item__thumb" style="background-image:url('{{ $member->getProfileImage() }}')"></span>
                @if($member->isOnline())
                    <span class="ur-online-dot" title="Online in the last month"></span>
                @endif
            </span>
            <div class="ur-admin-list-item__body">
                <div class="ur-admin-list-item__name">{{ $member->first_name }} {{ $member->last_name }}</div>
                <div class="ur-admin-list-item__sub">
                    {{ date_diff(date_create($member->birthday), date_create('now'))->y }} &bull; {{ $member->lbl_city }}
                    @if(!empty($member->con_of_residence_code))
                        <img class="ur-admin-list-item__flag" src="{{ \App\Profile::countryFlagUrl($member->con_of_residence_code) }}" alt="" title="{{ $member->lbl_con_of_residence }}" loading="lazy" onerror="this.style.display='none';">
                    @endif
                </div>
            </div>
            @php
                $rowStatus = $member->active == 0
                    ? ['label' => 'Pending', 'class' => 'ur-admin-list-item__status--warning']
                    : ($member->photo_verification_status !== 'verified'
                        ? ['label' => 'Not Verified', 'class' => 'ur-admin-list-item__status--danger']
                        : ['label' => 'Active', 'class' => 'ur-admin-list-item__status--success']);
            @endphp
            <span class="ur-admin-list-item__status {{ $rowStatus['class'] }}">{{ $rowStatus['label'] }}</span>
        </div>

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
                <h3 class="member-card__name">
                    @if($member->isOnline())
                        <span class="member-card__online-dot" title="Online in the last month"></span>
                    @endif
                    {{ $member->first_name }}
                </h3>
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
