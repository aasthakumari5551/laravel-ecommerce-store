<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;

use App\Services\ProfileService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load([
            'orders',
            'reviews',
        ]);

        $avatarUrl = $this->profileService->avatarUrl($user);

        $orderCount = $user->orders()->count();

        $reviewCount = $user->reviews()->count();

        return view('shop.profile.edit', [
            'user'        => $user,
            'avatarUrl'   => $avatarUrl,
            'orderCount'  => $orderCount,
            'reviewCount' => $reviewCount,
        ]);
    }

    /**
     * Update profile information.
     */
    public function update(
        UpdateProfileRequest $request
    ): RedirectResponse {

        $data = $request->safe()->except('avatar');

        // Upload avatar

        if ($request->hasFile('avatar')) {

            $this->profileService->updateAvatar(
                auth()->user(),
                $request->file('avatar')
            );
        }

        // Update profile

        $this->profileService->update(
            auth()->user(),
            $data
        );

        return back()->with(
            'success',
            'Profile updated.'
        );
    }

    /**
     * Update account password.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): RedirectResponse {

        $this->profileService->updatePassword(
            auth()->user(),
            $request->validated('password')
        );

        return back()->with(
            'success',
            'Password changed successfully.'
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}