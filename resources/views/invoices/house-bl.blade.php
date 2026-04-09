@extends('layouts.app')

@section('title', 'House BL Invoice')
@section('page-title', 'House BL Invoicing')

@section('content')

{{-- Pending entries warning --}}
@if($pendingEntries->isNotEmpty())
<div style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
    <svg style="width: 18px; height: 18px; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <p style="font-size: 0.8rem; font-weight: 600; color: #92400e;">Pending invoice charges for HBL# {{ $pendingHouseBL }}</p>
        <p style="font-size: 0.75rem; color: #92400e; margin-top: 2px;">You have {{ $pendingEntries->count() }} staged charge(s). Submit or clear to start a new invoice.</p>
    </div>
    <button onclick="clearCharges()" style="margin-left: auto; padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(234,179,8,0.4); background: transparent; color: #92400e; font-size: 0.75rem; cursor: pointer;">
        Clear & Start New
    </button>
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

    {{-- ── Left Column ── --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- Consignee Manifest Search --}}
        <div class="card">
            <p class="form-title">Consignee Manifest Search</p>
            <p class="form-subtitle">Search by consignee name or House BL number</p>
            <div style="display: flex; gap: 8px; margin-top: 0.75rem;">
                <input type="text" id="search-input"
                    placeholder="Search consignee or House BL..."
                    class="form-input"
                    oninput="debounceSearch()"
                    onkeydown="if(event.key==='Enter') doSearch()">
                <button onclick="doSearch()" id="search-btn"
                    style="padding: 10px 16px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.875rem; font-weight: 500; cursor: pointer; white-space: nowrap;">
                    Search
                </button>
            </div>
            <p id="search-error" class="form-error"></p>

            {{-- Search results dropdown --}}
            <div id="search-results" style="display: none; margin-top: 8px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;"></div>
        </div>

        {{-- Additional Charges --}}
        <div class="card" id="charges-panel" style="display: none;">
            <p class="form-title">Additional Charges</p>
            <p class="form-subtitle">Add or update charges for this consignee</p>

            <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Select Account <span style="color: #ef4444;">*</span></label>
                    <select id="charge-account" class="form-input">
                        <option value="">Select income account...</option>
                        @foreach($incomeAccounts as $account)
                            <option value="{{ $account->AccountNo }}">{{ $account->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="charge-account-error" class="form-error"></p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Amount <span style="color: #ef4444;">*</span></label>
                        <input type="number" id="charge-amount" min="0.01" step="0.01" class="form-input" oninput="previewTax()">
                        <p id="charge-amount-error" class="form-error"></p>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Transaction ID</label>
                        <input type="text" id="receipt-no" class="form-input" value="{{ $receipt['receipt_no'] }}" readonly
                            style="background: var(--content-bg); color: var(--text-muted); font-size: 0.8rem;">
                    </div>
                </div>

                {{-- Tax preview --}}
                <div id="tax-preview" style="display: none; background: var(--content-bg); border-radius: 8px; padding: 10px 12px; font-size: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--text-muted);">Base Amount</span>
                        <span id="preview-base" style="font-weight: 500;">0.00</span>
                    </div>
                    <div id="preview-tax-lines"></div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); margin-top: 6px; padding-top: 6px;">
                        <span style="font-weight: 600; color: var(--text-primary);">Total</span>
                        <span id="preview-total" style="font-weight: 700; color: #16a34a;">0.00</span>
                    </div>
                </div>

                {{-- Taxable toggle --}}
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label class="form-label" style="margin: 0;">Taxable</label>
                    <div id="taxable-toggle"
                        onclick="toggleTaxable()"
                        style="width: 44px; height: 24px; border-radius: 9999px; background: #16a34a; cursor: pointer; position: relative; transition: background 0.2s;">
                        <div id="taxable-knob"
                            style="width: 18px; height: 18px; background: white; border-radius: 9999px; position: absolute; top: 3px; right: 3px; transition: right 0.2s;">
                        </div>
                    </div>
                    <span id="taxable-label" style="font-size: 0.8rem; color: var(--text-muted);">Tax will be applied</span>
                </div>

                <button onclick="addCharge()" id="add-charge-btn" class="btn-primary">
                    Add / Update Charge
                </button>
                <p id="add-charge-error" class="form-error" style="text-align: center;"></p>
            </div>
        </div>

    </div>

    {{-- ── Right Column ── --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- Consignee Manifest Details --}}
        <div class="card">
            <p class="form-title">Consignee Manifest Details</p>
            <div id="manifest-details" style="margin-top: 0.75rem;">
                <div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                    Search for a consignee or House BL to view manifest details.
                </div>
            </div>
        </div>

        {{-- Handling Charges --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <p class="form-title" style="margin: 0;">Handling Charges</p>
                <button onclick="saveInvoice()" id="save-invoice-btn"
                    style="display: none; padding: 10px 20px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                    Save Consignee Invoice
                </button>
            </div>

            <p id="save-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
            <p id="save-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>

            <div id="charges-table">
                <div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                    No charges added yet.
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Hidden fields --}}
<input type="hidden" id="selected-consignment-id">
<input type="hidden" id="selected-main-bl">
<input type="hidden" id="selected-house-bl">
<input type="hidden" id="selected-consignee-id">
<input type="hidden" id="receipt-id" value="{{ $receipt['id'] }}">
<input type="hidden" id="taxable-value" value="1">

@endsection

@push('scripts')
<script>
const CSRF           = '{{ csrf_token() }}';
const TAX_COMPONENTS = @json($taxComponents);
let searchTimer      = null;
let isTaxable        = true;

// ── Search ──
function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(doSearch, 500);
}

