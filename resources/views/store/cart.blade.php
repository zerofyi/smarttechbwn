@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6" style="color: #6B1A00;">Your Shopping Cart</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @php
        $cartItems = $cart['items'] ?? [];
        $cartCount = $cart['count'] ?? (is_countable($cartItems) ? count($cartItems) : 0);
    @endphp

    @if(empty($cartItems) || $cartCount == 0)
        <div class="text-center py-12 bg-white rounded-xl border p-8">
            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700">Your cart is currently empty</h3>
            <p class="text-sm text-gray-500 mt-1">Add some products to get started.</p>
            <a href="{{ route('products') }}" class="inline-block mt-4 px-6 py-2 bg-red-800 text-white rounded-full font-semibold hover:bg-red-900">Browse Products</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    @php
                        $inventory = $item['inventory'] ?? ($item->inventory ?? null);
                        $product = $inventory->product ?? ($item['product'] ?? null);
                        $itemId = $item['cart_item_id'] ?? $item['id'] ?? ($inventory->inventory_id ?? null);
                        $qty = $item['qty'] ?? 1;
                        $rate = $item['rate'] ?? ($inventory->sell_rate ?? 0);
                        $amount = $item['amount'] ?? ($qty * $rate);
                        $image = $product->img_one ?? null;
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 bg-white rounded-xl border">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name ?? 'Product' }}" class="w-20 h-20 object-contain shrink-0">
                            @else
                                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fas fa-image text-gray-300 text-2xl"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                @if($product)
                                    <a href="{{ route('product.show', ['product' => $product->slug, 'inventory' => $inventory->inventory_id]) }}" class="font-bold text-sm text-gray-800 hover:text-red-800 line-clamp-2">{{ $product->name }}</a>
                                @else
                                    <h4 class="font-bold text-sm text-gray-800">Product unavailable</h4>
                                @endif
                                @if($inventory)
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if(!empty($inventory->health))
                                            <span class="text-green-700 font-semibold">&#10003; {{ ucfirst($inventory->health) }}</span>
                                        @endif
                                        @if(!empty($inventory->color))
                                            &middot; {{ ucfirst($inventory->color) }}
                                        @endif
                                        @if(!empty($inventory->warranty))
                                            &middot; {{ $inventory->warranty }} mo warranty
                                        @endif
                                    </div>
                                @endif
                                <div class="text-sm text-gray-600 mt-1">&#8377;{{ number_format($rate, 2) }} each</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <form method="POST" action="{{ route('cart.update', $itemId) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" value="{{ $qty }}" min="1" max="{{ $inventory->stock ?? 99 }}" class="w-16 px-2 py-1.5 border rounded-lg text-center text-sm">
                                <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">Update</button>
                            </form>
                            <div class="font-bold text-red-800 whitespace-nowrap">&#8377;{{ number_format($amount, 2) }}</div>
                            <form method="POST" action="{{ route('cart.remove', $itemId) }}" onsubmit="return confirm('Remove this item from your cart?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Clear your entire cart?')">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:underline">Clear cart</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-xl border h-fit shadow-sm">
                <h3 class="font-bold text-lg mb-4" style="color: #6B1A00;">Order Summary</h3>

                @if(empty($cart['coupon_code']))
                    <form method="POST" action="{{ route('cart.apply-coupon') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="code" placeholder="Coupon code" class="flex-1 min-w-0 px-3 py-2 border rounded-lg text-sm uppercase" required>
                        <button type="submit" class="px-4 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Apply</button>
                    </form>
                @else
                    <div class="flex items-center justify-between mb-4 p-2 bg-green-50 border border-green-200 rounded-lg text-sm">
                        <span class="text-green-800 font-semibold">Coupon {{ $cart['coupon_code'] }} applied</span>
                        <form method="POST" action="{{ route('cart.remove-coupon') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                        </form>
                    </div>
                @endif

                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">Subtotal ({{ $cartCount }} item{{ $cartCount == 1 ? '' : 's' }})</span>
                    <span class="font-semibold">&#8377;{{ number_format($cart['subtotal'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">GST (included in prices)</span>
                    <span class="font-semibold">&#8377;{{ number_format($cart['tax'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-semibold {{ ($cart['shipping'] ?? 0) == 0 ? 'text-green-700' : '' }}">
                        {{ ($cart['shipping'] ?? 0) == 0 ? 'Free' : '₹' . number_format($cart['shipping'], 2) }}
                    </span>
                </div>
                @if(!empty($cart['discount']))
                    <div class="flex justify-between py-2 border-b text-sm">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-semibold text-green-700">&minus;&#8377;{{ number_format($cart['discount'], 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 font-bold text-base text-red-800">
                    <span>Total</span>
                    <span>&#8377;{{ number_format($cart['total'] ?? 0, 2) }}</span>
                </div>
                <p class="text-xs text-gray-400 mb-4">Prices are inclusive of GST. Free shipping on orders over &#8377;999.</p>
                <a href="{{ route('checkout.index') }}" class="block w-full text-center py-3 bg-red-800 text-white font-bold rounded-xl hover:bg-red-900 transition">
                    Proceed to Checkout
                </a>
                <a href="{{ route('products') }}" class="block w-full text-center py-2.5 mt-2 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">
                    Continue Shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
