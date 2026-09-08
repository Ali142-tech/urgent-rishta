{{--
    Shared member-card partial — one profile tile (photo, quick facts,
    details, View Full Profile / Express Interest footer). Styled by
    public/css/ur-member-card.css. Expects a single $member (a hydrated
    Profile row, e.g. from Profile::profiles()).

    Used by:
      - member/searchdata.blade.php (the real search results grid)
      - member/user.blade.php (the "Recommended Matches For You" grid on
        the My Profile dashboard-home section)
--}}
<?php use App\User; ?>
<div class="member-card" id="block_{{$member->dataid}}">
    <div class="member-card__photo">
        <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
            <span class="member-card__photo-bg" style="background-image:url('{{ $member->getProfileImage() }}')"></span>
            <img src="{{ $member->getProfileImage() }}" alt="{{ $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';" />
        </a>
        @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))
            <span class="member-card__ribbon member-card__ribbon--new">New</span>
        @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))
            <span class="member-card__ribbon member-card__ribbon--updated">Updated</span>
        @endif
        @if($member->photo_verification_status === 'verified')
            <span class="member-card__badge--verified"><i class="fa fa-check-circle"></i> Verified</span>
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
            <li>
                <i class="fa fa-map-marker"></i>{{$member->lbl_city}}
                @if(!empty($member->con_of_residence_code))
                    <img class="member-card__flag" src="{{ \App\Profile::countryFlagUrl($member->con_of_residence_code) }}" alt="" title="{{$member->lbl_con_of_residence}}" loading="lazy" onerror="this.style.display='none';">
                @endif
            </li>
        </ul>
        @if(!empty($member->profession))
            <div class="member-card__designation"><i class="fa fa-briefcase"></i> {{$member->profession}}</div>
        @endif
        <ul class="member-card__details">
            <li><span>Religion</span><b>{{$member->lbl_religion}}</b></li>
            <li><span>Caste / Sect</span><b>{{$member->lbl_caste}} / {{$member->sect}}</b></li>
            <li><span>Marital Status</span><b>{{$member->lbl_marital_status}}</b></li>
        </ul>
    </div>
    <div class="member-card__footer">
        <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
            <i class="fa fa-eye"></i> View Full Profile
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
