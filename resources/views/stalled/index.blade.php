@extends('layouts.app')

@section('title', 'Needs Attention')

@section('content')

    <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 1.25rem;">
        <h1 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary);">Needs Attention</h1>
        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $total }} consignment(s) waiting</span>
    </div>

    @if (!$total)
        <div class="card" style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
            Nothing is stuck. Everything is moving.
        </div>
    @endif

    @foreach ([
            'type' => ['Type not confirmed', 'Arrived, nobody has said whether this is LCL or FCL'],
            'return' => ['Container overdue', 'Gated out, container not returned'],
            'gateout' => ['Gate-out overdue', 'Disbursed, still at the port'],
            'manifest' => ['Manifest not broken down', 'LCL arrived, no house BLs entered'],
            'disbursement' => ['Disbursement overdue', 'Arrived, no disbursement raised'],
        ] as $stage => $meta)
        @continue(empty($groups[$stage]))

        <div class="card" style="margin-bottom: 1.25rem; padding: 0;">

            <div style="padding: 14px 18px; border-bottom: 1px solid var(--border-color);">
                <p style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">
                    {{ $meta[0] }}
                    <span style="color: var(--text-muted); font-weight: 500;">— {{ count($groups[$stage]) }}</span>
                </p>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $meta[1] }}</p>
            </div>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--content-bg);">
                        <th
                            style="text-align: left; padding: 8px 18px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-muted);">
                            BL</th>
                        <th
                            style="text-align: left; padding: 8px 12px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-muted);">
                            Consignee</th>
                        <th
                            style="text-align: left; padding: 8px 12px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-muted);">
                            Vessel</th>
                        <th
                            style="text-align: right; padding: 8px 12px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-muted);">
                            Waiting</th>
                        <th
                            style="text-align: right; padding: 8px 18px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-muted);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups[$stage] as $item)
                        <tr style="border-top: 1px solid var(--border-color);"
                            id="stall-{{ $item['ConsignmentID'] }}-{{ $stage }}" data-row="{{ $stage }}"
                            data-idx="{{ $loop->index }}">
                            <td
                                style="padding: 10px 18px; font-family: monospace; font-size: 0.8rem; color: var(--text-primary);">
                                {{ $item['BL'] }}</td>
                            <td style="padding: 10px 12px; font-size: 0.8rem; color: var(--text-primary);">
                                {{ $item['ConsigneeName'] }}</td>
                            <td style="padding: 10px 12px; font-size: 0.8rem; color: var(--text-muted);">
                                {{ $item['VesselName'] ?: '—' }}</td>
                            <td
                                style="padding: 10px 12px; text-align: right; font-size: 0.8rem; font-weight: 600; {{ $item['Days'] > 7 ? 'color: #b91c1c;' : 'color: var(--text-primary);' }}">
                                {{ $item['Days'] }}d
                            </td>
                            <td style="padding: 10px 18px; text-align: right; white-space: nowrap;">
                                <span class="claim-slot" data-key="{{ $item['ConsignmentID'] }}-{{ $stage }}"
                                    data-bl="{{ $item['BL'] }}" data-cid="{{ $item['ConsignmentID'] }}"
                                    data-stage="{{ $stage }}">
                                    @if ($item['ClaimedBy'])
                                        <span
                                            style="font-size: 0.75rem; color: {{ $item['GoneQuiet'] ? '#b45309' : 'var(--text-muted)' }};">
                                            {{ $item['ClaimedBy'] }}{{ $item['GoneQuiet'] ? ' — gone quiet' : '' }}
                                        </span>
                                        @if ($item['CanRelease'])
                                            <button
                                                onclick="releaseStall({{ $item['ConsignmentID'] }}, '{{ $item['BL'] }}', '{{ $stage }}')"
                                                style="margin-left: 6px; padding: 5px 11px; border-radius: 6px; border: 1px solid var(--border-color); background: transparent; color: var(--text-muted); font-size: 0.75rem; cursor: pointer;">
                                                Release
                                            </button>
                                        @endif
                                    @else
                                        <button
                                            onclick="claimStall({{ $item['ConsignmentID'] }}, '{{ $item['BL'] }}', '{{ $stage }}')"
                                            style="padding: 5px 11px; border-radius: 6px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 0.75rem; cursor: pointer;">
                                            I'm on this
                                        </button>
                                    @endif
                                </span>
                                @if ($stage === 'type')
                                    <button
                                        onclick="confirmType(this, {{ $item['ConsignmentID'] }}, '{{ $item['BL'] }}', 'LCL')"
                                        style="margin-left: 6px; padding: 5px 11px; border-radius: 6px; border: none; background: #185FA5; color: #fff; font-size: 0.75rem; cursor: pointer;">
                                        LCL
                                    </button>
                                    <button
                                        onclick="confirmType(this, {{ $item['ConsignmentID'] }}, '{{ $item['BL'] }}', 'FCL')"
                                        style="margin-left: 6px; padding: 5px 11px; border-radius: 6px; border: 1px solid #185FA5; background: transparent; color: #185FA5; font-size: 0.75rem; cursor: pointer;">
                                        FCL
                                    </button>
                                @else
                                    <button onclick="startStall(this, '{{ $stage }}', '{{ $item['BL'] }}')"
                                        style="margin-left: 6px; padding: 5px 11px; border-radius: 6px; border: none; background: #185FA5; color: #fff; font-size: 0.75rem; cursor: pointer;">
                                        Start
                                    </button>
                                @endif
                            </td>
                        </tr>
                        <tr id="reply-{{ $item['ConsignmentID'] }}-{{ $stage }}" style="display: none;"
                            data-reply="{{ $stage }}" data-idx="{{ $loop->index }}">
                            <td colspan="5" style="padding: 0 18px 12px; font-size: 0.8rem; color: var(--text-muted);">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (count($groups[$stage]) > 10)
                <div
                    style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 12px 18px; border-top: 1px solid var(--border-color);">
                    <span id="pager-label-{{ $stage }}"
                        style="font-size: 0.875rem; color: var(--text-muted);"></span>
                    <button onclick="pageStall('{{ $stage }}', -1)" id="pager-prev-{{ $stage }}"
                        style="padding: 6px 14px; border-radius: 6px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">Prev</button>
                    <button onclick="pageStall('{{ $stage }}', 1)" id="pager-next-{{ $stage }}"
                        style="padding: 6px 14px; border-radius: 6px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">Next</button>
                </div>
            @endif
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        const STALL = {
            claim: '{{ route('stalled.claim') }}',
            release: '{{ route('stalled.release') }}',
            confirmType: '{{ route('consignment-type.confirm') }}',
            csrf: '{{ csrf_token() }}',
            me: '{{ auth()->user()->ID }}',
        };

        const STAGE_URLS = {
            manifest: '{{ route('manifest.index') }}',
            disbursement: '{{ route('disbursement.analysis.index') }}',
        };

        const PER_PAGE = 10;
        const stallPage = {};

        window.pageStall = function(stage, step) {
            const rows = document.querySelectorAll(`tr[data-row="${stage}"]`);
            const last = Math.max(0, Math.ceil(rows.length / PER_PAGE) - 1);

            stallPage[stage] = Math.min(last, Math.max(0, (stallPage[stage] ?? 0) + step));

            const from = stallPage[stage] * PER_PAGE;
            const to = from + PER_PAGE;

            rows.forEach(r => {
                const i = Number(r.dataset.idx);
                r.style.display = (i >= from && i < to) ? '' : 'none';
            });

            document.querySelectorAll(`tr[data-reply="${stage}"]`).forEach(r => r.style.display = 'none');

            const label = document.getElementById(`pager-label-${stage}`);
            if (label) {
                label.textContent = `${from + 1}–${Math.min(to, rows.length)} of ${rows.length}`;
            }

            const prev = document.getElementById(`pager-prev-${stage}`);
            const next = document.getElementById(`pager-next-${stage}`);
            if (prev) prev.disabled = stallPage[stage] === 0;
            if (next) next.disabled = stallPage[stage] === last;
        };

        document.addEventListener('DOMContentLoaded', () => {
            ['type', 'manifest', 'return', 'gateout', 'disbursement'].forEach(s => {
                if (document.querySelector(`tr[data-row="${s}"]`)) pageStall(s, 0);
            });
        });

        // ── Claims ──────────────────────────────────────────────────────────

        window.claimStall = function(consignmentId, bl, stage) {
            const slot = document.querySelector(`.claim-slot[data-key="${consignmentId}-${stage}"]`);
            slot.innerHTML = '<span style="font-size:0.75rem;color:var(--text-muted);">Saving…</span>';

            fetch(STALL.claim, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': STALL.csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ConsignmentID: consignmentId,
                        BL: bl,
                        Stage: stage
                    }),
                })
                .then(r => r.json())
                .then(d => {
                    slot.innerHTML = d.success ?
                        claimedMarkup(d.ClaimedBy, consignmentId, bl, stage) :
                        '<span style="font-size:0.75rem;color:#b91c1c;">Could not save</span>';
                })
                .catch(() => {
                    slot.innerHTML = '<span style="font-size:0.75rem;color:#b91c1c;">Could not save</span>';
                });
        };

        function claimedMarkup(who, consignmentId, bl, stage) {
            return '<span style="font-size:0.75rem;color:var(--text-muted);">' + escapeHtml(who) + '</span>' +
                '<button onclick="releaseStall(' + consignmentId + ', \'' + bl + '\', \'' + stage + '\')" ' +
                'style="margin-left:6px;padding:5px 11px;border-radius:6px;border:1px solid var(--border-color);' +
                'background:transparent;color:var(--text-muted);font-size:0.75rem;cursor:pointer;">Release</button>';
        }

        window.releaseStall = function(consignmentId, bl, stage) {
            const slot = document.querySelector(`.claim-slot[data-key="${consignmentId}-${stage}"]`);
            slot.innerHTML = '<span style="font-size:0.75rem;color:var(--text-muted);">Releasing…</span>';

            fetch(STALL.release, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': STALL.csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ConsignmentID: consignmentId,
                        BL: bl,
                        Stage: stage
                    }),
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        slot.innerHTML = '<button onclick="claimStall(' + consignmentId + ', \'' + bl +
                            '\', \'' + stage + '\')" ' +
                            'style="padding:5px 11px;border-radius:6px;border:1px solid var(--border-color);' +
                            'background:transparent;color:var(--text-primary);font-size:0.75rem;cursor:pointer;">I\'m on this</button>';
                    } else {
                        slot.innerHTML = '<span style="font-size:0.75rem;color:#b91c1c;">' +
                            escapeHtml(d.message || 'Could not release') + '</span>';
                    }
                })
                .catch(() => {
                    slot.innerHTML = '<span style="font-size:0.75rem;color:#b91c1c;">Could not release</span>';
                });
        };

        // ── Actions ─────────────────────────────────────────────────────────

        // Each stage finishes on its own screen — take the user there.
        window.startStall = function(btn, stage, bl) {
            const base = STAGE_URLS[stage];

            if (!base) {
                showStallNote(btn, 'There is no screen wired up for this step yet.');
                return;
            }

            window.location.href = `${base}?bl=${encodeURIComponent(bl)}`;
        };

        window.confirmType = function(btn, consignmentId, bl, type) {
            const buttons = btn.closest('tr').querySelectorAll('button');
            buttons.forEach(b => b.disabled = true);

            fetch(STALL.confirmType, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': STALL.csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ConsignmentID: consignmentId,
                        BL: bl,
                        Type: type
                    }),
                })
                .then(r => r.json())
                .then(d => {
                    if (!d.success) {
                        buttons.forEach(b => b.disabled = false);
                        showStallNote(btn, d.message || 'Could not save.');
                        return;
                    }

                    // Confirmed — the consignment now belongs to a different stage.
                    startStall(btn, d.NextStage, bl);
                })
                .catch(() => {
                    buttons.forEach(b => b.disabled = false);
                    showStallNote(btn, 'Could not reach the server.');
                });
        };

        function showStallNote(btn, text) {
            const reply = btn.closest('tr').nextElementSibling;
            reply.style.display = '';
            reply.querySelector('td').textContent = text;
        }

        function escapeHtml(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }
    </script>
@endpush
