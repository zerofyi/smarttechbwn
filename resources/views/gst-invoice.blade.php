<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B2500;
            --primary-dark: #6B1A00;
            --primary-mid: #A83010;
            --accent: #C44A20;
            --accent-light: #E8714A;
            --warm-bg: #FDF6F3;
            --warm-border: #F0D8CE;
            --warm-border-mid: #DEB8A8;
            --text-dark: #2A1208;
            --text-mid: #5C3020;
            --text-light: #8A5A48;
            --text-muted: #B88A7A;
            --white: #FFFFFF;
            --row-alt: #FEF0EB;
            --gold: #C68A40;
            --gold-light: #F5E8D0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Sans 3', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: var(--text-dark);
            background: #F5EDE8;
        }

        .invoice-container {
            max-width: 820px;
            margin: 0 auto;
            background: var(--white);
            box-shadow: 0 4px 40px rgba(139, 37, 0, 0.12);
        }

        /* ── TOP ACCENT BAR ── */
        .top-bar {
            height: 6px;
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--accent) 50%, var(--gold) 100%);
        }

        .inner-pad {
            padding: 28px 36px;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            padding-bottom: 24px;
            margin-bottom: 0;
            border-bottom: 1px solid var(--warm-border-mid);
        }

        .company-info {
            display: table-cell;
            vertical-align: middle;
            width: 55%;
        }

        .invoice-info {
            display: table-cell;
            vertical-align: middle;
            width: 45%;
            text-align: right;
        }

        .company-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-details {
            color: var(--text-light);
            font-size: 11.5px;
            line-height: 1.7;
        }

        .invoice-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .invoice-badge {
            display: inline-block;
            background: var(--primary);
            color: var(--white);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 2px;
            margin-bottom: 10px;
        }

        .invoice-meta {
            color: var(--text-mid);
            font-size: 11.5px;
            line-height: 1.8;
        }

        .invoice-meta strong {
            color: var(--text-dark);
        }

        /* ── BILLING BAND ── */
        .billing-band {
            background: var(--warm-bg);
            border-top: 1px solid var(--warm-border);
            border-bottom: 1px solid var(--warm-border);
            padding: 20px 36px;
            margin-bottom: 0;
        }

        .billing-section {
            display: table;
            width: 100%;
        }

        .bill-to,
        .ship-to {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-right: 20px;
        }

        .ship-to {
            padding-right: 0;
            padding-left: 20px;
            border-left: 1px solid var(--warm-border-mid);
        }

        .section-title {
            font-size: 9px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--warm-border-mid);
        }

        .address {
            line-height: 1.7;
            color: var(--text-mid);
            font-size: 11.5px;
        }

        .address strong {
            color: var(--text-dark);
            font-size: 13px;
        }

        /* ── ITEMS TABLE ── */
        .table-wrap {
            padding: 0 36px;
            margin-top: 24px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .items-table thead tr {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-mid) 100%);
        }

        .items-table th {
            color: var(--white);
            padding: 11px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border: none;
        }

        .items-table td {
            padding: 10px 10px;
            border-bottom: 1px solid var(--warm-border);
            vertical-align: top;
            color: var(--text-mid);
            font-size: 11.5px;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: var(--row-alt);
        }

        .items-table tbody tr:last-child td {
            border-bottom: 2px solid var(--warm-border-mid);
        }

        .product-name {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 12px;
            margin-bottom: 2px;
        }

        .product-meta {
            color: var(--text-muted);
            font-size: 10.5px;
            line-height: 1.5;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .amount {
            font-family: 'Courier New', 'Lucida Console', monospace;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* ── TOTALS + NOTES ── */
        .bottom-section {
            display: table;
            width: 100%;
            padding: 24px 36px;
        }

        .totals-left {
            display: table-cell;
            vertical-align: top;
            width: 58%;
            padding-right: 24px;
        }

        .totals-right {
            display: table-cell;
            vertical-align: top;
            width: 42%;
        }

        /* Notes */
        .notes-title {
            font-size: 9px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .notes-content {
            color: var(--text-light);
            font-size: 11px;
            line-height: 1.7;
            background: var(--warm-bg);
            padding: 14px;
            border-left: 3px solid var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .notes-content strong {
            color: var(--text-dark);
        }

        .terms {
            margin-top: 8px;
            font-size: 9.5px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Totals table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--warm-bg);
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--warm-border-mid);
        }

        .totals-table td {
            padding: 9px 14px;
            border-bottom: 1px solid var(--warm-border);
            font-size: 12px;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .total-label {
            font-weight: 600;
            text-align: right;
            color: var(--text-mid);
            width: 58%;
        }

        .total-amount {
            text-align: right;
            width: 42%;
        }

        .grand-total td {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-mid) 100%);
            color: var(--white) !important;
            font-weight: 700;
            font-size: 13.5px;
            padding: 12px 14px;
        }

        .grand-total .total-label {
            color: rgba(255,255,255,0.85) !important;
        }

        .grand-total .amount {
            color: var(--white);
        }

        /* ── PAYMENT TERMS ── */
        .payment-terms {
            margin: 0 36px 24px;
            background: var(--gold-light);
            border: 1px solid var(--gold);
            border-left: 4px solid var(--gold);
            padding: 14px 18px;
            border-radius: 0 4px 4px 0;
        }

        .payment-terms-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
            font-size: 11px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .payment-terms p {
            color: var(--text-mid);
            font-size: 11px;
        }

        /* ── FOOTER ── */
        .footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            padding: 20px 36px;
            text-align: center;
            color: rgba(255,255,255,0.75);
            font-size: 11px;
            line-height: 1.8;
        }

        .footer strong {
            color: var(--white);
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* ── BOTTOM ACCENT BAR ── */
        .bottom-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, var(--accent) 50%, var(--primary-dark) 100%);
        }

        /* ── PRINT & BUTTONS ── */
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
        }

        .buttons-container {
            display: flex;
            justify-content: center;
            position: fixed;
            bottom: 60px;
            right: 60px;
            gap: 10px;
        }

        .buttons-container .btn {
            padding: 9px 16px;
            font-size: 12px;
            font-family: 'Source Sans 3', sans-serif;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 3px 12px rgba(139, 37, 0, 0.2);
            transition: all 0.2s;
        }

        .buttons-container .btn-outline-secondary {
            background: var(--white);
            color: var(--text-mid);
            border: 1px solid var(--warm-border-mid);
        }

        .buttons-container .btn-outline-primary {
            background: var(--warm-bg);
            color: var(--primary);
            border: 1px solid var(--primary);
            text-decoration: none;
        }

        .buttons-container .btn-outline-success {
            background: var(--warm-bg);
            color: var(--primary-dark);
            border: 1px solid var(--primary-dark);
        }

        .buttons-container .btn:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }
        .signatures{
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<body>
    <div class="invoice-container">
        <div class="top-bar"></div>

        <!-- Header -->
        <div class="inner-pad" style="padding-bottom: 24px;">
            <div class="header">
                <div class="company-info">
                    @if ($invoice->distributor && $invoice->distributor->shop)
                        <div class="company-name"><img src="../assets/img/icons/logo.png" alt="SMART TECH" width="30" /> {{ $invoice->distributor->shop->shop_name }}</div>
                        <div class="company-details">
                            {{ $invoice->distributor->shop->shop_address }}<br>
                            {{-- Contact: {{ $invoice->distributor->mobile }} | {{ $invoice->distributor->email }}<br> --}}
                            Contact: 9733927304 | {{ $invoice->distributor->email }}<br>
                            @if ($invoice->distributor->shop->shop_gstin)
                                GSTIN: {{ $invoice->distributor->shop->shop_gstin }}<br>
                            @endif
                            New<br>
                            All types of premium new/preowned smartphone retailer.
                        </div>
                    @else
                        <div class="company-name">{{ $invoice->distributor->name ?? 'Company Name' }}</div>
                    @endif
                </div>
                <div class="invoice-info">
                    <div class="invoice-badge">
                        @if ($invoice->invoice_type === 'purchase') Purchase Bill @else Tax Invoice @endif
                    </div>
                    <div class="invoice-title">
                        @if ($invoice->invoice_type === 'purchase') PURCHASE BILL @else TAX INVOICE @endif
                    </div>
                    <div class="invoice-meta">
                        <strong>Invoice #:</strong> {{ $invoice->invoice_no }}<br>
                        <strong>Invoice Type:</strong> {{ ucfirst($invoice->invoice_type) }}<br>
                        <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('j F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Band -->
        <div class="billing-band">
            <div class="billing-section">
                <div class="bill-to">
                    <div class="section-title">
                        @if ($invoice->invoice_type === 'purchase') Bill From @else Bill To @endif
                    </div>
                    <div class="address">
                        @if ($invoice->customer)
                            <strong>{{ $invoice->customer->name }}</strong><br>
                            {{ $invoice->customer->address }}<br>
                            Phone: {{ $invoice->customer->mobile }}<br>
                            @if ($invoice->customer->pan_no) PAN: {{ $invoice->customer->pan_no }} @endif
                        @elseif($invoice->party)
                            <strong>{{ $invoice->party->name }}</strong><br>
                            {{ $invoice->party->address }}
                            @if ($invoice->party->state) {{ $invoice->party->state }} @endif<br>
                            Phone: {{ $invoice->party->phone }}
                            @if ($invoice->party->gstin) GSTIN: {{ $invoice->party->gstin }} @endif
                        @else
                            <strong>{{ $invoice->name }}</strong><br>
                            {{ $invoice->address }}<br>
                            Phone: {{ $invoice->mobile }}
                        @endif
                    </div>
                </div>
                <div class="ship-to">
                    <div class="section-title">Other Details</div>
                    <div class="address">
                        @if ($invoice->order)
                            <strong>Order #:</strong> {{ $invoice->order->order_number }}<br>
                            <strong>Order Date:</strong> {{ $invoice->order->created_at->format('j F Y') }}<br>
                        @endif
                        @if ($invoice->loan)
                            <strong>Loan Number:</strong> {{ $invoice->loan->loan_no }}<br>
                        @endif
                        @if ($invoice->agreement)
                            <strong>Agreement:</strong> MB/AG/{{ $invoice->invoice_date->format('Y') }}{{ $invoice->agreement->agreement_id }}<br>
                        @endif
                        <strong>Invoice For:</strong> {{ ucfirst($invoice->invoice_for) }}<br>
                        <strong>Prepared By:</strong> {{ $invoice->distributor->shop->shop_name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 4%;" class="text-center">#</th>
                        <th style="width: 34%;">Product Description</th>
                        <th style="width: 10%;" class="text-center">Warranty</th>
                        <th style="width: 10%;" class="text-right">MRP</th>
                        <th style="width: 6%;" class="text-center">Qty</th>
                        <th style="width: 11%;" class="text-right">Unit Price</th>
                        <th style="width: 13%;" class="text-center">GST%</th>
                        <th style="width: 12%;" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $index => $item)
                        <tr>
                            <td class="text-center" style="color: var(--text-muted); font-weight: 600;">{{ $index + 1 }}</td>
                            <td>
                                <div class="product-name">{{ $item->inventory->product_name }}</div>
                                <div class="product-meta">
                                    @if ($item->inventory->imei_one) IMEI 1: {{ $item->inventory->imei_one }}<br> @endif
                                    @if ($item->inventory->imei_two) IMEI 2: {{ $item->inventory->imei_two }}<br> @endif
                                    @if ($item->inventory->serial_number) Serial: {{ $item->inventory->serial_number }}<br> @endif
                                    @if ($item->inventory->color) Condition: {{ ucfirst($item->inventory->product_type) }}, Color: {{ ucfirst($item->inventory->color) }}, @endif
                                    @if(!is_null($item->inventory->battery_health) && $item->inventory->battery_health !== '')
                                        BH: {{ $item->inventory->battery_health }}%<br>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($item->inventory->activation_date)
                                    @php
                                        $actDate = \Carbon\Carbon::parse($item->inventory->activation_date);
                                        $expDate = $actDate->copy()->addYear(); // 1 Year standard warranty
                                        $daysLeft = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($expDate, false);
                                    @endphp
                                    <div class="d-inline-block text-start">
                                        <span class="text-dark">
                                            <small class="text-muted">Act. Date:</small>
                                            <strong class="text-primary">{{ $actDate->format('d/m/Y') }}</strong>
                                        </span>
                                        <div class="text-center" style="font-size: 0.72rem; margin-top: 1px;">
                                            @if ($daysLeft > 0)
                                                <span class="text-success fw-semibold">Approx. {{ $daysLeft }} days left</span>
                                            @elseif ($daysLeft === 0)
                                                <span class="text-warning fw-semibold">(Expires Today)</span>
                                            @else
                                                <span class="text-danger fw-semibold">(Expired)</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($item->inventory->warranty)
                                    {{ ucfirst($item->inventory->warranty) }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-right amount">
                                @if ($item->inventory->product) ₹{{ number_format($item->inventory->product->mrp, 2) }} @else Unknown @endif
                            </td>
                            <td class="text-center">{{ $item->qty }} {{ ucfirst($item->inventory->unit) }}</td>
                            <td class="text-right amount">₹{{ number_format($item->rate, 2) }}</td>
                            <td class="text-center" style="font-size:11px;">{{ $item->tax }} ({{ number_format($item->tax_percent, 0) }}%)</td>
                            <td class="text-right amount">₹{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals + Notes -->
        <div class="bottom-section">
            <div class="totals-left">
                <div class="notes-title">Notes &amp; Additional Information</div>
                <div class="notes-content">
                    <strong>Amount in Words:</strong> {{ convertToIndianCurrency($invoice->total_amount) }}<br>
                    @if ($invoice->notes) <strong>Notes:</strong> {{ $invoice->notes }}<br> @endif
                    @if ($invoice->invoice_type === 'sale')
                        <div class="terms">
                            1. We are not responsible for any Breakage/Damage/Shortage/Leakage in transit.<br>
                            2. Our responsibility ceases when the goods are delivered to the carrier.<br>
                            3. Goods once sold will not be accepted back.<br>
                            4. Any kinds of handset problem after purchase please contact Customer Care.<br>
                            4. Subject to <strong>Burdwan jurisdiction</strong><br>
                        </div>
                    @endif
                </div>
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal:</td>
                        <td class="total-amount amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if ($invoice->total_discount > 0)
                        <tr>
                            <td class="total-label">Discount:</td>
                            <td class="total-amount amount" style="color: var(--accent);">-₹{{ number_format($invoice->total_discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="total-label">CGST (9%):</td>
                        <td class="total-amount amount">₹{{ number_format($invoice->total_tax/2, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">SGST (9%):</td>
                        <td class="total-amount amount">₹{{ number_format($invoice->total_tax/2, 2) }}</td>
                    </tr>
                    @if ($invoice->shipping_amount > 0)
                        <tr>
                            <td class="total-label">Shipping:</td>
                            <td class="total-amount amount">₹{{ number_format($invoice->shipping_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td class="total-label">TOTAL AMOUNT:</td>
                        <td class="total-amount amount">₹{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                    @if ($invoice->paid > 0)
                        <tr>
                            <td class="total-label">Amount Paid:</td>
                            <td class="total-amount amount">₹{{ number_format($invoice->paid, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">Balance Due:</td>
                            <td class="total-amount amount" style="color: var(--accent); font-weight: 700;">₹{{ number_format($invoice->due_amount, 2) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Payment Terms -->
        {{-- @if ($invoice->invoice_type === 'sale')
            <div class="payment-terms">
                <div class="payment-terms-title">⚠ Payment Terms &amp; Instructions</div>
                <p>
                    Payment is due by {{ $invoice->due_date->format('j F Y') }}.
                    @if ($invoice->payment_type === 'credit')
                        This is a credit invoice with terms of {{ $invoice->due_date->diffInDays($invoice->invoice_date) }} days.
                    @else
                        Payment is expected immediately upon receipt of this invoice.
                    @endif
                    Late payments may be subject to interest charges as per our terms.
                </p>
            </div>
        @endif --}}

        <!-- Footer -->
        <div class="footer">
            <strong>Thank you for your business!</strong>
            <p>For queries, contact {{ $invoice->distributor->shop->shop_name }} at {{ $invoice->distributor->mobile }}</p>
            @if ($invoice->distributor->shop)
                <p style="margin-top:4px;">
                    {{ $invoice->distributor->shop->shop_name }} &nbsp;|&nbsp; {{ $invoice->distributor->shop->shop_address }}
                    @if ($invoice->distributor->shop->shop_gstin)
                        &nbsp;|&nbsp; GSTIN: {{ $invoice->distributor->shop->shop_gstin }}
                    @endif
                </p>
            @endif
            <div class="signatures">
                <div class="receiver">Receiver's Signature</div>
                <div class="shop">For {{ $invoice->distributor->shop->shop_name }}</div>
            </div>
        </div>
        <div class="bottom-bar"></div>
    </div>

    <!-- Action Buttons -->
    <div class="no-print buttons-container">
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="shareInvoice()" class="btn btn-sm btn-outline-success">
            <i class="fas fa-share-alt"></i> Share
        </button>
        <a href="https://wa.me/{{ $invoice->mobile }}?text={{ urlencode(
            'Greetings from ' . $invoice->distributor->shop->shop_name . "\n\n" .
            'We are pleased to have you as a valuable customer. Please find the details of your transaction.' . "\n\n" .
            'Invoice : ' . $invoice->invoice_no . "\n" .
            'Invoice Amount: ₹' . $invoice->total_amount . "\n\n" .
            'Thanks for doing business with us.' . "\n" .
            'Regards,' . "\n" .
            $invoice->distributor->shop->shop_name . "\n\n" .
            'Invoice Link:' . "\n" .
            'https://smarttechbwn.in/gst-invoice/' . $invoice->invoice_url
        ) }}" class="btn btn-sm btn-outline-success" target="_blank">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>

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
</body>
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
            '0' => '', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four',
            '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
            '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen',
            '14' => 'Fourteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty',
            '40' => 'Forty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy',
            '80' => 'Eighty', '90' => 'Ninety',
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        while ($i < $digits_length) {
            $divider = $i == 2 ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $str[] = $number < 21
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
