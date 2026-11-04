{{-- "Edit Full Profile" from the member dashboard: the same wizard as the first-time page (member/complete-profile.blade.php),
     but inside the dashboard layout (sidebar + topbar) so it feels like part of the dashboard. --}}
@extends('layouts.dashboard')
@section('dashboard-title', 'Edit Full Profile')
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
@endpush
@section('main-content')
@include('member.partials.profile-wizard', ['embedded' => true])
@endsection
