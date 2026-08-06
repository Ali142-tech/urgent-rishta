<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\User;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        Log::info("Session id is " . $request->session()->getId());
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $loggedInUser = User::retrieveUserObject(null, true);
            $loggedInUser->profile(true);

            if (!$loggedInUser->hasVerifiedEmail()) {
                try {
                    // if (!$userEmailVerified) {
                        $loggedInUser->sendEmailVerificationNotification();
                        Session::flash('message', 'danger|Email has not been verified. Please check your email (junk/spam also) for a verification link. If you did not receive the email, contact Nimrah at 0307-0227000 for a new email.|15000');
                        Log::info("User email not verified for " . $request->email);
                    // }
                    Auth::logout();
                } catch (\Exception $e) {
                    if ($loggedInUser) Auth::logout();
                }
                return redirect('login');
            } else {
                //Session::flash('message', 'success|Login successful');
                Log::info("User (".$loggedInUser->dataid.") logged in using " . $request->email);
                if (empty($loggedInUser->package)) {
                    return redirect('packages');
                } else return redirect('home');
            }
        } else {
            Session::flash('message', 'danger|Unable to authenticate. Please check your password and try again.');
            Log::info("Failed login attempt detected using " . $request->email);
            return redirect('login');
        }
    }
}
