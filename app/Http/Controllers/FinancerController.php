<?php

namespace App\Http\Controllers;

use App\Models\Financer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinancerController extends Controller
{
    public function index()
    {
        $financers = Financer::where('user_id', Auth::id())
            ->withCount(['loans' => function($query) {
                $query->where('loan_status', 'open');
            }])
            ->with(['loans' => function($query) {
                $query->where('loan_status', 'open');
            }])
            ->latest()
            ->get()
            ->map(function($financer) {
                // Calculate loan statistics
                $financer->total_loan_amount = $financer->loans->sum('loan_amount');
                $financer->total_payable_amount = $financer->loans->sum('payable_amount');
                $financer->total_paid_amount = $financer->loans->sum('paid_amount');
                $financer->pending_amount = $financer->total_payable_amount - $financer->total_paid_amount;
                $financer->recovery_percentage = $financer->total_payable_amount > 0
                    ? ($financer->total_paid_amount / $financer->total_payable_amount) * 100
                    : 0;

                return $financer;
            });

        $total = $financers->count();

        return view('distributor.financers', compact('financers', 'total'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'id_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'doc_one' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_two' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_three' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:20|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan' => 'nullable|string|max:20|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'bank' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'ac_no' => 'nullable|string|max:30',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid.',
            'pan.regex' => 'The PAN format is invalid.',
            'photo.max' => 'The photo must not be greater than 2MB.',
            'doc_one.max' => 'The document must not be greater than 5MB.',
            'doc_two.max' => 'The document must not be greater than 5MB.',
            'doc_three.max' => 'The document must not be greater than 5MB.',
            'signature.max' => 'The signature must not be greater than 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $financerData = $request->except(['photo', 'doc_one', 'doc_two', 'doc_three', 'signature']);
        $financerData['user_id'] = Auth::id();

        // Handle file uploads
        if ($request->hasFile('photo')) {
            $financerData['photo'] = $this->uploadFinancerFile($request->file('photo'), 'photo', $financerData['name']);
        }

        if ($request->hasFile('doc_one')) {
            $financerData['doc_one'] = $this->uploadFinancerFile($request->file('doc_one'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('doc_two')) {
            $financerData['doc_two'] = $this->uploadFinancerFile($request->file('doc_two'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('doc_three')) {
            $financerData['doc_three'] = $this->uploadFinancerFile($request->file('doc_three'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('signature')) {
            $financerData['signature'] = $this->uploadFinancerFile($request->file('signature'), 'signature', $financerData['name']);
        }

        Financer::create($financerData);

        return redirect()->route('distributor.financers')
            ->with('success', 'Financer created successfully');
    }

    public function update(Request $request, Financer $financer)
    {
        // Verify the financer belongs to the current user
        if ($financer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'id_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'doc_one' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_two' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_three' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:20|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan' => 'nullable|string|max:20|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'bank' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'ac_no' => 'nullable|string|max:30',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid.',
            'pan.regex' => 'The PAN format is invalid.',
            'photo.max' => 'The photo must not be greater than 2MB.',
            'doc_one.max' => 'The document must not be greater than 5MB.',
            'doc_two.max' => 'The document must not be greater than 5MB.',
            'doc_three.max' => 'The document must not be greater than 5MB.',
            'signature.max' => 'The signature must not be greater than 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $financerData = $request->except(['photo', 'doc_one', 'doc_two', 'doc_three', 'signature']);

        // Handle file uploads
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($financer->photo) {
                Storage::disk('public_html')->delete($financer->photo);
            }
            $financerData['photo'] = $this->uploadFinancerFile($request->file('photo'), 'photo', $financerData['name']);
        }

        if ($request->hasFile('doc_one')) {
            if ($financer->doc_one) {
                Storage::disk('public_html')->delete($financer->doc_one);
            }
            $financerData['doc_one'] = $this->uploadFinancerFile($request->file('doc_one'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('doc_two')) {
            if ($financer->doc_two) {
                Storage::disk('public_html')->delete($financer->doc_two);
            }
            $financerData['doc_two'] = $this->uploadFinancerFile($request->file('doc_two'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('doc_three')) {
            if ($financer->doc_three) {
                Storage::disk('public_html')->delete($financer->doc_three);
            }
            $financerData['doc_three'] = $this->uploadFinancerFile($request->file('doc_three'), 'doc', $financerData['name']);
        }

        if ($request->hasFile('signature')) {
            if ($financer->signature) {
                Storage::disk('public_html')->delete($financer->signature);
            }
            $financerData['signature'] = $this->uploadFinancerFile($request->file('signature'), 'signature', $financerData['name']);
        }

        $financer->update($financerData);

        return redirect()->route('distributor.financers')
            ->with('success', 'Financer updated successfully');
    }

    public function destroy(Financer $financer)
    {
        // Verify the financer belongs to the current user
        if ($financer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($financer->agreements()->count() > 0 || $financer->loans()->count() > 0) {
            return redirect()->route('distributor.financers')
                ->with('error', 'Cannot delete party with associated invoices');
        }

        // Delete associated files
        if ($financer->photo) {
            Storage::disk('public_html')->delete($financer->photo);
        }
        if ($financer->doc_one) {
            Storage::disk('public_html')->delete($financer->doc_one);
        }
        if ($financer->doc_two) {
            Storage::disk('public_html')->delete($financer->doc_two);
        }
        if ($financer->doc_three) {
            Storage::disk('public_html')->delete($financer->doc_three);
        }
        if ($financer->signature) {
            Storage::disk('public_html')->delete($financer->signature);
        }

        $financer->delete();

        return redirect()->route('distributor.financers')
            ->with('success', 'Financer deleted successfully');
    }

    protected function uploadFinancerFile($file, $type, $name)
    {
        try {
            $slug = Str::slug($name);
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $slug . '-' . $type . '-' . $randomString . '.' . $extension;
            return $file->storeAs('Financer', $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Financer file upload failed: ' . $e->getMessage());
            return false;
        }
    }
}
