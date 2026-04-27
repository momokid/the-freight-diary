<div class="rpt-footer">
    <span class="rpt-footer-txt">
        The Freight Diary &nbsp;·&nbsp; {{ $company?->InstName ?? 'Prime Survivors International Ltd' }} &nbsp;·&nbsp;
        Confidential — for internal use only
    </span>
    <span class="rpt-footer-txt">
        Printed by: {{ auth()->user()->FullName ?? auth()->user()->ID }} &nbsp;·&nbsp;
        {{ now()->format('d M Y, h:i A') }}
    </span>
</div>
