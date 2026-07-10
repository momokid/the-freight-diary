{{-- 
     SHARED CONSIGNMENT DETAIL MODAL
     Included in: port-aging-print.blade.php, pending-clearance-print.blade.php
     Triggered by: window.openConsignmentModal(consignmentId)
     Data fetched via: AJAX GET /reports/operations/consignment-modal/{id}
 --}}

{{-- ── Modal overlay ── --}}
<div id="cdm-overlay"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; overflow-y:auto; padding:24px 16px;">

    <div
        style="background:#fff; border-radius:12px; max-width:860px; margin:0 auto; position:relative; overflow:hidden;">

        {{-- ── Modal header ── --}}
        <div id="cdm-header"
            style="background:#185FA5; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <p style="font-size:13px; font-weight:700; color:#fff; font-family:monospace;" id="cdm-bl">—</p>
                <p style="font-size:10px; color:#bfdbfe; margin-top:2px;" id="cdm-carrier-consignee">—</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <span id="cdm-status-badge"
                    style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:99px;"></span>
                <button onclick="window.closeConsignmentModal()"
                    style="background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:6px; padding:6px 12px; cursor:pointer; font-size:12px; font-weight:600;">
                    ✕ Close
                </button>
            </div>
        </div>

        {{-- ── Loading state ── --}}
        <div id="cdm-loading" style="padding:3rem; text-align:center; color:#6b7280;">
            <p style="font-size:13px;">Loading consignment details...</p>
        </div>

        {{-- ── Error state ── --}}
        <div id="cdm-error" style="display:none; padding:3rem; text-align:center; color:#b91c1c;">
            <p style="font-size:13px;" id="cdm-error-msg">Failed to load consignment details.</p>
        </div>

        {{-- ── Modal body ── --}}
        <div id="cdm-body" style="display:none;">

            {{-- ── Tabs ── --}}
            <div style="display:flex; border-bottom:2px solid #e5e7eb; padding:0 20px; gap:4px;">
                <button class="cdm-tab cdm-tab-active" onclick="window.cdmTab('timeline')" id="cdm-tab-timeline"
                    style="padding:10px 16px; border:none; background:none; cursor:pointer; font-size:12px; font-weight:700; color:#185FA5; border-bottom:2px solid #185FA5; margin-bottom:-2px;">
                    📦 Timeline
                </button>
                <button class="cdm-tab" onclick="window.cdmTab('manifest')" id="cdm-tab-manifest"
                    style="padding:10px 16px; border:none; background:none; cursor:pointer; font-size:12px; font-weight:600; color:#6b7280; border-bottom:2px solid transparent; margin-bottom:-2px;">
                    📋 Manifest / Consignees
                </button>
                <button class="cdm-tab" onclick="window.cdmTab('payments')" id="cdm-tab-payments"
                    style="padding:10px 16px; border:none; background:none; cursor:pointer; font-size:12px; font-weight:600; color:#6b7280; border-bottom:2px solid transparent; margin-bottom:-2px;">
                    💳 Payments
                </button>
            </div>

            {{-- ════════════════════════════════ TAB 1: TIMELINE ════════════════════════════════ --}}
            <div id="cdm-panel-timeline" style="padding:20px;">

                {{-- Info strip ── --}}
                <div id="cdm-info-strip"
                    style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px;">
                </div>

                {{-- Timeline ── --}}
                <div style="position:relative; padding:10px 0 30px;">
                    <div
                        style="position:absolute; top:36px; left:calc(100%/14); right:calc(100%/14); height:3px; background:#e5e7eb; z-index:0;">
                    </div>
                    <div id="cdm-timeline-stages"
                        style="display:flex; justify-content:space-between; position:relative; z-index:1;">
                    </div>
                </div>

                {{-- Containers table ── --}}
                <p
                    style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6b7280; margin-bottom:8px;">
                    Containers
                </p>
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Container No</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Size</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Weight</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Gate Out</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Returned</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Demurrage</th>
                        </tr>
                    </thead>
                    <tbody id="cdm-containers-tbody"></tbody>
                </table>
            </div>

            {{-- ════════════════════════════════ TAB 2: MANIFEST ════════════════════════════════ --}}
            <div id="cdm-panel-manifest" style="display:none; padding:20px;">

                <div id="cdm-manifest-empty"
                    style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px; display:none;">
                    This may be an FCL consignment with no HBL entries.
                </div>

                <table id="cdm-manifest-table"
                    style="width:100%; border-collapse:collapse; font-size:12px; display:none;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">House BL</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Consignee</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Phone</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Description</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Weight</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Packages</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cdm-manifest-tbody"></tbody>
                </table>
            </div>

            {{-- ════════════════════════════════ TAB 3: PAYMENTS ════════════════════════════════ --}}
            <div id="cdm-panel-payments" style="display:none; padding:20px;">

                <div id="cdm-payments-empty"
                    style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px; display:none;">
                    No disbursement payments found for this consignment.
                </div>

                <table id="cdm-payments-table"
                    style="width:100%; border-collapse:collapse; font-size:12px; display:none;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Date</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Receipt No</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">HBL</th>
                            <th style="padding:8px 10px; text-align:left; font-size:10px;">Account</th>
                            <th style="padding:8px 10px; text-align:right; font-size:10px;">Expenditure</th>
                            <th style="padding:8px 10px; text-align:right; font-size:10px;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody id="cdm-payments-tbody"></tbody>
                    <tfoot id="cdm-payments-tfoot"></tfoot>
                </table>
            </div>

        </div>{{-- end cdm-body --}}

    </div>
