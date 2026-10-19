<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        // Get all sale invoices with their items and inventory
        $saleInvoices = Invoice::with(['items.inventory'])
            ->where('invoice_type', 'sale')
            ->get();

        // Get all purchase invoices with their items and inventory
        $purchaseInvoices = Invoice::with(['items.inventory'])
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
        $totalDue = Invoice::where('invoice_type', 'sale')
            ->sum('due_amount');

        // Inventory value (current stock)
        //$inventoryValue = Inventory::sum(\DB::raw('stock * purchase_rate'));
        $inventoryValue = round(
            Inventory::sum(\DB::raw('stock * purchase_rate * (1 + gst_per / 100)')),
            2
        );

        $inventoryItems = Inventory::sum('stock');

        // Monthly data for charts (last 6 months)
        $months = [];
        $monthlySales = [];
        $monthlyProfit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $monthSaleInvoices = Invoice::with(['items.inventory'])
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

        return view('admin.dashboard', compact(
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

    public function distributors()
    {
        $distributors = User::with('shop')
            ->where('role', 'distributor')
            ->latest()
            ->paginate(10);

        return view('admin.shops', compact('distributors'));
    }

    public function storeDistributor(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'mobile' => 'required|string|max:20',
                'password' => 'required|string|min:8',
                'shop_name' => 'required|string|max:255',
                'shop_gstin' => 'required|string|max:255',
                'shop_address' => 'required|string',
                'shop_pin_code' => 'required|string|max:10',
                'shop_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            DB::transaction(function() use ($validated, $request) {
                $distributor = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'distributor'
                ]);

                $shopData = [
                    'shop_name' => $validated['shop_name'],
                    'shop_gstin' => $validated['shop_gstin'],
                    'shop_address' => $validated['shop_address'],
                    'shop_pin_code' => $validated['shop_pin_code'],
                    'shop_location' => $request->input('shop_location', ''),
                ];

                if ($request->hasFile('shop_photo')) {
                    $shopData['shop_photo'] = $this->uploadShopPhoto(
                        $request->file('shop_photo'),
                        Str::slug($validated['shop_name'])
                    );
                }

                $distributor->shop()->create($shopData);
            });

            return redirect()->back()->with('success', 'Distributor and shop added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add distributor: ' . $e->getMessage());
        }
    }

    public function updateDistributor(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$user->user_id.',user_id',
                'mobile' => 'required|string|max:20|unique:users,mobile,'.$user->user_id.',user_id',
                'password' => 'nullable|string|min:8',
                'shop_name' => 'required|string|max:255',
                'shop_gstin' => 'required|string|max:255',
                'shop_address' => 'required|string',
                'shop_pin_code' => 'required|string|max:10',
                'shop_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'remove_photo' => 'nullable|boolean',
            ]);

            DB::transaction(function() use ($user, $validated, $request) {
                $updateData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'],
                ];

                if (!empty($validated['password'])) {
                    $updateData['password'] = Hash::make($validated['password']);
                }

                $user->update($updateData);

                $shop = $user->shop;
                $shopData = [
                    'shop_name' => $validated['shop_name'],
                    'shop_gstin' => $validated['shop_gstin'],
                    'shop_address' => $validated['shop_address'],
                    'shop_pin_code' => $validated['shop_pin_code'],
                    'shop_location' => $request->input('shop_location', ''),
                ];

                // Handle photo removal or update
                if ($request->has('remove_photo') && $request->input('remove_photo')) {
                    // Delete existing photo if requested
                    if ($shop->shop_photo && Storage::disk('public_html')->exists($shop->shop_photo)) {
                        Storage::disk('public_html')->delete($shop->shop_photo);
                    }
                    $shopData['shop_photo'] = null;
                } elseif ($request->hasFile('shop_photo')) {
                    // Delete old photo if exists
                    if ($shop->shop_photo && Storage::disk('public_html')->exists($shop->shop_photo)) {
                        Storage::disk('public_html')->delete($shop->shop_photo);
                    }
                    // Upload new photo
                    $shopData['shop_photo'] = $this->uploadShopPhoto(
                        $request->file('shop_photo'),
                        Str::slug($validated['shop_name'])
                    );
                }

                $user->shop()->updateOrCreate(
                    ['user_id' => $user->user_id],
                    $shopData
                );
            });

            return redirect()->back()->with('success', 'Distributor and shop updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update distributor: ' . $e->getMessage());
        }
    }

    public function destroyDistributor(User $user)
    {
        try {
            DB::transaction(function() use ($user) {
                // Delete shop photo if exists
                $shop = $user->shop;
                if ($shop && $shop->shop_photo && Storage::disk('public_html')->exists($shop->shop_photo)) {
                    Storage::disk('public_html')->delete($shop->shop_photo);
                }

                $user->shop()->delete();
                $user->delete();
            });

            return redirect()->back()->with('success', 'Distributor and shop deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete distributor: ' . $e->getMessage());
        }
    }

    protected function uploadShopPhoto($file, $slug)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $slug . '-' . $randomString . '.' . $extension;
            return $file->storeAs('Shop', $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Shop photo upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
