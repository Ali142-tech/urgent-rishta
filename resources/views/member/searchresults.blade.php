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

    /* ===== Search results member cards ===== */
    .member-results {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
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
        aspect-ratio: 4 / 5;
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
        grid-template-columns: 1fr 1fr;
        gap: 0 14px;
        list-style: none;
        padding: 10px 0 4px;
        margin: 0;
        border-top: 1px dashed var(--ur-border, #E7E2D6);
    }

    .member-card__details li {
        display: flex;
        flex-direction: column;
        padding: 6px 0;
        border-bottom: 1px dashed var(--ur-border, #E7E2D6);
        font-size: .78rem;
        overflow: hidden;
    }

    .member-card__details li span {
        color: var(--ur-text-muted, #6B7570);
        text-transform: uppercase;
        font-size: .68rem;
        letter-spacing: .3px;
    }

    .member-card__details li b {
        color: var(--ur-text, #1C2321);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-card__footer {
        display: flex;
        border-top: 1px solid var(--ur-border, #E7E2D6);
        margin-top: 12px;
    }

    .member-card__footer a {
        flex: 1;
        text-align: center;
        padding: 12px 6px;
        font-size: .78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        cursor: pointer;
        color: var(--ur-text, #1C2321);
        transition: background-color .2s ease, color .2s ease;
    }

    .member-card__footer a:first-child {
        border-right: 1px solid var(--ur-border, #E7E2D6);
    }

    .member-card__footer a:hover {
        background: var(--ur-bg, #F6F4EF);
    }

    .member-card__footer a.is-interest {
        color: var(--ur-green, #123A2E);
    }

    .member-card__footer a.is-interest:hover {
        background: #EAF1EE;
    }

    @media (max-width: 991px) and (min-width: 768px) {
        .member-card__details {
            grid-template-columns: 1fr;
        }
    }
</style>
<section class="page-title page-title--style-1 ur-search-page">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Search Results - Active Members</h2>
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
                                                <div class="radio radio-primary">
                                                    <input type="radio" name="gender" id="bride" value="female" required="required" {{request()->gender=='female'?'checked="checked"':''}} />
                                                    <label for="bride" class="pr-3">Bride</label>
                                                    <input type="radio" name="gender" id="groom" value="male" required="required" {{request()->gender=='male'?'checked="checked"':''}} />
                                                    <label for="groom" class="pr-3">Groom</label>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback">
                                                <input type="checkbox" name="withpics" value="true" {{request()->withpics==true?'checked="checked"':''}} />
                                                <label for="withpics" class="text-uppercase"> With images</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">Age From</label>
                                                <select name="aged_from" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose starting age" data-hide-disabled="true">
                                                    <option value="">Choose from age</option>
                                                    @for ($i=18; $i<=75; $i++) <option {{request()->aged_from==($i<10?"0".$i:$i)?'selected="selected"':''}}>{{$i<10?"0".$i:$i}}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-feedback">
                                                <label for="" class="text-uppercase">To</label>
                                                <select name="aged_to" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose starting age" data-hide-disabled="true">
                                                    <option value="">Choose to age</option>
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
                                    <button type="submit" id="search_button" class="btn btn-block btn-base-1 mt-2 z-depth-2-bottom">Search</button>
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
                        <form id="controls-form" action="javascript:void();">
                            <div class="col-sm-12 col-md-12">
                                <span><label>Number of entries: <select id="selpagesize" name="selpagesize" aria-controls="datatable" class="custom-select custom-select-sm form-control form-control-sm" onchange="javascript:refreshProfiles(true);">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select></label></span>
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
