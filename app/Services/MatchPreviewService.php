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
    public const PROFILES_PER_EMAIL = 5;

    /** @return Collection<int, object{dataid:string,name_line:string,location_line:string,profession:?string,image_url:string}> */
    public function curatedProfilesFor(string $recipientGender): Collection
    {
        $oppositeGender = $recipientGender === 'male' ? 'female' : 'male';

        $dataids = HomeController::CURATED_PROFILE_DATAIDS;
        $quoted = "'" . implode("','", array_map('addslashes', $dataids)) . "'";
        $pool = Profile::profiles("`u`.`active`=1 and `u`.`dataid` in ($quoted)", "`images`<>''", null, null)->keyBy('dataid');

        return collect($dataids)
            ->map(fn ($id) => $pool->get($id))
            ->filter()
            ->filter(fn ($profile) => $profile->gender === $oppositeGender)
            ->shuffle()
            ->take(self::PROFILES_PER_EMAIL)
            ->values()
            ->map(function ($profile) {
                $faceBlurPath = HomeController::FACE_BLUR_DIR . '/' . $profile->dataid . '.jpg';
                $blurredRelativeUrl = file_exists(public_path($faceBlurPath))
                    ? '/' . $faceBlurPath . '?v=' . filemtime(public_path($faceBlurPath))
                    : $profile->getBlurredProfileImage();

                $age = !empty($profile->birthday) ? Carbon::parse($profile->birthday)->age : null;

                // Pre-formatted display strings (rather than several small
                // @if checks in the Blade template) — one field, one clean
                // concatenation, easier to get right than juggling optional
                // pieces inline in the view.
                $nameLine = $profile->first_name . ($age ? ", {$age} yrs" : '');
                $locationParts = array_filter([$profile->height, trim(($profile->city ?: '') . ($profile->country ? ', ' . $profile->country : ''))]);
                $locationLine = implode(' • ', $locationParts);

                return (object) [
                    'dataid' => $profile->dataid,
                    'name_line' => $nameLine,
                    'location_line' => $locationLine,
                    'profession' => $profile->profession,
                    // Absolute production URL, not url()/asset() — this renders
                    // in an email client, never through this app's own request
                    // cycle, so a dynamically-resolved APP_URL (e.g. localhost
                    // in a dev environment) would silently break the image.
                    'image_url' => 'https://urgentrishta.com' . $blurredRelativeUrl,
                ];
            });
    }
}
