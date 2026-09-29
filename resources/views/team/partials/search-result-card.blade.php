{{--
    Compact horizontal result card for team/proposals-search.blade.php's
    Advanced Search redesign (Sep 2026) — a photo strip + ID/verified badge,
    quick facts line, classification badges (profile_category/looking_from/
    presentation_highlight — added by the Sep 2026 migration but not yet
    settable from any Add/Edit Proposal form, so these stay blank until that
    form exists), an "Owner" byline, and the same View/Edit/Share actions
    member-card.blade.php already uses for the team-added pool — just in a
    denser layout suited to scanning many results at once, unlike the full
    member-card used on My Clients. Expects a single $member, already
    decorated by TeamController::buildProposalGrid() with added_by_name/
    added_by_dataid/added_by_experience/team_card_role/has_partner_preference.
--}}
@php
    $category = strtolower($member->profile_category ?? '');
    $highlight = strtolower($member->presentation_highlight ?? '');
    $isAbroad = strtolower($member->looking_from ?? '') === 'abroad';
    $isNew = round((time() - strtotime($member->created_at)) / 604800) <= config('app.new_profile_duration');
    $isUpdated = !$isNew && round((time() - strtotime($member->updated_at)) / 604800) <= config('app.updated_profile_duration');
@endphp
<div class="ur-search-card">
    <a class="ur-search-card__photo" href="{{ route('team.proposals.view', $member->dataid) }}" target="_blank" rel="noopener">
        <img src="{{ $member->getProfileImage(true) }}" alt="{{ $member->first_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
        @if($isNew)
            <span class="ur-search-card__ribbon ur-search-card__ribbon--new">New</span>
        @elseif($isUpdated)
            <span class="ur-search-card__ribbon ur-search-card__ribbon--updated">Updated</span>
        @endif
    </a>

    <div class="ur-search-card__body">
        <div class="ur-search-card__top">
            <span class="ur-search-card__id">{{ $member->dataid }}</span>
            @if($member->photo_verification_status === 'verified')
                <span class="ur-search-card__verified"><i class="fa fa-check-circle"></i> Verified</span>
            @endif
        </div>

        <div class="ur-search-card__meta">
            {{ ucfirst($member->gender) }}
            @if(!empty($member->birthday)) &bull; {{ date_diff(date_create($member->birthday), date_create('now'))->y }} yrs @endif
            @if(!empty($member->lbl_marital_status)) &bull; {{ $member->lbl_marital_status }} @endif
            @if(!empty($member->lbl_city) || !empty($member->lbl_con_of_residence)) &bull; {{ $member->lbl_city ?: $member->lbl_con_of_residence }} @endif
        </div>

        @if($category === 'premium' || $category === 'royal' || $isAbroad || $highlight === 'highly attractive')
        <div class="ur-search-card__badges">
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

        @if(!empty($member->profession))
            <div class="ur-search-card__profession"><i class="fa fa-briefcase"></i> {{ $member->profession }}</div>
        @endif

        <div class="ur-search-card__owner">
            <i class="fa fa-user-circle"></i> Owner: {{ $member->added_by_name }}
            @if($member->added_by_dataid) &bull; {{ $member->added_by_dataid }} @endif
            @if($member->added_by_experience) &bull; {{ $member->added_by_experience }} @endif
        </div>

        <div class="ur-search-card__actions">
            <a class="ur-search-card__btn" href="{{ route('team.proposals.view', $member->dataid) }}" target="_blank" rel="noopener">
                <i class="fa fa-eye"></i> View file
            </a>
            @if($member->team_card_role === 'own')
                @if($member->has_partner_preference)
                    <a class="ur-search-card__btn ur-search-card__btn--dark" href="{{ route('team.matches.show', $member->dataid) }}">
                        <i class="fa fa-magic"></i> AI Match
                    </a>
                @endif
                <a class="ur-search-card__btn" href="{{ route('team.proposals.edit', $member->dataid) }}">
                    <i class="fa fa-pencil"></i> Edit
                </a>
            @endif
            <a class="ur-search-card__btn ur-search-card__btn--whatsapp" href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener">
                <i class="fa fa-whatsapp"></i> Share
            </a>
        </div>
    </div>
</div>
