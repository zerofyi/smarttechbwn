@extends('layouts.distributor')

@section('title', 'Distributor : Financers')

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
            <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Financers</li>
        </ol>
    </nav>
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Financers</h2>
            </div>
        </div>
        <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">
                    <span>All </span><span class="text-body-tertiary fw-semibold">({{ $total }})</span>
                </a>
            </li>
        </ul>
        <div id="financers" data-list='{"valueNames":["name","mobile"],"page":10,"pagination":true}'>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-3">
                    <div class="search-box">
                        <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search financers" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                    <div class="ms-xxl-auto">
                        <button class="btn btn-primary" id="addBtn" type="button" data-bs-toggle="modal" data-bs-target="#addFinancerModal"><span class="fas fa-plus me-2"></span>Add new financer</button>
                    </div>
                </div>
            </div>
            <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                <div class="table-responsive scrollbar mx-n1 px-1">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="sort align-middle ps-4" scope="col" data-sort="name">FINANCER</th>
                                <th class="sort align-middle ps-4" scope="col" data-sort="mobile">CONTACT</th>
                                <th class="sort align-middle ps-4" scope="col">ACTIVE LOANS</th>
                                <th class="sort align-middle ps-4" scope="col">TOTAL AMOUNT</th>
                                <th class="sort align-middle ps-4" scope="col">RECOVERABLE</th>
                                <th class="sort align-middle ps-4" scope="col">RECOVERED</th>
                                <th class="sort align-middle ps-4" scope="col">PENDING</th>
                                <th class="sort align-middle ps-4" scope="col">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="financers-table-body">
                            @foreach($financers as $financer)
                            @php
                                $activeLoans = $financer->loans()->where('loan_status', 'open')->get();
                                $totalLoanAmount = $activeLoans->sum('loan_amount');
                                $totalPayableAmount = $activeLoans->sum('payable_amount');
                                $totalPaidAmount = $activeLoans->sum('paid_amount');
                                $pendingAmount = $totalPayableAmount - $totalPaidAmount;
                                $recoveryPercentage = $totalPayableAmount > 0 ? ($totalPaidAmount / $totalPayableAmount) * 100 : 0;
                            @endphp
                            <tr class="position-static">
                                <td class="name align-middle ps-4">
                                    <div class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#viewFinancerModal-{{ $financer->financer_id }}">
                                        @if($financer->photo)
                                            <img src="{{ asset('storage/'.$financer->photo) }}" alt="{{ $financer->name }}" class="rounded-circle me-3" width="40" height="40">
                                        @else
                                            <div class="avatar avatar-xl me-3">
                                                <div class="avatar-name rounded-circle bg-soft-primary text-primary">
                                                    <span>{{ substr($financer->name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $financer->name }}</h6>
                                            <p class="mb-0 text-body-secondary">ID: {{ $financer->id_no ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="mobile align-middle ps-4">
                                    <p class="mb-0">{{ $financer->mobile }}</p>
                                    <p class="mb-0 text-body-secondary">{{ $financer->state ?? 'N/A' }}</p>
                                </td>
                                <td class="align-middle ps-4 text-center">
                                    <a href="{{ route('distributor.loans', ['status'=>'open', 'financer_id'=>$financer->financer_id]) }}">{{ $activeLoans->count() }}</a>
                                </td>
                                <td class="align-middle ps-4">
                                    ₹{{ number_format($totalLoanAmount, 2) }}
                                </td>
                                <td class="align-middle ps-4">
                                    ₹{{ number_format($totalPayableAmount, 2) }}
                                </td>
                                <td class="align-middle ps-4">
                                    ₹{{ number_format($totalPaidAmount, 2) }}
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ $recoveryPercentage }}%"
                                             aria-valuenow="{{ $recoveryPercentage }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($recoveryPercentage, 1) }}%</small>
                                </td>
                                <td class="align-middle ps-4">
                                    ₹{{ number_format($pendingAmount, 2) }}
                                </td>
                                <td class="align-middle ps-4">
                                    <a class="btn btn-phoenix-primary me-1 mb-1" href="{{ route('distributor.loans', ['status'=>'open', 'financer_id'=>$financer->financer_id]) }}">
                                        <span data-feather="eye"></span>
                                    </a>
                                    <button class="btn btn-phoenix-secondary me-1 mb-1" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editFinancerModal-{{ $financer->financer_id }}"
                                            title="Edit">
                                        <span data-feather="edit-3"></span>
                                    </button>
                                </td>
                            </tr>

                            <!-- View Financer Modal -->
                            <div class="modal fade" id="viewFinancerModal-{{ $financer->financer_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title text-white">Financer Details</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center mb-4">
                                                    @if($financer->photo)
                                                        <img src="{{ asset('storage/'.$financer->photo) }}" alt="{{ $financer->name }}" class="rounded-circle mb-3" width="120" height="120">
                                                    @else
                                                        <div class="avatar avatar-4xl mb-3">
                                                            <div class="avatar-name rounded-circle bg-soft-primary text-primary">
                                                                <span>{{ substr($financer->name, 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <h4>{{ $financer->name }}</h4>
                                                    <p class="text-muted mb-1">ID: {{ $financer->id_no ?? 'N/A' }}</p>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p><strong>Mobile:</strong> {{ $financer->mobile }}</p>
                                                            <p><strong>Address:</strong> {{ $financer->address ?? 'N/A' }}</p>
                                                            <p><strong>State:</strong> {{ $financer->state ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>GSTIN:</strong> {{ $financer->gstin ?? 'N/A' }}</p>
                                                            <p><strong>PAN:</strong> {{ $financer->pan ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h5 class="mb-3">Bank Details</h5>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p><strong>Bank Name:</strong> {{ $financer->bank ?? 'N/A' }}</p>
                                                            <p><strong>Account No:</strong> {{ $financer->ac_no ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>IFSC Code:</strong> {{ $financer->ifsc ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h5 class="mb-3">Loan Statistics</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Active Loans:</strong> {{ $activeLoans->count() }}</p>
                                                            <p><strong>Total Loan Amount:</strong> ₹{{ number_format($totalLoanAmount, 2) }}</p>
                                                            <p><strong>Total Recoverable:</strong> ₹{{ number_format($totalPayableAmount, 2) }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Total Recovered:</strong> ₹{{ number_format($totalPaidAmount, 2) }}</p>
                                                            <p><strong>Pending Amount:</strong> ₹{{ number_format($pendingAmount, 2) }}</p>
                                                            <p><strong>Recovery Percentage:</strong> {{ number_format($recoveryPercentage, 1) }}%</p>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h5 class="mb-3">Documents</h5>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if($financer->doc_one)
                                                            <a href="{{ asset($financer->doc_one) }}" target="_blank" class="badge badge-phoenix badge-phoenix-primary">Doc 1</a>
                                                        @endif
                                                        @if($financer->doc_two)
                                                            <a href="{{ asset($financer->doc_two) }}" target="_blank" class="badge badge-phoenix badge-phoenix-primary">Doc 2</a>
                                                        @endif
                                                        @if($financer->doc_three)
                                                            <a href="{{ asset($financer->doc_three) }}" target="_blank" class="badge badge-phoenix badge-phoenix-primary">Doc 3</a>
                                                        @endif
                                                        @if($financer->signature)
                                                            <a href="{{ asset($financer->signature) }}" target="_blank" class="badge badge-phoenix badge-phoenix-success">Signature</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editFinancerModal-{{ $financer->financer_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Financer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('distributor.update-financer', $financer->financer_id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerName">Name*</label>
                                                        <input class="form-control" id="EditFinancerName" type="text" name="name"
                                                               value="{{ $financer->name }}" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerMobile">Mobile*</label>
                                                        <input class="form-control" id="EditFinancerMobile" type="text" name="mobile"
                                                               value="{{ $financer->mobile }}" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerIdNo">ID Number</label>
                                                        <input class="form-control" id="EditFinancerIdNo" type="text" name="id_no"
                                                               value="{{ $financer->id_no }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerPhoto">Photo</label>
                                                        <input class="form-control" id="EditFinancerPhoto" type="file" name="photo" accept="image/*" />
                                                        @if($financer->photo)
                                                            <small class="text-muted">Current: <a href="{{ asset($financer->photo) }}" target="_blank">View</a></small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label" for="EditFinancerAddress">Address</label>
                                                        <textarea class="form-control" id="EditFinancerAddress" name="address"
                                                                  rows="2">{{ $financer->address }}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerState">State</label>
                                                        <input class="form-control" id="EditFinancerState" type="text" name="state"
                                                               value="{{ $financer->state }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerGSTIN">GSTIN</label>
                                                        <input class="form-control" id="EditFinancerGSTIN" type="text" name="gstin"
                                                               value="{{ $financer->gstin }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerPAN">PAN</label>
                                                        <input class="form-control" id="EditFinancerPAN" type="text" name="pan"
                                                               value="{{ $financer->pan }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerBank">Bank Name</label>
                                                        <input class="form-control" id="EditFinancerBank" type="text" name="bank"
                                                               value="{{ $financer->bank }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerIFSC">IFSC Code</label>
                                                        <input class="form-control" id="EditFinancerIFSC" type="text" name="ifsc"
                                                               value="{{ $financer->ifsc }}" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerAccount">Account Number</label>
                                                        <input class="form-control" id="EditFinancerAccount" type="text" name="ac_no"
                                                               value="{{ $financer->ac_no }}" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="EditFinancerDocOne">Document 1</label>
                                                        <input class="form-control" id="EditFinancerDocOne" type="file" name="doc_one" accept="image/*,.pdf" />
                                                        @if($financer->doc_one)
                                                            <small class="text-muted">Current: <a href="{{ asset($financer->doc_one) }}" target="_blank">View</a></small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="EditFinancerDocTwo">Document 2</label>
                                                        <input class="form-control" id="EditFinancerDocTwo" type="file" name="doc_two" accept="image/*,.pdf" />
                                                        @if($financer->doc_two)
                                                            <small class="text-muted">Current: <a href="{{ asset($financer->doc_two) }}" target="_blank">View</a></small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="EditFinancerDocThree">Document 3</label>
                                                        <input class="form-control" id="EditFinancerDocThree" type="file" name="doc_three" accept="image/*,.pdf" />
                                                        @if($financer->doc_three)
                                                            <small class="text-muted">Current: <a href="{{ asset($financer->doc_three) }}" target="_blank">View</a></small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="EditFinancerSignature">Signature</label>
                                                        <input class="form-control" id="EditFinancerSignature" type="file" name="signature" accept="image/*" />
                                                        @if($financer->signature)
                                                            <small class="text-muted">Current: <a href="{{ asset($financer->signature) }}" target="_blank">View</a></small>
                                                        @endif
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
                            <div class="modal fade" id="deleteFinancerModal-{{ $financer->financer_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Financer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete <strong>"{{ $financer->name }}"</strong>?</p>
                                            <ul class="text-danger">
                                                <li>This will permanently delete all associated documents</li>
                                                <li>This action cannot be undone</li>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="{{ route('distributor.destroy-financer', $financer->financer_id) }}">
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

<!-- Add Financer Modal -->
<div class="modal fade" id="addFinancerModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addFinancerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-content-between bg-primary">
                <h5 class="modal-title text-white dark__text-gray-1100" id="addFinancerModalLabel">Add new financer</h5>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span class="fas fa-times fs-9 text-white dark__text-gray-1100"></span>
                </button>
            </div>
            <form class="needs-validation" novalidate method="post" action="{{ route('distributor.add-financer') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerName">Name*</label>
                            <input class="form-control" id="FinancerName" type="text" name="name" placeholder="Financer name" required />
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerMobile">Mobile*</label>
                            <input class="form-control" id="FinancerMobile" type="text" name="mobile" placeholder="Mobile number" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerIdNo">ID Number</label>
                            <input class="form-control" id="FinancerIdNo" type="text" name="id_no" placeholder="ID number" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerPhoto">Photo</label>
                            <input class="form-control" id="FinancerPhoto" type="file" name="photo" accept="image/*" />
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="FinancerAddress">Address</label>
                            <textarea class="form-control" id="FinancerAddress" name="address" placeholder="Address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerState">State</label>
                            <input class="form-control" id="FinancerState" type="text" name="state" placeholder="State" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerGSTIN">GSTIN</label>
                            <input class="form-control" id="FinancerGSTIN" type="text" name="gstin" placeholder="GSTIN" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerPAN">PAN</label>
                            <input class="form-control" id="FinancerPAN" type="text" name="pan" placeholder="PAN" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerBank">Bank Name</label>
                            <input class="form-control" id="FinancerBank" type="text" name="bank" placeholder="Bank name" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerIFSC">IFSC Code</label>
                            <input class="form-control" id="FinancerIFSC" type="text" name="ifsc" placeholder="IFSC code" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerAccount">Account Number</label>
                            <input class="form-control" id="FinancerAccount" type="text" name="ac_no" placeholder="Account number" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="FinancerDocOne">Document 1</label>
                            <input class="form-control" id="FinancerDocOne" type="file" name="doc_one" accept="image/*,.pdf" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="FinancerDocTwo">Document 2</label>
                            <input class="form-control" id="FinancerDocTwo" type="file" name="doc_two" accept="image/*,.pdf" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="FinancerDocThree">Document 3</label>
                            <input class="form-control" id="FinancerDocThree" type="file" name="doc_three" accept="image/*,.pdf" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="FinancerSignature">Signature</label>
                            <input class="form-control" id="FinancerSignature" type="file" name="signature" accept="image/*" />
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
