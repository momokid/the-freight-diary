@extends('layouts.app')

@section('title', 'New Consignment')
@section('page-title', 'New Consignment')

@section('content')

{{-- Pending containers warning --}}
@if($pendingContainers->isNotEmpty())
<div style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
    <svg style="width: 18px; height: 18px; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <p style="font-size: 0.8rem; font-weight: 600; color: #92400e;">Pending containers found for BL# {{ $pendingBOL }}</p>
        <p style="font-size: 0.75rem; color: #92400e; margin-top: 2px;">You have {{ $pendingContainers->count() }} staged container(s). Complete this consignment or clear to start a new one.</p>
    </div>
    <button onclick="clearContainers()" style="margin-left: auto; padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(234,179,8,0.4); background: transparent; color: #92400e; font-size: 0.75rem; cursor: pointer;">
        Clear & Start New
    </button>
</div>
@endif

<form id="consignment-form">
<div class="flex gap-6">

    {{-- ── Main Form Area ── --}}
    <div style="flex: 1; min-width: 0;">

        {{-- OCR Upload Section --}}
        <div class="card mb-4" style="margin-bottom: 1rem;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="form-title">📄 Auto-fill from BL Document</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        Upload a BL image or PDF to automatically extract and fill the form fields
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="file" id="bl-file-input" accept="image/*,.pdf" style="display: none;" onchange="extractFromBL(this)">
                    <button type="button" onclick="document.getElementById('bl-file-input').click()"
                        id="ocr-btn"
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; border: 1.5px solid #16a34a; background: rgba(22,163,74,0.06); color: #16a34a; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.15s;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload BL Document
                    </button>
                    <div id="ocr-status" style="font-size: 0.75rem; color: var(--text-muted); display: none;">
                        <span id="ocr-status-text">Extracting data...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cargo Details --}}
        <div class="card" style="margin-bottom: 1rem;">

            <p class="form-title" style="margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Cargo Details
            </p>

            {{-- Row 1: DOT, ETA --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Date of Transaction <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="dot" name="DOT" class="form-input" value="{{ now()->toDateString() }}">
                    <p id="dot-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">ETA <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="eta" name="ETA" class="form-input">
                    <p id="eta-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 2: Carrier, Shipper --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Shipping Line (Carrier) <span style="color: #ef4444;">*</span>
                        <button type="button" onclick="openQuickAdd('carrier')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                    </label>
                    <select id="carrier-id" name="CarrierID" class="form-input">
                        <option value="">Select carrier...</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->CarrierID }}">{{ $carrier->CarrierName }}</option>
                        @endforeach
                    </select>
                    <p id="carrier-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Shipper <span style="color: #ef4444;">*</span>
                        <button type="button" onclick="openQuickAdd('shipper')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                    </label>
                    <select id="shipper-id" name="ShipperID" class="form-input">
                        <option value="">Select shipper...</option>
                        @foreach($shippers as $shipper)
                            <option value="{{ $shipper->ShipperID }}">{{ $shipper->ShipperName }}</option>
                        @endforeach
                    </select>
                    <p id="shipper-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 3: Vessel Name, Voyage No --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Vessel Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="vessel-name" name="VesselName" placeholder="e.g. MSC EMMA" maxlength="80" class="form-input">
                    <p id="vessel-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Voyage No <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="voyage-no" name="VoyageNo" placeholder="e.g. 241N" maxlength="80" class="form-input">
                    <p id="voyage-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 4: BL, Place of Issue --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Bill of Lading <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="bl" name="BL" placeholder="e.g. MSCU1234567" maxlength="50" class="form-input" style="text-transform: uppercase;">
                    <p id="bl-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Place of Issue <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="pois" name="POIS" placeholder="e.g. Shanghai" maxlength="80" class="form-input">
                    <p id="pois-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 5: Date of Issue, Shipped on Board --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Date of Issue <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="dois" name="DOIS" class="form-input">
                    <p id="dois-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Shipped on Board <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="sob" name="SOB" class="form-input">
                    <p id="sob-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 6: POL, POD --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Port of Loading (POL) <span style="color: #ef4444;">*</span>
                        <button type="button" onclick="openQuickAdd('pol')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                    </label>
                    <select id="pol-id" name="POL_ID" class="form-input">
                        <option value="">Select POL...</option>
                        @foreach($pols as $pol)
                            <option value="{{ $pol->POL_ID }}">{{ $pol->POL_Name }}</option>
                        @endforeach
                    </select>
                    <p id="pol-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Port of Discharge (POD) <span style="color: #ef4444;">*</span>
                        <button type="button" onclick="openQuickAdd('pod')" style="margin-left: 6px; color: #16a34a; background: none; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">+ New</button>
                    </label>
                    <select id="pod-id" name="POD_ID" class="form-input">
                        <option value="">Select POD...</option>
                        @foreach($pods as $pod)
                            <option value="{{ $pod->POD_ID }}">{{ $pod->POD_Name }}</option>
                        @endforeach
                    </select>
                    <p id="pod-error" class="form-error"></p>
                </div>
            </div>

            {{-- Row 7: Rotation, Agent Contact --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Rotation # <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="rotation" name="Rotation" placeholder="e.g. RTN-2024-001" maxlength="30" class="form-input">
                    <p id="rotation-error" class="form-error"></p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Agent Contact <span style="color: var(--text-muted);">optional</span></label>
                    <input type="text" id="agent-contact" name="AgentContact" placeholder="e.g. 0244000000" maxlength="20" class="form-input">
                </div>
            </div>

            {{-- Row 8: Destination and Ownership only --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                
                 <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Destination <span style="color: var(--text-muted);">optional</span></label>
                    <input type="text" id="destination" name="Destination" placeholder="e.g. Kumasi, Ghana" class="form-input">
                    <p id="destination-error" class="form-error"></p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Ownership <span style="color: #ef4444;">*</span></label>
                    <select id="ownership" name="Ownership" class="form-input">
                        <option value="">Select ownership...</option>
                        <option value="1">Self</option>
                        <option value="2">Third Party</option>
                    </select>
                    <p id="ownership-error" class="form-error"></p>
                </div>
            </div>

        </div>

        {{-- Container Details --}}
        <div class="card" style="margin-bottom: 1rem;">

            <p class="form-title" style="margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Container Details
            </p>

            {{-- Container input row --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 120px 120px 140px auto; gap: 0.75rem; align-items: end; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Seal No <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="seal-no" placeholder="e.g. SL123456" maxlength="50" class="form-input" style="text-transform: uppercase;">
                </div>
                <div>
                    <label class="form-label">Container No <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="container-no" placeholder="e.g. MSCU1234567" maxlength="50" class="form-input" style="text-transform: uppercase;">
                </div>
                <div>
                    <label class="form-label">Size <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="container-size" placeholder="e.g. 40" maxlength="15" class="form-input">
                </div>
                <div>
                    <label class="form-label">Weight (KG) <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="container-weight" placeholder="0.00" min="0.01" step="0.01" class="form-input">
                </div>
                <div>
                    <label class="form-label">Est. Handling Cost <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="handling-cost" placeholder="0.00" min="0" step="0.01" value="{{ $defaultHandlingCost }}" class="form-input">
                </div>
                <div>
                    <label class="form-label" style="visibility: hidden;">Add</label>
                    <button type="button" onclick="addContainer()"
                        style="width: 40px; height: 42px; border-radius: 8px; border: none; background: #16a34a; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>
            <p id="container-error" class="form-error" style="margin-bottom: 0.5rem;"></p>

            {{-- BL field for containers --}}
            <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 14px; height: 14px; color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    Containers are staged under the BL number entered above. The BL field must be filled before adding containers.
                </p>
            </div>

            {{-- Staged containers table --}}
            <div id="staged-containers">
                @if($pendingContainers->isNotEmpty())
                    @include('consignments.partials.container-table', ['containers' => $pendingContainers])
                @else
                    <div id="empty-staging" style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                        No containers added yet. Add at least one container before submitting.
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- ── Right Summary Panel ── --}}
    <div class="flex-shrink-0" style="width: 260px;">
        <div class="card" style="position: sticky; top: 76px;">

            <p class="form-title" style="margin-bottom: 1rem;">Summary</p>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">BL Number</span>
                    <span id="summary-bl" style="font-weight: 600; color: var(--text-primary);">—</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">Containers</span>
                    <span id="summary-containers" style="font-weight: 600; color: var(--text-primary);">0</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">Total Weight</span>
                    <span id="summary-weight" style="font-weight: 600; color: var(--text-primary);">0 KG</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">Total Handling</span>
                    <span id="summary-cost" style="font-weight: 600; color: #16a34a;">GHS 0.00</span>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="button" onclick="submitConsignment()" id="submit-btn"
                    style="width: 100%; padding: 12px; background: #16a34a; color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: opacity 0.15s;">
                    Register Consignment
                </button>
                <p id="submit-error" class="form-error" style="margin-top: 8px; text-align: center;"></p>
                <p id="submit-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>
            </div>

            {{-- Pre-requisite checks --}}
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 8px;">System Checks</p>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ DB::table('active_handling_cost')->exists() ? '#16a34a' : '#ef4444' }};"></span>
                        <span style="color: var(--text-muted);">Handling charges account</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ DB::table('active_ie')->exists() ? '#16a34a' : '#ef4444' }};"></span>
                        <span style="color: var(--text-muted);">IE account</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ DB::table('active_vault')->exists() ? '#16a34a' : '#ef4444' }};"></span>
                        <span style="color: var(--text-muted);">Vault account</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</form>

