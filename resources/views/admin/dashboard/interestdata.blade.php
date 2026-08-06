@extends('admin.dashboard.interests')
@section('interest-data')
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshInterests(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshInterests(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshInterests(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
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
    <span>@if (isset($resultCount)) from {{ $resultCount }} filtered interests @endif</span>
    out of {{ $total }} total interests @endif</div>
@if(!empty($interests) && sizeof($interests)>0)
@foreach ($interests as $interest)
<div class="block block--style-3 list z-depth-1-top">
    <div class="d-inline-block w100">
        <div class="float-left" style="height: 220px; width: 40%" id="block_{{$interest->sid}}">
            <div class="block-image">
                <a href="{{url('/member/profile/'.$interest->sid)}}" target="_blank">
                    <div class="listing-image" style="background-image: url('{{ explode(',', $interest->sender_images)[0] }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper" style="padding-top:50px">
                <div class="heading heading-5 strong-500 mt-4 d-inline-block w100">
                    <div class="float-left" style="display: inline-block">
                        <div><a href="{{url('/member/profile/'.$interest->sid)}}" target="_blank" class="c-base-1">{{ $interest->sender }}</a></div>
                        <div style="font-size: 12px;"><a class="c-gray" href="{{url('/member/profile/'.$interest->sid)}}" target="_blank"> {{$interest->sid}} </a></div>
                        <div style="font-size: 12px;"><a href="mailto:{{$interest->sender_email}}">{{ $interest->sender_email }}</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="center float-left" style="height: 220px; width: 20%; padding-top: 95px">
            @php
                $interest_back = $interest->interest_back;
                $class = null;
                $label = null;
                if ($interest_back==1) {
                    $class = "btn-green";
                    $label = "ACCEPTED";
                } else if ($interest_back==-1) {
                    $class = "btn-red";
                    $label = "DECLINED";
                } else {
                    $class = "btn-base-1";
                    $label = "PENDING";
                }
            @endphp
            <div class="center btn btn-styled btn-shadow padding-lr20 {{$class}}">{{$label}}</div>
        </div>
        <div class="float-left" style="height: 220px; width: 40%" id="block_{{$interest->rid}}">
            <div class="block-image center">
                <a href="{{url('/member/profile/'.$interest->rid)}}" target="_blank">
                    <div class="listing-image" style="background-image: url('{{ explode(',', $interest->receiver_images)[0] }}')"></div>
                </a>
            </div>
            <div class="block-title-wrapper" style="padding-top:50px">
                <div class="heading heading-5 strong-500 mt-4 d-inline-block w100">
                    <div class="float-left" style="display: inline-block">
                        <div><a href="{{url('/member/profile/'.$interest->rid)}}" target="_blank" class="c-base-1">{{ $interest->receiver }}</a> </div>
                        <div style="font-size: 12px;"><a class="c-gray" href="{{url('/member/profile/'.$interest->rid)}}" target="_blank"> {{$interest->rid}} </a></div>
                        <div style="font-size: 12px;"><a href="mailto:{{$interest->receiver_email}}">{{ $interest->receiver_email }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="center c-gray" style="font-size: 12px; border-top: 1px solid silver; padding: 10px 30px 30px 30px">
        <div class="center float-left" style="width: 40%"><i>Sent On:</i> {{ date('d/m/Y', strtotime($interest->created_at)) }}</div>
        <div class="float-left" style="width: 20%">&nbsp;</div>
        <div class="center float-left" style="width: 40%"><i>Updated On:</i> {{ date('d/m/Y', strtotime($interest->updated_at)) }}</div>
    </div>
</div>
@endforeach
<!-- <div class="card card-primary card-outline">
    <div class="card-body">
        <div class="card-text">
            <table id="datatable" class="table-striped table-bordered" cellpadding="20">
                <thead>
                    <tr>
                        <th>Send By</th>
                        <th>Send To</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @ foreach ($interests as $interest)
                    <tr>
                        <td>
                            <div class="float-left padding-lr20" style="width: 110px; height: 70px; text-align: center; vertical-align: middle">
                                <img src="{{ explode(',', $interest->sender_images)[0] }}" style="padding: 20px 0 30px 0; vertical-align: middle; max-height:110px; max-width: 70px" />
                            </div>
                            <div class="block--style-2.grid">
                                <div class="heading heading-5 strong-500 mt-4 c-base-1">{{$interest->sender}}</div>
                                <div class="heading heading-xs c-gray-light strong-400">{{$interest->sender_email}}</div>
                            </div>
                        </td>
                        <td>
                            <div class="float-left padding-lr20" style="width: 110px; height: 70px; text-align: center; vertical-align: middle">
                                <img src="{{ explode(',', $interest->receiver_images)[0] }}" style="padding: 20px 0 30px 0; vertical-align: middle; max-height:110px; max-width: 70px" />
                            </div>
                            <div>
                                <div class="heading heading-5 strong-500 mt-4 c-base-1">{{$interest->receiver}}</div>
                                <div class="heading heading-xs c-gray-light strong-400">{{$interest->receiver_email}}</div>
                            </div>
                        </td>
                        <td>
                            <div class="center">
                                <span class="btn btn-styled btn-xs btn-shadow padding-lr20 {{($interest->interest_back==1)?'btn-base-1':'btn-orange'}}">{{($interest->interest_back==1)?'Accepted':'Pending'}}</span>
                            </div>
                        </td>
                    </tr>
                    @ endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>/.card -->
@else
<div class="block block--style-3 list z-depth-1-top">
    <i>No interests found!!!</i>
</div>
@endif
<div class="row">
    <div class="col-md-6 col-sm-12">
        <ul class="pagination">
            @if($currentPage!=1)
            <li class="paginate_button page-item previous"><a onclick="javascript:refreshInterests(true, {{ $currentPage - 1 }});" class="page-link">Previous</a></li>
            @endif

            @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
            <li class="paginate_button page-item {{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshInterests(true, {{ $i }});" class="page-link">{{ $i }}</a></li>
            @endfor

            @if($currentPage<$numPages)
            <li class="paginate_button page-item next"><a onclick="javascript:refreshInterests(true, {{ $currentPage + 1 }});" class="page-link">Next</a></li>
            @endif
        </ul>
    </div>
</div>
@endsection
