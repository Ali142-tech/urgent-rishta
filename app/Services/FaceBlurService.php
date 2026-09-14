<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

/**
 * Selective face-only obscuring — detects the face region via AWS
 * Rekognition (same credentials/client pattern as PhotoVerificationService)
 * and pixelates just that region, leaving the rest of the photo (outfit,
 * pose, background) sharp. Built specifically for the homepage "Meet Our
 * Members" slider (see HomeController::CURATED_PROFILE_DATAIDS and the
 * homepage:blur-faces artisan command) — the client wants a genuinely real,
 * viewable photo shown, just with the face itself obscured, unlike the
 * whole-image blur used elsewhere on the site for guest privacy.
 *
 * Pixelation, not blur: an initial attempt used a Gaussian blur, which
 * turned out to be numerically applied but barely noticeable once pasted
 * into the full photo at normal viewing size (confirmed via direct pixel
 * comparison) — pixelation reads as unmistakably obscured at any size.
 */
class FaceBlurService
{
    /**
     * Pixelation is scaled RELATIVE to the cropped face region's width, not
     * a fixed pixel count — a fixed block size looked fine on a tightly-
     * cropped face but was nearly invisible on a close-up selfie where the
     * face (and so the padded crop) filled most of the frame (e.g. a
     * 447px-wide crop vs a 959px-wide crop from a different photo — the
     * same "10px block" is chunky on one and imperceptible on the other).
     * This targets roughly the same number of blocks across the face
     * regardless of the source photo's resolution or how close-up the
     * selfie is. Tuned so a ~447px-wide crop lands on block size ~10 (the
     * level settled on after client feedback: 16 was too strong, 7 was too
     * weak/face became recognizable).
     */
    const TARGET_BLOCKS_ACROSS_FACE = 10;
    const MIN_PIXEL_BLOCK_SIZE = 14;

    protected RekognitionClient $client;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'version' => 'latest',
            'region' => config('services.rekognition.region'),
            'credentials' => [
                'key' => config('services.rekognition.key'),
                'secret' => config('services.rekognition.secret'),
            ],
        ]);
    }

    /**
     * Detect the largest face in $sourcePath (largest, in case of an
     * incidental second face in the background) and save a copy to
     * $outputPath with just that region pixelated.
     *
     * Returns true on success, false if no face was detected or anything
     * else went wrong — callers should fall back to the site's existing
     * whole-image blur in that case, never leave the photo unobscured.
     */
    public function blurFace(string $sourcePath, string $outputPath): bool
    {
        if (!file_exists($sourcePath)) {
            Log::warning("FaceBlurService: source not found: $sourcePath");
            return false;
        }

        try {
            $result = $this->client->detectFaces([
                'Image' => ['Bytes' => file_get_contents($sourcePath)],
                'Attributes' => [],
            ]);

            $faces = $result['FaceDetails'] ?? [];
            if (empty($faces)) {
                Log::info("FaceBlurService: no face detected in $sourcePath");
                return false;
            }

            $largestFace = collect($faces)->sortByDesc(function ($face) {
                return $face['BoundingBox']['Width'] * $face['BoundingBox']['Height'];
            })->first();
            $box = $largestFace['BoundingBox'];

            $manager = new ImageManager(extension_loaded('imagick') ? ['driver' => 'imagick'] : ['driver' => 'gd']);

            // Canvas: the copy we'll paste the pixelated face onto and save.
            $canvas = $manager->make($sourcePath)->orientate();
            $imgWidth = $canvas->width();
            $imgHeight = $canvas->height();

            // Rekognition's box is tight to facial landmarks (eyes/nose/
            // mouth) — pad it out generously so the whole head (hair,
            // forehead, chin, ears) is covered, not just the inner face.
            $boxWidth = $box['Width'] * $imgWidth;
            $boxHeight = $box['Height'] * $imgHeight;
            $boxLeft = $box['Left'] * $imgWidth;
            $boxTop = $box['Top'] * $imgHeight;

            $padX = $boxWidth * 0.45;
            $padTop = $boxHeight * 0.65;
            $padBottom = $boxHeight * 0.35;

            $cropX = max(0, (int) round($boxLeft - $padX));
            $cropY = max(0, (int) round($boxTop - $padTop));
            $cropWidth = min($imgWidth - $cropX, (int) round($boxWidth + $padX * 2));
            $cropHeight = min($imgHeight - $cropY, (int) round($boxHeight + $padTop + $padBottom));

            if ($cropWidth <= 0 || $cropHeight <= 0) {
                Log::warning("FaceBlurService: computed an empty crop region for $sourcePath");
                return false;
            }

            // Separate instance (fresh read of the same file) for the
            // region we crop + pixelate, so cropping it doesn't also crop
            // the canvas we're about to paste it back onto.
            $faceRegion = $manager->make($sourcePath)->orientate();
            $faceRegion->crop($cropWidth, $cropHeight, $cropX, $cropY);
            $pixelSize = max(self::MIN_PIXEL_BLOCK_SIZE, (int) round($cropWidth / self::TARGET_BLOCKS_ACROSS_FACE));
            $faceRegion->pixelate($pixelSize);

            $canvas->insert($faceRegion, 'top-left', $cropX, $cropY);

            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $canvas->save($outputPath, 90);

            return true;
        } catch (AwsException $e) {
            Log::error('FaceBlurService: Rekognition detectFaces failed: ' . $e->getAwsErrorMessage());
            return false;
        } catch (\Throwable $e) {
            Log::error('FaceBlurService: unexpected error: ' . $e->getMessage());
            return false;
        }
    }
}
