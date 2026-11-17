<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #222222; margin: 0; padding: 0; }
        .header { background-color: #6B1A00; color: #ffffff; padding: 18px 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #ffd9cc; }
        .meta { width: 100%; margin: 16px 0; }
        .meta td { vertical-align: top; padding: 4px 20px; font-size: 12px; }
        .meta h3 { margin: 0 0 6px; font-size: 12px; text-transform: uppercase; color: #6B1A00; }
        .meta p { margin: 0; line-height: 1.5; }
        table.items { width: 100%; border-collapse: collapse; margin: 8px 0 0; }
        table.items th { background-color: #f0e6e2; color: #6B1A00; text-align: left; padding: 8px 10px; border: 1px solid #dddddd; font-size: 11px; text-transform: uppercase; }
        table.items td { padding: 8px 10px; border: 1px solid #dddddd; }
        .right { text-align: right; }
        .center { text-align: center; }
        table.totals { width: 45%; margin: 12px 0 0 auto; border-collapse: collapse; }
        table.totals td { padding: 6px 10px; border: 1px solid #dddddd; }
        table.totals tr.grand td { font-weight: bold; background-color: #f0e6e2; }
        .footer { margin-top: 24px; padding: 12px 20px; border-top: 1px solid #dddddd; font-size: 11px; color: #777777; }
        .content { padding: 0 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SmartTech</h1>
        <p>Tax Invoice</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width:50%;">
                <h3>Billed To</h3>
                <p>
                    <strong>{{ $user->name ?? '' }}</strong><br>
                    {{ $user->email ?? '' }}<br>
                    @if($user->mobile ?? null)
                    {{ $user->mobile }}<br>
                    @endif
                    @if($address)
                        {{ $address->address_line_1 }}<br>
                        @if($address->address_line_2)
                        {{ $address->address_line_2 }}<br>
                        @endif
                        @if($address->landmark)
                        {{ $address->landmark }}<br>
                        @endif
                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                        {{ $address->country ?? '' }}
                    @endif
                </p>
            </td>
            <td style="width:50%;">
                <h3>Invoice Details</h3>
                <p>
                    Invoice No: <strong>{{ $invoice_no }}</strong><br>
                    Order No: <strong>{{ $order->order_number }}</strong><br>
                    Invoice Date: {{ $invoice_date ? \Carbon\Carbon::parse($invoice_date)->format('d M Y') : '' }}<br>
                    Order Date: {{ $order->created_at ? $order->created_at->format('d M Y') : '' }}<br>
                    Payment Method: {{ strtoupper($order->payment_method ?? '') }}<br>
                    Payment Status: {{ ucfirst($order->payment_status ?? '') }}<br>
                    Order Status: {{ ucfirst($order->status ?? '') }}
                </p>
            </td>
        </tr>
        @if($address)
        <tr>
            <td colspan="2">
                <h3>Delivery Address</h3>
                <p>
                    {{ $address->contact_person ?? ($user->name ?? '') }} &middot; {{ $address->contact_number ?? '' }}<br>
                    {{ $address->address_line_1 }}
                    @if($address->address_line_2)
                    , {{ $address->address_line_2 }}
                    @endif
                    <br>
                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country ?? '' }}
                </p>
            </td>
        </tr>
        @endif
    </table>

    <div class="content">
        <table class="items">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th>Item</th>
                    <th class="center" style="width:10%;">Qty</th>
                    <th class="right" style="width:16%;">Price</th>
                    <th class="right" style="width:16%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">&#8377;{{ number_format((float) $item->price, 2) }}</td>
                    <td class="right">&#8377;{{ number_format((float) $item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal (incl. GST)</td>
                <td class="right">&#8377;{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>CGST @ 9%</td>
                <td class="right">&#8377;{{ number_format($cgst, 2) }}</td>
            </tr>
            <tr>
                <td>SGST @ 9%</td>
                <td class="right">&#8377;{{ number_format($sgst, 2) }}</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td class="right">
                @if($shipping > 0)
                &#8377;{{ number_format($shipping, 2) }}
                @else
                FREE
                @endif
                </td>
            </tr>
            @if($discount > 0)
            <tr>
                <td>Discount</td>
                <td class="right">&minus;&#8377;{{ number_format($discount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td>Total</td>
                <td class="right">&#8377;{{ number_format($total, 2) }}</td>
            </tr>
        </table>

        <p style="font-size:11px;color:#555555;">Prices are inclusive of GST @ 18% (CGST 9% + SGST 9%). Total GST included: &#8377;{{ number_format($tax, 2) }}.</p>
    </div>

    <div class="footer">
        Thank you for shopping with SmartTech. This is a computer-generated invoice.
    </div>
</body>
</html>
