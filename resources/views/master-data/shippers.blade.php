@extends('layouts.app')

@section('title', 'Shippers')
@section('page-title', 'Shipper Management')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel: Add New Shipper ── --}}
    <div class="flex-shrink-0" style="width: 340px;">
        <div class="card h-full flex flex-col overflow-y-auto">

            <p class="form-title">New Shipper</p>
            <p class="form-subtitle">Add a new shipper to the system</p>

            <div class="form-group">
                <label class="form-label">Shipper Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="name-input" placeholder="Name of Shipper" maxlength="150" class="form-input">
                <p id="name-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Address Line 1 <span style="color: #ef4444;">*</span></label>
                <input type="text" id="address1-input" placeholder="Street / P.O. Box" maxlength="500" class="form-input">
                <p id="address1-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Address Line 2 <span style="color: var(--text-muted);">optional</span></label>
                <input type="text" id="address2-input" placeholder="City / Town" maxlength="500" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Address Line 3 <span style="color: var(--text-muted);">optional</span></label>
                <input type="text" id="address3-input" placeholder="Region / Country" maxlength="500" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Address Line 4 <span style="color: var(--text-muted);">optional</span></label>
                <input type="text" id="address4-input" placeholder="Additional info" maxlength="500" class="form-input">
            </div>

            <button onclick="addShipper()" id="add-btn" class="btn-primary">
                Add Shipper
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Shippers ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <p class="form-title">Existing Shippers</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $shippers->count() }} active shippers
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search shippers..." oninput="filterRows()" class="form-input" style="padding-left: 32px; width: 220px;">
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
                            <th>Shipper Name</th>
                            <th>Address</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="shippers-tbody">
                        @forelse($shippers as $shipper)
                        <tr class="shipper-row active-row" data-name="{{ strtolower($shipper->ShipperName) }}">
                            <td style="font-weight: 500; color: var(--text-primary);">{{ $shipper->ShipperName }}</td>
                            <td class="td-muted" style="font-size: 0.8rem;">
                                {{ $shipper->AddressLine1 }}
                                @if($shipper->AddressLine2), {{ $shipper->AddressLine2 }}@endif
                                @if($shipper->AddressLine3), {{ $shipper->AddressLine3 }}@endif
                                @if($shipper->AddressLine4), {{ $shipper->AddressLine4 }}@endif
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openEditModal({{ $shipper->ShipperID }}, '{{ addslashes($shipper->ShipperName) }}', '{{ addslashes($shipper->AddressLine1) }}', '{{ addslashes($shipper->AddressLine2) }}', '{{ addslashes($shipper->AddressLine3) }}', '{{ addslashes($shipper->AddressLine4) }}')"
                                        class="btn-icon btn-icon-success" title="Edit">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deactivate({{ $shipper->ShipperID }}, this)" class="btn-icon btn-icon-danger" title="Deactivate">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-muted);">No shippers found.</td></tr>
                        @endforelse

                        @foreach($inactiveShippers as $shipper)
                        <tr class="shipper-row inactive-row" data-name="{{ strtolower($shipper->ShipperName) }}" style="display: none; opacity: 0.6;">
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">{{ $shipper->ShipperName }}</span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">INACTIVE</span>
                            </td>
                            <td class="td-muted" style="font-size: 0.8rem;">{{ $shipper->AddressLine1 }}</td>
                            <td style="text-align: center;">
                                <button onclick="restore({{ $shipper->ShipperID }}, this)" class="btn-icon btn-icon-success" title="Restore">
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

