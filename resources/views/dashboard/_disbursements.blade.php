<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
            Pending Disbursements
        </p>
        <div style="display:flex; align-items:center; gap:8px;">
            @if ($summary->total > 0)
                <span onclick="window.DashboardApp.openDisbursementsDrawer()"
                    style="font-size:12px; color:#185FA5; font-weight:600; cursor:pointer;">
                    View All →
                </span>
            @endif
            <button onclick="window.DashboardApp.loadWidget('disbursements')"
                style="background:none; border:0.5px solid var(--border-color);
                           border-radius:6px; padding:3px 10px; font-size:11px;
                           color:var(--text-muted); cursor:pointer;">
                ↻ Refresh
            </button>
        </div>
    </div>

    {{-- ── Overdue alert banner ── --}}
    @if ($overdue > 0)
        <div
            style="margin:12px 1.25rem 0; background:#FAEEDA; border:0.5px solid #EF9F27;
                border-radius:8px; padding:8px 12px;
                display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:12px; color:#854F0B; font-weight:500;">
                &#9888;
                {{ $overdue }} consignment{{ $overdue > 1 ? 's' : '' }}
                with overdue ETA and no cost entry
            </span>
            <span onclick="window.DashboardApp.openDisbursementsDrawer()"
                style="background:#EF9F27; color:#412402; font-size:11px;
                     font-weight:600; border-radius:10px; padding:2px 10px;
                     cursor:pointer;">
                {{ $overdue }} overdue →
            </span>
        </div>
    @endif

    <div style="padding:1rem 1.25rem;">

        @if ($summary->total > 0 || $summary->gatedOut > 0)
            {{-- ── Headline ── --}}
            <div style="margin-bottom:14px;">
                <div
                    style="font-size:11px; color:var(--text-muted); text-transform:uppercase;
                            letter-spacing:0.6px; margin-bottom:4px;">
                    No Cost Entry
                </div>
                <div style="font-size:22px; font-weight:700; color:#185FA5; line-height:1;">
                    {{ number_format($summary->total) }}
                    <span style="font-size:14px; font-weight:400; color:var(--text-muted);">
                        consignment{{ $summary->total != 1 ? 's' : '' }}
                    </span>
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                    overdue ETA — awaiting disbursement entry
                </div>
            </div>

            {{-- ── Breakdown cards ── --}}
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">

                {{-- Arrived (Status=1, ETA overdue, no expenditure) --}}
                <div style="background:#FBF0DA; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#92600E; margin-bottom:4px;">
                        {{ number_format($summary->arrived) }}
                    </div>
                    <div
                        style="font-size:11px; color:#92600E; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        Arrived
                    </div>
                </div>

                {{-- In Harbor (Status=2, ETA overdue, no expenditure) --}}
                <div style="background:#FCEBEB; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#A32D2D; margin-bottom:4px;">
                        {{ number_format($summary->inHarbor) }}
                    </div>
                    <div
                        style="font-size:11px; color:#A32D2D; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        In Harbor
                    </div>
                </div>

                {{-- Gated Out (Status=3, has expenditure — compliance monitoring) --}}
                <div style="background:#EAF3DE; border-radius:8px; padding:10px 12px;">
                    <div style="font-size:14px; font-weight:600; color:#3B6D11; margin-bottom:4px;">
                        {{ number_format($summary->gatedOut) }}
                    </div>
                    <div
                        style="font-size:11px; color:#3B6D11; text-transform:uppercase;
                                letter-spacing:0.4px;">
                        Gated Out
                    </div>
                </div>

            </div>
        @else
            {{-- ── Empty state ── --}}
            <div style="padding:18px 0; text-align:center;">
                <div style="font-size:18px; font-weight:700; color:#3B6D11; margin-bottom:4px;">
                    ✓
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin:0;">
                    All owned consignments have disbursement entries.
                </p>
            </div>
        @endif

    </div>

</div>
