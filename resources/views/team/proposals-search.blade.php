{{--
    "Search Proposals" — the global team-added pool (everyone's), with
    filters cloned from HomeController::search()'s field set (just
    `added_by IS NOT NULL` instead of `IS NULL`). See
    TeamController::searchProposals(). This is what the old single "Team
    Dashboard" page used to show unconditionally — now split out as its own
    page as part of the Dashboard redesign.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Search Proposals')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-team-page__head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .ur-team-page__head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0; }
    .ur-team-page__count { font-size: 13px; color: #6B7570; }
    .ur-team-search-form { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 16px; margin-bottom: 22px; display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; align-items: end; }
    .ur-team-search-form label { font-size: 12px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-team-search-form .form-control, .ur-team-search-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; width: 100%; font-size: 13px; }
    .ur-team-search-form button { height: 38px; border-radius: 999px; background: #C9974D; color: #fff; border: none; padding: 0 20px; font-size: 13px; font-weight: 700; }
    .ur-team-page .pagination { gap: 6px; flex-wrap: wrap; margin-top: 26px; }
    .ur-team-page .pagination .page-link, .ur-team-page .pagination .page-item > span { border-radius: 999px !important; min-width: 38px; text-align: center; }
    .ur-team-page .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
    .ur-team-added-by { font-size: 12px; color: #6B7570; margin-top: 6px; }
    .ur-team-added-by i { color: #C9974D; margin-right: 4px; }

    .ur-paste-box { background: #FCF6EA; border: 1px solid #E9D2A0; border-radius: 12px; padding: 18px; margin-bottom: 18px; }
    .ur-paste-box h3 { font-size: 14px; font-weight: 700; color: #123A2E; margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
    .ur-paste-box p { font-size: 12.5px; color: #6B7570; margin: 0 0 10px; }
    .ur-paste-box textarea { width: 100%; border: 1px solid #E9D2A0; border-radius: 8px; padding: 10px 12px; font-size: 12.5px; font-family: monospace; height: 110px; }
    .ur-paste-box__actions { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
    .ur-paste-box__fill-btn { background: #123A2E; color: #fff; border: none; border-radius: 999px; padding: 9px 18px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
    .ur-paste-box__fill-btn:hover { background: #0F2E24; }
    .ur-paste-box__status { font-size: 12px; color: #2E7D5B; font-weight: 600; }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <h1>Search Proposals</h1>
    </div>

    <div class="ur-paste-box">
        <h3><i class="fa fa-clipboard" style="color:#C9974D;"></i> Paste a client's details to search for their match</h3>
        <p>Paste the client's filled-in template here — this reads their own details (gender, age, religion, caste, city, qualification) and fills in the filters below, to find their record or similar profiles. Review the filters, then click Search.</p>
        <textarea id="search_paste_box" placeholder="PERSONAL INFORMATION&#10;Gender: ...&#10;...&#10;YOUR REQUIREMENTS:&#10;Age Limit: ...&#10;..."></textarea>
        <div class="ur-paste-box__actions">
            <button type="button" class="ur-paste-box__fill-btn" id="search_paste_fill_btn"><i class="fa fa-magic"></i> Fill filters from this text</button>
            <span class="ur-paste-box__status" id="search_paste_status"></span>
        </div>
    </div>

    <form method="GET" action="{{ route('team.proposals.search') }}" class="ur-team-search-form" id="search_form">
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
            <label>Country</label>
            <select name="country" class="form-control">
                <option value="">Any</option>
                @foreach($countries as $country)
                    <option value="{{ $country->dataid }}" {{ request('country') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>City</label>
            <input type="text" class="form-control" name="city" value="{{ request('city') }}">
        </div>
        <div>
            <label>Profession</label>
            <input type="text" class="form-control" name="profession" value="{{ request('profession') }}">
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
        <div>
            <button type="submit"><i class="fa fa-search"></i> Search</button>
        </div>
    </form>

    @include('team.partials.proposal-grid', [
        'members' => $members, 'resultCount' => $resultCount, 'currentPage' => $currentPage, 'numPages' => $numPages,
        'paginationRoute' => 'team.proposals.search',
        'paginationParams' => request()->except('page'),
        'emptyMessage' => 'No proposals match these filters.',
    ])
</div>

<script>
(function () {
    var form = document.getElementById('search_form');

    function norm(s) {
        return (s || '').toString().toLowerCase().replace(/['".]/g, '').replace(/\s+/g, ' ').trim();
    }
    function setVal(name, value) {
        var el = form.querySelector('[name="' + name + '"]');
        if (el && value !== undefined && value !== null && String(value).trim() !== '') {
            el.value = value;
        }
    }
    function selectFuzzy(name, text) {
        var el = form.querySelector('select[name="' + name + '"]');
        if (!el || !text) return false;
        var target = norm(text);
        var options = Array.from(el.options);
        var match = options.find(function (o) { return norm(o.textContent) === target; })
            || options.find(function (o) { return target.indexOf(norm(o.textContent)) !== -1 && norm(o.textContent).length > 2; })
            || options.find(function (o) { return norm(o.textContent).indexOf(target) !== -1 && target.length > 2; });
        if (match) { el.value = match.value; return true; }
        return false;
    }
    // Small tolerance around a stated age, rather than an exact single-year
    // match — the point is finding this person/similar profiles, not
    // filtering out anyone a year off.
    function fillAgeAround(value) {
        var m = String(value).match(/(\d{1,3})/);
        if (!m) return;
        var n = parseInt(m[1], 10);
        if (n < 18 || n > 99) return;
        setVal('aged_from', Math.max(18, n - 2));
        setVal('aged_to', Math.min(99, n + 2));
    }

    // This searches for THIS PERSON's own record / similar profiles — same
    // gender, and every filter pulled from their OWN attributes (Personal
    // Information / Religion / Residence / Education / Occupation).
    // "Your Requirements" describes what THEY want in a partner, not
    // themselves, so it's deliberately never used here.
    var pastedGender = null;
    var SECTIONS = [
        { test: /personal\s*information/i, fields: {
            'gender': function (v) { pastedGender = /female/i.test(v) ? 'female' : 'male'; },
            'age': fillAgeAround,
            'marital status': function (v) { selectFuzzy('marital_status', v); },
        }},
        { test: /education\s*details?/i, fields: {
            'qualification': function (v) {
                // No fallback into Profession here — that filter does a LIKE
                // match, so a whole sentence in it would return zero
                // results instead of harmlessly doing nothing.
                if (selectFuzzy('education', v)) return;
                var known = ['doctor', 'engineer', 'teacher', 'business', 'lawyer', 'accountant', 'banker'];
                var found = known.find(function (k) { return norm(v).indexOf(k) !== -1; });
                if (found) setVal('profession', found);
            },
        }},
        { test: /occupation\s*detail?s?/i, fields: {
            'job/business': function (v) { setVal('profession', v); },
        }},
        { test: /religion\s*details?/i, fields: {
            'religion': function (v) { selectFuzzy('religion', v); },
            'cast': function (v) { selectFuzzy('caste', v); },
            'caste': function (v) { selectFuzzy('caste', v); },
        }},
        { test: /resid[ae]nce\s*details?/i, fields: {
            'city': function (v) { setVal('city', v); },
            'current city': function (v) { setVal('current_city', v); },
        }},
        // Recognized as section boundaries with no handlers, so lines
        // under them are correctly ignored instead of leaking into
        // whichever section came right before (e.g. "City:" under Your
        // Requirements would otherwise silently overwrite the real
        // Residence Details "City:" already parsed above it).
        { test: /family\s*details?/i, fields: {} },
        { test: /your\s*requirements?/i, fields: {} },
    ];

    function parseAndFill(rawText) {
        var lines = rawText.split(/\r?\n/);
        var currentSection = null;
        var filledCount = 0;

        lines.forEach(function (rawLine) {
            var line = rawLine.replace(/^[^a-zA-Z]*/, '').trim();
            if (!line) return;

            var matchedSection = SECTIONS.find(function (s) { return s.test.test(line); });
            if (matchedSection) {
                currentSection = matchedSection;
                return;
            }
            if (!currentSection) return;

            var colonIndex = line.indexOf(':');
            if (colonIndex === -1) return;
            var label = norm(line.substring(0, colonIndex));
            var value = line.substring(colonIndex + 1).trim();
            if (!value) return;

            var handler = currentSection.fields[label];
            if (handler) {
                handler(value);
                filledCount++;
            }
        });

        if (pastedGender) {
            setVal('gender', pastedGender);
            filledCount++;
        }

        return filledCount;
    }

    document.getElementById('search_paste_fill_btn').addEventListener('click', function () {
        var text = document.getElementById('search_paste_box').value;
        if (!text.trim()) return;
        var count = parseAndFill(text);
        document.getElementById('search_paste_status').textContent = count > 0
            ? ('Filled ' + count + ' filter' + (count === 1 ? '' : 's') + ' — review, then click Search.')
            : 'Could not recognize any fields in that text — please set filters manually.';
    });
})();
</script>
@endsection
