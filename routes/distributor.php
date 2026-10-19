<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinancerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoanController;

Route::group([
    'middleware' => ['auth', 'verified', 'distributor'],
    'prefix' => 'distributor'
], function ($router) {
    Route::get('dashboard', [DistributorController::class, 'index'])->name('distributor.dashboard');
    Route::get('inventory', [DistributorController::class, 'inventory'])->name('distributor.invcat');
    Route::get('dashboard2', [DistributorController::class, 'index2'])->name('distributor.dashboard2');
    Route::get('profile', [DistributorController::class, 'profile'])->name('distributor.profile');

    Route::get('parties', [DistributorController::class, 'parties'])->name('distributor.parties');
    Route::post('add-party', [DistributorController::class, 'partyStore'])->name('distributor.add-party');
    Route::put('update-party/{party}', [DistributorController::class, 'partyUpdate'])->name('distributor.update-party');
    Route::delete('destroy-party/{party}', [DistributorController::class, 'partyDestroy'])->name('distributor.destroy-party');


    Route::get('financers', [FinancerController::class, 'index'])->name('distributor.financers');
    Route::post('add-financer', [FinancerController::class, 'store'])->name('distributor.add-financer');
    Route::put('update-financer/{financer}', [FinancerController::class, 'update'])->name('distributor.update-financer');
    Route::delete('destroy-financer/{financer}', [FinancerController::class, 'destroy'])->name('distributor.destroy-financer');

    Route::post('/financers/{financer}/upload-document', [FinancerController::class, 'uploadDocument'])->name('financer.upload-document');
    Route::delete('/financers/{financer}/delete-document/{type}', [FinancerController::class, 'deleteDocument'])->name('financer.delete-document');


    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('distributor.invoices.show');
    Route::post('invoices/restore-item', [InvoiceController::class, 'restoreItem'])->name('distributor.invoices.restore-item');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('distributor.invoices');
    Route::get('inventories', [InventoryController::class, 'inventories'])->name('distributor.inventories');
    Route::get('inventories2', [InventoryController::class, 'inventories2'])->name('distributor.inventories2');
    Route::get('invoices?invoice_type=purchase', [InvoiceController::class, 'index'])->name('distributor.invoices.purchase');
    Route::get('invoices?invoice_type=sale', [InvoiceController::class, 'index'])->name('distributor.invoices.sale');

    Route::get('products', [DistributorController::class, 'products'])->name('distributor.products');

    Route::get('purchase', [InventoryController::class, 'create'])->name('distributor.purchase');
    Route::post('purchase', [InventoryController::class, 'store'])->name('distributor.purchase.store');

    Route::get('purchase/{id}', [InventoryController::class, 'editPurchase'])->name('distributor.purchase.edit');
    Route::put('purchase/{id}', [InventoryController::class, 'updatePurchase'])->name('distributor.purchase.update');
    Route::delete('purchase/{id}', [InventoryController::class, 'destroyPurchase'])->name('distributor.purchase.destroy');

    // Ajax search routes for dynamic dropdowns & modal scanning
    Route::get('api/search-inventory', [InventoryController::class, 'apiSearchInventory'])->name('distributor.api.search_inventory');
    Route::get('api/search-products', [InventoryController::class, 'apiSearchProducts'])->name('distributor.api.search_products');
    Route::get('api/search-customers', [InventoryController::class, 'apiSearchCustomers'])->name('distributor.api.search_customers');
    Route::post('inventories/{inventory}/make-offline',[InventoryController::class, 'makeInventoryOffline'])->name('distributor.inventories.make-offline');

    Route::get('sale', [InventoryController::class, 'sale'])->name('distributor.sale');
    Route::post('sale', [InventoryController::class, 'storeSale'])->name('distributor.sale.store');

    Route::get('sale/{id}', [InventoryController::class, 'editSale'])->name('distributor.sale.edit');
    Route::put('sale/{id}', [InventoryController::class, 'updateSale'])->name('distributor.sale.update');
    Route::delete('sale/{id}', [InventoryController::class, 'destroySale'])->name('distributor.sale.destroy');

    Route::get('agreements', [AgreementController::class, 'index'])->name('distributor.agreements');
    Route::get('create-agreement/{invoice_id}', [AgreementController::class, 'create'])->name('distributor.agreement.create');
    Route::post('create-agreement', [AgreementController::class, 'store'])->name('distributor.agreement.store');
    Route::get('edit-agreement/{agreement}', [AgreementController::class, 'edit'])->name('distributor.agreement.edit');
    Route::put('agreement/{agreement}', [AgreementController::class, 'update'])->name('distributor.agreement.update');
    Route::get('agreement/{agreement}', [AgreementController::class, 'print'])->name('distributor.agreement.print');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');


    // Loan Routes
    Route::get('create-loan/{invoice_id}', [LoanController::class, 'create'])->name('distributor.loan.create');
    Route::post('store-loan', [LoanController::class, 'store'])->name('distributor.loan.store');
    Route::get('loans', [LoanController::class, 'index'])->name('distributor.loans');
    Route::get('loan/{loan}', [LoanController::class, 'show'])->name('distributor.loan.show');
    Route::get('loan-print/{loan}', [LoanController::class, 'print'])->name('distributor.loan.print');
    Route::post('loan/{loan}/pay-emi', [LoanController::class, 'payEmi'])->name('distributor.loan.pay-emi');
    Route::post('loan/{loan}/settle', [LoanController::class, 'settle'])->name('distributor.loan.settle');
    Route::get('edit-loan/{loan}', [LoanController::class, 'edit'])->name('distributor.loan.edit');
    Route::put('loan/{loan}', [LoanController::class, 'update'])->name('distributor.loan.update');
    Route::get('loans/export', [LoanController::class, 'export'])->name('distributor.loans.export');

    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::get('employees/{employee}', [EmployeeController::class, 'showSales'])->name('employees.sales');

    Route::post('/verify-delete-pin', function (\Illuminate\Http\Request $request) {
        $inputPin = (string) $request->input('pin');
        $envPin = (string) config('services.delete_confirm_pin');

        if ($inputPin !== '' && hash_equals($envPin, $inputPin)) {
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ], 401);
    });

    Route::get('invoice-migration', [InvoiceController::class, 'showMigrationPage'])->name('invoices.migration.preview');
    Route::post('invoice-migration/run', [InvoiceController::class, 'runMigration'])->name('invoices.migration.run');



});
