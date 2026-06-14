@foreach ($rows as $row)
    @php
        $priority = 1;
        if ($row->Status == 1) {
            $priority = 3;
        } elseif ($row->Status == 2) {
            $priority = $row->HasDisbursement ? 1 : 2;
        } elseif ($row->Status == 3) {
            $priority = $row->ReturnedContainers > 0 ? 5 : 4;
        }

        $badgeClass = match ($priority) {
            1 => 'badge-green',
            2 => 'badge-red',
            3 => 'badge-blue',
            4 => 'badge-amber',
            5 => 'badge-purple',
        };

        $badgeLabel = match ($priority) {
            1 => 'Gate-Out Ready',
            2 => 'Pending Disbursement',
            3 => 'Not Arrived',
            4 => 'Gated Out',
            5 => 'Awaiting Return',
        };

        $eta = $row->ETA ? \Carbon\Carbon::parse($row->ETA)->format('d M Y') : '—';
        $etaDays = isset($row->ETADays) ? (int) $row->ETADays : null;

        // ETA text and colour
        if ($row->Status == 1 && $etaDays !== null) {
            if ($etaDays < 0) {
                $etaText = abs($etaDays) . 'd overdue';
                $etaColor = '#b91c1c';
            } elseif ($etaDays === 0) {
                $etaText = 'Arriving today';
                $etaColor = '#15803d';
            } elseif ($etaDays <= 3) {
                $etaText = 'In ' . $etaDays . 'd';
                $etaColor = '#92400e';
            } else {
                $etaText = 'In ' . $etaDays . 'd';
                $etaColor = 'var(--text-muted)';
            }
        } else {
            $etaText = $eta;
            $etaColor = 'var(--text-muted)';
        }

        // $registeredDate = $row->RegisteredDate ? \Carbon\Carbon::parse($row->RegisteredDate)->format('d M Y') : '—';

        $bl = e($row->BL);
        $consignmentId = (int) $row->ConsignmentID;
        $consignee = e($row->ConsigneeName);
        $destination = e($row->Destination ?? '—');
    @endphp

    <div class="tracker-card priority-{{ $priority }}">

        {{-- Top row: BL + badge + action --}}
        <div style="display:flex; align-items:flex-start;
                    justify-content:space-between; gap:8px;">
            <div style="flex:1; min-width:0;">
                <div class="tracker-bl">{{ $bl }}</div>
                <div class="tracker-consignee">{{ $consignee }}</div>
                <div class="tracker-meta">
                    {{ $destination }}
                    &nbsp;·&nbsp;
                    <span style="color:{{ $etaColor }}; font-weight:600;">
                        {{ $etaText }}
                    </span>
                    &nbsp;·&nbsp;
                    ETA: <span style="color:{{ $etaColor }}; font-weight:600;">{{ $eta }}</span>
                </div>
            </div>

            {{-- Status badge --}}
            <div style="flex-shrink:0; text-align:right;">
                <span class="tracker-badge {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            </div>
        </div>

        {{-- Action row --}}
        <div
            style="margin-top:0.625rem; display:flex;
                    align-items:center; justify-content:flex-end; gap:8px;">

            @if ($priority === 1)
                {{-- Gate-Out Ready --}}
                <button class="tracker-action-btn btn-gateout"
                    onclick="window.DashboardApp.gateOut(
                        {{ $consignmentId }}, '{{ $bl }}', this
                    )">
                    Gate Out →
                </button>
            @elseif($priority === 4 || $priority === 5)
                {{-- Gated Out or Awaiting Return — show containers --}}
                @if ($priority === 5)
                    <span style="font-size:0.72rem; color:var(--text-muted);">
                        {{ $row->ReturnedContainers }}/{{ $row->TotalContainers }} returned
                    </span>
                @endif
                <button class="tracker-action-btn btn-show"
                    onclick="window.DashboardApp.showContainers(
                        {{ $consignmentId }}, '{{ $bl }}', this
                    )">
                    Show Containers
                </button>
            @endif

        </div>

    </div>
@endforeach

@if (empty($rows))
    <p style="font-size:var(--db-text-sm); color:var(--text-muted);
              text-align:center; padding:2rem 0;">
        No active consignments requiring action.
    </p>
@endif
