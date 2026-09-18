{{--
    Shared member-card partial — one profile tile (photo, quick facts,
    details, View Full Profile / Express Interest footer). Styled by
    public/css/ur-member-card.css. Expects a single $member (a hydrated
    Profile row, e.g. from Profile::profiles()).

    Used by:
      - member/searchdata.blade.php (the real search results grid)
      - member/user.blade.php (the "Recommended Matches For You" grid on
        the My Profile dashboard-home section)
      - welcome.blade.php's "Meet Our Members" guest-facing slider, passing
        hideImage=true so the real photo always shows blurred regardless of
        whether the viewer is logged in — never the sharp/identifiable
        original. That slider also sets $member->homepageFaceBlurredImage
        (see HomeController) when a face-only-pixelated version has been
        pre-generated (AWS Rekognition + GenerateHomepageFaceBlur), which
        takes priority over the whole-image blur so the body/outfit/
        background stay visible and only the face is obscured.

    Optional: $hideImage (bool, default false) — when true, shows (in order
    of preference) the member's face-only-obscured photo if one was
    pre-generated, else the pre-generated whole-image blur, else a generic
    gender silhouette — never the sharp original.
--}}
<?php
use App\User;
use Illuminate\Support\Str;
$hideImage = $hideImage ?? false;
$hiddenImageUrl = $hideImage ? ($member->homepageFaceBlurredImage ?? $member->getBlurredProfileImage()) : null;
// Guests on the homepage slider only ever get the single pre-blurred
// image (never the real gallery) — the carousel is member-card-images
// only, so guest/hideImage mode stays a single static photo.
$cardImages = $hideImage ? [$hiddenImageUrl] : $member->getCardImages();
$packageSlug = !empty($member->lbl_package) ? Str::slug($member->lbl_package) : null;
$packageIcon = match ($packageSlug) {
    'platinum' => '<i class="fa fa-star"></i>',
    'diamond' => '<i class="fa fa-diamond"></i>',
    'royal', 'imperial' => '&#128081;', // crown emoji
    default => '<i class="fa fa-diamond"></i>',
};
?>
<div class="member-card" id="block_{{$member->dataid}}">
    <div class="member-card__photo">
        @foreach($cardImages as $i => $cardImage)
            <a class="member-card__slide {{ $i === 0 ? 'is-active' : '' }}" onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <span class="member-card__photo-bg" style="background-image:url('{{ $cardImage }}')"></span>
                <img src="{{ $cardImage }}" alt="{{ $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';" />
            </a>
        @endforeach
        @if(count($cardImages) > 1)
            <button type="button" class="member-card__nav member-card__nav--prev" onclick="return cardCarouselNav(event, this, -1);" aria-label="Previous photo">&lsaquo;</button>
            <button type="button" class="member-card__nav member-card__nav--next" onclick="return cardCarouselNav(event, this, 1);" aria-label="Next photo">&rsaquo;</button>
        @endif
        @if(round((time() - strtotime($member->created_at))/(604800)) <= config('app.new_profile_duration'))
            <span class="member-card__ribbon member-card__ribbon--new">New</span>
        @elseif(round((time() - strtotime($member->updated_at))/(604800)) <= config('app.updated_profile_duration'))
            <span class="member-card__ribbon member-card__ribbon--updated">Updated</span>
        @endif
        @if($member->photo_verification_status === 'verified')
            <span class="member-card__badge--verified"><i class="fa fa-check-circle"></i> Verified <i class="fa fa-shield"></i></span>
        @endif
        @if($packageSlug)
            <span class="member-card__badge--package member-card__badge--package-{{ $packageSlug }}">
                {!! $packageIcon !!} {{ $member->lbl_package }}
            </span>
        @endif
        <span class="member-card__id-badge">
            @auth
                ID: {{$member->dataid}}
                <i class="fa fa-clipboard member-card__id-copy" onclick="return copyMemberId(event, '{{$member->dataid}}');" title="Copy ID"></i>
            @endauth
            @guest
                <i class="fa fa-lock"></i> ID Hidden
            @endguest
        </span>
    </div>
    <div class="member-card__body">
        <h3 class="member-card__name">
            @if($member->isOnline())
                <span class="member-card__online-dot" title="Online in the last month"></span>
            @endif
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
