<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Public "Become a Partner" signup for prospective matchmakers. Creates a
 * `team_members` row (its own table and its own login at /team/login — never
 * a `users` row), pending until an admin approves it. Sign-in stays blocked
 * until then (see TeamMember::loginBlockMessage()).
 */
class MatchmakerApplicationController extends Controller
{
    public function create()
    {
        return view('matchmaker.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:team_members,email',
            'contact_mobile_number' => 'required|string|max:30',
            'city' => 'required|string|max:100',
            'experience' => 'required|string|max:255',
            'about_me' => 'required|string|max:2000',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'required|image|max:5120',
        ]);

        // A matchmaker is a team member — their own table and login, not a
        // `users` row. Pending until an admin approves the application.
        $member = \App\TeamMember::create([
            'dataid' => \App\TeamMember::newDataid(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_mobile_number' => $request->contact_mobile_number,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'is_approved' => false,
            'status' => 'active',
            'application_status' => 'pending',
            'experience' => $request->experience,
            'about_me' => $request->about_me,
        ]);

        (new \App\Services\TeamMemberPhotoService())->store($member, $request->file('image'));

        Log::info('New matchmaker application: ' . $member->dataid . ' (' . $member->first_name . ' ' . $member->last_name . ', ' . $member->email . ')');

        Session::flash('message', 'success|Thanks for applying! Our team will review your application and get back to you shortly.');
        return redirect()->route('matchmaker.apply.thanks');
    }

    public function thanks()
    {
        return view('matchmaker.thanks');
    }
}
