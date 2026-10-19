<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Party;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $pdf;

    public function __construct(DomPDF $pdf)
    {
        $this->pdf = $pdf;
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['party', 'customer', 'items.inventory.product'])
            ->where('distributor_id', Auth::id())
            ->orderBy('invoice_date', 'desc')
            ->orderBy('invoice_id', 'desc');

        // Apply filters
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'like', '%'.$request->invoice_no.'%');
        }

        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->filled('invoice_for')) {
            $query->where('invoice_for', $request->invoice_for);
        }

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('product_id')) {
            $query->whereHas('items.inventory', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->filled('inventory_id')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('inventory_id', $request->inventory_id);
            });
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('due_amount', '<=', 0);
            } elseif ($request->status === 'unpaid') {
                $query->where('due_amount', '>', 0);
            } elseif ($request->status === 'partial') {
                $query->where('due_amount', '>', 0)
                    ->where('paid', '>', 0);
            }
        }

        $totalFilteredAmount = 0;
        if ($request->filled('invoice_type')) {
            $totalFilteredAmount = (clone $query)->sum('total_amount');
        }

        $invoices = $query->paginate(25);

        // Get filter options
        $parties = Party::where('user_id', Auth::id())->get();
        $customers = Customer::all();
        $products = Product::whereHas('inventories', function($q) {
            $q->where('user_id', Auth::id());
        })->get();

        return view('distributor.invoices', compact(
            'invoices',
            'parties',
            'customers',
            'products',
            'totalFilteredAmount'
        ));
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'party',
            'customer',
            'items' => function($query) {
                $query->withTrashed()->with(['inventory' => function($q) {
                    $q->withTrashed()->with('product');
                }]);
            },
            'agreement'
        ])
        ->where('distributor_id', Auth::id())
        ->findOrFail($id);

        return view('distributor.invoice-detail', compact('invoice'));
    }

    public function restoreItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:invoice_items,invoice_item_id',
            'inventory_id' => 'nullable|exists:inventories,inventory_id',
            'restore_inventory' => 'sometimes|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Restore the invoice item
            $item = InvoiceItem::withTrashed()->findOrFail($request->item_id);
            $item->restore();

            // Restore inventory if requested and it exists
            if ($request->restore_inventory && $request->inventory_id) {
                $inventory = Inventory::withTrashed()->findOrFail($request->inventory_id);
                $inventory->restore();
            }

            // Recalculate invoice totals
            $invoice = $item->invoice;
            $invoice->recalculateTotals();
            $invoice->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item restored successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($invoice_url)
    {
        $invoice = Invoice::where('invoice_url', $invoice_url)
        ->with([
            'distributor',
            'user',
            'party',
            'customer',
            'agreement',
            'items.inventory.product',
            'items.inventory.shop',
            'order'
        ])
        ->firstOrFail();

        return view('invoice', compact('invoice'));
    }

    public function gst($invoice_url)
    {
        $invoice = Invoice::where('invoice_url', $invoice_url)
        ->with([
            'distributor',
            'user',
            'party',
            'customer',
            'agreement',
            'items.inventory.product',
            'items.inventory.shop',
            'order'
        ])
        ->firstOrFail();

        return view('gst-invoice', compact('invoice'));
    }

    public function print2($invoice_url)
    {
        $invoice = Invoice::where('invoice_url', $invoice_url)
        ->with([
            'distributor',
            'user',
            'party',
            'customer',
            'agreement',
            'items.inventory.product',
            'items.inventory.shop',
            'order'
        ])
        ->firstOrFail();

        return view('print', compact('invoice'));
    }

    public function download($invoice_url)
    {
        $invoice = Invoice::with([
            'distributor',
            'user',
            'party',
            'customer',
            'agreement',
            'items.inventory.product',
            'items.inventory.shop',
            'order'
        ])
        ->where('invoice_url', $invoice_url)
        ->firstOrFail();

        $pdf = $this->pdf->loadView('invoice', compact('invoice'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                        'defaultFont' => 'sans-serif',
                    ]);

        return $pdf->download("MOBILE BAZAR - #{$invoice->invoice_no}.pdf");
    }
    
    
    // Hardcoded array of distributor IDs allowed to use the GST pattern
    private $gstEnabledDistributors = [3];

    public function showMigrationPage()
    {
        $distributorId = Auth::id();

        if (!in_array($distributorId, $this->gstEnabledDistributors)) {
            abort(403, 'Your account profile is not configured for structural invoice migrations.');
        }

        // Fetch all invoices for this distributor sorted oldest to newest by invoice date
        $invoices = Invoice::where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->orderBy('invoice_date', 'asc')
            ->orderBy('invoice_id', 'asc')
            ->get();

        $previewData = $this->calculateTargetInvoiceNumbers($invoices);

        return view('distributor.invoice_migration', compact('previewData'));
    }

    public function runMigration()
    {
        $distributorId = Auth::id();

        if (!in_array($distributorId, $this->gstEnabledDistributors)) {
            return redirect()->back()->with('error', 'Unauthorized migration attempt.');
        }

        $invoices = Invoice::where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->orderBy('invoice_date', 'asc')
            ->orderBy('invoice_id', 'asc')
            ->get();

        $previewData = $this->calculateTargetInvoiceNumbers($invoices);

        DB::beginTransaction();
        try {
            foreach ($previewData as $item) {
                if ($item['is_old_pattern']) {
                    Invoice::where('invoice_id', $item['id'])->update([
                        'invoice_no' => $item['preview_no']
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Invoices securely converted to fiscal GST structuring patterns successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Algorithmic loop processing to allocate sequential structural preview tags
     */
    private function calculateTargetInvoiceNumbers($invoices)
    {
        $processed = [];
        $sessionSequences = [];

        // Step 1: Scan and seed initial max counts based on existing GST numbers in the DB
        foreach ($invoices as $invoice) {
            $currentNo = $invoice->invoice_no;

            // Matches modern pattern layout: ST/YY-YY/Count
            if (preg_match('/^ST\/(\d{2}-\d{2})\/(\d+)$/', $currentNo, $matches)) {
                $session = $matches[1];
                $sequenceNum = (int)$matches[2];

                if (!isset($sessionSequences[$session])) {
                    $sessionSequences[$session] = 100; // Default safety baseline fallback
                }
                if ($sequenceNum > $sessionSequences[$session]) {
                    $sessionSequences[$session] = $sequenceNum;
                }
            }
        }

        // Step 2: Build transformation preview mapping array
        foreach ($invoices as $invoice) {
            $date = Carbon::parse($invoice->invoice_date);
            $year = $date->year;

            // Calculate Fiscal April-March Session
            if ($date->month >= 4) {
                $sessionString = sprintf('%s-%s', substr($year, -2), substr($year + 1, -2));
            } else {
                $sessionString = sprintf('%s-%s', substr($year - 1, -2), substr($year, -2));
            }

            $isOldPattern = !preg_match('/^ST\/\d{2}-\d{2}\/\d+$/', $invoice->invoice_no);

            if ($isOldPattern) {
                // Initialize session at baseline index if not encountered yet
                if (!isset($sessionSequences[$sessionString])) {
                    $sessionSequences[$sessionString] = 0;
                }

                $sessionSequences[$sessionString]++;
                $allocatedSequence = $sessionSequences[$sessionString];

                // Format with padded left-zeros (e.g., 001, 045, 102)
                $previewNo = sprintf('ST/%s/%03d', $sessionString, $allocatedSequence);
            } else {
                $previewNo = $invoice->invoice_no;
            }

            $processed[] = [
                'id' => $invoice->invoice_id,
                'current_no' => $invoice->invoice_no,
                'preview_no' => $previewNo,
                'invoice_date' => $date->format('Y-m-d'),
                'total_amount' => $invoice->total_amount,
                'name' => $invoice->name ?? 'N/A',
                'is_old_pattern' => $isOldPattern
            ];
        }

        return $processed;
    }
}
