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
            font-size: 18px;
            font-weight: 700;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-gray .stat-val {
            color: #374151;
        }

        .stat-profit {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .stat-profit .stat-val {
            color: #15803d;
        }

        .stat-loss {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .stat-loss .stat-val {
            color: #b91c1c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 16px;
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
            font-size: 13px;
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

        .pnl-positive {
            color: #15803d;
            font-weight: 700;
        }

        .pnl-negative {
            color: #b91c1c;
            font-weight: 700;
        }

        .pnl-zero {
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

    {{-- ── Summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-gray">
            <div class="stat-label">Total Entries</div>
            <div class="stat-val">{{ count($rows) }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-val">GH₵ {{ number_format($totals['revenue'], 2) }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Total Expenditure</div>
            <div class="stat-val">GH₵ {{ number_format($totals['expenditure'], 2) }}</div>
        </div>
        <div class="stat-card {{ $totals['net_profit'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
            <div class="stat-label">Net Profit</div>
            <div class="stat-val">GH₵ {{ number_format($totals['net_profit'], 2) }}</div>
        </div>
    </div>

    {{-- ── Detail table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:10%">Date</th>
                <th style="width:13%">Main BL</th>
                <th style="width:10%">HBL</th>
                <th style="width:18%">Consignee</th>
                <th style="width:18%">Account</th>
                <th style="width:12%">Receipt No</th>
                <th class="r" style="width:7%">Exp.</th>
                <th class="r" style="width:7%">Rev.</th>
                <th class="r" style="width:7%">Net</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $net = round($row->Revenue - $row->Expenditure, 2);
                    $netCls = $net > 0 ? 'pnl-positive' : ($net < 0 ? 'pnl-negative' : 'pnl-zero');
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="color:#6b7280">
                        {{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}
                    </td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                    <td style="font-family:monospace; color:#6b7280">{{ $row->HBL ?? '—' }}</td>
                    <td>{{ $row->ConsigneeName ?? '—' }}</td>
                    <td>{{ $row->AccountName ?? '—' }}</td>
                    <td style="font-family:monospace; color:#6b7280">{{ $row->ReceiptNo ?? '—' }}</td>
                    <td class="r" style="color:#b91c1c;">
                        {{ number_format($row->Expenditure, 2) }}
                    </td>
                    <td class="r" style="color:#185FA5;">
                        {{ number_format($row->Revenue, 2) }}
                    </td>
                    <td class="r {{ $netCls }}">
                        {{ number_format($net, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No disbursement records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align:right;">TOTALS</td>
                    <td class="r" style="color:#b91c1c;">
                        {{ number_format($totals['expenditure'], 2) }}
                    </td>
                    <td class="r" style="color:#185FA5;">
                        {{ number_format($totals['revenue'], 2) }}
                    </td>
                    <td class="r {{ $totals['net_profit'] >= 0 ? 'pnl-positive' : 'pnl-negative' }}">
                        {{ number_format($totals['net_profit'], 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.disbursement.detail.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
