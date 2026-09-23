{{--
    "AI Matches" — one row per proposal, collapsed by default (a "face
    pile" preview + match count), expandable to show the full preview
    strip. Search/score-filter/sort are client-side JS over the current
    page's already-loaded rows (kept to the page's ~10-50 rows, never the
    whole system) — "Most matches first" etc. only reorders WITHIN the
    loaded page, it does not re-fetch across pages. "New this week" is an
    approximation (candidate added in the last 7 days, checked only on
    the 3 preview matches per row) — see TeamController::matches()'s
    docblock for why a real "first seen" tracker isn't built here.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'AI Matches')
@section('main-content')
<style>
    .ur-am-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 18px; }
    .ur-am-badge { display: inline-flex; align-items: center; gap: 6px; background: #FCF3E3; color: #C9974D; font-size: 11px; font-weight: 700; letter-spacing: .04em; padding: 4px 10px; border-radius: 999px; margin-bottom: 8px; }
    .ur-am-header h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 26px; margin: 0; }
    .ur-am-header p { color: #6B7570; font-size: 13.5px; margin: 4px 0 0; }
    .ur-am-stats { display: flex; gap: 10px; }
    .ur-am-stat { border-radius: 12px; padding: 10px 18px; text-align: center; min-width: 100px; }
    .ur-am-stat--light { background: #fff; border: 1px solid #E7E2D6; }
    .ur-am-stat--dark { background: #123A2E; color: #fff; }
    .ur-am-stat__label { display: block; font-size: 11px; opacity: .8; }
    .ur-am-stat__value { display: block; font-size: 22px; font-weight: 700; font-family: 'Playfair Display', serif; }
    .ur-am-stat--dark .ur-am-stat__value { color: #C9974D; }

    .ur-am-toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 12px; margin-bottom: 12px; }
    .ur-am-toolbar input[type=text] { flex: 1 1 220px; border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 12px; font-size: 13px; }
    .ur-am-toolbar select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; font-size: 13px; }
    .ur-am-toolbar button { border: 1px solid #E7E2D6; background: #fff; border-radius: 8px; height: 38px; padding: 0 14px; font-size: 12.5px; font-weight: 700; color: #123A2E; cursor: pointer; }
    .ur-am-toolbar button:hover { background: #F6F4EF; }
    .ur-am-showing { font-size: 12.5px; color: #6B7570; margin-bottom: 10px; }

    .ur-am-row { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 16px 18px; margin-bottom: 12px; }
    .ur-am-row__head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; cursor: pointer; }
    .ur-am-row__who { display: flex; align-items: center; gap: 12px; }
    .ur-am-row__avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
    .ur-am-row__name { font-weight: 700; color: #123A2E; font-size: 15px; }
    .ur-am-row__meta { font-size: 12px; color: #6B7570; }
    .ur-am-row__actions { display: flex; align-items: center; gap: 10px; }
    .ur-am-facepile { display: flex; }
    .ur-am-facepile img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; margin-left: -10px; }
    .ur-am-facepile img:first-child { margin-left: 0; }
    .ur-am-count-pill { background: #E7F3EC; color: #123A2E; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }
    .ur-am-new-pill { background: #FCF3E3; color: #C9974D; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 999px; white-space: nowrap; }
    .ur-am-chevron { transition: transform .15s ease; color: #6B7570; }
    .ur-am-row.is-expanded .ur-am-chevron { transform: rotate(180deg); }
    .ur-am-view-btn { background: #123A2E; color: #fff !important; font-size: 12.5px; font-weight: 700; padding: 9px 16px; border-radius: 999px; text-decoration: none; white-space: nowrap; }
    .ur-am-view-btn:hover { background: #0F2E24; }

    .ur-am-previews { display: none; gap: 12px; flex-wrap: wrap; margin-top: 16px; padding-top: 16px; border-top: 1px dashed #E7E2D6; }
    .ur-am-row.is-expanded .ur-am-previews { display: flex; }
    .ur-am-tile { flex: 1 1 220px; max-width: 260px; border: 1px solid #E7E2D6; border-radius: 12px; padding: 10px; display: flex; gap: 10px; align-items: center; text-decoration: none; }
    .ur-am-tile__photo { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
    .ur-am-tile__body { flex: 1; min-width: 0; }
    .ur-am-tile__name { font-weight: 700; color: #123A2E; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ur-am-tile__meta { font-size: 11px; color: #6B7570; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ur-am-tile__bar-row { display: flex; align-items: center; gap: 6px; margin-top: 4px; }
    .ur-am-tile__bar-track { flex: 1; height: 4px; border-radius: 3px; background: #E7E2D6; overflow: hidden; }
    .ur-am-tile__bar-fill { display: block; height: 100%; background: #C9974D; }
    .ur-am-tile__pct { font-size: 11px; font-weight: 700; color: #1C2321; }

    .ur-am-pagination { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
    .ur-am-perpage { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #6B7570; }
    .ur-am-perpage select { border: 1px solid #E7E2D6; border-radius: 8px; height: 34px; padding: 0 8px; }
    .ur-am-pagination .pagination { margin: 0; gap: 6px; flex-wrap: wrap; }
    .ur-am-pagination .pagination .page-link, .ur-am-pagination .pagination .page-item > span { border-radius: 999px !important; min-width: 34px; text-align: center; }
    .ur-am-pagination .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
</style>

<div class="ur-am-header">
    <div>
        <span class="ur-am-badge"><i class="fa fa-bolt"></i> AI POWERED</span>
        <h1>AI Matches</h1>
        <p>Suggested rishtas based on each proposal's Partner Requirements</p>
    </div>
    <div class="ur-am-stats">
        <div class="ur-am-stat ur-am-stat--light">
            <span class="ur-am-stat__label">Proposals set up</span>
            <span class="ur-am-stat__value">{{ $totalProposals }}</span>
        </div>
        <div class="ur-am-stat ur-am-stat--light">
            <span class="ur-am-stat__label">New this week</span>
            <span class="ur-am-stat__value">{{ $newThisWeekCount }}</span>
        </div>
        <div class="ur-am-stat ur-am-stat--dark">
            <span class="ur-am-stat__label">Total matches</span>
            <span class="ur-am-stat__value">{{ $totalMatchesThisPage }}</span>
        </div>
    </div>
</div>

@if($rows->isNotEmpty())
<div class="ur-am-toolbar">
    <input type="text" id="am_filter_text" placeholder="Filter proposals by name or ID...">
    <select id="am_filter_score">
        <option value="0">Any match score</option>
        <option value="70">70%+ best match</option>
        <option value="80">80%+ best match</option>
        <option value="90">90%+ best match</option>
    </select>
    <select id="am_sort">
        <option value="default">Most recent first</option>
        <option value="matches_desc">Most matches first</option>
        <option value="score_desc">Highest score first</option>
        <option value="name_asc">Name A-Z</option>
    </select>
    <button type="button" id="am_expand_all">Expand all</button>
    <button type="button" id="am_collapse_all">Collapse all</button>
</div>

<p class="ur-am-showing">Showing {{ (($currentPage - 1) * $pageSize) + 1 }}&ndash;{{ min($currentPage * $pageSize, $totalProposals) }} of {{ $totalProposals }} proposals</p>

<div id="am_rows">
    @foreach($rows as $row)
        @php
            $proposal = $row['proposal'];
            $age = $proposal->birthday ? date_diff(date_create($proposal->birthday), date_create('now'))->y : null;
        @endphp
        <div class="ur-am-row" data-name="{{ strtolower($proposal->first_name.' '.$proposal->last_name.' '.$proposal->dataid) }}" data-matches="{{ $row['matchCount'] }}" data-score="{{ $row['bestScore'] }}" data-updated="{{ $proposal->updated_at->timestamp }}">
            <div class="ur-am-row__head" onclick="amToggleRow(this.closest('.ur-am-row'))">
                <div class="ur-am-row__who">
                    <img class="ur-am-row__avatar" src="{{ $proposal->profile()->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($proposal->gender) }}';">
                    <div>
                        <div class="ur-am-row__name">{{ $proposal->first_name }} {{ $proposal->last_name }}</div>
                        <div class="ur-am-row__meta">ID {{ $proposal->dataid }} {{ $age ? '&bull; '.$age : '' }} {{ $proposal->lbl_city ? '&bull; '.$proposal->lbl_city : '' }} {{ $proposal->profession ? '&bull; '.$proposal->profession : '' }}</div>
                    </div>
                </div>
                <div class="ur-am-row__actions">
                    @if($row['previewMatches']->isNotEmpty())
                    <div class="ur-am-facepile">
                        @foreach($row['previewMatches'] as $match)
                            <img src="{{ $match->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender) }}';">
                        @endforeach
                    </div>
                    @endif
                    @if($row['newCount'] > 0)
                    <span class="ur-am-new-pill">{{ $row['newCount'] }} new</span>
                    @endif
                    <span class="ur-am-count-pill">{{ $row['matchCount'] }} match{{ $row['matchCount'] == 1 ? '' : 'es' }}</span>
                    <i class="fa fa-chevron-down ur-am-chevron"></i>
                </div>
            </div>

            @if($row['previewMatches']->isNotEmpty())
            <div class="ur-am-previews">
                @foreach($row['previewMatches'] as $match)
                    @php $pct = $match->preview_compat['percent'] ?? 0; @endphp
                    <a href="{{ route('team.matches.show', $proposal->dataid) }}" class="ur-am-tile">
                        <img class="ur-am-tile__photo" src="{{ $match->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender) }}';">
                        <div class="ur-am-tile__body">
                            <div class="ur-am-tile__name">{{ $match->first_name }}</div>
                            <div class="ur-am-tile__meta">{{ $match->lbl_city }} &bull; {{ $match->profession }}</div>
                            <div class="ur-am-tile__bar-row">
                                <span class="ur-am-tile__bar-track"><span class="ur-am-tile__bar-fill" style="width:{{ $pct }}%"></span></span>
                                <span class="ur-am-tile__pct">{{ $pct }}%</span>
                            </div>
                        </div>
                    </a>
                @endforeach
                <a href="{{ route('team.matches.show', $proposal->dataid) }}" class="ur-am-view-btn" style="align-self:center;">View all &rarr;</a>
            </div>
            @endif
        </div>
    @endforeach
</div>

<div class="ur-am-pagination">
    <form class="ur-am-perpage" method="GET" action="{{ route('team.matches') }}">
        <label for="am_per_page">Per page</label>
        <select name="per_page" id="am_per_page" onchange="this.form.submit()">
            @foreach([5, 10, 20, 50] as $size)
                <option value="{{ $size }}" {{ $pageSize == $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
    </form>

    @if($numPages > 1)
    <ul class="pagination">
        @if($currentPage != 1)
        <li class="page-item"><a href="{{ route('team.matches', ['page' => $currentPage - 1, 'per_page' => $pageSize]) }}" class="page-link">&larr; Prev</a></li>
        @endif
        @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route('team.matches', ['page' => $i, 'per_page' => $pageSize]) }}" class="page-link">{{ $i }}</a></li>
        @endfor
        @if($currentPage < $numPages)
        <li class="page-item"><a href="{{ route('team.matches', ['page' => $currentPage + 1, 'per_page' => $pageSize]) }}" class="page-link">Next &rarr;</a></li>
        @endif
    </ul>
    @endif
</div>

<script>
    function amToggleRow(row) {
        row.classList.toggle('is-expanded');
    }
    document.getElementById('am_expand_all').addEventListener('click', function () {
        document.querySelectorAll('#am_rows .ur-am-row').forEach(r => r.classList.add('is-expanded'));
    });
    document.getElementById('am_collapse_all').addEventListener('click', function () {
        document.querySelectorAll('#am_rows .ur-am-row').forEach(r => r.classList.remove('is-expanded'));
    });

    function amApplyFilters() {
        var text = document.getElementById('am_filter_text').value.trim().toLowerCase();
        var minScore = parseInt(document.getElementById('am_filter_score').value, 10);
        document.querySelectorAll('#am_rows .ur-am-row').forEach(function (row) {
            var matchesText = !text || row.dataset.name.indexOf(text) !== -1;
            var matchesScore = parseInt(row.dataset.score, 10) >= minScore;
            row.style.display = (matchesText && matchesScore) ? '' : 'none';
        });
    }
    document.getElementById('am_filter_text').addEventListener('input', amApplyFilters);
    document.getElementById('am_filter_score').addEventListener('change', amApplyFilters);

    document.getElementById('am_sort').addEventListener('change', function () {
        var container = document.getElementById('am_rows');
        var rows = Array.from(container.querySelectorAll('.ur-am-row'));
        var mode = this.value;
        rows.sort(function (a, b) {
            if (mode === 'matches_desc') return b.dataset.matches - a.dataset.matches;
            if (mode === 'score_desc') return b.dataset.score - a.dataset.score;
            if (mode === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
            return b.dataset.updated - a.dataset.updated; // default: most recent first
        });
        rows.forEach(r => container.appendChild(r));
    });
</script>
@else
<div class="block block--style-3 list z-depth-1-top">
    <i>No AI matches yet — add Partner Requirements/Preferred fields on a proposal (under My Proposals &rarr; Edit) to start finding matches for it.</i>
</div>
@endif
@endsection
