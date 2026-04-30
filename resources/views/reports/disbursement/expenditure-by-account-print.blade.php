<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            background: #fff;
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
            color: #111827;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-red .stat-val {
            color: #b91c1c;
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
            letter-spacing: 0.04em;
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

        tfoot td {
            padding: 8px 10px;
            font-weight: 700;
            font-size: 13px;
            background: #f3f4f6;
            border-top: 2px solid #185FA5;
        }

        tfoot td.r {
            text-align: right;
        }

        /* ── Progress bar ── */
        .progress-wrap {
            background: #e5e7eb;
            border-radius: 99px;
            height: 6px;
            width: 100%;
            overflow: hidden;
        }

        .progress-bar {
            height: 6px;
            border-radius: 99px;
            background: #185FA5;
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
            <p class="rpt-title">{{ $reportTitle }}</p>
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-red">
            <div class="stat-label">Total Expenditure</div>
            <div class="stat-val">GH₵ {{ number_format($totalExp, 2) }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Expense Categories</div>
            <div class="stat-val">{{ count($rows) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Highest Spend</div>
            <div class="stat-val" style="font-size:13px; color:#b91c1c;">
                {{ $rows->first()?->AccountName ?? '—' }}
            </div>
            <div style="font-size:10px; color:#6b7280; margin-top:2px;">
                GH₵ {{ number_format($rows->first()?->TotalExpenditure ?? 0, 2) }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Lowest Spend</div>
            <div class="stat-val" style="font-size:13px; color:#15803d;">
                {{ $rows->last()?->AccountName ?? '—' }}
            </div>
            <div style="font-size:10px; color:#6b7280; margin-top:2px;">
                GH₵ {{ number_format($rows->last()?->TotalExpenditure ?? 0, 2) }}
            </div>
        </div>
    </div>

    {{-- ── Main table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:30%">Account / Expense Category</th>
                <th class="r" style="width:10%">Consignments</th>
                <th class="r" style="width:16%">Total Expenditure</th>
                <th class="r" style="width:14%">Total Revenue</th>
                <th class="r" style="width:14%">Avg per Entry</th>
                <th style="width:12%">% of Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $pct = $totalExp > 0 ? round(($row->TotalExpenditure / $totalExp) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $row->AccountName ?? '—' }}</td>
                    <td class="r">{{ $row->ConsignmentCount }}</td>
                    <td class="r" style="color:#b91c1c; font-weight:700;">
                        GH₵ {{ number_format($row->TotalExpenditure, 2) }}
                    </td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($row->TotalRevenue, 2) }}
                    </td>
                    <td class="r" style="color:#6b7280;">
                        GH₵ {{ number_format($row->AvgPerEntry, 2) }}
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div class="progress-wrap" style="flex:1;">
                                <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                            </div>
                            <span style="font-size:10px; color:#6b7280; min-width:32px;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No disbursement data found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">TOTAL</td>
                    <td class="r" style="color:#b91c1c;">
                        GH₵ {{ number_format($totalExp, 2) }}
                    </td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($rows->sum('TotalRevenue'), 2) }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
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
            window.location.href = '{{ route('reports.disbursement.expenditure-by-account.export') }}?' + params
                .toString();
        };
    </script>

</body>

</html>
