<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Agreement #{{ $loan->loan_no }}</title>
    <style>
        /* DomPDF Compatible Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }

        .agreement-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
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
            margin-bottom: 15px;
        }

        .agreement-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .agreement-subtitle {
            font-size: 11px;
            color: rgba(46, 46, 46, 0.9);
            font-style: italic;
        }

        .agreement-meta {
            font-size: 12px;
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
            margin-bottom: 15px;
        }

        .buyer{
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 3px;
        }

        .address {
            line-height: 1.4;
            color: #666;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .clause {
            margin-bottom: 15px;
        }

        .clause-title {
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .clause-content {
            color: #555;
            line-height: 1.4;
        }

        .signature-section {
            display: flex;
            gap: 80px;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-top: 30px;
            position: relative;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 100%;
            margin-bottom: 5px;
        }

        .w-signature-section {
            display: flex;
            gap: 80px;
            width: 100%;
            margin-top: 15px;
        }

        .w-signature-box {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-top: 15px;
            position: relative;
        }

        .footer {
            border-top: 2px solid #2c5aa0;
            padding-top: 15px;
            text-align: center;
            color: #666;
            font-size: 10px;
            margin-top: 20px;
        }

        .legal-notice {
            background-color: #fff8f8;
            border-left: 4px solid #ff6b6b;
            padding: 10px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }

        .critical-notice {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 10px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }

        .note-title {
            font-weight: bold;
            color: #d63031;
            margin-bottom: 5px;
        }

        .critical-title {
            font-weight: bold;
            color: #e65100;
            margin-bottom: 5px;
        }

        .sub-clause {
            margin-bottom: 8px;
        }

        .sub-clause-title {
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 3px;
        }

        .list-item {
            margin-bottom: 5px;
            padding-left: 12px;
            position: relative;
        }

        .list-item:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #2c5aa0;
            font-weight: bold;
        }

        .highlight-item {
            background-color: #fff3e0;
            padding: 6px;
            border-left: 3px solid #ff9800;
            margin-bottom: 8px;
        }

        .stamp {
            position: absolute;
            right: 30px;
            bottom: 30px;
            width: 100px;
            height: 100px;
            border: 2px solid #d63031;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(15deg);
            opacity: 0.7;
            color: #d63031;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .photo-container{
            margin-top: 20px;
            width: 100%;
            align-items: center;
            text-align: center;
            display: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #2c5aa0 !important;
                color: white !important;
            }
            .photo-container{
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="agreement-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    <div class="company-name">{{ $loan->agreement->shop->shop_name }}</div>
                    <div class="company-details">
                        {{ $loan->agreement->shop->shop_address }}<br>
                        @if($loan->agreement->shop->shop_gstin) GSTIN: {{ $loan->agreement->shop->shop_gstin }} @endif
                    </div>
                </div>
                <div class="agreement-info">
                    <div class="agreement-meta">
                        <strong>Date:</strong> {{ $loan->loan_date->format('d M Y') }}<br>
                        @if ($loan->invoice)
                            <strong>Invoice:</strong> {{ $loan->invoice->invoice_no }}<br>
                        @endif
                        <strong>Loan No:</strong> {{ $loan->loan_no }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Introduction -->
        <div class="agreement-header">
            <div class="agreement-title">Loan Agreement</div>
            <div class="clause-content" style="text-align: center; font-style: italic;">
                This Loan Agreement is executed on {{ $loan->loan_date->format('d M Y') }} at {{ $loan->agreement->shop->shop_address ?? 'Murshidabad' }}, India, by and between:
            </div>
        </div>

        <!-- Parties Section -->
        <div class="parties-section">
            <div class="buyer">
                <div class="section-title">THE LENDER</div>
                <div class="address">
                    <strong>{{ $loan->financer->name }}</strong>, represented by its authorized signatory. (hereinafter referred to as the "Lender")
                </div>
            </div>
            <div class="seller">
                <div class="section-title">THE BORROWER</div>
                <div class="address">
                    <strong>{{ $loan->name }}</strong>, residing at {{ $loan->address }}, Aadhaar: <u>{{ $loan->aadhaar_no }}</u> (hereinafter referred to as the "Borrower")
                </div>
            </div>
        </div>

        <div class="agreement-subtitle">Under Indian Contract Act, 1872</div>

        <!-- Legal Notice -->
        <div class="legal-notice">
            <div class="note-title">DECLARATION:</div>
            <div>I confirm all information provided is true. Any false declaration may lead to legal consequences under Indian laws.</div>
        </div>

        <!-- Critical Notice -->
        <div class="critical-notice">
            <div class="critical-title">DEVICE SECURITY NOTICE:</div>
            <div>The device is equipped with remote locking features. The Borrower agrees to device security, unlock charges, and recovery terms as detailed below.</div>
        </div>

        <!-- Loan Details -->
        <div class="clause">
            <div class="clause-title">1. LOAN DETAILS</div>
            <div class="clause-content">
                Invoice: ₹{{ number_format($loan->invoice_amount, 2) }}, Down Payment: ₹{{ number_format($loan->down_payment, 2) }}, Loan Amount: ₹{{ number_format($loan->loan_amount, 2) }}, Processing: ₹{{ number_format($loan->processing_charge, 2) }}, File Charge: ₹{{ number_format($loan->file_charge, 2) }}
                <br><strong>Total Payable: ₹{{ number_format($loan->payable_amount, 2) }}</strong>
            </div>
        </div>

        <!-- EMI Schedule -->
        <div class="clause">
            <div class="clause-title">2. EMI SCHEDULE</div>
            <div class="clause-content">
                EMI repayment schedule: @foreach($loan->emis as $emi){{ $emi->emi_date->format('d M Y') }} - ₹{{ number_format($emi->emi_amount, 2) }}, @endforeach
            </div>
        </div>

        <!-- Combined Terms -->
        <div class="clause">
            <div class="clause-title">3. REPAYMENT TERMS & OBLIGATIONS</div>
            <div class="clause-content">
                <div class="sub-clause">
                    <div class="sub-clause-title">3.1 Payment Terms:</div>
                    EMIs must be paid on or before due dates. Late payment penalty: 2% per month on overdue amounts. Processing and file charges are non-refundable.
                </div>

                <div class="sub-clause">
                    <div class="sub-clause-title">3.2 Borrower's Obligations:</div>
                    <div class="list-item">Make timely payments and inform lender of any address/contact changes.</div>
                    <div class="list-item">Handle device with reasonable care and protect from damage, loss, or theft.</div>
                    <div class="list-item">Not sell, transfer, or pledge the device until full loan repayment.</div>
                    <div class="highlight-item">Loan repayment continues regardless of device condition, loss, damage, or theft.</div>
                </div>
            </div>
        </div>

        <!-- Device Security -->
        <div class="clause">
            <div class="clause-title">4. DEVICE SECURITY & LOCK MECHANISM</div>
            <div class="clause-content">
                <div class="sub-clause">
                    <div class="sub-clause-title">4.1 Lock Authorization & Conditions:</div>
                    <div class="highlight-item">Borrower authorizes remote device locking for EMI non-payment. Device locks immediately upon EMI default.</div>
                    <div class="list-item">Device remains locked until overdue amount is paid.</div>
                    <div class="list-item">Borrower shall not tamper with or disable security software.</div>
                </div>

                <div class="sub-clause">
                    <div class="sub-clause-title">4.2 Unlock Charges:</div>
                    <div class="highlight-item">If device remains locked for more than 2 days, unlock charge of ₹272 applies in addition to overdue EMI.</div>
                    <div class="list-item">Unlock charge is non-negotiable and separate from late payment penalties.</div>
                </div>
            </div>
        </div>

        <!-- Lender's Rights & Default -->
        <div class="clause">
            <div class="clause-title">5. LENDER'S RIGHTS & DEFAULT EVENTS</div>
            <div class="clause-content">
                <div class="sub-clause">
                    <div class="sub-clause-title">5.1 Recovery & Repossession Rights:</div>
                    <div class="highlight-item">After 2 consecutive months of EMI default, lender has absolute right to repossess the device.</div>
                    <div class="list-item">Lender may engage recovery agents and report defaults to credit bureaus.</div>
                    <div class="list-item">Repossession does not absolve borrower of remaining loan obligation.</div>
                    <div class="list-item">Lender may initiate legal proceedings for recovery of dues.</div>
                </div>

                <div class="sub-clause">
                    <div class="sub-clause-title">5.2 Default Events:</div>
                    Non-payment for 1+ day (device lock), 30+ days (legal action), 2 consecutive months (repossession), providing false information, breach of terms, tampering with security system.
                </div>

                <div class="sub-clause">
                    <div class="sub-clause-title">5.3 Default Consequences:</div>
                    <div class="highlight-item">Upon default: immediate device lock, entire amount becomes due, repossession rights, legal action, borrower liable for all recovery costs.</div>
                </div>
            </div>
        </div>

        <!-- Legal & General Provisions -->
        <div class="clause">
            <div class="clause-title">6. LEGAL & GENERAL PROVISIONS</div>
            <div class="clause-content">
                <div class="sub-clause">
                    <div class="sub-clause-title">6.1 Jurisdiction & Governing Law:</div>
                    All disputes subject to exclusive jurisdiction of courts at Domkal, Murshidabad, India. Governed by Indian laws.
                </div>

                <div class="sub-clause">
                    <div class="sub-clause-title">6.2 General Terms:</div>
                    Amendments require written consent of both parties. If any provision is invalid, remaining provisions stay valid. This agreement constitutes the entire agreement between parties.
                </div>
            </div>
        </div>

        <!-- Acknowledgment -->
        <div class="clause">
            <div class="clause-title">7. ACKNOWLEDGMENT</div>
            <div class="clause-content">
                Both parties acknowledge reading, understanding, and agreeing to all terms. Borrower specifically acknowledges device security features, lock mechanisms, unlock charges, repossession rights, and continuous repayment obligations regardless of device condition. This agreement is executed voluntarily without coercion.
            </div>
        </div>

        <!-- Witnesses -->
        <div class="clause">
            <div class="clause-title">8. WITNESSES</div>
            <div class="clause-content">
                This agreement is executed in the presence of the following witnesses:
                <div class="w-signature-section">
                    <div class="w-signature-box" style="text-align: left;">
                        <div class="signature-line"></div>
                        <div><strong>Witness 1</strong></div>
                    </div>
                    <div class="w-signature-box" style="text-align: right;">
                        <div class="signature-line"></div>
                        <div><strong>Witness 2</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box" style="text-align: left;">
                <div class="signature-line"></div>
                <div><strong>BORROWER'S SIGNATURE</strong></div>
                <div>Name: <u>{{ $loan->name }}</u></div>
                <div>Date: <u>{{ $loan->loan_date->format('d/m/Y') }}</u></div>
            </div>
            <div class="signature-box" style="text-align: right;">
                <div class="signature-line"></div>
                <div><strong>LENDER'S SIGNATURE</strong></div>
                <div>Name: <u>{{ $loan->financer->name }}</u></div>
                <div>Date: <u>{{ $loan->loan_date->format('d/m/Y') }}</u></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>This is a legally binding document. Please retain a copy for your records.</strong></p>
            <p>For queries, contact {{ $loan->financer->name }} at {{ $loan->financer->mobile }}</p>
            <p style="color: #d63031; font-weight: bold;">IMPORTANT: Device lock, unlock charges, and repossession terms are integral parts of this agreement.</p>
        </div>

        <!-- Official Stamp -->
        <div class="stamp no-print">
            OFFICIAL<br>SEAL
        </div>
    </div>

    <div class="photo-container">
        <div class="photo">
            @if ($loan->agreement->photo)
                <img src="{{ asset('storage/' . $loan->agreement->photo) }}" alt="Photo" style="width: 3.5cm; height: 4.5cm;">
            @endif
        </div>
        <div class="documents">
            @if ($loan->customer->doc_one)
                <img src="{{ asset('storage/' . $loan->customer->doc_one) }}" alt="Photo" style="width: 85.60mm; height: 53.98mm;">
            @endif
            @if ($loan->customer->doc_two)
                <img src="{{ asset('storage/' . $loan->customer->doc_two) }}" alt="Photo" style="width: 85.60mm; height: 53.98mm;">
            @endif
            @if ($loan->customer->doc_three)
                <img src="{{ asset('storage/' . $loan->customer->doc_three) }}" alt="Photo" style="width: 85.60mm; height: 53.98mm;">
            @endif
        </div>
    </div>
</body>
</html>
