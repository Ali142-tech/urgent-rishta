<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\LoginOtp;
use App\Mail\RegisterOtpMail;
use App\MasterData;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/packages';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function emailInUse()
    {
        return response()->json([
            'code' => '200',
            'data' => User::where('email', request()['e'])->count(),
        ]);
    }

    public function showRegistrationForm(Request $request)
    {
        $religions = MasterData::where('type', 'RELIGION')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $googleOAuth = Session::get('google_oauth');

        $mode = $request->query('mode', 'experience'); // experience | start | community | contact | otp | build | build2 | build3 | preferences | build4 | verify_mobile | profile
        if (!in_array($mode, ['experience', 'start', 'community', 'contact', 'otp', 'build', 'build2', 'build3', 'preferences', 'build4', 'verify_mobile', 'profile'], true)) {
            $mode = 'experience';
        }

        // Fresh "Register" click (/register with no mode) → start from step 0, drop stale session
        if (!$request->has('mode')) {
            $this->forgetRegisterSession();
            $googleOAuth = null;
            $mode = 'experience';
        }

        // Website Upgrade Brief §6 routing rule — must choose Online vs Personalized
        // before continuing into the name/DOB step.
        if ($mode === 'start' && !Session::has('register_service_type')) {
            return redirect()->route('register', ['mode' => 'experience']);
        }

        // Prefill names from Google on first step
        if (!empty($googleOAuth)) {
            if (!Session::has('register_first_name') && !empty($googleOAuth['first_name'])) {
                Session::put('register_first_name', $googleOAuth['first_name']);
            }
            if (!Session::has('register_last_name') && !empty($googleOAuth['last_name'])) {
                Session::put('register_last_name', $googleOAuth['last_name']);
            }
            if (!Session::has('register_email') && !empty($googleOAuth['email'])) {
                Session::put('register_email', $googleOAuth['email']);
            }
        }

        if ($mode === 'community' && !Session::has('register_first_name')) {
            return redirect()->route('register');
        }

        if ($mode === 'contact' && (!Session::has('register_first_name') || !Session::has('register_religion'))) {
            return redirect()->route('register', Session::has('register_first_name') ? ['mode' => 'community'] : []);
        }

        if ($mode === 'otp') {
            if (!Session::get('register_contact_saved') || !Session::has('register_email')) {
                return redirect()->route('register', ['mode' => 'contact']);
            }
        }

        // Old second-OTP screen removed — one email OTP after contact only
        if ($mode === 'verify_mobile') {
            return redirect()->route('register', [
                'mode' => Session::get('register_preferences_saved') ? 'build4' : (Session::has('register_education') ? 'preferences' : (Session::get('register_verified') ? 'build' : 'contact')),
            ]);
        }

        if (in_array($mode, ['build', 'build2', 'build3', 'preferences', 'build4', 'profile'], true) && !Session::get('register_verified')) {
            return redirect()->route('register');
        }

        if ($mode === 'build2' && !Session::has('register_city')) {
            return redirect()->route('register', ['mode' => 'build']);
        }

        if ($mode === 'build3' && !Session::has('register_height')) {
            return redirect()->route('register', ['mode' => 'build2']);
        }

        if ($mode === 'preferences' && !Session::has('register_education')) {
            return redirect()->route('register', ['mode' => 'build3']);
        }

        if ($mode === 'build4' && !Session::get('register_preferences_saved')) {
            return redirect()->route('register', ['mode' => Session::has('register_education') ? 'preferences' : 'build3']);
        }

        if ($mode === 'profile') {
            return redirect()->route('register', ['mode' => Session::get('register_preferences_saved') ? 'build4' : 'build']);
        }

        // States for build step (from country chosen earlier)
        $states = collect();
        $countryId = Session::get('register_country');
        if ($mode === 'build' && $countryId) {
            $states = MasterData::where(['type' => 'STATE', 'subtype' => $countryId])
                ->orderBy('order', 'DESC')
                ->orderBy('name', 'ASC')
                ->get();
        }

        return view('auth.register', compact(
            'religions',
            'maritalstatuses',
            'mothertongues',
            'education',
            'countries',
            'caste',
            'states',
            'googleOAuth',
            'mode'
        ) + [
            'registerFirstName' => Session::get('register_first_name', $googleOAuth['first_name'] ?? ''),
            'registerLastName' => Session::get('register_last_name', $googleOAuth['last_name'] ?? ''),
            'registerDay' => Session::get('register_day'),
            'registerMonth' => Session::get('register_month'),
            'registerYear' => Session::get('register_year'),
            'registerReligion' => Session::get('register_religion'),
            'registerCaste' => Session::get('register_caste'),
            'registerCountry' => Session::get('register_country'),
            'registerState' => Session::get('register_state'),
            'registerCity' => Session::get('register_city'),
            'registerSect' => Session::get('register_sect'),
            'registerMaritalStatus' => Session::get('register_marital_status'),
            'registerHeight' => Session::get('register_height'),
            'registerGender' => Session::get('register_gender'),
            'registerEducation' => Session::get('register_education'),
            'registerProfession' => Session::get('register_profession'),
            'registerOnBehalf' => Session::get('register_on_behalf'),
            'registerMotherTongue' => Session::get('register_mother_tongue'),
            'registerEmail' => Session::get('register_email', $googleOAuth['email'] ?? null),
            'registerMobile' => Session::get('register_mobile'),
            'registerMobileLocal' => Session::get('register_mobile_local'),
            'registerCountryCode' => Session::get('register_country_code', '92'),
            'registerEmailMasked' => Session::get('register_email_masked'),
            'otpResendSeconds' => (int) config('otp.resend_cooldown_seconds', 60),
            'registerServiceType' => Session::get('register_service_type'),
            'registerRAge' => Session::get('register_r_age'),
            'registerRHeight' => Session::get('register_r_height'),
            'registerRMaritalStatus' => Session::get('register_r_marital_status'),
            'registerRReligion' => Session::get('register_r_religion'),
            'registerRGenReq' => Session::get('register_r_gen_req'),
        ]);
    }

    /**
     * Step 0 (Website Upgrade Brief §6 routing rule): "I want to search
     * profiles myself" → online, or "I want your team to find matches for
     * me" → personalized. Stored on the account so it can route the user to
     * the right dashboard/queue later; does not gate anything by itself yet.
     */
    public function saveExperience(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:online,personalized',
        ], [
            'service_type.required' => 'Please choose how you would like to find your match.',
        ]);

        Session::put('register_service_type', $request->service_type);

        return redirect()->route('register', ['mode' => 'start']);
    }

    /**
     * Step 1: name + date of birth (Shaadi-style).
     */
    public function saveBasics(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'day' => 'required|string|size:2',
            'month' => 'required|string|size:2',
            'year' => 'required|digits:4',
        ], [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'day.required' => 'Please enter day of birth.',
            'month.required' => 'Please enter month of birth.',
            'year.required' => 'Please enter year of birth.',
        ]);

        $year = (int) $request->year;
        $month = (int) $request->month;
        $day = (int) $request->day;
        $dobOk = checkdate($month, $day, $year) && $year >= 1927;
        if ($dobOk) {
            try {
                $dob = \Carbon\Carbon::createFromDate($year, $month, $day)->startOfDay();
                $dobOk = $dob->lte(now()->subYears(18)->startOfDay());
            } catch (\Exception $e) {
                $dobOk = false;
            }
        }
        if (!$dobOk) {
            Session::flash('message', 'danger|Please enter a valid date of birth. You must be at least 18.');
            return redirect()->route('register', ['mode' => 'start'])->withInput();
        }

        Session::put('register_first_name', trim($request->first_name));
        Session::put('register_last_name', trim($request->last_name));
        Session::put('register_day', str_pad((string) $day, 2, '0', STR_PAD_LEFT));
        Session::put('register_month', str_pad((string) $month, 2, '0', STR_PAD_LEFT));
        Session::put('register_year', (string) $year);

        return redirect()->route('register', ['mode' => 'community']);
    }

    /**
     * Step 2: religion, community (caste), living in (country).
     */
    public function saveCommunity(Request $request)
    {
        if (!Session::has('register_first_name') || !Session::has('register_year')) {
            Session::flash('message', 'danger|Please enter your name and date of birth first.');
            return redirect()->route('register');
        }

        $request->validate([
            'religion' => 'required|string|max:50',
            'caste' => 'required|string|max:50',
            'country' => 'required|string|max:50',
        ], [
            'religion.required' => 'Please select your religion.',
            'caste.required' => 'Please select your community.',
            'country.required' => 'Please select where you live.',
        ]);

        $prevCountry = Session::get('register_country');
        Session::put('register_religion', $request->religion);
        Session::put('register_caste', $request->caste);
        Session::put('register_country', $request->country);

        // Country change invalidates state/city from a previous attempt
        if ((string) $prevCountry !== (string) $request->country) {
            Session::forget(['register_state', 'register_city']);
        }

        return redirect()->route('register', ['mode' => 'contact']);
    }

    /**
     * Step 3: email + mobile only (no OTP). OTP happens in a later step.
     */
    public function saveContact(Request $request)
    {
        if (!Session::has('register_first_name') || !Session::has('register_year')) {
            Session::flash('message', 'danger|Please enter your name and date of birth first.');
            return redirect()->route('register');
        }

        if (!Session::has('register_religion') || !Session::has('register_country')) {
            Session::flash('message', 'danger|Please select religion, community and country.');
            return redirect()->route('register', ['mode' => 'community']);
        }

        $request->validate([
            'email' => 'required|email',
            'country_code' => 'required|string|max:5',
            'mobile' => 'required|string|max:20',
        ]);

        $email = strtolower(trim($request->email));
        $google = Session::get('google_oauth');
        if (!empty($google['email'])) {
            $email = strtolower($google['email']);
        }

        if (User::where('email', $email)->exists()) {
            if (!empty($google)) {
                Session::flash('message', 'danger|This email ID is already registered. Please login instead.');
                return redirect()->route('login');
            }
            return redirect()->route('register', ['mode' => 'contact'])
                ->withInput()
                ->with('email_taken', true)
                ->with('message', 'danger|This email ID is already registered.');
        }

        $countryCode = preg_replace('/\D+/', '', $request->country_code);
        $local = preg_replace('/\D+/', '', $request->mobile);
        $full = $this->normalizePhoneNumber($countryCode . $local);

        if (!$this->validPhoneNumber($full)) {
            Session::flash('message', 'danger|Please enter a valid mobile number.');
            return redirect()->route('register', ['mode' => 'contact'])->withInput();
        }

        if (User::where('contact_mobile_number', $full)->exists()) {
            Session::flash('message', 'danger|This mobile number is already registered. Please login.');
            return redirect()->route('register', ['mode' => 'contact'])->withInput();
        }

        $previousEmail = strtolower((string) Session::get('register_email', ''));

        Session::put('register_email', $email);
        Session::put('register_mobile', $full);
        Session::put('register_mobile_local', $local);
        Session::put('register_country_code', $countryCode);
        Session::put('register_contact_saved', true);
        Session::forget('register_otp_email');

        // Google email already trusted — skip email OTP, go to profile build
        if (!empty($google['id'])) {
            Session::put('register_verified', true);
            if (Session::has('register_education')) {
                return redirect()->route('register', ['mode' => 'build4']);
            }
            return redirect()->route('register', ['mode' => 'build']);
        }

        // Same email already verified — resume (mobile-only edit doesn't need OTP again)
        if (Session::get('register_verified') && $previousEmail !== '' && $previousEmail === $email) {
            if (Session::has('register_education')) {
                return redirect()->route('register', ['mode' => 'build4']);
            }
            return redirect()->route('register', ['mode' => 'build']);
        }

        // New/changed email — require OTP
        Session::forget('register_verified');

        // Local development: SMTP isn't configured/working here, so skip the email
        // OTP step entirely instead of failing to send. Never bypasses in production
        // or staging — only when APP_ENV is local or development.
        if (app()->environment('local', 'development')) {
            Session::put('register_verified', true);
            Session::flash('message', 'success|Local environment — email OTP skipped.');
            Log::info('Register OTP skipped (APP_ENV=local) for ' . $email);
            if (Session::has('register_education')) {
                return redirect()->route('register', ['mode' => 'build4']);
            }
            return redirect()->route('register', ['mode' => 'build']);
        }

        // Send email OTP immediately, then show verify screen
        $identifier = 'reg:' . $email;
        $plainCode = (string) random_int(100000, 999999);
        try {
            Mail::to($email)->send(new RegisterOtpMail($plainCode, $email));
            LoginOtp::issue($identifier, $plainCode, 'email', $email);
            Session::put('register_otp_email', $email);
            Session::put('register_email_masked', $this->maskEmail($email));
            Session::flash('message', 'success|OTP sent to ' . $this->maskEmail($email) . '. Check inbox/spam.');
            Log::info('Register OTP emailed to ' . $email);
        } catch (\Exception $e) {
            Log::error('Register OTP send failed: ' . $e->getMessage());
            Session::flash('message', 'danger|Could not send OTP right now. Please try again from the next screen.');
        }

        return redirect()->route('register', ['mode' => 'otp']);
    }

    public function sendOtp(Request $request)
    {
        if (!Session::get('register_contact_saved') && !Session::has('register_email')) {
            Session::flash('message', 'danger|Please enter email and mobile first.');
            return redirect()->route('register', ['mode' => 'contact']);
        }

        $email = Session::get('register_email');
        $countryCode = Session::get('register_country_code', '92');
        $local = Session::get('register_mobile_local');

        // Allow resend form to pass values
        if ($request->filled('email')) {
            $email = strtolower(trim($request->email));
        }
        if ($request->filled('country_code')) {
            $countryCode = preg_replace('/\D+/', '', $request->country_code);
        }
        if ($request->filled('mobile')) {
            $local = preg_replace('/\D+/', '', $request->mobile);
        }

        if (!$email || !$local) {
            Session::flash('message', 'danger|Please enter email and mobile first.');
            return redirect()->route('register', ['mode' => 'contact']);
        }

        $google = Session::get('google_oauth');
        if (!empty($google['email'])) {
            $email = strtolower($google['email']);
        }

        if (User::where('email', $email)->exists() && empty($google)) {
            Session::flash('message', 'danger|This email ID is already registered.');
            return redirect()->route('register', ['mode' => 'contact'])->withInput();
        }

        $full = $this->normalizePhoneNumber($countryCode . $local);
        if (!$this->validPhoneNumber($full)) {
            Session::flash('message', 'danger|Please enter a valid mobile number.');
            return redirect()->route('register', ['mode' => 'contact'])->withInput();
        }

        Session::put('register_email', $email);
        Session::put('register_mobile', $full);
        Session::put('register_mobile_local', $local);
        Session::put('register_country_code', $countryCode);

        if (!empty($google['id'])) {
            Session::put('register_verified', true);
            return redirect()->route('register', ['mode' => 'build']);
        }

        // Local development: SMTP isn't configured/working here, so skip the email
        // OTP step entirely instead of failing to send.
        if (app()->environment('local', 'development')) {
            Session::put('register_verified', true);
            Session::flash('message', 'success|Local environment — email OTP skipped.');
            Log::info('Register OTP skipped (APP_ENV=local) for ' . $email);
            return redirect()->route('register', ['mode' => 'build']);
        }

        $identifier = 'reg:' . $email;
        $cooldown = (int) config('otp.resend_cooldown_seconds', 60);
        $recent = LoginOtp::where('identifier', $identifier)
            ->where('created_at', '>=', now()->subSeconds($cooldown))
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if ($recent) {
            Session::flash('message', 'warning|Please wait a moment before requesting another OTP.');
            return redirect()->route('register', ['mode' => 'otp']);
        }

        $plainCode = (string) random_int(100000, 999999);

        try {
            Mail::to($email)->send(new RegisterOtpMail($plainCode, $email));
            LoginOtp::issue($identifier, $plainCode, 'email', $email);

            Session::put('register_otp_email', $email);
            Session::put('register_email_masked', $this->maskEmail($email));
            Session::forget('register_verified');

            Session::flash('message', 'success|OTP sent to ' . $this->maskEmail($email) . '. Check inbox/spam.');
            Log::info('Register OTP emailed to ' . $email);

            return redirect()->route('register', ['mode' => 'otp']);
        } catch (\Exception $e) {
            Log::error('Register OTP send failed: ' . $e->getMessage());
            Session::flash('message', 'danger|Could not send OTP right now. Please try again.');
            return redirect()->route('register', ['mode' => 'otp']);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:4|max:8',
        ]);

        $email = Session::get('register_otp_email') ?: Session::get('register_email');
        if (!$email) {
            Session::flash('message', 'danger|OTP session expired. Please enter your email again.');
            return redirect()->route('register', ['mode' => 'contact']);
        }

        $identifier = 'reg:' . $email;
        $otpRow = LoginOtp::where('identifier', $identifier)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otpRow || !$otpRow->isValid()) {
            Session::flash('message', 'danger|OTP expired or too many attempts. Please request a new code.');
            return redirect()->route('register', ['mode' => 'otp']);
        }

        $code = preg_replace('/\D+/', '', $request->otp);
        if (!$otpRow->matches($code)) {
            $otpRow->incrementAttempts();
            Session::flash('message', 'danger|Incorrect OTP. Please try again.');
            return redirect()->route('register', ['mode' => 'otp']);
        }

        $otpRow->markConsumed();
        Session::put('register_verified', true);
        Session::forget(['register_otp_email', 'register_email_masked']);

        // Resume later steps if user re-verified after editing contact
        if (Session::has('register_password') && Session::has('register_mother_tongue')) {
            return $this->completeRegistration();
        }
        if (Session::has('register_education')) {
            return redirect()->route('register', ['mode' => 'build4']);
        }
        if (Session::has('register_height')) {
            return redirect()->route('register', ['mode' => 'build3']);
        }
        if (Session::has('register_city')) {
            return redirect()->route('register', ['mode' => 'build2']);
        }

        return redirect()->route('register', ['mode' => 'build']);
    }

    /**
     * After OTP: State + City.
     */
    public function saveBuild(Request $request)
    {
        if (!Session::get('register_verified')) {
            Session::flash('message', 'danger|Please verify your email first.');
            return redirect()->route('register');
        }

        $request->validate([
            'state' => 'required|string|max:50',
            'city' => 'required|string|max:50',
        ], [
            'state.required' => 'Please select the state you live in.',
            'city.required' => 'Please select the city you live in.',
        ]);

        Session::put('register_state', $request->state);
        Session::put('register_city', $request->city);

        return redirect()->route('register', ['mode' => 'build2']);
    }

    /**
     * Build step 2: marital status + height (no diet).
     */
    public function saveBuild2(Request $request)
    {
        if (!Session::get('register_verified') || !Session::has('register_city')) {
            return redirect()->route('register', ['mode' => 'build']);
        }

        $request->validate([
            'marital_status' => 'required|string|max:50',
            'height' => 'required|string|max:30',
            'gender' => 'required|in:male,female',
        ], [
            'marital_status.required' => 'Please select your marital status.',
            'height.required' => 'Please select your height.',
            'gender.required' => 'Please select your gender.',
        ]);

        Session::put('register_marital_status', $request->marital_status);
        Session::put('register_height', $request->height);
        Session::put('register_gender', $request->gender);

        return redirect()->route('register', ['mode' => 'build3']);
    }

    /**
     * Build step 3: highest qualification + profession.
     */
    public function saveBuild3(Request $request)
    {
        if (!Session::get('register_verified') || !Session::has('register_height')) {
            return redirect()->route('register', ['mode' => 'build2']);
        }

        $request->validate([
            'education' => 'required|string|max:50',
            'profession' => 'required|string|max:100',
        ], [
            'education.required' => 'Please select your highest qualification.',
            'profession.required' => 'Please enter your profession.',
        ]);

        Session::put('register_education', $request->education);
        Session::put('register_profession', trim($request->profession));

        return redirect()->route('register', ['mode' => 'preferences']);
    }

    /**
     * Step (Website Upgrade Brief §5 "Step 3 - Partner Preferences"). Maps
     * onto the r* columns already used by the profile-edit "Partner
     * Expectation" section (ProfileController@updateProfile) — this just
     * captures a starter set of them at signup instead of leaving the
     * section fully blank until the member edits their profile later.
     */
    public function savePreferences(Request $request)
    {
        if (!Session::get('register_verified') || !Session::has('register_education')) {
            return redirect()->route('register', ['mode' => 'build3']);
        }

        $request->validate([
            'r_age' => 'nullable|string|max:30',
            'r_height' => 'nullable|string|max:30',
            'r_marital_status' => 'nullable|string|max:50',
            'r_religion' => 'nullable|string|max:50',
            'r_gen_req' => 'nullable|string|max:255',
        ]);

        Session::put('register_r_age', trim((string) $request->r_age));
        Session::put('register_r_height', trim((string) $request->r_height));
        Session::put('register_r_marital_status', $request->r_marital_status);
        Session::put('register_r_religion', $request->r_religion);
        Session::put('register_r_gen_req', trim((string) $request->r_gen_req));
        Session::put('register_preferences_saved', true);

        return redirect()->route('register', ['mode' => 'build4']);
    }

    /**
     * Build step 4: sect, on behalf, mother tongue, password (from old register form).
     */
    public function saveBuild4(Request $request)
    {
        if (!Session::get('register_verified') || !Session::get('register_preferences_saved')) {
            return redirect()->route('register', ['mode' => Session::has('register_education') ? 'preferences' : 'build3']);
        }

        $request->validate([
            'sect' => 'required|string|max:100',
            'on_behalf' => 'required|string|max:50',
            'mother_tongue' => 'required|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'sect.required' => 'Please enter your sect.',
            'on_behalf.required' => 'Please select on whose behalf you are registering.',
            'mother_tongue.required' => 'Please select mother tongue.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        Session::put('register_sect', trim($request->sect));
        Session::put('register_on_behalf', $request->on_behalf);
        Session::put('register_mother_tongue', $request->mother_tongue);
        Session::put('register_password', $request->password);

        // Single OTP already done after contact — create account now
        return $this->completeRegistration();
    }

    /**
     * Create account from session after email OTP + profile steps.
     */
    private function completeRegistration()
    {
        $email = Session::get('register_email');
        $mobile = Session::get('register_mobile');

        if (!$email || !$mobile || !Session::get('register_verified')) {
            Session::flash('message', 'danger|Registration session expired. Please start again.');
            return redirect()->route('register');
        }

        if (User::where('email', $email)->exists()) {
            $this->forgetRegisterSession();
            Session::flash('message', 'danger|Email already exists. Please login.');
            return redirect()->route('login');
        }

        if (!$this->validPhoneNumber($mobile)) {
            Session::flash('message', 'danger|Invalid mobile number.');
            return redirect()->route('register', ['mode' => 'contact']);
        }

        if (User::where('contact_mobile_number', $mobile)->exists()) {
            Session::flash('message', 'danger|This mobile number is already registered. Please login or use another number.');
            return redirect()->route('register', ['mode' => 'contact']);
        }

        if (!Session::has('register_password') || !Session::has('register_mother_tongue') || !Session::has('register_gender') || !Session::has('register_city')) {
            Session::flash('message', 'danger|Please complete all profile steps before creating your account.');
            if (!Session::has('register_city')) {
                return redirect()->route('register', ['mode' => 'build']);
            }
            if (!Session::has('register_gender')) {
                return redirect()->route('register', ['mode' => 'build2']);
            }
            return redirect()->route('register', ['mode' => 'build4']);
        }

        $data = [
            'first_name' => Session::get('register_first_name'),
            'last_name' => Session::get('register_last_name'),
            'day' => Session::get('register_day'),
            'month' => Session::get('register_month'),
            'year' => Session::get('register_year'),
            'gender' => Session::get('register_gender', 'male'),
            'service_type' => Session::get('register_service_type'),
            'email' => $email,
            'mobile' => $mobile,
            'country' => Session::get('register_country'),
            'state' => Session::get('register_state'),
            'city' => Session::get('register_city'),
            'religion' => Session::get('register_religion'),
            'caste' => Session::get('register_caste'),
            'sect' => Session::get('register_sect', ''),
            'marital_status' => Session::get('register_marital_status'),
            'height' => Session::get('register_height'),
            'education' => Session::get('register_education'),
            'profession' => Session::get('register_profession'),
            'on_behalf' => Session::get('register_on_behalf', 'Self'),
            'mother_tongue' => Session::get('register_mother_tongue', ''),
            'password' => Session::get('register_password'),
            // Partner Preferences (brief §5 step 3) — same r* columns the
            // profile-edit "Partner Expectation" section already uses.
            'r_age' => Session::get('register_r_age', ''),
            'r_height' => Session::get('register_r_height', ''),
            'r_marital_status' => Session::get('register_r_marital_status', ''),
            'r_religion' => Session::get('register_r_religion', ''),
            'r_gen_req' => Session::get('register_r_gen_req', ''),
        ];

        try {
            $user = $this->create($data);
            event(new Registered($user));
            $this->guard()->login($user);
            $this->forgetRegisterSession();

            Session::flash('message', 'success|Profile created successfully. Welcome!');
            Log::info('New user registered (Name: ' . $user->first_name . ' ' . $user->last_name . ', Email: ' . $user->email . ')');

            // Website Upgrade Brief §5 "Mandatory photo policy" — send new
            // accounts to the required-photos gate before their normal
            // post-registration destination (ProfileController@mustUploadPhotos
            // skips straight through once 2 photos exist).
            Session::put('photos_gate_redirect', $this->redirectPath());
            return redirect()->route('member.photos.required');
        } catch (\Exception $e) {
            Log::error('Registration complete failed: ' . $e->getMessage());

            // User may already exist if create succeeded but login/event failed
            if (User::where('email', $email)->exists()) {
                $this->forgetRegisterSession();
                Session::flash('message', 'warning|Account was created. Please login to continue.');
                return redirect()->route('login');
            }

            Session::flash('message', 'danger|Could not create profile. Please try again.');
            return redirect()->route('register', ['mode' => 'build4']);
        }
    }

    /** Legacy routes — second OTP removed; keep redirects so old bookmarks don't break. */
    public function verifyMobile()
    {
        return redirect()->route('register', ['mode' => Session::get('register_preferences_saved') ? 'build4' : (Session::has('register_education') ? 'preferences' : 'build')]);
    }

    public function sendMobileOtp()
    {
        return redirect()->route('register', ['mode' => Session::get('register_preferences_saved') ? 'build4' : (Session::has('register_education') ? 'preferences' : 'build')]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, []);
    }

    protected function create(array $data)
    {
        $payload = [
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'service_type' => $data['service_type'] ?? null,
            'email' => $data['email'],
            'contact_mobile_number' => $this->getNormalizedPhoneNumber($data['mobile']),
            'height' => $data['height'],
            'birthday' => $data['year'] . '-' . $data['month'] . '-' . $data['day'],
            'mobile_country' => $data['country'],
            'con_of_residence' => $data['country'],
            'state' => $data['state'] ?? null,
            'city' => $data['city'],
            'religion' => $data['religion'],
            'caste' => $data['caste'],
            'sect' => $data['sect'],
            'profile_for' => $data['on_behalf'],
            'mother_tongue' => $data['mother_tongue'],
            'marital_status' => $data['marital_status'],
            'education' => $data['education'],
            'profession' => $data['profession'],
            'password' => Hash::make($data['password']),
        ];

        $google = Session::get('google_oauth');
        if (!empty($google['id']) && !empty($google['email']) && strcasecmp($google['email'], $data['email']) === 0) {
            $payload['google_id'] = $google['id'];
        }

        $user = User::create($payload);

        // Verified via register OTP or Google
        if (!empty($payload['google_id']) || Session::get('register_verified')) {
            $user->email_verified_at = now();
        }

        // Partner Preferences (r* columns) aren't mass-assignable — set them
        // directly, same as ProfileController@updateProfile's "partner_expectation"
        // section does when a member edits these later.
        $user->rage = $data['r_age'] ?? '';
        $user->rheight = $data['r_height'] ?? '';
        $user->rmarital_status = $data['r_marital_status'] ?? '';
        $user->rreligion = $data['r_religion'] ?? '';
        $user->rgen_req = $data['r_gen_req'] ?? '';
        $user->save();

        // Session cleared in completeRegistration() after successful login

        return $user;
    }

    /**
     * Override Laravel Auth::routes() POST /register — do not create users from raw form posts.
     */
    public function register(Request $request)
    {
        if (Session::get('register_verified') && Session::has('register_password') && Session::has('register_mother_tongue')) {
            return $this->completeRegistration();
        }

        if (Session::has('register_education')) {
            return redirect()->route('register', ['mode' => 'build4']);
        }

        Session::flash('message', 'danger|Please complete registration steps to create your profile.');
        return redirect()->route('register');
    }

    private function getNormalizedPhoneNumber($n)
    {
        return $this->normalizePhoneNumber($n);
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

        $n = Str::remove(' ', $n);
        $n = Str::remove('-', $n);

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

    private function validPhoneNumber($n)
    {
        $n = preg_replace('/\D+/', '', (string) $n);
        return preg_match('/^\d+$/', $n) == 1 && strlen($n) >= 7;
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

    private function forgetRegisterSession(): void
    {
        Session::forget([
            'google_oauth',
            'register_service_type',
            'register_verified',
            'register_email',
            'register_mobile',
            'register_mobile_local',
            'register_country_code',
            'register_otp_email',
            'register_email_masked',
            'register_first_name',
            'register_last_name',
            'register_day',
            'register_month',
            'register_year',
            'register_religion',
            'register_caste',
            'register_country',
            'register_state',
            'register_city',
            'register_sect',
            'register_marital_status',
            'register_height',
            'register_gender',
            'register_education',
            'register_profession',
            'register_r_age',
            'register_r_height',
            'register_r_marital_status',
            'register_r_religion',
            'register_r_gen_req',
            'register_preferences_saved',
            'register_on_behalf',
            'register_mother_tongue',
            'register_password',
            'register_contact_saved',
        ]);
    }

    /**
     * Issue register OTP email (used when entering OTP step / resend).
     */
    private function dispatchRegisterOtp(string $email): bool
    {
        $email = strtolower(trim($email));
        $identifier = 'reg:' . $email;
        $cooldown = (int) config('otp.resend_cooldown_seconds', 60);
        $recent = LoginOtp::where('identifier', $identifier)
            ->where('created_at', '>=', now()->subSeconds($cooldown))
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if ($recent) {
            Session::put('register_otp_email', $email);
            Session::put('register_email_masked', $this->maskEmail($email));
            return true;
        }

        $plainCode = (string) random_int(100000, 999999);

        try {
            Mail::to($email)->send(new RegisterOtpMail($plainCode, $email));
            LoginOtp::issue($identifier, $plainCode, 'email', $email);
            Session::put('register_otp_email', $email);
            Session::put('register_email_masked', $this->maskEmail($email));
            Session::forget('register_verified');
            Session::flash('message', 'success|OTP sent to ' . $this->maskEmail($email) . '. Check inbox/spam.');
            Log::info('Register OTP emailed to ' . $email);
            return true;
        } catch (\Exception $e) {
            Log::error('Register OTP send failed: ' . $e->getMessage());
            Session::flash('message', 'danger|Could not send OTP right now. Please try again.');
            return false;
        }
    }
}
