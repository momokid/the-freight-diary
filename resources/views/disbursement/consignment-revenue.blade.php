@extends('layouts.app')

@section('title', 'Consignment Revenue')
@section('page-title', 'Consignment Revenue Receipt')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Consignment Search ── --}}
        <div class="card">
            <p class="form-title">Consignment Details</p>
            <p class="form-subtitle">Search for a consignment by Main BL#</p>

            <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                <label class="form-label">Search Main BL#</label>
                <input type="text" id="bl-input" class="form-input" placeholder="Type to search BL..."
                    style="text-transform: uppercase;" autocomplete="off">
                <div id="bl-dropdown"
                    style="display: none; position: absolute; z-index: 100;
                       background: var(--card-bg); border: 1px solid var(--border-color);
                       border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                       width: 100%; top: calc(100% + 4px); max-height: 220px; overflow-y: auto;">
                </div>
                <input type="hidden" id="bl-value">
                <input type="hidden" id="consignee-id-value">
                <p id="bl-error" class="form-error"></p>
            </div>

            {{-- Consignment Meta --}}
            <div id="consignment-meta"
                style="display: none; margin-top: 1rem; padding: 0.6rem 0.75rem;
            border-radius: 6px; background: var(--content-bg);
            gap: 2rem; flex-wrap: wrap;">
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted);">BL#</p>
                    <p id="meta-bl"
                        style="font-size: 0.85rem; font-weight: 700;
                    color: var(--text-primary); font-family: monospace;">
                    </p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted);">Destination</p>
                    <p id="meta-destination"
                        style="font-size: 0.85rem; font-weight: 600;
                    color: var(--text-primary);"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted);">Status</p>
                    <p id="meta-status" style="font-size: 0.85rem; font-weight: 600;"></p>
                </div>
            </div>
        </div>

        {{-- ── Revenue Transaction ── --}}
        <div class="card">
            <p class="form-title">Revenue Transaction</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

                {{-- Date of Transaction --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Date of Transaction <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="payment-date" class="form-input" max="{{ now()->toDateString() }}"
                        value="{{ now()->toDateString() }}">
                    <p id="date-error" class="form-error"></p>
                </div>

                {{-- Income Account — pre-set from active_consignment_revenue --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Income Account</label>
                    @if ($incomeAccount)
                        <input type="text" class="form-input" value="{{ $incomeAccount->AccountName }}" readonly
                            style="background: var(--content-bg); color: var(--text-muted);">
                        <input type="hidden" id="account-no" value="{{ $incomeAccount->AccountNo }}">
                    @else
                        <input type="text" class="form-input" value="No income account configured" readonly
                            style="background: var(--content-bg); color: #ef4444;">
                        <input type="hidden" id="account-no" value="">
                        <p class="form-error visible">
                            Configure an income account in Basic Setup → Active Accounts.
                        </p>
                    @endif
                </div>

                {{-- Select Account (Cash Source) --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Select Account <span style="color: #ef4444;">*</span></label>
                    <select id="cash-account" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($cashAccounts as $acc)
                            <option value="{{ $acc->AccountNo }}">{{ $acc->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="cash-error" class="form-error"></p>
                </div>

            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-top: 1rem;">

                {{-- Amount --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount (GH₵) <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="amount" class="form-input" placeholder="0.00" min="0.01"
                        step="0.01">
                    <p id="amount-error" class="form-error"></p>
                </div>

                {{-- Transaction Description --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction Description <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="description" class="form-input"
                        placeholder="e.g. Revenue received for consignment" maxlength="500">
                    <p id="description-error" class="form-error"></p>
                </div>

            </div>

            {{-- Submit --}}
            <div style="margin-top: 1.5rem; padding-top: 1.25rem;
            border-top: 1px solid var(--border-color);">
                <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
                <button onclick="saveTransaction()" id="save-btn" class="btn-primary" style="width: 100%;">
                    Save Transaction
                </button>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const ROUTES = {
            search: '{{ route('disbursement.consignment-revenue.search') }}',
            save: '{{ route('disbursement.consignment-revenue.save') }}',
        };

        // ── Expose to global scope ────────────────────────────────────────────────────
        window.saveTransaction = saveTransaction;

        // ── Status label helper ───────────────────────────────────────────────────────
        function statusLabel(status) {
            const map = {
                1: {
                    label: 'Not Arrived',
                    color: '#6b7280'
                },
                2: {
                    label: 'Pending',
                    color: '#d97706'
                },
                3: {
                    label: 'Gated Out',
                    color: '#2563eb'
                },
                0: {
                    label: 'Cleared',
                    color: '#16a34a'
                },
            };
            return map[status] ?? {
                label: 'Unknown',
                color: '#6b7280'
            };
        }

        // ── Init Search ───────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initSearch, 0);
        });

        function initSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: ROUTES.search,
                labelKey: 'BL',
                subKey: 'Destination',
                valueKey: 'BL',
                minLength: 2,
                onSelect: (bl) => {
                    const selected = window._lastBLResults?.find(r => r.BL === bl);
                    if (selected) populateMeta(selected);
                },
            });

            // Cache results for meta population
            const original = window.blSearch._render.bind(window.blSearch);
            window.blSearch._render = function(items) {
                window._lastBLResults = items;
                original(items);
            };
        }

        function populateMeta(item) {
            document.getElementById('consignee-id-value').value = item.ConsigneeID;
            document.getElementById('meta-bl').textContent = item.BL;
            document.getElementById('meta-destination').textContent = item.Destination ?? '—';

            const s = statusLabel(item.Status);
            const statusEl = document.getElementById('meta-status');
            statusEl.textContent = s.label;
            statusEl.style.color = s.color;

            const meta = document.getElementById('consignment-meta');
            meta.style.display = 'flex';

            // Auto-fill description
            const descEl = document.getElementById('description');
            if (!descEl.value) {
                descEl.value = `CONSIGNMENT REVENUE IFO ${item.BL}`;
            }
        }

        // ── Save Transaction ──────────────────────────────────────────────────────────
        function saveTransaction() {
            clearErrors();

            const bl = document.getElementById('bl-value').value.trim();
            const consigneeID = document.getElementById('consignee-id-value').value;
            const accountNo = document.getElementById('account-no').value;
            const cashAccount = document.getElementById('cash-account').value;
            const amount = document.getElementById('amount').value;
            const description = document.getElementById('description').value.trim();
            const paymentDate = document.getElementById('payment-date').value;

            let valid = true;

            if (!bl) {
                showError('bl-error', 'Please search and select a consignment.');
                valid = false;
            }
            if (!accountNo) {
                showError('submit-error', 'Income account not configured. Set it up in Basic Setup.');
                valid = false;
            }
            if (!cashAccount) {
                showError('cash-error', 'Please select a cash account.');
                valid = false;
            }
            if (!amount || parseFloat(amount) <= 0) {
                showError('amount-error', 'Please enter a valid amount.');
                valid = false;
            }
            if (!description) {
                showError('description-error', 'Please enter a transaction description.');
                valid = false;
            }
            if (!paymentDate) {
                showError('date-error', 'Please select a date.');
                valid = false;
            }

            if (!valid) return;

            const btn = document.getElementById('save-btn');
            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch(ROUTES.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: bl,
                        ConsigneeID: consigneeID,
                        AccountNo: accountNo,
                        CashAccount: cashAccount,
                        Amount: parseFloat(amount),
                        Description: description,
                        PaymentDate: paymentDate,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showError('submit-error', data.message ?? 'Save failed.');
                        return;
                    }

                    const successEl = document.getElementById('submit-success');
                    successEl.textContent = data.message;
                    successEl.classList.add('visible');

                    resetForm();

                    setTimeout(() => {
                        successEl.classList.remove('visible');
                        successEl.textContent = '';
                    }, 4000);
                })
                .catch(err => {
                    console.error('Consignment revenue save error:', err);
                    showError('submit-error', 'Connection error. Please try again.');
                })
                .finally(() => {
                    btn.textContent = 'Save Transaction';
                    btn.disabled = false;
                });
        }

        // ── Reset Form ────────────────────────────────────────────────────────────────
        function resetForm() {
            document.getElementById('bl-input').value = '';
            document.getElementById('bl-value').value = '';
            document.getElementById('consignee-id-value').value = '';
            document.getElementById('cash-account').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('description').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
            document.getElementById('consignment-meta').style.display = 'none';
            window._lastBLResults = [];
        }

        // ── Helpers ───────────────────────────────────────────────────────────────────
        function showError(id, msg) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = msg;
                el.classList.add('visible');
            }
        }

        function clearErrors() {
            document.querySelectorAll('.form-error').forEach(el => {
                el.textContent = '';
                el.classList.remove('visible');
            });
        }
    </script>
@endpush
