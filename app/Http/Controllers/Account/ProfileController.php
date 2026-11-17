<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('profile');

        return view('account.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alternate_mobile' => 'nullable|string|regex:/^[0-9]{10}$/',
            'whatsapp_number' => 'nullable|string|regex:/^[0-9]{10}$/',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user->update(['name' => $validated['name']]);

        UserProfile::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'alternate_mobile' => $validated['alternate_mobile'] ?? null,
                'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
            ]
        );

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password changed.');
    }

    public function updateAvatar(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $profile = $user->profile;

        if ($profile && $profile->profile_picture) {
            Storage::disk('public')->delete($profile->profile_picture);
        }

        $path = $request->file('avatar')->store('profile-pictures', 'public');

        UserProfile::updateOrCreate(
            ['user_id' => $user->user_id],
            ['profile_picture' => $path]
        );

        return back()->with('success', 'Profile picture updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string|current_password',
        ]);

        $user = auth()->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Soft-delete (User uses SoftDeletes).
        $user->delete();

        return redirect()->route('index')
            ->with('success', 'Your account has been deleted.');
    }
}
