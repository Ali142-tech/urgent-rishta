{{--
    Right-hand summary panel for the Appointments list+detail layout
    (admin/dashboard/appointments.blade.php). Rendered server-side for the
    initially-selected request, and re-rendered via AJAX (AdminController::
    appointmentPanel()) whenever a different row is clicked, or after any
    status-changing action — see loadAppointmentDetail() in appointments.blade.php.
--}}
@if(empty($appointment))
    <div class="ur-admin-empty"><i class="fa fa-calendar"></i> Select a request from the list to see its details.</div>
@else
    @php
        $statusClass = match($appointment->status) {
            'pending' => 'ur-admin-badge--warning',
            'confirmed' => 'ur-admin-badge--success',
            'completed' => 'ur-admin-badge--info',
            'cancelled' => 'ur-admin-badge--neutral',
            default => 'ur-admin-badge--neutral',
        };
        $initials = strtoupper(substr(trim($appointment->contact_name ?: 'G'), 0, 1));
    @endphp
    <div class="ur-detail-panel" data-id="{{ $appointment->id }}">
        <div class="ur-detail-panel__head">
            <div class="ur-detail-panel__photo ur-detail-panel__photo--initials">{{ $initials }}</div>
            <div class="ur-detail-panel__who">
                <h3>{{ $appointment->contact_name ?: 'Guest' }}</h3>
                <p class="ur-detail-panel__contact">
                    {{ $appointment->contact_email ?: '—' }} &bull; {{ $appointment->contact_phone ?: '—' }}
                    @if($appointment->user_id)
                        &bull; <a href="{{ url('/member/profile/'.$appointment->user->dataid) }}" target="_blank">View Member Profile</a>
                    @endif
                </p>
                <div class="ur-detail-panel__badges">
                    <span class="ur-admin-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                    @if(!$appointment->user_id)
                        <span class="ur-admin-badge ur-admin-badge--neutral">Guest</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ur-detail-panel__grid">
            <div>
                <label>Preferred Date &amp; Time</label>
                <span id="apt_slot_display_{{ $appointment->id }}">{{ $appointment->appointment_date->format('d M Y') }} &bull; {{ $appointment->appointment_time ?: '—' }}</span>
            </div>
            <div>
                <label>Requested On</label>
                <span>{{ $appointment->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div id="reschedule_{{ $appointment->id }}" class="ur-apt-reschedule-box" style="display:none;">
            <form onsubmit="return saveReschedule(event, {{ $appointment->id }}, '{{ $appointment->status }}');" class="ur-admin-appointment-form">
                <input type="date" name="appointment_date" class="form-control form-control-sm" value="{{ $appointment->appointment_date->format('Y-m-d') }}">
                <input type="text" name="appointment_time" class="form-control form-control-sm" value="{{ $appointment->appointment_time }}" placeholder="e.g. 4:00 PM">
                <button type="submit" class="ur-btn ur-btn--dark">Save</button>
            </form>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:10.5px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:var(--ur-dash-text-muted); margin-bottom:6px;">Reason for Consultation</label>
            <p style="margin:0; font-size:13.5px; color:var(--ur-dash-text);">{{ $appointment->subject ?: '—' }}</p>
        </div>

        @if($appointment->notes)
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:10.5px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:var(--ur-dash-text-muted); margin-bottom:6px;">Additional Notes</label>
                <p style="margin:0; font-size:13.5px; color:var(--ur-dash-text);">{{ $appointment->notes }}</p>
            </div>
        @endif

        <div class="ur-detail-panel__actions">
            @if($appointment->status === 'pending')
                <a class="ur-btn ur-btn--success" onclick="return confirmAppointment({{ $appointment->id }});"><i class="fa fa-check"></i> Confirm &amp; Schedule</a>
                <a class="ur-btn ur-btn--outline" onclick="return toggleReschedule({{ $appointment->id }});"><i class="fa fa-clock-o"></i> Reschedule</a>
                <a class="ur-btn ur-btn--danger-outline" onclick="return cancelAppointmentRequest({{ $appointment->id }});"><i class="fa fa-times"></i> Cancel Request</a>
            @elseif($appointment->status === 'confirmed')
                <a class="ur-btn ur-btn--success" onclick="return markAppointmentCompleted({{ $appointment->id }});"><i class="fa fa-check-circle"></i> Mark Completed</a>
                <a class="ur-btn ur-btn--outline" onclick="return toggleReschedule({{ $appointment->id }});"><i class="fa fa-clock-o"></i> Reschedule</a>
                <a class="ur-btn ur-btn--danger-outline" onclick="return cancelAppointmentRequest({{ $appointment->id }});"><i class="fa fa-times"></i> Cancel Request</a>
            @elseif($appointment->status === 'cancelled')
                <a class="ur-btn ur-btn--outline" onclick="return reopenAppointment({{ $appointment->id }});"><i class="fa fa-undo"></i> Reopen Request</a>
            @else
                <span class="ur-admin-badge ur-admin-badge--info">This consultation has been completed.</span>
            @endif
        </div>
    </div>
@endif