{{-- ── Quick Add Modals ── --}}

{{-- Carrier Modal --}}
<div id="modal-carrier" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div class="flex items-center justify-between mb-4">
            <p class="form-title">New Carrier</p>
            <button onclick="closeQuickAdd('carrier')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Carrier Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qa-carrier-name" class="form-input" placeholder="e.g. Maersk">
            <p id="qa-carrier-error" class="form-error"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeQuickAdd('carrier')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAdd('carrier')" id="qa-carrier-btn" class="btn-primary" style="flex: 1;">Add Carrier</button>
        </div>
    </div>
</div>

{{-- Shipper Modal --}}
<div id="modal-shipper" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div class="flex items-center justify-between mb-4">
            <p class="form-title">New Shipper</p>
            <button onclick="closeQuickAdd('shipper')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Shipper Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qa-shipper-name" class="form-input" placeholder="e.g. China Shipping">
            <p id="qa-shipper-error" class="form-error"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Address <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qa-shipper-address" class="form-input" placeholder="Address line 1">
            <p id="qa-shipper-address-error" class="form-error"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeQuickAdd('shipper')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAdd('shipper')" id="qa-shipper-btn" class="btn-primary" style="flex: 1;">Add Shipper</button>
        </div>
    </div>
</div>

{{-- POL Modal --}}
<div id="modal-pol" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div class="flex items-center justify-between mb-4">
            <p class="form-title">New Port of Loading</p>
            <button onclick="closeQuickAdd('pol')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Port Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qa-pol-name" class="form-input" placeholder="e.g. Shanghai">
            <p id="qa-pol-error" class="form-error"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeQuickAdd('pol')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAdd('pol')" id="qa-pol-btn" class="btn-primary" style="flex: 1;">Add POL</button>
        </div>
    </div>