{{-- Edit Modal --}}
<div id="edit-modal" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="flex items-center justify-between mb-4">
            <p class="form-title">Edit Shipper</p>
            <button onclick="closeModal()" style="color: var(--text-muted); background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
        </div>
        <input type="hidden" id="edit-id">
        <div class="form-group">
            <label class="form-label">Shipper Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="edit-name" maxlength="150" class="form-input">
            <p id="edit-name-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Address Line 1 <span style="color: #ef4444;">*</span></label>
            <input type="text" id="edit-address1" maxlength="500" class="form-input">
            <p id="edit-address1-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Address Line 2 <span style="color: var(--text-muted);">optional</span></label>
            <input type="text" id="edit-address2" maxlength="500" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Address Line 3 <span style="color: var(--text-muted);">optional</span></label>
            <input type="text" id="edit-address3" maxlength="500" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Address Line 4 <span style="color: var(--text-muted);">optional</span></label>
            <input type="text" id="edit-address4" maxlength="500" class="form-input">
        </div>
        <div class="flex gap-3">
            <button onclick="closeModal()" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveEdit()" id="edit-save-btn" class="btn-primary" style="flex: 1;">Save Changes</button>
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
        document.querySelectorAll('.shipper-row').forEach(row => {
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

    function addShipper() {
        const nameEl     = document.getElementById('name-input');
        const address1El = document.getElementById('address1-input');
        const btn        = document.getElementById('add-btn');
        const successEl  = document.getElementById('add-success');

        ['name', 'address1'].forEach(f => document.getElementById(f + '-error').classList.remove('visible'));
        successEl.classList.remove('visible');

        let valid = true;
        if (!nameEl.value.trim()) {
            document.getElementById('name-error').textContent = 'Shipper name is required.';
            document.getElementById('name-error').classList.add('visible');
            valid = false;
        }
        if (!address1El.value.trim()) {
            document.getElementById('address1-error').textContent = 'Address line 1 is required.';
            document.getElementById('address1-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.shippers.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                ShipperName:  nameEl.value.trim(),
                AddressLine1: address1El.value.trim(),
                AddressLine2: document.getElementById('address2-input').value.trim(),
                AddressLine3: document.getElementById('address3-input').value.trim(),
                AddressLine4: document.getElementById('address4-input').value.trim(),
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
    // Clear form
    nameEl.value = address1El.value = document.getElementById('address2-input').value =
    document.getElementById('address3-input').value = document.getElementById('address4-input').value = '';
    successEl.textContent = data.message;
    successEl.classList.add('visible');
    setTimeout(() => successEl.classList.remove('visible'), 3000);

    // CHANGED: inject new row — no page reload
    const tbody = document.getElementById('shippers-tbody');
    const emptyRow = tbody.querySelector('td[colspan]');
    if (emptyRow) emptyRow.closest('tr').remove();

    const address = [
        data.AddressLine1,
        data.AddressLine2,
        data.AddressLine3,
        data.AddressLine4
    ].filter(a => a).join(', ');

    const tr = document.createElement('tr');
    tr.className = 'shipper-row active-row';
    tr.setAttribute('data-name', data.ShipperName.toLowerCase());
    tr.innerHTML = `
        <td style="font-weight: 500; color: var(--text-primary);">${data.ShipperName}</td>
        <td class="td-muted" style="font-size: 0.8rem;">${address}</td>
        <td style="text-align: center;">
            <div class="flex items-center justify-center gap-1">
                <button onclick="openEditModal(${data.ShipperID}, '${data.ShipperName.replace(/'/g, "\\'")}', '${data.AddressLine1.replace(/'/g, "\\'")}', '', '', '')"
                    class="btn-icon btn-icon-success" title="Edit">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick="deactivate(${data.ShipperID}, this)" class="btn-icon btn-icon-danger" title="Deactivate">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </button>
            </div>
        </td>`;

    // Insert in alphabetical order
    const newName   = data.ShipperName.toLowerCase();
    const activeRows = Array.from(tbody.querySelectorAll('.active-row'));
    const insertBefore = activeRows.find(row => row.getAttribute('data-name') > newName);
    if (insertBefore) {
        tbody.insertBefore(tr, insertBefore);
    } else {
        const firstInactive = tbody.querySelector('.inactive-row');
        firstInactive ? tbody.insertBefore(tr, firstInactive) : tbody.appendChild(tr);
    }

    // Update count
    const countEl = document.querySelector('.form-title + p');
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        countEl.textContent = `${current + 1} active shippers`;
    }
}
        })
        .catch(() => {
            document.getElementById('name-error').textContent = 'Something went wrong.';
            document.getElementById('name-error').classList.add('visible');
        })
        .finally(() => { btn.textContent = 'Add Shipper'; btn.disabled = false; });
    }

    function openEditModal(id, name, a1, a2, a3, a4) {
        document.getElementById('edit-id').value       = id;
        document.getElementById('edit-name').value     = name;
        document.getElementById('edit-address1').value = a1;
        document.getElementById('edit-address2').value = a2;
        document.getElementById('edit-address3').value = a3;
        document.getElementById('edit-address4').value = a4;
        ['edit-name', 'edit-address1'].forEach(f => document.getElementById(f + '-error').classList.remove('visible'));
        document.getElementById('edit-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }

    function saveEdit() {
        const id   = document.getElementById('edit-id').value;
        const name = document.getElementById('edit-name').value.trim();
        const a1   = document.getElementById('edit-address1').value.trim();
        const btn  = document.getElementById('edit-save-btn');

        let valid = true;
        if (!name) {
            document.getElementById('edit-name-error').textContent = 'Shipper name is required.';
            document.getElementById('edit-name-error').classList.add('visible');
            valid = false;
        }
        if (!a1) {
            document.getElementById('edit-address1-error').textContent = 'Address line 1 is required.';
            document.getElementById('edit-address1-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Saving...';
        btn.disabled    = true;

        fetch(`/master-data/shippers/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                ShipperName:  name,
                AddressLine1: a1,
                AddressLine2: document.getElementById('edit-address2').value.trim(),
                AddressLine3: document.getElementById('edit-address3').value.trim(),
                AddressLine4: document.getElementById('edit-address4').value.trim(),
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { closeModal(); location.reload(); }
            else {
                document.getElementById('edit-name-error').textContent = data.message ?? 'Failed to update.';
                document.getElementById('edit-name-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('edit-name-error').textContent = 'Something went wrong.';
            document.getElementById('edit-name-error').classList.add('visible');
        })
        .finally(() => { btn.textContent = 'Save Changes'; btn.disabled = false; });
    }

    function deactivate(id, btn) {
        if (!confirm('Deactivate this shipper?')) return;
        fetch(`/master-data/shippers/${id}/deactivate`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) btn.closest('tr').remove(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }

    function restore(id, btn) {
        fetch(`/master-data/shippers/${id}/restore`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }

    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush