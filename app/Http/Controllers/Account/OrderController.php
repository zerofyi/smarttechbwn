<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->orders()->with('items.inventory.product')->latest();

        $status = $request->query('status');
        if ($status && in_array($status, OrderService::STATUSES, true)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('account.orders.index', compact('orders'));
    }

    public function show($order)
    {
        $order = $this->scopedOrder((int) $order);

        $order->load('items.inventory.product', 'updates', 'address');

        return view('account.orders.show', compact('order'));
    }

    public function cancel($order)
    {
        $order = $this->scopedOrder((int) $order);
        $service = app(OrderService::class);

        if (! $service->canCancel($order)) {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'This order can no longer be cancelled.');
        }

        $service->cancel($order, auth()->user());

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Order cancelled.');
    }

    public function reorder($order)
    {
        $order = $this->scopedOrder((int) $order);

        app(OrderService::class)->reorder($order);

        return redirect()->route('cart.index')
            ->with('success', 'In-stock items from this order were added to your cart.');
    }

    public function requestReturn(Request $request, $order)
    {
        $order = $this->scopedOrder((int) $order);
        $service = app(OrderService::class);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if (! $service->canReturn($order)) {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'This order is not eligible for a return request (returns are accepted within 7 days of delivery).');
        }

        $service->requestReturn($order, $validated['reason']);

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Return requested. Our team will contact you.');
    }

    /**
     * Load an order belonging to the logged-in user, or 404.
     */
    protected function scopedOrder(int $orderId)
    {
        $order = auth()->user()->orders()
            ->where('order_id', $orderId)
            ->first();

        if (! $order) {
            abort(404);
        }

        return $order;
    }
}
