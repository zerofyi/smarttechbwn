@extends('layouts.navbarless')

@section('title', 'Purchase Invoice')

@section('vendor-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <script src="../../assets/js/slimselect.js"></script>
    <link href="../../assets/css/slimselect.css" rel="stylesheet"></link>
    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
@endsection

@section('page-style')
<style>
    .product-suggestions {
        position: absolute;
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        display: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .product-suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 0.9rem;
    }
    .product-suggestion-item:hover {
        background-color: #f8f9fa;
    }
    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .supplier-info, .invoice-info {
        width: 48%;
    }

    .product-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .product-table th {
        background-color: #f8f9fa;
        padding: 10px;
        text-align: center;
        font-size: 0.85rem;
    }

    .product-table td {
        padding: 0;
        border-bottom: 1px solid #d0d0d0;
    }

    .product-table input, .product-table select {
        border: none;
        border-radius: 0;
        height: 100%;
        width: 100%;
        padding: 8px;
        background: transparent;
    }

    .product-table input:focus, .product-table select:focus {
        outline: none;
        box-shadow: none;
    }

    .invoice-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .payment-section, .totals-section {
        width: 48%;
    }

    .photo-uploader {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .photo-upload-item {
        width: 100px;
        height: 100px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        background-color: #f9f9f9;
    }

    .photo-upload-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    .photo-upload-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: none;
        font-size: 12px;
        padding: 0;
        line-height: 1;
    }

    .photo-upload-item:hover .remove-btn {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-upload-item input {
        display: none;
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        align-items: center;
    }

    .amount-total {
        font-weight: bold;
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 10px;
    }

    .add-product-btn {
        margin-bottom: 15px;
    }

    .remove-product {
        color: #dc3545;
        cursor: pointer;
        text-align: center;
        display: block;
        padding: 8px;
    }

    .remove-product:hover {
        color: #bb2d3b;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .d-flex {
        display: flex;
        gap: 5px;
    }

    .form-switch {
        padding-left: 0;
        margin: 0 10px;
    }
    .slx {
        text-align: center;
        font-weight: bold;
    }

    #quickEntryModal .modal-dialog {
        max-width: 860px;
    }
    #quickEntryModal .modal-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: #fff;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 1rem 1.25rem;
    }
    #quickEntryModal .modal-header .btn-close {
        filter: invert(1);
    }
    #quickEntryModal .qe-section-title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd22;
        padding-bottom: 6px;
        margin-bottom: 14px;
    }
    #quickEntryModal .qe-basic-section {
        background: #f8faff;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border: 1px solid #e0e8ff;
    }
    #quickEntryModal .qe-imei-section {
        background: #fff8f0;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid #ffe0b2;
    }
    #quickEntryModal .qe-imei-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    #quickEntryModal .qe-imei-table th {
        background: #fff3e0;
        padding: 8px 10px;
        font-weight: 600;
        text-align: center;
        border: 1px solid #ffe0b2;
        font-size: 0.8rem;
    }
    #quickEntryModal .qe-imei-table td {
        padding: 4px 4px;
        border: 1px solid #f0dcc8;
        vertical-align: middle;
    }
    #quickEntryModal .qe-imei-table td input {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 7px;
        font-size: 0.82rem;
        background: #fff;
        transition: border-color 0.15s;
    }
    #quickEntryModal .qe-imei-table td input:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.12);
    }
    #quickEntryModal .qe-imei-table td .row-num {
        text-align: center;
        font-weight: 600;
        color: #888;
        font-size: 0.8rem;
    }
    #quickEntryModal .qe-qty-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1.5px solid #0d6efd;
        border-radius: 20px;
        padding: 4px 14px;
        font-weight: 700;
        color: #0d6efd;
        font-size: 0.95rem;
        margin-bottom: 12px;
    }
    #quickEntryModal .qe-qty-badge input[type=number] {
        width: 54px;
        border: none;
        border-bottom: 1.5px solid #0d6efd;
        border-radius: 0;
        padding: 0 4px;
        font-weight: 700;
        color: #0d6efd;
        font-size: 0.95rem;
        text-align: center;
        background: transparent;
    }
    #quickEntryModal .qe-qty-badge input[type=number]:focus {
        outline: none;
        border-color: #0a58ca;
    }
    #quickEntryModal .product-suggestions {
        z-index: 9999;
    }
    .btn-quick-entry {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        color: #fff;
        border: none;
        font-weight: 600;
        letter-spacing: 0.03em;
        transition: opacity 0.15s;
    }
    .btn-quick-entry:hover {
        opacity: 0.88;
        color: #fff;
    }
    .btn-quick-entry i {
        margin-right: 6px;
    }
    #qe-imei-rows-container {
        max-height: 280px;
        overflow-y: auto;
    }
    #qe-imei-rows-container::-webkit-scrollbar {
        width: 6px;
    }
    #qe-imei-rows-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #qe-imei-rows-container::-webkit-scrollbar-thumb {
        background: #f0904c;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="content" style="padding-left: 15px; padding-right: 15px;">
    @if ($errors->any())
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Error</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div class="toast show text-white bg-success bg-opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Success</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('distributor.purchase.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="invoice_type" value="purchase">

        <!-- Top Section: Supplier and Invoice Info -->
        <div class="row flex-between-end mb-6">
            <div class="col-md-4">
                <h4 class="mb-3">Supplier Details</h4>
                <div class="row g-2">
                    <div class="col-md-2">
                        <select name="invoice_for" id="invoice_for" class="form-control">
                            <option value="party">Party</option>
                            <option value="customer" selected>Customer</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="supplier_name" id="supplier_name_input" placeholder="Supplier Name" autocomplete="off">
                        <div id="supplier_suggestions_results" style="position: absolute; z-index: 1000; width: 500px; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; border-top: none; display: none;"></div>

                        <select name="supplier_name_select" id="supplier_name_select" class="form-control">
                            <option value="">Select a Party</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->party_id }}" data-name="{{ $party->name }}" data-phone="{{ $party->phone }}" data-address="{{ $party->address }}">{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="supplier_phone" id="supplier_phone" placeholder="Phone Number" required>
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" name="supplier_address" id="supplier_address" placeholder="Address" rows="2" required></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h4 class="mb-3">Invoice Details</h4>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" value="{{$nextInvoiceNumber}}" name="invoice_no" placeholder="Invoice No" required>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" name="invoice_date" id="invoice_date" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="customer_id" id="customer_id" placeholder="Customer ID" readonly>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="party_id" id="party_id" placeholder="Party ID" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Section: Products Table -->
        <div class="mb-6">
            <div class="table-responsive">
                <table class="product-table table-bordered">
                    <thead>
                        <tr>
                            <th width="1%">#</th>
                            <th width="19%">PRODUCT</th>
                            <th width="3%">TYPE</th>
                            <th width="2%">QUALITY</th>
                            <th width="5%">WARRANTY</th>
                            <th width="6%">COLOR</th>
                            <th width="10%">IMEI 1</th>
                            <th width="10%">IMEI 2</th>
                            <th width="8%">SERIAL</th>
                            <th width="3%">QTY</th>
                            <th width="3%">UNIT</th>
                            <th width="7%">PRICE/UNIT</th>
                            <th width="7%">SALE RATE</th>
                            <th width="7%">TAX</th>
                            <th width="8%">AMOUNT</th>
                            <th width="1%">*</th>
                        </tr>
                    </thead>
                    <tbody id="product-rows">
                        <!-- First product row -->
                        <tr class="product-row" data-index="0">
                            <td class="slx">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2 w-100">
                                    <div class="product-search-container flex-grow-1">
                                        <input type="text" class="form-control product-search" name="items[0][product_name]"
                                            placeholder="Search product..." required autocomplete="off">
                                        <input type="hidden" class="product-id" name="items[0][product_id]">
                                        <div class="product-suggestions"></div>
                                    </div>

                                    <div class="border-start ps-2 flex-shrink-0" style="width: 55px;">
                                        <input type="number" class="form-control battery-health text-center px-1" name="items[0][battery_health]"
                                            placeholder="BH" min="0" max="100" title="Battery Health Percentage">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select name="items[0][condition]" class="form-control">
                                    <option value="new">New</option>
                                    <option value="used" selected>Used</option>
                                    <option value="unused">Unused</option>
                                    <option value="refurbished">Refurbished</option>
                                    <option value="open box">Open Box</option>
                                </select>
                            </td>
                            <td>
                                <select name="items[0][quality]" class="form-control">
                                    <option value="fair">Fair</option>
                                    <option value="good">Good</option>
                                    <option value="super mint" selected>Super Mint</option>
                                    <option value="sealed pack">Sealed Pack</option>
                                </select>
                            </td>
                            <td>
                                <div class="warranty-wrapper px-1 d-flex align-items-center gap-1 w-100">
                                    <div class="flex-grow-1">
                                        <select name="items[0][warranty]" class="form-control warranty-select">
                                            <option value="out of warranty">No Warranty</option>
                                            <option value="1 week">1 Week</option>
                                            <option value="1 month">1 Month</option>
                                            <option value="2 months">2 Months</option>
                                            <option value="3 months">3 Months</option>
                                            <option value="4 months">4 Months</option>
                                            <option value="5 months">5 Months</option>
                                            <option value="6 months">6 Months</option>
                                            <option value="7 months">7 Months</option>
                                            <option value="8 months">8 Months</option>
                                            <option value="9 months">9 Months</option>
                                            <option value="10 months">10 Months</option>
                                            <option value="11 months">11 Months</option>
                                            <option value="1 year">1 Year</option>
                                            <option value="2 years">2 Years</option>
                                            <option value="3 years">3 Years</option>
                                            <option value="4 years">4 Years</option>
                                            <option value="5 years">5 Years</option>
                                        </select>
                                        <input type="date" name="items[0][activation_date]" class="form-control activation-date-input" style="display: none;">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0)" class="toggle-warranty-mode d-inline-block text-center text-primary" style="font-size: 0.85rem; text-decoration: none; padding: 0 4px;">📅</a>
                                    </div>
                                </div>
                            </td>
                            <td><input type="text" class="form-control color-input" name="items[0][color]" placeholder="Color"></td>
                            <td><input type="text" class="form-control" name="items[0][imei_one]" placeholder="IMEI 1"></td>
                            <td><input type="text" class="form-control" name="items[0][imei_two]" placeholder="IMEI 2"></td>
                            <td><input type="text" class="form-control" name="items[0][serial_number]" placeholder="Serial"></td>
                            <td><input type="number" class="form-control qty" name="items[0][qty]" min="1" value="1" required></td>
                            <td>
                                <select class="form-control" name="items[0][unit]">
                                    <option value="piece">Piece</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control price" name="items[0][price]" required></td>
                            <td>
                                <input type="number" step="0.01" class="form-control sale-rate" name="items[0][sell_rate]" required>
                                <div class="d-flex">
                                    <input type="hidden" step="0.01" class="form-control discount-percent" name="items[0][discount_percent]" placeholder="%" value="0" min="0" max="100">
                                    <input type="hidden" step="0.01" class="form-control discount-amount" name="items[0][discount_amount]" placeholder="₹" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <input type="number" step="0.01" class="form-control tax-percent" name="items[0][tax_percent]" placeholder="%" value="0" min="0" max="100">
                                    <input type="number" step="0.01" class="form-control tax-amount" name="items[0][tax_amount]" placeholder="₹" readonly>
                                </div>
                            </td>
                            <td><input type="number" step="0.01" class="form-control amount" name="items[0][amount]" readonly></td>
                            <td><span class="remove-product"><i class="fas fa-trash"></i></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button type="button" class="btn btn-primary btn-sm add-product-btn" id="add-product">
                    <i class="fas fa-plus me-2"></i>Add Product
                </button>
                <button type="button" class="btn btn-quick-entry btn-sm add-product-btn" id="open-quick-entry">
                    <i class="fas fa-bolt"></i>Quick Entry
                </button>
            </div>
        </div>

        <!-- Bottom Section: Payment and Totals -->
        <div class="row flex-between-end mb-6">
            <div class="col-md-4">
                <h4 class="mb-3">Payment Method</h4>
                <div class="mb-3">
                    <select class="form-control" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="net banking">Bank Transfer</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>

                <h4>Notes</h4>
                <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes..."></textarea>

                <h4 class="mt-3">Attachments</h4>
                <div class="photo-uploader">
                    <div class="photo-upload-item">
                        <input type="file" name="photo" accept="image/*">
                        <i class="fas fa-camera"></i>
                        <img style="display: none;">
                        <button type="button" class="remove-btn">×</button>
                    </div>
                    <div class="photo-upload-item">
                        <input type="file" name="document" accept="image/*">
                        <i class="fas fa-file-alt"></i>
                        <img style="display: none;">
                        <button type="button" class="remove-btn">×</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h4 class="mb-3">Invoice Summary</h4>
                <div class="amount-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">₹0.00</span>
                    <input type="hidden" name="subtotal" value="0">
                </div>
                <div class="amount-row">
                    <span>Total Discount:</span>
                    <span id="total-discount">₹0.00</span>
                    <input type="hidden" name="total_discount" value="0">
                </div>
                <div class="amount-row">
                    <span>Total Tax:</span>
                    <span id="total-tax">₹0.00</span>
                    <input type="hidden" name="total_tax" value="0">
                </div>
                <div class="amount-row">
                    <span>Shipping:</span>
                    <input type="number" step="0.01" class="form-control" name="shipping_amount" id="shipping-amount" value="0" min="0" style="width: 120px;">
                </div>
                <div class="amount-row amount-total">
                    <span>Total:</span>
                    <span id="total-amount">₹0.00</span>
                    <input type="hidden" name="total_amount" value="0">
                </div>
                <div class="amount-row">
                    <span>Paid:</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is-paid" name="is_paid">
                    </div>
                    <input type="number" step="0.01" class="form-control" name="paid" id="paid-amount" value="0" min="0" style="width: 120px;">
                </div>
                <div class="amount-row amount-total">
                    <span>Due:</span>
                    <span id="due-amount">₹0.00</span>
                    <input type="hidden" name="due_amount" value="0">
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Invoice</button>
        </div>
    </form>

    <!-- QUICK ENTRY MODAL -->
    <div class="modal fade" id="quickEntryModal" tabindex="-1" aria-labelledby="quickEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="quickEntryModalLabel">
                            <i class="fas fa-bolt me-2"></i>Quick Entry
                        </h5>
                        <small style="opacity:0.82; font-size:0.78rem;">Define product once — fill IMEI / Serial per unit</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 1.25rem;">
                    <!-- SECTION 1: Basic Info -->
                    <div class="qe-basic-section">
                        <div class="qe-section-title"><i class="fas fa-info-circle me-1"></i>Basic Info</div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label form-label-sm mb-1">Product</label>
                                <div style="position:relative;">
                                    <input type="text" id="qe-product-search" class="form-control form-control-sm" placeholder="Search product..." autocomplete="off">
                                    <input type="hidden" id="qe-product-id">
                                    <div class="product-suggestions" id="qe-product-suggestions" style="width:100%;"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Type / Condition</label>
                                <select id="qe-condition" class="form-select form-select-sm">
                                    <option value="new">New</option>
                                    <option value="used" selected>Used</option>
                                    <option value="unused">Unused</option>
                                    <option value="refurbished">Refurbished</option>
                                    <option value="open box">Open Box</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Quality</label>
                                <select id="qe-quality" class="form-select form-select-sm">
                                    <option value="fair">Fair</option>
                                    <option value="good">Good</option>
                                    <option value="super mint" selected>Super Mint</option>
                                    <option value="sealed pack">Sealed Pack</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label form-label-sm mb-0">Warranty</label>
                                    <a href="javascript:void(0)" id="qe-warranty-toggle" class="text-primary" style="font-size: 0.85rem; text-decoration: none;" title="Switch to Activation Date">📅</a>
                                </div>
                                <select id="qe-warranty" class="form-select form-select-sm">
                                    <option value="out of warranty">No Warranty</option>
                                    <option value="1 week">1 Week</option>
                                    <option value="1 month">1 Month</option>
                                    <option value="2 months">2 Months</option>
                                    <option value="3 months">3 Months</option>
                                    <option value="4 months">4 Months</option>
                                    <option value="5 months">5 Months</option>
                                    <option value="6 months">6 Months</option>
                                    <option value="7 months">7 Months</option>
                                    <option value="8 months">8 Months</option>
                                    <option value="9 months">9 Months</option>
                                    <option value="10 months">10 Months</option>
                                    <option value="11 months">11 Months</option>
                                    <option value="1 year">1 Year</option>
                                    <option value="2 years">2 Years</option>
                                    <option value="3 years">3 Years</option>
                                    <option value="4 years">4 Years</option>
                                    <option value="5 years">5 Years</option>
                                </select>
                                <input type="date" id="qe-activation-date" class="form-control form-control-sm" style="display: none;">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Color</label>
                                <input type="text" id="qe-color" class="form-control form-control-sm" placeholder="e.g. Phantom Black">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Battery Health (%)</label>
                                <input type="number" id="qe-battery-health" class="form-control form-control-sm" placeholder="BH" min="0" max="100">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Unit</label>
                                <select id="qe-unit" class="form-select form-select-sm">
                                    <option value="piece">Piece</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Price / Unit (₹)</label>
                                <input type="number" step="0.01" id="qe-price" class="form-control form-control-sm" placeholder="0.00">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Sale Rate (₹)</label>
                                <input type="number" step="0.01" id="qe-sell-rate" class="form-control form-control-sm" placeholder="0.00">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Tax (%)</label>
                                <input type="number" step="0.01" id="qe-tax-percent" class="form-control form-control-sm" placeholder="0" value="0" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: IMEI / Serial rows -->
                    <div class="qe-imei-section">
                        <div class="qe-section-title"><i class="fas fa-barcode me-1"></i>IMEI &amp; Serial Numbers</div>

                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                            <div class="qe-qty-badge">
                                <span>Qty:</span>
                                <input type="number" id="qe-qty" value="1" min="1" max="200">
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="qe-generate-rows">
                                <i class="fas fa-sync-alt me-1"></i>Generate Rows
                            </button>
                            <small class="text-muted" style="font-size:0.77rem;">
                                Change qty then click <strong>Generate Rows</strong> to rebuild the table.
                            </small>
                        </div>

                        <div id="qe-imei-rows-container">
                            <table class="qe-imei-table" id="qe-imei-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="38%">IMEI 1</th>
                                        <th width="38%">IMEI 2</th>
                                        <th width="19%">Serial No.</th>
                                    </tr>
                                </thead>
                                <tbody id="qe-imei-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="justify-content:space-between; align-items:center;">
                    <small class="text-muted" id="qe-summary-text"></small>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" id="qe-apply-btn">
                            <i class="fas fa-check me-1"></i>Apply to Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-admin.footer />
