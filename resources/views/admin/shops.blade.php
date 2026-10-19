@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'Stores')

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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Distributors</li>
            </ol>
        </nav>

        <h2 class="text-bold text-body-emphasis mb-5">Distributors & Shops</h2>

        <div id="distributors">
            <div class="row align-items-center justify-content-between g-3 mb-4">
                <div class="col col-auto">
                    <div class="search-box">
                        <form class="position-relative"><input class="form-control search-input search" type="search"
                                placeholder="Search distributors" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDistributorModal">
                        <span class="fas fa-plus me-2"></span>Add Distributor
                    </button>
                </div>
            </div>

            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
                <div class="table-responsive scrollbar ms-n1 ps-1">
                    <table class="table table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="white-space-nowrap fs-9 align-middle ps-0">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox" />
                                    </div>
                                </th>
                                <th class="sort align-middle" scope="col" style="width:20%; min-width:200px;">DISTRIBUTOR
                                </th>
                                <th class="sort align-middle" scope="col" style="width:15%; min-width:150px;">SHOP NAME
                                </th>
                                <th class="sort align-middle" scope="col" style="width:20%; min-width:200px;">SHOP
                                    ADDRESS</th>
                                <th class="sort align-middle" scope="col" style="width:10%;">PIN CODE</th>
                                <th class="sort align-middle text-end pe-0" scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="distributors-table-body">
                            @foreach ($distributors as $distributor)
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0 py-3">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" />
                                        </div>
                                    </td>
                                    <td class="align-middle white-space-nowrap">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-m">
                                                <img class="rounded-circle"
                                                    src="{{ asset('assets/img/team/avatar-placeholder.webp') }}"
                                                    alt="" />
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-semibold">{{ $distributor->name }}</h6>
                                                <p class="mb-0 text-body-secondary">{{ $distributor->email }}</p>
                                                <p class="mb-0 text-body-secondary">{{ $distributor->mobile }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle white-space-nowrap">
                                        <h6 class="mb-0 fw-semibold">{{ $distributor->shop->shop_name ?? 'N/A' }}</h6>
                                        <p class="mb-0 text-body-secondary">GSTIN: {{ $distributor->shop->shop_gstin ?? 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap">
                                        {{ $distributor->shop->shop_address ?? 'N/A' }}
                                    </td>
                                    <td class="align-middle white-space-nowrap">
                                        {{ $distributor->shop->shop_pin_code ?? 'N/A' }}
                                    </td>
                                    <td class="align-middle white-space-nowrap text-body-tertiary text-end">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-subtle-primary me-1 mb-1" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editDistributorModal-{{ $distributor->user_id }}"
                                                title="Edit">
                                                <span data-feather="edit-3"></span>
                                            </button>
                                            <button class="btn btn-subtle-danger mb-1" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteDistributorModal-{{ $distributor->user_id }}"
                                                title="Delete">
                                                <span data-feather="trash-2"></span>
                                            </button>
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
                            Showing {{ $distributors->firstItem() }} to {{ $distributors->lastItem() }} of
                            {{ $distributors->total() }} entries
                        </p>
                    </div>
                    <div class="col-auto d-flex">
                        {{ $distributors->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Distributor Modal -->
        <div class="modal fade" id="addDistributorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Distributor & Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.distributors.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Distributor Information</h6>
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
                                <div class="col-md-6">
                                    <h6 class="mb-3">Shop Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Shop Name</label>
                                        <input type="text" class="form-control" name="shop_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shop GSTIN</label>
                                        <input type="text" class="form-control" name="shop_gstin" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shop Photo</label>
                                        <input type="file" class="form-control" name="shop_photo"
                                            accept="image/jpeg,image/png">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shop Address</label>
                                        <textarea class="form-control" name="shop_address" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">PIN Code</label>
                                        <input type="text" class="form-control" name="shop_pin_code" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location (Optional)</label>
                                        <input type="text" class="form-control" name="shop_location">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modals for each distributor -->
        @foreach ($distributors as $distributor)
            <div class="modal fade" id="editDistributorModal-{{ $distributor->user_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit {{ $distributor->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.distributors.update', $distributor->user_id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Distributor Information</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $distributor->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ $distributor->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" class="form-control" name="mobile"
                                                value="{{ $distributor->mobile }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password (leave blank to keep current)</label>
                                            <input type="password" class="form-control" name="password"
                                                placeholder="••••••••">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Shop Information</h6>
                                        @php $shop = $distributor->shop @endphp
                                        <div class="mb-3">
                                            <label class="form-label">Shop Name</label>
                                            <input type="text" class="form-control" name="shop_name"
                                                value="{{ $shop->shop_name ?? '' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Shop GSTIN</label>
                                            <input type="text" class="form-control" name="shop_gstin"
                                                value="{{ $shop->shop_gstin ?? '' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Shop Photo</label>
                                            <input type="file" class="form-control" name="shop_photo"
                                                accept="image/jpeg,image/png">
                                            @if ($shop->shop_photo ?? false)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $shop->shop_photo) }}" alt="Shop photo"
                                                        style="max-height: 100px;" class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="remove_photo" id="remove_photo_{{ $distributor->user_id }}"
                                                            value="1">
                                                        <label class="form-check-label text-danger"
                                                            for="remove_photo_{{ $distributor->user_id }}">
                                                            Remove current photo
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Shop Address</label>
                                            <textarea class="form-control" name="shop_address" rows="3" required>{{ $shop->shop_address ?? '' }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">PIN Code</label>
                                            <input type="text" class="form-control" name="shop_pin_code"
                                                value="{{ $shop->shop_pin_code ?? '' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Location (Optional)</label>
                                            <input type="text" class="form-control" name="shop_location"
                                                value="{{ $shop->shop_location ?? '' }}">
                                        </div>
                                    </div>
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

            <!-- Delete Modals for each distributor -->
            <div class="modal fade" id="deleteDistributorModal-{{ $distributor->user_id }}" tabindex="-1"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.distributors.destroy', $distributor->user_id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <p>Are you sure you want to delete distributor <strong>{{ $distributor->name }}</strong>
                                    and their shop?</p>
                                <p class="text-danger">This will permanently remove all associated data.</p>
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
