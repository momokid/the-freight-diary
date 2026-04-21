@extends('layouts.app')

@section('title', 'Gate-Out Expense')
@section('page-title', 'Gate-Out Expense')

@section('content')

    <div class="card" style="max-width: 90vw;">

        <p class="form-title">Gate-Out Expense Transaction</p>
        <p class="form-subtitle">Record an expense incurred during consignment transit</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.25rem;">

            {{-- Consignment Details --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Consignment Details <span style="color: #ef4444;">*</span></label>
                <select id="bl-select" class="form-input" onchange="onBLChange()">
                    <option value="">Select consignment...</option>
                    @foreach ($consignments as $c)
                        <option value="{{ $c->BL }}">{{ $c->BL }}</option>
                    @endforeach
                </select>
                <p id="bl-error" class="form-error"></p>
            </div>

            {{-- Expense Account --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Expense Account <span style="color: #ef4444;">*</span></label>
                <select id="account-select" class="form-input">
                    <option value="">Select account...</option>
                    @foreach ($expenseAccounts as $acc)
                        <option value="{{ $acc->AccountNo }}">{{ $acc->AccountName }}</option>
                    @endforeach
                </select>
                <p id="account-error" class="form-error"></p>
            </div>

            {{-- Amount --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Amount (GH₵) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount" class="form-input" placeholder="0.00" min="0.01" step="0.01">
                <p id="amount-error" class="form-error"></p>
            </div>

            {{-- Cash Source --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Cash Source <span style="color: #ef4444;">*</span></label>
                <select id="cash-account" class="form-input">
                    <option value="">Select account...</option>
                    @foreach ($cashAccounts as $acc)
                        <option value="{{ $acc->AccountNo }}">{{ $acc->AccountName }}</option>
                    @endforeach
                </select>
                <p id="cash-error" class="form-error"></p>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem;">

            {{-- Transaction Description --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Transaction Description <span style="color: #ef4444;">*</span></label>
                <input type="text" id="description" class="form-input" placeholder="e.g. Transport to Tema port"
                    maxlength="500">
                <p id="description-error" class="form-error"></p>
            </div>

            {{-- Date of Transaction --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Date of Transaction <span style="color: #ef4444;">*</span></label>
                <input type="date" id="payment-date" class="form-input" max="{{ now()->toDateString() }}"
                    value="{{ now()->toDateString() }}">
                <p id="date-error" class="form-error"></p>
            </div>

            {{-- Truck # (optional) --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">
                    Truck #
                    <span style="color: var(--text-muted); font-weight: 400;">(optional)</span>
                </label>
                <input type="text" id="truck-no" class="form-input" placeholder="e.g. GR-1234-20" maxlength="50"
                    style="text-transform: uppercase;">
            </div>

            {{-- Driver's Contact (optional) --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">
                    Driver's Contact
                    <span style="color: var(--text-muted); font-weight: 400;">(optional)</span>
                </label>
                <input type="text" id="driver-contact" class="form-input" placeholder="e.g. 024 000 0000" maxlength="50">
            </div>

        </div>

        {{-- Submit --}}
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
            <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
            <button onclick="saveTransaction()" id="save-btn" class="btn-primary" style="width: 100%;">
                Save Transaction
            </button>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const ROUTES = {
            save: '{{ route('disbursement.gate-out.save') }}',
        };

        // ── Expose to global scope ────────────────────────────────────────────────────
        window.onBLChange = onBLChange;
        window.saveTransaction = saveTransaction;

        // ── BL Change ─────────────────────────────────────────────────────────────────
        function onBLChange() {
            // Auto-populate description prefix when BL selected
            const select = document.getElementById('bl-select');
            const bl = select.value;
            const descEl = document.getElementById('description');

            if (bl && !descEl.value) {
                descEl.value = `GATE-OUT EXPENSE IFO ${bl}`;
            }

            clearErrors();
        }

        // ── Save Transaction ──────────────────────────────────────────────────────────
        function saveTransaction() {
            clearErrors();

            const bl = document.getElementById('bl-select').value;
            const accountNo = document.getElementById('account-select').value;
            const amount = document.getElementById('amount').value;
            const cashAccount = document.getElementById('cash-account').value;
            const description = document.getElementById('description').value.trim();
            const paymentDate = document.getElementById('payment-date').value;
            const truckNo = document.getElementById('truck-no').value.trim();
            const driverContact = document.getElementById('driver-contact').value.trim();

            // Validate
            let valid = true;

            if (!bl) {
                showError('bl-error', 'Please select a consignment.');
                valid = false;
            }
            if (!accountNo) {
                showError('account-error', 'Please select an expense account.');
                valid = false;
            }
            if (!amount || parseFloat(amount) <= 0) {
                showError('amount-error', 'Please enter a valid amount.');
                valid = false;
            }
            if (!cashAccount) {
                showError('cash-error', 'Please select a cash source.');
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
                        AccountNo: accountNo,
                        CashAccount: cashAccount,
                        Amount: parseFloat(amount),
                        Description: description,
                        PaymentDate: paymentDate,
                        TruckNo: truckNo,
                        DriverContact: driverContact,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showError('submit-error', data.message ?? 'Save failed.');
                        return;
                    }

                    // Success — show message and reset form
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
                    console.error('Gate-out save error:', err);
                    showError('submit-error', 'Connection error. Please try again.');
                })
                .finally(() => {
                    btn.textContent = 'Save Transaction';
                    btn.disabled = false;
                });
        }

        // ── Reset Form ────────────────────────────────────────────────────────────────
        function resetForm() {
            document.getElementById('bl-select').value = '';
            document.getElementById('account-select').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('cash-account').value = '';
            document.getElementById('description').value = '';
            document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
            document.getElementById('truck-no').value = '';
            document.getElementById('driver-contact').value = '';
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
