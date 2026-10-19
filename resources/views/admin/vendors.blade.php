@extends('layouts.admin')

@section('title', 'Admin : Vendors')

@section('vendor-style')
@endsection

@section('page-style')
<link href="../../vendors/prism/prism-okaidia.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="../../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
<link href="../../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
<link href="../../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
<link href="../../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
<link href="../../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
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

    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendors</li>
        </ol>
    </nav>
    <div class="mb-9">
      <div class="row g-3 mb-4">
        <div class="col-auto">
          <h2 class="mb-0">Vendors</h2>
        </div>
      </div>
      <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
                <span>All </span><span class="text-body-tertiary fw-semibold">({{ $total }})</span>
            </a>
        </li>
      </ul>
      <div id="vendors" data-list='{"valueNames":["name","phone","address","inventories"],"page":10,"pagination":true}'>
        <div class="mb-4">
          <div class="d-flex flex-wrap gap-3">
            <div class="search-box">
              <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search vendors" aria-label="Search" />
                <span class="fas fa-search search-box-icon"></span>
              </form>
            </div>
            <div class="ms-xxl-auto">
                <button class="btn btn-primary" id="addBtn" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><span class="fas fa-plus me-2"></span>Add new vendor</button>

                <div class="modal fade" id="staticBackdrop" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header justify-content-between bg-primary">
                          <h5 class="modal-title text-white dark__text-gray-1100" id="staticBackdropLabel">Add new vendor</h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9 text-white dark__text-gray-1100"></span></button>
                        </div>
                        <form class="needs-validation" novalidate="" method="post" action="{{ route('admin.add-vendor') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <input class="form-control" id="VendorName" type="text" name="name" placeholder="Vendor name" aria-label="Vendor name" required />
                                        <div class="invalid-feedback">Please enter a valid name.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <input class="form-control" id="VendorPhone" type="text" name="phone" placeholder="Phone number" aria-label="Phone number" />
                                    </div>
                                    <div class="col-md-12">
                                        <textarea class="form-control" id="VendorAddress" name="address" placeholder="Address" aria-label="Address" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit">Save</button>
                                <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
          <div class="table-responsive scrollbar mx-n1 px-1">
            <table class="table fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="sort align-middle ps-4" scope="col" data-sort="name">VENDOR NAME</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="phone">PHONE</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="address">ADDRESS</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="inventories">INVENTORIES</th>
                        <th class="sort align-middle ps-4" scope="col">ACTION</th>
                    </tr>
                </thead>
                <tbody class="list" id="vendors-table-body">
                    @foreach($vendors as $vendor)
                    <tr class="position-static">
                        <td class="name align-middle ps-4">
                            <b>{{ $vendor->name }}</b>
                        </td>
                        <td class="phone align-middle ps-4">
                            {{ $vendor->phone ?? 'N/A' }}
                        </td>
                        <td class="address align-middle ps-4">
                            {{ $vendor->address ?? 'N/A' }}
                        </td>
                        <td class="inventories align-middle ps-4">
                            {{ $vendor->inventories_count }} items
                        </td>
                        <td class="align-middle ps-4">
                            <button class="btn btn-phoenix-primary me-1 mb-1" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editVendorModal-{{ $vendor->vendor_id }}"
                                    title="Edit Vendor">
                                <span data-feather="edit-3"></span>
                            </button>
                            <button class="btn btn-danger me-1 mb-1" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteVendorModal-{{ $vendor->vendor_id }}"
                                    title="Delete Vendor">
                                <span data-feather="trash-2"></span>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editVendorModal-{{ $vendor->vendor_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Vendor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.update-vendor', $vendor->vendor_id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <input class="form-control" type="text" name="name"
                                                       value="{{ $vendor->name }}" required />
                                            </div>
                                            <div class="col-md-12">
                                                <input class="form-control" type="text" name="phone"
                                                       value="{{ $vendor->phone }}" placeholder="Phone number" />
                                            </div>
                                            <div class="col-md-12">
                                                <textarea class="form-control" name="address"
                                                          placeholder="Address" rows="2">{{ $vendor->address }}</textarea>
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

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteVendorModal-{{ $vendor->vendor_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Vendor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete <strong>"{{ $vendor->name }}"</strong>?</p>
                                    <ul class="text-danger">
                                        <li>This will delete {{ $vendor->inventories_count }} inventory items</li>
                                        <li>This action cannot be undone</li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.destroy-vendor', $vendor->vendor_id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
          </div>
          <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
              <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
            </div>
            <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
              <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <x-admin.footer />
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
@endsection
