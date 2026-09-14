<?php

namespace App\Console\Commands;

use App\Http\Controllers\HomeController;
use App\Profile;
use Illuminate\Console\Command;
use App\Services\FaceBlurService;

/**
 * Pre-generates a face-only-pixelated copy of each homepage-curated
 * profile's real photo (HomeController::CURATED_PROFILE_DATAIDS), saved to
 * public/homepage-blurred/<dataid>.jpg. Uses AWS Rekognition to find the
 * face and pixelate only that region — body/outfit/background stay sharp,
 * per client request (they didn't want the whole photo blurred).
 *
 * Run this:
 *   - once now, and
 *   - again any time HomeController::CURATED_PROFILE_DATAIDS changes (new
 *     profiles added/swapped by the client), or FaceBlurService::PIXELATE_SIZE
 *     is tuned (e.g. client asks for more/less obscuring).
 *
 * A profile with no face detected (bad angle, sunglasses, etc.) is skipped
 * and reported — the homepage automatically falls back to the site's
 * existing whole-image blur for that one profile until it's fixed, it never
 * shows broken.
 */
class GenerateHomepageFaceBlur extends Command
{
    protected $signature = 'homepage:blur-faces';

    protected $description = 'Generate face-only-pixelated photos for the homepage curated profile slider';

    public function handle(FaceBlurService $service)
    {
        $dataids = HomeController::CURATED_PROFILE_DATAIDS;
        $quoted = "'" . implode("','", array_map('addslashes', $dataids)) . "'";
        $profiles = Profile::profiles("`u`.`dataid` in ($quoted)", "`images`<>''", null, null)->keyBy('dataid');

        $outputDir = public_path(HomeController::FACE_BLUR_DIR);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $succeeded = 0;
        $skipped = [];

        foreach ($dataids as $dataid) {
            $profile = $profiles->get($dataid);
            if (!$profile) {
                $skipped[] = "$dataid (no active profile with photos found)";
                continue;
            }

            $sourceRelative = null;
            if (!empty($profile->displaypic)) {
                $sourceRelative = $profile->displaypic;
            } elseif (!empty($profile->images)) {
                $images = is_array($profile->images) ? $profile->images : explode(',', $profile->images);
                $sourceRelative = $images[0] ?? null;
            }
            if (empty($sourceRelative)) {
                $skipped[] = "$dataid (no photo on record)";
                continue;
            }

            $sourcePath = public_path($sourceRelative);
            if (!file_exists($sourcePath)) {
                $skipped[] = "$dataid (photo file missing on disk: $sourceRelative)";
                continue;
            }

            $outputPath = $outputDir . '/' . $dataid . '.jpg';
            $this->line("Processing $dataid ({$profile->first_name})...");

            if ($service->blurFace($sourcePath, $outputPath)) {
                $succeeded++;
                $this->info("  -> saved " . HomeController::FACE_BLUR_DIR . "/$dataid.jpg");
            } else {
                $skipped[] = "$dataid (no face detected or processing failed — see log)";
            }
        }

        $this->line('');
        $this->info("Done: $succeeded/" . count($dataids) . " generated.");
        if (!empty($skipped)) {
            $this->warn('Skipped (will fall back to whole-image blur on the homepage):');
            foreach ($skipped as $reason) {
                $this->warn("  - $reason");
            }
        }

        return 0;
    }
}
