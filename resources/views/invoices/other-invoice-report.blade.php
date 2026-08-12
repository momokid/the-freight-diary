<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice — {{ $receiptNo }}</title>
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
            padding: 20px;
            max-width: 750px;
            margin: 0 auto;
        }

        .wrapper {
            border: 3px solid #16a34a;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
            gap: 16px;
        }

        .header-left {
            flex: 1;
        }

        .header-left .title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .header-left .tin {
            font-size: 11px;
            font-weight: bold;
        }

        .header-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .date-row {
            text-align: right;
            font-size: 11px;
            margin-bottom: 16px;
        }

        .date-row .receipt {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .from-to {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .from-to .label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .from-to .value {
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11px;
        }

        thead th {
            background: #16a34a;
            color: white;
            padding: 8px 12px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        thead th:last-child {
            text-align: right;
        }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tbody td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .total-row td {
            font-weight: 800;
            background: #f0f0f0;
            border-top: 2px solid #000;
        }

        .issued {
            text-align: center;
            font-size: 11px;
            margin: 16px 0 4px;
        }

        .issued .name {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
            margin-top: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .bank-qr {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
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

        .print-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-print {
            padding: 10px 20px;
            background: #16a34a;
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

        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                <div class="title">INVOICE</div>
                <div class="tin">TIN: {{ $tin ?? 'C0015786307' }}</div>
            </div>
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
            </div>
        </div>

        {{-- Date --}}
        <div class="date-row">
            <div>{{ \Carbon\Carbon::parse($first->Date)->format('M d, Y') }}</div>
            <div class="receipt">- {{ $receiptNo }}</div>
        </div>

        {{-- From / To --}}
        <div class="from-to">
            <div>
                <div class="label">From:</div>
                <div class="value">
                    {{ $company?->InstName }}<br>
                    {{ $company?->Address ?? 'P.O.Box CT3635' }}<br>
                    {{ $company?->TelNo ?? '0242 - 947 228 / 0201 - 382 199' }}
                </div>
            </div>
            <div>
                <div class="label">To:</div>
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
        </div>

        {{-- Charges table --}}
        @php
            $totalFee = $entries->sum('Amount');
            $totalTax = $entries->sum(fn($e) => $e->GetFundNHIL + $e->VAT);
            $subtotalFee = $totalFee;

            // Build tax label
            $taxLabel = collect($taxComponents)
                ->filter(fn($t) => $t['applies_on'] === 'base')
                ->map(fn($t) => $t['name'] . '(' . $t['rate'] . '%)')
                ->implode('+');
            $vatRate = collect($taxComponents)->firstWhere('name', 'VAT')['rate'] ?? 15;

            $getFundNHILTotal = $entries->sum('GetFundNHIL');
            $vatTotal = $entries->sum('VAT');
            $subTotal = $totalFee + $getFundNHILTotal;
            $grandTotal = $subTotal + $vatTotal;
        @endphp

        <table>
            <thead>
                <tr>
                    <th style="text-align: center;">Description</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ $entry->AccountName }}</td>
                        <td>{{ number_format($entry->Amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>Total</td>
                    <td>{{ number_format($totalFee, 2) }}</td>
                </tr>
                <tr>
                    <td>{{ $taxLabel }}</td>
                    <td>{{ number_format($getFundNHILTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Sub Total</td>
                    <td>{{ number_format($subTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>VAT({{ $vatRate }}%)</td>
                    <td>{{ number_format($vatTotal, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount Payable</td>
                    <td>{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Issued By --}}
        <div class="issued">
            <div>ISSUED BY:</div>
            <div class="name">{{ strtoupper($first->Username) }}</div>
        </div>

        {{-- Bank + QR --}}
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

    </div>

    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">🖨 Download PDF</button>
        <button class="btn-print" onclick="window.close()" style="background: #6b7280;">✕ Close</button>
    </div>
</body>

</html>
