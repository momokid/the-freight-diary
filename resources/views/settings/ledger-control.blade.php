@extends('layouts.app')

@section('title', 'Ledger Control')
@section('page-title', 'Ledger Control Setup')

@section('content')

<div style="display: flex; gap: 1.5rem; height: calc(100vh - 120px);">

    {{-- ── Left Panel: Add New Ledger Control ── --}}
    <div style="width: 300px; flex-shrink: 0;">
        <div class="card h-full" style="display: flex; flex-direction: column;">

            <p class="form-title">New Ledger Control</p>
            <p class="form-subtitle">Add a new control group for ledger accounts</p>

            <div class="form-group">
                <label class="form-label">Control Name</label>
                <input
                    type="text"
                    id="control-name-input"
                    placeholder="e.g. Bank Ctrl"
                    maxlength="100"
                    class="form-input">
                <p id="control-name-error" class="form-error"></p>
            </div>

            <button onclick="addControl()" id="add-btn" class="btn-primary">
                Add Ledger Control
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Controls ── --}}
    <div style="flex: 1; min-width: 0;">
        <div class="card h-full" style="display: flex; flex-direction: column;">

            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-shrink: 0;">
                <div>
                    <p class="form-title">Existing Ledger Controls</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        <span id="active-count">{{ $activeControls->count() }}</span> active controls
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
                            placeholder="Search controls..."
                            oninput="filterControls()"
                            class="form-input"
                            style="padding-left: 32px; width: 200px;">
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
                            <th style="width: 100px;">Control ID</th>
                            <th>Control Name</th>
                            <th style="width: 140px;">Added By</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="controls-table-body">

                        {{-- Active controls --}}
                        @forelse($activeControls as $control)
                        <tr class="control-row active-row" data-name="{{ strtolower($control->ControlName) }}">
                            <td class="td-mono">{{ $control->ControlID }}</td>
                            <td>
                                {{-- Inline edit --}}
                                <span
                                    class="control-name-display"
                                    style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                    onmouseover="this.style.color='#16a34a'"
                                    onmouseout="this.style.color='var(--text-primary)'"
                                    onclick="startEdit(this, {{ $control->ControlID }})"
                                    title="Click to edit">
                                    {{ $control->ControlName }}
                                </span>
                                <input
                                    type="text"
                                    class="control-name-input form-input"
                                    style="display: none; width: 200px; padding: 6px 10px;"
                                    value="{{ $control->ControlName }}"
                                    data-original="{{ $control->ControlName }}"
                                    data-id="{{ $control->ControlID }}"
                                    onkeydown="handleEditKey(event, this)"
                                    onblur="saveEdit(this)">
                            </td>
                            <td class="td-muted">{{ $control->Username }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="deactivateControl({{ $control->ControlID }}, this)"
                                    class="btn-icon btn-icon-danger"
                                    title="Deactivate">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-active-row">
                            <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No active ledger controls found. Add one using the form on the left.
                            </td>
                        </tr>
                        @endforelse

                        {{-- Inactive controls — hidden by default --}}
                        @foreach($inactiveControls as $control)
                        <tr class="control-row inactive-row"
                            data-name="{{ strtolower($control->ControlName) }}"
                            style="display: none; opacity: 0.6;">
                            <td class="td-mono">{{ $control->ControlID }}</td>
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">
                                    {{ $control->ControlName }}
                                </span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">
                                    INACTIVE
                                </span>
                            </td>
                            <td class="td-muted">{{ $control->Username }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="restoreControl({{ $control->ControlID }}, this)"
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
    const CSRF = '{{ csrf_token() }}';
    let showingInactive = false;

    // ── Filter controls by search ──
    function filterControls() {
        const query = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.control-row').forEach(row => {
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

    // ── Add new control ──
    function addControl() {
        const input     = document.getElementById('control-name-input');
        const errorEl   = document.getElementById('control-name-error');
        const successEl = document.getElementById('add-success');
        const btn       = document.getElementById('add-btn');
        const name      = input.value.trim();

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!name) {
            errorEl.textContent = 'Control name is required.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.ledger-control.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ ControlName: name }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value           = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                errorEl.textContent = data.message ?? 'Failed to add control.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Ledger Control';
            btn.disabled    = false;
        });
    }

    document.getElementById('control-name-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') addControl();
    });

    // ── Inline edit ──
    function startEdit(span, id) {
        const input = span.nextElementSibling;
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

        fetch(`/settings/ledger-control/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ ControlName: newName }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                span.textContent = data.ControlName;
                input.setAttribute('data-original', data.ControlName);
                input.value = data.ControlName;
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

    // ── Deactivate ──
    function deactivateControl(id, btn) {
        if (!confirm('Deactivate this ledger control?')) return;

        fetch(`/settings/ledger-control/${id}/deactivate`, {
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
                const countEl     = document.getElementById('active-count');
                countEl.textContent = parseInt(countEl.textContent) - 1;
            } else {
                alert(data.message ?? 'Failed to deactivate.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }

    // ── Restore ──
    function restoreControl(id, btn) {
        fetch(`/settings/ledger-control/${id}/restore`, {
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