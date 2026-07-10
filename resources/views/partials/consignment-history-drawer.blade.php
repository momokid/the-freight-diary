{{-- ── Backdrop ── --}}
<div id="chd-backdrop" onclick="window.ConsignmentHistory.close()"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000;"></div>

{{-- ── Drawer ── --}}
<div id="chd-drawer"
    style="display:none; position:fixed; top:0; right:0; bottom:0; width:820px; max-width:95vw; background:var(--card-bg); z-index:1001; overflow-y:auto; box-shadow:-4px 0 24px rgba(0,0,0,0.15); transition:transform 0.3s ease; transform:translateX(100%);">

    {{-- ── Header ── --}}
    <div
        style="position:sticky; top:0; z-index:10; background:#185FA5; padding:14px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <p style="font-size:var(--db-text-base); font-weight:700; color:#fff;">Consignment History</p>
            <p style="font-size:var(--db-text-xs); color:#bfdbfe; margin-top:2px;">
                Search by Bill of Lading to view full lifecycle
            </p>
        </div>
        <button onclick="window.ConsignmentHistory.close()"
            style="background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:6px; padding:6px 14px; cursor:pointer; font-size:var(--db-text-xs); font-weight:600;">
            ✕ Close
        </button>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:20px;">

        {{-- Search --}}
        <div style="margin-bottom:16px;">
            <label
                style="font-size:var(--db-text-xs); font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:6px;">
                Bill of Lading Number
            </label>
            <div style="display:flex; gap:8px;">
                <input type="text" id="chd-bl-input" placeholder="e.g. MEDUY9898550" class="form-input"
                    style="text-transform:uppercase; flex:1;"
                    onkeydown="if(event.key==='Enter') window.ConsignmentHistory.search()">
                <button onclick="window.ConsignmentHistory.search()"
                    style="padding:0 20px; background:#185FA5; color:#fff; border:none; border-radius:8px; font-size:0.85rem; font-weight:600; cursor:pointer;">
                    Search
                </button>
            </div>
            <p id="chd-search-error" class="form-error"></p>
        </div>

        {{-- Loading --}}
        <div id="chd-loading"
            style="display:none; text-align:center; padding:2rem; color:var(--text-muted); font-size:0.85rem;">
            Loading consignment history...
        </div>

        {{-- Empty state (before first search) --}}
        <div id="chd-empty-state" style="text-align:center; padding:2rem; color:var(--text-muted); font-size:0.85rem;">
            Enter a Bill of Lading number above to view its full history.
        </div>

        {{-- Results --}}
        <div id="chd-results" style="display:none;">

            {{-- Header strip --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div>
                    <p id="chd-bl"
                        style="font-size:13px; font-weight:700; font-family:monospace; color:var(--text-primary);">—</p>
                    <p id="chd-carrier-consignee" style="font-size:11px; color:var(--text-muted); margin-top:2px;">—</p>
                </div>
                <span id="chd-status-badge"
                    style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:99px;"></span>
            </div>

            {{-- Info strip --}}
            <div id="chd-info-strip"
                style="display:grid; grid-template-columns:repeat(4, 1fr); gap:8px; margin-bottom:20px;"></div>

            {{-- Timeline --}}
            <p
                style="font-size:0.75rem; font-weight:700; color:var(--text-primary); margin-bottom:10px; text-transform:uppercase; letter-spacing:0.05em;">
                Timeline</p>
            <div id="chd-timeline-stages" style="display:flex; margin-bottom:24px;"></div>

            {{-- Containers --}}
            <p
                style="font-size:0.75rem; font-weight:700; color:var(--text-primary); margin-bottom:10px; text-transform:uppercase; letter-spacing:0.05em;">
                Containers</p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <thead>
                    <tr style="background:#f3f4f6;">
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Container No</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Size</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Weight</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Gate Out</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Returned</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Demurrage</th>
                    </tr>
                </thead>
                <tbody id="chd-containers-tbody"></tbody>
            </table>

            {{-- Manifest --}}
            <p
                style="font-size:0.75rem; font-weight:700; color:var(--text-primary); margin-bottom:10px; text-transform:uppercase; letter-spacing:0.05em;">
                Manifest Breakdown</p>
            <div id="chd-manifest-empty"
                style="display:none; text-align:center; padding:1rem; color:var(--text-muted); font-size:0.8rem;">No
                manifest entries found.</div>
            <table id="chd-manifest-table"
                style="width:100%; border-collapse:collapse; margin-bottom:20px; display:none;">
                <thead>
                    <tr style="background:#f3f4f6;">
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">House BL</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Consignee</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Tel</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Description</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Weight</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Packages</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            {{-- Payments --}}
            <p
                style="font-size:0.75rem; font-weight:700; color:var(--text-primary); margin-bottom:10px; text-transform:uppercase; letter-spacing:0.05em;">
                Payments</p>
            <div id="chd-payments-empty"
                style="display:none; text-align:center; padding:1rem; color:var(--text-muted); font-size:0.8rem;">No
                payment records found.</div>
            <table id="chd-payments-table" style="width:100%; border-collapse:collapse; display:none;">
                <thead>
                    <tr style="background:#f3f4f6;">
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Date</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Receipt No</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">HBL</th>
                        <th style="padding:7px 10px; text-align:left; font-size:11px;">Account</th>
                        <th style="padding:7px 10px; text-align:right; font-size:11px;">Expenditure</th>
                        <th style="padding:7px 10px; text-align:right; font-size:11px;">Revenue</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot id="chd-payments-tfoot"></tfoot>
            </table>

        </div>

    </div>
</div>

<script src="{{ asset('js/consignment-timeline.js') }}"></script>
<script>
    (function() {
        const RESOLVE_URL = '{{ route('reports.operations.consignment-lookup.resolve') }}';
        const MODAL_URL = '{{ route('reports.operations.consignment-modal', ':id') }}';
        const CT = window.ConsignmentTimeline;

        window.ConsignmentHistory = window.ConsignmentHistory || {};

        window.ConsignmentHistory.open = function(bl) {
            document.getElementById('chd-backdrop').style.display = 'block';
            document.getElementById('chd-drawer').style.display = 'block';
            setTimeout(function() {
                document.getElementById('chd-drawer').style.transform = 'translateX(0)';
            }, 10);

            if (bl) {
                document.getElementById('chd-bl-input').value = bl;
                window.ConsignmentHistory.search();
            }
        };

        window.ConsignmentHistory.close = function() {
            document.getElementById('chd-drawer').style.transform = 'translateX(100%)';
            setTimeout(function() {
                document.getElementById('chd-backdrop').style.display = 'none';
                document.getElementById('chd-drawer').style.display = 'none';
            }, 300);
        };

        document.getElementById('chd-backdrop').addEventListener('click', window.ConsignmentHistory.close);

        window.ConsignmentHistory.search = function() {
            const bl = document.getElementById('chd-bl-input').value.trim().toUpperCase();
            const errorEl = document.getElementById('chd-search-error');
            errorEl.classList.remove('visible');

            if (!bl) {
                errorEl.textContent = 'Please enter a Bill of Lading number.';
                errorEl.classList.add('visible');
                return;
            }

            document.getElementById('chd-empty-state').style.display = 'none';
            document.getElementById('chd-results').style.display = 'none';
            document.getElementById('chd-loading').style.display = 'block';

            fetch(RESOLVE_URL + '?bl=' + encodeURIComponent(bl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (!data.success) {
                        document.getElementById('chd-loading').style.display = 'none';
                        errorEl.textContent = data.message ?? 'No consignment found for this BL.';
                        errorEl.classList.add('visible');
                        return;
                    }
                    return fetch(MODAL_URL.replace(':id', data.consignmentId), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                })
                .then(function(res) {
                    return res ? res.json() : null;
                })
                .then(function(data) {
                    document.getElementById('chd-loading').style.display = 'none';
                    if (!data) return;

                    if (!data.success) {
                        errorEl.textContent = data.message ?? 'Failed to load consignment history.';
                        errorEl.classList.add('visible');
                        return;
                    }

                    renderResults(data);
                    document.getElementById('chd-results').style.display = 'block';
                })
                .catch(function() {
                    document.getElementById('chd-loading').style.display = 'none';
                    errorEl.textContent = 'Network error. Please try again.';
                    errorEl.classList.add('visible');
                });
        };

        function renderResults(data) {
            const c = data.consignment;
            const st = CT.statusLabel(c.Status);

            document.getElementById('chd-bl').textContent = 'BL# ' + (c.MainBL || '—');
            document.getElementById('chd-carrier-consignee').textContent = (c.CarrierName || '—') + '  ·  ' + (c
                .ConsigneeName || '—');

            const badge = document.getElementById('chd-status-badge');
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
                        .DaysOverdue + ' days</span>' :
                        '<span style="color:#15803d;">On track</span>'
                },
            ];
            document.getElementById('chd-info-strip').innerHTML = infoItems.map(function(i) {
                return '<div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px;">' +
                    '<p style="font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:0.05em; margin-bottom:3px;">' +
                    i.label + '</p>' +
                    '<p style="font-size:12px; font-weight:700; color:#111827;">' + i.val + '</p>' +
                    '</div>';
            }).join('');

            CT.renderTimeline(data.timeline, 'chd-timeline-stages');
            CT.renderContainers(data.containers, 'chd-containers-tbody');
            CT.renderManifest(data.manifest, 'chd-manifest-table', 'chd-manifest-empty');
            CT.renderPayments(data.payments, data.totalExpenditure, data.totalRevenue, 'chd-payments-table',
                'chd-payments-empty', 'chd-payments-tfoot');
        }
    })();
</script>
