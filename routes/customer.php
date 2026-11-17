<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Legacy /customer/* redirects
|--------------------------------------------------------------------------
| The old customer cart/checkout routes (UserCartController /
| AppController, route names customer.cart / customer.checkout) no
| longer exist. Old URLs redirect to their new equivalents; anything
| else under /customer goes to the account dashboard.
*/

Route::redirect('/customer/cart', '/cart', 301);
Route::redirect('/customer/checkout', '/checkout', 301);

Route::get('/customer/{any}', function () {
    return redirect()->route('account.dashboard');
})->where('any', '.*');
