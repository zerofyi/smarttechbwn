<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /*
     * Wishlist v1 is session-based (session key 'wishlist' =
     * [inventory_id, ...]) for guests and logged-in users alike.
     * No new migration.
     */

    public function index()
    {
        $ids = session('wishlist', []);

        $items = $ids
            ? Inventory::with('product')
                ->whereIn('inventory_id', $ids)
                ->get()
                // Keep the order the customer added them in.
                ->sortBy(fn ($item) => array_search((int) $item->inventory_id, array_map('intval', $ids)))
                ->values()
            : collect();

        return view('account.wishlist', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|integer|exists:inventories,inventory_id',
        ]);

        $wishlist = session('wishlist', []);
        $id = (int) $validated['inventory_id'];

        if (! in_array($id, array_map('intval', $wishlist), true)) {
            $wishlist[] = $id;
            session(['wishlist' => $wishlist]);
        }

        return back()->with('success', 'Added to wishlist.');
    }

    public function destroy($inventory)
    {
        $wishlist = array_values(array_filter(
            session('wishlist', []),
            fn ($id) => (int) $id !== (int) $inventory
        ));

        session(['wishlist' => $wishlist]);

        return back()->with('success', 'Removed from wishlist.');
    }
}
