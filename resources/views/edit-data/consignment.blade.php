@extends('layouts.app')

@section('title', 'Edit Consignment Details')
@section('page-title', 'Edit Consignment Details')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 90vw;">

        {{-- ── Row 1: Two Search Panels ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

            {{-- Consignment Search --}}
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

            {{-- House BL Search --}}
            <div class="card">
                <p class="form-title" style="color: #dc2626;">House BL Search</p>
                <div class="form-group" style="margin-bottom: 0; position: relative; margin-top: 1rem;">
                    <label class="form-label">House BL#</label>
                    <input type="text" id="hbl-input" class="form-input" placeholder="Enter House BL# or Client Name..."
                        style="text-transform: uppercase;" autocomplete="off">
                    <div id="hbl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                           background: var(--card-bg); border: 1px solid var(--border-color);
                           border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                           width: 100%; top: 100%; max-height: 220px; overflow-y: auto;">
                    </div>
                    <input type="hidden" id="hbl-value">
                    <p id="hbl-error" class="form-error"></p>
                </div>
            </div>

        </div>

        {{-- ── Consignment Edit Form ── --}}
        <div id="consignment-form-section" style="display: none;">
            <div class="card">
                <p class="form-title" style="color: #16a34a; margin-bottom: 1rem;">Search Results</p>

                {{-- Row 1: DOT + ETA + Carrier + Shipper --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">D.O.T.</label>
                        <p id="cns-dot"
                            style="font-weight: 700; font-size: 0.95rem;
                        color: var(--text-primary); padding-top: 6px;">
                            —</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">ETA <span style="color:#ef4444">*</span></label>
                        <input type="date" id="cns-eta" class="form-input">
                        <p id="cns-eta-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Shipping Line <span style="color:#ef4444">*</span></label>
                        <select id="cns-carrier" class="form-input">
                            <option value="">— Select Carrier —</option>
                            @foreach ($carriers as $carrier)
                                <option value="{{ $carrier->CarrierID }}">{{ $carrier->CarrierName }}</option>
                            @endforeach
                        </select>
                        <p id="cns-carrier-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Shipper <span style="color:#ef4444">*</span></label>
                        <select id="cns-shipper" class="form-input">
                            <option value="">— Select Shipper —</option>
                            @foreach ($shippers as $shipper)
                                <option value="{{ $shipper->ShipperID }}">{{ $shipper->ShipperName }}</option>
                            @endforeach
                        </select>
                        <p id="cns-shipper-error" class="form-error"></p>
                    </div>

                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 1rem 0;"></div>

                {{-- Row 2: Vessel + BL + Seal + POL + POD --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Vessel <span style="color:#ef4444">*</span></label>
                        <input type="text" id="cns-vessel" class="form-input" style="text-transform: uppercase;"
                            placeholder="Vessel name">
                        <p id="cns-vessel-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Bill of Lading <span style="color:#ef4444">*</span></label>
                        <input type="text" id="cns-bl" class="form-input" style="text-transform: uppercase;"
                            placeholder="BL No.">
                        <p id="cns-bl-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Seal No</label>
                        <input type="text" id="cns-seal" class="form-input" style="text-transform: uppercase;"
                            placeholder="Seal No.">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">POL <span style="color:#ef4444">*</span></label>
                        <select id="cns-pol" class="form-input">
                            <option value="">— Select POL —</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->POL_ID }}">{{ $pol->POL_Name }}</option>
                            @endforeach
                        </select>
                        <p id="cns-pol-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">POD <span style="color:#ef4444">*</span></label>
                        <select id="cns-pod" class="form-input">
                            <option value="">— Select POD —</option>
                            @foreach ($pods as $pod)
                                <option value="{{ $pod->POD_ID }}">{{ $pod->POD_Name }}</option>
                            @endforeach
                        </select>
                        <p id="cns-pod-error" class="form-error"></p>
                    </div>

                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 1rem 0;"></div>

                {{-- Row 3: Container No + POIS + DOIS + SOB + Voyage No --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Container No.</label>
                        <input type="text" id="cns-container-no" class="form-input"
                            style="text-transform: uppercase;" placeholder="Container No.">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Place of Issue</label>
                        <input type="text" id="cns-pois" class="form-input" style="text-transform: uppercase;"
                            placeholder="Place of issue">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Date of Issue</label>
                        <input type="date" id="cns-dois" class="form-input">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Shipped On Board</label>
                        <input type="date" id="cns-sob" class="form-input">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Voyage No <span style="color:#ef4444">*</span></label>
                        <input type="text" id="cns-voyage" class="form-input" style="text-transform: uppercase;"
                            placeholder="Voyage No.">
                        <p id="cns-voyage-error" class="form-error"></p>
                    </div>

                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 1rem 0;"></div>

                {{-- Row 4: Container Size + Rotation + Weight + Charges + Agent Contact --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Cargo Type <span style="color:#ef4444">*</span></label>
                        <select id="cns-islcl" class="form-input">
                            <option value="">— Not confirmed —</option>
                            <option value="1">LCL</option>
                            <option value="0">FCL</option>
                        </select>
                        <p id="cns-islcl-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Container Size</label>
                        <input type="text" id="cns-container-size" class="form-input"
                            style="text-transform: uppercase;" placeholder="e.g. 40">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Rotation #</label>
                        <input type="text" id="cns-rotation" class="form-input" style="text-transform: uppercase;"
                            placeholder="Rotation No.">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Gross Weight (KG)</label>
                        <input type="number" id="cns-weight" class="form-input" placeholder="0" min="0"
                            step="0.001">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Handling Cost</label>
                        <input type="number" id="cns-charges" class="form-input" placeholder="0" min="0"
                            step="0.01">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Agent Contact</label>
                        <input type="text" id="cns-agent-contact" class="form-input" placeholder="Phone No.">
                    </div>

                </div>

                {{-- Submit messages + button --}}
                <div style="margin-top: 1.5rem;">
                    <p id="cns-submit-error" class="form-error" style="text-align:center; margin-bottom: 8px;"></p>
                    <p id="cns-submit-success"
                        style="text-align:center; margin-bottom: 8px;
                    font-size: 0.82rem; color: #16a34a; display: none;">
                    </p>
                    <button onclick="updateConsignment()"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #16a34a; color: white; font-size: 0.9rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                        Update Consignment Details
                    </button>
                </div>

            </div>

            {{-- FCL Container Details --}}
            <div id="containers-section" style="display: none;">
                <div class="card">
                    <p class="form-title" style="margin-bottom: 1rem;">Container Details</p>
                    <div id="containers-table-body"></div>
                </div>
            </div>

        </div>

        {{-- ── HBL Edit Form ── --}}
        <div id="hbl-form-section" style="display: none;">
            <div class="card">
                <p class="form-title" style="color: #dc2626; margin-bottom: 1rem;">Search Results</p>

                {{-- Read-only: Main BL + House BL --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <p
                            style="font-size: 0.7rem; text-transform: uppercase;
                        letter-spacing: 0.05em; color: var(--text-muted);">
                            Bill of Lading</p>
                        <p id="hbl-main-bl-display"
                            style="font-weight: 700;
                        font-size: 1rem; color: var(--text-primary);">
                            —</p>
                    </div>
                    <div>
                        <p
                            style="font-size: 0.7rem; text-transform: uppercase;
                        letter-spacing: 0.05em; color: var(--text-muted);">
                            House BL</p>
                        <p id="hbl-house-bl-display"
                            style="font-weight: 700;
                        font-size: 1rem; color: var(--text-primary);">
                            —</p>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border-color); margin-bottom: 1rem;"></div>

                {{-- Row: Consignee + Notify Party --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Consignee <span style="color:#ef4444">*</span></label>
                        <select id="hbl-consignee" class="form-input">
                            <option value="">— Select Consignee —</option>
                            @foreach ($consignees as $consignee)
                                <option value="{{ $consignee->ConsigneeID }}">{{ $consignee->FullName }}</option>
                            @endforeach
                        </select>
                        <p id="hbl-consignee-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Notify Party</label>
                        <select id="hbl-notify-party" class="form-input">
                            <option value="">— None —</option>
                            @foreach ($consignees as $consignee)
                                <option value="{{ $consignee->ConsigneeID }}">{{ $consignee->FullName }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 1rem 0;"></div>

                {{-- Row: Weight (readonly) + Package + Unit + Item Type --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Weight (KG)</label>
                        <p id="hbl-weight-display"
                            style="font-weight: 700; font-size: 0.95rem;
                        color: var(--text-primary); padding-top: 6px;">
                            —</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Package <span style="color:#ef4444">*</span></label>
                        <input type="number" id="hbl-package" class="form-input" placeholder="0" min="1">
                        <p id="hbl-package-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Unit <span style="color:#ef4444">*</span></label>
                        <select id="hbl-unit" class="form-input">
                            <option value="">— Select Unit —</option>
                            <option value="UNIT">UNIT</option>
                            <option value="CTN">CTN</option>
                            <option value="PKG">PKG</option>
                            <option value="PCS">PCS</option>
                            <option value="BAG">BAG</option>
                            <option value="SET">SET</option>
                        </select>
                        <p id="hbl-unit-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Item Type <span style="color:#ef4444">*</span></label>
                        <select id="hbl-item-type" class="form-input">
                            <option value="">— Select Type —</option>
                            <option value="GOODS">GOODS</option>
                            <option value="VEHICLE">VEHICLE</option>
                            <option value="MOTORBIKE">MOTORBIKE</option>
                        </select>
                        <p id="hbl-item-type-error" class="form-error"></p>
                    </div>

                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 1rem 0;"></div>

                {{-- Row: Description + VIN + Other Info --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description <span style="color:#ef4444">*</span></label>
                        <input type="text" id="hbl-description" class="form-input" style="text-transform: uppercase;"
                            placeholder="Item description">
                        <p id="hbl-description-error" class="form-error"></p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">VIN**</label>
                        <input type="text" id="hbl-vin" class="form-input" style="text-transform: uppercase;"
                            placeholder="VIN (vehicles only)">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Other Information**</label>
                        <input type="text" id="hbl-other-info" class="form-input" style="text-transform: uppercase;"
                            placeholder="Other info">
                    </div>

                </div>

                {{-- Submit messages + button --}}
                <div style="margin-top: 1.5rem;">
                    <p id="hbl-submit-error" class="form-error" style="text-align:center; margin-bottom: 8px;"></p>
                    <p id="hbl-submit-success"
                        style="text-align:center; margin-bottom: 8px;
                    font-size: 0.82rem; color: #16a34a; display: none;">
                    </p>
                    <button onclick="updateHBL()"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none;
                           background: #dc2626; color: white; font-size: 0.9rem;
                           font-weight: 600; cursor: pointer; letter-spacing: 0.02em;">
                        Update House BL Details
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

        // ── State ──
        let currentConsignmentId = null;
        let currentIsLCL = true;
        let currentHBLConsignmentId = null;
        let currentHouseBL = null;

        // ── FCL fields that get disabled when empty ──
        const FCL_OPTIONAL_FIELDS = [
            'cns-seal', 'cns-container-no', 'cns-container-size',
            'cns-pois', 'cns-dois', 'cns-sob',
            'cns-rotation', 'cns-weight', 'cns-charges', 'cns-agent-contact',
        ];

        // ── Init SearchDropdown instances ──
        function initSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: '{{ route('edit-data.consignment.search-bl') }}',
                labelKey: 'BL',
                subKey: 'VesselName',
                valueKey: 'BL',
                minLength: 2,
                onSelect: (bl) => loadBL(bl),
            });

            window.hblSearch = new SearchDropdown({
                inputId: 'hbl-input',
                dropdownId: 'hbl-dropdown',
                hiddenId: 'hbl-value',
                url: '{{ route('edit-data.consignment.search-hbl') }}',
                labelKey: 'label',
                subKey: 'MainBL',
                valueKey: 'HouseBL',
                minLength: 2,
                onSelect: (hbl) => loadHBL(hbl),
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initSearch, 0);
        });

        // ── Load consignment by BL ──
        function loadBL(bl) {
            setMsg('bl-error', '', false);

            fetch('{{ route('edit-data.consignment.load-bl') }}?BL=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setMsg('bl-error', data.message ?? 'Consignment not found.', false);
                        document.getElementById('consignment-form-section').style.display = 'none';
                        document.getElementById('containers-section').style.display = 'none';
                        return;
                    }

                    currentConsignmentId = data.consignment.ConsignmentID;
                    currentIsLCL = data.isLCL;

                    populateConsignmentForm(data.consignment);

                    // Show FCL containers table only for FCL with container rows
                    if (!data.isLCL && data.containers && data.containers.length) {
                        renderContainers(data.containers, data.consignment.ConsignmentID);
                        document.getElementById('containers-section').style.display = 'block';
                    } else {
                        document.getElementById('containers-section').style.display = 'none';
                    }

                    // CHANGED: hide HBL section when consignment loads
                    document.getElementById('hbl-form-section').style.display = 'none';
                    document.getElementById('consignment-form-section').style.display = 'block';

                })
                .catch(() => setMsg('bl-error', 'An error occurred. Please try again.', false));
        }

        // ── Populate consignment form fields ──
        function populateConsignmentForm(c) {
            document.getElementById('cns-dot').textContent = formatDate(c.Date);
            document.getElementById('cns-eta').value = c.ETA ?? '';
            document.getElementById('cns-carrier').value = c.CarrierID ?? '';
            document.getElementById('cns-shipper').value = c.ShipperID ?? '';
            document.getElementById('cns-vessel').value = c.VesselName ?? '';
            document.getElementById('cns-bl').value = c.BL ?? '';
            document.getElementById('cns-seal').value = c.SealNo ?? '';
            document.getElementById('cns-pol').value = c.POL_ID ?? '';
            document.getElementById('cns-pod').value = c.POD_ID ?? '';
            document.getElementById('cns-container-no').value = c.ContainerNo ?? '';
            document.getElementById('cns-pois').value = c.POIS ?? '';
            document.getElementById('cns-dois').value = c.DOIS ?? '';
            document.getElementById('cns-sob').value = c.SOB ?? '';
            document.getElementById('cns-voyage').value = c.VoyageNo ?? '';
            document.getElementById('cns-container-size').value = c.ContainerSize ?? '';
            document.getElementById('cns-rotation').value = c.Rotation ?? '';
            document.getElementById('cns-weight').value = c.ContWeight ?? '';
            document.getElementById('cns-charges').value = c.Charges ?? '';
            document.getElementById('cns-islcl').value = c.IsLCL === null ? '' : String(Number(c.IsLCL));
            document.getElementById('cns-agent-contact').value = c.AgentContact ?? '';

            // For FCL: disable fields that loaded as empty or zero
            FCL_OPTIONAL_FIELDS.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (!currentIsLCL) {
                    const empty = !el.value || el.value.trim() === '' || el.value === '0';
                    el.disabled = empty;
                    el.style.opacity = empty ? '0.5' : '1';
                } else {
                    el.disabled = false;
                    el.style.opacity = '1';
                }
            });
        }

        // ── Render FCL container detail cards ──
        function renderContainers(containers, consignmentId) {
            const tbody = document.getElementById('containers-table-body');
            tbody.innerHTML = '';

            containers.forEach((cont, index) => {
                const origNo = escHtml(cont.ContainerNo ?? '');
                const card = document.createElement('div');
                card.style.cssText =
                    'border:1px solid var(--border-color); border-radius:10px;' +
                    'padding:1rem; margin-bottom:1rem; background:var(--content-bg);';

                card.innerHTML = `
            <p style="font-size:0.75rem; font-weight:600; color:var(--text-muted);
                text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem;">
                Container ${index + 1}
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;
                gap:0.75rem; align-items:end;">
                <div>
                    <label style="font-size:0.72rem; color:var(--text-muted);
                        text-transform:uppercase; display:block; margin-bottom:4px;">Seal No</label>
                    <input type="text" id="cont-seal-${index}" class="form-input"
                        value="${escHtml(cont.SealNo ?? '')}"
                        style="text-transform:uppercase;">
                </div>
                <div>
                    <label style="font-size:0.72rem; color:var(--text-muted);
                        text-transform:uppercase; display:block; margin-bottom:4px;">
                        Container No <span style="color:#ef4444">*</span></label>
                    <input type="text" id="cont-no-${index}" class="form-input"
                        value="${escHtml(cont.ContainerNo ?? '')}"
                        style="text-transform:uppercase;">
                </div>
                <div>
                    <label style="font-size:0.72rem; color:var(--text-muted);
                        text-transform:uppercase; display:block; margin-bottom:4px;">
                        Size <span style="color:#ef4444">*</span></label>
                    <input type="text" id="cont-size-${index}" class="form-input"
                        value="${escHtml(cont.ContainerSize ?? '')}"
                        style="text-transform:uppercase;">
                </div>
                <div>
                    <label style="font-size:0.72rem; color:var(--text-muted);
                        text-transform:uppercase; display:block; margin-bottom:4px;">
                        Weight (KG) <span style="color:#ef4444">*</span></label>
                    <input type="number" id="cont-weight-${index}" class="form-input"
                        value="${cont.Weight ?? 0}" min="0" step="0.001">
                </div>
                <div>
                    <label style="font-size:0.72rem; color:var(--text-muted);
                        text-transform:uppercase; display:block; margin-bottom:4px;">
                        Handling Cost <span style="color:#ef4444">*</span></label>
                    <input type="number" id="cont-hcost-${index}" class="form-input"
                        value="${cont.HandlingCost ?? 0}" min="0" step="0.01">
                </div>
                <div>
                    <button onclick="updateContainer(${consignmentId}, '${origNo}', ${index})"
                        style="padding:8px 16px; border-radius:8px; border:none;
                               background:#16a34a; color:white; font-size:0.78rem;
                               font-weight:600; cursor:pointer; white-space:nowrap;">
                        Save
                    </button>
                </div>
            </div>
            <p id="cont-msg-${index}" style="font-size:0.78rem; margin-top:6px;"></p>
        `;

                tbody.appendChild(card);
            });
        }

        // ── Update container_main ──
        window.updateConsignment = function() {
            if (!currentConsignmentId) return;
            clearErrors(['cns-eta-error', 'cns-carrier-error', 'cns-shipper-error',
                'cns-vessel-error', 'cns-bl-error', 'cns-pol-error',
                'cns-pod-error', 'cns-voyage-error', 'cns-submit-error'
            ]);

            if (!confirm('Are you sure you want to update this consignment?')) return;

            fetch('{{ route('edit-data.consignment.update-bl') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        ConsignmentID: currentConsignmentId,
                        ETA: document.getElementById('cns-eta').value,
                        CarrierID: document.getElementById('cns-carrier').value,
                        ShipperID: document.getElementById('cns-shipper').value,
                        VesselName: document.getElementById('cns-vessel').value,
                        VoyageNo: document.getElementById('cns-voyage').value,
                        SealNo: document.getElementById('cns-seal').value,
                        BL: document.getElementById('cns-bl').value,
                        ContainerNo: document.getElementById('cns-container-no').value,
                        ContainerSize: document.getElementById('cns-container-size').value,
                        POIS: document.getElementById('cns-pois').value,
                        DOIS: document.getElementById('cns-dois').value,
                        SOB: document.getElementById('cns-sob').value,
                        POL_ID: document.getElementById('cns-pol').value,
                        POD_ID: document.getElementById('cns-pod').value,
                        Rotation: document.getElementById('cns-rotation').value,
                        AgentContact: document.getElementById('cns-agent-contact').value,
                        ContWeight: document.getElementById('cns-weight').value,
                        Charges: document.getElementById('cns-charges').value,
                        IsLCL: document.getElementById('cns-islcl').value,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('cns-submit-success', data.message);

                        if (data.eta_changed) {
                            openSmsModal(
                                data.bl,
                                '',
                                0,
                                '{{ route('consignments.send-notification') }}',
                                'eta_change', {
                                    phone: data.phone ?? '',
                                    consignee: data.consignee ?? '—'
                                }
                            );
                        }

                        setTimeout(() => {
                            currentConsignmentId = null;
                            currentIsLCL = true;
                            document.getElementById('bl-input').value = '';
                            document.getElementById('bl-value').value = '';
                            document.getElementById('consignment-form-section').style.display = 'none';
                            document.getElementById('containers-section').style.display = 'none';
                        }, 1500);
                    } else {
                        setMsg('cns-submit-error', data.message ?? 'Update failed.', false);
                    }
                })
                .catch(() => setMsg('cns-submit-error', 'An error occurred. Please try again.', false));
        };

        // ── Update container_details row ──
        window.updateContainer = function(consignmentId, originalContainerNo, index) {
            const msgEl = document.getElementById('cont-msg-' + index);
            msgEl.textContent = '';

            if (!confirm('Save changes to this container?')) return;

            fetch('{{ route('edit-data.consignment.update-container') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        ConsignmentID: consignmentId,
                        OriginalContainerNo: originalContainerNo,
                        SealNo: document.getElementById('cont-seal-' + index).value,
                        ContainerNo: document.getElementById('cont-no-' + index).value,
                        ContainerSize: document.getElementById('cont-size-' + index).value,
                        Weight: document.getElementById('cont-weight-' + index).value,
                        HandlingCost: document.getElementById('cont-hcost-' + index).value,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    msgEl.textContent = data.message ?? (data.success ? 'Saved.' : 'Failed.');
                    msgEl.style.color = data.success ? '#16a34a' : '#ef4444';
                })
                .catch(() => {
                    msgEl.textContent = 'An error occurred. Please try again.';
                    msgEl.style.color = '#ef4444';
                });
        };

        // ── Load HBL ──
        function loadHBL(hbl) {
            setMsg('hbl-error', '', false);

            fetch('{{ route('edit-data.consignment.load-hbl') }}?HouseBL=' + encodeURIComponent(hbl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setMsg('hbl-error', data.message ?? 'House BL not found.', false);
                        document.getElementById('hbl-form-section').style.display = 'none';
                        return;
                    }

                    currentHBLConsignmentId = data.entry.ConsignmentID;
                    currentHouseBL = data.entry.HouseBL;

                    populateHBLForm(data.entry);
                    // CHANGED: hide consignment section when HBL loads
                    document.getElementById('consignment-form-section').style.display = 'none';
                    document.getElementById('containers-section').style.display = 'none';
                    document.getElementById('hbl-form-section').style.display = 'block';

                })
                .catch(() => setMsg('hbl-error', 'An error occurred. Please try again.', false));
        }

        // ── Populate HBL form fields ──
        function populateHBLForm(entry) {
            document.getElementById('hbl-main-bl-display').textContent = entry.MainBL ?? '—';
            document.getElementById('hbl-house-bl-display').textContent = entry.HouseBL ?? '—';
            document.getElementById('hbl-weight-display').textContent = entry.Weight ?? '—';
            document.getElementById('hbl-consignee').value = entry.ConsigneeID ?? '';
            document.getElementById('hbl-notify-party').value = entry.Consigenee2_ID ?? '';
            document.getElementById('hbl-package').value = entry.Package ?? '';
            document.getElementById('hbl-unit').value = entry.Unit ?? '';
            document.getElementById('hbl-item-type').value = entry.ItemType ?? '';
            document.getElementById('hbl-description').value = entry.Description ?? '';
            document.getElementById('hbl-vin').value = entry.VIN ?? '';
            document.getElementById('hbl-other-info').value = entry.OtherInfo ?? '';
        }

        // ── Update manifestation_breakdown ──
        window.updateHBL = function() {
            if (!currentHBLConsignmentId || !currentHouseBL) return;
            clearErrors(['hbl-consignee-error', 'hbl-package-error', 'hbl-unit-error',
                'hbl-item-type-error', 'hbl-description-error', 'hbl-submit-error'
            ]);

            if (!confirm('Are you sure you want to update this House BL?')) return;

            fetch('{{ route('edit-data.consignment.update-hbl') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        ConsignmentID: currentHBLConsignmentId,
                        HouseBL: currentHouseBL,
                        ConsigneeID: document.getElementById('hbl-consignee').value,
                        Consigenee2_ID: document.getElementById('hbl-notify-party').value,
                        Package: document.getElementById('hbl-package').value,
                        Unit: document.getElementById('hbl-unit').value,
                        ItemType: document.getElementById('hbl-item-type').value,
                        Description: document.getElementById('hbl-description').value,
                        VIN: document.getElementById('hbl-vin').value,
                        OtherInfo: document.getElementById('hbl-other-info').value,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('hbl-submit-success', data.message);
                        setTimeout(() => {
                            currentHBLConsignmentId = null;
                            currentHouseBL = null;
                            document.getElementById('hbl-input').value = '';
                            document.getElementById('hbl-value').value = '';
                            document.getElementById('hbl-form-section').style.display = 'none';
                        }, 1500);
                    } else {
                        setMsg('hbl-submit-error', data.message ?? 'Update failed.', false);
                    }
                })
                .catch(() => setMsg('hbl-submit-error', 'An error occurred. Please try again.', false));
        };

        // ── Utility functions ──
        function formatDate(str) {
            if (!str) return '—';
            const d = new Date(str);
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function escHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function setMsg(id, message, isSuccess) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = message;
            if (message) el.classList.add('visible');
            else el.classList.remove('visible');
        }

        function showSuccess(id, message) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = message;
            el.style.display = 'block';
            setTimeout(() => {
                el.textContent = '';
                el.style.display = 'none';
            }, 4000);
        }

        function clearErrors(ids) {
            ids.forEach(id => setMsg(id, '', false));
        }
    </script>
@endpush