</div>
@endsection

@section('vendor-script')
    <script src="../../vendors/popper/popper.min.js"></script>
    <script src="../../vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../../vendors/anchorjs/anchor.min.js"></script>
    <script src="../../vendors/is/is.min.js"></script>
    <script src="../../vendors/fontawesome/all.min.js"></script>
    <script src="../../vendors/lodash/lodash.min.js"></script>
    <script src="../../vendors/list.js/list.min.js"></script>
    <script src="../../vendors/feather-icons/feather.min.js"></script>
    <script src="../../vendors/dayjs/dayjs.min.js"></script>
    <script src="../../assets/js/phoenix.js"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const form = e.target.form;
            if (!form) return;

            const inputs = Array.from(form.querySelectorAll('input, select, textarea'))
                .filter(el => !el.disabled && el.offsetParent !== null);

            const index = inputs.indexOf(e.target);
            if (index > -1) {
                e.preventDefault();
                const next = inputs[index + 1];
                if (next) {
                    next.focus();
                }
            }
        }
    });
});
</script>

<script>
class InvoiceManager {
    constructor() {
        this.productCounter = 0;
        this.debounceTimer = null;
        this.elements = {};
        this.init();
    }

    init() {
        try {
            this.cacheElements();
            this.bindEvents();
            this.initializeDefaults();
            this.initPhotoUploaders();
            this.setupCustomerMode();
        } catch (error) {
            this.handleError('Initialization failed', error);
        }
    }

