{{-- ── Backdrop ── --}}
<div id="hsa-backdrop" onclick="window.HSAdvisor.close()"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
           z-index:1000;"></div>

{{-- ── Drawer ── --}}
<div id="hsa-drawer"
    style="display:none; position:fixed; top:0; right:0; bottom:0; width:820px;
           max-width:95vw; background:var(--card-bg); z-index:1001; overflow-y:auto;
           box-shadow:-4px 0 24px rgba(0,0,0,0.15);
           transition:transform 0.3s ease; transform:translateX(100%);">

    {{-- ── Header ── --}}
    <div
        style="position:sticky; top:0; z-index:10; background:#185FA5;
                padding:14px 20px; display:flex; justify-content:space-between;
                align-items:center;">
        <div>
            <p style="font-size:var(--db-text-base); font-weight:700; color:#fff;">
                HS Code Advisor
            </p>
            <p style="font-size:var(--db-text-xs); color:#bfdbfe; margin-top:2px;">
                Classify goods, compare duty rates and build your legal argument
            </p>
        </div>
        <button onclick="window.HSAdvisor.close()"
            style="background:rgba(255,255,255,0.2); border:none; color:#fff;
                   border-radius:6px; padding:6px 14px; cursor:pointer;
                   font-size:var(--db-text-xs); font-weight:600;">
            ✕ Close
        </button>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:20px;">

        {{-- Step 1 — BL Search --}}
        <div style="margin-bottom:16px;">
            <label
                style="font-size:var(--db-text-xs); font-weight:700; color:var(--text-primary);
                          text-transform:uppercase; letter-spacing:0.05em;
                          display:block; margin-bottom:6px;">
                Bill of Lading Number
            </label>
            <div style="display:flex; gap:8px;">
                <input type="text" id="hsa-bl-input" placeholder="e.g. MSKU2024001" maxlength="50"
                    style="flex:1; padding:8px 12px; border:1px solid var(--border-color);
                           border-radius:8px; font-size:var(--db-text-sm);
                           font-family:monospace; text-transform:uppercase;
                           color:var(--text-primary); background:var(--content-bg);"
                    onkeydown="if(event.key==='Enter') window.HSAdvisor.load()">
                <button onclick="window.HSAdvisor.load()" id="hsa-load-btn"
                    style="padding:8px 20px; background:#185FA5; color:#fff;
                           border:none; border-radius:8px; font-size:var(--db-text-sm);
                           font-weight:600; cursor:pointer; white-space:nowrap;">
                    Search
                </button>
            </div>
            <p id="hsa-load-error" style="display:none; font-size:var(--db-text-xs); color:#b91c1c; margin-top:6px;">
            </p>
        </div>

        {{-- Loading --}}
        <div id="hsa-loading"
            style="display:none; text-align:center; padding:2rem;
                   color:var(--text-muted); font-size:var(--db-text-sm);">
            Loading consignment...
        </div>

        {{-- Consignment panel --}}
        <div id="hsa-consignment-panel" style="display:none;">

            {{-- Info strip --}}
            <div id="hsa-info-strip"
                style="background:var(--content-bg); border:1px solid var(--border-color);
                       border-radius:8px; padding:12px 16px; margin-bottom:16px;
                       display:grid; grid-template-columns:repeat(4,1fr);
                       gap:10px;">
            </div>

            {{-- Container cards --}}
            <div id="hsa-containers-list" style="margin-bottom:16px;"></div>

            {{-- Run All button --}}
            <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
                <button onclick="window.HSAdvisor.runAll()" id="hsa-run-all-btn"
                    style="padding:10px 28px; background:#15803d; color:#fff;
                           border:none; border-radius:8px; font-size:var(--db-text-sm);
                           font-weight:700; cursor:pointer; display:flex;
                           align-items:center; gap:8px;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Run All Simulations
                </button>
            </div>

            {{-- Summary panel --}}
            <div id="hsa-summary-panel"
                style="display:none; margin-top:16px;
                 border:2px solid #185FA5; border-radius:10px; overflow:hidden;">
                <div style="background:#185FA5; padding:10px 16px;">
                    <p
                        style="font-size:var(--db-text-xs); font-weight:700; color:#fff;
                              text-transform:uppercase; letter-spacing:0.05em;">
                        Duty Simulation Summary
                    </p>
                </div>
                <div id="hsa-summary-body" style="padding:16px;"></div>
                <div style="padding:0 16px 16px; display:flex; gap:10px; justify-content:flex-end;">
                    <button onclick="window.HSAdvisor.printReport()"
                        style="padding:8px 20px; background:#374151; color:#fff;
                               border:none; border-radius:8px; font-size:var(--db-text-xs);
                               font-weight:600; cursor:pointer;">
                        Print Report
                    </button>
                    <button onclick="window.HSAdvisor.acceptAll()" id="hsa-accept-all-btn"
                        style="padding:8px 24px; background:#15803d; color:#fff;
                               border:none; border-radius:8px; font-size:var(--db-text-xs);
                               font-weight:700; cursor:pointer;">
                        Accept All Recommended Codes
                    </button>
                </div>
            </div>

        </div>{{-- end consignment panel --}}

    </div>{{-- end body --}}
