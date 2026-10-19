@extends('layouts.navbarless')

@section('title', 'Edit Sale Invoice')

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
    .customer-info, .invoice-info {
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
        border-bottom: 1px solid #eee;
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
    .modal-dialog {
        max-width: 800px;
    }
    .search-item {
        cursor: pointer;
        padding: 8px;
        border-bottom: 1px solid #eee;
    }
    .search-item:hover {
        background-color: #f8f9fa;
    }
    .search-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        margin-right: 10px;
    }
    .search-item-stock {
        font-weight: bold;
        color: #28a745;
        white-space: nowrap;
        margin-left: 15px;
    }
    .search-item-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }
    .search-item-details {
        flex-grow: 1;
    }
    .modal-header .btn-close {
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
    }
</style>
@endsection

@section('content')
<div class="content">
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

    @if (session('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Error</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
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

    <form method="POST" action="{{ route('distributor.sale.update', $invoice->invoice_id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="invoice_type" value="sale">

        <!-- Top Section: Customer and Invoice Info -->
        <div class="row flex-between-end mb-6">
            <div class="col-md-4">
                <h4 class="mb-3">Customer Details</h4>
                <div class="row g-2">
                    <div class="col-md-2">
                        <select name="invoice_for" id="invoice_for" class="form-control">
                            <option value="party" {{ $invoice->invoice_for === 'party' ? 'selected' : '' }}>Party</option>
                            <option value="customer" {{ $invoice->invoice_for === 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="customer_name" id="customer_name_input"
                            value="{{ $invoice->name }}" placeholder="Customer Name" autocomplete="off"
                            {{ $invoice->invoice_for !== 'customer' ? 'style=display:none;' : '' }}>

                        <div id="customer_suggestions_results"
                            style="position: absolute; z-index: 1000; width: 500px; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; border-top: none; display: none;">
                        </div>

                        <select class="form-control" name="customer_name_select" id="customer_name_select"
                            {{ $invoice->invoice_for === 'customer' ? 'style=display:none;' : '' }}>
                            <option value="">Select a Party</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->party_id }}" data-name="{{ $party->name }}" data-phone="{{ $party->phone }}" data-address="{{ $party->address }}"
                                    {{ $invoice->party_id == $party->party_id ? 'selected' : '' }}>
                                    {{ $party->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="customer_phone" id="customer_phone"
                            value="{{ $invoice->mobile }}" placeholder="Phone Number" required>
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" name="customer_address" id="customer_address"
                            placeholder="Address" rows="2" required>{{ $invoice->address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h4 class="mb-3">Invoice Details</h4>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" value="{{ $invoice->invoice_no }}" name="invoice_no" placeholder="Invoice No" readonly>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" name="invoice_date" id="invoice_date"
                            value="{{ $invoice->invoice_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <input type="text" class="form-control" name="customer_id" id="customer_id"
                            value="{{ $invoice->customer_id }}" placeholder="Customer ID" readonly>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="party_id" id="party_id"
                            value="{{ $invoice->party_id }}" placeholder="Party ID" readonly>
                    </div>

                    <div class="col-md-6">
                        <select class="form-select" id="posEmployee" name="employee_id">
                            <option value="">Select Employee (No Attributed Incentive)</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}" {{ $invoice->employee_id == $employee->employee_id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Section: Products Table -->
        <div class="mb-6">
            <div class="d-flex">
                <button type="button" class="btn btn-primary btn-sm add-product-btn me-2" id="add-product">
                    <i class="fas fa-plus me-2"></i>Add Product
                </button>
                <button type="button" class="btn btn-success btn-sm add-product-btn me-2" id="search-by-imei" data-bs-toggle="modal" data-bs-target="#productSearchModal">
                    <i class="fas fa-barcode me-2"></i>Scan
                </button>
            </div>

            <div class="table-responsive">
                <table class="product-table table-bordered">
                    <thead>
                        <tr>
                            <th width="19%">PRODUCT</th>
                            <th width="3%">CONDITION</th>
                            <th width="2%">QUALITY</th>
                            <th width="3%">WARRANTY</th>
                            <th width="5%">COLOR</th>
                            <th width="8%">IMEI 1</th>
                            <th width="8%">IMEI 2</th>
                            <th width="8%">SERIAL</th>
                            <th width="5%">QTY</th>
                            <th width="5%">UNIT</th>
                            <th width="5%">PRICE/UNIT</th>
                            <th width="10%">DISCOUNT</th>
                            <th width="10%">TAX</th>
                            <th width="8%">AMOUNT</th>
                            <th width="1%"></th>
                        </tr>
                    </thead>
                    <tbody id="product-rows">
                        @php
                            $globalIndex = 0;
                        @endphp
                        @foreach($invoice->items as $index => $item)
                        <tr class="product-row" data-index="{{ $index }}">
                            <td>
                                <div class="d-flex align-items-center gap-2 w-100">
                                    <div class="product-search-container flex-grow-1 ">
                                        <input type="text" class="form-control product-search" name="items[{{ $index }}][product_name]"
                                            value="{{ $item->inventory->product_name }}" placeholder="Search inventory..." required autocomplete="off">
                                        <input type="hidden" class="inventory-id" name="items[{{ $index }}][inventory_id]" value="{{ $item->inventory_id }}">
                                        <div class="product-suggestions"></div>
                                    </div>
                                    @if(!is_null($item->inventory->battery_health) && $item->inventory->battery_health !== '')
                                    <div class="border-start ps-2 flex-shrink-0" style="width: 55px;">
                                        <input type="text" class="form-control battery-health text-center px-1 fw-bold text-success"
                                            value="{{ $item->inventory->battery_health }}%" readonly title="Battery Health">
                                    </div>
                                    @else
                                    <div class="border-start ps-2 flex-shrink-0 battery-health-wrapper" style="width: 55px; display: none;">
                                        <input type="text" class="form-control battery-health text-center px-1 fw-bold text-success" value="" readonly title="Battery Health">
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control condition" name="items[{{ $index }}][condition]"
                                    value="{{ $item->inventory->product_type }}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control quality" name="items[{{ $index }}][quality]"
                                    value="{{ $item->inventory->health }}" readonly>
                            </td>
                            <td>
                                @if($item->inventory->activation_date)
                                    <input type="text" class="form-control warranty text-primary fw-600" name="items[{{ $index }}][warranty]"
                                        value="📅 {{ \Carbon\Carbon::parse($item->inventory->activation_date)->format('d/m/Y') }}" readonly title="Activation Date">
                                @else
                                    <input type="text" class="form-control warranty" name="items[{{ $index }}][warranty]"
                                        value="{{ $item->inventory->warranty }}" readonly>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control color" name="items[{{ $index }}][color]"
                                    value="{{ $item->inventory->color }}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control imei_one" name="items[{{ $index }}][imei_one]"
                                    value="{{ $item->inventory->imei_one }}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control imei_two" name="items[{{ $index }}][imei_two]"
                                    value="{{ $item->inventory->imei_two }}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control serial_number" name="items[{{ $index }}][serial_number]"
                                    value="{{ $item->inventory->serial_number }}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control qty" name="items[{{ $index }}][qty]"
                                    min="1" value="{{ $item->qty }}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control unit" name="items[{{ $index }}][unit]"
                                    value="{{ $item->inventory->unit }}" readonly>
                            </td>
                            <td>
                                <input type="number" step="0.001" class="form-control price" name="items[{{ $index }}][price]"
                                    value="{{ $item->rate }}" required>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <input type="number" step="0.01" class="form-control discount-percent" name="items[{{ $index }}][discount_percent]"
                                        value="{{ $item->discount_percent }}" placeholder="%" min="0" max="100" readonly>
                                    <input type="number" step="0.001" class="form-control discount-amount" name="items[{{ $index }}][discount_amount]"
                                        value="{{ $item->discount }}" placeholder="₹" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <input type="number" step="0.01" class="form-control tax-percent" name="items[{{ $index }}][tax_percent]"
                                        value="{{ $item->tax_percent }}" placeholder="%" min="0" max="100">
                                    <input type="number" step="0.001" class="form-control tax-amount" name="items[{{ $index }}][tax_amount]"
                                        value="{{ $item->tax }}" placeholder="₹" readonly>
                                </div>
                            </td>
                            <td>
                                <input type="number" step="0.001" class="form-control amount" name="items[{{ $index }}][amount]"
                                    value="{{ $item->amount }}" readonly>
                            </td>
                            <td>
                                <span class="remove-product"><i class="fas fa-trash"></i></span>
                            </td>
                        </tr>
                        @php
                            $globalIndex++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Section: Payment and Totals -->
        <div class="row flex-between-end mb-6">
            <div class="col-md-4">
                <h4 class="mb-3">Payment Method</h4>
                <div class="mb-3">
                    <select class="form-control" name="payment_method" required>
                        <option value="cash" {{ $invoice->payment_type === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="upi" {{ $invoice->payment_type === 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="card" {{ $invoice->payment_type === 'card' ? 'selected' : '' }}>Card</option>
                        <option value="cheque" {{ $invoice->payment_type === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="net banking" {{ $invoice->payment_type === 'net banking' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="credit" {{ $invoice->payment_type === 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>

                <h4>Notes</h4>
                <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes...">{{ $invoice->notes }}</textarea>

                <h4 class="mt-3">Attachments</h4>
                <div class="photo-uploader">
                    <div class="photo-upload-item">
                        <input type="file" name="photo" accept="image/*">
                        @if($invoice->photo_url)
                            <img src="{{ asset('storage/' . $invoice->photo_url) }}" style="display: block;">
                            <i class="fas fa-camera" style="display: none;"></i>
                        @else
                            <i class="fas fa-camera"></i>
                            <img style="display: none;">
                        @endif
                        <button type="button" class="remove-btn">×</button>
                    </div>
                    <div class="photo-upload-item">
                        <input type="file" name="document" accept="image/*">
                        @if($invoice->doc_url)
                            <img src="{{ asset('storage/' . $invoice->doc_url) }}" style="display: block;">
                            <i class="fas fa-file-alt" style="display: none;"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                            <img style="display: none;">
                        @endif
                        <button type="button" class="remove-btn">×</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h4 class="mb-3">Invoice Summary</h4>
                <div class="amount-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    <input type="hidden" name="subtotal" value="{{ $invoice->subtotal }}">
                </div>
                <div class="amount-row">
                    <span>Total Discount:</span>
                    <span id="total-discount">₹{{ number_format($invoice->total_discount, 2) }}</span>
                    <input type="hidden" name="total_discount" value="{{ $invoice->total_discount }}">
                </div>
                <div class="amount-row">
                    <span>Total Tax:</span>
                    <span id="total-tax">₹{{ number_format($invoice->total_tax, 2) }}</span>
                    <input type="hidden" name="total_tax" value="{{ $invoice->total_tax }}">
                </div>
                <div class="amount-row">
                    <span>Shipping:</span>
                    <input type="number" step="0.001" class="form-control" name="shipping_amount" id="shipping-amount"
                        value="{{ $invoice->shipping_amount }}" min="0" style="width: 120px;">
                </div>
                <div class="amount-row amount-total">
                    <span>Total:</span>
                    <span id="total-amount">₹{{ number_format($invoice->total_amount, 2) }}</span>
                    <input type="hidden" name="total_amount" value="{{ $invoice->total_amount }}">
                </div>
                <div class="amount-row">
                    <span>Paid:</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is-paid" name="is_paid" {{ $invoice->paid >= $invoice->total_amount ? 'checked' : '' }}>
                    </div>
                    <input type="number" step="0.001" class="form-control" name="paid" id="paid-amount"
                        value="{{ $invoice->paid }}" min="0" style="width: 120px;">
                </div>
                <div class="amount-row amount-total">
                    <span>Due:</span>
                    <span id="due-amount">₹{{ number_format($invoice->due_amount, 2) }}</span>
                    <input type="hidden" name="due_amount" value="{{ $invoice->due_amount }}">
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Invoice</button>
        </div>
    </form>

    <!-- Product Search Modal -->
    <div class="modal fade" id="productSearchModal" tabindex="-1" aria-labelledby="productSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productSearchModalLabel">Scan IMEI/Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="inventorySearchInput" placeholder="Scan IMEI or Serial Number" autofocus autocomplete="off">
                    </div>
                    <div id="inventorySearchResults" style="max-height: 400px; overflow-y: auto;">
                        <!-- API results rendered here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
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
const globalIndex = {{ $globalIndex }};

class InvoiceManager {
    constructor() {
        this.productCounter = globalIndex;
        this.selectedInventoryIds = new Set();
        this.debounceTimers = new Map();

        this.elements = {
            invoiceDate: document.getElementById('invoice_date'),
            invoiceFor: document.getElementById('invoice_for'),
            customerInput: document.getElementById('customer_name_input'),
            customerSelect: document.getElementById('customer_name_select'),
            customerSuggestionsResults: document.getElementById('customer_suggestions_results'),
            phoneInput: document.getElementById('customer_phone'),
            addressInput: document.getElementById('customer_address'),
            customerIdInput: document.getElementById('customer_id'),
            partyIdInput: document.getElementById('party_id'),
            addProductBtn: document.getElementById('add-product'),
            productRows: document.getElementById('product-rows'),
            shippingAmount: document.getElementById('shipping-amount'),
            paidAmount: document.getElementById('paid-amount'),
            isPaid: document.getElementById('is-paid'),
            searchModal: document.getElementById('productSearchModal'),
            searchInput: document.getElementById('inventorySearchInput'),
            searchResults: document.getElementById('inventorySearchResults'),
            subtotal: document.getElementById('subtotal'),
            totalDiscount: document.getElementById('total-discount'),
            totalTax: document.getElementById('total-tax'),
            totalAmount: document.getElementById('total-amount'),
            dueAmount: document.getElementById('due-amount')
        };

        this.init();
    }

    init() {
        try {
            this.setupEventListeners();
            this.initPhotoUploaders();

            const productRows = document.querySelectorAll('.product-row');
            productRows.forEach(row => {
                const invId = row.querySelector('.inventory-id')?.value;
                if (invId) {
                    this.selectedInventoryIds.add(String(invId));
                }
                this.addProductRowListeners(row);
            });

            if (this.elements.invoiceFor?.value === 'customer') {
                this.setupCustomerMode();
            } else {
                this.setupPartyMode();
            }

            this.calculateTotals();
        } catch (error) {
            console.error('Error initializing InvoiceManager:', error);
        }
    }

    setupEventListeners() {
        this.elements.invoiceFor?.addEventListener('change', () => {
            const mode = this.elements.invoiceFor.value;
            if (mode === 'customer') {
                this.setupCustomerMode();
            } else {
                this.setupPartyMode();
            }
        });

        this.elements.addProductBtn?.addEventListener('click', () => this.addProductRow());

        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-product')) {
                this.removeProductRow(e.target.closest('.product-row'));
            }
        });

        this.elements.isPaid?.addEventListener('change', () => this.handlePaidToggle());
        this.elements.shippingAmount?.addEventListener('input', () => this.calculateTotals());
        this.elements.paidAmount?.addEventListener('input', () => this.calculateTotals());

        this.setupSearchModal();
    }

    debounce(key, func, delay = 350) {
        if (this.debounceTimers.has(key)) {
            clearTimeout(this.debounceTimers.get(key));
        }

        const timeoutId = setTimeout(() => {
            func();
            this.debounceTimers.delete(key);
        }, delay);

        this.debounceTimers.set(key, timeoutId);
    }

    clearFields() {
        const fields = [
            this.elements.phoneInput,
            this.elements.addressInput,
            this.elements.customerIdInput,
            this.elements.partyIdInput
        ];

        fields.forEach(field => {
            if (field) field.value = '';
        });
    }

    clearCustomerIds() {
        if (this.elements.customerIdInput) this.elements.customerIdInput.value = '';
        if (this.elements.partyIdInput) this.elements.partyIdInput.value = '';
    }

    fillFieldsFromParty(option) {
        if (!option) return;

        const fieldMappings = [
            [this.elements.phoneInput, option.dataset.phone],
            [this.elements.addressInput, option.dataset.address],
            [this.elements.partyIdInput, option.value]
        ];

        fieldMappings.forEach(([element, value]) => {
            if (element) element.value = value || '';
        });

        if (this.elements.customerIdInput) this.elements.customerIdInput.value = '';
    }

    setupCustomerMode() {
        if (!this.elements.customerInput || !this.elements.customerSelect || !this.elements.customerSuggestionsResults) return;

        this.elements.customerInput.style.display = 'block';
        this.elements.customerInput.setAttribute('required', 'required');
        this.elements.customerSelect.style.display = 'none';
        this.elements.customerSelect.removeAttribute('required');

        const newInput = this.elements.customerInput.cloneNode(true);
        this.elements.customerInput.parentNode.replaceChild(newInput, this.elements.customerInput);
        this.elements.customerInput = newInput;

        this.elements.customerInput.addEventListener('input', (e) => {
            this.clearCustomerIds();
            this.debounce('customer-auto-search', () => {
                this.searchCustomers(e.target.value);
            }, 250);
        });

        this.elements.customerInput.addEventListener('blur', () => {
            setTimeout(() => {
                if (this.elements.customerSuggestionsResults) {
                    this.elements.customerSuggestionsResults.style.display = 'none';
                }
            }, 150);
        });
    }

    async searchCustomers(query) {
        const resultsContainer = this.elements.customerSuggestionsResults;
        if (!query || query.length < 1) {
            if (resultsContainer) resultsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`{{ route('distributor.api.search_customers') }}?q=${encodeURIComponent(query)}`);
            if (!response.ok) return;

            const filteredCustomers = await response.json();
            resultsContainer.innerHTML = '';

            if (filteredCustomers.length > 0) {
                filteredCustomers.forEach(customer => {
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
        if (this.elements.customerInput) this.elements.customerInput.value = customer.name;
        if (this.elements.phoneInput) this.elements.phoneInput.value = customer.mobile || '';
        if (this.elements.addressInput) this.elements.addressInput.value = customer.address || '';
        if (this.elements.customerIdInput) this.elements.customerIdInput.value = customer.customer_id;
        if (this.elements.partyIdInput) this.elements.partyIdInput.value = '';
        if (this.elements.customerSuggestionsResults) this.elements.customerSuggestionsResults.style.display = 'none';
    }

    setupPartyMode() {
        if (!this.elements.customerInput || !this.elements.customerSelect || !this.elements.customerSuggestionsResults) return;

        this.elements.customerInput.style.display = 'none';
        this.elements.customerInput.removeAttribute('required');
        this.elements.customerSelect.style.display = 'block';
        this.elements.customerSelect.setAttribute('required', 'required');
        this.elements.customerSuggestionsResults.style.display = 'none';

        const newSelect = this.elements.customerSelect.cloneNode(true);
        this.elements.customerSelect.parentNode.replaceChild(newSelect, this.elements.customerSelect);
        this.elements.customerSelect = newSelect;

        this.elements.customerSelect.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (selectedOption && selectedOption.value) {
                this.fillFieldsFromParty(selectedOption);
            } else {
                this.clearFields();
            }
        });

        new SlimSelect({
            select: this.elements.customerSelect
        });
    }

    initPhotoUploaders() {
        const photoItems = document.querySelectorAll('.photo-upload-item');

        photoItems.forEach(item => {
            const input = item.querySelector('input');
            const icon = item.querySelector('i');
            const img = item.querySelector('img');
            const removeBtn = item.querySelector('.remove-btn');

            if (!input || !icon) return;

            const clickHandler = (e) => {
                if (e.target !== removeBtn && !removeBtn?.contains(e.target)) {
                    input.click();
                }
            };

            const changeHandler = (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                if (input.accept?.includes('image') && img) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        img.src = event.target.result;
                        img.style.display = 'block';
                        icon.style.display = 'none';
                    };
                    reader.onerror = () => console.error('Error reading file');
                    reader.readAsDataURL(file);
                } else {
                    icon.className = 'fas fa-file-pdf';
                    icon.style.display = 'block';
                }
            };

            const removeHandler = (e) => {
                e.stopPropagation();
                input.value = '';
                if (img) {
                    img.src = '';
                    img.style.display = 'none';
                }
                icon.style.display = 'block';
                icon.className = input.accept?.includes('image') ? 'fas fa-camera' : 'fas fa-file-alt';
            };

            item.addEventListener('click', clickHandler);
            input.addEventListener('change', changeHandler);
            removeBtn?.addEventListener('click', removeHandler);
        });
    }

    addProductRow() {
        this.productCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'product-row';
        newRow.dataset.index = this.productCounter;

        newRow.innerHTML = this.getProductRowTemplate(this.productCounter);
        this.elements.productRows?.appendChild(newRow);
        this.addProductRowListeners(newRow);

        return newRow;
    }

    getProductRowTemplate(counter) {
        return `
            <td>
                <div class="d-flex align-items-center gap-2 w-100">
                    <div class="product-search-container flex-grow-1">
                        <input type="text" class="form-control product-search" name="items[${counter}][product_name]"
                               placeholder="Search inventory..." required autocomplete="off">
                        <input type="hidden" class="inventory-id" name="items[${counter}][inventory_id]">
                        <div class="product-suggestions"></div>
                    </div>
                    <div class="border-start ps-2 flex-shrink-0 battery-health-wrapper" style="width: 55px; display: none;">
                        <input type="text" class="form-control battery-health text-center px-1 fw-bold text-success" value="" readonly title="Battery Health">
                    </div>
                </div>
            </td>
            <td><input type="text" class="form-control condition" name="items[${counter}][condition]"></td>
            <td><input type="text" class="form-control quality" name="items[${counter}][quality]"></td>
            <td><input type="text" class="form-control warranty" name="items[${counter}][warranty]" readonly></td>
            <td><input type="text" class="form-control color" name="items[${counter}][color]"></td>
            <td><input type="text" class="form-control imei_one" name="items[${counter}][imei_one]"></td>
            <td><input type="text" class="form-control imei_two" name="items[${counter}][imei_two]"></td>
            <td><input type="text" class="form-control serial_number" name="items[${counter}][serial_number]"></td>
            <td><input type="number" class="form-control qty" name="items[${counter}][qty]" min="1" value="1" required></td>
            <td><input type="text" class="form-control unit" name="items[${counter}][unit]"></td>
            <td><input type="number" step="0.001" class="form-control price" name="items[${counter}][price]" required></td>
            <td>
                <div class="d-flex">
                    <input type="number" step="0.01" class="form-control discount-percent" name="items[${counter}][discount_percent]" placeholder="%" value="0" min="0" max="100" readonly>
                    <input type="number" step="0.001" class="form-control discount-amount" name="items[${counter}][discount_amount]" placeholder="₹" value="0" min="0">
                </div>
            </td>
            <td>
                <div class="d-flex">
                    <input type="number" step="0.01" class="form-control tax-percent" name="items[${counter}][tax_percent]" placeholder="%" value="0" min="0" max="100">
                    <input type="number" step="0.001" class="form-control tax-amount" name="items[${counter}][tax_amount]" placeholder="₹" readonly>
                </div>
            </td>
            <td><input type="number" step="0.001" class="form-control amount" name="items[${counter}][amount]" readonly></td>
            <td><span class="remove-product"><i class="fas fa-trash"></i></span></td>
        `;
    }

    removeProductRow(row) {
        if (!row) return;

        const inventoryId = row.querySelector('.inventory-id')?.value;
        if (inventoryId) {
            this.selectedInventoryIds.delete(String(inventoryId));
        }

        row.remove();
        this.calculateTotals();
    }

    addProductRowListeners(row) {
        if (!row) return;

        const calculationInputs = row.querySelectorAll('.qty, .price, .discount-amount, .tax-percent');
        calculationInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.debounce(`calc-${row.dataset.index}`, () => {
                    this.calculateRowAmount(row);
                    this.calculateTotals();
                }, 100);
            });
        });

        this.setupInventorySearch(row);
    }

    setupInventorySearch(row) {
        const productSearch = row.querySelector('.product-search');
        const suggestionsContainer = row.querySelector('.product-suggestions');
        const inventoryIdInput = row.querySelector('.inventory-id');

        if (!productSearch || !suggestionsContainer || !inventoryIdInput) return;

        productSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();

            this.debounce(`search-${row.dataset.index}`, async () => {
                if (searchTerm.length < 2) {
                    this.hideSuggestions(suggestionsContainer);
                    return;
                }

                const filteredInventory = await this.getFilteredInventory(searchTerm);
                this.displaySuggestions(filteredInventory, suggestionsContainer, row);
            }, 350);
        });

        productSearch.addEventListener('change', () => {
            if (!inventoryIdInput.value) {
                this.clearProductFields(row);
            }
            this.calculateTotals();
        });

        document.addEventListener('click', (e) => {
            if (!row.contains(e.target)) {
                this.hideSuggestions(suggestionsContainer);
            }
        });
    }

    async getFilteredInventory(searchTerm) {
        try {
            const response = await fetch(`{{ route('distributor.api.search_inventory') }}?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) return [];

            const data = await response.json();
            return data.filter(item => !this.selectedInventoryIds.has(String(item.inventory_id)));
        } catch (error) {
            console.error('Error fetching inventory search:', error);
            return [];
        }
    }

    displaySuggestions(filteredInventory, container, row) {
        if (!filteredInventory || filteredInventory.length === 0) {
            container.innerHTML = '<div class="no-results p-2 text-muted">No available items found</div>';
            container.style.display = 'block';
            return;
        }

        const html = filteredInventory.map(item => `
            <div class="product-suggestion-item"
                data-id="${item.inventory_id}"
                data-name="${this.escapeHtml(item.product_name)}"
                data-condition="${this.escapeHtml(item.product_type || '')}"
                data-quality="${this.escapeHtml(item.health || '')}"
                data-battery-health="${item.battery_health !== null && item.battery_health !== undefined ? item.battery_health : ''}"
                data-activation-date="${item.activation_date || ''}"
                data-warranty="${this.escapeHtml(item.warranty || '')}"
                data-color="${this.escapeHtml(item.color || '')}"
                data-imei-one="${this.escapeHtml(item.imei_one || '')}"
                data-imei-two="${this.escapeHtml(item.imei_two || '')}"
                data-serial-number="${this.escapeHtml(item.serial_number || '')}"
                data-unit="${this.escapeHtml(item.unit || '')}"
                data-stock="${item.stock || 0}"
                data-gst-per="${item.gst_per || 0}"
                data-price="${
                    item.gst_per > 0
                        ? ((item.sell_rate || 0) / (1 + (item.gst_per / 100))).toFixed(3)
                        : (item.sell_rate || 0)
                }">
                ${this.escapeHtml(item.product_name)}
                ${item.imei_one ? ` (IMEI1: ${this.escapeHtml(item.imei_one)})` : ''}
                ${item.serial_number ? ` (S/N: ${this.escapeHtml(item.serial_number)})` : ''}
                <small class="text-muted d-block">Stock: ${item.stock || 0}</small>
            </div>
        `).join('');

        container.innerHTML = html;
        container.style.display = 'block';

        container.querySelectorAll('.product-suggestion-item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectInventoryItem(item, row);
                this.hideSuggestions(container);
            });
        });
    }

    hideSuggestions(container) {
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    }

    selectInventoryItem(item, row) {
        const inventoryId = String(item.dataset.id);

        if (this.selectedInventoryIds.has(inventoryId)) {
            alert('This item is already added to the invoice.');
            return;
        }

        this.selectedInventoryIds.add(inventoryId);

        const previousId = row.querySelector('.inventory-id')?.value;
        if (previousId && String(previousId) !== inventoryId) {
            this.selectedInventoryIds.delete(String(previousId));
        }

        this.fillProductFields(row, item);
        this.calculateRowAmount(row);
        this.calculateTotals();

        this.addProductRow();
    }

    fillProductFields(row, item) {
        const fieldMappings = [
            ['.product-search', 'name'],
            ['.inventory-id', 'id'],
            ['.condition', 'condition'],
            ['.quality', 'quality'],
            ['.color', 'color'],
            ['.imei_one', 'imeiOne'],
            ['.imei_two', 'imeiTwo'],
            ['.serial_number', 'serialNumber'],
            ['.unit', 'unit'],
            ['.price', 'price'],
            ['.tax-percent', 'gstPer']
        ];

        fieldMappings.forEach(([selector, dataKey]) => {
            const field = row.querySelector(selector);
            if (field) {
                field.value = item.dataset[dataKey] || '';
            }
        });

        const bhWrapper = row.querySelector('.battery-health-wrapper') || row.querySelector('.battery-health')?.parentElement;
        const bhInput = row.querySelector('.battery-health');
        const rawBh = item.dataset.batteryHealth;

        if (bhInput && bhWrapper) {
            if (rawBh !== undefined && rawBh !== null && String(rawBh).trim() !== '') {
                bhInput.value = `${rawBh}%`;
                bhWrapper.style.display = 'block';
            } else {
                bhInput.value = '';
                bhWrapper.style.display = 'none';
            }
        }

        const warrantyInput = row.querySelector('.warranty');
        const actDate = item.dataset.activationDate;
        const warrantyVal = item.dataset.warranty;

        if (warrantyInput) {
            if (actDate && actDate.trim() !== '' && actDate !== 'null') {
                const d = new Date(actDate);
                const formattedDate = !isNaN(d.getTime())
                    ? `📅 ${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
                    : `📅 ${actDate}`;
                warrantyInput.value = formattedDate;
                warrantyInput.classList.add('text-primary', 'fw-600');
            } else {
                warrantyInput.value = warrantyVal || 'No Warranty';
                warrantyInput.classList.remove('text-primary', 'fw-600');
            }
        }

        const qtyInput = row.querySelector('.qty');
        if (qtyInput) {
            qtyInput.max = item.dataset.stock || 0;
            qtyInput.value = 1;
        }
    }

    clearProductFields(row) {
        const fields = [
            '.inventory-id', '.condition', '.quality', '.warranty',
            '.color', '.imei_one', '.imei_two', '.serial_number',
            '.unit', '.price', '.tax-percent'
        ];

        fields.forEach(selector => {
            const field = row.querySelector(selector);
            if (field) field.value = '';
        });

        const bhWrapper = row.querySelector('.battery-health-wrapper') || row.querySelector('.battery-health')?.parentElement;
        if (bhWrapper) bhWrapper.style.display = 'none';
    }

    calculateRowAmount(row) {
        if (!row) return { subtotal: 0, discountAmount: 0, taxAmount: 0, amount: 0 };

        const getFieldValue = (selector) => {
            const field = row.querySelector(selector);
            const value = parseFloat(field?.value);
            return isNaN(value) ? 0 : value;
        };

        const setFieldValue = (selector, value) => {
            const field = row.querySelector(selector);
            if (field) field.value = Number(value).toFixed(2);
        };

        const round = (num) => Math.round((num + Number.EPSILON) * 100) / 100;

        const qty = getFieldValue('.qty');
        const price = getFieldValue('.price');
        const discountAmount = getFieldValue('.discount-amount');
        const taxPercent = getFieldValue('.tax-percent');

        const subtotal = round(qty * price);
        const taxableAmount = round(Math.max(0, subtotal - discountAmount));

        const discountPercent = subtotal > 0
            ? Math.min(100, (discountAmount / subtotal) * 100)
            : 0;

        const amount = Math.floor((taxableAmount * (1 + taxPercent / 100)) * 100) / 100;
        const taxAmount = round(amount - taxableAmount);

        setFieldValue('.discount-percent', discountPercent);
        setFieldValue('.tax-amount', taxAmount);
        setFieldValue('.amount', amount);

        return { subtotal, discountAmount, taxAmount, amount };
    }

    calculateTotals() {
        const rows = document.querySelectorAll('.product-row');
        let totals = { subtotal: 0, totalDiscount: 0, totalTax: 0, totalAmount: 0 };

        rows.forEach(row => {
            const rowAmounts = this.calculateRowAmount(row);
            totals.subtotal += rowAmounts.subtotal;
            totals.totalDiscount += rowAmounts.discountAmount;
            totals.totalTax += rowAmounts.taxAmount;
            totals.totalAmount += rowAmounts.amount;
        });

        const shipping = parseFloat(this.elements.shippingAmount?.value) || 0;
        const finalTotal = totals.totalAmount + shipping;

        if (this.elements.isPaid?.checked && this.elements.paidAmount) {
            this.elements.paidAmount.value = finalTotal.toFixed(2);
        }

        const paid = parseFloat(this.elements.paidAmount?.value) || 0;
        const due = finalTotal - paid;

        this.updateTotalDisplays(totals, finalTotal, due);
    }

    updateTotalDisplays(totals, finalTotal, due) {
        const updates = [
            [this.elements.subtotal, `₹${totals.subtotal.toFixed(2)}`],
            [this.elements.totalDiscount, `₹${totals.totalDiscount.toFixed(2)}`],
            [this.elements.totalTax, `₹${totals.totalTax.toFixed(2)}`],
            [this.elements.totalAmount, `₹${finalTotal.toFixed(2)}`],
            [this.elements.dueAmount, `₹${due.toFixed(2)}`]
        ];

        updates.forEach(([element, value]) => {
            if (element) element.textContent = value;
        });

        const hiddenInputs = [
            ['subtotal', totals.subtotal],
            ['total_discount', totals.totalDiscount],
            ['total_tax', totals.totalTax],
            ['total_amount', finalTotal],
            ['due_amount', due]
        ];

        hiddenInputs.forEach(([name, value]) => {
            const input = document.querySelector(`input[name="${name}"]`);
            if (input) input.value = value.toFixed(2);
        });
    }

    handlePaidToggle() {
        if (!this.elements.isPaid || !this.elements.paidAmount) return;

        if (this.elements.isPaid.checked) {
            const total = parseFloat(this.elements.totalAmount?.textContent?.replace('₹', '')) || 0;
            this.elements.paidAmount.value = total.toFixed(2);
            this.elements.paidAmount.readOnly = true;
        } else {
            this.elements.paidAmount.value = '0';
            this.elements.paidAmount.readOnly = false;
        }
        this.calculateTotals();
    }

    setupSearchModal() {
        if (!this.elements.searchModal || !this.elements.searchInput || !this.elements.searchResults) return;

        this.elements.searchInput.addEventListener('input', (e) => {
            this.debounce('modal-search', () => {
                this.handleModalSearch(e.target.value.trim());
            }, 300);
        });

        this.elements.searchModal.addEventListener('shown.bs.modal', () => {
            this.elements.searchInput.focus();
        });

        this.elements.searchModal.addEventListener('hidden.bs.modal', () => {
            this.elements.searchInput.value = '';
            this.elements.searchResults.innerHTML = '';
        });
    }

    async handleModalSearch(searchTerm) {
        if (!searchTerm) {
            this.elements.searchResults.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`{{ route('distributor.api.search_inventory') }}?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) return;

            const filteredItems = await response.json();

            if (filteredItems.length === 0) {
                this.elements.searchResults.innerHTML = '<div class="alert alert-info mb-0">No items found matching your scan</div>';
                return;
            }

            this.displayModalResults(filteredItems);
        } catch (error) {
            console.error('Error in modal scan search:', error);
        }
    }

    displayModalResults(items) {
        const html = items.map(item => `
            <div class="search-item ${this.selectedInventoryIds.has(String(item.inventory_id)) ? 'disabled' : ''}"
                data-id="${item.inventory_id}"
                data-name="${this.escapeHtml(item.product_name)}"
                data-condition="${this.escapeHtml(item.product_type || '')}"
                data-quality="${this.escapeHtml(item.health || '')}"
                data-battery-health="${item.battery_health !== null && item.battery_health !== undefined ? item.battery_health : ''}"
                data-activation-date="${item.activation_date || ''}"
                data-warranty="${this.escapeHtml(item.warranty || '')}"
                data-color="${this.escapeHtml(item.color || '')}"
                data-imei-one="${this.escapeHtml(item.imei_one || '')}"
                data-imei-two="${this.escapeHtml(item.imei_two || '')}"
                data-serial-number="${this.escapeHtml(item.serial_number || '')}"
                data-unit="${this.escapeHtml(item.unit || '')}"
                data-stock="${item.stock || 0}"
                data-gst-per="${item.gst_per || 0}"
                data-price="${
                    item.gst_per > 0
                        ? ((item.sell_rate || 0) / (1 + (item.gst_per / 100))).toFixed(3)
                        : (item.sell_rate || 0)
                }">
                <div class="search-item-container">
                    <div class="search-item-details">
                        <strong>${this.escapeHtml(item.product_name)}</strong>
                        <div class="text-muted small">
                            ${item.imei_one ? `IMEI1: ${this.escapeHtml(item.imei_one)}<br>` : ''}
                            ${item.imei_two ? `IMEI2: ${this.escapeHtml(item.imei_two)}<br>` : ''}
                            ${item.serial_number ? `S/N: ${this.escapeHtml(item.serial_number)}<br>` : ''}
                            Condition: ${this.escapeHtml(item.product_type)} | Quality: ${this.escapeHtml(item.health)} | GST: ${item.gst_per || 0}%
                        </div>
                        ${this.selectedInventoryIds.has(String(item.inventory_id)) ? '<small class="text-danger">Already added</small>' : ''}
                    </div>
                    <div class="search-item-stock">
                        Stock: ${item.stock || 0}
                    </div>
                </div>
            </div>
        `).join('');

        this.elements.searchResults.innerHTML = html;

        this.elements.searchResults.querySelectorAll('.search-item:not(.disabled)').forEach(item => {
            item.addEventListener('click', () => {
                this.handleModalItemSelection(item);
            });
        });
    }

    handleModalItemSelection(item) {
        const lastRow = document.querySelector('.product-row:last-child');
        let targetRow = lastRow;

        if (!lastRow || lastRow.querySelector('.product-search')?.value) {
            targetRow = this.addProductRow();
        }

        this.fillProductFields(targetRow, item);
        this.selectedInventoryIds.add(String(item.dataset.id));

        const modal = bootstrap.Modal.getInstance(this.elements.searchModal);
        modal?.hide();

        this.elements.searchInput.value = '';
        this.elements.searchResults.innerHTML = '';

        targetRow.querySelector('.price')?.focus();

        this.calculateRowAmount(targetRow);
        this.calculateTotals();
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
@endsection
