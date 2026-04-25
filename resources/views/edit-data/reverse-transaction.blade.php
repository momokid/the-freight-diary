@extends('layouts.app')

@section('title', 'Reverse Transaction')
@section('page-title', 'Reverse Consignment Register / Cargo Manifest / User Transaction')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ── Three Search Panels ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">

            {{-- Reverse Consignment Register --}}
            @if (isset($userAuth) && $userAuth->hasPermission('ReverseConsignment'))
                <div class="card">
                    <p class="form-title" style="color: #dc2626;">Reverse Consignment Register</p>
                    <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                        <label class="form-label">Transaction No.</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="cns-bl-input" class="form-input" placeholder="Enter Main BL..."
                                style="text-transform: uppercase; flex: 1;" autocomplete="off"
                                onkeydown="if(event.key==='Enter') window.loadConsignment()">
                            <button onclick="window.loadConsignment()"
                                style="padding: 8px 16px; border-radius: 8px; border: none;
                               background: #dc2626; color: white; font-size: 0.8rem;
                               font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Search
                            </button>
                        </div>
                        <p id="cns-error" class="form-error"></p>
                    </div>
                </div>
            @endif

            {{-- Reverse Cargo Manifest --}}
            @if (isset($userAuth) && $userAuth->hasPermission('ReverseTransaction'))
                <div class="card">
                    <p class="form-title" style="color: #dc2626;">Reverse Cargo Manifest</p>
                    <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                        <label class="form-label">Transaction No.</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="mfst-bl-input" class="form-input" placeholder="Enter Main BL..."
                                style="text-transform: uppercase; flex: 1;" autocomplete="off"
                                onkeydown="if(event.key==='Enter') window.loadManifest()">
                            <button onclick="window.loadManifest()"
                                style="padding: 8px 16px; border-radius: 8px; border: none;
                               background: #dc2626; color: white; font-size: 0.8rem;
                               font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Search
                            </button>
                        </div>
                        <p id="mfst-error" class="form-error"></p>
                    </div>
                </div>

                {{-- Reverse User Transaction --}}
                <div class="card">
                    <p class="form-title" style="color: #dc2626;">Reverse User Transaction</p>
                    <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                        <label class="form-label">Transaction No.</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="txn-receipt-input" class="form-input"
                                placeholder="Enter Receipt No..." style="text-transform: uppercase; flex: 1;"
                                autocomplete="off" onkeydown="if(event.key==='Enter') window.loadTransaction()">
                            <button onclick="window.loadTransaction()"
                                style="padding: 8px 16px; border-radius: 8px; border: none;
                               background: #dc2626; color: white; font-size: 0.8rem;
                               font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Search
                            </button>
                        </div>
                        <p id="txn-error" class="form-error"></p>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Search Results ── --}}
        <div id="results-section" style="display: none;">
            <div class="card">
                <p class="form-title" style="margin-bottom: 1rem;">Search Results</p>
                <div id="results-body"></div>

                <div id="results-action" style="display: none; margin-top: 1.5rem;">
                    <p id="submit-error" class="form-error" style="text-align: center; margin-bottom: 8px;"></p>
                    <p id="submit-success"
                        style="text-align: center; margin-bottom: 8px;
                    font-size: 0.82rem; color: #16a34a; display: none;">
                    </p>
                    <button id="reverse-btn" onclick="window.executeReverse()"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #dc2626; color: white; font-size: 0.9rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                        Confirm Reverse
                    </button>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        'use strict';

        const CSRF = '{{ csrf_token() }}';

        // ── State ──
        let currentMode = null;
        let currentConsignmentId = null;
        let currentBL = null;
        let currentReceiptNo = null;

        // ── Helpers ──
        function setError(id, msg) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = msg;
            msg ? el.classList.add('visible') : el.classList.remove('visible');
        }

        function clearAllErrors() {
            ['cns-error', 'mfst-error', 'txn-error', 'submit-error'].forEach(id => setError(id, ''));
        }

        function showResults(html, showAction = true) {
            document.getElementById('results-body').innerHTML = html;
            document.getElementById('results-section').style.display = 'block';
            document.getElementById('results-action').style.display = showAction ? 'block' : 'none';
            document.getElementById('submit-success').style.display = 'none';
        }

        function hideResults() {
            document.getElementById('results-section').style.display = 'none';
            document.getElementById('results-body').innerHTML = '';
            currentMode = currentConsignmentId = currentBL = currentReceiptNo = null;
        }

        function formatDate(str) {
            if (!str) return '—';
            const d = new Date(str);
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatNumber(val) {
            if (val === null || val === undefined) return '—';
            return Number(val).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ── Load Consignment ──
        window.loadConsignment = function() {
            clearAllErrors();
            hideResults();

            const bl = document.getElementById('cns-bl-input').value.trim().toUpperCase();
            if (!bl) {
                setError('cns-error', 'Please enter a BL number.');
                return;
            }

            fetch('{{ route('edit-data.reverse-transaction.load-consignment') }}?BL=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        const container = document.getElementById('cns-error');
                        if (data.blockingReceipts && data.blockingReceipts.length) {
                            const links = data.blockingReceipts.map(r =>
                                '<a href="#" onclick="window.prefillTransaction(this.dataset.receipt); return false;"' +
                                ' data-receipt="' + escHtml(r) + '"' +
                                ' style="display:inline-block; margin: 4px 4px 0 0; padding: 3px 10px;' +
                                ' background:#dc2626; color:white; border-radius:6px;' +
                                ' font-size:0.78rem; font-weight:600; text-decoration:none;">' +
                                escHtml(r) + '</a>'
                            ).join('');
                            container.innerHTML =
                                escHtml(data.message) +
                                '<br><span style="font-size:0.78rem; color:var(--text-muted);">Click to reverse:</span><br>' +
                                links;
                            container.classList.add('visible');
                        } else {
                            setError('cns-error', data.message);
                        }
                        return;
                    }

                    currentMode = 'consignment';
                    currentConsignmentId = data.consignment.ConsignmentID;
                    currentBL = data.consignment.BL;

                    const c = data.consignment;
                    showResults(`
            <div style="display: grid; grid-template-columns: repeat(4, 1fr);
                gap: 1rem; padding-bottom: 1rem;
                border-bottom: 1px solid var(--border-color); margin-bottom: 1rem;">
                <div>
                    <p class="form-label">D.O.T.</p>
                    <p style="font-weight:700; color:var(--text-primary);">${formatDate(c.Date)}</p>
                </div>
                <div>
                    <p class="form-label">Bill of Lading</p>
                    <p style="font-weight:700; color:var(--text-primary);">${escHtml(c.BL)}</p>
                </div>
                <div>
                    <p class="form-label">Carrier</p>
                    <p style="font-weight:700; color:var(--text-primary);">${escHtml(c.CarrierName ?? '—')}</p>
                </div>
                <div>
                    <p class="form-label">Consignee</p>
                    <p style="font-weight:700; color:var(--text-primary);">${escHtml(c.ConsigneeName ?? '—')}</p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div>
                    <p class="form-label">Container No.</p>
                    <p style="color:var(--text-primary);">${escHtml(c.ContainerNo ?? '—')}</p>
                </div>
                <div>
                    <p class="form-label">Containers</p>
                    <p style="color:var(--text-primary);">${data.containerCount}</p>
                </div>
                <div>
                    <p class="form-label">House BL Entries</p>
                    <p style="color:var(--text-primary);">${data.hblCount}</p>
                </div>
            </div>
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #fef2f2;
                border-radius: 8px; border-left: 4px solid #dc2626;">
                <p style="font-size: 0.82rem; color: #dc2626; font-weight: 600; margin: 0;">
                    ⚠ This will reverse the consignment register and all related manifest entries.
                    This action cannot be undone.
                </p>
            </div>
        `);
                })
                .catch(() => setError('cns-error', 'An error occurred. Please try again.'));
        };

        // ── Load Manifest ──
        window.loadManifest = function() {
            clearAllErrors();
            hideResults();

            const bl = document.getElementById('mfst-bl-input').value.trim().toUpperCase();
            if (!bl) {
                setError('mfst-error', 'Please enter a BL number.');
                return;
            }

            fetch('{{ route('edit-data.reverse-transaction.load-manifest') }}?BL=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setError('mfst-error', data.message);
                        return;
                    }

                    currentMode = 'manifest';
                    currentConsignmentId = data.consignment.ConsignmentID;
                    currentBL = data.consignment.BL;

                    const header = `
            <div style="display: grid; grid-template-columns: 2fr 1fr 2fr 1fr 1fr;
                gap: 0.5rem; padding: 0.5rem 0.75rem;
                background: var(--table-header-bg); border-radius: 6px; margin-bottom: 0.25rem;">
                <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">Consignee</p>
                <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">House BL#</p>
                <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">Description</p>
                <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">Package</p>
                <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">Weight</p>
            </div>`;

                    const rows = data.rows.map(r => `
            <div style="display: grid; grid-template-columns: 2fr 1fr 2fr 1fr 1fr;
                gap: 0.5rem; padding: 0.6rem 0.75rem; align-items: center;
                border-bottom: 1px solid var(--border-color);">
                <p style="font-size:0.85rem; color:var(--text-primary); margin:0; font-weight:500;">
                    ${escHtml(r.ConsigneeName)}</p>
                <p style="font-size:0.85rem; color:var(--text-primary); margin:0;">
                    ${escHtml(r.HouseBL)}</p>
                <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">
                    ${escHtml(r.Description)}</p>
                <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">
                    ${escHtml(r.Package + ' ' + r.Unit)}</p>
                <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">
                    ${formatNumber(r.Weight)} KG</p>
            </div>`).join('');

                    showResults(`
            ${header}${rows}
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #fef2f2;
                border-radius: 8px; border-left: 4px solid #dc2626;">
                <p style="font-size: 0.82rem; color: #dc2626; font-weight: 600; margin: 0;">
                    ⚠ This will reverse all ${data.rows.length} manifest entries for BL# ${escHtml(currentBL)}.
                    This action cannot be undone.
                </p>
            </div>`);
                })
                .catch(() => setError('mfst-error', 'An error occurred. Please try again.'));
        };

        // ── Load Transaction ──
        window.loadTransaction = function() {
            clearAllErrors();
            hideResults();

            const receiptNo = document.getElementById('txn-receipt-input').value.trim().toUpperCase();
            if (!receiptNo) {
                setError('txn-error', 'Please enter a receipt number.');
                return;
            }

            fetch('{{ route('edit-data.reverse-transaction.load-transaction') }}?ReceiptNo=' + encodeURIComponent(
                    receiptNo), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setError('txn-error', data.message);
                        return;
                    }

                    currentMode = 'transaction';
                    currentReceiptNo = data.receipt.ReceiptNo;

                    // CHANGED: consistent 5-column grid across header and rows
                    const header = `
                        <div style="display: grid; grid-template-columns: 80px 180px 1fr 120px 120px;
                            gap: 0.5rem; padding: 0.5rem 0.75rem;
                            background: var(--table-header-bg); border-radius: 6px; margin-bottom: 0.25rem;">
                            <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                                letter-spacing:0.05em; color:var(--text-muted); margin:0;">Account No</p>
                            <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                                letter-spacing:0.05em; color:var(--text-muted); margin:0;">Account Name</p>
                            <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                                letter-spacing:0.05em; color:var(--text-muted); margin:0;">Description</p>
                            <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                                letter-spacing:0.05em; color:var(--text-muted); margin:0;">Debit</p>
                            <p style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                                letter-spacing:0.05em; color:var(--text-muted); margin:0;">Credit</p>
                        </div>`;

                    const entries = data.entries.map(e => `
                        <div style="display: grid; grid-template-columns: 80px 180px 1fr 120px 120px;
                            gap: 0.5rem; padding: 0.6rem 0.75rem; align-items: center;
                            border-bottom: 1px solid var(--border-color);">
                            <p style="font-size:0.85rem; color:var(--text-primary); margin:0;">
                                ${escHtml(e.AccountID)}</p>
                            <p style="font-size:0.85rem; color:var(--text-primary); margin:0; font-weight:500;">
                                ${escHtml(e.SubAccountName ?? '—')}</p>
                            <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">
                                ${escHtml(e.Description ?? '—')}</p>
                            <p style="font-size:0.85rem; color:#dc2626; margin:0; font-weight:600;">
                                ${parseFloat(e.Dr) > 0 ? 'Dr ' + formatNumber(e.Dr) : '—'}</p>
                            <p style="font-size:0.85rem; color:#16a34a; margin:0; font-weight:600;">
                                ${parseFloat(e.Cr) > 0 ? 'Cr ' + formatNumber(e.Cr) : '—'}</p>
                        </div>`).join('');

                    showResults(`
            <div style="margin-bottom: 1rem; padding-bottom: 1rem;
                border-bottom: 1px solid var(--border-color);">
                <p class="form-label">Receipt No.</p>
                <p style="font-weight:700; font-size:1rem; color:var(--text-primary);">
                    ${escHtml(data.receipt.ReceiptNo)}</p>
            </div>
            ${header}${entries}
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #fef2f2;
                border-radius: 8px; border-left: 4px solid #dc2626;">
                <p style="font-size: 0.82rem; color: #dc2626; font-weight: 600; margin: 0;">
                    ⚠ This will reversed transactional entries for receipt ${escHtml(currentReceiptNo)}. This action cannot be reversed.
                </p>
            </div>`);
                })
                .catch(() => setError('txn-error', 'An error occurred. Please try again.'));
        };

        // ── Execute Reverse ──
        window.executeReverse = function() {
            if (!currentMode) return;

            setError('submit-error', '');

            if (!confirm('Are you sure you want to reverse this ' + currentMode + '? This cannot be undone.')) return;

            const btn = document.getElementById('reverse-btn');
            btn.textContent = 'Processing...';
            btn.disabled = true;

            let url = '';
            let body = {};

            if (currentMode === 'consignment') {
                url = '{{ route('edit-data.reverse-transaction.reverse-consignment') }}';
                body = {
                    ConsignmentID: currentConsignmentId,
                    BL: currentBL
                };
            } else if (currentMode === 'manifest') {
                url = '{{ route('edit-data.reverse-transaction.reverse-manifest') }}';
                body = {
                    ConsignmentID: currentConsignmentId,
                    BL: currentBL
                };
            } else if (currentMode === 'transaction') {
                url = '{{ route('edit-data.reverse-transaction.reverse-transaction') }}';
                body = {
                    ReceiptNo: currentReceiptNo
                };
            }

            fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                })
                .then(res => res.json())
                .then(data => {
                    btn.textContent = 'Confirm Reverse';
                    btn.disabled = false;

                    if (data.success) {
                        const successEl = document.getElementById('submit-success');
                        successEl.textContent = data.message;
                        successEl.style.display = 'block';

                        setTimeout(() => {
                            hideResults();
                            const cnsInput = document.getElementById('cns-bl-input');
                            const mfstInput = document.getElementById('mfst-bl-input');
                            const txnInput = document.getElementById('txn-receipt-input');
                            if (cnsInput) cnsInput.value = '';
                            if (mfstInput) mfstInput.value = '';
                            if (txnInput) txnInput.value = '';
                        }, 2000);
                    } else {
                        setError('submit-error', data.message ?? 'Reverse failed. Please try again.');
                    }
                })
                .catch(() => {
                    btn.textContent = 'Confirm Reverse';
                    btn.disabled = false;
                    setError('submit-error', 'An error occurred. Please try again.');
                });
        };

        // ── Prefill transaction input from blocking receipt button ──
        window.prefillTransaction = function(receiptNo) {
            const input = document.getElementById('txn-receipt-input');
            if (!input) return;
            input.value = receiptNo;
            input.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            window.loadTransaction();
        };
    </script>
@endpush
