<?php

namespace App\Services;

use App\Proposal;
use App\TeamMember;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

/**
 * Stamps the OWNER's branding on a proposal's photos when they leave the dashboard (share, forward,
 * public share page), so the watermark stays on the picture wherever it is passed on. The stored
 * photo is never touched: a branded copy is made once per photo + branding and reused
 * (public/proposals/branded).
 *
 * Content: always "Owner name · P-112" (the owner and the proposal's reference, so a shared picture
 * can be traced back), plus the owner's own logo and watermark text ONLY when they have set them —
 * nothing of the platform's own branding is added.
 * Two looks, picked on the profile page:
 *   diagonal — repeated, rotated, faint pattern across the photo
 *   corner   — one large faint logo + text in the centre and a small ID badge bottom-right
 */
class PhotoBrandingService
{
    public const DIR = 'proposals/branded';
    public const STYLES = ['diagonal' => 'Diagonal pattern', 'corner' => 'Centre mark + corner badge'];
    private const VERSION = 'v5';

    /** Public URL path of the branded copy of $fileName for the proposal's owner, or null when it can't be made. */
    public function brandedPath(?TeamMember $owner, string $fileName, ?string $reference = null): ?string
    {
        if (!$owner) {
            return null;
        }
        $thumbWidth = null;
        $originalName = $fileName;
        if (str_starts_with($fileName, 'thumbnail_')) {
            $originalName = preg_replace('/^thumbnail_(sm_)?/', '', $fileName);
            $thumbWidth = str_starts_with($fileName, 'thumbnail_sm_') ? 100 : 300;
        }
        $source = public_path(trim(Proposal::PHOTO_PATH, '/') . '/' . $originalName);
        if (!file_exists($source)) {
            return null;
        }

        $logoFile = !empty($owner->logo) ? public_path(TeamMember::PHOTO_PATH . '/logos/' . $owner->logo) : null;
        if (!$logoFile || !file_exists($logoFile)) {
            $logoFile = null;   // no logo uploaded: none is drawn
        }
        $brandText = trim((string) $owner->watermark_text);   // empty unless the owner set one
        $idLine = trim(trim($owner->first_name . ' ' . $owner->last_name) . ' · ' . ($reference ?: 'ID ' . $owner->dataid), ' ·');
        $style = array_key_exists((string) $owner->watermark_style, self::STYLES) ? $owner->watermark_style : 'diagonal';

        $hash = substr(sha1(implode('|', [self::VERSION, $thumbWidth, $style, $brandText, $idLine, $logoFile ? basename($logoFile) . filemtime($logoFile) : '', $fileName, filemtime($source)])), 0, 20);
        $relative = self::DIR . '/' . $hash . '.jpg';
        $target = public_path($relative);
        if (file_exists($target)) {
            return '/' . $relative;
        }

        try {
            File::ensureDirectoryExists(dirname($target));
            $manager = new ImageManager(extension_loaded('imagick') ? ['driver' => 'imagick'] : ['driver' => 'gd']);
            $image = $manager->make($source)->orientate();
            $font = collect(WatermarkService::FONT_CANDIDATES)->first(fn ($p) => file_exists($p));

            if ($style === 'corner') {
                $this->cornerStyle($manager, $image, $logoFile, $brandText, $idLine, $font);
            } else {
                $this->diagonalStyle($manager, $image, $logoFile, $brandText, $idLine, $font);
            }

            if ($thumbWidth) {
                // same sizing rule as ProposalPhotoService: the shorter side's thumbnail, longer side scaled
                if ($image->width() > $image->height()) {
                    $image->resize($thumbWidth, null, function ($c) { $c->aspectRatio(); });
                } else {
                    $image->resize(null, $thumbWidth, function ($c) { $c->aspectRatio(); });
                }
            }
            $image->save($target, 90);
            return '/' . $relative;
        } catch (\Throwable $e) {
            Log::warning('PhotoBrandingService failed for ' . $fileName . ': ' . $e->getMessage());
            return null;
        }
    }

    /** Text with a faint dark shadow, so it reads on both light and dark photos. */
    private function drawText($img, string $text, int $x, int $y, int $size, ?string $font, float $alpha, string $align = 'left'): void
    {
        foreach ([[1, 1, [0, 0, 0, $alpha * 0.55]], [0, 0, [255, 255, 255, $alpha]]] as [$dx, $dy, $color]) {
            $img->text($text, $x + $dx, $y + $dy, function ($f) use ($font, $size, $color, $align) {
                $f->size($size);
                $f->color($color);
                $f->align($align);
                $f->valign('middle');
                if ($font) {
                    $f->file($font);
                }
            });
        }
    }

