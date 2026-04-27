<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignment Detail Report</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
            background: #ffffff;
            padding: 24px;
        }

        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: flex-end;
        }

        .btn-print {
            padding: 8px 20px;
            background: #185FA5;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-export {
            padding: 8px 20px;
            background: #15803d;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .rpt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 14px;
            border-bottom: 3px solid #185FA5;
        }

        .rpt-logo-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .rpt-logo {
            height: 56px;
            width: auto;
            object-fit: contain;
        }

        .rpt-company-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
        }

        .rpt-company-sub {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.7;
        }

        .rpt-meta-block {
            text-align: right;
        }

        .rpt-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .rpt-meta-row {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.9;
        }

        .rpt-meta-val {
            font-weight: 600;
            color: #111827;
        }

        .summary-strip {
            display: flex;
            gap: 10px;
            margin: 16px 0;
        }

        .stat-card {
            flex: 1;
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
        }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stat-val {
            font-size: 20px;
            font-weight: 700;
        }

        .stat-amber .stat-val {
            color: #b45309;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-gray .stat-val {
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            background: #185FA5;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        thead th.r {
            text-align: right;
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
            vertical-align: middle;
            font-size: 14px;
        }

        tbody td.r {
            text-align: right;
        }

        tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .badge-notarrived {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-pending {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-gatedout {
            background: #dcfce7;
            color: #166534;
        }

        .badge-cleared {
            background: #f3f4f6;
            color: #374151;
        }

        .hbl-count {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 99px;
            background: #ede9fe;
            color: #5b21b6;
        }

        .age-warn {
            color: #b91c1c;
            font-weight: 700;
        }

        .age-ok {
            color: #6b7280;
        }

        .rpt-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
        }

        @media print {
            .action-bar {
                display: none;
            }

            body {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar">
        <button class="btn-export" onclick="window.exportReport()">Export to Excel</button>
        <button class="btn-print" onclick="window.print()">Print</button>
    </div>

    <div class="rpt-header">
        <div class="rpt-logo-block">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $company?->InstName ?? 'PSIL' }}" class="rpt-logo"
                onerror="this.style.display='none'">
            <div>
                <p class="rpt-company-name">{{ $company?->InstName ?? 'Prime Survivors International Ltd' }}</p>
                <p class="rpt-company-sub">
                    {{ $company?->Address ?? '' }}<br>
                    @if ($company?->TelNo)
                        Tel: {{ $company->TelNo }}
                    @endif
                    @if ($company?->Email)
                        &nbsp;·&nbsp; {{ $company->Email }}
                    @endif
                </p>
            </div>
        </div>
        <div class="rpt-meta-block">
            <p class="rpt-title">Consignment Detail Report</p>
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    <div class="summary-strip">
        <div class="stat-card stat-amber">
            <div class="stat-label">Not Arrived</div>
            <div class="stat-val">{{ $summary['not_arrived'] }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Pending</div>
            <div class="stat-val">{{ $summary['pending'] }}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Gated Out</div>
            <div class="stat-val">{{ $summary['gated_out'] }}</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Cleared</div>
            <div class="stat-val">{{ $summary['cleared'] }}</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Total</div>
            <div class="stat-val">{{ $summary['total'] }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:12%">Main BL</th>
                <th style="width:14%">Vessel / Voyage</th>
                <th style="width:10%">Carrier</th>
                <th style="width:12%">Shipper</th>
                <th style="width:12%">Consignee / HBLs</th>
                <th style="width:8%">POL</th>
                <th style="width:12%">Container No(s).</th>
                <th style="width:5%">Type</th>
                <th style="width:7%">Status</th>
                <th class="r" style="width:5%">Age</th>
                <th class="r" style="width:10%">Date Reg.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $statusMap = [
                        0 => ['label' => 'Cleared', 'cls' => 'badge-cleared'],
                        1 => ['label' => 'Not Arrived', 'cls' => 'badge-notarrived'],
                        2 => ['label' => 'Pending', 'cls' => 'badge-pending'],
                        3 => ['label' => 'Gated Out', 'cls' => 'badge-gatedout'],
                    ];
                    $st = $statusMap[$row->Status] ?? ['label' => '-', 'cls' => ''];
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '-' }}</td>
                    <td>{{ $row->VesselName ?? '-' }}<br>
                        <span style="color:#6b7280; font-size:11px;">{{ $row->VoyageNo ?? '' }}</span>
                    </td>
                    <td>{{ $row->CarrierName ?? '-' }}</td>
                    <td>{{ $row->ShipperName ?? '-' }}</td>
                    <td>
                        @if ($row->CmdtTypeID == 1)
                            <span class="hbl-count">{{ $row->HBLCount }}
                                HBL{{ $row->HBLCount != 1 ? 's' : '' }}</span>
                        @else
                            {{ $row->ConsigneeName ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $row->POL_Name ?? '-' }}</td>
                    <td style="font-family:monospace">{{ $row->ContainerNos ?? '-' }}</td>
                    <td>{{ $row->CmdtTypeID == 1 ? 'LCL' : 'FCL' }}</td>
                    <td><span class="badge {{ $st['cls'] }}">{{ $st['label'] }}</span></td>
                    <td class="r {{ $row->Status != 0 && $row->AgeDays > 7 ? 'age-warn' : 'age-ok' }}">
                        {{ $row->AgeDays }}
                    </td>
                    <td class="r" style="color:#6b7280">
                        {{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.operations.consignment-detail.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
