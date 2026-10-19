<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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

class OldInventoryController extends Controller
{
    public function create()
    {
        $user_id = Auth::id();
        $lastInvoice = Invoice::where('invoice_type', 'purchase')
            ->where('distributor_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int) str_replace('PUR-'.$user_id, '', $lastInvoice->invoice_no) + 1 : 1001;
        $nextInvoiceNumber = 'PUR-'.$user_id . $nextNumber;

        $parties = Party::where('user_id', $user_id)->get();
        $customers = Customer::all();

        $products = Product::with([
                    'brand',
                    'category',
                    'productAttributes.attribute',
                    'productAttributes.attributeValue'
                ])
                ->orderBy('created_at', 'desc');

        return view('distributor.purchase', [
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'parties' => $parties,
            'products' => $products,
            'customers' => $customers,
            'shop' => Auth::user()->shop
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:invoices,invoice_no',
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|in:party,user,customer',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_name_select' => 'nullable|string',
            'supplier_phone' => 'required|string|max:20',
            'supplier_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'party_id' => 'nullable|exists:parties,party_id',
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
            'items.*.product_id' => 'nullable|exists:products,product_id',
            'items.*.product_name' => 'required|string',
            'items.*.condition' => 'required|in:new,used,refurbished,open box',
            'items.*.quality' => 'required|in:fair,good,superb,super mint,sealed pack',
            'items.*.warranty' => 'required|string',
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
        ]);

        DB::beginTransaction();

        try {
            // Handle customer creation if it's a new customer
            $customerId = $request->customer_id;
            if ($request->invoice_for === 'customer' && empty($customerId)) {
                $customer = Customer::create([
                    'name' => $request->supplier_name,
                    'mobile' => $request->supplier_phone,
                    'address' => $request->supplier_address,
                ]);
                $customerId = $customer->customer_id;
            }

            // Determine the supplier name based on invoice_for
            $supplierName = null;
            if ($request->invoice_for === 'party') {
                $party = Party::find($request->party_id);
                $supplierName = $party ? $party->name : null;
            } elseif ($request->invoice_for === 'customer') {
                $supplierName = $request->supplier_name;
            }

            // Handle file uploads
            $photoPath = null;
            $docPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadInvoiceFile($request->file('photo'), 'photo', $request->invoice_no);
            }

            if ($request->hasFile('document')) {
                $docPath = $this->uploadInvoiceFile($request->file('document'), 'document', $request->invoice_no);
            }

            // Create the invoice
            $invoice = Invoice::create([
                'distributor_id' => auth()->id(),
                'user_id' => null,
                'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
                'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
                'agreement_id' => null,
                'invoice_no' => $request->invoice_no,
                'invoice_url' => $this->generateUniqueInvoiceUrl(),
                'invoice_type' => 'purchase',
                'invoice_for' => $request->invoice_for,
                'name' => $supplierName,
                'mobile' => $request->supplier_phone,
                'address' => $request->supplier_address,
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
            ]);

            // Process each item
            foreach ($request->items as $item) {
                // Create inventory record
                $inventory = Inventory::create([
                    'user_id' => auth()->id(),
                    'shop_id' => Auth::user()->shop->shop_id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'product_type' => $item['condition'],
                    'health' => $item['quality'],
                    'age' => $this->calculateItemAge($item['warranty']),
                    'warranty' => $item['warranty'],
                    'color' => $item['color'],
                    'imei_one' => $item['imei_one'],
                    'imei_two' => $item['imei_two'],
                    'serial_number' => $item['serial_number'],
                    'unit' => $item['unit'],
                    'stock' => $item['qty'],
                    'purchase_rate' => $item['price'],
                    'sell_rate' => $item['sell_rate'],
                    'notes' => $request->notes,
                ]);

                // Create invoice item
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

            DB::commit();

            return redirect()->route('distributor.invoices.purchase')->with('success', 'Purchase invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Invoice creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->withInput()->with('error', 'Failed to create purchase invoice: ' . $e->getMessage());
        }
    }

    public function sale()
    {
        $user_id = Auth::id();
        $lastInvoice = Invoice::where('invoice_type', 'sale')
            ->where('distributor_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int) str_replace('SALE-'.$user_id, '', $lastInvoice->invoice_no) + 1 : 1001;
        $nextInvoiceNumber = 'SALE-'.$user_id . $nextNumber;

        $parties = Party::where('user_id', $user_id)->get();
        $customers = Customer::all();

        // Get inventory items that are in stock
        $inventory = Inventory::where('user_id', $user_id)
            ->where('stock', '>', 0)
            ->with(['product.brand', 'product.category'])
            ->get();

        $products = Product::with([
                'brand',
                'category',
                'productAttributes.attribute',
                'productAttributes.attributeValue'
            ])
            ->orderBy('created_at', 'desc');

        return view('distributor.sale', [
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'parties' => $parties,
            'products' => $products,
            'customers' => $customers,
            'inventory' => $inventory,
            'shop' => Auth::user()->shop
        ]);
    }

    public function storeSale(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:invoices,invoice_no',
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|in:party,user,customer',
            'customer_name' => 'nullable|string|max:255',
            'customer_name_select' => 'nullable|string',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'party_id' => 'nullable|exists:parties,party_id',
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
            'items.*.inventory_id' => 'required|exists:inventories,inventory_id',
            'items.*.product_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'required|numeric|min:0|max:100',
            'items.*.discount_amount' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'required|numeric|min:0|max:100',
            'items.*.tax_amount' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Handle customer creation if it's a new customer
            $customerId = $request->customer_id;
            if ($request->invoice_for === 'customer' && empty($customerId)) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'mobile' => $request->customer_phone,
                    'address' => $request->customer_address,
                ]);
                $customerId = $customer->customer_id;
            }

            // Determine the customer name based on invoice_for
            $customerName = null;
            if ($request->invoice_for === 'party') {
                $party = Party::find($request->party_id);
                $customerName = $party ? $party->name : null;
            } elseif ($request->invoice_for === 'customer') {
                $customerName = $request->customer_name;
            }

            // Handle file uploads
            $photoPath = null;
            $docPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadInvoiceFile($request->file('photo'), 'photo', $request->invoice_no);
            }

