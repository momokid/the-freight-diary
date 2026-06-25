@extends('layouts.app')

@section('title', 'Disbursement Analysis')
@section('page-title', 'Disbursement Analysis')

@section('content')

    {{-- ── Hidden state passed from controller ── --}}
    <input type="hidden" id="working-bl" value="{{ $workingBL ?? '' }}">
    <input type="hidden" id="working-hbl" value="{{ $workingHBL ?? '' }}">
    <input type="hidden" id="working-type" value="{{ $workingType ?? '' }}">

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ══════════════════════════════════════════════════════ ROW 1 ══ --}}
        <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 1.25rem;">

            {{-- ── Search Panel ── --}}
            <div class="card">
                <p class="form-title">Disbursement Search</p>

                {{-- FCL / LCL Toggle --}}
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; margin-top: 0.75rem;">
                    <button id="btn-fcl" onclick="setType('FCL')" class="btn-primary"
                        style="flex: 1; font-size: 0.8rem; padding: 6px 0;">
                        FCL
                    </button>
                    <button id="btn-lcl" onclick="setType('LCL')"
                        style="flex: 1; font-size: 0.8rem; padding: 6px 0; border-radius: 6px;
                           border: 1px solid var(--border-color); background: var(--card-bg);
                           color: var(--text-primary); cursor: pointer;">
                        LCL
                    </button>
                </div>

                {{-- BL Search --}}
                <div class="form-group" style="margin-bottom: 0; position: relative;">
                    <label class="form-label">Search by Main BL#</label>
                    <input type="text" id="bl-input" class="form-input" placeholder="Type Main BL..."
                        style="text-transform: uppercase;" autocomplete="off">
                    <div id="bl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                           background: var(--card-bg); border: 1px solid var(--border-color);
                           border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                           width: 100%; top: calc(100% + 4px); max-height: 220px; overflow-y: auto;">
                    </div>
                    <input type="hidden" id="bl-value">
                    <p id="bl-error" class="form-error"></p>
                </div>

                {{-- Temp Session Banner --}}
                <div id="temp-banner"
                    style="display: none; margin-top: 1rem; padding: 0.75rem;
                border-radius: 8px; background: rgba(234,179,8,0.1);
                border: 1px solid rgba(234,179,8,0.3);">
                    <p style="font-size: 0.8rem; color: #b45309; font-weight: 600; margin-bottom: 0.5rem;">
                        ⚠️ Unsaved disbursement for <span id="banner-bl" style="font-family: monospace;"></span>
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="clearAndLoad()"
                            style="flex: 1; font-size: 0.75rem; padding: 5px 0; border-radius: 5px;
                               background: #dc2626; color: white; border: none; cursor: pointer;">
                            Clear &amp; Load New
                        </button>
                        <button onclick="continueWorking()"
                            style="flex: 1; font-size: 0.75rem; padding: 5px 0; border-radius: 5px;
                               border: 1px solid var(--border-color); background: var(--card-bg);
                               color: var(--text-primary); cursor: pointer;">
                            Continue Working
                        </button>
                    </div>
                </div>

                {{-- Reopen Banner --}}
                <div id="reopen-banner"
                    style="display: none; margin-top: 1rem; padding: 0.75rem;
                border-radius: 8px; background: rgba(59,130,246,0.08);
                border: 1px solid rgba(59,130,246,0.25);">
                    <p style="font-size: 0.8rem; color: #1d4ed8; font-weight: 600; margin-bottom: 0.5rem;">
                        🔄 Pending disbursement for <span id="reopen-bl" style="font-family: monospace;"></span>
                    </p>
                    <button onclick="reopenDisbursement()"
                        style="width: 100%; font-size: 0.75rem; padding: 5px 0; border-radius: 5px;
                           background: #2563eb; color: white; border: none; cursor: pointer;">
                        Reopen for Editing
                    </button>
                </div>

                {{-- Active Session Info --}}
                <div id="session-info"
                    style="display: none; margin-top: 1rem; padding: 0.75rem;
                border-radius: 8px; background: rgba(22,163,74,0.08);
                border: 1px solid rgba(22,163,74,0.25);">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px;">Working on</p>
                    <p id="session-bl-label"
                        style="font-size: 0.85rem; font-weight: 700;
                    color: var(--text-primary); font-family: monospace;">
                    </p>
                    <button onclick="confirmClearSession()"
                        style="margin-top: 0.5rem; width: 100%; font-size: 0.75rem; padding: 4px 0;
                           border-radius: 5px; border: 1px solid #dc2626; background: transparent;
                           color: #dc2626; cursor: pointer;">
                        Clear Session
                    </button>
                </div>
            </div>

            {{-- ── Reference Panel ── --}}
            <div class="card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <p class="form-title" id="reference-panel-title">Consignment Details</p>
                    <span id="consignment-type-badge"
                        style="display: none; font-size: 0.7rem;
                    font-weight: 700; padding: 3px 10px; border-radius: 20px;
                    background: rgba(22,163,74,0.12); color: #16a34a; border: 1px solid rgba(22,163,74,0.3);">
                    </span>
                </div>

                {{-- Consignment meta --}}
                <div id="consignment-meta"
                    style="display: none; margin-bottom: 0.75rem;
                padding: 0.6rem 0.75rem; border-radius: 6px; background: var(--content-bg);
                display: none; gap: 1.5rem; flex-wrap: wrap;">
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">Consignee</p>
                        <p id="meta-consignee" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);">
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">Vessel</p>
                        <p id="meta-vessel" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);"></p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">ETA</p>
                        <p id="meta-eta" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);"></p>
                    </div>
                </div>

                {{-- FCL Containers table --}}
                <div id="containers-panel" style="display: none;">
                    <table class="data-table" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>CONTAINER #</th>
                                <th>SIZE</th>
                                <th style="text-align: right;">HANDL. COST</th>
                            </tr>
                        </thead>
                        <tbody id="containers-tbody">
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                    Search and load a Main BL to see containers.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- LCL HBL list --}}
                <div id="hbl-panel" style="display: none;">
                    <table class="data-table" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>HOUSE BL</th>
                                <th>CONSIGNEE</th>
                                <th>WEIGHT</th>
                                <th style="text-align: center;">STATUS</th>
                                <th style="text-align: center;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="hbl-tbody">
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                    Search and load a Main BL to see House BLs.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Placeholder when nothing loaded --}}
                <div id="reference-placeholder">
                    <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2rem 0;">
                        Search a Main BL to load consignment details.
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ ROW 2 ══ --}}
        <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 1.25rem;">

            {{-- ── Disbursement Account Details ── --}}
            <div class="card">
                <p class="form-title">Disbursement Account Details</p>
                <p id="accounts-hbl-label" style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                </p>

                <table class="data-table" style="font-size: 0.82rem;">
                    <thead>
                        <tr>
                            <th>ACCOUNT NAME</th>
                            <th style="width: 160px;">AMOUNT (GH₵)</th>
                            <th style="width: 60px; text-align: center;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="accounts-tbody">
                        <tr id="accounts-empty-row">
                            <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                Load a BL to see disbursement accounts.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 0.5rem 0;">
                                {{-- Add extra account row --}}
                                <div id="add-account-row"
                                    style="display: none; display: flex; align-items: center; gap: 0.5rem;">
                                    <select id="extra-account-select" class="form-input"
                                        style="font-size: 0.8rem; flex: 1;">
                                        <option value="">Add extra account...</option>
                                    </select>
                                    <button onclick="addExtraAccount()"
                                        style="padding: 6px 12px; border-radius: 6px; background: #2563eb;
                                           color: white; border: none; cursor: pointer; font-size: 0.8rem;">
                                        ➕ Add
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="disbursement-total-row" style="display: none;">
                            <td
                                style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary); padding-top: 0.5rem;">
                                HBL TOTAL
                            </td>
                            <td style="font-weight: 700; font-size: 0.9rem; color: #dc2626; padding-top: 0.5rem;"
                                id="disbursement-total">
                                GH₵ 0.00
                            </td>
                            <td></td>
                        </tr>
                        <tr id="bl-total-row" style="display: none;">
                            <td
                                style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary); padding-top: 0.25rem;">
                                BL TOTAL
                            </td>
                            <td style="font-weight: 700; font-size: 0.95rem; color: #185FA5; padding-top: 0.25rem;"
                                id="bl-total">
                                GH₵ 0.00
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- ── Transaction Summary ── --}}
            <div class="card">
                <p class="form-title">Transaction Summary</p>

                {{-- Payment Account --}}
                <div class="form-group">
                    <label class="form-label">Payment Account <span style="color: #ef4444;">*</span></label>
                    <select id="payment-account" class="form-input">
                        <option value="">Select account...</option>
                        @foreach ($cashAccounts as $acc)
                            <option value="{{ $acc->AccountNo }}">{{ $acc->AccountName }}</option>
                        @endforeach
                    </select>
                    <p id="payment-account-error" class="form-error"></p>
                </div>

                {{-- Budgeted Expenses (optional) --}}
                <div class="form-group">
                    <label class="form-label">
                        Budgeted Expenses
                        <span style="color: var(--text-muted); font-weight: 400;">(optional)</span>
                    </label>
                    <input type="number" id="budgeted-expenses" class="form-input" placeholder="0.00" min="0"
                        step="0.01" value="0">
                </div>

                {{-- Variance --}}
                <div id="variance-row"
                    style="display: none; margin-bottom: 0.75rem; padding: 0.6rem 0.75rem;
                border-radius: 6px; background: var(--content-bg);">
                    <p style="font-size: 0.72rem; color: var(--text-muted);">Variance (Budget − Actual)</p>
                    <p id="variance-value" style="font-size: 0.9rem; font-weight: 700;"></p>
                </div>

                {{-- Date of Transaction --}}
                <div class="form-group">
                    <label class="form-label">Date of Transaction <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="payment-date" class="form-input" max="{{ now()->toDateString() }}"
                        value="{{ now()->toDateString() }}">
                    <p id="payment-date-error" class="form-error"></p>
                </div>

                {{-- Save Button --}}
                <button onclick="saveTransaction()" id="save-btn" class="btn-primary"
                    style="width: 100%; margin-top: 0.5rem;">
                    Save Transaction
                </button>

                <p id="save-error" class="form-error" style="margin-top: 0.5rem; text-align: center;"></p>
                <p id="save-success" class="form-success" style="margin-top: 0.5rem; text-align: center;"></p>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const ROUTES = {
            search: '{{ route('disbursement.analysis.search') }}',
            loadBL: '{{ route('disbursement.analysis.load') }}',
            loadHBL: '{{ route('disbursement.analysis.hbl.load') }}',
            clearTemp: '{{ route('disbursement.analysis.temp.clear') }}',
            saveTempRow: '{{ route('disbursement.analysis.temp.save') }}',
            addTempRow: '{{ route('disbursement.analysis.temp.add') }}',
            deleteTempRow: (accountNo) => `{{ url('disbursement/analysis/temp') }}/${accountNo}`,
            save: '{{ route('disbursement.analysis.save') }}',
            reopen: '{{ route('disbursement.analysis.reopen') }}',
            hblList: '{{ route('disbursement.analysis.hbl.list') }}',
        };

        // ── State ─────────────────────────────────────────────────────────────────────
        let state = {
            type: document.getElementById('working-type').value || 'FCL',
            bl: document.getElementById('working-bl').value || '',
            hbl: document.getElementById('working-hbl').value || '',
            pendingBL: '', // BL the user tried to load while having a temp session
            pendingHBL: '', // HBL the user tried to load (LCL)
            reopenBL: '',
            reopenHBL: '',
            reopenType: '',
            savedExpenditure: 0, // total already saved in disbursement_analysis for current BL
            blTotal: 0,
            allTempRows: [],
        };

        // ── Init ──────────────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {

            // ── Expose functions called from inline onclick attributes ────────────────────
            window.setType = setType;
            window.clearAndLoad = clearAndLoad;
            window.continueWorking = continueWorking;
            window.reopenDisbursement = reopenDisbursement;
            window.confirmClearSession = confirmClearSession;
            window.loadHBL = loadHBL;
            window.updateTempRow = updateTempRow;
            window.removeTempRow = removeTempRow;
            window.addExtraAccount = addExtraAccount;
            window.saveTransaction = saveTransaction;
            setType(state.type, true);

            // Auto-restore existing temp session
            @if (!empty($tempRows))
                restoreSession(
                    @json($workingBL),
                    @json($workingHBL),
                    @json($workingType),
                    @json($tempRows),
                    @json($consignment),
                    @json($containers),
                    @json($hblList)
                );
            @endif

            // Budgeted expenses → recalculate variance
            document.getElementById('budgeted-expenses').addEventListener('input', updateVariance);

            // SearchDropdown init
            setTimeout(initSearch, 0);
        });

        // ── Search Dropdown ───────────────────────────────────────────────────────────
        function initSearch() {
            window.blSearch = new SearchDropdown({
                inputId: 'bl-input',
                dropdownId: 'bl-dropdown',
                hiddenId: 'bl-value',
                url: ROUTES.search,
                labelKey: 'BL',
                subKey: 'ConsigneeName',
                valueKey: 'BL',
                minLength: 2,
                onSelect: (bl) => {
                    // Find the selected item's Ownership from the last results
                    triggerLoadBL(bl, state.type);
                },
            });
        }

        // ── Type Toggle ───────────────────────────────────────────────────────────────
        function setType(type, silent) {
            state.type = type;
            const btnFCL = document.getElementById('btn-fcl');
            const btnLCL = document.getElementById('btn-lcl');

            if (type === 'FCL') {
                btnFCL.classList.add('btn-primary');
                btnFCL.style.cssText = '';
                btnLCL.classList.remove('btn-primary');
                btnLCL.style.cssText =
                    'flex:1;font-size:0.8rem;padding:6px 0;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-primary);cursor:pointer;';
                showPanel('containers');
            } else {
                btnLCL.classList.add('btn-primary');
                btnLCL.style.cssText = '';
                btnFCL.classList.remove('btn-primary');
                btnFCL.style.cssText =
                    'flex:1;font-size:0.8rem;padding:6px 0;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-primary);cursor:pointer;';
                showPanel('hbl');
            }
        }

        // ── Load BL ───────────────────────────────────────────────────────────────────
        function triggerLoadBL(bl, type) {
            clearErrors();
            fetch(ROUTES.loadBL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: bl,
                        Type: type ?? state.type
                    }),
                })
                .then(r => {
                    if (!r.ok && r.status !== 409 && r.status !== 422 && r.status !== 404) {
                        return r.text().then(t => {
                            throw new Error(t);
                        });
                    }
                    return r.json();
                })
                .then(data => {
                    if (!data.success) {
                        if (data.code === 'HAS_TEMP') {
                            state.pendingBL = bl;
                            state.pendingHBL = '';
                            showTempBanner(data.existingBL);
                            return;
                        }
                        if (data.code === 'CAN_REOPEN') {
                            state.reopenBL = data.BL;
                            state.reopenHBL = data.HBL;
                            state.reopenType = type ?? state.type;
                            showReopenBanner(data.BL);
                            return;
                        }
                        showError('bl-error', data.message);
                        return;
                    }

                    // Success
                    state.bl = bl;
                    state.hbl = (type === 'FCL') ? bl : '';
                    state.savedExpenditure = data.savedExpenditure ?? 0;
                    setType(type ?? state.type, true);

                    renderConsignmentMeta(data.consignment, type ?? state.type);

                    if ((type ?? state.type) === 'FCL') {
                        renderContainers(data.containers);
                        renderAccountRows(data.tempRows);
                        setSessionInfo(bl, bl, 'FCL');
                    } else {
                        renderHBLList(data.hblList);
                        clearAccountRows();
                        setSessionInfo(bl, null, 'LCL');
                    }
                })
                .catch((err) => {
                    console.error('loadBL error:', err);
                    showError('bl-error', 'Connection error. Please try again.');
                });
        }

        // ── Load HBL (LCL) ────────────────────────────────────────────────────────────
        function loadHBL(hbl) {
            clearErrors();
            fetch(ROUTES.loadHBL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: state.bl,
                        HBL: hbl
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        if (data.code === 'HAS_TEMP') {
                            state.pendingBL = state.bl;
                            state.pendingHBL = hbl;
                            showTempBanner(data.existingHBL);
                            return;
                        }
                        if (data.code === 'CAN_REOPEN') {
                            state.reopenBL = data.BL;
                            state.reopenHBL = data.HBL;
                            state.reopenType = 'LCL';
                            showReopenBanner(data.HBL);
                            return;
                        }
                        alert(data.message);
                        return;
                    }

                    state.hbl = hbl;
                    state.savedExpenditure = data.savedExpenditure ?? 0;

                    state.allTempRows = data.tempRows;
                    const hblRows = data.tempRows.filter(r => r.HouseBL === hbl);
                    renderAccountRows(hblRows);
                    updateBLTotal(state.allTempRows);

                    setSessionInfo(state.bl, hbl, 'LCL');
                    document.getElementById('accounts-hbl-label').textContent =
                        `Entering expenses for HBL# ${hbl}`;
                })
                .catch(() => alert('Connection error. Please try again.'));
        }

        function updateBLTotal(allTempRows) {
            if (!allTempRows || state.type !== 'LCL') {
                document.getElementById('bl-total-row').style.display = 'none';
                return;
            }
            state.blTotal = allTempRows.reduce((sum, r) => sum + (parseFloat(r.Amount) || 0), 0);
            document.getElementById('bl-total').textContent = `GH₵ ${state.blTotal.toFixed(2)}`;
            document.getElementById('bl-total-row').style.display = '';
            updateVariance();
        }

        // ── Temp Banner Actions ───────────────────────────────────────────────────────
        function showTempBanner(existingBL) {
            document.getElementById('banner-bl').textContent = existingBL;
            document.getElementById('temp-banner').style.display = 'block';
            document.getElementById('reopen-banner').style.display = 'none';
        }

        function clearAndLoad() {
            fetch(ROUTES.clearTemp, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                })
                .then(() => {
                    hideBanners();
                    clearAccountRows();
                    if (state.pendingHBL) {
                        loadHBL(state.pendingHBL);
                    } else {
                        triggerLoadBL(state.pendingBL, state.type);
                    }
                });
        }

        function continueWorking() {
            hideBanners();
            // Reload the page to restore existing session cleanly
            location.reload();
        }

        // ── Reopen Banner ─────────────────────────────────────────────────────────────
        function showReopenBanner(label) {
            document.getElementById('reopen-bl').textContent = label;
            document.getElementById('reopen-banner').style.display = 'block';
            document.getElementById('temp-banner').style.display = 'none';
        }

        function reopenDisbursement() {
            fetch(ROUTES.reopen, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: state.reopenBL,
                        HBL: state.reopenHBL,
                        Type: state.reopenType
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                        return;
                    }
                    hideBanners();
                    state.bl = state.reopenBL;
                    state.hbl = state.reopenHBL;
                    state.type = state.reopenType;
                    renderAccountRows(data.tempRows);
                    setSessionInfo(state.bl, state.hbl, state.type);
                    document.getElementById('accounts-hbl-label').textContent =
                        `Reopened: ${state.hbl}`;
                })
                .catch(() => alert('Connection error.'));
        }

        // ── Clear Session ─────────────────────────────────────────────────────────────
        function confirmClearSession() {
            if (!confirm('Clear your current working session? Unsaved amounts will be lost.')) return;
            fetch(ROUTES.clearTemp, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                })
                .then(() => {
                    state.bl = '';
                    state.hbl = '';
                    clearAccountRows();
                    hideSessionInfo();
                    renderContainers([]);
                    renderHBLList([]);
                });
        }

        // ── Account Rows ──────────────────────────────────────────────────────────────
        function renderAccountRows(rows) {
            const tbody = document.getElementById('accounts-tbody');

            if (!rows || rows.length === 0) {
                clearAccountRows();
                return;
            }

            tbody.innerHTML = '';

            rows.forEach(row => {
                tbody.insertAdjacentHTML('beforeend', buildAccountRow(row));
            });

            document.getElementById('add-account-row').style.display = 'flex';
            document.getElementById('disbursement-total-row').style.display = '';

            updateTotal();
            loadExtraAccountDropdown(rows.map(r => r.AccountNo));
        }

        function buildAccountRow(row) {
            return `<tr id="row-${row.AccountNo}">
        <td style="font-weight: 500; color: var(--text-primary);">${row.AccountName}</td>
        <td>
            <input type="number" min="0" step="0.01"
                value="${parseFloat(row.Amount).toFixed(2)}"
                class="form-input"
                style="font-size: 0.82rem; padding: 5px 8px; width: 140px;"
                onchange="updateTempRow(${row.AccountNo}, this.value)"
                oninput="updateTotal()">
        </td>
        <td style="text-align: center;">
            <button onclick="removeTempRow(${row.AccountNo}, this)"
                style="background: none; border: none; cursor: pointer; color: #dc2626; font-size: 1rem;"
                title="Remove">⊖</button>
        </td>
    </tr>`;
        }

        function clearConsignmentContext() {
            // Reset consignment meta
            document.getElementById('meta-consignee').textContent = '';
            document.getElementById('meta-vessel').textContent = '';
            document.getElementById('meta-eta').textContent = '';
            document.getElementById('consignment-meta').style.display = 'none';
            document.getElementById('consignment-type-badge').style.display = 'none';
            document.getElementById('reference-placeholder').style.display = 'block';

            // Clear containers and HBL panels
            document.getElementById('containers-tbody').innerHTML = `<tr><td colspan="3"
        style="text-align:center;color:var(--text-muted);padding:1.5rem;">
        Search and load a Main BL to see containers.</td></tr>`;
            document.getElementById('hbl-tbody').innerHTML = `<tr><td colspan="5"
        style="text-align:center;color:var(--text-muted);padding:1.5rem;">
        Search and load a Main BL to see House BLs.</td></tr>`;

            // Reset search box
            document.getElementById('bl-input').value = '';
            document.getElementById('bl-value').value = '';
        }

        function clearAccountRows() {
            const tbody = document.getElementById('accounts-tbody');
            tbody.innerHTML = `<tr id="accounts-empty-row">
        <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">
            Load a BL to see disbursement accounts.
        </td>
    </tr>`;
            document.getElementById('add-account-row').style.display = 'none';
            document.getElementById('disbursement-total-row').style.display = 'none';
            document.getElementById('disbursement-total').textContent = 'GH₵ 0.00';
            document.getElementById('accounts-hbl-label').textContent = '';
        }

        function updateTempRow(accountNo, amount) {
            const parsed = parseFloat(amount) || 0;

            // Keep allTempRows in sync so BL total stays accurate
            const row = state.allTempRows.find(
                r => r.AccountNo === accountNo && r.HouseBL === state.hbl
            );
            if (row) row.Amount = parsed;
            updateBLTotal(state.allTempRows);

            fetch(ROUTES.saveTempRow, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    AccountNo: accountNo,
                    BL: state.bl,
                    HBL: state.hbl,
                    Amount: parsed,
                }),
            });
        }

        function removeTempRow(accountNo, btn) {
            btn.disabled = true;
            fetch(ROUTES.deleteTempRow(accountNo), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`row-${accountNo}`)?.remove();
                        updateTotal();
                    } else {
                        btn.disabled = false;
                    }
                });
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('#accounts-tbody input[type="number"]').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('disbursement-total').textContent = `GH₵ ${total.toFixed(2)}`;

            // For FCL, blTotal = screen total. For LCL, updateBLTotal owns variance.
            if (state.type === 'FCL') {
                state.blTotal = total;
                updateVariance();
            }
        }

        // ── Extra Account ─────────────────────────────────────────────────────────────
        function loadExtraAccountDropdown(excludeNos) {
            const select = document.getElementById('extra-account-select');
            select.innerHTML = '<option value="">Add extra account...</option>';

            fetch('{{ route('disbursement.analysis.search') }}?extra=1')
                .then(() => {
                    // Load all disbursement_accounts from a simple inline approach
                    // The controller returns all via addTempRow validation
                });

            // We pre-populate from server-rendered disbursement accounts
            @php
                $allDisbAccounts = \DB::table('disbursement_accounts as da')
                    ->join('ledger_account as la', 'da.AccountNo', '=', 'la.AccountNo')
                    ->orderBy('la.AccountName')
                    ->get(['da.AccountNo', 'la.AccountName']);
            @endphp

            const allAccounts = @json($allDisbAccounts);

            allAccounts.forEach(acc => {
                if (!excludeNos.includes(acc.AccountNo)) {
                    const opt = document.createElement('option');
                    opt.value = acc.AccountNo;
                    opt.textContent = acc.AccountName;
                    select.appendChild(opt);
                }
            });
        }

        function addExtraAccount() {
            const select = document.getElementById('extra-account-select');
            const accountNo = parseInt(select.value);
            if (!accountNo) return;

            fetch(ROUTES.addTempRow, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        AccountNo: accountNo
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                        return;
                    }

                    document.getElementById('accounts-tbody')
                        .insertAdjacentHTML('beforeend', buildAccountRow({
                            AccountNo: data.AccountNo,
                            AccountName: data.AccountName,
                            Amount: 0,
                        }));

                    // Remove from dropdown
                    select.querySelector(`option[value="${accountNo}"]`)?.remove();
                    select.value = '';
                    updateTotal();
                });
        }

        // ── Containers Panel ──────────────────────────────────────────────────────────
        function renderContainers(containers) {
            const tbody = document.getElementById('containers-tbody');
            showPanel('containers');

            if (!containers || containers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3"
            style="text-align:center;color:var(--text-muted);padding:1.5rem;">
            No containers found.
        </td></tr>`;
                return;
            }

            tbody.innerHTML = containers.map(c => `
        <tr>
            <td style="font-family: monospace; font-weight: 600;">${c.ContainerNo}</td>
            <td>${c.ContainerSize}</td>
            <td style="text-align: right;">GH₵ ${parseFloat(c.HandlingCost || 0).toFixed(2)}</td>
        </tr>
    `).join('');
        }

        // ── HBL Panel ─────────────────────────────────────────────────────────────────
        function renderHBLList(hblList) {
            const tbody = document.getElementById('hbl-tbody');
            showPanel('hbl');

            if (!hblList || hblList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5"
            style="text-align:center;color:var(--text-muted);padding:1.5rem;">
            No House BLs found under this consignment.
        </td></tr>`;
                return;
            }

            tbody.innerHTML = hblList.map(h => {
                const hasDisbursement = h.HasDisbursement > 0;
                const isApproved = h.DisbursementStatus == 0;
                const isPending = h.DisbursementStatus == 2;

                let statusBadge = `<span style="font-size:0.7rem;padding:2px 8px;border-radius:10px;
            background:rgba(var(--text-muted-rgb,100,116,139),0.1);color:var(--text-muted);">
            Empty</span>`;

                if (isApproved) {
                    statusBadge = `<span style="font-size:0.7rem;padding:2px 8px;border-radius:10px;
                background:rgba(22,163,74,0.12);color:#16a34a;font-weight:600;">
                ✓ Approved</span>`;
                } else if (isPending) {
                    statusBadge = `<span style="font-size:0.7rem;padding:2px 8px;border-radius:10px;
                background:rgba(234,179,8,0.12);color:#b45309;font-weight:600;">
                ⏳ Pending</span>`;
                }

                const actionBtn = isApproved ?
                    `<span style="color:var(--text-muted);font-size:0.75rem;">Locked</span>` :
                    `<button onclick="loadHBL('${h.HouseBL}')"
                style="padding:4px 10px;font-size:0.75rem;border-radius:5px;
                       background:var(--accent,#16a34a);color:white;border:none;cursor:pointer;">
                ${isPending ? '🔄 Reopen' : '📋 Load'}
               </button>`;

                return `<tr>
            <td style="font-family:monospace;font-weight:700;">${h.HouseBL}</td>
            <td style="font-size:0.78rem;">${h.ConsigneeName}</td>
            <td style="font-size:0.78rem;">${h.Weight ?? '—'}</td>
            <td style="text-align:center;">${statusBadge}</td>
            <td style="text-align:center;">${actionBtn}</td>
        </tr>`;
            }).join('');
        }

        // ── Consignment Meta ──────────────────────────────────────────────────────────
        function renderConsignmentMeta(c, type) {
            if (!c) return;
            document.getElementById('meta-consignee').textContent = c.ConsigneeName ?? '—';
            document.getElementById('meta-vessel').textContent = c.VesselName ?? '—';
            document.getElementById('meta-eta').textContent = c.ETA ?? '—';

            const meta = document.getElementById('consignment-meta');
            meta.style.display = 'flex';

            const badge = document.getElementById('consignment-type-badge');
            badge.textContent = type;
            badge.style.display = 'inline-block';

            document.getElementById('reference-placeholder').style.display = 'none';
        }

        // ── Session Info ──────────────────────────────────────────────────────────────
        function setSessionInfo(bl, hbl, type) {
            const label = hbl && hbl !== bl ? `${bl} / HBL: ${hbl}` : bl;
            document.getElementById('session-bl-label').textContent = `${type} — ${label}`;
            document.getElementById('session-info').style.display = 'block';
            hideBanners();
        }

        function hideSessionInfo() {
            document.getElementById('session-info').style.display = 'none';
        }

        // ── Variance ──────────────────────────────────────────────────────────────────
        function updateVariance() {
            const budgeted = parseFloat(document.getElementById('budgeted-expenses').value) || 0;
            const row = document.getElementById('variance-row');

            if (budgeted <= 0) {
                row.style.display = 'none';
                return;
            }

            const totalAllExpenses = state.savedExpenditure + state.blTotal;
            const variance = budgeted - totalAllExpenses;

            row.style.display = 'block';
            const el = document.getElementById('variance-value');
            el.textContent = `GH₵ ${variance.toFixed(2)}`;
            el.style.color = variance >= 0 ? '#16a34a' : '#dc2626';
        }

        function getTotalExpenditure() {
            let total = 0;
            document.querySelectorAll('#accounts-tbody input[type="number"]').forEach(i => {
                total += parseFloat(i.value) || 0;
            });
            return total;
        }

        // ── Save Transaction ──────────────────────────────────────────────────────────
        function saveTransaction() {
            clearErrors();
            const accountNo = document.getElementById('payment-account').value;
            const paymentDate = document.getElementById('payment-date').value;
            const budgeted = parseFloat(document.getElementById('budgeted-expenses').value) || 0;

            if (!accountNo) {
                showError('payment-account-error', 'Please select a payment account.');
                return;
            }
            if (!paymentDate) {
                showError('payment-date-error', 'Please select a date of transaction.');
                return;
            }
            if (!state.bl) {
                showError('save-error', 'No BL loaded. Please search and load a consignment first.');
                return;
            }

            const btn = document.getElementById('save-btn');
            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch(ROUTES.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        BL: state.bl,
                        Type: state.type,
                        AccountNo: accountNo,
                        PaymentDate: paymentDate,
                        BudgetedExpenses: budgeted,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showError('save-error', data.message ?? 'Save failed.');
                        return;
                    }

                    // Success
                    document.getElementById('save-success').textContent = data.message;
                    document.getElementById('save-success').classList.add('visible');

                    const savedType = state.type;
                    const savedBL = state.bl;
                    const savedHBL = state.hbl;

                    // 1. Clear accounts panel
                    clearAccountRows();

                    // 2. Clear session info
                    hideSessionInfo();

                    // 3. Clear search input
                    document.getElementById('bl-input').value = '';
                    document.getElementById('bl-value').value = '';

                    // 4. Clear consignment meta and reference panels
                    clearConsignmentContext();

                    // 5. Clear Transaction Summary inputs
                    document.getElementById('payment-account').value = '';
                    document.getElementById('budgeted-expenses').value = '0';
                    document.getElementById('payment-date').value = '{{ now()->toDateString() }}';
                    document.getElementById('variance-row').style.display = 'none';

                    // 6. Clear BL total and allTempRows
                    state.allTempRows = state.allTempRows.filter(r => r.HouseBL !== savedHBL);
                    state.blTotal = 0;
                    state.savedExpenditure = 0;
                    state.hbl = '';
                    updateBLTotal(state.allTempRows);

                    state.bl = '';
                    state.hbl = '';

                    setTimeout(() => {
                        document.getElementById('save-success').classList.remove('visible');
                        document.getElementById('save-success').textContent = '';
                    }, 4000);
                })
                .catch(() => showError('save-error', 'Connection error. Please try again.'))
                .finally(() => {
                    btn.textContent = 'Save Transaction';
                    btn.disabled = false;
                });
        }

        // ── Restore Session (from PHP) ────────────────────────────────────────────────
        function restoreSession(bl, hbl, type, tempRows, consignment, containers, hblList) {
            state.bl = bl;
            state.hbl = hbl;
            state.type = type;

            setType(type, true);
            renderConsignmentMeta(consignment, type);

            if (type === 'FCL') {
                renderContainers(containers);
            } else {
                renderHBLList(hblList);
            }

            state.allTempRows = tempRows;
            const visibleRows = type === 'LCL' ?
                tempRows.filter(r => r.HouseBL === hbl) :
                tempRows;
            renderAccountRows(visibleRows);
            updateBLTotal(state.allTempRows);

            setSessionInfo(bl, hbl, type);

            document.getElementById('accounts-hbl-label').textContent =
                type === 'LCL' ? `Entering expenses for HBL# ${hbl}` : `Entering expenses for BL# ${bl}`;
        }

        // ── Panel Switcher ────────────────────────────────────────────────────────────
        function showPanel(panel) {
            document.getElementById('containers-panel').style.display = panel === 'containers' ? 'block' : 'none';
            document.getElementById('hbl-panel').style.display = panel === 'hbl' ? 'block' : 'none';
        }

        // ── Banners ───────────────────────────────────────────────────────────────────
        function hideBanners() {
            document.getElementById('temp-banner').style.display = 'none';
            document.getElementById('reopen-banner').style.display = 'none';
        }

        // ── Errors ────────────────────────────────────────────────────────────────────
        function showError(id, msg) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = msg;
                el.classList.add('visible');
            }
        }

        function clearErrors() {
            document.querySelectorAll('.form-error').forEach(el => {
                el.textContent = '';
                el.classList.remove('visible');
            });
        }
    </script>
@endpush
