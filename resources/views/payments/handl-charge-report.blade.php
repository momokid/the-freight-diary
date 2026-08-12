<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handling Charge Receipt — {{ $receiptNo }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 30px;
            max-width: 750px;
            margin: 0 auto;
        }

        .wrapper {
            border: 2px dashed #1a3d26;
            padding: 20px;
        }

        /* ── Company header ── */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 16px;
        }

        .header-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .header-info {
            text-align: right;
            flex: 1;
        }

        .header-info .company {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-info .tagline {
            font-size: 9px;
            color: #444;
            margin-top: 4px;
            line-height: 1.4;
            text-transform: uppercase;
        }

        /* ── Banner ── */
        .banner {
            background: #1a3d26;
            color: white;
            text-align: center;
            padding: 12px;
            margin: 12px 0;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.1em;
        }

        /* ── Meta row ── */
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 11px;
        }

        /* ── Details grid ── */
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            margin-bottom: 16px;
            font-size: 11px;
        }

        .details .row {
            display: flex;
            gap: 6px;
        }

        .details .lbl {
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
            min-width: 110px;
        }

        .details .val {
            flex: 1;
            border-bottom: 1px dotted #999;
            padding-bottom: 1px;
        }

        /* ── Amount table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11px;
        }

        thead th {
            background: #1a3d26;
            color: white;
            padding: 8px 12px;
            text-align: left;
            text-transform: uppercase;
            font-weight: bold;
        }

        thead th:last-child {
            text-align: right;
        }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }

        tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .total-row td {
            font-weight: 800;
            background: #f0f0f0;
            border-top: 2px solid #000;
            font-size: 12px;
        }

        /* ── Footer ── */
        .currency {
            background: #f59e0b;
            color: #000;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 14px;
        }

        .footer-address {
            font-size: 10px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .issued {
            text-align: center;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .issued .name {
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

        /* ── Bank + QR ── */
        .bank-qr {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-top: 16px;
            font-size: 10px;
        }

        .bank-details .title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .bank-details div {
            line-height: 1.8;
        }

        /* ── Print button ── */
        .print-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-print {
            padding: 10px 20px;
            background: #1a3d26;
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
        }
    </style>
</head>

<body>

    <div class="wrapper">

        {{-- Company header --}}
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
            </div>
            <div class="header-info">
                <div class="company">{{ $company?->InstName }}</div>
                <div class="tagline">Custom Brokers, Consolidation, Sea &amp; Air Freight, Clearing &amp;<br>
                    Forwarding, Transit, Haulage, Import &amp; Export</div>
            </div>
        </div>

        {{-- Banner --}}
        <div class="banner">HANDLING CHARGE RECEIPT</div>

        {{-- Receipt meta --}}
        <div class="meta-row">
            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($first->Date)->format('M d, Y') }}</div>
            <div><strong>Receipt#:</strong> {{ $receiptNo }}</div>
        </div>

        {{-- Consignment details --}}
        <div class="details">
            <div class="row">
                <span class="lbl">Consignee:</span>
                <span class="val">{{ $consignee?->FullName ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="lbl">House BL#:</span>
                <span class="val">{{ $first->SubClassID }}</span>
            </div>
            <div class="row">
                <span class="lbl">Main BL#:</span>
                <span class="val">{{ $manifest?->MainBL ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="lbl">Vessel:</span>
                <span class="val">{{ $manifest?->VesselName ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="lbl">ETA:</span>
                <span class="val">
                    {{ $manifest?->ETA ? \Carbon\Carbon::parse($manifest->ETA)->format('M d, Y') : '—' }}
                </span>
            </div>
            <div class="row">
                <span class="lbl">Description:</span>
                <span class="val">{{ $first->Description }}</span>
            </div>
        </div>

        {{-- Fee breakdown table --}}
        <table>
            <thead>
                <tr>
                    <th>Charge Description</th>
                    <th style="text-align: right;">Amount (GHC)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines as $line)
                    <tr>
                        <td>{{ $line->AccountName }}</td>
                        <td>{{ number_format($line->Cr, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Amount Received</td>
                    <td>{{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Currency note --}}
        <div class="currency">ALL CURRENCIES ARE IN GHANA CEDIS (GHC)</div>

        {{-- Footer address --}}
        <div class="footer-address">
            <strong>Ghana Address:</strong>
            {{ $company?->Address ?? 'P.O.Box CT3635, Comm 5 Adj. Guinness Depot - Tema' }}<br>
            {{ $company?->TelNo ?? '0242 - 947 228 / 0201 - 382 199' }} |
            {{ $company?->Email ?? 'info@primesurvivors.com' }}
        </div>

        {{-- Issued by --}}
        <div class="issued">
            <div>ISSUED BY:</div>
            <div class="name">{{ strtoupper($first->Username) }}</div>
        </div>
        <div class="thank-you">Thank you.</div>

    </div>

    {{-- Bank details + QR outside wrapper --}}
    <div class="bank-qr">
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
                <img src="{{ asset($bankDetails->MomoQR) }}" alt="MoMo QR"
                    style="width: 80px; height: 80px; object-fit: contain;"
                    onerror="this.parentElement.style.display='none'">
                <div style="font-weight: bold; margin-top: 4px;">Scan QR code</div>
                @if ($bankDetails->MerchantID)
                    <div>Merchant ID: {{ $bankDetails->MerchantID }}</div>
                @endif
                @if ($bankDetails->MerchantName)
                    <div style="font-weight: bold;">{{ $bankDetails->MerchantName }}</div>
                @endif
            </div>
        @endif
    </div>

    {{-- Print button --}}
    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">🖨 Download PDF</button>
        <button class="btn-print" onclick="window.close()" style="background: #6b7280;">✕ Close</button>
    </div>

</body>

</html>
