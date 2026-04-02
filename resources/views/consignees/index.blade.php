@extends('layouts.app')

@section('title', 'Consignee Management')
@section('page-title', 'Consignee Management')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel: Add New Consignee ── --}}
    <div class="shrink-0" style="width: 340px;">
        <div class="card h-full flex flex-col overflow-y-auto">

            <p class="form-title">New Consignee</p>
            <p class="form-subtitle">Add a new consignee to the system</p>

            <div class="form-group">
                <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="fullname-input" placeholder="e.g. John Mensah Enterprises" maxlength="500" class="form-input">
                <p id="fullname-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number <span style="color: #ef4444;">*</span></label>
                <input type="text" id="telno-input" placeholder="e.g. 0244000000" maxlength="30" class="form-input">
                <p id="telno-error" class="form-error"></p>
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

            <button onclick="addConsignee()" id="add-btn" class="btn-primary">
                Add Consignee
            </button>

            <p id="add-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel: Existing Consignees ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div>
                    <p class="form-title">Existing Consignees</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $consignees->total() }} active consignees
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    {{-- CHANGED: no form needed — AJAX search --}}
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
                            placeholder="Search consignees..."
                            class="form-input"
                            style="padding-left: 32px; width: 220px;"
                            oninput="debounceSearch()">
                    </div>

                    {{-- Show inactive toggle --}}
                    <button
                        id="toggle-inactive-btn"
                        onclick="toggleInactive()"
                        style="padding: 8px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; cursor: pointer; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted);">
                        Show Inactive
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Full Name</th>
                            <th style="width: 130px;">Phone</th>
                            <th>Address</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Active consignees --}}
                        @forelse($consignees as $consignee)
                        <tr>
                            <td class="td-mono">{{ $consignee->ConsigneeID }}</td>
                            <td style="font-weight: 500; color: var(--text-primary);">
                                {{ $consignee->FullName }}
                            </td>
                            <td class="td-muted">{{ $consignee->TelNo }}</td>
                            <td class="td-muted" style="font-size: 0.8rem;">
                                {{ $consignee->Address1 }}
                                @if($consignee->Address2) , {{ $consignee->Address2 }} @endif
                                @if($consignee->Address3) , {{ $consignee->Address3 }} @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Edit --}}
                                    <button
                                        onclick="openEditModal({{ $consignee->ConsigneeID }}, '{{ addslashes($consignee->FullName) }}', '{{ addslashes($consignee->TelNo) }}', '{{ addslashes($consignee->Address1) }}', '{{ addslashes($consignee->Address2) }}', '{{ addslashes($consignee->Address3) }}')"
                                        class="btn-icon btn-icon-success"
                                        title="Edit">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Deactivate --}}
                                    <button
                                        onclick="deactivateConsignee({{ $consignee->ConsigneeID }}, this)"
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
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No consignees found. Add one using the form on the left.
                            </td>
                        </tr>
                        @endforelse

                        {{-- Inactive consignees --}}
                        @foreach($inactiveConsignees as $consignee)
                        <tr class="inactive-row" style="display: none; opacity: 0.6;">
                            <td class="td-mono">{{ $consignee->ConsigneeID }}</td>
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">
                                    {{ $consignee->FullName }}
                                </span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">
                                    INACTIVE
                                </span>
                            </td>
                            <td class="td-muted">{{ $consignee->TelNo }}</td>
                            <td class="td-muted" style="font-size: 0.8rem;">{{ $consignee->Address1 }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="restoreConsignee({{ $consignee->ConsigneeID }}, this)"
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

            {{-- Pagination --}}
            @if($consignees->hasPages())
            <div class="shrink-0 flex items-center justify-between pt-4" style="border-top: 1px solid var(--border-color);">
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    Showing {{ $consignees->firstItem() }} to {{ $consignees->lastItem() }} of {{ $consignees->total() }}
                </p>
                <div class="flex items-center gap-1">
                    @if($consignees->onFirstPage())
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; color: var(--text-muted); border: 1px solid var(--border-color); background: var(--content-bg);">Previous</span>
                    @else
                        <a href="{{ $consignees->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; color: var(--text-primary); border: 1px solid var(--border-color); background: var(--card-bg); text-decoration: none;">Previous</a>
                    @endif

                   @php
    $current  = $consignees->currentPage();
    $last     = $consignees->lastPage();
    $pages    = [];

    // Always show first 2, last 2, current and 2 around current
    for ($p = 1; $p <= $last; $p++) {
        if (
            $p <= 7 ||
            $p >= $last - 1 ||
            abs($p - $current) <= 1
        ) {
            $pages[] = $p;
        }
    }
    $pages = array_unique($pages);
    sort($pages);
@endphp

        @php $prev = null; @endphp
        @foreach($pages as $page)
            @if($prev !== null && $page - $prev > 1)
                <span style="padding: 6px 4px; font-size: 0.75rem; color: var(--text-muted);">...</span>
            @endif

            @if($page == $current)
                <span style="padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: white; background: #16a34a;">{{ $page }}</span>
            @else
                <a href="{{ $consignees->url($page) }}" style="padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; color: var(--text-primary); border: 1px solid var(--border-color); background: var(--card-bg); text-decoration: none;">{{ $page }}</a>
            @endif

            @php $prev = $page; @endphp
        @endforeach


                    @if($consignees->hasMorePages())
                        <a href="{{ $consignees->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; color: var(--text-primary); border: 1px solid var(--border-color); background: var(--card-bg); text-decoration: none;">Next</a>
                    @else
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; color: var(--text-muted); border: 1px solid var(--border-color); background: var(--content-bg);">Next</span>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

{{-- ── Edit Modal ── --}}
<div id="edit-modal" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem; max-height: 90vh; overflow-y: auto;">

        <div class="flex items-center justify-between mb-4">
            <p class="form-title">Edit Consignee</p>
            <button onclick="closeEditModal()" style="color: var(--text-muted); background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
        </div>

        <input type="hidden" id="edit-id">

        <div class="form-group">
            <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="edit-fullname" maxlength="500" class="form-input">
            <p id="edit-fullname-error" class="form-error"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number <span style="color: #ef4444;">*</span></label>
            <input type="text" id="edit-telno" maxlength="30" class="form-input">
            <p id="edit-telno-error" class="form-error"></p>
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

        <div class="flex gap-3">
            <button onclick="closeEditModal()" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveEdit()" id="edit-save-btn" class="btn-primary" style="flex: 1;">Save Changes</button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let showingInactive = false;

    // ── Toggle inactive ──
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

    // ── Add consignee ──
    function addConsignee() {
        const fullnameEl = document.getElementById('fullname-input');
        const telnoEl    = document.getElementById('telno-input');
        const address1El = document.getElementById('address1-input');
        const address2El = document.getElementById('address2-input');
        const address3El = document.getElementById('address3-input');
        const successEl  = document.getElementById('add-success');
        const btn        = document.getElementById('add-btn');

        // Reset errors
        ['fullname', 'telno', 'address1'].forEach(f => {
            document.getElementById(f + '-error').classList.remove('visible');
        });
        successEl.classList.remove('visible');

        let valid = true;
        if (!fullnameEl.value.trim()) {
            document.getElementById('fullname-error').textContent = 'Full name is required.';
            document.getElementById('fullname-error').classList.add('visible');
            valid = false;
        }
        if (!telnoEl.value.trim()) {
            document.getElementById('telno-error').textContent = 'Phone number is required.';
            document.getElementById('telno-error').classList.add('visible');
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

        fetch('{{ route("master-data.consignees.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                FullName: fullnameEl.value.trim(),
                TelNo:    telnoEl.value.trim(),
                Address1: address1El.value.trim(),
                Address2: address2El.value.trim(),
                Address3: address3El.value.trim(),
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fullnameEl.value = '';
                telnoEl.value    = '';
                address1El.value = '';
                address2El.value = '';
                address3El.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                document.getElementById('fullname-error').textContent = data.message ?? 'Failed to add consignee.';
                document.getElementById('fullname-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('fullname-error').textContent = 'Something went wrong. Please try again.';
            document.getElementById('fullname-error').classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Consignee';
            btn.disabled    = false;
        });
    }

    // ── Edit modal ──
    function openEditModal(id, fullname, telno, address1, address2, address3) {
        document.getElementById('edit-id').value       = id;
        document.getElementById('edit-fullname').value = fullname;
        document.getElementById('edit-telno').value    = telno;
        document.getElementById('edit-address1').value = address1;
        document.getElementById('edit-address2').value = address2;
        document.getElementById('edit-address3').value = address3;

        ['edit-fullname', 'edit-telno', 'edit-address1'].forEach(f => {
            document.getElementById(f + '-error').classList.remove('visible');
        });

        document.getElementById('edit-modal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }

    function saveEdit() {
        const id         = document.getElementById('edit-id').value;
        const fullnameEl = document.getElementById('edit-fullname');
        const telnoEl    = document.getElementById('edit-telno');
        const address1El = document.getElementById('edit-address1');
        const address2El = document.getElementById('edit-address2');
        const address3El = document.getElementById('edit-address3');
        const btn        = document.getElementById('edit-save-btn');

        let valid = true;
        if (!fullnameEl.value.trim()) {
            document.getElementById('edit-fullname-error').textContent = 'Full name is required.';
            document.getElementById('edit-fullname-error').classList.add('visible');
            valid = false;
        }
        if (!telnoEl.value.trim()) {
            document.getElementById('edit-telno-error').textContent = 'Phone number is required.';
            document.getElementById('edit-telno-error').classList.add('visible');
            valid = false;
        }
        if (!address1El.value.trim()) {
            document.getElementById('edit-address1-error').textContent = 'Address line 1 is required.';
            document.getElementById('edit-address1-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Saving...';
        btn.disabled    = true;

        fetch(`/master-data/consignees/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                FullName: fullnameEl.value.trim(),
                TelNo:    telnoEl.value.trim(),
                Address1: address1El.value.trim(),
                Address2: address2El.value.trim(),
                Address3: address3El.value.trim(),
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                location.reload();
            } else {
                document.getElementById('edit-fullname-error').textContent = data.message ?? 'Failed to update.';
                document.getElementById('edit-fullname-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('edit-fullname-error').textContent = 'Something went wrong.';
            document.getElementById('edit-fullname-error').classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Save Changes';
            btn.disabled    = false;
        });
    }

    // ── Deactivate ──
    function deactivateConsignee(id, btn) {
        if (!confirm('Deactivate this consignee?')) return;

        fetch(`/master-data/consignees/${id}/deactivate`, {
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
        .catch(() => alert('Something went wrong.'));
    }

    // ── Restore ──
    function restoreConsignee(id, btn) {
        fetch(`/master-data/consignees/${id}/restore`, {
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
        .catch(() => alert('Something went wrong.'));
    }

    // CHANGED: AJAX search — only table body updates, no full page reload
let searchTimer = null;

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        searchConsignees();
    }, 800);
}

function searchConsignees() {
    const query = document.getElementById('search-input').value;
    const tbody = document.querySelector('table tbody');

    // Show skeleton while loading
    showTableSkeleton();

    fetch(`{{ route('master-data.consignees.table') }}?search=${encodeURIComponent(query)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        tbody.innerHTML = html;
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" style="padding: 2rem; text-align: center; color: #ef4444;">Failed to load results. Please try again.</td></tr>';
    });
}

function showTableSkeleton() {
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;
    let html = '';
    for (let i = 0; i < 8; i++) {
        html += `
            <tr>
                <td><span class="skeleton" style="width: 40px;"></span></td>
                <td><span class="skeleton" style="width: 70%;"></span></td>
                <td><span class="skeleton" style="width: 100px;"></span></td>
                <td><span class="skeleton" style="width: 80%;"></span></td>
                <td style="text-align: center;"><span class="skeleton" style="width: 50px;"></span></td>
            </tr>`;
    }
    tbody.innerHTML = html;
}

    // Close modal on backdrop click
    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush