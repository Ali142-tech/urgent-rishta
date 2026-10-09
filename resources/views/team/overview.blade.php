{{--
    Dashboard overview — stat cards, a short AI Match Recommendations
    preview, a Live Notifications panel, and the Quick Add Proposal form.
    This is what "Team Dashboard" (team/dashboard route) now shows — the
    old single-page proposal grid was split into My Proposals / Search
    Proposals (see team/proposals-mine.blade.php, team/proposals-search.blade.php).
    Keeps this site's real brand palette (green/gold, --ur-dash-*) rather
    than the client mockup's literal maroon — see ur-member-card.css:279-283
    for the prior precedent of that same call.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Dashboard')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-ov-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
    .ur-ov-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0; }
    .ur-ov-head p { color: #6B7570; font-size: 13.5px; margin: 4px 0 0; }

    .ur-ov-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
    @media (max-width: 991px) { .ur-ov-layout { grid-template-columns: 1fr; } }

    .ur-ov-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 26px; }
    @media (max-width: 900px) { .ur-ov-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .ur-ov-stats { grid-template-columns: 1fr; } }
    .ur-ov-stat { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px; }
    .ur-ov-stat__top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .ur-ov-stat__icon { width: 48px; height: 48px; border-radius: 10px; background: #F6F4EF; color: #C9974D; display: flex; align-items: center; justify-content: center; font-size: 16px; }
    .ur-ov-stat__badge { font-size: 10.5px; font-weight: 700; color: #2E7D5B; background: #EAF6EF; padding: 3px 9px; border-radius: 999px; }
    .ur-ov-stat__label { display: block; font-size: 12.5px; color: #6B7570; }
    .ur-ov-stat__value { display: block; font-size: 36px; font-weight: 900; color: #123A2E; font-family: 'Playfair Display', serif; }
    .ur-ov-stat__delta { display: inline-block; margin-top: 6px; font-size: 11.5px; font-weight: 700; color: #C9974D; text-decoration: none; }

    .ur-ov-section { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; margin-bottom: 24px; }
    .ur-ov-section__head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px; }
    .ur-ov-section__head h2 { font-size: 16px; color: #123A2E; font-weight: 700; margin: 0; }
    .ur-ov-section__head a { font-size: 12.5px; color: #C9974D; font-weight: 700; text-decoration: none; }

    .ur-ov-panel { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
    .ur-ov-panel h3 { font-size: 14px; color: #123A2E; font-weight: 700; margin: 0 0 12px; }
    .ur-notif-mini { display: flex; gap: 10px; padding: 8px 0; border-bottom: 1px solid #F0EEE7; font-size: 12.5px; }
    .ur-notif-mini:last-child { border-bottom: none; }
    .ur-notif-mini i { color: #C9974D; margin-top: 2px; }
    .ur-notif-mini__time { color: #9AA5A0; font-size: 11px; }

    .ur-quick-form .row { margin-bottom: 10px; }
    .ur-quick-form label { font-size: 11.5px; font-weight: 600; color: #1C2321; margin-bottom: 3px; display: block; }
    .ur-quick-form .form-control, .ur-quick-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 36px; padding: 0 10px; width: 100%; font-size: 12.5px; }
    .ur-quick-form button { width: 100%; height: 42px; border-radius: 999px; background: #C9974D; color: #fff; border: none; font-size: 13px; font-weight: 700; margin-top: 6px; }
    .ur-quick-form button:hover { background: #B07C3D; }

    .ur-trust-bar { display: flex; align-items: center; justify-content: center; gap: 24px; flex-wrap: wrap; margin-top: 30px; padding: 16px; background: #F6F4EF; border-radius: 12px; font-size: 12.5px; color: #123A2E; font-weight: 600; }
    .ur-trust-bar i { color: #C9974D; margin-right: 6px; }

    .ur-ov-hero { background: linear-gradient(135deg, #123A2E, #0F2E24); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 18px; }
    .ur-ov-hero h1 { font-family: 'Playfair Display', serif; color: #fff; font-weight: 700; font-size: 24px; margin: 0 0 6px; }
    .ur-ov-hero p { color: rgba(255,255,255,.7); font-size: 13.5px; margin: 0; max-width: 440px; }
    .ur-ov-hero__actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ur-ov-hero__btn { display: inline-flex; align-items: center; gap: 7px; height: 40px; padding: 0 18px; border-radius: 999px; font-size: 12.5px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .ur-ov-hero__btn--primary { background: #C9974D; color: #1C2321; }
    .ur-ov-hero__btn--primary:hover { background: #B07C3D; color: #1C2321; }
    .ur-ov-hero__btn--outline { background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.25); }
    .ur-ov-hero__btn--outline:hover { background: rgba(255,255,255,.18); color: #fff; }

    .ur-ov-tiles { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 26px; }
    @media (max-width: 991px) { .ur-ov-tiles { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 560px) { .ur-ov-tiles { grid-template-columns: repeat(2, 1fr); } }
    .ur-ov-tile { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 14px; text-decoration: none; display: block; }
    .ur-ov-tile:hover { border-color: #C9974D; text-decoration: none; }
    .ur-ov-tile i { color: #C9974D; font-size: 25px; margin-bottom: 8px; display: block; }
    .ur-ov-tile__label { display: block; font-size: 12.5px; font-weight: 700; color: #123A2E; }
    .ur-ov-tile__sub { display: block; font-size: 10.5px; color: #9AA5A0; margin-top: 2px; }

    .ur-ov-mtable { width: 100%; border-collapse: collapse; }
    .ur-ov-mtable th { text-align: left; font-size: 10.5px; text-transform: uppercase; letter-spacing: .4px; color: #9AA5A0; padding: 0 0 10px; font-weight: 700; }
    .ur-ov-mtable td { padding: 10px 0; border-top: 1px solid #F0EEE7; font-size: 13px; color: #1C2321; vertical-align: middle; }
    .ur-ov-mtable__score { font-weight: 700; color: #123A2E; }
    .ur-ov-mtable th, .ur-ov-mtable td { padding-left: 10px; padding-right: 10px; }
    .ur-ov-mtable td { padding-top: 10px; padding-bottom: 10px; }
    .ur-ov-mtable span, .ur-ov-mtable img { margin: 0; padding: 0; box-sizing: border-box; min-height: 0; }
    .ur-ov-layout > * { min-width: 0; }
    /* phones: each match row becomes a small card instead of a table that runs off the screen */
    @media (max-width: 560px) {
        .ur-ov-section { padding: 14px; }
        .ur-ov-mtable, .ur-ov-mtable tbody, .ur-ov-mtable tr, .ur-ov-mtable td { display: block !important; width: 100% !important; box-sizing: border-box; }
        .ur-ov-mtable thead { display: none !important; }
        .ur-ov-mtable tr { padding: 12px 0 !important; border-top: 1px solid #F0EEE7; }
        .ur-ov-mtable tr:first-child { border-top: 0; padding-top: 0 !important; }
        .ur-ov-mtable td { border: 0 !important; padding: 3px 0 !important; height: auto !important; }
        .ur-ov-mtable td:nth-child(2)::before { content: 'Match: '; color: #9AA5A0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
        .ur-ov-mtable td > div[style*="width:110px"] { width: 100% !important; max-width: 220px; }
        .ur-ov-mtable td:nth-child(4) { padding-top: 6px !important; }
    }
    .ur-ov-mtable .ur-ov-client { display: flex; align-items: center; gap: 12px; }
    .ur-ov-mtable .ur-ov-client__photo { display: block; width: 40px; height: 40px; max-width: none; border-radius: 10px; object-fit: cover; flex: 0 0 40px; background: #F6F4EF; }
    .ur-ov-mtable .ur-ov-client__text { display: block; }
    .ur-ov-mtable .ur-ov-client__id { display: block; font-size: 13.5px; font-weight: 700; color: #1C2321; line-height: 18px; }
    .ur-ov-mtable .ur-ov-client__sub { display: block; font-size: 11.5px; font-weight: 400; color: #6B7570; line-height: 16px; text-transform: capitalize; }
    .ur-ov-mtable .ur-ov-score { display: block; width: 110px; }
    .ur-ov-mtable .ur-ov-score__pct { display: block; font-size: 13.5px; font-weight: 700; color: #123A2E; line-height: 18px; margin-bottom: 4px; }
    .ur-ov-mtable .ur-ov-score__bar { display: block; width: 100%; height: 5px; border-radius: 999px; background: #EFEBE0; overflow: hidden; }
    .ur-ov-mtable .ur-ov-score__fill { display: block; height: 5px; border-radius: 999px; background: linear-gradient(90deg, #123A2E, #3E8E6B); }

    /* ---- softer, card-based lower dashboard: quick tiles, Recent AI Matches, Activity ---- */
    .ur-ov-tiles { grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 14px; }
    @media (max-width: 1280px) { .ur-ov-tiles { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 700px) { .ur-ov-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 420px) { .ur-ov-tiles { grid-template-columns: 1fr; } }
    .ur-ov-tile { display: flex; align-items: center; gap: 12px; padding: 14px 14px; border: 0; border-radius: 16px; box-shadow: 0 2px 10px rgba(15,46,36,.06); transition: transform .15s ease, box-shadow .15s ease; }
    .ur-ov-tile:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,46,36,.1); }
    .ur-ov-tile__icon { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin: 0 !important; }
    .ur-ov-tile i.ur-ov-tile__go { display: block; margin: 0 0 0 auto !important; font-size: 18px; color: #A9B3AE; flex-shrink: 0; }
    .ur-ov-tile__icon i { margin: 0 !important; font-size: 18px; }
    .ur-ov-tile__icon--green { background: #E3F3EA; } .ur-ov-tile__icon--green i { color: #1C8A5E; }
    .ur-ov-tile__icon--rose { background: #FCE4E8; } .ur-ov-tile__icon--rose i { color: #C23A55; }
    .ur-ov-tile__icon--indigo { background: #E9E8FB; } .ur-ov-tile__icon--indigo i { color: #4A47B5; }
    .ur-ov-tile__icon--amber { background: #FCEBD2; } .ur-ov-tile__icon--amber i { color: #C77A1E; }
    .ur-ov-tile__text { min-width: 0; }
    .ur-ov-tile__label { font-size: 13.5px; line-height: 1.25; }
    .ur-ov-tile__sub { font-size: 11.5px; line-height: 1.3; }

    .ur-ov-card { background: #fff; border-radius: 20px; box-shadow: 0 2px 14px rgba(15,46,36,.07); overflow: hidden; margin-bottom: 24px; }
    .ur-ov-card__head { display: flex; align-items: center; gap: 14px; padding: 18px 22px; }
    .ur-ov-card--matches .ur-ov-card__head { background: linear-gradient(180deg, #EAF6EF, #F4FAF6); }
    .ur-ov-card--activity .ur-ov-card__head { background: linear-gradient(180deg, #FDEEF0, #FEF6F7); }
    .ur-ov-card__icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 21px; flex-shrink: 0; color: #fff; }
    .ur-ov-card__icon--green { background: #1C7A58; }
    .ur-ov-card__icon--rose { background: #FBE0E5; color: #C23A55; }
    .ur-ov-card__title { flex: 1; min-width: 0; }
    .ur-ov-card__title h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #123A2E; margin: 0; line-height: 1.2; }
    .ur-ov-card__title p { margin: 3px 0 0; font-size: 12.5px; color: #4B5651; line-height: 1.4; }
    .ur-ov-card__all { flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; text-decoration: none !important; white-space: nowrap; }
    .ur-ov-card__all--green { background: #DDF0E6; color: #1C7A58 !important; }
    .ur-ov-card__all--rose { background: #FBE0E5; color: #C23A55 !important; }
    .ur-ov-card__empty { margin: 0; padding: 22px; font-size: 13px; color: #6B7570; }

    .ur-ov-mhead, .ur-ov-mrow { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.6fr) minmax(0, .8fr) 90px 44px; gap: 12px; align-items: center; }
    .ur-ov-mhead { padding: 14px 22px 8px 26px; font-size: 10.5px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; color: #8A938E; }
    .ur-ov-mrow { position: relative; margin: 0 16px 12px; padding: 14px 16px; background: #fff; border-radius: 14px; box-shadow: 0 1px 8px rgba(15,46,36,.07); text-decoration: none !important; color: inherit !important; transition: box-shadow .15s ease; }
    .ur-ov-mrow::before { content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; border-radius: 3px; background: #1C9A6C; }
    .ur-ov-mrow:hover { box-shadow: 0 6px 18px rgba(15,46,36,.13); }
    .ur-ov-mcell { min-width: 0; display: flex; align-items: center; gap: 12px; }
    .ur-ov-mcell img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background: #F3EFE6; }
    .ur-ov-mcell b { display: block; font-size: 14px; font-weight: 800; color: #123A2E; line-height: 1.25; }
    .ur-ov-mcell small { display: block; font-size: 12px; color: #6B7570; line-height: 1.4; }
    .ur-ov-mcell--score { flex-direction: column; align-items: flex-start; gap: 6px; }
    .ur-ov-mcell--score b { font-size: 16px; }
    .ur-ov-bar { display: block; width: 100%; max-width: 110px; height: 6px; border-radius: 999px; background: #E5EDE8; overflow: hidden; }
    .ur-ov-bar span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #123A2E, #2FA678); }
    .ur-ov-new { display: inline-block; background: #E3F3EA; color: #1C7A58; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 999px; }
    .ur-ov-mcell--go { justify-content: flex-end; }
    .ur-ov-mcell--go i { width: 34px; height: 34px; border-radius: 50%; background: #F3F1EA; color: #123A2E; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .ur-ov-mtags { display: block; margin-top: 4px; }
    .ur-ov-mtags .ur-badge-pill { display: inline-block; font-size: 10.5px; font-weight: 700; line-height: 1.2; padding: 3px 9px; border-radius: 999px; margin: 0 5px 3px 0; }
    .ur-ov-mtags .ur-badge-pill--premium { background: #FBF0DA; color: #8A6218; }
    .ur-ov-mtags .ur-badge-pill--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-ov-mtags .ur-badge-pill--abroad { background: #EAF2FB; color: #2C5F8A; }
    .ur-ov-mtags .ur-badge-pill--highlight { background: #FBE4E9; color: #A23B57; }
    .ur-ov-card--matches > .ur-ov-mrow:last-child { margin-bottom: 16px; }

    .ur-ov-act { display: flex; align-items: center; gap: 12px; padding: 14px 22px; border-bottom: 1px solid #F3EFE8; text-decoration: none !important; color: inherit !important; }
    .ur-ov-act:hover { background: #FAF8F4; }
    .ur-ov-act__dot { width: 10px; height: 10px; border-radius: 50%; background: #1C9A6C; flex-shrink: 0; }
    .ur-ov-act__dot.is-read { background: #C9D6CF; }
    .ur-ov-act__body { flex: 1; min-width: 0; }
    .ur-ov-act__body b { display: block; font-size: 13px; font-weight: 700; color: #1C2321; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ur-ov-act__body small { display: block; font-size: 12px; color: #8A938E; }
    .ur-ov-act__go { font-size: 12px; color: #6B7570; flex-shrink: 0; }
    .ur-ov-soon { display: flex; gap: 12px; align-items: flex-start; margin: 0; padding: 16px 22px 20px; background: #EAF6EF; }
    .ur-ov-soon__icon { width: 36px; height: 36px; border-radius: 50%; background: #fff; color: #C9974D; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-ov-soon b { display: block; font-size: 13px; color: #123A2E; }
    .ur-ov-soon p { margin: 2px 0 0; font-size: 12px; color: #4B5651; line-height: 1.45; }

    @media (max-width: 1100px) {
        .ur-ov-mhead { display: none; }
        .ur-ov-mrow { grid-template-columns: minmax(0, 1fr) auto; row-gap: 10px; }
        .ur-ov-mcell--match { grid-column: 1 / -1; order: 3; }
        .ur-ov-mcell--client { grid-column: 1; }
        .ur-ov-mcell--score { grid-column: 2; grid-row: 1; align-items: flex-end; }
        .ur-ov-mcell:nth-child(4), .ur-ov-mcell--go { display: none; }
    }
    @media (max-width: 560px) {
        .ur-ov-card__head { flex-wrap: wrap; padding: 16px; }
        .ur-ov-card__title h2 { font-size: 19px; }
        .ur-ov-card__all { margin-left: 64px; }
        .ur-ov-mrow { margin: 0 12px 10px; padding: 12px; }
    }
</style>

<div class="ur-ov-hero mt-5">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->first_name }}.</h1>
        <p>Your team has {{ number_format($teamProposalsCount) }} proposals ready for AI matching and direct owner WhatsApp sharing.</p>
    </div>
    <div class="ur-ov-hero__actions">
        <a href="{{ route('team.proposals.create') }}" class="ur-ov-hero__btn ur-ov-hero__btn--primary"><i class="fa fa-clipboard"></i> Paste a Profile</a>
        <a href="{{ route('team.proposals.search') }}" class="ur-ov-hero__btn ur-ov-hero__btn--outline"><i class="fa fa-search"></i> Advanced Search</a>
        <a href="{{ route('team.matches') }}" class="ur-ov-hero__btn ur-ov-hero__btn--outline"><i class="fa fa-magic"></i> My Matches</a>
    </div>
</div>

<div class="ur-ov-stats">
    <div class="ur-ov-stat">
        <div class="ur-ov-stat__top">
            <div class="ur-ov-stat__icon"><i class="fa fa-users"></i></div>
        </div>
        <span class="ur-ov-stat__label">Team Proposals</span>
        <span class="ur-ov-stat__value fw-bold">{{ number_format($teamProposalsCount) }}</span>
        <a href="{{ route('team.proposals.search') }}" class="ur-ov-stat__delta">View all &rarr;</a>
    </div>
    <div class="ur-ov-stat">
        <div class="ur-ov-stat__top">
            <div class="ur-ov-stat__icon"><i class="fa fa-magic"></i></div>
            <span class="ur-ov-stat__badge" title="Computed live on every page load, not a cached/batched number">Live</span>
        </div>
        <span class="ur-ov-stat__label">AI Matches (70%+)</span>
        <span class="ur-ov-stat__value">{{ number_format($aiMatchesCount) }}</span>
        <a href="{{ route('team.matches') }}" class="ur-ov-stat__delta">View all &rarr;</a>
    </div>
    <div class="ur-ov-stat">
        <div class="ur-ov-stat__top">
            <div class="ur-ov-stat__icon"><i class="fa fa-bell"></i></div>
        </div>
        <span class="ur-ov-stat__label">Pending Requests</span>
        <span class="ur-ov-stat__value">{{ number_format($pendingRequestsCount) }}</span>
        <a href="{{ route('team.notifications') }}" class="ur-ov-stat__delta">View all &rarr;</a>
    </div>
    <div class="ur-ov-stat">
        <div class="ur-ov-stat__top">
            <div class="ur-ov-stat__icon"><i class="fa fa-trophy"></i></div>
            @if($successfulMatchesThisMonth > 0)
                <span class="ur-ov-stat__badge">+{{ $successfulMatchesThisMonth }} this month</span>
            @endif
        </div>
        <span class="ur-ov-stat__label">Successful Introductions</span>
        <span class="ur-ov-stat__value">{{ number_format($successfulMatchesCount) }}</span>
        <a href="{{ route('team.successful-matches') }}" class="ur-ov-stat__delta">View all &rarr;</a>
    </div>
</div>

<div class="ur-ov-tiles">
    <a href="{{ route('team.proposals.create') }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--green"><i class="fa fa-clipboard"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">Paste Profile</span><span class="ur-ov-tile__sub">Import client text</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
    <a href="{{ route('team.proposals.search') }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--rose"><i class="fa fa-search"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">Advanced Search</span><span class="ur-ov-tile__sub">Search partner profiles</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
    <a href="{{ route('team.matches') }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--indigo"><i class="fa fa-magic"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">AI Match</span><span class="ur-ov-tile__sub">Find compatible clients</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
    <a href="{{ route('team.proposals.search', ['view' => 'all']) }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--amber"><i class="fa fa-users"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">Team Proposals</span><span class="ur-ov-tile__sub">{{ number_format($teamProposalsCount) }} profiles</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
    <a href="{{ route('team.proposals.mine') }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--green"><i class="fa fa-file-text-o"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">My Clients</span><span class="ur-ov-tile__sub">Manage your proposals</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
    <a href="{{ route('team.notifications') }}" class="ur-ov-tile"><span class="ur-ov-tile__icon ur-ov-tile__icon--rose"><i class="fa fa-bell-o"></i></span><span class="ur-ov-tile__text"><span class="ur-ov-tile__label">Requests</span><span class="ur-ov-tile__sub">Review activity</span></span><i class="fa fa-angle-right ur-ov-tile__go"></i></a>
</div>

<div class="ur-ov-layout">
    <div class="ur-ov-card ur-ov-card--matches">
        <div class="ur-ov-card__head">
            <span class="ur-ov-card__icon ur-ov-card__icon--green"><i class="fa fa-heart-o"></i></span>
            <div class="ur-ov-card__title">
                <h2>Recent AI Matches</h2>
                <p>Your latest AI-powered matches based on your preferences and compatibility.</p>
            </div>
            <a href="{{ route('team.my-matches') }}" class="ur-ov-card__all ur-ov-card__all--green">View All <i class="fa fa-long-arrow-right"></i></a>
        </div>

        @if($previewMatches->isEmpty())
            <p class="ur-ov-card__empty">No AI matches yet — add Partner Requirements/Preferred fields on a proposal to start finding matches for it.</p>
        @else
            <div class="ur-ov-mhead"><span>Your Client</span><span>Potential Match</span><span>Score</span><span>Status</span><span></span></div>
            @foreach($previewMatches as $match)
                @php
                    $client = $match->for_proposal_profile;
                    $compat = $match->compatibilityWith($client);
                    $scorePct = $compat ? (int) $compat['percent'] : null;
                    $matchPlace = $match->lbl_city ?: ($match->city ?? '');
                    $matchAge = !empty($match->birthday) ? date_diff(date_create($match->birthday), date_create('now'))->y : null;
                @endphp
                <a class="ur-ov-mrow" href="{{ route('team.matches.show', $match->for_proposal_dataid) }}#match-{{ $match->dataid }}">
                    <span class="ur-ov-mcell ur-ov-mcell--client">
                        <img src="{{ $client->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($client->gender ?? '') }}';">
                        <span>
                            <b>{{ $match->for_proposal_dataid }}</b>
                            <small>{{ collect([ucfirst($client->gender ?? ''), $client->age ?? null, $client->lbl_city ?? null])->filter()->implode(' • ') }}</small>
                        </span>
                    </span>
                    <span class="ur-ov-mcell ur-ov-mcell--match">
                        <img src="{{ $match->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($match->gender ?? '') }}';">
                        <span>
                            <b>{{ $match->dataid }}</b>
                            <small>{{ collect([ucfirst($match->gender ?? ''), $matchAge, $matchPlace])->filter()->implode(' • ') }}</small>
                            <span class="ur-ov-mtags">@include('team.partials.classification-badges', ['profile' => $match])</span>
                        </span>
                    </span>
                    <span class="ur-ov-mcell ur-ov-mcell--score">
                        @if($scorePct !== null)
                            <b>{{ $scorePct }}%</b>
                            <span class="ur-ov-bar"><span style="width: {{ min(100, $scorePct) }}%"></span></span>
                        @else —
                        @endif
                    </span>
                    <span class="ur-ov-mcell"><span class="ur-ov-new">New</span></span>
                    <span class="ur-ov-mcell ur-ov-mcell--go"><i class="fa fa-arrow-right"></i></span>
                </a>
            @endforeach
        @endif
    </div>

    <div class="ur-ov-card ur-ov-card--activity">
        <div class="ur-ov-card__head">
            <span class="ur-ov-card__icon ur-ov-card__icon--rose"><i class="fa fa-bell-o"></i></span>
            <div class="ur-ov-card__title"><h2>Activity</h2></div>
            <a href="{{ route('team.notifications') }}" class="ur-ov-card__all ur-ov-card__all--rose">View All <i class="fa fa-long-arrow-right"></i></a>
        </div>

        @forelse($recentNotifications as $notification)
            @php $u = $notificationUi[$notification->id] ?? null; @endphp
            <a class="ur-ov-act" href="{{ $u['url'] ?? route('team.notifications') }}">
                <span class="ur-ov-act__dot {{ $notification->read_at ? 'is-read' : '' }}"></span>
                <span class="ur-ov-act__body">
                    <b>{{ $u['title'] ?? ($notification->data['status'] ?? class_basename($notification->type)) }}</b>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </span>
                <i class="fa fa-arrow-right ur-ov-act__go"></i>
            </a>
        @empty
            <p class="ur-ov-card__empty">No notifications yet.</p>
        @endforelse

        <div class="ur-ov-soon">
            <span class="ur-ov-soon__icon"><i class="fa fa-magic"></i></span>
            <div>
                <b>More matches coming!</b>
                <p>Our AI is continuously finding better matches for you based on your preferences.</p>
            </div>
        </div>
    </div>
</div>
<div class="ur-trust-bar">
    <span><i class="fa fa-lock"></i> Private</span>
    <span><i class="fa fa-check-circle"></i> Verified</span>
    <span><i class="fa fa-briefcase"></i> Professional</span>
</div>
@endsection
