{{-- One card of the admin Team Members list; included by members.blade.php and returned as HTML by "Load more". --}}
@foreach($members as $member)
        @php
            $status = $member->status ?? 'active';
            $initials = strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name ?? '', 0, 1)) ?: 'TM';
            $isMe = $member->id === auth()->id();
            $palette = ['#123A2E', '#C9974D', '#8B6BA8', '#3F8F68', '#B5574A', '#4A6FA5', '#A65E8B'];
        @endphp
        <div class="ur-tmx-card {{ $isMe ? 'is-me' : '' }}" id="tm_row_{{ $member->dataid }}" data-status="{{ $status }}" data-search="{{ strtolower($member->first_name . ' ' . $member->last_name . ' ' . $member->email . ' ' . $member->dataid) }}">
            <div class="ur-tmx-row">
                <div class="ur-tmx-person">
                    <div class="ur-tmx-avatar" style="background: {{ $palette[$member->id % count($palette)] }};">{{ $initials }}</div>
                    <div>
                        <div class="ur-tmx-name"><a href="{{ route('team.manage.members.show', $member->dataid) }}" style="color:inherit;">{{ $member->first_name }} {{ $member->last_name }}</a>@if($member->isPremium()) @include('team.partials.premium-badge') @endif</div>
                        <div class="ur-tmx-id">{{ $member->dataid }}</div>
                    </div>
                </div>

                <div class="ur-tmx-contact">
                    {{ $member->email }}
                    <span>{{ $member->contact_mobile_number }}</span>
                </div>

                <div class="ur-tmx-field" id="tm_status_{{ $member->dataid }}">
                    @if($isMe)
                        <span class="ur-tmx-tag">{{ ucfirst($status) }} &middot; you</span>
                    @else
                        <select class="js-member-status" data-id="{{ $member->dataid }}" data-current="{{ $status }}" aria-label="Status">
                            @foreach(['active' => 'Active', 'suspended' => 'Suspended', 'deactivated' => 'Deactivated'] as $v => $l)
                                <option value="{{ $v }}" {{ $status === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="ur-tmx-field">
                    @if($member->isAdmin())
                        <span class="ur-tmx-tag">Originals (admin)</span>
                    @else
                        <select class="js-photo-access" data-id="{{ $member->dataid }}" data-current="{{ $member->can_view_originals ? '1' : '0' }}" aria-label="Photos">
                            <option value="0" {{ !$member->can_view_originals ? 'selected' : '' }}>With watermark</option>
                            <option value="1" {{ $member->can_view_originals ? 'selected' : '' }}>Original (no watermark)</option>
                        </select>
                    @endif
                </div>

                <div class="ur-tmx-field">
                    @if($member->isAdmin())
                        <span class="ur-tmx-tag">Sees all (admin)</span>
                    @else
                        <select class="js-private-member" data-id="{{ $member->dataid }}" data-current="{{ $member->is_private_member ? '1' : '0' }}" title="A private member's proposals are private" aria-label="Their proposals">
                            <option value="0" {{ !$member->is_private_member ? 'selected' : '' }}>Proposals: public</option>
                            <option value="1" {{ $member->is_private_member ? 'selected' : '' }}>Proposals: private</option>
                        </select>
                        <select class="js-private-access" data-id="{{ $member->dataid }}" data-current="{{ $member->can_view_private ? '1' : '0' }}" title="Whether this member can see private proposals" aria-label="Private access">
                            <option value="0" {{ !$member->can_view_private ? 'selected' : '' }}>Cannot see private</option>
                            <option value="1" {{ $member->can_view_private ? 'selected' : '' }}>Can see private</option>
                        </select>
                    @endif
                </div>

                <div class="ur-tmx-actions">
                    <button type="button" class="ur-tmx-btn ur-tmx-btn--dark" onclick="return sendResetLink(this, '{{ $member->dataid }}');">Reset password</button>
                    <a class="ur-tmx-btn ur-tmx-btn--gold" href="{{ route('team.manage.proposals', ['matchmaker' => $member->id]) }}">View proposals</a>
                    <a class="ur-tmx-btn ur-tmx-btn--ghost" href="{{ route('team.manage.members.show', $member->dataid) }}"><i class="fa fa-id-card-o"></i> View profile</a>
                </div>
            </div>

        </div>
@endforeach
