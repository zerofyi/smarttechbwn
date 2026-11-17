<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartTech — Order {{ $order->order_number }} {{ $status }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222222;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#6B1A00,#8B2500,#A83010);background-color:#6B1A00;color:#ffffff;padding:20px 24px;">
                            <h1 style="margin:0;font-size:22px;">SmartTech</h1>
                            <p style="margin:4px 0 0;font-size:13px;color:#ffd9cc;">Order update</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;font-size:15px;">Hi {{ $order->user->name ?? 'there' }},</p>
                            <p style="margin:0 0 16px;font-size:15px;">
                                Your order <strong>{{ $order->order_number }}</strong> is now
                                <strong style="text-transform:capitalize;">{{ str_replace('_', ' ', $status) }}</strong>.
                            </p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e5e5e5;border-radius:6px;font-size:14px;">
                                <tr>
                                    <td style="color:#666666;">Order number</td>
                                    <td align="right"><strong>{{ $order->order_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="color:#666666;">Status</td>
                                    <td align="right" style="text-transform:capitalize;"><strong>{{ str_replace('_', ' ', $status) }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="color:#666666;">Order total</td>
                                    <td align="right"><strong>&#8377;{{ number_format((float) $order->total, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="color:#666666;">Payment</td>
                                    <td align="right">{{ strtoupper($order->payment_method ?? '') }} &middot; {{ ucfirst($order->payment_status ?? '') }}</td>
                                </tr>
                            </table>
                            @if(! empty($note))
                                <p style="margin:16px 0 0;font-size:14px;color:#555555;">{{ $note }}</p>
                            @endif
                            <p style="margin:20px 0 0;">
                                <a href="{{ route('account.orders.show', $order->order_id) }}" style="display:inline-block;background-color:#6B1A00;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:6px;font-size:14px;font-weight:bold;">View your order</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #eeeeee;font-size:12px;color:#888888;">
                            SmartTech &middot; This is an automated message, please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
