<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Party;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function inventories(Request $request)
    {
        // Query for inventory items
        $inventoryQuery = Inventory::with(['product.category', 'product.brand'])
            ->where('user_id', auth()->id());

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $inventoryQuery->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('imei_one', 'like', "%{$search}%")
                ->orWhere('imei_two', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            });
        }

        // Apply stock filters
        if ($request->has('stock')) {
            if ($request->stock === 'out') {
                $inventoryQuery->where('stock', '<=', 0);
            } elseif ($request->stock === 'low') {
                $inventoryQuery->where('stock', '>', 0)
                            ->where('stock', '<=', 10);
            } elseif ($request->stock === 'in') {
                $inventoryQuery->where('stock', '>', 0);
            }
        }

        // Calculate totals on un-ordered base query (fixes MySQL strict mode error)
        $totals = (clone $inventoryQuery)
            ->reorder()
            ->selectRaw('SUM(stock) as total_stock, ROUND(SUM(stock * purchase_rate * (1 + gst_per / 100)), 2) as total_value')
            ->first();

        $totalStock = $totals->total_stock ?? 0;
        $totalValue = $totals->total_value ?? 0;

        // Apply ordering and paginate results
        $inventories = $inventoryQuery->orderBy('created_at', 'desc')->paginate(50);

        return view('distributor.inventories', compact('inventories', 'totalStock', 'totalValue'));
    }

    public function makeInventoryOffline(Request $request, $inventory)
    {
        $inventory = Inventory::where('inventory_id', $inventory)
            ->where('user_id', auth()->id())
            ->first();

        if (!$inventory) {
            return redirect()
                ->back()
                ->with('error', 'Inventory item not found.');
        }

        // Already offline
        if (is_null($inventory->product_id)) {
            return redirect()
                ->back()
                ->with('error', 'This inventory item is already offline.');
        }

        // Remove product association
        $inventory->product_id = null;
        $inventory->save();

        return redirect()
            ->back()
            ->with('success', 'Inventory item has been made offline successfully.');
    }

    public function inventories2(Request $request)
    {
        $inventories = Inventory::with(['product.category', 'product.brand'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $transformedData = $inventories->map(function ($inventory) {
            $mrp = $inventory->sell_rate
                ? $inventory->sell_rate * 1.20
                : $inventory->purchase_rate * 1.20;

            $minSellingPrice = $inventory->sell_rate
                ? (($inventory->purchase_rate + $inventory->sell_rate) / 2)
                : $inventory->purchase_rate * 1.10;

            return [
                'inventory_id' => $inventory->inventory_id,
                'product_name' => $inventory->product_name,
                'bar_one' => $inventory->imei_one,
                'bar_two' => $inventory->imei_two,
                'bar_three' => $inventory->serial_number,
                'stock' => $inventory->stock,
                'purchase_rate' => (float) $inventory->purchase_rate,
                'sell_rate' => (float) $inventory->sell_rate,
                'mrp' => round($mrp, 2),
                'min_selling_price' => round($minSellingPrice, 2)
            ];
        });

        return response()->json([
            'data' => $transformedData,
            'count' => $inventories->count(),
            'total_stock' => $inventories->sum('stock'),
            'total_value' => round($inventories->sum(fn($item) => $item->stock * $item->purchase_rate), 2)
        ]);
    }

    protected $invoiceTypes = [
        'purchase' => [
            'prefix' => 'PUR',
            'route' => 'distributor.invoices.purchase',
            'view' => 'distributor.purchase'
        ],
        'sale' => [
            'prefix' => 'SALE',
            'route' => 'distributor.invoices.sale',
            'view' => 'distributor.sale'
        ]
    ];

    /**
     * Highly Optimized Sequence Generator using SQL string extraction
     */
    protected function generateNextInvoiceNumber($type)
    {
        $user_id = Auth::id();
        $gstEnabledDistributors = [3];

        // --- STRATEGY A: COMPLIANT GST PATTERN SYSTEM ---
        if (in_array($user_id, $gstEnabledDistributors) && strtolower($type) === 'sale') {
            $date = now();
            $year = $date->year;

            $sessionString = ($date->month >= 4)
                ? sprintf('%s-%s', substr($year, -2), substr($year + 1, -2))
                : sprintf('%s-%s', substr($year - 1, -2), substr($year, -2));

            $gstPrefix = "ST/" . $sessionString . "/";
            $prefixLength = strlen($gstPrefix) + 1;

            $maxNumber = Invoice::withTrashed()
                ->where('distributor_id', $user_id)
                ->where('invoice_no', 'like', $gstPrefix . '%')
                ->selectRaw("MAX(CAST(SUBSTRING(invoice_no, {$prefixLength}) AS UNSIGNED)) as max_seq")
                ->value('max_seq');

            $nextSequence = $maxNumber ? ((int) $maxNumber) + 1 : 1;

            return $gstPrefix . sprintf('%03d', $nextSequence);
        }

        // --- STRATEGY B: LEGACY PATTERN SYSTEM ---
        $prefix = $this->invoiceTypes[$type]['prefix'] . '-' . $user_id;
        $prefixLength = strlen($prefix) + 1;

        $maxNumber = Invoice::withTrashed()
            ->where('invoice_type', $type)
            ->where('distributor_id', $user_id)
            ->where('invoice_no', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(invoice_no, {$prefixLength}) AS UNSIGNED)) as max_seq")
            ->value('max_seq');

        $nextNumber = $maxNumber ? ((int) $maxNumber) + 1 : 1001;

        return $prefix . $nextNumber;
    }

    /**
     * Lightweight Common Data Fetcher
     */
    protected function getCommonViewData()
    {
        $user_id = Auth::id();

        return [
            'parties' => Party::where('user_id', $user_id)
                ->get(['party_id', 'name', 'phone', 'address']),
            'customers' => Customer::latest()
                ->limit(100)
                ->get(['customer_id', 'name', 'mobile', 'address']),
            'products' => Product::with([
                'brand:brand_id,name',
                'category:category_id,name',
                'productAttributes.attribute',
                'productAttributes.attributeValue'
            ])->orderBy('created_at', 'desc'),
            'shop' => Auth::user()->shop
        ];
    }

    public function create()
    {
        $user_id = Auth::id();

        return view($this->invoiceTypes['purchase']['view'], [
            'nextInvoiceNumber' => $this->generateNextInvoiceNumber('purchase'),
            'parties' => Party::where('user_id', $user_id)->get(['party_id', 'name', 'phone', 'address']),
            'customers' => collect([]), // Prevents Blade undefined variable errors cleanly
            'products' => Product::where('is_active', true)->limit(50),
        ]);
    }

    public function store(Request $request)
    {
        return $this->processInvoiceRequest($request, 'purchase');
    }

    public function sale()
    {
        $user_id = Auth::id();

        return view($this->invoiceTypes['sale']['view'], [
            'nextInvoiceNumber' => $this->generateNextInvoiceNumber('sale'),
            'parties' => Party::where('user_id', $user_id)->get(['party_id', 'name', 'phone', 'address']),
            'employees' => Employee::where('is_active', true)
                ->where('distributor_id', $user_id)
                ->get(['employee_id', 'name']),
            'customers' => collect([]), // Prevents Blade undefined variable errors cleanly
            'products' => Product::where('is_active', true)->limit(50), // Fallback empty/limited query
        ]);
    }

    public function storeSale(Request $request)
    {
        return $this->processInvoiceRequest($request, 'sale');
    }

    protected function processInvoiceRequest(Request $request, $type)
    {
        $validationRules = $this->getValidationRules($type);
        $validated = $request->validate($validationRules);

        DB::beginTransaction();

        try {
            $invoiceData = $this->prepareInvoiceData($request, $type);
            $invoice = Invoice::create($invoiceData);

            $this->processInvoiceItems($request, $invoice, $type);

            DB::commit();

            return redirect()->route($this->invoiceTypes[$type]['route'])
                ->with('success', ucfirst($type) . ' invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(ucfirst($type) . ' invoice creation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    protected function getValidationRules($type)
    {
        $commonRules = [
            'invoice_no' => 'required|string|unique:invoices,invoice_no',
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|in:party,user,customer',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'party_id' => 'nullable|exists:parties,party_id',
            'employee_id' => 'nullable|exists:employees,employee_id',
            'payment_method' => 'required|in:cash,upi,card,cheque,net banking,credit',
            'subtotal' => 'required|numeric|min:0',
            'total_discount' => 'required|numeric|min:0',
            'total_tax' => 'required|numeric|min:0',
            'shipping_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'document' => 'nullable|image|max:2048',
            'items' => 'required|array|min:1',
        ];

        if ($type === 'purchase') {
            $specificRules = [
                'supplier_name' => 'nullable|string|max:255',
                'supplier_phone' => 'required|string|max:20',
                'supplier_address' => 'required|string',
                'items.*.product_id' => 'nullable|exists:products,product_id',
                'items.*.product_name' => 'required|string',
                'items.*.condition' => 'required|in:new,used,unused,refurbished,open box',
                'items.*.quality' => 'required|in:fair,good,superb,super mint,sealed pack',
                'items.*.warranty' => 'nullable|string',
                'items.*.activation_date' => 'nullable|date',
                'items.*.battery_health' => 'nullable|integer|min:0|max:100',
                'items.*.color' => 'nullable|string',
                'items.*.imei_one' => 'nullable|string',
                'items.*.imei_two' => 'nullable|string',
                'items.*.serial_number' => 'nullable|string',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.unit' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.sell_rate' => 'required|numeric|min:0',
                'items.*.discount_percent' => 'required|numeric|min:0|max:100',
                'items.*.discount_amount' => 'required|numeric|min:0',
                'items.*.tax_percent' => 'required|numeric|min:0|max:100',
                'items.*.tax_amount' => 'required|numeric|min:0',
                'items.*.amount' => 'required|numeric|min:0',
            ];
        } else {
            $specificRules = [
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_address' => 'required|string',
                'items.*.inventory_id' => 'required|exists:inventories,inventory_id',
                'items.*.product_name' => 'required|string',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.discount_percent' => 'required|numeric|min:0|max:100',
                'items.*.discount_amount' => 'required|numeric|min:0',
                'items.*.tax_percent' => 'required|numeric|min:0|max:100',
                'items.*.tax_amount' => 'required|numeric|min:0',
                'items.*.amount' => 'required|numeric|min:0',
            ];
        }

        return array_merge($commonRules, $specificRules);
    }

    protected function prepareInvoiceData(Request $request, $type)
    {
        $customerId = $this->handleCustomerData($request);
        $name = $this->getInvoiceName($request, $customerId);

        $photoPath = $this->handleFileUpload($request, 'photo');
        $docPath = $this->handleFileUpload($request, 'document');

        return [
            'distributor_id' => auth()->id(),
            'user_id' => null,
            'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
            'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
            'employee_id' => $request->employee_id,
            'agreement_id' => null,
            'invoice_no' => $request->invoice_no,
            'invoice_url' => $this->generateUniqueInvoiceUrl(),
            'invoice_type' => $type,
            'invoice_for' => $request->invoice_for,
            'name' => $name,
            'mobile' => $type === 'purchase' ? $request->supplier_phone : $request->customer_phone,
            'address' => $type === 'purchase' ? $request->supplier_address : $request->customer_address,
            'subtotal' => $request->subtotal,
            'total_discount' => $request->total_discount,
            'total_tax' => $request->total_tax,
            'shipping_amount' => $request->shipping_amount,
            'total_amount' => $request->total_amount,
            'paid' => $request->paid,
            'due_amount' => $request->due_amount,
            'payment_type' => $request->payment_method,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->invoice_date,
            'notes' => $request->notes,
            'photo_url' => $photoPath,
            'doc_url' => $docPath,
        ];
    }

    protected function handleCustomerData(Request $request)
    {
        if ($request->invoice_for !== 'customer') {
            return null;
        }

        $customerId = $request->customer_id;
        $nameField = $request->has('supplier_name') ? 'supplier_name' : 'customer_name';
        $phoneField = $request->has('supplier_phone') ? 'supplier_phone' : 'customer_phone';
        $addressField = $request->has('supplier_address') ? 'supplier_address' : 'customer_address';

        $name = $request->$nameField;
        $mobile = $request->$phoneField;
        $address = $request->$addressField;

        if (!empty($customerId)) {
            $customer = Customer::find($customerId);
            if ($customer) {
                if ($customer->mobile !== $mobile) {
                    $conflict = Customer::where('mobile', $mobile)
                        ->where('customer_id', '!=', $customerId)
                        ->exists();

                    if ($conflict) {
                        throw new \Exception("The mobile number ({$mobile}) is registered with another customer!");
                    }
                }

                $customer->update([
                    'name' => $name,
                    'mobile' => $mobile,
                    'address' => $address,
                ]);
            }
            return $customerId;
        }

        $existingCustomer = Customer::where('mobile', $mobile)->first();
        if ($existingCustomer) {
            $existingCustomer->update([
                'name' => $name,
                'address' => $address,
            ]);
            return $existingCustomer->customer_id;
        }

        $newCustomer = Customer::create([
            'name' => $name,
            'mobile' => $mobile,
            'address' => $address,
        ]);

        return $newCustomer->customer_id;
    }

    protected function getInvoiceName(Request $request, $customerId)
    {
        if ($request->invoice_for === 'party') {
            $party = Party::find($request->party_id);
            return $party ? $party->name : null;
        } elseif ($request->invoice_for === 'customer') {
            return $request->supplier_name ?? $request->customer_name;
        }
        return null;
    }

    protected function processInvoiceItems(Request $request, $invoice, $type)
    {
        foreach ($request->items as $item) {
            if ($type === 'purchase') {
                $this->processPurchaseItem($item, $invoice);
            } else {
                $this->processSaleItem($item, $invoice);
            }
        }
    }

    protected function processPurchaseItem($item, $invoice)
    {
        $inventory = Inventory::create([
            'user_id' => auth()->id(),
            'shop_id' => Auth::user()->shop->shop_id,
            'product_id' => $item['product_id'] ?? null,
            'product_name' => $item['product_name'],
            'product_type' => $item['condition'],
            'health' => $item['quality'],
            'age' => $this->calculateItemAge($item['warranty'] ?? null, $item['activation_date'] ?? null),
            'warranty' => !empty($item['activation_date']) ? null : ($item['warranty'] ?? null),
            'activation_date' => !empty($item['activation_date']) ? $item['activation_date'] : null,
            'battery_health' => isset($item['battery_health']) && $item['battery_health'] !== '' ? $item['battery_health'] : null,
            'color' => $item['color'],
            'imei_one' => $item['imei_one'],
            'imei_two' => $item['imei_two'],
            'serial_number' => $item['serial_number'],
            'unit' => $item['unit'],
            'stock' => $item['qty'],
            'purchase_rate' => $item['price'],
            'gst_per' => $item['tax_percent'] ?? 0,
            'sell_rate' => $item['sell_rate'],
            'notes' => $invoice->notes,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'inventory_id' => $inventory->inventory_id,
            'qty' => $item['qty'],
            'rate' => $item['price'],
            'discount_percent' => $item['discount_percent'],
            'discount' => $item['discount_amount'],
            'tax_percent' => $item['tax_percent'],
            'tax' => $item['tax_amount'],
            'amount' => $item['amount'],
        ]);
    }

    /**
     * Race-Condition Proof Atomic Stock Reduction
     */
    protected function processSaleItem($item, $invoice)
    {
        // Pessimistic Row Lock
        $inventory = Inventory::where('inventory_id', $item['inventory_id'])
            ->lockForUpdate()
            ->first();

        if (!$inventory) {
            throw new \Exception("Inventory item not found: " . $item['inventory_id']);
        }

        if ($inventory->stock < $item['qty']) {
            throw new \Exception("Insufficient stock for product: " . $item['product_name']);
        }

        // Atomic Decrement
        $inventory->decrement('stock', $item['qty']);

        InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'inventory_id' => $inventory->inventory_id,
            'product_id' => $inventory->product_id,
            'qty' => $item['qty'],
            'rate' => $item['price'],
            'discount_percent' => $item['discount_percent'],
            'discount' => $item['discount_amount'],
            'tax_percent' => $item['tax_percent'],
            'tax' => $item['tax_amount'],
            'amount' => $item['amount'],
        ]);
    }

    public function editPurchase($id)
    {
        $user_id = Auth::id();
        $invoice = $this->getInvoiceForEditing($id, 'purchase');

        return view('distributor.purchase-edit', [
            'invoice'  => $invoice,
            'parties'  => Party::where('user_id', $user_id)->get(['party_id', 'name', 'phone', 'address']),
            'products' => collect([]), // Prevents Blade undefined variable errors cleanly
        ]);
    }

    public function editSale($id)
    {
        $user_id = Auth::id();
        $invoice = $this->getInvoiceForEditing($id, 'sale');

        return view('distributor.sale-edit', [
            'invoice'   => $invoice,
            'parties'   => Party::where('user_id', $user_id)->get(['party_id', 'name', 'phone', 'address']),
            'employees' => Employee::where('is_active', true)
                ->where('distributor_id', $user_id)
                ->get(['employee_id', 'name']),
        ]);
    }

    protected function getInvoiceForEditing($id, $type)
    {
        return Invoice::with(['items.inventory', 'party', 'customer'])
            ->where('invoice_id', $id)
            ->where('invoice_type', $type)
            ->where('distributor_id', Auth::id())
            ->firstOrFail();
    }

    public function updatePurchase(Request $request, $id)
    {
        return $this->updateInvoice($request, $id, 'purchase');
    }

    public function updateSale(Request $request, $id)
    {
        return $this->updateInvoice($request, $id, 'sale');
    }

    protected function updateInvoice(Request $request, $id, $type)
    {
        $invoice = $this->getInvoiceForEditing($id, $type);

        $validationRules = $this->getValidationRules($type);
        $validationRules['invoice_no'] = [
            'required',
            'string',
            Rule::unique('invoices', 'invoice_no')->ignore($invoice->invoice_id, 'invoice_id')
        ];

        $validated = $request->validate($validationRules);

        DB::beginTransaction();

        try {
            $this->updateInvoiceData($request, $invoice, $type);
            $this->updateInvoiceItems($request, $invoice, $type);

            DB::commit();

            return redirect()->route($this->invoiceTypes[$type]['route'])
                ->with('success', ucfirst($type) . ' invoice updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(ucfirst($type) . ' invoice update failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    protected function updateInvoiceData(Request $request, $invoice, $type)
    {
        $customerId = $this->handleCustomerData($request);
        $name = $this->getInvoiceName($request, $customerId);

        $photoPath = $this->handleFileUpload($request, 'photo', $invoice->photo_url);
        $docPath = $this->handleFileUpload($request, 'document', $invoice->doc_url);

        $invoice->update([
            'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
            'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
            'employee_id' => $request->employee_id,
            'invoice_for' => $request->invoice_for,
            'name' => $name,
            'mobile' => $request->supplier_phone ?? $request->customer_phone,
            'address' => $request->supplier_address ?? $request->customer_address,
            'subtotal' => $request->subtotal,
            'total_discount' => $request->total_discount,
            'total_tax' => $request->total_tax,
            'shipping_amount' => $request->shipping_amount,
            'total_amount' => $request->total_amount,
            'paid' => $request->paid,
            'due_amount' => $request->due_amount,
            'payment_type' => $request->payment_method,
            'invoice_date' => $request->invoice_date,
            'notes' => $request->notes,
            'photo_url' => $photoPath,
            'doc_url' => $docPath,
        ]);
    }

    protected function updateInvoiceItems(Request $request, $invoice, $type)
    {
        if ($type === 'purchase') {
            $this->updatePurchaseItems($request, $invoice);
        } else {
            $this->updateSaleItems($request, $invoice);
        }
    }

    protected function updatePurchaseItems(Request $request, $invoice)
    {
        $existingInventoryIds = $invoice->items->pluck('inventory_id')->toArray();
        $newInventoryIds = [];

        foreach ($request->items as $item) {
            if (!empty($item['inventory_id'])) {
                $this->updateExistingPurchaseItem($item, $invoice, $existingInventoryIds);
                $newInventoryIds[] = $item['inventory_id'];
            } else {
                $newInventoryIds[] = $this->createNewPurchaseItem($item, $invoice);
            }
        }

        $this->removeDeletedPurchaseItems($existingInventoryIds, $newInventoryIds, $invoice);
    }

    protected function updateExistingPurchaseItem($item, $invoice, $existingInventoryIds)
    {
        $inventory = Inventory::find($item['inventory_id']);

        if (!in_array($item['inventory_id'], $existingInventoryIds)) {
            throw new \Exception("Invalid inventory item");
        }

        $soldQty = $inventory->invoiceItems()
            ->whereHas('invoice', function ($q) {
                $q->where('invoice_type', 'sale');
            })
            ->sum('qty');

        if ($soldQty > 0 && $item['qty'] < $inventory->stock + $soldQty) {
            throw new \Exception("Cannot reduce quantity below sold items for product: " . $item['product_name']);
        }

        $inventory->update([
            'product_id' => $item['product_id'] ?? null,
            'product_name' => $item['product_name'],
            'product_type' => $item['condition'],
            'health' => $item['quality'],
            'age' => $this->calculateItemAge($item['warranty'] ?? null, $item['activation_date'] ?? null),
            'warranty' => !empty($item['activation_date']) ? null : ($item['warranty'] ?? null),
            'activation_date' => !empty($item['activation_date']) ? $item['activation_date'] : null,
            'battery_health' => isset($item['battery_health']) && $item['battery_health'] !== '' ? $item['battery_health'] : null,
            'color' => $item['color'],
            'imei_one' => $item['imei_one'],
            'imei_two' => $item['imei_two'],
            'serial_number' => $item['serial_number'],
            'unit' => $item['unit'],
            'stock' => $item['qty'] - $soldQty,
            'purchase_rate' => $item['price'],
            'gst_per' => $item['tax_percent'] ?? 0,
            'sell_rate' => $item['sell_rate'],
            'notes' => $invoice->notes,
        ]);

        $invoiceItem = InvoiceItem::where('invoice_id', $invoice->invoice_id)
            ->where('inventory_id', $inventory->inventory_id)
            ->first();

        if ($invoiceItem) {
            $invoiceItem->update([
                'qty' => $item['qty'],
                'rate' => $item['price'],
                'discount_percent' => $item['discount_percent'],
                'discount' => $item['discount_amount'],
                'tax_percent' => $item['tax_percent'],
                'tax' => $item['tax_amount'],
                'amount' => $item['amount'],
            ]);
        }
    }

    protected function createNewPurchaseItem($item, $invoice)
    {
        $inventory = Inventory::create([
            'user_id' => auth()->id(),
            'shop_id' => Auth::user()->shop->shop_id,
            'product_id' => $item['product_id'] ?? null,
            'product_name' => $item['product_name'],
            'product_type' => $item['condition'],
            'health' => $item['quality'],
            'age' => $this->calculateItemAge($item['warranty'] ?? null, $item['activation_date'] ?? null),
            'warranty' => !empty($item['activation_date']) ? null : ($item['warranty'] ?? null),
            'activation_date' => !empty($item['activation_date']) ? $item['activation_date'] : null,
            'battery_health' => isset($item['battery_health']) && $item['battery_health'] !== '' ? $item['battery_health'] : null,
            'color' => $item['color'],
            'imei_one' => $item['imei_one'],
            'imei_two' => $item['imei_two'],
            'serial_number' => $item['serial_number'],
            'unit' => $item['unit'],
            'stock' => $item['qty'],
            'purchase_rate' => $item['price'],
            'gst_per' => $item['tax_percent'] ?? 0,
            'sell_rate' => $item['sell_rate'],
            'notes' => $invoice->notes,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'inventory_id' => $inventory->inventory_id,
            'qty' => $item['qty'],
            'rate' => $item['price'],
            'discount_percent' => $item['discount_percent'],
            'discount' => $item['discount_amount'],
            'tax_percent' => $item['tax_percent'],
            'tax' => $item['tax_amount'],
            'amount' => $item['amount'],
        ]);

        return $inventory->inventory_id;
    }

    protected function removeDeletedPurchaseItems($existingInventoryIds, $newInventoryIds, $invoice)
    {
        $itemsToRemove = array_diff($existingInventoryIds, $newInventoryIds);

        if (!empty($itemsToRemove)) {
            foreach ($itemsToRemove as $inventoryId) {
                $inventory = Inventory::find($inventoryId);

                $soldQty = $inventory->invoiceItems()
                    ->whereHas('invoice', function ($q) {
                        $q->where('invoice_type', 'sale');
                    })
                    ->sum('qty');

                if ($soldQty > 0) {
                    throw new \Exception("Cannot remove inventory item that has been sold: " . $inventory->product_name);
                }

                InvoiceItem::where('invoice_id', $invoice->invoice_id)
                    ->where('inventory_id', $inventoryId)
                    ->delete();

                $inventory->delete();
            }
        }
    }

    protected function updateSaleItems(Request $request, $invoice)
    {
        $existingItemIds = $invoice->items->pluck('invoice_item_id')->toArray();
        $newItemIds = [];

        foreach ($request->items as $itemData) {
            $invoiceItem = InvoiceItem::where('invoice_id', $invoice->invoice_id)
                ->where('inventory_id', $itemData['inventory_id'])
                ->first();

            if ($invoiceItem) {
                $this->updateExistingSaleItem($invoiceItem, $itemData);
                $newItemIds[] = $invoiceItem->invoice_item_id;
            } else {
                $newItemIds[] = $this->createNewSaleItem($invoice, $itemData);
            }
        }

        $this->removeDeletedSaleItems($existingItemIds, $newItemIds);
    }

    protected function updateExistingSaleItem($invoiceItem, $itemData)
    {
        $inventory = Inventory::where('inventory_id', $itemData['inventory_id'])
            ->lockForUpdate()
            ->first();

        if (!$inventory) {
            throw new \Exception("Inventory item not found");
        }

        $originalQty = $invoiceItem->qty;
        $qtyChange = $itemData['qty'] - $originalQty;

        if ($qtyChange > 0 && $inventory->stock < $qtyChange) {
            throw new \Exception("Not enough stock for product: " . $itemData['product_name']);
        }

        if ($qtyChange > 0) {
            $inventory->decrement('stock', $qtyChange);
        } elseif ($qtyChange < 0) {
            $inventory->increment('stock', abs($qtyChange));
        }

        $invoiceItem->update([
            'qty' => $itemData['qty'],
            'rate' => $itemData['price'],
            'discount_percent' => $itemData['discount_percent'],
            'discount' => $itemData['discount_amount'],
            'tax_percent' => $itemData['tax_percent'],
            'tax' => $itemData['tax_amount'],
            'amount' => $itemData['amount'],
        ]);
    }

    protected function createNewSaleItem($invoice, $itemData)
    {
        $inventory = Inventory::where('inventory_id', $itemData['inventory_id'])
            ->lockForUpdate()
            ->first();

        if (!$inventory) {
            throw new \Exception("Inventory item not found");
        }

        if ($inventory->stock < $itemData['qty']) {
            throw new \Exception("Not enough stock for product: " . $itemData['product_name']);
        }

        $inventory->decrement('stock', $itemData['qty']);

        $newItem = InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'inventory_id' => $inventory->inventory_id,
            'product_id' => $inventory->product_id,
            'qty' => $itemData['qty'],
            'rate' => $itemData['price'],
            'discount_percent' => $itemData['discount_percent'],
            'discount' => $itemData['discount_amount'],
            'tax_percent' => $itemData['tax_percent'],
            'tax' => $itemData['tax_amount'],
            'amount' => $itemData['amount'],
        ]);

        return $newItem->invoice_item_id;
    }

    protected function removeDeletedSaleItems($existingItemIds, $newItemIds)
    {
        $itemsToRemove = array_diff($existingItemIds, $newItemIds);

        if (!empty($itemsToRemove)) {
            foreach ($itemsToRemove as $itemId) {
                $item = InvoiceItem::find($itemId);
                if ($item) {
                    $inventory = Inventory::find($item->inventory_id);
                    if ($inventory) {
                        $inventory->increment('stock', $item->qty);
                    }
                    $item->delete();
                }
            }
        }
    }

    public function destroyPurchase($id)
    {
        return $this->destroyInvoice($id, 'purchase');
    }

    public function destroySale($id)
    {
        return $this->destroyInvoice($id, 'sale');
    }

    protected function destroyInvoice($id, $type)
    {
        $invoice = Invoice::with('items.inventory')
            ->where('invoice_id', $id)
            ->where('invoice_type', $type)
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();

        try {
            if ($type === 'purchase') {
                $this->deletePurchaseInvoice($invoice);
            } else {
                $this->deleteSaleInvoice($invoice);
            }

            DB::commit();

            return redirect()->route($this->invoiceTypes[$type]['route'])
                ->with('success', ucfirst($type) . ' invoice deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(ucfirst($type) . ' invoice deletion failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    protected function deletePurchaseInvoice($invoice)
    {
        foreach ($invoice->items as $item) {
            if ($item->inventory) {
                $soldQty = $item->inventory->invoiceItems()
                    ->whereHas('invoice', function ($q) {
                        $q->where('invoice_type', 'sale');
                    })
                    ->sum('qty');

                if ($soldQty > 0) {
                    throw new \Exception("Cannot delete purchase invoice because some items have been sold");
                }
            }
        }

        foreach ($invoice->items as $item) {
            if ($item->inventory) {
                $item->inventory->delete();
            }
            $item->delete();
        }

        $invoice->delete();
    }

    protected function deleteSaleInvoice($invoice)
    {
        foreach ($invoice->items as $item) {
            if ($item->inventory) {
                $item->inventory->increment('stock', $item->qty);
            }
            $item->delete();
        }

        $invoice->delete();
    }

    protected function handleFileUpload(Request $request, $type, $oldPath = null)
    {
        if (!$request->hasFile($type)) {
            return $oldPath;
        }

        $newPath = $this->uploadInvoiceFile($request->file($type), $type, $request->invoice_no);

        if ($newPath && $oldPath) {
            Storage::disk('public_html')->delete($oldPath);
        }

        return $newPath ?: $oldPath;
    }

    protected function uploadInvoiceFile($file, $type, $invoiceNo)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $invoiceNo . '-' . $type . '-' . $randomString . '.' . $extension;
            $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $fileName);

            $directory = 'Invoices/' . ucfirst($type);
            if (!Storage::disk('public_html')->exists($directory)) {
                Storage::disk('public_html')->makeDirectory($directory);
            }

            return $file->storeAs($directory, $fileName, 'public_html');
        } catch (\Exception $e) {
            Log::error('Invoice file upload failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function generateUniqueInvoiceUrl()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxAttempts = 20;
        $length = random_int(10, 30);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, strlen($characters) - 1)];
            }

            if (!Invoice::where('invoice_url', $randomString)->exists()) {
                return $randomString;
            }

            $length = min(30, $length + 1);
        }

        return Str::random(30) . time();
    }

    protected function calculateItemAge($warranty = null, $activationDate = null)
    {
        if (!empty($activationDate)) {
            $actDate = \Carbon\Carbon::parse($activationDate);
            $today = \Carbon\Carbon::now();

            $exactDays = $actDate->diffInDays($today, true);
            $daysDiff = (int) round($exactDays);

            if ($daysDiff < 90) {
                return $daysDiff . ' ' . Str::plural('Day', $daysDiff);
            } else {
                $exactMonths = $exactDays / 30.4375;
                $formattedMonths = floatval(number_format($exactMonths, 1));

                return $formattedMonths . ' ' . Str::plural('Month', $formattedMonths);
            }
        }

        $warranty = strtolower(trim($warranty ?? ''));
        $mapping = [
            'out of warranty' => '1 Year+',
            '1 week' => '1 Year',
            '1 month' => '11 Months',
            '2 months' => '10 Months',
            '3 months' => '9 Months',
            '4 months' => '8 Months',
            '5 months' => '7 Months',
            '6 months' => '6 Months',
            '7 months' => '5 Months',
            '8 months' => '4 Months',
            '9 months' => '3 Months',
            '10 months' => '2 Months',
            '11 months' => '1 Month',
            '1 year' => 'New',
            '2 years' => 'New',
            '3 years' => 'New',
            '4 years' => 'New',
            '5 years' => 'New',
        ];

        return $mapping[$warranty] ?? '0 Days';
    }

    /**
     * Ultra-fast, lightweight API endpoint for Sale page inventory search & IMEI scanner modal
     */
    public function apiSearchInventory(Request $request)
    {
        $search = strtolower(trim($request->get('q', '')));

        // Require at least 2 characters to trigger search
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $inventories = Inventory::query()
            ->where('user_id', auth()->id())
            ->where('stock', '>', 0)
            ->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('imei_one', 'like', "%{$search}%")
                ->orWhere('imei_two', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%");
            })
            // STRICT Column Selection: Only select exact fields used by JS
            ->select([
                'inventory_id',
                'product_name',
                'product_type',
                'health',
                'warranty',
                'activation_date',
                'battery_health',
                'color',
                'imei_one',
                'imei_two',
                'serial_number',
                'unit',
                'stock',
                'purchase_rate',
                'sell_rate',
                'gst_per'
            ])
            ->limit(10) // Limit results to top 10 matches
            ->toBase()  // Bypass Eloquent model instantiation overhead
            ->get();

        return response()->json($inventories);
    }

    /**
     * Highly Optimized, Lightweight API endpoint for Purchase page product search
     */
    public function apiSearchProducts(Request $request)
    {
        $search = strtolower(trim($request->get('q', '')));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::query()
            ->with([
                'productAttributes.attributeValue:attribute_value_id,value'
            ])
            ->where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(100)
            ->get(['product_id', 'name', 'color']);

        $formatted = $products->map(function ($product) {
            $attrs = $product->productAttributes
                ->pluck('attributeValue.value')
                ->filter()
                ->values()
                ->toArray();

            $attrString = !empty($attrs) ? ' (' . implode('/', $attrs) . ')' : '';
            $colorString = !empty($product->color) ? ' - ' . $product->color : '';

            return [
                'id'    => $product->product_id,
                'name'  => $product->name . $attrString . $colorString,
                'color' => $product->color ?? '',
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Lightweight API endpoint for Customer autocomplete
     */
    public function apiSearchCustomers(Request $request)
    {
        $search = trim($request->get('q', ''));

        if (strlen($search) < 1) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('mobile', 'like', "%{$search}%")
            ->limit(10)
            ->get(['customer_id', 'name', 'mobile', 'address']);

        return response()->json($customers);
    }

}