            if ($request->hasFile('document')) {
                $docPath = $this->uploadInvoiceFile($request->file('document'), 'document', $request->invoice_no);
            }

            // Create the invoice
            $invoice = Invoice::create([
                'distributor_id' => auth()->id(),
                'user_id' => null,
                'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
                'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
                'agreement_id' => null,
                'invoice_no' => $request->invoice_no,
                'invoice_url' => $this->generateUniqueInvoiceUrl(),
                'invoice_type' => 'sale',
                'invoice_for' => $request->invoice_for,
                'name' => $customerName,
                'mobile' => $request->customer_phone,
                'address' => $request->customer_address,
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
            ]);

            // Process each item
            foreach ($request->items as $item) {
                // Get the inventory item
                $inventory = Inventory::find($item['inventory_id']);

                if (!$inventory) {
                    throw new \Exception("Inventory item not found: " . $item['inventory_id']);
                }

                // Check if there's enough stock
                if ($inventory->stock < $item['qty']) {
                    throw new \Exception("Not enough stock for product: " . $item['product_name']);
                }

                // Decrease the stock
                $inventory->stock -= $item['qty'];
                $inventory->save();

                // Create invoice item
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

            DB::commit();

            return redirect()->route('distributor.invoices.sale')->with('success', 'Sale invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Sale invoice creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->withInput()->with('error', 'Failed to create sale invoice: ' . $e->getMessage());
        }
    }



