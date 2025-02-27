<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProfilePictureServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilePictureController extends Controller
{
    protected ProfilePictureServiceInterface $profilePictureService;

    public function __construct(ProfilePictureServiceInterface $profilePictureService)
    {
        $this->profilePictureService = $profilePictureService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // ✅ Ensure user is authenticated before proceeding
        if (! $user instanceof User) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $file = $request->file('profile_picture');
        $path = $this->profilePictureService->uploadProfilePicture($user, $file);

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'profile_picture_url' => asset('storage/'.$path),
        ]);
    }
}
