@extends('layouts.distributor')

@section('title', 'Agreements')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    .agreement-table {
        --phoenix-table-accent-bg: var(--phoenix-background-color-tertiary);
        --phoenix-table-striped-bg: var(--phoenix-background-color-tertiary);
        --phoenix-table-hover-bg: var(--phoenix-background-color-secondary);
    }
    .agreement-table th {
        background-color: var(--phoenix-background-color-tertiary);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .agreement-table td {
        vertical-align: middle;
    }
    .customer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .badge-active {
        background-color: #2cbe4e;
    }
    .badge-expired {
        background-color: #f5365c;
    }
    .badge-pending {
        background-color: #fb6340;
    }
    .action-btns .btn {
        padding: 0.375rem 0.5rem;
        border-radius: 0.375rem;
    }
    .search-box {
        max-width: 300px;
    }
    .filter-dropdown {
        min-width: 200px;
    }
    .agreement-preview {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .agreement-preview:hover {
        transform: scale(1.8);
        z-index: 10;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Agreements</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Agreements</h2>
            </div>
            <div class="col-sm-auto ms-auto">
                <div class="d-flex align-items-center gap-3">
                    <form class="search-box">
                        <div class="position-relative">
                            <input class="form-control search-input search" type="search" placeholder="Search agreements..." aria-label="Search">
                            <span class="fas fa-search search-box-icon"></span>
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-link text-900 me-4 px-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fas fa-filter fs--1"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end filter-dropdown py-0">
                            <div class="bg-white py-2 rounded-1">
                                <div class="px-3">
                                    <h5 class="mb-2 fs-0">Filter</h5>
                                    <div class="mb-3">
                                        <label class="form-label mb-0">Date Range</label>
                                        <input class="form-control form-control-sm datetimepicker" type="text" placeholder="Select date range" data-options='{"mode":"range","dateFormat":"d M Y","disableMobile":true}' />
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-phoenix-secondary">Reset</button>
                                        <button class="btn btn-sm btn-primary">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table table-hover table-striped overflow-hidden agreement-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Agreement ID</th>
                                <th>Customer</th>
                                <th>Invoice</th>
                                <th>Type</th>
                                <th>Created At</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agreements as $agreement)
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <span class="fw-semi-bold">#{{ $agreement->agreement_id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($agreement->customer->photo)
                                        <img class="customer-avatar" src="{{ Storage::disk('public_html')->url($agreement->customer->photo) }}" alt="{{ $agreement->customer->name }}">
                                        @else
                                        <div class="customer-avatar bg-soft-primary d-flex align-items-center justify-content-center">
                                            <span class="fs-0 fw-bold text-primary">{{ substr($agreement->customer->name, 0, 1) }}</span>
                                        </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $agreement->customer->name }}</h6>
                                            <small class="text-500">{{ $agreement->customer->mobile }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($agreement->invoices->isNotEmpty())
                                    <a href="{{ route('distributor.invoices.show', $agreement->invoices->first()->invoice_id) }}" class="fw-semi-bold">
                                        {{ $agreement->invoices->first()->invoice_no }}
                                    </a>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{ ucfirst($agreement->type) }}
                                </td>
                                <td>
                                    {{ $agreement->created_at->format('d M Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end action-btns">
                                        @if($agreement->photo)
                                        <a href="#" class="btn btn-sm btn-phoenix-info me-2" data-bs-toggle="modal" data-bs-target="#previewModal"
                                           data-image="{{ Storage::disk('public_html')->url($agreement->photo) }}">
                                            <span class="fas fa-eye"></span>
                                        </a>
                                        @endif
                                        <a href="{{ route('distributor.agreement.edit', $agreement->agreement_id) }}" class="btn btn-sm btn-phoenix-primary me-2">
                                            <span class="fas fa-edit"></span>
                                        </a>
                                        @if($agreement->doc)
                                        <a href="{{ Storage::disk('public_html')->url($agreement->doc) }}" target="_blank" class="btn btn-sm btn-phoenix-secondary">
                                            <span class="fas fa-file-pdf"></span>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <img src="{{ asset('assets/img/no-data.svg') }}" alt="No data found" style="height: 120px">
                                        <h5 class="mt-3">No agreements found</h5>
                                        <p class="text-muted">Create your first agreement to get started</p>
                                        <a href="{{ route('distributor.invoices') }}" class="btn btn-primary btn-sm mt-2">
                                            <span class="fas fa-plus me-2"></span>Create Agreement
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($agreements->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between py-2">
                    <p class="mb-0 text-700">
                        Showing <span class="fw-semi-bold">{{ $agreements->firstItem() }}</span> to
                        <span class="fw-semi-bold">{{ $agreements->lastItem() }}</span> of
                        <span class="fw-semi-bold">{{ $agreements->total() }}</span> entries
                    </p>
                    <div>
                        {{ $agreements->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agreement Preview</h5>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span class="fas fa-times fs--1"></span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPreviewImage" src="" alt="Agreement Preview" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                <a id="downloadPreviewBtn" href="#" class="btn btn-phoenix-primary" download>
                    <span class="fas fa-download me-2"></span>Download
                </a>
            </div>
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
        // Initialize date picker
        if (document.querySelector('.datetimepicker')) {
            flatpickr('.datetimepicker', {
                mode: "range",
                dateFormat: "d M Y",
                disableMobile: true
            });
        }

        // Preview modal functionality
        const previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imageUrl = button.getAttribute('data-image');
                const modalImage = document.getElementById('modalPreviewImage');
                const downloadBtn = document.getElementById('downloadPreviewBtn');

                modalImage.src = imageUrl;
                downloadBtn.href = imageUrl;
                downloadBtn.setAttribute('download', 'agreement-' + new Date().toISOString().slice(0, 10) + '.jpg');
            });
        }

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('.agreement-table tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection
