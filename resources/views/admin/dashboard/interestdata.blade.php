@extends('admin.dashboard.interests')
@section('interest-data')
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mb-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshInterests(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshInterests(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshInterests(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
<div class="ur-admin-summary">@if(!empty($pageSize)) Showing
    <span>{{ isset($resultCount) && $resultCount==0 ? 0 : ($currentPage-1)*$pageSize+1 }}</span>
    to <span>{{ isset($resultCount) ?
        ($resultCount-(($currentPage-1)*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $resultCount)
        :
        ($total-($currentPage*$pageSize)>$pageSize ? ($currentPage*$pageSize) : $total) }}</span>
    @if (isset($resultCount)) from <span>{{ $resultCount }}</span> filtered interests @endif
    out of <span>{{ $total }}</span> total interests @endif</div>
@if(!empty($interests) && sizeof($interests)>0)
@foreach ($interests as $interest)
<div class="ur-admin-card">
    <div class="ur-admin-duo-card__row">
        <div class="ur-admin-duo-card__side" id="block_{{$interest->sid}}">
            <a href="{{url('/member/profile/'.$interest->sid)}}" target="_blank">
                <span class="ur-admin-thumb" style="background-image: url('{{ explode(',', $interest->sender_images)[0] }}')"></span>
            </a>
            <div>
                <a href="{{url('/member/profile/'.$interest->sid)}}" target="_blank" class="ur-admin-duo-card__name">{{ $interest->sender }}</a>
                <a class="ur-admin-duo-card__id" href="{{url('/member/profile/'.$interest->sid)}}" target="_blank">{{$interest->sid}}</a>
                <a class="ur-admin-duo-card__email" href="mailto:{{$interest->sender_email}}">{{ $interest->sender_email }}</a>
                @if(!empty($interest->sender_mobile))
                <div class="ur-admin-duo-card__mobile">{{ $interest->sender_mobile }} &middot; <a href="{{ \App\User::whatsappLinkForNumber($interest->sender_mobile) }}" target="_blank">Send WhatsApp</a></div>
                @endif
            </div>
        </div>
        <div class="ur-admin-duo-card__status">
            @php
                $interest_back = $interest->interest_back;
                if ($interest_back==1) {
                    $label = "ACCEPTED";
                    $badgeClass = 'ur-admin-badge--success';
                    $statusMessage = "Interest accepted by " . $interest->receiver;
                } else if ($interest_back==-1) {
                    $label = "DECLINED";
                    $badgeClass = 'ur-admin-badge--danger';
                    $statusMessage = "Not accepted from " . $interest->receiver . "'s side";
                } else {
                    $label = "PENDING";
                    $badgeClass = 'ur-admin-badge--warning';
                    $statusMessage = "Not accepted from " . $interest->receiver . "'s side";
                }
            @endphp
            <span class="ur-admin-badge {{$badgeClass}}">{{$label}}</span>
            <div class="ur-admin-duo-card__statusmsg">{{ $statusMessage }}</div>
        </div>
        <div class="ur-admin-duo-card__side" id="block_{{$interest->rid}}">
            <a href="{{url('/member/profile/'.$interest->rid)}}" target="_blank">
                <span class="ur-admin-thumb" style="background-image: url('{{ explode(',', $interest->receiver_images)[0] }}')"></span>
            </a>
            <div>
                <a href="{{url('/member/profile/'.$interest->rid)}}" target="_blank" class="ur-admin-duo-card__name">{{ $interest->receiver }}</a>
                <a class="ur-admin-duo-card__id" href="{{url('/member/profile/'.$interest->rid)}}" target="_blank">{{$interest->rid}}</a>
                <a class="ur-admin-duo-card__email" href="mailto:{{$interest->receiver_email}}">{{ $interest->receiver_email }}</a>
                @if(!empty($interest->receiver_mobile))
                <div class="ur-admin-duo-card__mobile">{{ $interest->receiver_mobile }} &middot; <a href="{{ \App\User::whatsappLinkForNumber($interest->receiver_mobile) }}" target="_blank">Send WhatsApp</a></div>
                @endif
            </div>
        </div>
    </div>
    <div class="ur-admin-duo-card__foot">
        <div><i>Sent On:</i> {{ date('d/m/Y', strtotime($interest->created_at)) }}</div>
        <div><i>Updated On:</i> {{ date('d/m/Y', strtotime($interest->updated_at)) }}</div>
    </div>
</div>
@endforeach
@else
<div class="ur-admin-empty"><i class="fa fa-heart"></i> No interests found.</div>
@endif
@if($currentPage > 1 || $currentPage < $numPages)
<ul class="ur-admin-pagination mt-3">
    @if($currentPage!=1)
    <li><a onclick="javascript:refreshInterests(true, {{ $currentPage - 1 }});">Previous</a></li>
    @endif

    @for ($i=($currentPage-3>1 ? $currentPage-3 : 1); $i<=( $currentPage+4<=$numPages ? $currentPage+4 : $numPages); $i++)
    <li class="{{ $i == $currentPage ? 'active':'' }}"><a {{ $currentPage==$i?"disabled":"" }} onclick="javascript:refreshInterests(true, {{ $i }});">{{ $i }}</a></li>
    @endfor

    @if($currentPage<$numPages)
    <li><a onclick="javascript:refreshInterests(true, {{ $currentPage + 1 }});">Next</a></li>
    @endif
</ul>
@endif
@endsection
