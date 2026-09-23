{{--
    "My Proposals" — this team member's own added proposals only
    (added_by = auth()->id()). See TeamController::myProposals(). Split out
    from the old single "Team Dashboard" page (now team/overview.blade.php)
    as part of the Dashboard redesign.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Proposals')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-team-page__head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .ur-team-page__head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0; }
    .ur-team-page__count { font-size: 13px; color: #6B7570; }
    .ur-team-page__add { display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 22px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; }
    .ur-team-page__add:hover { background: #B07C3D; }
    .ur-team-page .pagination { gap: 6px; flex-wrap: wrap; margin-top: 26px; }
    .ur-team-page .pagination .page-link, .ur-team-page .pagination .page-item > span { border-radius: 999px !important; min-width: 38px; text-align: center; }
    .ur-team-page .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
    .ur-team-added-by { font-size: 12px; color: #6B7570; margin-top: 6px; }
    .ur-team-added-by i { color: #C9974D; margin-right: 4px; }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <h1>My Proposals</h1>
        <a href="{{ route('team.proposals.create') }}" class="ur-team-page__add"><i class="fa fa-plus"></i> Add Proposal</a>
    </div>

    @include('team.partials.proposal-grid', [
        'members' => $members, 'resultCount' => $resultCount, 'currentPage' => $currentPage, 'numPages' => $numPages,
        'paginationRoute' => 'team.proposals.mine',
        'emptyMessage' => 'You haven\'t added any proposals yet — use "Add Proposal" above to add the first one.',
    ])
</div>
@endsection
