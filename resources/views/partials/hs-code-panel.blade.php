{{-- ══════════════════════════════════════════════════════════════════════════
     HS CODE PREDICTION PANEL
     Shared partial — included in:
       - Manifest form (LCL): after Description field
       - Container details form (FCL): after ItemDetails field

     Usage:
       @include('partials.hs-code-panel', [
           'sourceType'     => 'LCL',           // or 'FCL'
           'consignmentId'  => $consignment->ConsignmentID,
           'bl'             => $consignment->BL,
           'houseBlVar'     => 'hbl_row_id',     // JS variable holding the HouseBL
           'descriptionVar' => 'description_input_id', // input element ID
       ])
════════════════════════════════════════════════════════════════════════════ --}}

<div id="hs-panel-{{ $sourceType }}" style="margin-top:8px; display:none;">

    {{-- ── Trigger button ── --}}
    <button type="button" onclick="window.HSCodePanel.predict('{{ $sourceType }}')"
        id="hs-predict-btn-{{ $sourceType }}"
        style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px;
               background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px;
               color:#185FA5; font-size:12px; font-weight:600; cursor:pointer;">
        <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        Get HS Code Suggestions
    </button>

    {{-- ── Loading state ── --}}
    <div id="hs-loading-{{ $sourceType }}" style="display:none; margin-top:10px; font-size:12px; color:#6b7280;">
        Analysing item description...
    </div>

    {{-- ── Error state ── --}}
    <div id="hs-error-{{ $sourceType }}"
        style="display:none; margin-top:10px; font-size:12px; color:#b91c1c; padding:8px 12px;
               background:#fef2f2; border:1px solid #fecaca; border-radius:6px;">
    </div>

    {{-- ── Results panel ── --}}
    <div id="hs-results-{{ $sourceType }}" style="display:none; margin-top:10px;">

        {{-- Header ── --}}
        <div
            style="display:flex; justify-content:space-between; align-items:center;
                    margin-bottom:8px;">
            <p style="font-size:11px; font-weight:700; color:#374151;">
                HS Code Suggestions
                <span id="hs-source-badge-{{ $sourceType }}"
                    style="font-size:9px; font-weight:600; padding:2px 7px; border-radius:99px;
                           background:#f3f4f6; color:#6b7280; margin-left:6px;"></span>
            </p>
            <button type="button" onclick="window.HSCodePanel.clear('{{ $sourceType }}')"
                style="background:none; border:none; color:#6b7280; cursor:pointer;
                       font-size:11px;">✕
                Clear</button>
        </div>

        {{-- CIF value input for duty calculation ── --}}
        <div style="display:flex; gap:8px; align-items:center; margin-bottom:10px;">
            <input type="number" id="hs-cif-{{ $sourceType }}"
                placeholder="CIF Value (GH₵) — optional for duty estimate"
                style="flex:1; padding:6px 10px; border:1px solid #e5e7eb; border-radius:6px;
                       font-size:12px; color:#374151;"
                min="0" step="0.01">
            <button type="button" onclick="window.HSCodePanel.recalculate('{{ $sourceType }}')"
                style="padding:6px 12px; background:#185FA5; color:#fff; border:none;
                       border-radius:6px; font-size:11px; font-weight:600; cursor:pointer;
                       white-space:nowrap;">
                Calculate Duty
            </button>
        </div>

        {{-- Candidates list ── --}}
        <div id="hs-candidates-{{ $sourceType }}"></div>

        {{-- Currently accepted ── --}}
        <div id="hs-accepted-{{ $sourceType }}"
            style="display:none; margin-top:10px;
            padding:8px 12px; background:#f0fdf4; border:1px solid #bbf7d0;
            border-radius:6px; font-size:12px; color:#15803d;">
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     HS CODE PANEL JAVASCRIPT
     Loaded once — guards against duplicate initialisation
