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
        hideImage=true so the real photo always shows blurred (whole image,
        not just the face) regardless of whether the viewer is logged in —
        never the sharp/identifiable original. Now that the photo also gets
        the "Private Photo" lock overlay below, a fully-blurred backdrop
        reads more consistently than the old face-only-pixelated version
        (client's earlier request) — see $member->homepageFaceBlurredImage /
        GenerateHomepageFaceBlur if that's ever wanted back.

    Optional: $hideImage (bool, default false) — when true, shows the
    pre-generated whole-image blur, or a generic gender silhouette if none
    exists — never the sharp original.

    Optional: $viewerPreference (App\PartnerPreference|null) — the LOGGED-IN
    VIEWER's own partner preferences (not $member's), passed once per page
    by the controller (never recomputed per-card — see HomeController::
    search()/recommendedMatches()). When present, shows a "XX% Match" badge
    using the same Profile::compatibilityWith() calculation as the single
    profile page's Compatibility tab — a pure in-memory comparison against
    already-loaded fields, no extra query per card. Client request (Sep 2026).
--}}
<?php
use App\User;
use Illuminate\Support\Str;
$hideImage = $hideImage ?? false;
$hiddenImageUrl = $hideImage ? $member->getBlurredProfileImage() : null;
// Guests on the homepage slider only ever get the single pre-blurred
// image (never the real gallery) — the carousel is member-card-images
// only, so guest/hideImage mode stays a single static photo.
$cardImages = $hideImage ? [$hiddenImageUrl] : $member->getCardImages(null, !empty($watermarked));
$compatibility = (!$hideImage && !empty($viewerPreference)) ? $member->compatibilityWith($viewerPreference) : null;
$compatTier = null;
if ($compatibility) {
    $compatTier = match (true) {
        $compatibility['percent'] >= 85 => ['label' => 'Excellent Match', 'sub' => 'High compatibility across key factors'],
        $compatibility['percent'] >= 70 => ['label' => 'Very Good Match', 'sub' => 'Strong alignment with your preferences'],
        $compatibility['percent'] >= 50 => ['label' => 'Good Match', 'sub' => 'Solid compatibility with potential'],
        $compatibility['percent'] >= 30 => ['label' => 'Fair Match', 'sub' => 'Some shared preferences'],
        default => ['label' => 'Low Match', 'sub' => 'Few of your preferences matched'],
    };
}
// Card shows only these 5 factor bars (client request, Sep 2026) — Education/
// Mother Tongue/Children are still counted in the overall $compatibility['percent']
// above if the viewer set them, just not shown as their own bar on the card.
$compatCardFactors = ['Age', 'Location', 'Religion', 'Caste', 'Marital Status'];
$compatCardChecks = $compatibility ? array_filter($compatibility['checks'], fn ($c) => in_array($c['factor'], $compatCardFactors, true)) : [];
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
        {{-- Homepage teaser slider only ($hideImage) — the photo underneath
             is already a real (never-sharp) blur, this just makes the "this
             is locked" intent explicit instead of just looking indistinctly
             blurry. Sits below the ribbon/ID/verified/package badges
             (z-index 1 vs their 2) so those stay visible on top, and repeats
             the same open-profile/register click so tapping the overlay
             itself still works like tapping the photo. --}}
        @if($hideImage)
            <div class="member-card__lock-overlay" onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <div class="member-card__lock-icon"><i class="fa fa-lock"></i></div>
                <div class="member-card__lock-title">Private Photo</div>
                <div class="member-card__lock-sub">Upgrade your plan to view this profile</div>
            </div>
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
        @if($compatibility)
            <div class="member-card__compat">
                <div class="member-card__compat-head">
                    <div class="member-card__compat-score">{{ $compatibility['percent'] }}%</div>
                    <div>
                        <div class="member-card__compat-label">{{ $compatTier['label'] }}</div>
                        <div class="member-card__compat-sub">{{ $compatTier['sub'] }}</div>
                    </div>
                </div>
                <div class="member-card__compat-bars">
                    @foreach($compatCardChecks as $check)
                        <div class="member-card__compat-row">
                            <span class="member-card__compat-row-label">{{ $check['factor'] }}</span>
                            <span class="member-card__compat-row-track"><span class="member-card__compat-row-fill" style="width:{{ $check['score'] }}%"></span></span>
                            <span class="member-card__compat-row-pct">{{ $check['score'] }}%</span>
                        </div>
                    @endforeach
                </div>
                {{-- AI Match Explanation (Website Upgrade Brief §6) — optional,
                     only passed by team/matches.blade.php and the overview's
                     AI Match Recommendations preview today. --}}
                @if(!empty($matchExplanation ?? null))
                    <div class="member-card__compat-explain">{{ $matchExplanation }}</div>
                @endif
            </div>
        @endif
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
        @if(!empty($addedByName ?? null))
            <div class="ur-team-added-by"><i class="fa fa-user-circle"></i> Added by {{ $addedByName }}</div>
        @endif
    </div>
    <div class="member-card__footer">
        @if($hideImage)
            {{-- Homepage teaser slider — no Express Interest here (it's a
                 sample card, not a real search result), just the same
                 register/open-profile action styled as an explicit unlock CTA. --}}
            <div class="member-card__locked-footer">
                <p class="member-card__locked-note">Available to paid members only</p>
                <a class="member-card__unlock-btn" onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                    <i class="fa fa-lock"></i> Unlock Full Profile
                </a>
            </div>
        @else
            <a onclick="javascript:@auth window.open('{{url('/member/profile/'.$member->dataid)}}'); @endauth @guest return register_request(); @endguest">
                <i class="fa fa-eye"></i> View Full Profile
            </a>
            @if(!empty($member->team_card_role))
                {{-- Team Dashboard card — not the regular Express Interest
                     flow (team-added proposals are login-less shell
                     records; see TeamController::store()). The whole
                     team-added pool is open to every team member, so
                     only ownership (not any request/approval step) decides
                     whether Edit shows up. --}}
                @if($member->team_card_role === 'own')
                    <a class="is-interest" href="{{ route('team.proposals.edit', $member->dataid) }}">
                        <i class="fa fa-pencil"></i> <span>Edit</span>
                    </a>
                @endif
                <a class="is-interest" href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener">
                    <i class="fa fa-whatsapp"></i> <span>Share</span>
                </a>
            @else
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
            @endif
        @endif
    </div>
</div>
