<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HS Code Classification Report — {{ $consignment->MainBL }}</title>
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

        /* ── Header ── */
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

        /* ── Consignment info ── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .info-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .info-val {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
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

        /* ── Item card ── */
        .item-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .item-card-header {
            background: #f9fafb;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .item-card-body {
            padding: 12px;
        }

        .item-label {
            font-size: 12px;
            font-weight: 700;
            color: #185FA5;
        }

        .item-desc {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── HS code result ── */
        .hs-result {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .hs-badge {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            background: #185FA5;
            color: #fff;
            padding: 4px 10px;
            border-radius: 5px;
        }

        .hs-desc {
            flex: 1;
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }

        .hs-duty {
            font-size: 14px;
            font-weight: 700;
        }

        /* ── Justification ── */
        .justification {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 8px;
            font-size: 11px;
            color: #374151;
            line-height: 1.7;
            white-space: pre-line;
        }

        .justification-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #92400e;
            margin-bottom: 4px;
        }

        /* ── Summary ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 16px 0;
        }

        .summary-card {
            text-align: center;
            padding: 14px;
            border-radius: 8px;
        }

        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .summary-val {
            font-size: 20px;
            font-weight: 700;
        }

        .sc-red {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .sc-red .summary-val {
            color: #b91c1c;
        }

        .sc-green {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .sc-green .summary-val {
            color: #15803d;
        }

        .sc-blue {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .sc-blue .summary-val {
            color: #185FA5;
        }

        /* ── Disclaimer ── */
        .disclaimer {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 16px;
            font-size: 10px;
            color: #78350f;
            line-height: 1.7;
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

    <div class="action-bar">
        <button class="btn-print" onclick="window.print()">Print / Save PDF</button>
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
            <p class="rpt-title">HS Code Classification Report</p>
            <p class="rpt-meta-row">BL Number &nbsp;<span class="rpt-meta-val">{{ $consignment->MainBL }}</span></p>
            @if ($cifValue > 0)
                <p class="rpt-meta-row">CIF Value &nbsp;<span class="rpt-meta-val">GH₵
                        {{ number_format($cifValue, 2) }}</span></p>
            @endif
            <p class="rpt-meta-row">Generated &nbsp;<span
                    class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span></p>
            <p class="rpt-meta-row">By &nbsp;<span
                    class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
        </div>
    </div>

    {{-- ── Consignment info ── --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">Consignee</div>
            <div class="info-val">{{ $consignment->ConsigneeName ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Carrier</div>
            <div class="info-val">{{ $consignment->CarrierName ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Vessel / Voyage</div>
            <div class="info-val">{{ $consignment->VesselName ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">ETA</div>
            <div class="info-val">
                {{ $consignment->ETA && $consignment->ETA !== '0000-00-00'
                    ? \Carbon\Carbon::parse($consignment->ETA)->format('d M Y')
                    : '—' }}
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">POL</div>
            <div class="info-val">{{ $consignment->POL_Name ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Consignee Tel</div>
            <div class="info-val">{{ $consignment->ConsigneeTel ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Classifications</div>
            <div class="info-val">{{ $predictions->count() }} item(s)</div>
        </div>
        <div class="info-card">
            <div class="info-label">Report Purpose</div>
            <div class="info-val" style="color:#185FA5;">Customs Defence</div>
        </div>
    </div>

    {{-- ── Duty savings summary ── --}}
    @if ($cifValue > 0)
        <p class="section-header">Duty Simulation Summary</p>
        <div class="summary-grid">
            <div class="summary-card sc-red">
                <div class="summary-label">GRA Worst Case</div>
                <div class="summary-val">GH₵ {{ number_format($totalHighestDuty, 2) }}</div>
                <div style="font-size:10px; color:#6b7280; margin-top:4px;">Highest duty scenario</div>
            </div>
            <div class="summary-card sc-green">
                <div class="summary-label">Recommended (Your Argument)</div>
                <div class="summary-val">GH₵ {{ number_format($totalAcceptedDuty, 2) }}</div>
                <div style="font-size:10px; color:#6b7280; margin-top:4px;">Lowest defensible codes</div>
            </div>
            <div class="summary-card sc-blue">
                <div class="summary-label">Potential Savings</div>
                <div class="summary-val">GH₵ {{ number_format($potentialSavings, 2) }}</div>
                <div style="font-size:10px; color:#6b7280; margin-top:4px;">
                    {{ $totalHighestDuty > 0 ? round(($potentialSavings / $totalHighestDuty) * 100) : 0 }}% reduction
                </div>
            </div>
        </div>
    @endif

    {{-- ── Per item classifications ── --}}
    <p class="section-header">Item Classifications & Legal Arguments</p>

    @forelse($predictions as $pred)
        @php
            $dutyData = $dutyResults[$pred->AcceptedHSCode] ?? null;
            $dutyColor =
                $pred->AcceptedDutyRate == 0
                    ? '#15803d'
                    : ($pred->AcceptedDutyRate <= 10
                        ? '#185FA5'
                        : ($pred->AcceptedDutyRate <= 20
                            ? '#b45309'
                            : '#b91c1c'));
        @endphp
        <div class="item-card">
            <div class="item-card-header">
                <p class="item-label">
                    {{ $pred->SourceType === 'LCL' ? 'HBL: ' . $pred->HouseBL : 'Container item' }}
                </p>
                <p class="item-desc">{{ $pred->ItemDescription }}</p>
            </div>
            <div class="item-card-body">

                {{-- Accepted HS code ── --}}
                <div class="hs-result">
                    <span class="hs-badge">{{ $pred->AcceptedHSCode }}</span>
                    <span class="hs-desc">{{ $pred->AcceptedHSDesc }}</span>
                    <span class="hs-duty" style="color:{{ $dutyColor }};">
                        {{ $pred->AcceptedDutyRate }}% duty
                    </span>
                    @if ($pred->WasPredictionAccepted)
                        <span
                            style="font-size:9px; font-weight:700; padding:2px 8px;
                                     border-radius:99px; background:#15803d; color:#fff;">
                            Recommended
                        </span>
                    @endif
                </div>

                {{-- Duty breakdown ── --}}
                @if ($dutyData)
                    <div
                        style="display:grid; grid-template-columns:repeat(6,1fr);
                                gap:6px; margin:8px 0; padding:8px;
                                background:#f9fafb; border-radius:6px;">
                        @foreach ([['Import Duty', $dutyData['ImportDuty'], $pred->AcceptedDutyRate . '%'], ['NHIL', $dutyData['NHIL'], '2.5%'], ['GETFund', $dutyData['GETFund'], '2.5%'], ['ECOWAS', $dutyData['ECOWAS'], '0.5%'], ['AU Levy', $dutyData['AULevy'], '0.2%'], ['VAT', $dutyData['VAT'], '15%']] as [$label, $amount, $rate])
                            <div style="text-align:center;">
                                <p style="font-size:9px; color:#6b7280;">{{ $label }} ({{ $rate }})</p>
                                <p style="font-size:11px; font-weight:700; color:#374151;">
                                    GH₵ {{ number_format($amount, 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <div
                        style="display:flex; justify-content:space-between; padding:6px 8px;
                                background:#f3f4f6; border-radius:6px; font-size:12px;">
                        <span style="font-weight:700;">Total Duty</span>
                        <span style="font-weight:700; color:#b91c1c;">
                            GH₵ {{ number_format($dutyData['TotalDuty'], 2) }}
                        </span>
                        <span style="color:#6b7280; font-size:11px;">
                            Effective rate: {{ $dutyData['EffectiveRate'] }}% of CIF
                        </span>
                    </div>
                @endif

                {{-- Legal justification ── --}}
                @if ($pred->Justification)
                    <div class="justification" style="margin-top:10px;">
                        <p class="justification-title">Legal Classification Argument</p>
                        {{ $pred->Justification }}
                    </div>
                @endif

                {{-- Accepted by ── --}}
                <p style="font-size:10px; color:#9ca3af; margin-top:8px; text-align:right;">
                    Classified by {{ $pred->AcceptedBy }}
                    · {{ $pred->AcceptedAt ? \Carbon\Carbon::parse($pred->AcceptedAt)->format('d M Y, h:i A') : '—' }}
                </p>

            </div>
        </div>
    @empty
        <p style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px;">
            No HS code classifications found for this consignment.
            Run the HS Code Advisor to generate predictions.
        </p>
    @endforelse

    {{-- ── Legal disclaimer ── --}}
    <div class="disclaimer">
        <strong>Disclaimer:</strong>
        This report is generated by the Freight Diary HS Code Advisor using the WCO Harmonized System
        and Ghana GRA ECOWAS Common External Tariff (CET) schedule.
        HS code classifications are recommendations only and do not constitute legal advice.
        The clearing agent is responsible for verifying classifications against the current GRA tariff
        schedule and exercising professional judgement during customs declaration.
        Duty rates shown are based on the ECOWAS CET — actual GRA assessed values may vary.
    </div>

    {{-- ── Footer ── --}}
    <div class="rpt-footer">
        <span>
            The Freight Diary &nbsp;·&nbsp;
            {{ $company?->InstName }}
            &nbsp;·&nbsp; Confidential — for internal use only
        </span>
        <span>
            Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }}
            &nbsp;·&nbsp; {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>

</body>

</html>
