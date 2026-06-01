<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Collections — As at {{ date('d M Y', strtotime($asAt)) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
            background: #fff;
            padding: 20px;
        }

        .action-bar {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-bottom: 16px;
        }

        .btn-action {
            padding: 6px 16px;
            border: 1px solid #185FA5;
            border-radius: 4px;
            background: #fff;
            color: #185FA5;
            cursor: pointer;
            font-size: 11px;
        }

        .btn-action:hover {
            background: #185FA5;
            color: #fff;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-block img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #185FA5;
            margin-bottom: 3px;
        }

        .company-addr {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }

        .report-meta {
            text-align: right;
        }

        .report-title {
            font-size: 15px;
            font-weight: bold;
            color: #185FA5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .report-sub {
            font-size: 10px;
            color: #888;
            line-height: 1.6;
        }

        .blue-bar {
            height: 3px;
            background: #185FA5;
            margin-bottom: 14px;
        }

        /* Summary strip */
        .summary-strip {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .summary-card {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .summary-card.total {
            background: #E6F1FB;
        }

        .summary-card.c30 {
            background: #EAF3DE;
        }

        .summary-card.c60 {
            background: #FAEEDA;
        }

        .summary-card.c90 {
            background: #FEE9D1;
        }

        .summary-card.c90p {
            background: #FCEBEB;
        }

        .summary-num {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .summary-num.total {
            color: #0C447C;
        }

        .summary-num.c30 {
            color: #3B6D11;
        }

        .summary-num.c60 {
            color: #854F0B;
        }

        .summary-num.c90 {
            color: #b45309;
        }

        .summary-num.c90p {
            color: #A32D2D;
        }

        .summary-lbl {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #185FA5;
            color: #fff;
        }

        thead th {
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        thead th.right {
            text-align: right;
        }

        tbody tr {
            border-bottom: 0.5px solid #eef0f2;
        }

        tbody tr:nth-child(even) {
            background: #f9fbfd;
        }

        tbody td {
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: middle;
        }

        tbody td.right {
            text-align: right;
            font-family: monospace;
            font-size: 10px;
        }

        tbody td.mono {
            font-family: monospace;
        }

        /* Stamp badge */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge.bl {
            background: #E6F1FB;
            color: #0C447C;
        }

        .badge.nonbl {
            background: #f0f0f0;
            color: #555;
        }

        /* Days badge */
        .days-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .days-badge.c30 {
            background: #EAF3DE;
            color: #3B6D11;
        }

        .days-badge.c60 {
            background: #FAEEDA;
            color: #854F0B;
        }

        .days-badge.c90 {
            background: #FEE9D1;
            color: #b45309;
        }

        .days-badge.c90p {
            background: #FCEBEB;
            color: #A32D2D;
        }

        /* Consignee group separator */
        .group-start td {
            border-top: 1.5px solid #d0dff0;
        }

        /* Footer */
        .report-footer {
            border-top: 0.5px solid #ddd;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
        }

        .footer-text {
            font-size: 9px;
            color: #bbb;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #aaa;
            font-size: 12px;
        }

        @media print {
            .action-bar {
                display: none !important;
            }

            body {
                padding: 12px;
            }

            @page {
                margin: 1cm;
            }
        }
    </style>
</head>

<body>

    {{-- Action buttons --}}
    <div class="action-bar">
        <button class="btn-action" onclick="window.print()">&#128438; Print</button>
    </div>

    {{-- Report header --}}
    <div class="report-header">
        <div class="logo-block">
            <img src="{{ asset('images/logo.png') }}" alt="PSIL Logo">
            <div>
                <div class="company-name">{{ $company->CompanyName ?? '' }}</div>
                <div class="company-addr">
                    {{ $company->Address ?? '' }}<br>
                    Tel: {{ $company->Phone ?? '' }} &nbsp;|&nbsp; {{ $company->Email ?? '' }}
                </div>
            </div>
        </div>
        <div class="report-meta">
            <div class="report-title">Outstanding Collections</div>
            <div class="report-sub">
                As at: {{ date('d M Y', strtotime($asAt)) }}<br>
                Generated: {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; By: {{ $user->Username }}
            </div>
        </div>
    </div>
    <div class="blue-bar"></div>

    {{-- Summary strip --}}
    <div class="summary-strip">
        <div class="summary-card total">
            <div class="summary-num total">GHS {{ number_format($summary->total ?? 0, 2) }}</div>
            <div class="summary-lbl">Total Outstanding</div>
        </div>
        <div class="summary-card c30">
            <div class="summary-num c30">GHS {{ number_format($summary->bucket_30 ?? 0, 2) }}</div>
            <div class="summary-lbl">0 – 30 Days</div>
        </div>
        <div class="summary-card c60">
            <div class="summary-num c60">GHS {{ number_format($summary->bucket_60 ?? 0, 2) }}</div>
            <div class="summary-lbl">31 – 60 Days</div>
        </div>
        <div class="summary-card c90">
            <div class="summary-num c90">GHS {{ number_format($summary->bucket_90 ?? 0, 2) }}</div>
            <div class="summary-lbl">61 – 90 Days</div>
        </div>
        <div class="summary-card c90p">
            <div class="summary-num c90p">GHS {{ number_format($summary->bucket_90plus ?? 0, 2) }}</div>
            <div class="summary-lbl">90+ Days</div>
        </div>
    </div>

    {{-- Table --}}
    @if (count($rows) > 0)
        @php
            $prevConsignee = null;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Consignee</th>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Invoice Date</th>
                    <th>Days</th>
                    <th class="right">Outstanding (GHS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        $days = $row->DaysOutstanding ?? 0;
                        $dayClass = $days <= 30 ? 'c30' : ($days <= 60 ? 'c60' : ($days <= 90 ? 'c90' : 'c90p'));
                        $isNewGroup = $row->ConsigneeName !== $prevConsignee;
                        $prevConsignee = $row->ConsigneeName;
                    @endphp
                    <tr class="{{ $isNewGroup && !$loop->first ? 'group-start' : '' }}">
                        <td>{{ $row->ConsigneeName }}</td>
                        <td class="mono">{{ $row->Reference }}</td>
                        <td>
                            @if ($row->Stamp === 'BL')
                                <span class="badge bl">BL</span>
                            @else
                                <span class="badge nonbl">Non-BL</span>
                            @endif
                        </td>
                        <td>{{ $row->InvoiceDate ? date('d M Y', strtotime($row->InvoiceDate)) : '—' }}</td>
                        <td>
                            <span class="days-badge {{ $dayClass }}">{{ $days }} days</span>
                        </td>
                        <td class="right">{{ number_format($row->Outstanding, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            No outstanding collections as at {{ date('d M Y', strtotime($asAt)) }}.
        </div>
    @endif

    {{-- Footer --}}
    <div class="report-footer">
        <span class="footer-text">Confidential — for management use only</span>
        <span class="footer-text">
            Printed by {{ $user->Username }} &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }}
        </span>
    </div>

</body>

</html>
