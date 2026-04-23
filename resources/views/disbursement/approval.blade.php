@extends('layouts.app')

@section('title', 'Disbursement Approval')
@section('page-title', 'Disbursement Approval — In-Harbor')

@section('content')

    <div id="approval-list" style="display: flex; flex-direction: column; gap: 1.25rem;">

        @forelse($disbursements as $disbursement)
            <div class="card border-l-4 border-green-500" id="bl-card-{{ str_replace(' ', '_', $disbursement['BL']) }}"
                data-bl="{{ $disbursement['BL'] }}">

                {{-- ── Card Header ── --}}
                <div
                    style="display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span
                            style="font-size: 1rem; font-weight: 700; color: var(--text-primary);
                    font-family: monospace;">
                            {{ $disbursement['BL'] }}
                        </span>
                        <span
                            style="font-size: 0.7rem; font-weight: 700; padding: 3px 10px;
                    border-radius: 20px; background: rgba(234,179,8,0.12);
                    color: #b45309; border: 1px solid rgba(234,179,8,0.3);">
                            {{ $disbursement['Type'] }}
                        </span>
                        <span style="font-size: 0.78rem; color: var(--text-muted);">
                            {{ $disbursement['ConsigneeName'] }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 0.78rem; color: var(--text-muted);">Total:</span>
                        <span style="font-size: 0.95rem; font-weight: 700; color: #dc2626;">
                            GH₵ {{ number_format($disbursement['total'], 2) }}
                        </span>
                    </div>
                </div>

                {{-- ── Containers (collapsible) ── --}}
                <div style="margin-bottom: 1rem;">
                    <button onclick="toggleContainers('{{ $disbursement['BL'] }}')"
                        style="display: flex; align-items: center; gap: 0.5rem; background: none;
                    border: none; cursor: pointer; color: var(--text-muted); font-size: 0.78rem;
                    padding: 0; margin-bottom: 0.5rem;">
                        <span id="container-icon-{{ $disbursement['BL'] }}"
                            style="font-size: 1rem; transition: transform 0.2s;">➕</span>
                        <span>CONSIGNMENT DETAILS</span>
                    </button>

                    <div id="containers-{{ $disbursement['BL'] }}" style="display: none;">
                        <table class="data-table" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>CONTAINER #</th>
                                    <th>SIZE</th>
                                    <th>WEIGHT</th>
                                    <th style="text-align: right;">HANDL. COST</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disbursement['containers'] as $container)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 600;">
                                            {{ $container->ContainerNo }}
                                        </td>
                                        <td>{{ $container->ContainerSize }}</td>
                                        <td>{{ number_format($container->Weight, 2) }}</td>
                                        <td style="text-align: right;">
                                            GH₵ {{ number_format($container->HandlingCost, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            style="text-align: center;
                                color: var(--text-muted); padding: 1rem;">
                                            No container details found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── Expenditure Details ── --}}
                @if ($disbursement['Type'] === 'FCL')
                    {{-- FCL — direct rows --}}
                    <table class="data-table" style="font-size: 0.82rem; margin-bottom: 1rem;">
                        <thead>
                            <tr>
                                <th>ACCOUNT NAME</th>
                                <th style="text-align: right;">EXPENDITURE</th>
                                <th>T. DATE</th>
                                <th>USERNAME</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disbursement['entries'] as $entry)
                                <tr>
                                    <td style="font-weight: 500;">{{ $entry->AccountName }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #dc2626;">
                                        GH₵ {{ number_format($entry->Expenditure, 2) }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($entry->Date)->format('M d, Y') }}</td>
                                    <td style="font-family: monospace; font-size: 0.78rem;">
                                        {{ $entry->Username }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- LCL — account totals with View HBLs button --}}
                    <table class="data-table" style="font-size: 0.82rem; margin-bottom: 1rem;">
                        <thead>
                            <tr>
                                <th>ACCOUNT NAME</th>
                                <th style="text-align: right;">TOTAL EXPENDITURE</th>
                                <th style="text-align: center;">HBL BREAKDOWN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disbursement['accountTotals'] as $accountTotal)
                                <tr>
                                    <td style="font-weight: 500;">{{ $accountTotal['AccountName'] }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #dc2626;">
                                        GH₵ {{ number_format($accountTotal['Total'], 2) }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button
                                            onclick="viewHBLs('{{ $disbursement['BL'] }}', {{ $accountTotal['AccountID'] }}, '{{ addslashes($accountTotal['AccountName']) }}')"
                                            style="padding: 4px 12px; font-size: 0.75rem; border-radius: 5px;
                                    background: #2563eb; color: white; border: none; cursor: pointer;">
                                            View HBLs
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- ── Action Buttons ── --}}
                <div
                    style="display: flex; gap: 0.75rem; padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);">
                    <button onclick="approveTransaction('{{ $disbursement['BL'] }}', this)"
                        style="padding: 8px 24px; border-radius: 6px; background: #16a34a;
                    color: white; border: none; cursor: pointer; font-size: 0.85rem;
                    font-weight: 600;">
                        ✓ Approve Transaction
                    </button>
                    <button onclick="declineTransaction('{{ $disbursement['BL'] }}', this)"
                        style="padding: 8px 24px; border-radius: 6px; background: #dc2626;
                    color: white; border: none; cursor: pointer; font-size: 0.85rem;
                    font-weight: 600;">
                        ✕ Decline Transaction
                    </button>
                </div>

            </div>
        @empty
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    ✓ No pending disbursements to review.
                </p>
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    All In-Harbor entries have been approved.
                </p>
            </div>
        @endforelse

    </div>

    {{-- ── Approve All Button ── --}}
    @if (count($disbursements) > 0)
        <div
            style="margin-top: 1.5rem; padding: 1.25rem; background: var(--card-bg);
    border-radius: 10px; border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">
                    Approve All Pending Transactions
                </p>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                    {{ count($disbursements) }} BL{{ count($disbursements) > 1 ? 's' : '' }} pending approval
                </p>
            </div>
            <button onclick="approveAll(this)"
                style="padding: 10px 32px; border-radius: 8px; background: #16a34a;
            color: white; border: none; cursor: pointer; font-size: 0.9rem;
            font-weight: 700;">
                ✓ Approve All
            </button>
        </div>
    @endif

    {{-- ── HBL Modal ── --}}
    <div id="hbl-modal"
        style="display: none; position: fixed; inset: 0; z-index: 50;
    align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
        <div class="card"
            style="width: 100%; max-width: 680px; margin: 1rem;
        max-height: 85vh; overflow-y: auto;">

            <div
                style="display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem;">
                <div>
                    <p class="form-title" id="modal-title">HBL Breakdown</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted);" id="modal-subtitle"></p>
                </div>
                <button onclick="closeModal()"
                    style="background: none; border: none; cursor: pointer;
                    color: var(--text-muted); font-size: 1.4rem; line-height: 1;">
                    ✕
                </button>
            </div>

            <div id="modal-loading"
                style="text-align: center; padding: 2rem;
            color: var(--text-muted); font-size: 0.85rem;">
                Loading...
            </div>

            <table class="data-table" id="modal-table" style="font-size: 0.82rem; display: none;">
                <thead>
                    <tr>
                        <th>HOUSE BL</th>
                        <th>CONSIGNEE</th>
                        <th style="text-align: right;">EXPENDITURE</th>
                        <th>DATE</th>
                        <th>USERNAME</th>
                    </tr>
                </thead>
                <tbody id="modal-tbody"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="font-weight: 700; padding-top: 0.5rem;">
                            TOTAL
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #dc2626;
                        padding-top: 0.5rem;"
                            id="modal-total"></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            <p id="modal-error"
                style="display: none; text-align: center;
            color: #dc2626; font-size: 0.85rem; padding: 1rem;">
            </p>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const ROUTES = {
            approve: '{{ route('disbursement.approval.approve') }}',
            decline: '{{ route('disbursement.approval.decline') }}',
            approveAll: '{{ route('disbursement.approval.approve-all') }}',
            hbls: '{{ route('disbursement.approval.hbls') }}',
        };

        // ── Expose to global scope ────────────────────────────────────────────────────
        window.toggleContainers = toggleContainers;
        window.viewHBLs = viewHBLs;
        window.closeModal = closeModal;
        window.approveTransaction = approveTransaction;
        window.declineTransaction = declineTransaction;
        window.approveAll = approveAll;

        // ── Toggle Containers ─────────────────────────────────────────────────────────
        function toggleContainers(bl) {
            const panel = document.getElementById(`containers-${bl}`);
            const icon = document.getElementById(`container-icon-${bl}`);
            const isHidden = panel.style.display === 'none';
            panel.style.display = isHidden ? 'block' : 'none';
            icon.textContent = isHidden ? '➖' : '➕';
        }

        // ── View HBLs Modal ───────────────────────────────────────────────────────────
        function viewHBLs(bl, accountId, accountName) {
            // Show modal
            const modal = document.getElementById('hbl-modal');
            modal.style.display = 'flex';

            document.getElementById('modal-title').textContent = accountName;
            document.getElementById('modal-subtitle').textContent = `BL# ${bl}`;
            document.getElementById('modal-loading').style.display = 'block';
            document.getElementById('modal-table').style.display = 'none';
            document.getElementById('modal-error').style.display = 'none';

            fetch(`${ROUTES.hbls}?BL=${encodeURIComponent(bl)}&AccountID=${accountId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                })
                .then(r => r.json())
                .then(rows => {
                    document.getElementById('modal-loading').style.display = 'none';

                    if (!rows || rows.length === 0) {
                        document.getElementById('modal-error').textContent = 'No HBL entries found.';
                        document.getElementById('modal-error').style.display = 'block';
                        return;
                    }

                    const tbody = document.getElementById('modal-tbody');
                    tbody.innerHTML = '';

                    let total = 0;
                    rows.forEach(row => {
                        total += parseFloat(row.Expenditure) || 0;
                        tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td style="font-family: monospace; font-weight: 700;">${row.HBL}</td>
                    <td style="font-size: 0.78rem;">${row.ConsigneeName}</td>
                    <td style="text-align: right; font-weight: 600; color: #dc2626;">
                        GH₵ ${parseFloat(row.Expenditure).toFixed(2)}
                    </td>
                    <td>${formatDate(row.Date)}</td>
                    <td style="font-family: monospace; font-size: 0.78rem;">${row.Username}</td>
                </tr>
            `);
                    });

                    document.getElementById('modal-total').textContent = `GH₵ ${total.toFixed(2)}`;
                    document.getElementById('modal-table').style.display = '';
                })
                .catch(() => {
                    document.getElementById('modal-loading').style.display = 'none';
                    document.getElementById('modal-error').textContent = 'Failed to load HBL details.';
                    document.getElementById('modal-error').style.display = 'block';
                });
        }

        function closeModal() {
            document.getElementById('hbl-modal').style.display = 'none';
        }

        // Close modal on backdrop click
        document.getElementById('hbl-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // ── Approve Transaction ───────────────────────────────────────────────────────
        function approveTransaction(bl, btn) {
            if (!confirm(`Approve all disbursement entries for BL# ${bl}?`)) return;

            btn.textContent = 'Approving...';
            btn.disabled = true;

            fetch(ROUTES.approve, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: bl
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message ?? 'Approval failed.');
                        btn.textContent = '✓ Approve Transaction';
                        btn.disabled = false;
                        return;
                    }
                    removeCard(bl);
                    updatePendingCount(-1);
                })
                .catch(() => {
                    alert('Connection error. Please try again.');
                    btn.textContent = '✓ Approve Transaction';
                    btn.disabled = false;
                });
        }

        // ── Decline Transaction ───────────────────────────────────────────────────────
        function declineTransaction(bl, btn) {
            if (!confirm(`Decline and reverse all disbursement entries for BL# ${bl}? This cannot be undone.`)) return;

            btn.textContent = 'Declining...';
            btn.disabled = true;

            fetch(ROUTES.decline, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: bl
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message ?? 'Decline failed.');
                        btn.textContent = '✕ Decline Transaction';
                        btn.disabled = false;
                        return;
                    }
                    removeCard(bl);
                    updatePendingCount(-1);
                })
                .catch(() => {
                    alert('Connection error. Please try again.');
                    btn.textContent = '✕ Decline Transaction';
                    btn.disabled = false;
                });
        }

        // ── Approve All ───────────────────────────────────────────────────────────────
        function approveAll(btn) {
            if (!confirm('Approve ALL pending In-Harbor disbursements? This cannot be undone.')) return;

            btn.textContent = 'Approving All...';
            btn.disabled = true;

            fetch(ROUTES.approveAll, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({}),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message ?? 'Approve all failed.');
                        btn.textContent = '✓ Approve All';
                        btn.disabled = false;
                        return;
                    }

                    // Remove all cards and hide approve-all bar
                    document.querySelectorAll('[id^="bl-card-"]').forEach(card => {
                        card.remove();
                    });

                    // Show empty state
                    document.getElementById('approval-list').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    ✓ No pending disbursements to review.
                </p>
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    All In-Harbor entries have been approved.
                </p>
            </div>`;

                    // Hide approve all bar
                    btn.closest('div[style*="margin-top: 1.5rem"]').style.display = 'none';
                })
                .catch(() => {
                    alert('Connection error. Please try again.');
                    btn.textContent = '✓ Approve All';
                    btn.disabled = false;
                });
        }

        // ── Helpers ───────────────────────────────────────────────────────────────────
        function removeCard(bl) {
            const safeId = bl.replace(/\s+/g, '_');
            const card = document.getElementById(`bl-card-${safeId}`);
            if (card) {
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    checkEmpty();
                }, 300);
            }
        }

        function checkEmpty() {
            const list = document.getElementById('approval-list');
            if (list.children.length === 0) {
                list.innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    ✓ No pending disbursements to review.
                </p>
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    All In-Harbor entries have been approved.
                </p>
            </div>`;

                // Hide approve all bar
                const approveAllBar = document.querySelector('div[style*="margin-top: 1.5rem"]');
                if (approveAllBar) approveAllBar.style.display = 'none';
            }
        }

        function updatePendingCount(delta) {
            const bar = document.querySelector('div[style*="margin-top: 1.5rem"] p');
            if (!bar) return;
            const match = bar.textContent.match(/\d+/);
            if (match) {
                const newCount = parseInt(match[0]) + delta;
                if (newCount <= 0) {
                    const approveAllBar = document.querySelector('div[style*="margin-top: 1.5rem"]');
                    if (approveAllBar) approveAllBar.style.display = 'none';
                } else {
                    bar.textContent = `${newCount} BL${newCount > 1 ? 's' : ''} pending approval`;
                }
            }
        }

        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    </script>
@endpush
