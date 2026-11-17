<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('profile');

        $recentOrders = $user->orders()
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $ordersCount = $user->orders()->count();
        $addressesCount = $user->addresses()->count();
        $wishlistCount = count(session('wishlist', []));

        return view('account.dashboard', compact(
            'user',
            'recentOrders',
            'ordersCount',
            'addressesCount',
            'wishlistCount'
        ));
    }
}
