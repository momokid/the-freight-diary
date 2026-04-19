@extends('layouts.app')

@section('title', 'Accounting Transaction')
@section('page-title', 'Accounting Transaction')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Row 1: Entry Type + Transaction Type ── --}}
        <div class="card">
            <p class="form-title">Transaction Setup</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.25rem;">

                {{-- Entry Type --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Entry Type <span style="color: #ef4444;">*</span></label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <label
                            style="display: flex; align-items: center; gap: 8px; cursor: pointer;
                        font-size: 0.875rem; color: var(--text-primary);">
                            <input type="radio" name="entry-type" value="double" checked onchange="onEntryTypeChange()"
                                style="accent-color: #16a34a; width: 16px; height: 16px;">
                            Double Entry
                        </label>
                        <label
                            style="display: flex; align-items: center; gap: 8px; cursor: pointer;
                        font-size: 0.875rem; color: var(--text-primary);">
                            <input type="radio" name="entry-type" value="single" onchange="onEntryTypeChange()"
                                style="accent-color: #16a34a; width: 16px; height: 16px;">
                            Single Entry
                            <span style="font-size: 0.7rem; color: #f59e0b; font-weight: 600;">
                                ⚠ Unbalanced
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Transaction Type --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction Type <span style="color: #ef4444;">*</span></label>
                    <select id="transaction-type" class="form-input" onchange="onTransactionTypeChange()">
                        <optgroup label="Double Entry" id="double-entry-group">
                            <option value="GL_DOUBLE">G.L. Transfer</option>
                            <option value="DR_GL_CR_INC">Dr G.L. – Cr Income</option>
                            <option value="CR_GL_DR_EXP">Dr Expense – Cr G.L.</option>
                            <option value="CR_GL_DR_INC">Dr Income – Cr G.L.</option>
                            <option value="DR_GL_CR_EXP">Dr G.L. – Cr Expense</option>
                        </optgroup>
                        <optgroup label="Single Entry" id="single-entry-group" style="display: none;">
                            <option value="GL_SINGLE">G.L. Transfer</option>
                            <option value="SINGLE_DR_INC">Dr Income</option>
                            <option value="SINGLE_CR_INC">Cr Income</option>
                            <option value="SINGLE_DR_EXP">Dr Expense</option>
                            <option value="SINGLE_CR_EXP">Cr Expense</option>
                        </optgroup>
                    </select>
                    <p id="transaction-type-error" class="form-error"></p>
                </div>

            </div>
        </div>

        {{-- ── Row 2: Account Selection ── --}}
        <div class="card">
            <p class="form-title">Account Selection</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.25rem;">

                {{-- Dr Account (double entry) --}}
                <div id="dr-account-group" class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" id="dr-account-label">Debit Account <span
                            style="color: #ef4444;">*</span></label>
                    <select id="dr-account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($glAccounts as $account)
                            <option value="{{ $account->AccountNo }}" data-type="GL">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="dr-account-error" class="form-error"></p>
                </div>

                {{-- Cr Account (double entry) --}}
                <div id="cr-account-group" class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" id="cr-account-label">Credit Account <span
                            style="color: #ef4444;">*</span></label>
                    <select id="cr-account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($glAccounts as $account)
                            <option value="{{ $account->AccountNo }}" data-type="GL">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="cr-account-error" class="form-error"></p>
                </div>

                {{-- Single Account --}}
                <div id="single-account-group" class="form-group" style="margin-bottom: 0; display: none;">
                    <label class="form-label" id="single-account-label">Account <span
                            style="color: #ef4444;">*</span></label>
                    <select id="single-account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($glAccounts as $account)
                            <option value="{{ $account->AccountNo }}" data-type="GL">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="single-account-error" class="form-error"></p>
                </div>

                {{-- Single Mode (Dr/Cr) — only for GL_SINGLE --}}
                <div id="single-mode-group" class="form-group" style="margin-bottom: 0; display: none;">
                    <label class="form-label">Mode <span style="color: #ef4444;">*</span></label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <label
                            style="display: flex; align-items: center; gap: 8px; cursor: pointer;
                        font-size: 0.875rem; color: var(--text-primary);">
                            <input type="radio" name="single-mode" value="Dr"
                                style="accent-color: #16a34a; width: 16px; height: 16px;">
                            Debit (Dr)
                        </label>
                        <label
                            style="display: flex; align-items: center; gap: 8px; cursor: pointer;
                        font-size: 0.875rem; color: var(--text-primary);">
                            <input type="radio" name="single-mode" value="Cr"
                                style="accent-color: #16a34a; width: 16px; height: 16px;">
                            Credit (Cr)
                        </label>
                    </div>
                    <p id="single-mode-error" class="form-error"></p>
                </div>

            </div>
        </div>

        {{-- ── Row 3: Payment Details ── --}}
        <div class="card">
            <p class="form-title">Transaction Details</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

                {{-- Amount --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="amount" class="form-input" min="0.01" step="0.01"
                        placeholder="0.00">
                    <p id="amount-error" class="form-error"></p>
                </div>

                {{-- Payment Date --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction Date <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="payment-date" class="form-input" value="{{ now()->toDateString() }}"
                        max="{{ now()->toDateString() }}"
                        onchange="window.refreshReceipt('receipt-no', 'receipt-id', 'payment-date')">
                    <p id="payment-date-error" class="form-error"></p>
                </div>

                {{-- Transaction ID --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction ID</label>
                    <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}"
                        readonly style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
                </div>

            </div>

            {{-- Description --}}
            <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                <input type="text" id="description" class="form-input" placeholder="Enter transaction description"
                    style="text-transform: uppercase;">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Submit --}}
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
                <button onclick="saveTransaction()" id="save-btn"
                    style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                       background: #16a34a; color: white; font-size: 0.925rem;
                       font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                    Save Transaction
                </button>
            </div>
        </div>

    </div>

    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">

    {{-- Account data for JS population --}}
    <script>
        const GL_ACCOUNTS = @json($glAccounts);
        const INCOME_ACCOUNTS = @json($incomeAccounts);
        const EXPENDITURE_ACCOUNTS = @json($expenditureAccounts);
    </script>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        // ── Account dropdown configurations per transaction type ──
        const TYPE_CONFIG = {
            GL_DOUBLE: {
                dr: 'GL',
                cr: 'GL',
                single: null
            },
            DR_GL_CR_INC: {
                dr: 'GL',
                cr: 'INCOME',
                single: null
            },
            CR_GL_DR_EXP: {
                dr: 'Expenditure',
                cr: 'GL',
                single: null
            },
            CR_GL_DR_INC: {
                dr: 'INCOME',
                cr: 'GL',
                single: null
            },
            DR_GL_CR_EXP: {
                dr: 'GL',
                cr: 'Expenditure',
                single: null
            },
            GL_SINGLE: {
                dr: null,
                cr: null,
                single: 'GL',
                showMode: true
            },
            SINGLE_DR_INC: {
                dr: null,
                cr: null,
                single: 'INCOME',
                showMode: false
            },
            SINGLE_CR_INC: {
                dr: null,
                cr: null,
                single: 'INCOME',
                showMode: false
            },
            SINGLE_DR_EXP: {
                dr: null,
                cr: null,
                single: 'Expenditure',
                showMode: false
            },
            SINGLE_CR_EXP: {
                dr: null,
                cr: null,
                single: 'Expenditure',
                showMode: false
            },
        };

        function getAccounts(type) {
            if (type === 'GL') return GL_ACCOUNTS;
            if (type === 'INCOME') return INCOME_ACCOUNTS;
            if (type === 'Expenditure') return EXPENDITURE_ACCOUNTS;
            return [];
        }

        function populateSelect(selectId, accountType, label) {
            const select = document.getElementById(selectId);
            const accounts = getAccounts(accountType);
            select.innerHTML = `<option value="">Select ${label}...</option>`;
            accounts.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.AccountNo;
                opt.textContent = a.AccountName;
                select.appendChild(opt);
            });
        }

        // ── Entry Type change ──
        function onEntryTypeChange() {
            const entryType = document.querySelector('input[name="entry-type"]:checked').value;
            const txSelect = document.getElementById('transaction-type');

            // Toggle optgroup visibility
            document.getElementById('double-entry-group').style.display =
                entryType === 'double' ? '' : 'none';
            document.getElementById('single-entry-group').style.display =
                entryType === 'single' ? '' : 'none';

            // Set first option of the visible group
            txSelect.value = entryType === 'double' ? 'GL_DOUBLE' : 'GL_SINGLE';

            onTransactionTypeChange();
        }

        // ── Transaction Type change ──
        function onTransactionTypeChange() {
            const type = document.getElementById('transaction-type').value;
            const config = TYPE_CONFIG[type];
            if (!config) return;

            const isSingle = config.single !== null;

            // Show/hide account groups
            document.getElementById('dr-account-group').style.display = isSingle ? 'none' : '';
            document.getElementById('cr-account-group').style.display = isSingle ? 'none' : '';
            document.getElementById('single-account-group').style.display = isSingle ? '' : 'none';
            document.getElementById('single-mode-group').style.display =
                (isSingle && config.showMode) ? '' : 'none';

            if (isSingle) {
                populateSelect('single-account-no', config.single, 'account');
            } else {
                populateSelect('dr-account-no', config.dr, 'debit account');
                populateSelect('cr-account-no', config.cr, 'credit account');
            }
        }

        // Initialise on load
        onTransactionTypeChange();

        // ── Save Transaction ──
        function saveTransaction() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            const type = document.getElementById('transaction-type').value;
            const config = TYPE_CONFIG[type];
            const isSingle = config.single !== null;

            const fields = {
                TransactionType: type,
                Amount: document.getElementById('amount').value,
                PaymentDate: document.getElementById('payment-date').value,
                Description: document.getElementById('description').value.trim(),
                ReceiptID: document.getElementById('receipt-id').value,
                ReceiptNo: document.getElementById('receipt-no').value,
            };

            if (isSingle) {
                fields.SingleAccountNo = document.getElementById('single-account-no').value;
                if (config.showMode) {
                    const modeEl = document.querySelector('input[name="single-mode"]:checked');
                    fields.SingleMode = modeEl ? modeEl.value : '';
                }
            } else {
                fields.DrAccountNo = document.getElementById('dr-account-no').value;
                fields.CrAccountNo = document.getElementById('cr-account-no').value;
            }

            // Client-side validation
            let valid = true;
            const checks = [
                ['amount-error', !fields.Amount || parseFloat(fields.Amount) <= 0, 'Amount is required.'],
                ['payment-date-error', !fields.PaymentDate, 'Transaction date is required.'],
                ['description-error', !fields.Description, 'Description is required.'],
            ];

            if (isSingle) {
                checks.push(['single-account-error', !fields.SingleAccountNo, 'Please select an account.']);
                if (config.showMode) {
                    checks.push(['single-mode-error', !fields.SingleMode, 'Please select Dr or Cr.']);
                }
            } else {
                checks.push(['dr-account-error', !fields.DrAccountNo, 'Please select a debit account.']);
                checks.push(['cr-account-error', !fields.CrAccountNo, 'Please select a credit account.']);
                if (fields.DrAccountNo && fields.CrAccountNo && fields.DrAccountNo === fields.CrAccountNo) {
                    document.getElementById('cr-account-error').textContent =
                        'Debit and credit accounts cannot be the same.';
                    document.getElementById('cr-account-error').classList.add('visible');
                    valid = false;
                }
            }

            checks.forEach(([errorId, condition, message]) => {
                const el = document.getElementById(errorId);
                if (el) el.classList.remove('visible');
                if (condition) {
                    if (el) {
                        el.textContent = message;
                        el.classList.add('visible');
                    }
                    valid = false;
                }
            });

            if (!valid) return;

            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch('{{ route('accounting.transaction.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(fields),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        successEl.textContent = data.message;
                        successEl.classList.add('visible');
                        setTimeout(() => {
                            resetForm();
                            successEl.classList.remove('visible');
                            btn.textContent = 'Save Transaction';
                            btn.disabled = false;

                            // Refresh receipt number for next transaction
                            window.refreshReceipt('receipt-no', 'receipt-id', 'payment-date');
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save transaction.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Transaction';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'A network error occurred. Please try again.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Transaction';
                    btn.disabled = false;
                });
        }

        // ── Reset ──
        function resetForm() {
            document.getElementById('amount').value = '';
            document.getElementById('description').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
            document.querySelector('input[name="entry-type"][value="double"]').checked = true;
            document.getElementById('transaction-type').value = 'GL_DOUBLE';
            onEntryTypeChange();

            // Fetch a fresh receipt number
            fetch('{{ route('accounting.transaction.index') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newNo = doc.getElementById('receipt-no')?.value;
                    const newId = doc.getElementById('receipt-id')?.value;
                    if (newNo) document.getElementById('receipt-no').value = newNo;
                    if (newId) document.getElementById('receipt-id').value = newId;
                });

        }
    </script>
@endpush
