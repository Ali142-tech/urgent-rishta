<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function __construct()
    {
        // The homepage's "Book a Private Consultation" form is public —
        // guests (not just logged-in verified members) can submit it.
        $this->middleware(['auth', 'verified'])->except(['storeConsultationRequest']);
    }

    /**
     * List user's appointments and show book form.
     */
    public function index()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Store a new appointment (book).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string|max:40',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ], [
            'appointment_date.after_or_equal' => 'Please select today or a future date.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('appointments.index')
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'danger|Please correct the errors below.');
        }

        Appointment::create([
            'user_id' => Auth::id(),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'subject' => $request->subject,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('message', 'success|Your appointment has been booked. We will confirm it shortly.');
    }

    /**
     * Public "Book a Private Consultation" request — the homepage popup
     * (welcome.blade.php). Open to guests as well as logged-in members;
     * this is a REQUEST only (status starts 'pending') — the ₨300 fee is
     * collected separately after admin reviews and schedules it, not
     * through this form. Admin sets the final date/time when confirming
     * (see AdminController::updateAppointmentStatus).
     */
    public function storeConsultationRequest(Request $request)
    {
        $isGuest = !Auth::check();

        $rules = [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string|max:40',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string|max:2000',
            // Required for both guests and logged-in members. The phone
            // field always submits a full "+<dialcode><number>" value (see
            // welcome.blade.php's intl-tel-input integration); client-side
            // iti.isValidNumber() checks it against the selected country's
            // format before submit, but that's just UX — the `phone` rule
            // here (no country param = auto-detects country from the
            // leading "+<dialcode>") is the authoritative check, since
            // client JS can be bypassed or buggy.
            'guest_email' => 'required|email|max:150',
            'guest_phone' => ['required', 'phone', 'max:30'],
        ];

        if ($isGuest) {
            $rules['guest_name'] = 'required|string|max:150';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['code' => '422', 'message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        Appointment::create([
            'user_id' => $user->id ?? null,
            'guest_name' => $isGuest ? $request->guest_name : null,
            // Kept for logged-in members too, in case the email/number
            // submitted here differs from their account (e.g. a different
            // contact for this specific consultation).
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'subject' => $request->subject,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        if ($request->ajax()) {
            return response()->json(['code' => '200', 'message' => 'Your consultation request has been received. Our team will review it and contact you to confirm.']);
        }

        return back()->with('message', 'success|Your consultation request has been received. Our team will review it and contact you to confirm.');
    }

    /**
     * Cancel an appointment (soft: set status to cancelled).
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        if ($appointment->isCancelled()) {
            return redirect()->route('appointments.index')->with('message', 'info|This appointment is already cancelled.');
        }
        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->route('appointments.index')->with('message', 'success|Appointment cancelled.');
    }
}
