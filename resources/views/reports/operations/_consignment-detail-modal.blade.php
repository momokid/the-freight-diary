{{-- ══════════════════════════════════════════════════════════════════════
     SHARED CONSIGNMENT DETAIL MODAL
     Included in: port-aging-print.blade.php, pending-clearance-print.blade.php
     Triggered by: window.openConsignmentModal(consignmentId)
     Data fetched via: AJAX GET /reports/operations/consignment-modal/{id}
════════════════════════════════════════════════════════════════════════ --}}

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

                    {{-- Spine line --}}
                    <div
                        style="position:absolute; top:36px; left:calc(100%/14); right:calc(100%/14); height:3px; background:#e5e7eb; z-index:0;">
                    </div>

                    {{-- Stages --}}
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
                        <tr style="background:#f3f4f6;">
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Container No
                            </th>
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Size</th>
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Weight</th>
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Gate Out Date
                            </th>
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Return Date
                            </th>
                            <th style="padding:7px 10px; text-align:left; font-size:10px; color:#374151;">Demurrage
                                (days)</th>
                        </tr>
                    </thead>
                    <tbody id="cdm-containers-tbody">
                    </tbody>
                </table>
            </div>

            {{-- ════════════════════════════════ TAB 2: MANIFEST ════════════════════════════════ --}}
            <div id="cdm-panel-manifest" style="display:none; padding:20px;">

                <div id="cdm-manifest-empty"
                    style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px; display:none;">
                    No manifest breakdown found. This may be an FCL consignment with no HBL entries.
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
                    <tbody id="cdm-manifest-tbody">
                    </tbody>
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
                    <tbody id="cdm-payments-tbody">
                    </tbody>
                    <tfoot id="cdm-payments-tfoot">
                    </tfoot>
                </table>
            </div>

        </div>{{-- end cdm-body --}}

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     SHARED MODAL JS
     window.openConsignmentModal(consignmentId) — called from eye icon
     window.closeConsignmentModal()              — called from close button
     window.cdmTab(name)                         — switches tabs
