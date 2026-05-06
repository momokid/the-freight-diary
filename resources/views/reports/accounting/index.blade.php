@extends('layouts.app')

@section('title', 'Accounting Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Accounting Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                System Reports &rsaquo; Accounting
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; align-items:flex-start;">

            {{-- ── Card 1: Trial Balance ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">TRIAL BALANCE</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">All account balances as at a
                        selected date</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">As At Date</label>
                    <input type="date" id="tb_as_at" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="tb_branch" class="form-input"></select>
                    <p id="tb_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewTrialBalance()" class="btn-primary" style="width:100%;">View Report</button>
                </div>
            </div>

            {{-- ── Card 2: GL Statement ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">GL STATEMENT</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Movements on a GL account with
                        running balance</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">GL Account</label>
                    <select id="gl_account" class="form-input">
                        <option value="">Loading accounts...</option>
                    </select>
                    <label class="form-label">Date From</label>
                    <input type="date" id="gl_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="gl_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="gl_branch" class="form-input"></select>
                    <p id="gl_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewGlStatement()" class="btn-primary" style="width:100%;">View Report</button>
                </div>
            </div>

            {{-- ── Card 3: Income & Expenditure ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">INCOME & EXPENDITURE
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Income vs expenditure for a period
                        — net surplus/deficit</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Date From</label>
                    <input type="date" id="ie_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="ie_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="ie_branch" class="form-input"></select>
                    <p id="ie_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewIncomeExpenditure()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 4: Daily Balancing Sheet ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">DAILY BALANCING
                        SHEET</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Opening, movements and closing
                        balances per cash account</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Date</label>
                    <input type="date" id="dbs_date" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="dbs_branch" class="form-input"></select>
                    <p id="dbs_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewDailyBalance()" class="btn-primary" style="width:100%;">View Report</button>
                </div>
            </div>

            {{-- ── Card 5: Waste Sheet ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">WASTE SHEET</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">All reversed transactions — full
                        audit trail</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Date From</label>
                    <input type="date" id="ws_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="ws_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="ws_branch" class="form-input"></select>
                    <input type="text" id="ws_username" class="form-input"
                        placeholder="Filter by username (optional)">
                    <p id="ws_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewWasteSheet()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 6: Receipt Register ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">RECEIPT REGISTER
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">All receipts issued —
                        reconciliation and audit</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Date From</label>
                    <input type="date" id="rr_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="rr_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="rr_branch" class="form-input"></select>
                    <input type="text" id="rr_username" class="form-input"
                        placeholder="Filter by username (optional)">
                    <p id="rr_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewReceiptRegister()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 7: Account Activity ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">ACCOUNT ACTIVITY
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Movements on any account with
                        running balance</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Account Type</label>
                    <select id="aa_type" class="form-input" onchange="window.loadActivityAccounts()">
                        <option value="GL">GL</option>
                        <option value="INCOME">Income</option>
                        <option value="EXPENDITURE">Expenditure</option>
                    </select>
                    <label class="form-label">Account</label>
                    <select id="aa_account" class="form-input">
                        <option value="">Select type first...</option>
                    </select>
                    <label class="form-label">Date From</label>
                    <input type="date" id="aa_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="aa_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="aa_branch" class="form-input"></select>
                    <p id="aa_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewAccountActivity()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 8: Balance Sheet ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">BALANCE SHEET</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Assets vs Liabilities &amp;
                        Equity — point-in-time snapshot</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">As At Date</label>
                    <input type="date" id="bs_as_at" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="bs_branch" class="form-input"></select>
                    <p id="bs_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewBalanceSheet()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 9: Cash Flow Statement ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">CASH FLOW STATEMENT
                    </p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Cash inflows &amp; outflows per
                        bank/cash account for a period</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Date From</label>
                    <input type="date" id="cf_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="cf_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="cf_branch" class="form-input"></select>
                    <p id="cf_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewCashFlow()" class="btn-primary" style="width:100%;">View Report</button>
                </div>
            </div>

            {{-- ── Card 10: Income Account Statement ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">INCOME ACCOUNT
                        STATEMENT</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Movements on a single income
                        account with running balance</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Income Account</label>
                    <select id="inc_account" class="form-input">
                        <option value="">Loading accounts...</option>
                    </select>
                    <label class="form-label">Date From</label>
                    <input type="date" id="inc_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="inc_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="inc_branch" class="form-input"></select>
                    <p id="inc_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewIncomeStatement()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

            {{-- ── Card 11: Expenditure Account Statement ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">EXPENDITURE ACCOUNT
                        STATEMENT</p>
                    <p style="font-size:0.7rem; color:var(--text-muted); margin-top:3px;">Movements on a single expenditure
                        account with running balance</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <label class="form-label">Expenditure Account</label>
                    <select id="exp_account" class="form-input">
                        <option value="">Loading accounts...</option>
                    </select>
                    <label class="form-label">Date From</label>
                    <input type="date" id="exp_date_from" class="form-input">
                    <label class="form-label">Date To</label>
                    <input type="date" id="exp_date_to" class="form-input">
                    <label class="form-label">Branch</label>
                    <select id="exp_branch" class="form-input"></select>
                    <p id="exp_error" style="display:none; font-size:0.75rem; color:#b91c1c;"></p>
                    <button onclick="window.viewExpenditureStatement()" class="btn-primary" style="width:100%;">View
                        Report</button>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.AccountingReports = {
            tbPrint: '{{ route('reports.accounting.trial-balance.print') }}',
            tbExport: '{{ route('reports.accounting.trial-balance.export') }}',
            glPrint: '{{ route('reports.accounting.gl-statement.print') }}',
            glAccUrl: '{{ route('reports.accounting.gl-statement.accounts') }}',
            iePrint: '{{ route('reports.accounting.income-expenditure.print') }}',
            dbsPrint: '{{ route('reports.accounting.daily-balance.print') }}',
            wsPrint: '{{ route('reports.accounting.waste-sheet.print') }}',
            rrPrint: '{{ route('reports.accounting.receipt-register.print') }}',
            aaPrint: '{{ route('reports.accounting.account-activity.print') }}',
            aaAccUrl: '{{ route('reports.accounting.account-activity.accounts') }}',
            bsPrint: '{{ route('reports.accounting.balance-sheet.print') }}',
            cfPrint: '{{ route('reports.accounting.cash-flow.print') }}',
            incPrint: '{{ route('reports.accounting.income-statement.print') }}',
            incAccUrl: '{{ route('reports.accounting.income-statement.accounts') }}',
            expPrint: '{{ route('reports.accounting.expenditure-statement.print') }}',
            expAccUrl: '{{ route('reports.accounting.expenditure-statement.accounts') }}',
        };

        // ── Helper: require dates ─────────────────────────────────────────────────
        function reqDates(fromId, toId, errId) {
            const f = document.getElementById(fromId)?.value;
            const t = document.getElementById(toId)?.value;
            const e = document.getElementById(errId);
            if (!f || !t) {
                if (e) {
                    e.textContent = 'Please select both dates.';
                    e.style.display = 'block';
                }
                return null;
            }
            if (e) e.style.display = 'none';
            return {
                dateFrom: f,
                dateTo: t
            };
        }

        function reqDate(id, errId) {
            const v = document.getElementById(id)?.value;
            const e = document.getElementById(errId);
            if (!v) {
                if (e) {
                    e.textContent = 'Please select a date.';
                    e.style.display = 'block';
                }
                return null;
            }
            if (e) e.style.display = 'none';
            return v;
        }

        function branch(id) {
            return document.getElementById(id)?.value || 'ALL';
        }

        // ── Load branches into all selects ────────────────────────────────────────
        function loadBranches() {
            fetch('/reports/accounting/trial-balance/print', {
                method: 'HEAD',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(() => {});

            // Build branch options from server — use inst_branch via a simple approach
            const branchSelects = ['tb_branch', 'gl_branch', 'ie_branch', 'dbs_branch', 'ws_branch', 'rr_branch',
                'aa_branch', 'bs_branch', 'cf_branch', 'inc_branch', 'exp_branch'
            ];
            const userBranch = '{{ auth()->user()->BranchID }}';

            branchSelects.forEach(id => {
                const sel = document.getElementById(id);
                if (!sel) return;
                sel.innerHTML =
                    `<option value="${userBranch}">My Branch</option><option value="ALL">All Branches</option>`;
            });
        }

        // ── Load GL accounts ──────────────────────────────────────────────────────
        fetch(window.AccountingReports.glAccUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('gl_account');
                sel.innerHTML = '<option value="">Select GL Account...</option>';
                data.forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.AccountNo;
                    o.textContent = a.AccountName;
                    sel.appendChild(o);
                });
            });


        // ── Load activity accounts on type change ─────────────────────────────────
        window.loadActivityAccounts = function() {
            const type = document.getElementById('aa_type').value;
            const sel = document.getElementById('aa_account');
            sel.innerHTML = '<option value="">Loading...</option>';

            fetch(window.AccountingReports.aaAccUrl + '?type=' + type, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    sel.innerHTML = '<option value="">Select Account...</option>';
                    data.forEach(a => {
                        const o = document.createElement('option');
                        o.value = a.AccountNo;
                        o.textContent = a.AccountName;
                        sel.appendChild(o);
                    });
                });
        };

        // ── Report launchers ──────────────────────────────────────────────────────
        window.viewTrialBalance = function() {
            const d = reqDate('tb_as_at', 'tb_error');
            if (!d) return;
            window.open(window.AccountingReports.tbPrint + '?' +
                new URLSearchParams({
                    as_at: d,
                    branch_id: branch('tb_branch')
                }), '_blank');
        };

        window.viewGlStatement = function() {
            const d = reqDates('gl_date_from', 'gl_date_to', 'gl_error');
            if (!d) return;
            const acct = document.getElementById('gl_account').value;
            if (!acct) {
                document.getElementById('gl_error').textContent = 'Please select an account.';
                document.getElementById('gl_error').style.display = 'block';
                return;
            }
            window.open(window.AccountingReports.glPrint + '?' +
                new URLSearchParams({
                    account_no: acct,
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('gl_branch')
                }), '_blank');
        };

        window.viewIncomeExpenditure = function() {
            const d = reqDates('ie_date_from', 'ie_date_to', 'ie_error');
            if (!d) return;
            window.open(window.AccountingReports.iePrint + '?' +
                new URLSearchParams({
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('ie_branch')
                }), '_blank');
        };

        window.viewDailyBalance = function() {
            const d = reqDate('dbs_date', 'dbs_error');
            if (!d) return;
            window.open(window.AccountingReports.dbsPrint + '?' +
                new URLSearchParams({
                    date: d,
                    branch_id: branch('dbs_branch')
                }), '_blank');
        };

        window.viewWasteSheet = function() {
            const d = reqDates('ws_date_from', 'ws_date_to', 'ws_error');
            if (!d) return;
            const params = {
                date_from: d.dateFrom,
                date_to: d.dateTo,
                branch_id: branch('ws_branch')
            };
            const u = document.getElementById('ws_username').value.trim();
            if (u) params.username = u;
            window.open(window.AccountingReports.wsPrint + '?' + new URLSearchParams(params), '_blank');
        };

        window.viewReceiptRegister = function() {
            const d = reqDates('rr_date_from', 'rr_date_to', 'rr_error');
            if (!d) return;
            const params = {
                date_from: d.dateFrom,
                date_to: d.dateTo,
                branch_id: branch('rr_branch')
            };
            const u = document.getElementById('rr_username').value.trim();
            if (u) params.username = u;
            window.open(window.AccountingReports.rrPrint + '?' + new URLSearchParams(params), '_blank');
        };

        window.viewAccountActivity = function() {
            const d = reqDates('aa_date_from', 'aa_date_to', 'aa_error');
            if (!d) return;
            const acct = document.getElementById('aa_account').value;
            const type = document.getElementById('aa_type').value;
            if (!acct) {
                document.getElementById('aa_error').textContent = 'Please select an account.';
                document.getElementById('aa_error').style.display = 'block';
                return;
            }
            window.open(window.AccountingReports.aaPrint + '?' +
                new URLSearchParams({
                    account_no: acct,
                    account_type: type,
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('aa_branch')
                }), '_blank');
        };

        window.viewBalanceSheet = function() {
            const d = reqDate('bs_as_at', 'bs_error');
            if (!d) return;
            window.open(window.AccountingReports.bsPrint + '?' +
                new URLSearchParams({
                    as_at: d,
                    branch_id: branch('bs_branch')
                }), '_blank');
        };

        window.viewCashFlow = function() {
            const d = reqDates('cf_date_from', 'cf_date_to', 'cf_error');
            if (!d) return;
            window.open(window.AccountingReports.cfPrint + '?' +
                new URLSearchParams({
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('cf_branch')
                }), '_blank');
        };

        // ── Load income & expenditure accounts on page load ───────────────────
        fetch(window.AccountingReports.incAccUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('inc_account');
                sel.innerHTML = '<option value="">Select Income Account...</option>';
                data.forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.AccountNo;
                    o.textContent = a.AccountName;
                    sel.appendChild(o);
                });
            });

        fetch(window.AccountingReports.expAccUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('exp_account');
                sel.innerHTML = '<option value="">Select Expenditure Account...</option>';
                data.forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.AccountNo;
                    o.textContent = a.AccountName;
                    sel.appendChild(o);
                });
            });

        window.viewIncomeStatement = function() {
            const d = reqDates('inc_date_from', 'inc_date_to', 'inc_error');
            if (!d) return;
            const acct = document.getElementById('inc_account').value;
            if (!acct) {
                document.getElementById('inc_error').textContent = 'Please select an income account.';
                document.getElementById('inc_error').style.display = 'block';
                return;
            }
            window.open(window.AccountingReports.incPrint + '?' +
                new URLSearchParams({
                    account_no: acct,
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('inc_branch')
                }), '_blank');
        };

        window.viewExpenditureStatement = function() {
            const d = reqDates('exp_date_from', 'exp_date_to', 'exp_error');
            if (!d) return;
            const acct = document.getElementById('exp_account').value;
            if (!acct) {
                document.getElementById('exp_error').textContent = 'Please select an expenditure account.';
                document.getElementById('exp_error').style.display = 'block';
                return;
            }
            window.open(window.AccountingReports.expPrint + '?' +
                new URLSearchParams({
                    account_no: acct,
                    date_from: d.dateFrom,
                    date_to: d.dateTo,
                    branch_id: branch('exp_branch')
                }), '_blank');
        };

        // ── Init ──────────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            loadBranches();
            loadActivityAccounts();
        });
    </script>
@endpush
