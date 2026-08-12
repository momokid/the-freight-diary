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

        /* ── Period summary strip ── */
        .summary-strip {
            display: flex;
            gap: 10px;
            margin: 16px 0 12px;
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
            color: #111827;
        }

        .stat-sub {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-purple .stat-val {
            color: #5b21b6;
        }

        .stat-pink .stat-val {
            color: #9d174d;
        }

        .stat-teal .stat-val {
            color: #0f766e;
        }

        /* ── Best / worst strip ── */
        .insight-strip {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .insight-card {
            flex: 1;
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .insight-green {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .insight-red {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .insight-icon {
            font-size: 18px;
        }

        .insight-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .insight-val {
            font-size: 12px;
            font-weight: 700;
        }

        .insight-green .insight-val {
            color: #15803d;
        }

        .insight-red .insight-val {
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

        /* ── % change colours ── */
        .pct-up {
            color: #15803d;
            font-weight: 700;
        }

        .pct-down {
            color: #b91c1c;
            font-weight: 700;
        }

        .pct-flat {
            color: #6b7280;
        }

        /* ── Totals row ── */
        .totals-row td {
            padding: 10px;
            font-weight: 700;
            font-size: 13px;
            background: #f3f4f6;
            border-top: 2px solid #185FA5;
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
                <p class="rpt-company-name">{{ $company?->InstName }}</p>
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
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Period summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Consignments</div>
            <div class="stat-val">{{ $periodTotals['total'] }}</div>
            <div class="stat-sub">{{ $periodTotals['months_count'] }} month(s)</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-label">Total LCL</div>
            <div class="stat-val">{{ $periodTotals['lcl'] }}</div>
            <div class="stat-sub">
                {{ $periodTotals['total'] > 0 ? round(($periodTotals['lcl'] / $periodTotals['total']) * 100, 1) : 0 }}%
                of total
            </div>
        </div>
        <div class="stat-card stat-pink">
            <div class="stat-label">Total FCL</div>
            <div class="stat-val">{{ $periodTotals['fcl'] }}</div>
            <div class="stat-sub">
                {{ $periodTotals['total'] > 0 ? round(($periodTotals['fcl'] / $periodTotals['total']) * 100, 1) : 0 }}%
                of total
            </div>
        </div>
        <div class="stat-card stat-teal">
            <div class="stat-label">Unique Consignees</div>
            <div class="stat-val">{{ $periodTotals['unique_consignees'] }}</div>
            <div class="stat-sub">active clients</div>
        </div>
    </div>

    {{-- ── Best / worst month insight strip ── --}}
    @if ($rows->count() > 1)
        <div class="insight-strip">
            <div class="insight-card insight-green">
                <div class="insight-icon">🏆</div>
                <div>
                    <div class="insight-label">Best Month</div>
                    <div class="insight-val">
                        {{ $periodTotals['best_month'] }}
                        <span style="font-size:10px; font-weight:400; color:#6b7280;">
                            — {{ $periodTotals['best_month_count'] }} consignments
                        </span>
                    </div>
                </div>
            </div>
            <div class="insight-card insight-red">
                <div class="insight-icon">📉</div>
                <div>
                    <div class="insight-label">Lowest Month</div>
                    <div class="insight-val">
                        {{ $periodTotals['worst_month'] }}
                        <span style="font-size:10px; font-weight:400; color:#6b7280;">
                            — {{ $periodTotals['worst_month_count'] }} consignments
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Monthly breakdown table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:22%">Month</th>
                <th class="r" style="width:15%">Total</th>
                <th class="r" style="width:15%">LCL</th>
                <th class="r" style="width:15%">FCL</th>
                <th class="r" style="width:18%">Unique Consignees</th>
                <th class="r" style="width:15%">% Change</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                @php
                    $pctClass =
                        $row->PctChange === null
                            ? 'pct-flat'
                            : ($row->PctChange > 0
                                ? 'pct-up'
                                : ($row->PctChange < 0
                                    ? 'pct-down'
                                    : 'pct-flat'));

                    $pctLabel =
                        $row->PctChange === null
                            ? '—'
                            : ($row->PctChange > 0
                                ? '+' . $row->PctChange . '%'
                                : $row->PctChange . '%');
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $row->MonthLabel }}</td>
                    <td class="r" style="font-weight:700; color:#185FA5;">{{ $row->Total }}</td>
                    <td class="r" style="color:#5b21b6;">{{ $row->LCL }}</td>
                    <td class="r" style="color:#9d174d;">{{ $row->FCL }}</td>
                    <td class="r" style="color:#0f766e;">{{ $row->UniqueConsignees }}</td>
                    <td class="r {{ $pctClass }}">{{ $pctLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No consignments found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- ── Period totals row ── --}}
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr class="totals-row">
                    <td>PERIOD TOTAL</td>
                    <td class="r" style="color:#185FA5;">{{ $periodTotals['total'] }}</td>
                    <td class="r" style="color:#5b21b6;">{{ $periodTotals['lcl'] }}</td>
                    <td class="r" style="color:#9d174d;">{{ $periodTotals['fcl'] }}</td>
                    <td class="r" style="color:#0f766e;">{{ $periodTotals['unique_consignees'] }}</td>
                    <td class="r" style="color:#6b7280;">{{ $periodTotals['months_count'] }} months</td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ── Footer ── --}}
    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.operations.consignment-volume.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
