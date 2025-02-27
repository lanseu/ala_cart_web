<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface ProfilePictureServiceInterface
{
    public function uploadProfilePicture(User $user, UploadedFile $file): string;
}
