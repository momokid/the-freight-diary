@extends('layouts.app')

@section('title', 'Ledger Account')
@section('page-title', 'Ledger Account Setup')

@section('content')

<div style="display: flex; gap: 1.5rem; height: calc(100vh - 90px);">

    {{-- ── Left Panel: Add New Ledger Account ── --}}
    <div class="flex-shrink-0" style="width: 320px;">
        <div class="card h-full flex flex-col overflow-y-auto">
            <p class="form-title">New Ledger Account</p>
            <p class="form-subtitle">Add a new account to the ledger</p>

            {{-- Ledger Control --}}
            <div class="form-group">
                <label class="form-label">Ledger Control</label>
                <select id="control-input" class="form-input">
                    <option value="">Select control...</option>
                    @foreach($controls as $control)
                        <option value="{{ $control->ControlID }}">{{ $control->ControlName }}</option>
                    @endforeach
                </select>
                <p id="control-error" class="form-error"></p>
            </div>

            {{-- Account Type --}}
            <div class="form-group">
                <label class="form-label">Account Type</label>
                <select id="type-input" class="form-input" onchange="loadCategories()">
                    <option value="">Select type...</option>
                    <option value="GL">GL</option>
                    <option value="INCOME">Income</option>
                    <option value="EXPENDITURE">Expenditure</option>
                </select>
                <p id="type-error" class="form-error"></p>
            </div>

            {{-- Ledger Category --}}
            <div class="form-group">
                <label class="form-label">Ledger Category</label>
                <select id="category-input" class="form-input" onchange="onCategoryChange()">
                    <option value="">Select category...</option>
                </select>
                <p id="category-error" class="form-error"></p>
            </div>

            {{-- Class — auto filled --}}
            <div class="form-group">
                <label class="form-label">Class</label>
                <div id="class-display"
                    style="padding: 10px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted); font-size: 0.875rem;">
                    Auto-filled from category
                </div>
                <input type="hidden" id="class-input">
            </div>

            {{-- Account Name --}}
            <div class="form-group">
                <label class="form-label">Account Name</label>
                <input
                    type="text"
                    id="account-name-input"
                    placeholder="e.g. Access Bank A/C"
                    maxlength="150"
                    class="form-input">
                <p id="account-name-error" class="form-error"></p>
            </div>

            {{-- Nature --}}
            <div class="form-group">
                <label class="form-label">Nature</label>
                <select id="nature-input" class="form-input">
                    <option value="">Select nature...</option>
                    <option value="NB">NB — Non Billing</option>
                    <option value="BL">BL — Billing</option>
                </select>
                <p id="nature-error" class="form-error"></p>
            </div>

            <button onclick="addAccount()" id="add-btn" class="btn-primary">
                Add Ledger Account
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Accounts ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-shrink: 0;">
                <div>
                    <p class="form-title">Existing Ledger Accounts</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $accounts->count() }} active accounts
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    {{-- Search --}}
                    <div style="position: relative;">
                        <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Search accounts..."
                            oninput="filterAccounts()"
                            class="form-input"
                            style="padding-left: 32px; width: 220px;">
                    </div>

                    {{-- Show inactive toggle --}}
                    <button
                        id="toggle-inactive-btn"
                        onclick="toggleInactive()"
                        style="padding: 8px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; cursor: pointer; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted); transition: all 0.15s;">
                        Show Inactive
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Acc No</th>
                            <th>Account Name</th>
                            <th style="width: 140px;">Control</th>
                            <th style="width: 60px;">Class</th>
                            <th style="width: 100px;">Type</th>
                            <th style="width: 60px;">Nature</th>
                            <th style="width: 100px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="accounts-table-body">

                        {{-- Active accounts --}}
                        @forelse($accounts as $account)
                        <tr class="account-row active-row"
                            data-name="{{ strtolower($account->AccountName) }} {{ strtolower($account->control->ControlName ?? '') }}">
                            <td class="td-mono">{{ $account->AccountNo }}</td>
                            <td>
                                {{-- Inline edit --}}
                                <span
                                    class="account-name-display"
                                    style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                    onmouseover="this.style.color='#16a34a'"
                                    onmouseout="this.style.color='var(--text-primary)'"
                                    onclick="startEdit(this, {{ $account->AccountNo }})"
                                    title="Click to edit">
                                    {{ $account->AccountName }}
                                </span>
                                <input
                                    type="text"
                                    class="account-name-input form-input"
                                    style="display: none; width: 200px; padding: 6px 10px;"
                                    value="{{ $account->AccountName }}"
                                    data-original="{{ $account->AccountName }}"
                                    data-id="{{ $account->AccountNo }}"
                                    onkeydown="handleEditKey(event, this)"
                                    onblur="saveEdit(this)">
                            </td>
                            <td class="td-muted">{{ $account->control->ControlName ?? '-' }}</td>
                            <td>
                                <span style="font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px;
                                    background: {{ $account->Class === 'Dr' ? 'rgba(59,130,246,0.1)' : 'rgba(22,163,74,0.1)' }};
                                    color: {{ $account->Class === 'Dr' ? '#3b82f6' : '#16a34a' }};">
                                    {{ $account->Class }}
                                </span>
                            </td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $account->Type }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $account->Nature }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                    {{-- Visibility toggle --}}
                                    <button
                                        onclick="toggleVisible({{ $account->AccountNo }}, this)"
                                        class="btn-icon"
                                        style="background: {{ $account->Visible ? 'rgba(22,163,74,0.08)' : 'rgba(0,0,0,0.05)' }}; color: {{ $account->Visible ? '#16a34a' : 'var(--text-muted)' }};"
                                        title="{{ $account->Visible ? 'Visible — click to hide' : 'Hidden — click to show' }}">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($account->Visible)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            @endif
                                        </svg>
                                    </button>

                                    {{-- Deactivate --}}
                                    <button
                                        onclick="deactivateAccount({{ $account->AccountNo }}, this)"
                                        class="btn-icon btn-icon-danger"
                                        title="Deactivate">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No active ledger accounts found. Add one using the form on the left.
                            </td>
                        </tr>
                        @endforelse

                        {{-- Inactive accounts --}}
                        @foreach($inactiveAccounts as $account)
                        <tr class="account-row inactive-row"
                            data-name="{{ strtolower($account->AccountName) }} {{ strtolower($account->control->ControlName ?? '') }}"
                            style="display: none; opacity: 0.6;">
                            <td class="td-mono">{{ $account->AccountNo }}</td>
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">
                                    {{ $account->AccountName }}
                                </span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">
                                    INACTIVE
                                </span>
                            </td>
                            <td class="td-muted">{{ $account->control->ControlName ?? '-' }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $account->Class }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $account->Type }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $account->Nature }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="restoreAccount({{ $account->AccountNo }}, this)"
                                    class="btn-icon btn-icon-success"
                                    title="Restore">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const CSRF       = '{{ csrf_token() }}';
    let showingInactive = false;
    let categoriesMap   = {}; // stores category data for auto-fill

    // ── Load categories by type via AJAX ──
    function loadCategories() {
        const type          = document.getElementById('type-input').value;
        const categorySelect = document.getElementById('category-input');
        const classDisplay  = document.getElementById('class-display');
        const classInput    = document.getElementById('class-input');

        // Reset
        categorySelect.innerHTML = '<option value="">Select category...</option>';
        classDisplay.textContent = 'Auto-filled from category';
        classInput.value         = '';
        categoriesMap            = {};

        if (!type) return;

        fetch(`{{ route('settings.ledger-account.categories-by-type') }}?type=${type}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            data.forEach(cat => {
                categoriesMap[cat.SubCategoryID] = cat;
                const option       = document.createElement('option');
                option.value       = cat.SubCategoryID;
                option.textContent = `${cat.CategoryName} — ${cat.SubCategoryName}`;
                categorySelect.appendChild(option);
            });
        })
        .catch(() => {
            categorySelect.innerHTML = '<option value="">Failed to load categories</option>';
        });
    }

    // ── Auto-fill Class when category is selected ──
    function onCategoryChange() {
        const categoryId   = document.getElementById('category-input').value;
        const classDisplay = document.getElementById('class-display');
        const classInput   = document.getElementById('class-input');

        if (!categoryId || !categoriesMap[categoryId]) {
            classDisplay.textContent = 'Auto-filled from category';
            classDisplay.style.color = 'var(--text-muted)';
            classInput.value         = '';
            return;
        }

        const cat        = categoriesMap[categoryId];
        classDisplay.textContent = cat.Class === 'Dr' ? 'Dr — Debit' : 'Cr — Credit';
        classDisplay.style.color = cat.Class === 'Dr' ? '#3b82f6' : '#16a34a';
        classInput.value         = cat.Class;
    }

    // ── Filter accounts by search ──
    // CHANGED: client side search — works across all records since everything is loaded
function filterAccounts() {
    const query = document.getElementById('search-input').value.toLowerCase();
    document.querySelectorAll('.account-row').forEach(row => {
        const name    = row.getAttribute('data-name');
        const visible = name.includes(query);
        if (row.classList.contains('inactive-row')) {
            row.style.display = showingInactive && visible ? '' : 'none';
        } else {
            row.style.display = visible ? '' : 'none';
        }
    });
}

    // ── Toggle inactive rows ──
    function toggleInactive() {
        showingInactive = !showingInactive;
        const btn = document.getElementById('toggle-inactive-btn');
        document.querySelectorAll('.inactive-row').forEach(row => {
            row.style.display = showingInactive ? '' : 'none';
        });
        btn.style.background  = showingInactive ? 'rgba(22,163,74,0.08)' : 'var(--content-bg)';
        btn.style.borderColor = showingInactive ? 'rgba(22,163,74,0.3)'  : 'var(--border-color)';
        btn.style.color       = showingInactive ? '#16a34a'               : 'var(--text-muted)';
        btn.textContent       = showingInactive ? 'Hide Inactive'         : 'Show Inactive';
    }

    // ── Add account ──
    function addAccount() {
        const controlEl  = document.getElementById('control-input');
        const typeEl     = document.getElementById('type-input');
        const categoryEl = document.getElementById('category-input');
        const classEl    = document.getElementById('class-input');
        const nameEl     = document.getElementById('account-name-input');
        const natureEl   = document.getElementById('nature-input');
        const successEl  = document.getElementById('add-success');
        const btn        = document.getElementById('add-btn');

        // Reset errors
        ['control', 'type', 'category', 'account-name', 'nature'].forEach(f => {
            const el = document.getElementById(f + '-error');
            if (el) el.classList.remove('visible');
        });
        successEl.classList.remove('visible');

        let valid = true;

        if (!controlEl.value) {
            document.getElementById('control-error').textContent = 'Please select a ledger control.';
            document.getElementById('control-error').classList.add('visible');
            valid = false;
        }
        if (!typeEl.value) {
            document.getElementById('type-error').textContent = 'Please select an account type.';
            document.getElementById('type-error').classList.add('visible');
            valid = false;
        }
        if (!categoryEl.value) {
            document.getElementById('category-error').textContent = 'Please select a ledger category.';
            document.getElementById('category-error').classList.add('visible');
            valid = false;
        }
        if (!nameEl.value.trim()) {
            document.getElementById('account-name-error').textContent = 'Account name is required.';
            document.getElementById('account-name-error').classList.add('visible');
            valid = false;
        }
        if (!natureEl.value) {
            document.getElementById('nature-error').textContent = 'Please select a nature.';
            document.getElementById('nature-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.ledger-account.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                ControlID:   controlEl.value,
                CategoryID:  categoryEl.value,
                Class:       classEl.value,
                Type:        typeEl.value,
                AccountName: nameEl.value.trim(),
                Nature:      natureEl.value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nameEl.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                document.getElementById('account-name-error').textContent = data.message ?? 'Failed to add account.';
                document.getElementById('account-name-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('account-name-error').textContent = 'Something went wrong. Please try again.';
            document.getElementById('account-name-error').classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Ledger Account';
            btn.disabled    = false;
        });
    }

    // ── Inline edit ──
    function startEdit(span, id) {
        const input         = span.nextElementSibling;
        span.style.display  = 'none';
        input.style.display = 'inline-block';
        input.focus();
        input.select();
    }

    function handleEditKey(e, input) {
        if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { cancelEdit(input); }
    }

    function cancelEdit(input) {
        const span          = input.previousElementSibling;
        input.value         = input.getAttribute('data-original');
        input.style.display = 'none';
        span.style.display  = '';
    }

    function saveEdit(input) {
        const span     = input.previousElementSibling;
        const id       = input.getAttribute('data-id');
        const original = input.getAttribute('data-original');
        const newName  = input.value.trim();

        if (!newName || newName === original) {
            cancelEdit(input);
            return;
        }

        fetch(`/settings/ledger-account/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ AccountName: newName }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                span.textContent = data.AccountName;
                input.setAttribute('data-original', data.AccountName);
                input.value = data.AccountName;
            } else {
                cancelEdit(input);
                alert(data.message ?? 'Failed to update.');
            }
        })
        .catch(() => {
            cancelEdit(input);
            alert('Something went wrong. Please try again.');
        })
        .finally(() => {
            input.style.display = 'none';
            span.style.display  = '';
        });
    }

    // ── Toggle visibility ──
    function toggleVisible(id, btn) {
        fetch(`/settings/ledger-account/${id}/toggle-visible`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const visible = data.visible;
                btn.style.background = visible ? 'rgba(22,163,74,0.08)' : 'rgba(0,0,0,0.05)';
                btn.style.color      = visible ? '#16a34a' : 'var(--text-muted)';
                btn.title            = visible ? 'Visible — click to hide' : 'Hidden — click to show';

                // Swap icon
                btn.innerHTML = visible
                    ? `<svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                       </svg>`
                    : `<svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                       </svg>`;
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }

    // ── Deactivate ──
    function deactivateAccount(id, btn) {
        if (!confirm('Deactivate this ledger account?')) return;

        fetch(`/settings/ledger-account/${id}/deactivate`, {
            method: 'PATCH',
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
                alert(data.message ?? 'Failed to deactivate.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }

    // ── Restore ──
    function restoreAccount(id, btn) {
        fetch(`/settings/ledger-account/${id}/restore`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message ?? 'Failed to restore.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }
</script>
@endpush