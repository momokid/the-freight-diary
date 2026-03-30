@extends('layouts.app')

@section('title', 'Disbursement Setup')
@section('page-title', 'Disbursement Setup')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel: Add Disbursement Account ── --}}
    <div class="flex-shrink-0" style="width: 320px;">
        <div class="card h-full flex flex-col">

            <p class="form-title">New Disbursement Account</p>
            <p class="form-subtitle">Add an account to the disbursement list</p>

            {{-- Ledger Account --}}
            <div class="form-group">
                <label class="form-label">Expenditure Account</label>
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

            <button onclick="addDisbursement()" id="add-btn" class="btn-primary">
                Add Disbursement Account
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Disbursement Accounts ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <p class="form-title">Existing Disbursement Accounts</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $disbursements->count() }} {{ Str::plural('account', $disbursements->count()) }} configured
                    </p>
                </div>

                {{-- Search --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        id="search-input"
                        placeholder="Search accounts..."
                        oninput="filterDisbursements()"
                        class="form-input"
                        style="padding-left: 32px; width: 220px;">
                </div>
            </div>

            {{-- Table --}}
            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Account No</th>
                            <th>Account Name</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="disbursements-table-body">
                        @forelse($disbursements as $disbursement)
                        <tr class="disbursement-row"
                            data-name="{{ strtolower($disbursement->account->AccountName ?? '') }}">
                            <td class="td-mono">{{ $disbursement->AccountNo }}</td>
                            <td style="font-weight: 500; color: var(--text-primary);">
                                {{ $disbursement->account->AccountName ?? '-' }}
                            </td>
                            <td style="text-align: center;">
                                <button
                                    onclick="deleteDisbursement({{ $disbursement->AccountNo }}, this)"
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
                            <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No disbursement accounts configured. Add one using the form on the left.
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

    // ── Filter disbursements by search ──
    function filterDisbursements() {
        const query = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.disbursement-row').forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(query) ? '' : 'none';
        });
    }

    // ── Add disbursement account ──
    function addDisbursement() {
        const accountEl = document.getElementById('account-input');
        const errorEl   = document.getElementById('account-error');
        const successEl = document.getElementById('add-success');
        const btn       = document.getElementById('add-btn');

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!accountEl.value) {
            errorEl.textContent = 'Please select a ledger account.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.disbursement-account.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ AccountNo: accountEl.value }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                errorEl.textContent = data.message ?? 'Failed to add account.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Disbursement Account';
            btn.disabled    = false;
        });
    }

    // ── Delete disbursement account ──
    function deleteDisbursement(id, btn) {
        if (!confirm('Remove this disbursement account? This cannot be undone.')) return;

        fetch(`/settings/disbursement-account/${id}`, {
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
                alert(data.message ?? 'Failed to remove account.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }
</script>
@endpush