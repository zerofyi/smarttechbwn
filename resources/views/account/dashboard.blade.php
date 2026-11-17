@extends('account.layout')

@section('account')
<div class="space-y-6">
    {{-- Welcome card --}}
    <div class="bg-white rounded-xl border shadow-sm p-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-red-800 text-white flex items-center justify-center font-bold text-2xl shrink-0">
            {{ $user->initials() ?? strtoupper(substr($user->name ?? 'S', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <h2 class="text-xl font-bold" style="color: #6B1A00;">Welcome back, {{ $user->name ?? '' }}!</h2>
            <p class="text-sm text-gray-500 truncate">{{ $user->email ?? '' }}
            @if(!empty($user->mobile))
            &middot; {{ $user->mobile }}
            @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">Member since {{ optional($user->created_at)->format('d M Y') ?? '' }}</p>
        </div>
        <a href="{{ route('account.profile') }}" class="sm:ml-auto px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50 whitespace-nowrap">Edit Profile</a>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('account.orders.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total Orders</span>
                <i class="fas fa-box-open text-red-800/60"></i>
            </div>
            <p class="text-3xl font-bold mt-1" style="color: #6B1A00;">{{ $ordersCount ?? 0 }}</p>
        </a>
        <a href="{{ route('account.addresses.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Saved Addresses</span>
                <i class="fas fa-location-dot text-red-800/60"></i>
            </div>
            <p class="text-3xl font-bold mt-1" style="color: #6B1A00;">{{ $addressesCount ?? 0 }}</p>
        </a>
        <a href="{{ route('account.wishlist.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Wishlist Items</span>
                <i class="fas fa-heart text-red-800/60"></i>
            </div>
            <p class="text-3xl font-bold mt-1" style="color: #6B1A00;">{{ $wishlistCount ?? 0 }}</p>
        </a>
    </div>

    {{-- Recent orders --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg" style="color: #6B1A00;">Recent Orders</h3>
            <a href="{{ route('account.orders.index') }}" class="text-sm text-red-800 font-semibold hover:underline">View All</a>
        </div>

        @if(($recentOrders ?? collect())->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-500">You haven't placed any orders yet.</p>
                <a href="{{ route('products') }}" class="inline-block mt-3 px-5 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Start Shopping</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3 font-semibold">Order</th>
                            <th class="py-2 pr-3 font-semibold">Date</th>
                            <th class="py-2 pr-3 font-semibold">Total</th>
                            <th class="py-2 pr-3 font-semibold">Status</th>
                            <th class="py-2 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr class="border-b last:border-0">
                                <td class="py-3 pr-3 font-semibold text-gray-800">{{ $order->order_number ?? '' }}</td>
                                <td class="py-3 pr-3 text-gray-600">{{ optional($order->created_at)->format('d M Y') ?? '' }}</td>
                                <td class="py-3 pr-3 font-semibold">&#8377;{{ number_format($order->total ?? 0, 2) }}</td>
                                <td class="py-3 pr-3">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700 capitalize">{{ $order->status ?? '' }}</span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('account.orders.show', $order->order_id ?? $order->id ?? '') }}" class="text-red-800 font-semibold hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('account.addresses.index') }}" class="bg-white rounded-xl border shadow-sm p-4 text-center hover:shadow-md transition">
            <i class="fas fa-location-dot text-red-800 text-xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-700">Addresses</p>
        </a>
        <a href="{{ route('account.wishlist.index') }}" class="bg-white rounded-xl border shadow-sm p-4 text-center hover:shadow-md transition">
            <i class="fas fa-heart text-red-800 text-xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-700">Wishlist</p>
        </a>
        <a href="{{ route('account.invoices.index') }}" class="bg-white rounded-xl border shadow-sm p-4 text-center hover:shadow-md transition">
            <i class="fas fa-file-invoice text-red-800 text-xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-700">Invoices</p>
        </a>
        <a href="{{ route('account.support') }}" class="bg-white rounded-xl border shadow-sm p-4 text-center hover:shadow-md transition">
            <i class="fas fa-headset text-red-800 text-xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-700">Support</p>
        </a>
    </div>
</div>
@endsection
