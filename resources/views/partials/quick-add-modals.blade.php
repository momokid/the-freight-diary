{{--
    Shared Quick-Add Modals
    Used in: consignments/create.blade.php, consignments/manifest.blade.php, consignments/cmdts.blade.php
    Includes: Consignee, Carrier, Category, Release Type modals
    JS functions: openQuickAddModal(), closeQuickAddModal(), saveQuickAddConsignee(), saveQuickAddCarrier(), saveQuickAddCategory(), saveQuickAddRelease()
--}}

{{-- ── Consignee Modal ── --}}
<div id="modal-quick-consignee" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 440px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">New Consignee</p>
            <button onclick="closeQuickAddModal('consignee')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-consignee-name" class="form-input">
            <p id="qam-consignee-name-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Phone <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-consignee-phone" class="form-input">
            <p id="qam-consignee-phone-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Address <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-consignee-address" class="form-input">
            <p id="qam-consignee-address-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closeQuickAddModal('consignee')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAddConsignee()" id="qam-consignee-btn" class="btn-primary" style="flex: 1;">Add Consignee</button>
        </div>
    </div>
</div>

{{-- ── Carrier Modal ── --}}
<div id="modal-quick-carrier" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">New Shipping Line</p>
            <button onclick="closeQuickAddModal('carrier')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Carrier Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-carrier-name" class="form-input">
            <p id="qam-carrier-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closeQuickAddModal('carrier')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAddCarrier()" id="qam-carrier-btn" class="btn-primary" style="flex: 1;">Save</button>
        </div>
    </div>
</div>

{{-- ── Commodity Category Modal ── --}}
<div id="modal-quick-category" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">New Category</p>
            <button onclick="closeQuickAddModal('category')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Category Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-category-name" class="form-input">
            <p id="qam-category-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closeQuickAddModal('category')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAddCategory()" id="qam-category-btn" class="btn-primary" style="flex: 1;">Save</button>
        </div>
    </div>
</div>

{{-- ── Release Type Modal ── --}}
<div id="modal-quick-release" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <p class="form-title">New Release Type</p>
            <button onclick="closeQuickAddModal('release')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Release Type <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qam-release-name" class="form-input">
            <p id="qam-release-error" class="form-error"></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closeQuickAddModal('release')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAddRelease()" id="qam-release-btn" class="btn-primary" style="flex: 1;">Save</button>
        </div>
    </div>
</div>

{{-- ── Shared JS ── --}}
<script>
// Each form that includes this partial must define these callbacks:
// window.onQuickAddConsignee(id, name) — called after consignee saved
// window.onQuickAddCarrier(id, name)   — called after carrier saved
// window.onQuickAddCategory(id, name)  — called after category saved
// window.onQuickAddRelease(id, name)   — called after release type saved

const QAM_CSRF = '{{ csrf_token() }}';

function openQuickAddModal(type) {
    document.getElementById(`modal-quick-${type}`).style.display = 'flex';
}

function closeQuickAddModal(type) {
    document.getElementById(`modal-quick-${type}`).style.display = 'none';
    // Clear fields
    const fields = {
        consignee: ['qam-consignee-name', 'qam-consignee-phone', 'qam-consignee-address'],
        carrier:   ['qam-carrier-name'],
        category:  ['qam-category-name'],
        release:   ['qam-release-name'],
    };
    const errors = {
        consignee: ['qam-consignee-name-error', 'qam-consignee-phone-error', 'qam-consignee-address-error'],
        carrier:   ['qam-carrier-error'],
        category:  ['qam-category-error'],
        release:   ['qam-release-error'],
    };
    (fields[type] || []).forEach(id => { document.getElementById(id).value = ''; });
    (errors[type] || []).forEach(id => { document.getElementById(id).classList.remove('visible'); });
}

// Close on backdrop click
['consignee', 'carrier', 'category', 'release'].forEach(type => {
    const el = document.getElementById(`modal-quick-${type}`);
    if (el) el.addEventListener('click', function(e) {
        if (e.target === this) closeQuickAddModal(type);
    });
});