</div>{{-- end drawer --}}

<script>
    window.HSAdvisor = (function() {

        const LOAD_URL = '{{ route('hs-code.load-consignment') }}';
        const PREDICT_URL = '{{ route('hs-code.predict') }}';
        const ACCEPT_URL = '{{ route('hs-code.accept') }}';
        const ACCEPT_ALL_URL = '{{ route('hs-code.accept-all') }}';
        const PRINT_URL = '{{ route('hs-code.print-report') }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // ── State ─────────────────────────────────────────────────────────────
        let _consignment = null;
        // Array of { containerNo, containerSize, description, existingHS, candidates, accepted, cifValue }
        let _containers = [];

        // ── Open / Close ──────────────────────────────────────────────────────
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

        // ── Load consignment ──────────────────────────────────────────────────
        function load() {
            const bl = document.getElementById('hsa-bl-input').value.trim().toUpperCase();
            if (!bl) {
                showLoadError('Please enter a BL number.');
                return;
            }

            showLoadError('');
            reset();
            setLoading(true);
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
                    setLoading(false);
                    if (!data.success) {
                        showLoadError(data.message || 'Consignment not found.');
                        return;
                    }

                    _consignment = data.consignment;

                    // Build internal state from containers
                    _containers = (data.containers || []).map(c => ({
                        containerNo: c.ContainerNo,
                        containerSize: c.ContainerSize,
                        description: c.ItemDetails || '',
                        existingHS: c.HSCode || null,
                        cifValue: 0,
                        candidates: [],
                        accepted: null,
                    }));

                    renderInfoStrip(data);
                    renderContainerCards();
                    document.getElementById('hsa-consignment-panel').style.display = 'block';
                })
                .catch(err => {
                    setLoading(false);
                    showLoadError('Network error: ' + err.message);
                });
        }

        // ── Render info strip ─────────────────────────────────────────────────
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
                    label: 'Status',
                    val: status[c.Status] ?? '—',
                    mono: false
                },
                {
                    label: 'Containers',
                    val: data.totalItems + ' container(s)',
                    mono: false
                },
            ].map(i => `
            <div>
                <p style="font-size:9px; text-transform:uppercase; letter-spacing:0.05em;
                           color:var(--text-muted); margin-bottom:2px;">${i.label}</p>
                <p style="font-size:var(--db-text-sm); font-weight:700; color:var(--text-primary);
                           ${i.mono ? 'font-family:monospace;' : ''}">
                    ${escHtml(String(i.val ?? '—'))}
                </p>
            </div>`).join('');
        }

        // ── Render container cards ─────────────────────────────────────────────
        function renderContainerCards() {
            const list = document.getElementById('hsa-containers-list');
            if (!list) return;

            list.innerHTML = _containers.map((c, idx) => `
            <div id="hsa-card-${idx}"
                 style="border:1px solid var(--border-color); border-radius:10px;
                        overflow:hidden; margin-bottom:12px;">

                {{-- Card header --}}
                <div style="background:var(--content-bg); padding:10px 14px;
                            border-bottom:1px solid var(--border-color);
                            display:flex; align-items:center; gap:8px;">
                    <span style="font-family:monospace; font-size:var(--db-text-sm);
                                 font-weight:700; color:#185FA5;">
                        ${escHtml(c.containerNo)}
                    </span>
                    <span style="font-size:var(--db-text-xs); color:var(--text-muted);">
                        ${escHtml(c.containerSize)}ft
                    </span>
                    ${c.existingHS ? `
                    <span style="font-size:0.72rem; padding:2px 8px; background:#f0fdf4;
                                 color:#15803d; border-radius:99px; border:1px solid #bbf7d0;">
                        Existing HS: ${escHtml(c.existingHS)}
                    </span>` : ''}
                </div>

                {{-- Card body --}}
                <div style="padding:14px;">

                    {{-- Editable description --}}
                    <div style="margin-bottom:12px;">
                        <label style="font-size:var(--db-text-xs); font-weight:600;
                                      color:var(--text-primary); display:block; margin-bottom:4px;">
                            Item Description
                            <span style="font-size:0.7rem; color:var(--text-muted);
                                         font-weight:400; margin-left:4px;">
                                (edit to refine classification)
                            </span>
                        </label>
                        <textarea id="hsa-desc-${idx}" rows="2"
                            style="width:100%; padding:8px 10px;
                                   border:1px solid var(--border-color); border-radius:6px;
                                   font-size:var(--db-text-sm); color:var(--text-primary);
                                   background:var(--content-bg); resize:vertical;
                                   box-sizing:border-box;"
                            placeholder="Describe the goods in detail for accurate classification..."
                        >${escHtml(c.description)}</textarea>
                    </div>

                    {{-- CIF input --}}
                    <div style="margin-bottom:12px;">
                        <label style="font-size:var(--db-text-xs); font-weight:600;
                                      color:var(--text-primary); display:block; margin-bottom:4px;">
                            CIF Value (GH₵)
                            <span style="font-size:0.7rem; color:var(--text-muted);
                                         font-weight:400; margin-left:4px;">
                                (optional — for duty calculation)
                            </span>
                        </label>
                        <input type="number" id="hsa-cif-${idx}"
                            placeholder="0.00" min="0" step="0.01"
                            style="width:200px; padding:7px 10px;
                                   border:1px solid var(--border-color); border-radius:6px;
                                   font-size:var(--db-text-sm); color:var(--text-primary);
                                   background:var(--content-bg);">
                    </div>

                    {{-- Run button --}}
                    <button onclick="window.HSAdvisor.runOne(${idx})"
                        id="hsa-run-${idx}"
                        style="padding:6px 16px; background:#185FA5; color:#fff;
                               border:none; border-radius:6px; font-size:var(--db-text-xs);
                               font-weight:600; cursor:pointer;">
                        Analyse This Container
                    </button>

                    {{-- Results area --}}
                    <div id="hsa-results-${idx}" style="margin-top:12px;"></div>

                </div>
            </div>`).join('');
        }

        // ── Run simulation for one container ──────────────────────────────────
        async function runOne(idx) {
            const container = _containers[idx];
            if (!container) return;

            // Read from DOM — officer may have edited the description
            const description = document.getElementById(`hsa-desc-${idx}`)?.value.trim() ?? '';
            const cifValue = parseFloat(document.getElementById(`hsa-cif-${idx}`)?.value || 0);

            if (!description) {
                showResultError(idx, 'Please enter an item description before analysing.');
                return;
            }

            // Update state from DOM
            container.description = description;
            container.cifValue = cifValue;

            const btn = document.getElementById(`hsa-run-${idx}`);
            const results = document.getElementById(`hsa-results-${idx}`);

            btn.disabled = true;
            btn.textContent = 'Analysing...';
            results.innerHTML = `
            <div style="font-size:var(--db-text-xs); color:var(--text-muted);
                        padding:8px 0;">
                Classifying goods and comparing HS codes...
            </div>`;

            try {
                const res = await fetch(PREDICT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        description: description,
                        source_type: 'FCL',
                        consignment_id: _consignment.ConsignmentID,
                        bl: _consignment.MainBL,
                        cif_value: cifValue || null,
                    }),
                }).then(r => r.json());

                if (!res.success) {
                    showResultError(idx, res.message || 'No matching HS codes found.');
                    btn.disabled = false;
                    btn.textContent = 'Analyse This Container';
                    return;
                }

                container.candidates = res.candidates;

                // Pre-select recommended candidate
                const recommended = res.candidates.find(c => c.IsRecommended);
                if (recommended) container.accepted = recommended;

                renderCandidates(idx);
                updateSummary();

            } catch (e) {
                showResultError(idx, 'Analysis failed. Please try again.');
                console.error('[HSAdvisor] runOne error:', e);
            }

            btn.disabled = false;
            btn.textContent = 'Re-analyse';
        }

        // ── Run all containers sequentially ───────────────────────────────────
        async function runAll() {
            const btn = document.getElementById('hsa-run-all-btn');
            btn.disabled = true;
            btn.textContent = 'Running...';

            for (let i = 0; i < _containers.length; i++) {
                await runOne(i);
            }

            btn.disabled = false;
            btn.textContent = 'Run All Simulations';
            document.getElementById('hsa-summary-panel').style.display = 'block';
        }

        // ── Render candidates for one container ───────────────────────────────
        function renderCandidates(idx) {
            const container = _containers[idx];
            const results = document.getElementById(`hsa-results-${idx}`);
            if (!results) return;

            if (!container.candidates.length) {
                results.innerHTML = `
                <p style="font-size:var(--db-text-xs); color:var(--text-muted); padding:8px 0;">
                    No matching codes found. Try a more detailed description.
                </p>`;
                return;
            }

            results.innerHTML = container.candidates.map((c, ci) => {
                const isRec = c.IsRecommended;
                const isAccepted = container.accepted?.HSCode === c.HSCode;

                // Confidence colour
                const confColor = c.Confidence >= 85 ? '#15803d' :
                    c.Confidence >= 60 ? '#92400e' : '#b91c1c';
                const confBg = c.Confidence >= 85 ? '#dcfce7' :
                    c.Confidence >= 60 ? '#fef3c7' : '#fee2e2';

                // Duty colour
                const dutyColor = c.ImportDutyRate === 0 ? '#15803d' :
                    c.ImportDutyRate <= 10 ? '#185FA5' :
                    c.ImportDutyRate <= 20 ? '#b45309' : '#b91c1c';

                // Source badge
                const sourceBadge = c.Source === 'analysis' ?
                    `<span style="font-size:0.68rem; padding:1px 7px; background:#eff6ff;
                               color:#185FA5; border-radius:99px; border:1px solid #bfdbfe;">
                       Advanced Analysis
                   </span>` :
                    `<span style="font-size:0.68rem; padding:1px 7px; background:#f3f4f6;
                               color:#6b7280; border-radius:99px; border:1px solid #e5e7eb;">
                       Pattern Match
                   </span>`;

                // Duty breakdown
                const dutyBreakdown = c.DutyBreakdown ? `
                <div style="margin-top:8px; padding:10px; background:var(--content-bg);
                            border-radius:6px; border:1px solid var(--border-color);">
                    <p style="font-size:var(--db-text-xs); font-weight:700;
                              color:var(--text-primary); margin-bottom:6px;">
                        Duty Breakdown — CIF: GH₵ ${fmtNum(c.DutyBreakdown.CIFValue)}
                    </p>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px;">
                        ${dutyRow('Import Duty', c.DutyBreakdown.ImportDuty, c.ImportDutyRate + '%')}
                        ${dutyRow('VAT', c.DutyBreakdown.VAT, '15%')}
                        ${dutyRow('NHIL', c.DutyBreakdown.NHIL, '2.5%')}
                        ${dutyRow('GETFund', c.DutyBreakdown.GETFund, '2.5%')}
                        ${dutyRow('ECOWAS', c.DutyBreakdown.ECOWAS, '0.5%')}
                        ${dutyRow('AU Levy', c.DutyBreakdown.AULevy, '0.2%')}
                    </div>
                    <div style="margin-top:8px; padding-top:8px;
                                border-top:1px solid var(--border-color);
                                display:flex; justify-content:space-between;
                                font-weight:700; font-size:var(--db-text-sm);">
                        <span>Total Duty</span>
                        <span style="color:#b91c1c;">
                            GH₵ ${fmtNum(c.DutyBreakdown.TotalDuty)}
                            <span style="font-size:var(--db-text-xs); color:var(--text-muted);
                                         font-weight:400; margin-left:4px;">
                                (${c.DutyBreakdown.EffectiveRate}% of CIF)
                            </span>
                        </span>
                    </div>
                </div>` : '';

                // Legal argument
                const legalArg = c.Justification ? `
                <div style="margin-top:8px;">
                    <button type="button"
                        onclick="window.HSAdvisor.toggleArg('hsa-arg-${idx}-${ci}')"
                        style="font-size:var(--db-text-xs); color:#185FA5; background:none;
                               border:none; cursor:pointer; padding:0; font-weight:600;">
                        ⚖ Legal Argument & Rebuttal
                    </button>
                    <div id="hsa-arg-${idx}-${ci}"
                        style="display:none; margin-top:6px; font-size:var(--db-text-xs);
                               color:var(--text-primary); line-height:1.8;
                               white-space:pre-line; padding:10px;
                               background:#fffbeb; border:1px solid #fde68a;
                               border-radius:6px;">
                        ${escHtml(c.Justification)}
                    </div>
                </div>` : '';

                return `
                <div style="border:${isAccepted ? '2px solid #15803d' : '1px solid var(--border-color)'};
                            background:${isAccepted ? '#f0fdf4' : 'var(--card-bg)'};
                            border-radius:8px; padding:12px; margin-bottom:8px;">

                    {{-- Top row: HS code, description, duty rate --}}
                    <div style="display:flex; align-items:flex-start; gap:10px; flex-wrap:wrap;">
                        <span style="font-family:monospace; font-size:var(--db-text-base);
                                     font-weight:700; background:#185FA5; color:#fff;
                                     padding:4px 12px; border-radius:6px; flex-shrink:0;">
                            ${escHtml(c.HSCode)}
                        </span>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:var(--db-text-sm); font-weight:700;
                                      color:var(--text-primary);">
                                ${escHtml(c.HeadingDesc)}
                            </p>
                            <p style="font-size:var(--db-text-xs); color:var(--text-muted); margin-top:2px;">
                                Chapter ${escHtml(c.Chapter)} — ${escHtml(c.ChapterDesc)}
                            </p>
                        </div>
                        <div style="text-align:right; flex-shrink:0;">
                            <p style="font-size:var(--db-text-lg); font-weight:700;
                                      color:${dutyColor};">
                                ${c.ImportDutyRate}%
                            </p>
                            <p style="font-size:0.7rem; color:var(--text-muted);">import duty</p>
                            <p style="font-size:0.7rem; color:var(--text-muted);">
                                Total: ${c.TotalLevyRate}%
                            </p>
                        </div>
                    </div>

                    {{-- Badges row --}}
                    <div style="display:flex; align-items:center; gap:6px;
                                flex-wrap:wrap; margin-top:8px;">
                        ${sourceBadge}
                        ${isRec ? `<span style="font-size:0.68rem; font-weight:700;
                                               padding:2px 8px; border-radius:99px;
                                               background:#15803d; color:#fff;">
                                       ★ Recommended
                                   </span>` : ''}
                        ${isAccepted ? `<span style="font-size:0.68rem; font-weight:700;
                                                    padding:2px 8px; border-radius:99px;
                                                    background:#15803d; color:#fff;">
                                            ✓ Accepted
                                        </span>` : ''}
                    </div>

                    {{-- Confidence bar --}}
                    <div style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                        <span style="font-size:0.7rem; color:var(--text-muted);
                                     white-space:nowrap;">Confidence</span>
                        <div style="flex:1; background:var(--border-color);
                                    border-radius:99px; height:5px; overflow:hidden;">
                            <div style="height:5px; border-radius:99px;
                                        background:${confColor};
                                        width:${c.Confidence}%;
                                        transition:width 0.5s ease;"></div>
                        </div>
                        <span style="font-size:var(--db-text-xs); font-weight:700;
                                     color:${confColor}; white-space:nowrap;
                                     background:${confBg}; padding:1px 8px;
                                     border-radius:99px;">
                            ${c.Confidence}%
                        </span>
                    </div>

                    ${dutyBreakdown}
                    ${legalArg}

                    {{-- Action button --}}
                    <div style="margin-top:10px;">
                        <button type="button"
                            onclick="window.HSAdvisor.acceptOne(${idx}, ${ci})"
                            style="padding:6px 16px;
                                   background:${isAccepted ? '#15803d' : '#185FA5'};
                                   color:#fff; border:none; border-radius:6px;
                                   font-size:var(--db-text-xs); font-weight:600;
                                   cursor:pointer;">
                            ${isAccepted ? '✓ Accepted' : 'Use ' + escHtml(c.HSCode)}
                        </button>
                    </div>
                </div>`;
            }).join('');
        }

        // ── Accept one candidate ──────────────────────────────────────────────
        function acceptOne(containerIdx, candidateIdx) {
            const container = _containers[containerIdx];
            const candidate = container?.candidates[candidateIdx];
            if (!container || !candidate) return;

            container.accepted = candidate;

            fetch(ACCEPT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        hs_code: candidate.HSCode,
                        source_type: 'FCL',
                        consignment_id: _consignment.ConsignmentID,
                        bl: _consignment.MainBL,
                        container_no: container.containerNo,
                        description: container.description,
                        predicted_hs_code: container.candidates[0]?.HSCode,
                        was_recommended: candidate.IsRecommended,
                        all_candidates: container.candidates.map(c => ({
                            HSCode: c.HSCode,
                            Confidence: c.Confidence,
                            ImportDutyRate: c.ImportDutyRate,
                        })),
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        renderCandidates(containerIdx);
                        updateSummary();
                    }
                });
        }

        // ── Accept all recommended ────────────────────────────────────────────
        function acceptAll() {
            const items = _containers
                .filter(c => c.accepted)
                .map(c => ({
                    id: c.containerNo,
                    hs_code: c.accepted.HSCode,
                    house_bl: null,
                    description: c.description,
                    was_recommended: c.accepted.IsRecommended,
                }));

            if (!items.length) return;

            const btn = document.getElementById('hsa-accept-all-btn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

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
                        type: 'FCL',
                        items: items,
                    }),
                })
                .then(r => r.json())
                .then(data => {
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

        // ── Update summary panel ──────────────────────────────────────────────
        function updateSummary() {
            const panel = document.getElementById('hsa-summary-panel');
            const body = document.getElementById('hsa-summary-body');
            if (!panel || !body) return;

            const analysed = _containers.filter(c => c.candidates.length > 0);
            if (!analysed.length) return;

            panel.style.display = 'block';

            const accepted = _containers.filter(c => c.accepted);
            const pending = _containers.filter(c => !c.accepted && c.candidates.length > 0);

            let lowestDuty = 0;
            let highestDuty = 0;
            let hasCIF = false;

            _containers.forEach(c => {
                if (!c.accepted) return;
                const acc = c.accepted.DutyBreakdown;
                if (acc) {
                    lowestDuty += acc.TotalDuty;
                    hasCIF = true;
                }

                const highest = c.candidates.reduce(
                    (max, cand) => cand.ImportDutyRate > (max?.ImportDutyRate ?? 0) ? cand : max,
                    null
                );
                const highDB = highest?.DutyBreakdown;
                if (highDB) highestDuty += highDB.TotalDuty;
            });

            const savings = Math.max(0, highestDuty - lowestDuty);
            const savingsPct = highestDuty > 0 ?
                Math.round((savings / highestDuty) * 100) : 0;

            body.innerHTML = hasCIF ? `
            <div style="display:grid; grid-template-columns:repeat(3,1fr);
                        gap:12px; margin-bottom:12px;">
                <div style="text-align:center; padding:12px; background:#fef2f2;
                            border-radius:8px; border:1px solid #fecaca;">
                    <p style="font-size:var(--db-text-xs); text-transform:uppercase;
                              color:var(--text-muted); margin-bottom:4px;">GRA Worst Case</p>
                    <p style="font-size:var(--db-val); font-weight:700; color:#b91c1c;">
                        GH₵ ${fmtNum(highestDuty)}
                    </p>
                    <p style="font-size:var(--db-text-xs); color:var(--text-muted);">
                        highest duty codes
                    </p>
                </div>
                <div style="text-align:center; padding:12px; background:#f0fdf4;
                            border-radius:8px; border:1px solid #bbf7d0;">
                    <p style="font-size:var(--db-text-xs); text-transform:uppercase;
                              color:var(--text-muted); margin-bottom:4px;">Your Best Case</p>
                    <p style="font-size:var(--db-val); font-weight:700; color:#15803d;">
                        GH₵ ${fmtNum(lowestDuty)}
                    </p>
                    <p style="font-size:var(--db-text-xs); color:var(--text-muted);">
                        recommended codes
                    </p>
                </div>
                <div style="text-align:center; padding:12px; background:#eff6ff;
                            border-radius:8px; border:1px solid #bfdbfe;">
                    <p style="font-size:var(--db-text-xs); text-transform:uppercase;
                              color:var(--text-muted); margin-bottom:4px;">Potential Savings</p>
                    <p style="font-size:var(--db-val); font-weight:700; color:#185FA5;">
                        GH₵ ${fmtNum(savings)}
                    </p>
                    <p style="font-size:var(--db-text-xs); color:var(--text-muted);">
                        ${savingsPct}% reduction
                    </p>
                </div>
            </div>
            <p style="font-size:var(--db-text-xs); color:var(--text-muted); text-align:center;">
                ${accepted.length} of ${_containers.length} containers classified.
                ${pending.length > 0
                    ? `<span style="color:#b91c1c;">${pending.length} still need a code accepted.</span>`
                    : ''}
            </p>` : `
            <p style="font-size:var(--db-text-sm); color:var(--text-muted); text-align:center;">
                ${accepted.length} of ${_containers.length} containers classified.
                Enter CIF values on each container card and re-analyse to see duty calculations.
            </p>`;
        }

        // ── Print report ──────────────────────────────────────────────────────
        function printReport() {
            if (!_consignment) return;
            const totalCif = _containers.reduce((s, c) => s + (c.cifValue || 0), 0);
            const params = new URLSearchParams({
                bl: _consignment.MainBL,
                cif_value: totalCif || '',
            });
            window.open(PRINT_URL + '?' + params.toString(), '_blank');
        }

        // ── Toggle legal argument ─────────────────────────────────────────────
        function toggleArg(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }

        // ── Helpers ───────────────────────────────────────────────────────────
        function reset() {
            _consignment = null;
            _containers = [];
            const list = document.getElementById('hsa-containers-list');
            const summary = document.getElementById('hsa-summary-panel');
            if (list) list.innerHTML = '';
            if (summary) summary.style.display = 'none';
            const btn = document.getElementById('hsa-accept-all-btn');
            if (btn) {
                btn.textContent = 'Accept All Recommended Codes';
                btn.disabled = false;
                btn.style.background = '#15803d';
            }
        }

        function setLoading(loading) {
            const btn = document.getElementById('hsa-load-btn');
            if (btn) {
                btn.textContent = loading ? 'Searching...' : 'Search';
                btn.disabled = loading;
            }
            const el = document.getElementById('hsa-loading');
            if (el) el.style.display = loading ? 'block' : 'none';
        }

        function showLoadError(msg) {
            const el = document.getElementById('hsa-load-error');
            if (el) {
                el.textContent = msg;
                el.style.display = msg ? 'block' : 'none';
            }
        }

        function showResultError(idx, msg) {
            const el = document.getElementById(`hsa-results-${idx}`);
            if (el) el.innerHTML = `
            <p style="font-size:var(--db-text-xs); color:#b91c1c; padding:6px 0;">
                ${escHtml(msg)}
            </p>`;
        }

        function dutyRow(label, amount, rate) {
            return `
            <div style="padding:4px 0;">
                <p style="font-size:0.7rem; color:var(--text-muted);">${label} (${rate})</p>
                <p style="font-size:var(--db-text-xs); font-weight:700;
                           color:var(--text-primary);">
                    GH₵ ${fmtNum(amount)}
                </p>
            </div>`;
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
            runOne,
            runAll,
            acceptOne,
            acceptAll,
            printReport,
            toggleArg
        };

    })();
</script>
