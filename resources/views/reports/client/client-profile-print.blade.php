<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile — {{ $consignee->FullName ?? 'Unknown' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* ── Summary cards ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 16px 0;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            background: #f9fafb;
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #185FA5;
        }

        .card-body {
            padding: 10px;
        }

        /* ── Section header ── */
        .section-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #185FA5;
            padding: 8px 0 6px;
            border-bottom: 1px solid #e5e7eb;
            margin: 16px 0 10px;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 16px;
        }

        thead th {
            background: #185FA5;
            color: #fff;
            padding: 7px 9px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        thead th.r {
            text-align: right;
        }

        tbody td {
            padding: 7px 9px;
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
            padding: 8px 9px;
            font-weight: 700;
            font-size: 12px;
            background: #f3f4f6;
            border-top: 2px solid #185FA5;
        }

        tfoot td.r {
            text-align: right;
        }

        /* ── Status badges ── */
        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px;
        }

        .badge-notarrived {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-pending {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-gatedout {
            background: #dcfce7;
            color: #166534;
        }

        .badge-cleared {
            background: #f3f4f6;
            color: #374151;
        }

        /* ── Ranking badge ── */
        .rank-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 99px;
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
            <p class="rpt-title">Client Profile Report</p>
            <p class="rpt-meta-row">Client &nbsp;<span class="rpt-meta-val">{{ $consignee->FullName ?? '—' }}</span></p>
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Summary cards ── --}}
    <div class="cards-grid">

        {{-- Card 1 — Consignee details ── --}}
        <div class="card">
            <div class="card-header">Consignee Details</div>
            <div class="card-body">
                <p style="font-size:13px; font-weight:700; margin-bottom:4px;">
                    {{ $consignee->FullName ?? '—' }}
                </p>
                @if ($consignee->TelNo)
                    <p style="font-size:11px; color:#6b7280; margin-bottom:2px;">
                        <svg style="width:11px;height:11px;display:inline;vertical-align:middle;margin-right:3px;"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z" />
                        </svg> {{ $consignee->TelNo }}
                    </p>
                @endif
                @if ($consignee->Address1)
                    <p style="font-size:11px; color:#6b7280; margin-bottom:2px;">
                        {{ $consignee->Address1 }}
                    </p>
                @endif
                @if ($memberSince)
                    <p style="font-size:10px; color:#9ca3af; margin-top:4px;">
                        Member since: {{ \Carbon\Carbon::parse($memberSince)->format('d M Y') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Card 2 — Consignment summary ── --}}
        <div class="card">
            <div class="card-header">Consignments</div>
            <div class="card-body">
                <p style="font-size:22px; font-weight:700; color:#185FA5; margin-bottom:4px;">
                    {{ $consignmentSummary['total'] + $consignmentSummary['hbl_total'] }}
                </p>
                <p style="font-size:10px; color:#6b7280; margin-bottom:6px;">total consignments</p>
                <div style="display:flex; flex-wrap:wrap; gap:3px;">
                    @if ($consignmentSummary['not_arrived'] > 0)
                        <span class="badge badge-notarrived">Not Arrived:
                            {{ $consignmentSummary['not_arrived'] }}</span>
                    @endif
                    @if ($consignmentSummary['pending'] > 0)
                        <span class="badge badge-pending">Pending: {{ $consignmentSummary['pending'] }}</span>
                    @endif
                    @if ($consignmentSummary['gated_out'] > 0)
                        <span class="badge badge-gatedout">Gated-Out: {{ $consignmentSummary['gated_out'] }}</span>
                    @endif
                    @if ($consignmentSummary['cleared'] > 0)
                        <span class="badge badge-cleared">Cleared: {{ $consignmentSummary['cleared'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3 — Invoice summary ── --}}
        <div class="card">
            <div class="card-header">Invoices & Payments</div>
            <div class="card-body">
                @if ($invoiceSummary && ($invoiceSummary->TotalInvoiced || $invoiceSummary->TotalPaid))
                    <div style="display:flex; flex-direction:column; gap:5px; font-size:11px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:#6b7280;">Invoiced</span>
                            <span style="font-weight:700;">
                                GH₵ {{ number_format($invoiceSummary->TotalInvoiced, 2) }}
                            </span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:#6b7280;">Paid</span>
                            <span style="font-weight:700; color:#15803d;">
                                GH₵ {{ number_format($invoiceSummary->TotalPaid, 2) }}
                            </span>
                        </div>
                        <div
                            style="border-top:1px solid #e5e7eb; padding-top:5px;
                                    display:flex; justify-content:space-between;">
                            <span style="font-weight:700;">Outstanding</span>
                            <span
                                style="font-weight:700;
                                color:{{ $invoiceSummary->Outstanding > 0 ? '#b91c1c' : '#15803d' }};">
                                GH₵ {{ number_format($invoiceSummary->Outstanding, 2) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p style="font-size:11px; color:#6b7280;">No invoice data.</p>
                @endif
            </div>
        </div>

        {{-- Card 4 — Customer ranking ── --}}
        <div class="card">
            <div class="card-header">Customer Ranking</div>
            <div class="card-body" style="text-align:center;">
                @php
                    $r = $ranking;
                    $clsMap = [
                        'gold' => ['#fef3c7', '#92400e'],
                        'silver' => ['#f3f4f6', '#374151'],
                        'bronze' => ['#fff7ed', '#9a3412'],
                        'standard' => ['#eff6ff', '#1e40af'],
                    ];
                    [$rbg, $rcolor] = $clsMap[$r['badge']['cls']] ?? ['#f3f4f6', '#374151'];
                @endphp
                @php
                    $rankSvg = match ($r['badge']['cls']) {
                        'gold'
                            => '<svg style="width:22px;height:22px;" fill="none" stroke="#92400e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
                        'silver'
                            => '<svg style="width:22px;height:22px;" fill="none" stroke="#374151" viewBox="0 0 24 24"><circle cx="12" cy="14" r="6" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2l1.5 4h3L15 2"/></svg>',
                        'bronze'
                            => '<svg style="width:22px;height:22px;" fill="none" stroke="#9a3412" viewBox="0 0 24 24"><circle cx="12" cy="14" r="6" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2l1.5 4h3L15 2"/></svg>',
                        default
                            => '<svg style="width:22px;height:22px;" fill="none" stroke="#1e40af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
                    };
                @endphp
                {!! $rankSvg !!}
                <span class="rank-badge"
                    style="background:{{ $rbg }}; color:{{ $rcolor }}; margin-top:4px; margin-bottom:8px; display:inline-block;">
                    {{ $r['badge']['label'] }}
                </span>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; text-align:left; margin-top:4px;">
                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:6px 8px;">
                        <p style="font-size:8px; text-transform:uppercase; color:#185FA5; margin-bottom:2px;">By Volume
                        </p>
                        <p style="font-size:14px; font-weight:700; color:#185FA5;">
                            #{{ $r['volume_rank'] }}
                            <span style="font-size:9px; font-weight:400; color:#6b7280;">of
                                {{ $r['volume_total'] }}</span>
                        </p>
                        <p style="font-size:9px; color:#6b7280;">{{ $r['volume_count'] }} consignments</p>
                        <p style="font-size:9px; color:#9ca3af;">Top {{ $r['volume_percentile'] }}%</p>
                    </div>
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:6px 8px;">
                        <p style="font-size:8px; text-transform:uppercase; color:#15803d; margin-bottom:2px;">By Value
                        </p>
                        <p style="font-size:14px; font-weight:700; color:#15803d;">
                            #{{ $r['value_rank'] }}
                            <span style="font-size:9px; font-weight:400; color:#6b7280;">of
                                {{ $r['value_total'] }}</span>
                        </p>
                        <p style="font-size:9px; color:#6b7280;">GH₵ {{ number_format($r['client_total_value'], 2) }}
                        </p>
                        <p style="font-size:9px; color:#9ca3af;">Top {{ $r['value_percentile'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Chart ── --}}
    <p class="section-header">Transaction History</p>
    <div style="margin-bottom:16px;">
        <canvas id="print_chart" height="60"></canvas>
    </div>

    {{-- ── Consignments table ── --}}
    @if (count($consignments) > 0)
        <p class="section-header">Consignments (FCL)</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:14%">Main BL</th>
                    <th style="width:10%">ETA</th>
                    <th style="width:14%">Carrier</th>
                    <th style="width:18%">Container No(s)</th>
                    <th style="width:12%">Commodity</th>
                    <th style="width:9%">Status</th>
                    <th class="r" style="width:7%">Age</th>
                    <th class="r" style="width:12%">Date Reg.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consignments as $i => $row)
                    @php
                        $statusMap = [
                            0 => ['Cleared', 'badge-cleared'],
                            1 => ['Not Arrived', 'badge-notarrived'],
                            2 => ['Pending', 'badge-pending'],
                            3 => ['Gated Out', 'badge-gatedout'],
                        ];
                        $st = $statusMap[$row->Status] ?? ['—', ''];
                        $age = (int) $row->AgeDays;
                        $ageColor =
                            $row->Status == 0
                                ? '#6b7280'
                                : ($age <= 7
                                    ? '#15803d'
                                    : ($age <= 14
                                        ? '#b45309'
                                        : ($age <= 30
                                            ? '#c2410c'
                                            : '#b91c1c')));
                    @endphp
                    <tr>
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                        <td>{{ $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '—' }}</td>
                        <td>{{ $row->CarrierName ?? '—' }}</td>
                        <td style="font-family:monospace">{{ $row->ContainerNos ?? '—' }}</td>
                        <td>{{ $row->CommodityType ?? '—' }}</td>
                        <td><span class="badge {{ $st[1] }}">{{ $st[0] }}</span></td>
                        <td class="r" style="color:{{ $ageColor }}; font-weight:700;">{{ $age }}
                        </td>
                        <td class="r" style="color:#6b7280">
                            {{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── HBL entries ── --}}
    @if (count($hblEntries) > 0)
        <p class="section-header">HBL Entries (LCL)</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:14%">Main BL</th>
                    <th style="width:12%">House BL</th>
                    <th style="width:10%">ETA</th>
                    <th style="width:12%">Carrier</th>
                    <th style="width:22%">Description</th>
                    <th style="width:8%">Weight</th>
                    <th style="width:9%">Packages</th>
                    <th style="width:9%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hblEntries as $i => $row)
                    @php
                        $statusMap = [
                            0 => ['Cleared', 'badge-cleared'],
                            1 => ['Not Arrived', 'badge-notarrived'],
                            2 => ['Pending', 'badge-pending'],
                            3 => ['Gated Out', 'badge-gatedout'],
                        ];
                        $st = $statusMap[$row->Status] ?? ['—', ''];
                    @endphp
                    <tr>
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                        <td style="font-family:monospace">{{ $row->HouseBL ?? '—' }}</td>
                        <td>{{ $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '—' }}</td>
                        <td>{{ $row->CarrierName ?? '—' }}</td>
                        <td>{{ $row->Description ?? '—' }}</td>
                        <td>{{ $row->Weight ?? '—' }}</td>
                        <td>{{ $row->Package ?? '—' }} {{ $row->Unit ?? '' }}</td>
                        <td><span class="badge {{ $st[1] }}">{{ $st[0] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Invoices ── --}}
    @if (count($invoices) > 0)
        <p class="section-header">Invoices & Receipts</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:10%">Date</th>
                    <th style="width:14%">Receipt No</th>
                    <th style="width:16%">BL / HBL</th>
                    <th style="width:18%">Account</th>
                    <th style="width:20%">Description</th>
                    <th class="r" style="width:9%">Dr</th>
                    <th class="r" style="width:9%">Cr</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $i => $row)
                    <tr>
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}</td>
                        <td style="font-family:monospace">{{ $row->ReceiptNo ?? '—' }}</td>
                        <td style="font-family:monospace">
                            {{ $row->MainBL ?? '—' }}{{ $row->HouseBL ? ' / ' . $row->HouseBL : '' }}
                        </td>
                        <td>{{ $row->AccountName ?? '—' }}</td>
                        <td>{{ $row->Description ?? '—' }}</td>
                        <td class="r" style="color:#b91c1c;">
                            GH₵ {{ number_format($row->Dr, 2) }}
                        </td>
                        <td class="r" style="color:#15803d;">
                            GH₵ {{ number_format($row->Cr, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @if ($invoiceSummary)
                <tfoot>
                    <tr>
                        <td colspan="6" class="r">Totals</td>
                        <td class="r" style="color:#b91c1c;">
                            GH₵ {{ number_format($invoiceSummary->TotalInvoiced, 2) }}
                        </td>
                        <td class="r" style="color:#15803d;">
                            GH₵ {{ number_format($invoiceSummary->TotalPaid, 2) }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @endif

    {{-- ── Disbursements ── --}}
    @if (count($disbursements) > 0)
        <p class="section-header">Disbursements (PSIL Expenditure on Behalf of Client)</p>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:10%">Date</th>
                    <th style="width:14%">Receipt No</th>
                    <th style="width:14%">Main BL</th>
                    <th style="width:10%">HBL</th>
                    <th style="width:22%">Account</th>
                    <th class="r" style="width:13%">Expenditure</th>
                    <th class="r" style="width:13%">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($disbursements as $i => $row)
                    <tr>
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->Date)->format('d M Y') }}</td>
                        <td style="font-family:monospace">{{ $row->ReceiptNo ?? '—' }}</td>
                        <td style="font-family:monospace">{{ $row->MainBL ?? '—' }}</td>
                        <td style="font-family:monospace">{{ $row->HBL ?? '—' }}</td>
                        <td>{{ $row->AccountName ?? '—' }}</td>
                        <td class="r" style="color:#b91c1c;">
                            GH₵ {{ number_format($row->Expenditure, 2) }}
                        </td>
                        <td class="r" style="color:#15803d;">
                            GH₵ {{ number_format($row->Revenue, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="r">Totals</td>
                    <td class="r" style="color:#b91c1c;">
                        GH₵ {{ number_format($disbursementTotals['expenditure'], 2) }}
                    </td>
                    <td class="r" style="color:#15803d;">
                        GH₵ {{ number_format($disbursementTotals['revenue'], 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ── Footer ── --}}
    <div class="rpt-footer">
        <span>
            The Freight Diary &nbsp;·&nbsp;
            {{ $company?->InstName ?? 'Prime Survivors International Ltd' }}
            &nbsp;·&nbsp; Confidential — for internal use only
        </span>
        <span>
            Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }}
            &nbsp;·&nbsp; {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>

    <script>
        // ── Chart.js — render on print view ──────────────────────────────
        (function() {
            const chartData = @json($chartData);

            if (!chartData || !chartData.length) return;

            const labels = chartData.map(r => r.MonthLabel);
            const invoiced = chartData.map(r => parseFloat(r.Invoiced || 0));
            const paid = chartData.map(r => parseFloat(r.Paid || 0));

            new Chart(document.getElementById('print_chart').getContext('2d'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Invoiced (GH₵)',
                            data: invoiced,
                            borderColor: '#185FA5',
                            backgroundColor: 'rgba(24,95,165,0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                        },
                        {
                            label: 'Paid (GH₵)',
                            data: paid,
                            borderColor: '#15803d',
                            backgroundColor: 'rgba(21,128,61,0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    animation: false, // no animation on print view
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            enabled: false
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => 'GH₵ ' + v.toLocaleString()
                            }
                        }
                    }
                }
            });
        })();
    </script>

</body>

</html>
