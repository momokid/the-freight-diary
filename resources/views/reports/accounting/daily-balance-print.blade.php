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

        /* Account cards */
        .account-block {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .account-header {
            background: #185FA5;
            color: #fff;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-name {
            font-size: 13px;
            font-weight: 700;
        }

        .balance-badges {
            display: flex;
            gap: 10px;
        }

        .badge-item {
            text-align: right;
        }

        .badge-label {
            font-size: 9px;
            color: #bfdbfe;
        }

        .badge-val {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            background: #f3f4f6;
            color: #374151;
            padding: 7px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        thead th.r {
            text-align: right;
        }

        tbody td {
            padding: 7px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        tbody td.r {
            text-align: right;
        }

        .no-tx {
            padding: 12px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            font-style: italic;
        }

        /* Summary grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .s-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .s-val {
            font-size: 16px;
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
                <p class="rpt-company-name">{{ $company?->InstName }}</p>
                <p class="rpt-company-sub">{{ $company?->Address ?? '' }}<br>
                    @if ($company?->TelNo)
                        Tel: {{ $company->TelNo }}
                    @endif
                </p>
            </div>
        </div>
        <div class="rpt-meta-block">
            <p class="rpt-title">{{ $reportTitle }}</p>
            <p class="rpt-meta-row">Date &nbsp;<span class="rpt-meta-val">{{ $dateFormatted }}</span></p>
            <p class="rpt-meta-row">Branch &nbsp;<span
                    class="rpt-meta-val">{{ $branchID === 'ALL' ? 'All Branches' : $branchID }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- Summary grid ─────────────────────────────────────────────────────── --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="s-label">Accounts</div>
            <div class="s-val" style="color:#185FA5;">{{ count($accounts) }}</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Opening</div>
            <div class="s-val" style="font-size:13px;">GH₵ {{ number_format($accounts->sum('OpeningBalance'), 2) }}
            </div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Movements</div>
            <div class="s-val" style="font-size:13px; color:#185FA5;">
                {{ $accounts->sum(fn($a) => $a->Transactions->count()) }} txn(s)
            </div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Closing</div>
            <div class="s-val" style="font-size:13px;">GH₵ {{ number_format($accounts->sum('ClosingBalance'), 2) }}
            </div>
        </div>
    </div>

    {{-- Per account blocks ──────────────────────────────────────────────── --}}
    @forelse($accounts as $account)
        <div class="account-block">
            <div class="account-header">
                <span class="account-name">{{ $account->AccountName }}</span>
                <div class="balance-badges">
                    <div class="badge-item">
                        <div class="badge-label">Opening</div>
                        <div class="badge-val">GH₵ {{ number_format($account->OpeningBalance, 2) }}</div>
                    </div>
                    <div class="badge-item">
                        <div class="badge-label">Dr Today</div>
                        <div class="badge-val">GH₵ {{ number_format($account->TodayDr, 2) }}</div>
                    </div>
                    <div class="badge-item">
                        <div class="badge-label">Cr Today</div>
                        <div class="badge-val">GH₵ {{ number_format($account->TodayCr, 2) }}</div>
                    </div>
                    <div class="badge-item">
                        <div class="badge-label">Closing</div>
                        <div class="badge-val"
                            style="color:{{ $account->ClosingBalance >= 0 ? '#bbf7d0' : '#fecaca' }}">
                            GH₵ {{ number_format($account->ClosingBalance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            @if ($account->Transactions->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:10%">Time</th>
                            <th style="width:14%">Receipt No</th>
                            <th style="width:36%">Description</th>
                            <th style="width:10%">Mode</th>
                            <th class="r" style="width:15%">Dr (GH₵)</th>
                            <th class="r" style="width:15%">Cr (GH₵)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($account->Transactions as $tx)
                            <tr>
                                <td style="color:#6b7280; font-family:monospace;">
                                    {{ \Carbon\Carbon::parse($tx->Time)->format('h:i A') }}
                                </td>
                                <td style="font-family:monospace;">{{ $tx->ReceiptNo ?? '—' }}</td>
                                <td>{{ $tx->Description ?? '—' }}</td>
                                <td>
                                    <span
                                        style="display:inline-block; font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px; background:{{ $tx->Mode === 'Dr' ? '#fef3c7' : '#dcfce7' }}; color:{{ $tx->Mode === 'Dr' ? '#92400e' : '#166534' }}">
                                        {{ $tx->Mode }}
                                    </span>
                                </td>
                                <td class="r" style="color:#b91c1c;">{{ number_format($tx->Dr, 2) }}</td>
                                <td class="r" style="color:#15803d;">{{ number_format($tx->Cr, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-tx">No transactions on this date</div>
            @endif
        </div>
    @empty
        <p style="text-align:center;padding:2rem;color:#9ca3af;">No active bank/cash accounts found.</p>
    @endforelse

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.daily-balance.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
