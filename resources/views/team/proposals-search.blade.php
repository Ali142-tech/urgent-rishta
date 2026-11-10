{{--
    "Advanced Search" — the global team-added pool (everyone's), with filters
    cloned from HomeController::search()'s field set (just `added_by IS NOT
    NULL` instead of `IS NULL`). See TeamController::searchProposals().
    Redesigned (Sep 2026) to match the client's "AI Partner Portal" Advanced
    Search mockup: quick keyword/profession/country tabs backed by a
    collapsible "More filters" panel for the rest of the existing filter
    set, a compact result-card list (team/partials/search-result-card.blade.php),
    and a results header showing count + sort order. This is what the old
    single "Team Dashboard" page used to show unconditionally — now split
    out as its own page as part of the Dashboard redesign.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Advanced Search')
@push('styles')
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
<link rel="stylesheet" href="/css/ur-multiselect.css?v={{ filemtime(public_path('css/ur-multiselect.css')) }}">
@endpush
@section('main-content')
<style>
    .ur-team-page__head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .ur-team-page__head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-team-page__head p { color: #6B7570; font-size: 13.5px; margin: 0; max-width: 640px; }
    .ur-team-page .pagination { gap: 6px; flex-wrap: wrap; margin-top: 26px; }
    .ur-team-page .pagination .page-link, .ur-team-page .pagination .page-item > span { border-radius: 999px !important; min-width: 38px; text-align: center; }
    .ur-team-page .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }

    /* ---------- Search panel ---------- */
    .ur-adv-search { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 18px 20px; margin-bottom: 22px; }
    .ur-adv-search__tabs { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .ur-adv-search__tab { background: #F6F4EF; border: 1px solid #E7E2D6; color: #1C2321; border-radius: 999px; padding: 9px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
    .ur-adv-search__tab.is-active { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-adv-search__more-btn { margin-left: auto; background: #fff; border: 1px solid #E7E2D6; color: #1C2321; border-radius: 999px; padding: 9px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .ur-adv-search__more-btn.is-active { border-color: #C9974D; color: #C9974D; }
    /* searchable dropdowns (select2) look like the keyword input */
    .ur-adv-search__field .select2-container { width: 100% !important; }
    .ur-adv-search__field .select2-container--default .select2-selection--single { height: 42px; border: 1px solid #E7E2D6; border-radius: 8px; background: #fff; display: flex; align-items: center; outline: none; }
    .ur-adv-search__field .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 40px !important; padding: 0 36px 0 13px !important; font-size: 13.5px; color: #123A2E; width: 100%; }
    .ur-adv-search__field .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #8A938E; }
    .ur-adv-search__field .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; right: 8px; }
    .ur-adv-search__field .select2-container--default.select2-container--focus .select2-selection--single, .ur-adv-search__field .select2-container--default.select2-container--open .select2-selection--single { border-color: #1F6B52; }
    .select2-dropdown { border: 1px solid #E7E2D6 !important; border-radius: 8px !important; box-shadow: 0 8px 24px rgba(15,46,36,.12); overflow: hidden; }
    .select2-container--default .select2-search--dropdown .select2-search__field { border: 1px solid #E7E2D6; border-radius: 6px; padding: 7px 10px; font-size: 13.5px; outline: none; }
    /* option padding / look comes from public/css/ur-multiselect.css (it leaves room for the tick box) */
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #123A2E; color: #fff; }
    .ur-adv-search__row { display: flex; flex-wrap: wrap; gap: 12px; align-items: stretch; }
    .ur-adv-search__field { flex: 1; }
    .ur-adv-search__field .form-control, .ur-adv-search__field select { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; width: 100%; font-size: 13.5px; }
    .ur-adv-search__submit { flex-shrink: 0; height: 42px; border-radius: 999px; background: #123A2E; color: #fff; border: none; padding: 0 22px; font-size: 13px; font-weight: 700; white-space: nowrap; }
    .ur-adv-search__submit:hover { background: #0F2E24; }
    .ur-adv-search__clear { flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; height: 42px; border-radius: 999px; background: #fff; color: #6B7570; border: 1px solid #E7E2D6; padding: 0 18px; font-size: 13px; font-weight: 700; white-space: nowrap; text-decoration: none; }
    .ur-adv-search__clear:hover { background: #F6F4EF; color: #1C2321; border-color: #C9974D; text-decoration: none; }
    /* multi-select dropdown look: public/css/ur-multiselect.css (shared by every search/filter box) */
    .ur-adv-search__hint { font-size: 12px; color: #9AA5A0; margin: 12px 0 0; }
    .ur-adv-search__network { font-size: 12px; color: #2E7D5B; font-weight: 700; text-align: right; margin: -4px 0 12px; }

    .ur-adv-search__filters { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin: 18px 0 0; padding-top: 18px; border-top: 1px dashed #E7E2D6; }
    .ur-adv-search__filters label { font-size: 12px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-adv-search__filters .form-control, .ur-adv-search__filters select { border: 1px solid #E7E2D6; border-radius: 8px; height: 38px; padding: 0 10px; width: 100%; font-size: 13px; }

    /* ---------- Results header ---------- */
    .ur-adv-search__results-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; font-size: 13px; }
    .ur-adv-search__results-head strong { color: #1C2321; }
    .ur-adv-search__sort { color: #9AA5A0; }
    .ur-adv-search__empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 30px; text-align: center; color: #6B7570; }

    /* ---------- Result cards ---------- */
    .ur-search-results { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 18px; align-items: stretch; }
    /* Dark profile card (client design, Nov 2026) — same look as the shared member card (public/css/ur-member-card.css) */
    .ur-search-card { display: flex; flex-direction: column; background: #123A2E; border: 1px solid #0F2E24; border-radius: 26px; overflow: hidden; color: #fff; box-shadow: 0 10px 26px rgba(0,0,0,.22); transition: box-shadow .2s ease, transform .2s ease; }
    .ur-search-card:hover { box-shadow: 0 16px 34px rgba(0,0,0,.32); transform: translateY(-2px); }
    .ur-search-card__head { display: flex; gap: 16px; align-items: flex-start; padding: 18px 18px 16px; background: #F6F4EF; color: #0F2E24; }
    .ur-search-card__photo { position: relative; flex: 0 0 132px; width: 132px; height: 176px; border-radius: 18px; overflow: hidden; background: #DDE8E2; display: block; }
    /* the whole photo is shown (never cropped); the empty sides are filled with a soft blurred copy of the same photo */
    .ur-search-card__photo::before { content: ''; position: absolute; inset: -16px; background: var(--ph) center / cover no-repeat; filter: blur(14px) saturate(1.1) brightness(.92); transform: scale(1.06); }
    .ur-search-card__photo img { position: relative; z-index: 1; width: 100%; height: 100%; object-fit: contain; object-position: center; display: block; }
    .ur-search-card__headinfo { flex: 1; min-width: 0; }
    .ur-search-card__top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px 8px; margin-bottom: 4px; }
    .ur-search-card__id { font-weight: 700; color: #123A2E; font-size: 26px; line-height: 1.1; letter-spacing: -.2px; }
    .ur-search-card__verified { display: inline-flex; align-items: center; gap: 5px; background: #C9F0D8; color: #0F3D2E; font-size: 12.5px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }
    .ur-search-card__meta { font-size: 15px; line-height: 1.45; color: #1F3A30; }
    .ur-search-card__meta--sub { font-size: 14px; color: #4F6A5F; }
    .ur-search-card__flag { width: 20px !important; height: 14px !important; margin: 0 3px 2px 0 !important; vertical-align: middle; object-fit: cover; border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,.12); display: inline-block !important; }
    .ur-search-card__badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .ur-search-card__badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: .3px; text-transform: uppercase; }
    .ur-search-card__badge--new { background: #C9974D; color: #0F2E24; }
    .ur-search-card__badge--updated { background: #123A2E; color: #fff; }
    .ur-search-card__badge--premium { background: #FBF0DA; color: #8A6218; }
    .ur-search-card__badge--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
    .ur-search-card__badge--abroad { background: #E4EEF7; color: #2C5F8A; }
    .ur-search-card__badge--highlight { background: #FBE4E9; color: #A23B57; }
    .ur-search-card__badge--private { background: #3B2A4F; color: #fff; }
    .ur-search-card__owner { font-size: 12.5px; font-weight: 700; color: #4F6A5F; margin-top: 8px; }
    .ur-search-card__owner i { color: #0F8A55; margin-right: 4px; }
    .ur-search-card__panels { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; padding: 16px 16px 4px; }
    .ur-search-card__panel { background: #0F2E24; border: 1px solid #24594A; border-radius: 18px; padding: 14px 14px 10px; min-width: 0; }
    .ur-search-card__panel--alt { background: #184A3A; border-color: #184A3A; }
    .ur-search-card__panel h4 { margin: 0 0 10px; font-size: 16.5px; font-weight: 700; color: #E2B66E; }
    .ur-search-card__panel ul { list-style: none; margin: 0; padding: 0; }
    .ur-search-card__panel li { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 9px; font-size: 14.5px; line-height: 1.3; color: #F6F4EF; }
    .ur-search-card__panel li i { flex: 0 0 18px; width: 18px; margin-top: 2px; text-align: center; font-size: 16px; color: #D9B26B; }
    .ur-search-card__panel li span { min-width: 0; overflow-wrap: anywhere; color: inherit; }
    .ur-search-card__panel li.is-empty span { color: #9DB5AA; font-style: italic; }
    .ur-search-card__actions { display: flex; flex-wrap: wrap; gap: 12px; padding: 14px 16px 18px; }
    .ur-search-card__btn { flex: 1 1 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 46px; border: 1.5px solid rgba(226,182,110,.55); color: #F6F4EF !important; background: transparent; border-radius: 999px; padding: 9px 14px; font-size: 14.5px; font-weight: 700; cursor: pointer; text-decoration: none; white-space: nowrap; }
    .ur-search-card__btn i, .ur-search-card__btn span { color: inherit !important; }
    .ur-search-card__btn:hover { background: rgba(255,255,255,.07); border-color: #E2B66E; color: #F6F4EF !important; }
    .ur-search-card__btn--dark { background: #C9974D; border-color: #C9974D; color: #0F2E24 !important; }
    .ur-search-card__btn--dark:hover { background: #B07C3D; border-color: #B07C3D; color: #0F2E24 !important; }
    .ur-search-card__btn--whatsapp { background: #C9974D; border-color: #C9974D; color: #0F2E24 !important; }
    .ur-search-card__btn--whatsapp:hover { background: #B07C3D; border-color: #B07C3D; color: #0F2E24 !important; }

    /* Classic refinement (Nov 2026): serif ID + headings, slim gold hairlines, calmer buttons (mirrors the shared member card) */
    .ur-search-card { border-radius: 20px; border: 1px solid rgba(201,151,77,.55); box-shadow: 0 8px 22px rgba(15,46,36,.18); }
    .ur-search-card:hover { box-shadow: 0 14px 30px rgba(15,46,36,.26); }
    .ur-search-card__head { padding: 16px 16px 14px; background: #FBF8F1; border-bottom: 3px double #C9974D; }
    .ur-search-card__photo { border-radius: 14px; box-shadow: 0 0 0 2px #C9974D, 0 0 0 5px #FBF8F1; }
    .ur-search-card__id { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-weight: 700; font-size: 23px; letter-spacing: .6px; font-variant-numeric: lining-nums; color: #123A2E; }
    .ur-search-card__verified { background: transparent; border: 1px solid #1E7A46; color: #1E7A46; font-size: 10.5px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; padding: 3px 10px; }
    .ur-search-card__meta { font-size: 13.5px; color: #2A3B34; }
    .ur-search-card__meta--sub { font-size: 12.5px; color: #7A6A4A; font-style: italic; }
    .ur-search-card__flag { width: 18px !important; height: 12px !important; }
    .ur-search-card__badge { font-size: 9.5px; padding: 2px 9px; }
    .ur-search-card__panels { gap: 10px; padding: 14px 14px 2px; }
    .ur-search-card__panel, .ur-search-card__panel--alt { background: rgba(255,255,255,.035); border: 1px solid rgba(201,151,77,.35); border-radius: 12px; padding: 12px 12px 6px; }
    .ur-search-card__panel h4 { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 15.5px; font-weight: 700; letter-spacing: .4px; color: #E2B66E; margin: 0 0 10px; padding-bottom: 8px; border-bottom: 1px solid rgba(201,151,77,.3); }
    .ur-search-card__panel li { font-size: 13.5px; margin-bottom: 8px; gap: 9px; }
    .ur-search-card__panel li i { font-size: 14px; color: #C9974D; }
    .ur-search-card__actions { gap: 10px; padding: 12px 14px 16px; }
    .ur-search-card__btn { min-height: 42px; border-radius: 10px; border: 1px solid rgba(226,182,110,.6); font-size: 12px; font-weight: 700 !important; letter-spacing: .7px; text-transform: uppercase; }
    .ur-search-card__btn i, .ur-search-card__btn span { font-weight: 700 !important; }
    .ur-search-card__btn--dark, .ur-search-card__btn--whatsapp { background: linear-gradient(180deg, #D9AC63, #B88436); border-color: #B88436; }
    .ur-search-card__btn--dark:hover, .ur-search-card__btn--whatsapp:hover { background: linear-gradient(180deg, #E2B66E, #C9974D); border-color: #C9974D; }
    /* Photo on top (Nov 2026): the whole photo gets the full card width, the ID / quick facts sit underneath */
    .ur-search-card__head { flex-direction: column; align-items: stretch; gap: 0; padding: 0; }
    .ur-search-card__photo { flex: none; width: 100%; height: auto; aspect-ratio: 5 / 4; max-height: 300px; border-radius: 0; box-shadow: none; }
    .ur-search-card__headinfo { width: 100%; box-sizing: border-box; padding: 14px 16px; border-top: 3px double #C9974D; }

    /* Ivory & emerald (client design, Nov 2026) — same look as the shared member card (public/css/ur-member-card.css) */
    .ur-search-card { background: linear-gradient(180deg, #FFFDF8 0%, #F7F1E3 100%); border: 2px solid #D4AE6A; border-radius: 22px; color: #1C2A24; box-shadow: 0 10px 26px rgba(15,46,36,.14), inset 0 0 0 4px #FBF6EA; }
    .ur-search-card:hover { box-shadow: 0 16px 32px rgba(15,46,36,.22), inset 0 0 0 4px #FBF6EA; }
    .ur-search-card__head { flex-direction: row; align-items: flex-start; gap: 16px; padding: 18px 18px 6px; background: transparent; border-bottom: 0; }
    .ur-search-card__photo { flex: 0 0 42%; width: 42%; height: auto; aspect-ratio: 4 / 5; max-height: none; border-radius: 16px; box-shadow: 0 4px 12px rgba(15,46,36,.18); }
    .ur-search-card__headinfo { flex: 1; width: auto; min-width: 0; padding: 0; border-top: 0; }
    .ur-search-card__top { flex-wrap: nowrap; justify-content: flex-start; margin: 0 0 8px; padding-bottom: 8px; border-bottom: 1px solid rgba(201,151,77,.45); }
    .ur-search-card__id { font-size: 19.5px; letter-spacing: .2px; white-space: nowrap; }
    /* photo is as tall as the intro column beside it */
    .ur-search-card__head { align-items: stretch; }
    .ur-search-card__photo { aspect-ratio: auto; height: auto; align-self: stretch; min-height: 200px; max-width: 220px; }
    /* the image fills the frame instead of setting its height, so a tall photo can no longer stretch the card */
    .ur-search-card__photo img { position: absolute; inset: 0; width: 100%; height: 100%; }
    /* equal-height cards: the panels take the spare room, the buttons stay at the bottom, long values are cut at two lines */
    .ur-search-card__panels { flex: 1; }
    .ur-search-card__actions { margin-top: auto; }
    .ur-search-card__panel li span { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; }
    .ur-search-card__realname { margin: -2px 0 8px; font-size: 14.5px; font-weight: 600; color: #4A4330; }
    .ur-search-card__factlist { list-style: none; margin: 0; padding: 0; }
    .ur-search-card__factlist li { display: flex; align-items: flex-start; gap: 10px; margin: 0 0 7px; font-size: 14px; line-height: 1.3; color: #1C2A24; }
    .ur-search-card__factlist li i { flex: 0 0 18px; width: 18px; margin-top: 1px; text-align: center; font-size: 16.5px; color: #B88436 !important; }
    .ur-search-card__factlist li span { min-width: 0; overflow-wrap: anywhere; color: inherit !important; }
    .ur-search-card__flag { width: 22px !important; height: 15px !important; }
    .ur-search-card__badge--photo { position: absolute; top: 10px; left: 10px; z-index: 3; font-size: 11px; padding: 4px 12px; background: linear-gradient(180deg, #F1D38E, #D9AC63); color: #3A2A08; box-shadow: 0 2px 6px rgba(0,0,0,.25); }
    .ur-search-card__photo .ur-search-card__verified { position: absolute; left: 50%; bottom: 8px; transform: translateX(-50%); z-index: 3; background: #0F4A35; border: 0; color: #fff; font-size: 10.5px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; padding: 4px 12px; box-shadow: 0 2px 6px rgba(0,0,0,.3); }
    .ur-search-card__photo .ur-search-card__verified i { color: #fff !important; }
    .ur-search-card__owner { color: #6B6350; }
    .ur-search-card__owner i { color: #B88436; }
    .ur-search-card__panels { gap: 10px; padding: 10px 18px 2px; }
    .ur-search-card__panel, .ur-search-card__panel--alt { background: linear-gradient(180deg, #FFFCF4, #F8F0DD); border: 1px solid rgba(201,151,77,.6); border-radius: 14px; padding: 12px 12px 4px; box-shadow: 0 2px 6px rgba(120,90,30,.08); }
    .ur-search-card__panel h4 { display: flex; align-items: center; gap: 6px; white-space: nowrap; font-size: 15px; color: #123A2E; border-bottom: 1px solid rgba(201,151,77,.55); margin: 0 0 4px; padding-bottom: 8px; }
    .ur-search-card__panel h4 i { color: #B88436 !important; font-size: 15px; }
    .ur-search-card__panel li { margin: 0; padding: 8px 0; font-size: 13.5px; color: #1C2A24; border-bottom: 1px solid rgba(201,151,77,.3); }
    .ur-search-card__panel li:last-child { border-bottom: 0; }
    .ur-search-card__panel li i { font-size: 15px; color: #B88436; }
    .ur-search-card__panel li.is-empty span { color: #8C8470; }
    .ur-search-card__actions { gap: 10px; padding: 12px 18px 18px; }
    .ur-search-card__btn { flex: 1 1 calc(50% - 5px); min-height: 46px; border-radius: 999px; border: 1.5px solid #123A2E; background: #FFFDF8; color: #123A2E !important; font-size: 13px; letter-spacing: .3px; text-transform: none; box-sizing: border-box; }
    .ur-search-card__btn:hover { background: #EFE7D2; border-color: #0A2A20; color: #123A2E !important; }
    .ur-search-card__btn:first-child { order: -1; flex: 1 1 100%; min-height: 52px; font-size: 16px; background: linear-gradient(180deg, #11634A, #0C4A38); border-color: #C9974D; color: #fff !important; box-shadow: 0 4px 10px rgba(15,46,36,.25); }
    .ur-search-card__btn:first-child:hover { background: linear-gradient(180deg, #157A5B, #0F5A44); border-color: #E2B66E; color: #fff !important; }
    .ur-search-card__btn--whatsapp { background: #FFFDF8; border-color: #123A2E; color: #123A2E !important; }
    .ur-search-card__btn--whatsapp:hover { background: #EFE7D2; border-color: #0A2A20; color: #123A2E !important; }
    .ur-search-card__btn--whatsapp i { color: #1E9B57 !important; }
    @media (max-width: 900px) {
        .ur-search-results { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        /* Phones: a smaller header photo and tighter panels so the card stays compact */
        .ur-search-card__head { gap: 12px; padding: 14px 14px 4px; }
        .ur-search-card__photo { flex-basis: 42%; width: 42%; border-radius: 14px; }
        .ur-search-card__id { font-size: 17px; letter-spacing: 0; }
        .ur-search-card__factlist li { font-size: 12.5px; gap: 7px; margin-bottom: 6px; }
        .ur-search-card__panels { gap: 8px; padding: 8px 12px 2px; }
        .ur-search-card__panel { padding: 10px 10px 2px; }
        .ur-search-card__panel h4 { font-size: 13.5px; gap: 5px; }
        .ur-search-card__panel li { font-size: 12px; gap: 6px; }
        .ur-search-card__actions { gap: 8px; padding: 10px 12px 14px; }
        .ur-search-card__btn { min-height: 42px; font-size: 12.5px; padding: 8px 8px; }
        .ur-search-card__btn:first-child { min-height: 48px; font-size: 15px; }
        /* Phone search form: compact, two fields per row (like a native filter sheet) instead of one tall field per row */
        .ur-adv-search { padding: 14px; --ur-ms-h: 42px; --ur-ms-radius: 10px; }
        .ur-adv-search__row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .ur-adv-search__row .ur-adv-search__field { grid-column: 1 / -1; }
        .ur-adv-search__submit { grid-column: 1 / -1; width: 100%; justify-content: center; border-radius: 12px; }
        .ur-adv-search__clear, .ur-adv-search__more-btn { width: 100%; justify-content: center; margin: 0; border-radius: 12px; height: 42px; }
        .ur-adv-search__filters { grid-template-columns: 1fr 1fr; gap: 12px 10px; margin-top: 14px; padding-top: 14px; }
        .ur-adv-search__filters > div { min-width: 0; }
        /* Gender+Marital, Age from+to and Height from+to sit side by side; the long lists (Education, Caste) and City take the full row */
        .ur-adv-search__filters > div:nth-child(n+7) { grid-column: 1 / -1; }
        .ur-adv-search__filters label { font-size: 12px; font-weight: 600; color: #6B7570; margin-bottom: 5px; }
        .ur-adv-search__filters .form-control, .ur-adv-search__filters select { height: 42px; font-size: 14px; border-radius: 10px; padding: 0 10px; }
        .ur-adv-search__filters .select2-container--default .select2-selection--multiple .select2-selection__choice { font-size: 12.5px; }
        .ur-adv-search__hint { display: none; }
        .ur-adv-search__more-btn { margin-left: 0; }
    }
</style>

<div class="ur-team-page">
    <div class="ur-team-page__head">
        <div>
            <h1>Advanced search</h1>
            <p>Search the shared team-added network by keyword — open "More filters" for the full filter set (gender, age, height, education, caste and more).</p>
        </div>
    </div>

    @php
        $activeTab = request()->filled('country') ? 'country' : (request('profession') ? 'profession' : 'keyword');
        $hasMoreFilters = collect(['height_from', 'height_to', 'gender', 'aged_from', 'aged_to', 'caste', 'marital_status', 'education', 'city', 'current_city', 'country', 'nationality', 'sect'])
            ->contains(fn ($f) => request()->filled($f));
        $selectedCountries = collect((array) request('country'))->filter()->values();
        $selectedCountry = $selectedCountries->isNotEmpty() ? (object) ['name' => $selectedCountries->implode(', ')] : null;
    @endphp

    <p class="ur-adv-search__network">{{ number_format($totalNetworkCount) }} profile{{ $totalNetworkCount == 1 ? '' : 's' }} in the shared network</p>

    <form method="GET" action="{{ route('team.proposals.search') }}" class="ur-adv-search" id="search_form">
        {{-- keyword search first (the quick search), then the optional "More filters" set below it --}}
        <div class="ur-adv-search__row">
            <div class="ur-adv-search__field" data-panel="keyword">
                <input type="text" class="form-control" name="keyword" placeholder="Search by name, profession or city…" value="{{ request('keyword') }}" autocomplete="off">
            </div>
            <button type="submit" class="ur-adv-search__submit"><i class="fa fa-search"></i> Search</button>
            <a href="{{ route('team.proposals.search') }}" class="ur-adv-search__clear"><i class="fa fa-times"></i> Clear filters</a>
            <button type="button" class="ur-adv-search__more-btn is-active" id="more_filters_toggle"><i class="fa fa-sliders"></i> More filters</button>
        </div>

        <div class="ur-adv-search__filters" id="more_filters_panel">
            @php
                $heightChoices = [];
                for ($ft = 4; $ft <= 6; $ft++) { for ($in = 0; $in <= 11; $in++) { $heightChoices[$ft . "'" . $in . '"'] = $ft * 12 + $in; } }
                $heightChoices["7'0\""] = 84;
            @endphp
            <div>
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Any</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div>
                <label>Marital Status</label>
                <select name="marital_status[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ in_array((string) $maritalstatus->dataid, array_map('strval', (array) request('marital_status')), true) ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Age From</label>
                <input type="number" class="form-control" name="aged_from" value="{{ request('aged_from') }}" min="18" max="99">
            </div>
            <div>
                <label>Age To</label>
                <input type="number" class="form-control" name="aged_to" value="{{ request('aged_to') }}" min="18" max="99">
            </div>
            <div>
                <label>Height From</label>
                <select name="height_from" class="form-control">
                    <option value="">Any</option>
                    @foreach($heightChoices as $label => $inches)
                        <option value="{{ $inches }}" {{ (string) request('height_from') === (string) $inches ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Height To</label>
                <select name="height_to" class="form-control">
                    <option value="">Any</option>
                    @foreach($heightChoices as $label => $inches)
                        <option value="{{ $inches }}" {{ (string) request('height_to') === (string) $inches ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Education / Qualification</label>
                <select name="education[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ in_array((string) $degree->dataid, array_map('strval', (array) request('education')), true) ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Caste</label>
                <select name="caste[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ in_array((string) $cst->dataid, array_map('strval', (array) request('caste')), true) ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Sect</label>
                <select name="sect[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($sects as $sectName)
                        <option value="{{ $sectName }}" {{ in_array($sectName, array_map('strval', (array) request('sect')), true) ? 'selected' : '' }}>{{ $sectName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Country</label>
                <select name="country[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($countries as $cty)
                        <option value="{{ $cty->dataid }}" {{ in_array((string) $cty->dataid, array_map('strval', (array) request('country')), true) ? 'selected' : '' }}>{{ $cty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nationality</label>
                <select name="nationality[]" class="form-control ur-multi" multiple data-placeholder="Any">
                    @foreach($nationalities as $nat)
                        <option value="{{ $nat->dataid }}" {{ in_array((string) $nat->dataid, array_map('strval', (array) request('nationality')), true) ? 'selected' : '' }}>{{ $nat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>City / Location</label>
                <input type="text" class="form-control" name="city" value="{{ request('city') }}" placeholder="Hometown or current city">
            </div>
        </div>


        <p class="ur-adv-search__hint">Search runs against the standardized fields pulled from each pasted profile — every client's original pasted form stays unchanged in their private file.</p>

    </form>

    <div class="ur-adv-search__results-head">
        <div><strong>{{ number_format($resultCount) }}</strong> profile{{ $resultCount == 1 ? '' : 's' }} found{{ $selectedCountry ? ' in ' . $selectedCountry->name : '' }}</div>
        <div class="ur-adv-search__sort">Sorted by newest</div>
    </div>

    <div class="ur-search-results">
        @forelse($members as $member)
            @include('team.partials.search-result-card', ['member' => $member])
        @empty
            <div class="ur-adv-search__empty">No proposals match these filters.</div>
        @endforelse
    </div>

    @if($numPages > 1)
    <ul class="pagination">
        @if($currentPage != 1)
        <li class="page-item"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $currentPage - 1])) }}" class="page-link">Previous</a></li>
        @endif

        @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $i])) }}" class="page-link">{{ $i }}</a></li>
        @endfor

        @if($currentPage < $numPages)
        <li class="page-item"><a href="{{ route('team.proposals.search', array_merge(request()->except('page'), ['page' => $currentPage + 1])) }}" class="page-link">Next <i class="fa fa-angle-right"></i></a></li>
        @endif
    </ul>
    @endif
</div>

<script>
(function () {
    var form = document.getElementById('search_form');
    var moreBtn = document.getElementById('more_filters_toggle');
    var morePanel = document.getElementById('more_filters_panel');

    moreBtn.addEventListener('click', function () {
        var isOpen = morePanel.style.display !== 'none';
        morePanel.style.display = isOpen ? 'none' : '';
        moreBtn.classList.toggle('is-active', !isOpen);
    });
})();
</script>
<script src="/js/ur-multiselect.js?v={{ filemtime(public_path('js/ur-multiselect.js')) }}"></script>
@endsection
