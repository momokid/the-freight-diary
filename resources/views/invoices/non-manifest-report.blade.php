<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice — {{ $receiptNo }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-wrapper {
            border: 2px dashed #2d6a3f;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .header-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .header-info {
            text-align: right;
            flex: 1;
            padding-left: 16px;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-tagline {
            font-size: 9px;
            color: #444;
            margin-top: 4px;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .invoice-banner {
            background: #5a3015;
            color: white;
            text-align: center;
            padding: 12px;
            margin: 12px 0;
            display: flex;
            align-items: center;
        }

        .invoice-banner .title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.1em;
            flex: 1;
        }

        .invoice-banner .banner-box {
            background: #5a3015;
            border: 2px solid #fff;
            width: 80px;
            height: 40px;
        }

        .client-invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .client-details .label {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .client-details .value {
            font-size: 12px;
            line-height: 1.6;
        }

        .invoice-info {
            text-align: right;
            font-size: 11px;
            line-height: 1.8;
        }

        .invoice-info .row {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .invoice-info .row .lbl {
            font-weight: bold;
        }

        .item-line {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11px;
        }

        thead th {
            background: #2d6a3f;
            color: white;
            padding: 10px 14px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        thead th:last-child {
            text-align: right;
        }

        tbody td {
            padding: 8px 14px;
            border-bottom: 1px solid #ddd;
        }

        tbody td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .subtotal-row td {
            font-weight: 700;
            background: #f5f5f5;
        }

        .total-row td {
            font-weight: 800;
            background: #f0f0f0;
            border-top: 2px solid #000;
            text-transform: uppercase;
        }

        .currency-banner {
            background: #f59e0b;
            color: #000;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 16px;
        }

        .footer-address {
            font-size: 10px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 6px 0;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .issued-by {
            text-align: center;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .issued-by .label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .issued-by .name {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .thank-you {
            text-align: center;
            font-size: 11px;
            font-style: italic;
            margin-bottom: 16px;
        }

        .bank-qr-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
        }

        .bank-details {
            font-size: 10px;
            line-height: 1.8;
        }

        .bank-details .title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .print-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-print {
            padding: 10px 20px;
            background: #2d6a3f;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            .print-bar {
                display: none;
            }

            body {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-wrapper">

        {{-- Header --}}
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
            </div>
            <div class="header-info">
                <div class="company-name">{{ $company?->InstName }}</div>
                <div class="company-tagline">Custom Brokers, Consolidation, Sea &amp; Air Freight, Clearing
                    &amp;<br>Forwarding, Transit, Haulage, Import &amp; Export</div>
            </div>
        </div>

        {{-- Invoice Banner --}}
        <div class="invoice-banner">
            <div class="title">INVOICE</div>
            <div class="banner-box"></div>
        </div>

        {{-- Client + Invoice Info --}}
        <div class="client-invoice-row">
            <div class="client-details">
                <div class="label">Invoice To;</div>
                <div class="value">
                    <strong>{{ $first->FullName }}</strong><br>
                    @if ($first->Address1)
                        {{ $first->Address1 }}<br>
                    @endif
                    @if ($first->Address2)
                        {{ $first->Address2 }}<br>
                    @endif
                    @if ($first->Address3)
                        {{ $first->Address3 }}<br>
                    @endif
                </div>
            </div>
            <div class="invoice-info">
                <div class="row"><span
                        class="lbl">Date:</span><span>{{ \Carbon\Carbon::parse($first->Date)->format('M d, Y') }}</span>
                </div>
                <div class="row"><span class="lbl">Invoice #:</span><span>{{ $receiptNo }}</span></div>
            </div>
        </div>

        {{-- Item line --}}
        @if ($item)
            <div class="item-line">ITEM: {{ strtoupper($item) }}</div>
        @endif

        {{-- Charges Table --}}
        @php
            $totalFee = $entries->sum('Amount');
            $totalGetFundNHIL = $entries->sum('GetFundNHIL');
            $totalVAT = $entries->sum('VAT');
            $grandTotal = $totalFee + $totalGetFundNHIL + $totalVAT;

            $taxComponents = collect($taxComponents);
            $baseComponents = $taxComponents->filter(fn($t) => $t['applies_on'] === 'base');
            $vatComponent = $taxComponents->firstWhere('name', 'VAT');
            $vatRate = $vatComponent ? $vatComponent['rate'] : 15;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (GHC)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ strtoupper($entry->AccountName) }}</td>
                        <td>{{ number_format($entry->Amount, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="subtotal-row">
                    <td>Sub Total</td>
                    <td>{{ number_format($totalFee, 2) }}</td>
                </tr>

                {{-- Individual base tax lines --}}
                @foreach ($baseComponents as $tc)
                    <tr>
                        <td>{{ $tc['name'] }} ({{ $tc['rate'] }}%)</td>
                        <td>{{ number_format($totalFee * ($tc['rate'] / 100), 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td>VAT ({{ $vatRate }}%)</td>
                    <td>{{ number_format($totalVAT, 2) }}</td>
                </tr>

                <tr class="total-row">
                    <td>Total Amount Payable</td>
                    <td>{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Currency Banner --}}
        <div class="currency-banner">ALL CURRENCIES ARE IN GHANA CEDIS (GHC)</div>

        {{-- Footer --}}
        <div class="footer-address">
            <strong>Ghana Address:</strong>
            {{ $company?->Address ?? 'P.O.Box CT3635, Comm 5 Adj. Guinness Depot - Tema' }}<br>
            {{ $company?->TelNo ?? '0242 - 947 228 / 0201 - 382 199' }} |
            {{ $company?->Email ?? 'info@primesurvivors.com' }}
        </div>

        <div class="issued-by">
            <div class="label">Issued By:</div>
            <div class="name">{{ strtoupper($first->Username) }}</div>
        </div>

        <div class="thank-you">Thank you.</div>

    </div>

    {{-- Bank + QR --}}
    <div class="bank-qr-row" style="margin-top: 16px;">
        <div class="bank-details">
            <div class="title">Bank Account Details</div>
            <div><strong>Bank Account:</strong> {{ $bankDetails?->AccountName ?? '—' }}</div>
            <div><strong>Account#:</strong> {{ $bankDetails?->AccountNo ?? '—' }}</div>
            <div><strong>Bank Name:</strong> {{ $bankDetails?->BankName ?? '—' }}</div>
            @if ($bankDetails?->Branch)
                <div><strong>Branch:</strong> {{ $bankDetails->Branch }}</div>
            @endif
        </div>
        @if ($bankDetails?->MomoQR)
            <div style="text-align: center; font-size: 9px;">
                <img src="{{ asset($bankDetails->MomoQR) }}" style="width: 80px; height: 80px; object-fit: contain;"
                    onerror="this.parentElement.style.display='none'">
                <div style="margin-top: 4px; font-weight: bold;">Scan QR code</div>
                @if ($bankDetails->MerchantID)
                    <div>Merchant ID: {{ $bankDetails->MerchantID }}</div>
                @endif
                @if ($bankDetails->MerchantName)
                    <div style="font-weight: bold;">{{ $bankDetails->MerchantName }}</div>
                @endif
            </div>
        @endif
    </div>

    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">🖨 Download PDF</button>
        <button class="btn-print" onclick="window.close()" style="background: #6b7280;">✕ Close</button>
    </div>

</body>

</html>
