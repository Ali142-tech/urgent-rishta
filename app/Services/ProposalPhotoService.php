<?php

namespace App\Services;

use App\Proposal;
use App\ProposalPhoto;
use App\Traits\HandlesImageUploads;
use Illuminate\Support\Facades\File;

/**
 * Stores and removes proposal photos in public/proposals: the original plus a
 * 300px and a 100px thumbnail. Team members always see the sharp image, so
 * (unlike member photos) there is no blurred or watermarked copy.
 */
class ProposalPhotoService
{
    use HandlesImageUploads;

    /** Returns the saved photo, or null when the upload isn't a valid image. */
    public function store(Proposal $proposal, $file): ?ProposalPhoto
    {
        $extension = $file ? $this->validateUploadedImage($file) : null;
        if (!$extension) {
            return null;
        }

        $dir = public_path(trim(Proposal::PHOTO_PATH, '/'));
        File::ensureDirectoryExists($dir);

        $name = time() . '_' . $proposal->id . '_' . random_int(1000, 9999) . '.' . $extension;
        $file->move($dir, $name);

        $manager = $this->createImageManager();
        if ($manager) {
            foreach (['thumbnail_' => 300, 'thumbnail_sm_' => 100] as $prefix => $size) {
                $image = $manager->make($dir . '/' . $name);
                if ($image->width() > $image->height()) {
                    $image->resize($size, null, function ($c) { $c->aspectRatio(); });
                } else {
                    $image->resize(null, $size, function ($c) { $c->aspectRatio(); });
                }
                $image->save($dir . '/' . $prefix . $name);
            }
        } else {
            File::copy($dir . '/' . $name, $dir . '/thumbnail_' . $name);
            File::copy($dir . '/' . $name, $dir . '/thumbnail_sm_' . $name);
        }

        $hasPhotos = $proposal->photos()->exists();
        return $proposal->photos()->create([
            'file_name' => $name,
            'is_main' => !$hasPhotos,                       // the first photo is the main one
            'sort_order' => (int) $proposal->photos()->max('sort_order') + 1,
        ]);
    }

    public function delete(ProposalPhoto $photo): void
    {
        $dir = public_path(trim(Proposal::PHOTO_PATH, '/'));
        foreach (['', 'thumbnail_', 'thumbnail_sm_'] as $prefix) {
            $path = $dir . '/' . $prefix . $photo->file_name;
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $proposalId = $photo->proposal_id;
        $wasMain = $photo->is_main;
        $photo->delete();

        // Keep one main photo: promote the next one.
        if ($wasMain) {
            $next = ProposalPhoto::where('proposal_id', $proposalId)->orderBy('sort_order')->orderBy('id')->first();
            if ($next) {
                $next->update(['is_main' => true]);
            }
        }
    }
}
