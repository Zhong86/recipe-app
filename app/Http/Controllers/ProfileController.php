<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        return view('profile', ['user' => auth()->user()]);
    }

    /**
     * Update the user's display name.
     */
    public function updateName(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->update(['name' => $data['name']]);

        return back()->with('success', 'Username updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'The current password is incorrect.'])
                ->withFragment('password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.')->withFragment('password');
    }

    /**
     * Upload a new profile picture.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete old avatar from S3 if it exists
        if ($user->avatar_url) {
            $path = ltrim(parse_url($user->avatar_url, PHP_URL_PATH), '/');
            Storage::disk('s3')->delete($path);
        }

        $path = Storage::disk('s3')->putFile('avatars', $request->file('avatar'));
        $avatarUrl = Storage::disk('s3')->url($path);

        $user->update(['avatar_url' => $avatarUrl]);

        return back()->with('success', 'Profile picture updated.');
    }

    /**
     * Remove the profile picture.
     */
    public function deleteAvatar()
    {
        $user = auth()->user();

        if ($user->avatar_url) {
            $path = ltrim(parse_url($user->avatar_url, PHP_URL_PATH), '/');
            Storage::disk('s3')->delete($path);
            $user->update(['avatar_url' => null]);
        }

        return back()->with('success', 'Profile picture removed.');
    }
}
