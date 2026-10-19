<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProductController;

Route::group([
    'middleware' => ['auth', 'verified', 'admin'],
    'prefix' => 'admin'
], function ($router) {
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('distributors', [AdminController::class, 'distributors'])->name('admin.distributors');
    Route::post('distributors', [AdminController::class, 'storeDistributor'])->name('admin.distributors.store');
    Route::put('distributors/{user}', [AdminController::class, 'updateDistributor'])->name('admin.distributors.update');
    Route::delete('distributors/{user}', [AdminController::class, 'destroyDistributor'])->name('admin.distributors.destroy');

    Route::get('categories', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('add-category', [CategoryController::class, 'store'])->name('admin.add-category');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('attributes', [AttributeController::class, 'index'])->name('admin.attributes');
    Route::post('attributes', [AttributeController::class, 'store'])->name('admin.add-attribute');
    Route::delete('attributes/{attribute}', [AttributeController::class, 'destroy'])->name('admin.destroy-attribute');

    Route::get('attributes-values/{attribute}', [AttributeValueController::class, 'index'])->name('admin.attribute-values');
    Route::post('attributes-values/{attribute}/', [AttributeValueController::class, 'store'])->name('admin.attribute-values.store');
    Route::delete('attribute-values/{value}', [AttributeValueController::class, 'destroy'])->name('admin.attribute-values.destroy');

    Route::get('brands', [BrandController::class, 'index'])->name('admin.brands');
    Route::post('brands', [BrandController::class, 'store'])->name('admin.add-brand');
    Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');

    Route::get('products', [ProductController::class, 'index'])->name('admin.products');
    Route::get('add-product', [ProductController::class, 'create'])->name('admin.add-product');
    Route::get('get-data', [ProductController::class, 'data'])->name('admin.get-data');
    Route::post('add-product', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('delete-product/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('duplicate-product/{product}', [ProductController::class, 'duplicate'])->name('admin.products.duplicate');
    Route::get('edit-product/{product}', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('edit-product/{product}', [ProductController::class, 'update'])->name('admin.products.update');

    Route::get('parties', [PartyController::class, 'index'])->name('admin.parties');
    Route::post('add-party', [PartyController::class, 'store'])->name('admin.add-party');
    Route::put('update-party/{party}', [PartyController::class, 'update'])->name('admin.update-party');
    Route::delete('destroy-party/{party}', [PartyController::class, 'destroy'])->name('admin.destroy-party');

    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners');
    Route::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');





    Route::get('profile', [AdminController::class, 'index'])->name('admin.profile');



    Route::get('users', [AdminController::class, 'customers'])->name('admin.users');
    Route::get('user/{user}', [AdminController::class, 'showCustomer'])->name('admin.users.show');
    Route::post('users', [AdminController::class, 'storeCustomer'])->name('admin.users.store');
    Route::put('user/{user}', [AdminController::class, 'updateCustomer'])->name('admin.users.update');
    Route::delete('user/{user}', [AdminController::class, 'destroyCustomer'])->name('admin.users.destroy');
});
