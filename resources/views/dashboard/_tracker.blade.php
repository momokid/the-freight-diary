@php
    $urgentCount = collect($left['rows'])
        ->filter(fn($r) => !is_null($r->ETADays) && (int) $r->ETADays >= 0 && (int) $r->ETADays <= 3)
        ->count();
@endphp

<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:1rem; font-weight:700; color:var(--text-primary); margin:0;">
            Consignment Tracker
        </p>
        <button onclick="window.DashboardApp.loadWidget('tracker')"
            style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:12px;
                       color:var(--text-muted); cursor:pointer;">
            ↻ Refresh
        </button>
    </div>

    {{-- ── ETA alert banner ── --}}
    @if ($urgentCount > 0)
        <div
            style="margin:12px 1.25rem 0; background:#FAEEDA; border:0.5px solid #EF9F27;
                border-radius:8px; padding:8px 12px;
                display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:13px; color:#854F0B; font-weight:500;">
                &#9888;
                {{ $urgentCount }} consignment{{ $urgentCount > 1 ? 's' : '' }}
                arriving within 3 days — update ETA if needed
            </span>
            <span
                style="background:#EF9F27; color:#412402; font-size:12px;
                     font-weight:600; border-radius:10px; padding:2px 8px;">
                {{ $urgentCount }} urgent
            </span>
        </div>
    @endif

    {{-- ── Two-panel layout: left 30% / right 70% ── --}}
    <div style="display:grid; grid-template-columns:7fr 3fr; gap:0; padding:1rem 1.25rem;">

        {{-- ── Left panel — pending consignments (30%) ── --}}
        <div style="padding-right:16px; border-right:0.5px solid var(--border-color);">

            {{-- Panel heading + View All link --}}
            <div
                style="display:flex; align-items:center; justify-content:space-between;
                       margin-bottom:10px;">
                <p
                    style="font-size:13px; font-weight:600; color:var(--text-muted);
                          text-transform:uppercase; letter-spacing:0.6px; margin:0;">
                    Pending
                    <span style="color:var(--text-primary);">({{ $left['total'] }})</span>
                </p>
                <span onclick="window.DashboardApp.openPendingDrawer()"
                    style="font-size:12px; color:#185FA5; font-weight:600;
                        cursor:pointer; white-space:nowrap;">
                    View All →
                </span>
            </div>

            @if (count($left['rows']) > 0)
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th
                                style="padding:7px 8px; font-size:12px; text-align:left;
                                       font-weight:600; letter-spacing:0.3px;">
                                BL / Consignee
                            </th>
                            <th
                                style="padding:7px 8px; font-size:12px; text-align:center;
                                       font-weight:600; letter-spacing:0.3px;">
                                ETA
                            </th>
                            <th
                                style="padding:7px 8px; font-size:12px; text-align:center;
                                       font-weight:600; letter-spacing:0.3px;">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($left['rows'] as $row)
                            @php
                                $days = is_null($row->ETADays) ? null : (int) $row->ETADays;

                                [$etaStyle, $etaText] = match (true) {
                                    is_null($days) => ['color:#aaa; font-style:italic;', 'No ETA'],
                                    $days < 0 => ['color:#A32D2D; font-weight:600;', abs($days) . 'd overdue'],
                                    $days === 0 => ['color:#3B6D11; font-weight:600;', 'Today'],
                                    $days <= 3 => ['color:#854F0B; font-weight:600;', $days . 'd'],
                                    default => ['color:var(--text-muted);', $days . 'd'],
                                };

                                [$statusBg, $statusLabel] =
                                    (int) $row->ETADays < 0
                                        ? ['background:#E6F1FB; color:#0C447C;', 'In Harbor']
                                        : ['background:#FAEEDA; color:#854F0B;', 'Not Arrived'];
                            @endphp
                            <tr style="border-bottom:0.5px solid var(--border-color);">

                                <td style="padding:7px 8px;">
                                    <div
                                        style="font-family:monospace; font-size:13px;
                                               color:var(--text-primary);">
                                        {{ $row->BL }}
                                    </div>
                                    <div style="font-size:12px; color:var(--text-muted); margin-top:1px;">
                                        {{ Str::limit($row->ConsigneeName, 20) }}
                                    </div>
                                    @if ($row->Destination)
                                        <div style="font-size:11px; color:var(--text-muted);">
                                            {{ $row->Destination }}
                                        </div>
                                    @endif
                                    <span
                                        style="display:inline-block; padding:1px 6px; border-radius:8px;
                                               font-size:11px; font-weight:600; margin-top:2px;
                                               {{ $statusBg }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td
                                    style="padding:7px 8px; font-size:13px; text-align:center;
                                           white-space:nowrap; {{ $etaStyle }}">
                                    {{ $etaText }}
                                </td>

                                <td style="padding:7px 8px; text-align:center;">
                                    @if ($row->DisbursementApproved)
                                        <button
                                            onclick="window.DashboardApp.gateOut(
                                                {{ $row->ConsignmentID }},
                                                {{ json_encode($row->BL) }},
                                                this)"
                                            style="background:#185FA5; color:#fff; border:none;
                                                   border-radius:5px; padding:4px 8px;
                                                   font-size:12px; font-weight:600; cursor:pointer;">
                                            Gate-Out
                                        </button>
                                    @else
                                        <span style="font-size:12px; color:#bbb;">Pending disb.</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p
                    style="font-size:14px; color:var(--text-muted); text-align:center;
                           padding:24px 0;">
                    No pending consignments.
                </p>
            @endif

            {{-- Left pagination --}}
            @if ($left['lastPage'] > 1)
                <div
                    style="display:flex; align-items:center; justify-content:center;
                           gap:8px; margin-top:10px;">
                    <button
                        onclick="window.DashboardApp.trackerPage(
                            {{ max(1, $left['page'] - 1) }},
                            {{ $right['page'] }})"
                        {{ $left['page'] <= 1 ? 'disabled' : '' }}
                        style="background:none; border:0.5px solid var(--border-color);
                               border-radius:4px; padding:3px 8px; font-size:13px;
                               cursor:pointer; color:var(--text-muted);">
                        &#8592;
                    </button>
                    <span style="font-size:13px; color:var(--text-muted);">
                        {{ $left['page'] }} of {{ $left['lastPage'] }}
                    </span>
                    <button
                        onclick="window.DashboardApp.trackerPage(
                            {{ min($left['lastPage'], $left['page'] + 1) }},
                            {{ $right['page'] }})"
                        {{ $left['page'] >= $left['lastPage'] ? 'disabled' : '' }}
                        style="background:none; border:0.5px solid var(--border-color);
                               border-radius:4px; padding:3px 8px; font-size:13px;
                               cursor:pointer; color:var(--text-muted);">
                        &#8594;
                    </button>
                </div>
            @endif

        </div>{{-- end left panel --}}

        {{-- ── Right panel — gated out containers (70%) ── --}}
        <div style="padding-left:16px;">

            <p
                style="font-size:13px; font-weight:600; color:var(--text-muted);
                      text-transform:uppercase; letter-spacing:0.6px; margin:0 0 10px;">
                Gated Out — Awaiting Return
                <span style="color:var(--text-primary);">({{ $right['total'] }})</span>
            </p>

            @if (count($right['rows']) > 0)

                @foreach ($right['rows'] as $row)
                    <div
                        style="display:flex; align-items:center; justify-content:space-between;
                               padding:8px 0; border-bottom:0.5px solid var(--border-color);">
                        <div style="min-width:0; flex:1;">
                            <div
                                style="font-size:13px; color:var(--text-primary);
                                       font-family:monospace;">
                                {{ $row->BL }}
                                <span style="color:#E24B4A; font-weight:700;">
                                    [{{ $row->ContainerNo }}]
                                </span>
                            </div>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                                {{ $row->Destination ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ $row->ContainerSize ?? '—' }}
                            </div>
                            <div style="font-size:12px; color:var(--text-muted);">
                                Out:
                                {{ $row->GateOutDate ? date('d M Y', strtotime($row->GateOutDate)) : '—' }}
                            </div>
                        </div>
                        <button
                            onclick="window.DashboardApp.containerClear(
                                {{ $row->ConsignmentID }},
                                {{ json_encode($row->ContainerNo) }},
                                this)"
                            style="background:#EF9F27; color:#412402; border:none;
                                   border-radius:5px; padding:4px 10px; font-size:12px;
                                   font-weight:600; cursor:pointer;
                                   flex-shrink:0; margin-left:10px;">
                            Clear
                        </button>
                    </div>
                @endforeach

                {{-- Right pagination --}}
                @if ($right['lastPage'] > 1)
                    <div
                        style="display:flex; align-items:center; justify-content:center;
                               gap:8px; margin-top:10px;">
                        <button
                            onclick="window.DashboardApp.trackerPage(
                                {{ $left['page'] }},
                                {{ max(1, $right['page'] - 1) }})"
                            {{ $right['page'] <= 1 ? 'disabled' : '' }}
                            style="background:none; border:0.5px solid var(--border-color);
                                   border-radius:4px; padding:3px 8px; font-size:13px;
                                   cursor:pointer; color:var(--text-muted);">
                            &#8592;
                        </button>
                        <span style="font-size:13px; color:var(--text-muted);">
                            {{ $right['page'] }} of {{ $right['lastPage'] }}
                        </span>
                        <button
                            onclick="window.DashboardApp.trackerPage(
                                {{ $left['page'] }},
                                {{ min($right['lastPage'], $right['page'] + 1) }})"
                            {{ $right['page'] >= $right['lastPage'] ? 'disabled' : '' }}
                            style="background:none; border:0.5px solid var(--border-color);
                                   border-radius:4px; padding:3px 8px; font-size:13px;
                                   cursor:pointer; color:var(--text-muted);">
                            &#8594;
                        </button>
                    </div>
                @endif
            @else
                <p
                    style="font-size:14px; color:var(--text-muted); text-align:center;
                           padding:24px 0;">
                    No containers awaiting return.
                </p>
            @endif

        </div>{{-- end right panel --}}

    </div>{{-- end two-panel grid --}}

</div>
