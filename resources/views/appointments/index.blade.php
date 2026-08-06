@extends('layouts.master')

@section('main-content')
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">My Appointments</h2>
                <p class="mt-2 text-muted">Book an appointment or view your booked appointments below.</p>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1">
    <div class="container">
        @if(session('message'))
            @php
                $msg = session('message');
                $parts = explode('|', $msg, 2);
                $type = $parts[0] ?? 'info';
                $text = $parts[1] ?? $msg;
            @endphp
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                {{ $text }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-base-1 text-white">
                        <h5 class="mb-0">Book an Appointment</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('appointments.store') }}" method="post" class="form-default">
                            @csrf
                            <div class="form-group">
                                <label for="appointment_date" class="text-uppercase c-gray-light">Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control form-control-md" value="{{ old('appointment_date') }}" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label for="appointment_time" class="text-uppercase c-gray-light">Preferred Time <span class="text-danger">*</span></label>
                                <select name="appointment_time" id="appointment_time" class="form-control form-control-md" required>
                                    <option value="">Select time</option>
                                    @foreach(['09:00 AM','10:00 AM','11:00 AM','12:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM'] as $slot)
                                        <option value="{{ $slot }}" {{ old('appointment_time') == $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="subject" class="text-uppercase c-gray-light">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control form-control-md" value="{{ old('subject') }}" placeholder="e.g. Profile discussion">
                            </div>
                            <div class="form-group">
                                <label for="notes" class="text-uppercase c-gray-light">Notes</label>
                                <textarea name="notes" id="notes" class="form-control no-resize" rows="3" placeholder="Any additional details...">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-styled btn-base-1">Book Appointment</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Your Appointments</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($appointments->isEmpty())
                            <p class="p-4 text-muted mb-0">You have no appointments yet. Use the form to book one.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointments as $apt)
                                            <tr>
                                                <td>{{ $apt->appointment_date->format('d M Y') }}</td>
                                                <td>{{ $apt->appointment_time ?? '—' }}</td>
                                                <td>{{ $apt->subject ?: '—' }}</td>
                                                <td>
                                                    @if($apt->status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($apt->status === 'confirmed')
                                                        <span class="badge badge-success">Confirmed</span>
                                                    @elseif($apt->status === 'cancelled')
                                                        <span class="badge badge-secondary">Cancelled</span>
                                                    @else
                                                        <span class="badge badge-info">{{ ucfirst($apt->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$apt->isCancelled() && $apt->isUpcoming())
                                                        <form action="{{ route('appointments.cancel', $apt->id) }}" method="post" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                {{ $appointments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