function doSearch() {
    const q       = document.getElementById('search-input').value.trim();
    const errorEl = document.getElementById('search-error');
    const btn     = document.getElementById('search-btn');

    errorEl.classList.remove('visible');
    if (!q || q.length < 2) return;

    btn.textContent = '...';
    btn.disabled    = true;

    fetch(`{{ route('invoice.hbl.search') }}?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        const resultsEl = document.getElementById('search-results');
        if (!data.length) {
            resultsEl.innerHTML = `<div style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 0.8rem;">No results found.</div>`;
            resultsEl.style.display = 'block';
            return;
        }

        resultsEl.innerHTML = data.map(r => `
            <div onclick="selectManifest(${JSON.stringify(r).replace(/"/g, '&quot;')})"
                style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--border-color); font-size: 0.8rem;"
                onmouseover="this.style.background='var(--content-bg)'"
                onmouseout="this.style.background=''">
                <div style="font-weight: 600; color: var(--text-primary);">${r.FullName}</div>
                <div style="color: var(--text-muted); margin-top: 2px;">HBL: ${r.HouseBL} &nbsp;|&nbsp; ${r.MainBL} &nbsp;|&nbsp; ${r.Weight} KG</div>
            </div>`).join('');
        resultsEl.style.display = 'block';
    })
    .catch(() => {
        errorEl.textContent = 'Search failed. Please try again.';
        errorEl.classList.add('visible');
    })
    .finally(() => { btn.textContent = 'Search'; btn.disabled = false; });
}

// Close search results on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#search-input') && !e.target.closest('#search-results')) {
        document.getElementById('search-results').style.display = 'none';
    }
});

