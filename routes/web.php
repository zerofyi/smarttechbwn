<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('logout', [AuthController::class, 'logout']);

Route::view('privacy', 'auth.login')->name('privacy');
Route::view('term-conditions', 'auth.login')->name('terms');

Route::get('invoice/{invoice_url}', [InvoiceController::class, 'print'])->name('invoice.print');
Route::get('gst-invoice/{invoice_url}', [InvoiceController::class, 'gst'])->name('invoice.print.gst');
Route::get('invoice/v2/{invoice_url}', [InvoiceController::class, 'print2'])->name('invoice.print2');
Route::get('invoice/{invoice_url}/download', [InvoiceController::class, 'download'])->name('invoice.download');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/customer.php';
require __DIR__.'/distributor.php';
