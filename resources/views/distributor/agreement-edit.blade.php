@extends('layouts.distributor')

@section('title', 'Edit Agreement')

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
    /* Same styles as agreement-create.blade.php */
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

<div class="content">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Edit Agreement</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributor.invoices') }}">Invoices</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Agreement</li>
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
                                <span class="invoice-info-label">Status:</span>
                                @if($invoice->due_amount <= 0)
                                    <span class="status-badge bg-success">Paid</span>
                                @elseif($invoice->paid > 0)
                                    <span class="status-badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="status-badge bg-danger">Unpaid</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('distributor.agreement.update', $agreement->agreement_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_id }}">
                <input type="hidden" name="shop_id" value="{{ $shop->shop_id }}">

                <div class="form-section">
                    <h4 class="section-title">Customer Information</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label required-field">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name', $formData['name'] ?? '') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile" class="form-label required-field">Mobile Number</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                       id="mobile" name="mobile"
                                       value="{{ old('mobile', $formData['mobile'] ?? '') }}"
                                       required>
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aadhaar_no" class="form-label">Aadhaar Number</label>
                                <input type="text" class="form-control @error('aadhaar_no') is-invalid @enderror"
                                       id="aadhaar_no" name="aadhaar_no"
                                       value="{{ old('aadhaar_no', $formData['aadhaar_no'] ?? '') }}">
                                @error('aadhaar_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pan_no" class="form-label">PAN Number</label>
                                <input type="text" class="form-control @error('pan_no') is-invalid @enderror"
                                       id="pan_no" name="pan_no"
                                       value="{{ old('pan_no', $formData['pan_no'] ?? '') }}">
                                @error('pan_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label required-field">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                  name="address" rows="3" required>{{ old('address', $formData['address'] ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">Customer Documents</h4>
                    <p class="text-muted mb-3">Update customer documents as needed</p>

                    <div class="row">
                        @foreach(['photo', 'signature', 'doc_one', 'doc_two', 'doc_three'] as $docType)
                        <div class="col-md-4 mb-4">
                            <div class="document-upload-container">
                                <label class="form-label">{{ ucfirst(str_replace('_', ' ', $docType)) }}</label>

                                @if($customer->{$docType})
                                <div class="existing-doc mb-2">
                                    <small>Current file:</small>
                                    <a href="{{ Storage::disk('public_html')->url($customer->{$docType}) }}" target="_blank" class="d-block">
                                        View {{ $docType }}
                                    </a>
                                </div>
                                @endif

                                <div class="document-upload" id="{{ $docType }}-upload">
                                    <i class="fas fa-upload icon"></i>
                                    <div class="upload-text">Click to upload {{ $docType }}</div>
                                    <input type="file" name="{{ $docType }}" accept="image/*">
                                </div>
                                <div class="document-preview" id="{{ $docType }}-preview" style="display:none;">
                                    <img src="" alt="{{ ucfirst($docType) }} Preview">
                                    <div class="remove-doc" onclick="removeDocument('{{ $docType }}')">×</div>
                                </div>
                                <small class="text-muted">Max 2MB (JPEG, PNG, JPG)</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">Agreement Document</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="document-upload-container">
                                @if($agreement->photo)
                                <div class="existing-doc mb-3">
                                    <small>Current agreement photo:</small>
                                    <a href="{{ Storage::disk('public_html')->url($agreement->photo) }}" target="_blank" class="d-block">
                                        View Agreement Photo
                                    </a>
                                </div>
                                @endif

                                <div class="document-upload" id="agreement-upload">
                                    <i class="fas fa-file-contract icon"></i>
                                    <div class="upload-text">Click to update agreement photo</div>
                                    <input type="file" name="agreement_photo" accept="image/*">
                                </div>
                                <div class="document-preview" id="agreement-preview" style="display:none;">
                                    <img src="" alt="Agreement Preview">
                                    <div class="remove-doc" onclick="removeDocument('agreement')">×</div>
                                </div>
                                <small class="text-muted">Max 2MB (JPEG, PNG, JPG)</small>
                                @error('agreement_photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('distributor.invoices.show', $invoice->invoice_id) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Agreement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
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
        initDocumentUpload('signature');
        initDocumentUpload('doc-one');
        initDocumentUpload('doc-two');
        initDocumentUpload('doc-three');
        initDocumentUpload('agreement');

        // Set up any existing previews from old input
        @foreach(['photo', 'signature', 'doc_one', 'doc_two', 'doc_three', 'agreement_photo'] as $file)
            @if(old($file . '_preview'))
                setPreview('{{ str_replace('_', '-', $file) }}', '{{ old($file . '_preview') }}');
            @endif
        @endforeach
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
