@php
    $ragColors = match ($rag) {
        'green' => ['bar' => '#3B6D11', 'bg' => '#EAF3DE', 'text' => '#3B6D11', 'label' => 'On Track'],
        'amber' => ['bar' => '#EF9F27', 'bg' => '#FBF0DA', 'text' => '#92600E', 'label' => 'At Risk'],
        'red' => ['bar' => '#E24B4A', 'bg' => '#FCEBEB', 'text' => '#A32D2D', 'label' => 'Behind'],
        default => ['bar' => '#aaa', 'bg' => '#f5f5f5', 'text' => '#888', 'label' => 'No Data'],
    };

    $remaining = max(0, (float) $target->TargetAmount - (float) $cumulative);
    $safePct = min(100, max(0, (float) $progressPct));
@endphp

<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
            Vision 5:29
            <span
                style="font-size:11px; font-weight:400; color:var(--text-muted);
                         margin-left:6px;">GH₵5M
                net profit by 2029</span>
        </p>
        <button onclick="window.DashboardApp.loadWidget('vision')"
            style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:11px;
                       color:var(--text-muted); cursor:pointer;">
            ↻ Refresh
        </button>
    </div>

    <div style="padding:1rem 1.25rem;">

        {{-- ── Stat cards: Cumulative vs Target vs Remaining ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-bottom:16px;">

            <div style="background:{{ $ragColors['bg'] }}; border-radius:8px; padding:10px 12px;">
                <div style="font-size:14px; font-weight:600; color:{{ $ragColors['text'] }}; margin-bottom:2px;">
                    GHS {{ number_format($cumulative, 2) }}
                </div>
                <div
                    style="font-size:11px; color:{{ $ragColors['text'] }}; text-transform:uppercase;
                            letter-spacing:0.4px;">
                    Cumulative
                </div>
            </div>

            <div style="background:#E6F1FB; border-radius:8px; padding:10px 12px;">
                <div style="font-size:14px; font-weight:600; color:#0C447C; margin-bottom:2px;">
                    GHS {{ number_format($target->TargetAmount, 2) }}
                </div>
                <div
                    style="font-size:11px; color:#0C447C; text-transform:uppercase;
                            letter-spacing:0.4px;">
                    Target
                </div>
            </div>

            <div
                style="background:#f9f9f9; border:0.5px solid var(--border-color);
                        border-radius:8px; padding:10px 12px;">
                <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-bottom:2px;">
                    GHS {{ number_format($remaining, 2) }}
                </div>
                <div
                    style="font-size:11px; color:var(--text-muted); text-transform:uppercase;
                            letter-spacing:0.4px;">
                    Remaining
                </div>
            </div>

        </div>

        {{-- ── Progress bar ── --}}
        <div style="margin-bottom:8px;">
            <div
                style="width:100%; background:var(--border-color);
                        border-radius:6px; height:12px; overflow:hidden;">
                <div
                    style="width:{{ $safePct }}%; height:100%;
                            background:{{ $ragColors['bar'] }};
                            border-radius:6px;
                            transition:width 0.4s ease;">
                </div>
            </div>
        </div>

        {{-- ── Progress footer ── --}}
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:12px; color:var(--text-muted);">
                {{ number_format($safePct, 1) }}% of target reached
            </span>
            <span
                style="background:{{ $ragColors['bg'] }}; color:{{ $ragColors['text'] }};
                        font-size:11px; font-weight:600; border-radius:10px;
                        padding:2px 10px; letter-spacing:0.3px;">
                {{ $ragColors['label'] }}
            </span>
        </div>

    </div>

</div>
