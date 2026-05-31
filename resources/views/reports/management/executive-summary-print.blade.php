<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
</div>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Summary — {{ date('d M Y', strtotime($dateFrom)) }} to {{ date('d M Y', strtotime($dateTo)) }}
    </title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
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

        .section {
            margin-bottom: 14px;
        }

        .section-label {
            font-size: 10px;
            font-weight: bold;
            color: #185FA5;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding-bottom: 5px;
            border-bottom: 0.5px solid #d0dff0;
            margin-bottom: 10px;
        }

        /* Pipeline KPIs */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .kpi-card {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .kpi-card.amber {
            background: #FAEEDA;
        }

        .kpi-card.blue {
            background: #E6F1FB;
        }

        .kpi-card.green {
            background: #EAF3DE;
        }

        .kpi-card.gray {
            background: #F1EFE8;
        }

        .kpi-card.neutral {
            background: #f5f5f5;
        }

        .kpi-num {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .kpi-num.amber {
            color: #854F0B;
        }

        .kpi-num.blue {
            color: #0C447C;
        }

        .kpi-num.green {
            color: #3B6D11;
        }

        .kpi-num.gray {
            color: #5F5E5A;
        }

        .kpi-num.neutral {
            color: #333;
        }

        .kpi-lbl {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Financial section */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 12px;
        }

        .fin-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .fin-card {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .fin-card.revenue {
            background: #EAF3DE;
        }

        .fin-card.expenditure {
            background: #FCEBEB;
        }

        .fin-card.net {
            background: #E6F1FB;
        }

        .fin-num {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .fin-num.revenue {
            color: #3B6D11;
        }

        .fin-num.expenditure {
            color: #A32D2D;
        }

        .fin-num.surplus {
            color: #3B6D11;
        }

        .fin-num.deficit {
            color: #A32D2D;
        }

        .fin-lbl {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .bar-wrap {
            height: 100px;
            position: relative;
        }

        .chart-legend {
            display: flex;
            gap: 12px;
            margin-top: 5px;
        }

        .legend-swatch {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        /* Donut + legend */
        .donut-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .donut-legend {
            margin-top: 8px;
            width: 100%;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 9px;
            color: #555;
            margin-bottom: 4px;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Cash position */
        .cash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 8px;
        }

        .cash-card {
            border: 0.5px solid #dce8f5;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .cash-name {
            font-size: 10px;
            color: #888;
            margin-bottom: 4px;
        }

        .cash-bal {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        /* Vision strip */
        .vision-strip {
            background: #f0f5fb;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .vision-title {
            font-size: 10px;
            font-weight: bold;
            color: #185FA5;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
        }

        .vision-row {
            display: grid;
            grid-template-columns: 160px 1fr 50px;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .vision-name {
            font-size: 10px;
            color: #444;
        }

        .progress-track {
            height: 8px;
            background: #dce8f5;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }

        .progress-fill.green {
            background: #639922;
        }

        .progress-fill.amber {
            background: #EF9F27;
        }

        .progress-fill.red {
            background: #E24B4A;
        }

        .vision-pct {
            font-size: 10px;
            font-weight: bold;
            text-align: right;
        }

        .vision-pct.green {
            color: #3B6D11;
        }

        .vision-pct.amber {
            color: #854F0B;
        }

        .vision-pct.red {
            color: #A32D2D;
        }

        .vision-meta {
            font-size: 9px;
            color: #888;
            margin-top: 6px;
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
            <div class="report-title">Executive Summary</div>
            <div class="report-sub">
                Period: {{ date('d M Y', strtotime($dateFrom)) }} – {{ date('d M Y', strtotime($dateTo)) }}<br>
                Generated: {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; By: {{ $user->Username }}
            </div>
        </div>
    </div>
    <div class="blue-bar"></div>

    {{-- Section 1: Consignment Pipeline --}}
    <div class="section">
        <div class="section-label">Consignment Pipeline</div>
        <div class="kpi-grid">
            <div class="kpi-card amber">
                <div class="kpi-num amber">{{ number_format($pipeline->not_arrived ?? 0) }}</div>
                <div class="kpi-lbl">Not Arrived</div>
            </div>
            <div class="kpi-card blue">
                <div class="kpi-num blue">{{ number_format($pipeline->pending ?? 0) }}</div>
                <div class="kpi-lbl">Pending</div>
            </div>
            <div class="kpi-card green">
                <div class="kpi-num green">{{ number_format($pipeline->gated_out ?? 0) }}</div>
                <div class="kpi-lbl">Gated Out</div>
            </div>
            <div class="kpi-card gray">
                <div class="kpi-num gray">{{ number_format($pipeline->cleared ?? 0) }}</div>
                <div class="kpi-lbl">Cleared</div>
            </div>
            <div class="kpi-card neutral">
                <div class="kpi-num neutral">{{ number_format($pipeline->total ?? 0) }}</div>
                <div class="kpi-lbl">Total Active</div>
            </div>
        </div>
    </div>

    {{-- Section 2: Financial Performance + Donut --}}
    <div class="section">
        <div class="section-label">Financial Performance — Selected Period</div>
        <div class="two-col">

            <div>
                <div class="fin-grid">
                    <div class="fin-card revenue">
                        <div class="fin-num revenue">GHS {{ number_format($revenue->total ?? 0, 2) }}</div>
                        <div class="fin-lbl">Revenue</div>
                    </div>
                    <div class="fin-card expenditure">
                        <div class="fin-num expenditure">GHS {{ number_format($expenditure->total ?? 0, 2) }}</div>
                        <div class="fin-lbl">Expenditure</div>
                    </div>
                    <div class="fin-card net">
                        @if ($netProfit >= 0)
                            <div class="fin-num surplus">GHS {{ number_format($netProfit, 2) }}</div>
                            <div class="fin-lbl">Net Surplus</div>
                        @else
                            <div class="fin-num deficit">GHS {{ number_format(abs($netProfit), 2) }}</div>
                            <div class="fin-lbl">Net Deficit</div>
                        @endif
                    </div>
                </div>
                <div class="bar-wrap">
                    <canvas id="barChart"></canvas>
                </div>
                <div class="chart-legend">
                    <div style="display:flex;align-items:center;gap:4px;">
                        <div class="legend-swatch" style="background:#C0DD97;"></div>
                        <span style="font-size:9px;color:#666;">Revenue</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <div class="legend-swatch" style="background:#F7C1C1;"></div>
                        <span style="font-size:9px;color:#666;">Expenditure</span>
                    </div>
                </div>
            </div>

            <div class="donut-wrap">
                <canvas id="donutChart" width="120" height="120"></canvas>
                <div class="donut-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#EF9F27;"></div>
                        Not Arrived ({{ $pipeline->not_arrived ?? 0 }})
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#185FA5;"></div>
                        Pending ({{ $pipeline->pending ?? 0 }})
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#639922;"></div>
                        Gated Out ({{ $pipeline->gated_out ?? 0 }})
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#D3D1C7;"></div>
                        Cleared ({{ $pipeline->cleared ?? 0 }})
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Section 3: Cash Position --}}
    <div class="section">
        <div class="section-label">Cash Position — As at {{ date('d M Y', strtotime($dateTo)) }}</div>
        @if (count($cashAccounts) > 0)
            <div class="cash-grid">
                @foreach ($cashAccounts as $acct)
                    <div class="cash-card">
                        <div class="cash-name">{{ $acct->AccountName }}</div>
                        <div class="cash-bal">GHS {{ number_format($acct->balance, 2) }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:10px;color:#aaa;">No active cash/bank accounts found.</p>
        @endif
    </div>

    {{-- Section 4: Vision 5:29 Strip --}}
    @if (!empty($vision))
        <div class="section">
            <div class="vision-strip">
                <div class="vision-title">Vision 5:29 — Progress to Date</div>
                <div class="vision-row">
                    <div class="vision-name">{{ $vision['target']->TargetName }}</div>
                    <div class="progress-track">
                        <div class="progress-fill {{ $vision['rag'] }}"
                            style="width: {{ min($vision['progress_pct'], 100) }}%;"></div>
                    </div>
                    <div class="vision-pct {{ $vision['rag'] }}">{{ $vision['progress_pct'] }}%</div>
                </div>
                <div class="vision-meta">
                    Cumulative surplus: GHS {{ number_format($vision['cumulative'], 2) }}
                    &nbsp;|&nbsp;
                    Target: GHS {{ number_format($vision['target']->TargetAmount, 2) }}
                    &nbsp;|&nbsp;
                    Target year: {{ $vision['target']->TargetYear }}
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="report-footer">
        <span class="footer-text">Confidential — for management use only</span>
        <span class="footer-text">
            Printed by {{ $user->Username }} &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }}
        </span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const chartRows = @json($chartRows);

            // Bar chart — weekly revenue vs expenditure
            const barLabels = chartRows.map(function(r) {
                const p = r.week_start.split('-');
                return new Date(p[0], p[1] - 1, p[2])
                    .toLocaleDateString('en-GB', {
                        month: 'short',
                        day: 'numeric'
                    });
            });

            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: barLabels.length ? barLabels : ['No data'],
                    datasets: [{
                            label: 'Revenue',
                            data: chartRows.map(r => parseFloat(r.revenue) || 0),
                            backgroundColor: '#C0DD97',
                            borderRadius: 3,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Expenditure',
                            data: chartRows.map(r => parseFloat(r.expenditure) || 0),
                            backgroundColor: '#F7C1C1',
                            borderRadius: 3,
                            barPercentage: 0.6
                        }
                    ]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    size: 9
                                },
                                callback: v => 'GHS ' + v.toLocaleString()
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });

            // Donut chart — pipeline breakdown
            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Not Arrived', 'Pending', 'Gated Out', 'Cleared'],
                    datasets: [{
                        data: [
                            {{ $pipeline->not_arrived ?? 0 }},
                            {{ $pipeline->pending ?? 0 }},
                            {{ $pipeline->gated_out ?? 0 }},
                            {{ $pipeline->cleared ?? 0 }}
                        ],
                        backgroundColor: ['#EF9F27', '#185FA5', '#639922', '#D3D1C7'],
                        borderWidth: 0
                    }]
                },
                options: {
                    animation: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            });

        });
    </script>
</body>

</html>
