<div class="container-accordion">

    @if ($containers->isEmpty())
        <p
            style="font-size:var(--db-text-sm); color:var(--text-muted);
                  text-align:center; padding:1rem 0;">
            No containers found for this consignment.
        </p>
    @else
        @foreach ($containers as $container)
            @php
                $isReturned = $container->Status == 4 && $container->ReturnDate !== null;
                $gateOutDate = $container->GateOutDate
                    ? \Carbon\Carbon::parse($container->GateOutDate)->format('d M Y')
                    : '—';
                $returnDate = $container->ReturnDate
                    ? \Carbon\Carbon::parse($container->ReturnDate)->format('d M Y')
                    : null;

                // Days since gate-out — colour coding
                $daysSinceGateOut = $container->GateOutDate
                    ? (int) \Carbon\Carbon::parse($container->GateOutDate)->diffInDays(now())
                    : null;

                if ($daysSinceGateOut === null || $isReturned) {
                    $daysColor = 'var(--text-muted)';
                    $daysText = $isReturned ? 'Returned' : '—';
                } elseif ($daysSinceGateOut <= 2) {
                    $daysColor = '#15803d';
                    $daysText = $daysSinceGateOut . 'd out';
                } elseif ($daysSinceGateOut <= 7) {
                    $daysColor = '#92400e';
                    $daysText = $daysSinceGateOut . 'd out';
                } else {
                    $daysColor = '#b91c1c';
                    $daysText = $daysSinceGateOut . 'd out';
                }

                $containerNo = e($container->ContainerNo);
                $containerSize = e($container->ContainerSize ?? '—');
            @endphp

            <div class="container-row">

                {{-- Left: container info --}}
                <div style="display:flex; flex-direction:column; gap:2px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="container-no">{{ $containerNo }}</span>
                        <span
                            style="font-size:0.72rem; font-weight:600;
                                     background:var(--border-color);
                                     color:var(--text-muted);
                                     border-radius:4px; padding:1px 7px;">
                            {{ $containerSize }}
                        </span>
                        @if ($isReturned)
                            <span
                                style="font-size:0.72rem; font-weight:700;
                                         background:#dcfce7; color:#15803d;
                                         border-radius:4px; padding:1px 7px;">
                                ✓ Returned
                            </span>
                        @endif
                    </div>
                    <div style="font-size:var(--db-text-xs); color:var(--text-muted);">
                        Gated out: {{ $gateOutDate }}
                        @if ($isReturned && $returnDate)
                            &nbsp;·&nbsp; Returned: {{ $returnDate }}
                        @else
                            &nbsp;·&nbsp;
                            <span style="color:{{ $daysColor }}; font-weight:600;">
                                {{ $daysText }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right: action --}}
                <div style="flex-shrink:0;">
                    @if (!$isReturned)
                        <button class="tracker-action-btn btn-return"
                            onclick="window.DashboardApp.containerClear(
                                {{ $consignmentId }},
                                '{{ $containerNo }}',
                                this
                            )">
                            Mark Returned
                        </button>
                    @else
                        <span
                            style="font-size:var(--db-text-xs);
                                     color:var(--text-muted);">
                            {{ $returnDate }}
                        </span>
                    @endif
                </div>

            </div>
        @endforeach
    @endif

</div>
