<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\User;
use App\MasterData;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

     /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = "/packages";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    public function emailInUse() {
        return response()->json([
            'code' => '200',
            'data' => User::where('email', request()['e'])->count()
        ]);
    }

    public function showRegistrationForm() {
        $religions = MasterData::where('type', 'RELIGION')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        $googleOAuth = Session::get('google_oauth');
        return view('auth.register', compact(
            'religions', 'maritalstatuses', 'mothertongues', 'education', 'countries', 'caste', 'googleOAuth'
        ));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            // 'name' => ['required', 'string', 'max:255'],
            // 'email' => ['unique:users']
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return View
     */

    protected function create(array $data) {
        $payload = [
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'contact_mobile_number' => $this->getNormalizedPhoneNumber($data['mobile']),
            'height' => $data['height'],
            'birthday' => $data['year'] . '-' . $data['month'] . '-' . $data['day'],
            'mobile_country' => $data['country'],
            'con_of_residence' => $data['country'],
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

        if (!empty($payload['google_id'])) {
            $user->email_verified_at = now();
            $user->save();
            Session::forget('google_oauth');
        }

        return $user;
    }

    public function Register(Request $request) {
        $existing = User::where('email',$request->email)->get();

        if (empty($existing) || $existing->count()==0) {

            // Combine selected country code and local number, then validate
            $rawMobile = ($request->mobile_country_code ?? '') . $request->mobile;
            $normalizedInput = preg_replace('/\D+/', '', $rawMobile);

            if (!$this->validPhoneNumber($normalizedInput)) {
                Session::flash('message', 'danger|You must provide a valid WhatsApp number. Select your country code and enter the number without leading zero, spaces or dashes. Example: 92 (code) + 3331234567 (number) will be saved as 923331234567.');
                return redirect()->route('register')->withInput();
            }

            $data = $request->all();
            $data['mobile'] = $normalizedInput;

            event(new Registered($user = $this->create($data)));

            if (!empty($user->google_id) && $user->hasVerifiedEmail()) {
                Session::flash('message', 'success|Registration complete. Your Google email is verified.');
            } else {
                Session::flash('message','success|A verification email has been sent to the email address. Please verify email for account activation. Check spam/junk folder if not found.');
            }
            Log::info("New user registered (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ")");

            if ($response = $this->registered($request, $user)) {
                return $response;
            }

            return $request->wantsJson()
                        ? new Response('', 201)
                        : redirect($this->redirectPath());
        } else {
            Log::info("User already registered with email : ".$request->email);
            Session::flash('message', 'danger|Email already exists in the system. Please use a different email address.');
            return redirect()->route('register')->withInput();
        }
    }

    private function getNormalizedPhoneNumber($n) {
        $pkCodes = ['300', '301', '302', '303', '304', '305', '306', '307', '308', '309', '310', '311', '312', '313', '314', '315', '316', '317', '318', '320', '321', '322', '323', '324', '330', '331', '332', '333', '334', '335', '336', '337', '340', '341', '342', '343', '344', '345', '346', '347', '348', '349', '355'];

        $n = Str::remove(' ', $n);
        $n = Str::remove('-', $n);

        foreach ($pkCodes as $code) {
            if (Str::startsWith($n, $code)) {
                return "92" . $n;
            }
        }

        if (Str::startsWith($n, "00")) {
            return Str::substr($n, 2);
        }

        if (Str::startsWith($n, "0")) {
            return "92". Str::substr($n, 1);
        }

        return $n;
    }

    private function validPhoneNumber($n) {
        $n = Str::remove(' ', $n);
        $n = Str::remove('-', $n);

        return preg_match('/^\d+$/', $n) == 1 &&
               Str::Length($n) != 0 && Str::Length($n) >= 7;
    }
}
