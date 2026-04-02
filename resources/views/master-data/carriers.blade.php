@extends('layouts.app')

@section('title', 'Carriers')
@section('page-title', 'Carrier Management')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel ── --}}
    <div class="flex-shrink-0" style="width: 300px;">
        <div class="card h-full flex flex-col">

            <p class="form-title">New Carrier</p>
            <p class="form-subtitle">Add a new shipping line carrier</p>

            <div class="form-group">
                <label class="form-label">Carrier Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="name-input" placeholder="e.g. Maersk" class="form-input">
                <p id="name-error" class="form-error"></p>
            </div>

            <button onclick="addCarrier()" id="add-btn" class="btn-primary">Add Carrier</button>
            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <p class="form-title">Existing Carriers</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $carriers->count() }} active carriers</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search carriers..." oninput="filterRows()" class="form-input" style="padding-left: 32px; width: 200px;">
                    </div>
                    <button id="toggle-inactive-btn" onclick="toggleInactive()"
                        style="padding: 8px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; cursor: pointer; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted);">
                        Show Inactive
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Carrier Name</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carriers as $carrier)
                        <tr class="carrier-row active-row" data-name="{{ strtolower($carrier->CarrierName) }}">
                            <td>
                                <span class="carrier-name-display" style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                    onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                                    onclick="startEdit(this, {{ $carrier->CarrierID }})" title="Click to edit">
                                    {{ $carrier->CarrierName }}
                                </span>
                                <input type="text" class="form-input carrier-name-input"
                                    style="display: none; width: 200px; padding: 6px 10px;"
                                    value="{{ $carrier->CarrierName }}"
                                    data-original="{{ $carrier->CarrierName }}"
                                    data-id="{{ $carrier->CarrierID }}"
                                    onkeydown="handleEditKey(event, this)"
                                    onblur="saveEdit(this)">
                            </td>
                            <td style="text-align: center;">
                                <button onclick="deactivate({{ $carrier->CarrierID }}, this)" class="btn-icon btn-icon-danger" title="Deactivate">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="padding: 2rem; text-align: center; color: var(--text-muted);">No carriers found.</td></tr>
                        @endforelse

                        @foreach($inactiveCarriers as $carrier)
                        <tr class="carrier-row inactive-row" data-name="{{ strtolower($carrier->CarrierName) }}" style="display: none; opacity: 0.6;">
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">{{ $carrier->CarrierName }}</span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">INACTIVE</span>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="restore({{ $carrier->CarrierID }}, this)" class="btn-icon btn-icon-success" title="Restore">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
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

    function filterRows() {
        const query = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.carrier-row').forEach(row => {
            const name = row.getAttribute('data-name');
            const visible = name.includes(query);
            if (row.classList.contains('inactive-row')) {
                row.style.display = showingInactive && visible ? '' : 'none';
            } else {
                row.style.display = visible ? '' : 'none';
            }
        });
    }

    function toggleInactive() {
        showingInactive = !showingInactive;
        const btn = document.getElementById('toggle-inactive-btn');
        document.querySelectorAll('.inactive-row').forEach(row => row.style.display = showingInactive ? '' : 'none');
        btn.style.background  = showingInactive ? 'rgba(22,163,74,0.08)' : 'var(--content-bg)';
        btn.style.borderColor = showingInactive ? 'rgba(22,163,74,0.3)'  : 'var(--border-color)';
        btn.style.color       = showingInactive ? '#16a34a'               : 'var(--text-muted)';
        btn.textContent       = showingInactive ? 'Hide Inactive'         : 'Show Inactive';
    }

    function addCarrier() {
        const nameEl    = document.getElementById('name-input');
        const errorEl   = document.getElementById('name-error');
        const successEl = document.getElementById('add-success');
        const btn       = document.getElementById('add-btn');

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!nameEl.value.trim()) {
            errorEl.textContent = 'Carrier name is required.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.carriers.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ CarrierName: nameEl.value.trim() }),
        })
        .then(res => res.json())
        .then(data => {
    if (data.success) {
        nameEl.value = '';
        successEl.textContent = data.message;
        successEl.classList.add('visible');
        setTimeout(() => successEl.classList.remove('visible'), 3000);

        // CHANGED: inject new row directly into table — no page reload
        const tbody = document.querySelector('table tbody');
        const emptyRow = tbody.querySelector('td[colspan]');
        if (emptyRow) emptyRow.closest('tr').remove();

        const tr = document.createElement('tr');
        tr.className = 'carrier-row active-row';
        tr.setAttribute('data-name', data.CarrierName.toLowerCase());
        tr.innerHTML = `
            <td>
                <span class="carrier-name-display"
                    style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                    onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                    onclick="startEdit(this, ${data.CarrierID})" title="Click to edit">
                    ${data.CarrierName}
                </span>
                <input type="text" class="form-input carrier-name-input"
                    style="display: none; width: 200px; padding: 6px 10px;"
                    value="${data.CarrierName}"
                    data-original="${data.CarrierName}"
                    data-id="${data.CarrierID}"
                    onkeydown="handleEditKey(event, this)"
                    onblur="saveEdit(this)">
            </td>
            <td style="text-align: center;">
                <button onclick="deactivate(${data.CarrierID}, this)" class="btn-icon btn-icon-danger" title="Deactivate">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </button>
            </td>`;

        // CHANGED: insert in alphabetical order
        const newName = data.CarrierName.toLowerCase();
        const activeRows = Array.from(tbody.querySelectorAll('.active-row'));
        const insertBefore = activeRows.find(row => row.getAttribute('data-name') > newName);
        if (insertBefore) {
            tbody.insertBefore(tr, insertBefore);
        } else {
            // Insert before first inactive row or append
            const firstInactive = tbody.querySelector('.inactive-row');
            if (firstInactive) {
                tbody.insertBefore(tr, firstInactive);
            } else {
                tbody.appendChild(tr);
            }
        }

        // Update count
        const countEl = document.querySelector('.form-title + p');
        if (countEl) {
            const current = parseInt(countEl.textContent) || 0;
            countEl.textContent = `${current + 1} active carriers`;
        }
    } else {
        errorEl.textContent = data.message ?? 'Failed to add carrier.';
        errorEl.classList.add('visible');
    }
})
        .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
        .finally(() => { btn.textContent = 'Add Carrier'; btn.disabled = false; });
    }

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
        const span = input.previousElementSibling;
        input.value         = input.getAttribute('data-original');
        input.style.display = 'none';
        span.style.display  = '';
    }

    function saveEdit(input) {
        const span     = input.previousElementSibling;
        const id       = input.getAttribute('data-id');
        const original = input.getAttribute('data-original');
        const newName  = input.value.trim();

        if (!newName || newName === original) { cancelEdit(input); return; }

        fetch(`/master-data/carriers/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ CarrierName: newName }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                span.textContent = data.CarrierName;
                input.setAttribute('data-original', data.CarrierName);
                input.value = data.CarrierName;
            } else { cancelEdit(input); alert(data.message ?? 'Failed to update.'); }
        })
        .catch(() => { cancelEdit(input); alert('Something went wrong.'); })
        .finally(() => { input.style.display = 'none'; span.style.display = ''; });
    }

    function deactivate(id, btn) {
        if (!confirm('Deactivate this carrier?')) return;
        fetch(`/master-data/carriers/${id}/deactivate`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) btn.closest('tr').remove(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }

    function restore(id, btn) {
        fetch(`/master-data/carriers/${id}/restore`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }
</script>
@endpush