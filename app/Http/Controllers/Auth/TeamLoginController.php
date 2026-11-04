<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

/**
 * Sign-in for matchmakers (team members) — the `team` guard, kept completely
 * apart from the member login. Accepts email or mobile number plus password.
 */
class TeamLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('team')->check()) {
            return redirect()->route('team.dashboard');
        }
        return view('team.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:150',
            'password' => 'required|string',
        ]);

        $login = trim($request->login);
        $throttleKey = 'team-login|' . strtolower($login) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $minutes = (int) ceil(RateLimiter::availableIn($throttleKey) / 60);
            Session::flash('message', 'danger|Too many attempts. Please try again in ' . $minutes . ' minute(s).');
            return redirect()->route('team.login')->withInput($request->only('login'));
        }

        $member = $this->findMember($login);

        if (!$member || !Hash::check($request->password, $member->password)) {
            // what happened, never the password itself
            Log::info('Team login failed: account ' . ($member ? 'found (' . $member->dataid . ', temporary=' . (int) $member->is_temporary . ')' : 'NOT found') . ', password length ' . strlen((string) $request->password) . ', db ' . \DB::connection()->getDatabaseName() . ', from ' . base_path());
            RateLimiter::hit($throttleKey, 300);
            Session::flash('message', 'danger|Those details don\'t match our records.');
            return redirect()->route('team.login')->withInput($request->only('login'));
        }

        if ($block = $member->loginBlockMessage()) {
            Log::info('Team login blocked for ' . $member->dataid . ' (application=' . $member->application_status . ', status=' . $member->status . ')');
            Session::flash('message', $block);
            return redirect()->route('team.login')->withInput($request->only('login'));
        }

        RateLimiter::clear($throttleKey);

        // a temporary account's clock starts at its first sign-in (the update only applies while it is still unset, so two quick
        // sign-ins cannot start it twice)
        if ($member->is_temporary && !$member->temp_first_login_at) {
            $now = now();
            TeamMember::whereKey($member->id)->whereNull('temp_first_login_at')->update([
                'temp_first_login_at' => $now,
                'temp_expires_at' => $now->copy()->addMinutes((int) ($member->temp_minutes ?: 15)),
            ]);
            $member = $member->fresh();
        }

        Auth::guard('team')->login($member, !$member->is_temporary && $request->boolean('remember'));
        $request->session()->regenerate();

        Log::info('Team member (' . $member->dataid . ') logged in');
        if ($member->is_temporary) {
            return redirect()->route('team.proposals.search');
        }
        return redirect()->intended(route('team.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('team')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('team.login');
    }

    private function findMember(string $login): ?TeamMember
    {
        if (str_contains($login, '@')) {
            return TeamMember::where('email', $login)->first();
        }

        $digits = preg_replace('/\D+/', '', $login);
        if ($digits === '') {
            return null;
        }
        // 03001234567, 923001234567 and 3001234567 all find the same number.
        $tail = substr($digits, -10);
        return TeamMember::whereRaw("REPLACE(REPLACE(REPLACE(contact_mobile_number, '+', ''), ' ', ''), '-', '') LIKE ?", ['%' . $tail])->first();
    }
}
