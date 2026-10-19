<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// Public Store Browsing Routes (Accessible by Guests & Logged-in Customers)
Route::middleware('check')->group(function () {
    Route::get('/', [GuestController::class, 'home'])->name('index');
    Route::get('explore', [GuestController::class, 'explore'])->name('explore');
    Route::get('stores', [GuestController::class, 'stores'])->name('stores');
    Route::get('products', [GuestController::class, 'products'])->name('products');
    Route::get('product/{product}/{inventory}', [GuestController::class, 'show'])->name('product.show');
    Route::get('search', [GuestController::class, 'search'])->name('search');

    // Registration Flow
    // Step 1 - Mobile & Email Details
    Route::get('register', [AuthController::class, 'showStepOne'])->name('register.form');
    Route::post('register', [AuthController::class, 'processStepOne'])
        ->middleware('throttle:5,1')
        ->name('register.submit');

    // Step 2 - OTP Verification
    Route::get('register/verify', [AuthController::class, 'showStepTwo'])->name('register.verify.form');
    Route::post('register/verify', [AuthController::class, 'processStepTwo'])
        ->middleware('throttle:10,1')
        ->name('register.verify.submit');
    Route::post('register/resend-otp', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:3,1')
        ->name('register.resend');

    // Step 3 - Password Setup
    Route::get('register/password/{user}', [AuthController::class, 'showStepThree'])
        ->middleware('signed')
        ->name('register.password.form');
    Route::post('register/password/{user}', [AuthController::class, 'processStepThree'])
        ->name('register.password.submit');

    // Authentication Routes
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'signin'])->name('signin');
    Route::get('forgot', [AuthController::class, 'login'])->name('forgot-password');
});
