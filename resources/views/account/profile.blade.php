@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">Profile</h2>

    @php $profile = $user->profile ?? null; @endphp

    {{-- Avatar --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-4" style="color: #6B1A00;">Profile Photo</h3>
        <div class="flex items-center gap-4">
            @if(!empty($profile->profile_picture))
                <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="Profile photo" class="w-20 h-20 rounded-full object-cover border">
            @else
                <div class="w-20 h-20 rounded-full bg-red-800 text-white flex items-center justify-center font-bold text-2xl">
                    {{ $user->initials() ?? strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
            @endif
            <form method="POST" action="{{ route('account.avatar.update') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                @csrf
                <input type="file" name="avatar" accept="image/*" required class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-800 hover:file:bg-red-100">
                <button type="submit" class="px-4 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Upload</button>
            </form>
        </div>
    </div>

    {{-- Profile details --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-4" style="color: #6B1A00;">Personal Details</h3>
        <form method="POST" action="{{ route('account.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $user->email ?? '' }}" disabled class="w-full px-3 py-2 border rounded-lg text-sm bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">Email cannot be changed here.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                    <input type="text" value="{{ $user->mobile ?? '' }}" disabled class="w-full px-3 py-2 border rounded-lg text-sm bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Mobile</label>
                    <input type="text" name="alternate_mobile" value="{{ old('alternate_mobile', $profile->alternate_mobile ?? '') }}" maxlength="15" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number ?? '') }}" maxlength="15" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">Prefer not to say</option>
                        @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('gender', $profile->gender ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Save Changes</button>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
        <h3 class="font-bold text-base mb-1 text-red-800">Delete Account</h3>
        <p class="text-sm text-gray-500 mb-4">Permanently delete your account and personal data. This cannot be undone.</p>
        <form method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="password" name="password" placeholder="Enter your password to confirm" required class="flex-1 px-3 py-2 border rounded-lg text-sm">
                <button type="submit" class="px-5 py-2 bg-red-700 text-white text-sm font-semibold rounded-lg hover:bg-red-800">Delete My Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
