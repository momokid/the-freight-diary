@extends('layouts.app')

@section('title', 'Receive Handling Charge')
@section('page-title', 'Receive Handling Charge')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Row 1: BL Search + Invoice Details ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

            {{-- BL# Search Panel --}}
            <div class="card">
                <p class="form-title">BL# Search</p>

                <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                    <label class="form-label">Search Client Invoice</label>
                    <input type="text" id="hbl-input" class="form-input"
                        placeholder="Search by consignee, Main BL or House BL..." style="text-transform: uppercase;"
                        autocomplete="off">
                    <div id="hbl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                               background: var(--card-bg); border: 1px solid var(--border-color);
                               border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                               width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                    </div>
                    {{-- Hidden fields storing selected values --}}
                    <input type="hidden" id="hbl-value">
                    <input type="hidden" id="main-bl-value">
                    <input type="hidden" id="consignee-id-value">
                    <p id="hbl-error" class="form-error"></p>
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
                            <tr id="balance-empty-row">
                                <td colspan="3"
                                    style="text-align: center; color: var(--text-muted);
                                           font-size: 0.8rem; padding: 1.5rem;">
                                    Search and select a House BL to load balance
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

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

                {{-- Amount — readonly, auto-filled from outstanding balance --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="amount" class="form-input" readonly placeholder="0.00" step="0.01"
                        style="background: var(--content-bg); color: var(--text-muted);">
                    <p id="amount-error" class="form-error"></p>
                </div>

                {{-- Cash Account --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cash Account <span style="color: #ef4444;">*</span></label>
                    <select id="account-no" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($cashAccounts as $account)
                            <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="account-no-error" class="form-error"></p>
                </div>

                {{-- Payment Date --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Payment Date <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="payment-date" class="form-input" value="{{ now()->toDateString() }}"
                        max="{{ now()->toDateString() }}">
                    <p id="payment-date-error" class="form-error"></p>
                </div>

                {{-- Transaction ID — auto-generated, readonly --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction ID</label>
                    <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}" readonly
                        style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
                </div>

            </div>

            {{-- Description — full width below the grid --}}
            <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                <input type="text" id="description" class="form-input"
                    placeholder="e.g. HANDLING CHARGE PAYMENT IFO JOHN MENSAH" style="text-transform: uppercase;">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Submit --}}
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
                <button onclick="savePayment()" id="save-btn"
                    style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.925rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                    Save Invoice Payment
                </button>
            </div>
        </div>

    </div>

    {{-- Hidden receipt ID --}}
    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        // ── HBL Typeahead ──
        function initHBLSearch() {
            window.hblSearch = new SearchDropdown({
                inputId: 'hbl-input',
                dropdownId: 'hbl-dropdown',
                hiddenId: 'hbl-value',
                url: '{{ route('payment.handl-charge.search-hbl') }}',
                labelKey: 'label',
                valueKey: 'HouseBL',
                minLength: 2,
                onSelect: (hbl, label) => {
                    // Fetch back to the same endpoint to get the full record
                    fetch(`{{ route('payment.handl-charge.search-hbl') }}?q=${encodeURIComponent(hbl)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const match = data.find(d => d.HouseBL === hbl);
                            if (match) {
                                document.getElementById('main-bl-value').value = match.MainBL;
                                document.getElementById('consignee-id-value').value = match.ConsigneeID;
                                document.getElementById('description').value =
                                    'HANDLING CHARGE PAYMENT IFO ' + hbl;
                                loadBalance(hbl, match.ConsigneeID);
                            }
                        });
                }
            });
        }

        // if (window.searchDropdownReady) {
        //     initHBLSearch();
        // } else {
        //     document.addEventListener('search-dropdown-ready', initHBLSearch);
        // }

        setTimeout(initHBLSearch, 0);

        // ── Load Balance ──
        function loadBalance(hbl, consigneeId) {
            const tbody = document.getElementById('balance-tbody');
            tbody.innerHTML = `
            <tr>
                <td colspan="3" style="text-align: center; color: var(--text-muted);
                    font-size: 0.8rem; padding: 1.5rem;">Loading...</td>
            </tr>`;
            document.getElementById('amount').value = '';

            fetch('{{ route('payment.handl-charge.get-balance') }}?hbl=' +
                    encodeURIComponent(hbl) + '&consignee_id=' + encodeURIComponent(consigneeId), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        tbody.innerHTML = `
                    <tr>
                        <td colspan="3" style="text-align: center; color: #ef4444;
                            font-size: 0.8rem; padding: 1.5rem;">${data.message}</td>
                    </tr>`;
                        return;
                    }

                    tbody.innerHTML = `
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">
                        ${formatNumber(data.TotalCharges)}
                    </td>
                    <td style="color: var(--text-muted);">
                        ${formatNumber(data.AmountPaid)}
                    </td>
                    <td style="font-weight: 700; color: #16a34a;">
                        ${formatNumber(data.Balance)}
                    </td>
                </tr>`;

                    // Auto-fill Amount with outstanding balance (full settlement)
                    document.getElementById('amount').value = data.Balance;
                })
                .catch(() => {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="3" style="text-align: center; color: #ef4444;
                        font-size: 0.8rem; padding: 1.5rem;">
                        Failed to load balance. Please try again.
                    </td>
                </tr>`;
                });
        }

        // ── Save Payment ──
        function savePayment() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            const fields = {
                HouseBL: document.getElementById('hbl-value').value.trim(),
                MainBL: document.getElementById('main-bl-value').value.trim(),
                ConsigneeID: document.getElementById('consignee-id-value').value.trim(),
                AccountNo: document.getElementById('account-no').value,
                PaymentDate: document.getElementById('payment-date').value,
                Description: document.getElementById('description').value.trim(),
                ReceiptID: document.getElementById('receipt-id').value,
                ReceiptNo: document.getElementById('receipt-no').value,
            };

            // Client-side validation
            let valid = true;
            const checks = [
                ['hbl-error', !fields.HouseBL, 'Please search and select a House BL.'],
                ['account-no-error', !fields.AccountNo, 'Please select a cash account.'],
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

            fetch('{{ route('payment.handl-charge.store') }}', {
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

                        const receiptNo = data.ReceiptNo;
                        setTimeout(() => {
                            window.open(
                                `{{ url('payment/handl-charge/report') }}/${receiptNo}`,
                                '_blank'
                            );
                            resetForm();
                            successEl.classList.remove('visible');
                            btn.textContent = 'Save Invoice Payment';
                            btn.disabled = false;
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save payment.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Invoice Payment';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'A network error occurred. Please try again.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Invoice Payment';
                    btn.disabled = false;
                });
        }

        // ── Reset form after successful save ──
        function resetForm() {
            ['hbl-input', 'hbl-value', 'main-bl-value', 'consignee-id-value',
                'amount', 'description'
            ].forEach(id => {
                document.getElementById(id).value = '';
            });

            document.getElementById('account-no').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';

            document.getElementById('balance-tbody').innerHTML = `
            <tr id="balance-empty-row">
                <td colspan="3" style="text-align: center; color: var(--text-muted);
                    font-size: 0.8rem; padding: 1.5rem;">
                    Search and select a House BL to load balance
                </td>
            </tr>`;
        }

        // ── Helpers ──
        function formatNumber(val) {
            return parseFloat(val).toLocaleString('en-GH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endpush
