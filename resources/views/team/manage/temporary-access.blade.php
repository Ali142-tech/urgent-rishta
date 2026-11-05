{{--
    Admin ▸ Temporary Access — short-visit accounts for the Team Dashboard. The admin enters a name + email and picks 15 or 30
    minutes; the page shows the sign-in details ONCE (only the password's hash is kept) with a ready-to-send message. The person's
    clock starts at their first sign-in; when it ends the account can never sign in again. They can only browse the shared
    proposals: no photos, no contact details, no sharing, no editing (see EnsureUserIsTeamMember + the proposal views).
    The list is filterable (status tabs + search) and paginated (TeamAdminController::temporaryAccess()).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Temporary Access')
@section('main-content')
<style>
    .ur-ta { width: 100%; }   /* fills the whole content area, like the other team pages */

    /* ---- hero + stats ---- */
    .ur-ta-hero { position: relative; overflow: hidden; border-radius: 22px; padding: 28px 30px 26px; margin-bottom: 18px; background: linear-gradient(135deg, #123A2E 0%, #0F2E24 60%, #174536 100%); color: #F6F4EF; box-shadow: 0 12px 30px rgba(15, 46, 36, .18); }
    .ur-ta-hero::after { content: ''; position: absolute; right: -60px; top: -60px; width: 220px; height: 220px; border-radius: 50%; border: 2px solid rgba(201, 151, 77, .35); box-shadow: 0 0 0 26px rgba(201, 151, 77, .07), 0 0 0 54px rgba(201, 151, 77, .04); pointer-events: none; }
    .ur-ta-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #E2B66E; margin: 0 0 8px; }
    .ur-ta-hero h1, .ur-ta-card h2, .ur-ta-av { font-variant-numeric: lining-nums; }
    .ur-ta-hero h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 700; color: #fff; margin: 0 0 8px; position: relative; z-index: 1; }
    .ur-ta-sub { color: #C9DAD2; font-size: 14px; margin: 0; max-width: 700px; line-height: 1.65; position: relative; z-index: 1; }
    .ur-ta-sub b { color: #E2B66E; }

    .ur-ta-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
    .ur-ta-stat { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 16px 18px; text-decoration: none !important; transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease; }
    .ur-ta-stat:hover { box-shadow: 0 10px 24px rgba(15, 46, 36, .1); transform: translateY(-2px); border-color: #C9974D; }
    .ur-ta-stat.is-on { border-color: #C9974D; box-shadow: 0 0 0 2px rgba(201, 151, 77, .35); }
    .ur-ta-stat__ico { flex: none; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .ur-ta-stat__ico i { color: inherit !important; }
    .ur-ta-stat--active .ur-ta-stat__ico { background: #E3F4EA; color: #17734B; }
    .ur-ta-stat--waiting .ur-ta-stat__ico { background: #EAF0FB; color: #2F4F8F; }
    .ur-ta-stat--expired .ur-ta-stat__ico { background: #F1EEE6; color: #6B7570; }
    .ur-ta-stat--ended .ur-ta-stat__ico { background: #FDECEA; color: #A3281E; }
    .ur-ta-stat__num { font-variant-numeric: lining-nums; display: block; font-family: 'Playfair Display', Georgia, serif; font-size: 26px; font-weight: 700; line-height: 1; color: #123A2E; }
    .ur-ta-stat__lbl { display: block; margin-top: 4px; font-size: 12px; font-weight: 700; letter-spacing: .04em; color: #6B7570; }

    /* ---- cards / form ---- */
    .ur-ta-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 20px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 14px rgba(15, 46, 36, .04); }
    .ur-ta-card__head { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .ur-ta-card__ico { flex: none; width: 40px; height: 40px; border-radius: 12px; background: #FBF0DA; color: #B07C3D; display: flex; align-items: center; justify-content: center; }
    .ur-ta-card__ico i { color: inherit !important; }
    .ur-ta-card h2 { font-family: 'Playfair Display', Georgia, serif; font-size: 19px; font-weight: 700; color: #123A2E; margin: 0; }
    .ur-ta-card p.hint { margin: 2px 0 0; font-size: 13px; color: #6B7570; }
    .ur-ta-form { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto auto; gap: 14px; align-items: end; }
    .ur-ta-field { min-width: 0; }
    .ur-ta-field label { display: block; margin: 0 0 6px; font-size: 11.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #6B7570; }
    .ur-ta-field input[type=text], .ur-ta-field input[type=email] { width: 100%; height: 46px; padding: 0 14px; border: 1px solid #E7E2D6; border-radius: 12px; background: #FBF7EF; font-size: 14px; color: #1C2321; }
    .ur-ta-field input:focus { outline: none; border-color: #C9974D; background: #fff; box-shadow: 0 0 0 3px rgba(201, 151, 77, .15); }
    .ur-ta-mins { display: inline-flex; gap: 8px; }
    .ur-ta-mins label { margin: 0; text-transform: none; letter-spacing: 0; cursor: pointer; }
    .ur-ta-mins input { position: absolute; opacity: 0; pointer-events: none; }
    .ur-ta-mins span { display: inline-flex; align-items: center; justify-content: center; height: 46px; min-width: 84px; padding: 0 16px; border: 1.5px solid #E7E2D6; border-radius: 12px; background: #fff; font-size: 14px; font-weight: 700; color: #123A2E; transition: all .15s ease; }
    .ur-ta-mins input:checked + span { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-ta-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 46px; padding: 0 22px; border: 0; border-radius: 999px; background: #123A2E; color: #fff !important; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none !important; white-space: nowrap; }
    .ur-ta-btn:hover { background: #0D2C23; }
    /* the site's global span/icon colours must not override the button text */
    .ur-ta-btn i, .ur-ta-btn span { color: inherit !important; }
    .ur-ta-btn--wa { background: #25D366; }
    .ur-ta-btn--wa:hover { background: #1DA851; }
    .ur-ta-btn--light { background: #fff; color: #123A2E !important; border: 1.5px solid #D9C79B; }
    .ur-ta-btn--light:hover { background: #FBF0DA; }
    .ur-ta-btn--sm { height: 34px; padding: 0 13px; font-size: 12.5px; }
    .ur-ta-btn--danger { background: #fff; color: #B5413A !important; border: 1.5px solid #E4A8A2; }
    .ur-ta-btn--danger:hover { background: #FDECEA; }
    .ur-ta-error { color: #B5413A; font-size: 12.5px; margin-top: 6px; }

    /* ---- credentials shown once ---- */
    .ur-ta-created { border: 2px solid #C9974D; background: linear-gradient(135deg, #FFFBF0, #fff); }
    .ur-ta-created__box { background: #FBF7EF; border: 1px dashed #D9C79B; border-radius: 12px; padding: 14px 16px; margin: 14px 0; }
    .ur-ta-created__row { display: flex; gap: 10px; padding: 4px 0; font-size: 14px; }
    .ur-ta-created__row span { width: 90px; color: #6B7570; font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; padding-top: 2px; }
    .ur-ta-created__row b { color: #123A2E; word-break: break-all; font-family: 'Courier New', monospace; font-size: 15px; }
    .ur-ta-created textarea { width: 100%; min-height: 150px; padding: 12px 14px; border: 1px solid #E7E2D6; border-radius: 12px; background: #fff; font-family: inherit; font-size: 13.5px; line-height: 1.6; color: #1C2321; resize: vertical; }
    .ur-ta-created__actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
    .ur-ta-warn { margin: 12px 0 0; font-size: 12.5px; color: #8A5A12; }

    /* ---- list toolbar ---- */
    .ur-ta-toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
    .ur-ta-tabs { display: flex; flex-wrap: wrap; gap: 6px; }
    .ur-ta-tab { display: inline-flex; align-items: center; gap: 7px; height: 36px; padding: 0 14px; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E !important; font-size: 12.5px; font-weight: 700; text-decoration: none !important; }
    .ur-ta-tab:hover { border-color: #C9974D; }
    .ur-ta-tab em { font-style: normal; font-size: 11px; font-weight: 700; background: #F1EEE6; color: #6B7570; border-radius: 999px; padding: 1px 8px; }
    .ur-ta-tab.is-on { background: #123A2E; border-color: #123A2E; color: #fff !important; }
    .ur-ta-tab.is-on em { background: rgba(255, 255, 255, .18); color: #fff; }
    .ur-ta-search { position: relative; }
    .ur-ta-search input { height: 38px; width: 250px; max-width: 100%; padding: 0 14px 0 36px; border: 1px solid #E7E2D6; border-radius: 999px; background: #FBF7EF; font-size: 13px; color: #1C2321; }
    .ur-ta-search input:focus { outline: none; border-color: #C9974D; background: #fff; }
    .ur-ta-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9AA5A0; font-size: 13px; pointer-events: none; }

    /* ---- list ---- */
    .ur-ta-head, .ur-ta-item { display: grid; grid-template-columns: minmax(0, 2.2fr) 84px minmax(0, 1.5fr) minmax(0, 1.1fr) 226px; gap: 16px; align-items: center; }
    .ur-ta-head { padding: 0 18px 8px; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #9AA5A0; }
    .ur-ta-list { display: grid; gap: 10px; }
    .ur-ta-item { padding: 14px 18px; border: 1px solid #EFEADD; border-radius: 16px; background: #fff; transition: box-shadow .2s ease, border-color .2s ease; }
    .ur-ta-item:hover { border-color: #D9C79B; box-shadow: 0 8px 20px rgba(15, 46, 36, .07); }
    .ur-ta-who { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ur-ta-av { flex: none; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #123A2E, #1E5A46); color: #E2B66E; font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 17px; }
    .ur-ta-item__name { font-weight: 700; color: #123A2E; font-size: 14.5px; }
    .ur-ta-item__meta { font-size: 12.5px; color: #6B7570; margin-top: 2px; overflow-wrap: anywhere; }
    .ur-ta-dur { display: inline-flex; align-items: center; justify-content: center; height: 28px; padding: 0 12px; border-radius: 8px; background: #FBF0DA; color: #8A6218; font-size: 12px; font-weight: 700; }
    .ur-ta-created-at { font-size: 12.5px; color: #1C2321; line-height: 1.35; }
    .ur-ta-created-at small { display: block; color: #9AA5A0; font-size: 11.5px; }
    .ur-ta-pill { display: inline-flex; align-items: center; gap: 7px; padding: 6px 13px; border-radius: 999px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .ur-ta-pill--waiting { background: #EAF0FB; color: #2F4F8F; }
    .ur-ta-pill--active { background: #E3F4EA; color: #17734B; }
    .ur-ta-pill--expired { background: #F1EEE6; color: #6B7570; }
    .ur-ta-pill--revoked { background: #FDECEA; color: #A3281E; }
    .ur-ta-item__actions { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
    .ur-ta-item__actions form { margin: 0; }
    .ur-ta-empty { text-align: center; padding: 38px 16px; color: #6B7570; font-size: 14px; border: 1px dashed #DDD5C2; border-radius: 16px; background: #FDFBF6; }

    /* ---- pagination ---- */
    .ur-ta-foot { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
    .ur-ta-count { font-size: 13px; color: #6B7570; }
    .ur-ta-count b { color: #123A2E; }
    .ur-ta-pager .pagination { margin: 0; display: flex; flex-wrap: wrap; gap: 6px; padding: 0; list-style: none; }
    .ur-ta-pager .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px !important; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-size: 13px; font-weight: 700; box-shadow: none; }
    .ur-ta-pager .page-item .page-link:hover { background: #FBF0DA; border-color: #C9974D; color: #123A2E; }
    .ur-ta-pager .page-item.active .page-link { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-ta-pager .page-item.disabled .page-link { background: #F6F4EF; color: #B8B2A2; }

    @media (max-width: 960px) {
        .ur-ta-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ur-ta-form { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .ur-ta-form .ur-ta-btn { grid-column: 1 / -1; width: 100%; }
        .ur-ta-head { display: none; }
        .ur-ta-item { grid-template-columns: minmax(0, 1fr) auto; gap: 10px 12px; }
        .ur-ta-who { grid-column: 1 / -1; }
        .ur-ta-created-at { grid-column: 1 / -1; }
        .ur-ta-item__actions { grid-column: 1 / -1; justify-content: flex-start; }
    }
    @media (max-width: 560px) {
        .ur-ta-hero { padding: 22px 20px; border-radius: 18px; }
        .ur-ta-hero h1 { font-size: 26px; }
        .ur-ta-form { grid-template-columns: minmax(0, 1fr); }
        .ur-ta-mins span { min-width: 0; flex: 1; }
        .ur-ta-mins { display: flex; }
        .ur-ta-mins label { flex: 1; }
        .ur-ta-card { padding: 18px; }
        .ur-ta-search, .ur-ta-search input { width: 100%; }
        .ur-ta-toolbar { align-items: stretch; }
        .ur-ta-foot { justify-content: center; }
        .ur-ta-stat { padding: 13px 14px; gap: 11px; }
        .ur-ta-stat__ico { width: 38px; height: 38px; font-size: 16px; }
        .ur-ta-stat__num { font-size: 22px; }
    }

    /* ---- phones: tidier hero/stats/form, swipeable filter chips, and each account as an ivory card with a full-width action row ---- */
    @media (max-width: 767.98px) {
        .ur-ta-hero { padding: 20px 18px; margin-bottom: 14px; }
        .ur-ta-hero h1 { font-size: 24px; }
        .ur-ta-hero::after { width: 150px; height: 150px; right: -50px; top: -50px; }
        .ur-ta-sub { font-size: 13px; line-height: 1.6; }
        .ur-ta-stats { gap: 10px; margin-bottom: 14px; }
        .ur-ta-stat { flex-direction: column; align-items: flex-start; gap: 10px; padding: 14px; background: #FBF8F1; border-left: 4px solid #C9974D; }
        .ur-ta-stat__lbl { font-size: 11.5px; line-height: 1.3; }
        .ur-ta-card { border-radius: 16px; margin-bottom: 14px; }
        .ur-ta-card__head { align-items: flex-start; }
        .ur-ta-form { gap: 12px; }
        .ur-ta-form .ur-ta-btn { width: 100%; }
        .ur-ta-created__row { flex-direction: column; gap: 2px; padding: 7px 0; border-bottom: 1px dashed #E8DDC0; }
        .ur-ta-created__row:last-child { border-bottom: 0; }
        .ur-ta-created__row span { width: auto; }
        .ur-ta-created__row b { font-size: 14px; }
        .ur-ta-created__actions .ur-ta-btn { flex: 1 1 100%; }
        .ur-ta-toolbar { flex-direction: column; gap: 10px; }
        .ur-ta-tabs { flex-wrap: nowrap; overflow-x: auto; margin: 0 -18px; padding: 0 18px 4px; scrollbar-width: none; }
        .ur-ta-tabs::-webkit-scrollbar { display: none; }
        .ur-ta-tab { flex: 0 0 auto; }
        .ur-ta-search input { height: 42px; }
        .ur-ta-list { gap: 12px; }
        .ur-ta-item { grid-template-columns: auto minmax(0, 1fr); gap: 0 10px; background: #FBF8F1; border: 1px solid #E3D9C3; border-left: 4px solid #C9974D; border-radius: 12px; padding: 14px; box-shadow: 0 2px 8px rgba(15,46,36,.06); }
        .ur-ta-item > .ur-ta-who { grid-column: 1 / -1; }
        .ur-ta-item__name { font-family: 'Playfair Display', Georgia, serif; font-size: 16px; }
        .ur-ta-item > div:nth-child(2) { grid-column: 1; grid-row: 2; margin-top: 12px; padding-top: 10px; border-top: 1px dashed #DCCFB4; }
        .ur-ta-item > div:nth-child(3) { grid-column: 2; grid-row: 2; margin-top: 12px; padding-top: 10px; border-top: 1px dashed #DCCFB4; text-align: right; }
        .ur-ta-item > .ur-ta-created-at { grid-row: 3; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #DCCFB4; }
        .ur-ta-item > .ur-ta-item__actions { grid-row: 4; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #DCCFB4; display: grid; grid-template-columns: repeat(auto-fit, minmax(60px, 1fr)); gap: 8px; }
        .ur-ta-item__actions form, .ur-ta-item__actions .ur-ta-btn { width: 100%; }
        .ur-ta-item__actions .ur-ta-btn--sm { height: 40px; }
        .ur-ta-pill { white-space: normal; text-align: left; }
        .ur-ta-foot { flex-direction: column; align-items: center; text-align: center; }
        .ur-ta-pager .pagination { justify-content: center; }
    }
</style>

<div class="ur-ta">
    <div class="ur-ta-hero">
        <div class="ur-ta-eyebrow">Admin &middot; Team</div>
        <h1>Temporary Access</h1>
        <p class="ur-ta-sub">Give someone a short look at the team portal. They sign in with the email and password you send, and their 15 or 30 minutes start at their <b>first sign-in</b>. When time is up the account can never sign in again. They can only browse the shared proposals &mdash; no photos, no phone numbers or emails, no sharing and no editing.</p>
    </div>

    <div class="ur-ta-stats">
        @foreach([
            'active' => ['Active now', 'fa-play-circle'],
            'waiting' => ['Not signed in yet', 'fa-hourglass-half'],
            'expired' => ['Expired', 'fa-clock-o'],
            'ended' => ['Ended by admin', 'fa-ban'],
        ] as $key => [$label, $icon])
            <a class="ur-ta-stat ur-ta-stat--{{ $key }} {{ $filter === $key ? 'is-on' : '' }}" href="{{ route('team.manage.temporary', ['status' => $key]) }}">
                <span class="ur-ta-stat__ico"><i class="fa {{ $icon }}"></i></span>
                <span><span class="ur-ta-stat__num">{{ $counts[$key] }}</span><span class="ur-ta-stat__lbl">{{ $label }}</span></span>
            </a>
        @endforeach
    </div>

    @if(session('temp_created'))
        @php
            $c = session('temp_created');
            $msg = "Hello " . $c['name'] . ",\n\nHere is your temporary access to the team portal.\n\nLogin: " . $loginUrl . "\nEmail: " . $c['email'] . "\nPassword: " . $c['password'] . "\n\nYou have " . $c['minutes'] . " minutes, starting the moment you sign in. After that the access ends and cannot be used again. Please do not share these details.";
        @endphp
        <div class="ur-ta-card ur-ta-created">
            <div class="ur-ta-card__head">
                <span class="ur-ta-card__ico" style="background:#E3F4EA; color:#17734B;"><i class="fa fa-check"></i></span>
                <div>
                    <h2>Access created for {{ $c['name'] }}</h2>
                    <p class="hint">Send these details now. The password is shown only this once.</p>
                </div>
            </div>
            <div class="ur-ta-created__box">
                <div class="ur-ta-created__row"><span>Login</span><b>{{ $loginUrl }}</b></div>
                <div class="ur-ta-created__row"><span>Email</span><b>{{ $c['email'] }}</b></div>
                <div class="ur-ta-created__row"><span>Password</span><b>{{ $c['password'] }}</b></div>
                <div class="ur-ta-created__row"><span>Time</span><b style="font-family:inherit;">{{ $c['minutes'] }} minutes from the first sign-in</b></div>
            </div>
            <textarea id="ta_msg" readonly>{{ $msg }}</textarea>
            <div class="ur-ta-created__actions">
                <button type="button" class="ur-ta-btn" id="ta_copy"><i class="fa fa-copy"></i> <span>Copy message</span></button>
                <a class="ur-ta-btn ur-ta-btn--wa" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($msg) }}"><i class="fa fa-whatsapp"></i> Send on WhatsApp</a>
            </div>
            <p class="ur-ta-warn"><i class="fa fa-exclamation-triangle"></i> If you lose this message you cannot see the password again &mdash; create a new access instead.</p>
        </div>
    @endif

    <div class="ur-ta-card">
        <div class="ur-ta-card__head">
            <span class="ur-ta-card__ico"><i class="fa fa-user-plus"></i></span>
            <div>
                <h2>Create temporary access</h2>
                <p class="hint">If they never sign in, the details stop working after 24 hours.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('team.manage.temporary.store') }}" class="ur-ta-form" autocomplete="off">
            @csrf
            <div class="ur-ta-field">
                <label for="ta_name">Name</label>
                <input type="text" id="ta_name" name="name" value="{{ old('name') }}" maxlength="100" placeholder="e.g. Ali Raza" required>
                @error('name')<div class="ur-ta-error">{{ $message }}</div>@enderror
            </div>
            <div class="ur-ta-field">
                <label for="ta_email">Email</label>
                <input type="email" id="ta_email" name="email" value="{{ old('email') }}" maxlength="190" placeholder="name@example.com" required>
                @error('email')<div class="ur-ta-error">{{ $message }}</div>@enderror
            </div>
            <div class="ur-ta-field">
                <label>Time</label>
                <div class="ur-ta-mins">
                    <label><input type="radio" name="minutes" value="15" {{ old('minutes', '15') == '15' ? 'checked' : '' }}><span>15 min</span></label>
                    <label><input type="radio" name="minutes" value="30" {{ old('minutes') == '30' ? 'checked' : '' }}><span>30 min</span></label>
                </div>
            </div>
            <button type="submit" class="ur-ta-btn"><i class="fa fa-plus"></i> Create access</button>
        </form>
    </div>

    <div class="ur-ta-card">
        <div class="ur-ta-card__head">
            <span class="ur-ta-card__ico"><i class="fa fa-users"></i></span>
            <div>
                <h2>All temporary accounts</h2>
                <p class="hint">{{ $counts['all'] }} account{{ $counts['all'] === 1 ? '' : 's' }} in total</p>
            </div>
        </div>

        <div class="ur-ta-toolbar">
            <div class="ur-ta-tabs">
                @foreach(['all' => 'All', 'active' => 'Active', 'waiting' => 'Waiting', 'expired' => 'Expired', 'ended' => 'Ended'] as $key => $label)
                    <a class="ur-ta-tab {{ $filter === $key ? 'is-on' : '' }}" href="{{ route('team.manage.temporary', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $search ?: null])) }}">{{ $label }} <em>{{ $counts[$key] }}</em></a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('team.manage.temporary') }}" class="ur-ta-search">
                @if($filter !== 'all')<input type="hidden" name="status" value="{{ $filter }}">@endif
                <i class="fa fa-search"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name or email" autocomplete="off">
            </form>
        </div>

        @if($accounts->isEmpty())
            <div class="ur-ta-empty"><i class="fa fa-clock-o" style="font-size:28px; color:#C9974D; display:block; margin-bottom:8px;"></i>
                @if($search !== '' || $filter !== 'all') No accounts match this view. <a href="{{ route('team.manage.temporary') }}" style="color:#B07C3D; font-weight:700;">Show all</a>
                @else No temporary access has been created yet. @endif
            </div>
        @else
            <div class="ur-ta-head"><span>Account</span><span>Time</span><span>Status</span><span>Created</span><span style="text-align:right;">Actions</span></div>
            <div class="ur-ta-list">
                @foreach($accounts as $a)
                    @php $state = $a->tempState(); $left = $a->tempSecondsLeft(); $full = trim($a->first_name . ' ' . $a->last_name); @endphp
                    <div class="ur-ta-item">
                        <div class="ur-ta-who">
                            <span class="ur-ta-av">{{ strtoupper(mb_substr($full ?: '?', 0, 1)) }}</span>
                            <div style="min-width:0;">
                                <div class="ur-ta-item__name">{{ $full }}</div>
                                <div class="ur-ta-item__meta">{{ $a->email }}</div>
                            </div>
                        </div>
                        <div><span class="ur-ta-dur">{{ $a->temp_minutes }} min</span></div>
                        <div>
                            @if($state === 'active')
                                <span class="ur-ta-pill ur-ta-pill--active"><i class="fa fa-circle" style="font-size:8px;"></i> Active &middot; <span class="js-ta-left" data-left="{{ $left }}">--:--</span> left</span>
                            @elseif($state === 'waiting')
                                <span class="ur-ta-pill ur-ta-pill--waiting"><i class="fa fa-hourglass-half"></i> Not signed in yet</span>
                            @elseif($state === 'revoked')
                                <span class="ur-ta-pill ur-ta-pill--revoked"><i class="fa fa-ban"></i> Ended by admin</span>
                            @else
                                <span class="ur-ta-pill ur-ta-pill--expired"><i class="fa fa-clock-o"></i> Expired</span>
                            @endif
                        </div>
                        <div class="ur-ta-created-at">{{ $a->created_at->format('d M Y') }}<small>{{ $a->created_at->format('h:i A') }} &middot; {{ $a->created_at->diffForHumans() }}</small></div>
                        <div class="ur-ta-item__actions">
                            @foreach([15, 30] as $m)
                            <form method="POST" action="{{ route('team.manage.temporary.extend', $a->dataid) }}">
                                @csrf
                                <input type="hidden" name="minutes" value="{{ $m }}">
                                <button type="submit" class="ur-ta-btn ur-ta-btn--light ur-ta-btn--sm" title="Give {{ $m }} more minutes starting now">+{{ $m }}</button>
                            </form>
                            @endforeach
                            @if($state === 'active' || $state === 'waiting')
                            <form method="POST" action="{{ route('team.manage.temporary.revoke', $a->dataid) }}" class="js-ta-confirm" data-title="End this access now?" data-text="{{ $full }} will be signed out and can never sign in again.">
                                @csrf
                                <button type="submit" class="ur-ta-btn ur-ta-btn--danger ur-ta-btn--sm" title="End access now"><i class="fa fa-ban"></i> End</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('team.manage.temporary.destroy', $a->dataid) }}" class="js-ta-confirm" data-title="Delete this account?" data-text="It is removed completely.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ur-ta-btn ur-ta-btn--danger ur-ta-btn--sm" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="ur-ta-foot">
                <div class="ur-ta-count">Showing <b>{{ $accounts->firstItem() }}&ndash;{{ $accounts->lastItem() }}</b> of <b>{{ $accounts->total() }}</b></div>
                @if($accounts->hasPages())
                    <div class="ur-ta-pager">{{ $accounts->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        // copy the ready-to-send message
        var copy = document.getElementById('ta_copy'), box = document.getElementById('ta_msg');
        if (copy && box) {
            copy.addEventListener('click', function () {
                var done = function () { var s = copy.querySelector('span'); s.textContent = 'Copied!'; setTimeout(function () { s.textContent = 'Copy message'; }, 1800); };
                if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(box.value).then(done);
                else { box.select(); document.execCommand('copy'); done(); }
            });
        }
        // live "time left" on running accounts
        var timers = [].slice.call(document.querySelectorAll('.js-ta-left'));
        var now = Date.now();
        timers.forEach(function (el) { el._end = now + parseInt(el.getAttribute('data-left'), 10) * 1000; });
        function tick() {
            var reload = false;
            timers.forEach(function (el) {
                var left = Math.max(0, Math.round((el._end - Date.now()) / 1000)), m = Math.floor(left / 60), s = left % 60;
                el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                if (left === 0 && !el._done) { el._done = true; reload = true; }
            });
            if (reload) setTimeout(function () { location.reload(); }, 800);
        }
        if (timers.length) { tick(); setInterval(tick, 1000); }
        // loader while an account is created / extended / ended / deleted (the page reloads when the server is done)
        function busy(text) {
            var L = document.getElementById('ur_search_loader');
            if (!L) return;
            var t = L.querySelector('strong'); if (t) t.textContent = text;
            L.classList.add('is-on');
        }
        window.addEventListener('pageshow', function () { var L = document.getElementById('ur_search_loader'); if (L) L.classList.remove('is-on'); });
        document.querySelectorAll('form[method="POST"][action*="temporary-access"]:not(.js-ta-confirm)').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                if (e.defaultPrevented) return;
                var b = f.querySelector('button[type="submit"]');
                busy(f.classList.contains('ur-ta-form') ? 'Creating account...' : 'Updating...');
                if (b) setTimeout(function () { b.disabled = true; }, 0);   // no double submit (a disabled button still lets this submit go through)
            });
        });
        // confirm before ending / deleting
        document.querySelectorAll('form.js-ta-confirm').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                if (f.dataset.ok === '1') return;
                e.preventDefault();
                var go = function () { f.dataset.ok = '1'; busy('Updating...'); f.submit(); };
                if (typeof swalConfirm === 'function') swalConfirm(f.dataset.title, f.dataset.text, go); else if (confirm(f.dataset.title + '\n' + f.dataset.text)) go();
            });
        });
    })();
</script>
@endsection
