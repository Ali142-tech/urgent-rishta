<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

/**
 * Diagonal "Urgent Rishta" + proposal ID overlay, baked once at upload
 * time for a team-added proposal's photos (TeamController::uploadPhoto()).
 * Currently unused at display time — any team member can see the sharp
 * photo directly (see User::canViewContactInfoOf()) — kept in case
 * per-proposal photo gating is reintroduced later. Structurally mirrors
 * FaceBlurService (same ImageManager driver preference, same "generate
 * once at upload time, bake to disk" pattern), but overlays text instead
 * of detecting/pixelating a face — no AWS Rekognition needed.
 */
class WatermarkService
{
    /**
     * A TTF font is required for Intervention/GD to draw angled, colored,
     * custom-sized text — this app bundles no such font (public/fonts only
     * has an icon font). Checked in order; the first that exists wins.
     * Falls back to an unrotated, default-font label if none are found,
     * so this never hard-fails just because a dev/prod box lacks one of
     * these paths.
     */
    const FONT_CANDIDATES = [
        'C:\\Windows\\Fonts\\arial.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
    ];

    public function watermark(string $sourcePath, string $outputPath, string $label): bool
    {
        if (!file_exists($sourcePath)) {
            Log::warning("WatermarkService: source not found: $sourcePath");
            return false;
        }

        try {
            $manager = new ImageManager(extension_loaded('imagick') ? ['driver' => 'imagick'] : ['driver' => 'gd']);
            $image = $manager->make($sourcePath)->orientate();

            $font = collect(self::FONT_CANDIDATES)->first(fn ($path) => file_exists($path));
            $fontSize = max(14, (int) round(min($image->width(), $image->height()) / 12));

            $image->text($label, $image->width() / 2, $image->height() / 2, function ($font_) use ($font, $fontSize) {
                $font_->size($fontSize);
                $font_->color([255, 255, 255, 70]);
                $font_->align('center');
                $font_->valign('center');
                if ($font) {
                    $font_->file($font);
                    $font_->angle(30);
                }
            });

            $image->save($outputPath, 90);
            return true;
        } catch (\Throwable $e) {
            Log::warning("WatermarkService: failed for $sourcePath — " . $e->getMessage());
            return false;
        }
    }
}
