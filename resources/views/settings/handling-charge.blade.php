@extends('layouts.app')

@section('title', 'Handling Charges Setup')
@section('page-title', 'Handling Charges Setup')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel: Add Handling Charge ── --}}
    <div class="shrink-0" style="width: 320px;">
        <div class="card h-full flex flex-col">

            <p class="form-title">New Handling Charge</p>
            <p class="form-subtitle">Set a billing account as a handling charge</p>

            {{-- Ledger Account --}}
            <div class="form-group">
                <label class="form-label">Billing Account</label>
                <select id="account-input" class="form-input">
                    <option value="">Select account...</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->AccountNo }}">
                            {{ $account->AccountName }}
                        </option>
                    @endforeach
                </select>
                <p id="account-error" class="form-error"></p>
            </div>

            {{-- Default Amount --}}
            <div class="form-group">
                <label class="form-label">Default Amount</label>
                <input
                    type="number"
                    id="amount-input"
                    placeholder="0.00"
                    min="0.01"
                    step="0.01"
                    class="form-input">
                <p id="amount-error" class="form-error"></p>
            </div>

            {{-- Priority Order --}}
            <div class="form-group">
                <label class="form-label">Priority Order</label>
                <input
                    type="number"
                    id="porder-input"
                    placeholder="e.g. 1"
                    min="1"
                    step="1"
                    value="{{ $charges->count() + 1 }}"
                    class="form-input">
                <p class="form-error" id="porder-error"></p>
                <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px;">
                    1 = highest priority
                </p>
            </div>

            <button onclick="addCharge()" id="add-btn" class="btn-primary">
                Add Handling Charge
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Charges ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div>
                    <p class="form-title">Existing Handling Charges</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $charges->count() }} {{ Str::plural('charge', $charges->count()) }} configured
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Priority</th>
                            <th>Account Name</th>
                            <th style="width: 120px;">Control</th>
                            <th style="width: 120px;">Default Amount</th>
                            <th style="width: 100px;">Set By</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($charges as $charge)
                        <tr>
                            <td class="td-mono">{{ $charge->POrder }}</td>
                            <td>
                                <span style="font-weight: 500; color: var(--text-primary);">
                                    {{ $charge->account->AccountName ?? '-' }}
                                </span>
                            </td>
                            <td class="td-muted">
                                {{ $charge->account->control->ControlName ?? '-' }}
                            </td>
                            <td>
                                {{-- Inline edit amount --}}
                                <span
                                    class="amount-display"
                                    style="font-weight: 600; color: #16a34a; cursor: pointer;"
                                    onmouseover="this.style.opacity='0.7'"
                                    onmouseout="this.style.opacity='1'"
                                    onclick="startAmountEdit(this, {{ $charge->AccountNo }})"
                                    title="Click to edit">
                                    {{ number_format($charge->Amount, 2) }}
                                </span>
                                <input
                                    type="number"
                                    class="form-input amount-input"
                                    style="display: none; width: 100px; padding: 4px 8px;"
                                    value="{{ $charge->Amount }}"
                                    data-original="{{ $charge->Amount }}"
                                    data-id="{{ $charge->AccountNo }}"
                                    min="0.01"
                                    step="0.01"
                                    onkeydown="handleAmountKey(event, this)"
                                    onblur="saveAmount(this)">
                            </td>
                            <td class="td-muted">{{ $charge->Username }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="deleteCharge({{ $charge->AccountNo }}, this)"
                                    class="btn-icon btn-icon-danger"
                                    title="Remove">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No handling charges configured. Add one using the form on the left.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';

    // ── Add handling charge ──
    function addCharge() {
        const accountEl  = document.getElementById('account-input');
        const amountEl   = document.getElementById('amount-input');
        const porderEl   = document.getElementById('porder-input');
        const successEl  = document.getElementById('add-success');
        const btn        = document.getElementById('add-btn');

        // Reset errors
        ['account', 'amount', 'porder'].forEach(f => {
            document.getElementById(f + '-error').classList.remove('visible');
        });
        successEl.classList.remove('visible');

        let valid = true;

        if (!accountEl.value) {
            document.getElementById('account-error').textContent = 'Please select a billing account.';
            document.getElementById('account-error').classList.add('visible');
            valid = false;
        }
        if (!amountEl.value || parseFloat(amountEl.value) <= 0) {
            document.getElementById('amount-error').textContent = 'Please enter a valid amount greater than zero.';
            document.getElementById('amount-error').classList.add('visible');
            valid = false;
        }
        if (!porderEl.value || parseInt(porderEl.value) < 1) {
            document.getElementById('porder-error').textContent = 'Priority order must be at least 1.';
            document.getElementById('porder-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.handling-charge.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                AccountNo: accountEl.value,
                Amount:    amountEl.value,
                POrder:    porderEl.value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                document.getElementById('account-error').textContent = data.message ?? 'Failed to add charge.';
                document.getElementById('account-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('account-error').textContent = 'Something went wrong. Please try again.';
            document.getElementById('account-error').classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Handling Charge';
            btn.disabled    = false;
        });
    }

    // ── Inline edit amount ──
    function startAmountEdit(span, id) {
        const input         = span.nextElementSibling;
        span.style.display  = 'none';
        input.style.display = 'inline-block';
        input.focus();
        input.select();
    }

    function handleAmountKey(e, input) {
        if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { cancelAmountEdit(input); }
    }

    function cancelAmountEdit(input) {
        const span          = input.previousElementSibling;
        input.value         = input.getAttribute('data-original');
        input.style.display = 'none';
        span.style.display  = '';
    }

    function saveAmount(input) {
        const span     = input.previousElementSibling;
        const id       = input.getAttribute('data-id');
        const original = input.getAttribute('data-original');
        const newAmount = parseFloat(input.value);

        if (!input.value || newAmount <= 0 || newAmount == parseFloat(original)) {
            cancelAmountEdit(input);
            return;
        }

        fetch(`/settings/handling-charge/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ Amount: newAmount }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                span.textContent = data.Amount;
                input.setAttribute('data-original', newAmount);
                input.value = newAmount;
            } else {
                cancelAmountEdit(input);
                alert(data.message ?? 'Failed to update amount.');
            }
        })
        .catch(() => {
            cancelAmountEdit(input);
            alert('Something went wrong. Please try again.');
        })
        .finally(() => {
            input.style.display = 'none';
            span.style.display  = '';
        });
    }

    // ── Delete handling charge ──
    function deleteCharge(id, btn) {
        if (!confirm('Remove this handling charge? This cannot be undone.')) return;

        fetch(`/settings/handling-charge/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest('tr').remove();
            } else {
                alert(data.message ?? 'Failed to remove charge.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }
</script>
@endpush