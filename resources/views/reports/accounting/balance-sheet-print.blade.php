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
            margin-bottom: 20px;
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
            font-size: 16px;
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

        /* Two-column layout */
        .bs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: flex-start;
        }

        .bs-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fff;
            background: #185FA5;
            padding: 8px 12px;
            border-radius: 4px 4px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td.r {
            text-align: right;
        }

        tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        tfoot td {
            padding: 9px 10px;
            font-weight: 700;
            font-size: 12px;
            background: #f3f4f6;
            border-top: 2px solid #185FA5;
        }

        tfoot td.r {
            text-align: right;
        }

        .bal-pos {
            color: #15803d;
            font-weight: 700;
        }

        .bal-neg {
            color: #b91c1c;
            font-weight: 700;
        }

        .acct-no {
            font-family: monospace;
            color: #6b7280;
            font-size: 10px;
        }

        .row-zero td {
            color: #9ca3af;
            font-style: italic;
        }

        /* Balance check bar */
        .check-bar {
            margin-top: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .check-bar.balanced {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #15803d;
        }

        .check-bar.unbalanced {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        .rpt-footer {
            margin-top: 24px;
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
            <p class="rpt-meta-row">As At &nbsp;<span class="rpt-meta-val">{{ $asAtFormatted }}</span></p>
            <p class="rpt-meta-row">Branch &nbsp;<span
                    class="rpt-meta-val">{{ $branchID === 'ALL' ? 'All Branches' : $branchID }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

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
                    style="color:{{ $vision['cumulative_surplus'] >= 0 ? '#15803d' : '#b91c1c' }}">GH₵
                    {{ number_format($vision['cumulative_surplus'], 2) }}</span>
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
                    <span
                        style="color:{{ $vision['on_track'] ? '#15803d' : '#b91c1c' }}; font-weight:700;">{{ $vision['on_track'] ? 'On Track' : 'Behind Target' }}</span>
                </p>
            </div>
        </div>
    @endif

    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Asset Accounts</div>
            <div class="stat-val">{{ $data['assets']->count() }}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Total Assets</div>
            <div class="stat-val" style="font-size:13px;">GH₵ {{ number_format($data['totalAssets'], 2) }}</div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-label">Liability / Equity Accounts</div>
            <div class="stat-val">{{ $data['liabilities']->count() }}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Total Liabilities & Equity</div>
            <div class="stat-val" style="font-size:13px;">GH₵ {{ number_format($data['totalLiabilities'], 2) }}</div>
        </div>
        <div class="stat-card {{ abs($data['difference']) < 0.01 ? 'stat-green' : 'stat-red' }}">
            <div class="stat-label">Balanced</div>
            <div class="stat-val">{{ abs($data['difference']) < 0.01 ? '✓ Yes' : '✗ No' }}</div>
        </div>
    </div>

    <div class="bs-grid">

        <div>
            <div class="bs-section-title">Assets</div>
            <table>
                <tbody>
                    @forelse ($data['assets'] as $row)
                        <tr class="{{ $row->NetBalance == 0 ? 'row-zero' : '' }}">
                            <td><span class="acct-no">{{ $row->AccountID }}</span></td>
                            <td>{{ $row->AccountName }}</td>
                            <td class="r {{ $row->NetBalance >= 0 ? 'bal-pos' : 'bal-neg' }}">GH₵
                                {{ number_format($row->NetBalance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="color:#9ca3af; padding:12px 10px;">No asset accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">TOTAL ASSETS</td>
                        <td class="r {{ $data['totalAssets'] >= 0 ? 'bal-pos' : 'bal-neg' }}">GH₵
                            {{ number_format($data['totalAssets'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div>
            <div class="bs-section-title">Liabilities &amp; Equity</div>
            <table>
                <tbody>
                    @forelse ($data['liabilities'] as $row)
                        <tr class="{{ $row->NetBalance == 0 ? 'row-zero' : '' }}">
                            <td><span class="acct-no">{{ $row->AccountID }}</span></td>
                            <td>{{ $row->AccountName }}</td>
                            <td class="r {{ $row->NetBalance >= 0 ? 'bal-pos' : 'bal-neg' }}">GH₵
                                {{ number_format($row->NetBalance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="color:#9ca3af; padding:12px 10px;">No liability/equity accounts
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">TOTAL LIABILITIES & EQUITY</td>
                        <td class="r {{ $data['totalLiabilities'] >= 0 ? 'bal-pos' : 'bal-neg' }}">GH₵
                            {{ number_format($data['totalLiabilities'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    @php $balanced = abs($data['difference']) < 0.01; @endphp
    <div class="check-bar {{ $balanced ? 'balanced' : 'unbalanced' }}">
        @if ($balanced)
            ✓ Books are balanced — Assets equal Liabilities & Equity
        @else
            ✗ Out of balance by GH₵ {{ number_format(abs($data['difference']), 2) }} — check for unposted or missing
            journal entries
        @endif
    </div>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.balance-sheet.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
