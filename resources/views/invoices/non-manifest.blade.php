@extends('layouts.app')

@section('title', 'Non-Manifest Invoice')
@section('page-title', 'Non-Manifest Invoice')

@section('content')

    {{-- Pending entries warning --}}
    @if ($pendingEntries->isNotEmpty())
        <div
            style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
            <svg style="width: 18px; height: 18px; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p style="font-size: 0.8rem; font-weight: 600; color: #92400e;">Pending charges for
                    {{ $pendingClient?->FullName ?? 'client' }}</p>
                <p style="font-size: 0.75rem; color: #92400e; margin-top: 2px;">You have {{ $pendingEntries->count() }}
                    staged charge(s). Submit or clear to start a new invoice.</p>
            </div>
            <button onclick="clearCharges()"
                style="margin-left: auto; padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(234,179,8,0.4); background: transparent; color: #92400e; font-size: 0.75rem; cursor: pointer;">
                Clear & Start New
            </button>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

        {{-- ── Left Column ── --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- Client Search --}}
            <div class="card">
                <p class="form-title">Client Search</p>

                <div style="display: flex; gap: 8px; margin-top: 0.75rem; position: relative;">
                    <div style="flex: 1; position: relative;">
                        <input type="text" id="client-search" placeholder="Search Client Name / House BL#..."
                            class="form-input" oninput="debounceClientSearch()">
                        <div id="client-dropdown"
                            style="display: none; position: absolute; z-index: 100; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; width: 100%; top: 100%;">
                        </div>
                    </div>
                    <button type="button" onclick="openQuickAddModal('consignee')"
                        style="padding: 10px 14px; border-radius: 8px; border: 1.5px solid #16a34a; background: transparent; color: #16a34a; font-weight: 700; cursor: pointer; font-size: 1rem;">+</button>
                </div>
                <input type="hidden" id="client-id">
                <p id="client-error" class="form-error"></p>

                {{-- BL# dropdown --}}
                <div id="bl-section" style="display: none; margin-top: 0.75rem; display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">BL# <span style="color: #ef4444;">*</span></label>
                            <select id="bl-select" class="form-input" onchange="onBLChange()">
                                <option value="">Select BL...</option>
                            </select>
                            <p id="bl-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Item</label>
                            <input type="text" id="item-display" class="form-input" readonly
                                style="background: var(--content-bg); color: var(--text-muted);">
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">D.O.T <span style="color: #ef4444;">*</span></label>
                        <input type="date" id="dot" class="form-input" value="{{ now()->toDateString() }}">
                        <p id="dot-error" class="form-error"></p>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Transaction ID</label>
                        <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}"
                            readonly style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
                    </div>
                </div>

                {{-- Invoice Description --}}
                <div class="form-group" style="margin-top: 0.75rem; margin-bottom: 0;">
                    <label class="form-label">Invoice Description <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="invoice-description" class="form-input"
                        placeholder="Overall description (appears on invoice)">
                    <p id="invoice-description-error" class="form-error"></p>
                </div>
            </div>

            {{-- Add Charges --}}
            <div class="card">
                <p class="form-title">Add Charges</p>
                <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Select Account <span style="color: #ef4444;">*</span></label>
                            <select id="charge-account" class="form-input">
                                <option value="">Select account...</option>
                                @foreach ($incomeAccounts as $account)
                                    <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                                @endforeach
                            </select>
                            <p id="charge-account-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                            <input type="number" id="charge-amount" min="0.01" step="0.01" class="form-input"
                                oninput="previewTax()">
                            <p id="charge-amount-error" class="form-error"></p>
                        </div>
                    </div>

                    {{-- Taxable toggle --}}
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label class="form-label" style="margin: 0;">Taxables</label>
                        <div id="taxable-toggle" onclick="toggleTaxable()"
                            style="width: 44px; height: 24px; border-radius: 9999px; background: #16a34a; cursor: pointer; position: relative; transition: background 0.2s;">
                            <div id="taxable-knob"
                                style="width: 18px; height: 18px; background: white; border-radius: 9999px; position: absolute; top: 3px; right: 3px; transition: right 0.2s;">
                            </div>
                        </div>
                        <span id="taxable-label" style="font-size: 0.8rem; color: var(--text-muted);">Tax will be
                            applied</span>
                    </div>

                    {{-- Tax preview --}}
                    <div id="tax-preview"
                        style="display: none; background: var(--content-bg); border-radius: 8px; padding: 10px 12px; font-size: 0.8rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="color: var(--text-muted);">Base Amount</span>
                            <span id="preview-base">0.00</span>
                        </div><p id="cns-submit-error" class="form-error" style="text-align: center; margin-bottom: 8px;"></p>
<p id="cns-submit-success" class="form-error" style="text-align: center; margin-bottom: 8px;"></p>
                        <div id="preview-tax-lines"></div>
                        <div
                            style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); margin-top: 6px; padding-top: 6px;">
                            <span style="font-weight: 600;">Total</span>
                            <span id="preview-total" style="font-weight: 700; color: #16a34a;">0.00</span>
                        </div>
                    </div>

                    <p id="add-error" class="form-error" style="text-align: center;"></p>
                    <button onclick="addCharge()" id="add-btn" class="btn-primary">Add / Update Charge</button>
                </div>
            </div>

        </div>

        {{-- ── Right Column ── --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <p class="form-title" style="margin: 0;">Charges Added</p>
                <button onclick="saveInvoice()" id="save-btn"
                    style="display: none; padding: 10px 20px; border-radius: 8px; border: none; background: #f59e0b; color: white; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                    Save Client Invoice
                </button>
            </div>

            <p id="save-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
            <p id="save-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>

            <div id="charges-table">
                <div
                    style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                    Search and select a client then add charges.
                </div>
            </div>
        </div>

    </div>

    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">
    <input type="hidden" id="taxable-value" value="1">
    <input type="hidden" id="selected-bl">
    <input type="hidden" id="selected-container">

    {{-- Shared quick-add modals --}}
    @include('partials.quick-add-modals')

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const TAX_COMPONENTS = @json($taxComponents);
        let searchTimer = null;
        let isTaxable = true;

        // ── Client search ──
        function debounceClientSearch() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(searchClient, 400);
        }

        function searchClient() {
            const q = document.getElementById('client-search').value.trim();
            const dropdown = document.getElementById('client-dropdown');
            if (!q || q.length < 2) {
                dropdown.style.display = 'none';
                return;
            }

            fetch(`{{ route('invoice.non-manifest.search-client') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.length) {
                        dropdown.style.display = 'none';
                        return;
                    }
                    dropdown.innerHTML = data.map(c => `
            <div onclick="selectClient(${c.ConsigneeID}, '${c.FullName.replace(/'/g, "\\'")}')"
                style="padding: 10px 14px; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid var(--border-color);"
                onmouseover="this.style.background='var(--content-bg)'"
                onmouseout="this.style.background=''">
                <div style="font-weight: 500;">${c.FullName}</div>
                <div style="color: var(--text-muted); font-size: 0.75rem;">${c.TelNo}</div>
            </div>`).join('');
                    dropdown.style.display = 'block';
                });
        }

        function selectClient(id, name) {
            document.getElementById('client-search').value = name;
            document.getElementById('client-id').value = id;
            document.getElementById('client-dropdown').style.display = 'none';
            document.getElementById('client-error').classList.remove('visible');

            // Load BLs for this client
            fetch(`{{ route('invoice.non-manifest.get-bls') }}?ConsigneeID=${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('bl-select');
                    select.innerHTML = '<option value="">Select House BL...</option>';

                    if (!data.length) {
                        select.innerHTML = '<option value="">No House BLs found</option>';
                        document.getElementById('bl-group').style.display = 'grid';
                        return;
                    }

                    data.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.HouseBL;
                        opt.dataset.mainBL = b.MainBL;
                        opt.dataset.item = b.ItemDescription ?? '';
                        opt.textContent = `${b.HouseBL} — ${b.MainBL}`;
                        select.appendChild(opt);
                    });
                    document.getElementById('bl-section').style.display = 'grid';
                });
        }

        function onBLChange() {
            const select = document.getElementById('bl-select');
            const opt = select.options[select.selectedIndex];
            document.getElementById('selected-bl').value = select.value;
            document.getElementById('item-display').value = opt.dataset.item ?? '';
            document.getElementById('selected-item').value = opt.dataset.item ?? '';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#client-search') && !e.target.closest('#client-dropdown')) {
                document.getElementById('client-dropdown').style.display = 'none';
            }
        });

        // ── Tax ──
        function calculateTax(amount, taxable) {
            if (!taxable) return {
                base: amount,
                lines: [],
                total: amount
            };
            let subtotal = amount;
            const lines = [];
            TAX_COMPONENTS.forEach(c => {
                const base = c.applies_on === 'subtotal' ? subtotal : amount;
                const tax = Math.round(base * (c.rate / 100) * 100) / 100;
                lines.push({
                    name: c.name,
                    label: c.label,
                    rate: c.rate,
                    tax
                });
                subtotal = Math.round((subtotal + tax) * 100) / 100;
            });
            return {
                base: amount,
                lines,
                total: subtotal
            };
        }

        function previewTax() {
            const amount = parseFloat(document.getElementById('charge-amount').value) || 0;
            const preview = document.getElementById('tax-preview');
            if (!amount) {
                preview.style.display = 'none';
                return;
            }

            const tax = calculateTax(amount, isTaxable);
            preview.style.display = 'block';
            document.getElementById('preview-base').textContent = formatAmount(tax.base);
            document.getElementById('preview-total').textContent = formatAmount(tax.total);
            document.getElementById('preview-tax-lines').innerHTML = tax.lines.map(l => `
        <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
            <span style="color: var(--text-muted);">${l.name} (${l.rate}%)</span>
            <span>${formatAmount(l.tax)}</span>
        </div>`).join('');
        }

        function toggleTaxable() {
            isTaxable = !isTaxable;
            const toggle = document.getElementById('taxable-toggle');
            const knob = document.getElementById('taxable-knob');
            toggle.style.background = isTaxable ? '#16a34a' : '#d1d5db';
            knob.style.right = isTaxable ? '3px' : '23px';
            document.getElementById('taxable-label').textContent = isTaxable ? 'Tax will be applied' : 'No tax';
            document.getElementById('taxable-value').value = isTaxable ? '1' : '0';
            previewTax();
        }

        // ── Add charge ──
        function addCharge() {
            const btn = document.getElementById('add-btn');
            const errorEl = document.getElementById('add-error');
            const clientId = document.getElementById('client-id').value;
            const accountNo = document.getElementById('charge-account').value;
            const amount = document.getElementById('charge-amount').value;

            errorEl.classList.remove('visible');

            let valid = true;
            if (!clientId) {
                document.getElementById('client-error').textContent = 'Please select a client.';
                document.getElementById('client-error').classList.add('visible');
                valid = false;
            }
            if (!accountNo) {
                document.getElementById('charge-account-error').textContent = 'Please select an account.';
                document.getElementById('charge-account-error').classList.add('visible');
                valid = false;
            }
            if (!amount) {
                document.getElementById('charge-amount-error').textContent = 'Amount is required.';
                document.getElementById('charge-amount-error').classList.add('visible');
                valid = false;
            }
            if (!valid) return;

            btn.textContent = 'Adding...';
            btn.disabled = true;

            fetch('{{ route('invoice.non-manifest.charges.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ClientID: clientId,
                        AccountNo: accountNo,
                        Amount: amount,
                        Taxable: isTaxable
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderChargesTable(data.entries);
                        document.getElementById('charge-account').value = '';
                        document.getElementById('charge-amount').value = '';
                        document.getElementById('tax-preview').style.display = 'none';
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to add charge.';
                        errorEl.classList.add('visible');
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong.';
                    errorEl.classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Add / Update Charge';
                    btn.disabled = false;
                });
        }

        // ── Render charges table ──
        function renderChargesTable(entries) {
            const wrapper = document.getElementById('charges-table');
            const saveBtn = document.getElementById('save-btn');
            saveBtn.style.display = entries.length > 0 ? 'block' : 'none';

            if (!entries.length) {
                wrapper.innerHTML =
                    `<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">No charges added yet.</div>`;
                return;
            }

            const total = entries.reduce((sum, e) => sum + parseFloat(e.SubTotal), 0);
            wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th style="text-align: right;">Charges</th>
                    <th style="text-align: right; width: 80px;">21%</th>
                    <th style="text-align: right; width: 100px;">Total</th>
                    <th style="width: 50px; text-align: center;"></th>
                </tr>
            </thead>
            <tbody>
                ${entries.map(e => {
                    const tax = parseFloat(e.GetFund) + parseFloat(e.NHIL) + parseFloat(e.Covid) + parseFloat(e.VAT);
                    return `
                                                        <tr>
                                                            <td style="font-size: 0.8rem;">${e.AccountName}</td>
                                                            <td style="text-align: right;">${formatAmount(e.Amount)}</td>
                                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">${formatAmount(tax)}</td>
                                                            <td style="text-align: right; font-weight: 600; color: #16a34a;">${formatAmount(e.SubTotal)}</td>
                                                            <td style="text-align: center;">
                                                                <button onclick="removeCharge(${e.AccountNo})" class="btn-icon btn-icon-danger">
                                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                    </svg>
                                                                </button>
                                                            </td>
                                                        </tr>`;
                }).join('')}
                <tr style="background: var(--content-bg);">
                    <td colspan="3" style="text-align: right; font-weight: 700; padding-right: 12px;">TOTAL CHARGES:</td>
                    <td style="text-align: right; font-weight: 800; color: #16a34a;">${formatAmount(total)}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>`;
        }

        // ── Remove charge ──
        function removeCharge(accountNo) {
            fetch('{{ route('invoice.non-manifest.charges.remove') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        AccountNo: accountNo
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) renderChargesTable(data.entries);
                });
        }

        // ── Clear ──
        function clearCharges() {
            if (!confirm('Clear all staged charges?')) return;
            fetch('{{ route('invoice.non-manifest.charges.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderChargesTable([]);
                        document.getElementById('client-search').value = '';
                        document.getElementById('client-id').value = '';
                        document.getElementById('bl-section').style.display = 'none';
                        const banner = document.querySelector('[style*="rgba(234,179,8"]');
                        if (banner) banner.remove();
                    }
                });
        }

        // ── Save invoice ──
        function saveInvoice() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('save-error');
            const successEl = document.getElementById('save-success');
            const clientId = document.getElementById('client-id').value;
            const dot = document.getElementById('dot').value;
            const invoiceDesc = document.getElementById('invoice-description').value.trim();
            const mainBL = document.getElementById('selected-bl').value;

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            if (!invoiceDesc) {
                document.getElementById('invoice-description-error').textContent = 'Invoice description is required.';
                document.getElementById('invoice-description-error').classList.add('visible');
                return;
            }

            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch('{{ route('invoice.non-manifest.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ReceiptID: document.getElementById('receipt-id').value,
                        ReceiptNo: document.getElementById('receipt-no').value,
                        ClientID: clientId,
                        DOT: dot,
                        MainBL: mainBL,
                        HouseBL: '',
                        Description: invoiceDesc,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        successEl.textContent = data.message;
                        successEl.classList.add('visible');
                        setTimeout(() => {
                            window.open(
                                `{{ url('invoice/non-manifest/report') }}/${encodeURIComponent(data.ReceiptNo)}`,
                                '_blank');
                            renderChargesTable([]);
                            document.getElementById('client-search').value = '';
                            document.getElementById('client-id').value = '';
                            document.getElementById('invoice-description').value = '';
                            document.getElementById('bl-section').style.display = 'none';
                            successEl.classList.remove('visible');
                            btn.style.display = 'none';

                            // Refresh receipt number for next transaction
                            window.refreshReceipt('receipt-no', 'receipt-id');

                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save invoice.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Client Invoice';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Client Invoice';
                    btn.disabled = false;
                });
        }

        function formatAmount(amount) {
            return parseFloat(amount).toLocaleString('en-GH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // ── Quick Add callbacks ──
        window.onQuickAddConsignee = function(id, name) {
            document.getElementById('client-search').value = name;
            document.getElementById('client-id').value = id;
        };

        // Load pending entries
        @if ($pendingEntries->isNotEmpty())
            document.addEventListener('DOMContentLoaded', function() {
                renderChargesTable(@json($pendingEntries));
                @if ($pendingClient)
                    document.getElementById('client-search').value = '{{ $pendingClient->FullName }}';
                    document.getElementById('client-id').value = '{{ $pendingClientID }}';
                @endif
            });
        @endif
    </script>
@endpush
