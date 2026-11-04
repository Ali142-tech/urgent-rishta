<?php

namespace App\Services;

use App\Http\Controllers\HomeController;
use App\Profile;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds the small set of opposite-gender profile cards shown in the
 * "Match Preview" email (App\Mail\MatchPreview) — reuses the exact same
 * curated list + face-blur images already set up for the homepage's
 * "Profiles From Around the World" section (HomeController), so there's
 * only one list to maintain and it's already proven/tested there.
 */
class MatchPreviewService
{
    /** How many profile cards to include in one email. */
    public const PROFILES_PER_EMAIL = 3;

    /** @return Collection<int, object{dataid:string,name_line:string,location_line:string,profession:?string,image_url:string}> */
    /** The same three profiles for the whole send: built once per gender and cached for a few hours. */
    public function curatedProfilesCached(string $recipientGender): Collection
    {
        return \Illuminate\Support\Facades\Cache::remember('weekly_email_profiles_' . $recipientGender, now()->addHours(6), fn () => $this->curatedProfilesFor($recipientGender));
    }

    /**
     * The newest opposite-gender profiles (self-registered, active, with a photo) — the same three for
     * everyone of the same gender, so the weekly e-mail only needs to build them once per gender.
     */
    public function curatedProfilesFor(string $recipientGender): Collection
    {
        $oppositeGender = $recipientGender === 'male' ? 'female' : 'male';

        $latest = Profile::profiles(
            "`u`.`active`=1 and `u`.`admin`=0 and `u`.`added_by` IS NULL and `u`.`gender`='" . $oppositeGender . "'",
            "`images`<>''",
            "`u`.`created_at` DESC",
            self::PROFILES_PER_EMAIL
        );

        return collect($latest)
            ->values()
            ->map(function ($profile) {
                $faceBlurPath = HomeController::FACE_BLUR_DIR . '/' . $profile->dataid . '.jpg';
                $blurredRelativeUrl = file_exists(public_path($faceBlurPath))
                    ? '/' . $faceBlurPath . '?v=' . filemtime(public_path($faceBlurPath))
                    : $profile->getBlurredProfileImage();

                $age = !empty($profile->birthday) ? Carbon::parse($profile->birthday)->age : null;
                $cityCountry = implode(', ', array_filter([$profile->lbl_city, $profile->lbl_con_of_residence]));

                return (object) [
                    'dataid' => $profile->dataid,
                    'name_line' => $profile->first_name . ($age ? ", {$age} yrs" : ''),
                    'location_line' => implode(' • ', array_filter([$profile->height, $cityCountry])),
                    'profession' => $profile->profession,
                    // Absolute production URL: this renders in an e-mail client, not through this app.
                    'image_url' => 'https://urgentrishta.com' . $blurredRelativeUrl,
                ];
            });
    }
}
