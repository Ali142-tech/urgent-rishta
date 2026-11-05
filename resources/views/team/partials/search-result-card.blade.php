
@php
    $category = strtolower($member->profile_category ?? '');
    $highlight = strtolower($member->presentation_highlight ?? '');
    $isAbroad = str_contains(strtolower($member->looking_from ?? ''), 'abroad');
    $countryName = $member->lbl_con_of_residence ?: ($member->lbl_con_of_citizenship ?? null) ?: ($member->lbl_con_of_birth ?? null);
    $countryCode = !empty($member->lbl_con_of_residence) ? ($member->con_of_residence_code ?? null)
        : (!empty($member->lbl_con_of_citizenship) ? ($member->con_of_citizenship_code ?? null)
        : ($member->con_of_birth_code ?? null));
    $isNew = round((time() - strtotime($member->created_at)) / 604800) <= config('app.new_profile_duration');
    $isUpdated = !$isNew && round((time() - strtotime($member->updated_at)) / 604800) <= config('app.updated_profile_duration');
    $__restricted = auth()->user()->isRestricted();   // temporary access: a plain silhouette instead of the client's photo, and no Share button
    $__cardImg = $__restricted ? \App\Profile::defaultImage($member->gender) : $member->getProfileImage();

    // Dark card (client design, Nov 2026): header (photo, ID, quick facts), "About Profile" / "Looking For" panels, action pills.
    $__age = !empty($member->birthday) ? date_diff(date_create($member->birthday), date_create('now'))->y : null;
    $__short = function ($items, $max = 2) {
        $items = array_values(array_filter((array) $items, fn ($v) => $v !== null && $v !== ''));
        if (!$items) return null;
        return implode(', ', array_slice($items, 0, $max)) . (count($items) > $max ? ' +' . (count($items) - $max) : '');
    };
    $__faithSub = $member->sect ?: ($member->lbl_caste ?: null);
    $__factRows = [[strtolower((string) $member->gender) === 'male' ? 'fa-mars' : 'fa-venus', ucfirst((string) $member->gender) . ($__age !== null ? ', ' . $__age . ' Years' : '')]];
    if (!empty($member->lbl_marital_status)) $__factRows[] = ['fa-heart-o', $member->lbl_marital_status];
    if (!empty($member->height)) $__factRows[] = ['fa-arrows-v', $member->height];
    if (!empty($member->lbl_religion) || $__faithSub) $__factRows[] = ['fa-moon-o', trim((string) $member->lbl_religion . ($__faithSub ? ' (' . $__faithSub . ')' : ''))];
    $__loc = array_values(array_filter([$member->lbl_city ?: null, $member->lbl_con_of_residence ?: null]));
    $__about = [];
    if (!empty($member->lbl_education)) $__about[] = ['fa-graduation-cap', $member->lbl_education];
    if (!empty($member->profession)) $__about[] = ['fa-briefcase', $member->profession];
    if ($__loc) $__about[] = ['fa-map-marker', implode(', ', array_unique($__loc))];
    $__look = [];
    $__a1 = $member->pref_age_min; $__a2 = $member->pref_age_max;
    $__m = $__short($member->prefMaritalNames()); $__e = $__short($member->prefEducationNames());
    $__l = $__short(array_filter([$member->pref_city ?: null, ...$member->prefNationalityNames()]));
    // always the same four rows; whatever was not filled in reads N/A
    $__look[] = ['fa-calendar', ($__a1 || $__a2) ? 'Age ' . ($__a1 && $__a2 ? $__a1 . '–' . $__a2 : ($__a1 ? $__a1 . '+' : 'up to ' . $__a2)) : 'N/A'];
    $__look[] = ['fa-heart-o', $__m ?: 'N/A'];
    $__look[] = ['fa-graduation-cap', $__e ?: 'N/A'];
    $__look[] = ['fa-map-marker', $__l ?: 'N/A'];
