{{--
    Shared proposal grid + pagination — used by team/proposals-mine.blade.php
    and team/proposals-search.blade.php (previously duplicated inline in the
    old single team/dashboard.blade.php before the Dashboard redesign split
    it into separate pages). Expects: $members, $resultCount, $currentPage,
    $numPages, $paginationRoute (route name), $paginationParams (array,
    optional — extra query params to preserve across pages, e.g. search
    filters), $emptyMessage (optional).
--}}
@php
    $paginationParams = $paginationParams ?? [];
@endphp
@if($resultCount > 0)
<p class="ur-team-page__count">{{ $resultCount }} proposal{{ $resultCount == 1 ? '' : 's' }}</p>
@endif

@if(!empty($members) && sizeof($members) > 0)
<div class="member-results">
    @foreach ($members as $member)
        @include('member.partials.member-card', ['member' => $member, 'addedByName' => $member->added_by_name])
    @endforeach
</div>

@if($numPages > 1)
<ul class="pagination">
    @if($currentPage != 1)
    <li class="page-item"><a href="{{ route($paginationRoute, array_merge($paginationParams, ['page' => $currentPage - 1])) }}" class="page-link">Previous</a></li>
    @endif

    @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route($paginationRoute, array_merge($paginationParams, ['page' => $i])) }}" class="page-link">{{ $i }}</a></li>
    @endfor

    @if($currentPage < $numPages)
    <li class="page-item"><a href="{{ route($paginationRoute, array_merge($paginationParams, ['page' => $currentPage + 1])) }}" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
    @endif
</ul>
@endif
@else
<div class="block block--style-3 list z-depth-1-top">
    <i>{{ $emptyMessage ?? 'No proposals found.' }}</i>
</div>
@endif
