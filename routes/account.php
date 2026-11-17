<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\InvoiceController;
use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\WishlistController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store Cart (guest + logged-in, no auth middleware — guests use a
| session cart; CartService scopes DB items by user when logged in)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove-coupon');

/*
|--------------------------------------------------------------------------
| Checkout (customer-only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/razorpay/verify', [CheckoutController::class, 'razorpayVerify'])->name('checkout.razorpay.verify');
});

/*
|--------------------------------------------------------------------------
| Customer Account (prefix /account, auth + verified + customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'customer'])->prefix('account')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('account.dashboard');

    // Profile / security / account deletion
    Route::get('/profile', [ProfileController::class, 'show'])->name('account.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('account.avatar.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('account.destroy');

    // Security + support pages (views owned by Lane D; forms post to
    // account.password.update / support handling added later)
    Route::get('/security', fn () => view('account.security', ['user' => auth()->user()]))->name('account.security');
    Route::get('/support', fn () => view('account.support', ['user' => auth()->user()]))->name('account.support');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('account.addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('account.addresses.default');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('account.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('account.orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('account.orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('account.orders.reorder');
    Route::post('/orders/{order}/return', [OrderController::class, 'requestReturn'])->name('account.orders.return');
    Route::get('/orders/{order}/invoice', [InvoiceController::class, 'show'])->name('account.orders.invoice');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('account.invoices.index');
    Route::get('/invoices/{order}/download', [InvoiceController::class, 'download'])->name('account.invoices.download');

    // Wishlist (session-based, v1)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('account.wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('account.wishlist.store');
    Route::delete('/wishlist/{inventory}', [WishlistController::class, 'destroy'])->name('account.wishlist.destroy');
});
