<div class="rpt-header">
    <div class="rpt-logo-block">
        <img src="{{ asset('images/logo.png') }}" alt="{{ $company?->InstName ?? 'PSIL' }}"
            style="height: 56px; width: auto; object-fit: contain; flex-shrink: 0;" onerror="this.style.display='none'">
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
        @if (isset($reportDate))
            <p class="rpt-meta-row">Date &nbsp;<span class="rpt-meta-val">{{ $reportDate }}</span></p>
        @else
            <p class="rpt-meta-row">Period &nbsp;<span class="rpt-meta-val">{{ $dateFrom }} —
                    {{ $dateTo }}</span></p>
        @endif
        <p class="rpt-meta-row">Generated &nbsp;<span class="rpt-meta-val">{{ now()->format('d M Y, h:i A') }}</span>
        </p>
        <p class="rpt-meta-row">By &nbsp;<span
                class="rpt-meta-val">{{ auth()->user()->FullName ?? auth()->user()->ID }}</span></p>
    </div>
</div>
<div class="rpt-divider"></div>
