@extends('layouts.distributor')

@section('title', 'Create Loan')

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
    .document-upload {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
    }
    .document-upload:hover {
        border-color: #5e72e4;
        background-color: #f0f2f5;
    }
    .document-upload .icon {
        font-size: 2rem;
        color: #5e72e4;
        margin-bottom: 0.5rem;
    }
    .document-upload .upload-text {
        font-weight: 500;
        color: #5e72e4;
    }
    .document-upload input[type="file"] {
        display: none;
    }
    .document-preview {
        position: relative;
        width: 100%;
        height: 150px;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .remove-doc {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 24px;
        height: 24px;
        background-color: #f5365c;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .invoice-info-card {
        background-color: #f8f9fa;
        border-left: 4px solid #5e72e4;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .invoice-info-item {
        margin-bottom: 0.5rem;
    }
    .invoice-info-label {
        font-weight: 600;
        color: #525f7f;
    }
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .existing-doc {
        background-color: #e9ecef;
        padding: 0.5rem;
        border-radius: 0.25rem;
        margin-bottom: 0.5rem;
    }
    .existing-doc a {
        color: #5e72e4;
    }
    .loan-calculator {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .calculator-result {
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> Please fix the following issues:
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

<div class="content">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Create Loan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributor.invoices') }}">Invoices</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Loan</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-section">
                <h4 class="section-title">Invoice Information</h4>
                <div class="invoice-info-card">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="invoice-info-item">
                                <span class="invoice-info-label">Invoice No:</span>
                                <span>{{ $invoice->invoice_no }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-info-item">
                                <span class="invoice-info-label">Date:</span>
                                <span>{{ $invoice->invoice_date->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-info-item">
                                <span class="invoice-info-label">Amount:</span>
                                <span>₹{{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-info-item">
                                <span class="invoice-info-label">Type:</span>
                                <span>{{ ucfirst($invoice->invoice_type) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-info-item">
                                <span class="invoice-info-label">For:</span>
                                <span>{{ ucfirst($invoice->invoice_for) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('distributor.loan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_id }}">

                <div class="form-section">
                    <h4 class="section-title">Loan Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="financer_id" class="form-label required-field">Financer</label>
                                <select class="form-select" id="financer_id" name="financer_id" required>
                                    <option value="">Select Financer</option>
                                    @foreach($financers as $financer)
                                        <option value="{{ $financer->financer_id }}">{{ $financer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_slab" class="form-label required-field">EMI Date</label>
                                <select class="form-select" id="loan_slab" name="loan_slab" required>
                                    <option value="1">1st of Month</option>
                                    <option value="15">15th of Month</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="loan-calculator">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="down_payment" class="form-label required-field">Down Payment (₹)</label>
                                    <input type="number" class="form-control" id="down_payment" name="down_payment"
                                           value="{{ old('down_payment', 0) }}" min="0" max="{{ $invoice->total_amount }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="processing_charge" class="form-label required-field">Processing Charge (₹)</label>
                                    <input type="number" class="form-control" id="processing_charge" name="processing_charge"
                                           value="{{ old('processing_charge', 0) }}" min="0" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="file_charge" class="form-label required-field">File Charge (₹)</label>
                                    <input type="number" class="form-control" id="file_charge" name="file_charge"
                                           value="{{ old('file_charge', 0) }}" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emi_amount" class="form-label required-field">EMI Amount (₹)</label>
                                    <input type="number" class="form-control" id="emi_amount" name="emi_amount"
                                           value="{{ old('emi_amount') }}" min="0" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emi_count" class="form-label required-field">Number of EMIs</label>
                                    <input type="number" class="form-control" id="emi_count" name="emi_count"
                                           value="{{ old('emi_count') }}" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="first_emi_date" class="form-label required-field">First EMI Date</label>
                                    <input type="date" class="form-control" id="first_emi_date" name="first_emi_date"
                                           value="{{ old('first_emi_date') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="calculator-result">
                            <div>Loan Amount: ₹<span id="loan-amount">0.00</span></div>
                            <div>Total Payable: ₹<span id="total-payable">0.00</span></div>
                            <div>Total Interest: ₹<span id="total-interest">0.00</span></div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">{{ $isParty ? 'Party' : 'Customer' }} Information</h4>

                    @if(!$isParty && $subject)
                    <div class="alert alert-info mb-4">
                        This invoice is associated with an existing customer. You can update the customer information below.
                    </div>
                    @endif

                    @if($isParty)
                    <div class="alert alert-warning mb-4">
                        This invoice is for a party. We'll create a new customer record from the party information.
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label required-field">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name', $subject->name ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile" class="form-label required-field">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                       value="{{ old('mobile', $isParty ? ($subject->phone ?? '') : ($subject->mobile ?? '')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alternative_mobile" class="form-label">Alternative Mobile</label>
                                <input type="text" class="form-control" id="alternative_mobile" name="alternative_mobile"
                                       value="{{ old('alternative_mobile') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aadhaar_no" class="form-label required-field">Aadhaar Number</label>
                                <input type="text" class="form-control" id="aadhaar_no" name="aadhaar_no"
                                       value="{{ old('aadhaar_no', $isParty ? '' : ($subject->aadhaar_no ?? '')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pan_no" class="form-label">PAN Number</label>
                                <input type="text" class="form-control" id="pan_no" name="pan_no"
                                       value="{{ old('pan_no', $isParty ? ($subject->pan ?? '') : ($subject->pan_no ?? '')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label required-field">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $subject->address ?? '') }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">Customer Documents</h4>
                    <p class="text-muted mb-3">Upload required documents for the {{ $isParty ? 'new customer' : 'customer' }}</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="document-upload-container">
                                <label class="form-label">Photo</label>
                                <div class="document-upload" id="photo-upload">
                                    <i class="fas fa-camera icon"></i>
                                    <div class="upload-text">Click to upload photo</div>
                                    <input type="file" name="photo" accept="image/*">
                                </div>
                                <div class="document-preview" id="photo-preview" style="display:none;">
                                    <img src="" alt="Photo Preview">
                                    <div class="remove-doc" onclick="removeDocument('photo')">×</div>
                                </div>
                                @if(!$isParty && $subject && $subject->photo)
                                <div class="existing-doc">
                                    <small>Current file:</small>
                                    <a href="{{ Storage::disk('public_html')->url($subject->photo) }}" target="_blank" class="d-block">
                                        View Photo
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="document-upload-container">
                                <label class="form-label">Document 1</label>
                                <div class="document-upload" id="doc-one-upload">
                                    <i class="fas fa-file-alt icon"></i>
                                    <div class="upload-text">Click to upload document</div>
                                    <input type="file" name="doc_one" accept="image/*">
                                </div>
                                <div class="document-preview" id="doc-one-preview" style="display:none;">
                                    <img src="" alt="Document 1 Preview">
                                    <div class="remove-doc" onclick="removeDocument('doc-one')">×</div>
                                </div>
                                @if(!$isParty && $subject && $subject->doc_one)
                                <div class="existing-doc">
                                    <small>Current file:</small>
                                    <a href="{{ Storage::disk('public_html')->url($subject->doc_one) }}" target="_blank" class="d-block">
                                        View Document 1
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="document-upload-container">
                                <label class="form-label">Document 2</label>
                                <div class="document-upload" id="doc-two-upload">
                                    <i class="fas fa-file-alt icon"></i>
                                    <div class="upload-text">Click to upload document</div>
                                    <input type="file" name="doc_two" accept="image/*">
                                </div>
                                <div class="document-preview" id="doc-two-preview" style="display:none;">
                                    <img src="" alt="Document 2 Preview">
                                    <div class="remove-doc" onclick="removeDocument('doc-two')">×</div>
                                </div>
                                @if(!$isParty && $subject && $subject->doc_two)
                                <div class="existing-doc">
                                    <small>Current file:</small>
                                    <a href="{{ Storage::disk('public_html')->url($subject->doc_two) }}" target="_blank" class="d-block">
                                        View Document 2
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">Agreement Document</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="document-upload-container">
                                <div class="document-upload" id="agreement-upload">
                                    <i class="fas fa-file-contract icon"></i>
                                    <div class="upload-text">Click to upload agreement photo</div>
                                    <input type="file" name="agreement_photo" accept="image/*">
                                </div>
                                <div class="document-preview" id="agreement-preview" style="display:none;">
                                    <img src="" alt="Agreement Preview">
                                    <div class="remove-doc" onclick="removeDocument('agreement')">×</div>
                                </div>
                                <small class="text-muted">Max file size: 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('distributor.invoices') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Loan Agreement</button>
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
        // Initialize file upload previews
        initDocumentUpload('photo');
        initDocumentUpload('doc-one');
        initDocumentUpload('doc-two');
        initDocumentUpload('agreement');

        // Set up any existing previews from old input
        @foreach(['photo', 'doc_one', 'doc_two', 'agreement_photo'] as $file)
            @if(old($file . '_preview'))
                setPreview('{{ str_replace('_', '-', $file) }}', '{{ old($file . '_preview') }}');
            @endif
        @endforeach

        // Loan calculator
        const invoiceAmount = {{ $invoice->total_amount }};
        const downPaymentInput = document.getElementById('down_payment');
        const processingChargeInput = document.getElementById('processing_charge');
        const fileChargeInput = document.getElementById('file_charge');
        const emiAmountInput = document.getElementById('emi_amount');
        const emiCountInput = document.getElementById('emi_count');

        const loanAmountSpan = document.getElementById('loan-amount');
        const totalPayableSpan = document.getElementById('total-payable');
        const totalInterestSpan = document.getElementById('total-interest');

        function calculateLoan() {
            const downPayment = parseFloat(downPaymentInput.value) || 0;
            const processingCharge = parseFloat(processingChargeInput.value) || 0;
            const fileCharge = parseFloat(fileChargeInput.value) || 0;
            const emiAmount = parseFloat(emiAmountInput.value) || 0;
            const emiCount = parseInt(emiCountInput.value) || 0;

            const loanAmount = invoiceAmount - downPayment;
            const totalPayable = loanAmount + processingCharge + fileCharge;
            const totalInterest = totalPayable - loanAmount;

            loanAmountSpan.textContent = loanAmount.toFixed(2);
            totalPayableSpan.textContent = totalPayable.toFixed(2);
            totalInterestSpan.textContent = totalInterest.toFixed(2);

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

    function initDocumentUpload(type) {
        const uploadElement = document.getElementById(`${type}-upload`);
        const fileInput = uploadElement.querySelector('input[type="file"]');

        uploadElement.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    setPreview(type, e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    function setPreview(type, src) {
        const previewElement = document.getElementById(`${type}-preview`);
        const uploadElement = document.getElementById(`${type}-upload`);

        previewElement.querySelector('img').src = src;
        previewElement.style.display = 'block';
        uploadElement.style.display = 'none';
    }

    function removeDocument(type) {
        const previewElement = document.getElementById(`${type}-preview`);
        const uploadElement = document.getElementById(`${type}-upload`);
        const fileInput = uploadElement.querySelector('input[type="file"]');

        fileInput.value = '';
        previewElement.style.display = 'none';
        uploadElement.style.display = 'block';
    }
</script>
@endsection
