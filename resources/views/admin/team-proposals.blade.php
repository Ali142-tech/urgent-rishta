{{--
    "Team Proposals" — every profile a team member has manually added
    (`added_by` IS NOT NULL), split out of the regular Member Profiles list
    (admin/profiles) so login-less shell proposals stop appearing mixed in
    with real self-registered members. See AdminController::teamProposals().
    Cards reuse member/partials/member-card.blade.php's "team card" mode
    (same one team/proposals-search.blade.php and team/proposals-mine.blade.php
    use), just listing the whole pool instead of one matchmaker's own.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Team Proposals')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
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

    .ur-tp-search { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 16px 18px; margin-bottom: 20px; display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .ur-tp-search .form-group { flex: 1 1 220px; }
    .ur-tp-search label { display: block; font-size: 12.5px; font-weight: 700; color: #123A2E; margin-bottom: 6px; }
    .ur-tp-search input[type=search], .ur-tp-search select { width: 100%; border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 12px; font-size: 13.5px; }
    .ur-tp-search-btn { background: #123A2E; color: #fff; border: none; border-radius: 8px; height: 38px; padding: 0 20px; font-size: 13px; font-weight: 700; }
    .ur-tp-clear-btn { display: inline-flex; align-items: center; height: 38px; padding: 0 16px; border: 1px solid #E7E2D6; border-radius: 8px; font-size: 13px; color: #5B6560; text-decoration: none; }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <div>
            <h1>Team Proposals</h1>
            <p>Every profile a matchmaker has manually added to the shared network — kept separate from self-registered Member Profiles.</p>
        </div>
    </div>

    <div class="ur-tp-search">
        <form method="GET" action="{{ route('admin.team-proposals') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; flex:1;">
            <div class="form-group">
                <label for="keyword">Search by name, profession, city or ID</label>
                <input type="search" name="keyword" id="keyword" placeholder="Search proposals..." value="{{ request('keyword') }}" autocomplete="off">
            </div>
            <div class="form-group" style="flex:0 0 220px;">
                <label for="matchmaker">Added by</label>
                <select name="matchmaker" id="matchmaker">
                    <option value="">Any matchmaker</option>
                    @foreach($matchmakers as $matchmaker)
                        <option value="{{ $matchmaker->id }}" {{ request('matchmaker') == $matchmaker->id ? 'selected' : '' }}>{{ $matchmaker->first_name }} {{ $matchmaker->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="ur-tp-search-btn">Search</button>
            @if(request()->filled('keyword') || request()->filled('matchmaker'))
            <a href="{{ route('admin.team-proposals') }}" class="ur-tp-clear-btn">Clear</a>
            @endif
        </form>
    </div>

    @include('team.partials.proposal-grid', [
        'members' => $members, 'resultCount' => $resultCount, 'currentPage' => $currentPage, 'numPages' => $numPages,
        'paginationRoute' => 'admin.team-proposals',
        'paginationParams' => request()->except('page'),
        'emptyMessage' => 'No team-added proposals match this search.',
    ])
</div>
@endsection
