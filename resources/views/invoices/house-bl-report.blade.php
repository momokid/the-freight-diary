<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice — {{ $hbl }}</title>
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

        /* ── Outer border ── */
        .invoice-wrapper {
            border: 2px dashed #2d6a3f;
            padding: 20px;
            overflow: visible;
        }

        /* ── Header ── */
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
            letter-spacing: 0.05em;
        }

        .company-tagline {
            font-size: 9px;
            color: #444;
            margin-top: 4px;
            line-height: 1.4;
            text-transform: uppercase;
        }

        /* ── Invoice banner ── */
        .invoice-banner {
            background: #5a3015;
            color: white;
            text-align: center;
            padding: 12px;
            margin: 12px 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
        }

        .invoice-banner .title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.1em;
            flex: 1;
            text-align: center;
        }

        .invoice-banner .banner-box {
            background: #5a3015;
            border: 2px solid #fff;
            width: 80px;
            height: 40px;
        }

        /* ── Client + Invoice info ── */
        .client-invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .client-details {
            flex: 1;
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

        /* ── Charges table ── */
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
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        thead th:last-child {
            text-align: right;
        }

        tbody td {
            padding: 8px 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tbody td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .subtotal-row td {
            font-weight: 600;
            background: #f5f5f5;
        }

        .total-row td {
            font-weight: 800;
            font-size: 12px;
            background: #f0f0f0;
            border-top: 2px solid #000;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Currency banner ── */
        .currency-banner {
            background: #f59e0b;
            color: #000;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 16px;
        }

        /* ── Footer ── */
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
            letter-spacing: 0.05em;
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

        /* ── Bank details + QR ── */
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

        /* ── Print button ── */
        .print-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
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

        .btn-print:hover {
            opacity: 0.85;
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

        {{-- ── Header ── --}}
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
            </div>
            <div class="header-info">
                <div class="company-name">{{ $company?->InstName ?? 'Prime Survivors International Ltd' }}</div>
                <div class="company-tagline">Custom Brokers, Consolidation, Sea &amp; Air Freight, Clearing
                    &amp;<br>Forwarding, Transit, Haulage, Import &amp; Export</div>
            </div>
        </div>

        {{-- ── Invoice Banner ── --}}
        <div class="invoice-banner">
            <div class="title">INVOICE</div>
            <div class="banner-box"></div>
        </div>

        {{-- ── Client Details + Invoice Info ── --}}
        <div class="client-invoice-row">
            <div class="client-details">
                <div class="label">Client Details:</div>
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
                <div class="row">
                    <span class="lbl">Date:</span>
                    <span>{{ \Carbon\Carbon::parse($first->Date)->format('M d, Y') }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Invoice# :</span>
                    <span>{{ $first->ReceiptNo }}</span>
                </div>
                <div class="row">
                    <span class="lbl">BL# :</span>
                    <span>{{ $hbl }}</span>
                </div>
            </div>
        </div>

        {{-- ── Charges Table ── --}}
        @php
            $totalFee = $entries->sum('Fee');
            $totalGetFundNHIL = $entries->sum('GetFundNHIL');
            $totalVAT = $entries->sum('VAT');
            $subTotal = $totalFee + $totalGetFundNHIL;
            $grandTotal = $subTotal + $totalVAT;

            // Build tax label dynamically from components
            $taxLabel = collect($taxComponents)
                ->filter(fn($t) => $t['applies_on'] === 'base')
                ->map(fn($t) => $t['name'] . '(' . $t['rate'] . '%)')
                ->implode('+');
            $vatComponent = collect($taxComponents)->firstWhere('name', 'VAT');
            $vatRate = $vatComponent ? $vatComponent['rate'] : 15;
        @endphp

        <table>
            <thead>
                <tr>
                    <th style="text-align: center;">Description</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                {{-- Individual charge lines --}}
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ $entry->AccountName }}</td>
                        <td>{{ number_format($entry->Fee, 2) }}</td>
                    </tr>
                @endforeach

                {{-- GetFund + NHIL line --}}
                <tr>
                    <td>{{ $taxLabel }}</td>
                    <td>{{ number_format($totalGetFundNHIL, 2) }}</td>
                </tr>

                {{-- New Sub Total --}}
                <tr class="subtotal-row">
                    <td>New Sub Total</td>
                    <td>{{ number_format($subTotal, 2) }}</td>
                </tr>

                {{-- VAT --}}
                <tr>
                    <td>VAT({{ $vatRate }}%)</td>
                    <td>{{ number_format($totalVAT, 2) }}</td>
                </tr>

                {{-- Total --}}
                <tr class="total-row">
                    <td>Total Amount Payable</td>
                    <td>{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ── Currency Banner ── --}}
        <div class="currency-banner">ALL CURRENCIES ARE IN GHANA CEDIS (GHC)</div>

        {{-- ── Footer Address ── --}}
        <div class="footer-address">
            <strong>Ghana Address:</strong>
            {{ $company?->Address ?? 'P.O.Box CT3635, Comm 5 Adj. Guinness Depot - Tema' }}<br>
            {{ $company?->TelNo ?? '0242 - 947 228 / 0201 - 382 199' }} |
            {{ $company?->Email ?? 'info@primesurvivors.com' }}
        </div>

        {{-- ── Issued By ── --}}
        <div class="issued-by">
            <div class="label">Issued By:</div>
            <div class="name">{{ strtoupper($first->Username) }}</div>
        </div>

        <div class="thank-you">Thank you.</div>

        {{-- ── Bank Details ── --}}
        <div class="bank-qr-row">
            <div class="bank-details">
                <div class="title">Bank Account Details</div>
                <div><strong>Bank Account:</strong> Prime Survivors International Limited</div>
                <div><strong>Account#:</strong> 1441004070750</div>
                <div><strong>Bank Name:</strong> ECOBANK</div>
                <div><strong>Branch:</strong> Tema Main</div>
            </div>

            @if ($bankDetails?->MomoQR)
                <div style="text-align: center; font-size: 9px;">
                    <img src="{{ asset($bankDetails->MomoQR) }}" alt="MoMo QR Code"
                        style="width: 80px; height: 80px; object-fit: contain;"
                        onerror="this.parentElement.style.display='none'">
                    <div style="margin-top: 4px; font-weight: bold;">Scan QR code</div>
                    @if ($bankDetails->MerchantID)
                        <div style="margin-top: 2px;">Merchant ID: {{ $bankDetails->MerchantID }}</div>
                    @endif
                    @if ($bankDetails->MerchantName)
                        <div style="font-weight: bold;">{{ $bankDetails->MerchantName }}</div>
                    @endif
                </div>
            @endif

        </div>

    </div>

    {{-- ── Print Button ── --}}
    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">🖨 Download PDF</button>
        <button class="btn-print" onclick="window.close()" style="background: #6b7280;">✕ Close</button>
    </div>

</body>

</html>
