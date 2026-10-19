@extends('layouts.distributor')

@section('title', 'Edit Loan')

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
    .form-section {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .section-title {
        color: #3a416f;
        font-weight: 600;
        margin-bottom: 1.25rem;
        position: relative;
        padding-bottom: 0.75rem;
    }
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background-color: #5e72e4;
        border-radius: 3px;
    }
    .required-field::after {
        content: " *";
        color: #f5365c;
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
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Edit Loan #{{ $loan->loan_no }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributor.loans') }}">Loans</a></li>
                <li class="breadcrumb-item active">Edit Loan</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-section">
                <h4 class="section-title">Loan Information</h4>
                <div class="alert alert-info">
                    <strong>Note:</strong> Changing EMI details will only affect future, unpaid EMIs.
                    Paid EMIs cannot be modified.
                </div>
            </div>

            <form action="{{ route('distributor.loan.update', $loan->loan_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <h4 class="section-title">Loan Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="financer_id" class="form-label required-field">Financer</label>
                                <select class="form-select" id="financer_id" name="financer_id" required>
                                    @foreach($financers as $financer)
                                        <option value="{{ $financer->financer_id }}"
                                            {{ $loan->financer_id == $financer->financer_id ? 'selected' : '' }}>
                                            {{ $financer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_slab" class="form-label required-field">EMI Date</label>
                                <select class="form-select" id="loan_slab" name="loan_slab" required>
                                    <option value="1" {{ $loan->loan_slab == 1 ? 'selected' : '' }}>1st of Month</option>
                                    <option value="15" {{ $loan->loan_slab == 15 ? 'selected' : '' }}>15th of Month</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="down_payment" class="form-label required-field">Down Payment (₹)</label>
                                <input type="number" class="form-control" id="down_payment" name="down_payment"
                                       value="{{ old('down_payment', $loan->down_payment) }}" min="0" max="{{ $loan->invoice->total_amount }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="processing_charge" class="form-label required-field">Processing Charge (₹)</label>
                                <input type="number" class="form-control" id="processing_charge" name="processing_charge"
                                       value="{{ old('processing_charge', $loan->processing_charge) }}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="file_charge" class="form-label required-field">File Charge (₹)</label>
                                <input type="number" class="form-control" id="file_charge" name="file_charge"
                                       value="{{ old('file_charge', $loan->file_charge) }}" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="emi_amount" class="form-label required-field">EMI Amount (₹)</label>
                                <input type="number" class="form-control" id="emi_amount" name="emi_amount"
                                       value="{{ old('emi_amount', $loan->emi_amount) }}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="emi_count" class="form-label required-field">Number of EMIs</label>
                                <input type="number" class="form-control" id="emi_count" name="emi_count"
                                       value="{{ old('emi_count', $loan->emi) }}" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="first_emi_date" class="form-label required-field">First EMI Date</label>
                                <input type="date" class="form-control" id="first_emi_date" name="first_emi_date"
                                       value="{{ old('first_emi_date', $loan->first_emi->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('distributor.loan.show', $loan->loan_id) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Loan</button>
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
        // Loan calculator logic similar to loan-create.blade.php
        const invoiceAmount = {{ $loan->invoice->total_amount }};
        const downPaymentInput = document.getElementById('down_payment');
        const processingChargeInput = document.getElementById('processing_charge');
        const fileChargeInput = document.getElementById('file_charge');
        const emiAmountInput = document.getElementById('emi_amount');
        const emiCountInput = document.getElementById('emi_count');

        function calculateLoan() {
            const downPayment = parseFloat(downPaymentInput.value) || 0;
            const processingCharge = parseFloat(processingChargeInput.value) || 0;
            const fileCharge = parseFloat(fileChargeInput.value) || 0;
            const emiAmount = parseFloat(emiAmountInput.value) || 0;
            const emiCount = parseInt(emiCountInput.value) || 0;

            const loanAmount = invoiceAmount - downPayment;
            const totalPayable = loanAmount + processingCharge + fileCharge;

            // Auto-fill EMI amount if not set
            if (emiCount > 0 && emiAmount === 0) {
                emiAmountInput.value = (totalPayable / emiCount).toFixed(2);
            }
        }

        [downPaymentInput, processingChargeInput, fileChargeInput, emiAmountInput, emiCountInput].forEach(input => {
            input.addEventListener('input', calculateLoan);
        });

        // Initial calculation
        calculateLoan();
    });
</script>
@endsection
