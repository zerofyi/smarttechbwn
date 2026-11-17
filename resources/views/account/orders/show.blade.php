@extends('account.layout')

@section('account')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold" style="color: #6B1A00;">Order {{ $order->order_number ?? '' }}</h2>
            <p class="text-sm text-gray-500">Placed {{ optional($order->created_at)->format('d M Y, h:i A') ?? '' }}</p>
        </div>
        <a href="{{ route('account.orders.index') }}" class="text-sm text-red-800 font-semibold hover:underline">&larr; Back to Orders</a>
    </div>

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
        $canCancel = in_array($order->status ?? '', ['placed', 'confirmed']);
        $canReturn = ($order->status ?? '') === 'delivered'
            && !empty($order->delivered_at)
            && \Carbon\Carbon::parse($order->delivered_at)->diffInDays(now()) <= 7;
        $orderId = $order->order_id ?? $order->id ?? '';
    @endphp

    {{-- Status + actions --}}
    <div class="bg-white rounded-xl border shadow-sm p-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold px-3 py-1 rounded-full capitalize {{ $badge }}">{{ str_replace('_', ' ', $order->status ?? '') }}</span>
            <span class="text-sm text-gray-600">Payment: <span class="font-semibold capitalize">{{ $order->payment_status ?? 'pending' }}</span> ({{ $order->payment_method === 'razorpay' ? 'Online' : 'Cash on Delivery' }})</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($canCancel)
                <form method="POST" action="{{ route('account.orders.cancel', $orderId) }}" onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-red-200 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-50">Cancel Order</button>
                </form>
            @endif
            @if(($order->status ?? '') !== 'cancelled')
                <form method="POST" action="{{ route('account.orders.reorder', $orderId) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Reorder</button>
                </form>
            @endif
            @if($canReturn)
                <details>
                    <summary class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 cursor-pointer inline-block">Request Return</summary>
                    <form method="POST" action="{{ route('account.orders.return', $orderId) }}" class="mt-3">
                        @csrf
                        <textarea name="reason" rows="3" required maxlength="500" placeholder="Reason for return (required)" class="w-full sm:w-80 px-3 py-2 border rounded-lg text-sm"></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Submit Return Request</button>
                    </form>
                </details>
            @endif
            <a href="{{ route('account.orders.invoice', $orderId) }}" class="px-4 py-2 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Download Invoice</a>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-4" style="color: #6B1A00;">Order Timeline</h3>
        @if(($order->updates ?? collect())->isEmpty())
            <p class="text-sm text-gray-500">No updates yet.</p>
        @else
            <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                @foreach($order->updates as $update)
                    <li class="ml-5">
                        <span class="absolute -left-2 mt-1 w-4 h-4 rounded-full bg-red-800 border-2 border-white"></span>
                        <p class="text-sm font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $update->status ?? '') }}</p>
                        @if(!empty($update->notes))
                            <p class="text-sm text-gray-600">{{ $update->notes }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ optional($update->created_at)->format('d M Y, h:i A') ?? '' }}</p>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-4" style="color: #6B1A00;">Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-3 font-semibold">Item</th>
                        <th class="py-2 pr-3 font-semibold">Price</th>
                        <th class="py-2 pr-3 font-semibold">Qty</th>
                        <th class="py-2 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items ?? [] as $orderItem)
                        @php
                            $itemProduct = optional(optional($orderItem->inventory)->product);
                        @endphp
                        <tr class="border-b last:border-0">
                            <td class="py-3 pr-3">
                                <div class="flex items-center gap-3">
                                    @if($itemProduct->img_one ?? null)
                                        <img src="{{ asset('storage/' . $itemProduct->img_one) }}" alt="" class="w-12 h-12 object-contain shrink-0">
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $orderItem->product_name ?? $itemProduct->name ?? 'Item' }}</span>
                                </div>
                            </td>
                            <td class="py-3 pr-3 text-gray-600">&#8377;{{ number_format($orderItem->price ?? 0, 2) }}</td>
                            <td class="py-3 pr-3 text-gray-600">{{ $orderItem->quantity ?? 1 }}</td>
                            <td class="py-3 font-semibold text-right">&#8377;{{ number_format($orderItem->total ?? (($orderItem->price ?? 0) * ($orderItem->quantity ?? 1)), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 space-y-1 text-sm max-w-xs ml-auto">
            <div class="flex justify-between py-1"><span class="text-gray-600">Subtotal</span><span class="font-semibold">&#8377;{{ number_format($order->subtotal ?? 0, 2) }}</span></div>
            <div class="flex justify-between py-1"><span class="text-gray-600">GST (included)</span><span class="font-semibold">&#8377;{{ number_format($order->tax ?? 0, 2) }}</span></div>
            <div class="flex justify-between py-1"><span class="text-gray-600">Shipping</span><span class="font-semibold">{{ ($order->shipping ?? 0) == 0 ? 'Free' : '₹' . number_format($order->shipping, 2) }}</span></div>
            @if(!empty($order->discount))
                <div class="flex justify-between py-1"><span class="text-gray-600">Discount</span><span class="font-semibold text-green-700">&minus;&#8377;{{ number_format($order->discount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-2 border-t font-bold text-red-800"><span>Total</span><span>&#8377;{{ number_format($order->total ?? 0, 2) }}</span></div>
        </div>
    </div>

    {{-- Delivery address --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-3" style="color: #6B1A00;">Delivery Address</h3>
        @if(!empty($order->address))
            <p class="text-sm text-gray-700">
                <span class="font-semibold">{{ $order->address->contact_person ?? '' }}</span> &middot; {{ $order->address->contact_number ?? '' }}<br>
                {{ $order->address->address_line_1 ?? '' }}
                @if(!empty($order->address->address_line_2))
                , {{ $order->address->address_line_2 }}
                @endif
                @if(!empty($order->address->landmark))
                , {{ $order->address->landmark }}
                @endif
                <br>
                {{ $order->address->city ?? '' }}
                @if(!empty($order->address->state))
                , {{ $order->address->state }}
                @endif
                @if(!empty($order->address->postal_code))
                &ndash; {{ $order->address->postal_code }}
                @endif
            </p>
        @else
            <p class="text-sm text-gray-500">Address details are not available for this order.</p>
        @endif
        @if(!empty($order->notes))
            <p class="text-sm text-gray-600 mt-3"><span class="font-semibold">Order notes:</span> {{ $order->notes }}</p>
        @endif
    </div>
</div>
@endsection
