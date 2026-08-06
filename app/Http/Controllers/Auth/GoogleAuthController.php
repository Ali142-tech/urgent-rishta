<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth failed: ' . $e->getMessage());
            Session::flash('message', 'danger|Google login failed. Please try again.');
            return redirect()->route('login');
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            Session::flash('message', 'danger|Google did not return an email. Please use another login method.');
            return redirect()->route('login');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();

            Auth::login($user, true);
            $user->profile(true);

            Log::info('User (' . $user->dataid . ') logged in via Google');

            if (empty($user->package)) {
                return redirect('packages');
            }

            return redirect('home');
        }

        // New Google user → complete registration (matrimony profile fields still required)
        $name = trim((string) $googleUser->getName());
        $parts = preg_split('/\s+/', $name, 2);
        $first = $parts[0] ?? '';
        $last = $parts[1] ?? '';

        $raw = $googleUser->user ?? [];
        if (!empty($raw['given_name'])) {
            $first = $raw['given_name'];
        }
        if (!empty($raw['family_name'])) {
            $last = $raw['family_name'];
        }

        Session::put('google_oauth', [
            'id' => $googleUser->getId(),
            'email' => $email,
            'first_name' => $first,
            'last_name' => $last,
            'avatar' => $googleUser->getAvatar(),
        ]);

        Session::flash(
            'message',
            'success|Google connected. Please complete your profile to finish registration.'
        );

        return redirect()->route('register');
    }
}
