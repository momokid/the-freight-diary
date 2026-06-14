<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Performance — {{ $currentLabel }} vs {{ $prevLabel }}</title>
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

        /* ── Action bar ── */
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

        /* ── Report header ── */
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

        /* ── Summary strip ── */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .summary-table thead tr {
            background: #185FA5;
            color: #fff;
        }

        .summary-table thead th {
            padding: 7px 10px;
            font-size: 10px;
            font-weight: 600;
            text-align: right;
        }

        .summary-table thead th:first-child {
            text-align: left;
        }

        .summary-table tbody tr {
            border-bottom: 0.5px solid #eef0f2;
        }

        .summary-table tbody tr:nth-child(even) {
            background: #f9fbfd;
        }

        .summary-table tbody td {
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            text-align: right;
        }

        .summary-table tbody td:first-child {
            text-align: left;
            color: #444;
        }

        .summary-table tfoot tr {
            background: #f0f5fb;
            border-top: 1.5px solid #185FA5;
        }

        .summary-table tfoot td {
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
        }

        .summary-table tfoot td:first-child {
            text-align: left;
        }

        /* ── Section label ── */
        .section-label {
            font-size: 10px;
            font-weight: bold;
            color: #185FA5;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding-bottom: 5px;
            border-bottom: 0.5px solid #d0dff0;
            margin-bottom: 8px;
            margin-top: 14px;
        }

        /* ── Breakdown table ── */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .breakdown-table thead tr {
            background: #185FA5;
            color: #fff;
        }

        .breakdown-table thead th {
            padding: 6px 8px;
            font-size: 10px;
            font-weight: 600;
            text-align: right;
        }

        .breakdown-table thead th:first-child {
            text-align: left;
        }

        .breakdown-table tbody tr {
            border-bottom: 0.5px solid #eef0f2;
        }

        .breakdown-table tbody tr:nth-child(even) {
            background: #f9fbfd;
        }

        .breakdown-table tbody td {
            padding: 5px 8px;
            font-size: 10px;
            text-align: right;
        }

        .breakdown-table tbody td:first-child {
            text-align: left;
            color: #333;
        }

        .breakdown-table tfoot tr {
            background: #f0f5fb;
            border-top: 1.5px solid #185FA5;
        }

        .breakdown-table tfoot td {
            padding: 6px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: right;
        }

        .breakdown-table tfoot td:first-child {
            text-align: left;
        }

        /* ── Variance colours ── */
        /* Revenue: positive = good (green), negative = bad (red) */
        .var-good {
            color: #3B6D11;
            font-weight: 600;
        }

        .var-bad {
            color: #A32D2D;
            font-weight: 600;
        }

        .var-neutral {
            color: #888;
        }

        /* ── Net row ── */
        .net-surplus {
            color: #3B6D11;
            font-weight: bold;
        }

        .net-deficit {
            color: #A32D2D;
            font-weight: bold;
        }

        /* ── Footer ── */
        .report-footer {
            border-top: 0.5px solid #ddd;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
        }

        .footer-text {
            font-size: 9px;
            color: #bbb;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 11px;
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

    {{-- ── Action buttons ── --}}
    <div class="action-bar">
        <button class="btn-action" onclick="window.print()">&#128438; Print</button>
    </div>

    {{-- ── Report header ── --}}
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
            <div class="report-title">Financial Performance</div>
            <div class="report-sub">
                Comparing: {{ $currentLabel }} vs {{ $prevLabel }}<br>
                Generated: {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; By: {{ $user->Username }}
            </div>
        </div>
    </div>
    <div class="blue-bar"></div>

    {{-- ── Summary strip ── --}}
    <table class="summary-table">
        <thead>
            <tr>
                <th style="text-align:left; width:30%;">Summary</th>
                <th>{{ $currentLabel }}</th>
                <th>{{ $prevLabel }}</th>
                <th>Variance (GHS)</th>
                <th>Variance (%)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Revenue row --}}
            @php
                $incomeVarClass = $incomeVarGhs >= 0 ? 'var-good' : 'var-bad';
            @endphp
            <tr>
                <td>Revenue</td>
                <td>{{ number_format($totalCurrIncome, 2) }}</td>
                <td>{{ number_format($totalPrevIncome, 2) }}</td>
                <td class="{{ $incomeVarClass }}">
                    {{ $incomeVarGhs >= 0 ? '+' : '' }}{{ number_format($incomeVarGhs, 2) }}
                </td>
                <td class="{{ $incomeVarClass }}">
                    {{ $incomeVarPct >= 0 ? '+' : '' }}{{ $incomeVarPct }}%
                </td>
            </tr>

            {{-- Expenditure row — reversed colour logic ── --}}
            @php
                $expendVarClass = $expendVarGhs <= 0 ? 'var-good' : 'var-bad';
            @endphp
            <tr>
                <td>Expenditure</td>
                <td>{{ number_format($totalCurrExpend, 2) }}</td>
                <td>{{ number_format($totalPrevExpend, 2) }}</td>
                <td class="{{ $expendVarClass }}">
                    {{ $expendVarGhs >= 0 ? '+' : '' }}{{ number_format($expendVarGhs, 2) }}
                </td>
                <td class="{{ $expendVarClass }}">
                    {{ $expendVarPct >= 0 ? '+' : '' }}{{ $expendVarPct }}%
                </td>
            </tr>
        </tbody>
        <tfoot>
            {{-- Net row ── --}}
            @php
                $netCurrClass = $totalCurrNet >= 0 ? 'net-surplus' : 'net-deficit';
                $netPrevClass = $totalPrevNet >= 0 ? 'net-surplus' : 'net-deficit';
                $netVarClass = $netVarGhs >= 0 ? 'var-good' : 'var-bad';
                $netCurrLabel = $totalCurrNet >= 0 ? 'Net Surplus' : 'Net Deficit';
            @endphp
            <td>{{ $netCurrLabel }}</td>
            <td class="{{ $netCurrClass }}">{{ number_format($totalCurrNet, 2) }}</td>
            <td class="{{ $netPrevClass }}">{{ number_format($totalPrevNet, 2) }}</td>
            <td class="{{ $netVarClass }}">
                {{ $netVarGhs >= 0 ? '+' : '' }}{{ number_format($netVarGhs, 2) }}
            </td>
            <td class="{{ $netVarClass }}">
                {{ $netVarPct >= 0 ? '+' : '' }}{{ $netVarPct }}%
            </td>
        </tfoot>
    </table>

    {{-- ── Income breakdown ── --}}
    <div class="section-label">Income Breakdown</div>

    @if (count($incomeRows) > 0)
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Account</th>
                    <th>{{ $currentLabel }}</th>
                    <th>{{ $prevLabel }}</th>
                    <th>Variance (GHS)</th>
                    <th>Variance (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incomeRows as $row)
                    @php
                        $vClass = $row['var_ghs'] >= 0 ? 'var-good' : 'var-bad';
                    @endphp
                    <tr>
                        <td>{{ $row['AccountName'] }}</td>
                        <td>{{ number_format($row['current'], 2) }}</td>
                        <td>{{ number_format($row['previous'], 2) }}</td>
                        <td class="{{ $vClass }}">
                            {{ $row['var_ghs'] >= 0 ? '+' : '' }}{{ number_format($row['var_ghs'], 2) }}
                        </td>
                        <td class="{{ $vClass }}">
                            {{ $row['var_pct'] >= 0 ? '+' : '' }}{{ $row['var_pct'] }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Revenue</td>
                    <td>{{ number_format($totalCurrIncome, 2) }}</td>
                    <td>{{ number_format($totalPrevIncome, 2) }}</td>
                    <td class="{{ $incomeVarGhs >= 0 ? 'var-good' : 'var-bad' }}">
                        {{ $incomeVarGhs >= 0 ? '+' : '' }}{{ number_format($incomeVarGhs, 2) }}
                    </td>
                    <td class="{{ $incomeVarPct >= 0 ? 'var-good' : 'var-bad' }}">
                        {{ $incomeVarPct >= 0 ? '+' : '' }}{{ $incomeVarPct }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="empty-state">No income recorded for the selected periods.</div>
    @endif

    {{-- ── Expenditure breakdown ── --}}
    <div class="section-label">Expenditure Breakdown</div>

    @if (count($expendRows) > 0)
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Account</th>
                    <th>{{ $currentLabel }}</th>
                    <th>{{ $prevLabel }}</th>
                    <th>Variance (GHS)</th>
                    <th>Variance (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expendRows as $row)
                    @php
                        //-- For expenditure: spending more = bad (red), spending less = good (green) --}}
                        $vClass = $row['var_ghs'] <= 0 ? 'var-good' : 'var-bad';
                    @endphp
                    <tr>
                        <td>{{ $row['AccountName'] }}</td>
                        <td>{{ number_format($row['current'], 2) }}</td>
                        <td>{{ number_format($row['previous'], 2) }}</td>
                        <td class="{{ $vClass }}">
                            {{ $row['var_ghs'] >= 0 ? '+' : '' }}{{ number_format($row['var_ghs'], 2) }}
                        </td>
                        <td class="{{ $vClass }}">
                            {{ $row['var_pct'] >= 0 ? '+' : '' }}{{ $row['var_pct'] }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Expenditure</td>
                    <td>{{ number_format($totalCurrExpend, 2) }}</td>
                    <td>{{ number_format($totalPrevExpend, 2) }}</td>
                    <td class="{{ $expendVarGhs <= 0 ? 'var-good' : 'var-bad' }}">
                        {{ $expendVarGhs >= 0 ? '+' : '' }}{{ number_format($expendVarGhs, 2) }}
                    </td>
                    <td class="{{ $expendVarPct <= 0 ? 'var-good' : 'var-bad' }}">
                        {{ $expendVarPct >= 0 ? '+' : '' }}{{ $expendVarPct }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="empty-state">No expenditure recorded for the selected periods.</div>
    @endif

    {{-- ── Footer ── --}}
    <div class="report-footer">
        <span class="footer-text">Confidential — for management use only</span>
        <span class="footer-text">
            Printed by {{ $user->Username }} &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }}
        </span>
    </div>

</body>

</html>
