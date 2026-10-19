@extends('layouts.distributor')

@section('title', 'Loan Details')

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
<style>
    .loan-detail-card {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .detail-label {
        font-weight: 600;
        color: #525f7f;
    }
    .detail-value {
        color: #333;
    }
    .emi-table {
        width: 100%;
    }
    .emi-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .emi-paid {
        background-color: #e6f7ee;
    }
    .emi-overdue {
        background-color: #fff5f5;
    }
    .emi-pending {
        background-color: #fff9e6;
    }
    .progress-thin {
        height: 5px;
    }
</style>
@endsection

@section('content')
<div class="content">
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
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('distributor.loans') }}">Loans</a></li>
            <li class="breadcrumb-item active">Loan Details</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Loan #{{ $loan->loan_no }}</h2>
            </div>
            <div class="col-sm-auto ms-auto">
                <div class="d-flex align-items-center gap-3">
                    @if ($loan->loan_status !== 'closed')
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#loanSettleModal">
                            <span class="fas fa-check me-2"></span>Settle Now
                        </button>
                    @endif
                    <a href="{{ route('distributor.loan.print', $loan->loan_id) }}" class="btn btn-sm btn-phoenix-secondary" target="_blank">
                        <span class="fas fa-print me-2"></span>Print
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="loan-detail-card">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Loan Details</h5>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Loan Number:</div>
                                <div class="col-7 detail-value">{{ $loan->loan_no }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Financer:</div>
                                <div class="col-7 detail-value">{{ $loan->financer->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Invoice:</div>
                                <div class="col-7 detail-value">
                                    @if($loan->invoice)
                                    <a href="{{ route('distributor.invoices.show', $loan->invoice_id) }}">
                                        {{ $loan->invoice->invoice_no }}
                                    </a>
                                    @else
                                    N/A
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Loan Date:</div>
                                <div class="col-7 detail-value">{{ $loan->loan_date->format('d M Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">EMI Date:</div>
                                <div class="col-7 detail-value">{{ $loan->loan_slab == 1 ? '1st' : '15th' }} of Month</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Amount Details</h5>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Invoice Amount:</div>
                                <div class="col-7 detail-value">₹{{ number_format($loan->invoice_amount, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Down Payment:</div>
                                <div class="col-7 detail-value">₹{{ number_format($loan->down_payment, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Loan Amount:</div>
                                <div class="col-7 detail-value">₹{{ number_format($loan->loan_amount, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Processing Charge:</div>
                                <div class="col-7 detail-value">₹{{ number_format($loan->processing_charge, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">File Charge:</div>
                                <div class="col-7 detail-value">₹{{ number_format($loan->file_charge, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 detail-label">Total Payable:</div>
                                <div class="col-7 detail-value fw-bold">₹{{ number_format($loan->payable_amount, 2) }}</div>
                            </div>
                        </div>
                        @if ($loan->settled_amount > 0)
                            <p class="text-warning"><strong class="text-success">Settled: </strong> The initial payable amount was <strong>₹{{$loan->payable_amount+$loan->settled_amount}}</strong>, which included an <strong>₹{{$loan->processing_charge+$loan->settled_amount}}</strong> processing charge. The loan was settled with a <strong>₹{{$loan->settled_amount}}</strong> discount on the processing charge.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">Payment Progress</h5>
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="progress progress-thin mb-2">
                                    @php
                                        $progress = ($loan->paid_amount / $loan->payable_amount) * 100;
                                        $progressClass = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="fw-bold">{{ round($progress) }}% Paid (₹{{ number_format($loan->paid_amount, 2) }} of ₹{{ number_format($loan->payable_amount, 2) }})</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">EMI Schedule</h5>
                            <span class="badge bg-soft-primary">Total EMIs: {{ $loan->emi }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered emi-table">
                                <thead>
                                    <tr>
                                        <th>EMI No</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->emis as $emi)
                                    @php
                                        $statusClass = $emi->paid_amount >= $emi->emi_amount ? 'emi-paid' :
                                                      (now()->gt($emi->emi_date) ? 'emi-overdue' : 'emi-pending');
                                        $statusText = $emi->paid_amount >= $emi->emi_amount ? 'Paid' :
                                                     (now()->gt($emi->emi_date) ? 'Overdue' : 'Pending');
                                    @endphp
                                    <tr class="{{ $statusClass }}">
                                        <td>{{ $emi->emi_no }}</td>
                                        <td>{{ $emi->emi_date->format('d M Y') }}</td>
                                        <td>₹{{ number_format($emi->emi_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $statusText == 'Paid' ? 'success' : ($statusText == 'Overdue' ? 'danger' : 'warning') }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>{{ $emi->paid_date ? $emi->paid_date->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($emi->paid_amount < $emi->emi_amount)
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payEmiModal"
                                                    data-emi-id="{{ $emi->emi_id }}" data-emi-amount="{{ $emi->emi_amount }}">
                                                Pay Now
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="loan-detail-card">
                    <h5 class="mb-3">Customer Details</h5>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">Name:</div>
                        <div class="col-7 detail-value">{{ $loan->name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">Mobile:</div>
                        <div class="col-7 detail-value">{{ $loan->mobile }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">Alt. Mobile:</div>
                        <div class="col-7 detail-value">{{ $loan->alternative_mobile ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">Aadhaar:</div>
                        <div class="col-7 detail-value">{{ $loan->aadhaar_no }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">PAN:</div>
                        <div class="col-7 detail-value">{{ $loan->pan_no ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 detail-label">Address:</div>
                        <div class="col-7 detail-value">{{ $loan->address }}</div>
                    </div>
                </div>

                <div class="loan-detail-card">
                    <h5 class="mb-3">Documents</h5>
                    <div class="row">
                        @if($loan->photo)
                        <div class="col-md-6 mb-3">
                            <div class="document-thumbnail">
                                <img src="{{ Storage::disk('public_html')->url($loan->photo) }}" alt="Customer Photo" class="img-thumbnail w-100">
                                <small class="d-block text-center mt-1">Photo</small>
                            </div>
                        </div>
                        @endif
                        @if($loan->doc_one)
                        <div class="col-md-6 mb-3">
                            <div class="document-thumbnail">
                                <img src="{{ Storage::disk('public_html')->url($loan->doc_one) }}" alt="Document 1" class="img-thumbnail w-100">
                                <small class="d-block text-center mt-1">Document 1</small>
                            </div>
                        </div>
                        @endif
                        @if($loan->doc_two)
                        <div class="col-md-6 mb-3">
                            <div class="document-thumbnail">
                                <img src="{{ Storage::disk('public_html')->url($loan->doc_two) }}" alt="Document 2" class="img-thumbnail w-100">
                                <small class="d-block text-center mt-1">Document 2</small>
                            </div>
                        </div>
                        @endif
                        @if($loan->doc_three)
                        <div class="col-md-6 mb-3">
                            <div class="document-thumbnail">
                                <img src="{{ Storage::disk('public_html')->url($loan->doc_three) }}" alt="Document 3" class="img-thumbnail w-100">
                                <small class="d-block text-center mt-1">Document 3</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pay EMI Modal -->
<div class="modal fade" id="payEmiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record EMI Payment</h5>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span class="fas fa-times fs--1"></span>
                </button>
            </div>
            <form action="{{ route('distributor.loan.pay-emi', $loan->loan_id) }}" method="POST">
                @csrf
                <input type="hidden" name="emi_id" id="modalEmiId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">EMI Amount</label>
                        <input type="number" class="form-control" id="modalEmiAmount" name="emi_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" name="paid_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="pay_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Record Payment</button>
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loan Settle Modal -->
<div class="modal fade" id="loanSettleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Settle this loan</h5>
            </div>
            <form action="{{ route('distributor.loan.settle', $loan->loan_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p><strong>Processing Charges: </strong>₹{{$loan->processing_charge}}</p>
                    <p><strong>Due Amount: </strong>₹{{$loan->payable_amount}} - ₹{{$loan->paid_amount}} = ₹{{$loan->payable_amount-$loan->paid_amount}}</p>
                    <div class="mb-3">
                        <label class="form-label">Settle Discount</label>
                        <input type="number" class="form-control" name="discount" max="{{$loan->processing_charge}}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Settle Now</button>
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pay EMI Modal
        const payEmiModal = document.getElementById('payEmiModal');
        if (payEmiModal) {
            payEmiModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const emiId = button.getAttribute('data-emi-id');
                const emiAmount = button.getAttribute('data-emi-amount');

                document.getElementById('modalEmiId').value = emiId;
                document.getElementById('modalEmiAmount').value = emiAmount;
            });
        }
    });
</script>
@endsection
