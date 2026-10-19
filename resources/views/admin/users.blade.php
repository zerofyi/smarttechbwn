@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'Customers')

{{-- VENDOR CSS START --}}
@section('vendor-style')
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
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
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')
    <div class="content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Customers</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Customers List</h2>
        <div id="users"
            data-list='{"valueNames":["user","email","mobile","joined"],"page":10,"pagination":true}'>
            <div class="row align-items-center justify-content-between g-3 mb-4">
                <div class="col col-auto">
                    <div class="search-box">
                        <form class="position-relative" id="search-form">
                            <input class="form-control search-input search" type="search" name="search"
                                placeholder="Search customers" aria-label="Search"
                                value="{{ request('search') }}"/>
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-body me-4 px-0">
                            <span class="fa-solid fa-file-export fs-9 me-2"></span>Export
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <span class="fas fa-plus me-2"></span>Add Customer
                        </button>
                    </div>
                </div>
            </div>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
                <div class="table-responsive scrollbar ms-n1 ps-1">
                    <table class="table table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="white-space-nowrap fs-9 align-middle ps-0">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" id="checkbox-bulk-users-select" type="checkbox"
                                            data-bulk-select='{"body":"users-table-body"}' />
                                    </div>
                                </th>
                                <th class="sort align-middle" scope="col" data-sort="user"
                                    style="width:20%; min-width:200px;">CUSTOMER NAME</th>
                                <th class="sort align-middle" scope="col" data-sort="email"
                                    style="width:15%; min-width:200px;">EMAIL</th>
                                <th class="sort align-middle pe-3" scope="col" data-sort="mobile"
                                    style="width:15%; min-width:200px;">MOBILE NUMBER</th>
                                <th class="sort align-middle" scope="col" data-sort="bought"
                                    style="width:10%;">BOUGHT</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="sold"
                                    style="width:10%;">SOLD</th>
                                <th class="sort align-middle text-end pe-0" scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="users-table-body">
                            @foreach($customers as $customer)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="fs-9 align-middle ps-0 py-3">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox"
                                            data-bulk-select-row='{"customer":{"name":"{{ $customer->name }}"},"email":"{{ $customer->email }}","mobile":"{{ $customer->mobile }}","joined":"{{ $customer->created_at->format('M d, Y') }}"}' />
                                    </div>
                                </td>
                                <td class="user align-middle white-space-nowrap">
                                    <a class="d-flex align-items-center text-body text-hover-1000"
                                       href="{{ route('admin.users.show', $customer->id) }}">
                                        <div class="avatar avatar-m">
                                            <img class="rounded-circle"
                                                src="{{ asset('assets/img/team/avatar-placeholder.webp') }}"
                                                alt="{{ $customer->name }}" />
                                        </div>
                                        <h6 class="mb-0 ms-3 fw-semibold">{{ $customer->name }}</h6>
                                    </a>
                                </td>
                                <td class="email align-middle white-space-nowrap">
                                    <a class="fw-semibold" href="mailto:{{ $customer->email }}">
                                        {{ $customer->email }}
                                    </a>
                                </td>
                                <td class="mobile align-middle white-space-nowrap">
                                    <a class="fw-bold text-body-emphasis" href="tel:{{ $customer->mobile }}">
                                        {{ $customer->mobile }}
                                    </a>
                                </td>
                                <td class="bought align-middle white-space-nowrap text-body">
                                    {{ $customer->bought_count }}
                                </td>
                                <td class="sold align-middle text-end white-space-nowrap text-body-tertiary">
                                    {{ $customer->sold_count }}
                                </td>
                                <td class="align-middle white-space-nowrap text-body-tertiary text-end">
                                    <div class="btn-group" role="group">
                                        <!-- Cart Button (from your old code) -->
                                        <a href="#" class="btn btn-subtle-primary me-1 mb-1" type="button">
                                            <span data-feather="shopping-cart"></span>
                                        </a>

                                        <!-- Edit Button -->
                                        <button class="btn btn-subtle-secondary me-1 mb-1" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCustomerModal-{{ $customer->id }}"
                                                title="Edit">
                                            <span data-feather="edit-3"></span>
                                        </button>

                                        <!-- Delete Button -->
                                        <button class="btn btn-subtle-danger mb-1" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCustomerModal-{{ $customer->id }}"
                                                title="Delete">
                                            <span data-feather="trash-2"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Customer Modal -->
                            <div class="modal fade" id="editCustomerModal-{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update {{ $customer->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.users.update', $customer->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $customer->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $customer->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Mobile</label>
                                                    <input type="text" class="form-control" name="mobile" value="{{ $customer->mobile }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Password (leave blank to keep current)</label>
                                                    <input type="password" class="form-control" name="password" placeholder="••••••••">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Customer Modal -->
                            <div class="modal fade" id="deleteCustomerModal-{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.users.destroy', $customer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete {{ $customer->name }}?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                            Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries
                        </p>
                    </div>
                    <div class="col-auto d-flex">
                        {{ $customers->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Customer</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-admin.footer />
    </div>
@endsection
{{-- CONTENT END --}}

{{-- VENDOR JS START --}}
@section('vendor-script')
@endsection
{{-- VENDOR JS END --}}

{{-- PAGE JS START --}}
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
@endsection
{{-- PAGE JS END --}}