// ── Select manifest entry ──
function selectManifest(r) {
    document.getElementById('selected-consignment-id').value = r.ConsignmentID;
    document.getElementById('selected-main-bl').value        = r.MainBL;
    document.getElementById('selected-house-bl').value       = r.HouseBL;
    document.getElementById('selected-consignee-id').value   = r.ConsigneeID;
    document.getElementById('search-input').value            = r.FullName + ' ' + r.HouseBL;
    document.getElementById('search-results').style.display  = 'none';

    // Show manifest details
    document.getElementById('manifest-details').innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>House BL</th>
                    <th>Description</th>
                    <th style="width: 100px; text-align: right;">Weight (KG)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 500; color: var(--text-primary);">${r.FullName}</td>
                    <td class="td-mono">${r.HouseBL}</td>
                    <td class="td-muted" style="font-size: 0.8rem;">${r.Description}</td>
                    <td style="text-align: right; font-weight: 600;">${parseFloat(r.Weight).toLocaleString()}</td>
                </tr>
            </tbody>
        </table>`;

    // Show charges panel
    document.getElementById('charges-panel').style.display = 'flex';

    // Load handling charges into staging automatically
    loadHandlingCharges(r);
}

// ── Auto-load handling charges ──
function loadHandlingCharges(r) {
    const handlingCharges = @json($handlingCharges);

    handlingCharges.forEach(hc => {
        const tax = calculateTax(parseFloat(hc.Amount), true);
        fetch('{{ route("invoice.hbl.charges.add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                ConsignmentID: r.ConsignmentID,
                MainBL:        r.MainBL,
                HouseBL:       r.HouseBL,
                ConsigneeID:   r.ConsigneeID,
                AccountNo:     hc.AccountNo,
                Amount:        hc.Amount,
                Taxable:       true,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) renderChargesTable(data.entries);
        });
    });
}

// ── Tax calculation (mirrors server-side TaxService) ──
function calculateTax(amount, taxable) {
    if (!taxable) return { base: amount, lines: [], total_tax: 0, total: amount };

    let subtotal = amount;
    const lines  = [];

    TAX_COMPONENTS.forEach(c => {
        const base = c.applies_on === 'subtotal' ? subtotal : amount;
        const tax  = Math.round(base * (c.rate / 100) * 100) / 100;
        lines.push({ name: c.name, label: c.label, rate: c.rate, base, tax });
        subtotal = Math.round((subtotal + tax) * 100) / 100;
    });

    return {
        base:      amount,
        lines,
        total_tax: Math.round((subtotal - amount) * 100) / 100,
        total:     subtotal,
    };
}

// ── Tax preview ──
function previewTax() {
    const amount   = parseFloat(document.getElementById('charge-amount').value) || 0;
    const preview  = document.getElementById('tax-preview');

    if (!amount) { preview.style.display = 'none'; return; }

    const tax = calculateTax(amount, isTaxable);
    preview.style.display = 'block';

    document.getElementById('preview-base').textContent  = formatAmount(tax.base);
    document.getElementById('preview-total').textContent = formatAmount(tax.total);

    document.getElementById('preview-tax-lines').innerHTML = tax.lines.map(l => `
        <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
            <span style="color: var(--text-muted);">${l.name} (${l.rate}%)</span>
            <span>${formatAmount(l.tax)}</span>
        </div>`).join('');
}

// ── Taxable toggle ──
function toggleTaxable() {
    isTaxable = !isTaxable;
    const toggle = document.getElementById('taxable-toggle');
    const knob   = document.getElementById('taxable-knob');
    const label  = document.getElementById('taxable-label');

    toggle.style.background = isTaxable ? '#16a34a' : '#d1d5db';
    knob.style.right        = isTaxable ? '3px' : '23px';
    label.textContent       = isTaxable ? 'Tax will be applied' : 'No tax';
    document.getElementById('taxable-value').value = isTaxable ? '1' : '0';

    previewTax();
}

// ── Add charge ──
function addCharge() {
    const btn       = document.getElementById('add-charge-btn');
    const errorEl   = document.getElementById('add-charge-error');
    const accountNo = document.getElementById('charge-account').value;
    const amount    = document.getElementById('charge-amount').value;
    const hbl       = document.getElementById('selected-house-bl').value;

    errorEl.classList.remove('visible');

    let valid = true;
    if (!accountNo) {
        document.getElementById('charge-account-error').textContent = 'Please select an account.';
        document.getElementById('charge-account-error').classList.add('visible');
        valid = false;
    }
    if (!amount || parseFloat(amount) <= 0) {
        document.getElementById('charge-amount-error').textContent = 'Please enter a valid amount.';
        document.getElementById('charge-amount-error').classList.add('visible');
        valid = false;
    }
    if (!hbl) {
        errorEl.textContent = 'Please search and select a consignee first.';
        errorEl.classList.add('visible');
        valid = false;
    }
    if (!valid) return;

    btn.textContent = 'Adding...';
    btn.disabled    = true;

    fetch('{{ route("invoice.hbl.charges.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            ConsignmentID: document.getElementById('selected-consignment-id').value,
            MainBL:        document.getElementById('selected-main-bl').value,
            HouseBL:       hbl,
            ConsigneeID:   document.getElementById('selected-consignee-id').value,
            AccountNo:     accountNo,
            Amount:        amount,
            Taxable:       isTaxable,
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderChargesTable(data.entries);
            document.getElementById('charge-account').value = '';
            document.getElementById('charge-amount').value  = '';
            document.getElementById('tax-preview').style.display = 'none';
        } else {
            errorEl.textContent = data.message ?? 'Failed to add charge.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
    .finally(() => { btn.textContent = 'Add / Update Charge'; btn.disabled = false; });
}

// ── Render charges table ──
function renderChargesTable(entries) {
    const wrapper = document.getElementById('charges-table');
    const saveBtn = document.getElementById('save-invoice-btn');

    saveBtn.style.display = entries.length > 0 ? 'block' : 'none';

    if (!entries.length) {
        wrapper.innerHTML = `<div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">No charges added yet.</div>`;
        return;
    }

    const totalCharges = entries.reduce((sum, e) => sum + parseFloat(e.SubTotal), 0);

    wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th style="width: 100px; text-align: right;">Charge</th>
                    <th style="width: 100px; text-align: right;">Tax</th>
                    <th style="width: 110px; text-align: right;">Sub Total</th>
                    <th style="width: 60px; text-align: center;">Remove</th>
                </tr>
            </thead>
            <tbody>
                ${entries.map(e => `
                <tr>
                    <td style="font-size: 0.8rem; color: var(--text-primary);">${e.AccountName}</td>
                    <td style="text-align: right; font-weight: 500;">${formatAmount(e.Amount)}</td>
                    <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">${formatAmount(parseFloat(e.GetFundNHIL) + parseFloat(e.Covid) + parseFloat(e.VAT))}</td>
                    <td style="text-align: right; font-weight: 600; color: #16a34a;">${formatAmount(e.SubTotal)}</td>
                    <td style="text-align: center;">
                        <button onclick="removeCharge(${e.AccountNo})" class="btn-icon btn-icon-danger" title="Remove">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>
                </tr>`).join('')}
                <tr style="background: var(--content-bg);">
                    <td colspan="3" style="text-align: right; font-weight: 700; font-size: 0.85rem; padding-right: 12px;">TOTAL CHARGES:</td>
                    <td style="text-align: right; font-weight: 800; color: #16a34a; font-size: 0.9rem;">${formatAmount(totalCharges)}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>`;
}

// ── Remove charge ──
function removeCharge(accountNo) {
    const hbl = document.getElementById('selected-house-bl').value;
    fetch('{{ route("invoice.hbl.charges.remove") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ HouseBL: hbl, AccountNo: accountNo }),
    })
    .then(res => res.json())
    .then(data => { if (data.success) renderChargesTable(data.entries); });
}

// ── Clear all charges ──
function clearCharges() {
    if (!confirm('Clear all staged charges?')) return;
    fetch('{{ route("invoice.hbl.charges.clear") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderChargesTable([]);
            document.getElementById('charges-panel').style.display    = 'none';
            document.getElementById('manifest-details').innerHTML      = `<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">Search for a consignee or House BL to view manifest details.</div>`;
            document.getElementById('search-input').value              = '';
            document.getElementById('selected-house-bl').value         = '';
            const banner = document.querySelector('[style*="rgba(234,179,8"]');
            if (banner) banner.remove();
        }
    });
}

