{{--
    Shared "Premium/Royal/Abroad/Highly Attractive" pills — reads
    profile_category/looking_from/presentation_highlight (Sep 2026
    migration; still has no Add/Edit Proposal form UI, so these stay blank
    until that form exists). Expects $profile (a Profile-hydrated row) and
    optional $class (base CSS class each pill gets, default "ur-badge-pill").
--}}
@php
    $__class = $class ?? 'ur-badge-pill';
    $__category = strtolower($profile->profile_category ?? '');
    $__highlight = strtolower($profile->presentation_highlight ?? '');
    $__abroad = str_contains(strtolower($profile->looking_from ?? ''), 'abroad');
@endphp
@if($__category === 'premium')<span class="{{ $__class }} {{ $__class }}--premium">Premium Profile</span>@endif
@if($__category === 'royal')<span class="{{ $__class }} {{ $__class }}--royal">Royal Profile</span>@endif
@if($__abroad)<span class="{{ $__class }} {{ $__class }}--abroad">Abroad Profile</span>@endif
@if($__highlight === 'highly attractive')<span class="{{ $__class }} {{ $__class }}--highlight">Highly Attractive</span>@endif
