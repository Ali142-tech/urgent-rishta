<?php

namespace App\Services;

use App\TeamMember;
use App\Traits\HandlesImageUploads;
use Illuminate\Support\Facades\File;

/**
 * A team member's own profile photo, in public/team-members: the original plus
 * a 300px and a 100px thumbnail. Replacing the photo removes the old files.
 */
class TeamMemberPhotoService
{
    use HandlesImageUploads;

    /** Saves the upload as the member's photo. False when it isn't a valid image. */
    public function store(TeamMember $member, $file): bool
    {
        $extension = $file ? $this->validateUploadedImage($file) : null;
        if (!$extension) {
            return false;
        }

        $dir = public_path(TeamMember::PHOTO_PATH);
        File::ensureDirectoryExists($dir);

        $name = time() . '_' . $member->id . '_' . random_int(1000, 9999) . '.' . $extension;
        $file->move($dir, $name);

        $manager = $this->createImageManager();
        if ($manager) {
            foreach (['thumbnail_' => 300, 'thumbnail_sm_' => 100] as $prefix => $size) {
                $image = $manager->make($dir . '/' . $name);
                $image->fit($size, $size);
                $image->save($dir . '/' . $prefix . $name);
            }
        } else {
            File::copy($dir . '/' . $name, $dir . '/thumbnail_' . $name);
            File::copy($dir . '/' . $name, $dir . '/thumbnail_sm_' . $name);
        }

        $this->deleteFiles($member->photo);
        $member->photo = $name;
        $member->save();

        return true;
    }

    private function deleteFiles(?string $name): void
    {
        if (!$name) {
            return;
        }
        $dir = public_path(TeamMember::PHOTO_PATH);
        foreach (['', 'thumbnail_', 'thumbnail_sm_'] as $prefix) {
            File::delete($dir . '/' . $prefix . $name);
        }
    }
}