@endphp
<div class="ur-search-card">
    <div class="ur-search-card__head">
        <a class="ur-search-card__photo" style="--ph: url('{{ $__cardImg }}');" href="{{ route('team.proposals.view', $member->dataid) }}" target="_blank" rel="noopener">
            <img src="{{ $__cardImg }}" alt="{{ $__restricted ? '' : $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
            @if($isNew)<span class="ur-search-card__badge ur-search-card__badge--new ur-search-card__badge--photo">New</span>@endif
            @if($member->photo_verification_status === 'verified')<span class="ur-search-card__verified"><i class="fa fa-check"></i> Verified</span>@endif
        </a>

        <div class="ur-search-card__headinfo">
            <div class="ur-search-card__top">
                <span class="ur-search-card__id">{{ $member->dataid }}</span>
            </div>
            @if(!$__restricted && ($__fullName = trim((string) $member->first_name . ' ' . (string) $member->last_name)) !== '')
                <div class="ur-search-card__realname">{{ $__fullName }}</div>
            @endif

            <ul class="ur-search-card__factlist">
                @foreach($__factRows as [$ico, $txt])
                    <li><i class="fa {{ $ico }}"></i><span title="{{ $txt }}">{{ $txt }}</span></li>
                @endforeach
                {{-- the flag always belongs to the country named beside it (the country the client lives in); the city (their home town, often in another country) is listed under About Profile --}}
                @if(!empty($countryName) || !empty($member->lbl_city))
                    <li>
                        <i class="fa fa-map-marker"></i>
                        <span>
                            @if(!empty($countryName))
                                @if(!empty($countryCode) && \App\Profile::countryFlagUrl($countryCode, 80))
                                    <img class="ur-search-card__flag" src="{{ \App\Profile::countryFlagUrl($countryCode, 80) }}" alt="" loading="lazy" onerror="this.style.display='none';">
                                @endif
                                {{ $countryName }}
                            @else
                                {{ $member->lbl_city }}
                            @endif
                        </span>
                    </li>
                @endif
            </ul>
            @if($isUpdated || $category === 'premium' || $category === 'royal' || $isAbroad || $highlight === 'highly attractive' || !empty($member->is_private))
            <div class="ur-search-card__badges">
                @if($isUpdated)
                    <span class="ur-search-card__badge ur-search-card__badge--updated">Updated</span>
                @endif
                @if(!empty($member->is_private))
                    <span class="ur-search-card__badge ur-search-card__badge--private"><i class="fa fa-lock"></i> Private</span>
                @endif
                @if($category === 'premium')
                    <span class="ur-search-card__badge ur-search-card__badge--premium">Premium Profile</span>
                @elseif($category === 'royal')
                    <span class="ur-search-card__badge ur-search-card__badge--royal">Royal Profile</span>
                @endif
                @if($isAbroad)
                    <span class="ur-search-card__badge ur-search-card__badge--abroad">Abroad</span>
                @endif
                @if($highlight === 'highly attractive')
                    <span class="ur-search-card__badge ur-search-card__badge--highlight">Highly Attractive</span>
                @endif
            </div>
            @endif

            @unless($__restricted)
            <div class="ur-search-card__owner">
                <i class="fa fa-user-circle"></i> Owner: {{ $member->added_by_name }}@if(!empty($member->added_by_premium)) @include('team.partials.premium-badge')@endif
                @if($member->added_by_experience) &bull; {{ $member->added_by_experience }} @endif
            </div>
            @endunless
        </div>
    </div>

    <div class="ur-search-card__panels">
        <div class="ur-search-card__panel">
            <h4><i class="fa fa-user"></i> About Profile</h4>
            <ul>
                @forelse($__about as [$ico, $txt])
                    <li><i class="fa {{ $ico }}"></i><span title="{{ $txt }}">{{ $txt }}</span></li>
                @empty
                    <li class="is-empty"><span>Not provided</span></li>
                @endforelse
            </ul>
        </div>
        <div class="ur-search-card__panel ur-search-card__panel--alt">
            <h4><i class="fa fa-bullseye"></i> Looking For</h4>
            <ul>
                @forelse($__look as [$ico, $txt])
                    <li class="{{ $txt === 'N/A' ? 'is-empty' : '' }}"><i class="fa {{ $ico }}"></i><span title="{{ $txt }}">{{ $txt }}</span></li>
                @empty
                    <li class="is-empty"><span>N/A</span></li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="ur-search-card__actions">
        <a class="ur-search-card__btn" href="{{ route('team.proposals.view', $member->dataid) }}" target="_blank" rel="noopener">
            <i class="fa fa-eye"></i> View file
        </a>

        @if($__restricted)
        <a class="ur-search-card__btn ur-search-card__btn--whatsapp" href="{{ \App\User::SUPPORT_WHATSAPP_URL }}?text={{ rawurlencode('Assalam o Alaikum, I am interested in proposal ' . $member->dataid . '. Please share more details.') }}" target="_blank" rel="noopener">
            <i class="fa fa-whatsapp"></i> WhatsApp
        </a>
        @endif
        @unless($__restricted)
        <a class="ur-search-card__btn ur-search-card__btn--whatsapp js-share-proposal" data-payload-url="{{ route('team.proposals.share.payload', $member->dataid) }}" href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener">
            <i class="fa fa-whatsapp"></i> Share
        </a>
        @endunless
    </div>
</div>
