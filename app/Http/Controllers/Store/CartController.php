<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /*
     * Guests are allowed on every cart action (no auth middleware —
     * routes define that). CartService scopes DB items by user when
     * logged in and uses the 'guest_cart' session otherwise.
     */

    public function index()
    {
        $cart = (new CartService)->getCartForRequest(auth()->user());

        return view('store.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|integer|exists:inventories,inventory_id',
            'quantity' => 'nullable|integer|min:1',
            'qty' => 'nullable|integer|min:1',
            'buy_now' => 'nullable|boolean',
        ]);

        $qty = (int) ($validated['quantity'] ?? $validated['qty'] ?? 1);

        (new CartService)->addToCart(
            (int) $validated['inventory_id'],
            $qty,
            auth()->user()
        );

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, $item)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'qty' => 'nullable|integer|min:1',
        ]);

        $qty = (int) ($validated['quantity'] ?? $validated['qty'] ?? 1);

        (new CartService)->updateQty((int) $item, $qty, auth()->user());

        return back()->with('success', 'Cart updated.');
    }

    public function remove($item)
    {
        (new CartService)->removeItem((int) $item, auth()->user());

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        (new CartService)->clear(auth()->user());

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $applied = (new CartService)->applyCoupon(
            $validated['code'],
            auth()->user()
        );

        if (! $applied) {
            return back()->withErrors(['code' => 'This coupon is not valid for your cart.']);
        }

        return back()->with('success', 'Coupon applied.');
    }

    public function removeCoupon()
    {
        (new CartService)->removeCoupon(auth()->user());

        return back()->with('success', 'Coupon removed.');
    }
}
