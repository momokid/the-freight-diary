<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* ── Benchmark cards ── */
        .benchmark-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 16px 0;
        }

        .bm-card {
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .bm-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .bm-val {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .bm-avg .bm-val {
            color: #185FA5;
        }

        .bm-min .bm-val {
            color: #15803d;
        }

        .bm-max .bm-val {
            color: #b91c1c;
        }

        .bm-prof .bm-val {
            color: #5b21b6;
        }

        .bm-cnt .bm-val {
            color: #374151;
        }

        /* ── Filter strip ── */
        .filter-strip {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .filter-pill {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 99px;
            background: #eff6ff;
            color: #185FA5;
            border: 1px solid #bfdbfe;
        }

        /* ── Chart ── */
        .chart-wrap {
            margin: 16px 0;
        }

        /* ── Table ── */
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

        /* ── vs Avg indicator ── */
        .vs-above {
            color: #b91c1c;
            font-weight: 700;
        }

        .vs-below {
            color: #15803d;
            font-weight: 700;
        }

        .vs-avg {
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
            @if ($ItemDetails)
                <p class="rpt-meta-row">Item Type &nbsp;<span class="rpt-meta-val">{{ $ItemDetails }}</span></p>
            @endif
            @if ($containerSize)
                <p class="rpt-meta-row">Container Size &nbsp;<span class="rpt-meta-val">{{ $containerSize }}</span></p>
            @endif
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Active filters strip ── --}}
    @if ($ItemDetails || $containerSize)
        <div class="filter-strip" style="margin-top:12px;">
            <span style="font-size:10px; color:#6b7280; align-self:center;">Filtered by:</span>
            @if ($ItemDetails)
                <span class="filter-pill">Item: {{ $ItemDetails }}</span>
            @endif
            @if ($containerSize)
                <span class="filter-pill">Size: {{ $containerSize }}</span>
            @endif
        </div>
    @endif

    {{-- ── Benchmark cards ── --}}
    <div class="benchmark-grid">
        <div class="bm-card bm-cnt">
            <div class="bm-label">Consignments</div>
            <div class="bm-val">{{ $benchmarks['total'] }}</div>
        </div>
        <div class="bm-card bm-avg">
            <div class="bm-label">Avg Expenditure</div>
            <div class="bm-val">GH₵ {{ number_format($benchmarks['avg_expenditure'], 2) }}</div>
        </div>
        <div class="bm-card bm-min">
            <div class="bm-label">Min Expenditure</div>
            <div class="bm-val">GH₵ {{ number_format($benchmarks['min_expenditure'], 2) }}</div>
        </div>
        <div class="bm-card bm-max">
            <div class="bm-label">Max Expenditure</div>
            <div class="bm-val">GH₵ {{ number_format($benchmarks['max_expenditure'], 2) }}</div>
        </div>
        <div class="bm-card bm-prof">
            <div class="bm-label">Avg Net Profit</div>
            <div class="bm-val">GH₵ {{ number_format($benchmarks['avg_net_profit'], 2) }}</div>
        </div>
    </div>

    {{-- ── Chart: Expenditure vs Average ── --}}
    @if ($rows->count() > 1)
        <div class="chart-wrap">
            <p
                style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6b7280; margin-bottom:8px;">
                Expenditure per Consignment vs Average
            </p>
            <canvas id="cmp_chart" height="60"></canvas>
        </div>
    @endif

    {{-- ── Main table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:14%">Main BL</th>
                <th style="width:20%">Consignee</th>
                <th style="width:14%">Item Description</th>
                <th style="width:10%">Size</th>
                <th class="r" style="width:12%">Revenue</th>
                <th class="r" style="width:12%">Expenditure</th>
                <th class="r" style="width:10%">Net Profit</th>
                <th class="r" style="width:10%">vs Avg</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $pnlCls =
                        $row->NetProfit > 0 ? 'pnl-positive' : ($row->NetProfit < 0 ? 'pnl-negative' : 'pnl-zero');

                    $vsAvg =
                        $benchmarks['avg_expenditure'] > 0
                            ? round(
                                (($row->TotalExpenditure - $benchmarks['avg_expenditure']) /
                                    $benchmarks['avg_expenditure']) *
                                    100,
                                1,
                            )
                            : 0;

                    $vsCls = $vsAvg > 10 ? 'vs-above' : ($vsAvg < -10 ? 'vs-below' : 'vs-avg');
                    $vsLbl = ($vsAvg > 0 ? '+' : '') . $vsAvg . '%';
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                    <td>{{ $row->ConsigneeName ?? '—' }}</td>
                    <td>{{ $row->ItemDescription ?? '—' }}</td>
                    <td>{{ $row->ContainerSize ?? '—' }}</td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($row->TotalRevenue, 2) }}
                    </td>
                    <td class="r" style="color:#b91c1c;">
                        GH₵ {{ number_format($row->TotalExpenditure, 2) }}
                    </td>
                    <td class="r {{ $pnlCls }}">
                        GH₵ {{ number_format($row->NetProfit, 2) }}
                    </td>
                    <td class="r {{ $vsCls }}">{{ $vsLbl }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No matching consignments found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5">BENCHMARKS</td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($rows->sum('TotalRevenue'), 2) }}
                    </td>
                    <td class="r" style="color:#b91c1c;">
                        Avg: GH₵ {{ number_format($benchmarks['avg_expenditure'], 2) }}
                    </td>
                    <td class="r {{ $benchmarks['avg_net_profit'] >= 0 ? 'pnl-positive' : 'pnl-negative' }}">
                        Avg: GH₵ {{ number_format($benchmarks['avg_net_profit'], 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ── Management insight ── --}}
    @if ($rows->count() >= 3)
        @php
            $aboveAvg = $rows
                ->filter(
                    fn($r) => $benchmarks['avg_expenditure'] > 0 &&
                        (($r->TotalExpenditure - $benchmarks['avg_expenditure']) / $benchmarks['avg_expenditure']) *
                            100 >
                            10,
                )
                ->count();
            $belowAvg = $rows
                ->filter(
                    fn($r) => $benchmarks['avg_expenditure'] > 0 &&
                        (($r->TotalExpenditure - $benchmarks['avg_expenditure']) / $benchmarks['avg_expenditure']) *
                            100 <
                            -10,
                )
                ->count();
        @endphp
        <div
            style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 16px; margin-top:4px;">
            <p style="font-size:11px; font-weight:700; color:#92400e; margin-bottom:4px;">
                Management Insight
            </p>
            <p style="font-size:11px; color:#78350f; line-height:1.7;">
                Of {{ $rows->count() }} similar consignments,
                <strong>{{ $aboveAvg }}</strong> spent more than 10% above average
                @if ($aboveAvg > 0)
                    (investigate for cost overruns)
                @endif
                and <strong>{{ $belowAvg }}</strong> spent more than 10% below average
                @if ($belowAvg > 0)
                    (use as cost benchmarks)
                @endif.
                Average expenditure: <strong>GH₵ {{ number_format($benchmarks['avg_expenditure'], 2) }}</strong>.
            </p>
        </div>
    @endif

    <div class="rpt-footer" style="margin-top:16px;">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.disbursement.comparative.export') }}?' + params.toString();
        };

        // ── Chart: Expenditure per BL vs Average line ───────────────────────
        (function() {
            const rows = @json(
                $rows->map(fn($r) => [
                        'bl' => $r->MainBL,
                        'exp' => $r->TotalExpenditure,
                    ]));
            const avg = {{ $benchmarks['avg_expenditure'] }};

            if (!rows.length || rows.length < 2) return;

            new Chart(document.getElementById('cmp_chart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: rows.map(r => r.bl),
                    datasets: [{
                            label: 'Expenditure (GH₵)',
                            data: rows.map(r => r.exp),
                            backgroundColor: rows.map(r =>
                                r.exp > avg * 1.1 ? 'rgba(185,28,28,0.7)' // red if >10% above avg
                                :
                                r.exp < avg * 0.9 ? 'rgba(21,128,61,0.7)' // green if >10% below
                                :
                                'rgba(24,95,165,0.7)' // blue = near avg
                            ),
                            borderRadius: 4,
                        },
                        {
                            label: 'Average (GH₵)',
                            data: rows.map(() => avg),
                            type: 'line',
                            borderColor: '#d97706',
                            borderWidth: 2,
                            borderDash: [6, 3],
                            pointRadius: 0,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    animation: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': GH₵ ' +
                                    ctx.parsed.y.toLocaleString('en-GH', {
                                        minimumFractionDigits: 2
                                    })
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => 'GH₵ ' + v.toLocaleString()
                            }
                        }
                    }
                }
            });
        })();
    </script>

</body>

</html>
