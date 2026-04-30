{{-- ══════════════════════════════════════════════════════════════════════════
     HS CODE ADVISOR MODAL
     A right-side drawer that loads a consignment by BL, runs HS predictions
     for all containers/HBLs, shows duty simulation and saves accepted codes.

     Include anywhere with: @include('partials.hs-advisor-modal')
     Trigger with: window.HSAdvisor.open()
     Or with a BL: window.HSAdvisor.open('MSKU2024001')
════════════════════════════════════════════════════════════════════════════ --}}

{{-- ── Backdrop ── --}}
<div id="hsa-backdrop" onclick="window.HSAdvisor.close()"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
           z-index:1000;"></div>

{{-- ── Drawer ── --}}
<div id="hsa-drawer"
    style="display:none; position:fixed; top:0; right:0; bottom:0; width:780px;
           max-width:95vw; background:#fff; z-index:1001; overflow-y:auto;
           box-shadow:-4px 0 24px rgba(0,0,0,0.15);
           transition:transform 0.3s ease; transform:translateX(100%);">

    {{-- ── Drawer header ── --}}
    <div
        style="position:sticky; top:0; z-index:10; background:#185FA5;
                padding:14px 20px; display:flex; justify-content:space-between;
                align-items:center;">
        <div>
            <p style="font-size:14px; font-weight:700; color:#fff;">
                HS Code Advisor
            </p>
            <p style="font-size:11px; color:#bfdbfe; margin-top:2px;">
                Predict, compare and optimise customs classification
            </p>
        </div>
        <button onclick="window.HSAdvisor.close()"
            style="background:rgba(255,255,255,0.2); border:none; color:#fff;
                   border-radius:6px; padding:6px 12px; cursor:pointer;
                   font-size:12px; font-weight:600;">
            ✕ Close
        </button>
    </div>

    {{-- ── Drawer body ── --}}
    <div style="padding:20px;">

        {{-- ── Step 1: BL Search ── --}}
        <div style="margin-bottom:16px;">
            <label
                style="font-size:11px; font-weight:700; color:#374151;
                          text-transform:uppercase; letter-spacing:0.05em;
                          display:block; margin-bottom:6px;">
                Bill of Lading Number
            </label>
            <div style="display:flex; gap:8px;">
                <input type="text" id="hsa-bl-input" placeholder="e.g. MSKU2024001" maxlength="50"
                    style="flex:1; padding:8px 12px; border:1px solid #e5e7eb;
                           border-radius:8px; font-size:13px; font-family:monospace;
                           text-transform:uppercase; color:#111827;"
                    onkeydown="if(event.key==='Enter') window.HSAdvisor.load()">
                <button onclick="window.HSAdvisor.load()" id="hsa-load-btn"
                    style="padding:8px 20px; background:#185FA5; color:#fff;
                           border:none; border-radius:8px; font-size:13px;
                           font-weight:600; cursor:pointer; white-space:nowrap;">
                    Load Consignment
                </button>
            </div>
            <p id="hsa-load-error" style="display:none; font-size:12px; color:#b91c1c; margin-top:6px;"></p>
        </div>

        {{-- ── Loading state ── --}}
        <div id="hsa-loading"
            style="display:none; text-align:center; padding:2rem; color:#6b7280;
                   font-size:13px;">
            Loading consignment...
        </div>

        {{-- ── Consignment panel (shown after load) ── --}}
        <div id="hsa-consignment-panel" style="display:none;">

            {{-- Consignment info strip ── --}}
            <div id="hsa-info-strip"
                style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px;
                       padding:12px 16px; margin-bottom:16px;
                       display:grid; grid-template-columns:repeat(4,1fr);
                       gap:10px; row-gap:10px;">
            </div>

            {{-- CIF input + Run button ── --}}
            <div
                style="display:flex; gap:10px; align-items:flex-end; margin-bottom:16px;
                        padding:14px; background:#eff6ff; border:1px solid #bfdbfe;
                        border-radius:8px;">
                <div style="flex:1;">
                    <label
                        style="font-size:11px; font-weight:700; color:#185FA5;
                                  display:block; margin-bottom:4px;">
                        CIF Value (GH₵) — optional, used for duty calculations
                    </label>
                    <input type="number" id="hsa-cif-input" placeholder="0.00" min="0" step="0.01"
                        style="width:100%; padding:8px 12px; border:1px solid #bfdbfe;
                               border-radius:6px; font-size:13px; color:#111827;">
                </div>
                <button onclick="window.HSAdvisor.runSimulation()" id="hsa-run-btn"
                    style="padding:8px 24px; background:#15803d; color:#fff;
                           border:none; border-radius:8px; font-size:13px;
                           font-weight:700; cursor:pointer; white-space:nowrap;
                           display:flex; align-items:center; gap:8px;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Run Simulation
                </button>
            </div>

            {{-- Simulation loading ── --}}
            <div id="hsa-sim-loading" style="display:none; text-align:center; padding:2rem; color:#6b7280;">
                <p style="font-size:13px; font-weight:600;">
                    Analysing items and predicting HS codes...
                </p>
                <p style="font-size:11px; color:#9ca3af; margin-top:4px;">
                    This may take a few seconds for AI-powered predictions
                </p>
                <div
                    style="margin:12px auto; width:200px; height:4px;
                            background:#e5e7eb; border-radius:99px; overflow:hidden;">
                    <div id="hsa-progress-fill"
                        style="height:4px; background:#185FA5; border-radius:99px;
                               width:0%; transition:width 0.3s;">
                    </div>
                </div>
                <p id="hsa-progress-text" style="font-size:11px; color:#6b7280; margin-top:4px;"></p>
            </div>

            {{-- Item results ── --}}
            <div id="hsa-items-panel" style="display:none;">

                <div id="hsa-items-list"></div>

                {{-- Duty Simulation Summary ── --}}
                <div
                    style="margin-top:16px; border:2px solid #185FA5;
                            border-radius:10px; overflow:hidden;">
                    <div style="background:#185FA5; padding:10px 16px;">
                        <p
                            style="font-size:12px; font-weight:700; color:#fff;
                                  text-transform:uppercase; letter-spacing:0.05em;">
                            Duty Simulation Summary
                        </p>
                    </div>
                    <div id="hsa-summary-body" style="padding:16px;"></div>
                </div>

                {{-- Action buttons ── --}}
                <div style="display:flex; gap:10px; margin-top:14px; justify-content:flex-end;">
                    <button onclick="window.HSAdvisor.printReport()"
                        style="padding:8px 20px; background:#374151; color:#fff;
                               border:none; border-radius:8px; font-size:12px;
                               font-weight:600; cursor:pointer; display:flex;
                               align-items:center; gap:6px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm0-12V5a2 2 0 012-2h2a2 2 0 012 2v4H9z" />
                        </svg>
                        Print Report
                    </button>
                    <button onclick="window.HSAdvisor.acceptAll()" id="hsa-accept-all-btn"
                        style="padding:8px 24px; background:#15803d; color:#fff;
                               border:none; border-radius:8px; font-size:12px;
                               font-weight:700; cursor:pointer;">
                        Accept All Recommended Codes
                    </button>
                </div>

            </div>{{-- end hsa-items-panel --}}

        </div>{{-- end hsa-consignment-panel --}}


    </div>{{-- end drawer body --}}
