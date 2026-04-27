<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
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

        /* ── Report header ── */
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

        /* ── Summary strips ── */
        .summary-strip-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-strip {
            display: flex;
            gap: 10px;
            margin: 12px 0 8px;
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

        /* Status strip */
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

        /* Overdue bracket strip */
        .stat-fresh {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .stat-fresh .stat-val {
            color: #15803d;
        }

        .stat-warning {
            border-color: #fde68a;
            background: #fffbeb;
        }

        .stat-warning .stat-val {
            color: #b45309;
        }

        .stat-critical {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .stat-critical .stat-val {
            color: #c2410c;
        }

        .stat-overdue {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .stat-overdue .stat-val {
            color: #b91c1c;
        }

        /* ── Table ── */
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

        /* ── Status badges ── */
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

        /* ── Days overdue colour coding ── */
        .age-fresh {
            color: #15803d;
        }

        .age-warning {
            color: #b45309;
        }

        .age-critical {
            color: #c2410c;
            font-weight: 700;
        }

        .age-overdue {
            color: #b91c1c;
            font-weight: 700;
        }

        /* ── Consignee + phone combined cell ── */
        .consignee-name {
            font-weight: 600;
        }

        .consignee-tel {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Footer ── */
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

            .no-print {
                display: none;
            }

            body {
                padding: 12px;
            }

            body {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    {{-- ── Action bar ── --}}
    <div class="action-bar">
        <button class="btn-export" onclick="window.exportReport()">Export to Excel</button>
        <button class="btn-print" onclick="window.print()">Print</button>
    </div>

    {{-- ── Report header ── --}}
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
            <p class="rpt-title">{{ $reportTitle }}</p>
            <p class="rpt-meta-row">ETA Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Summary strip 1: Status ── --}}
    <p class="summary-strip-label" style="margin-top:14px;">By Status</p>
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

    {{-- ── Summary strip 2: Overdue brackets ── --}}
    <p class="summary-strip-label">By Days Overdue (from ETA)</p>
    <div class="summary-strip" style="margin-bottom:16px;">
        <div class="stat-card stat-fresh">
            <div class="stat-label">1 – 7 Days</div>
            <div class="stat-val">{{ $overdueSummary['one_to_seven'] }}</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-label">8 – 14 Days</div>
            <div class="stat-val">{{ $overdueSummary['eight_to_fourteen'] }}</div>
        </div>
        <div class="stat-card stat-critical">
            <div class="stat-label">15 – 30 Days</div>
            <div class="stat-val">{{ $overdueSummary['fifteen_to_thirty'] }}</div>
        </div>
        <div class="stat-card stat-overdue">
            <div class="stat-label">30+ Days</div>
            <div class="stat-val">{{ $overdueSummary['over_thirty'] }}</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Total</div>
            <div class="stat-val">{{ $overdueSummary['total'] }}</div>
        </div>
    </div>

    {{-- ── Main table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:12%">Main BL</th>
                <th style="width:10%">ETA</th>
                <th class="r" style="width:7%">Days Overdue</th>
                <th style="width:18%">Consignee</th>
                <th style="width:12%">Carrier</th>
                <th style="width:14%">Container No(s).</th>
                <th style="width:10%">Commodity</th>
                <th style="width:7%">Status</th>
                <th class="r" style="width:7%">Date Reg.</th>
                <th class="no-print" style="width:4%"></th>
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
                    $days = (int) $row->DaysOverdue;
                    $bracket =
                        $days <= 7
                            ? 'age-fresh'
                            : ($days <= 14
                                ? 'age-warning'
                                : ($days <= 30
                                    ? 'age-critical'
                                    : 'age-overdue'));
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '-' }}</td>
                    <td>{{ $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '-' }}</td>
                    <td class="r {{ $bracket }}">{{ $days }}</td>
                    <td>
                        {{-- Combined consignee name + phone ── --}}
                        <div class="consignee-name">{{ $row->ConsigneeName ?? '-' }}</div>
                        @if ($row->ConsigneeTel)
                            <div class="consignee-tel">{{ $row->ConsigneeTel }}</div>
                        @endif
                    </td>
                    <td>{{ $row->CarrierName ?? '-' }}</td>
                    <td style="font-family:monospace">{{ $row->ContainerNos ?? '-' }}</td>
                    <td>{{ $row->CommodityType ?? '-' }}</td>
                    <td><span class="badge {{ $st['cls'] }}">{{ $st['label'] }}</span></td>
                    <td class="r" style="color:#6b7280">
                        {{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}
                    </td>
                    <td class="no-print" style="text-align:center;">
                        <button onclick="window.openConsignmentModal({{ $row->ConsignmentID }})" title="View Details"
                            style="background:none; border:1px solid #e5e7eb; border-radius:6px; padding:4px 8px; cursor:pointer; font-size:13px; color:#185FA5;">
                            👁
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No records found for the selected ETA period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Footer ── --}}
    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.operations.pending-clearance.export') }}?' + params.toString();
        };
    </script>

    @include('reports.operations._consignment-detail-modal')

</body>

</html>
