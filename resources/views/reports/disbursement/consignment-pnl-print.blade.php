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

        .stat-red .stat-val {
            color: #b91c1c;
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

        .section-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #185FA5;
            padding: 8px 0 6px;
            border-bottom: 1px solid #e5e7eb;
            margin: 16px 0 10px;
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

        .lcl-summary td {
            background: #eff6ff !important;
            font-weight: 700;
        }

        .lcl-hbl td {
            background: #f8faff !important;
            color: #6b7280;
            font-size: 12px;
        }

        .lcl-hbl td:first-child {
            padding-left: 24px;
        }

        .type-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .type-fcl {
            background: #dbeafe;
            color: #1e40af;
        }

        .type-lcl {
            background: #ede9fe;
            color: #5b21b6;
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
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-val">GH₵ {{ number_format($summary['total_revenue'], 2) }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Total Expenditure</div>
            <div class="stat-val">GH₵ {{ number_format($summary['total_expenditure'], 2) }}</div>
        </div>
        <div class="stat-card {{ $summary['net_profit'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
            <div class="stat-label">Net Profit</div>
            <div class="stat-val">GH₵ {{ number_format($summary['net_profit'], 2) }}</div>
            <div class="stat-sub">Margin: {{ $summary['margin'] }}%</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Profitable</div>
            <div class="stat-val">{{ $summary['profitable'] }}</div>
            <div class="stat-sub">consignments</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Loss Making</div>
            <div class="stat-val">{{ $summary['loss_making'] }}</div>
            <div class="stat-sub">consignments</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Total</div>
            <div class="stat-val">{{ $summary['total'] }}</div>
        </div>
    </div>

    {{-- ── Disclaimer ── --}}
    <div
        style="background:#fffbeb; border:1px solid #fde68a; border-radius:6px;
                padding:8px 14px; margin-bottom:14px; font-size:10px; color:#78350f;">
        <strong>Note:</strong> Revenue figures reflect amounts recorded via the
        Consignment Revenue feature only. Consignments with no revenue entry show
        GH₵ 0.00 — this indicates revenue has not yet been recorded, not that no
        revenue was earned.
    </div>


    {{-- ── FCL Consignments ── --}}
    @if (count($data['fcl']) > 0)
        <p class="section-header">FCL Consignments</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:16%">Main BL</th>
                    <th style="width:22%">Consignee</th>
                    <th style="width:12%">Container Size</th>
                    <th style="width:14%">Item Type</th>
                    <th class="r" style="width:11%">Revenue</th>
                    <th class="r" style="width:11%">Expenditure</th>
                    <th class="r" style="width:10%">Net Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['fcl'] as $i => $row)
                    @php
                        $pnlCls =
                            $row->NetProfit > 0 ? 'pnl-positive' : ($row->NetProfit < 0 ? 'pnl-negative' : 'pnl-zero');
                    @endphp
                    <tr>
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                        <td>{{ $row->ConsigneeName ?? '—' }}</td>
                        <td>{{ $row->ContainerSize ?? '—' }}</td>
                        <td>{{ $row->ItemDescription ?? '—' }}</td>
                        <td class="r">
                            @if ($row->TotalRevenue > 0)
                                <span style="color:#185FA5;">GH₵ {{ number_format($row->TotalRevenue, 2) }}</span>
                            @else
                                <span style="color:#9ca3af; font-size:10px;">Not recorded</span>
                            @endif
                        </td>
                        <td class="r" style="color:#b91c1c">GH₵ {{ number_format($row->TotalExpenditure, 2) }}
                        </td>
                        <td class="r {{ $pnlCls }}">GH₵ {{ number_format($row->NetProfit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── LCL Consignments ── --}}
    @if (count($data['lcl']) > 0)
        <p class="section-header">LCL Consignments</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:14%">Main BL</th>
                    <th style="width:14%">House BL</th>
                    <th style="width:20%">Consignee</th>
                    <th style="width:10%">Container Size</th>
                    <th class="r" style="width:12%">Revenue</th>
                    <th class="r" style="width:12%">Expenditure</th>
                    <th class="r" style="width:14%">Net Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['lcl'] as $i => $lclRow)
                    @php
                        $pnlCls =
                            $lclRow->NetProfit > 0
                                ? 'pnl-positive'
                                : ($lclRow->NetProfit < 0
                                    ? 'pnl-negative'
                                    : 'pnl-zero');
                    @endphp
                    {{-- LCL Summary row ── --}}
                    <tr class="lcl-summary">
                        <td>{{ $i + 1 }}</td>
                        <td style="font-family:monospace">{{ $lclRow->MainBL ?? '—' }}</td>
                        <td style="color:#6b7280">LCL Summary</td>
                        <td>{{ $lclRow->ConsigneeName }}</td>
                        <td>{{ $lclRow->ContainerSize ?? '—' }}</td>
                        <td class="r" style="color:#185FA5">GH₵ {{ number_format($lclRow->TotalRevenue, 2) }}
                        </td>
                        <td class="r" style="color:#b91c1c">GH₵ {{ number_format($lclRow->TotalExpenditure, 2) }}
                        </td>
                        <td class="r {{ $pnlCls }}">GH₵ {{ number_format($lclRow->NetProfit, 2) }}</td>
                    </tr>
                    {{-- HBL breakdown rows ── --}}
                    @foreach ($lclRow->hblRows as $hbl)
                        @php
                            $hblPnlCls =
                                $hbl->NetProfit > 0
                                    ? 'pnl-positive'
                                    : ($hbl->NetProfit < 0
                                        ? 'pnl-negative'
                                        : 'pnl-zero');
                        @endphp
                        <tr class="lcl-hbl">
                            <td></td>
                            <td style="font-family:monospace; padding-left:20px;">
                                └ {{ $hbl->MainBL ?? '—' }}
                            </td>
                            <td style="font-family:monospace">{{ $hbl->HBL ?? '—' }}</td>
                            <td>{{ $hbl->ConsigneeName ?? '—' }}</td>
                            <td></td>
                            <td class="r" style="color:#185FA5">GH₵ {{ number_format($hbl->TotalRevenue, 2) }}
                            </td>
                            <td class="r" style="color:#b91c1c">GH₵ {{ number_format($hbl->TotalExpenditure, 2) }}
                            </td>
                            <td class="r {{ $hblPnlCls }}">GH₵ {{ number_format($hbl->NetProfit, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Grand totals ── --}}
    @if (count($data['all']) > 0)
        <table>
            <tfoot>
                <tr>
                    <td colspan="5" style="font-weight:700;">PERIOD TOTALS</td>
                    <td class="r" style="color:#185FA5">
                        GH₵ {{ number_format($summary['total_revenue'], 2) }}
                    </td>
                    <td class="r" style="color:#b91c1c">
                        GH₵ {{ number_format($summary['total_expenditure'], 2) }}
                    </td>
                    <td class="r {{ $summary['net_profit'] >= 0 ? 'pnl-positive' : 'pnl-negative' }}">
                        GH₵ {{ number_format($summary['net_profit'], 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.disbursement.consignment-pnl.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
