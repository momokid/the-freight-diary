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

        .summary-strip {
            display: flex;
            gap: 10px;
            margin: 16px 0;
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
            font-size: 20px;
            font-weight: 700;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        .stat-amber .stat-val {
            color: #b45309;
        }

        .stat-gray .stat-val {
            color: #374151;
        }

        /* ── Urgent alert banner ── */
        .alert-banner {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-icon {
            color: #b91c1c;
            font-size: 16px;
            font-weight: 700;
        }

        .alert-text {
            font-size: 11px;
            color: #7f1d1d;
            line-height: 1.6;
        }

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

        /* ── Days pending ── */
        .days-ok {
            color: #15803d;
        }

        .days-warn {
            color: #b45309;
            font-weight: 700;
        }

        .days-urgent {
            color: #b91c1c;
            font-weight: 700;
        }

        /* ── Overdue row highlight ── */
        .row-overdue td {
            background: #fff7f7 !important;
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
            <p class="rpt-meta-row">As At &nbsp;<span class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span>
            </p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Urgent alert if overdue entries exist ── --}}
    @if ($totals['overdue'] > 0)
        <div class="alert-banner" style="margin-top:14px;">
            <div class="alert-icon">!</div>
            <div class="alert-text">
                <strong>{{ $totals['overdue'] }} disbursement{{ $totals['overdue'] != 1 ? 's' : '' }}</strong>
                have been pending for more than 7 days and require immediate management approval.
                Total outstanding expenditure: <strong>GH₵ {{ number_format($totals['expenditure'], 2) }}</strong>.
            </div>
        </div>
    @endif

    {{-- ── Summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Pending</div>
            <div class="stat-val">{{ $totals['total'] }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Overdue (&gt;7 days)</div>
            <div class="stat-val">{{ $totals['overdue'] }}</div>
        </div>
        <div class="stat-card stat-amber">
            <div class="stat-label">Total Expenditure</div>
            <div class="stat-val">GH₵ {{ number_format($totals['expenditure'], 2) }}</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-val">GH₵ {{ number_format($totals['revenue'], 2) }}</div>
        </div>
    </div>

    {{-- ── Main table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:10%">Date</th>
                <th style="width:13%">Main BL</th>
                <th style="width:10%">HBL</th>
                <th style="width:18%">Consignee</th>
                <th style="width:18%">Account</th>
                <th style="width:11%">Receipt No</th>
                <th class="r" style="width:9%">Expenditure</th>
                <th class="r" style="width:7%">Days</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $days = (int) $row->DaysPending;
                    $daysCls = $days > 14 ? 'days-urgent' : ($days > 7 ? 'days-warn' : 'days-ok');
                    $rowCls = $days > 7 ? 'row-overdue' : '';
                @endphp
                <tr class="{{ $rowCls }}">
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="color:#6b7280">
                        {{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}
                    </td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                    <td style="font-family:monospace; color:#6b7280">{{ $row->HBL ?? '—' }}</td>
                    <td>{{ $row->ConsigneeName ?? '—' }}</td>
                    <td>{{ $row->AccountName ?? '—' }}</td>
                    <td style="font-family:monospace; color:#6b7280">{{ $row->ReceiptNo ?? '—' }}</td>
                    <td class="r" style="color:#b91c1c; font-weight:700;">
                        GH₵ {{ number_format($row->Expenditure, 2) }}
                    </td>
                    <td class="r {{ $daysCls }}">{{ $days }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No pending disbursements found.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align:right;">TOTAL</td>
                    <td class="r" style="color:#b91c1c;">
                        GH₵ {{ number_format($totals['expenditure'], 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            window.location.href = '{{ route('reports.disbursement.unapproved.export') }}';
        };
    </script>

</body>

</html>
