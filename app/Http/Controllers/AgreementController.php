<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Agreement;
use App\Models\Shop;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AgreementController extends Controller
{

    public function index()
    {
        // Get the authenticated user's shop ID
        $shopId = Auth::user()->shop->shop_id;

        $agreements = Agreement::with(['customer', 'invoices', 'shop'])
            ->where('shop_id', $shopId)
            ->latest()
            ->paginate(15);

        return view('distributor.agreements', compact('agreements'));
    }

    // Create Agreement
    public function create($invoice_id)
    {
        $invoice = Invoice::with(['customer', 'party'])
            ->where('invoice_id', $invoice_id)
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $shop = Shop::where('user_id', Auth::id())->firstOrFail();

        $isParty = $invoice->invoice_for === 'party';
        $subject = $isParty ? $invoice->party : $invoice->customer;

        // Map data based on invoice type
        $formData = [
            'name' => $subject->name ?? '',
            'mobile' => $isParty ? ($subject->phone ?? '') : ($subject->mobile ?? ''),
            'address' => $subject->address ?? '',
            'pan_no' => $isParty ? ($subject->pan ?? '') : ($subject->pan_no ?? ''),
            'aadhaar_no' => $isParty ? '' : ($subject->aadhaar_no ?? ''),
        ];

        return view('distributor.agreement-create', [
            'invoice' => $invoice,
            'subject' => $subject,
            'formData' => $formData,
            'isParty' => $isParty,
            'shop' => $shop
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateAgreementRequest($request);

            $invoice = Invoice::where('invoice_id', $validated['invoice_id'])
                ->where('distributor_id', Auth::id())
                ->firstOrFail();

            // Handle customer creation/update
            $customer = $this->handleCustomer($request, $invoice);

            // Handle file uploads
            $customerUpdates = $this->handleCustomerFiles($request, $customer);

            if (!empty($customerUpdates)) {
                $customer->update($customerUpdates);
            }

            // Create agreement
            $agreement = $this->createAgreement($request, $invoice, $customer);

            // Link agreement to invoice
            $invoice->update(['agreement_id' => $agreement->agreement_id]);

            DB::commit();

            return redirect()->route('distributor.invoices.show', $invoice->invoice_id)
                ->with('success', 'Agreement created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create agreement: ' . $e->getMessage());
        }
    }

    // Edit Agreement
    public function edit(Agreement $agreement)
    {
        $agreement->load(['customer', 'shop', 'invoices']);

        // Ensure the agreement belongs to the authenticated distributor
        if ($agreement->shop->user_id !== Auth::id()) {
            abort(403);
        }

        $invoice = $agreement->invoices->first();
        $shop = $agreement->shop;
        $customer = $agreement->customer;

        return view('distributor.agreement-edit', [
            'agreement' => $agreement,
            'invoice' => $invoice,
            'customer' => $customer,
            'shop' => $shop,
            'formData' => [
                'name' => $customer->name,
                'mobile' => $customer->mobile,
                'address' => $customer->address,
                'pan_no' => $customer->pan_no,
                'aadhaar_no' => $customer->aadhaar_no,
            ]
        ]);
    }

    public function update(Request $request, Agreement $agreement)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateAgreementRequest($request, true);

            // Ensure the agreement belongs to the authenticated distributor
            if ($agreement->customer->distributor_id !== Auth::id()) {
                abort(403);
            }

            $customer = $agreement->customer;
            $customerUpdates = [
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'address' => $validated['address'],
                'pan_no' => $validated['pan_no'] ?? null,
                'aadhaar_no' => $validated['aadhaar_no'] ?? null,
            ];

            // Handle file uploads for update
            $fileUpdates = $this->handleCustomerFiles($request, $customer, true);
            $customerUpdates = array_merge($customerUpdates, $fileUpdates);

            $customer->update($customerUpdates);

            // Handle agreement photo update
            if ($request->hasFile('agreement_photo')) {
                // Delete old photo if exists
                if ($agreement->photo) {
                    Storage::disk('public_html')->delete($agreement->photo);
                }

                $agreement->update([
                    'photo' => $this->uploadFile(
                        $request->file('agreement_photo'),
                        'Agreements',
                        $agreement->agreement_id
                    )
                ]);
            }

            DB::commit();

            return redirect()->route('distributor.invoices.show', $agreement->invoices->first()->invoice_id)
                ->with('success', 'Agreement updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update agreement: ' . $e->getMessage());
        }
    }

    // Helper Methods
    protected function validateAgreementRequest(Request $request, $isUpdate = false)
    {
        $rules = [
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'shop_id' => 'required|exists:shops,shop_id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'aadhaar_no' => 'nullable|string|max:20',
            'pan_no' => 'nullable|string|max:20|regex:/[A-Z]{5}[0-9]{4}[A-Z]{1}/',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'doc_one' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'doc_two' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'doc_three' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'agreement_photo' => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if (!$isUpdate) {
            $rules['is_party'] = 'required|boolean';
        }

        return $request->validate($rules);
    }

    protected function handleCustomer(Request $request, Invoice $invoice)
    {
        if ($request->is_party) {
            return Customer::create([
                // 'distributor_id' => Auth::id(),
                'name' => $request->name,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'pan_no' => $request->pan_no ?? null,
                'aadhaar_no' => $request->aadhaar_no ?? null,
            ]);
        }

        $customer = Customer::where('customer_id', $invoice->customer_id)
            // ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $customer->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'pan_no' => $request->pan_no ?? null,
            'aadhaar_no' => $request->aadhaar_no ?? null,
        ]);

        return $customer;
    }

    protected function handleCustomerFiles(Request $request, Customer $customer, $isUpdate = false)
    {
        $updates = [];
        $fileFields = ['photo', 'signature', 'doc_one', 'doc_two', 'doc_three'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($isUpdate && $customer->{$field}) {
                    Storage::disk('public_html')->delete($customer->{$field});
                }

                $updates[$field] = $this->uploadFile(
                    $request->file($field),
                    "Customers/{$customer->customer_id}",
                    $field
                );
            }
        }

        return $updates;
    }

    protected function createAgreement(Request $request, Invoice $invoice, Customer $customer)
    {
        return Agreement::create([
            'shop_id' => $request->shop_id,
            'customer_id' => $customer->customer_id,
            'type' => $invoice->invoice_type,
            'photo' => $this->uploadFile(
                $request->file('agreement_photo'),
                'Agreements',
                'agreement-' . Str::random(6)
            ),
        ]);
    }

    public function print(Agreement $agreement)
    {
        // Verify agreement belongs to authenticated user's shop
        if ($agreement->shop_id !== Auth::user()->shop->shop_id) {
            abort(403, 'Unauthorized action.');
        }

        $agreement->load([
            'customer',
            'invoices.items',
            'shop',
            'shop.user'
        ]);

        $invoice = $agreement->invoices->first();
        $shop = $agreement->shop;
        $customer = $agreement->customer;

        return view('distributor.agreement', [
            'agreement' => $agreement,
            'invoice' => $invoice,
            'shop' => $shop,
            'customer' => $customer,
            'date' => now()->format('d M Y'),
            'day' => now()->format('d'),
            'month' => now()->format('F'),
            'year' => now()->format('Y'),
            'signatory' => $shop->user->name ?? Auth::user()->name
        ]);
    }

    protected function uploadFile($file, $folder, $identifier)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $identifier . '-' . $randomString . '.' . $extension;
            return $file->storeAs($folder, $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error("File upload failed for {$folder}: " . $e->getMessage());
            return null;
        }
    }
}
