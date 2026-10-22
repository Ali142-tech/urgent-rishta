@extends('layouts.admin.master')
@section('dashboard-title', 'Member Profiles')
@push('styles')
{{-- Needed for the mobile-only card view in memberdata.blade.php, which
     reuses member-card__* classes (not the outer boxed .member-card). --}}
<link rel="stylesheet" href="/css/ur-member-card.css?v={{ filemtime(public_path('css/ur-member-card.css')) }}">
@endpush
@section('admin-content')
<div class="ur-admin-header">
    <h2>Member Profiles</h2>
</div>

<style>
    /* Member Profiles filter panel */
    #controls-form.ur-admin-filters { background: linear-gradient(180deg, #FBF9F3, #F6F3EA); border: 1px solid #E7E2D6; border-radius: 14px; padding: 14px; gap: 12px; margin-bottom: 14px; }
    .ur-pf-head { display: flex; align-items: center; justify-content: space-between; min-height: 22px; }
    .ur-pf-head__title { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: #123A2E; }
    .ur-pf-head__title i { color: #C89B3C; }
    .ur-pf-head__title b, .ur-pf-more-toggle em { font-style: normal; font-size: 11px; font-weight: 800; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; background: #123A2E; color: #fff; display: inline-flex; align-items: center; justify-content: center; letter-spacing: 0; }
    #controls-form .ur-admin-filters__clear { margin: 0; padding: 4px 10px; border-radius: 999px; background: #FDECEC; color: #B5413A; font-size: 11.5px; }
    #controls-form .ur-admin-filters__clear:hover { text-decoration: none; background: #FADADA; }

    .ur-pf-search { position: relative; }
    .ur-pf-search > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9AA39E; font-size: 14px; pointer-events: none; }
    #controls-form .ur-pf-search .form-control { height: 44px; padding-left: 40px; border-radius: 12px; background: #fff; border: 1px solid #E2DDCF; font-size: 13.5px; }
    #controls-form .ur-pf-search .form-control:focus { border-color: #123A2E; box-shadow: 0 0 0 3px rgba(18,58,46,.10); }

    /* the labels stay visible (small caps) so every dropdown says what it is */
    #controls-form .ur-admin-filters__label { position: static !important; width: auto !important; height: auto !important; margin: 0 0 4px !important; clip: auto !important; overflow: visible !important; font-size: 10.5px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; color: #6B7570; white-space: nowrap; }
    #controls-form .ur-admin-filters__row3 { gap: 10px; }
    #controls-form .form-group { margin-bottom: 0; }

    /* bootstrap-select look */
    #controls-form .bootstrap-select { width: 100% !important; }
    #controls-form .bootstrap-select > .dropdown-toggle { height: 38px; border-radius: 10px; background: #fff; border: 1px solid #E2DDCF; font-size: 13px; font-weight: 600; color: #123A2E; padding: 0 28px 0 12px; box-shadow: none !important; display: flex; align-items: center; }
    #controls-form .bootstrap-select > .dropdown-toggle .filter-option { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #controls-form .bootstrap-select > .dropdown-toggle:hover { border-color: #C9C2AF; }
    #controls-form .bootstrap-select.show > .dropdown-toggle, #controls-form .bootstrap-select > .dropdown-toggle:focus { border-color: #123A2E; box-shadow: 0 0 0 3px rgba(18,58,46,.10) !important; outline: 0 !important; }
    #controls-form .bootstrap-select.has-value > .dropdown-toggle { border-color: #1C9A6C; background: #F1FAF5; color: #0F5B43; }
    #controls-form .bootstrap-select .dropdown-menu { border-radius: 12px; border: 1px solid #E7E2D6; box-shadow: 0 14px 34px rgba(15,46,36,.16); padding: 6px; min-width: 220px; }
    #controls-form .bootstrap-select .dropdown-menu .bs-searchbox .form-control { height: 36px; border-radius: 8px; font-size: 13px; }
    #controls-form .bootstrap-select .dropdown-menu li a, #controls-form .bootstrap-select .dropdown-item { border-radius: 8px; font-size: 13px; padding: 7px 10px; }
    #controls-form .bootstrap-select .dropdown-item.active, #controls-form .bootstrap-select .dropdown-item:active { background: #123A2E; color: #fff; }

    #controls-form select.form-control { height: 38px; border-radius: 10px; background-color: #fff; border: 1px solid #E2DDCF; font-size: 13px; font-weight: 600; color: #123A2E; }

    /* More filters: collapsible, two columns */
    .ur-pf-more-toggle { display: flex; align-items: center; justify-content: space-between; width: 100%; background: #fff; border: 1px dashed #D9D2BE; border-radius: 10px; padding: 9px 12px; font-size: 12.5px; font-weight: 700; color: #123A2E; cursor: pointer; }
    .ur-pf-more-toggle:hover { border-color: #123A2E; }
    .ur-pf-more-toggle span { display: inline-flex; align-items: center; gap: 8px; }
    .ur-pf-more-toggle span > i { color: #C89B3C; }
    .ur-pf-more-toggle__chev { transition: transform .2s; color: #6B7570; transform: rotate(180deg); }
    .ur-pf-more-toggle.is-open .ur-pf-more-toggle__chev { transform: rotate(0deg); }
    .ur-pf-more.is-collapsed { display: none; }
    .ur-pf-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 10px; }
    @media (max-width: 767px) { .ur-pf-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
<div class="ur-admin-list-detail">
    <div class="ur-admin-list-detail__list">
        {{-- The compact grid (no visible labels, Entries/Show Only tucked
             away) is a DESKTOP-only simplification (≥768px, see ur-admin.css)
             matching the list+detail redesign — mobile keeps the classic
             labeled, fully-featured filter form since mobile wasn't meant to
             change here at all. One shared <form> either way (so there's a
             single source of truth for refreshProfiles()'s serialize()) —
             the desktop/mobile difference is CSS-only (visibility, layout),
             never removed from the DOM, so nothing needs a hidden fallback. --}}
        <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
            @csrf
            <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>

            <div class="ur-pf-head">
                <span class="ur-pf-head__title"><i class="fa fa-sliders"></i> Filters <b id="pf-count" style="display:none;">0</b></span>
                <button type="button" id="clear-filters-btn" class="ur-admin-filters__clear" onclick="javascript:clearProfileFilters();" style="display:none;">
                    <i class="fa fa-times-circle"></i> Clear all
                </button>
            </div>
            <div class="form-group form-group--grow ur-pf-search">
                <i class="fa fa-search"></i>
                <input type="search" name="term" class="form-control form-control-sm" placeholder="Search name, email, ID, profession…" autocomplete="off" onkeyup="javascript:refreshProfiles(true);" value="" />
            </div>
            <div class="ur-admin-filters__row3">
                <div class="form-group">
                    <label for="gender" class="ur-admin-filters__label">Gender</label>
                    <select name="gender" class="form-control form-control-sm selectpicker" data-placeholder="Gender" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                        <option value="">Gender</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="package" class="ur-admin-filters__label">Package</label>
                    <select name="package" class="form-control form-control-sm selectpicker" data-placeholder="Package" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                        <option value="">Package</option>
                        <option value="null">Unassigned</option>
                        @foreach($packages as $package)
                        <option value="{{$package->dataid}}">{{$package->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status" class="ur-admin-filters__label">Status</label>
                    <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Status" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                        <option value="">Status</option>
                        <option value="1">Active</option>
                        <option value="0">Pending</option>
                    </select>
                </div>
            </div>
            <button type="button" class="ur-pf-more-toggle" id="more-filters-toggle" onclick="javascript:toggleMoreFilters();"><span><i class="fa fa-plus-circle"></i> More filters <em id="pf-more-count" style="display:none;">0</em></span><i class="fa fa-angle-up ur-pf-more-toggle__chev"></i></button>
            <div class="ur-pf-more" id="more-filters">
                <div class="ur-pf-grid">
                    <div class="form-group">
                        <label for="country" class="ur-admin-filters__label">Country</label>
                        <select name="country" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Country</option>
                            @foreach($filterCountries as $opt)<option value="{{ $opt->dataid }}">{{ $opt->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="profession" class="ur-admin-filters__label">Profession</label>
                        <select name="profession" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Profession</option>
                            @foreach($filterProfessions as $opt)<option value="{{ $opt }}">{{ $opt }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="religion" class="ur-admin-filters__label">Religion</label>
                        <select name="religion" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Religion</option>
                            @foreach($filterReligions as $opt)<option value="{{ $opt->dataid }}">{{ $opt->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="caste" class="ur-admin-filters__label">Caste</label>
                        <select name="caste" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Caste</option>
                            @foreach($filterCastes as $opt)<option value="{{ $opt->dataid }}">{{ $opt->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="marital_status" class="ur-admin-filters__label">Marital status</label>
                        <select name="marital_status" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Marital status</option>
                            @foreach($filterMaritalStatuses as $opt)<option value="{{ $opt->dataid }}">{{ $opt->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="education" class="ur-admin-filters__label">Education</label>
                        <select name="education" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Education</option>
                            @foreach($filterEducations as $opt)<option value="{{ $opt->dataid }}">{{ $opt->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="age_from" class="ur-admin-filters__label">Age from</label>
                        <select name="age_from" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Age from</option>
                            @for($i = 18; $i <= 70; $i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="age_to" class="ur-admin-filters__label">Age to</label>
                        <select name="age_to" class="form-control form-control-sm selectpicker" data-live-search="true" data-size="8" data-hide-disabled="true" onchange="javascript:if(!suppressFilterChange)refreshProfiles(true);">
                            <option value="">Age to</option>
                            @for($i = 18; $i <= 70; $i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
                        </select>
                    </div>
                </div>
            </div>
            {{-- Entries / Show Only — visible on mobile (classic layout),
                 hidden on desktop (≥768px) to match the compact mockup. --}}
            <div class="ur-admin-filters__extra">
                <div class="form-group">
                    <label for="pagesize">Entries</label>
                    <select name="pagesize" class="form-control form-control-sm" onchange="javascript:refreshProfiles(true);">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group form-group--grow">
                    <label>Show Only</label>
                    <div>
                        <div class="form-check form-check-inline"><input type="radio" checked="checked" name="showonly" value="all" class="form-check-input" onchange="javascript:$('#within').hide();refreshProfiles(true);" /><label class="form-check-label"> All Profiles </label></div>
                        <div class="form-check form-check-inline"><input type="radio" name="showonly" value="updated" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Updated </label></div>
                        <div class="form-check form-check-inline"><input type="radio" name="showonly" value="created" class="form-check-input" onchange="javascript:$('#within').show();" /><label class="form-check-label"> Created </label></div>
                        <span id="within" style="display: none"> within last
                            <select name="showwithin" class="form-control form-control-sm d-inline-block" style="width:auto;" onchange="javascript:refreshProfiles(true);">
                                <option value="">Select...</option>
                                <option>1</option>
                                <option>2</option>
                                <option>4</option>
                                <option>6</option>
                                <option>8</option>
                                <option>10</option>
                            </select> week(s)
                        </span>
                    </div>
                </div>
            </div>
        </form>

        <div id="member-data">
        @yield('member-data')
        </div>
    </div>

    {{-- Desktop only (see ur-admin.css responsive block) — on mobile the
         compact list rows aren't rendered at all (member-card design is
         used instead, see memberdata.blade.php), so this panel has nothing
         to be selected from and is hidden entirely. --}}
    <div class="ur-admin-list-detail__panel" id="profile-detail-panel">
        @include('admin.dashboard.profile-detail-panel', ['member' => $selectedMember ?? null])
    </div>
</div>
<script type="text/javascript">
    function refreshProfiles(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage?newPage:1);
        renderPage("{{url('admin/profiles/refresh')}}", "post", $("#controls-form").serialize(), $("#member-data"));

        // Keep the address bar's ?page= in sync with what's actually being
        // shown. Without this, pagination clicks swap the list via AJAX but
        // leave the URL pointing at whatever page was loaded initially, so a
        // refresh/bookmark (and the "Change Package" link's return-page,
        // which reads this same URL) would disagree with what's on screen.
        var page = parseInt($('#pagerequested').val(), 10) || 1;
        var url = new URL(window.location.href);
        if (page > 1) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        window.history.replaceState(null, '', url);

        updateClearFiltersVisibility();
    }

    var MORE_FILTERS = ['country', 'profession', 'religion', 'caste', 'marital_status', 'education', 'age_from', 'age_to'];

    /** Filters that are set get a green outline, and the counters beside "Filters" / "More filters" say how many. */
    function paintFilterState() {
        var form = document.getElementById('controls-form');
        if (!form) return;
        var all = ['gender', 'package', 'status'].concat(MORE_FILTERS), active = 0, more = 0;
        all.forEach(function (n) {
            var el = form[n], on = !!(el && el.value);
            $(el).closest('.bootstrap-select').toggleClass('has-value', on);
            if (on) { active++; if (MORE_FILTERS.indexOf(n) !== -1) more++; }
        });
        if (form.term.value) active++;
        var c = document.getElementById('pf-count'), mc = document.getElementById('pf-more-count');
        if (c) { c.textContent = active; c.style.display = active ? '' : 'none'; }
        if (mc) { mc.textContent = more; mc.style.display = more ? '' : 'none'; }
    }

    function toggleMoreFilters() {
        var box = document.getElementById('more-filters'), btn = document.getElementById('more-filters-toggle');
        var open = box.classList.toggle('is-collapsed') === false;
        btn.classList.toggle('is-open', open);
        try { localStorage.setItem('urProfilesMoreFilters', open ? '1' : '0'); } catch (e) {}
    }
    /** Show "Clear Filters" only once search/gender/package/status actually have something set. */
    function updateClearFiltersVisibility() {
        var form = document.getElementById('controls-form');
        var btn = document.getElementById('clear-filters-btn');
        if (!form || !btn) return;
        var hasFilter = !!(
            form.term.value ||
            form.gender.value ||
            form.package.value ||
            form.status.value ||
            MORE_FILTERS.some(function (n) { return form[n].value; })
        );
        btn.style.display = hasFilter ? '' : 'none';
        paintFilterState();
    }

    /** Guards the selects' onchange handlers while clearProfileFilters() resets
        them one at a time — without it, each reset fires its own refreshProfiles()
        call (4 total instead of 1) since .trigger('change') is what makes Select2
        actually repaint the widget back to its placeholder. */
    var suppressFilterChange = false;

    /** Resets search + the three dropdowns and reloads the list from scratch. */
    function clearProfileFilters() {
        var form = document.getElementById('controls-form');
        form.term.value = '';
        suppressFilterChange = true;
        $(form.gender).val('').trigger('change');
        $(form.package).val('').trigger('change');
        $(form.status).val('').trigger('change');
        MORE_FILTERS.forEach(function (n) { $(form[n]).val('').trigger('change'); });
        suppressFilterChange = false;
        refreshProfiles(true);
    }

    $(document).ready(function () {
        try { if (localStorage.getItem('urProfilesMoreFilters') === '0') toggleMoreFilters(); else document.getElementById('more-filters-toggle').classList.add('is-open'); } catch (e) { document.getElementById('more-filters-toggle').classList.add('is-open'); }
        updateClearFiltersVisibility();
    });

    /** Clicking a row in the list — AJAX-loads that member's summary into the right-hand panel. */
    function loadMemberDetail(dataid, rowElem) {
        var panel = document.getElementById('profile-detail-panel');
        if (!panel) return;

        document.querySelectorAll('.ur-admin-list-item.is-active').forEach(function (el) {
            el.classList.remove('is-active');
        });
        if (rowElem) rowElem.classList.add('is-active');

        panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-refresh fa-spin"></i> Loading...</div>';

        $.ajax({
            type: 'get',
            url: "{{ url('admin/profile/panel') }}/" + dataid,
            success: function (result) {
                if (result.code == '200') {
                    panel.innerHTML = result.html;
                } else {
                    panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-exclamation-triangle"></i> Could not load this member.</div>';
                }
            },
            error: function () {
                panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-exclamation-triangle"></i> Could not load this member.</div>';
            }
        });

        // Bookmarkable/shareable — matches the existing ?page= sync pattern in refreshProfiles().
        var url = new URL(window.location.href);
        url.searchParams.set('selected', dataid);
        window.history.replaceState(null, '', url);
    }

    function toggleActive(elem, id) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "post",
            url: "{{url('admin/profile/toggle/')}}" + "/" + id,
            data: { '_token': '{{ csrf_token() }}' },
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message;
                if (result.code == "200") {
                    var active = result.active;
                    clickHighlight($("#active_label_" + id), active == 1 ? "Active" : "Pending",
                        $("#active_" + id), active == 1 ? "toggle-on" : "toggle-off", "Make " + (active == 1 ? "Inactive" : "Active"), active == 1);
                    showAlert('success', message, 3000);
                } else showAlert('danger', message, 5000);
            }
        })
    }

    function deleteProfile(elem, id) {
        swalConfirm("Delete Profile?", "Are you sure you want to delete this profile? You will not be able to revert this!", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "delete",
                url: "{{ url('admin/profile')}}" + "/" + id,
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    if (result.code == '200') {
                        swalAlert("success", "Success", "Profile deleted successfully.", () => {
                            $("#block_"+id).remove();
                            // Deleting from the detail panel (data-dataid on
                            // .ur-detail-panel) leaves the panel showing a
                            // now-gone member — reset it to the empty state.
                            var panel = document.getElementById('profile-detail-panel');
                            var panelInner = panel ? panel.querySelector('.ur-detail-panel') : null;
                            if (panelInner && panelInner.dataset.dataid === id) {
                                panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-user"></i> Select a member from the list to see their details.</div>';
                            }
                        });
                    } else {
                        swal("error", "Error", "An error was encountered - " + result.message + ". Please contact admin of this website.");
                    }
                }
            });
        });
    }

    function resendVerificationEmail(elem, id) {
        swalConfirm("Resend Verification Email?", "Are you sure you want to resend verification email to this profile? This will override any previous verification email sent.", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "post",
                url: "{{ url('admin/profile/resendemail')}}" + "/" + id,
                data: { '_token': '{{ csrf_token() }}' },
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message;
                    if (result.code == '200') {
                        showAlert('success', message, 3000);
                    } else showAlert('danger', message, 5000);
                }
            });
        });
    }

    function sendPasswordResetEmail(elem, id) {
        swalConfirm("Reset Password?", "Are you sure you want to request password reset for this profile?", () => {
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            $.ajax({
                type: "post",
                url: "{{ url('admin/profile/requestreset')}}" + "/" + id,
                data: { '_token': '{{ csrf_token() }}' },
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message;
                    if (result.code == '200') {
                        showAlert('success', message, 3000);
                    } else showAlert('danger', message, 5000);
                }
            });

        });
    }

</script>
@endsection
