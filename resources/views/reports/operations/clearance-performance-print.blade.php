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

        /* ── Summary strip ── */
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

        .stat-sub {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-amber .stat-val {
            color: #b45309;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        .stat-gray .stat-val {
            color: #374151;
        }

        /* ── Section header ── */
        .section-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #185FA5;
            padding: 8px 0 6px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 6px;
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

        /* ── Days colour coding ── */
        .days-fresh {
            color: #15803d;
        }

        .days-warning {
            color: #b45309;
            font-weight: 700;
        }

        .days-critical {
            color: #c2410c;
            font-weight: 700;
        }

        .days-overdue {
            color: #b91c1c;
            font-weight: 700;
        }

        /* ── Performance indicator pill ── */
        .perf-pill {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .perf-good {
            background: #dcfce7;
            color: #166534;
        }

        .perf-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .perf-critical {
            background: #fed7aa;
            color: #9a3412;
        }

        .perf-poor {
            background: #fecaca;
            color: #991b1b;
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
            <p class="rpt-meta-row">ETA Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Carrier &nbsp;<span class="rpt-meta-val">{{ $carrierName }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Overall summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Consignments</div>
            <div class="stat-val">{{ $overallSummary['total'] }}</div>
            <div class="stat-sub">{{ $overallSummary['cleared'] }} cleared · {{ $overallSummary['pending'] }} pending
            </div>
        </div>
        <div
            class="stat-card {{ $overallSummary['avg_days'] <= 7 ? 'stat-green' : ($overallSummary['avg_days'] <= 14 ? 'stat-amber' : 'stat-red') }}">
            <div class="stat-label">Avg Days to Clear</div>
            <div class="stat-val">{{ $overallSummary['avg_days'] }}</div>
            <div class="stat-sub">overall average</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Fastest (days)</div>
            <div class="stat-val">{{ $overallSummary['fastest'] }}</div>
            <div class="stat-sub">best clearance</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Slowest (days)</div>
            <div class="stat-val">{{ $overallSummary['slowest'] }}</div>
            <div class="stat-sub">worst clearance</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SECTION 1 — CARRIER PERFORMANCE SUMMARY
    ══════════════════════════════════════════════════════════ --}}
    <p class="section-header">Carrier Performance Summary</p>

    <table>
        <thead>
            <tr>
                <th style="width:25%">Carrier</th>
                <th class="r" style="width:10%">Total</th>
                <th class="r" style="width:13%">Avg Days</th>
                <th class="r" style="width:13%">Fastest</th>
                <th class="r" style="width:13%">Slowest</th>
                <th class="r" style="width:13%">Cleared</th>
                <th class="r" style="width:13%">Pending</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carrierSummary as $c)
                @php
                    $avgDays = (float) $c->AvgDays;
                    $perfCls =
                        $avgDays <= 7
                            ? 'perf-good'
                            : ($avgDays <= 14
                                ? 'perf-warning'
                                : ($avgDays <= 30
                                    ? 'perf-critical'
                                    : 'perf-poor'));
                    $daysCls =
                        $avgDays <= 7
                            ? 'days-fresh'
                            : ($avgDays <= 14
                                ? 'days-warning'
                                : ($avgDays <= 30
                                    ? 'days-critical'
                                    : 'days-overdue'));
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $c->CarrierName }}</td>
                    <td class="r">{{ $c->Total }}</td>
                    <td class="r">
                        <span class="{{ $daysCls }}">{{ $c->AvgDays }}</span>
                        &nbsp;
                        <span class="perf-pill {{ $perfCls }}">
                            {{ $avgDays <= 7 ? 'Good' : ($avgDays <= 14 ? 'Fair' : ($avgDays <= 30 ? 'Slow' : 'Poor')) }}
                        </span>
                    </td>
                    <td class="r" style="color:#15803d; font-weight:700;">{{ $c->FastestDays }}</td>
                    <td class="r" style="color:#b91c1c; font-weight:700;">{{ $c->SlowestDays }}</td>
                    <td class="r" style="color:#15803d;">{{ $c->Cleared }}</td>
                    <td class="r" style="color:#b45309;">{{ $c->Pending }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No data found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══════════════════════════════════════════════════════════
         SECTION 2 — INDIVIDUAL CONSIGNMENT DETAIL
    ══════════════════════════════════════════════════════════ --}}
    <p class="section-header">Individual Consignment Detail</p>

    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:16%">Main BL</th>
                <th style="width:11%">ETA</th>
                <th style="width:26%">Consignee</th>
                <th style="width:16%">Carrier</th>
                <th class="r" style="width:12%">Days to Clear</th>
                <th style="width:15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $days = (int) $row->DaysToClear;
                    $daysCls =
                        $days <= 7
                            ? 'days-fresh'
                            : ($days <= 14
                                ? 'days-warning'
                                : ($days <= 30
                                    ? 'days-critical'
                                    : 'days-overdue'));

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
                    <td>{{ $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '-' }}</td>
                    <td>{{ $row->ConsigneeName ?? '-' }}</td>
                    <td>{{ $row->CarrierName ?? '-' }}</td>
                    <td class="r {{ $daysCls }}">{{ $days }}</td>
                    <td><span class="badge {{ $st['cls'] }}">{{ $st['label'] }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No consignments found for the selected ETA period.
                    </td>
                </tr>
            @endforelse
        </tbody>
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
            window.location.href = '{{ route('reports.operations.clearance-performance.export') }}?' + params
            .toString();
        };
    </script>

</body>

</html>
