<?php

use App\Http\Controllers\UserCartController;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth', 'verified', 'customer'],
    'prefix' => 'customer'
], function () {
    // Active Cart Routes
    Route::get('cart', [UserCartController::class, 'index'])->name('customer.cart');
    Route::post('cart/add', [UserCartController::class, 'addToCart'])->name('customer.cart.add');
    Route::delete('cart/items/{item}', [UserCartController::class, 'removeItem'])->name('customer.cart.remove');
    Route::post('cart/{cart}/clear', [UserCartController::class, 'clearCart'])->name('customer.cart.clear');
    Route::post('cart/apply-coupon', [UserCartController::class, 'applyCoupon'])->name('customer.cart.apply-coupon');
    Route::post('cart/{cart}/remove-coupon', [UserCartController::class, 'removeCoupon'])->name('customer.cart.remove-coupon');

    // Checkout Action
    Route::get('checkout', [AppController::class, 'checkout'])->name('customer.checkout');
});
