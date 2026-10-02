{{--
    "Complete Client File" modal content (client mockup, Sep 2026) —
    fetched by openClientFile() (see layouts/team/dashboard.blade.php) and
    injected into #ur_client_file_body. See TeamController::
    proposalFilePanel(). Expects $member (Profile-hydrated, decorated with
    added_by_name/dataid/experience), $matchCount, $canRunAiMatch,
    $hasRealPhotos, $photos, $intakeText, $intakeIsSynthesized. Two tabs:
    Profile details, and Original pasted form — always shown (client
    request, Sep 2026); when the proposal has no real raw_intake_text,
    $intakeText is instead reconstructed from the structured fields (see
    TeamController::buildSyntheticIntakeText()) and $intakeIsSynthesized
    is true, so the tab is labeled honestly rather than implying it's the
    client's actual original paste.
--}}
@php
    $asPage = $asPage ?? false;   // true when rendered as the full proposal page, not the popup
    $age = !empty($member->birthday) ? date_diff(date_create($member->birthday), date_create('now'))->y : null;
    $location = $member->lbl_city ?: ($member->lbl_con_of_residence ?: 'Location not provided');
    $nationality = $member->lbl_con_of_citizenship ?: $member->lbl_con_of_residence;
    $notProvided = 'Not provided';
@endphp
<div class="ur-cf-header">
    <div class="ur-cf-header__icon"><i class="fa fa-clipboard"></i></div>
    <div class="ur-cf-header__text">
        <h2>Complete Client File</h2>
        <p>Profile ID {{ $member->dataid }} &bull; Original and AI-organized data</p>
    </div>
    @if($matchCount > 0)<span class="ur-cf-matches-pill">{{ $matchCount }} top AI match{{ $matchCount == 1 ? '' : 'es' }}</span>@endif
    @unless($asPage)
    <button type="button" class="ur-cf-close" onclick="closeClientFile()" aria-label="Close"><i class="fa fa-times"></i></button>
    @endunless
</div>

<div class="ur-cf-top">
    <div class="ur-cf-photos">
        @for ($i = 0; $i < 2; $i++)
            @if($hasRealPhotos && isset($photos[$i]))
                <div class="ur-cf-photo-slot">
                    <img src="{{ $photos[$i] }}" alt="Client photo {{ $i + 1 }}">
                    <div class="ur-cf-photo-slot__footer">Photo {{ $i + 1 }}</div>
                </div>
            @else
                <div class="ur-cf-photo-slot ur-cf-photo-slot--empty">
                    <i class="fa fa-camera"></i>
                    <div class="ur-cf-photo-slot__caption">No photo uploaded</div>
                    <div class="ur-cf-photo-slot__id">{{ $member->dataid }}</div>
                    <div class="ur-cf-photo-slot__footer">Photo {{ $i + 1 }}</div>
                </div>
            @endif
        @endfor
    </div>

    <div class="ur-cf-info">
        @if($member->photo_verification_status === 'verified')
            <div class="ur-cf-verified"><i class="fa fa-check-circle"></i> Verified profile</div>
        @endif
        <div class="ur-cf-badges">@include('team.partials.classification-badges', ['profile' => $member, 'class' => 'ur-cf-badge'])</div>

        <h3>Profile {{ $member->dataid }}</h3>
        <p class="ur-cf-summary">
            {{ ucfirst($member->gender) }}
            @if($age) &bull; {{ $age }} @endif
            @if($nationality) &bull; {{ $nationality }} @endif
            @if(!empty($member->profession)) &bull; {{ $member->profession }} @if($member->lbl_city) in {{ $member->lbl_city }} @endif @endif
        </p>
        <p class="ur-cf-owner">
            Owned by {{ $member->added_by_name }}
            @if($member->added_by_dataid) &bull; Login ID {{ $member->added_by_dataid }} @endif
            @if($member->added_by_experience) &bull; {{ $member->added_by_experience }} @endif
        </p>

        <div class="ur-cf-stats">
            <div><span>Age</span><b>{{ $age ? $age.' years' : 'Not provided' }}</b></div>
            <div><span>Height</span><b>{{ $member->height ?: 'Not provided' }}</b></div>
            <div><span>Profession</span><b>{{ $member->profession ?: 'Not provided' }}</b></div>
            <div><span>Location</span><b>{{ $location }}</b></div>
        </div>

        <div class="ur-cf-notice">
            <p>{{ $hasRealPhotos ? 'Client photos are' : 'Client photo slots are' }} visible only to authorized matchmakers. The original pasted form remains unchanged beside this AI-organized profile.</p>
            <i class="fa fa-shield"></i>
        </div>
    </div>
</div>

{{-- Full modal width, below the photo/identity header — not squeezed into
     the right-hand column like the tabs used to be, which left a tall
     empty gap under the (much shorter) photo panel. --}}
