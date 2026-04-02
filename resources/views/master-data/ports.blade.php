@extends('layouts.app')

@section('title', 'Ports')
@section('page-title', 'Port Management')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel: POL ── --}}
    <div class="flex-shrink-0" style="width: 300px;">
        <div class="card h-full flex flex-col">

            <p class="form-title">Port of Loading (POL)</p>
            <p class="form-subtitle">Add a new port of loading</p>

            <div class="form-group">
                <label class="form-label">Port Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="pol-name-input" placeholder="e.g. Shanghai" maxlength="60" class="form-input">
                <p id="pol-name-error" class="form-error"></p>
            </div>

            <button onclick="addPol()" id="pol-add-btn" class="btn-primary">Add POL</button>
            <p id="pol-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

            <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0;"></div>

            <p class="form-title">Port of Discharge (POD)</p>
            <p class="form-subtitle">Add a new port of discharge</p>

            <div class="form-group">
                <label class="form-label">Port Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="pod-name-input" placeholder="e.g. Tema" maxlength="60" class="form-input">
                <p id="pod-name-error" class="form-error"></p>
            </div>

            <button onclick="addPod()" id="pod-add-btn" class="btn-primary">Add POD</button>
            <p id="pod-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel ── --}}
    <div class="flex-1 min-w-0 flex gap-6">

        {{-- POL Table --}}
        <div class="flex-1 min-w-0">
            <div class="card h-full flex flex-col">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <div>
                        <p class="form-title">Ports of Loading</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $pols->count() }} ports</p>
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="pol-search" placeholder="Search..." oninput="filterPol()" class="form-input" style="padding-left: 32px; width: 160px;">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Port Name</th>
                                <th style="width: 60px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pol-tbody">
                            @forelse($pols as $pol)
                            <tr class="pol-row" data-name="{{ strtolower($pol->POL_Name) }}">
                                <td>
                                    <span class="pol-name-display" style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                        onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                                        onclick="startInlineEdit(this, {{ $pol->POL_ID }}, 'pol')" title="Click to edit">
                                        {{ $pol->POL_Name }}
                                    </span>
                                    <input type="text" class="form-input pol-name-input"
                                        style="display: none; width: 150px; padding: 6px 10px;"
                                        value="{{ $pol->POL_Name }}"
                                        data-original="{{ $pol->POL_Name }}"
                                        data-id="{{ $pol->POL_ID }}"
                                        onkeydown="handleInlineKey(event, this, 'pol')"
                                        onblur="saveInlineEdit(this, 'pol')">
                                </td>
                                <td style="text-align: center;">
                                    <button onclick="deletePol({{ $pol->POL_ID }}, this)" class="btn-icon btn-icon-danger" title="Delete">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" style="padding: 2rem; text-align: center; color: var(--text-muted);">No POL found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- POD Table --}}
        <div class="flex-1 min-w-0">
            <div class="card h-full flex flex-col">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <div>
                        <p class="form-title">Ports of Discharge</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $pods->count() }} ports</p>
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="pod-search" placeholder="Search..." oninput="filterPod()" class="form-input" style="padding-left: 32px; width: 160px;">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Port Name</th>
                                <th style="width: 60px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pod-tbody">
                            @forelse($pods as $pod)
                            <tr class="pod-row" data-name="{{ strtolower($pod->POD_Name) }}">
                                <td>
                                    <span class="pod-name-display" style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                        onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                                        onclick="startInlineEdit(this, {{ $pod->POD_ID }}, 'pod')" title="Click to edit">
                                        {{ $pod->POD_Name }}
                                    </span>
                                    <input type="text" class="form-input pod-name-input"
                                        style="display: none; width: 150px; padding: 6px 10px;"
                                        value="{{ $pod->POD_Name }}"
                                        data-original="{{ $pod->POD_Name }}"
                                        data-id="{{ $pod->POD_ID }}"
                                        onkeydown="handleInlineKey(event, this, 'pod')"
                                        onblur="saveInlineEdit(this, 'pod')">
                                </td>
                                <td style="text-align: center;">
                                    <button onclick="deletePod({{ $pod->POD_ID }}, this)" class="btn-icon btn-icon-danger" title="Delete">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" style="padding: 2rem; text-align: center; color: var(--text-muted);">No POD found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';

    // ── Filter ──
    function filterPol() {
        const q = document.getElementById('pol-search').value.toLowerCase();
        document.querySelectorAll('.pol-row').forEach(row => {
            row.style.display = row.getAttribute('data-name').includes(q) ? '' : 'none';
        });
    }

    function filterPod() {
        const q = document.getElementById('pod-search').value.toLowerCase();
        document.querySelectorAll('.pod-row').forEach(row => {
            row.style.display = row.getAttribute('data-name').includes(q) ? '' : 'none';
        });
    }

    // ── Add POL ──
    function addPol() {
        const nameEl    = document.getElementById('pol-name-input');
        const errorEl   = document.getElementById('pol-name-error');
        const successEl = document.getElementById('pol-success');
        const btn       = document.getElementById('pol-add-btn');

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!nameEl.value.trim()) {
            errorEl.textContent = 'Port name is required.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.ports.pol.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ POL_Name: nameEl.value.trim() }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nameEl.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => successEl.classList.remove('visible'), 3000);

                // CHANGED: inject new row — no page reload
                const tbody = document.getElementById('pol-tbody');
                const emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();

                const tr = document.createElement('tr');
                tr.className = 'pol-row';
                tr.setAttribute('data-name', data.POL_Name.toLowerCase());
                tr.innerHTML = `
                    <td>
                        <span class="pol-name-display"
                            style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                            onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                            onclick="startInlineEdit(this, ${data.POL_ID}, 'pol')" title="Click to edit">
                            ${data.POL_Name}
                        </span>
                        <input type="text" class="form-input pol-name-input"
                            style="display: none; width: 150px; padding: 6px 10px;"
                            value="${data.POL_Name}"
                            data-original="${data.POL_Name}"
                            data-id="${data.POL_ID}"
                            onkeydown="handleInlineKey(event, this, 'pol')"
                            onblur="saveInlineEdit(this, 'pol')">
                    </td>
                    <td style="text-align: center;">
                        <button onclick="deletePol(${data.POL_ID}, this)" class="btn-icon btn-icon-danger" title="Delete">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>`;

                // Insert in alphabetical order
                const newName    = data.POL_Name.toLowerCase();
                const activeRows = Array.from(tbody.querySelectorAll('.pol-row'));
                const insertBefore = activeRows.find(row => row.getAttribute('data-name') > newName);
                insertBefore ? tbody.insertBefore(tr, insertBefore) : tbody.appendChild(tr);

                // Update count
                const countEl = document.querySelector('.form-title + p');
                if (countEl) countEl.textContent = `${tbody.querySelectorAll('.pol-row').length} ports`;
            } else {
                errorEl.textContent = data.message ?? 'Failed to add POL.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
        .finally(() => { btn.textContent = 'Add POL'; btn.disabled = false; });
    }

    // ── Add POD ──
    function addPod() {
        const nameEl    = document.getElementById('pod-name-input');
        const errorEl   = document.getElementById('pod-name-error');
        const successEl = document.getElementById('pod-success');
        const btn       = document.getElementById('pod-add-btn');

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!nameEl.value.trim()) {
            errorEl.textContent = 'Port name is required.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.ports.pod.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ POD_Name: nameEl.value.trim() }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
    nameEl.value = '';
    successEl.textContent = data.message;
    successEl.classList.add('visible');
    setTimeout(() => successEl.classList.remove('visible'), 3000);

    // CHANGED: inject new row — no page reload
    const tbody = document.getElementById('pod-tbody');
    const emptyRow = tbody.querySelector('td[colspan]');
    if (emptyRow) emptyRow.closest('tr').remove();

    const tr = document.createElement('tr');
    tr.className = 'pod-row';
    tr.setAttribute('data-name', data.POD_Name.toLowerCase());
    tr.innerHTML = `
        <td>
            <span class="pod-name-display"
                style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='var(--text-primary)'"
                onclick="startInlineEdit(this, ${data.POD_ID}, 'pod')" title="Click to edit">
                ${data.POD_Name}
            </span>
            <input type="text" class="form-input pod-name-input"
                style="display: none; width: 150px; padding: 6px 10px;"
                value="${data.POD_Name}"
                data-original="${data.POD_Name}"
                data-id="${data.POD_ID}"
                onkeydown="handleInlineKey(event, this, 'pod')"
                onblur="saveInlineEdit(this, 'pod')">
        </td>
        <td style="text-align: center;">
            <button onclick="deletePod(${data.POD_ID}, this)" class="btn-icon btn-icon-danger" title="Delete">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>`;

    // Insert in alphabetical order
    const newName    = data.POD_Name.toLowerCase();
    const activeRows = Array.from(tbody.querySelectorAll('.pod-row'));
    const insertBefore = activeRows.find(row => row.getAttribute('data-name') > newName);
    insertBefore ? tbody.insertBefore(tr, insertBefore) : tbody.appendChild(tr);

    // Update count
    const allCountEls = document.querySelectorAll('.form-title + p');
    if (allCountEls[1]) allCountEls[1].textContent = `${tbody.querySelectorAll('.pod-row').length} ports`;
} else {
                errorEl.textContent = data.message ?? 'Failed to add POD.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
        .finally(() => { btn.textContent = 'Add POD'; btn.disabled = false; });
    }

    // ── Inline edit (shared for POL and POD) ──
    function startInlineEdit(span, id, type) {
        const input = span.nextElementSibling;
        span.style.display  = 'none';
        input.style.display = 'inline-block';
        input.focus();
        input.select();
    }

    function handleInlineKey(e, input, type) {
        if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { cancelInlineEdit(input); }
    }

    function cancelInlineEdit(input) {
        const span = input.previousElementSibling;
        input.value         = input.getAttribute('data-original');
        input.style.display = 'none';
        span.style.display  = '';
    }

    function saveInlineEdit(input, type) {
        const span     = input.previousElementSibling;
        const id       = input.getAttribute('data-id');
        const original = input.getAttribute('data-original');
        const newName  = input.value.trim();

        if (!newName || newName === original) { cancelInlineEdit(input); return; }

        const url      = type === 'pol' ? `/master-data/ports/pol/${id}` : `/master-data/ports/pod/${id}`;
        const field    = type === 'pol' ? 'POL_Name' : 'POD_Name';
        const body     = {};
        body[field]    = newName;

        fetch(url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const nameKey = type === 'pol' ? 'POL_Name' : 'POD_Name';
                span.textContent = data[nameKey];
                input.setAttribute('data-original', data[nameKey]);
                input.value = data[nameKey];
            } else { cancelInlineEdit(input); alert(data.message ?? 'Failed to update.'); }
        })
        .catch(() => { cancelInlineEdit(input); alert('Something went wrong.'); })
        .finally(() => { input.style.display = 'none'; span.style.display = ''; });
    }

    // ── Delete ──
    function deletePol(id, btn) {
        if (!confirm('Delete this Port of Loading? This cannot be undone.')) return;
        fetch(`/master-data/ports/pol/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) btn.closest('tr').remove(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }

    function deletePod(id, btn) {
        if (!confirm('Delete this Port of Discharge? This cannot be undone.')) return;
        fetch(`/master-data/ports/pod/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) btn.closest('tr').remove(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }
</script>
@endpush