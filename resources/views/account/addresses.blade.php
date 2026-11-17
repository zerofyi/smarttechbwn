@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">My Addresses</h2>

    @php $defaultId = optional(optional(auth()->user())->profile)->default_address_id ?? null; @endphp

    {{-- Saved addresses --}}
    @if(($addresses ?? collect())->isEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
            <i class="fas fa-location-dot text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">You have no saved addresses yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($addresses as $address)
                <div class="bg-white rounded-xl border shadow-sm p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-semibold text-sm text-gray-800 capitalize">{{ $address->address_type ?? 'Address' }}</span>
                            @if($defaultId && $address->address_id == $defaultId)
                                <span class="ml-2 text-xs font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Default</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium text-gray-800">{{ $address->contact_person ?? '' }}</span> &middot; {{ $address->contact_number ?? '' }}<br>
                        {{ $address->address_line_1 ?? '' }}
                        @if(!empty($address->address_line_2))
                        , {{ $address->address_line_2 }}
                        @endif
                        @if(!empty($address->landmark))
                        , {{ $address->landmark }}
                        @endif
                        <br>
                        {{ $address->city ?? '' }}
                        @if(!empty($address->state))
                        , {{ $address->state }}
                        @endif
                        @if(!empty($address->postal_code))
                        &ndash; {{ $address->postal_code }}
                        @endif
                        @if(!empty($address->country))
                        , {{ $address->country }}
                        @endif
                    </p>

                    <div class="flex flex-wrap gap-2 mt-4">
                        @if(!($defaultId && $address->address_id == $defaultId))
                            <form method="POST" action="{{ route('account.addresses.default', $address->address_id) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-xs font-semibold rounded-lg">Set as Default</button>
                            </form>
                        @endif
                        <details class="w-full">
                            <summary class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-xs font-semibold rounded-lg cursor-pointer inline-block">Edit</summary>
                            <form method="POST" action="{{ route('account.addresses.update', $address->address_id) }}" class="mt-3 space-y-3 border-t pt-3">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                        <select name="address_type" class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                            @foreach(['home' => 'Home', 'work' => 'Work', 'other' => 'Other'] as $v => $l)
                                                <option value="{{ $v }}" {{ ($address->address_type ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Contact Person *</label>
                                        <input type="text" name="contact_person" value="{{ $address->contact_person ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Contact Number *</label>
                                        <input type="text" name="contact_number" value="{{ $address->contact_number ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">City *</label>
                                        <input type="text" name="city" value="{{ $address->city ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Address Line 1 *</label>
                                        <input type="text" name="address_line_1" value="{{ $address->address_line_1 ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Address Line 2</label>
                                        <input type="text" name="address_line_2" value="{{ $address->address_line_2 ?? '' }}" class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Landmark</label>
                                        <input type="text" name="landmark" value="{{ $address->landmark ?? '' }}" class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Postal Code *</label>
                                        <input type="text" name="postal_code" value="{{ $address->postal_code ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">State *</label>
                                        <input type="text" name="state" value="{{ $address->state ?? '' }}" required class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                                        <input type="text" name="country" value="{{ $address->country ?? 'India' }}" class="w-full px-2 py-1.5 border rounded-lg text-sm">
                                    </div>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-red-800 text-white text-xs font-bold rounded-lg hover:bg-red-900">Save Changes</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('account.addresses.destroy', $address->address_id) }}" onsubmit="return confirm('Delete this address?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add new address --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-4" style="color: #6B1A00;">Add New Address</h3>
        <form method="POST" action="{{ route('account.addresses.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type *</label>
                    <select name="address_type" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="home" {{ old('address_type') == 'home' ? 'selected' : '' }}>Home</option>
                        <option value="work" {{ old('address_type') == 'work' ? 'selected' : '' }}>Work</option>
                        <option value="other" {{ old('address_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person *</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                    <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Landmark</label>
                    <input type="text" name="landmark" value="{{ old('landmark') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                    <input type="text" name="state" value="{{ old('state') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', 'India') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Add Address</button>
        </form>
    </div>
</div>
@endsection
