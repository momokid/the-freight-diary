<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
            Outstanding Collections
            <span
                style="font-size:11px; font-weight:400; color:var(--text-muted);
                         margin-left:6px;">As
                at {{ now()->format('d M Y') }}</span>
        </p>
        <button onclick="window.DashboardApp.loadWidget('collections')"
            style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:11px;
                       color:var(--text-muted); cursor:pointer;">
            ↻ Refresh
        </button>
    </div>

    <div style="padding:1rem 1.25rem;">

        @if ((float) $summary->total > 0)
            {{-- ── Headline: total outstanding ── --}}
            <div style="margin-bottom:14px;">
                <div
                    style="font-size:11px; color:var(--text-muted); text-transform:uppercase;
                            letter-spacing:0.6px; margin-bottom:4px;">
                    Total Outstanding
                </div>
                <div style="font-size:22px; font-weight:700; color:#185FA5; line-height:1;">
                    GHS {{ number_format($summary->total, 2) }}
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                    across {{ number_format($summary->clientCount) }}
                    {{ Str::plural('client', $summary->clientCount) }}
                </div>
            </div>

            {{-- ── Aging buckets ── --}}
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px;">

                {{-- 0–30 days --}}
                <div style="background:#EAF3DE; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#3B6D11; margin-bottom:2px;">
                        GHS {{ number_format($summary->bucket_30, 2) }}
                    </div>
                    <div
                        style="font-size:11px; color:#3B6D11; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        0–30 Days
                    </div>
                </div>

                {{-- 31–60 days --}}
                <div style="background:#E6F1FB; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#0C447C; margin-bottom:2px;">
                        GHS {{ number_format($summary->bucket_60, 2) }}
                    </div>
                    <div
                        style="font-size:11px; color:#0C447C; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        31–60 Days
                    </div>
                </div>

                {{-- 61–90 days --}}
                <div style="background:#FBF0DA; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#92600E; margin-bottom:2px;">
                        GHS {{ number_format($summary->bucket_90, 2) }}
                    </div>
                    <div
                        style="font-size:11px; color:#92600E; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        61–90 Days
                    </div>
                </div>

                {{-- 90+ days --}}
                <div style="background:#FCEBEB; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#A32D2D; margin-bottom:2px;">
                        GHS {{ number_format($summary->bucket_90plus, 2) }}
                    </div>
                    <div
                        style="font-size:11px; color:#A32D2D; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        90+ Days
                    </div>
                </div>

            </div>
        @else
            {{-- ── Empty state ── --}}
            <div style="padding:18px 0; text-align:center;">
                <div style="font-size:18px; font-weight:700; color:#3B6D11; margin-bottom:4px;">
                    GHS 0.00
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin:0;">
                    No outstanding collections.
                </p>
            </div>
        @endif

    </div>

</div>
