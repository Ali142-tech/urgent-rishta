{{--
    The AI Match cards (one per compatible proposal). Rendered for the first page by team/matches.blade.php and for every
    "Load more" click by TeamController::matchesPartial(). $matchOffset keeps the MATCH 01, 02 ... numbering going across pages.
--}}
@php
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
    $nameOf = fn ($p) => trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: $p->dataid;
    $tierLabel = fn ($pct) => $pct >= 85 ? 'Excellent Match' : ($pct >= 70 ? 'Good Match' : ($pct >= 50 ? 'Fair Match' : 'Needs Review'));
    $proposalAge = $ageOf($proposalProfile);
    $proposalName = $nameOf($proposal);
@endphp
    @foreach($matches as $i => $match)
        @php
            $pct = $match->compat['percent'] ?? 0;
            $matchAge = $ageOf($match);
            $matchName = $nameOf($match);
            $bothAbroad = str_contains(strtolower($proposalProfile->looking_from ?? ''), 'abroad') && str_contains(strtolower($match->looking_from ?? ''), 'abroad');
        @endphp
        <div class="ur-am-match-card" id="match-{{ $match->dataid }}">
            <div class="ur-am-match-card__head">
                <div>
                    <span class="ur-am-match-card__num">MATCH {{ sprintf('%02d', $matchOffset + $i + 1) }}</span>
                    <h3>{{ $tierLabel($pct) }}</h3>
                </div>
                <div class="ur-am-score"><b>{{ $pct }}%</b><small>AI SCORE</small></div>
            </div>

            <div class="ur-am-pair">
                <div class="ur-am-pair__side ur-am-pair__side--client">
                    <div class="ur-am-pair__label">YOUR CLIENT</div>
                    <div class="ur-am-pair__head">
                        <img class="ur-am-pair__photo" src="{{ $proposalProfile->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
                        <div>
                            <div class="ur-am-pair__id">{{ $proposalName }}</div>
                            <div class="ur-am-pair__meta">{{ $proposal->dataid }} &bull; {{ ucfirst($proposal->gender) }}@if($proposalAge) &bull; {{ $proposalAge }}@endif @if(!empty($proposalProfile->lbl_marital_status)) &bull; {{ $proposalProfile->lbl_marital_status }}@endif</div>
                        </div>
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $proposalProfile])
                    <div class="ur-am-pair__field"><span>Profession</span><b>{{ $proposal->profession ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Location</span><b>{{ $proposalProfile->lbl_city ?: $proposalProfile->lbl_con_of_residence ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Education</span><b>{{ $proposalProfile->lbl_education ?: '—' }}</b></div>
                </div>
                <div class="ur-am-pair__connector"><i class="fa fa-magic"></i><span>AI MATCH</span></div>
                <div class="ur-am-pair__side ur-am-pair__side--match">
                    <div class="ur-am-pair__label">RECOMMENDED PROFILE</div>
                    <div class="ur-am-pair__head">
                        <img class="ur-am-pair__photo" src="{{ $match->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender) }}';">
                        <div>
                            <div class="ur-am-pair__id">{{ $matchName }}</div>
                            <div class="ur-am-pair__meta">{{ $match->dataid }} &bull; {{ ucfirst($match->gender) }}@if($matchAge) &bull; {{ $matchAge }}@endif @if(!empty($match->lbl_marital_status)) &bull; {{ $match->lbl_marital_status }}@endif</div>
                        </div>
                    </div>
                    @include('team.partials.classification-badges', ['profile' => $match])
                    <div class="ur-am-pair__field"><span>Profession</span><b>{{ $match->profession ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Location</span><b>{{ $match->lbl_city ?: $match->lbl_con_of_residence ?: '—' }}</b></div>
                    <div class="ur-am-pair__field"><span>Education</span><b>{{ $match->lbl_education ?: '—' }}</b></div>
                </div>
            </div>

            <div class="ur-am-why">
                @php $checkCount = count($match->compat['checks'] ?? []); @endphp
                <div class="ur-am-why__head">Why AI selected this match <span>{{ $checkCount }} check{{ $checkCount == 1 ? '' : 's' }}</span></div>
                <div class="ur-am-why__grid">
                    @foreach(($match->compat['checks'] ?? []) as $check)
                        <div class="ur-am-why-pill {{ $check['matched'] ? 'ur-am-why-pill--ok' : 'ur-am-why-pill--warn' }}">
                            <i class="fa {{ $check['matched'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                            {{ $check['matched'] ? $check['label'] : $check['factor'].' needs review' }}
                        </div>
                    @endforeach
                    @if($bothAbroad)
                        <div class="ur-am-why-pill ur-am-why-pill--ok"><i class="fa fa-check-circle"></i> Both profiles are abroad-based</div>
                    @endif
                </div>
            </div>

            <div class="ur-am-owner">
                <div class="ur-am-owner__avatar">{{ strtoupper(substr($match->added_by_name, 0, 1)) }}</div>
                <div class="ur-am-owner__body">
                    <div class="ur-am-owner__label">MATCHED PROFILE OWNER</div>
                    <div class="ur-am-owner__name">{{ $match->added_by_name }}@if(!empty($match->added_by_premium)) @include('team.partials.premium-badge')@endif</div>
                    <div class="ur-am-owner__meta">{{ $match->added_by_experience }}</div>
                </div>
                @if($match->added_by_experience)<span class="ur-am-owner__role">{{ $match->added_by_experience }}</span>@endif
                <a class="ur-am-owner__whatsapp" href="{{ route('team.proposals.share.whatsapp', $match->dataid) }}" target="_blank" rel="noopener" title="Message {{ $match->added_by_name }}"><i class="fa fa-whatsapp"></i></a>
            </div>

            <div class="ur-am-actions">
                <a href="{{ route('team.proposals.view', $match->dataid) }}" onclick="event.preventDefault(); openClientFile('{{ $match->dataid }}');"><i class="fa fa-eye"></i> View Match</a>
                @if($match->already_successful)
                    <button type="button" class="ur-am-btn--done" disabled><i class="fa fa-trophy"></i> Matched</button>
                @else
                    <form method="POST" action="{{ route('team.successful-matches.store') }}" onsubmit="return false;" style="flex:1 1 auto;">
                        @csrf
                        <input type="hidden" name="proposal_dataid" value="{{ $proposal->dataid }}">
                        <input type="hidden" name="counterpart_dataid" value="{{ $match->dataid }}">
                        <button type="button" class="ur-am-btn--dark" style="width:100%;" onclick="swalConfirm('Mark as Successful Match', 'Mark {{ $proposalName }} and {{ $matchName }} as a successful match?', () => this.closest('form').submit());"><i class="fa fa-trophy"></i> Mark Successful</button>
                    </form>
                @endif
                <a href="{{ route('team.matches.forward', [$proposal->dataid, $match->dataid]) }}" data-payload-url="{{ route('team.matches.forward.payload', [$proposal->dataid, $match->dataid]) }}" target="_blank" rel="noopener" class="ur-am-btn--whatsapp js-share-proposal"><i class="fa fa-whatsapp"></i> Forward Both</a>
            </div>
        </div>
    @endforeach
