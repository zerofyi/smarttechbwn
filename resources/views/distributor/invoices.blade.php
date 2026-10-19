@extends('layouts.distributor')

@section('title', 'Distributor : Invoices')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="../../../vendors/choices/choices.min.css" rel="stylesheet" />
    <style>
        .filter-section {
            background-color: #ffffff;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-toggle {
            cursor: pointer;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .invoice-type-badge {
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
    @if ($errors->any())
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Error</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"
                        aria-label="Close"></button>
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
            <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Error</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div class="toast show text-white bg-success bg-opacity-75 border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header text-white">
                    <strong class="me-auto">Success</strong>
                    <small class="text-light">Just now</small>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Invoices</li>
            </ol>
        </nav>
        <div class="mb-9">
            <div class="row g-3 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Invoices
                        @if (isset($totalFilteredAmount) && $totalFilteredAmount > 0)
                            (Total: ₹{{ number_format($totalFilteredAmount, 2) }})
                        @endif
                    </h2>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Filters</h5>
                    <span class="filter-toggle" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-sliders-h"></i>
                    </span>
                </div>

                <div class="collapse hide" id="filterCollapse">
                    <form id="filterForm">
                        <div class="row g-3">
                            <!-- Basic Filters -->
                            <div class="col-md-3">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" name="invoice_no"
                                    value="{{ request('invoice_no') }}" placeholder="Search by invoice no">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Invoice Type</label>
                                <select class="form-select" name="invoice_type">
                                    <option value="">All Types</option>
                                    <option value="sale" {{ request('invoice_type') == 'sale' ? 'selected' : '' }}>Sale
                                    </option>
                                    <option value="purchase" {{ request('invoice_type') == 'purchase' ? 'selected' : '' }}>
                                        Purchase</option>
                                    <option value="return" {{ request('invoice_type') == 'return' ? 'selected' : '' }}>
                                        Return</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Invoice For</label>
                                <select class="form-select" name="invoice_for">
                                    <option value="">All</option>
                                    <option value="party" {{ request('invoice_for') == 'party' ? 'selected' : '' }}>Party
                                    </option>
                                    <option value="customer" {{ request('invoice_for') == 'customer' ? 'selected' : '' }}>
                                        Customer</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" name="payment_type">
                                    <option value="">All</option>
                                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>
                                        Credit</option>
                                    <option value="bank" {{ request('payment_type') == 'bank' ? 'selected' : '' }}>Bank
                                        Transfer</option>
                                    <option value="online" {{ request('payment_type') == 'online' ? 'selected' : '' }}>
                                        Online Payment</option>
                                </select>
                            </div>

                            <!-- Party/Customer Filters -->
                            <div class="col-md-3">
                                <label class="form-label">Party</label>
                                <select class="form-select" name="party_id" data-choices="data-choices">
                                    <option value="">All Parties</option>
                                    @foreach ($parties as $party)
                                        <option value="{{ $party->party_id }}"
                                            {{ request('party_id') == $party->party_id ? 'selected' : '' }}>
                                            {{ $party->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Customer</label>
                                <select class="form-select" name="customer_id" data-choices="data-choices">
                                    <option value="">All Customers</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->customer_id }}"
                                            {{ request('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product Filter -->
                            <div class="col-md-3">
                                <label class="form-label">Product</label>
                                <select class="form-select" name="product_id" data-choices="data-choices">
                                    <option value="">All Products</option>
                                    @foreach ($products as $product)
                                        @php
                                            $attributeValues = [];
                                            foreach($product->productAttributes as $attribute) {
                                                if($attribute->attribute && $attribute->attributeValue) {
                                                    $attributeValues[] = $attribute->attributeValue->value;
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $product->product_id }}"
                                            {{ request('product_id') == $product->product_id ? 'selected' : '' }}>
                                            {{ $product->name }}@if($product->color) - {{ $product->color }}@endif
                                            @if(count($attributeValues))
                                                ({{ implode(' / ', $attributeValues) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid
                                    </option>
                                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>
                                        Partially Paid</option>
                                </select>
                            </div>

                            <!-- Date Range Filters -->
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" name="date_from"
                                    value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" name="date_to"
                                    value="{{ request('date_to') }}">
                            </div>

                            {{-- <div class="col-md-3">
                                <label class="form-label">Due Date From</label>
                                <input type="date" class="form-control" name="due_date_from"
                                    value="{{ request('due_date_from') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Due Date To</label>
                                <input type="date" class="form-control" name="due_date_to"
                                    value="{{ request('due_date_to') }}">
                            </div> --}}

                            <!-- Amount Range Filters -->
                            <div class="col-md-3">
                                <label class="form-label">Min Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount_min"
                                    value="{{ request('amount_min') }}" placeholder="Min amount">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Max Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount_max"
                                    value="{{ request('amount_max') }}" placeholder="Max amount">
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i> Apply Filters
                                </button>
                                <a href="{{ route('distributor.invoices') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoice Table -->
            <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                <div class="">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="sort align-middle ps-4" scope="col">INVOICE NO</th>
                                <th class="sort align-middle ps-4" scope="col">TYPE</th>
                                <th class="sort align-middle ps-4" scope="col">PARTY/CUSTOMER</th>
                                <th class="sort align-middle ps-4" scope="col">DATE</th>
                                <th class="sort align-middle ps-4" scope="col">AMOUNT</th>
                                <th class="sort align-middle ps-4" scope="col">PAID</th>
                                <th class="sort align-middle ps-4" scope="col">DUE</th>
                                <th class="sort align-middle ps-4" scope="col">STATUS</th>
                                <th class="sort align-middle ps-4" scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr class="position-static">
                                    <td class="align-middle ps-4">
                                        <a href="{{ route('distributor.invoices.show', $invoice->invoice_id) }}"
                                            class="fw-semibold">
                                            {{ $invoice->invoice_no }}
                                        </a>
                                    </td>
                                    <td class="align-middle ps-4">
                                        <span
                                            class="badge invoice-type-badge
                                    @if ($invoice->invoice_type === 'sale') bg-success
                                    @elseif($invoice->invoice_type === 'purchase') bg-primary
                                    @elseif($invoice->invoice_type === 'return') bg-danger
                                    @else bg-secondary @endif">
                                            {{ ucfirst($invoice->invoice_type) }}
                                        </span>
                                        @if ($invoice->invoice_for)
                                            <span class="badge invoice-type-badge bg-info">
                                                {{ ucfirst($invoice->invoice_for) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle ps-4">
                                        @if ($invoice->invoice_for === 'party' && $invoice->party)
                                            {{ $invoice->party->name }}
                                            <small class="text-muted d-block">{{ $invoice->party->phone }}</small>
                                        @elseif($invoice->invoice_for === 'customer' && $invoice->customer)
                                            {{ $invoice->customer->name }}
                                            <small class="text-muted d-block">{{ $invoice->customer->mobile }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="align-middle ps-4">
                                        {{ $invoice->invoice_date->format('d M Y') }}
                                    </td>
                                    <td class="align-middle ps-4">
                                        ₹{{ number_format($invoice->total_amount, 2) }}
                                    </td>
                                    <td class="align-middle ps-4">
                                        ₹{{ number_format($invoice->paid, 2) }}
                                    </td>
                                    <td class="align-middle ps-4">
                                        ₹{{ number_format($invoice->due_amount, 2) }}
                                    </td>
                                    <td class="align-middle ps-4">
                                        @if ($invoice->invoice_type === 'sale' && $invoice->loan)
                                            @if ($invoice->loan->loan_status === 'open')
                                                <span class="badge status-badge bg-info">Loan Open</span>
                                            @elseif($invoice->loan->loan_status === 'closed')
                                                <span class="badge status-badge bg-success">Loan Closed</span>
                                            @else
                                                <span class="badge status-badge bg-danger">Loan</span>
                                            @endif
                                        @else
                                            @if ($invoice->due_amount <= 0)
                                                <span class="badge status-badge bg-success">Paid</span>
                                            @elseif($invoice->paid > 0)
                                                <span class="badge status-badge bg-warning text-dark">Partial</span>
                                            @else
                                                <span class="badge status-badge bg-danger">Unpaid</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-middle ps-4">
                                        <div class="btn-group">
                                            @if ($invoice->invoice_type === 'sale')
                                                <a href="{{ route('distributor.sale.edit', $invoice->invoice_id) }}"
                                                    class="btn btn-sm btn-phoenix-primary" title="View">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif($invoice->invoice_type === 'purchase')
                                                <a href="{{ route('distributor.purchase.edit', $invoice->invoice_id) }}"
                                                    class="btn btn-sm btn-phoenix-primary" title="View">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                            @endif
                                            @if ($invoice->invoice_url)
                                                @if ($invoice->total_tax > 0)
                                                    <a href="{{ route('invoice.print.gst', $invoice->invoice_url) }}"
                                                        target="_blank" class="btn btn-sm btn-phoenix-info" title="Download">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('invoice.print', $invoice->invoice_url) }}"
                                                        target="_blank" class="btn btn-sm btn-phoenix-info" title="Download">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            <div class="btn-reveal-trigger position-static">
                                                <button
                                                    class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10"
                                                    type="button" data-bs-toggle="dropdown" data-boundary="window"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    data-bs-reference="parent">
                                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end py-2">
                                                    <a href="{{ route('distributor.invoices.show', $invoice->invoice_id) }}" class="dropdown-item">View</a>
                                                    @if ($invoice->invoice_type === 'sale' && !$invoice->loan)
                                                        <a class="dropdown-item" href="{{ route('distributor.loan.create', $invoice->invoice_id) }}">Apply for a loan</a>
                                                    @endif
                                                    @if ($invoice->invoice_type === 'sale' && $invoice->loan)
                                                        <a class="dropdown-item" href="{{ route('distributor.loan.show', $invoice->loan->loan_id) }}">View Loan Details</a>
                                                    @endif
                                                    @if ($invoice->invoice_type === 'purchase' && !$invoice->agreement)
                                                        <a class="dropdown-item" href="{{ route('distributor.agreement.create', $invoice->invoice_id) }}">Generate agreement</a>
                                                    @elseif ($invoice->invoice_type === 'purchase' && $invoice->agreement)
                                                        <a class="dropdown-item" href="{{ route('distributor.agreement.edit', $invoice->agreement->agreement_id) }}">Edit Agreement</a>
                                                        <a class="dropdown-item" href="{{ route('distributor.agreement.print', $invoice->agreement->agreement_id) }}" target="_blank">Print Agreement</a>
                                                    @endif
                                                    <div class="dropdown-divider"></div>
                                                    <!-- Add this form for each invoice in your dropdown -->
                                                    <form class="delete-invoice-form" method="POST" action="
                                                        @if ($invoice->invoice_type === 'sale')
                                                            {{ route('distributor.sale.destroy', $invoice->invoice_id) }}
                                                        @elseif($invoice->invoice_type === 'purchase')
                                                            {{ route('distributor.purchase.destroy', $invoice->invoice_id) }}
                                                        @endif
                                                    ">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirmDelete(event)">
                                                            <i class="fas fa-trash me-2"></i> Remove
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                            Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of
                            {{ $invoices->total() }} entries
                        </p>
                    </div>
                    <div class="col-auto d-flex">
                        {{ $invoices->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
        <x-admin.footer />
    </div>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
    <script src="../../../vendors/choices/choices.min.js"></script>
    <script>
        // Initialize date pickers
        flatpickr("input[type=date]", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Toggle filter section
        document.querySelector('.filter-toggle').addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-sliders-h')) {
                icon.classList.remove('fa-sliders-h');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-sliders-h');
            }
        });

        async function confirmDelete(event) {
            event.preventDefault();

            const form = event.target.closest('form');

            const { value: pin } = await Swal.fire({
                title: 'Enter 4 Digit PIN',
                input: 'password',
                inputLabel: 'Security PIN',
                inputPlaceholder: 'Enter PIN',
                inputAttributes: {
                    maxlength: 4,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Verify',
                confirmButtonColor: '#d33',
                inputValidator: (value) => {
                    if (!value) {
                        return 'PIN is required!';
                    }

                    if (value.length !== 4) {
                        return 'PIN must be 4 digits';
                    }
                }
            });

            if (!pin) {
                return;
            }

            try {

                const response = await fetch("{{ url('/distributor/verify-delete-pin') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ pin: pin })
                });

                if (response.ok) {

                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (result.isConfirmed) {
                        form.submit();
                    }

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid PIN',
                        text: 'The PIN you entered is incorrect.'
                    });

                }

            } catch (error) {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong.'
                });

            }

            return false;
        }
    </script>
@endsection
