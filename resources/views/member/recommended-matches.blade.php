{{--
    Dedicated "Recommended Matches" page — destination for the "View
    Recommended Matches"/"View All" links on the Search Profiles page
    (member/searchresults.blade.php). Same underlying profiles as that
    page's 3-card preview widget (User::getRecommendedMatches()), just a
    full, paginated listing. Always authenticated (see
    HomeController::recommendedMatches()).
--}}
@extends('layouts.dashboard')
@section('dashboard-title', 'Recommended Matches')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-rm-page {
        --ur-green: #123A2E;
        --ur-green-dark: #0F2E24;
        --ur-gold: #C9974D;
        --ur-bg: #F6F4EF;
        --ur-border: #E7E2D6;
        --ur-text: #1C2321;
        --ur-text-muted: #6B7570;
    }

    .ur-rm-page__head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 6px;
    }

    .ur-rm-page__head h1 {
        font-family: 'Playfair Display', serif;
        color: var(--ur-green);
        font-weight: 700;
        font-size: 24px;
        margin: 0;
    }

    .ur-rm-page__count {
        font-size: 13px;
        color: var(--ur-text-muted);
    }

    .ur-rm-page__back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ur-text-muted);
        margin-bottom: 22px;
    }

    .ur-rm-page__back:hover {
        color: var(--ur-green);
    }

    .ur-rm-page .pagination {
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .ur-rm-page .pagination .page-link,
    .ur-rm-page .pagination .page-item > span {
        border-radius: 999px !important;
        min-width: 38px;
        text-align: center;
    }

    .ur-rm-page .pagination > .active .page-link,
    .ur-rm-page .pagination > .active .page-link:focus,
    .ur-rm-page .pagination > .active .page-link:hover {
        background-color: var(--ur-green) !important;
        border-color: var(--ur-green) !important;
        color: #fff !important;
    }

    .ur-rm-page .pagination .page-item .page-link:focus,
    .ur-rm-page .pagination .page-item .page-link:hover {
        background-color: var(--ur-bg) !important;
        border-color: var(--ur-border) !important;
        color: var(--ur-green) !important;
    }
</style>

<div class="ur-rm-page">
    <a href="{{ route('searchresults') }}" class="ur-rm-page__back"><i class="fa fa-angle-left"></i> Back to Search Profiles</a>

    <div class="ur-rm-page__head">
        <h1>Recommended Matches For You</h1>
        @if($resultCount > 0)
        <span class="ur-rm-page__count">{{ $resultCount }} profile{{ $resultCount == 1 ? '' : 's' }}</span>
        @endif
    </div>

    @if(!empty($members) && sizeof($members) > 0)
    <div class="member-results">
        @foreach ($members as $member)
            @include('member.partials.member-card', ['member' => $member])
        @endforeach
    </div>

    @if($numPages > 1)
    <ul class="pagination">
        @if($currentPage != 1)
        <li class="page-item"><a href="{{ route('member.recommended-matches', ['page' => $currentPage - 1]) }}" class="page-link">Previous</a></li>
        @endif

        @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route('member.recommended-matches', ['page' => $i]) }}" class="page-link">{{ $i }}</a></li>
        @endfor

        @if($currentPage < $numPages)
        <li class="page-item"><a href="{{ route('member.recommended-matches', ['page' => $currentPage + 1]) }}" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
        @endif
    </ul>
    @endif
    @else
    <div class="block block--style-3 list z-depth-1-top">
        <i>No recommended matches right now — check back soon, or try Search Profiles for a broader look.</i>
    </div>
    @endif
</div>
@endsection
