@extends('layouts.app')

@section('title', 'Edit Disbursement Analysis')
@section('page-title', 'Edit Disbursement Analysis')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ── Search Panel ── --}}
        <div class="card">
            <p class="form-title" style="color: #16a34a;">Consignment Search</p>
            <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                <label class="form-label">Bill of Lading #</label>
                <input type="text" id="bl-input" class="form-input" placeholder="Enter Main BL..."
                    style="text-transform: uppercase;" autocomplete="off">
                <div id="bl-dropdown"
                    style="display: none; position: absolute; z-index: 100;
                       background: var(--card-bg); border: 1px solid var(--border-color);
                       border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                       width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                </div>
                <input type="hidden" id="bl-value">
                <p id="bl-error" class="form-error"></p>
            </div>
        </div>

        {{-- ── HBL List ── --}}
        <div id="hbl-section" style="display: none;">
            <div class="card">
                <p class="form-title" style="margin-bottom: 1rem;">House BL Entries</p>

                {{-- Table Header --}}
                <div
                    style="display: grid; grid-template-columns: 1fr 1fr 80px;
                gap: 0.5rem; padding: 0.5rem 0.75rem;
                background: var(--table-header-bg); border-radius: 6px; margin-bottom: 0.25rem;">
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        House BL#</p>
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Consignee</p>
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Status</p>
                </div>

                <div id="hbl-list-body"></div>
            </div>
        </div>

        {{-- ── Entry Edit Form ── --}}
        <div id="entries-section" style="display: none;">
            <div class="card">

                {{-- Header info --}}
                <div
                    style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;
                padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1rem;">
                    <div>
                        <p class="form-label">Bill of Lading</p>
                        <p id="entry-bl" style="font-weight:700; color:var(--text-primary);">—</p>
                    </div>
                    <div>
                        <p class="form-label">House BL</p>
                        <p id="entry-hbl" style="font-weight:700; color:var(--text-primary);">—</p>
                    </div>
                    <div>
                        <p class="form-label">Receipt No.</p>
                        <p id="entry-receipt" style="font-weight:700; color:var(--text-primary);">—</p>
                    </div>
                </div>

                {{-- Payment Date + Cash Account --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Payment Date <span style="color:#ef4444">*</span></label>
                        <input type="date" id="payment-date" class="form-input">
                        <p id="payment-date-error" class="form-error"></p>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Cash / Bank Account <span style="color:#ef4444">*</span></label>
                        <select id="cash-account" class="form-input">
                            <option value="">— Select Account —</option>
                        </select>
                        <p id="cash-account-error" class="form-error"></p>
                    </div>
                </div>

                {{-- Entries Table --}}
                <div style="border-top: 1px solid var(--border-color); margin-bottom: 1rem;"></div>

                <p class="form-title" style="margin-bottom: 0.75rem; font-size: 0.85rem;">
                    Disbursement Entries
                </p>

                {{-- Table Header --}}
                <div
                    style="display: grid; grid-template-columns: 2fr 1fr 1fr 80px;
                gap: 0.5rem; padding: 0.5rem 0.75rem;
                background: var(--table-header-bg); border-radius: 6px; margin-bottom: 0.25rem;">
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Expense Account</p>
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Type</p>
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Amount (GHS)</p>
                    <p
                        style="font-size:0.72rem; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.05em; color:var(--text-muted); margin:0;">
                        Budget</p>
                </div>

                <div id="entries-body"></div>

                {{-- Total --}}
                <div
                    style="display: flex; justify-content: flex-end; margin-top: 0.75rem;
                padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                    <p style="font-size:0.85rem; color:var(--text-muted); margin-right: 1rem;">Total:</p>
                    <p id="entries-total"
                        style="font-size:0.95rem; font-weight:700;
                    color:var(--text-primary);">GHS 0.00
                    </p>
                </div>

                {{-- Submit --}}
                <div style="margin-top: 1.5rem;">
                    <p id="submit-error" class="form-error" style="text-align:center; margin-bottom: 8px;"></p>
                    <p id="submit-success"
                        style="text-align:center; margin-bottom: 8px;
                    font-size: 0.82rem; color: #16a34a; display: none;">
                    </p>
                    <button onclick="window.updateDisbursement()"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.9rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                        Update Disbursement
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
        const EXP_ACCTS = @json($expenseAccounts);

        // ── State ──
        let currentBL = null;
        let currentHBL = null;
        let currentEntries = [];
        let cashAccounts = [];

        // ── Init Search ──
        function initSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: '{{ route('edit-data.disbursement.search-bl') }}',
                labelKey: 'label',
                subKey: null,
                valueKey: 'BL',
                minLength: 2,
                onSelect: (bl) => loadHBLs(bl),
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initSearch, 0);
        });

        // ── Load cash accounts for select ──
        function loadCashAccounts() {
            fetch('{{ route('edit-data.disbursement.load-hbls') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Load from active_bank_cash via a simple inline approach
            // We pass them from the controller via a separate endpoint — reuse index data
            // Cash accounts are loaded dynamically from the server below
        }

        // ── Load HBLs under BL ──
        function loadHBLs(bl) {
            setError('bl-error', '');
            document.getElementById('hbl-section').style.display = 'none';
            document.getElementById('entries-section').style.display = 'none';

            fetch('{{ route('edit-data.disbursement.load-hbls') }}?BL=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setError('bl-error', data.message);
                        return;
                    }

                    currentBL = bl;
                    renderHBLList(data.hbls);
                    document.getElementById('hbl-section').style.display = 'block';
                })
                .catch(() => setError('bl-error', 'An error occurred. Please try again.'));
        }

        // ── Render HBL list ──
        function renderHBLList(hbls) {
            const body = document.getElementById('hbl-list-body');
            body.innerHTML = '';

            hbls.forEach(hbl => {
                const approved = (parseInt(hbl.Status) === 0);
                const row = document.createElement('div');
                row.style.cssText =
                    'display: grid; grid-template-columns: 1fr 1fr 80px;' +
                    'gap: 0.5rem; padding: 0.6rem 0.75rem; align-items: center;' +
                    'border-bottom: 1px solid var(--border-color);' +
                    (!approved ? 'cursor: pointer;' : 'opacity: 0.6;');

                if (!approved) {
                    row.addEventListener('click', () => loadEntries(hbl.HBL));
                    row.addEventListener('mouseover', () => row.style.background = 'var(--hover-bg)');
                    row.addEventListener('mouseout', () => row.style.background = '');
                }

                row.innerHTML =
                    '<p style="font-size:0.85rem; font-weight:600; color:var(--text-primary); margin:0;">' +
                    escHtml(hbl.HBL) + '</p>' +
                    '<p style="font-size:0.85rem; color:var(--text-muted); margin:0;">' +
                    escHtml(hbl.ConsigneeName ?? '—') + '</p>' +
                    '<p style="font-size:0.75rem; font-weight:600; margin:0; color:' +
                    (approved ? '#dc2626' : '#16a34a') + ';">' +
                    (approved ? 'Approved' : 'Active') + '</p>';

                body.appendChild(row);
            });
        }

        // ── Load entries for BL + HBL ──
        function loadEntries(hbl) {
            document.getElementById('entries-section').style.display = 'none';

            fetch('{{ route('edit-data.disbursement.load-entries') }}?BL=' + encodeURIComponent(currentBL) +
                    '&HBL=' + encodeURIComponent(hbl), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                        return;
                    }

                    currentHBL = hbl;
                    currentEntries = data.entries;

                    document.getElementById('entry-bl').textContent = currentBL;
                    document.getElementById('entry-hbl').textContent = hbl;
                    document.getElementById('entry-receipt').textContent = data.entries[0]?.ReceiptNo ?? '—';
                    document.getElementById('payment-date').value = data.entries[0]?.Date ?? '';

                    renderEntries(data.entries);
                    loadCashAccountSelect(data.entries[0]?.ReceiptNo);
                    document.getElementById('entries-section').style.display = 'block';
                    document.getElementById('entries-section').scrollIntoView({
                        behavior: 'smooth'
                    });
                })
                .catch(() => alert('An error occurred. Please try again.'));
        }

        // ── Load cash account select ──
        function loadCashAccountSelect(receiptNo) {
            fetch('edit-data.disbursement.cash-accounts', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('cash-account');
                    select.innerHTML = '<option value="">— Select Account —</option>';
                    (data.accounts ?? []).forEach(acc => {
                        const opt = document.createElement('option');
                        opt.value = acc.AccountNo;
                        opt.textContent = acc.AccountName;
                        select.appendChild(opt);
                    });
                })
                .catch(() => {});
        }

        // ── Render entries rows ──
        function renderEntries(entries) {
            const body = document.getElementById('entries-body');
            body.innerHTML = '';

            entries.forEach((entry, index) => {
                const row = document.createElement('div');
                row.style.cssText =
                    'display: grid; grid-template-columns: 2fr 1fr 1fr 80px;' +
                    'gap: 0.5rem; padding: 0.6rem 0.75rem; align-items: center;' +
                    'border-bottom: 1px solid var(--border-color);';

                // Build account select
                const accountOptions = EXP_ACCTS.map(acc =>
                    '<option value="' + acc.AccountNo + '"' +
                    (acc.AccountNo === entry.AccountID ? ' selected' : '') + '>' +
                    escHtml(acc.AccountName) + '</option>'
                ).join('');

                // Build type select
                const types = ['LCL', 'FCL', 'RORO', 'AIR'];
                const typeOpts = types.map(t =>
                    '<option value="' + t + '"' + (t === entry.Type ? ' selected' : '') + '>' + t + '</option>'
                ).join('');

                row.innerHTML =
                    '<select id="entry-account-' + index + '" class="form-input" ' +
                    'style="font-size:0.82rem;" onchange="recalcTotal()">' +
                    accountOptions +
                    '</select>' +
                    '<select id="entry-type-' + index + '" class="form-input" style="font-size:0.82rem;">' +
                    typeOpts +
                    '</select>' +
                    '<input type="number" id="entry-amount-' + index + '" class="form-input" ' +
                    'value="' + entry.Expenditure + '" min="0" step="0.01" ' +
                    'style="font-size:0.82rem;" oninput="recalcTotal()">' +
                    '<input type="number" id="entry-budget-' + index + '" class="form-input" ' +
                    'value="' + entry.TotalCashReceipt + '" min="0" step="0.01" ' +
                    'style="font-size:0.82rem;">';

                body.appendChild(row);
            });

            recalcTotal();
        }

        // ── Recalculate total ──
        function recalcTotal() {
            let total = 0;
            currentEntries.forEach((_, index) => {
                const val = parseFloat(document.getElementById('entry-amount-' + index)?.value) || 0;
                total += val;
            });
            document.getElementById('entries-total').textContent =
                'GHS ' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
        }

        // ── Update disbursement ──
        window.updateDisbursement = function() {
            if (!currentBL || !currentHBL || !currentEntries.length) return;

            setError('submit-error', '');

            const paymentDate = document.getElementById('payment-date').value;
            const cashAccount = document.getElementById('cash-account').value;

            if (!paymentDate) {
                setError('payment-date-error', 'Payment date is required.');
                return;
            }
            if (!cashAccount) {
                setError('cash-account-error', 'Cash account is required.');
                return;
            }

            if (!confirm(
                    'Are you sure you want to update this disbursement? This will reverse and re-save all entries.'))
                return;

            const rows = currentEntries.map((entry, index) => ({
                AccountID: parseInt(document.getElementById('entry-account-' + index).value),
                Expenditure: parseFloat(document.getElementById('entry-amount-' + index).value) || 0,
                TotalCashReceipt: parseFloat(document.getElementById('entry-budget-' + index).value) || 0,
                Type: document.getElementById('entry-type-' + index).value,
                ConsigneeID: entry.ConsigneeID,
                ContainerNo: entry.ContainerNo,
                Stamp: entry.Stamp,
                Restricted: entry.Restricted,
            }));

            fetch('{{ route('edit-data.disbursement.update') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        BL: currentBL,
                        HBL: currentHBL,
                        PaymentDate: paymentDate,
                        CashAccountNo: parseInt(cashAccount),
                        rows: rows,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById('submit-success');
                        el.textContent = data.message;
                        el.style.display = 'block';

                        setTimeout(() => {
                            currentBL = currentHBL = null;
                            currentEntries = [];
                            document.getElementById('bl-input').value = '';
                            document.getElementById('bl-value').value = '';
                            document.getElementById('hbl-section').style.display = 'none';
                            document.getElementById('entries-section').style.display = 'none';
                            el.style.display = 'none';
                        }, 2000);
                    } else {
                        setError('submit-error', data.message ?? 'Update failed.');
                    }
                })
                .catch(() => setError('submit-error', 'An error occurred. Please try again.'));
        };

        // ── Utilities ──
        function setError(id, msg) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = msg;
            msg ? el.classList.add('visible') : el.classList.remove('visible');
        }

        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endpush
