<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Loan;
use App\Models\Financer;
use App\Models\Agreement;
use App\Models\Customer;
use App\Models\Emi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    public function create($invoice_id)
    {
        $invoice = Invoice::with(['customer', 'party'])
            ->where('invoice_id', $invoice_id)
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $financers = Financer::where('user_id', Auth::id())->get();
        $shop = Auth::user()->shop;

        $isParty = $invoice->invoice_for === 'party';
        $subject = $isParty ? $invoice->party : $invoice->customer;

        return view('distributor.loan-create', [
            'invoice' => $invoice,
            'financers' => $financers,
            'subject' => $subject,
            'isParty' => $isParty,
            'shop' => $shop
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,invoice_id',
                'financer_id' => 'required|exists:financers,financer_id',
                'loan_slab' => 'required|in:1,15',
                'down_payment' => 'required|numeric|min:0',
                'processing_charge' => 'required|numeric|min:0',
                'file_charge' => 'required|numeric|min:0',
                'emi_amount' => 'required|numeric|min:0',
                'emi_count' => 'required|integer|min:1',
                'first_emi_date' => 'required|date',

                // Customer fields
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'alternative_mobile' => 'nullable|string|max:20',
                'address' => 'required|string',
                'aadhaar_no' => 'required|string|max:20',
                'pan_no' => 'nullable|string|max:20',

                // Files
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'doc_one' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'doc_two' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'doc_three' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $invoice = Invoice::findOrFail($request->invoice_id);
            $financer = Financer::findOrFail($request->financer_id);

            // Handle customer creation/update
            if ($invoice->customer_id) {
                $customer = Customer::findOrFail($invoice->customer_id);
                $customer->update([
                    'name' => $request->name,
                    'mobile' => $request->mobile,
                    'address' => $request->address,
                    'aadhaar_no' => $request->aadhaar_no,
                    'pan_no' => $request->pan_no,
                ]);
            } else {
                $customer = Customer::create([
                    'distributor_id' => Auth::id(),
                    'name' => $request->name,
                    'mobile' => $request->mobile,
                    'address' => $request->address,
                    'aadhaar_no' => $request->aadhaar_no,
                    'pan_no' => $request->pan_no,
                ]);
                $invoice->update(['customer_id' => $customer->customer_id]);
            }

            // Handle file uploads
            $customerUpdates = [];
            foreach (['photo', 'doc_one', 'doc_two', 'doc_three'] as $field) {
                if ($request->hasFile($field)) {
                    $customerUpdates[$field] = $this->uploadFile($request->file($field), 'customers/'.$customer->customer_id, $field);
                }
            }

            if (!empty($customerUpdates)) {
                $customer->update($customerUpdates);
            }

            // Create agreement
            $agreement = Agreement::create([
                'shop_id' => Auth::user()->shop->shop_id,
                'financer_id' => $financer->financer_id,
                'customer_id' => $customer->customer_id,
                'type' => 'loan',
                'photo' => $request->hasFile('agreement_photo') ?
                    $this->uploadFile($request->file('agreement_photo'), 'agreements', 'loan-agreement') : null,
            ]);

            // Calculate loan details
            $loanAmount = $invoice->total_amount - $request->down_payment;
            $payableAmount = $loanAmount + $request->processing_charge + $request->file_charge;

            // Create loan
            $loan = Loan::create([
                'financer_id' => $financer->financer_id,
                'agreement_id' => $agreement->agreement_id,
                'invoice_id' => $invoice->invoice_id,
                'customer_id' => $customer->customer_id,
                'loan_no' => 'LN'.time(),
                'loan_slab' => $request->loan_slab,
                'invoice_amount' => $invoice->total_amount,
                'down_payment' => $request->down_payment,
                'loan_amount' => $loanAmount,
                'processing_charge' => $request->processing_charge,
                'file_charge' => $request->file_charge,
                'payable_amount' => $payableAmount,
                'paid_amount' => 0,
                'settled_amount' => 0,
                'emi_amount' => $request->emi_amount,
                'emi' => $request->emi_count,
                'loan_date' => now(),
                'first_emi' => $request->first_emi_date,
                'last_emi' => $this->calculateLastEmiDate($request->first_emi_date, $request->emi_count, $request->loan_slab),
                'loan_status' => 'open',
                'name' => $customer->name,
                'mobile' => $customer->mobile,
                'alternative_mobile' => $request->alternative_mobile,
                'address' => $customer->address,
                'aadhaar_no' => $customer->aadhaar_no,
                'pan_no' => $customer->pan_no,
                'photo' => $customer->photo,
                'doc_one' => $customer->doc_one,
                'doc_two' => $customer->doc_two,
                'doc_three' => $customer->doc_three,
            ]);

            // Generate EMIs
            $this->generateEmis($loan, $request->first_emi_date, $request->emi_count, $request->loan_slab, $request->emi_amount);

            DB::commit();

            return redirect()->route('distributor.loan.show', $loan->loan_id)
                ->with('success', 'Loan created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create loan: '.$e->getMessage());
        }
    }

    public function edit(Loan $loan)
    {
        $loan->load(['invoice', 'financer', 'customer', 'emis']);

        // Verify the loan belongs to the authenticated distributor
        if ($loan->invoice->distributor_id !== Auth::id()) {
            abort(403);
        }

        if ($loan->loan_status !== 'open') {
            abort(403);
        }

        $financers = Financer::where('user_id', Auth::id())->get();

        return view('distributor.loan-edit', [
            'loan' => $loan,
            'financers' => $financers,
            'customer' => $loan->customer,
        ]);
    }

    public function update(Request $request, Loan $loan)
    {
        DB::beginTransaction();

        try {
            // Verify loan ownership
            if ($loan->invoice->distributor_id !== Auth::id()) {
                abort(403);
            }

            $validated = $request->validate([
                'financer_id' => 'required|exists:financers,financer_id',
                'loan_slab' => 'required|in:1,15',
                'down_payment' => 'required|numeric|min:0|max:'.$loan->invoice->total_amount,
                'processing_charge' => 'required|numeric|min:0',
                'file_charge' => 'required|numeric|min:0',
                'emi_amount' => 'required|numeric|min:0',
                'emi_count' => 'required|integer|min:1',
                'first_emi_date' => 'required|date',
            ]);

            // Calculate updated loan amounts
            $loanAmount = $loan->invoice->total_amount - $request->down_payment;
            $payableAmount = $loanAmount + $request->processing_charge + $request->file_charge;

            // Update loan details
            $loan->update([
                'financer_id' => $request->financer_id,
                'loan_slab' => $request->loan_slab,
                'down_payment' => $request->down_payment,
                'loan_amount' => $loanAmount,
                'processing_charge' => $request->processing_charge,
                'file_charge' => $request->file_charge,
                'payable_amount' => $payableAmount,
                'emi_amount' => $request->emi_amount,
                'emi' => $request->emi_count,
                'first_emi' => $request->first_emi_date,
                'last_emi' => $this->calculateLastEmiDate($request->first_emi_date, $request->emi_count, $request->loan_slab),
            ]);

            // Only regenerate EMIs if count or amount changed
            if ($loan->wasChanged(['emi_amount', 'emi_count', 'first_emi_date', 'loan_slab'])) {
                // Delete future EMIs (not paid yet)
                $futureEmis = $loan->emis()
                    ->where('paid_amount', 0)
                    ->where('emi_date', '>', now());

                // Update emi_no before deleting
                $futureEmis->get()->each(function($emi) {
                    $emi->update(['emi_no' => $emi->emi_no . '-D']);
                });

                // Now delete them
                $futureEmis->delete();

                // Regenerate remaining EMIs
                $remainingEmis = $request->emi_count - $loan->emis()->where('paid_amount', '>', 0)->count();

                if ($remainingEmis > 0) {
                    $lastPaidEmi = $loan->emis()->where('paid_amount', '>', 0)
                        ->orderBy('emi_date', 'desc')
                        ->first();

                    $startDate = $lastPaidEmi ? $lastPaidEmi->emi_date->addMonth() : $request->first_emi_date;

                    $this->generateEmis(
                        $loan,
                        $startDate,
                        $remainingEmis,
                        $request->loan_slab,
                        $request->emi_amount
                    );
                }
            }

            DB::commit();

            return redirect()->route('distributor.loan.show', $loan->loan_id)
                ->with('success', 'Loan updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update loan: '.$e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $financers = Financer::where('user_id', Auth::id())->get();

        $loans = Loan::with(['financer', 'customer', 'invoice'])
            ->whereHas('invoice', function($query) {
                $query->where('distributor_id', Auth::id());
            })
            ->when($request->filled('financer_id'), function($query) use ($request) {
                $query->where('financer_id', $request->financer_id);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                $query->where('loan_status', $request->status);
            })
            ->when($request->filled('loan_slab'), function($query) use ($request) {
                $query->where('loan_slab', $request->loan_slab);
            })
            ->when($request->filled('customer_id'), function($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('from_date'), function($query) use ($request) {
                $query->whereDate('loan_date', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function($query) use ($request) {
                $query->whereDate('loan_date', '<=', $request->to_date);
            })
            ->when($request->filled('search'), function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('loan_no', 'like', '%'.$request->search.'%')
                    ->orWhere('name', 'like', '%'.$request->search.'%')
                    ->orWhere('mobile', 'like', '%'.$request->search.'%')
                    ->orWhereHas('financer', function($q) use ($request) {
                        $q->where('name', 'like', '%'.$request->search.'%');
                    });
                });
            })
            ->latest()
            ->paginate(25)
            ->appends($request->except('page'));

        return view('distributor.loans-index', compact('loans', 'financers'));
    }

    public function show(Loan $loan)
    {
        $loan->load(['financer', 'customer', 'invoice', 'agreement', 'emis']);
        return view('distributor.loan-show', compact('loan'));
    }

    public function print(Loan $loan)
    {
        $loan->load(['financer', 'customer', 'invoice', 'agreement', 'emis']);
        return view('distributor.loan-print', compact('loan'));
    }

    public function payEmi(Request $request, Loan $loan)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'emi_id' => 'required|exists:emis,emi_id,loan_id,'.$loan->loan_id,
                'emi_amount' => 'required|numeric|min:0|max:'.$loan->emi_amount,
                'paid_date' => 'required|date|before_or_equal:today',
                'pay_method' => 'required|string|in:cash,bank_transfer,cheque,upi',
            ]);

            // Find the existing EMI
            $emi = Emi::where('loan_id', $loan->loan_id)
                    ->where('emi_id', $request->emi_id)
                    ->firstOrFail();

            // Update the EMI record
            $emi->update([
                'paid_date' => $request->paid_date,
                'paid_amount' => $request->emi_amount,
                'due_amount' => $emi->emi_amount - $request->emi_amount,
                'method' => $request->pay_method,
            ]);

            // Recalculate loan paid amount from all EMIs
            $totalPaid = $loan->emis()->sum('paid_amount');
            $loan->paid_amount = $totalPaid;

            // Check if loan is fully paid
            if ($loan->paid_amount >= $loan->payable_amount-1) {
                $loan->loan_status = 'closed';
            }

            $loan->save();

            DB::commit();

            return back()->with('success', 'EMI payment recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: '.$e->getMessage());
        }
    }

    public function settle(Request $request, Loan $loan)
    {
        DB::beginTransaction();

        try {
            $totalDue = $loan->payable_amount - $loan->paid_amount;
            $validated = $request->validate([
                'discount' => 'required|numeric|min:1|max:' . min($loan->processing_charge, $totalDue),
            ]);

            $discount = $request->discount;
            $discountedDue = $totalDue-$discount;
            $newPayable = $loan->payable_amount-$discount;

            $loan->update([
                'processing_charge' => $loan->processing_charge-$discount,
                'payable_amount' => $newPayable,
                'paid_amount' => $loan->paid_amount + $discountedDue,
                'settled_amount' => $discount,
                'loan_status' => 'closed',
            ]);

            $futureEmis = $loan->emis()
                ->where('paid_amount', 0);

            $futureEmisCount = $futureEmis->count();
            $newEmiAmount = $futureEmisCount > 0 ? $discountedDue / $futureEmisCount : 0;

            // Update emi_no before deleting
            $futureEmis->get()->each(function($emi) use ($newEmiAmount) {
                $emi->update([
                    'paid_date' => now(),
                    'emi_amount' => $newEmiAmount,
                    'paid_amount' => $newEmiAmount,
                    'due_amount' => 0,
                ]);
            });

            $loan->save();

            DB::commit();

            return back()->with('success', 'Loan settled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to settle: '.$e->getMessage());
        }
    }

    protected function calculateLastEmiDate($firstEmiDate, $emiCount, $loanSlab)
    {
        $date = \Carbon\Carbon::parse($firstEmiDate);
        return $date->addMonths($emiCount - 1);
    }

    protected function generateEmis($loan, $firstEmiDate, $emiCount, $loanSlab, $emiAmount)
    {
        $date = \Carbon\Carbon::parse($firstEmiDate);

        for ($i = 1; $i <= $emiCount; $i++) {
            Emi::create([
                'loan_id' => $loan->loan_id,
                'emi_no' => 'EMI'.$loan->loan_no.'-'.$i,
                'emi_amount' => $emiAmount,
                'emi_date' => $date->copy()->addMonths($i - 1),
                'paid_amount' => 0, // All EMIs start as unpaid
                'due_amount' => $emiAmount, // Full amount is due initially
                'method' => null,
            ]);
        }
    }

    protected function uploadFile($file, $directory, $prefix)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $prefix.'-'.Str::random(10).'.'.$extension;
        return $file->storeAs($directory, $filename, 'public_html');
    }

    public function export(Request $request)
    {
        $loans = Loan::with(['financer'])
            ->when($request->status, fn($q) => $q->where('loan_status', $request->status))
            ->when($request->financer_id, fn($q) => $q->where('financer_id', $request->financer_id))
            ->when($request->loan_slab, fn($q) => $q->where('loan_slab', $request->loan_slab))
            ->orderBy('loan_no', 'asc')
            ->get();

        // === HEADER INFO ===
        $slab = $request->loan_slab ? $request->loan_slab . ' EMI' : 'All Slabs';
        $financerName = $request->financer_id
            ? optional(Financer::find($request->financer_id))->name
            : 'All Financers';
        $date = now()->format('d-m-Y');

        $filename = "loan_export_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');

        // Send headers
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

        // Top Header
        fputcsv($handle, []); // empty row (top outline)
        fputcsv($handle, ["Slab " . $slab . " List of " . $financerName . " - " . $date]);
        fputcsv($handle, []); // another blank row (spacing)

        // Table Headers
        fputcsv($handle, ['Customer', 'Mobile', 'EMI', 'Due Amount', 'Remarks']);

        // Data Rows
        foreach ($loans as $loan) {
            $payableAmount  = $loan->payable_amount ?: 0;
            $paidAmount     = $loan->paid_amount ?: 0;
            $dueAmount      = number_format($payableAmount - $paidAmount, 2);

            fputcsv($handle, [
                $loan->name,
                $loan->mobile,
                $loan->emi_amount,
                "{$payableAmount} - {$paidAmount} = {$dueAmount}",
                '' // empty remarks
            ]);
        }

        fputcsv($handle, []); // bottom empty row (outline)

        fclose($handle);
        exit;
    }

}
