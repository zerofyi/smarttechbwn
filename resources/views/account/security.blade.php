@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">Security</h2>

    {{-- Change password --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-1" style="color: #6B1A00;">Change Password</h3>
        <p class="text-sm text-gray-500 mb-4">Choose a strong password you don't use anywhere else.</p>
        <form method="POST" action="{{ route('account.password.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-w-md">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                    <input type="password" name="current_password" required autocomplete="current-password" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                    <input type="password" name="new_password" required minlength="8" autocomplete="new-password" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" autocomplete="new-password" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Update Password</button>
            </div>
        </form>
    </div>

    {{-- Sessions note --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-1" style="color: #6B1A00;">Sessions</h3>
        <p class="text-sm text-gray-600">
            You are signed in as <span class="font-semibold">{{ $user->email ?? '' }}</span>.
            @if(!empty($user->last_login_at))
                Your last sign-in was {{ optional($user->last_login_at)->format('d M Y, h:i A') ?? '' }}.
            @endif
            If you suspect someone else has access to your account, change your password above and sign out everywhere by using the Logout option in the sidebar.
        </p>
        <a href="{{ route('account.profile') }}" class="inline-block mt-3 text-sm text-red-800 font-semibold hover:underline">Back to Profile</a>
    </div>
</div>
@endsection
