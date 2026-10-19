@extends('layouts.distributor')

@section('title', 'Inventory')

@section('vendor-style')
@endsection

@section('page-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <style>
        .stock-in {
            color: #198754;
        }
        .stock-out {
            color: #dc3545;
        }
        .stock-low {
            color: #fd7e14;
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
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Inventory</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="text-bold text-body-emphasis mb-0">My Inventory</h2>
            <div>
                <a href="{{ route('distributor.inventories', request()->except('stock')) }}"
                class="btn btn-outline-secondary btn-sm me-2">
                    All Items
                </a>

                <a href="{{ route('distributor.inventories',
                        array_merge(request()->query(), ['stock' => 'out'])) }}"
                class="btn btn-outline-danger btn-sm me-2">
                    Out of Stock
                </a>

                <a href="{{ route('distributor.inventories',
                        array_merge(request()->query(), ['stock' => 'low'])) }}"
                class="btn btn-outline-warning btn-sm me-2">
                    Low Stock
                </a>

                <a href="{{ route('distributor.inventories',
                        array_merge(request()->query(), ['stock' => 'in'])) }}"
                class="btn btn-outline-info btn-sm">
                    In Stock
                </a>
            </div>
        </div>

        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col col-auto">
                <div class="search-box">
                    <form class="position-relative" method="GET" action="{{ route('distributor.inventories') }}">
                        <input class="form-control search-input search" type="search" name="search"
                            placeholder="Search inventory..." value="{{ request('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">Total Stock: {{ $totalStock }}</span>
                    <span class="badge bg-success">Total Value: ₹{{ number_format($totalValue, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
            <div class="table-responsive scrollbar ms-n1 ps-1">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="sort align-middle" scope="col" style="min-width:250px;">ITEM DETAILS</th>
                            <th class="sort align-middle" scope="col">TYPE & COLOR</th>
                            <th class="sort align-middle text-end" scope="col">STOCK</th>
                            <th class="sort align-middle text-end" scope="col">PURCHASE COST</th>
                            <th class="sort align-middle text-end" scope="col">SELL PRICE</th>
                            <th class="sort align-middle text-end" scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="inventory-table-body">
                        @foreach ($inventories as $inventory)
                            <tr class="position-static {{ $inventory->product_id ? 'product-item' : 'inventory-item' }}">
                                <td class="align-middle white-space-nowrap">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-m me-3">
                                            @if($inventory->product && $inventory->product->img_one)
                                                <img class="rounded" src="{{ asset('storage/' . $inventory->product->img_one) }}" alt="{{ $inventory->product_name }}" />
                                            @else
                                                <div class="avatar-name rounded bg-light">
                                                    <span class="fas fa-box-open"></span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">
                                                {{ $inventory->product ? $inventory->product->name : $inventory->product_name }}
                                                @if(!is_null($inventory->battery_health) && $inventory->battery_health !== '')
                                                    <span class="text-success fw-bold"> - BH: {{ $inventory->battery_health }}%</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-body-secondary">
                                                @if($inventory->product)
                                                    <span class="badge bg-light text-dark me-1">SKU: {{ $inventory->product->sku }}</span>
                                                @endif
                                                @if($inventory->imei_one)
                                                    <span class="badge bg-light text-dark me-1">IMEI1: {{ $inventory->imei_one }}</span>
                                                @endif
                                                @if($inventory->imei_two)
                                                    <span class="badge bg-light text-dark me-1">IMEI2: {{ $inventory->imei_two }}</span>
                                                @endif
                                                @if($inventory->serial_number)
                                                    <span class="badge bg-light text-dark me-1">S/N: {{ $inventory->serial_number }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle white-space-nowrap">
                                    {{ $inventory->product_type ?? ($inventory->product ? $inventory->product->category->name ?? 'N/A' : 'N/A') }}
                                    <br>
                                    <b>{{ $inventory->color ?? ($inventory->product ? $inventory->product->color : 'N/A') }}</b>
                                </td>
                                <td class="align-middle white-space-nowrap text-end">
                                    <a href="{{ route('distributor.invoices', ['inventory_id' => $inventory->inventory_id]) }}"
                                       class="{{ $inventory->stock > 10 ? 'stock-in' : ($inventory->stock > 0 ? 'stock-low' : 'stock-out') }}">
                                        {{ $inventory->stock }}
                                    </a>
                                </td>
                                <td class="align-middle white-space-nowrap text-end">
                                    @php
                                        $purchaseWithGst = $inventory->purchase_rate * (1 + ($inventory->gst_per / 100));
                                    @endphp

                                    ₹{{ number_format($purchaseWithGst, 2) }}<br>
                                    <small>({{ $inventory->purchase_rate ." + ". round(($inventory->purchase_rate * $inventory->gst_per / 100), 2) }})</small>
                                </td>
                                <td class="align-middle white-space-nowrap text-end">
                                    ₹{{ number_format($inventory->sell_rate, 2) }}
                                </td>
                                <td class="align-middle white-space-nowrap text-end">
                                    <a href="{{ route('distributor.invoices', ['inventory_id' => $inventory->inventory_id]) }}"
                                        class="btn btn-sm btn-phoenix-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Make Offline --}}
                                    @if ($inventory->product_id)
                                        <form method="POST"
                                            action="{{ route('distributor.inventories.make-offline', $inventory->inventory_id) }}"
                                            class="make-offline-form d-inline">

                                            @csrf

                                            <button type="submit"
                                                class="btn btn-sm btn-phoenix-danger"
                                                title="Make Offline"
                                                onclick="return confirmMakeOffline(event)">

                                                <i class="fas fa-unlink"></i>
                                            </button>

                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                        Showing {{ $inventories->firstItem() }} to {{ $inventories->lastItem() }} of
                        {{ $inventories->total() }} entries
                    </p>
                </div>
                <div class="col-auto d-flex">
                    {{ $inventories->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>

        <x-admin.footer />
    </div>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

@section('page-script')
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize feather icons
            feather.replace();
        });
    </script>
    <script>
        async function confirmMakeOffline(event) {

            event.preventDefault();

            const form = event.target.closest('form');

            // Step 1: Ask for PIN
            const { value: pin } = await Swal.fire({
                title: 'Enter 4 Digit PIN',
                input: 'password',
                inputLabel: 'Security PIN',
                inputPlaceholder: 'Enter PIN',
                inputAttributes: {
                    maxlength: 4,
                    autocapitalize: 'off',
                    autocorrect: 'off',
                    inputmode: 'numeric'
                },
                showCancelButton: true,
                confirmButtonText: 'Verify',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                inputValidator: (value) => {

                    if (!value) {
                        return 'PIN is required!';
                    }

                    if (value.length !== 4) {
                        return 'PIN must be 4 digits';
                    }

                    if (!/^\d{4}$/.test(value)) {
                        return 'PIN must contain only digits';
                    }
                }
            });

            // User cancelled PIN dialog
            if (!pin) {
                return false;
            }

            try {

                // Step 2: Verify PIN
                const response = await fetch(
                    "{{ url('/distributor/verify-delete-pin') }}",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            pin: pin
                        })
                    }
                );

                if (response.ok) {

                    // Step 3: Final confirmation
                    const result = await Swal.fire({
                        title: 'Make this item offline?',
                        text: 'This will remove the product association from this inventory item.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, make it offline!',
                        cancelButtonText: 'Cancel'
                    });

                    // Step 4: Submit form
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

                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while verifying the PIN.'
                });
            }

            return false;
        }
    </script>
@endsection