    public function editPurchase($id)
    {
        $invoice = Invoice::with(['items.inventory', 'party', 'customer'])
            ->where('invoice_id', $id)
            ->where('invoice_type', 'purchase')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $parties = Party::where('user_id', Auth::id())->get();
        $customers = Customer::all();

        $products = Product::with([
                    'brand',
                    'category',
                    'productAttributes.attribute',
                    'productAttributes.attributeValue'
                ])
                ->orderBy('created_at', 'desc')
                ->get(); // Added get() to execute the query

        return view('distributor.purchase-edit', [
            'invoice' => $invoice,
            'parties' => $parties,
            'products' => $products,
            'customers' => $customers,
            'shop' => Auth::user()->shop
        ]);
    }
    public function updatePurchase(Request $request, $id)
    {
        $invoice = Invoice::where('invoice_id', $id)
            ->where('invoice_type', 'purchase')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|in:party,user,customer',
            'supplier_name' => 'nullable|string|max:255|required_if:invoice_for,customer',
            'supplier_phone' => 'required|string|max:20',
            'supplier_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'party_id' => 'nullable|exists:parties,party_id',
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
            'items.*.inventory_id' => 'nullable|exists:inventories,inventory_id',
            'items.*.product_id' => 'nullable|exists:products,product_id',
            'items.*.product_name' => 'required|string',
            'items.*.condition' => 'required|in:new,used,refurbished,open box',
            'items.*.quality' => 'required|in:fair,good,superb,super mint,sealed pack',
            'items.*.warranty' => 'required|string',
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
        ]);

        DB::beginTransaction();

        try {
            // Handle customer creation if it's a new customer
            $customerId = $request->customer_id;
            if ($request->invoice_for === 'customer' && empty($customerId)) {
                $customer = Customer::create([
                    'name' => $request->supplier_name,
                    'mobile' => $request->supplier_phone,
                    'address' => $request->supplier_address,
                ]);
                $customerId = $customer->customer_id;
            }

            // Determine the supplier name
            $supplierName = null;
            if ($request->invoice_for === 'party') {
                $party = Party::find($request->party_id);
                $supplierName = $party ? $party->name : null;
            } elseif ($request->invoice_for === 'customer') {
                $supplierName = $request->supplier_name;
            }

            // Handle file uploads
            $photoPath = $invoice->photo_url;
            $docPath = $invoice->doc_url;

            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadInvoiceFile($request->file('photo'), 'photo', $invoice->invoice_no);
                // Delete old photo if exists
                if ($invoice->photo_url) {
                    Storage::disk('public_html')->delete($invoice->photo_url);
                }
            }

            if ($request->hasFile('document')) {
                $docPath = $this->uploadInvoiceFile($request->file('document'), 'document', $invoice->invoice_no);
                // Delete old document if exists
                if ($invoice->doc_url) {
                    Storage::disk('public_html')->delete($invoice->doc_url);
                }
            }

            // Update the invoice
            $invoice->update([
                'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
                'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
                'invoice_for' => $request->invoice_for,
                'name' => $supplierName,
                'mobile' => $request->supplier_phone,
                'address' => $request->supplier_address,
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

            // Process inventory items
            $existingInventoryIds = $invoice->items->pluck('inventory_id')->toArray();
            $newInventoryIds = [];

            foreach ($request->items as $item) {
                if (!empty($item['inventory_id'])) {
                    // Update existing inventory item
                    $inventory = Inventory::find($item['inventory_id']);

                    // Verify the item belongs to this invoice and hasn't been sold
                    if (!in_array($item['inventory_id'], $existingInventoryIds)) {
                        throw new \Exception("Invalid inventory item");
                    }

                    // Check if any of this inventory has been sold
                    $soldQty = $inventory->invoiceItems()
                        ->whereHas('invoice', function($q) {
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
                        'age' => $this->calculateItemAge($item['warranty']),
                        'warranty' => $item['warranty'],
                        'color' => $item['color'],
                        'imei_one' => $item['imei_one'],
                        'imei_two' => $item['imei_two'],
                        'serial_number' => $item['serial_number'],
                        'unit' => $item['unit'],
                        'stock' => $item['qty'] - $soldQty, // Adjust stock based on sold items
                        'purchase_rate' => $item['price'],
                        'sell_rate' => $item['sell_rate'],
                        'notes' => $request->notes,
                    ]);

                    // Update invoice item
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

                    $newInventoryIds[] = $item['inventory_id'];
                } else {
                    // Create new inventory record
                    $inventory = Inventory::create([
                        'user_id' => auth()->id(),
                        'shop_id' => Auth::user()->shop->shop_id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'],
                        'product_type' => $item['condition'],
                        'health' => $item['quality'],
                        'age' => $this->calculateItemAge($item['warranty']),
                        'warranty' => $item['warranty'],
                        'color' => $item['color'],
                        'imei_one' => $item['imei_one'],
                        'imei_two' => $item['imei_two'],
                        'serial_number' => $item['serial_number'],
                        'unit' => $item['unit'],
                        'stock' => $item['qty'],
                        'purchase_rate' => $item['price'],
                        'sell_rate' => $item['sell_rate'],
                        'notes' => $request->notes,
                    ]);

                    // Create invoice item
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

                    $newInventoryIds[] = $inventory->inventory_id;
                }
            }

            // Remove items that were deleted from the invoice
            $itemsToRemove = array_diff($existingInventoryIds, $newInventoryIds);
            if (!empty($itemsToRemove)) {
                foreach ($itemsToRemove as $inventoryId) {
                    $inventory = Inventory::find($inventoryId);

                    // Check if any of this inventory has been sold
                    $soldQty = $inventory->invoiceItems()
                        ->whereHas('invoice', function($q) {
                            $q->where('invoice_type', 'sale');
                        })
                        ->sum('qty');

                    if ($soldQty > 0) {
                        throw new \Exception("Cannot remove inventory item that has been sold: " . $inventory->product_name);
                    }

                    // Delete the inventory and invoice item
                    InvoiceItem::where('invoice_id', $invoice->invoice_id)
                        ->where('inventory_id', $inventoryId)
                        ->delete();

                    $inventory->delete();
                }
            }

            DB::commit();

            return redirect()->route('distributor.invoices.purchase')->with('success', 'Purchase invoice updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase invoice update failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to update purchase invoice: ' . $e->getMessage());
        }
    }
    public function editSale($id)
    {
        $invoice = Invoice::with(['items.inventory', 'party', 'customer'])
            ->where('invoice_id', $id)
            ->where('invoice_type', 'sale')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $parties = Party::where('user_id', Auth::id())->get();
        $customers = Customer::all();

        // Get available inventory items
        $inventory = Inventory::where('user_id', Auth::id())
            ->where('stock', '>', 0)
            ->with(['product.brand', 'product.category'])
            ->get();

        $products = Product::with([
                'brand',
                'category',
                'productAttributes.attribute',
                'productAttributes.attributeValue'
            ])
            ->orderBy('created_at', 'desc');

        return view('distributor.sale-edit', [
            'invoice' => $invoice,
            'parties' => $parties,
            'products' => $products,
            'customers' => $customers,
            'inventory' => $inventory,
            'shop' => Auth::user()->shop
        ]);
    }
    public function updateSale(Request $request, $id)
    {
        $invoice = Invoice::where('invoice_id', $id)
            ->where('invoice_type', 'sale')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|in:party,user,customer',
            'customer_name' => 'nullable|string|max:255|required_if:invoice_for,customer',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'party_id' => 'nullable|exists:parties,party_id',
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
            'items.*.inventory_id' => 'required|exists:inventories,inventory_id',
            'items.*.product_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'required|numeric|min:0|max:100',
            'items.*.discount_amount' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'required|numeric|min:0|max:100',
            'items.*.tax_amount' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Handle customer creation if it's a new customer
            $customerId = $request->customer_id;
            if ($request->invoice_for === 'customer' && empty($customerId)) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'mobile' => $request->customer_phone,
                    'address' => $request->customer_address,
                ]);
                $customerId = $customer->customer_id;
            }

            // Determine the customer name
            $customerName = null;
            if ($request->invoice_for === 'party') {
                $party = Party::find($request->party_id);
                $customerName = $party ? $party->name : null;
            } elseif ($request->invoice_for === 'customer') {
                $customerName = $request->customer_name;
            }

            // Handle file uploads
            $photoPath = $invoice->photo_url;
            $docPath = $invoice->doc_url;

            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadInvoiceFile($request->file('photo'), 'photo', $invoice->invoice_no);
                // Delete old photo if exists
                if ($invoice->photo_url) {
                    Storage::disk('public_html')->delete($invoice->photo_url);
                }
            }

            if ($request->hasFile('document')) {
                $docPath = $this->uploadInvoiceFile($request->file('document'), 'document', $invoice->invoice_no);
                // Delete old document if exists
                if ($invoice->doc_url) {
                    Storage::disk('public_html')->delete($invoice->doc_url);
                }
            }

            // Update the invoice
            $invoice->update([
                'party_id' => $request->invoice_for === 'party' ? $request->party_id : null,
                'customer_id' => $request->invoice_for === 'customer' ? $customerId : null,
                'invoice_for' => $request->invoice_for,
                'name' => $customerName,
                'mobile' => $request->customer_phone,
                'address' => $request->customer_address,
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

            // Process items
            $existingItemIds = $invoice->items->pluck('invoice_item_id')->toArray();
            $newItemIds = [];

            foreach ($request->items as $itemData) {
                $inventory = Inventory::find($itemData['inventory_id']);

                if (!$inventory) {
                    throw new \Exception("Inventory item not found");
                }

                // Check if this is an existing item
                $invoiceItem = InvoiceItem::where('invoice_id', $invoice->invoice_id)
                    ->where('inventory_id', $inventory->inventory_id)
                    ->first();

                if ($invoiceItem) {
                    // Update existing item
                    $originalQty = $invoiceItem->qty;
                    $qtyChange = $itemData['qty'] - $originalQty;

                    // Check if we have enough stock for the increase
                    if ($qtyChange > 0 && $inventory->stock < $qtyChange) {
                        throw new \Exception("Not enough stock for product: " . $itemData['product_name']);
                    }

                    // Update inventory stock
                    $inventory->stock -= $qtyChange;
                    $inventory->save();

                    // Update invoice item
                    $invoiceItem->update([
                        'qty' => $itemData['qty'],
                        'rate' => $itemData['price'],
                        'discount_percent' => $itemData['discount_percent'],
                        'discount' => $itemData['discount_amount'],
                        'tax_percent' => $itemData['tax_percent'],
                        'tax' => $itemData['tax_amount'],
                        'amount' => $itemData['amount'],
                    ]);

                    $newItemIds[] = $invoiceItem->invoice_item_id;
                } else {
                    // Add new item - check stock
                    if ($inventory->stock < $itemData['qty']) {
                        throw new \Exception("Not enough stock for product: " . $itemData['product_name']);
                    }

                    // Update inventory stock
                    $inventory->stock -= $itemData['qty'];
                    $inventory->save();

                    // Create invoice item
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

                    $newItemIds[] = $newItem->invoice_item_id;
                }
            }

            // Remove items that were deleted from the invoice
            $itemsToRemove = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToRemove)) {
                foreach ($itemsToRemove as $itemId) {
                    $item = InvoiceItem::find($itemId);
                    if ($item) {
                        // Return stock to inventory
                        $inventory = Inventory::find($item->inventory_id);
                        if ($inventory) {
                            $inventory->stock += $item->qty;
                            $inventory->save();
                        }
                        $item->delete();
                    }
                }
            }

            DB::commit();

            return redirect()->route('distributor.invoices.sale')->with('success', 'Sale invoice updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice update failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to update sale invoice: ' . $e->getMessage());
        }
    }
    public function destroyPurchase($id)
    {
        $invoice = Invoice::with('items.inventory')
            ->where('invoice_id', $id)
            ->where('invoice_type', 'purchase')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Check if any inventory items have been sold
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $soldQty = $item->inventory->invoiceItems()
                        ->whereHas('invoice', function($q) {
                            $q->where('invoice_type', 'sale');
                        })
                        ->sum('qty');

                    if ($soldQty > 0) {
                        throw new \Exception("Cannot delete purchase invoice because some items have been sold");
                    }
                }
            }

            // Delete all inventory items and invoice items
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $item->inventory->delete();
                }
                $item->delete();
            }

            // Delete the invoice
            $invoice->delete();

            DB::commit();

            return redirect()->route('distributor.invoices.purchase')->with('success', 'Purchase invoice deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase invoice deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete purchase invoice: ' . $e->getMessage());
        }
    }
    public function destroySale($id)
    {
        $invoice = Invoice::with('items.inventory')
            ->where('invoice_id', $id)
            ->where('invoice_type', 'sale')
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Return all items to inventory
            foreach ($invoice->items as $item) {
                if ($item->inventory) {
                    $item->inventory->stock += $item->qty;
                    $item->inventory->save();
                }
                $item->delete();
            }

            // Delete the invoice
            $invoice->delete();

            DB::commit();

            return redirect()->route('distributor.invoices.sale')->with('success', 'Sale invoice deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete sale invoice: ' . $e->getMessage());
        }
    }




    protected function uploadInvoiceFile($file, $type, $invoiceNo)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $invoiceNo . '-' . $type . '-' . $randomString . '.' . $extension;

            $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $fileName);

            // Create directory if it doesn't exist
            $directory = 'Invoices/' . ucfirst($type);
            if (!Storage::disk('public_html')->exists($directory)) {
                Storage::disk('public_html')->makeDirectory($directory);
            }

            return $file->storeAs($directory, $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Invoice file upload failed: ' . $e->getMessage());
            return false;
        }
    }

    private function generateUniqueInvoiceUrl()
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

    private function calculateItemAge($warranty)
    {
        $warranty = strtolower(trim($warranty));

        switch ($warranty) {
            case 'out of warranty':
                return '1 Year+';
            case '1 week':
                return '1 Year';
            case '1 month':
                return '11 Months';
            case '2 months':
                return '10 Months';
            case '3 months':
                return '9 Months';
            case '4 months':
                return '8 Months';
            case '5 months':
                return '7 Months';
            case '6 months':
                return '6 Months';
            case '7 months':
                return '5 Months';
            case '8 months':
                return '4 Months';
            case '9 months':
                return '3 Months';
            case '10 months':
                return '2 Months';
            case '11 months':
                return '1 Month';
            default:
                return '0 Days';
        }
    }

}
