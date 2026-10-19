@extends('layouts.guest')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6" style="color: #6B1A00;">Your Shopping Cart</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4 p-3 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    @if(!$cart || $cart->items->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl border p-8">
            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700">Your cart is currently empty</h3>
            <a href="{{ route('products') }}" class="inline-block mt-4 px-6 py-2 bg-red-800 text-white rounded-full font-semibold">Browse Products</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $item->inventory->product->img_one) }}" alt="{{ $item->inventory->product->name }}" class="w-16 h-16 object-contain">
                            <div>
                                <h4 class="font-bold text-sm text-gray-800">{{ $item->inventory->product->name }}</h4>
                                <span class="text-xs text-green-700 font-semibold">✓ {{ ucfirst($item->inventory->health) }}</span>
                                <div class="text-xs text-gray-500">Qty: {{ $item->qty }} @ ₹{{ number_format($item->rate) }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-red-800">₹{{ number_format($item->amount) }}</div>
                            <form method="POST" action="{{ route('customer.cart.remove', $item->cart_item_id) }}" class="inline-block mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-6 rounded-xl border h-fit">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Order Summary</h3>
                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">₹{{ number_format($cart->subtotal) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-sm">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-semibold text-green-700">Free</span>
                </div>
                <div class="flex justify-between py-3 font-bold text-base text-red-800">
                    <span>Total</span>
                    <span>₹{{ number_format($cart->total) }}</span>
                </div>
                <a href="{{ route('customer.checkout') }}" class="block w-full text-center py-3 bg-red-800 text-white font-bold rounded-xl mt-4 hover:bg-red-900 transition">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
