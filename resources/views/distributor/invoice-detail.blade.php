@extends('layouts.distributor')

@section('title', 'Distributor : Invoice Detail')

@section('vendor-style')
@endsection

@section('page-style')
    <link href="../../vendors/prism/prism-okaidia.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="../../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .invoice-header {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .invoice-table th {
            background-color: #f8f9fa;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    </style>
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

@section('content')
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributor.invoices') }}">Invoices</a></li>
                <li class="breadcrumb-item active">Invoice Detail</li>
            </ol>
        </nav>
        <div class="mb-9">
            <div class="row g-3 mb-4">
                <div class="col-auto">
                    <h2 class="mb-1">Invoice #{{ $invoice->invoice_no }}</h2>
                    @if ($invoice->invoice_for === 'party' && $invoice->party)
                        <p class="mb-1">{{ $invoice->party->name }}, {{ $invoice->party->phone }}
                            @if ($invoice->party->address)
                                , {{ $invoice->party->address }}
                            @endif
                            @if ($invoice->party->gstin)
                                <br> GSTIN: {{ $invoice->party->gstin }}
                            @endif
                        </p>
                    @elseif($invoice->invoice_for === 'customer' && $invoice->customer)
                        <p class="mb-1">{{ $invoice->customer->name }}, {{ $invoice->customer->mobile }}
                            @if ($invoice->customer->address)
                                , {{ $invoice->customer->address }}
                            @endif
                        </p>
                    @else
                        <p class="mb-0">N/A</p>
                    @endif
                </div>
                <div class="col-auto ms-auto">
                    @if ($invoice->invoice_url)
                        <a href="{{ route('invoice.print', $invoice->invoice_url) }}" target="_blank"
                            class="btn btn-primary">
                            <i class="fas fa-download me-2"></i> Download Invoice
                        </a>
                    @endif
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Invoice Info</h5>
                            <p class="mb-1"><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
                            <p class="mb-1"><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                            @if ($invoice->due_date)
                                <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
                            @endif
                            <p class="mb-1"><strong>Type:</strong>
                                <span
                                    class="badge
                                @if ($invoice->invoice_type === 'sale') bg-success
                                @elseif($invoice->invoice_type === 'purchase') bg-primary
                                @elseif($invoice->invoice_type === 'return') bg-danger
                                @else bg-secondary @endif">
                                    {{ ucfirst($invoice->invoice_type) }}
                                </span>
                            </p>
                            <p class="mb-0"><strong>Payment:</strong> {{ ucfirst($invoice->payment_type) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Amount Summary</h5>
                            <p class="mb-1"><strong>Subtotal:</strong> ₹{{ number_format($invoice->subtotal, 2) }}</p>
                            <p class="mb-1"><strong>Discount:</strong> ₹{{ number_format($invoice->total_discount, 2) }}
                            </p>
                            <p class="mb-1"><strong>Tax:</strong> ₹{{ number_format($invoice->total_tax, 2) }}</p>
                            <p class="mb-1"><strong>Shipping:</strong> ₹{{ number_format($invoice->shipping_amount, 2) }}
                            </p>
                            <p class="mb-0"><strong>Total:</strong> ₹{{ number_format($invoice->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Payment Status</h5>
                            <p class="mb-1"><strong>Total Amount:</strong>
                                ₹{{ number_format($invoice->total_amount, 2) }}</p>
                            <p class="mb-1"><strong>Paid:</strong> ₹{{ number_format($invoice->paid, 2) }}</p>
                            <p class="mb-1"><strong>Due:</strong> ₹{{ number_format($invoice->due_amount, 2) }}</p>
                            <p class="mb-0">
                                <strong>Status:</strong>
                                @if ($invoice->due_amount <= 0)
                                    <span class="badge status-badge bg-success">Paid</span>
                                @elseif($invoice->paid > 0)
                                    <span class="badge status-badge bg-warning text-dark">Partially Paid</span>
                                @else
                                    <span class="badge status-badge bg-danger">Unpaid</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover invoice-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Discount</th>
                                    <th>Tax</th>
                                    <th class="text-end pe-4">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->items as $item)
                                    <tr class="@if ($item->trashed()) table-danger @endif">
                                        <td class="ps-4">{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($item->inventory)
                                                {{ $item->inventory->product_name }}
                                                @if(!is_null($item->inventory->battery_health) && $item->inventory->battery_health !== '')
                                                    <span class="text-success fw-bold"> - BH: {{ $item->inventory->battery_health }}%</span>
                                                @endif
                                                @if ($item->inventory->imei_one)
                                                    <small class="text-muted d-block">IMEI1:
                                                        {{ $item->inventory->imei_one }}</small>
                                                @endif
                                                @if ($item->inventory->imei_two)
                                                    <small class="text-muted d-block">IMEI2:
                                                        {{ $item->inventory->imei_two }}</small>
                                                @endif
                                                @if ($item->inventory->serial_number)
                                                    <small class="text-muted d-block">SERIAL:
                                                        {{ $item->inventory->serial_number }}</small>
                                                @endif
                                                @if ($item->inventory->color)
                                                    <small class="text-muted d-block">Color:
                                                        {{ $item->inventory->color }}</small>
                                                @endif

                                                @if ($item->inventory->age || $item->inventory->activation_date || $item->inventory->warranty)
                                                    <small class="text-muted d-block">
                                                        @if ($item->inventory->age)
                                                            <span>Age: <strong class="text-dark">{{ $item->inventory->age }}</strong></span>
                                                        @endif

                                                        @if ($item->inventory->activation_date)
                                                            <span class="ms-1">| 📅 Act. Date: <strong class="text-primary">{{ \Carbon\Carbon::parse($item->inventory->activation_date)->format('d/m/Y') }}</strong></span>
                                                        @elseif ($item->inventory->warranty)
                                                            <span class="ms-1">| 🛡️ Warranty: <strong class="text-dark">{{ $item->inventory->warranty }}</strong></span>
                                                        @endif
                                                    </small>
                                                @endif

                                                @if ($item->inventory->trashed())
                                                    <small class="text-danger d-block">(Inventory Deleted)</small>
                                                @endif
                                            @else
                                                N/A
                                            @endif

                                            @if ($item->trashed())
                                                <small class="text-danger d-block">(Item Deleted)</small>
                                            @endif
                                            @if ($item->trashed())
                                                <button class="btn btn-sm btn-success restore-item"
                                                    data-item-id="{{ $item->invoice_item_id }}"
                                                    data-inventory-id="{{ $item->inventory_id }}"
                                                    data-inventory-deleted="{{ $item->inventory && $item->inventory->trashed() ? '1' : '0' }}">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            @endif
                                        </td>
                                        <td>{{ $item->qty }}</td>
                                        <td>₹{{ number_format($item->rate, 2) }}</td>
                                        <td>
                                            @if ($item->discount > 0)
                                                ₹{{ number_format($item->discount, 2) }}
                                                @if ($item->discount_percent > 0)
                                                    <small
                                                        class="text-muted d-block">({{ $item->discount_percent }}%)</small>
                                                @endif
                                            @else
                                                ₹0.00
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->tax > 0)
                                                ₹{{ number_format($item->tax, 2) }}
                                                @if ($item->tax_percent > 0)
                                                    <small class="text-muted d-block">({{ $item->tax_percent }}%)</small>
                                                @endif
                                            @else
                                                ₹0.00
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes and Documents -->
            <div class="row">
                @if ($invoice->notes)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Notes</h5>
                                <p>{{ $invoice->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($invoice->photo_url || $invoice->doc_url)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Attachments</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @if ($invoice->photo_url)
                                        <a href="{{ Storage::disk('public_html')->url($invoice->photo_url) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-image me-2"></i> View Photo
                                        </a>
                                    @endif
                                    @if ($invoice->doc_url)
                                        <a href="{{ Storage::disk('public_html')->url($invoice->doc_url) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-file me-2"></i> View Document
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($invoice->agreement)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Agreement</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('distributor.agreement.print', $invoice->agreement->agreement_id) }}" target="_blank">Print Agreement</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Loan Info -->
            @if ($invoice->loan)
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Loan Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Loan No:</strong>
                                    <a href="{{ route('distributor.loan.show', $invoice->loan->loan_id) }}" class="fw-semi-bold">{{ $invoice->loan->loan_no }}</a>
                                </p>
                                <p class="mb-1"><strong>Start Date:</strong>
                                    {{ $invoice->loan->first_emi->format('d M Y') }}</p>
                                <p class="mb-1"><strong>End Date:</strong>
                                    {{ $invoice->loan->last_emi->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status:</strong>
                                    @if ($invoice->loan->loan_status === 'open')
                                        <span class="badge bg-success">Open</span>
                                    @elseif($invoice->loan->loan_status === 'closed')
                                        <span class="badge bg-danger">Closed</span>
                                    @endif
                                </p>
                                {{-- <p class="mb-1"><strong>Loan Amount:</strong>₹{{ number_format($invoice->loan->loan_amount, 2) }}</p> --}}
                                <p class="mb-1"><strong>Payable Amount:</strong>₹{{ number_format($invoice->loan->payable_amount, 2) }}</p>
                                <p class="mb-0"><strong>Installments:</strong> {{ $invoice->loan->emi_amount }} X {{ $invoice->loan->emi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <x-admin.footer />
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script src="../../../vendors/popper/popper.min.js"></script>
    <script src="../../../vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../../../vendors/anchorjs/anchor.min.js"></script>
    <script src="../../../vendors/is/is.min.js"></script>
    <script src="../../../vendors/fontawesome/all.min.js"></script>
    <script src="../../../vendors/lodash/lodash.min.js"></script>
    <script src="../../../vendors/list.js/list.min.js"></script>
    <script src="../../../vendors/feather-icons/feather.min.js"></script>
    <script src="../../../vendors/dayjs/dayjs.min.js"></script>
    <script src="../../vendors/prism/prism.js"></script>
    <script src="../../../assets/js/phoenix.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle restore item clicks
        document.querySelectorAll('.restore-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const inventoryId = this.getAttribute('data-inventory-id');
                const inventoryDeleted = this.getAttribute('data-inventory-deleted') === '1';

                if (confirm('Are you sure you want to restore this item?' +
                        (inventoryDeleted ? ' The associated inventory will also be restored.' : ''))) {
                    restoreItem(itemId, inventoryId, inventoryDeleted, this);
                }
            });
        });

        function restoreItem(itemId, inventoryId, inventoryDeleted, buttonElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`{{route('distributor.invoices.restore-item')}}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    item_id: itemId,
                    inventory_id: inventoryId,
                    restore_inventory: inventoryDeleted
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('success', data.message);
                    window.location.reload();

                    // Remove the button and update the row appearance
                    const row = buttonElement.closest('tr');
                    row.classList.remove('table-danger');
                    buttonElement.remove();

                    // Update the deleted status text
                    const deletedText = row.querySelector('.text-danger');
                    if (deletedText) {
                        deletedText.remove();
                    }
                } else {
                    showToast('error', data.message || 'Failed to restore item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while restoring the item');
            });
        }

        function showToast(type, message) {
            // Implement your toast notification here
            // You can use Phoenix.js toast or any other notification library
            const toast = new bootstrap.Toast(document.querySelector('.toast'));
            const toastElement = document.querySelector('.toast');

            toastElement.querySelector('.toast-body').textContent = message;
            toastElement.classList.remove('bg-success', 'bg-danger');
            toastElement.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');

            toast.show();
        }
    });
    </script>
@endsection
