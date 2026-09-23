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
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <h1>Search Proposals</h1>
    </div>

    <form method="GET" action="{{ route('team.proposals.search') }}" class="ur-team-search-form">
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
@endsection
