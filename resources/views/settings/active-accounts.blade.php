@extends('layouts.app')

@section('title', 'Active Accounts Setup')
@section('page-title', 'Active Accounts Setup')

@section('content')

<div class="card">

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="form-title">Active Accounts Configuration</p>
            <p class="form-subtitle" style="margin-bottom: 0;">
                Configure the system accounts used for each transaction type
            </p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto" style="min-height: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 250px;">Account Type</th>
                    <th>Current Account</th>
                    <th style="width: 80px;">Type</th>
                    <th style="width: 80px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>

                {{-- Standard single-column accounts --}}
                @foreach($configs as $key => $config)
                    @if($key === 'accounts') @continue @endif
                    <tr>
                        <td style="font-weight: 500; color: var(--text-primary);">
                            {{ $config['label'] }}
                        </td>
                        <td>
                            @if($current[$key])
                                <span style="color: #16a34a; font-weight: 500;">
                                    {{ $current[$key]->AccountName }}
                                </span>
                                <span class="td-mono" style="margin-left: 8px;">
                                    #{{ $current[$key]->AccountNo }}
                                </span>
                            @else
                                <span style="color: #ef4444; font-size: 0.8rem;">
                                    Not configured
                                </span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px;
                                background: {{ $config['type'] === 'GL' ? 'rgba(59,130,246,0.1)' : 'rgba(22,163,74,0.1)' }};
                                color: {{ $config['type'] === 'GL' ? '#3b82f6' : '#16a34a' }};">
                                {{ $config['type'] }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button
                                onclick="openModal('{{ $key }}', '{{ $config['label'] }}', '{{ $config['type'] }}', {{ $current[$key] ? $current[$key]->AccountNo : 'null' }})"
                                class="btn-icon"
                                style="background: rgba(22,163,74,0.08); color: #16a34a;"
                                title="Change account">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach

                {{-- Special case — active_accounts --}}
                <tr>
                    <td style="font-weight: 500; color: var(--text-primary);">
                        {{ $configs['accounts']['label'] }}
                    </td>
                    <td>
                        @if($current['accounts'])
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 0.8rem; color: var(--text-muted);">IE Main:
                                    <span style="color: #16a34a; font-weight: 500;">
                                        {{ $current['accounts']['ie_main']?->AccountName ?? 'Not set' }}
                                    </span>
                                </span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Cash Receipt:
                                    <span style="color: #16a34a; font-weight: 500;">
                                        {{ $current['accounts']['cash_receipt']?->AccountName ?? 'Not set' }}
                                    </span>
                                </span>
                            </div>
                        @else
                            <span style="color: #ef4444; font-size: 0.8rem;">Not configured</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: rgba(59,130,246,0.1); color: #3b82f6;">
                            GL
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button
                            onclick="openAccountsModal({{ $current['accounts'] ? $current['accounts']['ie_main']?->AccountNo : 'null' }}, {{ $current['accounts'] ? $current['accounts']['cash_receipt']?->AccountNo : 'null' }})"
                            class="btn-icon"
                            style="background: rgba(22,163,74,0.08); color: #16a34a;"
                            title="Change accounts">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

{{-- ── Standard Account Modal ── --}}
<div id="account-modal" style="display: none; position: fixed; inset: 0; z-index: 50; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 420px; margin: 1rem;">

        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="form-title" id="modal-title">Change Account</p>
                <p style="font-size: 0.75rem; color: var(--text-muted);" id="modal-subtitle"></p>
            </div>
            <button onclick="closeModal()" style="color: var(--text-muted); background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
        </div>

        <div class="form-group">
            <label class="form-label">Select Account</label>
            <select id="modal-account-select" class="form-input">
                <option value="">Select account...</option>
            </select>
            <p id="modal-error" class="form-error"></p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeModal()" class="btn-secondary" style="width: auto; flex: 1;">
                Cancel
            </button>
            <button onclick="saveAccount()" id="modal-save-btn" class="btn-primary" style="flex: 1;">
                Save
            </button>
        </div>

    </div>
</div>

{{-- ── Active Accounts Modal (special case) ── --}}
<div id="accounts-modal" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 420px; margin: 1rem;">

        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="form-title">IE Main + Cash Receipt Accounts</p>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Configure both GL accounts</p>
            </div>
            <button onclick="closeAccountsModal()" style="color: var(--text-muted); background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
        </div>

        <div class="form-group">
            <label class="form-label">IE Main Account</label>
            <select id="ie-main-select" class="form-input">
                <option value="">Select account...</option>
                @foreach($glAccounts as $account)
                    <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Cash Receipt Account</label>
            <select id="cash-receipt-select" class="form-input">
                <option value="">Select account...</option>
                @foreach($glAccounts as $account)
                    <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                @endforeach
            </select>
            <p id="accounts-error" class="form-error"></p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeAccountsModal()" class="btn-secondary" style="width: auto; flex: 1;">
                Cancel
            </button>
            <button onclick="saveAccountsModal()" id="accounts-save-btn" class="btn-primary" style="flex: 1;">
                Save
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF       = '{{ csrf_token() }}';
    const GL_ACCOUNTS = @json($glAccounts->map(fn($a) => ['AccountNo' => $a->AccountNo, 'AccountName' => $a->AccountName]));
    const INC_ACCOUNTS = @json($incomeAccounts->map(fn($a) => ['AccountNo' => $a->AccountNo, 'AccountName' => $a->AccountName]));

    let currentKey     = null;
    let currentType    = null;

    // ── Standard modal ──
    function openModal(key, label, type, currentAccountNo) {
        currentKey  = key;
        currentType = type;

        document.getElementById('modal-title').textContent    = 'Change ' + label;
        document.getElementById('modal-subtitle').textContent = type + ' accounts only';
        document.getElementById('modal-error').classList.remove('visible');

        // Populate dropdown based on type
        const accounts = type === 'GL' ? GL_ACCOUNTS : INC_ACCOUNTS;
        const select   = document.getElementById('modal-account-select');
        select.innerHTML = '<option value="">Select account...</option>';
        accounts.forEach(acc => {
            const opt      = document.createElement('option');
            opt.value      = acc.AccountNo;
            opt.textContent = acc.AccountName;
            if (acc.AccountNo == currentAccountNo) opt.selected = true;
            select.appendChild(opt);
        });

        document.getElementById('account-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('account-modal').style.display = 'none';
        currentKey  = null;
        currentType = null;
    }

    function saveAccount() {
        const select  = document.getElementById('modal-account-select');
        const errorEl = document.getElementById('modal-error');
        const btn     = document.getElementById('modal-save-btn');

        errorEl.classList.remove('visible');

        if (!select.value) {
            errorEl.textContent = 'Please select an account.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Saving...';
        btn.disabled    = true;

        fetch(`/settings/active-accounts/${currentKey}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ account_no: select.value }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal();
                location.reload();
            } else {
                errorEl.textContent = data.message ?? 'Failed to update.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Save';
            btn.disabled    = false;
        });
    }

    // ── Active accounts modal (special case) ──
    function openAccountsModal(ieMainNo, cashReceiptNo) {
        // Pre-select current values
        const ieSelect   = document.getElementById('ie-main-select');
        const cashSelect = document.getElementById('cash-receipt-select');

        for (let opt of ieSelect.options) {
            if (opt.value == ieMainNo) { opt.selected = true; break; }
        }
        for (let opt of cashSelect.options) {
            if (opt.value == cashReceiptNo) { opt.selected = true; break; }
        }

        document.getElementById('accounts-error').classList.remove('visible');
        document.getElementById('accounts-modal').style.display = 'flex';
    }

    function closeAccountsModal() {
        document.getElementById('accounts-modal').style.display = 'none';
    }

    function saveAccountsModal() {
        const ieSelect   = document.getElementById('ie-main-select');
        const cashSelect = document.getElementById('cash-receipt-select');
        const errorEl    = document.getElementById('accounts-error');
        const btn        = document.getElementById('accounts-save-btn');

        errorEl.classList.remove('visible');

        if (!ieSelect.value || !cashSelect.value) {
            errorEl.textContent = 'Please select both accounts.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Saving...';
        btn.disabled    = true;

        fetch(`/settings/active-accounts/accounts`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                ie_main:      ieSelect.value,
                cash_receipt: cashSelect.value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeAccountsModal();
                location.reload();
            } else {
                errorEl.textContent = data.message ?? 'Failed to update.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Save';
            btn.disabled    = false;
        });
    }

    // Close modals on backdrop click
    document.getElementById('account-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.getElementById('accounts-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAccountsModal();
    });
</script>
@endpush