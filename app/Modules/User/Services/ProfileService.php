<?php

namespace App\Modules\User\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update the user profile data.
     */
    public function updateProfile(User $user, array $data): Profile
    {
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $profile->fill([
            'full_name'      => $data['full_name'],
            'phone'          => $data['phone'],
            'address'        => $data['address'] ?? null,
            'city'           => $data['city'] ?? null,
            'province'       => $data['province'] ?? null,
            'occupation'     => $data['occupation'] ?? null,
            'monthly_income' => $data['monthly_income'] ?? null,
        ]);

        $profile->save();

        return $profile;
    }

    /**
     * Upload and update user avatar image.
     */
    public function updateAvatar(User $user, UploadedFile $file): string
    {
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id, 'full_name' => 'User', 'phone' => '']);

        // Delete old avatar if exists
        if ($profile->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
        }

        // Store new avatar file in public disk
        $path = $file->store('avatars', 'public');
        $profile->update(['avatar_path' => $path]);

        return $path;
    }
}