════════════════════════════════════════════════════════════════════════════ --}}
@once
    <script>
        window.HSCodePanel = (function() {

            // ── Config ──────────────────────────────────────────────────────────────
            const PREDICT_URL = '{{ route('hs-code.predict') }}';
            const ACCEPT_URL = '{{ route('hs-code.accept') }}';
            const CALC_DUTY_URL = '{{ route('hs-code.calculate-duty') }}';
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            // State per source type
            const _state = {};

            function getState(sourceType) {
                if (!_state[sourceType]) {
                    _state[sourceType] = {
                        candidates: [],
                        selectedHSCode: null,
                        consignmentId: null,
                        bl: null,
                        houseBl: null,
                        description: null,
                    };
                }
                return _state[sourceType];
            }

            // ── Show the trigger button ─────────────────────────────────────────────
            // Called externally when description field has content
            function show(sourceType) {
                const panel = document.getElementById('hs-panel-' + sourceType);
                if (panel) panel.style.display = 'block';
            }

            function hide(sourceType) {
                const panel = document.getElementById('hs-panel-' + sourceType);
                if (panel) panel.style.display = 'none';
            }

            // ── Predict ─────────────────────────────────────────────────────────────
            function predict(sourceType, config) {
                // config can be passed programmatically or read from data attributes
                const state = getState(sourceType);

                // Allow external config override
                if (config) {
                    state.consignmentId = config.consignmentId ?? state.consignmentId;
                    state.bl = config.bl ?? state.bl;
                    state.houseBl = config.houseBl ?? state.houseBl;
                    state.description = config.description ?? state.description;
                }

                if (!state.description || state.description.length < 3) {
                    showError(sourceType, 'Please enter an item description first.');
                    return;
                }

                const cifValue = document.getElementById('hs-cif-' + sourceType)?.value ?? '';

                setLoading(sourceType, true);
                clearResults(sourceType);

                fetch(PREDICT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            description: state.description,
                            source_type: sourceType,
                            consignment_id: state.consignmentId,
                            bl: state.bl,
                            house_bl: state.houseBl,
                            cif_value: cifValue || null,
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        setLoading(sourceType, false);
                        if (!data.success) {
                            showError(sourceType, data.message || 'No HS codes found.');
                            return;
                        }
                        state.candidates = data.candidates;
                        renderCandidates(sourceType, data.candidates);

                        // Source badge
                        const badge = document.getElementById('hs-source-badge-' + sourceType);
                        if (badge) {
                            const src = data.source === 'gemini' ? 'AI-powered' : 'Rules engine';
                            badge.textContent = src;
                            badge.style.background = data.source === 'gemini' ? '#ede9fe' : '#f3f4f6';
                            badge.style.color = data.source === 'gemini' ? '#5b21b6' : '#6b7280';
                        }

                        document.getElementById('hs-results-' + sourceType).style.display = 'block';
                    })
                    .catch(err => {
                        setLoading(sourceType, false);
                        showError(sourceType, 'Prediction failed: ' + err.message);
                    });
            }

            // ── Render candidate cards ──────────────────────────────────────────────
            function renderCandidates(sourceType, candidates) {
                const container = document.getElementById('hs-candidates-' + sourceType);
                if (!container) return;

                container.innerHTML = candidates.map((c, i) => {
                    const isRec = c.IsRecommended;
                    const border = isRec ? '2px solid #15803d' : '1px solid #e5e7eb';
                    const bg = isRec ? '#f0fdf4' : '#fff';
                    const dutyColor = c.ImportDutyRate === 0 ? '#15803d' :
                        c.ImportDutyRate <= 10 ? '#185FA5' :
                        c.ImportDutyRate <= 20 ? '#b45309' : '#b91c1c';

                    const dutyBreakdown = c.DutyBreakdown ? `
                <div style="margin-top:8px; padding:8px; background:#f9fafb;
                            border-radius:6px; font-size:11px;">
                    <p style="font-weight:700; color:#374151; margin-bottom:4px;">
                        Duty Breakdown (CIF: GH₵ ${fmtNum(c.DutyBreakdown.CIFValue)})
                    </p>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:4px;">
                        ${dutyRow('Import Duty', c.DutyBreakdown.ImportDuty, c.ImportDutyRate + '%')}
                        ${dutyRow('NHIL', c.DutyBreakdown.NHIL, '2.5%')}
                        ${dutyRow('GETFund', c.DutyBreakdown.GETFund, '2.5%')}
                        ${dutyRow('ECOWAS', c.DutyBreakdown.ECOWAS, '0.5%')}
                        ${dutyRow('AU Levy', c.DutyBreakdown.AULevy, '0.2%')}
                        ${dutyRow('VAT', c.DutyBreakdown.VAT, '15%')}
                    </div>
                    <div style="margin-top:6px; padding-top:6px; border-top:1px solid #e5e7eb;
                                display:flex; justify-content:space-between; font-weight:700;">
                        <span>Total Duty</span>
                        <span style="color:#b91c1c;">GH₵ ${fmtNum(c.DutyBreakdown.TotalDuty)}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:10px;
                                color:#6b7280; margin-top:2px;">
                        <span>Effective Rate</span>
                        <span>${c.DutyBreakdown.EffectiveRate}% of CIF</span>
                    </div>
                </div>` : '';

                    const justification = c.Justification ? `
                <details style="margin-top:8px;">
                    <summary style="font-size:11px; font-weight:600; color:#185FA5;
                                    cursor:pointer; list-style:none;">
                        ▸ Legal Justification &amp; Argument
                    </summary>
                    <div style="margin-top:6px; font-size:11px; color:#374151;
                                line-height:1.7; white-space:pre-line; padding:8px;
                                background:#fffbeb; border:1px solid #fde68a; border-radius:6px;">
                        ${escHtml(c.Justification)}
                    </div>
                </details>` : '';

                    const exclusions = c.Exclusions ? `
                <p style="font-size:10px; color:#b91c1c; margin-top:4px;">
                    ⚠ ${escHtml(c.Exclusions)}
                </p>` : '';

                    return `
            <div style="border:${border}; background:${bg}; border-radius:8px;
                        padding:12px; margin-bottom:8px; position:relative;">

                ${isRec ? '<span style="position:absolute; top:8px; right:8px; font-size:9px; font-weight:700; padding:2px 8px; border-radius:99px; background:#15803d; color:#fff;">Recommended</span>' : ''}

                <div style="display:flex; align-items:flex-start; gap:10px;">
                    <div style="background:#185FA5; color:#fff; border-radius:6px;
                                padding:4px 10px; font-size:13px; font-weight:700;
                                font-family:monospace; white-space:nowrap; flex-shrink:0;">
                        ${c.HSCode}
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:13px; font-weight:700; color:#111827; margin-bottom:2px;">
                            ${escHtml(c.HeadingDesc)}
                        </p>
                        <p style="font-size:10px; color:#6b7280;">
                            Chapter ${c.Chapter} — ${escHtml(c.ChapterDesc)}
                        </p>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-size:16px; font-weight:700; color:${dutyColor};">
                            ${c.ImportDutyRate}%
                        </p>
                        <p style="font-size:9px; color:#6b7280;">import duty</p>
                        <p style="font-size:10px; color:#6b7280; margin-top:2px;">
                            Total: ${c.TotalLevyRate}%
                        </p>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:6px; margin-top:6px;">
                    <div style="flex:1; background:#e5e7eb; border-radius:99px; height:4px; overflow:hidden;">
                        <div style="height:4px; border-radius:99px; background:#185FA5;
                                    width:${c.Confidence}%;"></div>
                    </div>
                    <span style="font-size:10px; color:#6b7280; white-space:nowrap;">
                        ${c.Confidence}% confidence
                    </span>
                </div>

                ${exclusions}
                ${dutyBreakdown}
                ${justification}

                <div style="margin-top:10px; display:flex; gap:8px;">
                    <button type="button"
                        onclick="window.HSCodePanel.accept('${sourceType}', '${c.HSCode}', ${i})"
                        style="flex:1; padding:7px 0; background:${isRec ? '#15803d' : '#185FA5'};
                               color:#fff; border:none; border-radius:6px; font-size:12px;
                               font-weight:600; cursor:pointer;">
                        Use ${c.HSCode}
                    </button>
                </div>
            </div>`;
                }).join('');
            }

            // ── Accept a candidate ──────────────────────────────────────────────────
            function accept(sourceType, hsCode, candidateIndex) {
                const state = getState(sourceType);
                const candidate = state.candidates[candidateIndex];
                if (!candidate) return;

                if (!state.consignmentId || !state.bl) {
                    // If consignment not yet saved show a note
                    showError(sourceType,
                        'Please save the consignment first before accepting an HS Code.');
                    return;
                }

                fetch(ACCEPT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            hs_code: hsCode,
                            source_type: sourceType,
                            consignment_id: state.consignmentId,
                            bl: state.bl,
                            house_bl: state.houseBl,
                            description: state.description,
                            predicted_hs_code: state.candidates[0]?.HSCode,
                            was_recommended: candidate.IsRecommended,
                            all_candidates: state.candidates.map(c => ({
                                HSCode: c.HSCode,
                                Confidence: c.Confidence,
                            })),
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) {
                            showError(sourceType, data.message || 'Failed to save HS Code.');
                            return;
                        }
                        state.selectedHSCode = hsCode;
                        const accepted = document.getElementById('hs-accepted-' + sourceType);
                        if (accepted) {
                            accepted.style.display = 'block';
                            accepted.innerHTML =
                                `<strong>HS Code accepted:</strong> ${data.hs_code} — ${escHtml(data.description)} ` +
                                `<span style="color:#374151;">(Duty: ${data.duty_rate}%)</span>`;
                        }
                        // Dispatch event so parent form knows HS code was accepted
                        document.dispatchEvent(new CustomEvent('hscode:accepted', {
                            detail: {
                                sourceType,
                                hsCode,
                                dutyRate: data.duty_rate
                            }
                        }));
                    })
                    .catch(err => showError(sourceType, 'Failed to save: ' + err.message));
            }

            // ── Recalculate duty with new CIF value ─────────────────────────────────
            function recalculate(sourceType) {
                const state = getState(sourceType);
                const cifValue = parseFloat(
                    document.getElementById('hs-cif-' + sourceType)?.value ?? 0
                );

                if (!state.candidates.length || cifValue <= 0) return;

                const promises = state.candidates.map(c =>
                    fetch(CALC_DUTY_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({
                            hs_code: c.HSCode,
                            cif_value: cifValue
                        }),
                    }).then(r => r.json())
                );

                Promise.all(promises).then(results => {
                    results.forEach((res, i) => {
                        if (res.success) {
                            state.candidates[i].DutyBreakdown = res.breakdown;
                        }
                    });
                    renderCandidates(sourceType, state.candidates);
                });
            }

            // ── Helpers ─────────────────────────────────────────────────────────────
            function setLoading(sourceType, loading) {
                const btn = document.getElementById('hs-predict-btn-' + sourceType);
                const load = document.getElementById('hs-loading-' + sourceType);
                if (btn) btn.style.display = loading ? 'none' : 'inline-flex';
                if (load) load.style.display = loading ? 'block' : 'none';
            }

            function showError(sourceType, msg) {
                const el = document.getElementById('hs-error-' + sourceType);
                if (el) {
                    el.textContent = msg;
                    el.style.display = 'block';
                }
                setTimeout(() => {
                    if (el) el.style.display = 'none';
                }, 5000);
            }

            function clearResults(sourceType) {
                const res = document.getElementById('hs-results-' + sourceType);
                const err = document.getElementById('hs-error-' + sourceType);
                if (res) res.style.display = 'none';
                if (err) err.style.display = 'none';
            }

            function clear(sourceType) {
                clearResults(sourceType);
                getState(sourceType).candidates = [];
            }

            function escHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function fmtNum(val) {
                return parseFloat(val || 0).toLocaleString('en-GH', {
                    minimumFractionDigits: 2
                });
            }

            function dutyRow(label, amount, rate) {
                return `<div style="padding:3px 0;">
            <p style="font-size:9px; color:#6b7280;">${label} (${rate})</p>
            <p style="font-size:11px; font-weight:600; color:#374151;">
                GH₵ ${fmtNum(amount)}
            </p>
        </div>`;
            }

            // ── Public API ───────────────────────────────────────────────────────────
            return {
                show,
                hide,
                predict,
                accept,
                recalculate,
                clear,
                getState
            };

        })();
    </script>
@endonce