    cacheElements() {
        const elementIds = [
            'invoice_date', 'invoice_for', 'supplier_name_input', 'supplier_name_select',
            'supplier_suggestions_results', 'supplier_phone', 'supplier_address', 'customer_id',
            'party_id', 'add-product', 'product-rows', 'shipping-amount', 'paid-amount',
            'is-paid', 'subtotal', 'total-discount', 'total-tax', 'total-amount', 'due-amount'
        ];

        elementIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                this.elements[id.replace('-', '_')] = element;
            }
        });

        this.elements.subtotal_input = document.querySelector('input[name="subtotal"]');
        this.elements.total_discount_input = document.querySelector('input[name="total_discount"]');
        this.elements.total_tax_input = document.querySelector('input[name="total_tax"]');
        this.elements.total_amount_input = document.querySelector('input[name="total_amount"]');
        this.elements.due_amount_input = document.querySelector('input[name="due_amount"]');
    }

    bindEvents() {
        this.elements.invoice_for?.addEventListener('change', this.handleModeChange.bind(this));
        this.elements.add_product?.addEventListener('click', this.addProductRow.bind(this));
        document.addEventListener('click', this.handleProductRemoval.bind(this));
        this.elements.is_paid?.addEventListener('change', this.handlePaidToggle.bind(this));
        this.elements.shipping_amount?.addEventListener('input', this.debounceCalculation.bind(this));
        this.elements.paid_amount?.addEventListener('input', this.debounceCalculation.bind(this));
        window.addEventListener('error', this.handleGlobalError.bind(this));
    }

    initializeDefaults() {
        if (this.elements.invoice_date) {
            this.elements.invoice_date.valueAsDate = new Date();
        }
        const firstRow = document.querySelector('.product-row');
        if (firstRow) {
            this.addProductRowListeners(firstRow);
        }
        this.calculateTotals();
    }

    handleModeChange() {
        const mode = this.elements.invoice_for?.value;
        if (mode === 'customer') {
            this.setupCustomerMode();
        } else {
            this.setupPartyMode();
        }
    }

    setupCustomerMode() {
        if (!this.elements.supplier_name_input || !this.elements.supplier_name_select || !this.elements.supplier_suggestions_results) return;
        this.toggleInputDisplay(true);
        this.clearAllFields();
        this.elements.supplier_suggestions_results.style.display = 'none';

        const newInput = this.elements.supplier_name_input.cloneNode(true);
        this.elements.supplier_name_input.parentNode.replaceChild(newInput, this.elements.supplier_name_input);
        this.elements.supplier_name_input = newInput;

        this.elements.supplier_name_input.addEventListener('input', (e) => {
            this.clearCustomerIds();
            clearTimeout(this.searchDebounceTimer);
            this.searchDebounceTimer = setTimeout(() => {
                this.searchCustomers(e.target.value);
            }, 250);
        });

        this.elements.supplier_name_input.addEventListener('blur', () => {
            setTimeout(() => {
                if (this.elements.supplier_suggestions_results) {
                    this.elements.supplier_suggestions_results.style.display = 'none';
                }
            }, 150);
        });
    }

    async searchCustomers(query) {
        const resultsContainer = this.elements.supplier_suggestions_results;
        if (!query || query.length < 1) {
            if (resultsContainer) resultsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`{{ route('distributor.api.search_customers') }}?q=${encodeURIComponent(query)}`);
            if (!response.ok) return;

            const customers = await response.json();
            resultsContainer.innerHTML = '';

            if (customers.length > 0) {
                customers.forEach(customer => {
                    const item = document.createElement('div');
                    item.className = 'p-2 cursor-pointer hover:bg-light border-bottom';
                    item.innerHTML = `
                        <strong>${this.escapeHtml(customer.name)}</strong> (${customer.mobile || 'N/A'})
                        <small class="d-block text-muted">${this.escapeHtml(customer.address || 'No Address')}</small>
                    `;
                    item.addEventListener('mousedown', () => this.selectExistingCustomer(customer));
                    resultsContainer.appendChild(item);
                });
                resultsContainer.style.display = 'block';
            } else {
                resultsContainer.style.display = 'none';
            }
        } catch (error) {
            console.error('Error searching customers:', error);
        }
    }

    selectExistingCustomer(customer) {
        if (this.elements.supplier_name_input) this.elements.supplier_name_input.value = customer.name;
        if (this.elements.supplier_phone) this.elements.supplier_phone.value = customer.mobile || '';
        if (this.elements.supplier_address) this.elements.supplier_address.value = customer.address || '';
        if (this.elements.customer_id) this.elements.customer_id.value = customer.customer_id || '';
        if (this.elements.party_id) this.elements.party_id.value = '';
        if (this.elements.supplier_suggestions_results) this.elements.supplier_suggestions_results.style.display = 'none';
    }

    setupPartyMode() {
        if (!this.elements.supplier_name_input || !this.elements.supplier_name_select || !this.elements.supplier_suggestions_results) return;
        this.toggleInputDisplay(false);
        this.clearAllFields();
        this.elements.supplier_suggestions_results.style.display = 'none';

        const newSelect = this.elements.supplier_name_select.cloneNode(true);
        this.elements.supplier_name_select.parentNode.replaceChild(newSelect, this.elements.supplier_name_select);
        this.elements.supplier_name_select = newSelect;

        this.elements.supplier_name_select.addEventListener('change', this.handlePartyChange.bind(this));
        new SlimSelect({ select: this.elements.supplier_name_select });
    }

    toggleInputDisplay(showInput) {
        const input = this.elements.supplier_name_input;
        const select = this.elements.supplier_name_select;
        if (showInput) {
            input.removeAttribute('hidden');
            input.setAttribute('required', 'required');
            select.setAttribute('hidden', 'hidden');
            select.removeAttribute('required');
            input.value = '';
        } else {
            input.setAttribute('hidden', 'hidden');
            input.removeAttribute('required');
            select.removeAttribute('hidden');
            select.setAttribute('required', 'required');
            select.value = '';
        }
    }

    handlePartyChange() {
        const selectedOption = this.elements.supplier_name_select.options[this.elements.supplier_name_select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            this.fillFieldsFromParty(selectedOption);
        } else {
            this.clearAllFields();
        }
    }

    clearAllFields() {
        const fields = ['supplier_phone', 'supplier_address', 'customer_id', 'party_id'];
        fields.forEach(field => {
            if (this.elements[field]) {
                this.elements[field].value = '';
            }
        });
    }

    clearCustomerIds() {
        if (this.elements.customer_id) this.elements.customer_id.value = '';
        if (this.elements.party_id) this.elements.party_id.value = '';
    }

    fillFieldsFromParty(option) {
        if (this.elements.supplier_phone) this.elements.supplier_phone.value = option.dataset.phone || '';
        if (this.elements.supplier_address) this.elements.supplier_address.value = option.dataset.address || '';
        if (this.elements.party_id) this.elements.party_id.value = option.value;
        if (this.elements.customer_id) this.elements.customer_id.value = '';
    }

    initPhotoUploaders() {
        document.querySelectorAll('.photo-upload-item').forEach(item => {
            this.setupPhotoUploader(item);
        });
    }

    setupPhotoUploader(item) {
        const input = item.querySelector('input');
        const icon = item.querySelector('i');
        const img = item.querySelector('img');
        const removeBtn = item.querySelector('.remove-btn');
        if (!input) return;
        item.addEventListener('click', (e) => {
            if (e.target !== removeBtn) input.click();
        });
        input.addEventListener('change', () => this.handleFileUpload(input, icon, img));
        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.resetFileUpload(input, icon, img);
            });
        }
    }

    handleFileUpload(input, icon, img) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (input.accept && input.accept.includes('image')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (img) { img.src = e.target.result; img.style.display = 'block'; }
                    if (icon) icon.style.display = 'none';
                };
                reader.onerror = () => this.handleError('Failed to read image file');
                reader.readAsDataURL(file);
            } else if (icon) {
                icon.className = 'fas fa-file-pdf';
                icon.style.display = 'block';
            }
        }
    }

    resetFileUpload(input, icon, img) {
        input.value = '';
        if (img) { img.src = ''; img.style.display = 'none'; }
        if (icon) {
            icon.style.display = 'block';
            icon.className = input.accept && input.accept.includes('image') ? 'fas fa-camera' : 'fas fa-file-alt';
        }
    }

    addProductRow() {
        try {
            this.productCounter++;
            const newRow = this.createProductRow(this.productCounter);
            this.elements.product_rows.appendChild(newRow);
            this.addProductRowListeners(newRow);
            return newRow;
        } catch (error) {
            this.handleError('Failed to add product row', error);
        }
    }

    createProductRow(counter) {
        const row = document.createElement('tr');
        row.className = 'product-row';
        row.dataset.index = counter;
        row.innerHTML = this.getProductRowHTML(counter);
        return row;
    }

    getProductRowHTML(counter) {
        return `
            <td class="slx">${counter + 1}</td>
            <td>
                <div class="d-flex align-items-center gap-2 w-100">
                    <div class="product-search-container flex-grow-1">
                        <input type="text" class="form-control product-search" name="items[${counter}][product_name]"
                               placeholder="Search product..." required autocomplete="off">
                        <input type="hidden" class="product-id" name="items[${counter}][product_id]">
                        <div class="product-suggestions"></div>
                    </div>
                    <div class="border-start ps-2 flex-shrink-0" style="width: 55px;">
                        <input type="number" class="form-control battery-health text-center px-1" name="items[${counter}][battery_health]"
                               placeholder="BH" min="0" max="100" title="Battery Health Percentage">
                    </div>
                </div>
            </td>
            <td>
                <select name="items[${counter}][condition]" class="form-control">
                    <option value="new">New</option>
                    <option value="used" selected>Used</option>
                    <option value="unused">Unused</option>
                    <option value="refurbished">Refurbished</option>
                    <option value="open box">Open Box</option>
                </select>
            </td>
            <td>
                <select name="items[${counter}][quality]" class="form-control">
                    <option value="fair">Fair</option>
                    <option value="good">Good</option>
                    <option value="super mint" selected>Super Mint</option>
                    <option value="sealed pack">Sealed Pack</option>
                </select>
            </td>
            <td>
                <div class="warranty-wrapper px-1 d-flex align-items-center gap-1 w-100">
                    <div class="flex-grow-1">
                        <select name="items[${counter}][warranty]" class="form-control warranty-select">
                            <option value="out of warranty">No Warranty</option>
                            <option value="1 week">1 Week</option>
                            <option value="1 month">1 Month</option>
                            <option value="2 months">2 Months</option>
                            <option value="3 months">3 Months</option>
                            <option value="4 months">4 Months</option>
                            <option value="5 months">5 Months</option>
                            <option value="6 months">6 Months</option>
                            <option value="7 months">7 Months</option>
                            <option value="8 months">8 Months</option>
                            <option value="9 months">9 Months</option>
                            <option value="10 months">10 Months</option>
                            <option value="11 months">11 Months</option>
                            <option value="1 year">1 Year</option>
                            <option value="2 years">2 Years</option>
                            <option value="3 years">3 Years</option>
                            <option value="4 years">4 Years</option>
                            <option value="5 years">5 Years</option>
                        </select>
                        <input type="date" name="items[${counter}][activation_date]" class="form-control activation-date-input" style="display: none;">
                    </div>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0)" class="toggle-warranty-mode d-inline-block text-center text-primary" style="font-size: 0.85rem; text-decoration: none; padding: 0 4px;">📅</a>
                    </div>
                </div>
            </td>
            <td><input type="text" class="form-control color-input" name="items[${counter}][color]" placeholder="Color"></td>
            <td><input type="text" class="form-control" name="items[${counter}][imei_one]" placeholder="IMEI 1"></td>
            <td><input type="text" class="form-control" name="items[${counter}][imei_two]" placeholder="IMEI 2"></td>
            <td><input type="text" class="form-control" name="items[${counter}][serial_number]" placeholder="Serial"></td>
            <td><input type="number" class="form-control qty" name="items[${counter}][qty]" min="1" value="1" required></td>
            <td>
                <select class="form-control" name="items[${counter}][unit]">
                    <option value="piece">Piece</option>
                    <option value="box">Box</option>
                    <option value="pack">Pack</option>
                </select>
            </td>
            <td><input type="number" step="0.01" class="form-control price" name="items[${counter}][price]" required></td>
            <td>
                <input type="number" step="0.01" class="form-control sale-rate" name="items[${counter}][sell_rate]" required>
                <div class="d-flex">
                    <input type="hidden" step="0.01" class="form-control discount-percent" name="items[${counter}][discount_percent]" placeholder="%" value="0" min="0" max="100">
                    <input type="hidden" step="0.01" class="form-control discount-amount" name="items[${counter}][discount_amount]" placeholder="₹" readonly>
                </div>
            </td>
            <td>
                <div class="d-flex">
                    <input type="number" step="0.01" class="form-control tax-percent" name="items[${counter}][tax_percent]" placeholder="%" value="0" min="0" max="100">
                    <input type="number" step="0.01" class="form-control tax-amount" name="items[${counter}][tax_amount]" placeholder="₹" readonly>
                </div>
            </td>
            <td><input type="number" step="0.01" class="form-control amount" name="items[${counter}][amount]" readonly></td>
            <td><span class="remove-product"><i class="fas fa-trash"></i></span></td>
        `;
    }

    addProductRowListeners(row) {
        const calculationInputs = row.querySelectorAll('.qty, .price, .discount-percent, .tax-percent');
        calculationInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.calculateRowAmount(row);
                this.debounceCalculation();
            });
        });
        this.setupProductSearch(row);
        this.setupWarrantyToggle(row);
    }

    setupWarrantyToggle(row) {
        const toggleBtn = row.querySelector('.toggle-warranty-mode');
        const selectEl = row.querySelector('.warranty-select');
        const dateEl = row.querySelector('.activation-date-input');
        if (!toggleBtn || !selectEl || !dateEl) return;

        toggleBtn.addEventListener('click', () => {
            if (dateEl.style.display === 'none') {
                selectEl.style.display = 'none';
                selectEl.value = '';
                dateEl.style.display = 'block';
                toggleBtn.textContent = '📋';
                toggleBtn.title = 'Switch to Warranty Period';
            } else {
                dateEl.style.display = 'none';
                dateEl.value = '';
                selectEl.style.display = 'block';
                selectEl.value = 'out of warranty';
                toggleBtn.textContent = '📅';
                toggleBtn.title = 'Switch to Activation Date';
            }
        });
    }

    setupProductSearch(row) {
        const productSearch = row.querySelector('.product-search');
        const suggestionsContainer = row.querySelector('.product-suggestions');
        const productIdInput = row.querySelector('.product-id');

        if (!productSearch || !suggestionsContainer || !productIdInput) return;

        productSearch.addEventListener('input', () => {
            clearTimeout(this.searchDebounceTimer);
            this.searchDebounceTimer = setTimeout(() => {
                this.handleProductSearch(productSearch, suggestionsContainer);
            }, 300);
        });

        productSearch.addEventListener('blur', () => {
            setTimeout(() => { suggestionsContainer.style.display = 'none'; }, 200);
        });
    }

    async handleProductSearch(input, container) {
        const searchTerm = input.value.toLowerCase().trim();
        if (searchTerm.length < 2) {
            container.style.display = 'none';
            container.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`{{ route('distributor.api.search_products') }}?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) return;

            const products = await response.json();
            this.displayProductSuggestions(products, container, input);
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }

    displayProductSuggestions(products, container, inputEl) {
        if (!products || products.length === 0) {
            container.style.display = 'none';
            container.innerHTML = '';
            return;
        }

        const row = inputEl.closest('.product-row');
        const html = products.map(product => `
            <div class="product-suggestion-item"
                data-id="${product.id}"
                data-color="${this.escapeHtml(product.color)}"
                data-name="${this.escapeHtml(product.name)}">
                ${this.escapeHtml(product.name)}
            </div>
        `).join('');

        container.innerHTML = html;
        container.style.display = 'block';

        container.querySelectorAll('.product-suggestion-item').forEach(item => {
            item.addEventListener('click', () => {
                const productIdInput = row.querySelector('.product-id');
                const productColorInput = row.querySelector('.color-input');

                inputEl.value = item.dataset.name;
                if (productIdInput) productIdInput.value = item.dataset.id;
                if (productColorInput && item.dataset.color) productColorInput.value = item.dataset.color;

                container.style.display = 'none';
                this.calculateRowAmount(row);
                this.debounceCalculation();
            });
        });
    }

    handleProductRemoval(e) {
        if (!e.target.closest('.remove-product')) return;
        const row = e.target.closest('.product-row');
        if (row) {
            row.remove();
            this.debounceCalculation();
        }
    }

    calculateRowAmount(row) {
        try {
            const qty = this.parseFloatSafe(row.querySelector('.qty')?.value) || 0;
            const price = this.parseFloatSafe(row.querySelector('.price')?.value) || 0;
            const discountPercent = this.parseFloatSafe(row.querySelector('.discount-percent')?.value) || 0;
            const taxPercent = this.parseFloatSafe(row.querySelector('.tax-percent')?.value) || 0;
            const subtotal = qty * price;
            const discountAmount = subtotal * (discountPercent / 100);
            const taxableAmount = subtotal - discountAmount;
            const taxAmount = taxableAmount * (taxPercent / 100);
            const amount = taxableAmount + taxAmount;
            this.updateRowDisplays(row, {
                discountAmount: discountAmount.toFixed(2),
                taxAmount: taxAmount.toFixed(2),
                amount: amount.toFixed(2)
            });
            return { subtotal, discountAmount, taxAmount, amount };
        } catch (error) {
            this.handleError('Failed to calculate row amount', error);
            return { subtotal: 0, discountAmount: 0, taxAmount: 0, amount: 0 };
        }
    }

    updateRowDisplays(row, values) {
        const discountAmountEl = row.querySelector('.discount-amount');
        const taxAmountEl = row.querySelector('.tax-amount');
        const amountEl = row.querySelector('.amount');
        if (discountAmountEl) discountAmountEl.value = values.discountAmount;
        if (taxAmountEl) taxAmountEl.value = values.taxAmount;
        if (amountEl) amountEl.value = values.amount;
    }

    debounceCalculation() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.calculateTotals(), 150);
    }

    calculateTotals() {
        try {
            let totals = { subtotal: 0, totalDiscount: 0, totalTax: 0, totalAmount: 0 };
            document.querySelectorAll('.product-row').forEach(row => {
                const rowAmounts = this.calculateRowAmount(row);
                totals.subtotal += rowAmounts.subtotal;
                totals.totalDiscount += rowAmounts.discountAmount;
                totals.totalTax += rowAmounts.taxAmount;
                totals.totalAmount += rowAmounts.amount;
            });
            const shipping = this.parseFloatSafe(this.elements.shipping_amount?.value) || 0;
            const finalTotal = totals.totalAmount + shipping;
            this.updatePaidAmount(finalTotal);
            this.updateTotalDisplays(totals, shipping, finalTotal);
        } catch (error) {
            this.handleError('Failed to calculate totals', error);
        }
    }

    updatePaidAmount(finalTotal) {
        const isPaid = this.elements.is_paid?.checked;
        if (isPaid && this.elements.paid_amount) {
            this.elements.paid_amount.value = finalTotal.toFixed(2);
        }
    }

    updateTotalDisplays(totals, shipping, finalTotal) {
        const paid = this.parseFloatSafe(this.elements.paid_amount?.value) || 0;
        const due = finalTotal - paid;
        this.updateElementText(this.elements.subtotal, '₹' + totals.subtotal.toFixed(2));
        this.updateElementText(this.elements.total_discount, '₹' + totals.totalDiscount.toFixed(2));
        this.updateElementText(this.elements.total_tax, '₹' + totals.totalTax.toFixed(2));
        this.updateElementText(this.elements.total_amount, '₹' + finalTotal.toFixed(2));
        this.updateElementText(this.elements.due_amount, '₹' + due.toFixed(2));
        this.updateElementValue(this.elements.subtotal_input, totals.subtotal.toFixed(2));
        this.updateElementValue(this.elements.total_discount_input, totals.totalDiscount.toFixed(2));
        this.updateElementValue(this.elements.total_tax_input, totals.totalTax.toFixed(2));
        this.updateElementValue(this.elements.total_amount_input, finalTotal.toFixed(2));
        this.updateElementValue(this.elements.due_amount_input, due.toFixed(2));
    }

    handlePaidToggle() {
        const isPaid = this.elements.is_paid?.checked;
        const paidInput = this.elements.paid_amount;
        if (!paidInput) return;
        if (isPaid) {
            const totalText = this.elements.total_amount?.textContent || '₹0';
            const total = this.parseFloatSafe(totalText.replace('₹', ''));
            paidInput.value = total.toFixed(2);
            paidInput.readOnly = true;
        } else {
            paidInput.value = '0';
            paidInput.readOnly = false;
        }
        this.calculateTotals();
    }

    parseFloatSafe(value) {
        const parsed = parseFloat(value);
        return isNaN(parsed) ? 0 : parsed;
    }

    updateElementText(element, text) {
        if (element) element.textContent = text;
    }

    updateElementValue(element, value) {
        if (element) element.value = value;
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    handleError(message, error = null) {
        console.error(message, error);
    }

    handleGlobalError(event) {
        this.handleError('Unexpected error occurred', event.error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    try {
        window.invoiceManager = new InvoiceManager();
    } catch (error) {
        console.error('Failed to initialize Invoice Manager:', error);
    }
});
</script>

<script>
(function () {
    'use strict';

    class QuickEntryManager {
        constructor() {
            this.searchDebounce = null;
            this.selectedProduct = null;
            this.init();
        }

        init() {
            document.addEventListener('DOMContentLoaded', () => this.bindEvents());
            if (document.readyState !== 'loading') this.bindEvents();
        }

        bindEvents() {
            document.getElementById('open-quick-entry')?.addEventListener('click', () => this.openModal());
            document.getElementById('qe-generate-rows')?.addEventListener('click', () => this.generateImeiRows());
            document.getElementById('qe-qty')?.addEventListener('change', () => this.generateImeiRows());

            document.getElementById('qe-warranty-toggle')?.addEventListener('click', (e) => {
                const selectEl = document.getElementById('qe-warranty');
                const dateEl = document.getElementById('qe-activation-date');
                if (dateEl.style.display === 'none') {
                    selectEl.style.display = 'none';
                    selectEl.value = '';
                    dateEl.style.display = 'block';
                    e.target.textContent = '📋';
                    e.target.title = 'Switch to Warranty Period';
                } else {
                    dateEl.style.display = 'none';
                    dateEl.value = '';
                    selectEl.style.display = 'block';
                    selectEl.value = 'out of warranty';
                    e.target.textContent = '📅';
                    e.target.title = 'Switch to Activation Date';
                }
            });

            document.getElementById('qe-product-search')?.addEventListener('input', (e) => {
                clearTimeout(this.searchDebounce);
                this.searchDebounce = setTimeout(() => this.handleSearch(e.target.value), 280);
            });

            document.getElementById('qe-product-search')?.addEventListener('blur', () => {
                setTimeout(() => {
                    const s = document.getElementById('qe-product-suggestions');
                    if (s) s.style.display = 'none';
                }, 200);
            });

            document.getElementById('qe-apply-btn')?.addEventListener('click', () => this.applyToInvoice());
        }

        openModal() {
            this.resetModal();
            const modalEl = document.getElementById('quickEntryModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            this.generateImeiRows();
        }

        resetModal() {
            this.selectedProduct = null;
            const ids = ['qe-product-search','qe-product-id','qe-color','qe-price','qe-sell-rate','qe-tax-percent','qe-battery-health','qe-activation-date'];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = id === 'qe-tax-percent' ? '0' : '';
            });
            ['qe-condition','qe-quality','qe-warranty','qe-unit'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.selectedIndex = id === 'qe-condition' ? 1 : (id === 'qe-quality' ? 2 : 0);
            });

            const selectEl = document.getElementById('qe-warranty');
            const dateEl = document.getElementById('qe-activation-date');
            const toggleBtn = document.getElementById('qe-warranty-toggle');
            if (selectEl && dateEl && toggleBtn) {
                dateEl.style.display = 'none';
                selectEl.style.display = 'block';
                toggleBtn.textContent = '📅';
                toggleBtn.title = 'Switch to Activation Date';
            }

            document.getElementById('qe-qty').value = 1;
            document.getElementById('qe-summary-text').textContent = '';
            const sug = document.getElementById('qe-product-suggestions');
            if (sug) { sug.style.display = 'none'; sug.innerHTML = ''; }
        }

        async handleSearch(term) {
            const container = document.getElementById('qe-product-suggestions');
            if (!container) return;
            const q = term.toLowerCase().trim();
            if (q.length < 2) { container.style.display = 'none'; container.innerHTML = ''; return; }

            try {
                const response = await fetch(`{{ route('distributor.api.search_products') }}?q=${encodeURIComponent(q)}`);
                if (!response.ok) return;

                const results = await response.json();
                if (results.length === 0) { container.style.display = 'none'; container.innerHTML = ''; return; }

                container.innerHTML = results.map(p => {
                    return `<div class="product-suggestion-item"
                                data-id="${p.id}"
                                data-color="${p.color || ''}"
                                data-name="${p.name}">${p.name}</div>`;
                }).join('');
                container.style.display = 'block';

                container.querySelectorAll('.product-suggestion-item').forEach(item => {
                    item.addEventListener('click', () => {
                        this.selectedProduct = {
                            id: item.dataset.id,
                            name: item.dataset.name,
                            color: item.dataset.color,
                        };
                        document.getElementById('qe-product-search').value = item.dataset.name;
                        document.getElementById('qe-product-id').value = item.dataset.id;
                        const colorField = document.getElementById('qe-color');
                        if (colorField && !colorField.value) colorField.value = item.dataset.color;
                        container.style.display = 'none';
                    });
                });
            } catch (error) {
                console.error('Error fetching quick entry products:', error);
            }
        }

        generateImeiRows() {
            const qty = Math.max(1, Math.min(200, parseInt(document.getElementById('qe-qty').value) || 1));
            document.getElementById('qe-qty').value = qty;

            const tbody = document.getElementById('qe-imei-tbody');
            if (!tbody) return;

            const existing = this.collectImeiData();
            tbody.innerHTML = '';
            for (let i = 0; i < qty; i++) {
                const ex = existing[i] || {};
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="row-num">${i + 1}</span></td>
                    <td><input type="text" placeholder="IMEI 1" class="qe-imei1" value="${ex.imei1 || ''}"></td>
                    <td><input type="text" placeholder="IMEI 2" class="qe-imei2" value="${ex.imei2 || ''}"></td>
                    <td><input type="text" placeholder="Serial" class="qe-serial" value="${ex.serial || ''}"></td>
                `;
                tbody.appendChild(tr);
            }

            this.updateSummary(qty);
        }

        collectImeiData() {
            const rows = document.querySelectorAll('#qe-imei-tbody tr');
            return Array.from(rows).map(row => ({
                imei1: row.querySelector('.qe-imei1')?.value || '',
                imei2: row.querySelector('.qe-imei2')?.value || '',
                serial: row.querySelector('.qe-serial')?.value || '',
            }));
        }

        updateSummary(qty) {
            const el = document.getElementById('qe-summary-text');
            if (el) el.textContent = `${qty} unit${qty > 1 ? 's' : ''} will be added as separate rows.`;
        }

        applyToInvoice() {
            const im = window.invoiceManager;
            if (!im) { alert('Invoice manager not ready.'); return; }

            const productName = document.getElementById('qe-product-search').value.trim();
            const productId = document.getElementById('qe-product-id').value.trim();
            const condition = document.getElementById('qe-condition').value;
            const quality = document.getElementById('qe-quality').value;
            const warranty = document.getElementById('qe-warranty').value;
            const activationDate = document.getElementById('qe-activation-date').value;
            const batteryHealth = document.getElementById('qe-battery-health').value.trim();
            const color = document.getElementById('qe-color').value.trim();
            const unit = document.getElementById('qe-unit').value;
            const price = document.getElementById('qe-price').value.trim();
            const sellRate = document.getElementById('qe-sell-rate').value.trim();
            const taxPercent = document.getElementById('qe-tax-percent').value.trim() || '0';

            if (!productName) { alert('Please select a product first.'); return; }

            const imeiData = this.collectImeiData();
            if (imeiData.length === 0) { alert('Please generate at least 1 row.'); return; }

            const isDateMode = document.getElementById('qe-activation-date').style.display !== 'none';

            imeiData.forEach((unit_data) => {
                im.productCounter++;
                const counter = im.productCounter;

                const newRow = im.createProductRow(counter);
                im.elements.product_rows.appendChild(newRow);
                im.addProductRowListeners(newRow);

                const searchInput = newRow.querySelector('.product-search');
                const idInput = newRow.querySelector('.product-id');
                const colorInput = newRow.querySelector('.color-input');

                if (searchInput) searchInput.value = productName;
                if (idInput) idInput.value = productId;
                if (colorInput) colorInput.value = color;

                this.setSelectValue(newRow, `[name="items[${counter}][condition]"]`, condition);
                this.setSelectValue(newRow, `[name="items[${counter}][quality]"]`, quality);
                this.setSelectValue(newRow, `[name="items[${counter}][unit]"]`, unit);

                if (isDateMode) {
                    const toggleBtn = newRow.querySelector('.toggle-warranty-mode');
                    if (toggleBtn) toggleBtn.click();
                    const dateInput = newRow.querySelector('.activation-date-input');
                    if (dateInput) dateInput.value = activationDate;
                } else {
                    this.setSelectValue(newRow, `[name="items[${counter}][warranty]"]`, warranty);
                }

                const bhInput = newRow.querySelector('.battery-health');
                if (bhInput) bhInput.value = batteryHealth;

                const imei1Field = newRow.querySelector(`[name="items[${counter}][imei_one]"]`);
                const imei2Field = newRow.querySelector(`[name="items[${counter}][imei_two]"]`);
                const serialField = newRow.querySelector(`[name="items[${counter}][serial_number]"]`);
                if (imei1Field) imei1Field.value = unit_data.imei1;
                if (imei2Field) imei2Field.value = unit_data.imei2;
                if (serialField) serialField.value = unit_data.serial;

                const qtyInput = newRow.querySelector('.qty');
                const priceInput = newRow.querySelector('.price');
                const saleInput = newRow.querySelector('.sale-rate');
                const taxInput = newRow.querySelector('.tax-percent');
                if (qtyInput) qtyInput.value = '1';
                if (priceInput) priceInput.value = price;
                if (saleInput) saleInput.value = sellRate;
                if (taxInput) taxInput.value = taxPercent;

                im.calculateRowAmount(newRow);
            });

            im.debounceCalculation();
            this.renumberRows();

            const modalEl = document.getElementById('quickEntryModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }

        setSelectValue(row, selector, value) {
            const el = row.querySelector(selector);
            if (!el) return;
            const opt = Array.from(el.options).find(o => o.value === value);
            if (opt) el.value = value;
        }

        renumberRows() {
            document.querySelectorAll('#product-rows .product-row').forEach((row, idx) => {
                const badge = row.querySelector('.slx');
                if (badge) badge.textContent = idx + 1;
            });
        }
    }

    window.quickEntryManager = new QuickEntryManager();
})();
</script>
@endsection