function saveQuickAddConsignee() {
    const name    = document.getElementById('qam-consignee-name').value.trim();
    const phone   = document.getElementById('qam-consignee-phone').value.trim();
    const address = document.getElementById('qam-consignee-address').value.trim();
    const btn     = document.getElementById('qam-consignee-btn');

    let valid = true;
    [['qam-consignee-name-error', name, 'Name is required.'],
     ['qam-consignee-phone-error', phone, 'Phone is required.'],
     ['qam-consignee-address-error', address, 'Address is required.']
    ].forEach(([errId, val, msg]) => {
        const el = document.getElementById(errId);
        el.classList.remove('visible');
        if (!val) { el.textContent = msg; el.classList.add('visible'); valid = false; }
    });
    if (!valid) return;

    btn.textContent = 'Adding...';
    btn.disabled    = true;

    fetch('{{ route("master-data.consignees.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QAM_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ FullName: name, TelNo: phone, Address1: address, Address2: '', Address3: '' }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeQuickAddModal('consignee');
            if (typeof window.onQuickAddConsignee === 'function') {
                window.onQuickAddConsignee(data.ConsigneeID, data.FullName);
            }
        } else {
            document.getElementById('qam-consignee-name-error').textContent = data.message ?? 'Failed to add.';
            document.getElementById('qam-consignee-name-error').classList.add('visible');
        }
    })
    .catch(() => {
        document.getElementById('qam-consignee-name-error').textContent = 'Something went wrong.';
        document.getElementById('qam-consignee-name-error').classList.add('visible');
    })
    .finally(() => { btn.textContent = 'Add Consignee'; btn.disabled = false; });
}

function saveQuickAddCarrier() {
    const name = document.getElementById('qam-carrier-name').value.trim();
    const btn  = document.getElementById('qam-carrier-btn');
    const err  = document.getElementById('qam-carrier-error');

    err.classList.remove('visible');
    if (!name) { err.textContent = 'Carrier name is required.'; err.classList.add('visible'); return; }

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("master-data.carriers.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QAM_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ CarrierName: name }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeQuickAddModal('carrier');
            if (typeof window.onQuickAddCarrier === 'function') {
                window.onQuickAddCarrier(data.CarrierID, data.CarrierName);
            }
        } else {
            err.textContent = data.message ?? 'Failed to save.';
            err.classList.add('visible');
        }
    })
    .catch(() => { err.textContent = 'Something went wrong.'; err.classList.add('visible'); })
    .finally(() => { btn.textContent = 'Save'; btn.disabled = false; });
}

function saveQuickAddCategory() {
    const name = document.getElementById('qam-category-name').value.trim();
    const btn  = document.getElementById('qam-category-btn');
    const err  = document.getElementById('qam-category-error');

    err.classList.remove('visible');
    if (!name) { err.textContent = 'Category name is required.'; err.classList.add('visible'); return; }

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("master-data.commodities.category.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QAM_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ CategoryName: name }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeQuickAddModal('category');
            if (typeof window.onQuickAddCategory === 'function') {
                window.onQuickAddCategory(data.ID, data.CategoryName);
            }
        } else {
            err.textContent = data.message ?? 'Failed to save.';
            err.classList.add('visible');
        }
    })
    .catch(() => { err.textContent = 'Something went wrong.'; err.classList.add('visible'); })
    .finally(() => { btn.textContent = 'Save'; btn.disabled = false; });
}

function saveQuickAddRelease() {
    const name = document.getElementById('qam-release-name').value.trim();
    const btn  = document.getElementById('qam-release-btn');
    const err  = document.getElementById('qam-release-error');

    err.classList.remove('visible');
    if (!name) { err.textContent = 'Release type is required.'; err.classList.add('visible'); return; }

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("cmdts.release.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QAM_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ name }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeQuickAddModal('release');
            if (typeof window.onQuickAddRelease === 'function') {
                window.onQuickAddRelease(data.id, data.name);
            }
        } else {
            err.textContent = data.message ?? 'Failed to save.';
            err.classList.add('visible');
        }
    })
    .catch(() => { err.textContent = 'Something went wrong.'; err.classList.add('visible'); })
    .finally(() => { btn.textContent = 'Save'; btn.disabled = false; });
}
</script>