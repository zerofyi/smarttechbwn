<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->invoice_no }}</title>
    <style>
        /* Simple, PDF-compatible styling */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 15px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .logo {
            max-width: 120px;
            max-height: 60px;
        }
        .header-text {
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .invoice-info {
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .info-table .label {
            font-weight: bold;
            width: 25%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        .items-table th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-table {
            width: 250px;
            float: right;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        .total-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .total-table .label {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 10px;
            color: #777;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        @page {
            margin: 10px;
        }
        @media print {
            /* body {
                padding: 0;
                margin: 0;
                font-size: 12px;
                background: white;
                color: black;
            } */

            .no-print {
                display: none !important;
            }
            .container{
                margin-top: 20px;
            }

            /* .container {
                width: 100%;
                padding: 0;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .table {
                page-break-inside: avoid;
            }

            .avoid-break {
                page-break-inside: avoid;
            } */
        }
        .buttons-container{
            display: flex;
            justify-content: center;
            position: fixed;
            bottom: 80px;
            right: 80px;
            gap: 10px;
        }
        .buttons-container .btn{
            padding: 8px 15px;
            font-size: 12px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: -1px 5px 10px -6px rgba(0,37,88,0.58);
            -webkit-box-shadow: -1px 5px 10px -6px rgba(0,37,88,0.58);
            -moz-box-shadow: -1px 5px 10px -6px rgba(0,37,88,0.58);

        }
        .buttons-container .btn-outline-secondary {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #ced4da;
        }
        .buttons-container .btn-outline-primary {
            background-color: #e9ecef;
            color: #007bff;
            border: 1px solid #007bff;
            text-decoration: none;
        }
        .buttons-container .btn-outline-success {
            background-color: #e9f7ef;
            color: #28a745;
            border: 1px solid #28a745;
        }
        .buttons-container .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <div style="float: left;">
                @if ($invoice->distributor && $invoice->distributor->shop)
                    <h2 style="margin: 0 0 5px 0; font-size: 16px;">{{ $invoice->distributor->shop->shop_name }}</h2>
                    <div style="font-size: 11px;">
                        {{ $invoice->distributor->shop->shop_address }}<br>
                        Phone: {{ $invoice->distributor->mobile }}<br>
                        Email: {{ $invoice->distributor->email }}<br>
                        @if ($invoice->distributor->shop->shop_gstin)
                            GSTIN: {{ $invoice->distributor->shop->shop_gstin }}
                        @endif
                    </div>
                @else
                    <h2 style="margin: 0 0 5px 0; font-size: 16px;">{{ $invoice->distributor->name ?? 'Company Name' }}</h2>
                @endif
            </div>
            <div style="float: right;" class="header-text">
                <div class="invoice-title">
                    @if ($invoice->invoice_type === 'purchase')
                        PURCHASE BILL
                    @else
                        TAX INVOICE
                    @endif
                </div>
                <div>#{{ $invoice->invoice_no }}</div>
                <div>Date: {{ $invoice->invoice_date->format('j F Y') }}</div>
                @if ($invoice->due_date)
                    <div>Due Date: {{ $invoice->due_date->format('j F Y') }}</div>
                @endif
            </div>
        </div>

        <div class="invoice-info">
            <table class="info-table">
                <tr>
                    <td class="label">
                        @if ($invoice->invoice_type === 'purchase')
                            BILL FROM
                        @else
                            BILL TO
                        @endif
                    </td>
                    <td style="width: 30%;">
                        @if ($invoice->customer)
                            <strong>{{ $invoice->customer->name }}</strong><br>
                            {{ $invoice->customer->address }}<br>
                            Phone: {{ $invoice->customer->mobile }}<br>
                            @if ($invoice->customer->aadhaar_no)
                                Aadhaar: {{ $invoice->customer->aadhaar_no }}<br>
                            @endif
                            @if ($invoice->customer->pan_no)
                                PAN: {{ $invoice->customer->pan_no }}
                            @endif
                        @elseif($invoice->party)
                            <strong>{{ $invoice->party->name }}</strong><br>
                            {{ $invoice->party->address }}
                            @if ($invoice->party->state)
                                ,{{ $invoice->party->state }}
                            @endif
                            <br>
                            Phone: {{ $invoice->party->phone }}
                            @if ($invoice->party->gstin)
                                PAN: {{ $invoice->party->gstin }}
                            @endif
                        @else
                            <strong>{{ $invoice->name }}</strong><br>
                            {{ $invoice->address }}<br>
                            Phone: {{ $invoice->mobile }}
                        @endif
                    </td>
                    <td class="label">Payment Method:</td>
                    <td>{{ ucfirst($invoice->payment_type) }}</td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 35%;">Product Description</th>
                    <th style="width: 10%;" class="text-center">Warranty</th>
                    <th style="width: 10%;" class="text-right">MRP</th>
                    <th style="width: 5%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-right">Unit Price</th>
                    <th style="width: 15%;" class="text-center">GST%</th>
                    <th style="width: 10%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->inventory->product_name }}</strong><br>
                            @if ($item->inventory->imei_one)
                                <small>IMEI 1: {{ $item->inventory->imei_one }}</small><br>
                            @endif
                            @if ($item->inventory->imei_two)
                                <small>IMEI 2: {{ $item->inventory->imei_two }}</small><br>
                            @endif
                            @if ($item->inventory->serial_number)
                                <small>Serial: {{ $item->inventory->serial_number }}</small><br>
                            @endif
                            @if ($item->inventory->color)
                                <small>Condition: {{ ucfirst($item->inventory->product_type) }}, Color:
                                    {{ ucfirst($item->inventory->color) }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($item->inventory->warranty)
                                {{ ucfirst($item->inventory->warranty) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-right amount">₹{{ number_format($item->inventory->product->mrp * 1.1, 2) }}</td>
                        <td class="text-center">{{ $item->qty }} {{ ucfirst($item->inventory->unit) }}</td>
                        <td class="text-right amount">₹{{ number_format($item->rate, 2) }}</td>
                        <td class="text-center">{{ $item->tax }}
                            ({{ number_format($item->tax_percent * 1.1, 0) }}%)</td>
                        <td class="text-right amount">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <table class="total-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="text-right amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if ($invoice->total_discount > 0)
                    <tr>
                        <td class="label">Discount</td>
                        <td class="text-right amount">-₹{{ number_format($invoice->total_discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Tax (GST)</td>
                    <td class="text-right amount">₹{{ number_format($invoice->total_tax, 2) }}</td>
                </tr>
                @if ($invoice->shipping_amount > 0)
                    <tr>
                        <td class="label">Shipping</td>
                        <td class="text-right amount">₹{{ number_format($invoice->shipping_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Total</td>
                    <td class="text-right amount">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                @if ($invoice->paid > 0)
                    <tr>
                        <td class="label">Amount Paid</td>
                        <td class="text-right amount">₹{{ number_format($invoice->paid, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="background-color: #e9f7fe; color: #0c5460;">Balance Due</td>
                        <td class="text-right" style="background-color: #e9f7fe; color: #0c5460;">₹{{ number_format($invoice->due_amount, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="notes">
            <strong>Amount in Words:</strong> {{ convertToIndianCurrency($invoice->total_amount) }}<br>
            @if ($invoice->notes)
                <strong>Notes:</strong> {{ $invoice->notes }}<br>
            @endif
            @if ($invoice->invoice_type === 'sale')
                <div style="margin-top: 5px;">
                    ১| মোবাইল কেনার সাত দিন পর মোবাইল খারাপ হয়ে গেলে আমরা কোন ভাবে দায়ীবদ্ধ থাকবো না|<br>
                    ২| ওয়ারেন্টি থাকা ফোন সার্ভিস সেন্টার থেকে ওয়ারেন্টি নিতে হবে, আমরা সহযোগিতা করতে পারি মাত্র|<br>
                    ৩| বিক্রিত মোবাইল ফোন ফেরত দিতে চাইলে দোকানদার দামদর করে ফেরত নেবে, সঙ্গে বিল বক্স আবশ্যিক|<br>
                    ৪| নতুন ও পুরাতন মোবাইল কেনার সময় বিল বুঝিয়া নিন নচেৎ কোনরকম সমস্যা হলে দোকানদার দায়বদ্ধ থাকবে না|
                </div>
            @endif
            @if ($invoice->invoice_type === 'sale' && $invoice->due_date)
                <div style="margin-top: 5px;">
                    <strong>Payment Terms:</strong> Payment is due by {{ $invoice->due_date->format('j F Y') }}.
                    @if ($invoice->payment_type === 'credit')
                        This is a credit invoice with terms of {{ $invoice->due_date->diffInDays($invoice->invoice_date) }} days.
                    @else
                        Payment is expected immediately upon receipt of this invoice.
                    @endif
                </div>
            @endif
        </div>

        <div class="footer">
            <div class="text-center">Thank you for your business!</div>
            <div class="text-center" style="margin-top: 5px; font-size: 10px;">
                @if ($invoice->distributor->shop)
                    {{ $invoice->distributor->shop->shop_name }}
                    {{ $invoice->distributor->shop->shop_address }} |
                    {{-- {{ $invoice->distributor->mobile }} | {{ $invoice->distributor->email }} --}}
                    @if ($invoice->distributor->shop->shop_gstin)
                        | GSTIN: {{ $invoice->distributor->shop->shop_gstin }}
                    @endif
                @else
                    {{-- <strong>{{ $invoice->distributor->name }}</strong><br>
                    {{ $invoice->distributor->mobile }} | {{ $invoice->distributor->email }} --}}
                @endif
            </div>
        </div>
    </div>

    <div class="no-print buttons-container">
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-print"></i> Print
        </button>
        {{-- <a href="{{ route('invoice.download', $invoice->invoice_url) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download"></i> Download PDF
        </a> --}}
        <button onclick="shareInvoice()" class="btn btn-sm btn-outline-success">
            <i class="fas fa-share-alt"></i> Share
        </button>
    </div>
</body>
<script>
    function shareInvoice() {
        if (navigator.share) {
            navigator.share({
                title: 'Invoice #{{ $invoice->invoice_no }}',
                text: 'Please find your invoice from {{ $invoice->distributor->name }}',
                url: window.location.href
            }).catch(err => {
                console.log('Error sharing:', err);
                copyToClipboard();
            });
        } else {
            copyToClipboard();
        }
    }

    function copyToClipboard() {
        navigator.clipboard.writeText(window.location.href)
            .then(() => alert('Invoice link copied to clipboard!'))
            .catch(err => {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = window.location.href;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Invoice link copied to clipboard!');
            });
    }
</script>
</html>
@php
    function convertToIndianCurrency($number)
    {
        $no = floor($number);
        $decimal = round(($number - $no) * 100);
        $digits_length = strlen($no);
        $i = 0;
        $str = [];
        $words = [
            '0' => '',
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
            '4' => 'Four',
            '5' => 'Five',
            '6' => 'Six',
            '7' => 'Seven',
            '8' => 'Eight',
            '9' => 'Nine',
            '10' => 'Ten',
            '11' => 'Eleven',
            '12' => 'Twelve',
            '13' => 'Thirteen',
            '14' => 'Fourteen',
            '15' => 'Fifteen',
            '16' => 'Sixteen',
            '17' => 'Seventeen',
            '18' => 'Eighteen',
            '19' => 'Nineteen',
            '20' => 'Twenty',
            '30' => 'Thirty',
            '40' => 'Forty',
            '50' => 'Fifty',
            '60' => 'Sixty',
            '70' => 'Seventy',
            '80' => 'Eighty',
            '90' => 'Ninety',
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        while ($i < $digits_length) {
            $divider = $i == 2 ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $str[] =
                    $number < 21
                        ? $words[$number] . ' ' . $digits[count($str)]
                        : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[count($str)];
            } else {
                $str[] = null;
            }
        }
        $result = implode(' ', array_reverse($str));
        $paise = $decimal > 0 ? " and $decimal/100 Paise" : '';
        return $result . ' Rupees' . $paise . ' Only';
    }
@endphp
