@extends('layouts.app')

@section('title', 'Receive Service Charge')
@section('page-title', 'Receive Service Charge')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Row 1: Search + Declaration Details ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

            {{-- BL# Search Panel --}}
            <div class="card">
                <p class="form-title">BL# Search</p>

                <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                    <label class="form-label">Search Client Invoice</label>
                    <input type="text" id="dcl-input" class="form-input"
                        placeholder="Search by HBL, Declaration No. or Consignee..." style="text-transform: uppercase;"
                        autocomplete="off">
                    <div id="dcl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                               background: var(--card-bg); border: 1px solid var(--border-color);
                               border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                               width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                    </div>
                    <input type="hidden" id="hbl-value">
                    <input type="hidden" id="main-bl-value">
                    <input type="hidden" id="declaration-id-value">
                    <input type="hidden" id="declaration-no-value">
                    <input type="hidden" id="consignee-id-value">
                    <input type="hidden" id="consignee-name-value">
                    <p id="dcl-error" class="form-error"></p>
                </div>
            </div>

            {{-- Declaration Details Panel --}}
            <div class="card">
                <p class="form-title">Declaration Details</p>

                <div id="dcl-details-empty"
                    style="margin-top: 1rem; text-align: center;
                           color: var(--text-muted); font-size: 0.8rem; padding: 1.5rem 0;">
                    Search and select a declaration to load details
                </div>

                <div id="dcl-details" style="display: none; margin-top: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; font-size: 0.82rem;">
                        <div>
                            <p
                                style="color: var(--text-muted); font-size: 0.7rem;
                                text-transform: uppercase; letter-spacing: 0.05em;">
                                Consignee</p>
                            <p id="detail-consignee" style="font-weight: 600; color: var(--text-primary); margin-top: 2px;">
                            </p>
                        </div>
                        <div>
                            <p
                                style="color: var(--text-muted); font-size: 0.7rem;
                                text-transform: uppercase; letter-spacing: 0.05em;">
                                House BL#</p>
                            <p id="detail-hbl" style="font-weight: 600; color: var(--text-primary); margin-top: 2px;"></p>
                        </div>
                        <div>
                            <p
                                style="color: var(--text-muted); font-size: 0.7rem;
                                text-transform: uppercase; letter-spacing: 0.05em;">
                                Main BL#</p>
                            <p id="detail-main-bl" style="font-weight: 600; color: var(--text-primary); margin-top: 2px;">
                            </p>
                        </div>
                        <div>
                            <p
                                style="color: var(--text-muted); font-size: 0.7rem;
                                text-transform: uppercase; letter-spacing: 0.05em;">
                                Declaration No.</p>
                            <p id="detail-dcl-no" style="font-weight: 600; color: var(--text-primary); margin-top: 2px;">
                            </p>
                        </div>
                        <div style="grid-column: span 2;">
                            <p
                                style="color: var(--text-muted); font-size: 0.7rem;
                                text-transform: uppercase; letter-spacing: 0.05em;">
                                Item Description</p>
                            <p id="detail-description"
                                style="font-weight: 500; color: var(--text-primary); margin-top: 2px;"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Row 2: Payment Details ── --}}
        <div class="card">
            <p class="form-title">Payment Details</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

                {{-- Amount --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="amount" class="form-input" min="0.01" step="0.01"
                        placeholder="0.00">
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
                <input type="text" id="description" class="form-input" placeholder="Auto-filled on selection"
                    style="text-transform: uppercase;">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Submit --}}
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
                <button onclick="saveServCharge()" id="save-btn"
                    style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.925rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                    Save Service Charge
                </button>
            </div>
        </div>

    </div>

    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        // ── Declaration Typeahead ──
        function initDclSearch() {
            window.dclSearch = new SearchDropdown({
                inputId: 'dcl-input',
                dropdownId: 'dcl-dropdown',
                hiddenId: 'hbl-value',
                url: '{{ route('payment.serv-charge.search-dcl') }}',
                labelKey: 'label',
                valueKey: 'HBL',
                minLength: 2,
                onSelect: (hbl, label) => {
                    fetch('{{ route('payment.serv-charge.search-dcl') }}?q=' + encodeURIComponent(hbl), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const match = data.find(d => d.HBL === hbl);
                            if (match) {
                                // Populate hidden fields
                                document.getElementById('hbl-value').value = match.HBL;
                                document.getElementById('main-bl-value').value = match.MainBL;
                                document.getElementById('declaration-id-value').value = match.DeclarationID;
                                document.getElementById('declaration-no-value').value = match.DeclarationNo;
                                document.getElementById('consignee-id-value').value = match.ConsigneeID;
                                document.getElementById('consignee-name-value').value = match.ConsigneeName;

                                // Populate detail panel
                                document.getElementById('detail-consignee').textContent = match
                                    .ConsigneeName;
                                document.getElementById('detail-hbl').textContent = match.HBL;
                                document.getElementById('detail-main-bl').textContent = match.MainBL ?? '—';
                                document.getElementById('detail-dcl-no').textContent = match.DeclarationNo;
                                document.getElementById('detail-description').textContent = match
                                    .ItemDescription ?? '—';

                                document.getElementById('dcl-details-empty').style.display = 'none';
                                document.getElementById('dcl-details').style.display = 'block';

                                // Auto-fill description
                                document.getElementById('description').value =
                                    'SERVICE CHARGE IFO ~ ' + match.DeclarationNo;
                            }
                        });
                }
            });
        }

        // serv-charge.blade.php
        if (window.searchDropdownReady) {
            initDclSearch();
        } else {
            document.addEventListener('search-dropdown-ready', initDclSearch);
        }

        setTimeout(initDclSearch, 0);

        // ── Save Service Charge ──
        function saveServCharge() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            const fields = {
                HBL: document.getElementById('hbl-value').value.trim(),
                MainBL: document.getElementById('main-bl-value').value.trim(),
                DeclarationID: document.getElementById('declaration-id-value').value,
                DeclarationNo: document.getElementById('declaration-no-value').value.trim(),
                ConsigneeID: document.getElementById('consignee-id-value').value,
                ConsigneeName: document.getElementById('consignee-name-value').value.trim(),
                Amount: document.getElementById('amount').value,
                AccountNo: document.getElementById('account-no').value,
                PaymentDate: document.getElementById('payment-date').value,
                Description: document.getElementById('description').value.trim(),
                ReceiptID: document.getElementById('receipt-id').value,
                ReceiptNo: document.getElementById('receipt-no').value,
            };

            let valid = true;
            const checks = [
                ['dcl-error', !fields.HBL, 'Please search and select a declaration.'],
                ['amount-error', !fields.Amount || parseFloat(fields.Amount) <= 0, 'Amount is required.'],
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

            fetch('{{ route('payment.serv-charge.store') }}', {
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
                                `{{ url('payment/serv-charge/report') }}/${data.ReceiptNo}`,
                                '_blank'
                            );
                            resetForm();
                            successEl.classList.remove('visible');
                            btn.textContent = 'Save Service Charge';
                            btn.disabled = false;
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save service charge.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Service Charge';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'A network error occurred. Please try again.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Service Charge';
                    btn.disabled = false;
                });
        }

        // ── Reset ──
        function resetForm() {
            ['dcl-input', 'hbl-value', 'main-bl-value', 'declaration-id-value',
                'declaration-no-value', 'consignee-id-value', 'consignee-name-value',
                'amount', 'description'
            ].forEach(id => {
                document.getElementById(id).value = '';
            });

            document.getElementById('account-no').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';

            document.getElementById('dcl-details').style.display = 'none';
            document.getElementById('dcl-details-empty').style.display = 'block';
        }
    </script>
@endpush
