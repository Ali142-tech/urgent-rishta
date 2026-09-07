<?php use App\User; ?>
@extends('member.searchresults')
@section('search-data')

@if(!empty($hasSearched))
<h3 class="ur-search-results__heading">Search Results</h3>
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage+4<$numPages)
            <li class="paginate_button page-item disabled ur-ellipsis"><span class="page-link">&hellip;</span></li>
            @endif

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
            @endif
        </ul>
    </div>
</div>
@if(!empty($members) && sizeof($members)>0)
<div class="member-results">
@foreach ($members as $member)
    @include('member.partials.member-card', ['member' => $member])
@endforeach
</div>
@else
<div class="block block--style-3 list z-depth-1-top">
    <i>No members found!!!</i>
</div>
@endif
<div class="row">
    <div class="col-sm-12 col-md-6">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshProfiles(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=($currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshProfiles(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage+4<$numPages)
            <li class="paginate_button page-item disabled ur-ellipsis"><span class="page-link">&hellip;</span></li>
            @endif

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshProfiles(true, {{ $currentPage + 1 }});" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
            @endif
        </ul>
    </div>
</div>
@endif
@endsection