</div>

<script src="{{ asset('js/consignment-timeline.js') }}"></script>
<script>
    (function() {

        const MODAL_URL = '{{ route('reports.operations.consignment-modal', ':id') }}';
        const CT = window.ConsignmentTimeline;

        window.closeConsignmentModal = function() {
            document.getElementById('cdm-overlay').style.display = 'none';
        };

        document.getElementById('cdm-overlay').addEventListener('click', function(e) {
            if (e.target === this) window.closeConsignmentModal();
        });

        window.cdmTab = function(name) {
            ['timeline', 'manifest', 'payments'].forEach(function(t) {
                const panel = document.getElementById('cdm-panel-' + t);
                const tab = document.getElementById('cdm-tab-' + t);
                if (t === name) {
                    panel.style.display = 'block';
                    tab.style.color = '#185FA5';
                    tab.style.fontWeight = '700';
                    tab.style.borderBottom = '2px solid #185FA5';
                } else {
                    panel.style.display = 'none';
                    tab.style.color = '#6b7280';
                    tab.style.fontWeight = '600';
                    tab.style.borderBottom = '2px solid transparent';
                }
            });
        };

        window.openConsignmentModal = function(consignmentId) {
            const overlay = document.getElementById('cdm-overlay');
            const loading = document.getElementById('cdm-loading');
            const error = document.getElementById('cdm-error');
            const body = document.getElementById('cdm-body');

            loading.style.display = 'block';
            error.style.display = 'none';
            body.style.display = 'none';
            overlay.style.display = 'block';
            window.cdmTab('timeline');

            const url = MODAL_URL.replace(':id', consignmentId);

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (!data.success) {
                        document.getElementById('cdm-error-msg').textContent = data.message;
                        loading.style.display = 'none';
                        error.style.display = 'block';
                        return;
                    }
                    renderModal(data);
                    loading.style.display = 'none';
                    body.style.display = 'block';
                })
                .catch(function() {
                    document.getElementById('cdm-error-msg').textContent =
                        'Network error. Please try again.';
                    loading.style.display = 'none';
                    error.style.display = 'block';
                });
        };

        function renderModal(data) {
            const c = data.consignment;
            const st = CT.statusLabel(c.Status);

            document.getElementById('cdm-bl').textContent = 'BL# ' + (c.MainBL || '—');
            document.getElementById('cdm-carrier-consignee').textContent = (c.CarrierName || '—') + '  ·  ' + (c
                .ConsigneeName || '—');

            const badge = document.getElementById('cdm-status-badge');
            badge.textContent = st.label;
            badge.style.background = st.bg;
            badge.style.color = st.color;

            const infoItems = [{
                    label: 'Vessel / Voyage',
                    val: (c.VesselName || '—') + ' / ' + (c.VoyageNo || '—')
                },
                {
                    label: 'POL',
                    val: c.POL_Name || '—'
                },
                {
                    label: 'POD',
                    val: c.POD_Name || '—'
                },
                {
                    label: 'Days Overdue',
                    val: c.DaysOverdue > 0 ?
                        '<span style="color:' + CT.ageCls(c.DaysOverdue) + '; font-weight:700;">' + c
                        .DaysOverdue + ' days</span>' : '<span style="color:#15803d;">On track</span>'
                },
            ];
            document.getElementById('cdm-info-strip').innerHTML = infoItems.map(function(i) {
                return '<div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px;">' +
                    '<p style="font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:0.05em; margin-bottom:3px;">' +
                    i.label + '</p>' +
                    '<p style="font-size:12px; font-weight:700; color:#111827;">' + i.val + '</p>' +
                    '</div>';
            }).join('');

            CT.renderTimeline(data.timeline, 'cdm-timeline-stages');
            CT.renderContainers(data.containers, 'cdm-containers-tbody');
            CT.renderManifest(data.manifest, 'cdm-manifest-table', 'cdm-manifest-empty');
            CT.renderPayments(data.payments, data.totalExpenditure, data.totalRevenue, 'cdm-payments-table',
                'cdm-payments-empty', 'cdm-payments-tfoot');
        }

    })();
</script>
