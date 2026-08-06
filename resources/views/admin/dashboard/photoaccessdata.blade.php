@extends('admin.dashboard.photoaccess')
@section('photoaccess-data')
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshPhotoAccessRequests(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
            @endif
        </ul>
    </div>
</div>
<div class="block-footer b-xs-top" style="margin: 20px 0;">@if(!empty($pageSize)) Showing
    {{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}
    to {{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}
    <span>@if (isset($resultCount)) from {{ $resultCount }} filtered requests @endif</span>
    out of {{ $total }} total requests @endif</div>
@if(!empty($requests) && sizeof($requests)>0)
@foreach ($requests as $request)
<div class="block block--style-3 list z-depth-1-top">
    <div class="d-inline-block w100">
        <div class="float-left" style="height: 220px; width: 40%" id="block_{{$request->uid}}">
            <div class="block-image">
                <a href="{{url('/member/profile/'.$request->uid)}}" target="_blank">
                    <div class="listing-image" style="background-image: url('{{ explode(',', $request->user_images)[0] }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper" style="padding-top:50px">
                <div class="heading heading-5 strong-500 mt-4 d-inline-block w100">
                    <div class="float-left" style="display: inline-block">
                        <div><a href="{{url('/member/profile/'.$request->uid)}}" target="_blank" class="c-base-1">{{ $request->user }}</a></div>
                        <div style="font-size: 12px;"><a class="c-gray" href="{{url('/member/profile/'.$request->uid)}}" target="_blank"> {{$request->uid}} </a></div>
                        <div style="font-size: 12px;"><a href="mailto:{{$request->user_email}}">{{ $request->user_email }}</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="center float-left" style="height: 220px; width: 20%; padding-top: 95px">
            @php
                $allowed = $request->allowed;
                $class = null;
                $label = null;
                if ($allowed==1) {
                    $class = "btn-green";
                    $label = "GRANTED";
                } else if ($allowed==-1) {
                    $class = "btn-red";
                    $label = "DECLINED";
                } else {
                    $class = "btn-base-1";
                    $label = "REQUESTED";
                }
            @endphp
            <div class="center btn btn-styled btn-shadow padding-lr20 {{$class}}">{{$label}}</div>
        </div>
        <div class="float-left" style="height: 220px; width: 40%" id="block_{{$request->pid}}">
            <div class="block-image center">
                <a href="{{url('/member/profile/'.$request->pid)}}" target="_blank">
                    <div class="listing-image" style="background-image: url('{{ explode(',', $request->profile_images)[0] }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper" style="padding-top:50px">
                <div class="heading heading-5 strong-500 mt-4 d-inline-block w100">
                    <div class="float-left" style="display: inline-block">
                        <div><a href="{{url('/member/profile/'.$request->pid)}}" target="_blank" class="c-base-1">{{ $request->profile }}</a> </div>
                        <div style="font-size: 12px;"><a class="c-gray" href="{{url('/member/profile/'.$request->pid)}}" target="_blank"> {{$request->pid}} </a></div>
                        <div style="font-size: 12px;"><a href="mailto:{{$request->profile_email}}">{{ $request->profile_email }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="center c-gray" style="font-size: 12px; border-top: 1px solid silver; padding: 10px 30px 30px 30px">
        <div class="center float-left" style="width: 40%"><i>Sent On:</i> {{ date('d/m/Y', strtotime($request->created_at)) }}</div>
        <div class="float-left" style="width: 20%">&nbsp;</div>
        <div class="center float-left" style="width: 40%"><i>Updated On:</i> {{ date('d/m/Y', strtotime($request->updated_at)) }}</div>
    </div>
</div>
@endforeach
@else
<div class="block block--style-3 list z-depth-1-top">
    <i>No requests found!!!</i>
</div>
@endif
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshPhotoAccessRequests(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshPhotoAccessRequests(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
            @endif
        </ul>
    </div>
</div>
@endsection
