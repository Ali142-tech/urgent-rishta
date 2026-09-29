<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $member->dataid }} &bull; Urgent Rishta</title>
    <meta name="description" content="{{ ucfirst($member->gender) }}{{ $age ? ', '.$age : '' }}{{ $member->profession ? ', '.$member->profession : '' }}{{ $location ? ' &bull; '.$location : '' }} &mdash; shared via Urgent Rishta.">

    {{-- Open Graph / WhatsApp link preview — this real photo is
         deliberately public here (client's explicit choice, Sep 2026),
         unlike member/profile/{dataid} which stays behind login for
         everything else. See HomeController::sharePreview(). --}}
    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $member->dataid }} &bull; Urgent Rishta">
    <meta property="og:description" content="{{ ucfirst($member->gender) }}{{ $age ? ', '.$age : '' }}{{ $member->profession ? ', '.$member->profession : '' }}{{ $location ? ' &bull; '.$location : '' }}">
    <meta property="og:image" content="{{ $photoUrl }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Manrope', system-ui, sans-serif; background: #F6F4EF; color: #1C2321; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px 16px; }
        .card { background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; max-width: 380px; width: 100%; overflow: hidden; box-shadow: 0 16px 40px rgba(15,46,36,.12); }
        .card__photo { width: 100%; aspect-ratio: 4/5; object-fit: cover; display: block; background: #E7E2D6; }
        .card__body { padding: 22px; text-align: center; }
        .card__id { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 20px; color: #123A2E; margin: 0 0 6px; }
        .card__meta { font-size: 13.5px; color: #6B7570; margin: 0 0 20px; }
        .card__btn { display: inline-block; width: 100%; background: #123A2E; color: #fff; text-decoration: none; font-weight: 700; font-size: 13.5px; padding: 13px 0; border-radius: 999px; }
        .card__btn:hover { background: #0F2E24; }
        .card__brand { margin-top: 16px; font-size: 11.5px; color: #9AA5A0; }
    </style>
</head>
<body>
    <div class="card">
        <img class="card__photo" src="{{ $photoUrl }}" alt="{{ $member->dataid }}">
        <div class="card__body">
            <p class="card__id">Profile {{ $member->dataid }}</p>
            <p class="card__meta">
                {{ ucfirst($member->gender) }}
                @if($age) &bull; {{ $age }} @endif
                @if(!empty($member->profession)) &bull; {{ $member->profession }} @endif
                @if($location) &bull; {{ $location }} @endif
            </p>
            @auth
                <a class="card__btn" href="{{ url('member/profile/'.$member->dataid) }}">View Full Profile</a>
            @else
                <a class="card__btn" href="{{ route('login') }}">Log In to View Full Profile</a>
            @endauth
            <p class="card__brand">Urgent Rishta &bull; AI Partner Network</p>
        </div>
    </div>
</body>
</html>
