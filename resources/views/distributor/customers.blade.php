@extends('layouts.distributor')

@section('title', 'Distributor : Customers')

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
    @if (session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div class="toast show text-white bg-success bg-opacity-75 border-0" role="alert">
            <div class="toast-body">{{ session('success') }}</div>
        </div>
    </div>
    @endif

    <div class="mb-9">
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h2 class="mb-0">Customers Directory</h2>
            </div>

            <div class="col-auto d-flex">
                <!-- Search Form -->
                <form action="{{ route('customers.index') }}" method="GET" class="d-flex me-3">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search name or mobile..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                    @endif
                </form>

                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <span class="fas fa-plus me-2"></span>Add New Customer
                </button>
            </div>
        </div>

        <div class="mx-n4 px-4 bg-body-emphasis border-top border-bottom border-translucent position-relative">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="align-middle ps-4">#</th>
                            <th class="align-middle">NAME</th>
                            <th class="align-middle">MOBILE</th>
                            <th class="align-middle">ADDRESS</th>
                            <th class="align-middle text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        <tr>
                            <td class="align-middle ps-4 fw-semibold">{{ $customers->firstItem() + $index }}</td>
                            <td class="align-middle fw-semibold">{{ $customer->name }}</td>
                            <td class="align-middle text-body-secondary">{{ $customer->mobile }}</td>
                            <td class="align-middle text-body-secondary">{{ $customer->address ?? 'N/A' }}</td>
                            <td class="align-middle text-end pe-4">
                                <a href="{{ url('/distributor/invoices?invoice_type=sale&customer_id=' . $customer->customer_id) }}" class="btn btn-sm btn-phoenix-success me-1">View Sale</a>
                                <a href="{{ url('/distributor/invoices?invoice_type=purchase&customer_id=' . $customer->customer_id) }}" class="btn btn-sm btn-phoenix-info me-1">View Purchase</a>
                                <button class="btn btn-sm btn-phoenix-secondary" data-bs-toggle="modal" data-bs-target="#editCustomerModal-{{ $customer->customer_id }}"><span data-feather="edit"></span> Edit</button>
                            </td>
                        </tr>

                        <!-- Edit Modal for this specific customer -->
                        <div class="modal fade" id="editCustomerModal-{{ $customer->customer_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Customer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('customers.update', $customer->customer_id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Name*</label>
                                                <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mobile*</label>
                                                <input type="text" name="mobile" class="form-control" value="{{ $customer->mobile }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No customers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-3 mb-3">
                {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Add New Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Add New Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name*</label>
                            <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile*</label>
                            <input type="text" name="mobile" class="form-control" placeholder="1234567890" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="123 Main St">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
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
@endsection
