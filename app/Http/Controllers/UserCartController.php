<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserCartController extends Controller
{
    public function index()
    {
        $cart = UserCart::with(['items.inventory.product'])
            ->firstOrCreate(['user_id' => Auth::id()]);

        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,inventory_id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $qty = $validated['quantity'] ?? 1;
        $user = Auth::user();

        $inventory = Inventory::findOrFail($validated['inventory_id']);

        if ($inventory->stock < $qty) {
            return back()->withErrors(['error' => 'Requested quantity exceeds available stock.']);
        }

        DB::transaction(function () use ($user, $inventory, $qty) {
            $cart = UserCart::firstOrCreate(
                ['user_id' => $user->user_id],
                ['subtotal' => 0, 'tax' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0]
            );

            $cartItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('inventory_id', $inventory->inventory_id)
                ->first();

            $rate = $inventory->sell_rate;

            if ($cartItem) {
                $newQty = $cartItem->qty + $qty;
                $cartItem->update([
                    'qty' => $newQty,
                    'rate' => $rate,
                    'amount' => $rate * $newQty,
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->cart_id,
                    'inventory_id' => $inventory->inventory_id,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $rate * $qty,
                ]);
            }

            $this->recalculateCart($cart);
        });

        return back()->with('success', 'Item added to your cart!');
    }

    public function removeItem($cartItemId)
    {
        $user = Auth::user();
        $cart = UserCart::where('user_id', $user->user_id)->firstOrFail();

        $cartItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('cart_item_id', $cartItemId)
            ->firstOrFail();

        DB::transaction(function () use ($cartItem, $cart) {
            $cartItem->delete();
            $this->recalculateCart($cart);
        });

        return back()->with('success', 'Item removed from cart.');
    }

    public function clearCart($cartId)
    {
        $user = Auth::user();
        $cart = UserCart::where('user_id', $user->user_id)
            ->where('cart_id', $cartId)
            ->firstOrFail();

        DB::transaction(function () use ($cart) {
            CartItem::where('cart_id', $cart->cart_id)->delete();
            $this->recalculateCart($cart);
        });

        return back()->with('success', 'Cart cleared successfully.');
    }

    private function recalculateCart(UserCart $cart)
    {
        $cart->load('items');
        $subtotal = $cart->items->sum('amount');

        $cart->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $cart->shipping + $cart->tax - $cart->discount,
        ]);
    }
}
