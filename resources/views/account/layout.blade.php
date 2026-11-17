@extends('layouts.store')

@section('title', 'My Account')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
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

    <h1 class="text-2xl font-bold mb-6" style="color: #6B1A00;">My Account</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
        {{-- Sidebar --}}
        <aside class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-red-800 text-white flex items-center justify-center font-bold text-lg shrink-0">
                        {{ auth()->user()->initials() ?? strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-sm text-gray-800 truncate">{{ auth()->user()->name ?? '' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
            <nav class="p-2 text-sm">
                @php
                    $accountLinks = [
                        ['route' => 'account.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'match' => 'account.dashboard'],
                        ['route' => 'account.orders.index', 'label' => 'My Orders', 'icon' => 'fa-box-open', 'match' => 'account.orders.*'],
                        ['route' => 'account.addresses.index', 'label' => 'Addresses', 'icon' => 'fa-location-dot', 'match' => 'account.addresses.*'],
                        ['route' => 'account.wishlist.index', 'label' => 'Wishlist', 'icon' => 'fa-heart', 'match' => 'account.wishlist.*'],
                        ['route' => 'account.invoices.index', 'label' => 'Invoices', 'icon' => 'fa-file-invoice', 'match' => 'account.invoices.*'],
                        ['route' => 'account.profile', 'label' => 'Profile', 'icon' => 'fa-user', 'match' => 'account.profile'],
                        ['route' => 'account.security', 'label' => 'Security', 'icon' => 'fa-shield-halved', 'match' => 'account.security'],
                        ['route' => 'account.support', 'label' => 'Support', 'icon' => 'fa-headset', 'match' => 'account.support'],
                    ];
                @endphp
                @foreach($accountLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs($link['match']) ? 'bg-red-50 text-red-800' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center"></i>{{ $link['label'] }}
                    </a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-gray-700 hover:bg-gray-50 text-left">
                        <i class="fas fa-right-from-bracket w-5 text-center"></i>Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main column --}}
        <div class="md:col-span-3 min-w-0">
            @yield('account')
        </div>
    </div>
</div>
@endsection
