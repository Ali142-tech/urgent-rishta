@extends('admin.dashboard.photoaccess')
@section('photoaccess-data')

@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mb-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshPhotoAccessRequests(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
<div class="ur-admin-summary">@if(!empty($pageSize)) Showing
    <span>{{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}</span>
    to <span>{{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}</span>
    @if (isset($resultCount)) from <span>{{ $resultCount }}</span> filtered requests @endif
    out of <span>{{ $total }}</span> total requests @endif</div>
@if(!empty($requests) && sizeof($requests)>0)
@foreach ($requests as $request)
<div class="ur-admin-card">
    <div class="ur-admin-duo-card__row">
        <div class="ur-admin-duo-card__side" id="block_{{$request->uid}}">
            <a href="{{url('/member/profile/'.$request->uid)}}" target="_blank">
                <span class="ur-admin-thumb" style="background-image: url('{{ explode(',', $request->user_images)[0] }}')"></span>
            </a>
            <div>
                <a href="{{url('/member/profile/'.$request->uid)}}" target="_blank" class="ur-admin-duo-card__name">{{ $request->user }}</a>
                <a class="ur-admin-duo-card__id" href="{{url('/member/profile/'.$request->uid)}}" target="_blank">{{$request->uid}}</a>
                <a class="ur-admin-duo-card__email" href="mailto:{{$request->user_email}}">{{ $request->user_email }}</a>
            </div>
        </div>
        <div class="ur-admin-duo-card__status">
            @php
                $allowed = $request->allowed;
                $class = null;
                $label = null;
                if ($allowed==1) {
                    $class = "ur-admin-badge--success";
                    $label = "GRANTED";
                } else if ($allowed==-1) {
                    $class = "ur-admin-badge--danger";
                    $label = "DECLINED";
                } else {
                    $class = "ur-admin-badge--warning";
                    $label = "REQUESTED";
                }
            @endphp
            <span class="ur-admin-badge {{$class}}">{{$label}}</span>
        </div>
        <div class="ur-admin-duo-card__side" id="block_{{$request->pid}}">
            <a href="{{url('/member/profile/'.$request->pid)}}" target="_blank">
                <span class="ur-admin-thumb" style="background-image: url('{{ explode(',', $request->profile_images)[0] }}')"></span>
            </a>
            <div>
                <a href="{{url('/member/profile/'.$request->pid)}}" target="_blank" class="ur-admin-duo-card__name">{{ $request->profile }}</a>
                <a class="ur-admin-duo-card__id" href="{{url('/member/profile/'.$request->pid)}}" target="_blank">{{$request->pid}}</a>
                <a class="ur-admin-duo-card__email" href="mailto:{{$request->profile_email}}">{{ $request->profile_email }}</a>
            </div>
        </div>
    </div>
    <div class="ur-admin-duo-card__foot">
        <div><i>Sent On:</i> {{ date('d/m/Y', strtotime($request->created_at)) }}</div>
        <div><i>Updated On:</i> {{ date('d/m/Y', strtotime($request->updated_at)) }}</div>
    </div>
</div>
@endforeach
@else
<div class="ur-admin-empty"><i class="fa fa-image"></i> No requests found.</div>
@endif
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mt-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshPhotoAccessRequests(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
@endsection
