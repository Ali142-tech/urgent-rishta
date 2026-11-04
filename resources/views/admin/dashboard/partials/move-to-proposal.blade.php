{{--
    "Move to Team Proposal" for one member — used in the desktop detail panel (variant "panel") and the mobile card
    (variant "card"). Once the member has been saved as a team proposal the button is shown disabled, with its reference;
    a copy that was started but not saved yet ("draft") can still be continued.
--}}
@php
    $variant = $variant ?? 'panel';
    $memberUserId = \App\User::where('dataid', $member->dataid)->value('id');
    $copy = $memberUserId ? \App\Proposal::withoutGlobalScopes()->where('source_user_id', $memberUserId)->first() : null;
    $moved = $copy && !$copy->is_draft;
    $formId = 'move_form_' . $member->dataid . '_' . $variant;
    $confirmJs = "var f = document.getElementById('" . $formId . "'); swalConfirm('Move to Team Proposal?', 'A copy of this member (all details and photos) will be added to the team proposals. The member profile stays as it is.', function () { f.querySelectorAll('.js-move-btn').forEach(function (b) { b.dataset.html = b.innerHTML; b.classList.add('js-move-copying'); b.innerHTML = '<i class=\'fa fa-refresh fa-spin\'></i> Copying...'; b.style.pointerEvents = 'none'; }); f.submit(); }); return false;";
@endphp
@if(empty($member->added_by) && ($member->admin ?? 0) != 1)
    @if($moved)
        @if($variant === 'card')
            <a class="js-move-btn" style="white-space:nowrap; opacity:.55; cursor:not-allowed; pointer-events:none;" aria-disabled="true"><i class="fa fa-check"></i> <span>In Proposals ({{ $copy->reference }})</span></a>
        @else
            <button type="button" class="ur-btn ur-btn--outline" disabled style="opacity:.55; cursor:not-allowed;" title="Already moved to the team proposals"><i class="fa fa-check"></i> In Team Proposals ({{ $copy->reference }})</button>
        @endif
    @else
        <form id="{{ $formId }}" method="POST" action="{{ url('admin/profile/'.$member->dataid.'/move-to-team-proposal') }}" style="display:{{ $variant === 'card' ? 'contents' : 'inline' }};">
            @csrf
            @if($variant === 'card')
                <a class="js-move-btn" role="button" style="white-space:nowrap;" onclick="{{ $confirmJs }}"><i class="fa fa-share-square-o"></i> <span>{{ $copy ? 'Continue Proposal' : 'Move to Proposal' }}</span></a>
            @else
                <button type="button" class="ur-btn ur-btn--outline js-move-btn" onclick="{{ $confirmJs }}"><i class="fa fa-share-square-o"></i> {{ $copy ? 'Continue Team Proposal' : 'Move to Team Proposal' }}</button>
            @endif
        </form>
    @endif
@endif
