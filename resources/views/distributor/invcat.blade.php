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
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-decoration: none;
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
    function format_inr($amount)
    {
        $amount = number_format((float) $amount, 2, '.', '');
        $parts = explode('.', $amount);

        $integer = $parts[0];
        $decimal = $parts[1];

        $last3 = substr($integer, -3);
        $rest = substr($integer, 0, -3);

        if ($rest != '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',';
        }

        return $rest . $last3 . '.' . $decimal;
    }
    ?>
    <div class="content">
        <div class="pb-5">
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Inventory Category Summary</h4>
                            <p class="text-body-secondary mb-0">Stock & Value by Category</p>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Category</th>
                                            <th>Total Stock</th>
                                            <th>Total Value (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $grandStock = 0;
                                            $grandValue = 0;
                                        @endphp

                                        @foreach ($categorySummary as $category => $data)
                                            <tr>
                                                <td class="text-center">{{ $i++ }}</td>
                                                <td><strong>{{ $category }}</strong></td>
                                                <td>{{ number_format($data['stock']) }}</td>
                                                <td>₹{{ format_inr($data['value']) }}</td>
                                            </tr>

                                            @php
                                                $grandStock += $data['stock'];
                                                $grandValue += $data['value'];
                                            @endphp
                                        @endforeach
                                    </tbody>

                                    <tfoot class="table-dark">
                                        <tr>
                                            <th colspan="2">Grand Total</th>
                                            <th>{{ number_format($grandStock) }}</th>
                                            <th>₹{{ format_inr($grandValue) }}</th>
                                        </tr>
                                    </tfoot>
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
