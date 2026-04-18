@extends('layouts.app')

@section('title', 'Handling Charge Expenditure')
@section('page-title', 'Handling Charge Expenditure')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Row 1: Search + Invoice Details ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

            {{-- Main BL Search Panel --}}
            <div class="card">
                <p class="form-title">Main BL# Search</p>

                <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                    <label class="form-label">Search Main BL#</label>
                    <input type="text" id="main-bl-input" class="form-input" placeholder="Search by Main BL..."
                        style="text-transform: uppercase;" autocomplete="off">
                    <div id="main-bl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                               background: var(--card-bg); border: 1px solid var(--border-color);
                               border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                               width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                    </div>
                    <input type="hidden" id="main-bl-value">
                    <input type="hidden" id="consignment-id-value">
                    <p id="main-bl-error" class="form-error"></p>
                </div>
            </div>

            {{-- Client Invoice Details Panel --}}
            <div class="card">
                <p class="form-title">Client Invoice Details</p>

                <div style="margin-top: 1rem; overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>TOTAL CHARGES</th>
                                <th>AMOUNT PAID</th>
                                <th>OUTSTANDING BALANCE</th>
                            </tr>
                        </thead>
                        <tbody id="balance-tbody">
                            <tr>
                                <td colspan="3"
                                    style="text-align: center; color: var(--text-muted);
                                           font-size: 0.8rem; padding: 1.5rem;">
                                    Search and select a Main BL to load balance
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ── Row 2: Payment Details ── --}}
        <div class="card">
            <p class="form-title">Payment Details</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

                {{-- Amount --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="amount" class="form-input" min="0.01" step="0.01"
                        placeholder="0.00">
                    <p id="amount-error" class="form-error"></p>
                </div>

                {{-- Source Account --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Source Account <span style="color: #ef4444;">*</span></label>
                    <select id="source-account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($cashAccounts as $account)
                            <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="source-account-no-error" class="form-error"></p>
                </div>

                {{-- Source Cash Balance — readonly --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cash Balance</label>
                    <input type="text" id="source-cash-balance" class="form-input" readonly placeholder="0.00"
                        style="background: var(--content-bg); color: var(--text-muted);">
                </div>

                {{-- Expenditure Account --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Expenditure Account <span style="color: #ef4444;">*</span></label>
                    <select id="expenditure-account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($expenditureAccounts as $account)
                            <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="expenditure-account-no-error" class="form-error"></p>
                </div>

                {{-- Payment Date --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Payment Date <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="payment-date" class="form-input" value="{{ now()->toDateString() }}"
                        max="{{ now()->toDateString() }}">
                    <p id="payment-date-error" class="form-error"></p>
                </div>

                {{-- Transaction ID --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction ID</label>
                    <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}" readonly
                        style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
                </div>

            </div>

            {{-- Description --}}
            <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                <input type="text" id="description" class="form-input"
                    placeholder="e.g. HANDLING CHARGE EXPENSE IFO NAM3545302" style="text-transform: uppercase;">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Submit --}}
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
                <button onclick="saveExpense()" id="save-btn"
                    style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.925rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                    Save Handling Charge Expenditure
                </button>
            </div>
        </div>

    </div>

    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        // ── Main BL Typeahead ──
        function initMainBLSearch() {
            window.mainBLSearch = new SearchDropdown({
                inputId: 'main-bl-input',
                dropdownId: 'main-bl-dropdown',
                hiddenId: 'main-bl-value',
                url: '{{ route('payment.handling-charge-expense.search-main-bl') }}',
                labelKey: 'label',
                valueKey: 'MainBL',
                minLength: 2,
                onSelect: (mainBL, label) => {
                    fetch(`{{ route('payment.handling-charge-expense.search-main-bl') }}?q=${encodeURIComponent(mainBL)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const match = data.find(d => d.MainBL === mainBL);
                            if (match) {
                                document.getElementById('consignment-id-value').value = match.ConsignmentID;
                                document.getElementById('description').value =
                                    'HANDLING CHARGE EXPENSE IFO ' + mainBL;
                                loadBalance(match);
                            }
                        });
                }
            });
        }
        setTimeout(initMainBLSearch, 0);

        // ── Load Balance ──
        function loadBalance(match) {
            const tbody = document.getElementById('balance-tbody');
            tbody.innerHTML = `
            <tr>
                <td style="font-weight: 600; color: var(--text-primary);">
                    ${formatNumber(match.TFee)}
                </td>
                <td style="color: var(--text-muted);">
                    ${formatNumber(match.TDr)}
                </td>
                <td style="font-weight: 700; color: #16a34a;">
                    ${formatNumber(match.Balance)}
                </td>
            </tr>`;
        }

        // ── Save Expense ──
        function saveExpense() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            const fields = {
                MainBL: document.getElementById('main-bl-value').value.trim(),
                ConsignmentID: document.getElementById('consignment-id-value').value,
                SourceAccountNo: document.getElementById('source-account-no').value,
                ExpenditureAccountNo: document.getElementById('expenditure-account-no').value,
                Amount: document.getElementById('amount').value,
                PaymentDate: document.getElementById('payment-date').value,
                Description: document.getElementById('description').value.trim(),
                ReceiptID: document.getElementById('receipt-id').value,
                ReceiptNo: document.getElementById('receipt-no').value,
            };

            let valid = true;
            const checks = [
                ['main-bl-error', !fields.MainBL, 'Please search and select a Main BL.'],
                ['amount-error', !fields.Amount || parseFloat(fields.Amount) <= 0, 'Amount is required.'],
                ['source-account-no-error', !fields.SourceAccountNo, 'Please select a source account.'],
                ['expenditure-account-no-error', !fields.ExpenditureAccountNo, 'Please select an expenditure account.'],
                ['payment-date-error', !fields.PaymentDate, 'Payment date is required.'],
                ['description-error', !fields.Description, 'Description is required.'],
            ];

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

            fetch('{{ route('payment.handling-charge-expense.store') }}', {
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
                            window.open(
                                `{{ url('payment/handling-charge-expense/report') }}/${data.ReceiptNo}`,
                                '_blank'
                            );
                            resetForm();
                            successEl.classList.remove('visible');
                            btn.textContent = 'Save Handling Charge Expenditure';
                            btn.disabled = false;
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save expense.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Handling Charge Expenditure';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'A network error occurred. Please try again.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Handling Charge Expenditure';
                    btn.disabled = false;
                });
        }

        // ── Reset ──
        function resetForm() {
            ['main-bl-input', 'main-bl-value', 'consignment-id-value',
                'amount', 'description'
            ].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('source-account-no').value = '';
            document.getElementById('expenditure-account-no').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
            document.getElementById('balance-tbody').innerHTML = `
            <tr>
                <td colspan="3" style="text-align: center; color: var(--text-muted);
                    font-size: 0.8rem; padding: 1.5rem;">
                    Search and select a Main BL to load balance
                </td>
            </tr>`;
        }

        function formatNumber(val) {
            return parseFloat(val).toLocaleString('en-GH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endpush
