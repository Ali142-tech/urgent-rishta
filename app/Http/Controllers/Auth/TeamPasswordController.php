<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\TeamMember;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/** "Forgot password" for team members: e-mail a reset link, then set a new password. */
class TeamPasswordController extends Controller
{
    public function forgotForm()
    {
        return view('team.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Same answer whether or not the e-mail exists (and only approved, active members get a link),
        // so the form can't be used to find out who is registered.
        $member = TeamMember::where('email', $request->email)->first();
        if ($member && $member->isActiveMember()) {
            Password::broker('team_members')->sendResetLink(['email' => $member->email]);
            Log::info('Team password reset link sent to ' . $member->dataid);
        }

        Session::flash('message', 'success|If that e-mail belongs to a team account, a reset link is on its way.');
        return redirect()->route('team.password.forgot');
    }

    public function resetForm(Request $request, $token)
    {
        return view('team.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('team_members')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (TeamMember $member, string $password) {
                $member->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($member));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Session::flash('message', 'success|Password updated. Please sign in.');
            return redirect()->route('team.login');
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
    }
}