</div>{{-- end drawer --}}

{{-- ══════════════════════════════════════════════════════════════════════════
     HS ADVISOR JAVASCRIPT
════════════════════════════════════════════════════════════════════════════ --}}
<script>
    window.HSAdvisor = (function() {

        const LOAD_URL = '{{ route('hs-code.load-consignment') }}';
        const PREDICT_URL = '{{ route('hs-code.predict') }}';
        const ACCEPT_URL = '{{ route('hs-code.accept') }}';
        const ACCEPT_ALL_URL = '{{ route('hs-code.accept-all') }}';
        const PRINT_URL = '{{ route('hs-code.print-report') }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // State
        let _consignment = null;
        let _items = []; // { id, type, description, itemType, houseBl, candidates, accepted }
        let _type = 'FCL';
        let _cifValue = 0;

        // ── Open / Close ─────────────────────────────────────────────────────────
        function open(bl) {
            document.getElementById('hsa-backdrop').style.display = 'block';
            const drawer = document.getElementById('hsa-drawer');
            drawer.style.display = 'block';
            drawer.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';

            if (bl) {
                document.getElementById('hsa-bl-input').value = bl.toUpperCase();
                load();
            }
        }

        function close() {
            const drawer = document.getElementById('hsa-drawer');
            drawer.style.transform = 'translateX(100%)';
            setTimeout(() => {
                drawer.style.display = 'none';
                document.getElementById('hsa-backdrop').style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // ── Load consignment ─────────────────────────────────────────────────────
        function load() {
            const bl = document.getElementById('hsa-bl-input').value.trim().toUpperCase();
            if (!bl) {
                showLoadError('Please enter a BL number.');
                return;
            }
            showLoadError('');
            resetSimulation();
            setLoading('load', true);
            document.getElementById('hsa-consignment-panel').style.display = 'none';

            fetch(LOAD_URL + '?' + new URLSearchParams({
                    bl
                }), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    setLoading('load', false);
                    if (!data.success) {
                        showLoadError(data.message || 'Consignment not found.');
                        return;
                    }
                    _consignment = data.consignment;
                    _type = data.type;
                    renderInfoStrip(data);
                    document.getElementById('hsa-consignment-panel').style.display = 'block';
                })
                .catch(err => {
                    setLoading('load', false);
                    showLoadError('Network error: ' + err.message);
                });
        }

        // ── Render consignment info strip ────────────────────────────────────────
        function renderInfoStrip(data) {
            const c = data.consignment;
            const status = {
                0: 'Cleared',
                1: 'Not Arrived',
                2: 'Pending',
                3: 'Gated Out'
            };
            const strip = document.getElementById('hsa-info-strip');

            if (!strip) return;
            strip.style.display = 'grid'

            strip.innerHTML = [{
                        label: 'BL Number',
                        val: c.MainBL,
                        mono: true
                    },
                    {
                        label: 'Consignee',
                        val: c.ConsigneeName ?? '—',
                        mono: false
                    },
                    {
                        label: 'Carrier',
                        val: c.CarrierName ?? '—',
                        mono: false
                    },
                    {
                        label: 'ETA',
                        val: fmtDate(c.ETA),
                        mono: false
                    },
                    {
                        label: 'Type',
                        val: data.type,
                        mono: false
                    },
                    {
                        label: 'Status',
                        val: status[c.Status] ?? '—',
                        mono: false
                    },
                    {
                        label: 'Items',
                        val: data.totalItems + ' item(s)',
                        mono: false
                    },
                ].map(i => `
            <div>
                <p style="font-size:9px; text-transform:uppercase; letter-spacing:0.05em;
                           color:#6b7280; margin-bottom:2px;">${i.label}</p>
                <p style="font-size:12px; font-weight:700; color:#111827;
                           ${i.mono ? 'font-family:monospace;' : ''}">${escHtml(String(i.val ?? '—'))}</p>
            </div>`).join('') +
                // Item descriptions span full width below
                `<div style="grid-column:1/-1; border-top:1px solid #e5e7eb; padding-top:8px; margin-top:2px;">
            <p style="font-size:9px; text-transform:uppercase; letter-spacing:0.05em;
                       color:#6b7280; margin-bottom:4px;">Item Descriptions</p>
            <p style="font-size:11px; color:#374151; font-family:monospace; line-height:1.7;">
                ${escHtml(data.itemSummary || '—')}
            </p>
        </div>`;
        }

        // ── Run simulation ───────────────────────────────────────────────────────
        async function runSimulation() {
            if (!_consignment) return;

            _cifValue = parseFloat(document.getElementById('hsa-cif-input').value || 0);

            // Build items list from loaded consignment data
            let loadRes;
            try {
                const response = await fetch(
                    LOAD_URL + '?' + new URLSearchParams({
                        bl: _consignment.MainBL
                    }), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );
                loadRes = await response.json();
            } catch (err) {
                showLoadError('Failed to load consignment data: ' + err.message);
                return;
            }

            if (!loadRes || !loadRes.success) {
                showLoadError(loadRes?.message || 'Failed to reload consignment.');
                return;
            }

            const containers = loadRes.containers || [];
            const hblEntries = loadRes.hblEntries || [];

            const rawItems = _type === 'LCL' ?
                hblEntries.map(h => ({
                    container_no: null,
                    type: 'LCL',
                    description: h.Description,
                    itemType: h.ItemType,
                    houseBl: h.HouseBL,
                    label: 'HBL ' + h.HouseBL + ' — ' + (h.ConsigneeName ?? ''),
                    existingHS: h.HSCode,
                })) :
                containers.map(c => ({
                    container_no: c.ContainerNo,
                    type: 'FCL',
                    description: c.ItemDetails,
                    itemType: null,
                    houseBl: null,
                    label: 'Container ' + c.ContainerNo + ' (' + (c.ContainerSize ?? '') + ')',
                    existingHS: c.HSCode,
                }));

            if (!rawItems.length) {
                showLoadError('No items with descriptions found for this consignment.');
                return;
            }

            _items = rawItems.map(i => ({
                ...i,
                candidates: [],
                accepted: null
            }));

            document.getElementById('hsa-sim-loading').style.display = 'block';
            document.getElementById('hsa-items-panel').style.display = 'none';
            document.getElementById('hsa-run-btn').disabled = true;

            // Predict each item sequentially with progress bar
            for (let i = 0; i < _items.length; i++) {
                const item = _items[i];
                const pct = Math.round(((i + 1) / _items.length) * 100);
                document.getElementById('hsa-progress-fill').style.width = pct + '%';
                document.getElementById('hsa-progress-text').textContent =
                    `Analysing item ${i + 1} of ${_items.length}: ${item.description.substring(0, 50)}...`;

                try {
                    const body = {
                        description: item.description,
                        item_type: item.itemType,
                        source_type: item.type,
                        consignment_id: _consignment.ConsignmentID,
                        bl: _consignment.MainBL,
                        house_bl: item.houseBl,
                        cif_value: _cifValue || null,
                    };

                    const res = await fetch(PREDICT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(body),
                    }).then(r => r.json());

                    if (res.success) {
                        _items[i].candidates = res.candidates;
                        // Pre-accept the recommended candidate
                        const recommended = res.candidates.find(c => c.IsRecommended);
                        if (recommended) _items[i].accepted = recommended;
                    }
                } catch (e) {
                    console.error('Prediction failed for item', i, e);
                }
            }

            document.getElementById('hsa-sim-loading').style.display = 'none';
            document.getElementById('hsa-run-btn').disabled = false;
            renderItemResults();
            document.getElementById('hsa-items-panel').style.display = 'block';
            renderSummary();
        }

        // ── Render item result cards ─────────────────────────────────────────────
        function renderItemResults() {
            const list = document.getElementById('hsa-items-list');
            list.innerHTML = _items.map((item, itemIdx) => {
                const existing = item.existingHS ?
                    `<span style="font-size:10px; padding:2px 8px; background:#f0fdf4;
                                color:#15803d; border-radius:99px; margin-left:6px;
                                border:1px solid #bbf7d0;">
                    Existing: ${item.existingHS}
                   </span>` : '';

                const candidateCards = item.candidates.length ?
                    item.candidates.map((c, ci) => {
                        const isRec = c.IsRecommended;
                        const isAccepted = item.accepted?.HSCode === c.HSCode;
                        const dutyColor = c.ImportDutyRate === 0 ? '#15803d' :
                            c.ImportDutyRate <= 10 ? '#185FA5' :
                            c.ImportDutyRate <= 20 ? '#b45309' : '#b91c1c';

                        const dutyLine = c.DutyBreakdown ?
                            `<span style="font-size:10px; color:#b91c1c; margin-left:8px;">
                               Total duty: GH₵ ${fmtNum(c.DutyBreakdown.TotalDuty)}
                           </span>` : '';

                        const justBtn = c.Justification ?
                            `<button type="button"
                               onclick="window.HSAdvisor.toggleArg('arg-${itemIdx}-${ci}')"
                               style="font-size:11px; color:#185FA5; background:none;
                                      border:none; cursor:pointer; padding:0;">
                               ▸ Legal Argument
                           </button>
                           <div id="arg-${itemIdx}-${ci}"
                               style="display:none; margin-top:6px; font-size:11px;
                                      color:#374151; line-height:1.7; white-space:pre-line;
                                      padding:8px; background:#fffbeb;
                                      border:1px solid #fde68a; border-radius:6px;">
                               ${escHtml(c.Justification)}
                           </div>` : '';

                        return `
                    <div style="border:${isAccepted ? '2px solid #15803d' : '1px solid #e5e7eb'};
                                background:${isAccepted ? '#f0fdf4' : '#fff'};
                                border-radius:6px; padding:10px; margin-bottom:6px;">
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span style="font-family:monospace; font-size:13px; font-weight:700;
                                         background:#185FA5; color:#fff; padding:3px 10px;
                                         border-radius:5px;">
                                ${c.HSCode}
                            </span>
                            <span style="font-size:12px; font-weight:600; color:#111827; flex:1;">
                                ${escHtml(c.HeadingDesc)}
                            </span>
                            <span style="font-size:14px; font-weight:700; color:${dutyColor};">
                                ${c.ImportDutyRate}%
                            </span>
                            ${dutyLine}
                            ${isRec ? '<span style="font-size:9px; font-weight:700; padding:2px 8px; border-radius:99px; background:#15803d; color:#fff;">Recommended</span>' : ''}
                            ${isAccepted ? '<span style="font-size:9px; font-weight:700; padding:2px 8px; border-radius:99px; background:#15803d; color:#fff;">✓ Accepted</span>' : ''}
                        </div>
                        <div style="margin-top:6px; display:flex; align-items:center; gap:6px;">
                            <div style="flex:1; background:#e5e7eb; border-radius:99px; height:3px; overflow:hidden;">
                                <div style="height:3px; background:#185FA5; width:${c.Confidence}%; border-radius:99px;"></div>
                            </div>
                            <span style="font-size:10px; color:#6b7280;">${c.Confidence}% conf.</span>
                        </div>
                        <div style="margin-top:8px; display:flex; gap:6px; align-items:center;">
                            <button type="button"
                                onclick="window.HSAdvisor.acceptItem(${itemIdx}, ${ci})"
                                style="padding:4px 14px; background:${isAccepted ? '#15803d' : '#185FA5'};
                                       color:#fff; border:none; border-radius:5px; font-size:11px;
                                       font-weight:600; cursor:pointer;">
                                ${isAccepted ? '✓ Accepted' : 'Use ' + c.HSCode}
                            </button>
                            ${justBtn}
                        </div>
                    </div>`;
                    }).join('') :
                    '<p style="font-size:12px; color:#9ca3af; padding:8px 0;">No predictions available for this item.</p>';

                return `
            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;
                        margin-bottom:12px;">
                <div style="background:#f9fafb; padding:10px 14px; border-bottom:1px solid #e5e7eb;
                            display:flex; align-items:center; gap:8px;">
                    <span style="font-size:12px; font-weight:700; color:#185FA5;">
                        ${escHtml(item.label)}
                    </span>
                    ${existing}
                </div>
                <div style="padding:12px 14px;">
                    <p style="font-size:11px; color:#6b7280; margin-bottom:8px;">
                        <strong>Description:</strong> ${escHtml(item.description)}
                    </p>
                    ${candidateCards}
                </div>
            </div>`;
            }).join('');
        }

        // ── Render summary strip ─────────────────────────────────────────────────
        function renderSummary() {
            const body = document.getElementById('hsa-summary-body');
            const acceptedItems = _items.filter(i => i.accepted);
            const noHS = _items.filter(i => !i.accepted).length;

            if (!_cifValue || _cifValue <= 0) {
                body.innerHTML = `
                <p style="font-size:12px; color:#6b7280; text-align:center;">
                    Enter a CIF value above and re-run the simulation to see duty calculations.
                </p>
                <p style="font-size:13px; font-weight:700; color:#374151; margin-top:8px;">
                    ${acceptedItems.length} / ${_items.length} items have HS codes assigned.
                    ${noHS > 0 ? '<span style="color:#b91c1c;">' + noHS + ' item(s) still need classification.</span>' : ''}
                </p>`;
                return;
            }

            let lowestDuty = 0;
            let highestDuty = 0;

            _items.forEach(item => {
                if (!item.accepted) return;
                const acc = item.accepted.DutyBreakdown;
                if (acc) lowestDuty += acc.TotalDuty;

                // Highest = highest duty candidate
                const highestCandidate = item.candidates.reduce((max, c) =>
                    (c.ImportDutyRate > (max?.ImportDutyRate ?? 0)) ? c : max, null);
                const highDB = highestCandidate?.DutyBreakdown;
                if (highDB) highestDuty += highDB.TotalDuty;
            });

            const savings = Math.max(0, highestDuty - lowestDuty);
            const savingsPct = highestDuty > 0 ?
                Math.round((savings / highestDuty) * 100) : 0;

            body.innerHTML = `
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:12px;">
                <div style="text-align:center; padding:12px; background:#fef2f2;
                            border-radius:8px; border:1px solid #fecaca;">
                    <p style="font-size:10px; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">
                        GRA Worst Case
                    </p>
                    <p style="font-size:18px; font-weight:700; color:#b91c1c;">
                        GH₵ ${fmtNum(highestDuty)}
                    </p>
                    <p style="font-size:10px; color:#6b7280;">highest duty codes</p>
                </div>
                <div style="text-align:center; padding:12px; background:#f0fdf4;
                            border-radius:8px; border:1px solid #bbf7d0;">
                    <p style="font-size:10px; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">
                        Best Case (Your Argument)
                    </p>
                    <p style="font-size:18px; font-weight:700; color:#15803d;">
                        GH₵ ${fmtNum(lowestDuty)}
                    </p>
                    <p style="font-size:10px; color:#6b7280;">recommended codes</p>
                </div>
                <div style="text-align:center; padding:12px; background:#eff6ff;
                            border-radius:8px; border:1px solid #bfdbfe;">
                    <p style="font-size:10px; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">
                        Potential Savings
                    </p>
                    <p style="font-size:18px; font-weight:700; color:#185FA5;">
                        GH₵ ${fmtNum(savings)}
                    </p>
                    <p style="font-size:10px; color:#6b7280;">${savingsPct}% reduction</p>
                </div>
            </div>
            <p style="font-size:11px; color:#6b7280; text-align:center;">
                ${acceptedItems.length} of ${_items.length} items classified.
                ${noHS > 0 ? `<span style="color:#b91c1c;">${noHS} item(s) still need classification.</span>` : ''}
                CIF value used: GH₵ ${fmtNum(_cifValue)}
            </p>`;
        }

        // ── Accept individual item ────────────────────────────────────────────────
        function acceptItem(itemIdx, candidateIdx) {
            const item = _items[itemIdx];
            const candidate = item.candidates[candidateIdx];
            if (!item || !candidate) return;

            item.accepted = candidate;

            fetch(ACCEPT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        hs_code: candidate.HSCode,
                        source_type: item.type,
                        consignment_id: _consignment.ConsignmentID,
                        bl: _consignment.MainBL,
                        house_bl: item.houseBl,
                        description: item.description,
                        predicted_hs_code: item.candidates[0]?.HSCode,
                        was_recommended: candidate.IsRecommended,
                        all_candidates: item.candidates.map(c => ({
                            HSCode: c.HSCode,
                            Confidence: c.Confidence,
                            ImportDutyRate: c.ImportDutyRate,
                        })),
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        renderItemResults();
                        renderSummary();
                    }
                });
        }

        // ── Accept all recommended ────────────────────────────────────────────────
        function acceptAll() {
            const items = _items
                .filter(i => i.accepted)
                .map(i => ({
                    id: i.container_no,
                    hs_code: i.accepted.HSCode,
                    house_bl: i.houseBl,
                    description: i.description,
                    was_recommended: i.accepted.IsRecommended,
                }));

            if (!items.length) return;

            document.getElementById('hsa-accept-all-btn').textContent = 'Saving...';
            document.getElementById('hsa-accept-all-btn').disabled = true;

            fetch(ACCEPT_ALL_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        consignment_id: _consignment.ConsignmentID,
                        bl: _consignment.MainBL,
                        type: _type,
                        items: items,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    const btn = document.getElementById('hsa-accept-all-btn');
                    btn.disabled = false;
                    if (data.success) {
                        btn.textContent = '✓ ' + data.message;
                        btn.style.background = '#15803d';
                    } else {
                        btn.textContent = 'Accept All Recommended Codes';
                        alert(data.message || 'Failed to save.');
                    }
                });
        }

        // ── Print report ──────────────────────────────────────────────────────────
        function printReport() {
            const params = new URLSearchParams({
                bl: _consignment?.MainBL ?? '',
                cif_value: _cifValue || '',
            });
            window.open(PRINT_URL + '?' + params.toString(), '_blank');
        }

        // ── Toggle argument ───────────────────────────────────────────────────────
        function toggleArg(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }

        // ── Helpers ──────────────────────────────────────────────────────────────
        function resetSimulation() {
            _consignment = null;
            _items = [];
            _type = 'FCL';
            _cifValue = 0;
            ['hsa-items-panel', 'hsa-sim-loading'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            const itemsList = document.getElementById('hsa-items-list');
            const summary = document.getElementById('hsa-summary-body');
            if (itemsList) itemsList.innerHTML = '';
            if (summary) summary.innerHTML = '';
            const btn = document.getElementById('hsa-accept-all-btn');
            if (btn) {
                btn.textContent = 'Accept All Recommended Codes';
                btn.disabled = false;
                btn.style.background = '#15803d';
            }
            const fill = document.getElementById('hsa-progress-fill');
            if (fill) fill.style.width = '0%';
        }

        function setLoading(type, loading) {
            if (type === 'load') {
                const btn = document.getElementById('hsa-load-btn');
                if (btn) {
                    btn.textContent = loading ? 'Loading...' : 'Load Consignment';
                    btn.disabled = loading;
                }
                document.getElementById('hsa-loading').style.display = loading ? 'block' : 'none';
            }
        }

        function showLoadError(msg) {
            const el = document.getElementById('hsa-load-error');
            if (el) {
                el.textContent = msg;
                el.style.display = msg ? 'block' : 'none';
            }
        }

        function fmtDate(d) {
            if (!d || d === '0000-00-00') return '—';
            return new Date(d).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function fmtNum(val) {
            return parseFloat(val || 0).toLocaleString('en-GH', {
                minimumFractionDigits: 2
            });
        }

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        return {
            open,
            close,
            load,
            runSimulation,
            acceptItem,
            acceptAll,
            printReport,
            toggleArg
        };

    })();
</script>
