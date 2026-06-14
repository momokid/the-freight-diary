@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- ── Skeleton styles ── --}}
    <style>
        .db-sk {
            background: var(--border-color);
            border-radius: 4px;
            animation: dbPulse 1.5s ease-in-out infinite;
        }

        @keyframes dbPulse {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 0.2;
            }
        }

        .db-sk-title {
            height: 14px;
            width: 140px;
            margin-bottom: 14px;
        }

        .db-sk-kpi {
            height: 64px;
            border-radius: 8px;
        }

        .db-sk-chart {
            height: 90px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .db-sk-row {
            height: 12px;
            border-radius: 3px;
            margin-bottom: 8px;
        }

        .db-sk-row:nth-child(even) {
            width: 85%;
        }

        .db-sk-big {
            height: 28px;
            width: 60%;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .db-sk-line {
            height: 10px;
            border-radius: 3px;
        }

        .db-sk-prog {
            height: 8px;
            border-radius: 4px;
        }
    </style>

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- ── Page header ── --}}
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <p id="db-last-updated" style="font-size:0.75rem; color:var(--text-muted); margin:0;">
                Loading widgets...
            </p>
            <button onclick="window.DashboardApp.refreshAll()"
                style="display:flex; align-items:center; gap:6px; padding:6px 14px;
                       background:#185FA5; color:#fff; border:none; border-radius:6px;
                       font-size:12px; font-weight:600; cursor:pointer;">
                <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11
                                                                                   11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh All
            </button>
        </div>

        {{-- ── Widget: Consignment Tracker ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
            <div id="widget-tracker">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:8px; margin-bottom:12px;">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="db-sk db-sk-kpi"></div>
                        @endfor
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            @for ($i = 0; $i < 5; $i++)
                                <div class="db-sk db-sk-row"></div>
                            @endfor
                        </div>
                        <div>
                            @for ($i = 0; $i < 5; $i++)
                                <div class="db-sk db-sk-row"></div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Widget: Financial Performance + Cash Position ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('AccountingReport'))
            <div id="widget-financial">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:10px;">
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="db-sk db-sk-kpi"></div>
                                @endfor
                            </div>
                            <div class="db-sk db-sk-chart"></div>
                        </div>
                        <div>
                            <div class="db-sk db-sk-title" style="width:100px; margin-bottom:8px;"></div>
                            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px;">
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="db-sk" style="height:52px; border-radius:8px;"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Widgets: Collections + Disbursements (side by side) ── --}}
        @php
            $showCollections = isset($userAuth) && $userAuth->hasPermission('ManagementReport');
            $showDisbursements = isset($userAuth) && $userAuth->hasPermission('DisbursementApproval');
        @endphp

        @if ($showCollections || $showDisbursements)
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:1.25rem;">

                @if ($showCollections)
                    <div id="widget-collections">
                        <div class="card">
                            <div class="db-sk db-sk-title"></div>
                            <div class="db-sk db-sk-big"></div>
                            @for ($i = 0; $i < 4; $i++)
                                <div class="db-sk db-sk-row"></div>
                            @endfor
                        </div>
                    </div>
                @endif

                @if ($showDisbursements)
                    <div id="widget-disbursements">
                        <div class="card">
                            <div class="db-sk db-sk-title"></div>
                            <div class="db-sk db-sk-big"></div>
                            @for ($i = 0; $i < 3; $i++)
                                <div class="db-sk db-sk-row"></div>
                            @endfor
                        </div>
                    </div>
                @endif

            </div>
        @endif

        {{-- ── Widget: Recent Transactions ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('PaymentTransaction'))
            <div id="widget-transactions">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div class="db-sk db-sk-row" style="margin-bottom:10px;"></div>
                    @for ($i = 0; $i < 6; $i++)
                        <div class="db-sk db-sk-row"></div>
                    @endfor
                </div>
            </div>
        @endif

        {{-- ── Widget: Vision 5:29 ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('ManagementReport'))
            <div id="widget-vision">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div style="display:flex; flex-direction:column; gap:10px; padding:4px 0;">
                        @for ($i = 0; $i < 2; $i++)
                            <div style="display:grid; grid-template-columns:150px 1fr 50px; gap:8px; align-items:center;">
                                <div class="db-sk db-sk-line"></div>
                                <div class="db-sk db-sk-prog"></div>
                                <div class="db-sk db-sk-line"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- ── Disbursements Drawer ── --}}
    <div id="disb-drawer-overlay" onclick="window.DashboardApp.closeDisbursementsDrawer()"
        style="display:none; position:fixed; inset:0;
               background:rgba(0,0,0,0.4); z-index:1000;">
    </div>

    <div id="disb-drawer"
        style="position:fixed; top:0; right:0; height:100vh;
               width:680px; max-width:95vw; background:var(--card-bg);
               box-shadow:-4px 0 24px rgba(0,0,0,0.15); z-index:1001;
               display:flex; flex-direction:column;
               transform:translateX(100%); transition:transform 0.3s ease;
               pointer-events:none;">

        <div
            style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                    display:flex; align-items:center; justify-content:space-between;
                    flex-shrink:0;">
            <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
                Pending Disbursements
                <span style="font-size:12px; font-weight:400; color:var(--text-muted); margin-left:6px;">
                    owned consignments with no cost entry
                </span>
            </p>
            <button onclick="window.DashboardApp.closeDisbursementsDrawer()"
                style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:12px;
                       color:var(--text-muted); cursor:pointer;">
                ✕ Close
            </button>
        </div>

        <div id="disb-drawer-content" style="flex:1; overflow-y:auto; padding:1rem 1.25rem;">
            <p style="font-size:12px; color:var(--text-muted);">Loading...</p>
        </div>

    </div>

    {{-- ── Pending Consignments Drawer ── --}}
    <div id="pending-drawer-overlay" onclick="window.DashboardApp.closePendingDrawer()"
        style="display:none; position:fixed; inset:0;
           background:rgba(0,0,0,0.4); z-index:1000;">
    </div>

    <div id="pending-drawer"
        style="position:fixed; top:0; right:0; height:100vh;
           width:800px; max-width:95vw; background:var(--card-bg);
           box-shadow:-4px 0 24px rgba(0,0,0,0.15); z-index:1001;
           display:flex; flex-direction:column;
           transform:translateX(100%); transition:transform 0.3s ease;
           pointer-events:none;">

        <div
            style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;
                flex-shrink:0;">
            <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
                Pending Consignments
                <span style="font-size:12px; font-weight:400; color:var(--text-muted); margin-left:6px;">
                    all pending — update ETA as needed
                </span>
            </p>
            <button onclick="window.DashboardApp.closePendingDrawer()"
                style="background:none; border:0.5px solid var(--border-color);
                   border-radius:6px; padding:3px 10px; font-size:12px;
                   color:var(--text-muted); cursor:pointer;">
                ✕ Close
            </button>
        </div>

        <div id="pending-drawer-content" style="flex:1; overflow-y:auto; padding:1rem 1.25rem;">
            <p style="font-size:12px; color:var(--text-muted);">Loading...</p>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        window.DashboardApp = {

            csrfToken: '{{ csrf_token() }}',

            urls: {
                refresh: '{{ route('dashboard.refresh') }}',
                gateOut: '{{ url('dashboard/gate-out') }}',
                containerClear: '{{ url('dashboard/container-clear') }}',
                drawerDisbs: '{{ route('dashboard.drawer.disbursements') }}',
                drawerPending: '{{ route('dashboard.drawer.pending-consignments') }}',
                updateEta: '{{ route('dashboard.eta.update') }}',
            },

            widgets: [
                @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
                    'tracker',
                @endif
                @if (isset($userAuth) && $userAuth->hasPermission('AccountingReport'))
                    'financial',
                @endif
                @if (isset($userAuth) && $userAuth->hasPermission('ManagementReport'))
                    'collections',
                    'vision',
                @endif
                @if (isset($userAuth) && $userAuth->hasPermission('DisbursementApproval'))
                    'disbursements',
                @endif
                @if (isset($userAuth) && $userAuth->hasPermission('PaymentTransaction'))
                    'transactions',
                @endif
            ],

            openPendingDrawer() {
                const drawer = document.getElementById('pending-drawer');
                const overlay = document.getElementById('pending-drawer-overlay');
                const content = document.getElementById('pending-drawer-content');

                overlay.style.display = 'block';
                drawer.style.pointerEvents = 'auto';
                requestAnimationFrame(() => {
                    drawer.style.transform = 'translateX(0)';
                });

                content.innerHTML =
                    '<p style="font-size:12px; color:var(--text-muted); padding:12px 0;">Loading...</p>';

                fetch(this.urls.drawerPending, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(({
                        rows,
                        canEdit
                    }) => {
                        if (!rows.length) {
                            content.innerHTML =
                                '<p style="font-size:12px; color:var(--text-muted); text-align:center; padding:24px 0;">No pending consignments found.</p>';
                            return;
                        }

                        let html = `
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">BL</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">Consignee</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">Destination</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">ETA</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">Days</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">Status</th>
                            ${canEdit ? '<th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">Action</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>
            `;

                        rows.forEach(row => {
                            const days = parseInt(row.ETADays);
                            const etaDate = row.ETA ? row.ETA.substring(0, 10) : '';

                            let daysText = '—';
                            let daysStyle = 'color:var(--text-muted);';
                            if (etaDate) {
                                if (days < 0) {
                                    daysText = `${Math.abs(days)}d overdue`;
                                    daysStyle = 'color:#A32D2D; font-weight:600;';
                                } else if (days === 0) {
                                    daysText = 'Today';
                                    daysStyle = 'color:#3B6D11; font-weight:600;';
                                } else if (days <= 3) {
                                    daysText = `${days}d`;
                                    daysStyle = 'color:#854F0B; font-weight:600;';
                                } else {
                                    daysText = `${days}d`;
                                }
                            }

                            const statusBg = days < 0 ? 'background:#E6F1FB; color:#0C447C;' :
                                'background:#FAEEDA; color:#854F0B;';
                            const statusLabel = days < 0 ? 'In Harbor' : 'Not Arrived';

                            const locked = row.DisbursementApproved == 1;

                            const etaCell = (canEdit && !locked) ?
                                `<input type="date" value="${etaDate}"
                                style="border:0.5px solid var(--border-color); border-radius:5px;
                                        padding:3px 6px; font-size:12px; width:130px;">` :
                                `<span style="font-size:12px; color:var(--text-primary);">${etaDate || '—'}</span>`;

                            const actionCell = (canEdit && !locked) ?
                                `<button onclick="window.DashboardApp.saveEta(${row.ConsignmentID}, '${row.BL}', this)"
                                    style="background:#185FA5; color:#fff; border:none; border-radius:5px;
                                        padding:3px 10px; font-size:12px; font-weight:600; cursor:pointer;">
                                    Save
                                </button>` :
                                locked ?
                                `<span style="font-size:11px; color:var(--text-muted);">🔒 Locked</span>` :
                                '';

                            html += `
                    <tr style="border-bottom:0.5px solid var(--border-color);">
                        <td style="padding:8px 12px; font-family:monospace; font-size:13px; color:var(--text-primary); white-space:nowrap;">${row.BL}</td>
                        <td style="padding:8px 12px; font-size:12px; color:var(--text-primary);">${row.ConsigneeName}</td>
                        <td style="padding:8px 12px; font-size:12px; color:var(--text-muted);">${row.Destination || '—'}</td>
                        <td style="padding:8px 12px; text-align:center;" data-eta-cell>${etaCell}</td>
                        <td style="padding:8px 12px; font-size:12px; text-align:center; ${daysStyle}" data-days-cell>${daysText}</td>
                        <td style="padding:8px 12px; text-align:center;" data-status-cell>
                            <span style="${statusBg} font-size:11px; font-weight:600; border-radius:10px; padding:2px 10px;">${statusLabel}</span>
                        </td>
                        ${canEdit ? `<td style="padding:8px 12px; text-align:center;">${actionCell}</td>` : ''}
                    </tr>
                `;
                        });

                        html += '</tbody></table>';
                        content.innerHTML = html;
                    })
                    .catch(e => {
                        content.innerHTML =
                            '<p style="font-size:12px; color:#A32D2D; padding:12px 0;">Failed to load data. Please try again.</p>';
                        console.error('[Dashboard] Pending drawer error:', e);
                    });
            },

            closePendingDrawer() {
                const drawer = document.getElementById('pending-drawer');
                const overlay = document.getElementById('pending-drawer-overlay');
                drawer.style.transform = 'translateX(100%)';
                drawer.style.pointerEvents = 'none';
                overlay.style.display = 'none';
            },

            saveEta(consignmentId, bl, btn) {
                const row = btn.closest('tr');
                const input = row.querySelector('input[type="date"]');
                const eta = input ? input.value : '';

                if (!eta) {
                    alert('Please select a valid ETA date.');
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Saving...';

                fetch(this.urls.updateEta, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            consignmentId,
                            bl,
                            eta
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const days = data.etaDays;

                            // Update days cell
                            const daysCell = row.querySelector('[data-days-cell]');
                            if (daysCell) {
                                let daysText = `${days}d`;
                                let daysStyle = 'color:var(--text-muted); font-weight:normal;';
                                if (days < 0) {
                                    daysText = `${Math.abs(days)}d overdue`;
                                    daysStyle = 'color:#A32D2D; font-weight:600;';
                                } else if (days === 0) {
                                    daysText = 'Today';
                                    daysStyle = 'color:#3B6D11; font-weight:600;';
                                } else if (days <= 3) {
                                    daysStyle = 'color:#854F0B; font-weight:600;';
                                }
                                daysCell.textContent = daysText;
                                daysCell.setAttribute('style',
                                    `padding:8px 12px; font-size:12px; text-align:center; ${daysStyle}`);
                            }

                            // Update status badge
                            const statusCell = row.querySelector('[data-status-cell]');
                            if (statusCell) {
                                const bg = days >= 0 ? 'background:#FAEEDA; color:#854F0B;' :
                                    'background:#E6F1FB; color:#0C447C;';
                                const label = days >= 0 ? 'Not Arrived' : 'In Harbor';
                                statusCell.innerHTML =
                                    `<span style="${bg} font-size:11px; font-weight:600; border-radius:10px; padding:2px 10px;">${label}</span>`;
                            }

                            btn.disabled = false;
                            btn.textContent = '✓ Saved';
                            btn.style.background = '#3B6D11';
                            setTimeout(() => {
                                btn.textContent = 'Save';
                                btn.style.background = '#185FA5';
                            }, 2000);

                            this.loadWidget('tracker');
                        } else {
                            btn.disabled = false;
                            btn.textContent = 'Save';
                            alert('Update failed. Please try again.');
                        }
                    })
                    .catch(e => {
                        console.error('[Dashboard] ETA save error:', e);
                        btn.disabled = false;
                        btn.textContent = 'Save';
                        alert('An error occurred. Please try again.');
                    });
            },

            _autoRefreshTimer: null,
            hasEditData: {{ isset($userAuth) && $userAuth->hasPermission('EditData') ? 'true' : 'false' }},

            // ── Initialise ────────────────────────────────────────────────────────────
            init() {
                this.widgets.forEach(w => this.loadWidget(w));
                this._autoRefreshTimer = setInterval(() => this.refreshAll(), 300000);
            },

            // ── Load / refresh a single widget ────────────────────────────────────────
            async loadWidget(name, params = {}) {
                const el = document.getElementById('widget-' + name);
                if (!el) return;
                try {
                    const res = await fetch(this._buildUrl(name, params), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) {
                        el.innerHTML = await res.text();
                        this._initCharts(el);
                        this._updateTimestamp();
                    }
                } catch (e) {
                    console.error('[Dashboard] loadWidget failed:', name, e);
                }
            },

            // ── Refresh all permitted widgets ─────────────────────────────────────────
            refreshAll() {
                this.widgets.forEach(w => this.loadWidget(w));
            },

            // ── Tracker pagination ────────────────────────────────────────────────────
            trackerPage(leftPage, rightPage) {
                this.loadWidget('tracker', {
                    left_page: leftPage,
                    right_page: rightPage
                });
            },

            // ── Gate-Out ──────────────────────────────────────────────────────────────
            async gateOut(consignmentId, bl, btn) {
                if (!confirm('Confirm gate-out for all containers under BL ' + bl + '?')) return;
                btn.disabled = true;
                btn.textContent = 'Processing...';
                try {
                    const res = await fetch(
                        this.urls.gateOut + '/' + consignmentId + '/' + encodeURIComponent(bl), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        }
                    );
                    if (res.ok) {
                        this.loadWidget('tracker');
                    } else {
                        alert('Gate-out failed. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Gate-Out';
                    }
                } catch (e) {
                    alert('An error occurred. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Gate-Out';
                }
            },

            // ── Container Clear ───────────────────────────────────────────────────────
            async containerClear(consignmentId, containerNo, btn) {
                if (!confirm('Confirm return of container ' + containerNo + '?')) return;
                btn.disabled = true;
                btn.textContent = 'Clearing...';
                try {
                    const res = await fetch(
                        this.urls.containerClear + '/' + consignmentId + '/' + encodeURIComponent(
                            containerNo), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        }
                    );
                    if (res.ok) {
                        this.loadWidget('tracker');
                    } else {
                        alert('Clear failed. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Clear';
                    }
                } catch (e) {
                    alert('An error occurred. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Clear';
                }
            },

            // ── Disbursements Drawer ──────────────────────────────────────────────────
            openDisbursementsDrawer() {
                const drawer = document.getElementById('disb-drawer');
                const overlay = document.getElementById('disb-drawer-overlay');
                const content = document.getElementById('disb-drawer-content');

                overlay.style.display = 'block';
                drawer.style.pointerEvents = 'auto';
                requestAnimationFrame(() => {
                    drawer.style.transform = 'translateX(0)';
                });

                content.innerHTML =
                    '<p style="font-size:12px; color:var(--text-muted); padding:12px 0;">Loading...</p>';

                fetch(this.urls.drawerDisbs, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(rows => {
                        if (!rows.length) {
                            content.innerHTML =
                                '<p style="font-size:12px; color:var(--text-muted); text-align:center; padding:24px 0;">No pending disbursements found.</p>';
                            return;
                        }

                        const statusMap = {
                            1: {
                                label: 'Arrived',
                                bg: '#FBF0DA',
                                color: '#92600E'
                            },
                            2: {
                                label: 'In Harbor',
                                bg: '#FCEBEB',
                                color: '#A32D2D'
                            },
                        };

                        let html = `
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#185FA5; color:#fff;">
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">BL</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">Consignee</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:left; font-weight:600;">Destination</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">Status</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">ETA</th>
                            <th style="padding:8px 12px; font-size:12px; text-align:center; font-weight:600;">Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                        rows.forEach(row => {
                            const st = statusMap[row.Status] || {
                                label: 'Unknown',
                                bg: '#f5f5f5',
                                color: '#888'
                            };
                            const days = parseInt(row.DaysOverdue);
                            const etaDate = row.ETA ? row.ETA.substring(0, 10) : '—';
                            const overdueText = isNaN(days) ? '—' : `${days}d overdue`;

                            html += `
                    <tr style="border-bottom:0.5px solid var(--border-color);">
                        <td style="padding:8px 12px; font-family:monospace; font-size:13px;
                                   color:var(--text-primary); white-space:nowrap;">${row.BL}</td>
                        <td style="padding:8px 12px; font-size:12px; color:var(--text-primary);">${row.ConsigneeName}</td>
                        <td style="padding:8px 12px; font-size:12px; color:var(--text-muted);">${row.Destination || '—'}</td>
                        <td style="padding:8px 12px; text-align:center;">
                            <span style="background:${st.bg}; color:${st.color}; font-size:11px;
                                         font-weight:600; border-radius:10px; padding:2px 10px;">
                                ${st.label}
                            </span>
                        </td>
                        <td style="padding:8px 12px; font-size:12px; text-align:center;
                                   color:var(--text-muted);">${etaDate}</td>
                        <td style="padding:8px 12px; font-size:12px; text-align:center;
                                   color:#A32D2D; font-weight:600;">${overdueText}</td>
                    </tr>
                `;
                        });

                        html += '</tbody></table>';
                        content.innerHTML = html;
                    })
                    .catch(e => {
                        content.innerHTML =
                            '<p style="font-size:12px; color:#A32D2D; padding:12px 0;">Failed to load data. Please try again.</p>';
                        console.error('[Dashboard] Drawer load error:', e);
                    });
            },

            closeDisbursementsDrawer() {
                const drawer = document.getElementById('disb-drawer');
                const overlay = document.getElementById('disb-drawer-overlay');
                drawer.style.transform = 'translateX(100%)';
                drawer.style.pointerEvents = 'none';
                overlay.style.display = 'none';
            },

            // ── Chart initialisation (donut + bar) ────────────────────────────────────
            _initCharts(container) {
                if (typeof Chart === 'undefined') return;

                container.querySelectorAll('canvas[data-donut]').forEach(canvas => {
                    const existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                    try {
                        const cfg = JSON.parse(canvas.dataset.donut);
                        new Chart(canvas, {
                            type: 'doughnut',
                            data: {
                                labels: cfg.labels,
                                datasets: [{
                                    data: cfg.data,
                                    backgroundColor: cfg.colors,
                                    borderWidth: 2,
                                    borderColor: 'transparent'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                size: 11
                                            },
                                            padding: 10,
                                            usePointStyle: true,
                                            pointStyleWidth: 10
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: ctx => ' GHS ' + ctx.parsed.toLocaleString('en-GH', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            })
                                        }
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('[Dashboard] Donut chart init error:', e);
                    }
                });

                container.querySelectorAll('canvas[data-bar]').forEach(canvas => {
                    const existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                    try {
                        const cfg = JSON.parse(canvas.dataset.bar);
                        new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: cfg.labels,
                                datasets: [{
                                        label: 'Revenue',
                                        data: cfg.revenue,
                                        backgroundColor: '#C0DD97',
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Expenditure',
                                        data: cfg.expenditure,
                                        backgroundColor: '#F7C1C1',
                                        borderRadius: 4
                                    },
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                size: 11
                                            },
                                            padding: 10,
                                            usePointStyle: true,
                                            pointStyleWidth: 10
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: ctx => ' GHS ' + ctx.parsed.y.toLocaleString(
                                                'en-GH', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                })
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 11
                                            }
                                        }
                                    },
                                    y: {
                                        grid: {
                                            color: 'rgba(0,0,0,0.05)'
                                        },
                                        ticks: {
                                            font: {
                                                size: 10
                                            },
                                            callback: val => val >= 1000 ? (val / 1000).toFixed(0) +
                                                'k' : val
                                        }
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('[Dashboard] Bar chart init error:', e);
                    }
                });
            },

            // ── Helpers ───────────────────────────────────────────────────────────────
            _buildUrl(widget, params) {
                const url = new URL(this.urls.refresh, window.location.origin);
                url.searchParams.set('widget', widget);
                Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
                return url.toString();
            },

            _updateTimestamp() {
                const el = document.getElementById('db-last-updated');
                if (!el) return;
                const now = new Date();
                el.textContent = 'Last refreshed: ' + now.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.DashboardApp.init();
        });
    </script>
@endpush
