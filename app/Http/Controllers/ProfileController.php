<?php

namespace App\Http\Controllers;

use App\Mail\InterestSent as InterestSentMail;
use App\Mail\InterestAccepted as InterestAcceptedMail;
use App\Mail\InterestDeclined as InterestDeclinedMail;
use App\User;
use App\Interest;
use App\MasterData;
use App\Images;
use App\Filtered;
use App\AllowedProfiles;
use App\Profile;
use App\Notifications\InterestAccepted;
use App\Notifications\InterestDeclined;
use App\Notifications\InterestSent;
use App\Notifications\UserFollowed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use Mail;

class ProfileController extends Controller
{
    /** Website Upgrade Brief §5 "Mandatory photo policy": minimum before a profile is Active. */
    const REQUIRED_PHOTO_COUNT = 2;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function profile(Request $request, $dataid = null)
    {
        $profile = null;
        if ($dataid) {
            $profile = User::retrieveUserObject($dataid)->profile();
        } else $profile = User::retrieveUserObject()->profile();

        $religions = MasterData::where('type', 'RELIGION')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();
        // Only the own-profile "My Profile" page shows the completeness meter —
        // it's about nudging the logged-in member to finish their own profile,
        // not something shown while browsing someone else's.
        $completeness = $dataid ? null : $profile->profileCompleteness();

        return view($dataid ? 'member.profile' : 'member.user', compact('profile', 'religions', 'maritalstatuses', 'mothertongues', 'education', 'countries', 'caste', 'completeness'));
    }

    public function preferencesPage()
    {
        $user = User::retrieveUserObject();
        $preference = $user->partnerPreference()->first();

        $religions = MasterData::where('type', 'RELIGION')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        $maritalstatuses = MasterData::where('type', 'MARITAL_STATUS')->orderBy('name', 'ASC')->get();
        $mothertongues = MasterData::where('type', 'MOTHER_TONGUE')->orderBy('name', 'ASC')->get();
        $education = MasterData::where('type', 'EDUCATION')->orderBy('name', 'ASC')->get();
        $countries = MasterData::where('type', 'COUNTRY')->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
        // Religion->caste cascading isn't actually wired to a live route anywhere
        // in this app today (the JS for it on the My Profile page calls an
        // endpoint that was never routed) — same as ProfileController::profile(),
        // show the full flat caste list rather than pretend to filter it.
        $caste = MasterData::where('type', 'CASTE')->orderBy('name', 'ASC')->get();

        return view('member.preferences', compact(
            'preference', 'religions', 'maritalstatuses', 'mothertongues', 'education', 'countries', 'caste'
        ));
    }

