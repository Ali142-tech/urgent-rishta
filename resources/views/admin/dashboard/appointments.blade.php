@extends('layouts.admin.master')
@section('dashboard-title', 'Appointments')
@section('admin-content')
<div class="ur-admin-header">
    <h2>Appointments &amp; Consultations</h2>
</div>

<div class="ur-ov-stats">
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Pending Requests</span>
        <span class="ur-ov-stat__value">{{ number_format($pendingCount) }}</span>
        <span class="ur-ov-stat__delta is-warning">Awaiting confirmation</span>
    </div>
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Confirmed Today</span>
        <span class="ur-ov-stat__value">{{ number_format($confirmedTodayCount) }}</span>
        <span class="ur-ov-stat__delta is-up">On schedule</span>
    </div>
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">This Month</span>
        <span class="ur-ov-stat__value">{{ number_format($thisMonthCount) }}</span>
        <span class="ur-ov-stat__delta {{ $thisMonthDeltaPct >= 0 ? 'is-up' : 'is-down' }}">{{ $thisMonthDeltaPct >= 0 ? '+' : '' }}{{ $thisMonthDeltaPct }}% vs last month</span>
    </div>
    {{-- No fee/payment column exists on appointments (the consultation fee
         is collected separately after admin review — see
         AppointmentController::storeConsultationRequest), so this is a real
         "Completed" count rather than a fabricated revenue figure. --}}
    <div class="ur-ov-stat ur-ov-stat--accent">
        <span class="ur-ov-stat__label">Completed</span>
        <span class="ur-ov-stat__value">{{ number_format($completedCount) }}</span>
        <span class="ur-ov-stat__delta">Consultations held</span>
    </div>
</div>

<div class="ur-apt-layout">
    <div class="ur-apt-list">
        <form id="controls-form" class="ur-admin-filters" action="javascript:void();">
            @csrf
            <input type="hidden" id="pagerequested" name="pagerequested" value="{{ $currentPage }}"/>
            <input type="hidden" name="pagesize" value="10"/>

            <div class="form-group form-group--grow">
                <label for="term">Search</label>
                <input type="search" name="term" class="form-control form-control-sm" placeholder="Name, email, phone..." autocomplete="off" onkeyup="javascript:refreshAppointments(true);" value="" />
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control form-control-sm selectpicker" data-placeholder="Any" data-hide-disabled="true" onchange="javascript:refreshAppointments(true);">
                    <option value="">Any</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" onchange="javascript:refreshAppointments(true);">
            </div>
            <button type="button" id="clear-filters-btn" class="ur-admin-filters__clear" onclick="javascript:clearAppointmentFilters();" style="display:none;">
                <i class="fa fa-times-circle"></i> Clear Filters
            </button>
        </form>

        <div id="appointments-data">
            @yield('appointments-data')
        </div>
    </div>

    <div class="ur-apt-panel" id="appointment-detail-panel">
        @include('admin.dashboard.appointment-detail-panel', ['appointment' => $selectedAppointment ?? null])
    </div>
</div>

<script type="text/javascript">
    function refreshAppointments(resetCurrentPage, newPage) {
        if (resetCurrentPage)
            $('#pagerequested').val(newPage ? newPage : 1);
        renderPage("{{ url('admin/appointments/refresh') }}", "post", $("#controls-form").serialize(), $("#appointments-data"));
        updateClearFiltersVisibility();
    }

    function updateClearFiltersVisibility() {
        var form = document.getElementById('controls-form');
        var btn = document.getElementById('clear-filters-btn');
        if (!form || !btn) return;
        var hasFilter = !!(form.term.value || form.status.value || form.date.value);
        btn.style.display = hasFilter ? '' : 'none';
    }

    var suppressFilterChange = false;
    function clearAppointmentFilters() {
        var form = document.getElementById('controls-form');
        form.term.value = '';
        form.date.value = '';
        suppressFilterChange = true;
        $(form.status).val('').trigger('change');
        suppressFilterChange = false;
        refreshAppointments(true);
    }

    $(document).ready(function () {
        updateClearFiltersVisibility();
    });

    /** Clicking a row in the list — AJAX-loads that appointment's summary into the right-hand panel. */
    function loadAppointmentDetail(id, rowElem) {
        var panel = document.getElementById('appointment-detail-panel');
        if (!panel) return;

        document.querySelectorAll('.ur-apt-row.is-active').forEach(function (el) {
            el.classList.remove('is-active');
        });
        if (rowElem) rowElem.classList.add('is-active');

        panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-refresh fa-spin"></i> Loading...</div>';

        $.ajax({
            type: 'get',
            url: "{{ url('admin/appointments/panel') }}/" + id,
            success: function (result) {
                panel.innerHTML = (result.code == '200') ? result.html
                    : '<div class="ur-admin-empty"><i class="fa fa-exclamation-triangle"></i> Could not load this request.</div>';
            },
            error: function () {
                panel.innerHTML = '<div class="ur-admin-empty"><i class="fa fa-exclamation-triangle"></i> Could not load this request.</div>';
            }
        });

        var url = new URL(window.location.href);
        url.searchParams.set('selected', id);
        window.history.replaceState(null, '', url);
    }

    /** Shared by every detail-panel action button — posts to the same status-update endpoint used by the old table view. */
    function updateAppointmentRequest(id, data, successMessage) {
        $.ajax({
            type: 'post',
            url: "{{ url('admin/appointments') }}/" + id + "/status",
            data: $.extend({ _token: "{{ csrf_token() }}" }, data),
            success: function (result) {
                if (result.code == '200') {
                    showAlert('success', successMessage || result.message, 3000);
                    loadAppointmentDetail(id);
                    refreshAppointments(false);
                } else {
                    showAlert('danger', result.message, 5000);
                }
            },
            error: function () {
                showAlert('danger', 'Something went wrong. Please try again.', 5000);
            }
        });
    }

    function confirmAppointment(id) {
        updateAppointmentRequest(id, { status: 'confirmed' }, 'Request confirmed & scheduled.');
    }

    function markAppointmentCompleted(id) {
        updateAppointmentRequest(id, { status: 'completed' }, 'Marked as completed.');
    }

    function cancelAppointmentRequest(id) {
        if (!confirm('Cancel this consultation request?')) return;
        updateAppointmentRequest(id, { status: 'cancelled' }, 'Request cancelled.');
    }

    function reopenAppointment(id) {
        updateAppointmentRequest(id, { status: 'pending' }, 'Request reopened.');
    }

    function toggleReschedule(id) {
        var box = document.getElementById('reschedule_' + id);
        if (box) box.style.display = box.style.display === 'none' ? '' : 'none';
    }

    function saveReschedule(event, id, status) {
        event.preventDefault();
        var form = $(event.target);
        updateAppointmentRequest(id, {
            status: status,
            appointment_date: form.find('[name="appointment_date"]').val(),
            appointment_time: form.find('[name="appointment_time"]').val(),
        }, 'Date & time updated.');
        return false;
    }
</script>
@endsection
