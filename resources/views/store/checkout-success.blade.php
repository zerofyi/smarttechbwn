@extends('layouts.store')

@section('title', 'Order Placed')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-4">
            <i class="fas fa-check text-2xl text-green-600"></i>
        </div>
        <h1 class="text-2xl font-bold mb-2" style="color: #6B1A00;">Order Placed Successfully!</h1>
        <p class="text-sm text-gray-600">Thank you for shopping with SmartTech. Your order has been placed and is being processed.</p>

        <div class="mt-6 bg-gray-50 rounded-xl p-5 text-left space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Order Number</span>
                <span class="font-bold text-gray-800">{{ $order->order_number ?? '' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Order Date</span>
                <span class="font-semibold text-gray-800">{{ optional($order->created_at)->format('d M Y, h:i A') ?? '' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Payment Method</span>
                <span class="font-semibold text-gray-800">{{ $order->payment_method === 'razorpay' ? 'Paid Online (Razorpay)' : 'Cash on Delivery' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Payment Status</span>
                <span class="font-semibold text-gray-800 capitalize">{{ $order->payment_status ?? 'pending' }}</span>
            </div>
            <div class="flex justify-between border-t pt-2">
                <span class="text-gray-500">Total Paid</span>
                <span class="font-bold text-red-800">&#8377;{{ number_format($order->total ?? 0, 2) }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
            <a href="{{ route('account.orders.show', $order->order_id ?? $order->id ?? '') }}" class="px-6 py-3 bg-red-800 text-white font-bold rounded-xl hover:bg-red-900">View Order</a>
            <a href="{{ route('products') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
