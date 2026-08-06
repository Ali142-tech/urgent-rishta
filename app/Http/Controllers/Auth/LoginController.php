<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\LoginOtp;
use App\Mail\LoginOtpMail;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Shaadi-style login hub (phone-first).
     */
    public function showLoginForm(Request $request)
    {
        $mode = $request->query('mode', 'phone'); // phone | otp | password | email
        if (!in_array($mode, ['phone', 'otp', 'password', 'email'], true)) {
            $mode = 'phone';
        }

        if ($mode === 'otp' && !Session::has('login_otp_mobile')) {
            return redirect()->route('login');
        }

        return view('auth.login', [
            'mode' => $mode,
            'otpMobile' => Session::get('login_otp_mobile'),
            'otpEmailMasked' => Session::get('login_otp_email_masked'),
            'countryCode' => old('country_code', Session::get('login_otp_country_code', '92')),
            'mobileLocal' => old('mobile', Session::get('login_otp_mobile_local')),
        ]);
    }

    /**
     * Send OTP to the account email (SMS channel later via config).
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:5',
            'mobile' => 'required|string|max:20',
        ], [
            'mobile.required' => 'Please enter your mobile number.',
        ]);

        $countryCode = preg_replace('/\D+/', '', $request->country_code);
        $local = preg_replace('/\D+/', '', $request->mobile);
        $full = $this->normalizePhoneNumber($countryCode . $local);

        if ($full === '' || strlen($full) < 10) {
            Session::flash('message', 'danger|Please enter a valid mobile number.');
            return redirect()->route('login')->withInput();
        }

        $user = $this->findUserByMobile($full);
        if (!$user) {
            Session::flash('message', 'danger|No account found with this mobile number. Please register first.');
            return redirect()->route('login')->withInput();
        }

        if (empty($user->email)) {
            Session::flash('message', 'danger|This account has no email on file. Please use password login or contact support.');
            return redirect()->route('login', ['mode' => 'password'])->withInput();
        }

        $cooldown = (int) config('otp.resend_cooldown_seconds', 60);
        $recent = LoginOtp::where('identifier', $full)
            ->where('created_at', '>=', now()->subSeconds($cooldown))
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if ($recent) {
            Session::flash('message', 'warning|Please wait a moment before requesting another OTP.');
            return redirect()->route('login', ['mode' => 'otp']);
        }

        $plainCode = (string) random_int(100000, 999999);
        $channel = config('otp.channel', 'email');

        try {
            if ($channel === 'email') {
                Mail::to($user->email)->send(new LoginOtpMail(
                    $plainCode,
                    $user->getFullName() ?: 'Member',
                    $full
                ));
            } else {
                // Future: SMS provider. Keep structure ready.
                Session::flash('message', 'danger|SMS OTP is not configured yet. Please use email OTP or password login.');
                return redirect()->route('login')->withInput();
            }

            LoginOtp::issue($full, $plainCode, $channel, $user->email);

            Session::put('login_otp_mobile', $full);
            Session::put('login_otp_mobile_local', $local);
            Session::put('login_otp_country_code', $countryCode);
            Session::put('login_otp_email_masked', $this->maskEmail($user->email));

            Session::flash(
                'message',
                'success|OTP sent to your registered email (' . $this->maskEmail($user->email) . '). Check inbox/spam.'
            );
            Log::info('Login OTP emailed for mobile ' . $full . ' user ' . $user->dataid);

            return redirect()->route('login', ['mode' => 'otp']);
        } catch (\Exception $e) {
            Log::error('Login OTP send failed: ' . $e->getMessage());
            Session::flash('message', 'danger|Could not send OTP right now. Please try again or login with password.');
            return redirect()->route('login')->withInput();
        }
    }

    /**
     * Verify email OTP and log the user in.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:4|max:8',
        ], [
            'otp.required' => 'Please enter the OTP.',
        ]);

        $mobile = Session::get('login_otp_mobile');
        if (!$mobile) {
            Session::flash('message', 'danger|OTP session expired. Please request a new code.');
            return redirect()->route('login');
        }

        $otpRow = LoginOtp::where('identifier', $mobile)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otpRow || !$otpRow->isValid()) {
            Session::flash('message', 'danger|OTP expired or too many attempts. Please request a new code.');
            return redirect()->route('login', ['mode' => 'otp']);
        }

        $code = preg_replace('/\D+/', '', $request->otp);
        if (!$otpRow->matches($code)) {
            $otpRow->incrementAttempts();
            Session::flash('message', 'danger|Incorrect OTP. Please try again.');
            return redirect()->route('login', ['mode' => 'otp']);
        }

        $user = $this->findUserByMobile($mobile);
        if (!$user) {
            Session::flash('message', 'danger|Account not found. Please register.');
            return redirect()->route('login');
        }

        $otpRow->markConsumed();
        Session::forget(['login_otp_mobile', 'login_otp_mobile_local', 'login_otp_country_code', 'login_otp_email_masked']);

        Auth::login($user, true);
        return $this->finishLogin($user, 'otp:' . $mobile);
    }

    /**
     * Password login with mobile (Shaadi "Login with Password").
     */
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:5',
            'mobile' => 'required|string|max:20',
            'password' => 'required|string',
        ]);

        $countryCode = preg_replace('/\D+/', '', $request->country_code);
        $local = preg_replace('/\D+/', '', $request->mobile);
        $full = $this->normalizePhoneNumber($countryCode . $local);

        $credentials = $this->buildCredentials($full, $request->password);
        $remember = $request->boolean('remember') || $request->input('remember') === 'checked';

        if (!$credentials || !Auth::attempt($credentials, $remember)) {
            Session::flash('message', 'danger|Invalid mobile or password. Please try again.');
            return redirect()->route('login', ['mode' => 'password'])->withInput($request->only('country_code', 'mobile', 'remember'));
        }

        $user = User::retrieveUserObject(null, true);
        return $this->finishLogin($user, 'password-mobile:' . $full);
    }

    /**
     * Continue with Email + password.
     */
    public function loginWithEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember') || $request->input('remember') === 'checked';
        $credentials = [
            'email' => trim($request->email),
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, $remember)) {
            Session::flash('message', 'danger|Invalid email or password. Please try again.');
            return redirect()->route('login', ['mode' => 'email'])->withInput($request->only('email', 'remember'));
        }

        $user = User::retrieveUserObject(null, true);
        return $this->finishLogin($user, 'password-email:' . $request->email);
    }

    /**
     * Keep default POST /login working for older forms → email path.
     */
    public function login(Request $request)
    {
        if ($request->filled('email') && !$request->filled('mobile')) {
            return $this->loginWithEmail($request);
        }

        if ($request->filled('mobile')) {
            return $this->loginWithPassword($request);
        }

        // Backward compat: old "login" field
        if ($request->filled('login')) {
            $login = trim($request->input('login'));
            $remember = $request->boolean('remember') || $request->input('remember') === 'checked';
            $credentials = $this->buildCredentials($login, $request->password);
            if (!$credentials || !Auth::attempt($credentials, $remember)) {
                Session::flash('message', 'danger|Invalid email/mobile or password. Please try again.');
                return redirect()->route('login', ['mode' => 'email'])->withInput();
            }
            $user = User::retrieveUserObject(null, true);
            return $this->finishLogin($user, 'legacy:' . $login);
        }

        return redirect()->route('login');
    }

    private function finishLogin(User $loggedInUser, string $via)
    {
        $loggedInUser->profile(true);

        if (!$loggedInUser->hasVerifiedEmail()) {
            try {
                $loggedInUser->sendEmailVerificationNotification();
                Session::flash(
                    'message',
                    'danger|Your email is not verified yet. We sent a new verification link (check spam/junk). Need help? Contact Nimrah at 0307-0227000.|15000'
                );
                Log::info('User email not verified for ' . $loggedInUser->email);
                Auth::logout();
            } catch (\Exception $e) {
                Auth::logout();
                Session::flash('message', 'danger|Your email is not verified. Please contact support at 0307-0227000.');
            }
            return redirect()->route('login');
        }

        Log::info('User (' . $loggedInUser->dataid . ') logged in via ' . $via);

        if (empty($loggedInUser->package)) {
            return redirect('packages');
        }

        return redirect('home');
    }

    private function findUserByMobile(string $normalized): ?User
    {
        $digits = preg_replace('/\D+/', '', $normalized);

        return User::where(function ($query) use ($normalized, $digits) {
            $query->where('contact_mobile_number', $normalized);
            if (!empty($digits) && $digits !== $normalized) {
                $query->orWhere('contact_mobile_number', $digits);
            }
            // Also try without country code for older rows
            if (Str::startsWith($normalized, '92') && strlen($normalized) > 2) {
                $query->orWhere('contact_mobile_number', '0' . Str::substr($normalized, 2));
                $query->orWhere('contact_mobile_number', Str::substr($normalized, 2));
            }
        })->first();
    }

    private function buildCredentials(string $login, string $password): ?array
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => $login,
                'password' => $password,
            ];
        }

        $normalized = $this->normalizePhoneNumber($login);
        if ($normalized === '') {
            return null;
        }

        $user = $this->findUserByMobile($normalized);
        if (!$user || empty($user->email)) {
            return null;
        }

        return [
            'email' => $user->email,
            'password' => $password,
        ];
    }

    private function normalizePhoneNumber(string $n): string
    {
        $pkCodes = [
            '300', '301', '302', '303', '304', '305', '306', '307', '308', '309',
            '310', '311', '312', '313', '314', '315', '316', '317', '318',
            '320', '321', '322', '323', '324',
            '330', '331', '332', '333', '334', '335', '336', '337',
            '340', '341', '342', '343', '344', '345', '346', '347', '348', '349', '355',
        ];

        $n = preg_replace('/\D+/', '', $n);
        if ($n === null || $n === '') {
            return '';
        }

        if (Str::startsWith($n, '00')) {
            $n = Str::substr($n, 2);
        }

        foreach ($pkCodes as $code) {
            if (Str::startsWith($n, $code)) {
                return '92' . $n;
            }
        }

        if (Str::startsWith($n, '0')) {
            return '92' . Str::substr($n, 1);
        }

        return $n;
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***';
        }
        $name = $parts[0];
        $domain = $parts[1];
        $visible = max(1, min(3, (int) floor(strlen($name) / 3)));
        return substr($name, 0, $visible) . str_repeat('*', max(3, strlen($name) - $visible)) . '@' . $domain;
    }
}
