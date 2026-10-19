<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DistributorController extends Controller
{
    public function index()
    {
        // Get the distributor ID (assuming authenticated user is a distributor)
        $distributorId = auth()->id();

        // Get all sale invoices with their items and inventory
        $saleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->get();

        // Get all purchase invoices with their items and inventory
        $purchaseInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'purchase')
            ->get();

        // Calculate total sales
        $totalSales = $saleInvoices->sum('total_amount');
        $totalSaleInvoices = $saleInvoices->count();

        // Calculate total purchases
        $totalPurchases = $purchaseInvoices->sum('total_amount');

        // Calculate profit by comparing sale price with purchase cost for each item
        $totalProfit = 0;
        $profitDetails = [];

        foreach ($saleInvoices as $invoice) {
            $invoiceProfit = 0;

            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    // Calculate profit for this item: (sale price - purchase cost) * quantity
                    $itemProfit = ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                    $invoiceProfit += $itemProfit;
                }
            }

            // Add to total profit
            $totalProfit += $invoiceProfit;

            // Store profit for this invoice
            $invoice->profit = $invoiceProfit;
            $profitDetails[$invoice->invoice_id] = $invoiceProfit;
        }

        // Calculate profit margin
        $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        // Today's sales and profit
        $todaySaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereDate('invoice_date', today())
            ->get();

        $todaySales = $todaySaleInvoices->sum('total_amount');
        $totalTodaySaleInvoices = $todaySaleInvoices->count();
        $todayProfit = 0;

        foreach ($todaySaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $todayProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Last month sales and profit
        $lastMonthSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->get();

        $lastMonthSales = $lastMonthSaleInvoices->sum('total_amount');
        $totalLastMonthSaleInvoices = $lastMonthSaleInvoices->count();
        $lastMonthProfit = 0;

        foreach ($lastMonthSaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $lastMonthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Last month profit margin
        $lastMonthProfitMargin = $lastMonthSales > 0 ? ($lastMonthProfit / $lastMonthSales) * 100 : 0;

        // Previous month for growth calculations
        $prevMonthSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereMonth('invoice_date', now()->subMonths(2)->month)
            ->whereYear('invoice_date', now()->subMonths(2)->year)
            ->get();

        $prevMonthSales = $prevMonthSaleInvoices->sum('total_amount');
        $prevMonthProfit = 0;

        foreach ($prevMonthSaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $prevMonthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Growth calculations
        $salesGrowth = $prevMonthSales > 0 ? (($lastMonthSales - $prevMonthSales) / $prevMonthSales) * 100 : ($lastMonthSales > 0 ? 100 : 0);
        $profitGrowth = $prevMonthProfit != 0 ? (($lastMonthProfit - $prevMonthProfit) / abs($prevMonthProfit)) * 100 : ($lastMonthProfit != 0 ? ($lastMonthProfit > 0 ? 100 : -100) : 0);

        // Total due amount
        $totalDue = Invoice::where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->sum('due_amount');

        // Inventory value (current stock)
        // $inventoryValue = Inventory::where('user_id', $distributorId)
        //     ->sum(\DB::raw('stock * purchase_rate'));

        $inventoryValue = round(
            Inventory::where('user_id', $distributorId)->sum(
                \DB::raw('stock * purchase_rate * (1 + gst_per / 100)')
            ),
            2
        );

        $inventoryItems = Inventory::where('user_id', $distributorId)->sum('stock');

        // Monthly data for charts (last 6 months)
        $months = [];
        $monthlySales = [];
        $monthlyProfit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $monthSaleInvoices = Invoice::with(['items.inventory'])
                ->where('distributor_id', $distributorId)
                ->where('invoice_type', 'sale')
                ->whereMonth('invoice_date', $month->month)
                ->whereYear('invoice_date', $month->year)
                ->get();

            $monthSales = $monthSaleInvoices->sum('total_amount');
            $monthProfit = 0;

            foreach ($monthSaleInvoices as $invoice) {
                foreach ($invoice->items as $item) {
                    if ($item->inventory) {
                        $monthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                    }
                }
            }

            $monthlySales[] = $monthSales;
            $monthlyProfit[] = $monthProfit;
        }

        // Recent sale invoices with profit
        $recentSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->orderBy('invoice_date', 'desc')
            ->take(10)
            ->get();

        // Add profit to each recent invoice
        foreach ($recentSaleInvoices as $invoice) {
            $invoiceProfit = 0;
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $invoiceProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
            $invoice->profit = $invoiceProfit;
        }

        return view('distributor.dashboard', compact(
            'totalSales',
            'totalSaleInvoices',
            'totalProfit',
            'profitMargin',
            'todaySales',
            'todaySaleInvoices',
            'totalTodaySaleInvoices',
            'todayProfit',
            'lastMonthSales',
            'lastMonthSaleInvoices',
            'totalLastMonthSaleInvoices',
            'lastMonthProfit',
            'lastMonthProfitMargin',
            'salesGrowth',
            'profitGrowth',
            'totalDue',
            'inventoryValue',
            'inventoryItems',
            'months',
            'monthlySales',
            'monthlyProfit',
            'recentSaleInvoices'
        ));
    }

    public function index2()
    {
        // Get the distributor ID (assuming authenticated user is a distributor)
        $distributorId = auth()->id();

        // Get all sale invoices with their items and inventory
        $saleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->get();

        // Get all purchase invoices with their items and inventory
        $purchaseInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'purchase')
            ->get();

        // Calculate total sales
        $totalSales = $saleInvoices->sum('total_amount');
        $totalSaleInvoices = $saleInvoices->count();

        // Calculate total purchases
        $totalPurchases = $purchaseInvoices->sum('total_amount');

        // Calculate profit by comparing sale price with purchase cost for each item
        $totalProfit = 0;
        $profitDetails = [];

        foreach ($saleInvoices as $invoice) {
            $invoiceProfit = 0;

            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    // Calculate profit for this item: (sale price - purchase cost) * quantity
                    $itemProfit = ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                    $invoiceProfit += $itemProfit;
                }
            }

            // Add to total profit
            $totalProfit += $invoiceProfit;

            // Store profit for this invoice
            $invoice->profit = $invoiceProfit;
            $profitDetails[$invoice->invoice_id] = $invoiceProfit;
        }

        // Calculate profit margin
        $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        // Today's sales and profit
        $todaySaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereDate('invoice_date', today())
            ->get();

        $todaySales = $todaySaleInvoices->sum('total_amount');
        $totalTodaySaleInvoices = $todaySaleInvoices->count();
        $todayProfit = 0;

        foreach ($todaySaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $todayProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Last month sales and profit
        $lastMonthSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->get();

        $lastMonthSales = $lastMonthSaleInvoices->sum('total_amount');
        $totalLastMonthSaleInvoices = $lastMonthSaleInvoices->count();
        $lastMonthProfit = 0;

        foreach ($lastMonthSaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $lastMonthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Last month profit margin
        $lastMonthProfitMargin = $lastMonthSales > 0 ? ($lastMonthProfit / $lastMonthSales) * 100 : 0;

        // Previous month for growth calculations
        $prevMonthSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->whereMonth('invoice_date', now()->subMonths(2)->month)
            ->whereYear('invoice_date', now()->subMonths(2)->year)
            ->get();

        $prevMonthSales = $prevMonthSaleInvoices->sum('total_amount');
        $prevMonthProfit = 0;

        foreach ($prevMonthSaleInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $prevMonthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
        }

        // Growth calculations
        $salesGrowth = $prevMonthSales > 0 ? (($lastMonthSales - $prevMonthSales) / $prevMonthSales) * 100 : ($lastMonthSales > 0 ? 100 : 0);
        $profitGrowth = $prevMonthProfit != 0 ? (($lastMonthProfit - $prevMonthProfit) / abs($prevMonthProfit)) * 100 : ($lastMonthProfit != 0 ? ($lastMonthProfit > 0 ? 100 : -100) : 0);

        // Total due amount
        $totalDue = Invoice::where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->sum('due_amount');

        // Inventory value (current stock)
        // $inventoryValue = Inventory::where('user_id', $distributorId)
        //     ->sum(\DB::raw('stock * purchase_rate'));

        $inventoryValue = round(
            Inventory::where('user_id', $distributorId)->sum(
                \DB::raw('stock * purchase_rate * (1 + gst_per / 100)')
            ),
            2
        );

        $inventoryItems = Inventory::where('user_id', $distributorId)->sum('stock');

        // Monthly data for charts (last 6 months)
        $months = [];
        $monthlySales = [];
        $monthlyProfit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $monthSaleInvoices = Invoice::with(['items.inventory'])
                ->where('distributor_id', $distributorId)
                ->where('invoice_type', 'sale')
                ->whereMonth('invoice_date', $month->month)
                ->whereYear('invoice_date', $month->year)
                ->get();

            $monthSales = $monthSaleInvoices->sum('total_amount');
            $monthProfit = 0;

            foreach ($monthSaleInvoices as $invoice) {
                foreach ($invoice->items as $item) {
                    if ($item->inventory) {
                        $monthProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                    }
                }
            }

            $monthlySales[] = $monthSales;
            $monthlyProfit[] = $monthProfit;
        }

        // Recent sale invoices with profit
        $recentSaleInvoices = Invoice::with(['items.inventory'])
            ->where('distributor_id', $distributorId)
            ->where('invoice_type', 'sale')
            ->orderBy('invoice_date', 'desc')
            ->take(10)
            ->get();

        // Add profit to each recent invoice
        foreach ($recentSaleInvoices as $invoice) {
            $invoiceProfit = 0;
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $invoiceProfit += ($item->rate - $item->inventory->purchase_rate) * $item->qty;
                }
            }
            $invoice->profit = $invoiceProfit;
        }

        return view('distributor.dashboard2', compact(
            'totalSales',
            'totalSaleInvoices',
            'totalProfit',
            'profitMargin',
            'todaySales',
            'todaySaleInvoices',
            'totalTodaySaleInvoices',
            'todayProfit',
            'lastMonthSales',
            'lastMonthSaleInvoices',
            'totalLastMonthSaleInvoices',
            'lastMonthProfit',
            'lastMonthProfitMargin',
            'salesGrowth',
            'profitGrowth',
            'totalDue',
            'inventoryValue',
            'inventoryItems',
            'months',
            'monthlySales',
            'monthlyProfit',
            'recentSaleInvoices'
        ));
    }

    public function inventory()
    {
        $inventories = Inventory::where('user_id', auth()->id())
            ->with('product.category')
            ->get();

        $categorySummary = [];

        foreach ($inventories as $inv) {

            // stock value
            $value = $inv->stock * $inv->purchase_rate * (1 + $inv->gst_per / 100);

            // detect category
            if (!$inv->product || !$inv->product->category) {
                $categoryName = 'Others';
            } else {
                $categoryName = $inv->product->category->name;
            }

            // create bucket if not exists
            if (!isset($categorySummary[$categoryName])) {
                $categorySummary[$categoryName] = [
                    'stock' => 0,
                    'value' => 0
                ];
            }

            // add totals
            $categorySummary[$categoryName]['stock'] += $inv->stock;
            $categorySummary[$categoryName]['value'] += $value;
        }

        return view('distributor.invcat', compact('categorySummary'));
    }

    public function parties()
    {
        $parties = Party::where('user_id', Auth::id())
            ->withCount([
                'purchaseInvoices as purchase_invoices_count',
                'saleInvoices as sale_invoices_count'
            ])
            ->withSum('purchaseInvoices as purchase_amount', 'total_amount')
            ->withSum('saleInvoices as sale_amount', 'total_amount')
            ->latest()
            ->get();

        $total = $parties->count();

        return view('distributor.parties', compact('parties', 'total'));
    }

    public function partyStore(Request $request)
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

        return redirect()->route('distributor.parties')
            ->with('success', 'Party created successfully');
    }

    public function partyUpdate(Request $request, Party $party)
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

        return redirect()->route('distributor.parties')
            ->with('success', 'Party updated successfully');
    }

    public function partyDestroy(Party $party)
    {
        // Verify the party belongs to the current user
        if ($party->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if party has any invoices before deleting
        if ($party->purchaseInvoices()->count() > 0 || $party->saleInvoices()->count() > 0) {
            return redirect()->route('distributor.parties')
                ->with('error', 'Cannot delete party with associated invoices');
        }

        $party->delete();

        return redirect()->route('distributor.parties')
            ->with('success', 'Party deleted successfully');
    }


    public function products(Request $request)
    {
        // Query for products with relationships
        $productQuery = Product::with([
            'brand',
            'category',
            'productAttributes.attribute',
            'productAttributes.attributeValue',
            'inventories' => function ($query) {
                $query->where('user_id', auth()->id());
            }
        ])->orderBy('created_at', 'desc');

        // Search filter for products
        if ($request->has('search')) {
            $search = $request->search;
            $productQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Stock status filter
        if ($request->has('stock')) {
            if ($request->stock === 'out') {
                // Out of stock products
                $productQuery->whereDoesntHave('inventories', function ($q) {
                    $q->where('user_id', auth()->id())
                        ->where('stock', '>', 0);
                });
            } elseif ($request->stock === 'in') {
                // In stock products (stock > 0)
                $productQuery->whereHas('inventories', function ($q) {
                    $q->where('user_id', auth()->id())
                        ->where('stock', '>', 0);
                });
            }
        }

        // Paginate the results
        $products = $productQuery->paginate(20);

        return view('distributor.products', compact('products'));
    }
}