    public function updatePreferences(Request $request)
    {
        $user = User::retrieveUserObject();

        \App\PartnerPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'age_min' => $request->age_min !== '' ? $request->age_min : null,
                'age_max' => $request->age_max !== '' ? $request->age_max : null,
                'height' => $request->height,
                'weight' => $request->weight,
                'marital_status' => $request->marital_status,
                'with_children' => $request->with_children,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'religion_id' => $request->religion_id,
                'caste_id' => $request->caste_id,
                'sect' => $request->sect,
                'education_id' => $request->education_id,
                'profession' => $request->profession,
                'mother_tongue_id' => $request->mother_tongue_id,
                'languages' => is_array($request->preferred_languages) ? implode(',', $request->preferred_languages) : $request->preferred_languages,
                'preferred_country_id' => $request->preferred_country_id,
                'general_requirement' => $request->general_requirement,
            ]
        );

        Log::info("User (" . $user->dataid . ") updated their partner preferences.");
        User::retrieveUserObject($user->dataid, true);

        Session::flash('message', 'success|Your partner preferences have been saved.');
        return redirect()->route('member.preferences');
    }

    public function notifications()
    {
        return User::retrieveUserObject()->unreadNotifications()->limit(5)->get()->toArray();
    }

    public function accountTerminate()
    {
        $user = User::retrieveUserObject();

        $message = null;

        if (request()->ajax()) {
            try {
                Auth::logout();
                Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") deleted their account.");
                $message = 'success|We are sorry to see you go. Account has been deleted. You will need to register again if you want to use this website.';
            } catch (\Exception $e) {
                Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") could not delete their account. Error: " . $e->getMessage());
                $message = 'danger|There was an error deleting your account. Please try again later and contact admin if the error persists.';
            }
            return [
                'code' => '200',
                //'html' => $this->profile($request),//->renderSections()['main-content'],
                'message' => $message
            ];
        } else return [
            'code' => '200'
        ];
    }

    public function updatePassword(Request $request)
    {
        $user = User::retrieveUserObject();
        try {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();

                Session::flash('message', 'success|Password updated successfully. Please login with the new password.');
                Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") updated their password.");
                Auth::logout();

                User::retrieveUserObject($user->dataid, true);
                return redirect('login');
            } else {
                Session::flash('message', 'danger|Current password could not be verified. Try again later.');
                Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") could not update their password. Password mismatch!!");
            }
        } catch (\Exception $e) {
            Session::flash('message', 'danger|There was an error updating the password. Please try again later and contact admin if the error persists.');
            Log::info("User (Name: " . $user->first_name . " " . $user->last_name . ", Email: " . $user->email . ") could not update their password. Error: " . $e->getMessage());
        }
        return redirect()->back();
    }

    /**
     * Website Upgrade Brief §5 "Mandatory photo policy" — shown right after
     * registration (see RegisterController@completeRegistration) so a new
     * account uploads its 2 required photos + a live selfie before the
     * account goes into admin review. Reuses the existing
     * uploadImages()/Images model — nothing new stored, just a dedicated
     * screen + a minimum-requirement gate around it.
     *
     * Once satisfied, the account is logged out and sent to admin review —
     * it does NOT proceed into the site. It can't log back in until an
     * admin approves the photos (see User::photoVerificationBlockMessage(),
     * enforced in LoginController@finishLogin and GoogleAuthController).
     */
    public function mustUploadPhotos()
    {
        $user = User::retrieveUserObject();
        $count = Images::where('user_id', $user->id)->where('is_selfie', 0)->count();
        $hasSelfie = Images::where('user_id', $user->id)->where('is_selfie', 1)->exists();

        if ($count >= self::REQUIRED_PHOTO_COUNT && $hasSelfie) {
            session()->forget('photos_gate_redirect');
            // Covers both a fresh registration (already 'pending', harmless
            // no-op) and a reopened resubmission ('resubmit' -> 'pending' so
            // it lands back in the admin queue instead of staying invisible).
            $user->photo_verification_status = 'pending';
            $user->save();
            Auth::logout();
            Log::info('User (' . $user->dataid . ') completed registration photos — pending admin review.');
            Session::flash('message', 'success|Thanks! Your profile and photos have been submitted for review. We will email you once your account is verified.|10000');
            return redirect()->route('login');
        }

        return view('member.photos-required', [
            'count' => $count,
            'required' => self::REQUIRED_PHOTO_COUNT,
            'hasSelfie' => $hasSelfie,
        ]);
    }

    /**
     * Lightweight JSON poll used by the gate page's JS after each upload —
     * avoids changing uploadImages()'s existing response contract (other
     * pages already depend on it returning full profile HTML).
     */
    public function photosRequiredStatus()
    {
        $user = User::retrieveUserObject();
        $count = Images::where('user_id', $user->id)->where('is_selfie', 0)->count();
        $hasSelfie = Images::where('user_id', $user->id)->where('is_selfie', 1)->exists();

        return [
            'code' => '200',
            'count' => $count,
            'required' => self::REQUIRED_PHOTO_COUNT,
            'satisfied' => $count >= self::REQUIRED_PHOTO_COUNT && $hasSelfie,
            'has_selfie' => $hasSelfie,
        ];
    }

    public function uploadImages(Request $request)
    {
        $user = User::retrieveUserObject();
        $images = $request->images;
        $id = $user->id;
        $dataid = $user->dataid;
        $message = null;
        if (!empty($images)) {
            try {
                $countLargeFiles = 0;
                $largeFiles = "";
                $countInvalidFiles = 0;
                $invalidFiles = "";
                $countProcessingFailed = 0;

                foreach ($images as $key => $image) {
                    $imageName = $image->getClientOriginalName();
                    $name = null;
                    $publicPath = null;
                    $salt = null;
                    try {
                        $imageExtension = $image->getClientOriginalExtension();
                        $imageSize = $image->getSize();
                        $imageSizeInMb = floatval(number_format($imageSize / (1024 * 1024), 2));
                        if ($imageSizeInMb > 5) { // only allow images of 5 MB or less
                            $largeFiles .= ", " . $imageName . " (" . $imageSizeInMb . " MB)";
                            $countLargeFiles += 1;
                            Log::info("Skipping large image " . $imageName . " (" . $imageSizeInMb . " MB)");
                            continue;
                        }

                        if (!$image->isValid()) {
                            $invalidFiles .= ", " . $imageName;
                            $countInvalidFiles += 1;
                            continue;
                        }

                        // MEMBER_IMAGES_PATH → public/users
                        $name = time() . '_' . $id . $key . '.' . $imageExtension;

                        Log::info("Uploading image " . $imageName);
                        $rootImgPath = Profile::MEMBER_IMAGES_PATH;
                        $path = $rootImgPath . '/' . $name;
                        $publicPath = public_path($rootImgPath);
                        $image->move($publicPath, $name);

                        $salt = random_int(111, 99999);
                        Log::info("Generating blur and thumbnails");
                        $manager = $this->createImageManager();
                        if ($manager) {
                            $this->generateBlurAndThumbnails($manager, $publicPath, $name, $salt);
                        } else {
                            Log::warning('GD and Imagick are both unavailable; using full-size copies for thumbnails/blur paths. Enable extension=gd in php.ini for proper resizing.');
                            $this->copyDerivativesWithoutProcessing($publicPath, $name, $salt);
                        }
                        Log::info("Thumbnails generated");

                        Log::info("Saving image to db");
                        $image = new Images();
                        $image->dataid = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
                        $image->name = $name;
                        $image->user_id = $id;
                        $image->img_url = $path;
                        $image->salt = $salt;
                        $image->visibility = 'Public';
                        $image->displaypic = 0;

                        $image->save();
                        Log::info("Saved");
                    } catch (\Exception $e) {
                        Log::error("Error processing " . $imageName . ": " . $e->getMessage());
                        $countProcessingFailed++;
                        if ($name !== null && $publicPath !== null) {
                            $this->deleteUploadDerivatives($publicPath, $name, $salt);
                        }
                    }
                }
                $totalUploaded = (sizeof($images) - $countLargeFiles - $countInvalidFiles - $countProcessingFailed);

                $message = ($totalUploaded > 0 ?
                    'success|You have successfully uploaded ' . $totalUploaded . ' image(s).' :
                    'warning|Images(s) could not be uploaded.') .
                    ($countLargeFiles > 0 ? ' Following files were discarded due to size restriction of 5 MB - ' . substr($largeFiles, 2) : '') .
                    ($countInvalidFiles > 0 ? ' Following files were discarded as they were not fully uploaded - ' . substr($invalidFiles, 2) : '') .
                    ($countProcessingFailed > 0 ? ' ' . $countProcessingFailed . ' file(s) failed during processing—check laravel.log.' : '') .
                    ($totalUploaded > 0 ? ' Do not forget to update the visibility in the gallery.|5000' : '|5000');
                Log::info("User (" . $dataid . ") uploaded " . $totalUploaded . " image(s). ");
                User::retrieveUserObject($dataid, true);
            } catch (\Exception $e) {
                Log::info("User (" . $dataid . ") could not upload image(s). Error: " . $e->getMessage());
                $message = 'danger|There was an error uploading image(s). Please try again later and contact admin if the error persists.';
            }
        } else {
            Log::info("User (" . $dataid . ") selected no images for upload.");
            $message = 'danger|Please select images to upload.';
        }
        if (request()->ajax()) {
            return [
                'code' => '200',
                'nav_img' => User::retrieveUserObject()->getProfileImage(true),
                'html' => $this->profile($request)->renderSections()['main-content'],
                'message' => $message
            ]; // only return whats in the main-content section
        } else return [
            'code' => '200'
        ];
    }

    /**
     * Live-captured selfie (brief §5 "Selfie/liveness check ... where
     * technically feasible"). Kept separate from uploadImages() above: only
     * one selfie is kept at a time (a resubmission replaces it), it's
     * flagged is_selfie=1 so the admin verification queue can show it next
     * to the two regular uploads, and it resets a previously-rejected
     * profile back to "pending" for re-review.
     */
    public function uploadSelfie(Request $request)
    {
        $user = User::retrieveUserObject();
        $id = $user->id;
        $dataid = $user->dataid;
        $selfie = $request->file('selfie');

        if (empty($selfie)) {
            return ['code' => '400', 'message' => 'danger|No selfie captured. Please try again.'];
        }

        try {
            if (!$selfie->isValid()) {
                return ['code' => '400', 'message' => 'danger|Selfie capture failed. Please try again.'];
            }

            $imageSizeInMb = floatval(number_format($selfie->getSize() / (1024 * 1024), 2));
            if ($imageSizeInMb > 5) {
                return ['code' => '400', 'message' => 'danger|Selfie capture is too large. Please try again.'];
            }

            // Only one live selfie kept per account — replace on resubmission.
            $existing = Images::where('user_id', $id)->where('is_selfie', 1)->get();
            foreach ($existing as $old) {
                $publicPath = public_path(Profile::MEMBER_IMAGES_PATH);
                $this->deleteUploadDerivatives($publicPath, $old->name, $old->salt);
                $old->delete();
            }

            $extension = $selfie->getClientOriginalExtension() ?: 'jpg';
            $name = time() . '_selfie_' . $id . '.' . $extension;
            $rootImgPath = Profile::MEMBER_IMAGES_PATH;
            $path = $rootImgPath . '/' . $name;
            $publicPath = public_path($rootImgPath);
            $selfie->move($publicPath, $name);

            $salt = random_int(111, 99999);
            $manager = $this->createImageManager();
            if ($manager) {
                $this->generateBlurAndThumbnails($manager, $publicPath, $name, $salt);
            } else {
                $this->copyDerivativesWithoutProcessing($publicPath, $name, $salt);
            }

            $image = new Images();
            $image->dataid = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
            $image->name = $name;
            $image->user_id = $id;
            $image->img_url = $path;
            $image->salt = $salt;
            $image->visibility = 'Private';
            $image->displaypic = 0;
            $image->is_selfie = 1;
            $image->save();

            // A resubmitted selfie after a rejection puts the account back in the review queue.
            if ($user->photo_verification_status === 'rejected') {
                $user->photo_verification_status = 'pending';
                $user->photo_rejection_reason = null;
                $user->save();
            }

            Log::info("User (" . $dataid . ") captured a verification selfie.");

            return ['code' => '200', 'message' => 'success|Selfie captured.'];
        } catch (\Exception $e) {
            Log::error("User (" . $dataid . ") selfie capture failed: " . $e->getMessage());
            return ['code' => '500', 'message' => 'danger|Could not save selfie. Please try again.'];
        }
    }

    /**
     * Dedicated "Manage Pictures" dashboard page. Was previously a Bootstrap
     * modal (renderImagesModal()) opened from any page via AJAX — replaced
     * with a real page so refreshing it after an upload/delete no longer
     * depends on Bootstrap's modal/backdrop lifecycle at all (a stale modal
     * re-render was leaving orphaned .modal-backdrop divs stacking on every
     * action, compounding into what looked like a solid black screen).
     */
    public function picturesPage()
    {
        $loggedInUser = User::retrieveUserObject();
        $images = Images::where('user_id', $loggedInUser->id)->get();
        return view('member.pictures', compact('images'));
    }

    public function updateImage(Request $request, $action, $dataid)
    {

        $loggedInUser = User::retrieveUserObject();
        $loggedInUserId = $loggedInUser->id;
        $loggedInUserDataId = $loggedInUser->dataid;
        $image = Images::firstWhere(['dataid' => $dataid, 'user_id' => $loggedInUserId]);
        $message = null;

        if (!empty($image)) {
            try {
                if ($action == 'd') {
                    $name = $image->name;
                    Log::info("Deleting images, blurs and thumbnails from folder");
                    $publicPath = public_path(Profile::MEMBER_IMAGES_PATH) . "/";
                    $blurName = explode("_", $name);
                    File::delete(
                        $publicPath . $name,
                        $publicPath . $blurName[0] . $image->salt . $blurName[1],
                        $publicPath . "thumbnail_" . $name,
                        $publicPath . "thumbnail_md_" . $name,
                        $publicPath . "thumbnail_sm_" . $name
                    );
                    Log::info("Starting image delete");
                    $image->delete();

                    Log::info("User (" . $loggedInUserDataId . ") deleted image (" . $dataid . ").");
                    $message = 'success|You have successfully deleted image (' . $dataid . ').';
                } else if ($action == 'dp') {
                    $images = Images::where('displaypic', 1)->where('user_id', $loggedInUserId)->get();
                    foreach ($images as $img) {
                        $img->displaypic = 0;
                        $img->save();
                    }
                    $image->displaypic = 1;
                    $image->save();

                    Log::info("User (" . $loggedInUserDataId . ") updated display pic to image (" . $dataid . ").");
                    $message = 'success|You have successfully updated display pic to image (' . $dataid . ').';
                }
                User::retrieveUserObject($loggedInUserDataId, true);
            } catch (\Exception $e) {
                Log::info("User (" . $loggedInUserDataId . ") could not update image (" . $dataid . "). Error: " . $e->getMessage());
                $message = 'danger|There was an error updating image (' . $dataid . '). Please try again later and contact admin if the error persists.';
            }
        } else {
            Log::info("User (" . $loggedInUserDataId . ") could not update image (" . $dataid . "). Image could not be located!!!");
            $message = 'danger|Image could not be located!!!Please try again later and contact admin if the error persists.';
        }

        if (request()->ajax()) {
            return [
                'code' => '200',
                'nav_img' => User::retrieveUserObject()->getProfileImage(true),
                'message' => $message
            ];
        } else return [
            'code' => '200'
        ];
    }

    public function updateProfile(Request $request, $section)
    {
        $user = User::retrieveUserObject();
        $dataid = $user->dataid;
        try {
            if ($section == "introduction") {
                $user->intro = $request->introduction;
            } else if ($section == "basic_info") {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->gender = $request->gender;
                $user->birthday = $request->year . '-' . $request->month . '-' . $request->day;
                $user->marital_status = $request->marital_status;
                $user->children = $request->children;
                $user->area = $request->area;
                $user->profile_for = $request->on_behalf;
                $user->contact_mobile_number = $request->contact_mobile_number;
            } else if ($section == "education_and_career") {
                $user->education = $request->education;
                $user->profession = $request->profession;
                $user->salary = $request->salary;
            } else if ($section == "physical_attributes") {
                $user->height = $request->height;
                $user->weight = $request->weight;
            } else if ($section == "language") {
                $user->mother_tongue = $request->mother_tongue;
                $user->language = $request->language;
            } else if ($section == "residency_information") {
                $user->con_of_birth = $request->con_of_birth;
                $user->con_of_residence = $request->con_of_residence;
                $user->con_of_citizenship = $request->con_of_citizenship;
                $user->con_grew_up = $request->con_grew_up;
                $user->immigration_status = $request->immigration_status;
            } else if ($section == "spiritual_and_social_background") {
                $user->religion = $request->religion;
                $user->caste = $request->caste;
                $user->sect = $request->sect;
            } else if ($section == "permanent_address") {
                $user->con_of_residence = $request->con_of_residence;
                $user->state = $request->state;
                $user->city = $request->city;
                $user->society = $request->society;
            } else if ($section == "family_info") {
                $user->father = $request->father;
                $user->mother = $request->mother;
                // brother/sister (free-text) are frozen legacy fields, superseded by
                // brothers_count/sisters_count below — no longer written on save, same
                // as the users.r* columns frozen by the partner_preferences migration.
                $user->brothers_count = $request->brothers_count !== '' ? $request->brothers_count : null;
                $user->sisters_count = $request->sisters_count !== '' ? $request->sisters_count : null;
                $user->siblings = $request->siblings;
            } else if ($section == "additional_personal_details") {
                $user->district = $request->district;
                $user->family_residence = $request->family_residence;
                $user->father_profession = $request->father_profession;
                $user->mother_profession = $request->mother_profession;
                $user->special_circumstances = $request->special_circumstances;
                $user->family_values = $request->family_values;
            }
            // Partner Preferences no longer lives on this page/section switch —
            // it's its own dashboard page now, saved by updatePreferences() below,
            // writing to the partner_preferences table instead of users.r*.
            $user->save();
            Log::info("User (" . $dataid . ") updated " . $section . " section.");

            User::retrieveUserObject($dataid, true);

            if (request()->ajax()) {
                return [
                    'code' => '200',
                    'html' => $this->profile($request)->renderSections()['main-content'],
                    'message' => 'success|You have successfully updated your profile.'
                ]; // only return whats in the main-content section
            } else return [
                'code' => '200'
            ];
        } catch (\Exception $e) {
            Log::info("User (" . $dataid . ") could not update their profile. Error: " . $e->getMessage());
            return [
                'code' => '404',
                'message' => 'There was an error updating your profile. Please try again later and contact admin if the error persists.'
            ];
        }
    }

    public function updateFiltered($action, $type, $dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        switch ($action) {
            case "add":
                return $this->addFiltered($type, $dataid);
                break;
            case "remove":
                return $this->removeFiltered($type, $dataid);
                break;
            default:
                Log::info("User (" . $loggedInUser->id . ") requested " . $action . " " . $type . "filtered action on (" . $dataid . ")");
                return [
                    'code' => '404',
                    'message' => 'Action not permitted!!!'
                ];
        }
    }

    public function addFiltered($type, $dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $message = null;
            $existing = Filtered::where('member', $id)
                ->where('user', $loggedInUser->id)
                ->where('type', $type)
                ->first();
            if (empty($existing)) {
                $filtered = new Filtered();
                $filtered->member = $id;
                $filtered->user = $loggedInUser->id;
                $filtered->type = $type;
                $filtered->save();

                // add this to send a notification
                if ($type == "follow")
                    $member->notify(new UserFollowed($loggedInUser, $member));

                Log::info("User (" . $loggedInUser->dataid . ") added member (" . $id . ") to " . $type . " list.");
                $message = 'success|You have successfully added this member (id: ' . $dataid . ') to ' . $type . ' list.';
                User::retrieveUserObject($loggedInUser->dataid, true);
            } else {
                Log::info("User (" . $loggedInUser->dataid . ") already added " . $dataid . " to " . $type . " list");
                $message = 'warning|You have already added this member (id: ' . $dataid . ') to ' . $type . ' list.';
            }
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not update " . $type . " list for " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function removeFiltered($type, $dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $message = null;
            $filtered = Filtered::where('member', $id)
                ->where('user', $loggedInUser->id)
                ->where('type', $type)->first();
            $filtered->delete();

            Log::info("User (" . $loggedInUser->dataid . ") removed member (" . $id . ") from " . $type . " list.");
            $message = 'success|You have successfully removed this member (id: ' . $dataid . ') from ' . $type . ' list.';

            User::retrieveUserObject($loggedInUser->dataid, true);
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not update " . $type . " list for " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function updateInterest($action, $dataid, $who = null)
    {
        $loggedInUser = User::retrieveUserObject();

        switch ($action) {
            case "send":
                return $this->sendInterest($dataid);
                break;
            case "accept":
                return $this->acceptInterest($dataid);
                break;
            case "decline":
                return $this->declineInterest($dataid);
                break;
            case "withdraw":
                return $this->withdrawInterest($dataid, $who);
                break;
            default:
                Log::info("User (" . $loggedInUser->dataid . ") requested " . $action . " interest action on (" . $dataid . ")");
                return [
                    'code' => '404',
                    'message' => 'Action not permitted!!!'
                ];
        }
    }

    public function sendInterest($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $message = null;
            $existing = Interest::where('receiver', $id)->where('sender', $loggedInUser->id)->first();
            if (empty($existing)) {
                $interest = new Interest;
                $interest->receiver = $id;
                $interest->sender = $loggedInUser->id;
                $interest->save();

                $member->notify(new InterestSent($loggedInUser, $member));
                Mail::to($member)->send(new InterestSentMail($loggedInUser, $member));

                Log::info("User (" . $loggedInUser->dataid . ") sent interest to user (" . $id . ")");
                $message = 'success|You have successfully sent interest to this member (id: ' . $dataid . '). Now wait and see if they respond back to your request.';

                User::retrieveUserObject($dataid, true);
                User::retrieveUserObject($loggedInUser->dataid, true);
            } else {
                Log::info("User (" . $loggedInUser->dataid . ") already sent interest to " . $dataid);
                $message = 'warning|You have already sent interest to this member (id: ' . $dataid . '). Wait for them to respond back.';
            }
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not send interest to " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function acceptInterest($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $obj = Interest::where('sender', $id)->where('receiver', $loggedInUser->id)->first();
            $obj->interest_back = 1;
            $obj->update();

            $member->notify(new InterestAccepted($member, $loggedInUser));
            Mail::to($member)->send(new InterestAcceptedMail($member, $loggedInUser));

            Log::info("User (" . $loggedInUser->dataid . ") accepted interest from (" . $dataid . ")");
            $message = 'success|You have successfully accepted interest for user (id: ' . $dataid . ').';

            User::retrieveUserObject($dataid, true);
            User::retrieveUserObject($loggedInUser->dataid, true);
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not accept interest from " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function declineInterest($dataid)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $obj = Interest::where('sender', $id)->where('receiver', $loggedInUser->id)->first();
            $obj->interest_back = -1;
            $obj->update();

            $member->notify(new InterestDeclined($member, $loggedInUser));
            Mail::to($member)->send(new InterestDeclinedMail($member, $loggedInUser));

            Log::info("User (" . $loggedInUser->dataid . ") declined interest from (" . $dataid . ")");
            $message = 'success|You have successfully declined interest for user (id: ' . $dataid . ').';

            User::retrieveUserObject($dataid, true);
            User::retrieveUserObject($loggedInUser->dataid, true);
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not decline interest from " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    public function withdrawInterest($dataid, $who)
    {
        $loggedInUser = User::retrieveUserObject();

        $member = User::retrieveUserObject($dataid);
        if ($member) {
            $id = $member->id;
            $label = "";
            if ($who == 's') {
                $obj = Interest::where('receiver', $id)->where('sender', $loggedInUser->id)->first();
                $label = "Sender";
                $obj->delete();
            } else {
                $obj = Interest::where('sender', $id)->where('receiver', $loggedInUser->id)->first();
                $label = "Receiver";
                $obj->interest_back = 0;
                $obj->update();
            }

            Log::info($label . " (" . $loggedInUser->dataid . ") withdrew interest for (" . $dataid . ")");
            $message = 'success|You have successfully withdrawn interest for user (id: ' . $dataid . ').';

            User::retrieveUserObject($dataid, true);
            User::retrieveUserObject($loggedInUser->dataid, true);
            return [
                'code' => '200',
                'message' => $message
            ];
        } else {
            Log::info("User (" . $loggedInUser->dataid . ") could not withdraw interest from " . $dataid);
            return [
                'code' => '404',
                'message' => 'Member was not found (id: ' . $dataid . ')'
            ];
        }
    }

    /**
     * Intervention Image driver: prefer Imagick when available, else GD.
     */
    private function createImageManager(): ?ImageManager
    {
        if (extension_loaded('imagick')) {
            return new ImageManager(['driver' => 'imagick']);
        }
        if (extension_loaded('gd')) {
            return new ImageManager(['driver' => 'gd']);
        }

        return null;
    }

//     private function createImageManager(): ?ImageManager
// {
//     if (extension_loaded('imagick')) {
//         return new ImageManager('imagick');
//     }

//     if (extension_loaded('gd')) {
//         return new ImageManager('gd');
//     }

//     return null;
// }

    private function generateBlurAndThumbnails(ImageManager $manager, string $publicPath, string $name, int $salt): void
    {
        $thumbnail = $manager->make($publicPath . '/' . $name);
        $height = $thumbnail->height();
        $width = $thumbnail->width();

        $blur = $manager->make($publicPath . '/' . $name);
        $blurAmt = 70;

        if ($width > $height) {
            $blur->resize(210, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $blur->blur($blurAmt);
            $blurName = explode("_", $name);
            $blur->save($publicPath . '/' . $blurName[0] . $salt . $blurName[1]);

            $thumbnail->resize(210, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_" . $name);

            $thumbnail->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_md_" . $name);

            $thumbnail->resize(32, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_sm_" . $name);
        } else {
            $blur->resize(null, 210, function ($constraint) {
                $constraint->aspectRatio();
            });
            $blur->blur($blurAmt);
            $blurName = explode("_", $name);
            $blur->save($publicPath . '/' . $blurName[0] . $salt . $blurName[1]);

            $thumbnail->resize(null, 210, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_" . $name);

            $thumbnail->resize(null, 100, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_md_" . $name);

            $thumbnail->resize(null, 32, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnail->save($publicPath . "/thumbnail_sm_" . $name);
        }
    }

    /**
     * Last resort when neither GD nor Imagick is loaded: duplicate the original so expected paths exist.
     */
    private function copyDerivativesWithoutProcessing(string $publicPath, string $name, int $salt): void
    {
        $src = $publicPath . '/' . $name;
        File::copy($src, $publicPath . '/thumbnail_' . $name);
        File::copy($src, $publicPath . '/thumbnail_md_' . $name);
        File::copy($src, $publicPath . '/thumbnail_sm_' . $name);
        $blurName = explode("_", $name);
        if (count($blurName) >= 2) {
            File::copy($src, $publicPath . '/' . $blurName[0] . $salt . $blurName[1]);
        }
    }

    private function deleteUploadDerivatives(string $publicPath, string $name, ?int $salt): void
    {
        $paths = [
            $publicPath . '/' . $name,
            $publicPath . '/thumbnail_' . $name,
            $publicPath . '/thumbnail_md_' . $name,
            $publicPath . '/thumbnail_sm_' . $name,
        ];
        $blurName = explode("_", $name);
        if ($salt !== null && count($blurName) >= 2) {
            $paths[] = $publicPath . '/' . $blurName[0] . $salt . $blurName[1];
        }
        foreach ($paths as $p) {
            if (File::exists($p)) {
                File::delete($p);
            }
        }
    }

    public function passwordUpdate()
    {
        return view('auth.passwords.change', ['profile' => User::retrieveUserObject()->profile()]);
    }

    public function showListing($type)
    {
        $user = User::retrieveUserObject();
        $members = null;
        if ($type == "interests") {
            $members = $user->getInterestLists();
            return view('member.interestdata', compact('type', 'members'));
        } else {
            $members = $user->getTypeFilteredList($type);
            return view('member.filtereddata', compact('type', 'members'));
        }
    }
}
