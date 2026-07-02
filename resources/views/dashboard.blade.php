@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <style>
        /* ── Font scale ─────────────────────────────────────────── */
        :root {
            --db-text-xs: 0.8rem;
            --db-text-sm: 0.875rem;
            --db-text-base: 1rem;
            --db-text-lg: 1.125rem;
            --db-val: 2rem;
        }

        /* ── Skeleton ───────────────────────────────────────────── */
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
            height: 72px;
            border-radius: 8px;
        }

        .db-sk-chart {
            height: 120px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .db-sk-row {
            height: 14px;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .db-sk-row:nth-child(even) {
            width: 85%;
        }

        .db-sk-big {
            height: 32px;
            width: 60%;
            margin-bottom: 12px;
            border-radius: 6px;
        }

        .db-sk-line {
            height: 12px;
            border-radius: 3px;
        }

        .db-sk-prog {
            height: 10px;
            border-radius: 4px;
        }

        .db-sk-card {
            height: 90px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        /* ── Hero grid ──────────────────────────────────────────── */
        .db-hero {
            display: grid;
            grid-template-columns: 70fr 30fr;
            gap: 1.25rem;
            align-items: start;
        }

        .db-hero>* {
            height: 520px;
        }

        @media (max-width: 900px) {
            .db-hero {
                grid-template-columns: 1fr;
            }
        }

        /* ── Tracker cards ──────────────────────────────────────── */
        .tracker-card {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            padding: 0.875rem 1rem;
            border-left-width: 4px;
            border-left-style: solid;
            transition: box-shadow 0.2s;
        }

        .tracker-card:hover {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .tracker-card.priority-1 {
            border-left-color: #22c55e;
        }

        .tracker-card.priority-2 {
            border-left-color: #ef4444;
        }

        .tracker-card.priority-3 {
            border-left-color: #3b82f6;
        }

        .tracker-card.priority-4 {
            border-left-color: #f59e0b;
        }

        .tracker-card.priority-5 {
            border-left-color: #a855f7;
        }

        .tracker-card.priority-6 {
            border-left-color: #0d9488;
        }

        .tracker-bl {
            font-family: monospace;
            font-size: var(--db-text-base);
            font-weight: 700;
            color: var(--text-primary);
        }

        .tracker-consignee {
            font-size: var(--db-text-sm);
            color: var(--text-primary);
            margin-top: 2px;
        }

        .tracker-meta {
            font-size: var(--db-text-xs);
            color: var(--text-muted);
            margin-top: 4px;
        }

        .tracker-badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 2px 10px;
            letter-spacing: 0.02em;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-amber {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .badge-teal {
            background: #ccfbf1;
            color: #0f766e;
        }

        .tracker-action-btn {
            border: none;
            border-radius: 6px;
            padding: 5px 14px;
            font-size: var(--db-text-xs);
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.15s;
        }

        .tracker-action-btn:hover {
            opacity: 0.85;
        }

        .tracker-action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-gateout {
            background: #22c55e;
            color: #fff;
        }

        .btn-show {
            background: #185FA5;
            color: #fff;
        }

        .btn-return {
            background: #a855f7;
            color: #fff;
        }

        /* ── Container accordion ────────────────────────────────── */
        .container-accordion {
            margin-top: 0.625rem;
            border-top: 1px dashed var(--border-color);
            padding-top: 0.625rem;
        }

        .container-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 0.5px solid var(--border-color);
            font-size: var(--db-text-xs);
            color: var(--text-primary);
        }

        .container-row:last-child {
            border-bottom: none;
        }

        .container-no {
            font-family: monospace;
            font-size: var(--db-text-sm);
            font-weight: 600;
        }

        /* ── Chart panel ────────────────────────────────────────── */
        .chart-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .chart-panel-title {
            font-size: var(--db-text-base);
            font-weight: 700;
            color: var(--text-primary);
        }

        .chart-panel-subtitle {
            font-size: var(--db-text-xs);
            color: var(--text-muted);
            margin-top: 2px;
        }

        .chart-live-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #22c55e;
        }

        .chart-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: livePulse 1.5s ease-in-out infinite;
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.75);
            }
        }

        /* ── Tracker grid ───────────────────────────────────────── */
        .tracker-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.75rem;
        }

        @media (max-width: 1200px) {
            .tracker-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .tracker-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- ── Page header ── --}}
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <p id="db-last-updated" style="font-size:var(--db-text-xs); color:var(--text-muted); margin:0;">
                Loading widgets...
            </p>
            <button onclick="window.DashboardApp.refreshAll()"
                style="display:flex; align-items:center; gap:6px; padding:7px 16px;
                       background:#185FA5; color:#fff; border:none; border-radius:6px;
                       font-size:var(--db-text-xs); font-weight:600; cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0
                                                       004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003
                                                       8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh All
            </button>
        </div>

        {{-- ── Hero row: Chart (70%) + Recent Transactions (30%) ── --}}
        <div class="db-hero">

            {{-- Chart panel --}}
            @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
                <div class="card" style="padding:1.25rem;">
                    <div class="chart-panel-header">
                        <div>
                            <div class="chart-panel-title">Container Registration</div>
                            <div class="chart-panel-subtitle">12-month rolling volume</div>
                        </div>
                        <div class="chart-live-badge">
                            <span class="chart-live-dot"></span>
                            LIVE
                        </div>
                    </div>
                    <div id="widget-chart" style="position:relative; height:380px;">
                        <div class="db-sk" style="height:100%; border-radius:8px;"></div>
                    </div>
                </div>
            @endif

            {{-- Recent Transactions panel --}}
            @if (isset($userAuth) && $userAuth->hasPermission('PaymentTransaction'))
                <div id="widget-transactions" style="height:520px; overflow-y:auto;">
                    <div class="card" style="height:100%;">
                        <div class="db-sk db-sk-title"></div>
                        <div class="db-sk db-sk-row" style="margin-bottom:10px;"></div>
                        @for ($i = 0; $i < 6; $i++)
                            <div class="db-sk db-sk-row"></div>
                        @endfor
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Consignment Tracker (full width) ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
            <div class="card" style="padding:1.25rem;">
                <div
                    style="display:flex; align-items:center;
                            justify-content:space-between; margin-bottom:1rem;">
                    <div>
                        <div
                            style="font-size:var(--db-text-base); font-weight:700;
                                    color:var(--text-primary);">
                            Consignment Tracker
                        </div>
                        <div
                            style="font-size:var(--db-text-xs); color:var(--text-muted);
                                    margin-top:2px;">
                            Active consignments needing action
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        {{-- Search --}}
                        <div style="position:relative;">
                            <input id="tracker-search" type="text" placeholder="Search BL, consignee or destination..."
                                oninput="window.DashboardApp.searchTracker(this.value)"
                                style="width:280px; padding:7px 32px 7px 10px;
                                       border:1px solid var(--border-color);
                                       border-radius:8px; font-size:var(--db-text-xs);
                                       color:var(--text-primary); background:var(--content-bg);
                                       box-sizing:border-box; outline:none;"
                                onfocus="this.style.borderColor='#185FA5'"
                                onblur="this.style.borderColor='var(--border-color)'">
                            <button id="tracker-search-clear" onclick="window.DashboardApp.clearTrackerSearch()"
                                style="display:none; position:absolute; right:8px; top:50%;
                                       transform:translateY(-50%); background:none; border:none;
                                       font-size:0.875rem; color:var(--text-muted); cursor:pointer;
                                       line-height:1; padding:2px 4px;">
                                ✕
                            </button>
                        </div>
                        {{-- Count badge --}}
                        <button id="tracker-count-badge" onclick="window.DashboardApp.openPendingDrawer()"
                            style="font-size:0.72rem; font-weight:700; background:#185FA5;
                                   color:#fff; border:none; border-radius:20px; padding:5px 14px;
                                   cursor:pointer; transition:opacity 0.15s;"
                            onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
                            ...
                        </button>
                    </div>
                </div>

                {{-- Search result label --}}
                <div id="tracker-search-label"
                    style="display:none; font-size:var(--db-text-xs);
                           color:var(--text-muted); margin-bottom:0.75rem;">
                </div>

                {{-- Tracker cards --}}
                <div id="widget-tracker" style="display:flex; flex-direction:column; gap:0.625rem;">
                    @for ($i = 0; $i < 10; $i++)
                        <div class="db-sk db-sk-card"></div>
                    @endfor
                </div>

                {{-- Pagination --}}
                <div id="tracker-pagination"
                    style="display:none; margin-top:1rem;
                           align-items:center; justify-content:center; gap:1rem;">
                    <button onclick="window.DashboardApp.prevTrackerPage()" id="tracker-prev-btn"
                        style="padding:6px 16px; border-radius:6px;
                               border:1px solid var(--border-color);
                               background:var(--card-bg); color:var(--text-primary);
                               font-size:var(--db-text-xs); cursor:pointer;">
                        ◀ Prev
                    </button>
                    <span id="tracker-page-label"
                        style="font-size:var(--db-text-xs);
                               color:var(--text-muted); font-weight:600;">
                        Page 1
                    </span>
                    <button onclick="window.DashboardApp.nextTrackerPage()" id="tracker-next-btn"
                        style="padding:6px 16px; border-radius:6px;
                               border:1px solid var(--border-color);
                               background:var(--card-bg); color:var(--text-primary);
                               font-size:var(--db-text-xs); cursor:pointer;">
                        Next ▶
                    </button>
                </div>
            </div>
        @endif

        {{-- ── Widget: Financial Performance ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('AccountingReport'))
            <div id="widget-financial">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <div
                                style="display:grid; grid-template-columns:repeat(3,1fr);
                                        gap:8px; margin-bottom:10px;">
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
                                    <div class="db-sk" style="height:60px; border-radius:8px;"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Widgets: Collections + Disbursements ── --}}
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

        {{-- ── Widget: Vision 5:29 ── --}}
        @if (isset($userAuth) && $userAuth->hasPermission('ManagementReport'))
            <div id="widget-vision">
                <div class="card">
                    <div class="db-sk db-sk-title"></div>
                    <div style="display:flex; flex-direction:column; gap:10px; padding:4px 0;">
                        @for ($i = 0; $i < 2; $i++)
                            <div
                                style="display:grid;
                                        grid-template-columns:150px 1fr 50px;
                                        gap:8px; align-items:center;">
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
               background:rgba(0,0,0,0.4); z-index:1000;"></div>

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
            <p
                style="font-size:var(--db-text-sm); font-weight:700;
                      color:var(--text-primary); margin:0;">
                Pending Disbursements
                <span
                    style="font-size:var(--db-text-xs); font-weight:400;
                             color:var(--text-muted); margin-left:6px;">
                    owned consignments with no cost entry
                </span>
            </p>
            <button onclick="window.DashboardApp.closeDisbursementsDrawer()"
                style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:4px 12px;
                       font-size:var(--db-text-xs); color:var(--text-muted); cursor:pointer;">
                ✕ Close
            </button>
        </div>
        <div id="disb-drawer-content" style="flex:1; overflow-y:auto; padding:1rem 1.25rem;">
            <p style="font-size:var(--db-text-xs); color:var(--text-muted);">Loading...</p>
        </div>
    </div>

    {{-- ── Pending Consignments Drawer ── --}}
    <div id="pending-drawer-overlay" onclick="window.DashboardApp.closePendingDrawer()"
        style="display:none; position:fixed; inset:0;
               background:rgba(0,0,0,0.4); z-index:1000;"></div>

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
            <p
                style="font-size:var(--db-text-sm); font-weight:700;
                      color:var(--text-primary); margin:0;">
                All Active Consignments
                <span
                    style="font-size:var(--db-text-xs); font-weight:400;
                             color:var(--text-muted); margin-left:6px;">
                    Status 1 &amp; 2 — edit ETA where needed
                </span>
            </p>
            <button onclick="window.DashboardApp.closePendingDrawer()"
                style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:4px 12px;
                       font-size:var(--db-text-xs); color:var(--text-muted); cursor:pointer;">
                ✕ Close
            </button>
        </div>
        <div id="pending-drawer-content" style="flex:1; overflow-y:auto; padding:1rem 1.25rem;">
            <p style="font-size:var(--db-text-xs); color:var(--text-muted);">Loading...</p>
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
                chart: '{{ route('dashboard.chart') }}',
                tracker: '{{ route('dashboard.tracker') }}',
                trackerContainers: '{{ route('dashboard.tracker.containers') }}',
                gateOut: '{{ url('dashboard/gate-out') }}',
                containerClear: '{{ url('dashboard/container-clear') }}',
                drawerDisbs: '{{ route('dashboard.drawer.disbursements') }}',
                drawerPending: '{{ route('dashboard.drawer.pending-consignments') }}',
                updateEta: '{{ route('dashboard.eta.update') }}',
                sendNotification: '{{ route('consignments.send-notification') }}',
            },

            widgets: [
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

            hasEditData: {{ isset($userAuth) && $userAuth->hasPermission('EditData') ? 'true' : 'false' }},

            // Tracker state
            _trackerPage: 1,
            _trackerTotalPages: 1,
            _trackerPerPage: 10,

            // Chart instance
            _containerChart: null,
            _autoRefreshTimer: null,

            // ── Init ──────────────────────────────────────────────────────────────
            init() {
                this.widgets.forEach(w => this.loadWidget(w));
                this.loadChart();
                this.loadTracker(1, false);
                this._autoRefreshTimer = setInterval(() => this.refreshAll(), 300000);
            },

            // ── Refresh all ───────────────────────────────────────────────────────
            refreshAll() {
                this.widgets.forEach(w => this.loadWidget(w));
                this.loadChart();
                this.loadTracker(1, false);
            },

            // ── Load a standard AJAX widget ───────────────────────────────────────
            async loadWidget(name, params = {}) {
                const el = document.getElementById('widget-' + name);
                if (!el) return;
                try {
                    const res = await fetch(this._buildRefreshUrl(name, params), {
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

            // ── Load chart data and render/update ─────────────────────────────────
            async loadChart() {
                try {
                    const res = await fetch(this.urls.chart, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this._renderContainerChart(data.labels, data.values);
                    this._updateTimestamp();
                } catch (e) {
                    console.error('[Dashboard] Chart load failed:', e);
                }
            },

            // ── Render or update the 12-month container chart ─────────────────────
            _renderContainerChart(labels, values) {
                const container = document.getElementById('widget-chart');
                if (!container) return;

                if (!this._containerChart) {
                    container.innerHTML =
                        '<canvas id="container-chart-canvas" style="width:100%;height:100%;"></canvas>';
                }

                const canvas = document.getElementById('container-chart-canvas');
                if (!canvas) return;

                if (this._containerChart) {
                    this._containerChart.data.labels = labels;
                    this._containerChart.data.datasets[0].data = values;
                    this._containerChart.data.datasets[1].data = values;
                    this._containerChart.update('active');
                    return;
                }

                const ctx = canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(24, 95, 165, 0.35)');
                gradient.addColorStop(0.6, 'rgba(24, 95, 165, 0.08)');
                gradient.addColorStop(1, 'rgba(24, 95, 165, 0.0)');

                this._containerChart = new Chart(ctx, {
                    data: {
                        labels,
                        datasets: [{
                                type: 'line',
                                label: 'Volume',
                                data: values,
                                fill: true,
                                backgroundColor: gradient,
                                borderColor: '#185FA5',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#185FA5',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                order: 1,
                            },
                            {
                                type: 'bar',
                                label: 'Containers',
                                data: values,
                                backgroundColor: 'rgba(24, 95, 165, 0.12)',
                                borderColor: 'rgba(24, 95, 165, 0.25)',
                                borderWidth: 1,
                                borderRadius: 5,
                                order: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 900,
                            easing: 'easeInOutQuart'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx =>
                                        ` ${ctx.parsed.y} container${ctx.parsed.y !== 1 ? 's' : ''}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    color: 'var(--text-muted)'
                                },
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    color: 'var(--text-muted)',
                                    stepSize: 1,
                                    precision: 0,
                                },
                            },
                        },
                    },
                });
            },

            // ── Load tracker cards ────────────────────────────────────────────────
            async loadTracker(page, append) {
                const list = document.getElementById('widget-tracker');
                const pagination = document.getElementById('tracker-pagination');
                const badge = document.getElementById('tracker-count-badge');
                if (!list) return;

                list.innerHTML = Array(10).fill(
                    '<div class="db-sk db-sk-card"></div>'
                ).join('');

                try {
                    const url = new URL(this.urls.tracker, window.location.origin);
                    url.searchParams.set('page', page);
                    url.searchParams.set('perPage', this._trackerPerPage);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) return;

                    const data = await res.json();
                    list.innerHTML = data.html || this._emptyTracker();

                    this._trackerPage = data.currentPage;
                    this._trackerTotalPages = data.totalPages;

                    if (data.totalPages > 1) {
                        pagination.style.display = 'flex';
                        document.getElementById('tracker-page-label').textContent =
                            `Page ${data.currentPage} of ${data.totalPages}`;
                        document.getElementById('tracker-prev-btn').disabled =
                            data.currentPage === 1;
                        document.getElementById('tracker-next-btn').disabled =
                            data.currentPage === data.totalPages;
                    } else {
                        pagination.style.display = 'none';
                    }

                    if (badge) badge.textContent = data.total ?? '...';

                    this._updateTimestamp();
                } catch (e) {
                    console.error('[Dashboard] Tracker load failed:', e);
                }
            },

            // ── Tracker pagination ────────────────────────────────────────────────
            prevTrackerPage() {
                if (this._trackerPage > 1) {
                    this._trackerPage--;
                    this.loadTracker(this._trackerPage, false);
                }
            },

            nextTrackerPage() {
                if (this._trackerPage < this._trackerTotalPages) {
                    this._trackerPage++;
                    this.loadTracker(this._trackerPage, false);
                }
            },

            // ── Tracker search ────────────────────────────────────────────────────
            _searchTimer: null,

            searchTracker(value) {
                const clearBtn = document.getElementById('tracker-search-clear');
                const label = document.getElementById('tracker-search-label');

                if (clearBtn) clearBtn.style.display = value.length > 0 ? 'block' : 'none';

                if (this._searchTimer) clearTimeout(this._searchTimer);

                if (value.length === 0) {
                    this.clearTrackerSearch();
                    return;
                }

                if (value.length < 2) {
                    if (label) {
                        label.style.display = 'block';
                        label.textContent = 'Type at least 2 characters...';
                    }
                    return;
                }

                this._searchTimer = setTimeout(async () => {
                    const list = document.getElementById('widget-tracker');
                    const pagination = document.getElementById('tracker-pagination');
                    if (!list) return;

                    if (label) {
                        label.style.display = 'block';
                        label.textContent = 'Searching...';
                    }

                    try {
                        const url = new URL(this.urls.tracker, window.location.origin);
                        url.searchParams.set('search', value);

                        const res = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) return;

                        const data = await res.json();
                        list.innerHTML = data.html || this._emptyTracker();

                        if (pagination) pagination.style.display = 'none';

                        if (label) {
                            label.style.display = 'block';
                            label.textContent = data.count === 0 ?
                                'No results found.' :
                                `${data.count} result${data.count !== 1 ? 's' : ''} found`;
                        }

                        const badge = document.getElementById('tracker-count-badge');
                        if (badge) badge.textContent = data.count > 0 ? data.count : '0';

                    } catch (e) {
                        console.error('[Dashboard] Tracker search failed:', e);
                        if (label) {
                            label.style.display = 'block';
                            label.textContent = 'Search failed. Try again.';
                        }
                    }
                }, 300);
            },

            clearTrackerSearch() {
                const input = document.getElementById('tracker-search');
                const clearBtn = document.getElementById('tracker-search-clear');
                const label = document.getElementById('tracker-search-label');

                if (input) input.value = '';
                if (clearBtn) clearBtn.style.display = 'none';
                if (label) label.style.display = 'none';

                if (this._searchTimer) clearTimeout(this._searchTimer);

                this.loadTracker(1, false);
            },

            // ── Load container accordion ──────────────────────────────────────────
            async showContainers(consignmentId, bl, btn) {
                const card = btn.closest('.tracker-card');
                if (!card) return;

                const existing = card.querySelector('.container-accordion');
                if (existing) {
                    existing.remove();
                    btn.textContent = 'Show Containers';
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Loading...';

                try {
                    const url = new URL(this.urls.trackerContainers, window.location.origin);
                    url.searchParams.set('consignmentId', consignmentId);
                    url.searchParams.set('bl', bl);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('Failed');

                    const data = await res.json();
                    const div = document.createElement('div');
                    div.innerHTML = data.html.trim();
                    const accordion = div.firstElementChild;
                    if (accordion) card.appendChild(accordion);

                    btn.disabled = false;
                    btn.textContent = 'Hide Containers';
                } catch (e) {
                    console.error('[Dashboard] Container accordion failed:', e);
                    btn.disabled = false;
                    btn.textContent = 'Show Containers';
                }
            },

            // ── Gate-Out ──────────────────────────────────────────────────────────
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
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }
                    );
                    if (res.ok) {
                        this.loadTracker(this._trackerPage, false);
                    } else {
                        alert('Gate-out failed. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Gate Out';
                    }
                } catch (e) {
                    alert('An error occurred. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Gate Out';
                }
            },

            // ── Container Clear ───────────────────────────────────────────────────
            async containerClear(consignmentId, containerNo, btn) {
                if (!confirm('Confirm return of container ' + containerNo + '?')) return;
                btn.disabled = true;
                btn.textContent = 'Clearing...';
                try {
                    const res = await fetch(
                        this.urls.containerClear + '/' + consignmentId + '/' +
                        encodeURIComponent(containerNo), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }
                    );
                    if (res.ok) {
                        this.loadTracker(this._trackerPage, false);
                    } else {
                        alert('Clear failed. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Mark Returned';
                    }
                } catch (e) {
                    alert('An error occurred. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Mark Returned';
                }
            },

            // ── Disbursements Drawer ──────────────────────────────────────────────
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
                    '<p style="font-size:var(--db-text-xs); color:var(--text-muted); padding:12px 0;">Loading...</p>';

                fetch(this.urls.drawerDisbs, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(rows => {
                        if (!rows.length) {
                            content.innerHTML =
                                '<p style="font-size:var(--db-text-sm); color:var(--text-muted); text-align:center; padding:24px 0;">No pending disbursements found.</p>';
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
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">BL</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">Consignee</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">Destination</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">Status</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">ETA</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">Overdue</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        rows.forEach(row => {
                            const st = statusMap[row.Status] || {
                                label: 'Unknown',
                                bg: '#f5f5f5',
                                color: '#888'
                            };
                            const days = parseInt(row.DaysOverdue);
                            const etaDate = row.ETA ? row.ETA.substring(0, 10) : '—';
                            html += `
                                <tr style="border-bottom:0.5px solid var(--border-color);">
                                    <td style="padding:10px 12px; font-family:monospace; font-size:var(--db-text-base); color:var(--text-primary); white-space:nowrap;">${row.BL}</td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); color:var(--text-primary);">${row.ConsigneeName}</td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); color:var(--text-muted);">${row.Destination || '—'}</td>
                                    <td style="padding:10px 12px; text-align:center;">
                                        <span style="background:${st.bg}; color:${st.color}; font-size:var(--db-text-xs); font-weight:600; border-radius:10px; padding:3px 10px;">${st.label}</span>
                                    </td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; color:var(--text-muted);">${etaDate}</td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; color:#A32D2D; font-weight:600;">${isNaN(days) ? '—' : days + 'd overdue'}</td>
                                </tr>`;
                        });
                        content.innerHTML = html + '</tbody></table>';
                    })
                    .catch(() => {
                        content.innerHTML =
                            '<p style="font-size:var(--db-text-sm); color:#A32D2D; padding:12px 0;">Failed to load data. Please try again.</p>';
                    });
            },

            closeDisbursementsDrawer() {
                document.getElementById('disb-drawer').style.transform = 'translateX(100%)';
                document.getElementById('disb-drawer').style.pointerEvents = 'none';
                document.getElementById('disb-drawer-overlay').style.display = 'none';
            },

            // ── Pending Consignments Drawer ───────────────────────────────────────
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
                    '<p style="font-size:var(--db-text-xs); color:var(--text-muted); padding:12px 0;">Loading...</p>';

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
                                '<p style="font-size:var(--db-text-sm); color:var(--text-muted); text-align:center; padding:24px 0;">No active consignments found.</p>';
                            return;
                        }

                        let html = `
                            <table style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr style="background:#185FA5; color:#fff;">
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">BL</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">Consignee</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:left; font-weight:600;">Destination</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">ETA</th>
                                        <th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">Status</th>
                                        ${canEdit ? '<th style="padding:10px 12px; font-size:var(--db-text-sm); text-align:center; font-weight:600;">Action</th>' : ''}
                                    </tr>
                                </thead>
                                <tbody>`;

                        rows.forEach(row => {
                            const priority = parseInt(row.Priority);
                            const badge = window.ConsignmentPriority.badge(priority);
                            const etaLocked = window.ConsignmentPriority.etaLocked(priority);
                            const etaDate = row.ETA ? row.ETA.substring(0, 10) : '';
                            const days = parseInt(row.ETADays);

                            let daysText = '—';
                            let daysStyle = 'color:var(--text-muted);';
                            if (etaDate) {
                                if (days < 0) {
                                    daysText = `${Math.abs(days)}d overdue`;
                                    daysStyle = 'color:#b91c1c; font-weight:600;';
                                } else if (days === 0) {
                                    daysText = 'Today';
                                    daysStyle = 'color:#15803d; font-weight:600;';
                                } else if (days <= 3) {
                                    daysText = `${days}d`;
                                    daysStyle = 'color:#92400e; font-weight:600;';
                                } else {
                                    daysText = `${days}d`;
                                }
                            }

                            const etaCell = (canEdit && !etaLocked) ?
                                `<input type="date" value="${etaDate}"
                                   style="border:0.5px solid var(--border-color); border-radius:5px;
                                          padding:4px 8px; font-size:var(--db-text-sm); width:140px;">` :
                                `<span style="font-size:var(--db-text-sm); color:var(--text-primary);">${etaDate || '—'}</span>`;

                            const actionCell = (canEdit && !etaLocked) ?
                                `<button onclick="window.DashboardApp.saveEta(${row.ConsignmentID}, '${row.BL}', this)"
                                   style="background:#185FA5; color:#fff; border:none; border-radius:5px;
                                          padding:4px 12px; font-size:var(--db-text-sm); font-weight:600; cursor:pointer;">
                                   Save
                               </button>` :
                                `<span style="font-size:var(--db-text-xs); color:var(--text-muted);">🔒 Locked</span>`;

                            html += `
                                <tr style="border-bottom:0.5px solid var(--border-color);">
                                    <td style="padding:10px 12px; font-family:monospace; font-size:var(--db-text-base); color:var(--text-primary); white-space:nowrap;">${row.BL}</td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); color:var(--text-primary);">${row.ConsigneeName}</td>
                                    <td style="padding:10px 12px; font-size:var(--db-text-sm); color:var(--text-muted);">${row.Destination || '—'}</td>
                                    <td style="padding:10px 12px; text-align:center;" data-eta-cell>${etaCell}</td>
                                    <td style="padding:10px 12px; text-align:center;">
                                        <span style="background:${badge.bg}; color:${badge.color};
                                                     font-size:var(--db-text-xs); font-weight:600;
                                                     border-radius:10px; padding:3px 10px;">
                                            ${badge.label}
                                        </span>
                                        <div style="font-size:0.7rem; color:var(--text-muted); margin-top:2px;">
                                            ${daysText}
                                        </div>
                                    </td>
                                    ${canEdit ? `<td style="padding:10px 12px; text-align:center;">${actionCell}</td>` : ''}
                                </tr>`;
                        });

                        content.innerHTML = html + '</tbody></table>';
                    })
                    .catch(() => {
                        content.innerHTML =
                            '<p style="font-size:var(--db-text-sm); color:#b91c1c; padding:12px 0;">Failed to load. Please try again.</p>';
                    });
            },

            closePendingDrawer() {
                document.getElementById('pending-drawer').style.transform = 'translateX(100%)';
                document.getElementById('pending-drawer').style.pointerEvents = 'none';
                document.getElementById('pending-drawer-overlay').style.display = 'none';
            },

            // ── Save ETA from drawer ──────────────────────────────────────────────
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
                            btn.disabled = false;
                            btn.textContent = '✓ Saved';
                            btn.style.background = '#15803d';
                            setTimeout(() => {
                                btn.textContent = 'Save';
                                btn.style.background = '#185FA5';
                            }, 2000);
                            this.loadTracker(this._trackerPage, false);

                            if (data.eta_changed) {
                                openSmsModal(
                                    bl,
                                    '',
                                    0,
                                    this.urls.sendNotification,
                                    'eta_change', {
                                        phone: data.phone ?? '',
                                        consignee: data.consignee ?? '—'
                                    }
                                );
                            }

                        } else {
                            btn.disabled = false;
                            btn.textContent = 'Save';
                            alert('Update failed. Please try again.');
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.textContent = 'Save';
                        alert('An error occurred.');
                    });
            },

            // ── Chart.js init for financial/donut/bar widgets ─────────────────────
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
                                                size: 12
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
                        console.error('[Dashboard] Donut chart error:', e);
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
                                                size: 12
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
                                                size: 12
                                            }
                                        }
                                    },
                                    y: {
                                        grid: {
                                            color: 'rgba(0,0,0,0.05)'
                                        },
                                        ticks: {
                                            font: {
                                                size: 12
                                            },
                                            callback: val => val >= 1000 ?
                                                (val / 1000).toFixed(0) + 'k' : val
                                        }
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('[Dashboard] Bar chart error:', e);
                    }
                });
            },

            // ── Helpers ───────────────────────────────────────────────────────────
            _buildRefreshUrl(widget, params) {
                const url = new URL(this.urls.refresh, window.location.origin);
                url.searchParams.set('widget', widget);
                Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
                return url.toString();
            },

            _emptyTracker() {
                return `<p style="font-size:var(--db-text-sm); color:var(--text-muted);
                                   text-align:center; padding:2rem 0; grid-column:1/-1;">
                            No active consignments requiring action.
                        </p>`;
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
