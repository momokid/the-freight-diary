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
            font-size: 18px;
            font-weight: 700;
        }

        .stat-blue .stat-val {
            color: #185FA5;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-gray .stat-val {
            color: #374151;
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

        /* ── Change returned colours ── */
        .change-ok {
            color: #15803d;
            font-weight: 700;
        }

        .change-neg {
            color: #b91c1c;
            font-weight: 700;
        }

        /* ── Overrun row ── */
        .row-overrun td {
            background: #fff7f7 !important;
        }

        /* ── Officer rank badge ── */
        .rank-pill {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 99px;
            background: #eff6ff;
            color: #185FA5;
        }

        /* ── Accountability note ── */
        .accountability-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 14px;
            font-size: 11px;
            color: #78350f;
            line-height: 1.7;
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
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Accountability note if any officer overran ── --}}
    @php
        $overrunOfficers = $rows->filter(fn($r) => $r->ChangeReturned < 0);
    @endphp
    @if ($overrunOfficers->count() > 0)
        <div class="accountability-note" style="margin-top:14px;">
            <strong>Attention:</strong>
            {{ $overrunOfficers->count() }} officer{{ $overrunOfficers->count() != 1 ? 's' : '' }}
            spent more than the cash they received.
            Total overrun: <strong>GH₵ {{ number_format(abs($overrunOfficers->sum('ChangeReturned')), 2) }}</strong>.
            These entries require management review.
        </div>
    @endif

    {{-- ── Summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Officers</div>
            <div class="stat-val">{{ count($rows) }}</div>
        </div>
        <div class="stat-card stat-gray">
            <div class="stat-label">Total Cash Received</div>
            <div class="stat-val">GH₵ {{ number_format($totals['cash_received'], 2) }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Total Expenditure</div>
            <div class="stat-val">GH₵ {{ number_format($totals['expenditure'], 2) }}</div>
        </div>
        <div class="stat-card {{ $totals['change'] >= 0 ? 'stat-green' : 'stat-red' }}">
            <div class="stat-label">Total Change Returned</div>
            <div class="stat-val">GH₵ {{ number_format($totals['change'], 2) }}</div>
        </div>
    </div>

    {{-- ── Officer table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:18%">Officer</th>
                <th class="r" style="width:10%">Consignments</th>
                <th class="r" style="width:10%">Entries</th>
                <th class="r" style="width:16%">Cash Received</th>
                <th class="r" style="width:16%">Expenditure</th>
                <th class="r" style="width:16%">Change Returned</th>
                <th class="r" style="width:10%">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $changeCls = $row->ChangeReturned >= 0 ? 'change-ok' : 'change-neg';
                    $rowCls = $row->ChangeReturned < 0 ? 'row-overrun' : '';
                    $utilPct =
                        $row->TotalCashReceived > 0
                            ? round(($row->TotalExpenditure / $row->TotalCashReceived) * 100, 1)
                            : 0;
                @endphp
                <tr class="{{ $rowCls }}">
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-weight:600;">
                        {{ $row->Username }}
                        <span class="rank-pill" style="margin-left:4px;">
                            {{ $row->ConsignmentCount }} BL{{ $row->ConsignmentCount != 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td class="r">{{ $row->ConsignmentCount }}</td>
                    <td class="r" style="color:#6b7280;">{{ $row->EntryCount }}</td>
                    <td class="r" style="color:#374151;">
                        GH₵ {{ number_format($row->TotalCashReceived, 2) }}
                    </td>
                    <td class="r" style="color:#b91c1c;">
                        GH₵ {{ number_format($row->TotalExpenditure, 2) }}
                        <br>
                        <span style="font-size:9px; color:#6b7280;">{{ $utilPct }}% utilised</span>
                    </td>
                    <td class="r {{ $changeCls }}">
                        GH₵ {{ number_format($row->ChangeReturned, 2) }}
                        @if ($row->ChangeReturned < 0)
                            <br>
                            <span style="font-size:9px; font-weight:400;">OVERRUN</span>
                        @endif
                    </td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($row->TotalRevenue, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No disbursement records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">TOTALS</td>
                    <td class="r">GH₵ {{ number_format($totals['cash_received'], 2) }}</td>
                    <td class="r" style="color:#b91c1c;">GH₵ {{ number_format($totals['expenditure'], 2) }}</td>
                    <td class="r {{ $totals['change'] >= 0 ? 'change-ok' : 'change-neg' }}">
                        GH₵ {{ number_format($totals['change'], 2) }}
                    </td>
                    <td class="r" style="color:#185FA5;">
                        GH₵ {{ number_format($totals['revenue'], 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ── Cash utilisation insight ── --}}
    @if ($rows->count() >= 2)
        @php
            $topOfficer = $rows->sortByDesc('TotalExpenditure')->first();
            $overallUtil =
                $totals['cash_received'] > 0 ? round(($totals['expenditure'] / $totals['cash_received']) * 100, 1) : 0;
        @endphp
        <div
            style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:12px 16px; margin-top:4px;">
            <p style="font-size:11px; font-weight:700; color:#0c4a6e; margin-bottom:4px;">
                Cash Utilisation Summary
            </p>
            <p style="font-size:11px; color:#075985; line-height:1.7;">
                Overall cash utilisation rate: <strong>{{ $overallUtil }}%</strong>
                (GH₵ {{ number_format($totals['expenditure'], 2) }} spent
                of GH₵ {{ number_format($totals['cash_received'], 2) }} disbursed).
                Highest spending officer: <strong>{{ $topOfficer->Username }}</strong>
                (GH₵ {{ number_format($topOfficer->TotalExpenditure, 2) }}
                across {{ $topOfficer->ConsignmentCount }}
                consignment{{ $topOfficer->ConsignmentCount != 1 ? 's' : '' }}).
            </p>
        </div>
    @endif

    <div class="rpt-footer" style="margin-top:16px;">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.disbursement.officer-summary.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