    private function diagonalStyle(ImageManager $manager, $image, ?string $logoFile, string $brandText, string $idLine, ?string $font): void
    {
        $w = $image->width();
        $h = $image->height();
        $base = min($w, $h);
        $size = max(13, (int) round($base / 34));

        // One tile: [logo] BRAND TEXT / Name · ID — then rotated and repeated across the photo.
        // Wide enough for the longer line, so the text is never cut off (a character is ~0.68 x the font size).
        $longest = max(mb_strlen($brandText) * $size * 0.68, mb_strlen($idLine) * $size * 0.8 * 0.68);
        $tileH = (int) round($size * 4.2);
        $tileW = (int) round($longest + ($logoFile ? $tileH * 1.6 : 0) + $size * 2);
        $tile = $manager->canvas($tileW, $tileH);
        $x = 0;
        if ($logoFile) {
            $logo = $manager->make($logoFile)->orientate();
            $logo->resize(null, (int) round($tileH * 0.8), function ($c) { $c->aspectRatio(); $c->upsize(); });
            $logo->opacity(45);
            $tile->insert($logo, 'left', 0, 0);
            $x = $logo->width() + (int) round($size * 0.7);
        }
        if ($brandText !== '') {
            $this->drawText($tile, $brandText, $x, (int) round($tileH * 0.32), $size, $font, 0.7);
            $this->drawText($tile, $idLine, $x, (int) round($tileH * 0.72), (int) round($size * 0.8), $font, 0.6);
        } else {
            $this->drawText($tile, $idLine, $x, (int) round($tileH * 0.5), $size, $font, 0.7);
        }
        $tile->rotate(30);

        $stepX = (int) round($tile->width() * 0.95);
        $stepY = (int) round($tile->height() * 1.0);
        $row = 0;
        for ($y = -$tile->height() ; $y < $h + $tile->height(); $y += $stepY) {
            $offset = ($row++ % 2) ? (int) round($stepX / 2) : 0;
            for ($xx = -$tile->width() + $offset; $xx < $w; $xx += $stepX) {
                $image->insert($tile, 'top-left', $xx, $y);
            }
        }
    }

    private function cornerStyle(ImageManager $manager, $image, ?string $logoFile, string $brandText, string $idLine, ?string $font): void
    {
        $w = $image->width();
        $h = $image->height();
        $base = min($w, $h);

        // Large faint mark in the centre: logo + brand text.
        $cy = (int) round($h * 0.5);
        if ($logoFile) {
            $logo = $manager->make($logoFile)->orientate();
            $logo->resize((int) round($base * 0.5), null, function ($c) { $c->aspectRatio(); $c->upsize(); });
            $logo->opacity(28);
            $image->insert($logo, 'center', 0, -(int) round($base * 0.05));
            $cy += (int) round($logo->height() / 2) + (int) round($base * 0.02);
        }
        if ($brandText !== '') {
            $this->drawText($image, $brandText, (int) ($w / 2), $cy, max(14, (int) round($base / 24)), $font, 0.5, 'center');
        } elseif (!$logoFile) {
            $this->drawText($image, $idLine, (int) ($w / 2), (int) ($h / 2), max(14, (int) round($base / 26)), $font, 0.45, 'center');
        }

        // Small dark ID badge, bottom-right.
        $size = max(12, (int) round($base / 38));
        $bw = (int) round($base * 0.52);
        $bh = (int) round($size * 3.4);
        $margin = (int) round($base * 0.03);
        $bx = $w - $bw - $margin;
        $by = $h - $bh - $margin;
        $image->rectangle($bx, $by, $bx + $bw, $by + $bh, function ($d) { $d->background([18, 58, 46, 0.82]); });
        $textX = $bx + (int) round($size * 0.9);
        if ($logoFile) {
            $mini = $manager->make($logoFile)->orientate();
            $mini->resize(null, (int) round($bh * 0.62), function ($c) { $c->aspectRatio(); $c->upsize(); });
            $image->insert($mini, 'top-left', $bx + (int) round($size * 0.6), $by + (int) round(($bh - $mini->height()) / 2));
            $textX = $bx + (int) round($size * 0.6) + $mini->width() + (int) round($size * 0.7);
        }
        if ($brandText !== '') {
            $this->drawText($image, $brandText, $textX, $by + (int) round($bh * 0.33), (int) round($size * 0.85), $font, 0.95);
            $this->drawText($image, $idLine, $textX, $by + (int) round($bh * 0.7), (int) round($size * 0.8), $font, 0.85);
        } else {
            $this->drawText($image, $idLine, $textX, $by + (int) round($bh * 0.5), (int) round($size * 0.9), $font, 0.95);
        }
    }
}
