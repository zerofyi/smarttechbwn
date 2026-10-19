@extends('layouts.distributor')

@section('title', 'Loans')

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
    .loan-table {
        --phoenix-table-accent-bg: var(--phoenix-background-color-tertiary);
        --phoenix-table-striped-bg: var(--phoenix-background-color-tertiary);
        --phoenix-table-hover-bg: var(--phoenix-background-color-secondary);
    }
    .loan-table th {
        background-color: var(--phoenix-background-color-tertiary);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .loan-table td {
        vertical-align: middle;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .badge-open {
        background-color: #2cbe4e;
    }
    .badge-closed {
        background-color: #6c757d;
    }
    .badge-overdue {
        background-color: #f5365c;
    }
    .progress-thin {
        height: 5px;
    }
    .search-box {
        max-width: 300px;
    }
    .filter-dropdown {
        min-width: 200px;
    }
</style>
@endsection

@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('distributor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Loans</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Loan Management</h2>
            </div>
            <div class="col-sm-auto ms-auto">
                <div class="d-flex align-items-center gap-3">
                    <form class="search-box" action="{{ route('distributor.loans') }}" method="GET">
                        <div class="position-relative">
                            <input class="form-control search-input search" type="search"
                                name="search" placeholder="Search loans..."
                                value="{{ request('search') }}" aria-label="Search">
                            <span class="fas fa-search search-box-icon"></span>
                        </div>
                        <!-- Include existing filters as hidden fields -->
                        @if(request()->has('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request()->has('financer_id'))
                            <input type="hidden" name="financer_id" value="{{ request('financer_id') }}">
                        @endif
                        @if(request()->has('loan_slab'))
                            <input type="hidden" name="loan_slab" value="{{ request('loan_slab') }}">
                        @endif
                        @if(request()->has('from_date'))
                            <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                        @endif
                        @if(request()->has('to_date'))
                            <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                        @endif
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-phoenix-primary" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fas fa-filter fs--1"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end filter-dropdown py-0">
                            <div class="bg-white py-2 rounded-1">
                                <form id="filter-form" action="{{ route('distributor.loans') }}" method="GET">
                                    <div class="px-3">
                                        <h5 class="mb-2 fs-0">Filter</h5>

                                        <!-- Status Filter -->
                                        <div class="mb-3">
                                            <label class="form-label mb-0">Status</label>
                                            <select class="form-select form-select-sm" name="status">
                                                <option value="">All Status</option>
                                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            </select>
                                        </div>

                                        <!-- Financer Filter -->
                                        <div class="mb-3">
                                            <label class="form-label mb-0">Financer</label>
                                            <select class="form-select form-select-sm" name="financer_id">
                                                <option value="">All Financers</option>
                                                @foreach($financers as $financer)
                                                    <option value="{{ $financer->financer_id }}"
                                                        {{ request('financer_id') == $financer->financer_id ? 'selected' : '' }}>
                                                        {{ $financer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- EMI Date Filter -->
                                        <div class="mb-3">
                                            <label class="form-label mb-0">EMI Date</label>
                                            <select class="form-select form-select-sm" name="loan_slab">
                                                <option value="">All</option>
                                                <option value="1" {{ request('loan_slab') == '1' ? 'selected' : '' }}>1st of Month</option>
                                                <option value="15" {{ request('loan_slab') == '15' ? 'selected' : '' }}>15th of Month</option>
                                            </select>
                                        </div>

                                        <!-- Date Range Filter -->
                                        <div class="mb-3">
                                            <label class="form-label mb-0">From Date</label>
                                            <input type="date" class="form-control form-control-sm" name="from_date"
                                                value="{{ request('from_date') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label mb-0">To Date</label>
                                            <input type="date" class="form-control form-control-sm" name="to_date"
                                                value="{{ request('to_date') }}">
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('distributor.loans') }}" class="btn btn-sm btn-phoenix-secondary">Reset</a>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <span class="fas fa-file-excel me-1"></span> Export
                    </button>

                    <!-- Export Modal -->
                    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('distributor.loans.export') }}" method="GET" target="_blank">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="exportModalLabel">Export Loans to Excel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                <!-- Status -->
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                    <option value="">All</option>
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                    <option value="overdue">Overdue</option>
                                    </select>
                                </div>

                                <!-- Financer -->
                                <div class="mb-3">
                                    <label class="form-label">Financer</label>
                                    <select class="form-select" name="financer_id">
                                    <option value="">All Financers</option>
                                    @foreach($financers as $financer)
                                        <option value="{{ $financer->financer_id }}">{{ $financer->name }}</option>
                                    @endforeach
                                    </select>
                                </div>

                                <!-- EMI Slab -->
                                <div class="mb-3">
                                    <label class="form-label">EMI Slab</label>
                                    <select class="form-select" name="loan_slab">
                                    <option value="">All</option>
                                    <option value="1">1st of Month</option>
                                    <option value="15">15th of Month</option>
                                    </select>
                                </div>
                                </div>

                                <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <span class="fas fa-download me-1"></span> Download
                                </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table table-hover table-striped overflow-hidden loan-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Loan No</th>
                                <th>Customer</th>
                                <th>Financer</th>
                                <th>Invoice</th>
                                <th>Amount</th>
                                <th>EMIs</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <span class="fw-semi-bold">#{{ $loan->loan_no }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $loan->name }}</h6>
                                            <small class="text-500">{{ $loan->mobile }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $loan->financer->name }}
                                </td>
                                <td>
                                    @if($loan->invoice)
                                    <a href="{{ route('distributor.invoices.show', $loan->invoice_id) }}" class="fw-semi-bold">
                                        {{ $loan->invoice->invoice_no }}
                                    </a>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    ₹{{ number_format($loan->payable_amount, 2) }}
                                </td>
                                <td>
                                    {{ $loan->emi }}

                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'open' => 'badge-open',
                                            'closed' => 'badge-closed',
                                            'overdue' => 'badge-overdue'
                                        ][$loan->loan_status] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge status-badge {{ $statusClass }}">
                                        {{ ucfirst($loan->loan_status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $progress = ($loan->paid_amount / $loan->payable_amount) * 100;
                                        $progressClass = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress progress-thin">
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-700">{{ round($progress) }}% Paid</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('distributor.loan.show', $loan->loan_id) }}" class="btn btn-sm btn-phoenix-primary me-2">
                                            <span class="fas fa-eye"></span>
                                        </a>
                                        @if ($loan->paid_amount == 0)
                                            <a href="{{ route('distributor.loan.edit', $loan->loan_id) }}" class="btn btn-sm btn-phoenix-primary me-2">
                                                <span class="fas fa-edit"></span>
                                            </a>
                                        @endif
                                        <a href="{{ route('distributor.loan.print', $loan->loan_id) }}" class="btn btn-sm btn-phoenix-secondary" target="_blank">
                                            <span class="fas fa-print"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <img src="{{ asset('assets/img/icons/file.png') }}" alt="No data found" style="height: 120px">
                                        <h5 class="mt-3">No loans found</h5>
                                        <p class="text-muted">Create your first loan to get started</p>
                                        <a href="{{ route('distributor.invoices') }}" class="btn btn-primary btn-sm mt-2">
                                            <span class="fas fa-plus me-2"></span>Create Loan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block ms-3 me-3 fw-semibold text-body">
                            Showing {{ $loans->firstItem() }} to <span class="fw-semi-bold">{{ $loans->lastItem() }}</span> of
                        <span class="fw-semi-bold">{{ $loans->total() }}</span> entries
                        </p>
                    </div>
                    <div class="col-auto d-flex">
                        {{ $loans->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
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
@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter form submission
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            // Apply filter when dropdown items are changed
            const filterSelects = filterForm.querySelectorAll('select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });
</script>
@endsection
@endsection
