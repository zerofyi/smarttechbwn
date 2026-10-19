@extends('layouts.distributor')

@section('title', 'Employee Attributed Sales Log')

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
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active">{{ $employee->name }} Sales Profile</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2>Sales Invoices Log: {{ $employee->name }}</h2>
            <p class="text-body-secondary mb-0">
                @if(request()->has('view_all'))
                    Showing <strong>All History</strong> sales performance records.
                @elseif(request()->filled('start_date'))
                    Showing logs from <strong>{{ request('start_date') }}</strong> to <strong>{{ request('end_date') }}</strong>
                @else
                    Showing <strong>This Month's</strong> performance ledger by default.
                @endif
            </p>
        </div>

        <div>
            <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="col-auto">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-phoenix-primary">Filter</button>
                </div>
                @if(!request()->has('view_all'))
                <div class="col-auto">
                    <a href="{{ url()->current() }}?view_all=1" class="btn btn-sm btn-phoenix-secondary">View All</a>
                </div>
                @else
                <div class="col-auto">
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-phoenix-secondary">Reset to Current Month</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <div class="mx-n4 px-4 bg-body-emphasis border-top border-bottom border-translucent">
        <div class="table-responsive scrollbar">
            <table class="table fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">INVOICE NUMBER</th>
                        <th>DATE</th>
                        <th>CUSTOMER/ENTITY INFO</th>
                        <th class="text-end">GROSS VALUE</th>
                        <th class="text-end pe-4">ESTIMATED INCENTIVE ({{ $employee->incentive_percentage }}%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="ps-4"><span class="fw-bold text-primary">
                            <a href="{{ route('distributor.invoices.show', $sale->invoice_id) }}"
                                class="fw-semibold">
                                {{ $sale->invoice_no }}
                            </a>
                        </span></td>
                        <td>{{ \Carbon\Carbon::parse($sale->invoice_date)->format('d M Y') }}</td>
                        <td>{{ $sale->name ?? 'Walk-In Customer' }} <small class="text-muted">({{ $sale->mobile ?? 'N/A' }})</small></td>
                        <td class="text-end fw-semibold text-success">₹{{ number_format($sale->total_amount, 2) }}</td>
                        <td class="text-end pe-4 text-primary fw-bold">
                            ₹{{ number_format(($sale->total_amount * $employee->incentive_percentage) / 100, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No historical checkout records found for the selected timeframe.</td>
                    </tr>
                    @endforelse
                </tbody>

                @if($sales->count() > 0)
                <tfoot class="bg-body-secondary border-top border-translucent">
                    <tr class="fw-bold">
                        <td colspan="3" class="ps-4 text-start align-middle py-3 fs-8">TOTAL SUMMARY FOR TIMEFRAME:</td>
                        <td class="text-end text-success align-middle py-3 fs-8">₹{{ number_format($totalSalesAmount, 2) }}</td>
                        <td class="text-end pe-4 text-primary align-middle py-3 fs-8">₹{{ number_format($totalIncentiveAmount, 2) }}</td>
                    </tr>
                </footer>
                @endif
            </table>
        </div>
        <div class="py-2">
            {{ $sales->links() }}
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
