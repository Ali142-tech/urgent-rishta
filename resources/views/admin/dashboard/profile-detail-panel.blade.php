{{--
    Right-hand summary panel for the Member Profiles list+detail layout
    (admin/dashboard/profiles.blade.php). Rendered server-side for the
    initially-selected member, and re-rendered via AJAX (AdminController::
    profilePanel()) whenever a different row is clicked — see
    loadMemberDetail() in profiles.blade.php.
--}}
@if(empty($member))
    <div class="ur-admin-empty"><i class="fa fa-user"></i> Select a member from the list to see their details.</div>
@else
    @php
        $photoBadgeClass = match ($member->photo_verification_status) {
            'verified' => 'ur-admin-badge--success',
            'resubmit' => 'ur-admin-badge--warning',
            default => 'ur-admin-badge--danger',
        };
        $photoBadgeLabel = match ($member->photo_verification_status) {
            'verified' => 'Photo Verified',
            'resubmit' => 'Resubmit Needed',
            'rejected' => 'Photo Rejected',
            default => 'Not Verified',
        };
        $packageSlug = !empty($member->lbl_package) ? \Illuminate\Support\Str::slug($member->lbl_package) : null;
    @endphp
    {{-- Note: the LIST ROW (memberdata.blade.php) owns id="block_{dataid}"
         (shared with the existing deleteProfile()/toggleActive() JS) — this
         panel deliberately doesn't reuse that id to avoid a duplicate-id
         collision while both are on the page at once. --}}
    <div class="ur-detail-panel" data-dataid="{{ $member->dataid }}">
        <div class="ur-detail-panel__head">
            <img class="ur-detail-panel__photo" src="{{ $member->getProfileImage() }}" alt="{{ $member->first_name }}" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
            <div class="ur-detail-panel__who">
                <h3>{{ $member->first_name }} {{ $member->last_name }}</h3>
                <p class="ur-detail-panel__contact">
                    {{ $member->email }} &bull; {{ $member->contact_mobile_number }} &bull; Member ID {{ $member->dataid }}
                </p>
                <div class="ur-detail-panel__badges">
                    <span id="active_label_{{ $member->dataid }}" class="ur-admin-badge {{ $member->active == 0 ? 'ur-admin-badge--warning' : 'ur-admin-badge--success' }}">{{ $member->getActiveLabel() }}</span>
                    <span class="ur-admin-badge {{ $photoBadgeClass }}">{{ $photoBadgeLabel }}</span>
                    @if($packageSlug)
                        <span class="ur-admin-badge ur-admin-badge--package-{{ $packageSlug }}">{{ $member->lbl_package }}</span>
                    @else
                        <span class="ur-admin-badge ur-admin-badge--neutral">Unassigned</span>
                    @endif
                </div>
            </div>
            <a href="{{ url('admin/profile/preview/'.$member->dataid) }}?page={{ request()->query('page', 1) }}" class="ur-btn ur-btn--dark ur-detail-panel__full">Full Profile &rarr;</a>
        </div>

        <div class="ur-detail-panel__grid">
            <div><label>Gender</label><span>{{ ucfirst($member->gender) }}</span></div>
            <div><label>Age</label><span>{{ date_diff(date_create($member->birthday), date_create('now'))->y }}</span></div>
            <div><label>Height</label><span>{{ $member->height ?: '—' }}</span></div>

            <div><label>Religion</label><span>{{ $member->lbl_religion ?: '—' }}</span></div>
            <div><label>Caste / Sect</label><span>{{ $member->lbl_caste ?: '—' }}{{ $member->sect ? ' / '.$member->sect : '' }}</span></div>
            <div><label>Marital Status</label><span>{{ $member->lbl_marital_status ?: '—' }}</span></div>

            <div><label>Mother Tongue</label><span>{{ $member->lbl_mother_tongue ?: '—' }}</span></div>
            <div><label>Education</label><span>{{ $member->lbl_education ?: '—' }}</span></div>
            <div><label>Profession</label><span>{{ $member->profession ?: '—' }}</span></div>

            <div><label>City</label><span>{{ $member->lbl_city ?: '—' }}</span></div>
            <div><label>Location</label><span>{{ $member->lbl_city }}{{ $member->lbl_con_of_residence ? ', '.$member->lbl_con_of_residence : '' }}</span></div>
            <div><label>Created / Updated</label><span>{{ $member->created_at->format('d/m/Y') }} &bull; {{ $member->updated_at->format('d/m/Y') }}</span></div>
        </div>

        <div class="ur-detail-panel__actions">
            <a href="{{ url('admin/profile/listing/interests/'.$member->dataid) }}" class="ur-btn ur-btn--outline"><i class="fa fa-heart"></i> View Interests</a>
            <a href="{{ url('admin/profile/package/'.$member->dataid) }}?page={{ request()->query('page', 1) }}" class="ur-btn ur-btn--outline"><i class="fa fa-archive"></i> Change Package</a>
            <a class="ur-btn ur-btn--outline" onclick="return resendVerificationEmail($(this), '{{ $member->dataid }}');"><i class="fa fa-envelope"></i> Resend Verification Email</a>
            <a class="ur-btn ur-btn--outline" onclick="return sendPasswordResetEmail($(this), '{{ $member->dataid }}');"><i class="fa fa-unlock"></i> Password Reset</a>
            <a class="ur-btn ur-btn--danger-outline" onclick="return deleteProfile($(this), '{{ $member->dataid }}');"><i class="fa fa-trash"></i> Delete Profile</a>
        </div>
    </div>
@endif
