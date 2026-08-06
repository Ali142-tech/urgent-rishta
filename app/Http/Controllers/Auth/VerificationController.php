<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use App\User;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request) {
        Log::info("Session id is " . $request->session()->getId());

        $user = User::find($request->route('id'));
        Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") initiated verification");

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            Session::flash('message','success|Your email is already verified.');
            Log::info("User is already verified");
        } else if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Session::flash('message','success|Thank you for verifying your email address. Please contact Nimrah at 0307-0227000 for profile activation.');
            Log::info("User has been verified");
            User::retrieveUserObject($user->dataid, true);
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new Response('', 204)
                    : view("auth.login");
    }
}
