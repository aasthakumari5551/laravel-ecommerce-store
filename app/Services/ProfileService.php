<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        $fillable = ['name', 'phone', 'bio', 'notification_preferences'];

        $user->update(array_intersect_key($data, array_flip($fillable)));

        return $user->fresh();
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $user->fresh();
    }

    public function avatarUrl(User $user): string
    {
        return $user->avatar
            ? Storage::disk('public')->url($user->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff';
    }
}