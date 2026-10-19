@extends('layouts.distributor')

{{-- PAGE TITLE HERE --}}
@section('title', 'Distributor : Dashboard')

{{-- VENDOR CSS START --}}
@section('vendor-style')
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
<link href="../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
<link href="../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
<link href="../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
<link href="../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
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
<link href="../../vendors/leaflet/leaflet.css" rel="stylesheet">
<link href="../../vendors/leaflet.markercluster/MarkerCluster.css" rel="stylesheet">
<link href="../../vendors/leaflet.markercluster/MarkerCluster.Default.css" rel="stylesheet">

<style>
    .stats-card {
        border-radius: 12px;
        transition: all 0.3s ease;
        height: 100%;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .trend-indicator {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chart-container {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
    }
    .summary-card {
        border-left: 4px solid;
        border-radius: 8px;
    }
    .profit-positive {
        color: #28a745;
    }
    .profit-negative {
        color: #dc3545;
    }
</style>
@endsection
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')

<div class="content">
<div id="protectedPage" style="display:none;">
    <div class="pb-5">
        <div class="mb-8">
            <h2 class="mb-2">Business Dashboard</h2>
            <h5 class="text-body-tertiary fw-semibold">Key performance indicators and financial overview</h5>
        </div>

        <!-- Summary Row -->
        <div class="row g-4 mb-6">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card bg-primary bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-primary">Total Sales</h5>
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold">₹{{ number_format($totalSales, 2) }}</h2>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">{{ $totalSaleInvoices }} Invoices</p>
                            <span class="trend-indicator text-success">
                                <i class="fas fa-arrow-up"></i> {{ number_format($salesGrowth, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card bg-success bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-success">Total Profit</h5>
                            <div class="stats-icon bg-success">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold {{ $totalProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            ₹{{ number_format(abs($totalProfit), 2) }}
                        </h2>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">{{ number_format($profitMargin, 1) }}% Margin</p>
                            <span class="trend-indicator {{ $profitGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas {{ $profitGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                {{ number_format(abs($profitGrowth), 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card bg-info bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-info">Today's Sales</h5>
                            <div class="stats-icon bg-info">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold">₹{{ number_format($todaySales, 2) }}</h2>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">{{ $totalTodaySaleInvoices }} Invoices</p>
                            <span class="trend-indicator {{ $todayProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas {{ $todayProfit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                ₹{{ number_format(abs($todayProfit), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card bg-warning bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-warning">Due Amount</h5>
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold">₹{{ number_format($totalDue, 2) }}</h2>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">Outstanding</p>
                            <span class="trend-indicator text-danger">
                                <i class="fas fa-exclamation-circle"></i> Needs attention
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Stats Row -->
        <div class="row g-4">
            <!-- Monthly Performance Chart -->
            <div class="col-12 col-lg-8">
                <div class="card chart-container">
                    <div class="card-header bg-transparent">
                        <h4 class="mb-0">Monthly Performance</h4>
                        <p class="text-body-secondary mb-0">Sales and profit trends</p>
                    </div>
                    <div class="card-body">
                        <div class="echart-monthly-performance" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card summary-card border-left-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-primary">Last Month Sales</h5>
                                        <h3 class="fw-bold">₹{{ number_format($lastMonthSales, 2) }}</h3>
                                        <p class="text-body-secondary mb-0">{{ $totalLastMonthSaleInvoices }} invoices</p>
                                    </div>
                                    <i class="fas fa-calendar-alt fa-2x text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card summary-card border-left-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-success">Last Month Profit</h5>
                                        <h3 class="fw-bold {{ $lastMonthProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            ₹{{ number_format(abs($lastMonthProfit), 2) }}
                                        </h3>
                                        <p class="text-body-secondary mb-0">{{ number_format($lastMonthProfitMargin, 1) }}% margin</p>
                                    </div>
                                    <i class="fas fa-chart-pie fa-2x text-success opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card summary-card border-left-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-info">Inventory Value</h5>
                                        <h3 class="fw-bold">₹{{ number_format($inventoryValue, 2) }}</h3>
                                        <p class="text-body-secondary mb-0">{{ $inventoryItems }} items in stock</p>
                                    </div>
                                    <i class="fas fa-boxes fa-2x text-info opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Recent Sale Invoices</h4>
                        <p class="text-body-secondary mb-0">Latest 10 transactions</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Profit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSaleInvoices as $invoice)
                                    <tr>
                                        <td><a href="{{ route('distributor.invoices', ['invoice_no'=>$invoice->invoice_no]) }}">{{ $invoice->invoice_no }}</a></td>
                                        <td>{{ $invoice->name }}</td>
                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                        <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="{{ $invoice->profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            ₹{{ number_format(abs($invoice->profit), 2) }}
                                        </td>
                                        <td>
                                            @if($invoice->due_amount == 0)
                                            <span class="badge bg-success">Paid</span>
                                            @else
                                            <span class="badge bg-warning">Due: ₹{{ number_format($invoice->due_amount, 2) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No sale invoices found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <x-admin.footer />
</div>
@endsection
{{-- CONTENT END --}}

{{-- VENDOR JS START --}}
@section('vendor-script')
<script src="../../vendors/echarts/echarts.min.js"></script>
<script>
function loadDashboardCharts() {

    var monthlyChart = echarts.init(document.querySelector('.echart-monthly-performance'));

    var monthlyOption = {
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            formatter: function(params) {
                let result = params[0].axisValue + '<br/>';
                for (let i = 0; i < params.length; i++) {
                    const value = Math.abs(params[i].value);
                    const sign = params[i].value >= 0 ? '' : '-';
                    result += params[i].seriesName + ': ' + sign + '₹' + value.toLocaleString('en-IN', {maximumFractionDigits: 2}) + '<br/>';
                }
                return result;
            }
        },
        legend: { data: ['Sales', 'Profit'] },
        grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
        xAxis: { type: 'category', data: @json($months) },
        yAxis: { type: 'value', axisLabel: { formatter: '₹{value}' } },
        series: [
            {
                name: 'Sales',
                type: 'bar',
                data: @json($monthlySales),
                itemStyle: { color: '#3B7DDD' }
            },
            {
                name: 'Profit',
                type: 'line',
                data: @json($monthlyProfit),
                itemStyle: {
                    color: function(params) {
                        return params.value >= 0 ? '#4CAF50' : '#dc3545';
                    }
                },
                lineStyle: { width: 3 }
            }
        ]
    };

    monthlyChart.setOption(monthlyOption);
    window.addEventListener('resize', () => monthlyChart.resize());
}
</script>

<script>
(function () {

    const PASSWORD = "3205";

    function lockPage() {
        document.body.innerHTML =
            "<h2 style='text-align:center;margin-top:120px;font-family:sans-serif'>🔒 Access Denied</h2>";
    }

    function showPage() {
        document.getElementById("protectedPage").style.display = "block";

        // wait for layout to render, then load charts
        setTimeout(function(){
            if (typeof loadDashboardCharts === "function") {
                loadDashboardCharts();
            }
        }, 200);
    }

    window.onload = function () {
        let pass = prompt("Enter Distributor Dashboard Password:");

        if (pass === PASSWORD) {
            showPage();
        } else {
            alert("Wrong Password");
            lockPage();
        }
    };

})();
</script>

@endsection
{{-- VENDOR JS END --}}

{{-- PAGE JS START --}}
@section('page-script')
<script src="../../vendors/popper/popper.min.js"></script>
<script src="../../vendors/bootstrap/bootstrap.min.js"></script>
<script src="../../vendors/anchorjs/anchor.min.js"></script>
<script src="../../vendors/is/is.min.js"></script>
<script src="../../vendors/fontawesome/all.min.js"></script>
<script src="../../vendors/lodash/lodash.min.js"></script>
<script src="../../vendors/list.js/list.min.js"></script>
<script src="../../vendors/feather-icons/feather.min.js"></script>
<script src="../../vendors/dayjs/dayjs.min.js"></script>
<script src="../../vendors/leaflet/leaflet.js"></script>
<script src="../../vendors/leaflet.markercluster/leaflet.markercluster.js"></script>
<script src="../../vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js"></script>
<script src="../../assets/js/phoenix.js"></script>
<script src="../../vendors/echarts/echarts.min.js"></script>
<script src="../../assets/js/ecommerce-dashboard.js"></script>
@endsection
{{-- PAGE JS END --}}
