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

            <div class="form-group form-group--grow">
                <label for="term" class="ur-admin-filters__label">Search</label>
                <input type="search" name="term" class="form-control form-control-sm" placeholder="Search members..." autocomplete="off" onkeyup="javascript:refreshProfiles(true);" value="" />
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
            <button type="button" id="clear-filters-btn" class="ur-admin-filters__clear" onclick="javascript:clearProfileFilters();" style="display:none;">
                <i class="fa fa-times-circle"></i> Clear Filters
            </button>

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

    /** Show "Clear Filters" only once search/gender/package/status actually have something set. */
    function updateClearFiltersVisibility() {
        var form = document.getElementById('controls-form');
        var btn = document.getElementById('clear-filters-btn');
        if (!form || !btn) return;
        var hasFilter = !!(
            form.term.value ||
            form.gender.value ||
            form.package.value ||
            form.status.value
        );
        btn.style.display = hasFilter ? '' : 'none';
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
        suppressFilterChange = false;
        refreshProfiles(true);
    }

    $(document).ready(function () {
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

    function toggleTeamMember(elem, id) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "post",
            url: "{{url('admin/profile/toggle-team-member/')}}" + "/" + id,
            data: { '_token': '{{ csrf_token() }}' },
            success: function(result) {
                elem.prop('disabled', false);

                var message = result.message;
                if (result.code == "200") {
                    var isTeamMember = result.is_team_member == 1;
                    elem.html((isTeamMember ? "<i class='fa fa-briefcase'></i> Revoke Team Access" : "<i class='fa fa-briefcase'></i> Make Team Member"));

                    var existingLabel = $("#team_member_label_" + id);
                    if (isTeamMember && existingLabel.length === 0) {
                        $("<span id='team_member_label_" + id + "' class='ur-admin-badge ur-admin-badge--neutral'>Team Member</span>").appendTo('.ur-detail-panel__badges');
                    } else if (!isTeamMember) {
                        existingLabel.remove();
                    }
                    showAlert('success', message, 3000);
                } else {
                    elem.html(oldHtml);
                    showAlert('danger', message, 5000);
                }
            }
        })
    }

    function updateTeamMemberStatus(elem, action, id) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        $.ajax({
            type: "post",
            url: "{{url('admin/profile/team-member-status')}}" + "/" + action + "/" + id,
            data: { '_token': '{{ csrf_token() }}' },
            success: function(result) {
                if (result.code == "200") {
                    showAlert('success', result.message, 3000);
                    loadMemberDetail(id); // button set changes shape (suspend/deactivate <-> reactivate) — simplest to re-render the whole panel
                } else {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);
                    showAlert('danger', result.message, 5000);
                }
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
