@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'Admin : Dashboard')

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

<?php
    function format_inr($amount) {
        $amount = number_format((float)$amount, 2, '.', '');
        $parts = explode('.', $amount);

        $integer = $parts[0];
        $decimal = $parts[1];

        $last3 = substr($integer, -3);
        $rest = substr($integer, 0, -3);

        if ($rest != '') {
            $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest) . ",";
        }

        return $rest . $last3 . '.' . $decimal;
    }
?>


<div class="content">
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
                        <h3 class="fw-bold">₹{{ format_inr($totalSales) }}</h3>
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
                        <h3 class="fw-bold {{ $totalProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            ₹{{ format_inr(abs($totalProfit)) }}
                        </h3>
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
                        <h2 class="fw-bold">₹{{ format_inr($todaySales) }}</h2>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">{{ $totalTodaySaleInvoices }} Invoices</p>
                            <span class="trend-indicator {{ $todayProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas {{ $todayProfit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                ₹{{ format_inr(abs($todayProfit)) }}
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
                        <h2 class="fw-bold">₹{{ format_inr($totalDue) }}</h2>
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
                                        <h3 class="fw-bold">₹{{ format_inr($lastMonthSales) }}</h3>
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
                                            ₹{{ format_inr(abs($lastMonthProfit)) }}
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
                                        <h3 class="fw-bold">₹{{ format_inr($inventoryValue) }}</h3>
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
    <x-admin.footer />
</div>
@endsection
{{-- CONTENT END --}}

{{-- VENDOR JS START --}}
@section('vendor-script')
<script src="../../vendors/echarts/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Performance Chart
        var monthlyChart = echarts.init(document.querySelector('.echart-monthly-performance'));

        var monthlyOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
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
            legend: {
                data: ['Sales', 'Profit'],
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: @json($months)
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    formatter: '₹{value}'
                }
            },
            series: [
                {
                    name: 'Sales',
                    type: 'bar',
                    data: @json($monthlySales),
                    itemStyle: {
                        color: '#3B7DDD'
                    }
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
                    lineStyle: {
                        width: 3,
                        color: function(params) {
                            return params.value >= 0 ? '#4CAF50' : '#dc3545';
                        }
                    }
                }
            ]
        };

        monthlyChart.setOption(monthlyOption);

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            monthlyChart.resize();
        });
    });
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