// ── Save invoice ──
function saveInvoice() {
    const btn       = document.getElementById('save-invoice-btn');
    const errorEl   = document.getElementById('save-error');
    const successEl = document.getElementById('save-success');
    const hbl       = document.getElementById('selected-house-bl').value;

    errorEl.classList.remove('visible');
    successEl.classList.remove('visible');

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route("invoice.hbl.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            ReceiptID: document.getElementById('receipt-id').value,
            ReceiptNo: document.getElementById('receipt-no').value,
            HouseBL:   hbl,
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            successEl.textContent = data.message;
            successEl.classList.add('visible');
            setTimeout(() => {
                window.open(`{{ url('invoice/house-bl/report') }}/${encodeURIComponent(hbl)}`, '_blank');
                // Reset form
                document.getElementById('charges-panel').style.display   = 'none';
                document.getElementById('manifest-details').innerHTML     = `<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">Search for a consignee or House BL to view manifest details.</div>`;
                document.getElementById('search-input').value             = '';
                document.getElementById('selected-house-bl').value        = '';
                renderChargesTable([]);
                successEl.classList.remove('visible');
                btn.style.display = 'none';
            }, 1500);
        } else {
            errorEl.textContent = data.message ?? 'Failed to save invoice.';
            errorEl.classList.add('visible');
            btn.textContent = 'Save Consignee Invoice';
            btn.disabled    = false;
        }
    })
    .catch(() => {
        errorEl.textContent = 'Something went wrong. Please try again.';
        errorEl.classList.add('visible');
        btn.textContent = 'Save Consignee Invoice';
        btn.disabled    = false;
    });
}

// ── Format amount ──
function formatAmount(amount) {
    return parseFloat(amount).toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Load pending entries on page load
@if($pendingEntries->isNotEmpty())
document.addEventListener('DOMContentLoaded', function() {
    const entries = @json($pendingEntries);
    renderChargesTable(entries);
    document.getElementById('selected-house-bl').value       = '{{ $pendingHouseBL }}';
    document.getElementById('charges-panel').style.display   = 'flex';
    document.getElementById('save-invoice-btn').style.display = 'block';
});
@endif
</script>
@endpush