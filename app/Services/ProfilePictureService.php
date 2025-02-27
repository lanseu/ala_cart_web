<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePictureService implements ProfilePictureServiceInterface
{
    public function uploadProfilePicture(User $user, UploadedFile $file): string
    {
        // Define the storage path
        $path = $file->store("profile_pictures/{$user->id}", 'public');

        // Update user profile picture path
        $user->update(['profile_picture' => $path]);

        return $path;
    }
}
