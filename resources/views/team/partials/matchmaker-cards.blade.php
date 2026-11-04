{{--
    The Partner matchmakers cards. Rendered for the first batch by team/matchmakers.blade.php and for every "Load more"
    click by TeamController::matchmakers() (?partial=1).
--}}
@php $avatarPalette = ['#17493A', '#8E44AD', '#2C5F8A', '#B36B2E', '#1E7A46', '#5B2C6F']; @endphp
    @foreach($members as $member)
        @php $color = $avatarPalette[$member->id % count($avatarPalette)]; @endphp
        <div class="ur-mk-card">
            <div class="ur-mk-card__banner" style="background:linear-gradient(135deg, {{ $color }}, #0F2E24);">
                @if($member->isSenior)<span class="ur-mk-ribbon"><i class="fa fa-star"></i> Senior Partner</span>@endif
                <div class="ur-mk-card__avatar" style="background:{{ $color }};">
                    @if($member->hasRealPhoto)
                        <img src="{{ $member->getProfileImage() }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
                    @else
                        {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                        <small>Add photo</small>
                    @endif
                </div>
            </div>
            <div class="ur-mk-card__body">
                <div class="ur-mk-card__name">{{ $member->first_name }} {{ $member->last_name }}@if($member->isPremium()) @include('team.partials.premium-badge')@endif</div>
                <div class="ur-mk-card__sub">Verified Partner</div>

                @if($member->id === auth()->id())
                <form method="POST" action="{{ route('team.my-profile.photo') }}" enctype="multipart/form-data" class="ur-mk-upload-form">
                    @csrf
                    <label class="ur-mk-upload-btn">
                        <i class="fa fa-camera"></i> Upload my photo
                        <input type="file" name="image" accept="image/*" onchange="this.form.submit()" hidden>
                    </label>
                </form>
                @endif

                <div class="ur-mk-card__stats">
                    <div><span>City</span><b>{{ $member->city ?: '—' }}</b></div>
                    <div><span>Experience</span><b>{{ $member->experience ?: '—' }}</b></div>
                    <div><span>WhatsApp</span><b>{{ $member->contact_mobile_number ?: '—' }}</b></div>
                    <div><span>Proposals</span><b>{{ $member->activeProposalsCount }} client profile{{ $member->activeProposalsCount == 1 ? '' : 's' }}</b></div>
                </div>

                <div class="ur-mk-card__actions">
                    <a href="{{ route('team.proposals.search', ['matchmaker' => $member->id]) }}">View proposals</a>
                    @if(!empty($member->contact_mobile_number))
                        <a class="is-whatsapp" href="{{ \App\User::whatsappLinkForNumber($member->contact_mobile_number) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
