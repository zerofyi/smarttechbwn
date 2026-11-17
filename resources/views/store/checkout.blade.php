@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6" style="color: #6B1A00;">Checkout</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $cartItems = $cart['items'] ?? [];
        $isBuyNow = session()->has('buy_now') || request()->has('buy_now');
        $defaultId = $defaultAddress->address_id ?? null;
    @endphp

    @if($isBuyNow)
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-sm">
            Buy Now checkout &mdash; only the selected item will be ordered. Your cart will not be affected.
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-2 space-y-6">
                {{-- Delivery address --}}
                <div class="bg-white rounded-xl border p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-lg" style="color: #6B1A00;">Delivery Address</h2>
                        <a href="{{ route('account.addresses.index') }}" class="text-sm text-red-800 font-semibold hover:underline">+ Add New Address</a>
                    </div>

                    @if(($addresses ?? collect())->isEmpty())
                        <p class="text-sm text-gray-600">You have no saved addresses yet.</p>
                        <a href="{{ route('account.addresses.index') }}" class="inline-block mt-3 px-4 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Add an Address</a>
                    @else
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                                <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:border-red-300 {{ $defaultId && $address->address_id == $defaultId ? 'border-red-800 bg-red-50/40' : '' }}">
                                    <input type="radio" name="address_id" value="{{ $address->address_id }}"
                                           class="mt-1 accent-red-800"
                                           {{ (old('address_id', $defaultId) == $address->address_id) ? 'checked' : '' }} required>
                                    <span class="text-sm">
                                        <span class="font-semibold text-gray-800">{{ $address->contact_person ?? '' }}</span>
                                        @if($defaultId && $address->address_id == $defaultId)
                                            <span class="ml-2 text-xs font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Default</span>
                                        @endif
                                        @if(!empty($address->address_type))
                                            <span class="ml-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full capitalize">{{ $address->address_type }}</span>
                                        @endif
                                        <span class="block text-gray-600 mt-1">
                                            {{ $address->address_line_1 ?? '' }}
                                            @if(!empty($address->address_line_2))
                                            , {{ $address->address_line_2 }}
                                            @endif
                                            @if(!empty($address->landmark))
                                            , {{ $address->landmark }}
                                            @endif
                                        </span>
                                        <span class="block text-gray-600">{{ $address->city ?? '' }}
                                        @if(!empty($address->state))
                                        , {{ $address->state }}
                                        @endif
                                        @if(!empty($address->postal_code))
                                        &ndash; {{ $address->postal_code }}
                                        @endif
                                        </span>
                                        <span class="block text-gray-500 text-xs mt-1">{{ $address->contact_number ?? '' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Payment method --}}
                <div class="bg-white rounded-xl border p-6 shadow-sm">
                    <h2 class="font-bold text-lg mb-4" style="color: #6B1A00;">Payment Method</h2>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:border-red-300">
                            <input type="radio" name="payment_method" value="cod" class="mt-1 accent-red-800" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }} required>
                            <span>
                                <span class="block font-semibold text-sm text-gray-800">Cash on Delivery (COD)</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Pay in cash when your order is delivered.</span>
                            </span>
                        </label>
                        @if(!empty($razorpayEnabled))
                            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:border-red-300">
                                <input type="radio" name="payment_method" value="razorpay" class="mt-1 accent-red-800" {{ old('payment_method') == 'razorpay' ? 'checked' : '' }}>
                                <span>
                                    <span class="block font-semibold text-sm text-gray-800">Pay Online (Razorpay)</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">UPI, cards, net banking and wallets via Razorpay.</span>
                                </span>
                            </label>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl border p-6 shadow-sm">
                    <h2 class="font-bold text-lg mb-4" style="color: #6B1A00;">Order Notes <span class="text-sm font-normal text-gray-400">(optional)</span></h2>
                    <textarea name="notes" rows="3" maxlength="500" placeholder="Any delivery instructions?"
                              class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-800/30">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="bg-white p-6 rounded-xl border h-fit shadow-sm">
                <h3 class="font-bold text-lg mb-4" style="color: #6B1A00;">Order Summary</h3>

                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto pr-1">
                    @foreach($cartItems as $item)
                        @php
                            $inventory = $item['inventory'] ?? ($item->inventory ?? null);
                            $product = $inventory->product ?? ($item['product'] ?? null);
                            $qty = $item['qty'] ?? 1;
                            $amount = $item['amount'] ?? (($item['rate'] ?? 0) * $qty);
                        @endphp
                        <div class="flex justify-between gap-3 text-sm">
                            <span class="text-gray-700 line-clamp-2">{{ $product->name ?? 'Product' }} <span class="text-gray-400">&times; {{ $qty }}</span></span>
                            <span class="font-semibold whitespace-nowrap">&#8377;{{ number_format($amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between py-2 border-t text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">&#8377;{{ number_format($cart['subtotal'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-semibold {{ ($cart['shipping'] ?? 0) == 0 ? 'text-green-700' : '' }}">
                        {{ ($cart['shipping'] ?? 0) == 0 ? 'Free' : '₹' . number_format($cart['shipping'], 2) }}
                    </span>
                </div>
                @if(!empty($cart['discount']))
                    <div class="flex justify-between py-2 border-b text-sm">
                        <span class="text-gray-600">Discount
                        @if(!empty($cart['coupon_code']))
                        ({{ $cart['coupon_code'] }})
                        @endif
                        </span>
                        <span class="font-semibold text-green-700">&minus;&#8377;{{ number_format($cart['discount'], 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 font-bold text-base text-red-800">
                    <span>Total</span>
                    <span>&#8377;{{ number_format($cart['total'] ?? 0, 2) }}</span>
                </div>
                <p class="text-xs text-gray-400 mb-4">Amounts shown are indicative. The final total is calculated securely on our server.</p>

                <button type="submit" class="block w-full text-center py-3 bg-red-300 text-white font-bold rounded-xl hover:bg-red-300 transition" disabled>
                    Place Order
                </button>
                <a href="{{ route('cart.index') }}" class="block w-full text-center py-2.5 mt-2 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">
                    Back to Cart
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
