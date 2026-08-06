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
        $this->middleware(['auth', 'verified']);
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
            'appointment_time' => 'required|string|max:20',
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
