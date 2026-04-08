@extends('layouts.app')

@section('title', 'Consignment Cmdts')
@section('page-title', 'New Consignment — Commodities')

@section('content')

{{-- Pending containers warning --}}
@if($pendingContainers->isNotEmpty())
<div style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
    <svg style="width: 18px; height: 18px; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <p style="font-size: 0.8rem; font-weight: 600; color: #92400e;">Pending containers for BL# {{ $pendingBOL }}</p>
        <p style="font-size: 0.75rem; color: #92400e; margin-top: 2px;">You have {{ $pendingContainers->count() }} staged container(s). Submit or clear to start a new consignment.</p>
    </div>
    <button onclick="clearContainers()" style="margin-left: auto; padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(234,179,8,0.4); background: transparent; color: #92400e; font-size: 0.75rem; cursor: pointer;">
        Clear & Start New
    </button>
</div>
@endif

<div style="display: flex; flex-direction: column; gap: 1.25rem;">

    {{-- ── Section 1: Consignment Category ── --}}
    <div class="card">
        <p class="form-title">Consignment Category</p>
        <p class="form-subtitle">Select the commodity category and type for this consignment</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Select Category <span style="color: #ef4444;">*</span>
                    <button type="button" onclick="openQuickAddModal('category')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                </label>
                <select id="category-id" class="form-input" onchange="loadTypes()">
                    <option value="">Select category...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->ID }}">{{ $cat->CategoryName }}</option>
                    @endforeach
                </select>
                <p id="category-error" class="form-error"></p>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Category Type <span style="color: #ef4444;">*</span>
                    <button type="button" onclick="openQuickAddModal('type')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                </label>
                <select id="type-id" class="form-input" disabled>
                    <option value="">Select category first...</option>
                </select>
                <p id="type-error" class="form-error"></p>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Consignment Details ── --}}
    <div class="card">
        <p class="form-title">Consignment Details</p>
        <p class="form-subtitle">Enter the consignment information</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem;">

            {{-- BL --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Bill of Lading <span style="color: #ef4444;">*</span></label>
                <input type="text" id="bl" class="form-input" style="text-transform: uppercase;" placeholder="Enter BL number...">
                <p id="bl-error" class="form-error"></p>
            </div>

            {{-- Consignee --}}
            <div class="form-group" style="margin-bottom: 0; position: relative;">
                <label class="form-label">Consignee <span style="color: #ef4444;">*</span>
                    <button type="button" onclick="openQuickAddModal('consignee')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                </label>
                <input type="text" id="consignee-search" placeholder="Search consignee..." class="form-input" oninput="debounceSearch()">
                <div id="consignee-dropdown" style="display: none; position: absolute; z-index: 100; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; width: 100%; top: 100%;"></div>
                <input type="hidden" id="consignee-id">
                <p id="consignee-error" class="form-error"></p>
            </div>

            {{-- ETA --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">ETA <span style="color: #ef4444;">*</span></label>
                <input type="date" id="eta" class="form-input">
                <p id="eta-error" class="form-error"></p>
            </div>

            {{-- Carrier --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Shipping Line <span style="color: #ef4444;">*</span>
                    <button type="button" onclick="openQuickAddModal('carrier')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                </label>
                <select id="carrier-id" class="form-input">
                    <option value="">Select shipping line...</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->CarrierID }}">{{ $carrier->CarrierName }}</option>
                    @endforeach
                </select>
                <p id="carrier-error" class="form-error"></p>
            </div>

            {{-- Release Type --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Release Type <span style="color: #ef4444;">*</span>
                    <button type="button" onclick="openQuickAddModal('release')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                </label>
                <select id="release-type" class="form-input">
                    <option value="">Select release type...</option>
                    @foreach($releaseTypes as $rt)
                        <option value="{{ $rt->ID }}">{{ $rt->ReleaseType }}</option>
                    @endforeach
                </select>
                <p id="release-error" class="form-error"></p>
            </div>

            {{-- Location --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Location (Terminal) <span style="color: var(--text-muted);">optional</span></label>
                <input type="text" id="destination" class="form-input" placeholder="e.g. Tema Port Terminal 2">
            </div>

        </div>

        {{-- Container staging --}}
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">Container Details</p>
                    <p id="container-count" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">No containers added yet</p>
                </div>
                <button type="button" onclick="openContainerModal()"
                    style="padding: 8px 16px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Container
                </button>
            </div>

            <div id="containers-table">
                <div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                    No containers added yet. Click "Add Container" to add container details.
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <p id="submit-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
            <p id="submit-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>
            <button onclick="submitConsignment()" id="submit-btn"
                style="width: 100%; padding: 14px; border-radius: 10px; border: none; background: #16a34a; color: white; font-size: 0.925rem; font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                Add New Consignment
            </button>
        </div>
    </div>

</div>

{{-- ── Container Modal ── --}}
<div id="modal-container" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">Container Details</p>
            <button onclick="closeContainerModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Container# <span style="color: #ef4444;">*</span></label>
            <input type="text" id="modal-container-no" class="form-input" style="text-transform: uppercase;" placeholder="e.g. MSCU1234567">
            <p id="modal-container-no-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Container Size <span style="color: #ef4444;">*</span></label>
            <select id="modal-container-size" class="form-input">
                <option value="">Select size...</option>
                <option value="20">20ft</option>
                <option value="40">40ft</option>
                <option value="45">45ft</option>
            </select>
            <p id="modal-container-size-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Seal No <span style="color: var(--text-muted);">optional</span></label>
            <input type="text" id="modal-seal-no" class="form-input" style="text-transform: uppercase;" placeholder="e.g. SEAL123456">
        </div>
        <div class="form-group">
            <label class="form-label">Item Details <span style="color: #ef4444;">*</span></label>
            <textarea id="modal-item-details" rows="3" class="form-input" style="resize: none;" placeholder="Describe the goods in this container..."></textarea>
            <p id="modal-item-details-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button onclick="closeContainerModal()" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="addContainer()" id="modal-add-btn" class="btn-primary" style="flex: 1;">Add Container Details</button>
        </div>
        <p id="modal-error" class="form-error" style="margin-top: 8px; text-align: center;"></p>
    </div>
</div>

{{-- ── Commodity Type Quick Add Modal ── --}}
<div id="modal-quick-type" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">New Commodity Type</p>
            <button onclick="closeQuickAddModal('type')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Type Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-type-name" class="form-input">
            <p id="qam-type-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closeQuickAddModal('type')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAddType()" id="qam-type-btn" class="btn-primary" style="flex: 1;">Save</button>
        </div>
    </div>
</div>

{{-- Shared quick-add modals (consignee, carrier, category, release) --}}
@include('partials.quick-add-modals')

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let searchTimer = null;

// ── Load types by category ──
function loadTypes() {
    const categoryId = document.getElementById('category-id').value;
    const typeSelect = document.getElementById('type-id');

    typeSelect.innerHTML = '<option value="">Loading...</option>';
    typeSelect.disabled  = true;

    if (!categoryId) {
        typeSelect.innerHTML = '<option value="">Select category first...</option>';
        return;
    }

    fetch(`{{ route('cmdts.types-by-category') }}?category_id=${categoryId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        typeSelect.innerHTML = '<option value="">Select type...</option>';
        data.forEach(t => {
            const opt = document.createElement('option');
            opt.value       = t.TypeID;
            opt.textContent = t.TypeName;
            typeSelect.appendChild(opt);
        });
        typeSelect.disabled = false;
    })
    .catch(() => {
        typeSelect.innerHTML = '<option value="">Failed to load types</option>';
    });
}

// ── Consignee search ──
function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(searchConsignees, 400);
}

function searchConsignees() {
    const q        = document.getElementById('consignee-search').value.trim();
    const dropdown = document.getElementById('consignee-dropdown');

    if (!q) { dropdown.style.display = 'none'; return; }

    fetch(`{{ route('cmdts.consignee-search') }}?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.length) { dropdown.style.display = 'none'; return; }
        dropdown.innerHTML = data.map(c => `
            <div onclick="selectConsignee(${c.ConsigneeID}, '${c.FullName.replace(/'/g, "\\'")}')"
                style="padding: 10px 14px; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid var(--border-color);"
                onmouseover="this.style.background='var(--content-bg)'"
                onmouseout="this.style.background=''">
                <div style="font-weight: 500; color: var(--text-primary);">${c.FullName}</div>
                <div style="color: var(--text-muted); font-size: 0.75rem;">${c.TelNo}</div>
            </div>`).join('');
        dropdown.style.display = 'block';
    });
}

function selectConsignee(id, name) {
    document.getElementById('consignee-search').value           = name;
    document.getElementById('consignee-id').value               = id;
    document.getElementById('consignee-dropdown').style.display = 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#consignee-search') && !e.target.closest('#consignee-dropdown')) {
        document.getElementById('consignee-dropdown').style.display = 'none';
    }
});

// ── Container Modal ──
function openContainerModal() {
    document.getElementById('modal-container').style.display = 'flex';
    document.getElementById('modal-container-no').focus();
}

function closeContainerModal() {
    document.getElementById('modal-container').style.display    = 'none';
    document.getElementById('modal-container-no').value         = '';
    document.getElementById('modal-container-size').value       = '';
    document.getElementById('modal-seal-no').value              = '';
    document.getElementById('modal-item-details').value         = '';
    document.getElementById('modal-error').classList.remove('visible');
    ['modal-container-no-error', 'modal-container-size-error', 'modal-item-details-error'].forEach(id => {
        document.getElementById(id).classList.remove('visible');
    });
}

document.getElementById('modal-container').addEventListener('click', function(e) {
    if (e.target === this) closeContainerModal();
});

// ── Add container to staging ──
function addContainer() {
    const btn         = document.getElementById('modal-add-btn');
    const errorEl     = document.getElementById('modal-error');
    const containerNo = document.getElementById('modal-container-no').value.trim().toUpperCase();
    const size        = document.getElementById('modal-container-size').value;
    const sealNo      = document.getElementById('modal-seal-no').value.trim().toUpperCase();
    const itemDetails = document.getElementById('modal-item-details').value.trim();
    const bl          = document.getElementById('bl').value.trim().toUpperCase();

    errorEl.classList.remove('visible');

    let valid = true;
    const checks = [
        [!containerNo, 'modal-container-no-error', 'Container number is required.'],
        [!size, 'modal-container-size-error', 'Container size is required.'],
        [!itemDetails, 'modal-item-details-error', 'Item details are required.'],
    ];

    checks.forEach(([condition, errorId, message]) => {
        const el = document.getElementById(errorId);
        el.classList.remove('visible');
        if (condition) { el.textContent = message; el.classList.add('visible'); valid = false; }
    });

    if (!bl) {
        errorEl.textContent = 'Please enter the Bill of Lading number first.';
        errorEl.classList.add('visible');
        valid = false;
    }

    if (!valid) return;

    btn.textContent = 'Adding...';
    btn.disabled    = true;

    fetch('{{ route("cmdts.containers.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ BL: bl, ContainerNo: containerNo, SealNo: sealNo, Size: size, ItemDetails: itemDetails }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderContainersTable(data.containers);
            // clear fields but keep modal open for next container
            document.getElementById('modal-container-no').value  = '';
            document.getElementById('modal-container-size').value = '';
            document.getElementById('modal-seal-no').value        = '';
            document.getElementById('modal-item-details').value   = '';
            document.getElementById('modal-container-no').focus();

            // Show brief success feedback in modal
            errorEl.style.color     = '#16a34a';
            errorEl.textContent     = '✓ Container added. Add another or close when done.';
            errorEl.classList.add('visible');
            setTimeout(() => {
                errorEl.classList.remove('visible');
                errorEl.style.color = '';
            }, 2500);

        } else {
            errorEl.style.color = '';
            errorEl.textContent = data.message ?? 'Failed to add container.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => {
        errorEl.textContent = 'Something went wrong.';
        errorEl.classList.add('visible');
    })
    .finally(() => {
        btn.textContent = 'Add Container Details';
        btn.disabled    = false;
    });
}

// ── Render containers table ──
function renderContainersTable(containers) {
    const wrapper = document.getElementById('containers-table');
    const countEl = document.getElementById('container-count');

    countEl.textContent = containers.length + ' container(s) added';

    if (!containers.length) {
        wrapper.innerHTML = `<div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">No containers added yet.</div>`;
        return;
    }

    wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Container#</th>
                    <th style="width: 80px;">Size</th>
                    <th style="width: 100px;">Seal No</th>
                    <th>Item Details</th>
                    <th style="width: 60px; text-align: center;">Remove</th>
                </tr>
            </thead>
            <tbody>
                ${containers.map(c => `
                <tr>
                    <td class="td-mono">${c.ContainerNo}</td>
                    <td class="td-muted">${c.Size}ft</td>
                    <td class="td-muted">${c.SealNo || '—'}</td>
                    <td style="font-size: 0.8rem; color: var(--text-primary);">${c.ItemDetails}</td>
                    <td style="text-align: center;">
                        <button onclick="removeContainer('${c.ContainerNo}')" class="btn-icon btn-icon-danger" title="Remove">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>
                </tr>`).join('')}
            </tbody>
        </table>`;
}

// ── Remove container ──
function removeContainer(containerNo) {
    fetch('{{ route("cmdts.containers.remove") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ ContainerNo: containerNo }),
    })
    .then(res => res.json())
    .then(data => { if (data.success) renderContainersTable(data.containers); });
}

// ── Clear all containers ──
function clearContainers() {
    if (!confirm('Clear all staged containers?')) return;
    fetch('{{ route("cmdts.containers.clear") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderContainersTable([]);
            const banner = document.querySelector('[style*="rgba(234,179,8"]');
            if (banner) banner.remove();
        }
    });
}

// ── Submit consignment ──
function submitConsignment() {
    const btn       = document.getElementById('submit-btn');
    const errorEl   = document.getElementById('submit-error');
    const successEl = document.getElementById('submit-success');

    errorEl.classList.remove('visible');
    successEl.classList.remove('visible');

    const categoryId  = document.getElementById('category-id').value;
    const typeId      = document.getElementById('type-id').value;
    const bl          = document.getElementById('bl').value.trim().toUpperCase();
    const consigneeId = document.getElementById('consignee-id').value;
    const eta         = document.getElementById('eta').value;
    const carrierId   = document.getElementById('carrier-id').value;
    const releaseType = document.getElementById('release-type').value;
    const destination = document.getElementById('destination').value.trim();

    let valid = true;
    const checks = [
        [!categoryId, 'category-error', 'Please select a category.'],
        [!typeId, 'type-error', 'Please select a category type.'],
        [!bl, 'bl-error', 'Bill of Lading is required.'],
        [!consigneeId, 'consignee-error', 'Please select a consignee.'],
        [!eta, 'eta-error', 'ETA is required.'],
        [!carrierId, 'carrier-error', 'Please select a shipping line.'],
        [!releaseType, 'release-error', 'Please select a release type.'],
    ];

    checks.forEach(([condition, errorId, message]) => {
        const el = document.getElementById(errorId);
        if (el) el.classList.remove('visible');
        if (condition) { if (el) { el.textContent = message; el.classList.add('visible'); } valid = false; }
    });

    if (!valid) return;

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("cmdts.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ CmdtCategoryID: categoryId, CmdtTypeID: typeId, BL: bl, ConsigneeID: consigneeId, ETA: eta, CarrierID: carrierId, ReleaseType: releaseType, Destination: destination }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            successEl.textContent = data.message;
            successEl.classList.add('visible');
            setTimeout(() => {
                document.getElementById('category-id').value      = '';
                document.getElementById('type-id').innerHTML      = '<option value="">Select category first...</option>';
                document.getElementById('type-id').disabled       = true;
                document.getElementById('bl').value               = '';
                document.getElementById('consignee-search').value = '';
                document.getElementById('consignee-id').value     = '';
                document.getElementById('eta').value              = '';
                document.getElementById('carrier-id').value       = '';
                document.getElementById('release-type').value     = '';
                document.getElementById('destination').value      = '';
                renderContainersTable([]);
                successEl.classList.remove('visible');

                //re-enable button after rest is complete
                btn.textContent = 'Add New Consignment';
                btn.disabled = false; 
            }, 2500);
        } else {
            errorEl.textContent = data.message ?? 'Failed to save consignment.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => { errorEl.textContent = 'Something went wrong. Please try again.'; errorEl.classList.add('visible'); })
    
}

// ── Commodity Type Quick Add ──
function saveQuickAddType() {
    const name = document.getElementById('qam-type-name').value.trim();
    const btn  = document.getElementById('qam-type-btn');
    const err  = document.getElementById('qam-type-error');
    const categoryId = document.getElementById('category-id').value;

    err.classList.remove('visible');
    if (!name) { err.textContent = 'Type name is required.'; err.classList.add('visible'); return; }
    if (!categoryId) { err.textContent = 'Please select a category first.'; err.classList.add('visible'); return; }

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("master-data.commodities.type.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ CategoryID: categoryId, TypeName: name }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const opt = document.createElement('option');
            opt.value = data.TypeID; opt.textContent = data.TypeName;
            document.getElementById('type-id').appendChild(opt);
            document.getElementById('type-id').value = data.TypeID;
            document.getElementById('type-id').disabled = false;
            closeQuickAddModal('type');
            document.getElementById('qam-type-name').value = '';
        } else {
            err.textContent = data.message ?? 'Failed to save.';
            err.classList.add('visible');
        }
    })
    .catch(() => { err.textContent = 'Something went wrong.'; err.classList.add('visible'); })
    .finally(() => { btn.textContent = 'Save'; btn.disabled = false; });
}

document.addEventListener('DOMContentLoaded', function() {
    @if($pendingContainers->isNotEmpty())
        const pendingContainers = @json($pendingContainers);
        renderContainersTable(pendingContainers);
        document.getElementById('bl').value = '{{ $pendingBOL }}';
    @endif
});

// Close type modal on backdrop click
document.getElementById('modal-quick-type').addEventListener('click', function(e) {
    if (e.target === this) closeQuickAddModal('type');
});

// ── Quick Add callbacks — called by shared partials/quick-add-modals ──
window.onQuickAddConsignee = function(id, name) {
    document.getElementById('consignee-search').value = name;
    document.getElementById('consignee-id').value     = id;
};

window.onQuickAddCarrier = function(id, name) {
    const opt = document.createElement('option');
    opt.value = id; opt.textContent = name;
    document.getElementById('carrier-id').appendChild(opt);
    document.getElementById('carrier-id').value = id;
};

window.onQuickAddCategory = function(id, name) {
    const opt = document.createElement('option');
    opt.value = id; opt.textContent = name;
    document.getElementById('category-id').appendChild(opt);
    document.getElementById('category-id').value = id;
    loadTypes();
};

window.onQuickAddRelease = function(id, name) {
    const opt = document.createElement('option');
    opt.value = id; opt.textContent = name;
    document.getElementById('release-type').appendChild(opt);
    document.getElementById('release-type').value = id;
};


</script>
@endpush