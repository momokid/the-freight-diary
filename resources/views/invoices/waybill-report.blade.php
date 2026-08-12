<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Waybill #{{ $waybill->id }}</title>
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
            max-width: 750px;
            margin: 0 auto;
        }

        .header {
            border: 1px solid #000;
            display: flex;
            align-items: stretch;
            margin-bottom: 20px;
        }

        .header-left {
            padding: 12px;
            border-right: 1px solid #000;
            display: flex;
            align-items: center;
        }

        .header-left img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-center {
            padding: 12px;
            flex: 1;
            text-align: center;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-info {
            font-size: 10px;
            margin-top: 4px;
            line-height: 1.5;
        }

        .header-right {
            padding: 12px;
            border-left: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
        }

        .waybill-title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .info-item {
            display: flex;
            gap: 6px;
        }

        .info-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.05em;
        }

        .info-value {
            border-bottom: 1px dotted #000;
            min-width: 120px;
            padding-bottom: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        th {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #000;
            padding: 40px 12px;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 10px;
        }

        .signature-item {
            display: flex;
            gap: 6px;
            align-items: flex-end;
        }

        .signature-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            white-space: nowrap;
        }

        .signature-line {
            border-bottom: 1px dotted #000;
            min-width: 150px;
        }

        .thank-you {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.1em;
            margin-top: 24px;
            text-transform: uppercase;
        }

        .print-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
        }

        .btn-print {
            padding: 8px 18px;
            background: #2d6a3f;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
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

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
        </div>
        <div class="header-center">
            <div class="company-name">{{ $company?->InstName }}</div>
            <div class="company-info">
                @if ($company?->Address)
                    {{ $company->Address }}<br>
                @endif
                @if ($company?->Email)
                    {{ $company->Email }}<br>
                @endif
                @if ($company?->TelNo)
                    {{ $company->TelNo }}
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="waybill-title">Waybill</div>
        </div>
    </div>

    {{-- Info rows --}}
    <div class="info-row">
        <div class="info-item">
            <span class="info-label">Consignee:</span>
            <span class="info-value">{{ strtoupper($waybill->Consignee) }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($waybill->WaybillDate)->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="info-row">
        <div class="info-item">
            <span class="info-label">Vehicle No.:</span>
            <span class="info-value">{{ $waybill->VehicleNo }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Driver's Name:</span>
            <span class="info-value">{{ strtoupper($waybill->DriverName) }}</span>
        </div>
    </div>

    <div class="info-row">
        <div class="info-item">
            <span class="info-label">Port:</span>
            <span class="info-value">{{ strtoupper($waybill->Port) }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Driver's License No.:</span>
            <span class="info-value">{{ $waybill->DriverLicense }}</span>
        </div>
    </div>

    {{-- Package table --}}
    <table>
        <thead>
            <tr>
                <th>Package</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody
            style="border: 1px solid #000; padding: 10px; text-align: center; font-size: 11px; vertical-align: middle; height: 40vh;">
            <tr>
                <td>{{ $waybill->Package }}</td>
                <td>{{ $waybill->Description }}</td>
                <td>{{ $waybill->Quantity }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Signature lines --}}
    <div class="signature-row">
        <div class="signature-item">
            <span class="signature-label">Received By:</span>
            <span
                class="signature-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="signature-item">
            <span class="signature-label">Prepared By:</span>
            <span
                class="signature-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>

    <div class="signature-row" style="margin-top: 16px;">
        <div class="signature-item">
            <span class="signature-label">Driver's Signature:</span>
            <span
                class="signature-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="signature-item">
            <span class="signature-label">Signature:</span>
            <span
                class="signature-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>

    <div class="thank-you">Thank You</div>

    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">Print View</button>
    </div>

</body>

</html>
