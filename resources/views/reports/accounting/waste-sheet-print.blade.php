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

        .alert-banner {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 16px;
            font-size: 11px;
            color: #7f1d1d;
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
            font-size: 12px;
            background: #fff7f7;
        }

        tbody td.r {
            text-align: right;
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

    @if ($data['totals']['count'] > 0)
        <div class="alert-banner">
            <strong>Audit Alert:</strong> {{ $data['totals']['count'] }} reversed transaction(s) found in this period.
            Total Dr reversed: <strong>GH₵ {{ number_format($data['totals']['dr'], 2) }}</strong> —
            Total Cr reversed: <strong>GH₵ {{ number_format($data['totals']['cr'], 2) }}</strong>.
            All entries below represent corrected or voided transactions and should be reviewed by management.
        </div>
    @endif

    <div class="summary-strip">
        <div class="stat-card" style="border-color:#fecaca;">
            <div class="stat-label">Total Reversals</div>
            <div class="stat-val" style="color:#b91c1c;">{{ $data['totals']['count'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Dr Reversed</div>
            <div class="stat-val" style="font-size:14px; color:#b91c1c;">GH₵
                {{ number_format($data['totals']['dr'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Cr Reversed</div>
            <div class="stat-val" style="font-size:14px; color:#185FA5;">GH₵
                {{ number_format($data['totals']['cr'], 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:9%">Date</th>
                <th style="width:12%">Receipt No</th>
                <th style="width:16%">Account</th>
                <th style="width:22%">Description</th>
                <th class="r" style="width:9%">Dr</th>
                <th class="r" style="width:9%">Cr</th>
                <th style="width:9%">Posted By</th>
                <th style="width:9%">Reversed By</th>
                <th style="width:13%">Reversed At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['query'] as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}</td>
                    <td style="font-family:monospace;">{{ $row->ReceiptNo ?? '—' }}</td>
                    <td>{{ $row->AccountName ?? '—' }}</td>
                    <td>{{ $row->Description ?? '—' }}</td>
                    <td class="r" style="color:#b91c1c;">{{ number_format($row->Dr, 2) }}</td>
                    <td class="r" style="color:#185FA5;">{{ number_format($row->Cr, 2) }}</td>
                    <td style="color:#6b7280;">{{ $row->Username ?? '—' }}</td>
                    <td style="color:#b91c1c; font-weight:700;">{{ $row->ReversedBy ?? '—' }}</td>
                    <td style="color:#6b7280; font-size:10px;">
                        {{ $row->ReversedAt ? \Carbon\Carbon::parse($row->ReversedAt)->format('d M Y h:i A') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:2rem;color:#9ca3af;">No reversed transactions
                        found.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($data['query']->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTALS — {{ $data['totals']['count'] }} reversal(s)</td>
                    <td class="r" style="color:#b91c1c;">GH₵ {{ number_format($data['totals']['dr'], 2) }}</td>
                    <td class="r" style="color:#185FA5;">GH₵ {{ number_format($data['totals']['cr'], 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — Audit Document</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.accounting.waste-sheet.export') }}?' + params.toString();
        };
    </script>
</body>

</html>
