<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Manifest — {{ $consignment->MainBL }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        /* ── Header ── */
        .header {
            border: 2px solid #000;
            padding: 12px;
            margin-bottom: 12px;
            text-align: center;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .header-top img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-top: 8px;
        }

        /* ── Consignment Info ── */
        .consignment-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
            margin-bottom: 12px;
            font-size: 11px;
        }

        .consignment-info .row {
            display: flex;
            gap: 8px;
        }

        .consignment-info .label {
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11px;
        }

        thead th {
            background: #1a3d26;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        tbody td {
            padding: 8px 10px;
            border: 1px solid #999;
            vertical-align: top;
        }

        tbody tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .house-bl {
            font-weight: bold;
            font-size: 11px;
        }

        .consignee-name {
            font-weight: 500;
        }

        .weight-col {
            text-align: right;
            font-weight: 500;
        }

        /* ── Footer ── */
        .footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .sign-line {
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 4px;
            width: 220px;
            text-align: center;
        }

        /* ── Summary row ── */
        .summary-row td {
            font-weight: bold;
            background: #f0f0f0 !important;
            border-top: 2px solid #000;
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
            background: #1a3d26;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-print:hover { opacity: 0.85; }

        @media print {
            .print-bar { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

    {{-- ── Company Header ── --}}
    <div class="header-top">
    <img src="{{ asset('images/logo.png') }}" alt="Logo"
        onerror="this.style.display='none'"
        style="width: 70px; height: 70px; object-fit: contain;">
    <div>
        <div class="company-name">{{ $company?->InstName ?? '' }}</div>
        <div class="company-details">
            @if($company?->TelNo) 📞 {{ $company->TelNo }} &nbsp;&nbsp; @endif
            @if($company?->Email) ✉ {{ $company->Email }} &nbsp;&nbsp; @endif
            @if($company?->Address) ⚲ {{ $company->Address }} @endif
        </div>
    </div>
</div>

    {{-- ── Consignment Details ── --}}
    <div class="consignment-info">
        <div class="row">
            <span class="label">Vessel:</span>
            <span>{{ $consignment->VesselName }}</span>
        </div>
        <div class="row">
            <span class="label">ETA:</span>
            <span>{{ \Carbon\Carbon::parse($consignment->ETA)->format('M d, Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Bill of Lading:</span>
            <span>{{ $consignment->MainBL }}</span>
        </div>
        <div class="row">
            <span class="label">Container No & Size:</span>
            <span>
                @foreach($containers as $c)
                    ({{ $c->ContainerNo }}/{{ $c->ContainerSize }}ft)@if(!$loop->last), @endif
                @endforeach
            </span>
        </div>
        <div class="row">
            <span class="label">P.O.L.:</span>
            <span><strong>{{ $consignment->POL_Name }}</strong></span>
        </div>
        <div class="row">
            <span class="label">P.O.D.:</span>
            <span><strong>{{ $consignment->POD_Name }}</strong></span>
        </div>
    </div>

    {{-- ── Manifest Table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width: 100px;">House BL#</th>
                <th>Consignee</th>
                <th style="width: 80px;">Pkgs</th>
                <th>Description of Goods</th>
                <th style="width: 110px; text-align: right;">
                    Weight (KGS)<br>
                    <span style="font-weight: normal; font-size: 10px;">{{ $consignment->MainBL }}</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td class="house-bl">{{ $entry->HouseBL }}</td>
                <td>
                    <div class="consignee-name">{{ $entry->consignee?->FullName ?? '—' }}</div>
                    @if($entry->consignee?->Address1)
                        <div style="color: #555; font-size: 10px;">{{ $entry->consignee->Address1 }}</div>
                    @endif
                    @if($entry->consignee?->Address2)
                        <div style="color: #555; font-size: 10px;">{{ $entry->consignee->Address2 }}</div>
                    @endif
                    @if($entry->consignee?->Address3)
                        <div style="color: #555; font-size: 10px;">{{ $entry->consignee->Address3 }}</div>
                    @endif
                </td>
                <td>
                    {{ $entry->Package }} {{ $entry->Unit }}
                </td>
                <td>
                    <div>{{ $entry->Description }}</div>
                    @if($entry->VIN)
                        <div style="font-size: 10px; color: #333; margin-top: 2px;">{{ $entry->VIN }}</div>
                    @endif
                    @if($entry->OtherInfo)
                        <div style="font-size: 10px; color: #555; margin-top: 2px;">{{ $entry->OtherInfo }}</div>
                    @endif
                </td>
                <td class="weight-col">{{ number_format($entry->Weight, 2) }}</td>
            </tr>
            @endforeach

            {{-- Summary row --}}
            <tr class="summary-row">
                <td colspan="4" style="text-align: right; padding-right: 12px;">TOTAL WEIGHT</td>
                <td class="weight-col">{{ number_format($entries->sum('Weight'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Signature ── --}}
    <div class="footer">
        <div class="sign-line">SIGN...................................</div>
    </div>

    {{-- ── Print/Download Button ── --}}
    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">
            🖨 Download PDF
        </button>
        <button class="btn-print" onclick="window.close()" style="background: #6b7280;">
            ✕ Close
        </button>
    </div>

</body>
</html>