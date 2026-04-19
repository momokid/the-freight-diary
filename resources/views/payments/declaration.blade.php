@extends('layouts.app')

@section('title', 'Process Declaration')
@section('page-title', 'Process Declaration')

@section('content')

    <div class="card" style="max-width: 90vw;">
        <p class="form-title">Declaration Details</p>
        <p class="form-subtitle">Record a customs declaration payment for a consignment</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

            {{-- BL# Search --}}
            <div class="form-group" style="margin-bottom: 0; position: relative;">
                <label class="form-label">Enter BL# <span style="color: #ef4444;">*</span></label>
                {{-- Typeahead input — SearchDropdown handles the search and dropdown --}}
                <input type="text" id="bl-input" class="form-input" placeholder="Search BL No..."
                    style="text-transform: uppercase;">
                {{-- Dropdown container — populated by SearchDropdown --}}
                <div id="bl-dropdown"
                    style="display: none; position: absolute; z-index: 100; background: var(--card-bg);
                       border: 1px solid var(--border-color); border-radius: 8px;
                       box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; top: 100%;
                       max-height: 200px; overflow-y: auto;">
                </div>
                {{-- Hidden field stores selected BL value --}}
                <input type="hidden" id="bl-value">
                <p id="bl-error" class="form-error"></p>
            </div>

            {{-- Declaration No. --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Declaration No. <span style="color: #ef4444;">*</span></label>
                <input type="text" id="declaration-no" class="form-input" placeholder="e.g. DCL-2026-001">
                <p id="declaration-no-error" class="form-error"></p>
            </div>

            {{-- Item Description --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Item Description <span style="color: #ef4444;">*</span></label>
                <input type="text" id="description" class="form-input" placeholder="e.g. ASSORTED ELECTRICALS">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Duty Amount --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Duty Amount <span style="color: #ef4444;">*</span></label>
                {{-- The actual customs duty paid to GRA --}}
                <input type="number" id="duty-amount" class="form-input" min="0.01" step="0.01" placeholder="0.00">
                <p id="duty-amount-error" class="form-error"></p>
            </div>

            {{-- Agent's Name --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Agent's Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="agent-name" class="form-input" placeholder="e.g. John Mensah">
                <p id="agent-name-error" class="form-error"></p>
            </div>

            {{-- Agent's Contact --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Agent's Contact <span style="color: #ef4444;">*</span></label>
                <input type="text" id="agent-contact" class="form-input" placeholder="e.g. 0244000000">
                <p id="agent-contact-error" class="form-error"></p>
            </div>

            {{-- Container Size --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Container Size <span style="color: #ef4444;">*</span></label>
                <input type="text" id="container-size" class="form-input" placeholder="e.g. 40ft">
                <p id="container-size-error" class="form-error"></p>
            </div>

            {{-- Amount Charge — what we charge the client --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Amount Charge <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount-charge" class="form-input" min="0.01" step="0.01"
                    placeholder="0.00">
                <p id="amount-charge-error" class="form-error"></p>
            </div>

            {{-- Payment Date --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Payment Date <span style="color: #ef4444;">*</span></label>
                {{-- Default to today — cannot be a future date --}}
                <input type="date" id="payment-date" class="form-input" value="{{ now()->toDateString() }}"
                    max="{{ now()->toDateString() }}"
                    onchange="window.refreshReceipt('receipt-no', 'receipt-id', 'payment-date')">
                <p id="payment-date-error" class="form-error"></p>
            </div>

            {{-- Select Account — cash accounts from active_bank_cash --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Select Account <span style="color: #ef4444;">*</span></label>
                <select id="account-no" class="form-input">
                    <option value="">Select cash account...</option>
                    @foreach ($cashAccounts as $account)
                        <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                    @endforeach
                </select>
                <p id="account-no-error" class="form-error"></p>
            </div>

            {{-- Transaction ID — auto-generated, readonly --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Transaction ID</label>
                <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}" readonly
                    style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
            </div>

        </div>

        {{-- Submit --}}
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
            <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
            <button onclick="saveDeclaration()" id="save-btn"
                style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                   background: #16a34a; color: white; font-size: 0.925rem;
                   font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                Save Declaration
            </button>
        </div>
    </div>

    {{-- Hidden receipt ID --}}
    <input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        // ── BL Typeahead — uses shared SearchDropdown from layout.js ──
        // Initialised after DOM ready to ensure SearchDropdown class is available

        function initBLSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: '{{ route('payment.declaration.search-bl') }}',
                labelKey: 'label', // Shows "MainBL - HouseBL"
                subKey: 'Description', // Shows description as subtitle
                valueKey: 'BL', // Stores HouseBL as the value
                minLength: 2,
                onSelect: (bl, label) => {
                    // When a BL is selected, fetch its details and auto-fill the form
                    fetch(`{{ route('payment.declaration.search-bl') }}?q=${encodeURIComponent(bl)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const match = data.find(d => d.BL === bl);
                            if (match) {
                                // Auto-fill Description from manifestation_breakdown
                                if (match.Description) {
                                    document.getElementById('description').value = match.Description;
                                }
                                // Auto-fill Container Size from container_main
                                if (match.ContainerSize) {
                                    document.getElementById('container-size').value = match.ContainerSize;
                                }
                            }
                        });
                }
            });
        }
        // Vite modules have finished loading and window.SearchDropdown is available
        setTimeout(initBLSearch, 0);


        // ── Save Declaration ──
        function saveDeclaration() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            // Collect field values
            const fields = {
                BL: document.getElementById('bl-value').value.trim(),
                DeclarationNo: document.getElementById('declaration-no').value.trim(),
                Description: document.getElementById('description').value.trim(),
                DutyPaid: document.getElementById('duty-amount').value,
                Amount: document.getElementById('amount-charge').value,
                AgentName: document.getElementById('agent-name').value.trim(),
                AgentContact: document.getElementById('agent-contact').value.trim(),
                ContainerSize: document.getElementById('container-size').value.trim(),
                AccountNo: document.getElementById('account-no').value,
                PaymentDate: document.getElementById('payment-date').value,
                ReceiptID: document.getElementById('receipt-id').value,
                ReceiptNo: document.getElementById('receipt-no').value,
            };

            // Client-side validation — mirrors server-side rules
            let valid = true;
            const checks = [
                ['bl-error', !fields.BL, 'Please search and select a BL number.'],
                ['declaration-no-error', !fields.DeclarationNo, 'Declaration No. is required.'],
                ['description-error', !fields.Description, 'Item description is required.'],
                ['duty-amount-error', !fields.DutyPaid || parseFloat(fields.DutyPaid) <= 0, 'Duty amount is required.'],
                ['amount-charge-error', !fields.Amount || parseFloat(fields.Amount) <= 0, 'Amount charge is required.'],
                ['agent-name-error', !fields.AgentName, "Agent's name is required."],
                ['agent-contact-error', !fields.AgentContact, "Agent's contact is required."],
                ['container-size-error', !fields.ContainerSize, 'Container size is required.'],
                ['account-no-error', !fields.AccountNo, 'Please select a cash account.'],
                ['payment-date-error', !fields.PaymentDate, 'Payment date is required.'],
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

            fetch('{{ route('payment.declaration.store') }}', {
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
                            // Open printable receipt in new tab
                            window.open(`{{ url('payment/declaration/report') }}/${receiptNo}`, '_blank');

                            // Reset form fields
                            ['bl-input', 'bl-value', 'declaration-no', 'description',
                                'duty-amount', 'agent-name', 'agent-contact', 'container-size',
                                'amount-charge', 'account-no'
                            ].forEach(id => {
                                document.getElementById(id).value = '';
                            });
                            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
                            successEl.classList.remove('visible');

                            btn.textContent = 'Save Declaration';
                            btn.disabled = false;

                            // Refresh receipt number for next transaction
                            window.refreshReceipt('receipt-no', 'receipt-id', 'payment-date');

                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save declaration.';
                        errorEl.classList.add('visible');
                        btn.textContent = 'Save Declaration';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong. Please try again.';
                    errorEl.classList.add('visible');
                    btn.textContent = 'Save Declaration';
                    btn.disabled = false;
                });
        }
    </script>
@endpush
