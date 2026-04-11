@extends('layouts.app')

@section('title', 'Cargo Manifest')
@section('page-title', 'Cargo Manifest')

@section('content')

    {{-- Pending manifest warning --}}
    @if ($pendingItems->isNotEmpty())
        <div
            style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
            <svg style="width: 18px; height: 18px; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p style="font-size: 0.8rem; font-weight: 600; color: #92400e;">Pending manifest entries for BL#
                    {{ $pendingBOL }}</p>
                <p style="font-size: 0.75rem; color: #92400e; margin-top: 2px;">You have {{ $pendingItems->count() }} staged
                    entry(s). Complete or clear to start a new manifest.</p>
            </div>
            <button onclick="clearEntries()"
                style="margin-left: auto; padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(234,179,8,0.4); background: transparent; color: #92400e; font-size: 0.75rem; cursor: pointer;">
                Clear & Start New
            </button>
        </div>
    @endif

    <div class="flex gap-6" style="height: calc(100vh - 90px);">

        {{-- ── Left Panel ── --}}
        <div class="shrink-0" style="width: 360px;">
            <div class="card flex flex-col gap-4">

                {{-- Search Main BL --}}
                <div style="position: relative;">
                    <input type="text" id="search-bl" placeholder="Enter Main BL..." class="form-input"
                        style="text-transform: uppercase;">
                    <div id="search-bl-dropdown"
                        style="display: none; position: absolute; z-index: 100; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; top: 100%; max-height: 200px; overflow-y: auto;">
                    </div>
                </div>
                <p id="search-error" class="form-error"></p>

                {{-- Consignment Info (shown after search) --}}
                <div id="consignment-info" style="display: none;">
                    <div
                        style="background: var(--content-bg); border-radius: 8px; padding: 12px; border: 1px solid var(--border-color);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.8rem;">
                            <div>
                                <p
                                    style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Shipper</p>
                                <p id="info-shipper" style="font-weight: 500; color: var(--text-primary); margin-top: 2px;">
                                </p>
                            </div>
                            <div>
                                <p
                                    style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Vessel</p>
                                <p id="info-vessel" style="font-weight: 500; color: var(--text-primary); margin-top: 2px;">
                                </p>
                            </div>
                            <div>
                                <p
                                    style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Main BL</p>
                                <p id="info-bl" style="font-weight: 500; color: var(--text-primary); margin-top: 2px;">
                                </p>
                            </div>
                            <div>
                                <p
                                    style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Total Weight</p>
                                <p id="info-weight" style="font-weight: 600; color: #16a34a; margin-top: 2px;"></p>
                            </div>
                        </div>

                        {{-- Weight progress --}}
                        <div style="margin-top: 12px;">
                            <div
                                style="display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--text-muted); margin-bottom: 4px;">
                                <span>Staged: <span id="staged-weight"
                                        style="font-weight: 600; color: var(--text-primary);">0</span> KG</span>
                                <span>Remaining: <span id="remaining-weight"
                                        style="font-weight: 600; color: #ef4444;">0</span> KG</span>
                            </div>
                            <div
                                style="height: 6px; background: var(--border-color); border-radius: 9999px; overflow: hidden;">
                                <div id="weight-progress"
                                    style="height: 100%; background: #16a34a; border-radius: 9999px; width: 0%; transition: width 0.3s;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Manifest Breakdown Form --}}
                <div id="manifest-form" style="display: none;">
                    <p class="form-title"
                        style="margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                        Manifest Breakdown
                    </p>

                    {{-- House BL --}}
                    <div class="form-group">
                        <label class="form-label">House BL# <span style="color: #ef4444;">*</span></label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" id="house-bl" class="form-input" style="text-transform: uppercase;"
                                readonly>
                            <button type="button" onclick="refreshHouseBL()"
                                style="padding: 10px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted); cursor: pointer;"
                                title="Regenerate">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Container No (if multiple containers) --}}
                    <div class="form-group" id="container-select-group" style="display: none;">
                        <label class="form-label">Container <span style="color: #ef4444;">*</span></label>
                        <select id="container-no" class="form-input">
                            <option value="">Select container...</option>
                        </select>
                        <p id="container-error" class="form-error"></p>
                    </div>

                    {{-- Consignee --}}
                    <div class="form-group">
                        <label class="form-label">Consignee <span style="color: #ef4444;">*</span>
                            <button type="button" onclick="openQuickAddConsignee('consignee')"
                                style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+
                                New</button>
                        </label>
                        <input type="text" id="consignee-search" placeholder="Search consignee..." class="form-input">
                        <div id="consignee-dropdown"
                            style="display: none; position: absolute; z-index: 100; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; width: 320px;">
                        </div>
                        <input type="hidden" id="consignee-id">
                        <p id="consignee-error" class="form-error"></p>
                    </div>

                    {{-- Notify Party --}}
                    <div class="form-group">
                        <label class="form-label">Notify Party <span style="color: #ef4444;">*</span>
                            <button type="button" onclick="openQuickAddConsignee('notify')"
                                style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+
                                New</button>
                        </label>
                        <input type="text" id="notify-search" placeholder="Search notify party..."
                            class="form-input">
                        <div id="notify-dropdown"
                            style="display: none; position: absolute; z-index: 100; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; width: 320px;">
                        </div>
                        <input type="hidden" id="notify-id">
                        <p id="notify-error" class="form-error"></p>
                    </div>

                    {{-- Weight, Package, Unit --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Weight (KG) <span style="color: #ef4444;">*</span></label>
                            <input type="number" id="weight" min="0.001" step="0.001" class="form-input">
                            <p id="weight-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Package <span style="color: #ef4444;">*</span></label>
                            <input type="number" id="package" min="1" step="1" class="form-input">
                            <p id="package-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Unit <span style="color: #ef4444;">*</span></label>
                            <select id="unit" class="form-input">
                                <option value="">Select...</option>
                                <option value="LOT">LOT</option>
                                <option value="PLT">PLT</option>
                                <option value="PKG">PKG</option>
                                <option value="UNIT">UNIT</option>
                            </select>
                            <p id="unit-error" class="form-error"></p>
                        </div>
                    </div>

                    {{-- Item Type --}}
                    <div class="form-group" style="margin-top: 0.75rem;">
                        <label class="form-label">Item Type <span style="color: #ef4444;">*</span></label>
                        <select id="item-type" class="form-input" onchange="onItemTypeChange()">
                            <option value="">Select type...</option>
                            <option value="GOODS">GOODS</option>
                            <option value="VEHICLE">VEHICLE</option>
                            <option value="MOTORBIKE">MOTORBIKE</option>
                        </select>
                        <p id="item-type-error" class="form-error"></p>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                        <textarea id="description" rows="2" class="form-input" style="resize: none;"></textarea>
                        <p id="description-error" class="form-error"></p>
                    </div>

                    {{-- VIN (vehicles only) --}}
                    <div class="form-group" id="vin-group" style="display: none;">
                        <label class="form-label">VIN <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="vin" class="form-input"
                            placeholder="Vehicle Identification Number">
                        <p id="vin-error" class="form-error"></p>
                    </div>

                    {{-- Other Info --}}
                    <div class="form-group">
                        <label class="form-label">Other Info <span style="color: var(--text-muted);"
                                id="other-info-label">optional</span></label>
                        <input type="text" id="other-info" class="form-input">
                    </div>

                    <button onclick="addEntry()" id="add-entry-btn" class="btn-primary">
                        Add to Staging
                    </button>
                    <p id="add-error" class="form-error" style="margin-top: 8px; text-align: center;"></p>
                </div>

            </div>
        </div>

        {{-- ── Right Panel ── --}}
        <div class="flex-1 min-w-0" style="min-height: 0;">
            <div class="card h-full flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <div>
                        <p class="form-title">Staged Manifest Entries</p>
                        <p id="staged-count" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">No
                            entries staged</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="saveManifest()" id="save-manifest-btn"
                            style="display: none; padding: 10px 20px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                            Save Manifest
                        </button>
                    </div>
                </div>

                {{-- Save error/success --}}
                <p id="save-error" class="form-error" style="margin-bottom: 8px; text-align: center;"></p>
                <p id="save-success" class="form-success" style="margin-bottom: 8px; text-align: center;"></p>

                {{-- Table --}}
                <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                    <div id="staged-table">
                        <div
                            style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                            Search for a consignment by Main BL to start adding manifest entries.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Quick Add Consignee Modal --}}
    <div id="modal-quick-consignee"
        style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
        <div class="card" style="width: 100%; max-width: 440px; margin: 1rem;">
            <div class="flex items-center justify-between mb-4">
                <p class="form-title" id="quick-consignee-title">New Consignee</p>
                <button onclick="closeQuickAddConsignee()"
                    style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
            </div>
            <div class="form-group">
                <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="qc-name" class="form-input">
                <p id="qc-name-error" class="form-error"></p>
            </div>
            <div class="form-group">
                <label class="form-label">Phone <span style="color: #ef4444;">*</span></label>
                <input type="text" id="qc-phone" class="form-input">
                <p id="qc-phone-error" class="form-error"></p>
            </div>
            <div class="form-group">
                <label class="form-label">Address <span style="color: #ef4444;">*</span></label>
                <input type="text" id="qc-address" class="form-input">
                <p id="qc-address-error" class="form-error"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeQuickAddConsignee()" class="btn-secondary" style="flex: 1;">Cancel</button>
                <button onclick="saveQuickConsignee()" id="qc-save-btn" class="btn-primary" style="flex: 1;">Add
                    Consignee</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let currentConsignment = null;
        let currentContainers = [];
        let totalWeight = 0;
        let quickAddTarget = 'consignee'; // 'consignee' or 'notify'

        // ── Generate House BL ──
        function generateHouseBL() {
            if (!currentConsignment) return;

            fetch(`{{ route('manifest.generate-hbl') }}?MainBL=${encodeURIComponent(currentConsignment.MainBL)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) document.getElementById('house-bl').value = data.HouseBL;
                });
        }


        // ── Update weight display ──
        function updateWeightDisplay(staged, remaining, total) {
            document.getElementById('staged-weight').textContent = parseFloat(staged).toFixed(3);
            document.getElementById('remaining-weight').textContent = parseFloat(remaining).toFixed(3);
            const pct = total > 0 ? (staged / total * 100) : 0;
            document.getElementById('weight-progress').style.width = Math.min(pct, 100) + '%';
        }

        // ── Item type change ──
        function onItemTypeChange() {
            const type = document.getElementById('item-type').value;
            const vinGroup = document.getElementById('vin-group');
            const otherLabel = document.getElementById('other-info-label');

            vinGroup.style.display = (type === 'VEHICLE') ? 'block' : 'none';
            otherLabel.textContent = (type === 'MOTORBIKE') ? 'optional' : 'required if package > 1';
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.blSearch = new SearchDropdown({
                inputId: 'search-bl',
                dropdownId: 'search-bl-dropdown',
                url: '{{ route('manifest.search-bl') }}',
                labelKey: 'MainBL',
                subKey: 'ShipperName',
                valueKey: 'MainBL',
                minLength: 3,
                onSelect: (bl) => loadConsignment(bl),
            });

            window.consigneeSearch = new SearchDropdown({
                inputId: 'consignee-search',
                dropdownId: 'consignee-dropdown',
                hiddenId: 'consignee-id',
                url: '{{ route('manifest.consignee-search') }}',
                labelKey: 'FullName',
                subKey: 'TelNo',
                valueKey: 'ConsigneeID',
            });

            window.notifySearch = new SearchDropdown({
                inputId: 'notify-search',
                dropdownId: 'notify-dropdown',
                hiddenId: 'notify-id',
                url: '{{ route('manifest.consignee-search') }}',
                labelKey: 'FullName',
                subKey: 'TelNo',
                valueKey: 'ConsigneeID',
            });
        })

        function loadConsignment(bl) {
            const errorEl = document.getElementById('search-error');
            errorEl.classList.remove('visible');

            fetch(`{{ route('manifest.search') }}?BL=${encodeURIComponent(bl)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        errorEl.textContent = data.message ?? 'Consignment not found.';
                        errorEl.classList.add('visible');
                        return;
                    }

                    currentConsignment = data.consignment;
                    currentContainers = data.containers;
                    totalWeight = data.totalWeight;

                    document.getElementById('info-shipper').textContent = data.consignment.ShipperName;
                    document.getElementById('info-vessel').textContent = data.consignment.VesselName;
                    document.getElementById('info-bl').textContent = data.consignment.MainBL;
                    document.getElementById('info-weight').textContent = data.totalWeight + ' KG';

                    updateWeightDisplay(data.stagedWeight, data.remaining, data.totalWeight);

                    document.getElementById('consignment-info').style.display = 'block';
                    document.getElementById('manifest-form').style.display = 'block';

                    if (data.containers.length > 1) {
                        const select = document.getElementById('container-no');
                        select.innerHTML = '<option value="">Select container...</option>';
                        data.containers.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.ContainerNo;
                            opt.textContent =
                                `${c.ContainerNo} (${c.ContainerSize}) — ${parseFloat(c.Weight).toFixed(3)} KG`;
                            select.appendChild(opt);
                        });
                        document.getElementById('container-select-group').style.display = 'block';
                    } else if (data.containers.length === 1) {
                        document.getElementById('container-no').value = data.containers[0].ContainerNo;
                        document.getElementById('container-select-group').style.display = 'none';
                    }

                    generateHouseBL();
                    renderStagedTable(data.items, data.stagedWeight, data.totalWeight);
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong. Please try again.';
                    errorEl.classList.add('visible');
                });
        }


        // ── Add entry to staging ──
        function addEntry() {
            const btn = document.getElementById('add-entry-btn');
            const errorEl = document.getElementById('add-error');

            errorEl.classList.remove('visible');

            const houseBL = document.getElementById('house-bl').value.trim();
            const containerNo = document.getElementById('container-no').value.trim() ||
                (currentContainers.length === 1 ? currentContainers[0].ContainerNo : '');
            const consigneeId = document.getElementById('consignee-id').value;
            const notifyId = document.getElementById('notify-id').value;
            const weight = document.getElementById('weight').value;
            const pkg = document.getElementById('package').value;
            const unit = document.getElementById('unit').value;
            const itemType = document.getElementById('item-type').value;
            const description = document.getElementById('description').value.trim();
            const vin = document.getElementById('vin').value.trim();
            const otherInfo = document.getElementById('other-info').value.trim();

            // Validate
            let valid = true;
            const checks = [
                [!consigneeId, 'consignee-error', 'Please select a consignee.'],
                [!notifyId, 'notify-error', 'Please select a notify party.'],
                [!weight || parseFloat(weight) <= 0, 'weight-error', 'Weight is required.'],
                [!pkg || parseInt(pkg) < 1, 'package-error', 'Package is required.'],
                [!unit, 'unit-error', 'Unit is required.'],
                [!itemType, 'item-type-error', 'Item type is required.'],
                [!description, 'description-error', 'Description is required.'],
            ];

            checks.forEach(([condition, errorId, message]) => {
                const el = document.getElementById(errorId);
                if (el) el.classList.remove('visible');
                if (condition) {
                    if (el) {
                        el.textContent = message;
                        el.classList.add('visible');
                    }
                    valid = false;
                }
            });

            if (!containerNo) {
                errorEl.textContent = 'Please select a container.';
                errorEl.classList.add('visible');
                valid = false;
            }

            if (!valid) return;

            btn.textContent = 'Adding...';
            btn.disabled = true;

            fetch('{{ route('manifest.entries.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ConsignmentID: currentConsignment.ConsignmentID,
                        MainBL: currentConsignment.MainBL,
                        ContainerNo: containerNo,
                        HouseBL: houseBL,
                        CosigneeID: consigneeId,
                        Cosignee2_ID: notifyId,
                        Description: description,
                        ItemType: itemType,
                        VIN: vin,
                        OtherInfo: otherInfo,
                        Weight: weight,
                        Package: pkg,
                        Unit: unit,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Clear form fields
                        window.consigneeSearch?.clear();
                        window.notifySearch?.clear();
                        document.getElementById('weight').value = '';
                        document.getElementById('package').value = '';
                        document.getElementById('unit').value = '';
                        document.getElementById('item-type').value = '';
                        document.getElementById('description').value = '';
                        document.getElementById('vin').value = '';
                        document.getElementById('other-info').value = '';
                        document.getElementById('vin-group').style.display = 'none';

                        updateWeightDisplay(data.staged, data.remaining, data.total);
                        renderStagedTable(data.items, data.staged, data.total);

                        // Generate next House BL
                        generateHouseBL();
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to add entry.';
                        errorEl.classList.add('visible');
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong.';
                    errorEl.classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Add to Staging';
                    btn.disabled = false;
                });
        }

        // ── Render staged table ──
        function renderStagedTable(items, staged, total) {
            const wrapper = document.getElementById('staged-table');
            const countEl = document.getElementById('staged-count');
            const saveBtn = document.getElementById('save-manifest-btn');

            countEl.textContent = items.length + ' entry(s) staged — ' + parseFloat(staged).toFixed(3) + ' KG of ' +
                parseFloat(total).toFixed(3) + ' KG';
            saveBtn.style.display = items.length > 0 ? 'block' : 'none';

            if (!items.length) {
                wrapper.innerHTML =
                    `<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">No entries staged yet.</div>`;
                return;
            }

            wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>House BL</th>
                    <th>Consignee</th>
                    <th>Notify Party</th>
                    <th style="width: 80px;">Type</th>
                    <th style="width: 80px;">Weight</th>
                    <th style="width: 60px;">Pkg</th>
                    <th style="width: 60px;">Unit</th>
                    <th style="width: 60px; text-align: center;">Remove</th>
                </tr>
            </thead>
            <tbody>
                ${items.map(item => `
                                                            <tr>
                                                                <td class="td-mono">${item.HouseBL}</td>
                                                                <td style="font-weight: 500; color: var(--text-primary); font-size: 0.8rem;">${item.consignee?.FullName ?? '—'}</td>
                                                                <td class="td-muted" style="font-size: 0.8rem;">${item.notify_party?.FullName ?? '—'}</td>
                                                                <td class="td-muted">${item.ItemType}</td>
                                                                <td class="td-mono">${parseFloat(item.Weight).toFixed(3)}</td>
                                                                <td class="td-muted">${item.Package}</td>
                                                                <td class="td-muted">${item.Unit}</td>
                                                                <td style="text-align: center;">
                                                                    <button onclick="removeEntry('${item.HouseBL}')" class="btn-icon btn-icon-danger" title="Remove">
                                                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                        </svg>
                                                                    </button>
                                                                </td>
                                                            </tr>`).join('')}
            </tbody>
        </table>`;
        }

        // ── Remove entry ──
        function removeEntry(houseBL) {
            fetch('{{ route('manifest.entries.remove') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        HouseBL: houseBL
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        updateWeightDisplay(data.staged, data.remaining, data.total);
                        renderStagedTable(data.items, data.staged, data.total);
                        generateHouseBL();
                    }
                })
                .catch(() => alert('Something went wrong.'));
        }

        // ── Clear all entries ──
        function clearEntries() {
            if (!confirm('Clear all staged manifest entries?')) return;
            fetch('{{ route('manifest.entries.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentConsignment = null;
                        document.getElementById('consignment-info').style.display = 'none';
                        document.getElementById('manifest-form').style.display = 'none';
                        document.getElementById('staged-table').innerHTML =
                            `<div style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">Search for a consignment by Main BL to start adding manifest entries.</div>`;
                        document.getElementById('staged-count').textContent = 'No entries staged';
                        document.getElementById('save-manifest-btn').style.display = 'none';
                        const banner = document.querySelector('[style*="rgba(234,179,8"]');
                        if (banner) banner.remove();
                    }
                });
        }

        // ── Save manifest ──
        function saveManifest() {
            if (!currentConsignment) return;

            const btn = document.getElementById('save-manifest-btn');
            const errorEl = document.getElementById('save-error');
            const successEl = document.getElementById('save-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch('{{ route('manifest.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        MainBL: currentConsignment.MainBL
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        successEl.textContent = data.message;
                        successEl.classList.add('visible');

                        // CHANGED: open report in new tab then reset form
                        const reportBL = currentConsignment.MainBL;
                        setTimeout(() => {
                            // Open report in new tab
                            window.open(
                                `{{ url('manifest/manifest-breakdown') }}/${encodeURIComponent(reportBL)}`,
                                '_blank');

                            // Reset form
                            currentConsignment = null;
                            document.getElementById('consignment-info').style.display = 'none';
                            document.getElementById('manifest-form').style.display = 'none';
                            document.getElementById('search-bl').value = '';
                            document.getElementById('staged-table').innerHTML =
                                `<div style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">Manifest saved successfully. Search for another consignment.</div>`;
                            document.getElementById('staged-count').textContent = 'No entries staged';
                            btn.style.display = 'none';
                            successEl.classList.remove('visible');
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save manifest.';
                        errorEl.classList.add('visible');
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong. Please try again.';
                    errorEl.classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Save Manifest';
                    btn.disabled = false;
                });
        }

        // ── Quick Add Consignee ──
        function openQuickAddConsignee(target) {
            quickAddTarget = target;
            document.getElementById('quick-consignee-title').textContent = target === 'consignee' ? 'New Consignee' :
                'New Notify Party';
            document.getElementById('modal-quick-consignee').style.display = 'flex';
        }

        function closeQuickAddConsignee() {
            document.getElementById('modal-quick-consignee').style.display = 'none';
            document.getElementById('qc-name').value = '';
            document.getElementById('qc-phone').value = '';
            document.getElementById('qc-address').value = '';
            ['qc-name-error', 'qc-phone-error', 'qc-address-error'].forEach(id => {
                document.getElementById(id).classList.remove('visible');
            });
        }

        function saveQuickConsignee() {
            const name = document.getElementById('qc-name').value.trim();
            const phone = document.getElementById('qc-phone').value.trim();
            const address = document.getElementById('qc-address').value.trim();
            const btn = document.getElementById('qc-save-btn');

            let valid = true;
            if (!name) {
                document.getElementById('qc-name-error').textContent = 'Name is required.';
                document.getElementById('qc-name-error').classList.add('visible');
                valid = false;
            }
            if (!phone) {
                document.getElementById('qc-phone-error').textContent = 'Phone is required.';
                document.getElementById('qc-phone-error').classList.add('visible');
                valid = false;
            }
            if (!address) {
                document.getElementById('qc-address-error').textContent = 'Address is required.';
                document.getElementById('qc-address-error').classList.add('visible');
                valid = false;
            }
            if (!valid) return;

            btn.textContent = 'Adding...';
            btn.disabled = true;

            fetch('{{ route('master-data.consignees.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        FullName: name,
                        TelNo: phone,
                        Address1: address,
                        Address2: '',
                        Address3: ''
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (quickAddTarget === 'consignee') {
                            window.consigneeSearch?.setValue(data.ConsigneeID, data.FullName);
                        } else {
                            window.notifySearch?.setValue(data.ConsigneeID, data.FullName);
                        }
                        closeQuickAddConsignee();
                    } else {
                        document.getElementById('qc-name-error').textContent = data.message ?? 'Failed to add.';
                        document.getElementById('qc-name-error').classList.add('visible');
                    }
                })
                .catch(() => {
                    document.getElementById('qc-name-error').textContent = 'Something went wrong.';
                    document.getElementById('qc-name-error').classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Add Consignee';
                    btn.disabled = false;
                });
        }

        document.getElementById('modal-quick-consignee').addEventListener('click', function(e) {
            if (e.target === this) closeQuickAddConsignee();
        });

        // Load pending if exists
        @if ($pendingConsignment)
            currentConsignment = {
                ConsignmentID: {{ $pendingConsignment->ConsignmentID }},
                MainBL: '{{ $pendingConsignment->MainBL }}',
                ShipperName: '{{ $pendingConsignment->ShipperName }}',
                VesselName: '{{ $pendingConsignment->VesselName }}',
            };
            document.getElementById('search-bl').value = '{{ $pendingBOL }}';
            document.getElementById('info-shipper').textContent = '{{ $pendingConsignment->ShipperName }}';
            document.getElementById('info-vessel').textContent = '{{ $pendingConsignment->VesselName }}';
            document.getElementById('info-bl').textContent = '{{ $pendingConsignment->MainBL }}';
            document.getElementById('info-weight').textContent = '{{ $pendingConsignment->ContWeight }}' + ' KG';
            document.getElementById('consignment-info').style.display = 'block';
            document.getElementById('manifest-form').style.display = 'block';
            totalWeight = {{ $pendingConsignment->ContWeight }};
        @endif
    </script>
@endpush
