{{--
    "Advanced Search" — the global team-added pool (everyone's), with filters
    cloned from HomeController::search()'s field set (just `added_by IS NOT
    NULL` instead of `IS NULL`). See TeamController::searchProposals().
    Redesigned (Sep 2026) to match the client's "AI Partner Portal" Advanced
    Search mockup: quick keyword/profession/country tabs backed by a
    collapsible "More filters" panel for the rest of the existing filter
    set, a compact result-card list (team/partials/search-result-card.blade.php),
    and a results header showing count + sort order. This is what the old
    single "Team Dashboard" page used to show unconditionally — now split
    out as its own page as part of the Dashboard redesign.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Advanced Search')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-team-page__head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .ur-team-page__head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-team-page__head p { color: #6B7570; font-size: 13.5px; margin: 0; max-width: 640px; }
    .ur-team-page .pagination { gap: 6px; flex-wrap: wrap; margin-top: 26px; }
    .ur-team-page .pagination .page-link, .ur-team-page .pagination .page-item > span { border-radius: 999px !important; min-width: 38px; text-align: center; }
    .ur-team-page .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }

    /* ---------- Search panel ---------- */
    .ur-adv-search { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px 20px; margin-bottom: 22px; }
    .ur-adv-search__tabs { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .ur-adv-search__tab { background: #F6F4EF; border: 1px solid #E7E2D6; color: #1C2321; border-radius: 999px; padding: 9px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
    .ur-adv-search__tab.is-active { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-adv-search__more-btn { margin-left: auto; background: #fff; border: 1px solid #E7E2D6; color: #1C2321; border-radius: 999px; padding: 9px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .ur-adv-search__more-btn.is-active { border-color: #C9974D; color: #C9974D; }
    .ur-adv-search__row { display: flex; flex-wrap: wrap; gap: 12px; align-items: stretch; }
    .ur-adv-search__field { flex: 1; }
    .ur-adv-search__field .form-control, .ur-adv-search__field select { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; width: 100%; font-size: 13.5px; }
    .ur-adv-search__submit { flex-shrink: 0; height: 42px; border-radius: 999px; background: #123A2E; color: #fff; border: none; padding: 0 22px; font-size: 13px; font-weight: 700; white-space: nowrap; }
    .ur-adv-search__submit:hover { background: #0F2E24; }
    .ur-adv-search__clear { flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; height: 42px; border-radius: 999px; background: #fff; color: #6B7570; border: 1px solid #E7E2D6; padding: 0 18px; font-size: 13px; font-weight: 700; white-space: nowrap; text-decoration: none; }
    .ur-adv-search__clear:hover { background: #F6F4EF; color: #1C2321; border-color: #C9974D; text-decoration: none; }
    .ur-adv-search__hint { font-size: 12px; color: #9AA5A0; margin: 12px 0 0; }
    .ur-adv-search__network { font-size: 12px; color: #2E7D5B; font-weight: 700; text-align: right; margin: -4px 0 12px; }

    .ur-adv-search__filters { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin-top: 16px; padding-top: 16px; border-top: 1px dashed #E7E2D6; }
    .ur-adv-search__filters label { font-size: 12px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-adv-search__filters .form-control, .ur-adv-search__filters select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; width: 100%; font-size: 13px; }

    /* ---------- Results header ---------- */
    .ur-adv-search__results-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; font-size: 13px; }
    .ur-adv-search__results-head strong { color: #1C2321; }
    .ur-adv-search__sort { color: #9AA5A0; }
    .ur-adv-search__empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 30px; text-align: center; color: #6B7570; }

    /* ---------- Result cards ---------- */
    .ur-search-results { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; align-items: start; }
    .ur-search-card { display: flex; gap: 16px; background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 14px; transition: box-shadow .2s ease; }
    .ur-search-card:hover { box-shadow: 0 10px 26px rgba(15, 46, 36, .1); }
    .ur-search-card__photo { position: relative; flex: 0 0 120px; width: 120px; height: 120px; border-radius: 10px; overflow: hidden; background: #F6F4EF; display: block; }
    .ur-search-card__country { margin-left: auto; display: inline-flex; align-items: center; gap: 9px; padding: 6px 14px 6px 8px; border-radius: 10px; background: #fff; border: 1px solid #E7E2D6; box-shadow: 0 1px 2px rgba(15,46,36,.06); color: #123A2E; font-size: 12px; font-weight: 700; line-height: 1.2 !important; white-space: nowrap; }
    .ur-search-card__country img { display: block; width: 28px !important; height: auto !important; max-width: none; aspect-ratio: 3 / 2; object-fit: contain; border-radius: 3px; box-shadow: 0 0 0 1px rgba(0,0,0,.14), 0 1px 3px rgba(0,0,0,.18); margin: 0 !important; padding: 0 !important; flex: 0 0 28px; }
    .ur-search-card__country i { width: 28px; text-align: center; color: #9AA5A0; font-size: 15px; }
    .ur-search-card__photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ur-search-card__ribbon { position: absolute; top: 8px; left: -28px; transform: rotate(-45deg); width: 110px; padding: 2px 0; text-align: center; font-size: 9.5px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,.15); }
    .ur-search-card__ribbon--new { background: #C9974D; color: #0F2E24; }
    .ur-search-card__ribbon--updated { background: #123A2E; }
    .ur-search-card__body { flex: 1; min-width: 0; }
    .ur-search-card__top { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .ur-search-card__id { font-weight: 700; color: #123A2E; font-size: 14.5px; }
    .ur-search-card__verified { display: inline-flex; align-items: center; gap: 4px; background: #1E7A46; color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 9px; border-radius: 20px; }
    .ur-search-card__meta { font-size: 13px; color: #6B7570; margin-bottom: 8px; }
    .ur-search-card__badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
    .ur-search-card__badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: .2px; }
    .ur-search-card__badge--premium { background: #FBF0DA; color: #8A6218; }
    .ur-search-card__badge--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-search-card__badge--abroad { background: #E4EEF7; color: #2C5F8A; }
    .ur-search-card__badge--highlight { background: #FBE4E9; color: #A23B57; }
    .ur-search-card__profession { font-size: 12.5px; color: #6B7570; margin-bottom: 6px; }
    .ur-search-card__profession i { color: #C9974D; margin-right: 5px; }
    .ur-search-card__owner { font-size: 12px; color: #6B7570; margin-bottom: 10px; }
    .ur-search-card__owner i { color: #C9974D; margin-right: 4px; }
    .ur-search-card__actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .ur-search-card__btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #E7E2D6; color: #123A2E; background: #fff; border-radius: 999px; padding: 7px 14px; font-size: 12px; font-weight: 700; cursor: pointer; }
    .ur-search-card__btn:hover { background: #F6F4EF; border-color: #C9974D; color: #123A2E; }
    .ur-search-card__btn--dark { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-search-card__btn--dark:hover { background: #0F2E24; color: #fff; }
    .ur-search-card__btn--whatsapp { background: #25D366; border-color: #25D366; color: #fff; }
    .ur-search-card__btn--whatsapp:hover { background: #1DA851; color: #fff; }

    @media (max-width: 900px) {
        .ur-search-results { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .ur-search-card { flex-direction: column; }
        .ur-search-card__photo { width: 100%; height: 160px; }
        .ur-adv-search__row { flex-direction: column; }
        .ur-adv-search__submit, .ur-adv-search__clear { width: 100%; justify-content: center; }
        .ur-adv-search__more-btn { margin-left: 0; }
    }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <div>
            <h1>Advanced search</h1>
            <p>Search the shared team-added network by keyword, profession or country — open "More filters" for the full filter set (gender, age, religion, caste and more).</p>
        </div>
    </div>

    @php
        $activeTab = request('country') ? 'country' : (request('profession') ? 'profession' : 'keyword');
        $hasMoreFilters = collect(['gender', 'aged_from', 'aged_to', 'religion', 'caste', 'marital_status', 'mother_tongue', 'education', 'city', 'current_city'])
            ->contains(fn ($f) => request()->filled($f));
        $selectedCountry = request('country') ? $countries->firstWhere('dataid', request('country')) : null;
    @endphp

    <p class="ur-adv-search__network">{{ number_format($totalNetworkCount) }} profile{{ $totalNetworkCount == 1 ? '' : 's' }} in the shared network</p>

    <form method="GET" action="{{ route('team.proposals.search') }}" class="ur-adv-search" id="search_form">
        <div class="ur-adv-search__tabs">
            <button type="button" class="ur-adv-search__tab {{ $activeTab == 'keyword' ? 'is-active' : '' }}" data-tab="keyword">Search by keyword</button>
            <button type="button" class="ur-adv-search__tab {{ $activeTab == 'profession' ? 'is-active' : '' }}" data-tab="profession">Search by profession</button>
            <button type="button" class="ur-adv-search__tab {{ $activeTab == 'country' ? 'is-active' : '' }}" data-tab="country">Search by country</button>
            <button type="button" class="ur-adv-search__more-btn {{ $hasMoreFilters ? 'is-active' : '' }}" id="more_filters_toggle"><i class="fa fa-sliders"></i> More filters</button>
        </div>

        <div class="ur-adv-search__row">
            <div class="ur-adv-search__field" data-panel="keyword" style="{{ $activeTab == 'keyword' ? '' : 'display:none;' }}">
                <input type="text" class="form-control" name="keyword" placeholder="Search by name, profession or city…" value="{{ request('keyword') }}">
            </div>
            <div class="ur-adv-search__field" data-panel="profession" style="{{ $activeTab == 'profession' ? '' : 'display:none;' }}">
                <input type="text" class="form-control" name="profession" placeholder="e.g. Doctor, Engineer, Teacher…" value="{{ request('profession') }}">
            </div>
            <div class="ur-adv-search__field" data-panel="country" style="{{ $activeTab == 'country' ? '' : 'display:none;' }}">
                <select name="country" class="form-control">
                    <option value="">Any country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ request('country') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="ur-adv-search__submit"><i class="fa fa-search"></i> Search</button>
            <a href="{{ route('team.proposals.search') }}" class="ur-adv-search__clear"><i class="fa fa-times"></i> Clear filters</a>
        </div>

        <p class="ur-adv-search__hint">Search runs against the standardized fields pulled from each pasted profile — every client's original pasted form stays unchanged in their private file.</p>

        <div class="ur-adv-search__filters" id="more_filters_panel" style="{{ $hasMoreFilters ? '' : 'display:none;' }}">
            <div>
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Any</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div>
                <label>Age From</label>
                <input type="number" class="form-control" name="aged_from" value="{{ request('aged_from') }}" min="18" max="99">
            </div>
            <div>
                <label>Age To</label>
                <input type="number" class="form-control" name="aged_to" value="{{ request('aged_to') }}" min="18" max="99">
            </div>
            <div>
                <label>City</label>
                <input type="text" class="form-control" name="city" value="{{ request('city') }}">
            </div>
            <div>
                <label>Religion</label>
                <select name="religion" class="form-control">
                    <option value="">Any</option>
                    @foreach($religions as $religion)
                        <option value="{{ $religion->dataid }}" {{ request('religion') == $religion->dataid ? 'selected' : '' }}>{{ $religion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Caste</label>
                <select name="caste" class="form-control">
                    <option value="">Any</option>
                    @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ request('caste') == $cst->dataid ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Marital Status</label>
                <select name="marital_status" class="form-control">
                    <option value="">Any</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ request('marital_status') == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Mother Tongue</label>
                <select name="mother_tongue" class="form-control">
                    <option value="">Any</option>
                    @foreach($mothertongues as $mothertongue)
                        <option value="{{ $mothertongue->dataid }}" {{ request('mother_tongue') == $mothertongue->dataid ? 'selected' : '' }}>{{ $mothertongue->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Qualification</label>
                <select name="education" class="form-control">
                    <option value="">Any</option>
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ request('education') == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Current City</label>
                <input type="text" class="form-control" name="current_city" value="{{ request('current_city') }}">
            </div>
        </div>
    </form>

    <div class="ur-adv-search__results-head">
        <div><strong>{{ number_format($resultCount) }}</strong> profile{{ $resultCount == 1 ? '' : 's' }} found{{ $selectedCountry ? ' in ' . $selectedCountry->name : '' }}</div>
        <div class="ur-adv-search__sort">Sorted by newest</div>
    </div>

    <div class="ur-search-results">
        @forelse($members as $member)
            @include('team.partials.search-result-card', ['member' => $member])
        @empty
            <div class="ur-adv-search__empty">No proposals match these filters.</div>
        @endforelse
    </div>

    @if($numPages > 1)
    <ul class="pagination">
        @if($currentPage != 1)
        <li class="page-item"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $currentPage - 1])) }}" class="page-link">Previous</a></li>
        @endif

        @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $i])) }}" class="page-link">{{ $i }}</a></li>
        @endfor

        @if($currentPage < $numPages)
        <li class="page-item"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $currentPage + 1])) }}" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
        @endif
    </ul>
    @endif
</div>

<script>
(function () {
    var form = document.getElementById('search_form');
    var tabs = form.querySelectorAll('.ur-adv-search__tab');
    var panels = form.querySelectorAll('.ur-adv-search__field');
    var moreBtn = document.getElementById('more_filters_toggle');
    var morePanel = document.getElementById('more_filters_panel');

    function activateTab(name) {
        tabs.forEach(function (t) { t.classList.toggle('is-active', t.dataset.tab === name); });
        panels.forEach(function (p) {
            var isTarget = p.dataset.panel === name;
            p.style.display = isTarget ? '' : 'none';
            // Clear the other two quick-search fields so switching tabs doesn't
            // silently stack an old value on top of the new one.
            if (!isTarget) {
                var field = p.querySelector('input, select');
                if (field) field.value = '';
            }
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () { activateTab(tab.dataset.tab); });
    });

    moreBtn.addEventListener('click', function () {
        var isOpen = morePanel.style.display !== 'none';
        morePanel.style.display = isOpen ? 'none' : '';
        moreBtn.classList.toggle('is-active', !isOpen);
    });
})();
</script>
@endsection
