@extends('layouts.app')

@section('title', 'Client Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- ── Page title ── --}}
        <div>
            <h1 class="page-title">Client Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                System Reports &rsaquo; Client
            </p>
        </div>

        {{-- ── Search bar ── --}}
        <div class="card" style="padding:1.25rem;">
            <div style="display:grid; grid-template-columns:1fr 180px 180px auto; gap:0.75rem; align-items:flex-end;">

                {{-- Consignee search ── --}}
                <div>
                    <label
                        style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px;">
                        Search Consignee
                    </label>
                    <div style="position:relative;">
                        <input type="text" id="cr_search_input" class="form-input" placeholder="Type consignee name..."
                            autocomplete="off">
                        {{-- Dropdown results ── --}}
                        <div id="cr_search_results"
                            style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff;
                               border:1px solid var(--border-color); border-radius:8px; z-index:100;
                               box-shadow:0 4px 16px rgba(0,0,0,0.1); max-height:280px; overflow-y:auto; margin-top:4px;">
                        </div>
                    </div>
                </div>

                {{-- Date from ── --}}
                <div>
                    <label
                        style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px;">
                        Date From
                    </label>
                    <input type="date" id="cr_date_from" class="form-input">
                </div>

                {{-- Date to ── --}}
                <div>
                    <label
                        style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px;">
                        Date To
                    </label>
                    <input type="date" id="cr_date_to" class="form-input">
                </div>

                {{-- Search button ── --}}
                <div>
                    <button onclick="window.crSearch()" class="btn-primary" style="width:100%; padding:0.6rem 1.25rem;">
                        Search
                    </button>
                </div>

            </div>

            {{-- Selected consignee pill ── --}}
            <div id="cr_selected_pill" style="display:none; margin-top:10px;">
                <span style="font-size:0.8rem; color:var(--text-muted);">Selected: </span>
                <span id="cr_selected_name"
                    style="font-size:0.8rem; font-weight:700; color:#185FA5; background:#eff6ff;
                       padding:3px 10px; border-radius:99px;"></span>
                <button onclick="window.crClear()"
                    style="background:none; border:none; color:#b91c1c; cursor:pointer;
                       font-size:0.75rem; margin-left:6px;">✕
                    Clear</button>
            </div>

            {{-- Error ── --}}
            <p id="cr_error" style="display:none; font-size:0.75rem; color:#b91c1c; margin-top:8px;"></p>
        </div>

        {{-- ── Profile area — hidden until consignee selected ── --}}
        <div id="cr_profile_area" style="display:none;">

            {{-- ── Loading state ── --}}
            <div id="cr_loading" style="text-align:center; padding:3rem; color:var(--text-muted); font-size:13px;">
                Loading client profile...
            </div>

            {{-- ── Profile content ── --}}
            <div id="cr_profile_content" style="display:none;">

                {{-- ── 4 summary cards ── --}}
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1rem;">

                    {{-- Card 1 — Consignee Details ── --}}
                    <div class="card" style="padding:0;">
                        <div
                            style="padding:0.75rem 1rem; border-bottom:1px solid var(--border-color);
                                display:flex; justify-content:space-between; align-items:center;">
                            <p style="font-size:0.75rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                                CONSIGNEE DETAILS
                            </p>
                            <button onclick="window.crViewDetails('consignee')"
                                style="font-size:0.7rem; color:#185FA5; background:none; border:1px solid #185FA5;
                                   border-radius:4px; padding:2px 8px; cursor:pointer;">
                                View Details
                            </button>
                        </div>
                        <div style="padding:0.875rem 1rem;">
                            <p id="cr_c_name"
                                style="font-size:13px; font-weight:700; color:var(--text-primary); margin-bottom:4px;"></p>
                            <p id="cr_c_tel" style="font-size:11px; color:var(--text-muted); margin-bottom:2px;"></p>
                            <p id="cr_c_addr" style="font-size:11px; color:var(--text-muted); margin-bottom:6px;"></p>
                            <p id="cr_c_since" style="font-size:10px; color:var(--text-muted);"></p>
                        </div>
                    </div>

                    {{-- Card 2 — Consignment Summary ── --}}
                    <div class="card" style="padding:0;">
                        <div
                            style="padding:0.75rem 1rem; border-bottom:1px solid var(--border-color);
                                display:flex; justify-content:space-between; align-items:center;">
                            <p style="font-size:0.75rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                                CONSIGNMENTS
                            </p>
                            <button onclick="window.crViewDetails('consignments')"
                                style="font-size:0.7rem; color:#185FA5; background:none; border:1px solid #185FA5;
                                   border-radius:4px; padding:2px 8px; cursor:pointer;">
                                View Details
                            </button>
                        </div>
                        <div style="padding:0.875rem 1rem;">
                            <p style="font-size:24px; font-weight:700; color:#185FA5;" id="cr_cs_total"></p>
                            <p style="font-size:10px; color:var(--text-muted); margin-bottom:6px;">total consignments</p>
                            <div id="cr_cs_breakdown" style="display:flex; flex-wrap:wrap; gap:4px; font-size:10px;"></div>
                            <p style="font-size:10px; color:var(--text-muted); margin-top:6px;" id="cr_cs_carrier"></p>
                        </div>
                    </div>

                    {{-- Card 3 — Invoice Summary ── --}}
                    <div class="card" style="padding:0;">
                        <div
                            style="padding:0.75rem 1rem; border-bottom:1px solid var(--border-color);
                                display:flex; justify-content:space-between; align-items:center;">
                            <p style="font-size:0.75rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                                INVOICES & PAYMENTS
                            </p>
                            <button onclick="window.crViewDetails('invoices')"
                                style="font-size:0.7rem; color:#185FA5; background:none; border:1px solid #185FA5;
                                   border-radius:4px; padding:2px 8px; cursor:pointer;">
                                View Details
                            </button>
                        </div>
                        <div style="padding:0.875rem 1rem;" id="cr_inv_body">
                            <p style="font-size:11px; color:var(--text-muted);">No invoice data found.</p>
                        </div>
                    </div>

                    {{-- Card 4 — Customer Ranking ── --}}
                    <div class="card" style="padding:0;">
                        <div
                            style="padding:0.75rem 1rem; border-bottom:1px solid var(--border-color);
                                display:flex; justify-content:space-between; align-items:center;">
                            <p style="font-size:0.75rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                                CUSTOMER RANKING
                            </p>
                            <button onclick="window.crViewDetails('ranking')"
                                style="font-size:0.7rem; color:#185FA5; background:none; border:1px solid #185FA5;
                                   border-radius:4px; padding:2px 8px; cursor:pointer;">
                                View Details
                            </button>
                        </div>
                        <div style="padding:0.875rem 1rem; text-align:center;" id="cr_rank_body">
                        </div>
                    </div>

                </div>

                {{-- ── Chart ── --}}
                <div class="card" style="margin-bottom:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <p style="font-size:0.8rem; font-weight:700; color:var(--text-primary);">
                            Transaction History
                        </p>
                        <p style="font-size:0.7rem; color:var(--text-muted);" id="cr_chart_label"></p>
                    </div>
                    <canvas id="cr_chart" height="80"></canvas>
                </div>

                {{-- ── Tabs ── --}}
                <div class="card" style="padding:0;">

                    {{-- Tab headers ── --}}
                    <div style="display:flex; border-bottom:2px solid var(--border-color); padding:0 1rem; gap:4px;">
                        <button class="cr-tab cr-tab-active" id="cr-tab-consignments"
                            onclick="window.crTab('consignments')"
                            style="padding:10px 16px; border:none; background:none; cursor:pointer;
                               font-size:12px; font-weight:700; color:#185FA5;
                               border-bottom:2px solid #185FA5; margin-bottom:-2px;">
                            <svg style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:4px;"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg> Consignments
                        </button>
                        <button class="cr-tab" id="cr-tab-hbl" onclick="window.crTab('hbl')"
                            style="padding:10px 16px; border:none; background:none; cursor:pointer;
                               font-size:12px; font-weight:600; color:var(--text-muted);
                               border-bottom:2px solid transparent; margin-bottom:-2px;">
                            <svg style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:4px;"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg> HBL Entries
                        </button>
                        <button class="cr-tab" id="cr-tab-invoices" onclick="window.crTab('invoices')"
                            style="padding:10px 16px; border:none; background:none; cursor:pointer;
                               font-size:12px; font-weight:600; color:var(--text-muted);
                               border-bottom:2px solid transparent; margin-bottom:-2px;">
                            <svg style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:4px;"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg> Invoices
                        </button>
                        <button class="cr-tab" id="cr-tab-disbursements" onclick="window.crTab('disbursements')"
                            style="padding:10px 16px; border:none; background:none; cursor:pointer;
                               font-size:12px; font-weight:600; color:var(--text-muted);
                               border-bottom:2px solid transparent; margin-bottom:-2px;">
                            <svg style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:4px;"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg> Disbursements
                        </button>
                    </div>

                    {{-- Tab panels ── --}}
                    <div style="padding:1rem;">

                        {{-- Consignments panel ── --}}
                        <div id="cr-panel-consignments">
                            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                <thead>
                                    <tr style="background:#185FA5; color:#fff;">
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">#</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Main BL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">ETA</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Carrier</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Container No(s)</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Commodity</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Status</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Age</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Date Reg.</th>
                                        <th style="padding:8px 10px; text-align:center; font-size:10px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cr_consignments_tbody"></tbody>
                            </table>
                        </div>

                        {{-- HBL panel ── --}}
                        <div id="cr-panel-hbl" style="display:none;">
                            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                <thead>
                                    <tr style="background:#185FA5; color:#fff;">
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">#</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Main BL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">House BL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">ETA</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Carrier</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Description</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Weight</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Packages</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Status</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Age</th>
                                    </tr>
                                </thead>
                                <tbody id="cr_hbl_tbody"></tbody>
                            </table>
                        </div>

                        {{-- Invoices panel ── --}}
                        <div id="cr-panel-invoices" style="display:none;">
                            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                <thead>
                                    <tr style="background:#185FA5; color:#fff;">
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">#</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Date</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Receipt No</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">BL / HBL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Account</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Description</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Dr (Charged)</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Cr (Paid)</th>
                                    </tr>
                                </thead>
                                <tbody id="cr_invoices_tbody"></tbody>
                                <tfoot id="cr_invoices_tfoot"></tfoot>
                            </table>
                        </div>

                        {{-- Disbursements panel ── --}}
                        <div id="cr-panel-disbursements" style="display:none;">
                            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                <thead>
                                    <tr style="background:#185FA5; color:#fff;">
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">#</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Date</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Receipt No</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Main BL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">HBL</th>
                                        <th style="padding:8px 10px; text-align:left; font-size:10px;">Account</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Expenditure</th>
                                        <th style="padding:8px 10px; text-align:right; font-size:10px;">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody id="cr_disbursements_tbody"></tbody>
                                <tfoot id="cr_disbursements_tfoot"></tfoot>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- ── Print + export action bar ── --}}
                <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:1rem;">
                    <button onclick="window.crPrint()" class="btn-primary" style="padding:0.5rem 1.25rem;">
                        <svg style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:5px;"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm0-12V5a2 2 0 012-2h2a2 2 0 012 2v4H9z" />
                        </svg> Print Profile
                    </button>
                </div>

            </div>{{-- end cr_profile_content --}}
        </div>{{-- end cr_profile_area --}}

    </div>

    {{-- ── Detail modal overlay ── --}}
    <div id="cr_detail_modal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
           z-index:1000; overflow-y:auto; padding:24px 16px;">
        <div style="background:#fff; border-radius:12px; max-width:600px; margin:0 auto; position:relative;">
            <div
                style="background:#185FA5; padding:14px 20px; border-radius:12px 12px 0 0;
                    display:flex; justify-content:space-between; align-items:center;">
                <p style="font-size:13px; font-weight:700; color:#fff;" id="cr_detail_title">Details</p>
                <button onclick="document.getElementById('cr_detail_modal').style.display='none'"
                    style="background:rgba(255,255,255,0.2); border:none; color:#fff;
                       border-radius:6px; padding:4px 10px; cursor:pointer; font-size:12px;">
                    ✕ Close
                </button>
            </div>
            <div id="cr_detail_body" style="padding:1.25rem;"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {

            // ── Config ───────────────────────────────────────────────────────────
            const SEARCH_URL = '{{ route('reports.client.search') }}';
            const PROFILE_URL = '{{ route('reports.client.profile', ':id') }}';
            const PRINT_URL = '{{ route('reports.client.profile.print', ':id') }}';

            let selectedConsigneeId = null;
            let selectedConsigneeName = null;
            let profileData = null;
            let chartInstance = null;
            let searchTimer = null;

            // ── SVG icon strings — used in JS-rendered HTML ───────────────────────
            const SVG = {
                phone: `<svg style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/></svg>`,
                ship: `<svg style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19l9-14 9 14M3 19h18M3 19l3-4m12 4l-3-4m-6-6v6"/></svg>`,
                eye: `<svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`,
                trophy: `<svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>`,
                medal: `<svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="14" r="6" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2l1.5 4h3L15 2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2L7 6h10l-2-4"/></svg>`,
                star: `<svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>`,
                trending_down: `<svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>`,
            };

            // ── Helpers ──────────────────────────────────────────────────────────
            function fmt(date) {
                if (!date || date === '0000-00-00' || date === '1970-01-01') return '—';
                return new Date(date).toLocaleDateString('en-GB', {
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

            function statusBadge(s) {
                const map = {
                    0: ['Cleared', '#f3f4f6', '#374151'],
                    1: ['Not Arrived', '#fef3c7', '#92400e'],
                    2: ['Pending', '#dbeafe', '#1e40af'],
                    3: ['Gated Out', '#dcfce7', '#166534'],
                };
                const [label, bg, color] = map[s] ?? ['—', '#f3f4f6', '#6b7280'];
                return `<span style="display:inline-block; font-size:10px; font-weight:700;
                padding:2px 8px; border-radius:99px; background:${bg}; color:${color};">
                ${label}</span>`;
            }

            function ageCls(days, status) {
                if (status == 0) return '#6b7280';
                if (days <= 7) return '#15803d';
                if (days <= 14) return '#b45309';
                if (days <= 30) return '#c2410c';
                return '#b91c1c';
            }

            // ── Search ───────────────────────────────────────────────────────────
            document.getElementById('cr_search_input').addEventListener('input', function() {
                clearTimeout(searchTimer);
                const q = this.value.trim();
                if (q.length < 2) {
                    document.getElementById('cr_search_results').style.display = 'none';
                    return;
                }
                searchTimer = setTimeout(() => doSearch(q), 300);
            });

            function doSearch(q) {
                const dateFrom = document.getElementById('cr_date_from').value;
                const dateTo = document.getElementById('cr_date_to').value;
                const params = new URLSearchParams({
                    q
                });
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                fetch(SEARCH_URL + '?' + params, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        const box = document.getElementById('cr_search_results');
                        if (!data.length) {
                            box.innerHTML =
                                '<p style="padding:12px 14px; font-size:12px; color:#6b7280;">No consignees found.</p>';
                        } else {
                            box.innerHTML = data.map(c =>
                                `<div onclick="window.crSelectConsignee(${c.ConsigneeID}, '${c.FullName.replace(/'/g,"\\'")}' )"
                        style="padding:10px 14px; cursor:pointer; border-bottom:1px solid #f3f4f6;
                               font-size:12px; transition:background 0.15s;"
                        onmouseover="this.style.background='#eff6ff'"
                        onmouseout="this.style.background='#fff'">
                        <span style="font-weight:600; color:#111827;">${c.FullName}</span>
                        <span style="font-size:10px; color:#6b7280; margin-left:8px;">
                            ${c.ConsignmentCount} consignment${c.ConsignmentCount != 1 ? 's' : ''}
                        </span>
                    </div>`
                            ).join('');
                        }
                        box.style.display = 'block';
                    })
                    .catch(() => {});
            }

            // Close search dropdown on outside click
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#cr_search_input') &&
                    !e.target.closest('#cr_search_results')) {
                    document.getElementById('cr_search_results').style.display = 'none';
                }
            });

            window.crSearch = function() {
                const q = document.getElementById('cr_search_input').value.trim();
                if (q.length >= 2) doSearch(q);
            };

            window.crSelectConsignee = function(id, name) {
                selectedConsigneeId = id;
                selectedConsigneeName = name;

                document.getElementById('cr_search_input').value = name;
                document.getElementById('cr_search_results').style.display = 'none';
                document.getElementById('cr_selected_name').textContent = name;
                document.getElementById('cr_selected_pill').style.display = 'block';

                loadProfile();
            };

            window.crClear = function() {
                selectedConsigneeId = null;
                selectedConsigneeName = null;
                profileData = null;

                document.getElementById('cr_search_input').value = '';
                document.getElementById('cr_selected_pill').style.display = 'none';
                document.getElementById('cr_profile_area').style.display = 'none';
            };

            // ── Load profile ─────────────────────────────────────────────────────
            function loadProfile() {
                const dateFrom = document.getElementById('cr_date_from').value;
                const dateTo = document.getElementById('cr_date_to').value;
                const params = new URLSearchParams();
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                const url = PROFILE_URL.replace(':id', selectedConsigneeId) +
                    (params.toString() ? '?' + params : '');

                document.getElementById('cr_profile_area').style.display = 'block';
                document.getElementById('cr_loading').style.display = 'block';
                document.getElementById('cr_profile_content').style.display = 'none';
                document.getElementById('cr_error').style.display = 'none';

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) {
                            showError(data.message || 'Failed to load profile.');
                            return;
                        }
                        profileData = data;
                        renderProfile(data);
                        document.getElementById('cr_loading').style.display = 'none';
                        document.getElementById('cr_profile_content').style.display = 'block';
                    })
                    .catch(() => showError('Network error. Please try again.'));
            }

            function showError(msg) {
                document.getElementById('cr_loading').style.display = 'none';
                const err = document.getElementById('cr_error');
                err.textContent = msg;
                err.style.display = 'block';
            }

            // ── Render profile ────────────────────────────────────────────────────
            function renderProfile(d) {
                renderCard1(d);
                renderCard2(d);
                renderCard3(d);
                renderCard4(d);
                renderChart(d.chartData);
                renderConsignmentsTab(d.consignments);
                renderHblTab(d.hblEntries);
                renderInvoicesTab(d.invoices);
                renderDisbursementsTab(d.disbursements, d.disbursementTotals);
                window.crTab('consignments');
            }

            // ── Card 1: Consignee details ─────────────────────────────────────────
            function renderCard1(d) {
                const c = d.consignee;
                document.getElementById('cr_c_name').textContent = c.FullName ?? '—';
                document.getElementById('cr_c_tel').innerHTML = c.TelNo ?
                    SVG.phone + c.TelNo : '';
                document.getElementById('cr_c_addr').textContent = c.Address1 ?? '';
                document.getElementById('cr_c_since').textContent = d.memberSince ?
                    'Member since: ' + fmt(d.memberSince) : '';
            }

            // ── Card 2: Consignment summary ───────────────────────────────────────
            function renderCard2(d) {
                const s = d.consignmentSummary;
                document.getElementById('cr_cs_total').textContent =
                    (s.total + s.hbl_total);

                const badges = [
                    ['Not Arrived', s.not_arrived, '#fef3c7', '#92400e'],
                    ['Pending', s.pending, '#dbeafe', '#1e40af'],
                    ['Gated Out', s.gated_out, '#dcfce7', '#166534'],
                    ['Cleared', s.cleared, '#f3f4f6', '#374151'],
                ];

                document.getElementById('cr_cs_breakdown').innerHTML = badges
                    .filter(b => b[1] > 0)
                    .map(([label, count, bg, color]) =>
                        `<span style="background:${bg}; color:${color}; padding:2px 7px;
                        border-radius:99px; font-size:10px; font-weight:700;">
                    ${label}: ${count}
                </span>`
                    ).join('');

                document.getElementById('cr_cs_carrier').innerHTML =
                    d.mostUsedCarrier !== '—' ?
                    SVG.ship + 'Most used: ' + d.mostUsedCarrier :
                    '';
            }

            // ── Card 3: Invoice summary ───────────────────────────────────────────
            function renderCard3(d) {
                const inv = d.invoiceSummary;
                const body = document.getElementById('cr_inv_body');

                if (!inv || (!inv.TotalInvoiced && !inv.TotalPaid)) {
                    body.innerHTML = '<p style="font-size:11px; color:#6b7280;">No invoice data found.</p>';
                    return;
                }

                const outstanding = parseFloat(inv.Outstanding || 0);
                const outColor = outstanding > 0 ? '#b91c1c' : '#15803d';

                body.innerHTML = `
            <div style="display:flex; flex-direction:column; gap:6px;">
                <div style="display:flex; justify-content:space-between; font-size:11px;">
                    <span style="color:#6b7280;">Total Invoiced</span>
                    <span style="font-weight:700;">GH₵ ${fmtNum(inv.TotalInvoiced)}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:11px;">
                    <span style="color:#6b7280;">Total Paid</span>
                    <span style="font-weight:700; color:#15803d;">GH₵ ${fmtNum(inv.TotalPaid)}</span>
                </div>
                <div style="border-top:1px solid #e5e7eb; padding-top:6px;
                            display:flex; justify-content:space-between; font-size:12px;">
                    <span style="font-weight:700;">Outstanding</span>
                    <span style="font-weight:700; color:${outColor};">
                        GH₵ ${fmtNum(inv.Outstanding)}
                    </span>
                </div>
            </div>`;
            }

            // ── Card 4: Customer ranking ──────────────────────────────────────────
            function renderCard4(d) {
                const r = d.ranking;
                const body = document.getElementById('cr_rank_body');

                const clsMap = {
                    gold: ['#fef3c7', '#92400e'],
                    silver: ['#f3f4f6', '#374151'],
                    bronze: ['#fff7ed', '#9a3412'],
                    standard: ['#eff6ff', '#1e40af'],
                };
                const iconMap = {
                    gold: SVG.trophy,
                    silver: SVG.medal,
                    bronze: SVG.medal,
                    standard: SVG.star
                };
                const [bg, color] = clsMap[r.badge.cls] ?? ['#f3f4f6', '#374151'];

                body.innerHTML = `
            <div style="margin-bottom:4px; color:${color};">${iconMap[r.badge.cls] ?? SVG.star}</div>
            
            <p style="font-size:18px; font-weight:700; color:#185FA5;">
                #${r.rank} <span style="font-size:11px; font-weight:400; color:#6b7280;">of ${r.total}</span>
            </p>
            <span style="display:inline-block; margin-top:6px; font-size:11px; font-weight:700;
                    padding:3px 12px; border-radius:99px; background:${bg}; color:${color};">
                ${r.badge.label}
            </span>
            <p style="font-size:10px; color:#6b7280; margin-top:6px;">
                Top ${r.percentile}% of all clients
            </p>`;
            }

            // ── Chart ─────────────────────────────────────────────────────────────
            function renderChart(chartData) {
                const labels = chartData.map(r => r.MonthLabel);
                const invoiced = chartData.map(r => parseFloat(r.Invoiced || 0));
                const paid = chartData.map(r => parseFloat(r.Paid || 0));

                document.getElementById('cr_chart_label').textContent =
                    'Monthly invoiced vs payments received';

                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(
                    document.getElementById('cr_chart').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'Invoiced (GH₵)',
                                    data: invoiced,
                                    borderColor: '#185FA5',
                                    backgroundColor: 'rgba(24,95,165,0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                },
                                {
                                    label: 'Paid (GH₵)',
                                    data: paid,
                                    borderColor: '#15803d',
                                    backgroundColor: 'rgba(21,128,61,0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx =>
                                            ctx.dataset.label + ': GH₵ ' +
                                            ctx.parsed.y.toLocaleString('en-GH', {
                                                minimumFractionDigits: 2
                                            })
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: v => 'GH₵ ' + v.toLocaleString()
                                    }
                                }
                            }
                        }
                    });
            }

            // ── Tab switching ─────────────────────────────────────────────────────
            window.crTab = function(name) {
                ['consignments', 'hbl', 'invoices', 'disbursements'].forEach(t => {
                    const panel = document.getElementById('cr-panel-' + t);
                    const tab = document.getElementById('cr-tab-' + t);
                    const active = t === name;
                    panel.style.display = active ? 'block' : 'none';
                    tab.style.color = active ? '#185FA5' : 'var(--text-muted)';
                    tab.style.fontWeight = active ? '700' : '600';
                    tab.style.borderBottom = active ? '2px solid #185FA5' : '2px solid transparent';
                });
            };

            // ── Consignments tab ──────────────────────────────────────────────────
            function renderConsignmentsTab(rows) {
                const tbody = document.getElementById('cr_consignments_tbody');
                if (!rows || !rows.length) {
                    tbody.innerHTML = `<tr><td colspan="10"
                style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px;">
                No consignments found.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rows.map((r, i) => `
            <tr style="${i % 2 === 0 ? '' : 'background:#f9fafb'}">
                <td style="padding:7px 10px; font-size:12px; color:#9ca3af;">${i + 1}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.MainBL ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${fmt(r.ETA)}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.CarrierName ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.ContainerNos ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.CommodityType ?? '—'}</td>
                <td style="padding:7px 10px;">${statusBadge(r.Status)}</td>
                <td style="padding:7px 10px; font-size:12px; text-align:right;
                            color:${ageCls(r.AgeDays, r.Status)}; font-weight:700;">
                    ${r.AgeDays ?? '—'}
                </td>
                <td style="padding:7px 10px; font-size:12px; text-align:right; color:#6b7280;">
                    ${fmt(r.Date)}
                </td>
                <td style="padding:7px 10px; text-align:center;">
                    <button onclick="window.openConsignmentModal(${r.ConsignmentID})"
                            title="View Details"
                            style="background:none; border:1px solid #e5e7eb; border-radius:6px;
                                   padding:4px 7px; cursor:pointer; color:#185FA5; line-height:0;">
                            ${SVG.eye}
                        </button>
                </td>
            </tr>`).join('');
            }

            // ── HBL tab ───────────────────────────────────────────────────────────
            function renderHblTab(rows) {
                const tbody = document.getElementById('cr_hbl_tbody');
                if (!rows || !rows.length) {
                    tbody.innerHTML = `<tr><td colspan="10"
                style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px;">
                No HBL entries found.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rows.map((r, i) => `
            <tr style="${i % 2 === 0 ? '' : 'background:#f9fafb'}">
                <td style="padding:7px 10px; font-size:12px; color:#9ca3af;">${i + 1}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.MainBL ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.HouseBL ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${fmt(r.ETA)}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.CarrierName ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.Description ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.Weight ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.Package ?? '—'} ${r.Unit ?? ''}</td>
                <td style="padding:7px 10px;">${statusBadge(r.Status)}</td>
                <td style="padding:7px 10px; font-size:12px; text-align:right;
                            color:${ageCls(r.AgeDays, r.Status)}; font-weight:700;">
                    ${r.AgeDays ?? '—'}
                </td>
            </tr>`).join('');
            }

            // ── Invoices tab ──────────────────────────────────────────────────────
            function renderInvoicesTab(rows) {
                const tbody = document.getElementById('cr_invoices_tbody');
                const tfoot = document.getElementById('cr_invoices_tfoot');

                if (!rows || !rows.length) {
                    tbody.innerHTML = `<tr><td colspan="8"
                style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px;">
                No invoice records found.</td></tr>`;
                    tfoot.innerHTML = '';
                    return;
                }

                let totalDr = 0,
                    totalCr = 0;

                tbody.innerHTML = rows.map((r, i) => {
                    totalDr += parseFloat(r.Dr || 0);
                    totalCr += parseFloat(r.Cr || 0);
                    return `
            <tr style="${i % 2 === 0 ? '' : 'background:#f9fafb'}">
                <td style="padding:7px 10px; font-size:12px; color:#9ca3af;">${i + 1}</td>
                <td style="padding:7px 10px; font-size:12px;">${fmt(r.Date)}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.ReceiptNo ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">
                    ${r.MainBL ?? '—'}${r.HouseBL ? ' / ' + r.HouseBL : ''}
                </td>
                <td style="padding:7px 10px; font-size:12px;">${r.AccountName ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.Description ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; text-align:right; color:#b91c1c;">
                    GH₵ ${fmtNum(r.Dr)}
                </td>
                <td style="padding:7px 10px; font-size:12px; text-align:right; color:#15803d;">
                    GH₵ ${fmtNum(r.Cr)}
                </td>
            </tr>`;
                }).join('');

                tfoot.innerHTML = `
            <tr style="background:#f3f4f6; border-top:2px solid #185FA5;">
                <td colspan="6" style="padding:8px 10px; font-size:12px; font-weight:700;
                                        text-align:right;">Totals</td>
                <td style="padding:8px 10px; font-size:12px; font-weight:700;
                            text-align:right; color:#b91c1c;">GH₵ ${fmtNum(totalDr)}</td>
                <td style="padding:8px 10px; font-size:12px; font-weight:700;
                            text-align:right; color:#15803d;">GH₵ ${fmtNum(totalCr)}</td>
            </tr>`;
            }

            // ── Disbursements tab ─────────────────────────────────────────────────
            function renderDisbursementsTab(rows, totals) {
                const tbody = document.getElementById('cr_disbursements_tbody');
                const tfoot = document.getElementById('cr_disbursements_tfoot');

                if (!rows || !rows.length) {
                    tbody.innerHTML = `<tr><td colspan="8"
                style="text-align:center; padding:2rem; color:#9ca3af; font-size:12px;">
                No disbursement records found.</td></tr>`;
                    tfoot.innerHTML = '';
                    return;
                }

                tbody.innerHTML = rows.map((r, i) => `
            <tr style="${i % 2 === 0 ? '' : 'background:#f9fafb'}">
                <td style="padding:7px 10px; font-size:12px; color:#9ca3af;">${i + 1}</td>
                <td style="padding:7px 10px; font-size:12px;">${fmt(r.Date)}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.ReceiptNo ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.MainBL ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; font-family:monospace;">${r.HBL ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px;">${r.AccountName ?? '—'}</td>
                <td style="padding:7px 10px; font-size:12px; text-align:right; color:#b91c1c;">
                    GH₵ ${fmtNum(r.Expenditure)}
                </td>
                <td style="padding:7px 10px; font-size:12px; text-align:right; color:#15803d;">
                    GH₵ ${fmtNum(r.Revenue)}
                </td>
            </tr>`).join('');

                tfoot.innerHTML = `
            <tr style="background:#f3f4f6; border-top:2px solid #185FA5;">
                <td colspan="6" style="padding:8px 10px; font-size:12px; font-weight:700;
                                        text-align:right;">Totals</td>
                <td style="padding:8px 10px; font-size:12px; font-weight:700;
                            text-align:right; color:#b91c1c;">
                    GH₵ ${fmtNum(totals.expenditure)}
                </td>
                <td style="padding:8px 10px; font-size:12px; font-weight:700;
                            text-align:right; color:#15803d;">
                    GH₵ ${fmtNum(totals.revenue)}
                </td>
            </tr>`;
            }

            // ── View Details modal ────────────────────────────────────────────────
            window.crViewDetails = function(section) {
                if (!profileData) return;

                const modal = document.getElementById('cr_detail_modal');
                const title = document.getElementById('cr_detail_title');
                const body = document.getElementById('cr_detail_body');

                if (section === 'consignee') {
                    const c = profileData.consignee;
                    title.textContent = 'Consignee Details';
                    body.innerHTML = `
                <div style="display:flex; flex-direction:column; gap:10px; font-size:13px;">
                    <div><span style="color:#6b7280; font-size:11px;">Full Name</span>
                         <p style="font-weight:700;">${c.FullName ?? '—'}</p></div>
                    <div><span style="color:#6b7280; font-size:11px;">Phone</span>
                         <p>${c.TelNo ?? '—'}</p></div>
                    <div><span style="color:#6b7280; font-size:11px;">Address</span>
                         <p>${c.Address1 ?? '—'}</p></div>
                    <div><span style="color:#6b7280; font-size:11px;">Email</span>
                         <p>${c.Email ?? '—'}</p></div>
                    <div><span style="color:#6b7280; font-size:11px;">Member Since</span>
                         <p>${profileData.memberSince ? fmt(profileData.memberSince) : '—'}</p></div>
                </div>`;
                } else if (section === 'consignments') {
                    const s = profileData.consignmentSummary;
                    title.textContent = 'Consignment Breakdown';
                    body.innerHTML = `
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
                    ${[
                        ['Total (FCL)', s.total],
                        ['Total (HBL/LCL)', s.hbl_total],
                        ['Not Arrived', s.not_arrived],
                        ['Pending', s.pending],
                        ['Gated Out', s.gated_out],
                        ['Cleared', s.cleared],
                    ].map(([label, val]) => `
                                                                                                <div style="background:#f9fafb; border:1px solid #e5e7eb;
                                                                                                            border-radius:6px; padding:10px 12px;">
                                                                                                    <p style="font-size:9px; text-transform:uppercase;
                                                                                                              color:#6b7280; margin-bottom:4px;">${label}</p>
                                                                                                    <p style="font-size:20px; font-weight:700; color:#185FA5;">${val}</p>
                                                                                                </div>`
                    ).join('')}
                </div>
                <p style="margin-top:12px; font-size:12px; color:#6b7280;">
                    Most used carrier: <strong>${profileData.mostUsedCarrier}</strong>
                    ${profileData.avgDaysToClear !== null
                        ? ' &nbsp;·&nbsp; Avg days to clear: <strong>' + profileData.avgDaysToClear + '</strong>'
                        : ''}
                </p>`;
                } else if (section === 'invoices') {
                    const inv = profileData.invoiceSummary;
                    title.textContent = 'Invoice & Payment Summary';
                    if (!inv || (!inv.TotalInvoiced && !inv.TotalPaid)) {
                        body.innerHTML = '<p style="color:#6b7280; font-size:12px;">No invoice data found.</p>';
                    } else {
                        body.innerHTML = `
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:13px;">
                        <div style="display:flex; justify-content:space-between; padding:10px;
                                    background:#f9fafb; border-radius:6px;">
                            <span style="color:#6b7280;">Total Invoiced</span>
                            <strong>GH₵ ${fmtNum(inv.TotalInvoiced)}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:10px;
                                    background:#f0fdf4; border-radius:6px;">
                            <span style="color:#6b7280;">Total Paid</span>
                            <strong style="color:#15803d;">GH₵ ${fmtNum(inv.TotalPaid)}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:10px;
                                    background:${parseFloat(inv.Outstanding) > 0 ? '#fef2f2' : '#f0fdf4'};
                                    border-radius:6px; border:1px solid ${parseFloat(inv.Outstanding) > 0 ? '#fecaca' : '#bbf7d0'};">
                            <strong>Outstanding Balance</strong>
                            <strong style="color:${parseFloat(inv.Outstanding) > 0 ? '#b91c1c' : '#15803d'};">
                                GH₵ ${fmtNum(inv.Outstanding)}
                            </strong>
                        </div>
                    </div>`;
                    }
                } else if (section === 'ranking') {
                    const r = profileData.ranking;
                    title.textContent = 'Customer Ranking Details';
                    body.innerHTML = `
                <div style="text-align:center; padding:1rem;">
                    <div style="margin-bottom:8px; color:#185FA5;">${iconMap[r.badge.cls] ?? SVG.star}</div>
                    <p style="font-size:24px; font-weight:700; color:#185FA5; margin-bottom:4px;">
                        Ranked #${r.rank}
                    </p>
                    <p style="font-size:13px; color:#6b7280; margin-bottom:12px;">
                        out of ${r.total} active clients
                    </p>
                    <p style="font-size:13px; font-weight:700; margin-bottom:4px;">
                        ${r.badge.label}
                    </p>
                    <p style="font-size:12px; color:#6b7280;">
                        Top ${r.percentile}% of all clients by consignment volume
                    </p>
                </div>`;
                }

                modal.style.display = 'block';
            };

            // Close detail modal on overlay click
            document.getElementById('cr_detail_modal').addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });

            // ── Print ─────────────────────────────────────────────────────────────
            window.crPrint = function() {
                if (!selectedConsigneeId) return;
                const dateFrom = document.getElementById('cr_date_from').value;
                const dateTo = document.getElementById('cr_date_to').value;
                const params = new URLSearchParams();
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                const url = PRINT_URL.replace(':id', selectedConsigneeId) +
                    (params.toString() ? '?' + params : '');

                window.open(url, '_blank');
            };

            // ── Include consignment detail modal ──────────────────────────────────
            // openConsignmentModal is defined in _consignment-detail-modal partial
            // We include it here so the eye icon in the consignments tab works

        })();
    </script>

    {{-- Include shared consignment detail modal --}}
    @include('reports.operations._consignment-detail-modal')
@endpush
