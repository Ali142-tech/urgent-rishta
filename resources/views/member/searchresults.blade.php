{{--
    Logged-in members get the dashboard shell (sidebar + topbar) like the
    rest of member/*; guests keep the public marketing layout since this
    page (and its @guest "register to continue" prompts) is also reachable
    signed out — same conditional already used by member/profile.blade.php.
--}}
@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.master')
@section('dashboard-title', 'Search Profiles')
@section('main-content')
<?php use App\User; ?>
{{-- Shared member-card component styling, kept in its own file so the
     exact same rules could be reused elsewhere without duplicating them. --}}
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
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

    /* ---- Bring the search widget / buttons / form controls / pagination onto the same palette ---- */
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

    /* Filters modal — reuse the brand tokens instead of Bootstrap's defaults.
       This form/modal has no other visible content on the page — it's
       opened by the "Search Profiles" button in the welcome banner (members)
       or the standalone trigger above (guests). */
    .ur-filters-modal {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(15, 46, 36, .25);
    }

    .ur-filters-modal .modal-header {
        background: linear-gradient(135deg, var(--ur-green) 0%, var(--ur-green-dark) 100%);
        border-bottom: none;
        padding: 22px 28px;
        align-items: flex-start;
    }

    .ur-filters-modal .modal-header .ur-filters-modal__title-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ur-filters-modal .modal-title {
        font-family: 'Playfair Display', serif;
        color: #fff;
        font-size: 20px;
        font-weight: 700;
    }

    .ur-filters-modal .modal-header p {
        margin: 0;
        font-size: 12.5px;
        color: rgba(255, 255, 255, .72);
    }

    .ur-filters-modal .modal-header .close {
        color: #fff;
        opacity: .75;
        text-shadow: none;
        margin-top: 2px;
    }

    .ur-filters-modal .modal-header .close:hover {
        opacity: 1;
    }

    .ur-filters-modal .modal-body {
        padding: 26px 28px 8px;
    }

    /* Grouped field sections (Basic Details / Location / Background / etc.) */
    .ur-filter-section {
        margin-bottom: 24px;
    }

    .ur-filter-section__title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: var(--ur-gold);
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--ur-border);
    }

    .ur-filter-section__title i {
        font-size: 13px;
        width: 14px;
        text-align: center;
    }

    .ur-search-page .modal-body label.text-uppercase i {
        color: var(--ur-gold);
        margin-right: 5px;
        width: 13px;
        text-align: center;
        font-size: 11px;
    }

    .ur-filters-modal .modal-footer {
        background: var(--ur-bg);
        border-top: 1px solid var(--ur-border);
        padding: 16px 28px;
        justify-content: space-between;
        gap: 10px;
    }

    .ur-filters-modal .modal-footer .ur-btn-outline {
        flex: 0 0 auto;
        padding: 0 20px;
        border-color: var(--ur-border);
        color: var(--ur-text-muted) !important;
    }

    .ur-filters-modal .modal-footer .ur-btn-outline:hover {
        border-color: var(--ur-gold);
        color: var(--ur-green) !important;
    }

    .ur-filters-modal .modal-footer .ur-btn-solid {
        flex: 0 0 auto;
        padding: 0 28px;
    }

    @media (max-width: 480px) {
        .ur-filters-modal .modal-footer {
            flex-direction: column-reverse;
        }

        .ur-filters-modal .modal-footer .ur-btn-outline,
        .ur-filters-modal .modal-footer .ur-btn-solid {
            width: 100%;
        }
    }

    /* ===== Dashboard-home summary (logged-in members only): welcome
       banner, completeness ring, quick actions, "Recommended Matches"
       preview grid — sits above the guest-visible search hero/widget
       below. ===== */
    .ur-dash-home__welcome {
        font-family: 'Playfair Display', serif;
        color: var(--ur-green);
        font-weight: 700;
        font-size: 28px;
        margin: 0 0 20px;
    }

    .ur-dash-home__completeness {
        display: flex;
        align-items: center;
        gap: 22px;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 16px;
        padding: 22px 26px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(15, 46, 36, .06);
    }

    .ur-dash-ring {
        flex: 0 0 auto;
        width: 92px;
        height: 92px;
        border-radius: 50%;
        background: conic-gradient(var(--ur-gold) calc(var(--pct) * 1%), var(--ur-bg) 0);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ur-dash-ring__hole {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 19px;
        color: var(--ur-green);
    }

    .ur-dash-home__completeness-text h3 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        color: var(--ur-text);
    }

    .ur-dash-home__completeness-text p {
        margin: 0;
        font-size: 13px;
        color: var(--ur-text-muted);
    }

    .ur-dash-home__actions {
        display: flex;
        gap: 14px;
        margin-bottom: 28px;
    }

    .ur-dash-home__btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 52px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: all .2s ease;
    }

    .ur-dash-home__btn--solid {
        background: var(--ur-gold);
        color: #fff !important;
    }

    .ur-dash-home__btn--solid:hover {
        background: #B07C3D;
    }

    .ur-dash-home__btn--outline {
        background: #fff;
        color: var(--ur-green) !important;
        border-color: var(--ur-border);
    }

    .ur-dash-home__btn--outline:hover {
        background: var(--ur-bg);
    }

    @media (max-width: 575.98px) {
        .ur-dash-home__actions {
            flex-direction: column;
        }

        .ur-dash-home__completeness {
            flex-direction: column;
            text-align: center;
        }
    }

    .ur-dash-home__recommended-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .ur-dash-home__recommended-head h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--ur-text);
        margin: 0;
    }

    .ur-dash-home__recommended-head a {
        font-size: 13px;
        font-weight: 700;
        color: var(--ur-gold);
    }

    .ur-dash-home__recommended-head a:hover {
        color: var(--ur-green);
    }

    .ur-dash-home__recommended {
        margin: 8px 0 36px;
    }

    /* Heading for the real search-results area — only rendered once the
       member has actually searched (see searchdata.blade.php's
       $hasSearched gate), so it never sits next to/duplicates the
       "Recommended Matches" preview above it. */
    .ur-search-results__heading {
        font-size: 18px;
        font-weight: 700;
        color: var(--ur-text);
        margin: 0 0 14px;
    }

    /* Loading state shown in #search-data while an AJAX search is in
       flight (see refreshProfiles() below) — otherwise that area just sits
       blank between the request firing and the response landing. */
    .ur-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        padding: 70px 20px;
        color: var(--ur-text-muted);
    }

    .ur-loading__spinner {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 4px solid var(--ur-border);
        border-top-color: var(--ur-gold);
        animation: ur-spin .8s linear infinite;
    }

    @keyframes ur-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .ur-loading p {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .ur-search-page .modal-body label.text-uppercase {
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

</style>
@auth
{{--
    Dashboard-home summary — welcome banner, profile-completeness ring,
    quick actions, and a "Recommended Matches" preview grid. Logged-in
    members only; $completeness/$recommendedMatches/$viewerUser come from
    HomeController::search() (null/empty for guests, so this never renders
    for them). "View All" and the recommended cards use the exact same
    member-card partial/CSS as the results grid further down the page.
--}}
<section class="ur-search-page">
    <div class="container">
        <h1 class="ur-dash-home__welcome">Welcome Back, {{ $viewerUser->first_name }}</h1>

        @if(!empty($completeness))
        <div class="ur-dash-home__completeness">
            <div class="ur-dash-ring" style="--pct: {{ $completeness['percent'] }};">
                <div class="ur-dash-ring__hole"><span>{{ $completeness['percent'] }}%</span></div>
            </div>
            <div class="ur-dash-home__completeness-text">
                <h3>Your Profile is {{ $completeness['percent'] }}% Complete</h3>
                <p>A complete profile gets you better matches.</p>
            </div>
        </div>
        @endif

        <div class="ur-dash-home__actions">
            <button type="button" class="ur-dash-home__btn ur-dash-home__btn--solid" data-toggle="modal" data-target="#search_filters_modal"><i class="fa fa-search"></i> Search Profiles</button>
            <a href="{{ route('member.recommended-matches') }}" class="ur-dash-home__btn ur-dash-home__btn--outline"><i class="fa fa-users"></i> View Recommended Matches</a>
        </div>

        {{-- Hidden once a real search has actually run ($hasSearched — see
             HomeController::search()), not just via the AJAX "Apply Filters"
             flow (handled client-side below) but also a plain full-page POST
             like the homepage's own search form submits directly to this
             route — there's no JS involved in that case, so it has to be
             gated here too or this preview and the real results would both
             render at once. --}}
        @if(empty($hasSearched) && !empty($recommendedMatches) && $recommendedMatches->count() > 0)
        <div class="ur-dash-home__recommended" id="ur_recommended_matches">
            <div class="ur-dash-home__recommended-head">
                <h3>Recommended Matches For You</h3>
                <a href="{{ route('member.recommended-matches') }}">View All <i class="fa fa-angle-right"></i></a>
            </div>
            <div class="member-results">
                @foreach($recommendedMatches as $member)
                    @include('member.partials.member-card', ['member' => $member])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endauth
<section class="ur-search-page">
    <div class="container">
        @guest
        {{-- Guests don't get the welcome banner above, so they need their own
             visible trigger for the same filters popup. --}}
        <div class="text-center" style="padding: 24px 0 6px;">
            <button type="button" class="ur-dash-home__btn ur-dash-home__btn--solid" style="max-width: 280px; margin: 0 auto; display: inline-flex;" data-toggle="modal" data-target="#search_filters_modal"><i class="fa fa-search"></i> Search Profiles</button>
        </div>
        @endguest

        {{-- This form has no visible content of its own on the page — every
             field lives inside #search_filters_modal, opened by the "Search
             Profiles" button above (guests) or in the welcome banner (members).
             Unchanged field names/ids from before. --}}
        <form class="form-default" id="search_form" data-toggle="validator" role="form" action="{{route('searchresults')}}" method="post">
            @csrf
            <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
            <input type="hidden" id="pagesize" name="pagesize" value="{{ $pageSize }}"/>

            <div class="modal fade" id="search_filters_modal" tabindex="-1" role="dialog" aria-labelledby="search_filters_modal_label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content ur-filters-modal">
                        <div class="modal-header">
                            <div class="ur-filters-modal__title-wrap">
                                <h5 class="modal-title" id="search_filters_modal_label">Search Filters</h5>
                                <p>Narrow down your matches — combine as many as you like</p>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="ur-filter-section">
                                <div class="ur-filter-section__title"><i class="fa fa-venus-mars"></i> Looking For</div>
                                <div class="ur-toggle-group" style="max-width: 320px;">
                                    <input type="radio" name="gender" id="bride" value="female" required="required" {{ ($selectedGender ?? request()->gender)=='female'?'checked="checked"':''}} />
                                    <label for="bride" class="ur-toggle-pill">Bride</label>
                                    <input type="radio" name="gender" id="groom" value="male" required="required" {{ ($selectedGender ?? request()->gender)=='male'?'checked="checked"':''}} />
                                    <label for="groom" class="ur-toggle-pill">Groom</label>
                                </div>
                            </div>

                            <div class="ur-filter-section">
                                <div class="ur-filter-section__title"><i class="fa fa-sliders"></i> Basic Details</div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-birthday-cake"></i> Age Range (From)</label>
                                            <select name="aged_from" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="From" data-hide-disabled="true">
                                                <option value="">From</option>
                                                @for ($i=18; $i<=75; $i++) <option {{request()->aged_from==($i<10?"0".$i:$i)?'selected="selected"':''}}>{{$i<10?"0".$i:$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-birthday-cake"></i> Age Range (To)</label>
                                            <select name="aged_to" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="To" data-hide-disabled="true">
                                                <option value="">To</option>
                                                @for ($i=18; $i<=75; $i++) <option {{request()->aged_to==($i<10?"0".$i:$i)?'selected="selected"':''}}>{{$i<10?"0".$i:$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-briefcase"></i> Profession</label>
                                            <input type="text" class="form-control form-control-sm" name="profession" id="filter_profession" value="{{request()->profession?request()->profession:''}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-heart-o"></i> Marital Status</label>
                                            <select name="marital_status" onchange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" data-hide-disabled="true">
                                                <option value="">Choose a marital status</option>
                                                @foreach($maritalstatuses as $maritalstatus)
                                                <option value="{{$maritalstatus->dataid}}" {{request()->marital_status==$maritalstatus->dataid?'selected="selected"':''}}>{{$maritalstatus->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ur-filter-section">
                                <div class="ur-filter-section__title"><i class="fa fa-map-marker"></i> Location</div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-globe"></i> Country</label>
                                            <select name="country" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/1', $('#city'), '{{request()->city}}');" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" data-hide-disabled="true">
                                                <option value="">Choose a country</option>
                                                @foreach($countries as $country)
                                                <option value="{{$country->dataid}}" {{request()->country==$country->dataid?'selected="selected"':''}}>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-map-marker"></i> City</label>
                                            <select id="city" name="city" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" data-hide-disabled="true">
                                                <option value="">Choose a country first</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ur-filter-section">
                                <div class="ur-filter-section__title"><i class="fa fa-users"></i> Background</div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-book"></i> Religion</label>
                                            <select name="religion" onchange="(this.value,this)" class="form-control form-control-sm selectpicker s_religion" data-placeholder="Choose a religion" data-hide-disabled="true">
                                                <option value="">Choose a religion</option>
                                                @foreach($religions as $religion)
                                                <option value="{{$religion->dataid}}" {{request()->religion==$religion->dataid?'selected="selected"':''}}>{{$religion->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-users"></i> Caste</label>
                                            <select name="caste" class="form-control form-control-sm selectpicker" data-placeholder="Choose a caste" data-hide-disabled="true">
                                                <option value="">Choose a caste</option>
                                                @foreach($caste as $cst)
                                                <option value="{{$cst->dataid}}" {{request()->caste==$cst->dataid?'selected="selected"':''}}>{{$cst->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-comments-o"></i> Mother Tongue</label>
                                            <select name="mother_tongue" onchange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a language" data-hide-disabled="true">
                                                <option value="">Choose a mother tongue</option>
                                                @foreach($mothertongues as $mothertongue)
                                                <option value="{{$mothertongue->dataid}}" {{request()->mother_tongue==$mothertongue->dataid?'selected="selected"':''}}>{{$mothertongue->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ur-filter-section">
                                <div class="ur-filter-section__title"><i class="fa fa-search-plus"></i> More Filters</div>
                                @auth
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-id-card-o"></i> Member Id</label>
                                            <input type="text" class="form-control form-control-sm" name="member_id" id="filter_member_id" value="{{request()->member_id?request()->member_id:''}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-user"></i> Name</label>
                                            <input type="text" class="form-control form-control-sm" name="first_name" id="filter_first_name" value="{{request()->first_name?request()->first_name:''}}">
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group has-feedback">
                                            <label for="" class="text-uppercase"><i class="fa fa-user"></i> Name</label>
                                            <input type="text" class="form-control form-control-sm" name="first_name" id="filter_first_name" value="{{request()->first_name?request()->first_name:''}}">
                                        </div>
                                    </div>
                                </div>
                                @endauth
                                <div class="form-group has-feedback ur-check-row">
                                    <input type="checkbox" name="withpics" id="withpics" value="true" {{request()->withpics==true?'checked="checked"':''}} />
                                    <label for="withpics" class="text-uppercase mb-0"><i class="fa fa-camera"></i> With Images Only</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('searchresults') }}" class="ur-btn-outline"><i class="fa fa-refresh"></i> Reset Filters</a>
                            {{-- No data-dismiss here on purpose — combining it with type="submit"
                                 races against the form's own submit handler in some browsers/Bootstrap
                                 versions (the click can get swallowed by the dismiss/fade transition
                                 before the submit event fires). The submit handler below closes the
                                 modal itself once it has actually kicked off the search. --}}
                            <button type="submit" id="apply_filters_button" class="ur-btn-solid"><i class="fa fa-check"></i> Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>

        <div class="row">
            <div class="col-12">
                <div class="block-wrapper" id="result">
                    {{-- "Show X per page" toolbar removed — results are a
                         small, always-populated set (see
                         HomeController::search()'s recommended-matches
                         fallback), so a page-size picker isn't useful right
                         now. Kept as a hidden input (fixed at 10) so
                         refreshProfiles()/renderPage() below still work
                         unchanged. --}}
                    <input type="hidden" id="selpagesize" value="10">
                    <div id="search-data">
                    @yield('search-data')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        //$('.carousel').carousel();

        // preload city select
        @if(!empty(request()->country))
        loadSelect('{{url('cities')}}', '{{request()->country}}/1', $('#city'), '{{request()->city}}');
        @endif
    });

    $("#search_form").on("submit", function() {
        var elem = $("#apply_filters_button");
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);
        // Close the modal ourselves (see the button's comment above for why
        // it no longer carries data-dismiss) and hide the recommended-matches
        // preview — a real search is about to replace it. See
        // HomeController::search()'s $hasSearched, which is what makes the
        // results area below actually render once this submits.
        $('#search_filters_modal').modal('hide');
        $('#ur_recommended_matches').hide();
        refreshProfiles(true);
        elem.html(oldHtml);
        elem.prop('disabled', false);
        return false;
    });

    function refreshProfiles(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        $("#pagesize").val($("#selpagesize").val());
        // renderPage()'s own "loading" text only ever gets set inside its
        // success callback — i.e. after the response has already arrived —
        // so without this, #search-data just sits blank while the request
        // is in flight. Set a real loader here, before the request fires;
        // renderPage() overwrites it with the actual results once they land.
        $("#search-data").html('<div class="ur-loading"><div class="ur-loading__spinner"></div><p>Finding your matches&hellip;</p></div>');
        renderPage("{{url('member/searchresults/1')}}", "post", $("#search_form").serialize(), $("#search-data"));
    }
</script>
@endsection
