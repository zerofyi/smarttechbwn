<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PartyController extends Controller
{
    public function index()
    {
        $parties = Party::withCount([
                'purchaseInvoices as purchase_invoices_count',
                'saleInvoices as sale_invoices_count'
            ])
            ->withSum('purchaseInvoices as purchase_amount', 'total_amount')
            ->withSum('saleInvoices as sale_amount', 'total_amount')
            ->latest()
            ->get();

        $total = $parties->count();

        return view('admin.parties', compact('parties', 'total'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:20|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan' => 'nullable|string|max:20|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'bank' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'ac_no' => 'nullable|string|max:30',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid.',
            'pan.regex' => 'The PAN format is invalid.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $partyData = $request->all();
        $partyData['user_id'] = Auth::id();

        Party::create($partyData);

        return redirect()->route('admin.parties')
            ->with('success', 'Party created successfully');
    }

    public function update(Request $request, Party $party)
    {
        // Verify the party belongs to the current user
        if ($party->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:20|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan' => 'nullable|string|max:20|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'bank' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'ac_no' => 'nullable|string|max:30',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid.',
            'pan.regex' => 'The PAN format is invalid.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $party->update($request->all());

        return redirect()->route('admin.parties')
            ->with('success', 'Party updated successfully');
    }

    public function destroy(Party $party)
    {
        // Verify the party belongs to the current user
        if ($party->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if party has any invoices before deleting
        if ($party->purchaseInvoices()->count() > 0 || $party->saleInvoices()->count() > 0) {
            return redirect()->route('admin.parties')
                ->with('error', 'Cannot delete party with associated invoices');
        }

        $party->delete();

        return redirect()->route('admin.parties')
            ->with('success', 'Party deleted successfully');
    }
}
