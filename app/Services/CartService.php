<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\User;
use App\Models\UserCart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const GUEST_SESSION_KEY = 'guest_cart';

    public const GUEST_COUPON_KEY = 'guest_coupon';

    /**
     * Get the user's DB cart, creating an empty one if needed.
     */
    public function getOrCreateDbCart(User $user): UserCart
    {
        return UserCart::firstOrCreate(
            ['user_id' => $user->user_id],
            ['subtotal' => 0, 'tax' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0, 'coupon_code' => null]
        );
    }

    /**
     * Guest cart from the session: [inventory_id => qty].
     *
     * @return array<int, int>
     */
    public function getGuestItems(): array
    {
        $items = Session::get(self::GUEST_SESSION_KEY, []);

        if (! is_array($items)) {
            return [];
        }

        $clean = [];

        foreach ($items as $inventoryId => $qty) {
            $inventoryId = (int) $inventoryId;
            $qty = (int) $qty;

            if ($inventoryId > 0 && $qty > 0) {
                $clean[$inventoryId] = $qty;
            }
        }

        return $clean;
    }

    /**
     * Unified cart for the current request (guest session cart or DB cart).
     *
     * Items are a collection of arrays, each with inventory (with product),
     * product, qty, rate, amount, plus cart_item_id for DB items (null for
     * guests) so cart update/remove routes can address the row.
     */
    public function getCartForRequest(?User $user): array
    {
        if ($user) {
            $cart = $this->getOrCreateDbCart($user);

            $items = $cart->items()->with('inventory.product')->get()->map(function (CartItem $item) {
                $inventory = $item->inventory;
                $rate = $inventory ? (float) $inventory->sell_rate : (float) $item->rate;
                $qty = (int) $item->qty;

                // Never show more than is in stock; a zero-stock line is dropped.
                if ($inventory && (int) $inventory->stock <= 0) {
                    return null;
                }

                if ($inventory && $qty > (int) $inventory->stock) {
                    $qty = (int) $inventory->stock;
                }

                return [
                    'cart_item_id' => $item->cart_item_id,
                    'id' => $item->cart_item_id,
                    'inventory_id' => $item->inventory_id,
                    'inventory' => $inventory,
                    'product' => $inventory?->product,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => round($qty * $rate, 2),
                ];
            })->filter()->values();

            $subtotal = (float) $items->sum('amount');
            $couponCode = $cart->coupon_code;
        } else {
            $guestItems = $this->getGuestItems();

            $items = collect($guestItems)->map(function (int $qty, int $inventoryId) {
                $inventory = Inventory::with('product')->find($inventoryId);

                if (! $inventory || (int) $inventory->stock <= 0) {
                    return null;
                }

                $qty = min($qty, (int) $inventory->stock);
                $rate = (float) $inventory->sell_rate;

                return [
                    'cart_item_id' => null,
                    'id' => $inventoryId,
                    'inventory_id' => $inventoryId,
                    'inventory' => $inventory,
                    'product' => $inventory->product,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => round($qty * $rate, 2),
                ];
            })->filter()->values();

            $subtotal = (float) $items->sum('amount');
            $couponCode = Session::get(self::GUEST_COUPON_KEY);
        }

        $discount = $couponCode ? $this->couponDiscount($couponCode, $subtotal) : 0.0;

        if ($discount <= 0) {
            $couponCode = null;
            $discount = 0.0;
        }

        $totals = $this->totals($subtotal, $discount);

        return [
            'items' => $items,
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'shipping' => $totals['shipping'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'coupon_code' => $couponCode,
            'count' => (int) $items->sum('qty'),
        ];
    }

    /**
     * Add an inventory item to the guest session cart or the user's DB cart.
     * Qty is capped at available stock; prices always come from Inventory.
     */
    public function addToCart(int $inventoryId, int $qty = 1, ?User $user = null): array
    {
        $inventory = Inventory::findOrFail($inventoryId);

        if ((int) $inventory->stock <= 0) {
            throw ValidationException::withMessages([
                'inventory_id' => 'This item is currently out of stock.',
            ]);
        }

        $qty = max(1, $qty);
        $qty = min($qty, (int) $inventory->stock);

        if (! $user) {
            $guest = $this->getGuestItems();
            $guest[$inventoryId] = min(($guest[$inventoryId] ?? 0) + $qty, (int) $inventory->stock);
            Session::put(self::GUEST_SESSION_KEY, $guest);

            return $this->getCartForRequest(null);
        }

        $cart = $this->getOrCreateDbCart($user);

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->cart_id,
            'inventory_id' => $inventoryId,
        ]);

        $newQty = min((int) ($item->qty ?? 0) + $qty, (int) $inventory->stock);
        $rate = (float) $inventory->sell_rate;

        $item->qty = $newQty;
        $item->rate = $rate;
        $item->amount = round($newQty * $rate, 2);
        $item->save();

        $this->recalculate($cart);

        return $this->getCartForRequest($user);
    }

    /**
     * Change the qty of a cart line. For guests $cartItemId is the
     * inventory_id; for logged-in users it is the CartItem PK.
     */
    public function updateQty(int $cartItemId, int $qty, ?User $user = null): array
    {
        if (! $user) {
            $guest = $this->getGuestItems();

            if (! isset($guest[$cartItemId])) {
                throw ValidationException::withMessages([
                    'item' => 'This item is not in your cart.',
                ]);
            }

            if ($qty <= 0) {
                unset($guest[$cartItemId]);
                Session::put(self::GUEST_SESSION_KEY, $guest);

                return $this->getCartForRequest(null);
            }

            $inventory = Inventory::findOrFail($cartItemId);

            if ((int) $inventory->stock <= 0) {
                unset($guest[$cartItemId]);
                Session::put(self::GUEST_SESSION_KEY, $guest);

                throw ValidationException::withMessages([
                    'item' => 'This item is currently out of stock.',
                ]);
            }

            $guest[$cartItemId] = min($qty, (int) $inventory->stock);
            Session::put(self::GUEST_SESSION_KEY, $guest);

            return $this->getCartForRequest(null);
        }

        $cart = $this->getOrCreateDbCart($user);

        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('cart_item_id', $cartItemId)
            ->firstOrFail();

        if ($qty <= 0) {
            $item->delete();
            $this->recalculate($cart);

            return $this->getCartForRequest($user);
        }

        $inventory = Inventory::findOrFail($item->inventory_id);

        if ((int) $inventory->stock <= 0) {
            $item->delete();
            $this->recalculate($cart);

            throw ValidationException::withMessages([
                'item' => 'This item is currently out of stock.',
            ]);
        }

        $qty = min($qty, (int) $inventory->stock);
        $rate = (float) $inventory->sell_rate;

        $item->qty = $qty;
        $item->rate = $rate;
        $item->amount = round($qty * $rate, 2);
        $item->save();

        $this->recalculate($cart);

        return $this->getCartForRequest($user);
    }

    /**
     * Remove one line. For guests $cartItemId is the inventory_id;
     * for logged-in users it is the CartItem PK.
     */
    public function removeItem(int $cartItemId, ?User $user = null): array
    {
        if (! $user) {
            $guest = $this->getGuestItems();
            unset($guest[$cartItemId]);
            Session::put(self::GUEST_SESSION_KEY, $guest);

            return $this->getCartForRequest(null);
        }

        $cart = $this->getOrCreateDbCart($user);

        CartItem::where('cart_id', $cart->cart_id)
            ->where('cart_item_id', $cartItemId)
            ->delete();

        $this->recalculate($cart);

        return $this->getCartForRequest($user);
    }

    /**
     * Empty the cart (and drop any coupon).
     */
    public function clear(?User $user = null): void
    {
        if (! $user) {
            Session::forget(self::GUEST_SESSION_KEY);
            Session::forget(self::GUEST_COUPON_KEY);

            return;
        }

        $cart = $this->getOrCreateDbCart($user);
        $cart->items()->delete();
        $cart->coupon_code = null;
        $this->recalculate($cart);
    }

    /**
     * Apply a coupon code from config('shop.coupons'). Returns false when
     * the code is unknown or the cart subtotal is below its minimum.
     */
    public function applyCoupon(string $code, ?User $user = null): bool
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return false;
        }

        $coupons = config('shop.coupons', []);

        if (! isset($coupons[$code])) {
            return false;
        }

        $cart = $this->getCartForRequest($user);

        if ($this->couponDiscount($code, (float) $cart['subtotal']) <= 0) {
            return false;
        }

        if (! $user) {
            Session::put(self::GUEST_COUPON_KEY, $code);

            return true;
        }

        $dbCart = $this->getOrCreateDbCart($user);
        $dbCart->coupon_code = $code;
        $this->recalculate($dbCart);

        return true;
    }

    public function removeCoupon(?User $user = null): void
    {
        if (! $user) {
            Session::forget(self::GUEST_COUPON_KEY);

            return;
        }

        $cart = $this->getOrCreateDbCart($user);
        $cart->coupon_code = null;
        $this->recalculate($cart);
    }

    /**
     * Move the guest session cart into the user's DB cart after login or
     * registration. Quantities are capped at stock; a guest coupon is
     * carried over only if it is still valid for the merged subtotal.
     */
    public function mergeGuestCart(User $user): void
    {
        $guestItems = $this->getGuestItems();
        $guestCoupon = Session::get(self::GUEST_COUPON_KEY);

        if ($guestItems === []) {
            Session::forget(self::GUEST_SESSION_KEY);
            Session::forget(self::GUEST_COUPON_KEY);

            return;
        }

        foreach ($guestItems as $inventoryId => $qty) {
            $inventory = Inventory::find($inventoryId);

            if (! $inventory || (int) $inventory->stock <= 0) {
                continue;
            }

            $this->addToCart($inventoryId, min($qty, (int) $inventory->stock), $user);
        }

        Session::forget(self::GUEST_SESSION_KEY);
        Session::forget(self::GUEST_COUPON_KEY);

        if (is_string($guestCoupon) && $guestCoupon !== '') {
            $this->applyCoupon($guestCoupon, $user);
        }
    }

    /**
     * Re-price every line from Inventory::sell_rate, cap qty at stock
     * (dropping zero-stock lines), re-apply the coupon, and persist the
     * cart totals.
     */
    public function recalculate(UserCart $cart): void
    {
        $cart->loadMissing('items.inventory');

        $subtotal = 0.0;

        foreach ($cart->items as $item) {
            $inventory = $item->inventory;

            if (! $inventory || (int) $inventory->stock <= 0) {
                $item->delete();

                continue;
            }

            $qty = min((int) $item->qty, (int) $inventory->stock);
            $rate = (float) $inventory->sell_rate;

            $item->qty = $qty;
            $item->rate = $rate;
            $item->amount = round($qty * $rate, 2);
            $item->save();

            $subtotal += (float) $item->amount;
        }

        $couponCode = $cart->coupon_code;
        $discount = $couponCode ? $this->couponDiscount($couponCode, $subtotal) : 0.0;

        if ($couponCode && $discount <= 0) {
            $couponCode = null;
            $discount = 0.0;
        }

        $totals = $this->totals($subtotal, $discount);

        $cart->subtotal = $totals['subtotal'];
        $cart->tax = $totals['tax'];
        $cart->shipping = $totals['shipping'];
        $cart->discount = $totals['discount'];
        $cart->total = $totals['total'];
        $cart->coupon_code = $couponCode;
        $cart->save();
    }

    /**
     * Totals from a GST-inclusive subtotal. Tax is the GST component
     * inside the subtotal, for display: tax = subtotal - subtotal / 1.18.
     * Shipping is free when (subtotal - discount) reaches the threshold,
     * or when the subtotal is zero; otherwise the flat rate applies.
     *
     * @return array{subtotal: float, tax: float, shipping: float, discount: float, total: float}
     */
    public function totals(float $subtotal, float $discount = 0): array
    {
        $subtotal = round(max(0, $subtotal), 2);
        $discount = round(min(max(0, $discount), $subtotal), 2);

        $gstRate = (float) config('shop.gst_rate', 0.18);
        $tax = round($subtotal - ($subtotal / (1 + $gstRate)), 2);

        $threshold = (float) config('shop.free_shipping_threshold', 999);
        $flat = (float) config('shop.shipping_flat', 49);

        $shipping = ($subtotal == 0.0 || ($subtotal - $discount) >= $threshold) ? 0.0 : $flat;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => round($shipping, 2),
            'discount' => $discount,
            'total' => round($subtotal + $shipping - $discount, 2),
        ];
    }

    /**
     * Discount (₹) a coupon gives on a subtotal, or 0 when it does not apply.
     */
    protected function couponDiscount(string $code, float $subtotal): float
    {
        $coupons = config('shop.coupons', []);
        $coupon = $coupons[strtoupper(trim($code))] ?? null;

        if (! $coupon) {
            return 0.0;
        }

        if ($subtotal < (float) ($coupon['min_subtotal'] ?? 0)) {
            return 0.0;
        }

        if (($coupon['type'] ?? null) === 'percent') {
            $discount = $subtotal * ((float) $coupon['value'] / 100);

            if (isset($coupon['max_discount'])) {
                $discount = min($discount, (float) $coupon['max_discount']);
            }

            return round(min($discount, $subtotal), 2);
        }

        if (($coupon['type'] ?? null) === 'flat') {
            return round(min((float) $coupon['value'], $subtotal), 2);
        }

        return 0.0;
    }
}
