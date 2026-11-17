<?php

namespace App\Services;

use App\Mail\OrderStatusMail;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected PaymentService $paymentService,
    ) {}

    /**
     * Place an order for a logged-in customer.
     *
     * $data: address_id, payment_method (cod|razorpay), notes?,
     * buy_now_inventory_id?, buy_now_qty?
     *
     * Validates address ownership and stock (row-locked), creates the
     * order + items + placed timeline entry, decrements stock, clears the
     * cart (buy-now orders leave the cart alone), and for COD records a
     * pending Transaction. payment_status is pending for both methods;
     * Razorpay orders are completed by the verify step afterwards.
     * Everything is inside one DB transaction.
     */
    public function placeOrder(User $user, array $data): UserOrder
    {
        $paymentMethod = $data['payment_method'] ?? 'cod';

        if (! in_array($paymentMethod, ['cod', 'razorpay'], true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Please choose a valid payment method.',
            ]);
        }

        if ($paymentMethod === 'cod' && ! config('shop.cod_enabled', true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Cash on delivery is not available right now.',
            ]);
        }

        $address = UserAddress::where('user_id', $user->user_id)
            ->where('address_id', $data['address_id'] ?? null)
            ->first();

        if (! $address) {
            throw ValidationException::withMessages([
                'address_id' => 'Please choose a valid delivery address.',
            ]);
        }

        $buyNowInventoryId = isset($data['buy_now_inventory_id']) ? (int) $data['buy_now_inventory_id'] : null;
        $buyNowQty = max(1, (int) ($data['buy_now_qty'] ?? 1));

        $order = DB::transaction(function () use ($user, $data, $address, $paymentMethod, $buyNowInventoryId, $buyNowQty) {
            // Build the lines, locking each inventory row and checking stock.
            $lines = [];

            if ($buyNowInventoryId) {
                $inventory = Inventory::lockForUpdate()->find($buyNowInventoryId);

                if (! $inventory) {
                    throw ValidationException::withMessages([
                        'buy_now_inventory_id' => 'The item you tried to buy is no longer available.',
                    ]);
                }

                if ((int) $inventory->stock < $buyNowQty) {
                    throw ValidationException::withMessages([
                        'buy_now_inventory_id' => 'Sorry, '.($inventory->product_name ?? 'this item').' is out of stock.',
                    ]);
                }

                $lines[] = ['inventory' => $inventory, 'qty' => $buyNowQty];
            } else {
                $cart = $this->cartService->getOrCreateDbCart($user);
                $cartItems = $cart->items()->with('inventory.product')->get();

                if ($cartItems->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' => 'Your cart is empty.',
                    ]);
                }

                foreach ($cartItems as $cartItem) {
                    $inventory = Inventory::lockForUpdate()->find($cartItem->inventory_id);

                    if (! $inventory) {
                        throw ValidationException::withMessages([
                            'cart' => 'An item in your cart is no longer available.',
                        ]);
                    }

                    if ((int) $inventory->stock < (int) $cartItem->qty) {
                        $name = $inventory->product?->name ?? $inventory->product_name ?? 'An item';

                        throw ValidationException::withMessages([
                            'cart' => 'Sorry, '.$name.' is out of stock.',
                        ]);
                    }

                    $lines[] = ['inventory' => $inventory, 'qty' => (int) $cartItem->qty];
                }
            }

            // Re-price from Inventory — never trust client totals.
            $subtotal = 0.0;

            foreach ($lines as $line) {
                $subtotal += (float) $line['inventory']->sell_rate * $line['qty'];
            }

            $subtotal = round($subtotal, 2);

            $discount = 0.0;
            $cartId = null;

            if (! $buyNowInventoryId) {
                $cart = $this->cartService->getOrCreateDbCart($user);
                $cartId = $cart->cart_id;

                if ($cart->coupon_code) {
                    $cartSummary = $this->cartService->getCartForRequest($user);
                    $discount = (float) ($cartSummary['discount'] ?? 0);
                }
            }

            $totals = $this->cartService->totals($subtotal, $discount);

            $order = UserOrder::create([
                'order_number' => $this->nextOrderNumber(),
                'user_id' => $user->user_id,
                'address_id' => $address->address_id,
                'invoice_id' => null,
                'cart_id' => $cartId,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'shipping' => $totals['shipping'],
                'discount' => $totals['discount'],
                'total' => $totals['total'],
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'transaction_id' => null,
                'status' => 'placed',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                /** @var Inventory $inventory */
                $inventory = $line['inventory'];
                $qty = $line['qty'];
                $rate = (float) $inventory->sell_rate;
                $lineTotal = round($rate * $qty, 2);
                $gstRate = (float) config('shop.gst_rate', 0.18);

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'inventory_id' => $inventory->inventory_id,
                    'product_name' => $inventory->product?->name ?? $inventory->product_name ?? 'Product',
                    'quantity' => $qty,
                    'price' => $rate,
                    'tax' => round($lineTotal - ($lineTotal / (1 + $gstRate)), 2),
                    'discount' => 0,
                    'total' => $lineTotal,
                    'options' => null,
                    'status' => 'placed',
                ]);

                $inventory->decrement('stock', $qty);
            }

            OrderUpdate::create([
                'order_id' => $order->order_id,
                'status' => 'placed',
                'notes' => 'Order placed.',
                'updated_by' => $user->user_id,
            ]);

            if ($paymentMethod === 'cod') {
                $this->paymentService->recordCodTransaction($order);
            }

            if (! $buyNowInventoryId) {
                $this->cartService->clear($user);
            }

            return $order;
        });

        // Mail must never break checkout.
        try {
            Mail::to($user->email)->send(new OrderStatusMail($order, 'placed'));
        } catch (\Throwable) {
            // Log driver by default; checkout already succeeded.
        }

        return $order;
    }

    /**
     * Next order number: ST-YYYYMMDD-####, sequential per day.
     */
    protected function nextOrderNumber(): string
    {
        $prefix = 'ST-'.now()->format('Ymd').'-';
        $sequence = UserOrder::where('order_number', 'like', $prefix.'%')->count() + 1;

        while (UserOrder::where('order_number', $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT))->exists()) {
            $sequence++;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
