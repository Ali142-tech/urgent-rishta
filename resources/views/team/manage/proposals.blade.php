{{--
    "Team Proposals" — every profile a team member has manually added
    (`added_by` IS NOT NULL), split out of the regular Member Profiles list
    (admin/profiles) so login-less shell proposals stop appearing mixed in
    with real self-registered members. See TeamAdminController::proposals().
    Cards reuse member/partials/member-card.blade.php's "team card" mode
    (same one team/proposals-search.blade.php and team/proposals-mine.blade.php
    use), just listing the whole pool instead of one matchmaker's own.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', !empty($privateOnly) ? 'Private Proposals' : 'Team Proposals')
@push('styles')
<link rel="stylesheet" href="/css/ur-multiselect.css?v={{ filemtime(public_path('css/ur-multiselect.css')) }}">
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
<link rel="stylesheet" href="/css/ur-team-card.css?v={{ filemtime(public_path('css/ur-team-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-team-page__head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .ur-team-page__head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-team-page__head p { color: #6B7570; font-size: 13.5px; margin: 0; }
    .ur-team-page__count { font-size: 13px; color: #6B7570; }
    .ur-team-page .pagination { gap: 6px; flex-wrap: wrap; margin-top: 26px; }
    .ur-team-page .pagination .page-link, .ur-team-page .pagination .page-item > span { border-radius: 999px !important; min-width: 38px; text-align: center; }
    .ur-team-page .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
    .ur-team-added-by { font-size: 12px; color: #6B7570; margin-top: 6px; }
    .ur-team-added-by i { color: #C9974D; margin-right: 4px; }

    /* search bar: one row on desktop (search box grows, "Added by" fixed, buttons last), stacked on phones; every control is 44px tall */
    .ur-tp-search { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px 20px; margin-bottom: 22px; box-shadow: 0 2px 10px rgba(15,46,36,.04); }
    .ur-tp-search form { display: grid; grid-template-columns: minmax(0, 1fr) 240px auto; gap: 14px; align-items: end; margin: 0; }
    .ur-tp-search .form-group { margin: 0; min-width: 0; }
    .ur-tp-search label { display: block; font-size: 12px; font-weight: 700; letter-spacing: .02em; color: #123A2E; margin: 0 0 7px; }
    .ur-tp-field { position: relative; }
    .ur-tp-field > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9AA5A0; font-size: 14px; pointer-events: none; }
    .ur-tp-search input[type=search], .ur-tp-search select { width: 100%; height: 44px; border: 1px solid #E7E2D6; border-radius: 10px; background-color: #fff; padding: 0 14px; font-size: 14px; color: #1C2321; box-shadow: none; }
    .ur-tp-search input[type=search] { padding-left: 38px; -webkit-appearance: none; appearance: none; }
    .ur-tp-search select { -webkit-appearance: none; appearance: none; padding-right: 36px; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%236B7570' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center; }
    .ur-tp-search input[type=search]:focus, .ur-tp-search select:focus { outline: none; border-color: #C9974D; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .ur-tp-search input[type=search]::-webkit-search-cancel-button { cursor: pointer; }
    .ur-tp-actions { display: flex; gap: 10px; }
    .ur-tp-search-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; background: #123A2E; color: #fff; border: 1px solid #123A2E; border-radius: 10px; height: 44px; padding: 0 24px; font-size: 14px; font-weight: 700; cursor: pointer; }
    /* this page has its own "Searching proposals..." loader, so the gold Pace.js page bar is hidden here */
    .pace, .pace .pace-progress { display: none !important; }
    .ur-tp-search-btn:hover { background: #0D2C23; }
    .ur-tp-clear-btn { display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 20px; border: 1px solid #E7E2D6; border-radius: 10px; background: #fff; font-size: 14px; font-weight: 600; color: #123A2E; text-decoration: none; }
    .ur-tp-clear-btn:hover { background: #F6F4EF; text-decoration: none; }
    .ur-tp-extra { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .ur-tp-extra .select2-container { width: 100% !important; }
    @media (max-width: 575px) { .ur-tp-extra { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 991px) { .ur-tp-search form { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } .ur-tp-search .form-group:first-child { grid-column: 1 / -1; } .ur-tp-actions { grid-column: 1 / -1; } .ur-tp-actions > * { flex: 1 1 0; } }
    @media (max-width: 575px) { .ur-tp-search { padding: 14px; } .ur-tp-search form { grid-template-columns: 1fr; } }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <div>
            @if(!empty($privateOnly))
            <h1><i class="fa fa-lock" style="color:#3B2A4F; font-size:20px;"></i> Private Proposals</h1>
            <p>Only admins, the member who added each one and members you allowed can see these. Use "Make public" on a card to release it. {{ number_format($privateCount) }} in total.</p>
            @else
            <h1>Team Proposals</h1>
            <p>Every profile a matchmaker has manually added to the shared network — kept separate from self-registered Member Profiles. <a href="{{ route('team.manage.private-proposals') }}" style="font-weight:700; color:#3B2A4F;">{{ number_format($privateCount) }} private &rarr;</a></p>
            @endif
        </div>
    </div>

    <div class="ur-tp-search">
        <form method="GET" action="{{ route(!empty($privateOnly) ? 'team.manage.private-proposals' : 'team.manage.proposals') }}">
            <div class="form-group">
                <label for="keyword">Search by ID, profession, city, caste, religion or education</label>
                <div class="ur-tp-field">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" name="keyword" id="keyword" placeholder="Search proposals..." value="{{ request('keyword') }}" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="matchmaker">Added by</label>
                <select name="matchmaker" id="matchmaker">
                    <option value="">Any matchmaker</option>
                    @foreach($matchmakers as $matchmaker)
                        <option value="{{ $matchmaker->id }}" {{ request('matchmaker') == $matchmaker->id ? 'selected' : '' }}>{{ $matchmaker->first_name }} {{ $matchmaker->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ur-tp-actions">
                <button type="submit" class="ur-tp-search-btn"><i class="fa fa-search"></i> Search</button>
                @if(request()->filled('keyword') || request()->filled('matchmaker') || request()->filled('country') || request()->filled('nationality') || request()->filled('sect'))
                <a href="{{ route(!empty($privateOnly) ? 'team.manage.private-proposals' : 'team.manage.proposals') }}" class="ur-tp-clear-btn">Clear</a>
                @endif
            </div>
            <div class="ur-tp-extra">
                <div class="form-group">
                    <label for="tp_country">Country</label>
                    <select name="country[]" id="tp_country" class="ur-multi" multiple data-placeholder="Any">
                        @foreach($countries as $cty)
                            <option value="{{ $cty->dataid }}" {{ in_array((string) $cty->dataid, array_map('strval', (array) request('country')), true) ? 'selected' : '' }}>{{ $cty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tp_nationality">Nationality</label>
                    <select name="nationality[]" id="tp_nationality" class="ur-multi" multiple data-placeholder="Any">
                        @foreach($nationalities as $nat)
                            <option value="{{ $nat->dataid }}" {{ in_array((string) $nat->dataid, array_map('strval', (array) request('nationality')), true) ? 'selected' : '' }}>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tp_sect">Sect</label>
                    <select name="sect[]" id="tp_sect" class="ur-multi" multiple data-placeholder="Any">
                        @foreach($sects as $sectName)
                            <option value="{{ $sectName }}" {{ in_array($sectName, array_map('strval', (array) request('sect')), true) ? 'selected' : '' }}>{{ $sectName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    @php($__searching = request()->filled('keyword') || request()->filled('matchmaker') || request()->filled('country') || request()->filled('nationality') || request()->filled('sect'))
    <div id="tp-results">
    @include('team.partials.proposal-grid', [
        'members' => $members, 'resultCount' => $resultCount, 'currentPage' => $currentPage, 'numPages' => $numPages,
        'paginationRoute' => !empty($privateOnly) ? 'team.manage.private-proposals' : 'team.manage.proposals',
        'paginationParams' => request()->except('page'),
        'emptyTitle' => $__searching ? (request()->filled('keyword') ? 'No matches for "' . e(\Illuminate\Support\Str::limit(request('keyword'), 40)) . '"' : 'No proposals from this matchmaker') : (!empty($privateOnly) ? 'No private proposals yet' : 'No proposals yet'),
        'emptyMessage' => $__searching
            ? 'Check the spelling, try a shorter word, or search by ID (like P-123), profession, city, caste, religion or education.'
            : (!empty($privateOnly) ? 'Use "Make private" on any proposal card in All Proposals and it will show up here.' : 'Proposals added by the team will appear here.'),
        'emptyActionUrl' => $__searching ? route(!empty($privateOnly) ? 'team.manage.private-proposals' : 'team.manage.proposals') : null,
        'emptyActionLabel' => 'Clear search',
    ])
    </div>
</div>

<script>
    // Live search: the cards update as you type (no need to press Search); results are fetched from the same page and swapped in.
    (function () {
        var form = document.querySelector('.ur-tp-search form');
        if (!form) return;
        var input = form.elements['keyword'], select = form.elements['matchmaker'];
        var box = document.getElementById('tp-results');
        var base = form.getAttribute('action');
        var clearBtn = form.querySelector('.ur-tp-clear-btn');
        var countrySel = form.elements['country[]'], natSel = form.elements['nationality[]'], sectSel = form.elements['sect[]'];
        function multi(sel) { return Array.prototype.filter.call(sel.options, function (o) { return o.selected && o.value !== ''; }).map(function (o) { return o.value; }); }
        function sig() { return input.value.trim() + '|' + select.value + '|' + multi(countrySel).join(',') + '|' + multi(natSel).join(',') + '|' + multi(sectSel).join(','); }
        function anyFilter() { return input.value.trim() !== '' || select.value !== '' || multi(countrySel).length > 0 || multi(natSel).length > 0 || multi(sectSel).length > 0; }
        function resetAll() { input.value = ''; select.value = ''; $(countrySel).val(null).trigger('change'); $(natSel).val(null).trigger('change'); $(sectSel).val(null).trigger('change'); }
        var timer = null, xhr = null, last = sig();

        function urlFor() {
            var q = [];
            var term = input.value.trim().replace(/\s+/g, ' ');
            if (term) q.push('keyword=' + encodeURIComponent(term));
            if (select.value) q.push('matchmaker=' + encodeURIComponent(select.value));
            multi(countrySel).forEach(function (v) { q.push('country[]=' + encodeURIComponent(v)); });
            multi(natSel).forEach(function (v) { q.push('nationality[]=' + encodeURIComponent(v)); });
            multi(sectSel).forEach(function (v) { q.push('sect[]=' + encodeURIComponent(v)); });
            return base + (q.length ? '?' + q.join('&') : '');
        }
        function showClear() {
            var on = anyFilter();
            if (clearBtn) { clearBtn.style.display = on ? '' : 'none'; return; }
            if (!on) return;
            clearBtn = document.createElement('a'); clearBtn.className = 'ur-tp-clear-btn'; clearBtn.href = base; clearBtn.textContent = 'Clear';
            form.querySelector('.ur-tp-actions').appendChild(clearBtn);
            bindClear();
        }
        function bindClear() { if (clearBtn) clearBtn.addEventListener('click', function (e) { e.preventDefault(); resetAll(); clearTimeout(timer); run(); input.focus(); }); }
        function fail() { box.innerHTML = '<div class="ur-pg-empty"><div class="ur-pg-empty__icon"><i class="fa fa-exclamation-triangle"></i></div><h3>Could not load the proposals</h3><p>Please check your connection and try again.</p></div>'; }

        function run() {
            var url = urlFor();
            last = sig();
            showClear();
            box.innerHTML = '<div class="ur-pg-empty"><div class="ur-pg-empty__icon"><i class="fa fa-spinner fa-spin"></i></div><h3>Searching proposals...</h3><p>Just a moment.</p></div>';
            if (xhr) xhr.abort();
            xhr = new XMLHttpRequest();
            xhr.open('GET', url);
            xhr.onload = function () {
                if (xhr.status !== 200) return fail();
                var fresh = new DOMParser().parseFromString(xhr.responseText, 'text/html').getElementById('tp-results');
                if (!fresh) return fail();
                box.innerHTML = fresh.innerHTML;
                window.history.replaceState(null, '', url);
            };
            xhr.onerror = fail;
            xhr.send();
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { if (sig() !== last) run(); }, 350);
        });
        select.addEventListener('change', function () { clearTimeout(timer); run(); });
        $(countrySel).add(natSel).add(sectSel).on('change', function () { clearTimeout(timer); if (sig() !== last) run(); });
        form.addEventListener('submit', function (e) { e.preventDefault(); clearTimeout(timer); run(); });
        bindClear();
        // the empty-state "Clear search" button works the same way
        box.addEventListener('click', function (e) {
            var b = e.target.closest ? e.target.closest('.js-tp-clear') : null;
            if (!b) return; e.preventDefault(); resetAll(); run();
        });
    })();
</script>
<script src="/js/ur-multiselect.js?v={{ filemtime(public_path('js/ur-multiselect.js')) }}"></script>
@endsection
