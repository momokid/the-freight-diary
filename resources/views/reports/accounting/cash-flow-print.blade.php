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

        /* Account block */
        .account-block {
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .account-heading {
            background: #185FA5;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 14px;
        }

        .cf-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .cf-col {
            padding: 12px 14px;
        }

        .cf-col+.cf-col {
            border-left: 1px solid #e5e7eb;
        }

        .cf-col-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cf-col-title.inflow {
            color: #15803d;
        }

        .cf-col-title.outflow {
            color: #b91c1c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        tbody td {
            padding: 6px;
            border-bottom: 1px solid #f3f4f6;
        }

        tbody td.r {
            text-align: right;
        }

        tfoot td {
            padding: 8px 6px;
            font-weight: 700;
            font-size: 12px;
            background: #f9fafb;
            border-top: 1px solid #d1d5db;
        }

        tfoot td.r {
            text-align: right;
        }

        .net-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: #f9fafb;
            border-top: 2px solid #185FA5;
            font-size: 12px;
            font-weight: 700;
        }

        .net-pos {
            color: #15803d;
        }

        .net-neg {
            color: #b91c1c;
        }

        /* Grand total */
        .grand-total {
            margin-top: 8px;
            border: 2px solid #185FA5;
            border-radius: 6px;
            overflow: hidden;
        }

        .grand-total-heading {
            background: #185FA5;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 14px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .grand-total-body {
            display: flex;
        }

        .gt-cell {
            flex: 1;
            padding: 14px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
        }

        .gt-cell:last-child {
            border-right: none;
        }

        .gt-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .gt-val {
            font-size: 18px;
            font-weight: 700;
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

            .account-block {
                page-break-inside: avoid;
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
            <p class="rpt-meta-row">Branch &nbsp;<span
                    class="rpt-meta-val">{{ $branchID === 'ALL' ? 'All Branches' : $branchID }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Cash Accounts</div>
            <div class="stat-val">{{ $data['accounts']->count() }}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Total Inflows</div>
            <div class="stat-val" style="font-size:13px;">GH₵ {{ number_format($data['grandInflows'], 2) }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Total Outflows</div>
            <div class="stat-val" style="font-size:13px;">GH₵ {{ number_format($data['grandOutflows'], 2) }}</div>
        </div>
        <div class="stat-card {{ $data['netMovement'] >= 0 ? 'stat-green' : 'stat-red' }}">
            <div class="stat-label">Net Cash Movement</div>
            <div class="stat-val" style="font-size:13px;">GH₵ {{ number_format($data['netMovement'], 2) }}</div>
        </div>
    </div>

    @forelse ($data['accounts'] as $account)
        <div class="account-block">
            <div class="account-heading">{{ $account->AccountName }}</div>
            <div class="cf-body">
                <div class="cf-col">
                    <div class="cf-col-title inflow">↓ Inflows (Cash Received)</div>
                    <table>
                        <tbody>
                            @forelse ($account->Inflows as $row)
                                <tr>
                                    <td>{{ $row->SubAccountName ?? 'Account #' . $row->SubAccountID }}</td>
                                    <td class="r" style="color:#15803d; font-weight:600; white-space:nowrap;">GH₵
                                        {{ number_format($row->Amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="color:#9ca3af; font-style:italic;">No inflows.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Inflows</td>
                                <td class="r" style="color:#15803d;">GH₵
                                    {{ number_format($account->TotalInflows, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="cf-col">
                    <div class="cf-col-title outflow">↑ Outflows (Cash Paid Out)</div>
                    <table>
                        <tbody>
                            @forelse ($account->Outflows as $row)
                                <tr>
                                    <td>{{ $row->SubAccountName ?? 'Account #' . $row->SubAccountID }}</td>
                                    <td class="r" style="color:#b91c1c; font-weight:600; white-space:nowrap;">GH₵
                                        {{ number_format($row->Amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="color:#9ca3af; font-style:italic;">No outflows.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Outflows</td>
                                <td class="r" style="color:#b91c1c;">GH₵
                                    {{ number_format($account->TotalOutflows, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="net-bar">
                <span>Net Cash Movement — {{ $account->AccountName }}</span>
                <span class="{{ $account->NetMovement >= 0 ? 'net-pos' : 'net-neg' }}">
                    GH₵ {{ number_format($account->NetMovement, 2) }} {{ $account->NetMovement >= 0 ? '▲' : '▼' }}
                </span>
            </div>
        </div>
    @empty
        <div style="padding:32px; text-align:center; color:#9ca3af; border:1px solid #e5e7eb; border-radius:6px;">
            No active cash/bank accounts found. Set up accounts in Basic Setup → Active Accounts.
        </div>
    @endforelse

    @if ($data['accounts']->count() > 1)
        <div class="grand-total">
            <div class="grand-total-heading">Grand Total — All Cash &amp; Bank Accounts</div>
            <div class="grand-total-body">
                <div class="gt-cell">
                    <div class="gt-label">Total Inflows</div>
                    <div class="gt-val" style="color:#15803d;">GH₵ {{ number_format($data['grandInflows'], 2) }}</div>
                </div>
                <div class="gt-cell">
                    <div class="gt-label">Total Outflows</div>
                    <div class="gt-val" style="color:#b91c1c;">GH₵ {{ number_format($data['grandOutflows'], 2) }}</div>
                </div>
                <div class="gt-cell">
                    <div class="gt-label">Net Cash Movement</div>
                    <div class="gt-val" style="color:{{ $data['netMovement'] >= 0 ? '#15803d' : '#b91c1c' }};">GH₵
                        {{ number_format($data['netMovement'], 2) }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.cash-flow.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
