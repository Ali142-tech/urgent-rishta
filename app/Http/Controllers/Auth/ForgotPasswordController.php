<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

     /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = "/";

    protected function sendResetLinkResponse(Request $request, $response) {
        Log::info("Reset email sent to user email: ".($request->email?$request->email:"null"));
        Session::flash('message', 'success|An Email with a link to reset the password has been sent to you. Please click the link in email to reset password. Check spam/junk folder if not found.');

        return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($response)], 200)
                    : back()->with('status', trans($response));
    }
}
