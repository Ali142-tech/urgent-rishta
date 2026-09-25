<?php

namespace App\Http\Controllers;

use App\Traits\HandlesImageUploads;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Public "Become a Partner" signup for prospective matchmakers (client
 * request, Sep 2026). Deliberately creates a normal `users` row — same
 * table every other role in this app already lives in (self-registered
 * members, admins, team members, team-added proposals) — rather than a
 * separate table, so approving them is just flipping the same
 * is_team_member flag every other team member has, no migration/merging
 * needed.
 *
 * NOT the OTP-gated self-registration wizard (RegisterController) — a
 * matchmaker applicant isn't filling in a matrimonial profile (no gender/
 * DOB collected here), just applying for a role, so this is one direct
 * form submission, closer to TeamController::store()'s shell-record
 * pattern.
 *
 * Login itself stays fully blocked until an admin approves the application
 * (see User::matchmakerLoginBlockMessage()/LoginController::finishLogin())
 * — client requirement, Sep 2026: a member/matchmaker account and a team
 * member account are two fully separate roles now, never both at once, so
 * there's no "log in as a plain member in the meantime" state to allow.
 * Once approved, the account only ever reaches the Team Dashboard, never
 * the Member Dashboard (see User::isMatchmakerOnly() and
 * EnsureTeamMembersUseTeamDashboard).
 */
class MatchmakerApplicationController extends Controller
{
    use HandlesImageUploads;

    public function create()
    {
        return view('matchmaker.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'contact_mobile_number' => 'required|string|max:30',
            'city' => 'required|string|max:100',
            'experience' => 'required|string|max:255',
            'about_me' => 'required|string|max:2000',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'required|image|max:5120',
        ]);

        $user = User::create([
            'dataid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9)),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_mobile_number' => $request->contact_mobile_number,
            'city' => $request->city,
            'experience' => $request->experience,
            'about_me' => $request->about_me,
            'password' => Hash::make($request->password),
        ]);
        // Direct property writes, not mass-assignment — see User::$fillable's
        // exclusion docblock. active=1 is unrelated to login gating (that's
        // matchmaker_status's job, checked separately at login) — this just
        // keeps the account out of any "inactive/deleted" admin views.
        $user->active = 1;
        $user->matchmaker_status = 'pending';
        // No dating profile is ever built for this row (no gender/DOB
        // collected above), so the two member-facing gates that would
        // otherwise fire on first login don't apply: email ownership isn't
        // in question (an admin reviews the application by contacting them
        // directly) and there are no profile photos to verify. Login itself
        // still stays fully blocked until approved — see
        // User::matchmakerLoginBlockMessage() / LoginController::finishLogin().
        $user->email_verified_at = now();
        $user->photo_verification_status = 'verified';
        $user->save();

        if ($request->hasFile('image')) {
            $this->storeApplicantPhoto($user, $request->file('image'));
        }

        Log::info('New matchmaker application: ' . $user->dataid . ' (' . $user->first_name . ' ' . $user->last_name . ', ' . $user->email . ')');

        Session::flash('message', 'success|Thanks for applying! Our team will review your application and get back to you shortly.');
        return redirect()->route('matchmaker.apply.thanks');
    }

    public function thanks()
    {
        return view('matchmaker.thanks');
    }

    /** Same storage convention as every other photo upload in this app (see app/Traits/HandlesImageUploads.php). */
    private function storeApplicantPhoto(User $user, $image): bool
    {
        $imageExtension = $this->validateUploadedImage($image);
        if (!$imageExtension) {
            return false;
        }

        $name = time() . '_' . $user->id . '.' . $imageExtension;
        $rootImgPath = \App\Profile::MEMBER_IMAGES_PATH;
        $publicPath = public_path($rootImgPath);
        $image->move($publicPath, $name);

        $salt = random_int(111, 99999);
        $manager = $this->createImageManager();
        if ($manager) {
            $this->generateBlurAndThumbnails($manager, $publicPath, $name, $salt);
        } else {
            $this->copyDerivativesWithoutProcessing($publicPath, $name, $salt);
        }

        $img = new \App\Images();
        $img->dataid = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
        $img->name = $name;
        $img->user_id = $user->id;
        $img->img_url = $rootImgPath . '/' . $name;
        $img->salt = $salt;
        $img->visibility = 'Public';
        $img->displaypic = 1;
        $img->save();

        return true;
    }
}
