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
            margin-bottom: 16px;
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

        /* Vision strip */
        .vision-strip {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            padding: 12px 16px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .vision-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #185FA5;
            font-weight: 700;
            margin-right: 4px;
        }

        .vision-val {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .vision-sub {
            font-size: 10px;
            color: #6b7280;
            margin-left: 4px;
        }

        .progress-wrap {
            flex: 1;
            min-width: 200px;
            background: #e5e7eb;
            border-radius: 99px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar {
            height: 8px;
            border-radius: 99px;
        }

        /* Summary strip */
        .summary-strip {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
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

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        /* Table */
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
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
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

        .section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #185FA5;
            padding: 8px 0 6px;
            border-bottom: 1px solid #e5e7eb;
            margin: 12px 0 8px;
        }

        .bal-pos {
            color: #15803d;
            font-weight: 700;
        }

        .bal-neg {
            color: #b91c1c;
            font-weight: 700;
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
            <p class="rpt-meta-row">As At &nbsp;<span class="rpt-meta-val">{{ $asAtFormatted }}</span></p>
            <p class="rpt-meta-row">Branch &nbsp;<span
                    class="rpt-meta-val">{{ $branchID === 'ALL' ? 'All Branches' : $branchID }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- Vision 5:29 strip --}}
    @if (!empty($vision))
        @php
            $pct = min(100, $vision['progress_pct']);
            $barColor = $pct >= 75 ? '#15803d' : ($pct >= 40 ? '#d97706' : '#185FA5');
        @endphp
        <div class="vision-strip">
            <div>
                <span class="vision-label">{{ $vision['target']->TargetName }}</span>
                <span class="vision-val">GH₵ {{ number_format($vision['target']->TargetAmount, 0) }}</span>
                <span class="vision-sub">by {{ $vision['target']->TargetYear }}</span>
            </div>
            <div>
                <span class="vision-label">Cumulative Surplus</span>
                <span class="vision-val"
                    style="color:{{ $vision['cumulative_surplus'] >= 0 ? '#15803d' : '#b91c1c' }}">
                    GH₵ {{ number_format($vision['cumulative_surplus'], 2) }}
                </span>
            </div>
            <div>
                <span class="vision-label">YTD Surplus</span>
                <span class="vision-val">GH₵ {{ number_format($vision['ytd_surplus'], 2) }}</span>
            </div>
            <div>
                <span class="vision-label">Required/yr</span>
                <span class="vision-val">GH₵ {{ number_format($vision['required_annual'], 0) }}</span>
            </div>
            <div style="flex:1; min-width:160px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                    <span style="font-size:10px; color:#185FA5; font-weight:700;">Progress</span>
                    <span style="font-size:10px; font-weight:700; color:#185FA5;">{{ $vision['progress_pct'] }}%</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $barColor }};">
                    </div>
                </div>
                <p style="font-size:9px; color:#6b7280; margin-top:2px;">
                    {{ $vision['years_remaining'] }} year(s) remaining —
                    <span style="color:{{ $vision['on_track'] ? '#15803d' : '#b91c1c' }}; font-weight:700;">
                        {{ $vision['on_track'] ? 'On Track' : 'Behind Target' }}
                    </span>
                </p>
            </div>
        </div>
    @endif

    {{-- Summary ─────────────────────────────────────────────────────────── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Accounts</div>
            <div class="stat-val">{{ count($rows) }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Debit</div>
            <div class="stat-val" style="font-size:14px;">GH₵ {{ number_format($totalDr, 2) }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Credit</div>
            <div class="stat-val" style="font-size:14px;">GH₵ {{ number_format($totalCr, 2) }}</div>
        </div>
        <div class="stat-card {{ abs($totalDr - $totalCr) < 0.01 ? 'stat-green' : 'stat-red' }}">
            <div class="stat-label">Books Balance</div>
            <div class="stat-val">{{ abs($totalDr - $totalCr) < 0.01 ? '✓ Yes' : '✗ No' }}</div>
        </div>
    </div>

    {{-- Table by account type --}}
    @foreach (['GL' => 'GL Accounts', 'INCOME' => 'Income Accounts', 'EXPENDITURE' => 'Expenditure Accounts'] as $type => $label)
        @php $section = $rows->where('Type', $type); @endphp
        @if ($section->count() > 0)
            <p class="section-label">{{ $label }}</p>
            <table>
                <thead>
                    <tr>
                        <th style="width:10%">Account No</th>
                        <th style="width:36%">Account Name</th>
                        <th style="width:8%">Class</th>
                        <th class="r" style="width:15%">Total Dr (GH₵)</th>
                        <th class="r" style="width:15%">Total Cr (GH₵)</th>
                        <th class="r" style="width:16%">Balance (GH₵)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($section as $row)
                        <tr>
                            <td style="font-family:monospace">{{ $row->AccountID }}</td>
                            <td>{{ $row->AccountName }}</td>
                            <td>{{ $row->Class }}</td>
                            <td class="r">{{ number_format($row->TotalDr, 2) }}</td>
                            <td class="r">{{ number_format($row->TotalCr, 2) }}</td>
                            <td class="r {{ $row->Balance >= 0 ? 'bal-pos' : 'bal-neg' }}">
                                {{ number_format($row->Balance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Section Total</td>
                        <td class="r">GH₵ {{ number_format($section->sum('TotalDr'), 2) }}</td>
                        <td class="r">GH₵ {{ number_format($section->sum('TotalCr'), 2) }}</td>
                        <td class="r">GH₵ {{ number_format($section->sum('Balance'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
    @endforeach

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.trial-balance.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
