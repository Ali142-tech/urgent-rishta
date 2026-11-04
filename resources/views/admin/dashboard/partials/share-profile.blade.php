{{--
    "Share on WhatsApp" for one member — sends that member their complete profile (as the client form) with their photos.
    Used in the desktop detail panel (variant "panel") and the mobile card (variant "card"); the click is handled by
    the js-admin-share script in profiles.blade.php.
--}}
@php($variant = $variant ?? 'panel')
@if(!empty(trim((string) $member->contact_mobile_number)) && ($member->admin ?? 0) != 1)
    @if($variant === 'card')
        <a class="js-admin-share" role="button" style="white-space:nowrap;" data-payload-url="{{ url('admin/profile/'.$member->dataid.'/share-payload') }}"><i class="fa fa-whatsapp"></i> <span>Share Form</span></a>
    @else
        <a class="ur-btn ur-btn--outline js-admin-share" role="button" data-payload-url="{{ url('admin/profile/'.$member->dataid.'/share-payload') }}"><i class="fa fa-whatsapp" style="color:#25D366;"></i> Share on WhatsApp</a>
    @endif
@endif
