<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

/**
 * Extracted verbatim from ProfileController::uploadImages()'s private
 * helpers (validate/generate-derivatives/cleanup) so TeamController can
 * upload photos FOR a proposal it owns using the exact same storage
 * convention (public/users, salted blur filename, thumbnail_/thumbnail_md_/
 * thumbnail_sm_ sizes) without duplicating or risking the tested,
 * self-upload flow those methods still serve on ProfileController.
 */
trait HandlesImageUploads
{
    /**
     * Verifies an uploaded file is genuinely an image (not just named like
     * one) and returns the safe extension to store it under, or null if it
     * fails validation. Security-critical: this used to trust
     * getClientOriginalExtension() blindly and move the file straight into
     * public/users (a directly web-served, script-executable folder) with
     * no server-side content check — a file like "shell.php" could be
     * uploaded and run directly. Laravel's 'image' rule decodes the file
     * (getimagesize()-equivalent) to confirm it's real image data, not
     * just a plausible filename/extension, and the returned extension is
     * our own canonical mapping (never the client-supplied one) so a trick
     * like "shell.php.jpg" can't smuggle a second extension through.
     */
    private function validateUploadedImage($file): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $validator = Validator::make(['file' => $file], [
            'file' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);
        if ($validator->fails()) {
            return null;
        }

        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        return $mimeToExt[$file->getMimeType()] ?? null;
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
}