════════════════════════════════════════════════════════════════════════ --}}
<script>
    (function() {

        // ── Config — route injected by Blade ─────────────────────────────────
        const MODAL_URL = '{{ route('reports.operations.consignment-modal', ':id') }}';

        // ── Helpers ──────────────────────────────────────────────────────────
        function fmt(date) {
            if (!date || date === '1970-01-01' || date === '0000-00-00') return '—';
            const d = new Date(date);
            return d.toLocaleDateString('en-GB', {
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

        function statusLabel(s) {
            const map = {
                0: {
                    label: 'Cleared',
                    bg: '#f3f4f6',
                    color: '#374151'
                },
                1: {
                    label: 'Not Arrived',
                    bg: '#fef3c7',
                    color: '#92400e'
                },
                2: {
                    label: 'Pending',
                    bg: '#dbeafe',
                    color: '#1e40af'
                },
                3: {
                    label: 'Gated Out',
                    bg: '#dcfce7',
                    color: '#166534'
                },
            };
            return map[s] ?? {
                label: 'Unknown',
                bg: '#f3f4f6',
                color: '#6b7280'
            };
        }

        function ageCls(days) {
            if (days <= 7) return '#15803d';
            if (days <= 14) return '#b45309';
            if (days <= 30) return '#c2410c';
            return '#b91c1c';
        }

        // ── Show / hide overlay ───────────────────────────────────────────────
        window.closeConsignmentModal = function() {
            document.getElementById('cdm-overlay').style.display = 'none';
        };

        // Close on overlay click (outside modal card)
        document.getElementById('cdm-overlay').addEventListener('click', function(e) {
            if (e.target === this) window.closeConsignmentModal();
        });

        // ── Tab switching ─────────────────────────────────────────────────────
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

        // ── Open modal + fetch data ───────────────────────────────────────────
        window.openConsignmentModal = function(consignmentId) {
            const overlay = document.getElementById('cdm-overlay');
            const loading = document.getElementById('cdm-loading');
            const error = document.getElementById('cdm-error');
            const body = document.getElementById('cdm-body');

            // Reset state
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

        // ── Render modal content ──────────────────────────────────────────────
        function renderModal(data) {
            const c = data.consignment;
            const st = statusLabel(c.Status);

            // Header
            document.getElementById('cdm-bl').textContent =
                'BL# ' + (c.MainBL || '—');
            document.getElementById('cdm-carrier-consignee').textContent =
                (c.CarrierName || '—') + '  ·  ' + (c.ConsigneeName || '—');

            const badge = document.getElementById('cdm-status-badge');
            badge.textContent = st.label;
            badge.style.background = st.bg;
            badge.style.color = st.color;

            // Info strip
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
                        '<span style="color:' + ageCls(c.DaysOverdue) + '; font-weight:700;">' + c.DaysOverdue +
                        ' days</span>' :
                        '<span style="color:#15803d;">On track</span>'
                },
            ];
            document.getElementById('cdm-info-strip').innerHTML = infoItems.map(function(i) {
                return '<div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px;">' +
                    '<p style="font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:0.05em; margin-bottom:3px;">' +
                    i.label + '</p>' +
                    '<p style="font-size:12px; font-weight:700; color:#111827;">' + i.val + '</p>' +
                    '</div>';
            }).join('');

            // Timeline stages
            renderTimeline(data.timeline);

            // Containers
            renderContainers(data.containers);

            // Manifest
            renderManifest(data.manifest);

            // Payments
            renderPayments(data.payments, data.totalExpenditure, data.totalRevenue);
        }

        // ── Timeline renderer ─────────────────────────────────────────────────
        function renderTimeline(stages) {
            const wrap = document.getElementById('cdm-timeline-stages');

            wrap.innerHTML = stages.map(function(s, i) {
                const isLast = i === stages.length - 1;
                const dotBg = s.done ? '#15803d' : '#e5e7eb';
                const dotColor = s.done ? '#fff' : '#9ca3af';
                const dotIcon = s.done ? '✓' : (i + 1);
                const labelCls = s.done ? '#15803d' : '#9ca3af';
                const connector = !isLast ?
                    '<div style="position:absolute; top:21px; left:50%; right:-50%; height:3px; background:' +
                    (s.done ? '#15803d' : '#e5e7eb') + '; z-index:0;"></div>' :
                    '';

                return '<div style="flex:1; display:flex; flex-direction:column; align-items:center; position:relative; z-index:1;">' +
                    connector +
                    '<div style="width:42px; height:42px; border-radius:50%; background:' + dotBg + '; ' +
                    'color:' + dotColor + '; display:flex; align-items:center; justify-content:center; ' +
                    'font-size:12px; font-weight:700; position:relative; z-index:1; ' +
                    (s.done ? '' : '') + '">' + dotIcon + '</div>' +
                    '<p style="font-size:10px; font-weight:700; color:' + labelCls +
                    '; margin-top:8px; text-align:center; max-width:80px;">' + s.stage + '</p>' +
                    '<p style="font-size:9px; color:#6b7280; margin-top:3px; text-align:center;">' + fmt(s
                        .date) + '</p>' +
                    '</div>';
            }).join('');
        }

        // ── Containers renderer ───────────────────────────────────────────────
        function renderContainers(containers) {
            const tbody = document.getElementById('cdm-containers-tbody');

            if (!containers || containers.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" style="text-align:center; padding:1rem; color:#9ca3af; font-size:12px;">No container details found.</td></tr>';
                return;
            }

            tbody.innerHTML = containers.map(function(c, i) {
                const gateOut = fmt(c.GateOutDate);
                const ret = fmt(c.ReturnDate);

                // Calculate demurrage days
                let demurrage = '—';
                if (c.GateOutDate && c.GateOutDate !== '0000-00-00') {
                    const endDate = (c.ReturnDate && c.ReturnDate !== '0000-00-00') ?
                        new Date(c.ReturnDate) : new Date();
                    const startDate = new Date(c.GateOutDate);
                    const days = Math.floor((endDate - startDate) / 86400000);
                    demurrage = days > 0 ?
                        '<span style="color:' + ageCls(days) + '; font-weight:700;">' + days +
                        ' days</span>' :
                        '0 days';
                }

                return '<tr style="' + (i % 2 === 0 ? '' : 'background:#f9fafb;') + '">' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' + (c
                        .ContainerNo || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (c.ContainerSize || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (c.Weight || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + gateOut + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + ret + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + demurrage + '</td>' +
                    '</tr>';
            }).join('');
        }

        // ── Manifest renderer ─────────────────────────────────────────────────
        function renderManifest(manifest) {
            const empty = document.getElementById('cdm-manifest-empty');
            const table = document.getElementById('cdm-manifest-table');
            const tbody = document.getElementById('cdm-manifest-tbody');

            if (!manifest || manifest.length === 0) {
                empty.style.display = 'block';
                table.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            table.style.display = 'table';

            tbody.innerHTML = manifest.map(function(m, i) {
                const tel = m.ConsigneeTel || '';
                const callBtn = tel ?
                    '<a href="tel:' + tel +
                    '" style="display:inline-block; padding:3px 8px; background:#185FA5; color:#fff; border-radius:4px; font-size:9px; font-weight:700; text-decoration:none; margin-right:4px;">📞 Call</a>' :
                    '';
                const smsBtn = tel ?
                    '<a href="sms:' + tel +
                    '" style="display:inline-block; padding:3px 8px; background:#15803d; color:#fff; border-radius:4px; font-size:9px; font-weight:700; text-decoration:none;">💬 SMS</a>' :
                    '';

                return '<tr style="' + (i % 2 === 0 ? '' : 'background:#f9fafb;') + '">' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' + (m.HouseBL ||
                        '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px; font-weight:600;">' + (m.ConsigneeName ||
                        '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px; color:#6b7280;">' + (tel || '—') +
                    '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (m.Description || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (m.Weight || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (m.Package || '—') + ' ' + (m.Unit ||
                        '') + '</td>' +
                    '<td style="padding:7px 10px;">' + callBtn + smsBtn + '</td>' +
                    '</tr>';
            }).join('');
        }

        // ── Payments renderer ─────────────────────────────────────────────────
        function renderPayments(payments, totalExp, totalRev) {
            const empty = document.getElementById('cdm-payments-empty');
            const table = document.getElementById('cdm-payments-table');
            const tbody = document.getElementById('cdm-payments-tbody');
            const tfoot = document.getElementById('cdm-payments-tfoot');

            if (!payments || payments.length === 0) {
                empty.style.display = 'block';
                table.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            table.style.display = 'table';

            tbody.innerHTML = payments.map(function(p, i) {
                return '<tr style="' + (i % 2 === 0 ? '' : 'background:#f9fafb;') + '">' +
                    '<td style="padding:7px 10px; font-size:12px;">' + fmt(p.Date) + '</td>' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' + (p
                        .ReceiptNo || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' + (p.HBL ||
                    '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px;">' + (p.AccountName || '—') + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px; text-align:right; color:#b91c1c;">' +
                    fmtNum(p.Expenditure) + '</td>' +
                    '<td style="padding:7px 10px; font-size:12px; text-align:right; color:#15803d;">' +
                    fmtNum(p.Revenue) + '</td>' +
                    '</tr>';
            }).join('');

            tfoot.innerHTML =
                '<tr style="background:#f3f4f6; border-top:2px solid #185FA5;">' +
                '<td colspan="4" style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right;">Totals</td>' +
                '<td style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right; color:#b91c1c;">GH₵ ' +
                fmtNum(totalExp) + '</td>' +
                '<td style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right; color:#15803d;">GH₵ ' +
                fmtNum(totalRev) + '</td>' +
                '</tr>';
        }

    })();
</script>
