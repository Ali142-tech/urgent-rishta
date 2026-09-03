@extends('layouts.master')
@section('main-content')
<?php use App\User; ?>
<style>
    /* ===== Brand tokens (matches ur-dashboard / ur-profile / ur-navbar) ===== */
    .ur-search-page {
        --ur-green: #123A2E;
        --ur-green-dark: #0F2E24;
        --ur-gold: #C9974D;
        --ur-red: #B5674A;
        --ur-bg: #F6F4EF;
        --ur-border: #E7E2D6;
        --ur-text: #1C2321;
        --ur-text-muted: #6B7570;
        /* Safety net: nothing inside this page should ever be able to
           push the viewport into horizontal scroll on mobile. */
        max-width: 100vw;
        overflow-x: hidden;
    }

    /* ---- Bring the sidebar / buttons / form controls / pagination onto the same palette ---- */
    .ur-search-page .card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(15, 46, 36, .06);
        overflow: hidden;
    }

    .ur-search-page .card-title {
        background: #fff;
        border-bottom: 1px solid var(--ur-border) !important;
    }

    .ur-search-page .card-title h3 {
        color: var(--ur-green);
    }

    .ur-search-page .form-control:focus {
        border-color: var(--ur-gold) !important;
        box-shadow: none;
    }

    .ur-search-page .radio-primary input[type="radio"] + label::after,
    .ur-search-page .radio-primary input[type="radio"]:checked + label::before,
    .ur-search-page .radio-primary input[type="radio"]:checked + label::after {
        background-color: var(--ur-green) !important;
        border-color: var(--ur-green) !important;
    }

    .ur-search-page .btn-base-1 {
        background-color: var(--ur-green) !important;
        border-color: var(--ur-green) !important;
        color: #fff !important;
    }

    .ur-search-page .btn-base-1:active,
    .ur-search-page .btn-base-1.active,
    .ur-search-page .btn-base-1:focus,
    .ur-search-page .btn-base-1:hover {
        background-color: var(--ur-green-dark) !important;
        border-color: var(--ur-green-dark) !important;
        color: #fff !important;
    }

    .ur-search-page .pagination > .active .page-link,
    .ur-search-page .pagination > .active .page-link:focus,
    .ur-search-page .pagination > .active .page-link:hover,
    .ur-search-page .pagination > .active > span {
        background-color: var(--ur-green) !important;
        border-color: var(--ur-green) !important;
        color: #fff !important;
    }

    .ur-search-page .pagination .page-item .page-link:focus,
    .ur-search-page .pagination .page-item .page-link:hover {
        background-color: var(--ur-bg) !important;
        border-color: var(--ur-border) !important;
        color: var(--ur-green) !important;
    }

    /* Let the page use the full browser width instead of Bootstrap's
       ~1140px capped .container, which left huge empty gutters on wide
       screens. Capped (not 100%) so text/cards don't stretch absurdly
       wide on ultra-wide monitors. */
    .ur-search-page > .container {
        max-width: 1760px;
    }

    /* Keep the filter sidebar a sensible fixed width instead of
       stretching it along with the wider container — the extra room
       goes to the results grid instead, so it fills with more cards
       per row rather than one huge empty-feeling form column. */
    @media (min-width: 992px) {
        .ur-search-page .col-lg-4.size-sm {
            flex: 0 0 320px;
            max-width: 320px;
        }

        .ur-search-page .col-lg-8 {
            flex: 1 1 auto;
            max-width: calc(100% - 320px);
        }
    }

    /* ===== Search results member cards ===== */
    .member-results {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 22px;
    }

    @media (max-width: 767px) {
        .member-results {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .member-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--ur-border, #E7E2D6);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(15, 46, 36, .06);
        transition: box-shadow .25s ease, transform .25s ease;
    }

    .member-card:hover {
        box-shadow: 0 14px 30px rgba(15, 46, 36, .12);
        transform: translateY(-3px);
    }

    .member-card__photo {
        position: relative;
        width: 100%;
        aspect-ratio: 8 / 5;
        background: var(--ur-bg, #F6F4EF);
        overflow: hidden;
    }

    .member-card__photo a {
        display: block;
        position: relative;
        width: 100%;
        height: 100%;
    }

    /* Member photos are only ever generated up to ~210px on their longest
       side, so forcing them to `cover` a much larger box upscales and
       crops them hard (blurry, oddly zoomed faces). We show the photo
       untouched (`contain`, never cropped/upscaled beyond native size)
       and fill the rest of the box with a softly blurred copy of the
       same photo instead of dead space, so every card still reads as a
       uniform, deliberate frame regardless of the source image's size
       or orientation. */
    .member-card__photo-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(20px) saturate(1.1) brightness(.92);
        transform: scale(1.15);
    }

    .member-card__photo img {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
    }

    .member-card__ribbon {
        position: absolute;
        top: 12px;
        left: -34px;
        transform: rotate(-45deg);
        width: 130px;
        padding: 3px 0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #fff;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    .member-card__ribbon--new {
        background: var(--ur-gold, #C9974D);
        color: var(--ur-green-dark, #0F2E24);
    }

    .member-card__ribbon--updated {
        background: var(--ur-green, #123A2E);
    }

    .member-card__id-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
        background: rgba(15, 46, 36, 0.6);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .3px;
        padding: 4px 9px;
        border-radius: 20px;
        pointer-events: none;
    }

    .member-card__body {
        padding: 16px 16px 4px;
        flex: 1;
    }

    .member-card__name {
        margin: 0 0 10px;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .member-card__name a {
        color: var(--ur-text, #1C2321);
        cursor: pointer;
    }

    .member-card__name a:hover {
        color: var(--ur-green, #123A2E);
    }

    .member-card__quick {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 16px;
        list-style: none;
        padding: 0;
        margin: 0 0 12px;
        font-size: .8rem;
        color: var(--ur-text-muted, #6B7570);
    }

    .member-card__quick li i {
        color: var(--ur-gold, #C9974D);
        margin-right: 5px;
    }

    .member-card__details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0 10px;
        list-style: none;
        padding: 12px 0;
        margin: 0;
        border-top: 1px dashed var(--ur-border, #E7E2D6);
        border-bottom: 1px dashed var(--ur-border, #E7E2D6);
    }

    .member-card__details li {
        display: flex;
        flex-direction: column;
        font-size: .78rem;
        overflow: hidden;
    }

    .member-card__details li span {
        color: var(--ur-text-muted, #6B7570);
        text-transform: uppercase;
        font-size: .65rem;
        letter-spacing: .3px;
        margin-bottom: 2px;
    }

    .member-card__details li b {
        color: var(--ur-text, #1C2321);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-card__footer {
        display: flex;
        gap: 10px;
        padding: 14px 16px 16px;
    }

    .member-card__footer a {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-align: center;
        padding: 11px 8px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        cursor: pointer;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        border: 1px solid var(--ur-gold, #C9974D);
        color: var(--ur-green, #123A2E);
        background: #fff;
    }

    .member-card__footer a:hover {
        background: #FBF3E6;
    }

    .member-card__footer a.is-interest {
        border-color: var(--ur-green, #123A2E);
        background: var(--ur-green, #123A2E);
        color: #fff;
    }

    /* Force white text even for the accepted/declined states, whose
       inner <span class="c-green"/"c-red"> otherwise override the
       color with !important. */
    .member-card__footer a.is-interest span {
        color: #fff !important;
    }

    .member-card__footer a.is-interest:hover {
        background: var(--ur-green-dark, #0F2E24);
        border-color: var(--ur-green-dark, #0F2E24);
    }

    @media (max-width: 420px) {
        .member-card__footer {
            flex-direction: column;
        }
    }

    /* ===== Hero ===== */
    .ur-search-hero {
        background: linear-gradient(180deg, #FBF7EF 0%, #ffffff 100%);
        position: relative;
        padding: 46px 0 34px;
    }

    .ur-search-hero:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--ur-gold), transparent);
    }

    .ur-search-hero h2 {
        font-family: 'Playfair Display', serif;
        color: var(--ur-green);
        font-weight: 700;
    }

    .ur-search-hero p {
        color: var(--ur-text-muted);
        font-size: 15px;
        margin: 6px 0 0;
    }

    .ur-flourish {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 16px auto 0;
        width: 160px;
    }

    .ur-flourish:before,
    .ur-flourish:after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--ur-gold);
    }

    .ur-flourish i {
        width: 6px;
        height: 6px;
        background: var(--ur-gold);
        transform: rotate(45deg);
        display: inline-block;
        flex: 0 0 auto;
    }

    .ur-flourish--sm {
        width: 84px;
        margin: 8px auto 0;
    }

    /* ===== Sidebar card ===== */
    .ur-search-page .card-title {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 22px 20px 16px;
    }

    .ur-search-page .card-title h3 {
        font-family: 'Playfair Display', serif;
        font-size: 16px;
        letter-spacing: .5px;
    }

    .ur-search-page .card-body label.text-uppercase {
        display: block;
        font-size: 10.5px;
        letter-spacing: .5px;
        font-weight: 700;
        color: var(--ur-text-muted);
        margin-bottom: 6px;
    }

    .ur-search-page .form-control,
    .ur-search-page .select2-container .select2-selection--single {
        border-radius: 10px !important;
        border: 1px solid var(--ur-border) !important;
        min-height: 42px !important;
        height: 42px !important;
        font-size: 13px !important;
        color: var(--ur-text) !important;
        background: #fff !important;
    }

    .ur-search-page .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 14px !important;
        color: var(--ur-text) !important;
    }

    .ur-search-page .select2-container .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .ur-search-page input[type="checkbox"],
    .ur-search-page input[type="radio"] {
        accent-color: var(--ur-green);
    }

    /* Looking-for pill toggle */
    .ur-toggle-group {
        display: flex;
        gap: 10px;
        margin-bottom: 4px;
    }

    .ur-toggle-group {
        position: relative;
    }

    .ur-toggle-group input[type="radio"] {
        position: absolute !important;
        opacity: 0 !important;
        width: 1px !important;
        height: 1px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        pointer-events: none;
    }

    .ur-toggle-pill {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border: 1px solid var(--ur-border);
        border-radius: 999px;
        background: #fff;
        color: var(--ur-text-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin: 0;
        transition: all .15s ease;
    }

    .ur-toggle-pill:before {
        content: "";
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid var(--ur-border);
        display: inline-block;
        box-sizing: border-box;
        transition: all .15s ease;
    }

    .ur-toggle-group input[type="radio"]:checked + .ur-toggle-pill {
        background: var(--ur-green);
        border-color: var(--ur-green);
        color: #fff;
    }

    .ur-toggle-group input[type="radio"]:checked + .ur-toggle-pill:before {
        border-color: #fff;
        background: radial-gradient(circle, #fff 40%, transparent 44%);
    }

    .ur-toggle-group input[type="radio"]:focus-visible + .ur-toggle-pill {
        outline: 2px solid var(--ur-gold);
        outline-offset: 2px;
    }

    .ur-check-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 10px 0 18px;
    }

    .ur-check-row label {
        margin: 0;
        font-size: 11px;
        letter-spacing: .4px;
        font-weight: 700;
        color: var(--ur-text-muted);
    }

    .ur-age-caption {
        display: block;
        font-size: 10px;
        color: var(--ur-text-muted);
        margin-bottom: 4px;
    }

    .ur-search-actions {
        display: flex;
        gap: 10px;
        margin-top: 6px;
    }

    .ur-btn-solid,
    .ur-btn-outline {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 46px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .3px;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none !important;
        transition: all .2s ease;
    }

    .ur-btn-solid {
        background: var(--ur-green);
        color: #fff !important;
    }

    .ur-btn-solid:hover {
        background: var(--ur-green-dark);
        color: #fff !important;
    }

    .ur-btn-outline {
        background: #fff;
        color: var(--ur-green) !important;
        border-color: var(--ur-gold);
    }

    .ur-btn-outline:hover {
        background: #FBF3E6;
    }

    /* Toolbar */
    .ur-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .ur-toolbar label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--ur-text-muted);
        margin: 0;
        font-weight: 600;
    }

    .ur-toolbar select {
        box-sizing: border-box;
        border-radius: 8px;
        border: 1px solid var(--ur-border);
        height: 36px;
        padding: 0 8px;
        font-size: 13px;
        color: var(--ur-text);
        background: #fff;
    }

    /* Pagination */
    .ur-search-page .pagination {
        gap: 6px;
        flex-wrap: wrap;
    }

    .ur-search-page .pagination .page-link,
    .ur-search-page .pagination .page-item > span {
        border-radius: 999px !important;
        min-width: 38px;
        text-align: center;
    }

    .ur-search-page .pagination .ur-ellipsis span {
        border: none;
        background: transparent;
        color: var(--ur-text-muted);
    }

    /* Results banner */
    .ur-results-banner {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: var(--ur-bg);
        border: 1px solid var(--ur-border);
        border-radius: 10px;
        padding: 10px 16px;
        margin: 14px 0 20px;
        font-size: 13px;
        color: var(--ur-text);
    }

    .ur-results-banner i {
        color: var(--ur-gold);
        flex: 0 0 auto;
        margin-top: 2px;
    }

    .ur-results-banner__text {
        flex: 1 1 auto;
        min-width: 0;
        overflow-wrap: break-word;
    }

    @media (max-width: 480px) {
        .ur-results-banner {
            font-size: 12px;
        }
    }

    .ur-results-banner b {
        color: var(--ur-green);
        font-weight: 700;
    }
</style>
<section class="ur-search-hero ur-search-page">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Search Results - Active Members</h2>
                <p>Find your perfect match from our verified active members</p>
                <div class="ur-flourish"><i></i></div>
            </div>
        </div>
    </div>
</section>
<section class="slice sct-color-1 ur-search-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 size-sm">
                <div class="sidebar">
                    <div class="">
                        <div class="card">
                            <div class="card-title b-xs-bottom">
                                <h3 class="heading heading-sm text-uppercase">Advanced Search</h3>
                                <div class="ur-flourish ur-flourish--sm"><i></i></div>
                            </div>
                            <div class="card-body">
                                <form class="form-default" id="search_form" data-toggle="validator" role="form" action="{{route('searchresults')}}" method="post">
                                    @csrf
                                    <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
                                    <input type="hidden" id="pagesize" name="pagesize" value="{{ $pageSize }}"/>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Looking For</label>
                                                <div class="ur-toggle-group">
                                                    <input type="radio" name="gender" id="bride" value="female" required="required" {{request()->gender=='female'?'checked="checked"':''}} />
                                                    <label for="bride" class="ur-toggle-pill">Bride</label>
                                                    <input type="radio" name="gender" id="groom" value="male" required="required" {{request()->gender=='male'?'checked="checked"':''}} />
                                                    <label for="groom" class="ur-toggle-pill">Groom</label>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback ur-check-row">
                                                <input type="checkbox" name="withpics" id="withpics" value="true" {{request()->withpics==true?'checked="checked"':''}} />
                                                <label for="withpics" class="text-uppercase mb-0">With Images Only</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Age Range</label>
                                                <select name="aged_from" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="From" data-hide-disabled="true">
                                                    <option value="">From</option>
                                                    @for ($i=18; $i<=75; $i++) <option {{request()->aged_from==($i<10?"0".$i:$i)?'selected="selected"':''}}>{{$i<10?"0".$i:$i}}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">To</label>
                                                <select name="aged_to" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="To" data-hide-disabled="true">
                                                    <option value="">To</option>
                                                    @for ($i=18; $i<=75; $i++) <option {{request()->aged_to==($i<10?"0".$i:$i)?'selected="selected"':''}}>{{$i<10?"0".$i:$i}}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @auth
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Member Id</label>
                                                <input type="text" class="form-control form-control-sm" name="member_id" id="filter_member_id" value="{{request()->member_id?request()->member_id:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    @endauth
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Name</label>
                                                <input type="text" class="form-control form-control-sm" name="first_name" id="filter_first_name" value="{{request()->first_name?request()->first_name:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Profession</label>
                                                <input type="text" class="form-control form-control-sm" name="profession" id="filter_profession" value="{{request()->profession?request()->profession:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Marital Status</label>
                                                <select name="marital_status" onchange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" data-hide-disabled="true">
                                                    <option value="">Choose a marital status</option>
                                                    @foreach($maritalstatuses as $maritalstatus)
                                                    <option value="{{$maritalstatus->dataid}}" {{request()->marital_status==$maritalstatus->dataid?'selected="selected"':''}}>{{$maritalstatus->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Country</label>
                                                <select name="country" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/1', $('#city'), '{{request()->city}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" data-hide-disabled="true">
                                                    <option value="">Choose a country</option>
                                                    @foreach($countries as $country)
                                                    <option value="{{$country->dataid}}" {{request()->country==$country->dataid?'selected="selected"':''}}>{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">City</label>
                                                <select id="city" name="city" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" data-hide-disabled="true">
                                                    <option value="">Choose a country first</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Religion</label>
                                                <select name="religion" onchange="(this.value,this)" class="form-control form-control-sm selectpicker s_religion" data-placeholder="Choose a religion" data-hide-disabled="true">
                                                    <option value="">Choose a religion</option>
                                                    @foreach($religions as $religion)
                                                    <option value="{{$religion->dataid}}" {{request()->religion==$religion->dataid?'selected="selected"':''}}>{{$religion->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Caste</label>
                                                <select name="caste" class="form-control form-control-sm selectpicker" data-placeholder="Choose a caste" data-hide-disabled="true">
                                                    <option value="">Choose a caste</option>
                                                    @foreach($caste as $cst)
                                                    <option value="{{$cst->dataid}}" {{request()->caste==$cst->dataid?'selected="selected"':''}}>{{$cst->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Mother Tongue</label>
                                                <select name="mother_tongue" onchange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a language" data-hide-disabled="true">
                                                    <option value="">Choose a mother tongue</option>
                                                    @foreach($mothertongues as $mothertongue)
                                                    <option value="{{$mothertongue->dataid}}" {{request()->mother_tongue==$mothertongue->dataid?'selected="selected"':''}}>{{$mothertongue->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Profession</label>
                                                <input type="text" class="form-control form-control-sm" name="profession" id="filter_profession" value="">
                                            </div> -->
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Min Height (Feet)</label>
                                                <input type="text" class="form-control form-control-sm height_mask" name="min_height" id="min_height" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Max Height (Feet)</label>
                                                <input type="text" class="form-control form-control-sm height_mask" name="max_height" id="max_height" value="8.00">
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- <div class="pt-0">
                                        <div class="card-title b-xs-bottom">
                                            <h3 class="heading heading-sm text-uppercase">Member Type</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="filter-radio">
                                                <div class="radio radio-primary">
                                                    <input type="radio" name="search_member_type" id="s_all_members" value="all" checked="">
                                                    <label for="s_all_members">All Members</label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" name="search_member_type" id="s_premium_members" value="premium_members">
                                                    <label for="s_premium_members">Premium Members</label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" name="search_member_type" id="s_free_members" value="free_members">
                                                    <label for="s_free_members">Free Members</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="ur-search-actions">
                                        <button type="submit" id="search_button" class="ur-btn-solid"><i class="fa fa-search"></i> Search Members</button>
                                        <a href="{{ route('searchresults') }}" class="ur-btn-outline"><i class="fa fa-refresh"></i> Reset Filters</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 size-sm-btn mb-4">
                <button type="button" class="btn btn-block btn-base-1 mt-2 z-depth-2-bottom" onclick="$('.size-sm').show();$('.size-sm-btn').hide();">Advanced Search</button>
            </div>
            <div class="col-lg-8">
                <div class="block-wrapper" id="result">
                <div class="row">
                        <form id="controls-form" action="javascript:void();" class="w-100">
                            <div class="col-sm-12 col-md-12 ur-toolbar">
                                <label>Show <select id="selpagesize" name="selpagesize" aria-controls="datatable" onchange="javascript:refreshProfiles(true);">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select> entries per page</label>
                                <!-- <span><label>Search: <input type="search" name="term" class="form-control form-control-sm" placeholder="Enter search query..." autocomplete="off" onkeyup="javascript:refreshProfiles(true);" value="" /></label></span> -->
                            </div>
                        </form>
                    </div>
                    <div id="search-data">
                    @yield('search-data')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    /* xs */
    .size-sm {
        display: none;
    }

    .size-sm-btn {
        display: block;
    }

    /* sm */
    @media (min-width: 768px) {
        .size-sm {
            display: none;
        }

        .size-sm-btn {
            display: block;
        }
    }

    /* md */
    @media (min-width: 992px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }

    /* lg */
    @media (min-width: 1200px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        //$('.carousel').carousel();

        // preload city select
        @if(!empty(request()->country))
        loadSelect('{{url('cities')}}', '{{request()->country}}/1', $('#city'), '{{request()->city}}');
        @endif
    });

    $("#search_form").on("submit", function() {
        var elem = $("#search_button");
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
        refreshProfiles(true);
        elem.html(oldHtml);
        elem.prop('disabled', false);
        return false;
    });

    function refreshProfiles(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        $("#pagesize").val($("#selpagesize").val());
        renderPage("{{url('member/searchresults/1')}}", "post", $("#search_form").serialize(), $("#search-data"));
    }
</script>
@endsection
