@extends('layouts.app')

@section('title', 'Disbursement Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Disbursement Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                System Reports &rsaquo; Disbursement
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; align-items:flex-start;">

            {{-- ── Card 1: Consignment P&L ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT P&L REPORT
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        Revenue vs Expenditure per BL — Net Profit per consignment
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="pnl_date_from" class="form-input">
                    <input type="date" id="pnl_date_to" class="form-input">
                    <p id="pnl_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewPnl()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 2: Expenditure by Account ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        EXPENDITURE BY ACCOUNT
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        Total spent per expense category across all consignments
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="exp_date_from" class="form-input">
                    <input type="date" id="exp_date_to" class="form-input">
                    <p id="exp_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewExpByAccount()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 6: Officer Summary ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        OFFICER DISBURSEMENT SUMMARY
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        Cash received vs spent vs change returned per officer
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="off_date_from" class="form-input">
                    <input type="date" id="off_date_to" class="form-input">
                    <p id="off_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewOfficerSummary()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 3: Disbursement Detail ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        DISBURSEMENT DETAIL REPORT
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        Full audit trail — all entries per period or per BL
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="det_date_from" class="form-input">
                    <input type="date" id="det_date_to" class="form-input">
                    <input type="text" id="det_bl" class="form-input" placeholder="Filter by BL (optional)"
                        style="text-transform:uppercase;">
                    <p id="det_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewDetail()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>


            {{-- ── Card 4: Comparative Disbursement ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        COMPARATIVE DISBURSEMENT
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        Compare similar consignments by item type and container size
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="cmp_date_from" class="form-input" onchange="window.loadComparativeFilters()">
                    <input type="date" id="cmp_date_to" class="form-input" onchange="window.loadComparativeFilters()">
                    <select id="cmp_item" class="form-input">
                        <option value="">All Item Types</option>
                    </select>
                    <select id="cmp_size" class="form-input">
                        <option value="">All Container Sizes</option>
                    </select>
                    <p id="cmp_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewComparative()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 5: Unapproved Disbursements ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        UNAPPROVED DISBURSEMENTS
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">
                        All pending disbursements awaiting management approval
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <p style="font-size:0.75rem; color:var(--text-muted);">
                        No date filter — shows all pending disbursements as at today.
                    </p>
                    <p id="unap_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewUnapproved()" class="btn-primary" style="width:100%;">
                        View Report
                    </button>
                </div>
            </div>



        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.DisbursementReports = {
            pnlPrint: '{{ route('reports.disbursement.consignment-pnl.print') }}',
            expPrint: '{{ route('reports.disbursement.expenditure-by-account.print') }}',
            cmpPrint: '{{ route('reports.disbursement.comparative.print') }}',
            detPrint: '{{ route('reports.disbursement.detail.print') }}',
            unapPrint: '{{ route('reports.disbursement.unapproved.print') }}',
            offPrint: '{{ route('reports.disbursement.officer-summary.print') }}',
            itemDescUrl: '{{ route('reports.disbursement.comparative.item-descriptions') }}',
            containerSizesUrl: '{{ route('reports.disbursement.comparative.container-sizes') }}',
        };

        function requireDates(fromId, toId, errId) {
            const dateFrom = document.getElementById(fromId).value;
            const dateTo = document.getElementById(toId).value;
            const err = document.getElementById(errId);
            if (!dateFrom || !dateTo) {
                err.textContent = 'Please select both Date From and Date To.';
                err.style.display = 'block';
                return null;
            }
            err.style.display = 'none';
            return {
                dateFrom,
                dateTo
            };
        }

        // ── Card 1: P&L ──────────────────────────────────────────────────────────
        window.viewPnl = function() {
            const d = requireDates('pnl_date_from', 'pnl_date_to', 'pnl_error');
            if (!d) return;
            window.open(window.DisbursementReports.pnlPrint + '?' +
                new URLSearchParams({
                    date_from: d.dateFrom,
                    date_to: d.dateTo
                }), '_blank');
        };

        // ── Card 2: Expenditure by Account ───────────────────────────────────────
        window.viewExpByAccount = function() {
            const d = requireDates('exp_date_from', 'exp_date_to', 'exp_error');
            if (!d) return;
            window.open(window.DisbursementReports.expPrint + '?' +
                new URLSearchParams({
                    date_from: d.dateFrom,
                    date_to: d.dateTo
                }), '_blank');
        };

        // ── Card 3: Comparative ───────────────────────────────────────────────────
        window.viewComparative = function() {
            const d = requireDates('cmp_date_from', 'cmp_date_to', 'cmp_error');
            if (!d) return;
            const item = document.getElementById('cmp_item').value;
            const size = document.getElementById('cmp_size').value;
            const params = {
                date_from: d.dateFrom,
                date_to: d.dateTo
            };
            if (item) params.item_description = item;
            if (size) params.container_size = size;
            window.open(window.DisbursementReports.cmpPrint + '?' +
                new URLSearchParams(params), '_blank');
        };

        window.loadComparativeFilters = function() {
            const dateFrom = document.getElementById('cmp_date_from').value;
            const dateTo = document.getElementById('cmp_date_to').value;
            if (!dateFrom || !dateTo) return;

            // Load item descriptions
            fetch(window.DisbursementReports.itemDescUrl + '?' +
                    new URLSearchParams({
                        date_from: dateFrom,
                        date_to: dateTo
                    }), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(r => r.json())
                .then(data => {
                    const sel = document.getElementById('cmp_item');
                    sel.innerHTML = '<option value="">All Item Types</option>';
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        sel.appendChild(opt);
                    });
                });

            // Load container sizes
            fetch(window.DisbursementReports.containerSizesUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    const sel = document.getElementById('cmp_size');
                    sel.innerHTML = '<option value="">All Container Sizes</option>';
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        sel.appendChild(opt);
                    });
                });
        };

        // ── Card 4: Detail ────────────────────────────────────────────────────────
        window.viewDetail = function() {
            const d = requireDates('det_date_from', 'det_date_to', 'det_error');
            if (!d) return;
            const bl = document.getElementById('det_bl').value.trim().toUpperCase();
            const params = {
                date_from: d.dateFrom,
                date_to: d.dateTo
            };
            if (bl) params.bl = bl;
            window.open(window.DisbursementReports.detPrint + '?' +
                new URLSearchParams(params), '_blank');
        };

        // ── Card 5: Unapproved ────────────────────────────────────────────────────
        window.viewUnapproved = function() {
            window.open(window.DisbursementReports.unapPrint, '_blank');
        };

        // ── Card 6: Officer Summary ───────────────────────────────────────────────
        window.viewOfficerSummary = function() {
            const d = requireDates('off_date_from', 'off_date_to', 'off_error');
            if (!d) return;
            window.open(window.DisbursementReports.offPrint + '?' +
                new URLSearchParams({
                    date_from: d.dateFrom,
                    date_to: d.dateTo
                }), '_blank');
        };
    </script>
@endpush