<div class="ur-cf-tabbed">
    <div class="ur-cf-tabs" id="cf_tabs">
        <button type="button" data-tab="original" class="is-active">Original pasted form</button>
        <button type="button" data-tab="details">Profile details</button>
    </div>

    <div class="ur-cf-panel is-active" data-panel="original">
        @if($intakeIsSynthesized)
            <p class="ur-cf-synthesized-note"><i class="fa fa-info-circle"></i> No original pasted text was recorded for this proposal — reconstructed below from every field on file, so it can still be copied and shared as one block of text.</p>
        @endif
        @if(empty($intakeText))
            <p class="ur-cf-empty-note">No information has been entered for this profile yet.</p>
        @else
            <div class="ur-cf-raw-text-toolbar">
                <button type="button" class="ur-cf-copy-btn" id="cf_copy_btn" data-text="{{ $intakeText }}"><i class="fa fa-copy"></i> Copy</button>
            </div>
            <pre class="ur-cf-raw-text" id="cf_raw_text">{{ $intakeText }}</pre>
        @endif
    </div>

    <div class="ur-cf-panel" data-panel="details">
        <div class="ur-cf-fields-head">AI-organized profile fields</div>
        <div class="ur-cf-grid">
            <div><span>Gender</span><b>{{ $member->gender ? ucfirst($member->gender) : $notProvided }}</b></div>
            <div><span>Age</span><b>{{ $age ? $age.' years' : $notProvided }}</b></div>
            <div><span>Marital status</span><b>{{ $member->lbl_marital_status ?: $notProvided }}</b></div>
            <div><span>Height</span><b>{{ $member->height ?: $notProvided }}</b></div>
            <div><span>Religion</span><b>{{ $member->lbl_religion ?: $notProvided }}</b></div>
            <div><span>Sect</span><b>{{ $member->sect ?: $notProvided }}</b></div>
            <div><span>Caste</span><b>{{ $member->lbl_caste ?: $notProvided }}</b></div>
            <div><span>Nationality</span><b>{{ $nationality ?: $notProvided }}</b></div>
            <div><span>Hometown</span><b>{{ collect([$member->lbl_city ?: $member->city, $member->lbl_con_of_birth])->filter()->unique()->implode(', ') ?: $notProvided }}</b></div>
            <div><span>Current city</span><b>{{ $member->current_city ?: $notProvided }}</b></div>
            <div><span>Country</span><b>{{ $member->lbl_con_of_residence ?: $notProvided }}</b></div>
            <div><span>Education</span><b>{{ $member->lbl_education ?: $notProvided }}</b></div>
            <div><span>Profession</span><b>{{ $member->profession ?: $notProvided }}</b></div>
            <div><span>Income</span><b>{{ $member->income ?: $notProvided }}</b></div>
            <div><span>Mother tongue</span><b>{{ $member->lbl_mother_tongue ?: $notProvided }}</b></div>
            <div><span>Family status</span><b>{{ $member->family_status ?: $notProvided }}</b></div>
            <div><span>Looking from</span><b>{{ $member->looking_from ?: $notProvided }}</b></div>
            <div><span>Profile category</span><b>{{ $member->profile_category ?: $notProvided }}</b></div>
            <div><span>Presentation highlight</span><b>{{ $member->presentation_highlight ?: $notProvided }}</b></div>
            <div><span>Father</span><b>{{ $member->father_occupation ?: $notProvided }}</b></div>
            <div><span>Profile owner</span><b>{{ $member->added_by_name }}</b></div>
            <div><span>Owner login ID</span><b>{{ $member->added_by_dataid ?: $notProvided }}</b></div>
        </div>
        @if(!empty($member->profile_description))
            <p class="ur-cf-description">{{ $member->profile_description }}</p>
        @endif
    </div>
</div>

<div class="ur-cf-footer">
    <p>Every client file keeps the original form unchanged. Matched forms can be sent directly to the profile owner.</p>
    <div class="ur-cf-footer__actions">
        @unless($asPage)
        <button type="button" onclick="closeClientFile()">Close</button>
        @endunless
        @if($asPage && $member->team_card_role === 'own')
            <a href="{{ route('team.proposals.edit', $member->dataid) }}"><i class="fa fa-pencil"></i> Edit</a>
            <a href="{{ route('team.proposals.photos', $member->dataid) }}"><i class="fa fa-camera"></i> Manage photos</a>
            <a href="#" onclick="return deleteProposal('{{ $member->dataid }}');" style="color:#B5674A;"><i class="fa fa-trash"></i> Delete</a>
        @endif
        <a href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> Form only</a>
        @if($hasRealPhotos)
            {{-- Tries the OS/browser native share sheet first (real photo
                 FILES attached, not just a link — same "attach to any
                 app" picker Windows/Android/iOS show for a native Share
                 action) via navigator.share(); falls back to the WhatsApp
                 text+link redirect below on browsers that don't support
                 sharing files (see the delegated click handler in
                 layouts/team/dashboard.blade.php). --}}
            <button type="button" id="cf_share_photos_btn" class="is-gold"
                data-photos="{{ json_encode($photos) }}"
                data-title="{{ $member->dataid }}"
                data-text="{{ $intakeText }}"
                data-fallback="{{ route('team.proposals.share.whatsapp', ['dataid' => $member->dataid, 'with_photos' => 1]) }}">
                <i class="fa fa-whatsapp"></i> <span id="cf_share_photos_label">Form + {{ count($photos) }} photo{{ count($photos) == 1 ? '' : 's' }}</span>
            </button>
        @endif
        @if($canRunAiMatch)
            <a href="{{ route('team.matches.show', $member->dataid) }}" class="is-dark"><i class="fa fa-magic"></i> Run AI Match</a>
        @endif
    </div>
</div>
{{-- Tab-switching and Copy button are handled globally by a delegated
     click listener on #ur_client_file_body — see
     layouts/team/dashboard.blade.php. A <script> tag here would never
     execute (browsers don't run scripts injected via innerHTML), so it
     doesn't belong in this AJAX-fetched fragment. --}}
