@extends('layouts.app')

@section('title', 'Edit Consignment Weight')
@section('page-title', 'Edit Consignment Weight')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ── Search Panel ── --}}
        <div class="card">
            <p class="form-title" style="color: #16a34a;">Consignment Search</p>
            <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                <label class="form-label">Bill of Lading #</label>
                <input type="text" id="bl-input" class="form-input" placeholder="Enter Main BL..."
                    style="text-transform: uppercase;" autocomplete="off">
                <div id="bl-dropdown"
                    style="display: none; position: absolute; z-index: 100;
                       background: var(--card-bg); border: 1px solid var(--border-color);
                       border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                       width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                </div>
                <input type="hidden" id="bl-value">
                <p id="bl-error" class="form-error"></p>
            </div>
        </div>

        {{-- ── Results Panel ── --}}
        <div id="results-section" style="display: none;">
            <div class="card">

                {{-- Consignment Header --}}
                <p class="form-title" style="text-align: center; margin-bottom: 1rem;">
                    CONSIGNMENT DETAILS
                </p>

                <div
                    style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;
                padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">

                    <div>
                        <p class="form-label">D.O.T.</p>
                        <p id="meta-dot"
                            style="font-weight: 700; font-size: 0.95rem;
                        color: var(--text-primary);">
                            —</p>
                    </div>

                    <div>
                        <p class="form-label">Bill of Lading</p>
                        <p id="meta-bl"
                            style="font-weight: 700; font-size: 0.95rem;
                        color: var(--text-primary);">
                            —</p>
                    </div>

                    <div>
                        <p class="form-label">Container Size</p>
                        <p id="meta-size"
                            style="font-weight: 700; font-size: 0.95rem;
                        color: var(--text-primary);">
                            —</p>
                    </div>

                    <div>
                        <p class="form-label">Gross Weight (KG)</p>
                        <p id="meta-gross-weight"
                            style="font-weight: 700; font-size: 1rem;
                        color: #16a34a;">—</p>
                    </div>

                </div>

                {{-- Breakdown Header --}}
                <p class="form-title" style="text-align: center; margin-top: 1rem; margin-bottom: 1rem;">
                    MANIFEST BREAKDOWN DETAILS
                </p>

                {{-- Table Header --}}
                <div
                    style="display: grid;
                grid-template-columns: 2fr 1fr 2fr 1fr 1fr;
                gap: 0.5rem; padding: 0.5rem 0.75rem;
                background: var(--table-header-bg);
                border-radius: 6px; margin-bottom: 0.5rem;">
                    <p
                        style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: 0.05em; color: var(--text-muted); margin: 0;">
                        Consignee</p>
                    <p
                        style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: 0.05em; color: var(--text-muted); margin: 0;">
                        House BL#</p>
                    <p
                        style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: 0.05em; color: var(--text-muted); margin: 0;">
                        Description</p>
                    <p
                        style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: 0.05em; color: var(--text-muted); margin: 0;">
                        Package</p>
                    <p
                        style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: 0.05em; color: var(--text-muted); margin: 0;">
                        Weight (KG)</p>
                </div>

                {{-- Table Rows injected by JS --}}
                <div id="breakdown-rows"></div>

                {{-- Submit --}}
                <div style="margin-top: 1.5rem;">
                    <p id="submit-error" class="form-error" style="text-align: center; margin-bottom: 8px;"></p>
                    <p id="submit-success"
                        style="text-align: center; margin-bottom: 8px;
                    font-size: 0.82rem; color: #16a34a; display: none;">
                    </p>
                    <button onclick="updateWeights()"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.9rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                        Update Consignment Weight
                    </button>
                </div>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        'use strict';

        const CSRF = '{{ csrf_token() }}';

        let currentConsignmentId = null;
        let breakdownRows = []; // { HouseBL, Weight, ... }

        // ── Init Search ──
        function initSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: '{{ route('edit-data.weight.search-bl') }}',
                labelKey: 'label',
                subKey: null,
                valueKey: 'BL',
                minLength: 2,
                onSelect: (bl) => loadBL(bl),
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initSearch, 0);
        });

        // ── Load BL ──
        function loadBL(bl) {
            setError('');

            fetch('{{ route('edit-data.weight.load-bl') }}?BL=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log('loadBL response:', data);
                    if (!data.success) {
                        setError(data.message ?? 'Consignment not found.');
                        document.getElementById('results-section').style.display = 'none';
                        return;
                    }

                    currentConsignmentId = data.consignment.ConsignmentID;
                    breakdownRows = data.rows;

                    // CHANGED: pass all three arguments
                    populateHeader(data.consignment, data.containers, data.grossWeight);

                    renderRows(data.rows);

                    document.getElementById('results-section').style.display = 'block';
                })
                .catch((err) => {
                    setError('An error occurred. Please try again.');
                });
        }

        // ── Populate header ──
        function populateHeader(c, containers, grossWeight) {
            document.getElementById('meta-dot').textContent = formatDate(c.Date);
            document.getElementById('meta-bl').textContent = c.BL ?? '—';
            document.getElementById('meta-gross-weight').textContent = formatNumber(grossWeight);

            const sizeEl = document.getElementById('meta-size');
            if (containers && containers.length) {
                sizeEl.innerHTML = containers.map(ct =>
                    `<span style="display:block; font-size:0.85rem; font-weight:600;
                color:var(--text-primary);">${escHtml(ct.ContainerNo ?? '—')}
                <span style="color:var(--text-muted); font-weight:400;">
                    (${escHtml(ct.ContainerSize ?? '—')})</span></span>`
                ).join('');
            } else {
                sizeEl.textContent = '—';
            }
        }

        // ── Render breakdown rows ──
        function renderRows(rows) {
            const container = document.getElementById('breakdown-rows');
            container.innerHTML = '';

            rows.forEach((row, index) => {
                const div = document.createElement('div');
                div.style.cssText =
                    'display: grid; grid-template-columns: 2fr 1fr 2fr 1fr 1fr;' +
                    'gap: 0.5rem; padding: 0.6rem 0.75rem; align-items: center;' +
                    'border-bottom: 1px solid var(--border-color);';

                div.innerHTML = `
            <p style="font-size: 0.85rem; color: var(--text-primary);
                margin: 0; font-weight: 500;">${escHtml(row.ConsigneeName)}</p>
            <p style="font-size: 0.85rem; color: var(--text-primary);
                margin: 0;">${escHtml(row.HouseBL)}</p>
            <p style="font-size: 0.85rem; color: var(--text-muted);
                margin: 0;">${escHtml(row.Description)}</p>
            <p style="font-size: 0.85rem; color: var(--text-muted);
                margin: 0;">${escHtml(row.Package + ' ' + row.Unit)}</p>
            <input type="number"
                id="weight-${index}"
                data-housebl="${escHtml(row.HouseBL)}"
                class="form-input"
                value="${row.Weight}"
                min="0" step="0.001"
                oninput="recalculateGross()"
                style="padding: 6px 10px; font-size: 0.85rem;">
        `;

                container.appendChild(div);
            });
        }

        // ── Recalculate gross weight live ──
        // CHANGED: expose to window so oninput can reach it
        window.recalculateGross = function() {
            let total = 0;
            breakdownRows.forEach((_, index) => {
                const val = parseFloat(document.getElementById('weight-' + index)?.value) || 0;
                total += val;
            });
            document.getElementById('meta-gross-weight').textContent = formatNumber(round3(total));
        };

        // ── Save all weights ──
        window.updateWeights = function() {
            if (!currentConsignmentId) return;

            setError('');

            const weights = [];
            let valid = true;

            breakdownRows.forEach((row, index) => {
                const input = document.getElementById('weight-' + index);
                const val = parseFloat(input?.value);

                if (isNaN(val) || val < 0) {
                    setError('Weight for ' + row.HouseBL + ' is invalid.');
                    valid = false;
                    return;
                }

                weights.push({
                    HouseBL: row.HouseBL,
                    Weight: val
                });
            });

            if (!valid) return;

            if (!confirm('Are you sure you want to update the weights for this consignment?')) return;

            fetch('{{ route('edit-data.weight.update') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        ConsignmentID: currentConsignmentId,
                        weights: weights,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update gross weight display with value from server
                        document.getElementById('meta-gross-weight').textContent =
                            formatNumber(data.ContWeight);

                        showSuccess(data.message);

                        setTimeout(() => {
                            currentConsignmentId = null;
                            breakdownRows = [];
                            document.getElementById('bl-input').value = '';
                            document.getElementById('bl-value').value = '';
                            document.getElementById('results-section').style.display = 'none';
                            document.getElementById('submit-success').style.display = 'none';
                        }, 1500);
                    } else {
                        setError(data.message ?? 'Update failed.');
                    }
                })
                .catch(() => setError('An error occurred. Please try again.'));
        };

        // ── Utilities ──
        function setError(msg) {
            const el = document.getElementById('submit-error');
            el.textContent = msg;
            if (msg) el.classList.add('visible');
            else el.classList.remove('visible');
        }

        function showSuccess(msg) {
            const el = document.getElementById('submit-success');
            el.textContent = msg;
            el.style.display = 'block';
        }

        function formatDate(str) {
            if (!str) return '—';
            const d = new Date(str);
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatNumber(val) {
            if (val === null || val === undefined || val === '') return '—';
            return Number(val).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 3
            });
        }

        function round3(val) {
            return Math.round(val * 1000) / 1000;
        }

        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endpush
