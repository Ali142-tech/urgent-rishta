<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complete Your Profile | Urgent Rishta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root { --green:#123A2E; --green-deep:#0B2A20; --green-soft:#1C5A47; --gold:#C9974D; --gold-soft:#F6E9D0; --cream:#FBF7EF; --line:#EADFCB; --ink:#1C2321; --muted:#6B7570; --ok:#1C9A6C; --err:#B5413A; --mint:#EAF4EF; }
        * { box-sizing: border-box; }
        html, body { max-width:100%; overflow-x:hidden; }
        .layout > * { min-width:0; }
        body { margin:0; background:
            radial-gradient(900px 420px at 100% -80px, rgba(201,151,77,.14), transparent 60%),
            radial-gradient(800px 380px at -10% 0, rgba(18,58,46,.08), transparent 60%), var(--cream);
            color:var(--ink); font-family:'Manrope', system-ui, sans-serif; }
        .wrap { max-width:1260px; margin:0 auto; padding:22px 22px 60px; }

        /* header */
        .bar { display:flex; align-items:center; justify-content:space-between; gap:14px; margin-bottom:22px; }
        .brand { display:flex; align-items:center; gap:13px; text-decoration:none; color:inherit; }
        .brand__mark { width:48px; height:48px; border-radius:15px; background:linear-gradient(145deg, var(--green-soft), var(--green-deep)); color:#fff; display:grid; place-items:center; font-family:'Playfair Display', serif; font-weight:700; font-size:21px; letter-spacing:.02em; box-shadow:0 10px 22px rgba(18,58,46,.28), inset 0 0 0 2px rgba(201,151,77,.55); }
        .brand__name { display:block; font-family:'Playfair Display', serif; font-weight:700; font-size:21px; letter-spacing:.08em; color:var(--green); line-height:1.1; }
        .brand__tag { display:block; font-size:10.5px; letter-spacing:.22em; color:#8A7A57; font-weight:700; margin-top:3px; }
        .bar__actions { display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
        .btn { border:0; border-radius:12px; padding:11px 18px; font:inherit; font-weight:800; font-size:13.5px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
        .btn--ghost { background:#fff; border:1px solid var(--line); color:var(--green); }
        .btn--ghost:hover { border-color:var(--green); }
        .btn--gold { background:var(--gold-soft); border:1px solid #E8CC96; color:#7A5A12; }
        .btn--gold:hover { background:#F1DDB6; }
        .btn--green { background:linear-gradient(180deg, var(--green-soft), var(--green)); color:#fff; box-shadow:0 10px 20px rgba(18,58,46,.22); }
        .btn--green:hover { filter:brightness(1.08); }
        .btn[disabled] { opacity:.55; cursor:default; }
        .lockpill { font-size:12px; font-weight:800; color:#7A5A12; background:var(--gold-soft); border:1px solid #E8CC96; border-radius:999px; padding:8px 13px; display:inline-flex; gap:7px; align-items:center; }

        .layout { display:grid; grid-template-columns:318px minmax(0,1fr); gap:24px; align-items:start; }
        .card { background:#fff; border:1px solid var(--line); border-radius:20px; box-shadow:0 14px 36px rgba(15,46,36,.07); }

        /* left rail */
        .rail { position:sticky; top:16px; padding:22px 20px; }
        .me { display:flex; align-items:center; gap:13px; padding-bottom:16px; border-bottom:1px solid var(--line); }
        .me__av { width:56px; height:56px; border-radius:50%; flex:none; overflow:hidden; background:linear-gradient(145deg, var(--gold), #A9732A); color:#fff; display:grid; place-items:center; font-weight:800; font-size:20px; position:relative; box-shadow:0 0 0 3px #fff, 0 0 0 5px rgba(201,151,77,.45); }
        .me__av img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .me__name { font-weight:800; font-size:16px; color:var(--green); line-height:1.25; }
        .me__meta { font-size:12px; color:var(--muted); margin-top:3px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
        .chip { font-size:10.5px; font-weight:800; letter-spacing:.05em; text-transform:uppercase; border-radius:999px; padding:3px 9px; }
        .chip--draft { background:var(--gold-soft); color:#8A6411; }
        .chip--done { background:#DDF3E8; color:#13734F; }
        .pc { margin:16px 0 4px; display:flex; align-items:baseline; justify-content:space-between; }
        .pc b { font-size:13px; }
        .pc span { font-size:26px; font-weight:800; color:var(--ok); }
        .bar-track { height:9px; background:#F0E8D6; border-radius:999px; overflow:hidden; }
        .bar-fill { height:100%; width:0; background:linear-gradient(90deg, #35B98A, var(--ok)); border-radius:999px; transition:width .5s; }
        .help { font-size:12.5px; line-height:1.55; color:var(--muted); margin:12px 0 16px; }
        .steps { list-style:none; margin:0; padding:0; display:grid; gap:4px; }
        .steps li button { width:100%; text-align:left; background:transparent; border:0; border-radius:13px; padding:9px 10px; font:inherit; font-weight:700; font-size:13.5px; color:#3B4540; cursor:pointer; display:flex; align-items:center; gap:11px; }
        .steps li button:hover { background:#F7F2E6; }
        .steps li.is-active button { background:var(--mint); color:var(--green); box-shadow:inset 3px 0 0 var(--green); }
        .steps .num { width:28px; height:28px; border-radius:9px; background:#F3EDDD; color:#7A7052; display:grid; place-items:center; font-size:11.5px; font-weight:800; flex:none; }
        .steps li.is-active .num { background:var(--green); color:#fff; }
        .steps li.is-done .num { background:#DDF3E8; color:#13734F; font-size:13px; }
        .steps .cnt { margin-left:auto; font-size:11px; font-weight:800; color:#A39A7F; }
        .elig { margin-top:16px; background:linear-gradient(180deg, #FFF9EC, #FFF4DE); border:1px solid #F0DDB2; border-radius:15px; padding:14px 15px; }
        .elig b { display:flex; align-items:center; gap:8px; color:#7A5A12; font-size:13px; }
        .elig p { margin:6px 0 0; font-size:12.5px; line-height:1.55; color:#8B7340; }

        /* main */
        .main { overflow:hidden; }
        .hero { padding:28px 34px 22px; background:linear-gradient(135deg, #F1F8F4 0%, #FFFDF8 60%, #FDF4E2 100%); border-bottom:1px solid var(--line); position:relative; }
        .hero::after { content:""; position:absolute; right:-40px; top:-60px; width:220px; height:220px; border-radius:50%; background:radial-gradient(circle, rgba(201,151,77,.22), transparent 70%); }
        .eyebrow { color:var(--ok); font-size:11.5px; letter-spacing:.16em; font-weight:800; text-transform:uppercase; position:relative; }
        .hero h1 { font-family:'Playfair Display', serif; font-size:32px; margin:8px 0 8px; color:var(--green); position:relative; }
        .hero p { color:var(--muted); font-size:14px; margin:0; line-height:1.6; max-width:700px; position:relative; }
        .hero__in { display:flex; align-items:center; justify-content:space-between; gap:28px; position:relative; z-index:1; }
        .score { flex:none; display:flex; align-items:center; gap:16px; background:#fff; border:1px solid var(--line); border-radius:20px; padding:14px 20px 14px 14px; box-shadow:0 10px 26px rgba(15,46,36,.09); }
        .score__ring { --p:0; width:96px; height:96px; border-radius:50%; background:conic-gradient(var(--ok) calc(var(--p) * 1%), #EFE7D6 0); display:grid; place-items:center; transition:background .5s; }
        .score__ring::before { content:''; grid-area:1/1; width:76px; height:76px; border-radius:50%; background:#fff; }
        .score__num { grid-area:1/1; font-size:27px; font-weight:800; color:var(--green); line-height:1; position:relative; }
        .score__num small { font-size:14px; font-weight:800; }
        .score__lbl { font-size:11px; letter-spacing:.14em; font-weight:800; color:#8A7A57; text-transform:uppercase; }
        .score__sum { font-size:15px; font-weight:800; color:var(--green); margin-top:4px; }
        .score__sub { font-size:12.5px; color:var(--muted); margin-top:3px; }
        .score__grade { display:inline-block; margin-top:8px; font-size:11px; font-weight:800; border-radius:999px; padding:4px 10px; background:var(--gold-soft); color:#8A6411; }
        .score__grade.is-done { background:#DDF3E8; color:#13734F; }
        .segs { display:grid; grid-auto-flow:column; grid-auto-columns:1fr; gap:7px; padding:14px 34px 0; }
        .segs button { height:8px; border:0; border-radius:999px; background:#EDE5D2; cursor:pointer; padding:0; transition:.2s; }
        .segs button.is-done { background:#7CCBA8; }
        .segs button.is-active { background:var(--green); }
        .body { padding:20px 34px 28px; }
        .alert { border-radius:13px; padding:12px 15px; font-size:13.5px; font-weight:600; margin-bottom:18px; }
        .alert--gate { background:#FFF6E6; border:1px solid #F0D79B; color:#7A5A12; display:flex; gap:10px; align-items:center; }
        .alert--err { background:#FDECEC; border:1px solid #F4C6C3; color:var(--err); display:none; }
        .sect__head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:4px; }
        .sect__head h2 { font-size:22px; font-weight:800; color:var(--green); margin:0; }
        .pill { font-size:11.5px; font-weight:800; color:#5C6862; background:#F1EEE5; border-radius:999px; padding:6px 12px; white-space:nowrap; }
        .lead { color:var(--muted); font-size:13.5px; margin:0 0 22px; line-height:1.55; }
        .grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:20px 22px; }
        .field--wide { grid-column:1 / -1; }
        .field .lbl { display:flex; align-items:baseline; justify-content:space-between; gap:10px; margin-bottom:7px; }
        .field label { font-size:13.5px; font-weight:800; color:#26302B; }
        .tag { font-size:12px; font-weight:700; color:#9AA39E; white-space:nowrap; }
        .tag--req { color:var(--err); }
        .field input, .field select, .field textarea { width:100%; border:1px solid #DDD3BE; border-radius:13px; background:#fff; padding:13px 15px; font:inherit; font-size:14.5px; color:var(--ink); transition:.15s; }
        .field select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' fill='none' stroke='%23123A2E' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 15px center; padding-right:38px; }
        .field textarea { min-height:150px; resize:vertical; line-height:1.55; }
        .field input::placeholder, .field textarea::placeholder { color:#B2AC9C; }
        .field input:hover, .field select:hover, .field textarea:hover { border-color:#C9BE9F; }
        .field input:focus, .field select:focus, .field textarea:focus { outline:0; border-color:var(--green); box-shadow:0 0 0 4px rgba(18,58,46,.10); }
        .field.has-error input, .field.has-error select, .field.has-error textarea { border-color:var(--err); background:#FFF8F7; }
        .field .err { color:var(--err); font-size:12.5px; font-weight:600; margin-top:6px; display:none; }
        .field.has-error .err { display:block; }
        .field .note { font-size:12px; color:#8A938E; margin-top:6px; }
        .field.is-hidden { display:none; }
        .dob { display:grid; grid-template-columns:1fr 1.3fr 1fr; gap:10px; }
        .foot { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:30px; padding-top:22px; border-top:1px solid var(--line); flex-wrap:wrap; }
        .foot__right { display:flex; gap:10px; margin-left:auto; flex-wrap:wrap; }
        .missing { margin-top:22px; background:#FFF9EE; border:1px dashed #E9D2A0; border-radius:15px; padding:15px 17px; font-size:13px; }
        .missing b { color:var(--green); }
        .missing ul { margin:10px 0 0; padding:0; list-style:none; display:flex; flex-wrap:wrap; gap:8px; }
        .missing li button { background:#fff; border:1px solid #E9D2A0; border-radius:999px; padding:5px 12px; font:inherit; font-size:12px; font-weight:700; color:#7A5A12; cursor:pointer; }
        .missing li button:hover { background:#FFF3D6; }
        .toast { position:fixed; right:20px; bottom:20px; background:var(--green); color:#fff; padding:13px 19px; border-radius:13px; font-weight:700; font-size:14px; box-shadow:0 14px 30px rgba(0,0,0,.22); transform:translateY(20px); opacity:0; pointer-events:none; transition:.25s; z-index:50; }
        .toast.show { transform:none; opacity:1; }
        .step-pane { display:none; }
        .step-pane.is-active { display:block; animation:rise .25s ease; }
        @keyframes rise { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
        .fineprint { text-align:center; color:#9A9279; font-size:12px; margin-top:26px; letter-spacing:.04em; }

        @media (max-width: 960px) {
            .layout { grid-template-columns:1fr; }
            .rail { position:static; }
            .steps { grid-auto-flow:column; grid-auto-columns:max-content; overflow-x:auto; padding-bottom:4px; }
            .steps li button { white-space:nowrap; }
            .steps .cnt { display:none; }
            .hero, .body { padding-left:20px; padding-right:20px; }
            .segs { padding-left:20px; padding-right:20px; }
            .grid { grid-template-columns:1fr; }
            .hero h1 { font-size:26px; }
            .hero__in { flex-direction:column; align-items:flex-start; gap:18px; }
            .score { width:100%; }
            .wrap { padding:16px 14px 50px; }
            .brand__tag { display:none; }
            .bar { flex-direction:column; align-items:stretch; gap:14px; }
            .brand__name { font-size:19px; letter-spacing:.06em; }
            .brand__mark { width:42px; height:42px; font-size:18px; border-radius:13px; }
            .bar__actions { justify-content:flex-start; gap:8px; }
            .bar__actions .btn { padding:9px 13px; font-size:12.5px; }
            .lockpill { flex:1 1 100%; justify-content:center; }
            .bar__actions form { margin-left:auto !important; }
            .rail { padding:18px 16px; }
            .steps { max-width:100%; margin:0 -4px; padding:0 4px 6px; -webkit-overflow-scrolling:touch; }
            .steps li { flex:none; }
            .steps li button { padding:8px 12px 8px 8px; background:#F8F4E9; }
            .steps li.is-active button { box-shadow:none; background:var(--green); color:#fff; }
            .steps li.is-active .num { background:rgba(255,255,255,.2); }
            .elig { display:none; }
            .main { border-radius:18px; }
            .hero { padding:22px 18px 18px; }
            .hero h1 { font-size:24px; overflow-wrap:anywhere; }
            .score { padding:12px 14px; gap:12px; }
            .score__ring { width:84px; height:84px; }
            .score__ring::before { width:66px; height:66px; }
            .score__num { font-size:23px; }
            .segs { padding:12px 18px 0; gap:5px; }
            .body { padding:16px 18px 22px; }
            .sect__head { flex-direction:column; gap:8px; }
            .sect__head h2 { font-size:20px; }
            .field input, .field select, .field textarea { font-size:16px; }
            .dob { grid-template-columns:.9fr 1.3fr 1.1fr; gap:8px; }
            .dob select { padding:13px 26px 13px 11px; background-position:right 10px center; }
            .foot { flex-direction:column-reverse; align-items:stretch; }
            .foot .btn { width:100%; justify-content:center; }
            .foot__right { margin-left:0; width:100%; }
            .foot > span { display:none; }
            .alert--gate { align-items:flex-start; }
            .toast { left:14px; right:14px; bottom:14px; text-align:center; }
        }
    </style>
</head>
<body>
@php($firstName = trim((string) $user->first_name))
@php($fullName = trim($user->first_name . ' ' . $user->last_name))
@php($initials = strtoupper(mb_substr((string) $user->first_name, 0, 1) . mb_substr((string) $user->last_name, 0, 1)))
<div class="wrap">
    <header class="bar">
        <a class="brand" href="{{ url('/') }}">
            <span class="brand__mark">UR</span>
            <span><span class="brand__name">URGENT RISHTA</span><span class="brand__tag">PRIVATE · VERIFIED · PROFESSIONAL</span></span>
        </a>
        <div class="bar__actions">
            @if($gated)
                <span class="lockpill"><i class="fa fa-lock"></i> Dashboard unlocks at 100%</span>
            @else
                <a class="btn btn--ghost" href="{{ url('member/profile') }}"><i class="fa fa-th-large"></i> Member Dashboard</a>
            @endif
            <button type="button" class="btn btn--gold" id="save_draft"><i class="fa fa-floppy-o"></i> Save Draft</button>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">@csrf<button type="submit" class="btn btn--ghost"><i class="fa fa-power-off"></i> Log out</button></form>
        </div>
    </header>

    <div class="layout">
        <aside class="card rail">
            <div class="me">
                <div class="me__av"><span>{{ $initials ?: 'UR' }}</span>@if($avatar)<img src="{{ $avatar }}" alt="" onerror="this.remove()">@endif</div>
                <div>
                    <div class="me__name">{{ $fullName ?: 'New Member' }}</div>
                    <div class="me__meta"><span class="chip {{ $status['complete'] ? 'chip--done' : 'chip--draft' }}" id="status_chip">{{ $status['complete'] ? 'Complete' : 'Draft' }}</span> <span>ID {{ $user->dataid }}</span></div>
                </div>
            </div>
            <div class="pc"><b>Profile Completion</b><span><i id="pct_num" style="font-style:normal;">{{ $status['percent'] }}</i>%</span></div>
            <div class="bar-track"><div class="bar-fill" id="pct_bar"></div></div>
            <p class="help" id="rail_note">
                @if($gated) Fill in every required detail to unlock your dashboard. You can save a draft and come back any time, and edit all of this later. @else Keep your profile complete and up to date so the right families can find you. @endif
            </p>
            <ul class="steps" id="steps">
                @foreach($sections as $i => $section)
                    @php($st = $status['sections'][$section['key']])
                    <li data-step="{{ $section['key'] }}" class="{{ $st['done'] ? 'is-done' : '' }}">
                        <button type="button" onclick="goStep('{{ $section['key'] }}')">
                            <span class="num">{!! $st['done'] ? '<i class="fa fa-check"></i>' : sprintf('%02d', $i + 1) !!}</span>
                            <span>{{ $section['title'] }}</span>
                            <span class="cnt" id="cnt_{{ $section['key'] }}">{{ $st['filled'] }}/{{ $st['total'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="elig">
                <b><i class="fa fa-shield"></i> Matching Eligibility</b>
                <p>Your profile is shown to other members and used for matching once it is complete, verified and approved by our team.</p>
            </div>
        </aside>

        <main class="card main">
            <div class="hero">
              <div class="hero__in">
                <div>
                    <div class="eyebrow">Urgent Rishta · Member Profile Builder</div>
                    <h1>{{ $gated ? 'Create Your Matrimonial Profile' : 'Edit Your Matrimonial Profile' }}</h1>
                    <p>{{ $firstName ? 'Welcome, ' . $firstName . '. ' : '' }}{{ $gated ? 'Complete the sections below to unlock your dashboard. Save a draft whenever you like and finish the rest when convenient.' : 'Update any section below. Your changes are saved to your profile straight away.' }}</p>
                </div>
                <div class="score" aria-live="polite">
                    <div class="score__ring" id="score_ring" style="--p:{{ $status['percent'] }}"><span class="score__num"><span id="score_num">{{ $status['percent'] }}</span><small>%</small></span></div>
                    <div>
                        <div class="score__lbl">Profile Score</div>
                        <div class="score__sum" id="score_sum">{{ $status['filled'] }} of {{ $status['total'] }} required details</div>
                        <div class="score__sub" id="score_sub">{{ $status['complete'] ? 'Everything required is filled in.' : ($status['total'] - $status['filled']) . ' remaining to reach 100%' }}</div>
                        <span class="score__grade {{ $status['complete'] ? 'is-done' : '' }}" id="score_grade">{{ $status['complete'] ? 'Complete' : 'In progress' }}</span>
                    </div>
                </div>
              </div>
            </div>
            <div class="segs" id="segs">
                @foreach($sections as $section)<button type="button" data-step="{{ $section['key'] }}" onclick="goStep('{{ $section['key'] }}')" title="{{ $section['title'] }}"></button>@endforeach
            </div>

            <div class="body">
                @if($gated)
                    <div class="alert alert--gate"><i class="fa fa-lock"></i> <span>Your dashboard unlocks as soon as your profile reaches <b>100%</b>. Required fields are marked <b style="color:#B5413A">Required *</b>.</span></div>
                @endif
                <div class="alert alert--err" id="top_err"></div>

                @foreach($sections as $i => $section)
                    <section class="step-pane" id="pane_{{ $section['key'] }}" data-step="{{ $section['key'] }}">
                        <div class="sect__head"><h2>{{ $section['title'] }}</h2><span class="pill">Section {{ $i + 1 }} of {{ count($sections) }}</span></div>
                        <p class="lead">{{ $section['desc'] }}</p>
                        <form class="grid" id="form_{{ $section['key'] }}" autocomplete="off" onsubmit="return false;">
                            @foreach($section['fields'] as $field)
                                @php($name = $field['name'])
                                @php($val = $values[$name] ?? null)
                                @php($isReq = $field['required'] || !empty($field['requiredIfOptions']))
                                @if($field['type'] === 'dob')
                                    <div class="field field--wide" data-field="{{ $name }}" data-required="1">
                                        <div class="lbl"><label>{{ $field['label'] }}</label><span class="tag tag--req">Required *</span></div>
                                        <div class="dob">
                                            <select name="day"><option value="">Day</option>@for($d = 1; $d <= 31; $d++)<option value="{{ $d }}" {{ (int) ($dob['day'] ?? 0) === $d ? 'selected' : '' }}>{{ $d }}</option>@endfor</select>
                                            <select name="month"><option value="">Month</option>@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)<option value="{{ $mi + 1 }}" {{ (int) ($dob['month'] ?? 0) === $mi + 1 ? 'selected' : '' }}>{{ $mn }}</option>@endforeach</select>
                                            <select name="year"><option value="">Year</option>@for($y = (int) date('Y') - 18; $y >= (int) date('Y') - 80; $y--)<option value="{{ $y }}" {{ (int) ($dob['year'] ?? 0) === $y ? 'selected' : '' }}>{{ $y }}</option>@endfor</select>
                                        </div>
                                        <div class="err"></div>
                                    </div>
                                @else
                                    <div class="field {{ $field['type'] === 'textarea' ? 'field--wide' : '' }}" data-field="{{ $name }}" data-required="{{ $field['required'] ? 1 : 0 }}" @if(!empty($field['requiredIfOptions'])) data-required-if-options="1" @endif>
                                        <div class="lbl"><label for="f_{{ $name }}">{{ $field['label'] }}</label><span class="tag {{ $isReq ? 'tag--req' : '' }}">{{ $isReq ? 'Required *' : 'Optional' }}</span></div>
                                        @if($field['type'] === 'select' || $field['type'] === 'country')
                                            <select id="f_{{ $name }}" name="{{ $name }}" @if($name === 'religion') data-cascade="caste" @endif @if($name === 'con_of_residence') data-cascade="state" @endif>
                                                <option value="">Select an option</option>
                                                @foreach($field['choices'] as $ov => $ol)<option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>@endforeach
                                            </select>
                                        @elseif($field['type'] === 'caste')
                                            <select id="f_{{ $name }}" name="{{ $name }}"><option value="">Select an option</option>@foreach($dep['castes'] as $ov => $ol)<option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>@endforeach</select>
                                            <div class="note">The list follows the religion you chose.</div>
                                        @elseif($field['type'] === 'state')
                                            <select id="f_{{ $name }}" name="{{ $name }}" data-cascade="city"><option value="">Select an option</option>@foreach($dep['states'] as $ov => $ol)<option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>@endforeach</select>
                                        @elseif($field['type'] === 'city')
                                            <select id="f_{{ $name }}" name="{{ $name }}"><option value="">Select an option</option>@foreach($dep['cities'] as $ov => $ol)<option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>@endforeach</select>
                                        @elseif($field['type'] === 'textarea')
                                            <textarea id="f_{{ $name }}" name="{{ $name }}" maxlength="{{ $field['max'] ?? 2000 }}" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $val }}</textarea>
                                        @elseif($field['type'] === 'number')
                                            <input id="f_{{ $name }}" type="number" name="{{ $name }}" min="{{ $field['min'] ?? 0 }}" max="{{ $field['max'] ?? 99 }}" value="{{ $val }}" placeholder="{{ $field['placeholder'] ?? '' }}">
                                        @else
                                            <input id="f_{{ $name }}" type="text" name="{{ $name }}" maxlength="{{ $field['max'] ?? 255 }}" value="{{ $val }}" placeholder="{{ $field['placeholder'] ?? '' }}">
                                        @endif
                                        <div class="err"></div>
                                    </div>
                                @endif
                            @endforeach
                        </form>

                        <div class="foot">
                            @if($i > 0)<button type="button" class="btn btn--ghost" onclick="goStep('{{ $sections[$i - 1]['key'] }}')"><i class="fa fa-arrow-left"></i> Previous Section</button>@else<span></span>@endif
                            <div class="foot__right">
                                @if($i < count($sections) - 1)
                                    <button type="button" class="btn btn--green js-save" data-step="{{ $section['key'] }}" data-next="{{ $sections[$i + 1]['key'] }}">Save &amp; Next Section <i class="fa fa-arrow-right"></i></button>
                                @else
                                    <button type="button" class="btn btn--green js-save" data-step="{{ $section['key'] }}" data-last="1"><i class="fa fa-check"></i> {{ $gated ? 'Save & Finish' : 'Save Changes' }}</button>
                                @endif
                            </div>
                        </div>
                    </section>
                @endforeach

                <div class="missing" id="missing_box" style="display:none;">
                    <b>Still needed for 100%</b>
                    <ul id="missing_list"></ul>
                </div>
            </div>
        </main>
    </div>
    <div class="fineprint">Urgent Rishta · Private, clear, consent-based profiles. Your information is kept confidential.</div>
</div>

<form id="finish_form" method="POST" action="{{ route('member.complete.finish') }}" style="display:none;">@csrf</form>
<div class="toast" id="toast"></div>

<script>
(function () {
    var SECTIONS = {!! json_encode(collect($sections)->map(function ($s) { return ['key' => $s['key'], 'title' => $s['title']]; })->values()) !!};
    var STATUS = @json($status);
    var SAVE_URL = @json(url('member/profile/complete'));
    var BASE = @json(url('/'));
    var GATED = @json($gated);
    var CSRF = document.querySelector('meta[name=csrf-token]').content;
    var current = @json($start);

    function $(s, r) { return (r || document).querySelector(s); }
    function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
    function toast(msg) { var t = $('#toast'); t.textContent = msg; t.classList.add('show'); setTimeout(function () { t.classList.remove('show'); }, 2600); }
    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function paint(status) {
        STATUS = status;
        $('#pct_num').textContent = status.percent;
        $('#pct_bar').style.width = status.percent + '%';
        $('#score_ring').style.setProperty('--p', status.percent);
        $('#score_num').textContent = status.percent;
        $('#score_sum').textContent = status.filled + ' of ' + status.total + ' required details';
        $('#score_sub').textContent = status.complete ? 'Everything required is filled in.' : (status.total - status.filled) + ' remaining to reach 100%';
        var g = $('#score_grade'); g.textContent = status.complete ? 'Complete' : 'In progress'; g.classList.toggle('is-done', !!status.complete);
        var chip = $('#status_chip');
        chip.textContent = status.complete ? 'Complete' : 'Draft';
        chip.className = 'chip ' + (status.complete ? 'chip--done' : 'chip--draft');
        SECTIONS.forEach(function (s, i) {
            var st = status.sections[s.key], li = $('#steps li[data-step="' + s.key + '"]');
            li.classList.toggle('is-done', !!st.done);
            li.querySelector('.num').innerHTML = st.done ? '<i class="fa fa-check"></i>' : pad(i + 1);
            $('#cnt_' + s.key).textContent = st.filled + '/' + st.total;
            var seg = $('#segs button[data-step="' + s.key + '"]');
            seg.classList.toggle('is-done', !!st.done);
        });
        var box = $('#missing_box'), list = $('#missing_list');
        list.innerHTML = '';
        (status.missing || []).forEach(function (m) {
            var li = document.createElement('li'), b = document.createElement('button');
            b.type = 'button'; b.textContent = m.label; b.onclick = function () { goStep(m.section, m.name); };
            li.appendChild(b); list.appendChild(li);
        });
        box.style.display = (status.missing && status.missing.length) ? '' : 'none';
    }

    window.goStep = function (key, focusName) {
        current = key;
        $$('.step-pane').forEach(function (p) { p.classList.toggle('is-active', p.dataset.step === key); });
        $$('#steps li').forEach(function (li) { li.classList.toggle('is-active', li.dataset.step === key); });
        $$('#segs button').forEach(function (b) { b.classList.toggle('is-active', b.dataset.step === key); });
        $('#top_err').style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (focusName) { var el = document.querySelector('#pane_' + key + ' [name="' + focusName + '"]'); if (el) { setTimeout(function () { el.focus(); }, 250); } }
    };

    // ---- cascades: religion -> caste, country -> state -> city
    function ajax(url) { return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' }).then(function (r) { return r.json(); }); }
    function fill(sel, options, placeholder) {
        var keep = sel.value;
        sel.innerHTML = '';
        var o = document.createElement('option'); o.value = ''; o.textContent = placeholder || 'Select an option'; sel.appendChild(o);
        (options || []).forEach(function (x) { var op = document.createElement('option'); op.value = x.dataid; op.textContent = x.name; sel.appendChild(op); });
        sel.value = keep && (options || []).some(function (x) { return String(x.dataid) === String(keep); }) ? keep : '';
    }
    function stateField() { return $('[data-field="state"]'); }
    function loadCities(stateId, countryId) {
        var city = $('select[name="city"]');
        if (stateId) { ajax(BASE + '/cities/' + stateId + '/0').then(function (r) { fill(city, r.options); }); }
        else if (countryId) { ajax(BASE + '/cities/' + countryId + '/1').then(function (r) { fill(city, r.options); }); }
        else { fill(city, []); }
    }
    function loadStates(countryId) {
        var state = $('select[name="state"]'), wrap = stateField();
        if (!countryId) { fill(state, []); wrap.classList.remove('is-hidden'); return; }
        ajax(BASE + '/states/' + countryId).then(function (r) {
            fill(state, r.options);
            var has = r.options && r.options.length;
            wrap.classList.toggle('is-hidden', !has);
            wrap.dataset.hasOptions = has ? '1' : '0';
            loadCities(state.value, countryId);
        });
    }
    document.addEventListener('change', function (e) {
        var t = e.target;
        if (t.dataset && t.dataset.cascade === 'caste') { ajax(BASE + '/castes/' + t.value).then(function (r) { fill($('select[name="caste"]'), r.options); }); }
        if (t.dataset && t.dataset.cascade === 'state') { loadStates(t.value); }
        if (t.dataset && t.dataset.cascade === 'city') { loadCities(t.value, $('select[name="con_of_residence"]').value); }
        var f = t.closest && t.closest('.field'); if (f) { f.classList.remove('has-error'); }
    });
    document.addEventListener('input', function (e) { var f = e.target.closest && e.target.closest('.field'); if (f) f.classList.remove('has-error'); });

    // does this country have states? (decides whether State is shown / required)
    (function initState() {
        var c = $('select[name="con_of_residence"]'), wrap = stateField();
        if (!c || !wrap) return;
        var sel = $('select[name="state"]'), has = sel.options.length > 1;
        if (c.value && !has) { ajax(BASE + '/states/' + c.value).then(function (r) { var h = r.options && r.options.length; wrap.classList.toggle('is-hidden', !h); wrap.dataset.hasOptions = h ? '1' : '0'; if (!h) loadCities('', c.value); }); }
        else { wrap.dataset.hasOptions = has ? '1' : '0'; }
        var city = $('select[name="city"]');
        if (c.value && !has && city.options.length <= 1) loadCities('', c.value);
    })();

    // ---- validate + save
    function showErrors(form, errors) {
        var first = null;
        Object.keys(errors).forEach(function (name) {
            var f = form.querySelector('[data-field="' + name + '"]');
            if (!f) return;
            f.classList.add('has-error'); f.querySelector('.err').textContent = errors[name];
            if (!first) first = f;
        });
        if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    }
    function clientCheck(form) {
        var errors = {};
        $$('.field[data-required]', form).forEach(function (f) {
            if (f.classList.contains('is-hidden')) return;
            var needed = f.dataset.required === '1' || (f.dataset.requiredIfOptions && f.dataset.hasOptions === '1');
            if (!needed) return;
            var inputs = $$('input,select,textarea', f), empty = inputs.some(function (i) { return String(i.value).trim() === ''; });
            if (empty) errors[f.dataset.field] = 'This field is required.';
        });
        return errors;
    }

    function send(step, draft, btn, onOk) {
        var form = $('#form_' + step);
        $$('.field', form).forEach(function (f) { f.classList.remove('has-error'); });
        if (!draft) {
            var errs = clientCheck(form);
            if (Object.keys(errs).length) { showErrors(form, errs); toast('Please fill the highlighted fields.'); return; }
        }
        var original = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving…';
        var fd = new FormData(form);
        if (draft) fd.append('_draft', '1');
        fetch(SAVE_URL + '/' + step, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (j) {
                if (j.redirect) { window.location.href = j.redirect; return; }
                paint(j);
                if (!j.ok) { showErrors(form, j.errors); toast('Please fix the highlighted fields.'); return; }
                onOk(j);
            })
            .catch(function () { toast('Could not save. Check your connection and try again.'); })
            .then(function () { btn.disabled = false; btn.innerHTML = original; });
    }

    $$('.js-save').forEach(function (btn) {
        btn.addEventListener('click', function () {
            send(btn.dataset.step, false, btn, function (j) {
                if (btn.dataset.last) {
                    if (j.complete && GATED) { $('#finish_form').submit(); return; }
                    toast(j.complete ? 'Profile saved. You are at 100%.' : 'Saved. A few required details are still missing.');
                    return;
                }
                toast('Saved');
                goStep(btn.dataset.next);
            });
        });
    });
    $('#save_draft').addEventListener('click', function () {
        send(current, true, this, function () { toast('Draft saved. You can finish the rest later.'); });
    });

    paint(STATUS);
    goStep(current);
})();
</script>
</body>
</html>
