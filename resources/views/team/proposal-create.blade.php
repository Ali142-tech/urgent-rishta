{{--
    Direct-entry "Add Proposal" form for team members — a single flat form,
    no OTP/session wizard (unlike auth/register.blade.php's self-registration
    flow), since a team member is entering a client's proposal on their
    behalf. Submits to TeamController::store(), which stamps `added_by` and
    creates a login-less "shell" record (client confirmed: these proposals
    are never meant to log in themselves).

    2026-09: client sends a fixed WhatsApp template to prospective clients
    to fill in and send back — this form now mirrors that template's own
    section order/labels, and a "Paste client details" box at the top can
    parse that whole block and auto-fill the fields below (still fully
    editable before saving — the parser is a convenience, not a black box).
    "Your Requirements" only has the 5 fields the client's template lists
    (Age Limit/Height/City/Caste/Qualification) — this deliberately drops
    Preferred Country/Marital Status/Education from the old form, which
    used to feed AI Matches' hard filtering; new proposals added here only
    get AI-matched by age range now (client's explicit call).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', isset($proposal) ? 'Edit Proposal' : 'Add Proposal')
@section('main-content')
<style>
    .ur-team-form { max-width: 100%; }
    .ur-team-form h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 18px; }
    .ur-team-form h2 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 16px; margin: 26px 0 4px; display: flex; align-items: center; gap: 8px; }
    .ur-team-form h2 small { font-weight: 400; font-size: 12.5px; color: #6B7570; }
    .ur-team-form .row { margin-bottom: 14px; }
    .ur-team-form label { font-size: 13px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-team-form label .ur-opt { font-weight: 400; font-size: 11px; color: #9AA5A0; }
    .ur-team-form .form-control, .ur-team-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; width: 100%; }
    .ur-team-form textarea.form-control { height: auto; padding: 10px 12px; }
    .ur-team-form .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 28px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; }
    .ur-team-form .ur-submit-btn:hover { background: #B07C3D; }

    .ur-paste-box { background: #FCF6EA; border: 1px solid #E9D2A0; border-radius: 12px; padding: 18px; margin-bottom: 24px; }
    .ur-paste-box h3 { font-size: 14px; font-weight: 700; color: #123A2E; margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
    .ur-paste-box p { font-size: 12.5px; color: #6B7570; margin: 0 0 10px; }
    .ur-paste-box textarea { width: 100%; border: 1px solid #E9D2A0; border-radius: 8px; padding: 10px 12px; font-size: 12.5px; font-family: monospace; height: 130px; }
    .ur-paste-box__actions { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
    .ur-paste-box__fill-btn { background: #123A2E; color: #fff; border: none; border-radius: 999px; padding: 9px 18px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
    .ur-paste-box__fill-btn:hover { background: #0F2E24; }
    .ur-paste-box__status { font-size: 12px; color: #2E7D5B; font-weight: 600; }

    .ur-photo-slots { display: flex; gap: 16px; flex-wrap: wrap; }
    .ur-photo-slot { width: 140px; }
    .ur-photo-slot label { font-size: 12px; }
    .ur-photo-slot__drop { border: 1.5px dashed #E7E2D6; border-radius: 10px; height: 140px; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 11.5px; color: #9AA5A0; background: #F6F4EF; overflow: hidden; position: relative; }
    .ur-photo-slot__drop img { width: 100%; height: 100%; object-fit: cover; }
    .ur-photo-slot input[type=file] { margin-top: 8px; font-size: 11px; }

    .ur-step-label { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #123A2E; color: #fff; font-size: 12px; font-weight: 700; margin-right: 8px; flex-shrink: 0; }
    .ur-step-note { font-size: 11.5px; color: #9AA5A0; font-style: italic; margin: 6px 0 0; }
    .ur-review-heading { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 18px; margin: 0 0 4px; display: flex; align-items: center; }
    .ur-review-sub { font-size: 12.5px; color: #6B7570; margin: 0 0 18px 30px; }

    /* "Paste Profile" mockup match (Sep 2026) — wide, not constrained to
       the review form's 820px column. */
    .ur-pp-wrap { max-width: 100%; }
    .ur-pp-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-pp-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 26px; margin: 0 0 4px; }
    .ur-pp-head p { font-size: 13.5px; color: #6B7570; margin: 0; max-width: 560px; }
    .ur-pp-sample-btn { height: 40px; padding: 0 18px; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; }
    .ur-pp-sample-btn:hover { background: #F6F4EF; }

    .ur-pp-stepper { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .ur-pp-step { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #9AA5A0; }
    .ur-pp-step__num { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #F6F4EF; color: #9AA5A0; font-size: 12px; }
    .ur-pp-step.is-active { color: #123A2E; }
    .ur-pp-step.is-active .ur-pp-step__num { background: #123A2E; color: #fff; }
    .ur-pp-step.is-done .ur-pp-step__num { background: #C9974D; color: #fff; }
    .ur-pp-step__line { flex: 1 1 40px; height: 1px; background: #E7E2D6; max-width: 80px; }

    .ur-pp-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr); gap: 20px; margin-bottom: 20px; align-items: start; }
    @media (max-width: 991px) { .ur-pp-grid { grid-template-columns: 1fr; } }
    .ur-pp-section-head { display: flex; align-items: center; gap: 12px; margin: 26px 0 12px; padding-top: 22px; border-top: 1px dashed #E7E2D6; }
    .ur-pp-section-head__icon { flex: 0 0 36px; width: 36px; height: 36px; border-radius: 12px; background: #123A2E; display: flex; align-items: center; justify-content: center; }
    .ur-pp-section-head__icon i { color: #C9974D !important; font-size: 15px; }
    .ur-pp-section-head__title { font-size: 16px; font-weight: 800; color: #123A2E !important; line-height: 1.2; }
    .ur-pp-section-head__sub { font-size: 12.5px; color: #6B7570 !important; margin-top: 2px; }
    .ur-pp-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 22px; }
    .ur-pp-card__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 16px; }
    .ur-pp-card__head h3 { font-size: 16px; font-weight: 700; color: #123A2E; margin: 0 0 2px; }
    .ur-pp-card__head p { font-size: 12px; color: #6B7570; margin: 0; }
    .ur-pp-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 11px; border-radius: 999px; background: #EAF6EF; color: #2E7D5B; white-space: nowrap; }
    .ur-pp-badge--warn { background: #FCF0DC; color: #B07C3D; }
    .ur-pp-badge--dark { background: rgba(255,255,255,.15); color: #fff; }
    .ur-pp-badge--dark2 { background: #F0EEE7; color: #5B6560; }

    .ur-pp-template-toggle { margin-bottom: 16px; }
    .ur-pp-template-toggle summary { font-size: 12.5px; font-weight: 700; color: #123A2E; cursor: pointer; }
    .ur-pp-owner-display { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; display: flex; align-items: center; font-size: 13px; color: #1C2321; background: #F6F4EF; }

    .ur-pp-textarea { width: 100%; border: 1px solid #E7E2D6; border-radius: 10px; padding: 12px 14px; font-size: 13px; min-height: 160px; margin-bottom: 14px; }
    .ur-pp-photos-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 8px; }
    .ur-pp-photos-head label { margin: 0; }
    .ur-pp-meta-row { display: flex; align-items: center; gap: 12px; margin-top: 14px; font-size: 11.5px; color: #9AA5A0; flex-wrap: wrap; }
    .ur-pp-meta-row .ur-paste-box__fill-btn { margin-left: auto; }

    .ur-pp-protect-box { display: flex; gap: 10px; align-items: flex-start; background: #F6F4EF; border-radius: 10px; padding: 12px 14px; margin-top: 14px; font-size: 12px; color: #5B6560; }
    .ur-pp-protect-box i { color: #2E7D5B; margin-top: 2px; }

    .ur-pp-result-empty { text-align: center; padding: 30px 10px; }
    .ur-pp-result-empty__icon { width: 48px; height: 48px; border-radius: 12px; background: #F6F4EF; color: #C9974D; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 14px; }
    .ur-pp-result-empty h4 { font-size: 14.5px; color: #123A2E; margin: 0 0 6px; }
    .ur-pp-result-empty p { font-size: 12px; color: #6B7570; margin: 0; }

    /* Compact "AI structured profile" review (Sep 2026 mockup) — replaces
       revealing the full form for the ~13 fields realistically
       extractable from pasted text; the rest (name/contact/family/
       classification/requirements/etc.) stay in the fuller Review & Save
       form below, reachable the same way as before. */
    .ur-pp-review__summary { display: flex; align-items: center; gap: 14px; background: #FCF6EA; border: 1px solid #E9D2A0; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; flex-wrap: wrap; }
    .ur-pp-review__pct { width: 46px; height: 46px; border-radius: 50%; background: #C9974D; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
    .ur-pp-review__summary h4 { font-size: 13.5px; color: #123A2E; font-weight: 700; margin: 0 0 2px; }
    .ur-pp-review__summary p { font-size: 11.5px; color: #6B7570; margin: 0; }
    .ur-pp-review__badges { display: flex; gap: 6px; margin-left: auto; flex-wrap: wrap; }
    .ur-pp-review__badges .ur-pp-badge { background: #fff; }

    .ur-pp-review__grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .ur-pp-review__field label { font-size: 11.5px; }
    .ur-pp-review__field .form-control, .ur-pp-review__field select { height: 38px; font-size: 12.5px; }
    .ur-pp-review__field.is-missing .form-control, .ur-pp-review__field.is-missing select { border-color: #E6968C; background: #FCEBE8; }
    .ur-pp-review__err { display: none; font-size: 10.5px; color: #B5674A; margin: 4px 0 0; }
    .ur-pp-review__field.is-missing .ur-pp-review__err { display: block; }

    .ur-pp-original { background: linear-gradient(135deg, #123A2E, #0F2E24); border-radius: 14px; padding: 20px 22px; margin-bottom: 24px; }
    .ur-pp-original__head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .ur-pp-original__head b { color: #fff; font-size: 14px; display: block; }
    .ur-pp-original__head span:first-of-type { color: rgba(255,255,255,.55); font-size: 11.5px; }
    .ur-pp-original pre { color: rgba(255,255,255,.75); font-size: 12px; font-family: monospace; white-space: pre-wrap; margin: 0; max-height: 140px; overflow: auto; }

    .ur-pp-flow { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 22px; margin-bottom: 30px; }
    .ur-pp-flow h4 { font-size: 15px; color: #123A2E; font-weight: 700; margin: 0 0 4px; }
    .ur-pp-flow > p { font-size: 12.5px; color: #6B7570; margin: 0 0 16px; }
    .ur-pp-flow__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    @media (max-width: 900px) { .ur-pp-flow__grid { grid-template-columns: repeat(2, 1fr); } }
    .ur-pp-flow__num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #123A2E; color: #fff; font-size: 11px; font-weight: 700; margin-bottom: 8px; }
    .ur-pp-flow__grid b { display: block; font-size: 13px; color: #123A2E; margin-bottom: 3px; }
    .ur-pp-flow__grid p { font-size: 11.5px; color: #6B7570; margin: 0; }

    /* Paste Profile — polished look (matches the AI Partner Portal mockup) */
    .ur-team-form .ur-pp-head h1 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; font-size: 26px; line-height: 1.2 !important; margin: 0 0 6px; }
    .ur-team-form .ur-pp-head p { font-size: 14.5px; color: #4B5651; line-height: 1.5 !important; }
    .ur-team-form .ur-pp-card { border-radius: 22px; padding: 24px; }
    .ur-team-form .ur-pp-card__head h3 { font-family: 'Manrope', system-ui, sans-serif; font-size: 18px; font-weight: 800; line-height: 1.3 !important; margin: 0 0 4px; }
    .ur-team-form .ur-pp-card__head p { font-size: 13px; line-height: 1.4 !important; }
    .ur-team-form .ur-pp-card > label, .ur-team-form .ur-pp-photos-head label { font-size: 14px; font-weight: 800; color: #1C2321; margin: 0 0 8px; }
    .ur-team-form .ur-pp-owner-display { height: 48px; border-radius: 14px; background: #fff; font-size: 14.5px; padding: 0 16px; }
    .ur-team-form .ur-pp-textarea { min-height: 300px; border-radius: 14px; padding: 16px 18px; font-size: 14.5px; line-height: 1.7 !important; margin-bottom: 18px; resize: vertical; }
    .ur-team-form .ur-pp-photos-head { margin: 4px 0 10px; }
    .ur-team-form .ur-pp-photos-head .ur-opt { font-size: 12px; color: #6B7570; }
    .ur-pp-photo-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .ur-pp-photo-slot { position: relative; }
    .ur-pp-photo-slot__drop { height: 230px; border-radius: 14px; border: 1.5px dashed #D8D2C2; background: #F3F0E8; overflow: hidden; }
    .ur-pp-photo-slot__drop { position: relative; }
    .ur-pp-photo-slot__drop.is-empty { display: flex; align-items: center; justify-content: center; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
    .ur-pp-photo-slot__drop.is-empty:hover, .ur-pp-photo-slot__drop.is-empty:focus { border-color: #123A2E; background: #EEF3EF; outline: none; }
    .ur-pp-photo-empty { display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center; padding: 10px; }
    .ur-pp-photo-empty i { width: 46px; height: 46px; line-height: 46px; border-radius: 50%; background: #fff; color: #123A2E; font-size: 19px; box-shadow: 0 1px 3px rgba(15,46,36,.15); }
    .ur-pp-photo-empty b { font-size: 14px; color: #123A2E; line-height: 1.2 !important; }
    .ur-pp-photo-empty small { font-size: 11px; color: #9AA5A0; line-height: 1.2 !important; }
    .ur-pp-photo-slot__drop img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center 18%; }
    .ur-team-form label.ur-pp-photo-slot__btn { position: absolute; left: 6px; right: 6px; bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 8px; height: 36px; margin: 0; border-radius: 10px; background: rgba(15,63,48,.92); color: #fff; font-size: 12px; font-weight: 800; line-height: 1 !important; cursor: pointer; text-align: center; }
    .ur-team-form label.ur-pp-photo-slot__btn:hover { background: #0B3B2E; }
    .ur-team-form .ur-pp-meta-row { margin-top: 16px; font-size: 13px; color: #6B7570; }
    .ur-team-form .ur-paste-box__fill-btn { border-radius: 14px; padding: 0 22px; height: 46px; font-size: 14.5px; font-weight: 800; box-shadow: 0 8px 20px rgba(15,46,36,.18); }
    .ur-team-form .ur-pp-protect-box { border-radius: 14px; padding: 14px 16px; font-size: 13px; line-height: 1.5 !important; }
    @media (max-width: 560px) { .ur-pp-photo-grid { grid-template-columns: 1fr; } }

    .ur-pp-paste-meta { display: flex; justify-content: space-between; gap: 10px; margin: -8px 0 16px; font-size: 12px; }
    .ur-pp-paste-meta #paste_char_count { font-weight: 800; color: #6B7570; }
    .ur-pp-paste-meta #paste_error { color: #B5674A; font-weight: 700; }
    .ur-pp-textarea.is-invalid { border-color: #E6968C; background: #FCF5F3; }
    .ur-pp-saving { position: fixed; inset: 0; z-index: 100000; background: rgba(15,46,36,.55); display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
    .ur-pp-saving__box { display: flex; flex-direction: column; align-items: center; gap: 10px; background: #fff; border-radius: 18px; padding: 28px 34px; box-shadow: 0 24px 60px rgba(0,0,0,.28); text-align: center; max-width: 320px; }
    .ur-pp-saving__box b { font-size: 16px; color: #123A2E; }
    .ur-pp-saving__box small { font-size: 12px; color: #6B7570; line-height: 1.4; }
    .ur-pp-saving__spinner { width: 38px; height: 38px; border-radius: 50%; border: 4px solid #E7E2D6; border-top-color: #123A2E; animation: urPpSpin .8s linear infinite; }
    #pp_submit_btn.is-loading { opacity: .85; pointer-events: none; }
    #pp_submit_btn .ur-btn-spinner { display: inline-block; width: 15px; height: 15px; border-radius: 50%; border: 2px solid rgba(255,255,255,.45); border-top-color: #fff; animation: urPpSpin .8s linear infinite; }
    @keyframes urPpSpin { to { transform: rotate(360deg); } }
    .ur-pp-manual { border-top: 1px solid #E7E2D6; margin: 18px 0 8px; padding-top: 14px; }
    .ur-pp-manual__head { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
    .ur-pp-manual__head b { font-size: 13px; color: #1C2321; }
    .ur-pp-manual__head span { font-size: 11px; color: #9AA5A0; }
    .ur-pp-manual__tag { display: inline-block; margin-left: 4px; padding: 2px 8px; border-radius: 999px; background: #FBF0DA; color: #8A6218; font-size: 9.5px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; vertical-align: middle; }
    .ur-pp-manual__note { margin: 10px 0 0; padding: 10px 12px; border-radius: 10px; background: #FCF6EA; border: 1px solid #F0DDB0; font-size: 11.5px; line-height: 1.5; color: #6B5A33; }
</style>

<div class="ur-team-form">
  

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($proposal) ? route('team.proposals.update', $proposal->dataid) : route('team.proposals.store') }}" enctype="multipart/form-data" id="af_form" class="ur-pp-wrap">
        @csrf
        <input type="hidden" name="raw_intake_text" id="raw_intake_text_hidden" value="{{ old('raw_intake_text', $proposal->raw_intake_text ?? '') }}">
        <div class="ur-pp-head">
            <div>
                @if(isset($proposal))
                    <h1 style="margin:0 0 4px;">Edit Proposal <span style="font-size:12px; font-weight:800; background:#F3F0E8; color:#123A2E; padding:4px 11px; border-radius:999px; vertical-align:middle; margin-left:6px;">{{ $proposal->dataid }}</span></h1>
                    <p>Update the original pasted form or any field below, then save. Click <b>Extract with AI</b> after changing the pasted text to refresh the fields from it.</p>
                @else
                    <h1 style="margin:0 0 4px;">Paste Profile</h1>
                    <p>Paste a WhatsApp message, old profile, notes or any formatted text. This structures it without changing the original submission.</p>
                @endif
            </div>
          
        </div>

        @if(!isset($proposal) && !empty($sendTemplate))
        <style>
            .ur-cft { background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 20px; margin-bottom: 18px; box-shadow: 0 10px 28px rgba(15,46,36,.05); }
            .ur-cft__top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
            .ur-cft__icon { flex: 0 0 44px; width: 44px; height: 44px; border-radius: 14px; background: #123A2E; color: #C9974D !important; display: flex; align-items: center; justify-content: center; font-size: 20px; }
            .ur-cft__title { margin: 0 !important; font-size: 17px !important; font-weight: 800 !important; line-height: 1.25 !important; color: #123A2E !important; }
            .ur-cft__sub { margin: 3px 0 0 !important; font-size: 13px !important; line-height: 1.45 !important; color: #5B6560 !important; }
            .ur-cft__box { position: relative; }
            .ur-cft__text { width: 100%; height: 150px; resize: vertical; border: 1px solid #E7E2D6; border-radius: 12px; background: #FBF9F3; padding: 12px 14px; font-size: 13px; line-height: 1.55; color: #1C2321; font-family: inherit; }
            .ur-cft__fade { position: absolute; left: 1px; right: 16px; bottom: 1px; height: 34px; border-radius: 0 0 12px 12px; background: linear-gradient(to top, #FBF9F3, rgba(251,249,243,0)); pointer-events: none; }
            .ur-cft__actions { display: flex; gap: 10px; margin-top: 12px; }
            .ur-cft__btn { flex: 1 1 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 46px; border-radius: 999px; border: none; font-size: 14px; font-weight: 700; text-decoration: none !important; cursor: pointer; padding: 0 14px; line-height: 1 !important; }
            .ur-cft__btn i, .ur-cft__btn span { color: inherit !important; font-size: inherit; }
            .ur-cft__btn i { font-size: 16px; }
            .ur-cft__btn--copy { background: #123A2E; color: #fff !important; }
            .ur-cft__btn--wa { background: #25D366; color: #fff !important; }
            .ur-cft__hint { margin: 10px 0 0 !important; font-size: 12px !important; color: #6B7570 !important; text-align: center; }
            @media (max-width: 480px) {
                .ur-cft { padding: 16px; border-radius: 16px; }
                .ur-cft__actions { flex-direction: column; }
                .ur-cft__btn { flex: none; width: 100%; }
            }
        </style>
        <div class="ur-cft">
            <div class="ur-cft__top">
                <span class="ur-cft__icon"><i class="fa fa-whatsapp"></i></span>
                <div>
                    <h3 class="ur-cft__title">Client form to send</h3>
                    <p class="ur-cft__sub">Send this form to your client. When they send it back, paste it below and the fields fill in automatically.</p>
                </div>
            </div>
            <div class="ur-cft__box">
                <textarea id="client_form_template" class="ur-cft__text" readonly>{{ $sendTemplate }}</textarea>
                <span class="ur-cft__fade"></span>
            </div>
            <div class="ur-cft__actions">
                <button type="button" class="ur-cft__btn ur-cft__btn--copy" id="copy_client_form"><i class="fa fa-copy"></i> <span>Copy form</span></button>
                <a class="ur-cft__btn ur-cft__btn--wa" id="wa_client_form" href="https://wa.me/?text={{ rawurlencode($sendTemplate) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> <span>Send on WhatsApp</span></a>
            </div>
        </div>
        <script>
            (function () {
                var btn = document.getElementById('copy_client_form'), box = document.getElementById('client_form_template');
                if (!btn || !box) return;
                btn.addEventListener('click', function () {
                    var done = function () { var t = btn.querySelector('span'); t.textContent = 'Copied!'; setTimeout(function () { t.textContent = 'Copy form'; }, 2000); };
                    if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(box.value).then(done, function () { box.select(); document.execCommand('copy'); done(); }); }
                    else { box.select(); document.execCommand('copy'); done(); }
                });
            })();
        </script>
        @endif
        <div class="ur-pp-stepper">
            <div class="ur-pp-step is-active" id="pp_step_1"><span class="ur-pp-step__num">1</span> Paste data</div>
            <div class="ur-pp-step__line"></div>
            <div class="ur-pp-step" id="pp_step_2"><span class="ur-pp-step__num">2</span> Extract</div>
            <div class="ur-pp-step__line"></div>
            <div class="ur-pp-step" id="pp_step_3"><span class="ur-pp-step__num">3</span> Review &amp; save</div>
        </div>

        <div class="ur-pp-grid">
            <div class="ur-pp-card">
                <div class="ur-pp-card__head">
                    <div>
                        <h3>Client data</h3>
                        <p>Any language or format is acceptable</p>
                    </div>
                    <span class="ur-pp-badge"><i class="fa fa-lock"></i> Private</span>
                </div>

                

                @php($__owner = isset($proposal) ? (\App\TeamMember::find($proposal->added_by) ?: auth()->user()) : auth()->user())
                <label>Profile owner / teammate</label>
                <div class="ur-pp-owner-display" style="margin-bottom:14px;">
                    {{ $__owner->first_name }} {{ $__owner->last_name }}{{ $__owner->experience ? ' — '.$__owner->experience : '' }} — {{ $__owner->dataid }}
                </div>

                <label for="paste_box">Client details
                    @if(isset($proposal))
                        <span class="ur-pp-manual__tag" style="background:#E7F3EC;color:#2E7D5B;">Original form</span> <span class="ur-opt">edit it if the client sent an update</span>
                    @else
                        <span class="ur-pp-manual__tag" style="background:#FCEBE8;color:#B5674A;">Required</span> <span class="ur-opt">minimum 100 characters &mdash; include what the client is looking for in a partner</span>
                    @endif
                </label>
                <textarea id="paste_box" class="ur-pp-textarea" @if(!isset($proposal)) required minlength="100" @endif placeholder="Paste the client's filled-in reply here — PERSONAL INFORMATION&#10;Gender: ...&#10;Age: ...&#10;Marital Status: ...&#10;...">{{ old('raw_intake_text', $proposal->raw_intake_text ?? '') }}</textarea>
                <div class="ur-pp-paste-meta" id="paste_meta"><span id="paste_char_count">0 / 100 minimum</span><span id="paste_error" style="display:none;">Please enter at least 100 characters.</span></div>

                <div class="ur-pp-photos-head">
                    <label>Client photos</label>
                    <span class="ur-opt">Upload up to 2 clear images</span>
                </div>
                <div class="ur-pp-photo-grid">
                    @foreach([1, 2] as $__n)
                    @php($__ex = isset($existingPhotos) ? ($existingPhotos[$__n - 1] ?? null) : null)
                    <div class="ur-pp-photo-slot">
                        {{-- Empty "Upload image N" box (nothing looks pre-uploaded). Clicking
                             anywhere on it opens the file picker; once a file is chosen the
                             preview fills the box and the bar below switches to "Replace image N".
                             The real <input type="file"> is hidden and still named image1/image2. --}}
                        @if($__ex)
                        <div class="ur-pp-photo-slot__drop" id="photo_preview_{{ $__n }}" data-empty-n="{{ $__n }}"><img src="{{ $__ex }}" alt="Current photo {{ $__n }}"></div>
                        @else
                        <div class="ur-pp-photo-slot__drop is-empty" id="photo_preview_{{ $__n }}" data-empty-n="{{ $__n }}" role="button" tabindex="0"
                             onclick="document.getElementById('photo_input_{{ $__n }}').click()"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                            <div class="ur-pp-photo-empty">
                                <i class="fa fa-camera"></i>
                                <b>Upload image {{ $__n }}</b>
                                <small>JPG or PNG &middot; up to 5 MB</small>
                            </div>
                        </div>
                        @endif
                        <label class="ur-pp-photo-slot__btn" id="photo_label_{{ $__n }}" for="photo_input_{{ $__n }}" data-after="{{ isset($proposal) ? 'Add image '.$__n : '' }}" style="{{ $__ex ? '' : 'display:none;' }}"><i class="fa fa-camera"></i> {{ $__ex ? 'Add another image' : 'Replace image '.$__n }}</label>
                        <input type="file" id="photo_input_{{ $__n }}" name="image{{ $__n }}" form="af_form" accept="image/*" hidden onchange="urPreviewProposalPhoto(this, 'photo_preview_{{ $__n }}', 'photo_label_{{ $__n }}', 'Image {{ $__n }}')">
                    </div>
                    @endforeach
                </div>
                @if(isset($proposal))
                    <p class="ur-step-note" style="margin:8px 0 0;">New images are added to the existing ones. Delete or reorder photos on the <a href="{{ route('team.proposals.photos', $proposal->dataid) }}">Manage photos</a> page.</p>
                @endif
                <div class="ur-pp-meta-row">
                    Original text is preserved
                    <button type="button" class="ur-paste-box__fill-btn" id="paste_fill_btn"><i class="fa fa-magic"></i> Extract with AI</button>
                </div>
                <p class="ur-step-note">Currently matches known section headers/labels in the pasted text — full AI extraction is a planned upgrade, not live yet.</p>

                <div class="ur-pp-protect-box">
                    <i class="fa fa-shield"></i>
                    <div><b>Original data protection:</b> the submitted text is saved unchanged. A separate structured copy is created only for search and matching.</div>
                </div>
            </div>

            <div class="ur-pp-card">
                <div class="ur-pp-card__head">
                    <div>
                        <h3>AI structured profile</h3>
                        <p>Review every field before saving</p>
                    </div>
                    <span class="ur-pp-badge ur-pp-badge--warn">Review before saving</span>
                </div>
                <div id="pp_result_empty" class="ur-pp-result-empty">
                    <div class="ur-pp-result-empty__icon"><i class="fa fa-magic"></i></div>
                    <h4>Ready to organize</h4>
                    <p>Click "Extract with AI" to convert the pasted text into searchable profile fields.</p>
                </div>

                <div id="pp_review_compact" style="display:none;">
                    <div class="ur-pp-review__summary">
                        <div class="ur-pp-review__pct" id="pp_review_pct">0%</div>
                        <div>
                            <h4>Extraction review</h4>
                            <p id="pp_review_summary_text">Review every field below before saving.</p>
                        </div>
                        <div class="ur-pp-review__badges">
                            <span class="ur-pp-badge" id="pp_badge_found">0 found</span>
                            <span class="ur-pp-badge ur-pp-badge--warn" id="pp_badge_missing">0 missing</span>
                            <span class="ur-pp-badge ur-pp-badge--dark2">4 manual pending</span>
                        </div>
                    </div>

                    <div class="ur-pp-review__grid">
                        <div class="ur-pp-review__field" id="pp_field_gender">
                            <label>Gender</label>
                            <select name="gender" form="af_form" class="form-control" required>
                                <option value="" disabled selected>Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_marital_status">
                            <label>Marital Status</label>
                            <select name="marital_status" form="af_form" class="form-control" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($maritalstatuses as $maritalstatus)
                                    <option value="{{ $maritalstatus->dataid }}">{{ $maritalstatus->name }}</option>
                                @endforeach
                            </select>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_age">
                            <label>Age</label>
                            <input type="number" id="pp_age_input" class="form-control" min="18" max="99" required>
                            <input type="hidden" name="day" id="dob_day" form="af_form">
                            <input type="hidden" name="month" id="dob_month" form="af_form">
                            <input type="hidden" name="year" id="dob_year" form="af_form">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_height">
                            <label>Height</label>
                            <select name="height" form="af_form" class="form-control" data-height="1" required>
                                <option value="" disabled selected>Select height</option>
                                @for($i = 48; $i <= 84; $i++)
                                    <option value="{{ intdiv($i, 12) }}'{{ $i % 12 }}&quot;">{{ intdiv($i, 12) }}'{{ $i % 12 }}"</option>
                                @endfor
                            </select>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_education">
                            <label>Education</label>
                            <select name="education" form="af_form" class="form-control ur-search-select" data-other="education_other" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($education as $degree)
                                    <option value="{{ $degree->dataid }}">{{ $degree->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="education_other" form="af_form" class="form-control ur-other-input" maxlength="150" placeholder="Type the qualification" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_profession">
                            <label>Profession</label>
                            <select name="profession" form="af_form" class="form-control ur-search-select" data-other="profession_other" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($professionOptions as $job)
                                    <option value="{{ $job->dataid }}">{{ $job->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="profession_other" form="af_form" class="form-control ur-other-input" maxlength="150" placeholder="Type the profession" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_caste">
                            <label>Caste</label>
                            <select name="caste" form="af_form" class="form-control" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($caste as $cst)
                                    <option value="{{ $cst->dataid }}">{{ $cst->name }}</option>
                                @endforeach
                                <option value="__new">Not in the list — add manually…</option>
                            </select>
                            <input type="text" name="caste_other" form="af_form" id="caste_other" class="form-control" maxlength="80" placeholder="Type the caste name" style="display:none; margin-top:8px;" value="{{ old('caste_other') }}">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_sect">
                            <label>Sect</label>
                            <input type="text" name="sect" form="af_form" class="form-control" required>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_country">
                            <label>Country</label>
                            <select name="country" form="af_form" class="form-control ur-search-select" data-other="country_other" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->dataid }}">{{ $country->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="country_other" form="af_form" class="form-control ur-other-input" maxlength="100" placeholder="Type the country" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_current_city">
                            <label>Current City</label>
                            <input type="text" name="current_city" form="af_form" class="form-control" required>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_nationality">
                            <label>Nationality</label>
                            <select name="con_of_citizenship" form="af_form" class="form-control ur-search-select" data-other="nationality_other" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($nationalities as $nat)
                                    <option value="{{ $nat->dataid }}">{{ $nat->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="nationality_other" form="af_form" class="form-control ur-other-input" maxlength="100" placeholder="Type the nationality" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_city">
                            <label>Hometown</label>
                            <input type="text" name="city" form="af_form" class="form-control" required>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>

                    </div>

                    <div class="ur-pp-section-head">
                        <span class="ur-pp-section-head__icon"><i class="fa fa-heart"></i></span>
                        <div>
                            <div class="ur-pp-section-head__title">Partner Requirements</div>
                            <div class="ur-pp-section-head__sub">What the client is looking for in a partner</div>
                        </div>
                    </div>
                    <div class="ur-pp-review__grid" style="margin-top:10px;">
                        <div class="ur-pp-review__field" id="pp_field_pref_marital_statuses">
                            <label>Marital Status <small style="font-weight:400;">(select one or more)</small></label>
                            <select name="pref_marital_statuses[]" form="af_form" class="form-control ur-multi" multiple data-placeholder="Any marital status">
                                <option value="__any" {{ in_array('__any', (array) old('pref_marital_statuses', [])) ? 'selected' : '' }}>Any</option>
                                @foreach($maritalstatuses as $maritalstatus)
                                    <option value="{{ $maritalstatus->dataid }}" {{ in_array($maritalstatus->dataid, (array) old('pref_marital_statuses', [])) ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                                @endforeach
                            </select>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_age">
                            <label>Age Range</label>
                            <div style="display:flex; gap:6px; align-items:center;">
                                <input type="number" name="pref_age_min" form="af_form" class="form-control" placeholder="Min" min="18" max="99" value="{{ old('pref_age_min') }}" required>
                                <span>&ndash;</span>
                                <input type="number" name="pref_age_max" form="af_form" class="form-control" placeholder="Max" min="18" max="99" value="{{ old('pref_age_max') }}" required>
                            </div>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_height">
                            <label>Height <small style="font-weight:400;">(min &ndash; max)</small></label>
                            <div style="display:flex; gap:6px; align-items:center;">
                                <select name="pref_height_min" form="af_form" class="form-control" required>
                                    <option value="any" {{ old('pref_height_min', 'any') === 'any' ? 'selected' : '' }}>Min: Any</option>
                                    @for($i = 48; $i <= 84; $i++)
                                        <option value="{{ $i }}" {{ (string) old('pref_height_min') === (string) $i ? 'selected' : '' }}>{{ intdiv($i, 12) }}'{{ $i % 12 }}"</option>
                                    @endfor
                                </select>
                                <span>&ndash;</span>
                                <select name="pref_height_max" form="af_form" class="form-control" required>
                                    <option value="any" {{ old('pref_height_max', 'any') === 'any' ? 'selected' : '' }}>Max: Any</option>
                                    @for($i = 48; $i <= 84; $i++)
                                        <option value="{{ $i }}" {{ (string) old('pref_height_max') === (string) $i ? 'selected' : '' }}>{{ intdiv($i, 12) }}'{{ $i % 12 }}"</option>
                                    @endfor
                                </select>
                            </div>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_educations">
                            <label>Education <small style="font-weight:400;">(select one or more)</small></label>
                            <select name="pref_educations[]" form="af_form" class="form-control ur-multi" multiple data-placeholder="Any education">
                                <option value="__any" {{ in_array('__any', (array) old('pref_educations', [])) ? 'selected' : '' }}>Any</option>
                                @foreach($education as $degree)
                                    <option value="{{ $degree->dataid }}" {{ in_array($degree->dataid, (array) old('pref_educations', [])) ? 'selected' : '' }}>{{ $degree->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="pref_education_other" form="af_form" id="pref_education_other" class="form-control" maxlength="200" placeholder="Type the education (separate several with commas)" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_professions">
                            <label>Profession <small style="font-weight:400;">(select one or more)</small></label>
                            <select name="pref_professions[]" form="af_form" class="form-control ur-multi" multiple data-placeholder="Any profession">
                                <option value="__any" {{ in_array('__any', (array) old('pref_professions', [])) ? 'selected' : '' }}>Any</option>
                                @foreach($professionOptions as $job)
                                    <option value="{{ $job->dataid }}" {{ in_array($job->dataid, (array) old('pref_professions', [])) ? 'selected' : '' }}>{{ $job->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="pref_profession_other" form="af_form" id="pref_profession_other" class="form-control" maxlength="200" placeholder="Type the profession (separate several with commas)" style="display:none; margin-top:8px;">
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_castes">
                            <label>Caste <small style="font-weight:400;">(select one or more)</small></label>
                            <select name="pref_castes[]" form="af_form" class="form-control ur-multi" multiple data-placeholder="Any caste">
                                <option value="__any" {{ in_array('__any', (array) old('pref_castes', [])) ? 'selected' : '' }}>Any</option>
                                @foreach($caste as $cst)
                                    <option value="{{ $cst->dataid }}" {{ in_array($cst->dataid, array_map('strval', (array) old('pref_castes', []))) ? 'selected' : '' }}>{{ $cst->name }}</option>
                                @endforeach
                            </select>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_city">
                            <label>City</label>
                            <input type="text" name="pref_city" form="af_form" class="form-control" placeholder="e.g. Preferred city Lahore" value="{{ old('pref_city') }}" required>
                            <p class="ur-pp-review__err">Not detected — please fill.</p>
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_nationalities">
                            <label>Nationality <small style="font-weight:400;">(optional &mdash; select one or more)</small></label>
                            <select name="pref_nationalities[]" form="af_form" class="form-control ur-multi" multiple data-placeholder="Any nationality">
                                <option value="__any" {{ in_array('__any', (array) old('pref_nationalities', [])) ? 'selected' : '' }}>Any</option>
                                @foreach($nationalities as $nat)
                                    <option value="{{ $nat->dataid }}" {{ in_array($nat->dataid, (array) old('pref_nationalities', [])) ? 'selected' : '' }}>{{ $nat->name }}</option>
                                @endforeach
                                <option value="__other">Other — Add Manually</option>
                            </select>
                            <input type="text" name="pref_nationality_other" form="af_form" id="pref_nationality_other" class="form-control" maxlength="200" placeholder="Type the nationality (separate several with commas)" style="display:none; margin-top:8px;">
                        </div>
                        <div class="ur-pp-review__field" id="pp_field_pref_note" style="grid-column: 1 / -1;">
                            <label>Other requirements <small style="font-weight:400;">(optional)</small></label>
                            <textarea name="partner_requirements" form="af_form" class="form-control" rows="2" maxlength="2000" placeholder="Anything else the client wants in a partner">{{ old('partner_requirements') }}</textarea>
                        </div>
                    </div>

                    {{-- Classification fields that are never auto-filled by the
                         paste parser — the team member picks them by hand. (Profile
                         category was dropped from this form on purpose.) --}}
                    <div class="ur-pp-manual">
                        <div class="ur-pp-manual__head">
                            <b>Complete manually</b>
                            <span>AI will not select these fields.</span>
                        </div>
                        <div class="ur-pp-review__grid">
                            <div class="ur-pp-review__field" id="pp_field_family_status">
                                <label>Family status <span class="ur-pp-manual__tag">Manual</span></label>
                                <select name="family_status" form="af_form" class="form-control">
                                    <option value="">Select family status</option>
                                    @foreach(['Middle Class', 'Upper Middle Class', 'Royal Class'] as $__opt)
                                        <option value="{{ $__opt }}" {{ old('family_status') === $__opt ? 'selected' : '' }}>{{ $__opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ur-pp-review__field" id="pp_field_looking_from">
                                <label>Looking from Pak/Abroad <span class="ur-pp-manual__tag">Manual</span></label>
                                <select name="looking_from" form="af_form" class="form-control">
                                    <option value="">Select Pakistan / Abroad</option>
                                    @foreach(['Pakistan', 'Abroad'] as $__opt)
                                        <option value="{{ $__opt }}" {{ old('looking_from') === $__opt ? 'selected' : '' }}>{{ $__opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ur-pp-review__field" id="pp_field_presentation_highlight" style="grid-column: 1 / -1;">
                                <label>Presentation highlight <span class="ur-pp-manual__tag">Manual</span></label>
                                <select name="presentation_highlight" form="af_form" class="form-control">
                                    <option value="">Select presentation</option>
                                    <option value="Standard" {{ old('presentation_highlight') === 'Standard' ? 'selected' : '' }}>Standard Presentation</option>
                                    <option value="Highly Attractive" {{ old('presentation_highlight') === 'Highly Attractive' ? 'selected' : '' }}>Highly Attractive</option>
                                </select>
                            </div>
                        </div>
                        <p class="ur-pp-manual__note"><b>Fair premium tagging:</b> &ldquo;Highly Attractive&rdquo; is only ever chosen by a team member after looking at the client's photo &mdash; AI never selects it.</p>
                    </div>

                    <button type="submit" id="pp_submit_btn" class="ur-submit-btn" style="margin-top:14px; width:100%; justify-content:center;"><i class="fa fa-check"></i> <span id="pp_submit_label">{{ isset($proposal) ? 'Save changes' : 'Confirm & Save' }}</span></button>
                </div>
            </div>
        </div>

        <div class="ur-pp-original">
            <div class="ur-pp-original__head">
                <div><b>Original submission</b><span>Read-only source record</span></div>
                <span class="ur-pp-badge ur-pp-badge--dark">Unchanged</span>
            </div>
            <pre id="pp_original_text">Original data will appear here after extraction.</pre>
        </div>

        <div class="ur-pp-flow">
            <h4>How pasted data is handled</h4>
            <p>Works with the WhatsApp intake template, plain notes, or any pasted text — the same protected process either way.</p>
            <div class="ur-pp-flow__grid">
                <div><span class="ur-pp-flow__num">1</span><b>Original submission</b><p>Exact pasted text saved read-only and never overwritten.</p></div>
                <div><span class="ur-pp-flow__num">2</span><b>Structured copy</b><p>Fields extracted separately for search and matching.</p></div>
                <div><span class="ur-pp-flow__num">3</span><b>Owner + media</b><p>Assigned teammate and up to 2 photos attached.</p></div>
                <div><span class="ur-pp-flow__num">4</span><b>AI matching</b><p>Used for AI Matches and logged in the audit trail.</p></div>
            </div>
        </div>
    </form><!-- /.ur-pp-wrap -->
</div><!-- /.ur-team-form -->

<div id="pp_saving_overlay" class="ur-pp-saving" style="display:none;" role="status" aria-live="polite">
    <div class="ur-pp-saving__box">
        <span class="ur-pp-saving__spinner"></span>
        <b>Saving proposal&hellip;</b>
        <small>Uploading photos and finding matches &mdash; please don't close this page.</small>
    </div>
</div>
<script>
function urPreviewProposalPhoto(input, previewId, labelId, baseLabel) {
    var preview = document.getElementById(previewId);
    var label = labelId ? document.getElementById(labelId) : null;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.classList.remove('is-empty');
            preview.removeAttribute('onclick');
            preview.style.cursor = 'default';
            preview.innerHTML = '<img src="' + e.target.result + '" alt="">';
        };
        reader.readAsDataURL(input.files[0]);
        if (label) { label.style.display = ''; label.innerHTML = '<i class="fa fa-camera"></i> ' + (label.dataset.after || ('Replace ' + baseLabel.toLowerCase())); }
    } else {
        // File cleared -> back to the empty "Upload image N" box.
        var n = preview.getAttribute('data-empty-n') || '';
        preview.classList.add('is-empty');
        preview.style.cursor = '';
        preview.setAttribute('onclick', "document.getElementById('photo_input_" + n + "').click()");
        preview.innerHTML = '<div class="ur-pp-photo-empty"><i class="fa fa-camera"></i><b>Upload image ' + n + '</b><small>JPG or PNG &middot; up to 5 MB</small></div>';
        if (label) label.style.display = 'none';
    }
}

function urCopyTemplate(event) {
    event.preventDefault();
    var text = document.getElementById('template_box').value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
            showAlert('success', 'Template copied — paste it into WhatsApp.', 2000);
        }).catch(function () {
            showAlert('danger', 'Could not copy the template.', 2000);
        });
    }
    return false;
}

(function () {
    var form = document.getElementById('af_form');

    // ---------- helpers ----------
    function norm(s) {
        return (s || '').toString().toLowerCase().replace(/['".]/g, '').replace(/\s+/g, ' ').trim();
    }
    // NOTE: these helpers look up fields by `name` across the whole
    // document rather than scoping to the form element — every `name` on
    // this page is unique, so document.querySelector() is a safe, simpler
    // choice than tracking exactly which fields are form descendants vs.
    // form="af_form"-associated.
    // Picks an option in a searchable select, or "Other — Add Manually" + the typed text when nothing matches.
    function pickOrOther(el, text) {
        // "UK", "USA", "UAE" ... -> the long list entry (country dropdown only)
        if (el.name === 'country' && COUNTRY_ALIASES[norm(text)]) { text = COUNTRY_ALIASES[norm(text)]; }
        var target = norm(text);
        var options = Array.from(el.options).filter(function (o) { return o.value !== '' && o.value !== '__other'; });
        var match = options.find(function (o) { return norm(o.textContent) === target; })
            || options.find(function (o) { return target.length > 2 && norm(o.textContent).indexOf(target) !== -1; })
            || options.find(function (o) { return norm(o.textContent).length > 3 && target.indexOf(norm(o.textContent)) !== -1; });
        var other = el.dataset.other ? document.querySelector('[name="' + el.dataset.other + '"]') : null;
        if (match) { el.value = match.value; if (other) other.value = ''; }
        else if (other) { el.value = '__other'; other.value = String(text).trim(); }
        else return false;
        refreshSelect(el);
        return true;
    }
    // Adds a value to a multi-select: the matching option, or "Other" plus the typed text.
    function addToMulti(selectName, otherName, text) {
        var el = document.querySelector('select[name="' + selectName + '"]');
        if (!el || !text) return;
        var target = norm(text);
        var real = Array.from(el.options).filter(function (o) { return o.value !== '__other' && o.value !== ''; });
        var opt = real.find(function (o) { return norm(o.textContent) === target; })
            || real.find(function (o) { return target.length > 2 && norm(o.textContent).indexOf(target) !== -1; });
        if (opt) { opt.selected = true; }
        else {
            var other = Array.from(el.options).find(function (o) { return o.value === '__other'; });
            var box = document.querySelector('[name="' + otherName + '"]');
            if (other && box) { other.selected = true; box.value = box.value ? box.value + ', ' + text : text; }
        }
        refreshSelect(el);
    }
    // "Rajput, Arain or Jutt" -> one pick per answer. "Any" / "no preference" adds nothing.
    // short names people type for the long list entries
    var COUNTRY_ALIASES = { 'uk': 'United Kingdom (UK)', 'u.k': 'United Kingdom (UK)', 'u.k.': 'United Kingdom (UK)', 'england': 'United Kingdom (UK)', 'britain': 'United Kingdom (UK)', 'great britain': 'United Kingdom (UK)', 'usa': 'United States (USA)', 'us': 'United States (USA)', 'u.s.a': 'United States (USA)', 'america': 'United States (USA)', 'united states': 'United States (USA)', 'united kingdom': 'United Kingdom (UK)', 'uae': 'United Arab Emirates (UAE)', 'united arab emirates': 'United Arab Emirates (UAE)', 'dubai': 'United Arab Emirates (UAE)', 'ksa': 'Saudi Arabia' };
    var NATIONALITY_WORDS = { 'british': 'united kingdom', 'english': 'united kingdom', 'american': 'united states', 'emirati': 'united arab emirates', 'saudi': 'saudi arabia', 'pakistani': 'pakistan', 'indian': 'india', 'canadian': 'canada', 'australian': 'australia', 'german': 'germany', 'french': 'france', 'turkish': 'turkey', 'omani': 'oman', 'qatari': 'qatar', 'kuwaiti': 'kuwait', 'bangladeshi': 'bangladesh', 'afghan': 'afghanistan', 'chinese': 'china', 'norwegian': 'norway', 'swedish': 'sweden', 'italian': 'italy', 'spanish': 'spain', 'dutch': 'netherlands' };
    var MARITAL_SYNONYMS = { 'single': 'never married', 'unmarried': 'never married', 'divorcee': 'divorced', 'widowed': 'widow' };
    function addManyToMulti(selectName, otherName, text, synonyms) {
        String(text || '').split(/\s*(?:,|;|\/|&|\band\b|\bor\b)\s*/i).forEach(function (part) {
            part = part.trim();
            if (!part) return;
            if (/^(any|none|no preference|no restriction|open|all|not specified|n\/a)$/i.test(part)) {
                var sel = document.querySelector('select[name="' + selectName + '"]');
                var anyOpt = sel && Array.from(sel.options).find(function (o) { return o.value === '__any'; });
                if (anyOpt) { Array.from(sel.options).forEach(function (o) { o.selected = (o === anyOpt); }); refreshSelect(sel); }
                return;
            }
            if (synonyms && synonyms[part.toLowerCase()]) part = synonyms[part.toLowerCase()];
            addToMulti(selectName, otherName, part);
        });
    }
    function refreshSelect(el) {
        if (window.jQuery) { jQuery(el).trigger('change'); } else { el.dispatchEvent(new Event('change')); }
    }
    // ---- heights: free text ("5'8", "5.8", "5 ft 8") <-> the height dropdowns (value = 5'8" / inches)
    function textToInches(t) {
        var m = String(t).match(/([4-7])\s*(?:'|’|ft|feet|foot|\.|-)\s*(\d{1,2})?/i);
        if (!m) return null;
        var inch = m[2] ? parseInt(m[2], 10) : 0;
        if (inch > 11) inch = 0;
        return parseInt(m[1], 10) * 12 + inch;
    }
    function allInches(t) {
        var out = [], re = /([4-7])\s*(?:'|’|ft|feet|foot|\.|-)\s*(\d{1,2})?/gi, m;
        while ((m = re.exec(String(t))) !== null) {
            var inch = m[2] ? parseInt(m[2], 10) : 0; if (inch > 11) inch = 0;
            out.push(parseInt(m[1], 10) * 12 + inch);
        }
        return out;
    }
    function inchesLabel(n) { return Math.floor(n / 12) + "'" + (n % 12) + '"'; }
    function setHeightSelect(el, text) {
        var inches = textToInches(text);
        if (inches === null || inches < 48 || inches > 84) return false;
        el.value = inchesLabel(inches);
        refreshSelect(el);
        return true;
    }
    function setPrefHeightSelect(name, inches) {
        var el = document.querySelector('select[name="' + name + '"]');
        if (!el) return;
        el.value = inches ? String(Math.max(48, Math.min(84, inches))) : 'any';
        refreshSelect(el);
    }
    // "5'4 - 5'8", "5'6 or above", "up to 5'10", "around 5'6"
    function fillPrefHeight(text) {
        var list = allInches(text);
        if (!list.length) return;
        var min = null, max = null;
        if (list.length >= 2) { min = Math.min.apply(null, list); max = Math.max.apply(null, list); }
        else if (/above|taller|more|\+|at\s*least|minimum|min/i.test(text)) { min = list[0]; }
        else if (/below|under|less|up\s*to|maximum|max/i.test(text)) { max = list[0]; }
        else { min = list[0] - 3; max = list[0] + 3; }
        setPrefHeightSelect('pref_height_min', min);
        setPrefHeightSelect('pref_height_max', max);
    }
    function setVal(name, value) {
        var el = document.querySelector('[name="' + name + '"]');
        if (el && value !== undefined && value !== null && String(value).trim() !== '') {
            if (el.tagName === 'SELECT' && el.dataset.other) { pickOrOther(el, value); return; }
            if (el.tagName === 'SELECT' && el.dataset.height) { setHeightSelect(el, value); return; }
            el.value = value;
            if (el.tagName === 'SELECT') refreshSelect(el);
        }
    }
    function appendVal(name, value) {
        var el = document.querySelector('[name="' + name + '"]');
        if (el && value) {
            el.value = el.value ? (el.value + (el.tagName === 'TEXTAREA' ? '\n' : ' — ') + value) : value;
        }
    }
    function selectFuzzy(name, text) {
        var el = document.querySelector('select[name="' + name + '"]');
        if (!el || !text) return false;
        var target = norm(text);
        var options = Array.from(el.options);
        var match = options.find(function (o) { return norm(o.textContent) === target; })
            || options.find(function (o) { return target.indexOf(norm(o.textContent)) !== -1 && norm(o.textContent).length > 2; })
            || options.find(function (o) { return norm(o.textContent).indexOf(target) !== -1 && target.length > 2; });
        if (match) { el.value = match.value; refreshSelect(el); return true; }
        if (el.dataset.other) return pickOrOther(el, text);
        return false;
    }
    // Age <-> DOB bridge — the compact panel shows one editable "Age"
    // number field (matching the mockup), but the backend needs a real
    // day/month/year (see TeamController::store()'s validation). The 3
    // hidden inputs are computed from Age, never shown directly.
    var ageInput = document.getElementById('pp_age_input');
    function ageToDob(age) {
        age = parseInt(age, 10);
        if (!age || age < 1 || age > 110) return;
        var dob = new Date();
        dob.setFullYear(dob.getFullYear() - age);
        setVal('day', String(dob.getDate()).padStart(2, '0'));
        setVal('month', String(dob.getMonth() + 1).padStart(2, '0'));
        setVal('year', String(dob.getFullYear()));
    }
    if (ageInput) {
        ageInput.addEventListener('input', function () { ageToDob(ageInput.value); });
    }
    // Name <-> first/last name bridge — same pattern as Age <-> DOB: one
    // visible "Name" field in the compact panel, split into the 2 hidden
    // inputs the backend actually stores (see User::$fillable).
    var nameInput = document.getElementById('pp_name_input');
    var firstNameHidden = document.getElementById('pp_first_name');
    var lastNameHidden = document.getElementById('pp_last_name');
    function nameToParts(value) {
        var parts = String(value).trim().split(/\s+/).filter(Boolean);
        // Drop a leading title like "Dr." / "Mr." / "Engr." — folded into
        // first name instead of a separate field, to avoid a new column.
        var title = '';
        if (parts.length && /^(dr|mr|mrs|ms|miss|engr|prof|syed|hafiz)\.?$/i.test(parts[0])) {
            title = parts.shift() + ' ';
        }
        // Direct assignment (not setVal) — a shrinking name (e.g. editing
        // "John Smith" down to just "John") must actually clear the old
        // last name, which setVal's "never blank a field" guard won't do.
        if (firstNameHidden) firstNameHidden.value = parts.length ? title + parts[0] : '';
        if (lastNameHidden) lastNameHidden.value = parts.length > 1 ? parts.slice(1).join(' ') : '';
    }
    if (nameInput) {
        nameInput.addEventListener('input', function () { nameToParts(nameInput.value); });
    }
    function fillAgeFromYears(value) {
        var m = String(value).match(/(\d{1,3})/);
        if (!m) return;
        var age = parseInt(m[1], 10);
        if (age < 1 || age > 110) return;
        if (ageInput) ageInput.value = age;
        ageToDob(age);
    }
    function fillAgeLimit(value) {
        var nums = String(value).match(/\d{1,3}/g);
        if (!nums) return;
        if (nums.length >= 2) {
            setVal('pref_age_min', nums[0]);
            setVal('pref_age_max', nums[1]);
        } else {
            var n = parseInt(nums[0], 10);
            var isApprox = /around|approx|~|about/i.test(value);
            setVal('pref_age_min', isApprox ? Math.max(18, n - 2) : n);
            setVal('pref_age_max', isApprox ? Math.min(99, n + 2) : n);
        }
    }
    function fillName(value) {
        if (nameInput) nameInput.value = value;
        nameToParts(value);
    }

    // ---------- section-aware label maps ----------
    var SECTIONS = [
        { test: /personal\s*information/i, fields: {
            'gender': function (v) { setVal('gender', /female/i.test(v) ? 'female' : 'male'); },
            'name': fillName,
            'age': fillAgeFromYears,
            'marital status': function (v) { selectFuzzy('marital_status', MARITAL_SYNONYMS[String(v).trim().toLowerCase()] || v); },
            'height': function (v) { setVal('height', v); },
        }},
        { test: /education\s*details?/i, fields: {
            'qualification': function (v) {
                if (!selectFuzzy('education', v)) appendVal('college_university', v);
            },
            'college': function (v) { appendVal('college_university', v); },
            'college/university': function (v) { appendVal('college_university', v); },
            'university': function (v) { appendVal('college_university', v); },
        }},
        { test: /occupation\s*detail?s?/i, fields: {
            'job/business': function (v) { setVal('profession', v); },
            'job': function (v) { setVal('profession', v); },
            'business': function (v) { setVal('profession', v); },
            'income': function (v) { setVal('income', v); },
        }},
        { test: /religion\s*details?/i, fields: {
            'religion': function (v) { selectFuzzy('religion', v); },
            'sect': function (v) { setVal('sect', v); },
            'cast': function (v) { selectFuzzy('caste', v); },
            'caste': function (v) { selectFuzzy('caste', v); },
        }},
        { test: /resid[ae]nce\s*details?/i, fields: {
            'home own/on rent': function (v) { setVal('family_residence', /own/i.test(v) ? 'Own' : (/rent/i.test(v) ? 'Rent' : 'Do not know')); },
            'home own/rent': function (v) { setVal('family_residence', /own/i.test(v) ? 'Own' : (/rent/i.test(v) ? 'Rent' : 'Do not know')); },
            'size': function (v) { setVal('residence_size', v); },
            'city': function (v) { setVal('city', v); },
            'address': function (v) { setVal('address', v); },
            'adress': function (v) { setVal('address', v); },
            'nationality': function (v) { selectFuzzy('con_of_citizenship', v); },   // falls back to "Other — Add Manually" when it is not in the list
            'current city': function (v) { setVal('current_city', v); },
            'country': function (v) { selectFuzzy('country', v); },
            'country of residence': function (v) { selectFuzzy('country', v); },
            'living in': function (v) { selectFuzzy('country', v); },
        }},
        { test: /family\s*details?/i, fields: {
            "father's occupation": function (v) { setVal('father_occupation', v); },
            'fathers occupation': function (v) { setVal('father_occupation', v); },
            "mother's occupation": function (v) { setVal('mother_occupation', v); },
            'mothers occupation': function (v) { setVal('mother_occupation', v); },
            'brothers': function (v) { setVal('siblings_brothers', v); },
            'sisters': function (v) { setVal('siblings_sisters', v); },
            'married': function (v) { setVal('siblings_married_note', v); },
        }},
        { test: /(your|partner)\s*requirements?/i, fields: {
            'marital status': function (v) { addManyToMulti('pref_marital_statuses[]', null, v, MARITAL_SYNONYMS); },
            'age limit': fillAgeLimit,
            'age': fillAgeLimit,
            'height': function (v) { fillPrefHeight(v); },
            'qualification': function (v) {
                addManyToMulti('pref_educations[]', 'pref_education_other', v);
                setVal('pref_qualification_note', v);
                // "Well-educated professional, preferably a doctor" also tells us
                // the partner's profession (and religion, if it is named).
                parsePartnerRequirementsText(v);
            },
            'education': function (v) { addManyToMulti('pref_educations[]', 'pref_education_other', v); },
            'profession': function (v) { addManyToMulti('pref_professions[]', 'pref_profession_other', v); },
            'job/business': function (v) { addManyToMulti('pref_professions[]', 'pref_profession_other', v); },
            'caste': function (v) { addManyToMulti('pref_castes[]', null, v); setVal('pref_caste_note', v); },
            'city': function (v) { setVal('pref_city', v); },
            'nationality': function (v) { addManyToMulti('pref_nationalities[]', 'pref_nationality_other', v); },
            'other requirements': function (v) { setVal('partner_requirements', v); },
            'requirements': function (v) { setVal('partner_requirements', v); },
        }},
    ];

    function parseAndFill(rawText) {
        var lines = rawText.split(/\r?\n/);
        var currentSection = null;
        var filledCount = 0;

        lines.forEach(function (rawLine) {
            // Strip leading emoji/number bullets (1️⃣, 2️⃣, numbers, symbols) before matching.
            var line = rawLine.replace(/^[^a-zA-Z]*/, '').trim();
            if (!line) return;

            var matchedSection = SECTIONS.find(function (s) { return s.test.test(line); });
            if (matchedSection) {
                currentSection = matchedSection;
                return;
            }
            if (!currentSection) return;

            var colonIndex = line.indexOf(':');
            if (colonIndex === -1) return;
            var label = norm(line.substring(0, colonIndex));
            var value = line.substring(colonIndex + 1).trim();
            if (!value) return;

            var handler = currentSection.fields[label];
            if (handler) {
                handler(value);
                filledCount++;
            }
        });

        return filledCount;
    }

    // ---------- free-text fallback ----------
    // parseAndFill() above only recognizes the structured "SECTION HEADER
    // / Label: value" shape (the client's WhatsApp template). Real pasted
    // text is sometimes a plain paragraph instead (e.g. "Female, age 29,
    // single, height 5'6... MBBS doctor working in London...") with no
    // labels or colons at all — parseAndFill() would find nothing there.
    // This runs ONLY when that happens (filledCount === 0), using small,
    // honest heuristics on a fixed set of fields — never inventing a
    // value: caste/education/nationality/religion are only ever set to
    // one of this form's own real <select> options (loaded from
    // MasterData server-side), matched by scanning the raw text for that
    // option's exact name; sect/profession use a short fixed keyword list
    // (mirrors the same keyword-fallback approach already used on the
    // Search Proposals paste parser); gender/height/marital status use a
    // plain keyword/regex match. Deliberately does NOT attempt to guess a
    // first/last name from free text — too easy to mis-capture a city or
    // unrelated word as a name, and the field is optional anyway.
    var PROFESSION_KEYWORDS = ['doctor', 'engineer', 'teacher', 'accountant', 'lawyer', 'architect', 'nurse', 'pharmacist', 'dentist', 'professor', 'banker', 'businessman', 'businesswoman'];

    function scanSelectOptions(name, rawText) {
        var el = document.querySelector('select[name="' + name + '"]');
        if (!el) return false;
        var normText = norm(rawText);
        var options = Array.from(el.options)
            .filter(function (o) { return o.value && norm(o.textContent).length > 2; })
            .sort(function (a, b) { return norm(b.textContent).length - norm(a.textContent).length; });
        for (var i = 0; i < options.length; i++) {
            var label = norm(options[i].textContent).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            if (new RegExp('\\b' + label + '\\b', 'i').test(normText)) {
                el.value = options[i].value;
                return true;
            }
        }
        return false;
    }
    // Like scanSelectOptions(), but for a plain text target field instead
    // of a <select> — e.g. "Partner's Caste" is a free-text note, not a
    // masterdata-linked column, so this just returns the matched option's
    // real name (scanned from an existing <select>'s options elsewhere on
    // the page, e.g. the client's own Caste select) rather than assigning
    // anything itself.
    function findMatchingOptionLabel(selectName, rawText) {
        var el = document.querySelector('select[name="' + selectName + '"]');
        if (!el) return null;
        var normText = norm(rawText);
        var options = Array.from(el.options)
            .filter(function (o) { return o.value && norm(o.textContent).length > 2; })
            .sort(function (a, b) { return norm(b.textContent).length - norm(a.textContent).length; });
        for (var i = 0; i < options.length; i++) {
            var label = norm(options[i].textContent).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            if (new RegExp('\\b' + label + '\\b', 'i').test(normText)) {
                return options[i].textContent;
            }
        }
        return null;
    }

    function parseFreeTextFallback(rawText) {
        var filled = 0;

        if (/\bfemale\b/i.test(rawText)) { setVal('gender', 'female'); filled++; }
        else if (/\bmale\b/i.test(rawText)) { setVal('gender', 'male'); filled++; }

        var ageMatch = rawText.match(/\bage[:\s]+(\d{1,3})\b/i);
        if (ageMatch) { fillAgeFromYears(ageMatch[1]); filled++; }

        var heightMatch = rawText.match(/\b(\d)\s*'\s*(\d{1,2})\b/);
        if (heightMatch) { setVal('height', heightMatch[1] + "'" + heightMatch[2]); filled++; }

        if (/\bsingle\b/i.test(rawText)) { if (selectFuzzy('marital_status', 'Single')) filled++; }
        else if (/\bdivorced\b/i.test(rawText)) { if (selectFuzzy('marital_status', 'Divorced')) filled++; }
        else if (/\bwidow(ed)?\b/i.test(rawText)) { if (selectFuzzy('marital_status', 'Widow')) filled++; }
        else if (/\bmarried\b/i.test(rawText)) { if (selectFuzzy('marital_status', 'Married')) filled++; }

        var muslimSects = ['Sunni', 'Shia', 'Ahmadi', 'Ismaili', 'Bohra'];
        var sectFound = null;
        for (var s = 0; s < muslimSects.length; s++) {
            if (new RegExp('\\b' + muslimSects[s] + '\\b', 'i').test(rawText)) { sectFound = muslimSects[s]; break; }
        }
        if (sectFound) {
            setVal('sect', sectFound);
            filled++;
            if (selectFuzzy('religion', 'Muslim')) filled++;
        } else if (scanSelectOptions('religion', rawText)) {
            filled++;
        }

        if (scanSelectOptions('caste', rawText)) filled++;
        if (scanSelectOptions('education', rawText)) filled++;
        if (scanSelectOptions('con_of_citizenship', rawText)) filled++;

        for (var p = 0; p < PROFESSION_KEYWORDS.length; p++) {
            if (new RegExp('\\b' + PROFESSION_KEYWORDS[p] + '\\b', 'i').test(rawText)) {
                setVal('profession', PROFESSION_KEYWORDS[p].charAt(0).toUpperCase() + PROFESSION_KEYWORDS[p].slice(1));
                filled++;
                break;
            }
        }

        var cityMatch = rawText.match(/\b(?:originally from|based in|current city|from|in)\s+([A-Z][a-zA-Z]+(?:\s[A-Z][a-zA-Z]+)?)\b/);
        if (cityMatch) { setVal('city', cityMatch[1]); filled++; }

        return filled;
    }

    // "Partner Requirements" free-text -> structured fields (client
    // request, Sep 2026): grab whatever this heuristic can recognize out
    // of the textarea, leave the rest for manual entry — same "best
    // effort, never invents a value" approach as parseFreeTextFallback()
    // above, just scoped to the 6 partner-requirement fields instead of
    // the client's own attributes. Runs on blur (once they finish typing),
    // not on every keystroke.
    function parsePartnerRequirementsText(rawText) {
        if (!rawText || !rawText.trim()) return;

        var ageRangeMatch = rawText.match(/\b(\d{2})\s*(?:-|–|to)\s*(\d{2})\b/);
        if (ageRangeMatch) {
            setVal('pref_age_min', ageRangeMatch[1]);
            setVal('pref_age_max', ageRangeMatch[2]);
        }

        var heightMatch = rawText.match(/\b(\d)\s*'\s*(\d{1,2})\b/);
        if (heightMatch) { fillPrefHeight(heightMatch[0]); }

        for (var p = 0; p < PROFESSION_KEYWORDS.length; p++) {
            if (new RegExp('\\b' + PROFESSION_KEYWORDS[p] + '\\b', 'i').test(rawText)) {
                addToMulti('pref_professions[]', 'pref_profession_other', PROFESSION_KEYWORDS[p].charAt(0).toUpperCase() + PROFESSION_KEYWORDS[p].slice(1));
                break;
            }
        }

        scanSelectOptions('pref_religion', rawText);

        // Caste is a free-text note here (not a masterdata-linked select),
        // scanned against the client's own real Caste option list purely
        // as a source of legitimate caste names — never invented.
        var casteLabel = findMatchingOptionLabel('caste', rawText);
        if (casteLabel) setVal('pref_caste_note', casteLabel);

        // City / country: "Lahore based", "UK-based", "preferably in London",
        // "from Dubai", "near Karachi".
        var cityMatch = rawText.match(/\b(?:in|from|based in|near|preferably)\s+([A-Z][a-zA-Z]+(?:\s[A-Z][a-zA-Z]+)?)\b/)
            || rawText.match(/\b([A-Z][a-zA-Z]+)[-\s]based\b/);
        if (cityMatch) setVal('pref_city', cityMatch[1]);

        // An explicit "City: Lahore" / "Location: Lahore" line wins over the guess above.
        var cityLabel = rawText.match(/^\s*(?:preferred\s+)?(?:city|location)\s*[:\-]\s*([^\n,\/;]+)/im);
        if (cityLabel && cityLabel[1].trim()) {
            setVal('pref_city', cityLabel[1].trim().replace(/\b\w/g, function (c) { return c.toUpperCase(); }));
        }

        // "5 ft 10", "5.10", "5'10" -> 5'10
        if (!heightMatch) {
            var ftMatch = rawText.match(/\b(\d)\s*(?:ft|feet|foot)\s*(\d{1,2})?\b/i);
            if (ftMatch) fillPrefHeight(ftMatch[0]);
        }
    }

    // Partner requirements come from the SAME pasted text as everything else
    // (there is no separate box): pick out the "looking for / seeking /
    // partner requirements" passage and detect age range, height, city,
    // profession, religion from it. Labelled lines such as "Partner Age: 30-36"
    // are already handled by parseAndFill(); this covers free-flowing text.
    var PARTNER_PASSAGE_RE = /\b(?:looking for|seeking|searching for|requirements?|partner (?:preferences?|requirements?)|expect(?:ing|s)?)\b[:\s-]*([\s\S]{8,600}?)(?:\n\s*\n|\n[A-Z ]{6,}\n|$)/i;

    function detectPartnerRequirementsFromPaste(text) {
        var m = text.match(PARTNER_PASSAGE_RE);
        if (!m) return;
        var passage = m[1].replace(/[ \t]+/g, ' ').replace(/\n\s*\n/g, '\n').trim();
        if (!passage) return;
        parsePartnerRequirementsText(passage);

        // Partner qualification: when the passage talks about education,
        // keep the client's own words (trimmed) rather than leaving it blank.
        var q = document.querySelector('[name="pref_qualification_note"]');
        if (q && !String(q.value || '').trim() && /educat|qualif|graduat|degree|master|mba|mbbs|phd|bachelor/i.test(passage)) {
            // A labelled "Qualification: Well educated" line wins; otherwise keep
            // the passage in the client's own words.
            var qLine = passage.match(/^\s*(?:partner\s+)?(?:qualification|education)\s*[:\-]\s*(.+)$/im);
            q.value = (qLine ? qLine[1].trim() : passage.replace(/\s*\n\s*/g, ', ')).slice(0, 250);
        }
    }

    // The client's OWN fields must not be guessed from the partner passage
    // (e.g. "…preferably a doctor" must not make the client a doctor).
    function withoutPartnerPassage(text) {
        var m = text.match(PARTNER_PASSAGE_RE);
        return m ? text.replace(m[0], ' ') : text;
    }
    (function () {
        var sel = document.querySelector('select[name="caste"]'), other = document.getElementById('caste_other');
        if (!sel || !other) return;
        function sync() { var on = sel.value === '__new'; other.style.display = on ? '' : 'none'; other.required = on; if (on) other.focus(); }
        sel.addEventListener('change', sync); sync();
    })();
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.ur-multi').each(function () { jQuery(this).select2({ width: '100%', placeholder: this.getAttribute('data-placeholder'), closeOnSelect: false }).on('change', function () { if (typeof refreshExtractionSummary === 'function') refreshExtractionSummary(); }); });
    }
    if (window.jQuery && jQuery.fn.select2) {
        // every other single-choice dropdown in the form gets the search box too
        jQuery('#af_form select:not([multiple]), select[form="af_form"]:not([multiple])').not('.ur-search-select').each(function () {
            var el = this;
            jQuery(el).select2({ width: '100%', minimumResultsForSearch: 0, placeholder: 'Select' })
                .on('change', function () { if (typeof refreshExtractionSummary === 'function') refreshExtractionSummary(); });
        });
        jQuery('.ur-search-select').each(function () {
            var sel = this;
            jQuery(sel).select2({ width: '100%', minimumResultsForSearch: 0, placeholder: 'Select' })
                .on('change', function () {
                    var other = sel.dataset.other ? document.querySelector('[name="' + sel.dataset.other + '"]') : null;
                    if (other) { var on = sel.value === '__other'; other.style.display = on ? '' : 'none'; other.required = on && !EDIT_MODE; }
                    if (typeof refreshExtractionSummary === 'function') refreshExtractionSummary();
                });
        });
        jQuery('select[name="pref_nationalities[]"]').on('change', function () {
            var box = document.getElementById('pref_nationality_other');
            var on = (jQuery(this).val() || []).indexOf('__other') !== -1;
            if (box) { box.style.display = on ? '' : 'none'; box.required = on; }
        });
        jQuery('select[name="pref_professions[]"]').on('change', function () {
            var box = document.getElementById('pref_profession_other');
            var on = (jQuery(this).val() || []).indexOf('__other') !== -1;
            if (box) { box.style.display = on ? '' : 'none'; box.required = on; }
        });
        jQuery('select[name="pref_educations[]"]').on('change', function () {
            var box = document.getElementById('pref_education_other');
            var on = (jQuery(this).val() || []).indexOf('__other') !== -1;
            if (box) { box.style.display = on ? '' : 'none'; box.required = on; }
        });
    }
    if (window.jQuery) {
        jQuery('.ur-multi').on('select2:select', function (e) {
            var $el = jQuery(this), picked = e.params.data.id, vals = $el.val() || [];
            if (picked === '__any') { $el.val(['__any']).trigger('change'); }
            else if (vals.indexOf('__any') !== -1) { $el.val(vals.filter(function (v) { return v !== '__any'; })).trigger('change'); }
        });
    }
    var EDIT_MODE = @json(isset($proposal));
    var pasteBox = document.getElementById('paste_box');
    var charCount = document.getElementById('paste_char_count');
    var rawTextHidden = document.getElementById('raw_intake_text_hidden');
    var step1 = document.getElementById('pp_step_1');
    var step2 = document.getElementById('pp_step_2');
    var step3 = document.getElementById('pp_step_3');
    var resultEmpty = document.getElementById('pp_result_empty');
    var reviewCompact = document.getElementById('pp_review_compact');
    var originalText = document.getElementById('pp_original_text');

    // The 13 fields shown in the compact "AI structured profile" panel —
    // one entry per field wrapper id, and how to read its current value.
    // "Name" stays the one optional field in this panel — client
    // confirmed earlier this session that name/address are optional for
    // proposals; the other 13 are now ALL required (client request, Sep
    // 2026 — "All fields required", matching the mockup's own badge) and
    // block Confirm & Save until filled (see the submit handler below).
    var CORE_FIELDS = [
        // "Partner Requirements" structured fields (client request, Sep
        // 2026 — required same as everything else in this panel). The two
        // age-range inputs share one wrapper for highlighting purposes;
        // marking/clearing "is-missing" on the same element twice is harmless.
        { wrapper: 'pp_field_gender', getEl: function () { return document.querySelector('[name="gender"]'); }, required: true },
        { wrapper: 'pp_field_marital_status', getEl: function () { return document.querySelector('[name="marital_status"]'); }, required: true },
        { wrapper: 'pp_field_age', getEl: function () { return ageInput; }, required: true },
        { wrapper: 'pp_field_height', getEl: function () { return document.querySelector('[name="height"]'); }, required: true },
        { wrapper: 'pp_field_education', getEl: function () { return document.querySelector('[name="education"]'); }, required: true },
        { wrapper: 'pp_field_profession', getEl: function () { return document.querySelector('[name="profession"]'); }, required: true },
        { wrapper: 'pp_field_caste', getEl: function () { return document.querySelector('[name="caste"]'); }, required: true },
        { wrapper: 'pp_field_sect', getEl: function () { return document.querySelector('[name="sect"]'); }, required: true },
        { wrapper: 'pp_field_country', getEl: function () { return document.querySelector('[name="country"]'); }, required: true },
        { wrapper: 'pp_field_current_city', getEl: function () { return document.querySelector('[name="current_city"]'); }, required: true },
        { wrapper: 'pp_field_nationality', getEl: function () { return document.querySelector('[name="con_of_citizenship"]'); }, required: true },
        { wrapper: 'pp_field_city', getEl: function () { return document.querySelector('[name="city"]'); }, required: true },
        { wrapper: 'pp_field_pref_professions', getEl: function () { return document.querySelector('[name="pref_professions[]"]'); }, required: true },
        { wrapper: 'pp_field_pref_city', getEl: function () { return document.querySelector('[name="pref_city"]'); }, required: true },
        { wrapper: 'pp_field_pref_age', getEl: function () { return document.querySelector('[name="pref_age_min"]'); }, required: true },
        { wrapper: 'pp_field_pref_age', getEl: function () { return document.querySelector('[name="pref_age_max"]'); }, required: true },
        { wrapper: 'pp_field_pref_castes', getEl: function () { return document.querySelector('[name="pref_castes[]"]'); }, required: true },
        { wrapper: 'pp_field_pref_educations', getEl: function () { return document.querySelector('[name="pref_educations[]"]'); }, required: true },
        { wrapper: 'pp_field_pref_marital_statuses', getEl: function () { return document.querySelector('[name="pref_marital_statuses[]"]'); }, required: true },
        { wrapper: 'pp_field_pref_height', getEl: function () { return document.querySelector('[name="pref_height_min"]'); }, required: true },
    ];

    function refreshExtractionSummary() {
        var found = 0, missing = 0;
        CORE_FIELDS.forEach(function (f) {
            var el = f.getEl();
            var hasValue = el && String(el.value || '').trim() !== '';
            var wrapper = document.getElementById(f.wrapper);
            if (hasValue) {
                found++;
                if (wrapper) wrapper.classList.remove('is-missing');
            } else {
                if (!f.optional) missing++;
                if (wrapper) wrapper.classList.add('is-missing');
            }
        });

        var pctEl = document.getElementById('pp_review_pct');
        var textEl = document.getElementById('pp_review_summary_text');
        var foundBadge = document.getElementById('pp_badge_found');
        var missingBadge = document.getElementById('pp_badge_missing');
        var total = found + missing;
        var pct = total > 0 ? Math.round((found / total) * 100) : 0;
        if (pctEl) pctEl.textContent = pct + '%';
        if (foundBadge) foundBadge.textContent = found + ' found';
        if (missingBadge) {
            missingBadge.textContent = missing + ' missing';
            missingBadge.style.display = missing > 0 ? '' : 'none';
        }
        if (textEl) {
            textEl.textContent = missing > 0
                ? ('Required fields are missing: review the highlighted fields below.')
                : 'All core fields look complete — review before saving.';
        }
    }
    // Live-clear the "missing" flag as soon as a field gets filled in
    // manually, rather than only re-checking on the next Extract click.
    CORE_FIELDS.forEach(function (f) {
        var el = f.getEl();
        if (el) el.addEventListener('input', refreshExtractionSummary);
        if (el && el.tagName === 'SELECT') el.addEventListener('change', refreshExtractionSummary);
    });

    if (pasteBox && charCount) {
        pasteBox.addEventListener('input', function () {
            var n = pasteBox.value.trim().length;
            charCount.textContent = n + ' / 100 minimum';
            charCount.style.color = n >= 100 ? '#2E7D5B' : '';
            if (n >= 100) { pasteBox.classList.remove('is-invalid'); var pe = document.getElementById('paste_error'); if (pe) pe.style.display = 'none'; }
            if (pasteBox.value.trim() && step1 && step2) {
                step1.classList.add('is-done');
                step2.classList.add('is-active');
            }
        });
    }

    var sampleBtn = document.getElementById('ur_load_sample');
    if (sampleBtn && pasteBox) {
        sampleBtn.addEventListener('click', function () {
            pasteBox.value = [
                '1. PERSONAL INFORMATION:',
                'Gender: Female',
                'Age: 29',
                'Marital Status: Single',
                'Height: 5\'6',
                '',
                '2. EDUCATION DETAILS:',
                'Qualification: MBBS',
                'College/University: Government Medical College, Lahore',
                '',
                '3. OCCUPATION DETAIL:',
                'Job/Business: Doctor',
                'Income: Good',
                '',
                '4. RELIGION DETAILS:',
                'Religion: Islam',
                'Sect: Sunni',
                'Caste: Rajput',
                '',
                '5. RESIDENCE DETAILS:',
                'Home Own/On Rent: Own',
                'Size: 10 Marla',
                'City: Lahore',
                'Nationality: Pakistani',
                'Current City: London',
                '',
                '6. FAMILY DETAILS:',
                "Father's Occupation: Retired Government Officer",
                "Mother's Occupation: House Wife",
                'Brothers: 1, Business Owner',
                'Sisters: 1, Married',
                'Married: N/A',
                '',
                '7. YOUR REQUIREMENTS:',
                'Age Limit: 30-36',
                'Height: 5\'10 or above',
                'City: London or UK-based',
                'Caste: Any',
                'Qualification: Well-educated professional, preferably a doctor',
            ].join('\n');
            pasteBox.dispatchEvent(new Event('input'));
        });
    }

    function revealReviewCompact() {
        if (resultEmpty) resultEmpty.style.display = 'none';
        if (reviewCompact) reviewCompact.style.display = '';
        refreshExtractionSummary();
        if (step3) step3.classList.add('is-active');
        if (reviewCompact) reviewCompact.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('paste_fill_btn').addEventListener('click', function () {
        var text = pasteBox.value;
        if (text.trim().length < 100) {
            pasteBox.classList.add('is-invalid');
            var pe2 = document.getElementById('paste_error'); if (pe2) pe2.style.display = '';
            pasteBox.focus();
            if (typeof showAlert === 'function') showAlert('danger', 'Paste at least 100 characters of client details first (including what they are looking for).', 4500);
            return;
        }
        // Free-text detection of the partner fields first, so any explicit
        // "Label: value" lines from the template (parseAndFill below) win.
        detectPartnerRequirementsFromPaste(text);
        var count = parseAndFill(text);
        if (count === 0) {
            // No recognized "SECTION / Label: value" structure at all —
            // likely a free-flowing paragraph instead. Fall back to the
            // heuristic extractor rather than reporting a flat zero.
            count = parseFreeTextFallback(withoutPartnerPassage(text));
        }

        if (rawTextHidden) rawTextHidden.value = text;
        if (originalText) originalText.textContent = text;

        if (step1) step1.classList.add('is-done');
        if (step2) step2.classList.remove('is-active');
        if (step2) step2.classList.add('is-done');
        revealReviewCompact();
    });

    // Safety net — covers a submit with the owner/paste values never
    // having changed (defaults still apply) or changed without firing
    // the listeners above for any reason. Also where "All fields
    // required" is actually enforced: the `required` HTML attribute on
    // every core field already blocks a native submit on its own, but
    // this reinforces it with the same red-highlight UI used everywhere
    // else on this page rather than relying only on the browser's default
    // validation bubble (which can be easy to miss if it points at a
    // field currently scrolled out of view).
    window.addEventListener('pageshow', function () {
        var ov = document.getElementById('pp_saving_overlay');
        if (ov) ov.style.display = 'none';
        if (form) form.dataset.submitting = '';
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            // Already on its way to the server (Enter pressed twice, etc.).
            if (form.dataset.submitting === '1') { e.preventDefault(); return; }
            if (rawTextHidden && pasteBox) rawTextHidden.value = pasteBox.value;

            if (!EDIT_MODE && pasteBox && pasteBox.value.trim().length < 100) {
                e.preventDefault();
                pasteBox.classList.add('is-invalid');
                var pe = document.getElementById('paste_error'); if (pe) pe.style.display = '';
                pasteBox.focus();
                pasteBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (typeof showAlert === 'function') showAlert('danger', 'The client details box is required (at least 100 characters).', 4500);
                return;
            }

            // Collect every required field that is still empty, by label, so the
            // message can say exactly what is missing (and where to find it).
            var missingWrappers = [];
            CORE_FIELDS.forEach(function (f) {
                if (f.optional) return;
                var el = f.getEl();
                if (!el || String(el.value || '').trim() === '') {
                    var w = document.getElementById(f.wrapper);
                    if (w && missingWrappers.indexOf(w) === -1) missingWrappers.push(w);
                }
            });
            if (missingWrappers.length && !EDIT_MODE) {
                e.preventDefault();
                if (resultEmpty) resultEmpty.style.display = 'none';
                if (reviewCompact) reviewCompact.style.display = '';
                refreshExtractionSummary();

                var names = missingWrappers.map(function (w) {
                    var lab = w.querySelector('label');
                    return lab ? lab.firstChild.textContent.trim() || lab.textContent.trim() : w.id;
                });
                // "Height"/"Location" appear for both the client and the partner —
                // say which section when the plain label is ambiguous.
                names = names.map(function (n, i) {
                    return /^pp_field_pref_/.test(missingWrappers[i].id) ? "Partner's " + n.replace(/^Partner's\s*/i, '') : n;
                });

                missingWrappers[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                var firstInput = missingWrappers[0].querySelector('input, select, textarea');
                if (firstInput) setTimeout(function () { firstInput.focus(); }, 350);

                if (typeof showAlert === 'function') {
                    showAlert('danger', 'Please fill in: ' + names.join(', ') + '.', 7000);
                }
            } else {
                // Everything valid -> the form is really going to the server.
                var btn = document.getElementById('pp_submit_btn');
                var lbl = document.getElementById('pp_submit_label');
                if (btn) {
                    btn.classList.add('is-loading');
                    var ico = btn.querySelector('i');
                    if (ico) ico.outerHTML = '<span class="ur-btn-spinner"></span>';
                }
                if (lbl) lbl.textContent = 'Saving...';
                var ov = document.getElementById('pp_saving_overlay');
                if (ov) ov.style.display = 'flex';
                form.dataset.submitting = '1';
            }        });
    }

    // ---------- Edit mode: load the saved proposal into the same fields ----------
    if (EDIT_MODE) {
        var EDIT_VALUES = @json($editValues ?? []);
        Object.keys(EDIT_VALUES).forEach(function (name) {
            var el = document.querySelector('[name="' + name + '"]');
            var v = EDIT_VALUES[name];
            if (el && v !== null && v !== '') {
                if (el.tagName === 'SELECT' && el.dataset.other) { pickOrOther(el, v); }
                else if (el.tagName === 'SELECT' && el.dataset.height) { setHeightSelect(el, v); }
                else { el.value = v; if (el.tagName === 'SELECT') refreshSelect(el); }
            }
        });
        var EDIT_EXTRA = @json($editExtra ?? []);
        Object.keys(EDIT_EXTRA).forEach(function (name) { var el = document.querySelector('[name="' + name + '"]'); if (el && EDIT_EXTRA[name]) el.value = EDIT_EXTRA[name]; });
        var EDIT_MULTI = @json($editMulti ?? []);
        Object.keys(EDIT_MULTI).forEach(function (name) {
            var el = document.querySelector('[name="' + name + '"]');
            if (!el) return;
            if (window.jQuery) { jQuery(el).val(EDIT_MULTI[name]).trigger('change'); }
            else { Array.prototype.forEach.call(el.options, function (o) { o.selected = EDIT_MULTI[name].indexOf(o.value) !== -1; }); }
        });
        if (ageInput) ageInput.value = @json($editAge ?? null) || '';

        // An older proposal may lack some fields; only gender and date of birth
        // stay mandatory while editing (the server enforces the same).
        document.querySelectorAll('#af_form [required], [form="af_form"][required]').forEach(function (el) {
            var n = el.getAttribute('name');
            if (n === 'gender' || el.id === 'pp_age_input') return;
            el.removeAttribute('required');
            el.removeAttribute('minlength');
        });

        // Go straight to the review panel: pasted form + fields, ready to edit.
        if (step1) { step1.classList.remove('is-active'); step1.classList.add('is-done'); }
        if (step2) step2.classList.add('is-done');
        if (step3) step3.classList.add('is-active');
        if (resultEmpty) resultEmpty.style.display = 'none';
        if (reviewCompact) reviewCompact.style.display = '';
        if (originalText && pasteBox) originalText.textContent = pasteBox.value;
        if (pasteBox) pasteBox.dispatchEvent(new Event('input'));
        refreshExtractionSummary();
    }
})();
</script>
@endsection
