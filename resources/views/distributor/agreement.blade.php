<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Used Electronics Sale Agreement #{{ $agreement->agreement_id }}</title>
    <style>
        /* DomPDF Compatible Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .agreement-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }

        .header {
            background-color: #2c5aa0;
            color: white;
            padding: 25px 30px;
            margin-bottom: 25px;
            border-radius: 0 0 15px 15px;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, transparent 25%, #2c5aa0 25%) -25px 0,
                        linear-gradient(225deg, transparent 25%, #2c5aa0 25%) -25px 0;
            background-size: 50px 50px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .agreement-header{
            text-align: center;
            margin-bottom: 20px;
        }

        .agreement-title {
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .agreement-subtitle {
            font-size: 13px;
            color: rgba(46, 46, 46, 0.9);
            font-style: italic;
        }

        .agreement-meta {
            color: rgba(255,255,255,0.9);
        }

        .company-info {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .agreement-info {
            display: table-cell;
            vertical-align: top;
            width: 40%;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .company-details {
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
        }

        .parties-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .buyer{
            margin-bottom: 15px;
        }

        /* .buyer,
        .seller {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-right: 20px;
        } */

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 5px;
            position: relative;
        }

        .address {
            line-height: 1.6;
            color: #666;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 2px solid #2c5aa0;
        }

        .items-table th {
            background-color: #2c5aa0;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c5aa0;
        }

        .items-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .clause {
            margin-bottom: 20px;
            padding-left: 15px;
            border-left: 3px solid #2c5aa0;
        }

        .clause-title {
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .clause-content {
            color: #555;
            line-height: 1.6;
        }

        .signature-section {
            display: flex;
            gap: 100px;
            width: 100%;
            margin-top: 50px;
        }

        .signature-box {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-top: 60px;
            position: relative;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 100%;
            margin-bottom: 5px;
        }

        .footer {
            border-top: 2px solid #2c5aa0;
            padding-top: 20px;
            text-align: center;
            color: #666;
            font-size: 11px;
            margin-top: 30px;
        }

        .legal-notice {
            background-color: #fff8f8;
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            position: relative;
        }

        .note-title {
            font-weight: bold;
            color: #d63031;
            margin-bottom: 8px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .items-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #2c5aa0 !important;
                color: white !important;
            }
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #2c5aa0 !important;
                color: white !important;
            }
        }

        .buttons-container {
            display: flex;
            justify-content: center;
            position: fixed;
            bottom: 80px;
            right: 80px;
            gap: 10px;
        }

        .buttons-container .btn {
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

        .stamp {
            position: absolute;
            right: 50px;
            bottom: 50px;
            width: 120px;
            height: 120px;
            border: 2px solid #d63031;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(15deg);
            opacity: 0.7;
            color: #d63031;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="agreement-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    <div class="company-name">MOBILE BAZAR</div>
                    <div class="company-details">
                        {{ $shop->shop_address }}<br>
                        @if($shop->shop_gstin) GSTIN: {{ $shop->shop_gstin }} @endif
                    </div>
                </div>
                <div class="agreement-info">
                    <div class="agreement-meta">
                        <strong>Agreement No:</strong> MB/{{ $year }}/{{ $agreement->agreement_id }}<br>
                        <strong>Agreement Date:</strong> {{ $date }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Introduction -->
        <div class="agreement-header">
            <div class="agreement-title">Electronics Purchase Agreement</div>
            <div class="clause-content" style="text-align: center; font-style: italic;">
                This Agreement is executed on this <u>{{ $day }}</u> day of <u>{{ $month }}</u>, {{ $year }}, at {{ $shop->shop_address ?? 'Murshidabad' }}, India, by and between:
            </div>
        </div>

        <!-- Parties Section -->
        <div class="parties-section">
            <div class="buyer">
                <div class="section-title">THE BUYER</div>
                <div class="address">
                    <strong>MOBILE BAZAR</strong>, located at {{ $shop->shop_address }}<br>
                    (hereinafter referred to as the "Buyer", which expression shall, unless repugnant to the context or meaning thereof, include its successors, administrators, and permitted assigns).
                </div>
            </div>
            <div class="seller">
                <div class="section-title">THE SELLER</div>
                <div class="address">
                    Mr./Ms. <strong>{{ $customer->name }}</strong>, residing at {{ $customer->address }}, holding valid Government-issued Aadhaar: <u>{{ $customer->aadhaar_no ?? 'XXXX-XXXX-XXXX' }}</u><br>
                    (hereinafter referred to as the "Seller", which expression shall, unless repugnant to the context or meaning thereof, include their legal heirs, successors, and assigns).
                </div>
            </div>
        </div>

        <div class="agreement-subtitle">Under Indian Contract Act, 1872</div>

        <!-- Legal Notice -->
        <div class="legal-notice">
            <div class="note-title">LEGAL DECLARATION BY SELLER:</div>
            <div>I hereby confirm that I am the legal owner of the items being sold and that they are not stolen, lost, or involved in any criminal activity. I understand that false declarations may lead to legal consequences under Indian Penal Code Sections 415, 420, and other applicable laws.</div>
        </div>

        <!-- Clauses -->
        <div class="clause">
            <div class="clause-title">1. OBJECT AND PURPOSE</div>
            <div class="clause-content">
                The Seller is voluntarily selling to the Buyer certain electronic goods including, but not limited to, mobile phones, laptops, computers, tablets, cameras, or other devices (hereinafter referred to as the "Product(s)"). The purpose of this Agreement is to document the terms of sale and indemnify the Buyer against any legal, criminal, or civil liability arising from such transactions.
            </div>
        </div>

        <div>
            <div class="clause-title">2. DESCRIPTION OF PRODUCTS SOLD</div>
            <div class="clause-content">
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
                                <td class="text-right amount">
                                    @if ($item->inventory->product)
                                        ₹{{ number_format($item->inventory->product->mrp, 2) }}
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->qty }} {{ ucfirst($item->inventory->unit) }}</td>
                                <td class="text-right amount">₹{{ number_format($item->rate, 2) }}</td>
                                <td class="text-center">{{ $item->tax }}
                                    ({{ number_format($item->tax_percent, 0) }}%)</td>
                                <td class="text-right amount">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                            <tr>
                                <td colspan="7" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right"><strong>₹{{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Include all your legal clauses exactly as in your template -->
        <div class="clause">
            <div class="clause-title">3. SELLER'S DECLARATIONS</div>
            <div class="clause-content">
                The Seller hereby solemnly affirms and declares that:<br><br>
                a) They are the true and lawful owner of the aforementioned Product(s).<br>
                b) The Product(s) have not been stolen, lost, involved in any criminal activity, nor are they blacklisted, under EMI, lease, lien, or any form of encumbrance.<br>
                c) The information provided with respect to the Product(s), including brand, model, IMEI/serial number, and physical/functional condition, is true and correct to the best of their knowledge.<br>
                d) The Product(s) are not subject to any police case, investigation, or First Information Report (FIR) as of the date of sale.<br>
                e) They understand that providing false information or selling stolen/illegal property is a punishable offence under the Indian Penal Code (IPC), including but not limited to Sections 410, 411, 415, 417, 420, and 468.
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">4. INDEMNITY & LEGAL RESPONSIBILITY</div>
            <div class="clause-content">
                a) The Seller agrees to fully indemnify, defend, and hold harmless the Buyer, its owners, staff, and affiliates from and against any claims, liabilities, police inquiries, legal notices, FIRs, damages, losses, or costs (including legal fees) arising directly or indirectly from:<br>
                &nbsp;&nbsp;&nbsp;&nbsp;- The sale of stolen, lost, or illegal goods<br>
                &nbsp;&nbsp;&nbsp;&nbsp;- Misrepresentation of ownership or product condition<br>
                &nbsp;&nbsp;&nbsp;&nbsp;- Any civil or criminal proceedings initiated by third parties or law enforcement<br><br>
                b) The Buyer shall not be held liable in any manner whatsoever for any consequences arising out of the origin, ownership, or status of the Product(s) post-sale.<br><br>
                c) In the event that the Product(s) sold are found to be stolen or involved in a criminal investigation, the Seller shall bear full legal consequences and shall cooperate fully with law enforcement authorities.
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">5. FINAL SALE AND NO LIABILITY</div>
            <div class="clause-content">
                a) This sale is final and irrevocable. The Buyer is under no obligation to return the Product(s) or offer refunds unless agreed in writing.<br>
                b) The Buyer shall have the right to retain copies of the Seller's ID and any other relevant documents for compliance and verification.
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">6. JURISDICTION</div>
            <div class="clause-content">
                This Agreement shall be governed by and construed in accordance with the laws of India. Any disputes arising out of or in connection with this Agreement shall be subject to the exclusive jurisdiction of the courts at Berhampore, West Bengal.
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">7. ACKNOWLEDGMENT</div>
            <div class="clause-content">
                The Seller has read and fully understood the terms of this Agreement and signs it voluntarily with full understanding of its legal implications.
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box" style="text-align: left;">
                <div class="signature-line"></div>
                <div><strong>SELLER'S SIGNATURE</strong></div>
                <div>Name: <u>{{ $customer->name }}</u></div>
                <div>Date: <u>{{ now()->format('d/m/Y') }}</u></div>
            </div>
            <div class="signature-box" style="text-align: right;">
                <div class="signature-line"></div>
                <div><strong>BUYER'S REPRESENTATIVE SIGNATURE</strong></div>
                <div>Name: <u>{{ $signatory }}</u></div>
                <div>For <strong>{{ $shop->shop_name }}</strong></div>
                <div>Date: <u>{{ now()->format('d/m/Y') }}</u></div>
            </div>
        </div>

        <!-- Witnesses -->
        <div class="signature-section" style="margin-top: 15px;">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>WITNESS 1</strong></div>
                <div>Name: <u>_______________________________</u></div>
            </div>
            <div class="signature-box" style="text-align: right;">
                <div class="signature-line"></div>
                <div><strong>WITNESS 2</strong></div>
                <div><u>_______________________________</u> :Name</div>
            </div>
        </div>

        <!-- Stamp -->
        <div class="stamp no-print">
            <div style="text-align: center;">
                <div>Authorized</div>
                <div>Signatory</div>
                <div style="font-size: 10px;">{{ $shop->name }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>This is a legally binding document. Please retain a copy for your records.</strong></p>
            <p>For any queries, please contact {{ $shop->name }} at {{ $shop->phone }} @if($shop->email) or {{ $shop->email }} @endif</p>
            <p>{{ $shop->shop_name }} | {{ $shop->shop_address }} @if($shop->shop_gstin) | GSTIN: {{ $shop->shop_gstin }} @endif</p>
        </div>
    </div>

    <div class="no-print buttons-container">
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</body>
<script>
    // Auto-print if ?print=1 is in URL
    if (window.location.search.includes('print=1')) {
        window.print();
    }
</script>
</html>