</div>

{{-- POD Modal --}}
<div id="modal-pod" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
        <div class="flex items-center justify-between mb-4">
            <p class="form-title">New Port of Discharge</p>
            <button onclick="closeQuickAdd('pod')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Port Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="qa-pod-name" class="form-input" placeholder="e.g. Tema">
            <p id="qa-pod-error" class="form-error"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeQuickAdd('pod')" class="btn-secondary" style="flex: 1;">Cancel</button>
            <button onclick="saveQuickAdd('pod')" id="qa-pod-btn" class="btn-primary" style="flex: 1;">Add POD</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ── Commodity type filtering ──

function loadCommodityTypes() {
    const categoryId = document.getElementById('commodity-category').value;
    const typeSelect = document.getElementById('cmdt-type-id');
    typeSelect.innerHTML = '<option value="">Select type...</option>';
    if (!categoryId || !categoryTypes[categoryId]) return;
    categoryTypes[categoryId].forEach(type => {
        const opt = document.createElement('option');
        opt.value = type.TypeID;
        opt.textContent = type.TypeName;
        typeSelect.appendChild(opt);
    });
}

// ── Summary panel updates ──
function updateSummary(containers) {
    const bl = document.getElementById('bl').value.toUpperCase();
    document.getElementById('summary-bl').textContent = bl || '—';
    document.getElementById('summary-containers').textContent = containers.length;
    const totalWeight = containers.reduce((s, c) => s + parseFloat(c.Weight || 0), 0);
    const totalCost   = containers.reduce((s, c) => s + parseFloat(c.HandlingCost || 0), 0);
    document.getElementById('summary-weight').textContent = totalWeight.toFixed(2) + ' KG';
    document.getElementById('summary-cost').textContent   = 'GHS ' + totalCost.toFixed(2);
}

