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
        }

        .rpt-company-name {
            font-size: 14px;
            font-weight: 700;
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
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .v-item {
            display: flex;
            flex-direction: column;
        }

        .v-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #185FA5;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .v-val {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .progress-wrap {
            flex: 1;
            min-width: 180px;
        }

        /* IE Statement */
        .ie-section {
            margin-bottom: 20px;
        }

        .ie-section-header {
            background: #185FA5;
            color: #fff;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 6px 6px 0 0;
        }

        .ie-section-income {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .ie-section-expense {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        tbody td {
            padding: 7px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        tbody td.r {
            text-align: right;
        }

        tbody tr:nth-child(even) td {
            background: rgba(0, 0, 0, 0.02);
        }

        .total-row td {
            font-weight: 700;
            font-size: 13px;
            padding: 10px 12px;
            border-top: 2px solid #185FA5;
            background: #f9fafb;
        }

        .total-row td.r {
            text-align: right;
        }

        /* Net surplus box */
        .net-box {
            margin-top: 16px;
            padding: 16px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .net-surplus {
            background: #f0fdf4;
            border: 2px solid #15803d;
        }

        .net-deficit {
            background: #fef2f2;
            border: 2px solid #b91c1c;
        }

        .net-label {
            font-size: 13px;
            font-weight: 700;
        }

        .net-val {
            font-size: 22px;
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
                <p class="rpt-company-sub">{{ $company?->Address ?? '' }}<br>
                    @if ($company?->TelNo)
                        Tel: {{ $company->TelNo }}
                    @endif
                </p>
            </div>
        </div>
        <div class="rpt-meta-block">
            <p class="rpt-title">{{ $reportTitle }}</p>
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Branch &nbsp;<span
                    class="rpt-meta-val">{{ $branchID === 'ALL' ? 'All Branches' : $branchID }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- Vision 5:29 ──────────────────────────────────────────────────────── --}}
    @if (!empty($vision))
        @php
            $pct = min(100, $vision['progress_pct']);
            $barColor = $pct >= 75 ? '#15803d' : ($pct >= 40 ? '#d97706' : '#185FA5');
        @endphp
        <div class="vision-strip">
            <div class="v-item">
                <span class="v-label">{{ $vision['target']->TargetName }}</span>
                <span class="v-val">GH₵ {{ number_format($vision['target']->TargetAmount, 0) }} by
                    {{ $vision['target']->TargetYear }}</span>
            </div>
            <div class="v-item">
                <span class="v-label">Cumulative Surplus</span>
                <span class="v-val" style="color:{{ $vision['cumulative_surplus'] >= 0 ? '#15803d' : '#b91c1c' }}">GH₵
                    {{ number_format($vision['cumulative_surplus'], 2) }}</span>
            </div>
            <div class="v-item">
                <span class="v-label">YTD Surplus</span>
                <span class="v-val">GH₵ {{ number_format($vision['ytd_surplus'], 2) }}</span>
            </div>
            <div class="v-item">
                <span class="v-label">Required Annual</span>
                <span class="v-val">GH₵ {{ number_format($vision['required_annual'], 0) }}</span>
            </div>
            <div class="progress-wrap">
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:10px;color:#185FA5;font-weight:700;">{{ $vision['progress_pct'] }}% of
                        target</span>
                    <span
                        style="font-size:10px;font-weight:700;color:{{ $vision['on_track'] ? '#15803d' : '#b91c1c' }}">{{ $vision['on_track'] ? 'On Track' : 'Behind' }}</span>
                </div>
                <div style="background:#e5e7eb;border-radius:99px;height:8px;overflow:hidden;">
                    <div
                        style="height:8px;border-radius:99px;background:{{ $barColor }};width:{{ $pct }}%;">
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Income ──────────────────────────────────────────────────────────── --}}
    <div class="ie-section">
        <div class="ie-section-header" style="background:#15803d;">INCOME</div>
        <table>
            <tbody>
                @forelse($data['income'] as $row)
                    <tr>
                        <td style="width:12%; font-family:monospace; color:#6b7280;">{{ $row->AccountNo }}</td>
                        <td>{{ $row->AccountName }}</td>
                        <td class="r" style="width:20%; color:#15803d; font-weight:700;">
                            GH₵ {{ number_format($row->TotalCr, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding:1rem;color:#9ca3af;text-align:center;">No income records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="total-row"
            style="display:flex;justify-content:space-between;padding:10px 12px;background:#f0fdf4;border-top:2px solid #15803d;">
            <span style="font-weight:700;font-size:13px;">Total Income</span>
            <span style="font-weight:700;font-size:14px;color:#15803d;">GH₵
                {{ number_format($data['totalIncome'], 2) }}</span>
        </div>
    </div>

    {{-- Expenditure ─────────────────────────────────────────────────────── --}}
    <div class="ie-section">
        <div class="ie-section-header" style="background:#b91c1c;">EXPENDITURE</div>
        <table>
            <tbody>
                @forelse($data['expenditure'] as $row)
                    <tr>
                        <td style="width:12%; font-family:monospace; color:#6b7280;">{{ $row->AccountNo }}</td>
                        <td>{{ $row->AccountName }}</td>
                        <td class="r" style="width:20%; color:#b91c1c; font-weight:700;">
                            GH₵ {{ number_format($row->TotalDr, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding:1rem;color:#9ca3af;text-align:center;">No expenditure records.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div
            style="display:flex;justify-content:space-between;padding:10px 12px;background:#fef2f2;border-top:2px solid #b91c1c;">
            <span style="font-weight:700;font-size:13px;">Total Expenditure</span>
            <span style="font-weight:700;font-size:14px;color:#b91c1c;">GH₵
                {{ number_format($data['totalExpenditure'], 2) }}</span>
        </div>
    </div>

    {{-- Net Surplus / Deficit ───────────────────────────────────────────── --}}
    <div class="net-box {{ $data['netSurplus'] >= 0 ? 'net-surplus' : 'net-deficit' }}">
        <span class="net-label" style="color:{{ $data['netSurplus'] >= 0 ? '#15803d' : '#b91c1c' }}">
            {{ $data['netSurplus'] >= 0 ? 'NET SURPLUS' : 'NET DEFICIT' }}
        </span>
        <span class="net-val" style="color:{{ $data['netSurplus'] >= 0 ? '#15803d' : '#b91c1c' }}">
            GH₵ {{ number_format(abs($data['netSurplus']), 2) }}
        </span>
    </div>

    <div class="rpt-footer" style="margin-top:12px;">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.income-expenditure.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
