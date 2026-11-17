<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();

        return view('account.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $this->validateAddress($request);

        $address = $user->addresses()->create($validated);

        // First address becomes the default automatically.
        $profile = $user->profile;
        if (! $profile || ! $profile->default_address_id) {
            UserProfile::updateOrCreate(
                ['user_id' => $user->user_id],
                ['default_address_id' => $address->address_id]
            );
        }

        return back()->with('success', 'Address added.');
    }

    public function update(Request $request, $address)
    {
        $user = auth()->user();

        $address = $user->addresses()
            ->where('address_id', (int) $address)
            ->first();

        if (! $address) {
            abort(404);
        }

        $address->update($this->validateAddress($request));

        return back()->with('success', 'Address updated.');
    }

    public function destroy($address)
    {
        $user = auth()->user();

        $address = $user->addresses()
            ->where('address_id', (int) $address)
            ->first();

        if (! $address) {
            abort(404);
        }

        $address->delete();

        // If it was the default, clear the default so checkout does not
        // point at a deleted address.
        $profile = $user->profile;
        if ($profile && (int) $profile->default_address_id === (int) $address->address_id) {
            $profile->update(['default_address_id' => null]);
        }

        return back()->with('success', 'Address deleted.');
    }

    public function setDefault($address)
    {
        $user = auth()->user();

        $address = $user->addresses()
            ->where('address_id', (int) $address)
            ->first();

        if (! $address) {
            abort(404);
        }

        UserProfile::updateOrCreate(
            ['user_id' => $user->user_id],
            ['default_address_id' => $address->address_id]
        );

        return back()->with('success', 'Default address updated.');
    }

    protected function validateAddress(Request $request): array
    {
        $validated = $request->validate([
            'address_type' => 'required|in:home,work,other',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|regex:/^[0-9]{10}$/',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|regex:/^[0-9]{6}$/',
            'country' => 'nullable|string|max:100',
        ]);

        $validated['country'] = $validated['country'] ?? 'India';

        return $validated;
    }
}