// Update BL in summary when typed
document.getElementById('bl').addEventListener('input', function() {
    document.getElementById('summary-bl').textContent = this.value.toUpperCase() || '—';
});

// ── Add container to staging ──
function addContainer() {
    const bl       = document.getElementById('bl').value.trim();
    const sealNo   = document.getElementById('seal-no').value.trim();
    const contNo   = document.getElementById('container-no').value.trim();
    const size     = document.getElementById('container-size').value.trim();
    const weight   = document.getElementById('container-weight').value;
    const cost     = document.getElementById('handling-cost').value;
    const errorEl  = document.getElementById('container-error');

    errorEl.classList.remove('visible');

    if (!bl)     { errorEl.textContent = 'Please enter the Bill of Lading number first.'; errorEl.classList.add('visible'); return; }
    if (!sealNo) { errorEl.textContent = 'Seal No is required.'; errorEl.classList.add('visible'); return; }
    if (!contNo) { errorEl.textContent = 'Container No is required.'; errorEl.classList.add('visible'); return; }
    if (!size)   { errorEl.textContent = 'Container Size is required.'; errorEl.classList.add('visible'); return; }
    if (!weight || parseFloat(weight) <= 0) { errorEl.textContent = 'Weight must be greater than zero.'; errorEl.classList.add('visible'); return; }
    if (cost === '' || parseFloat(cost) < 0) { errorEl.textContent = 'Handling cost is required.'; errorEl.classList.add('visible'); return; }

    fetch('{{ route("consignments.containers.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            BOL: bl.toUpperCase(),
            SealNo: sealNo.toUpperCase(),
            ContainerNo: contNo.toUpperCase(),
            ContainerSize: size,
            Weight: weight,
            HandlingCost: cost,
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Clear container fields
            document.getElementById('seal-no').value = '';
            document.getElementById('container-no').value = '';
            document.getElementById('container-size').value = '';
            document.getElementById('container-weight').value = '';
            // Render staging table
            renderContainerTable(data.containers);
            updateSummary(data.containers);
        } else {
            errorEl.textContent = data.message ?? 'Failed to add container.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); });
}

// ── Render container staging table ──
function renderContainerTable(containers) {
    const wrapper = document.getElementById('staged-containers');
    if (!containers || containers.length === 0) {
        wrapper.innerHTML = `<div id="empty-staging" style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">No containers added yet.</div>`;
        return;
    }

    let rows = containers.map(c => `
        <tr>
            <td class="td-mono">${c.SealNo}</td>
            <td class="td-mono">${c.ContainerNo}</td>
            <td class="td-muted">${c.ContainerSize}</td>
            <td class="td-muted">${parseFloat(c.Weight).toFixed(2)}</td>
            <td style="color: #16a34a; font-weight: 500;">${parseFloat(c.HandlingCost).toFixed(2)}</td>
            <td style="text-align: center;">
                <button onclick="removeContainer('${c.BOL}', '${c.ContainerNo}')" class="btn-icon btn-icon-danger" title="Remove">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </td>
        </tr>`).join('');

    wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Seal No</th>
                    <th>Container No</th>
                    <th style="width: 80px;">Size</th>
                    <th style="width: 100px;">Weight (KG)</th>
                    <th style="width: 120px;">Handling Cost</th>
                    <th style="width: 60px; text-align: center;">Remove</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>`;
}

// ── Remove container ──
function removeContainer(bol, containerNo) {
    fetch('{{ route("consignments.containers.remove") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ BOL: bol, ContainerNo: containerNo }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderContainerTable(data.containers);
            updateSummary(data.containers);
        }
    })
    .catch(() => alert('Something went wrong.'));
}

// ── Clear all containers ──
function clearContainers() {
    if (!confirm('Clear all staged containers and start a new consignment?')) return;
    fetch('{{ route("consignments.containers.clear") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderContainerTable([]);
            updateSummary([]);
            // Hide warning banner
            const banner = document.querySelector('[style*="rgba(234,179,8"]');
            if (banner) banner.remove();
        }
    })
    .catch(() => alert('Something went wrong.'));
}

// Initialize summary if pending containers exist
@if($pendingContainers->isNotEmpty())
updateSummary(@json($pendingContainers));
@endif

// ── Submit consignment ──
function submitConsignment() {
    const btn     = document.getElementById('submit-btn');
    const errorEl = document.getElementById('submit-error');
    const successEl = document.getElementById('submit-success');

    errorEl.classList.remove('visible');
    successEl.classList.remove('visible');

    // Gather all field values
    const fields = {
        DOT:         document.getElementById('dot').value,
        ETA:         document.getElementById('eta').value,
        CarrierID:   document.getElementById('carrier-id').value,
        ShipperID:   document.getElementById('shipper-id').value,
        VesselName:  document.getElementById('vessel-name').value.trim(),
        VoyageNo:    document.getElementById('voyage-no').value.trim(),
        BL:          document.getElementById('bl').value.trim().toUpperCase(),
        POIS:        document.getElementById('pois').value.trim(),
        DOIS:        document.getElementById('dois').value,
        SOB:         document.getElementById('sob').value,
        POL_ID:      document.getElementById('pol-id').value,
        POD_ID:      document.getElementById('pod-id').value,
        Rotation:    document.getElementById('rotation').value.trim(),
        AgentContact: document.getElementById('agent-contact').value.trim(),
        Destination: document.getElementById('destination').value.trim(),
        Ownership:   document.getElementById('ownership').value,
    };

    // Client-side validation
    const required = [
        ['DOT', 'Date of Transaction'],
        ['ETA', 'ETA'],
        ['CarrierID', 'Carrier'],
        ['ShipperID', 'Shipper'],
        ['VesselName', 'Vessel Name'],
        ['VoyageNo', 'Voyage No'],
        ['BL', 'Bill of Lading'],
        ['POIS', 'Place of Issue'],
        ['DOIS', 'Date of Issue'],
        ['SOB', 'Shipped on Board'],
        ['POL_ID', 'Port of Loading'],
        ['POD_ID', 'Port of Discharge'],
        ['Rotation', 'Rotation #'],
        ['Destination', 'Destination'],
        ['Ownership', 'Ownership'],
    ];

    let firstError = null;
    required.forEach(([key, label]) => {
        const errEl = document.getElementById(key.toLowerCase().replace('_', '-') + '-error') ??
                      document.getElementById(key.toLowerCase() + '-error');
        if (errEl) errEl.classList.remove('visible');
        if (!fields[key]) {
            if (errEl) {
                errEl.textContent = `${label} is required.`;
                errEl.classList.add('visible');
            }
            if (!firstError) firstError = label;
        }
    });

    if (firstError) {
        errorEl.textContent = `Please fill in all required fields.`;
        errorEl.classList.add('visible');
        return;
    }

    btn.textContent = 'Registering...';
    btn.disabled    = true;

    fetch('{{ route("consignments.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(fields),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            successEl.textContent = `Consignment registered. Receipt: ${data.ReceiptNo}`;
            successEl.classList.add('visible');
            // Reset form after 2 seconds
            setTimeout(() => {
                document.getElementById('consignment-form').reset();
                renderContainerTable([]);
                updateSummary([]);
                successEl.classList.remove('visible');
            }, 3000);
        } else {
            errorEl.textContent = data.message ?? 'Failed to register consignment.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => {
        errorEl.textContent = 'Something went wrong. Please try again.';
        errorEl.classList.add('visible');
    })
    .finally(() => {
        btn.textContent = 'Register Consignment';
        btn.disabled    = false;
    });
}

// ── Quick Add Modals ──
function openQuickAdd(type) {
    document.getElementById('modal-' + type).style.display = 'flex';
}

function closeQuickAdd(type) {
    document.getElementById('modal-' + type).style.display = 'none';
    // Clear inputs
    document.querySelectorAll('#modal-' + type + ' input').forEach(el => el.value = '');
    document.querySelectorAll('#modal-' + type + ' .form-error').forEach(el => el.classList.remove('visible'));
}

function saveQuickAdd(type) {
    const routes = {
        carrier:   '{{ route("master-data.carriers.store") }}',
        shipper:   '{{ route("master-data.shippers.store") }}',
        pol:       '{{ route("master-data.ports.pol.store") }}',
        pod:       '{{ route("master-data.ports.pod.store") }}',
        consignee: '{{ route("master-data.consignees.store") }}',
    };

    const payloads = {
        carrier:   { CarrierName: document.getElementById('qa-carrier-name').value.trim() },
        shipper:   { ShipperName: document.getElementById('qa-shipper-name').value.trim(), AddressLine1: document.getElementById('qa-shipper-address').value.trim(), AddressLine2: '', AddressLine3: '', AddressLine4: '' },
        pol:       { POL_Name: document.getElementById('qa-pol-name').value.trim() },
        pod:       { POD_Name: document.getElementById('qa-pod-name').value.trim() },
        consignee: { FullName: document.getElementById('qa-consignee-name').value.trim(), TelNo: document.getElementById('qa-consignee-phone').value.trim(), Address1: document.getElementById('qa-consignee-address').value.trim(), Address2: '', Address3: '' },
    };

    const errorIds = {
        carrier:   'qa-carrier-error',
        shipper:   'qa-shipper-error',
        pol:       'qa-pol-error',
        pod:       'qa-pod-error',
        consignee: 'qa-consignee-error',
    };

    const btn     = document.getElementById('qa-' + type + '-btn');
    const errorEl = document.getElementById(errorIds[type]);

    errorEl.classList.remove('visible');

    // Basic validation
    const payload = payloads[type];
    const firstVal = Object.values(payload)[0];
    if (!firstVal) {
        errorEl.textContent = 'This field is required.';
        errorEl.classList.add('visible');
        return;
    }

    btn.textContent = 'Adding...';
    btn.disabled    = true;

    fetch(routes[type], {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Add new option to the corresponding dropdown
            const selectors = {
                carrier:   'carrier-id',
                shipper:   'shipper-id',
                pol:       'pol-id',
                pod:       'pod-id',
                consignee: 'consignee-id',
            };
            const labels = {
                carrier:   data.CarrierName,
                shipper:   data.ShipperName,
                pol:       data.POL_Name,
                pod:       data.POD_Name,
                consignee: data.FullName,
            };
            const values = {
                carrier:   data.CarrierID,
                shipper:   data.ShipperID,
                pol:       data.POL_ID,
                pod:       data.POD_ID,
                consignee: data.ConsigneeID,
            };

            const select = document.getElementById(selectors[type]);
            const option = document.createElement('option');
            option.value       = values[type];
            option.textContent = labels[type];
            option.selected    = true;
            select.appendChild(option);

            closeQuickAdd(type);
        } else {
            errorEl.textContent = data.message ?? 'Failed to add.';
            errorEl.classList.add('visible');
        }
    })
    .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
    .finally(() => {
        const labelMap = { carrier: 'Add Carrier', shipper: 'Add Shipper', pol: 'Add POL', pod: 'Add POD', consignee: 'Add Consignee' };
        btn.textContent = labelMap[type];
        btn.disabled    = false;
    });
}

// Close modals on backdrop click
['carrier', 'shipper', 'pol', 'pod', 'consignee'].forEach(type => {
    document.getElementById('modal-' + type).addEventListener('click', function(e) {
        if (e.target === this) closeQuickAdd(type);
    });
});

// ── OCR — Extract from BL document ──
function extractFromBL(input) {
    const file = input.files[0];
    if (!file) return;

    const statusEl      = document.getElementById('ocr-status');
    const statusTextEl  = document.getElementById('ocr-status-text');
    const btn           = document.getElementById('ocr-btn');

    statusEl.style.display   = 'flex';
    statusTextEl.textContent = 'Extracting fields with AI...';
    btn.disabled             = true;

    // CHANGED: send to our server route — not directly to Anthropic API
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', CSRF);

    fetch('{{ route("consignments.ocr") }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            statusTextEl.textContent = '⚠ ' + (data.message ?? 'Extraction failed.');
            setTimeout(() => { statusEl.style.display = 'none'; }, 4000);
            return;
        }

        const fields = data.fields;

        if (fields.BL)            { document.getElementById('bl').value = fields.BL.toUpperCase(); document.getElementById('summary-bl').textContent = fields.BL.toUpperCase(); }
        if (fields.VesselName)    document.getElementById('vessel-name').value = fields.VesselName;
        if (fields.VoyageNo)      document.getElementById('voyage-no').value = fields.VoyageNo;
        if (fields.POIS)          document.getElementById('pois').value = fields.POIS;
        if (fields.DOIS)          document.getElementById('dois').value = fields.DOIS;
        if (fields.SOB)           document.getElementById('sob').value = fields.SOB;
        if (fields.Destination)   document.getElementById('destination').value = fields.Destination;
        if (fields.ContainerNo)   document.getElementById('container-no').value = fields.ContainerNo.split(',')[0].trim().toUpperCase();
        if (fields.SealNo)        document.getElementById('seal-no').value = fields.SealNo.split(',')[0].trim().toUpperCase();
        if (fields.ContainerSize) document.getElementById('container-size').value = fields.ContainerSize;
        if (fields.Weight)        document.getElementById('container-weight').value = fields.Weight;

        if (fields.POL) matchDropdown('pol-id', fields.POL);
        if (fields.POD) matchDropdown('pod-id', fields.POD);

        statusTextEl.textContent = '✓ Fields extracted successfully';
        setTimeout(() => { statusEl.style.display = 'none'; }, 3000);
    })
    .catch(() => {
        statusTextEl.textContent = '⚠ Extraction failed. Fill manually.';
        setTimeout(() => { statusEl.style.display = 'none'; }, 4000);
    })
    .finally(() => { btn.disabled = false; });
}

// Try to match extracted text to a dropdown option
function matchDropdown(selectId, text) {
    const select = document.getElementById(selectId);
    const lower  = text.toLowerCase();
    for (let opt of select.options) {
        if (opt.textContent.toLowerCase().includes(lower) || lower.includes(opt.textContent.toLowerCase())) {
            opt.selected = true;
            break;
        }
    }
}
</script>
@endpush