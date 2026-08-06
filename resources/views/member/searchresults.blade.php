@extends('layouts.master')
@section('main-content')
<?php use App\User; ?>
<style>
    @media (max-width: 576px) {
        .listing-image {
            height: 330px !important;
        }
    }
</style>
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">Search Results - Active Members</h2>
            </div>
        </div>
    </div>
</section>
<section class="slice sct-color-1">
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
