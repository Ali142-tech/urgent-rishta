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
<style>
    .ur-pg-empty { background: #fff; border: 1px dashed #DDD5C2; border-radius: 18px; padding: 54px 24px 48px; text-align: center; margin: 4px 0 20px; }
    .ur-pg-empty__icon { width: 76px; height: 76px; margin: 0 auto 18px; border-radius: 50%; background: linear-gradient(135deg, #F6EBD3, #FBF3E0); display: flex; align-items: center; justify-content: center; color: #C9974D; font-size: 30px; box-shadow: 0 0 0 8px rgba(201,151,77,.08); }
    .ur-pg-empty h3 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #123A2E; margin: 0 0 8px; }
    .ur-pg-empty p { max-width: 460px; margin: 0 auto; color: #6B7570; font-size: 14px; line-height: 1.6; }
    .ur-pg-empty__btn { display: inline-flex; align-items: center; gap: 8px; margin-top: 22px; height: 42px; padding: 0 22px; border-radius: 999px; background: #123A2E; color: #fff !important; font-size: 13.5px; font-weight: 700; text-decoration: none !important; border: 0; cursor: pointer; }
    .ur-pg-empty__btn:hover { background: #0D2C23; }
</style>
<div class="ur-pg-empty">
    <div class="ur-pg-empty__icon"><i class="fa fa-search" aria-hidden="true"></i></div>
    <h3>{{ $emptyTitle ?? 'No proposals found' }}</h3>
    <p>{{ $emptyMessage ?? 'Try a different search, or clear the filters to see everyone.' }}</p>
    @if(!empty($emptyActionUrl))
        <a href="{{ $emptyActionUrl }}" class="ur-pg-empty__btn js-tp-clear"><i class="fa fa-times"></i> {{ $emptyActionLabel ?? 'Clear search' }}</a>
    @endif
</div>
@endif
