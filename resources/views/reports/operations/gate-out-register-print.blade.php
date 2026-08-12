<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: #ffffff;
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

        /* ── Report header ── */
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

        /* ── Summary strip ── */
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

        .stat-amber .stat-val {
            color: #b45309;
        }

        .stat-green .stat-val {
            color: #15803d;
        }

        .stat-red .stat-val {
            color: #b91c1c;
        }

        /* ── Table ── */
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
            letter-spacing: 0.05em;
        }

        thead th.r {
            text-align: right;
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
            vertical-align: middle;
            font-size: 14px;
        }

        tbody td.r {
            text-align: right;
        }

        tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        /* ── Status badges ── */
        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .badge-gatedout {
            background: #dcfce7;
            color: #166534;
        }

        .badge-returned {
            background: #f3f4f6;
            color: #374151;
        }

        /* ── Demurrage colour coding ── */
        .dem-zero {
            color: #6b7280;
        }

        .dem-amber {
            color: #b45309;
            font-weight: 700;
        }

        .dem-red {
            color: #b91c1c;
            font-weight: 700;
        }

        /* ── Totals row ── */
        .totals-row td {
            padding: 10px;
            font-weight: 700;
            font-size: 13px;
            background: #f3f4f6;
            border-top: 2px solid #185FA5;
        }

        /* ── Footer ── */
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

    {{-- ── Action bar ── --}}
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
            <p class="rpt-meta-row">Gate-Out Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Summary strip ── --}}
    <div class="summary-strip">
        <div class="stat-card stat-blue">
            <div class="stat-label">Total Containers</div>
            <div class="stat-val">{{ $summary['total'] }}</div>
        </div>
        <div class="stat-card stat-amber">
            <div class="stat-label">Gated Out (Outstanding)</div>
            <div class="stat-val">{{ $summary['gated_out'] }}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Returned</div>
            <div class="stat-val">{{ $summary['returned'] }}</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Total Demurrage Days</div>
            <div class="stat-val">{{ number_format($summary['total_demurrage_days']) }}</div>
        </div>
    </div>

    {{-- ── Main table ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:12%">Main BL</th>
                <th style="width:9%">ETA</th>
                <th style="width:12%">Container No</th>
                <th style="width:6%">Size</th>
                <th style="width:18%">Consignee</th>
                <th style="width:10%">Carrier</th>
                <th style="width:9%">Gate Out</th>
                <th style="width:9%">Return Date</th>
                <th class="r" style="width:7%">Demurrage</th>
                <th style="width:5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                @php
                    $days = (int) $row->DemurrageDays;
                    $demCls = $days > 7 ? 'dem-red' : ($days >= 1 ? 'dem-amber' : 'dem-zero');
                    $isReturned = $row->ReturnStatus === 'returned';
                @endphp
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $row->MainBL ?? '-' }}</td>
                    <td>{{ $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '-' }}</td>
                    <td style="font-family:monospace">{{ $row->ContainerNo ?? '-' }}</td>
                    <td>{{ $row->ContainerSize ?? '-' }}</td>
                    <td>{{ $row->ConsigneeName ?? '-' }}</td>
                    <td>{{ $row->CarrierName ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->GateOutDate)->format('d M Y') }}</td>
                    <td style="color:#6b7280">
                        {{ $row->ReturnDate && $row->ReturnDate !== '0000-00-00'
                            ? \Carbon\Carbon::parse($row->ReturnDate)->format('d M Y')
                            : '—' }}
                    </td>
                    <td class="r {{ $demCls }}">{{ $days }}</td>
                    <td>
                        <span class="badge {{ $isReturned ? 'badge-returned' : 'badge-gatedout' }}">
                            {{ $isReturned ? 'Returned' : 'Gated Out' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center; padding:2rem; color:#9ca3af;">
                        No gate-out records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- ── Totals row ── --}}
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr class="totals-row">
                    <td colspan="7" style="text-align:right; padding-right:12px;">
                        Outstanding: {{ $summary['gated_out'] }}
                        &nbsp;·&nbsp;
                        Returned: {{ $summary['returned'] }}
                    </td>
                    <td colspan="2" style="text-align:right; padding-right:12px;">
                        Total Demurrage:
                    </td>
                    <td class="r dem-red">{{ number_format($summary['total_demurrage_days']) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ── Footer ── --}}
    <div class="rpt-footer">
        <span>The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only</span>
        <span>Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
            {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <script>
        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route('reports.operations.gate-out-register.export') }}?' + params.toString();
        };
    </script>

</body>

</html>
