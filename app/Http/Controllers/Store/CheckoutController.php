<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\UserOrder;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = (new CartService)->getCartForRequest($user);

        if (empty($cart['count']) || empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses()->latest()->get();
        $defaultAddress = $this->defaultAddress($user, $addresses);

        $razorpayEnabled = class_exists(PaymentService::class)
            ? (new PaymentService)->enabled()
            : false;
        $razorpayKey = $razorpayEnabled
            ? config('shop.razorpay.key')
            : null;

        return view('store.checkout', compact(
            'cart',
            'addresses',
            'defaultAddress',
            'razorpayEnabled',
            'razorpayKey'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                Rule::exists('user_addresses', 'address_id')
                    ->where('user_id', $user->user_id),
            ],
            'payment_method' => 'required|in:cod,razorpay',
            'notes' => 'nullable|string|max:1000',
            'buy_now_inventory_id' => 'nullable|integer|exists:inventories,inventory_id',
            'buy_now_qty' => 'nullable|integer|min:1',
        ]);

        // Razorpay requested but not configured: do not create the order.
        if ($validated['payment_method'] === 'razorpay'
            && (! class_exists(PaymentService::class) || ! (new PaymentService)->enabled())
        ) {
            return back()
                ->withErrors(['payment_method' => 'Online payment is not available right now. Please choose Cash on Delivery.'])
                ->withInput();
        }

        $order = app(CheckoutService::class)->placeOrder($user, $validated);

        if ($validated['payment_method'] === 'razorpay') {
            $razorpayOrder = (new PaymentService)->createRazorpayOrder($order);

            if (! $razorpayOrder) {
                // Order exists but the gateway order could not be created.
                // Send the customer to the success page with a clear warning
                // rather than failing silently; payment can be retried from
                // the order page once the gateway recovers.
                return redirect()->route('checkout.success', $order)
                    ->with('error', 'We could not start the online payment for this order. Please try paying again from your orders, or contact support.');
            }

            return view('store.checkout-razorpay', [
                'order' => $order,
                'razorpayOrder' => $razorpayOrder,
                'key' => config('shop.razorpay.key'),
            ]);
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function success(UserOrder $order)
    {
        $user = auth()->user();

        if ((int) $order->user_id !== (int) $user->user_id) {
            abort(404);
        }

        $order->load('items.inventory.product', 'address', 'updates');

        return view('store.checkout-success', compact('order'));
    }

    public function razorpayVerify(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:user_orders,order_id',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $user = auth()->user();
        $order = UserOrder::where('order_id', $validated['order_id'])
            ->where('user_id', $user->user_id)
            ->first();

        if (! $order) {
            abort(404);
        }

        $verified = class_exists(PaymentService::class)
            ? (new PaymentService)->verifySignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ])
            : false;

        if (! $verified) {
            $order->update(['payment_status' => 'failed']);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed.',
                ], 422);
            }

            return redirect()->route('checkout.success', $order)
                ->with('error', 'Payment verification failed. Your order is placed but unpaid — please try again or contact support.');
        }

        // NOTE: UserOrder.transaction_id is a FK to the transactions table,
        // not a gateway payment id, so the Razorpay payment id is not
        // stored there. PaymentService records the gateway transaction.
        $order->update(['payment_status' => 'paid']);

        app(OrderService::class)->transition($order, 'confirmed');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('checkout.success', $order),
            ]);
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Payment successful. Order confirmed.');
    }

    /**
     * The user's default address (from their profile), falling back to
     * their most recent address.
     */
    protected function defaultAddress($user, $addresses)
    {
        $profile = $user->profile;

        if ($profile && $profile->default_address_id) {
            $default = $addresses->firstWhere(
                'address_id',
                (int) $profile->default_address_id
            );

            if ($default) {
                return $default;
            }
        }

        return $addresses->first();
    }
}
