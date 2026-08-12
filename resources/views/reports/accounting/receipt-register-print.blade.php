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
            color: #185FA5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            background: #185FA5;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        thead th.r {
            text-align: right;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
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
        <div class="stat-card">
            <div class="stat-label">Total Receipts</div>
            <div class="stat-val">{{ $data['totals']['count'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Dr</div>
            <div class="stat-val" style="font-size:14px; color:#b91c1c;">GH₵
                {{ number_format($data['totals']['dr'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Cr</div>
            <div class="stat-val" style="font-size:14px; color:#15803d;">GH₵
                {{ number_format($data['totals']['cr'], 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:11%">Date</th>
                <th style="width:14%">Receipt No</th>
                <th style="width:28%">Account</th>
                <th class="r" style="width:14%">Dr (GH₵)</th>
                <th class="r" style="width:14%">Cr (GH₵)</th>
                <th style="width:7%">Lines</th>
                <th style="width:8%">Officer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['query'] as $i => $row)
                <tr>
                    <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}</td>
                    <td style="font-family:monospace;">{{ $row->ReceiptNo ?? '—' }}</td>
                    <td>{{ $row->AccountName ?? '—' }}</td>
                    <td class="r" style="color:#b91c1c;">{{ number_format($row->TotalDr, 2) }}</td>
                    <td class="r" style="color:#15803d;">{{ number_format($row->TotalCr, 2) }}</td>
                    <td style="text-align:center; color:#6b7280;">{{ $row->LineCount }}</td>
                    <td style="color:#6b7280;">{{ $row->Username ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:2rem;color:#9ca3af;">No receipts found.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($data['query']->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTALS — {{ $data['totals']['count'] }} receipt(s)</td>
                    <td class="r" style="color:#b91c1c;">GH₵ {{ number_format($data['totals']['dr'], 2) }}</td>
                    <td class="r" style="color:#15803d;">GH₵ {{ number_format($data['totals']['cr'], 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.receipt-register.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
