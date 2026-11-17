@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">My Orders</h2>

    {{-- Status filter --}}
    @php $currentStatus = request('status', 'all'); @endphp
    <div class="flex flex-wrap gap-2">
        @foreach(['all' => 'All', 'placed' => 'Placed', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $value => $label)
            <a href="{{ $value === 'all' ? route('account.orders.index') : route('account.orders.index', ['status' => $value]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === $value ? 'bg-red-800 text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if(($orders ?? collect())->isEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
            <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">No orders found{{ $currentStatus !== 'all' ? ' with status "' . $currentStatus . '"' : ' yet' }}.</p>
            <a href="{{ route('products') }}" class="inline-block mt-3 px-5 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Browse Products</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $badgeColors = [
                        'placed' => 'bg-blue-100 text-blue-800',
                        'confirmed' => 'bg-indigo-100 text-indigo-800',
                        'shipped' => 'bg-amber-100 text-amber-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        'return_requested' => 'bg-purple-100 text-purple-800',
                    ];
                    $badge = $badgeColors[$order->status ?? ''] ?? 'bg-gray-100 text-gray-700';
                    $payBadge = ($order->payment_status ?? '') === 'paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800';
                @endphp
                <div class="bg-white rounded-xl border shadow-sm p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-gray-800">{{ $order->order_number ?? '' }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ optional($order->created_at)->format('d M Y, h:i A') ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full capitalize {{ $badge }}">{{ str_replace('_', ' ', $order->status ?? '') }}</span>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full capitalize {{ $payBadge }}">{{ $order->payment_status ?? 'pending' }}</span>
                        </div>
                    </div>

                    @if(!empty($order->items))
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            @foreach($order->items->take(3) as $orderItem)
                                <p class="line-clamp-1">{{ $orderItem->product_name ?? optional(optional($orderItem->inventory)->product)->name ?? 'Item' }} &times; {{ $orderItem->quantity ?? 1 }}</p>
                            @endforeach
                            @if($order->items->count() > 3)
                                <p class="text-xs text-gray-400">+ {{ $order->items->count() - 3 }} more item(s)</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-3 border-t">
                        <span class="font-bold text-red-800">&#8377;{{ number_format($order->total ?? 0, 2) }}</span>
                        <div class="flex items-center gap-2">
                            @if(in_array($order->status ?? '', ['placed', 'confirmed']))
                                <form method="POST" action="{{ route('account.orders.cancel', $order->order_id ?? $order->id ?? '') }}" onsubmit="return confirm('Cancel this order?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 border border-red-200 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-50">Cancel Order</button>
                                </form>
                            @endif
                            <a href="{{ route('account.orders.show', $order->order_id ?? $order->id ?? '') }}" class="px-4 py-1.5 bg-red-800 text-white text-xs font-bold rounded-lg hover:bg-red-900">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(method_exists($orders, 'links'))
            <div class="mt-6">{{ $orders->appends(request()->query())->links() }}</div>
        @endif
    @endif
</div>
@endsection
