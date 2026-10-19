@extends('layouts.distributor')

@section('title', 'Products')

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
        .variant-row {
            background-color: #f8f9fa;
            border-left: 4px solid #dee2e6;
        }
        .variant-row td {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .stock-in {
            color: #198754;
        }
        .stock-out {
            color: #dc3545;
        }
        .stock-low {
            color: #fd7e14;
        }
        .inventory-item {
            border-left: 4px solid #0d6efd;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-sm td, .table-sm th {
            padding: 0.75rem;
        }
        .badge {
            font-size: 0.75em;
            margin-bottom: 0.25rem;
        }
        .avatar img {
            object-fit: cover;
        }
        .form-check-input {
            margin-top: 0;
        }
        @media (max-width: 768px) {
            .table-responsive {
                width: 100%;
                margin-bottom: 1rem;
                overflow-y: hidden;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #dee2e6;
            }
            .d-flex.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .avatar {
                margin-bottom: 0.5rem;
            }
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
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="text-bold text-body-emphasis mb-0">Product Inventory</h2>
            <div>
                <!-- Add any buttons or actions here -->
            </div>
        </div>

        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col col-auto">
                <div class="search-box">
                    <form class="position-relative" method="GET" action="{{ route('distributor.products') }}">
                        <input class="form-control search-input search" type="search" name="search"
                            placeholder="Search products..." value="{{ request('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('distributor.products') }}" class="btn btn-outline-secondary btn-sm me-2">All Products</a>
                <a href="{{ route('distributor.products', ['stock' => 'out']) }}" class="btn btn-outline-danger btn-sm me-2">Out of Stock</a>
                <a href="{{ route('distributor.products', ['stock' => 'in']) }}" class="btn btn-outline-success btn-sm">In Stock</a>
            </div>
        </div>

        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1" data-list='{"valueNames":["product","brand","stock","price"]}'>
            <div class="table-responsive scrollbar ms-n1 ps-1">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 align-middle ps-0" style="width: 40px;">
                                <div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" type="checkbox" id="select-all" />
                                </div>
                            </th>
                            <th class="sort align-middle" scope="col" style="min-width:200px;" data-sort="product">PRODUCT</th>
                            <th class="sort align-middle" scope="col" style="min-width:150px;" data-sort="brand">BRAND & CATEGORY</th>
                            <th class="sort align-middle" scope="col" style="min-width:100px;" data-sort="stock">STOCK</th>
                            <th class="sort align-middle" scope="col" style="min-width:120px;" data-sort="price">PRICE</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="products-table-body">
                        @foreach ($products as $product)
                            <tr class="position-static">
                                <td class="fs-9 align-middle ps-0 py-3">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product->product_id }}" />
                                    </div>
                                </td>
                                <td class="align-middle white-space-nowrap product">
                                    <div class="d-flex align-items-center">
                                        @if($product->img_one)
                                            <div class="avatar avatar-m me-3">
                                                <img class="rounded" src="{{ asset('storage/' . $product->img_one) }}" alt="{{ $product->name }}" />
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $product->name }} @if($product->color)- {{ $product->color }}@endif</h6>
                                            <p class="mb-1 text-body-secondary">
                                                @foreach($product->productAttributes as $attribute)
                                                    @if($attribute->attribute && $attribute->attributeValue)
                                                        <span class="badge bg-primary me-1">
                                                            {{ $attribute->attribute->display_name ?? $attribute->attribute->name }}: {{ $attribute->attributeValue->value }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                                <span class="badge bg-light text-dark me-1">SKU: {{ $product->sku }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle white-space-nowrap brand">
                                    <b>{{ $product->brand->name ?? 'N/A' }}</b>
                                    <br>
                                    {{ $product->category->name ?? 'N/A' }}
                                </td>
                                <td class="align-middle white-space-nowrap stock">
                                    @php
                                        $distributorStock = $product->inventories()
                                            ->where('user_id', auth()->id())
                                            ->sum('stock');
                                    @endphp
                                    <a href="{{ route('distributor.invoices', ['invoice_type' => 'purchase', 'product_id' => $product->product_id]) }}"
                                       class="{{ $distributorStock > 10 ? 'stock-in' : ($distributorStock > 0 ? 'stock-low' : 'stock-out') }}">
                                        {{ $distributorStock }}
                                    </a>
                                </td>
                                <td class="align-middle white-space-nowrap price">
                                    ₹{{ number_format($product->retail_price, 2) }}
                                    @if($product->mrp)
                                        <del class="text-body-secondary ms-1">₹{{ number_format($product->mrp, 2) }}</del>
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
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of
                        {{ $products->total() }} entries
                    </p>
                </div>
                <div class="col-auto d-flex">
                    {{ $products->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        <x-admin.footer />
    </div>
@endsection

@section('vendor-script')
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
            // Select all checkbox functionality
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.product-checkbox, .inventory-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Initialize feather icons
            feather.replace();
        });
    </script>
@endsection
