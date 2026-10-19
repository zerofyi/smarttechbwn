@extends('layouts.distributor')

@section('title', 'Distributor : Employee Trackings')

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
                <h2 class="mb-0">Employee Incentives & Sales</h2>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <span class="fas fa-plus me-2"></span>Add New Employee
                </button>
            </div>
        </div>

        <div class="mx-n4 px-4 bg-body-emphasis border-top border-bottom border-translucent position-relative">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="align-middle ps-4">EMPLOYEE DETAILS</th>
                            <th class="align-middle text-end">INCENTIVE RATE</th>
                            <th class="align-middle text-end">LAST MONTH SALES</th>
                            <th class="align-middle text-end">THIS MONTH SALES</th>
                            <th class="align-middle text-end">TOTAL CUMULATIVE</th>
                            <th class="align-middle text-end pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td class="align-middle ps-4">
                                <h6 class="mb-0 fw-semibold">{{ $emp->name }}</h6>
                                <p class="mb-0 text-body-secondary fs-10">{{ $emp->phone }} | {{ $emp->email ?? 'No Email' }}</p>
                            </td>
                            <td class="align-middle text-end fw-bold text-primary">{{ $emp->incentive_percentage }}%</td>
                            <td class="align-middle text-end text-body-secondary">₹{{ number_format($emp->last_month_sales ?? 0, 2) }}</td>
                            <td class="align-middle text-end text-success fw-semibold">₹{{ number_format($emp->this_month_sales ?? 0, 2) }}</td>
                            <td class="align-middle text-end fw-bold">₹{{ number_format($emp->total_sales_amount ?? 0, 2) }}</td>
                            <td class="align-middle text-end pe-4">
                                <a href="{{ route('employees.sales', $emp->employee_id) }}" class="btn btn-sm btn-phoenix-primary me-2">View Sales</a>
                                <button class="btn btn-sm btn-phoenix-secondary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal-{{ $emp->employee_id }}"><span data-feather="edit"></span> Edit</button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editEmployeeModal-{{ $emp->employee_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Employee Properties</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('employees.update', $emp->employee_id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $emp->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone Reference</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $emp->phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email Access</label>
                                                <input type="email" name="email" class="form-control" value="{{ $emp->email }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Default Scale Incentive Ratio (%)</label>
                                                <input type="number" name="incentive_percentage" step="0.01" class="form-control" value="{{ $emp->incentive_percentage }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Add New Employee Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name*</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Reference Number*</label>
                            <input type="text" name="phone" class="form-control" placeholder="Phone layout contact" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Context</label>
                            <input type="email" name="email" class="form-control" placeholder="name@domain.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Incentive Threshold Ratio (%)</label>
                            <input type="number" name="incentive_percentage" step="0.01" class="form-control" placeholder="2.5" value="0.00" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
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
