@extends('layouts.app')

@section('title', 'Management Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Management Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                System Reports &rsaquo; Management
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.25rem; align-items:stretch;">

            {{-- Executive Summary card --}}
            <div class="card" style="padding:0; display:flex; flex-direction:column;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        EXECUTIVE SUMMARY
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem; flex:1;">
                    <input type="date" id="date_from" class="form-input">
                    <input type="date" id="date_to" class="form-input">
                    <button onclick="window.viewReport()" class="btn btn-primary" style="width:100%; margin-top:auto;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- Outstanding Collections card --}}
            <div class="card" style="padding:0; display:flex; flex-direction:column;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        OUTSTANDING COLLECTIONS
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem; flex:1;">
                    <p style="font-size:0.75rem; color:var(--text-muted); line-height:1.5;">
                        Shows all unpaid client charges aged by days outstanding as at a selected date.
                    </p>
                    <input type="date" id="oc_as_at" class="form-input">
                    <button onclick="window.viewOutstandingCollections()" class="btn btn-primary"
                        style="width:100%; margin-top:auto;">
                        View Report
                    </button>
                </div>
            </div>


            {{-- Financial Performance card  ----- --}}
            <div class="card" style="padding:0; display:flex; flex-direction:column;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        FINANCIAL PERFORMANCE
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem; flex:1;">
                    {{-- Month picker --}}
                    <input type="month" id="fp_period" class="form-input">
                    {{-- Comparison type --}}
                    <select id="fp_compare" class="form-input">
                        <option value="prev_month">vs Previous Month</option>
                        <option value="same_month_last_year">vs Same Month Last Year</option>
                        <option value="year_on_year">Year on Year (YTD)</option>
                    </select>
                    <button onclick="window.viewFinancialPerformance()" class="btn btn-primary"
                        style="width:100%; margin-top:auto;">
                        View Report
                    </button>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.ExecutiveSummary = {
            printUrl: '{{ route('reports.management.executive-summary.print') }}'
        };

        window.viewReport = function() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            if (!dateFrom || !dateTo) {
                alert('Please select both Date From and Date To.');
                return;
            }

            if (dateTo < dateFrom) {
                alert('Date To cannot be before Date From.');
                return;
            }

            const url = window.ExecutiveSummary.printUrl +
                '?date_from=' + dateFrom +
                '&date_to=' + dateTo;

            window.open(url, '_blank');
        };

        window.OutstandingCollections = {
            printUrl: '{{ route('reports.management.outstanding-collections.print') }}'
        };

        window.viewOutstandingCollections = function() {
            const asAt = document.getElementById('oc_as_at').value;

            if (!asAt) {
                alert('Please select an As At date.');
                return;
            }

            const url = window.OutstandingCollections.printUrl + '?as_at=' + asAt;
            window.open(url, '_blank');
        };

        // ── Financial Performance ──────────────────────────────────────────────
        window.FinancialPerformance = {
            printUrl: '{{ route('reports.management.financial-performance.print') }}'
        };

        window.viewFinancialPerformance = function() {
            const period = document.getElementById('fp_period').value;
            const compare = document.getElementById('fp_compare').value;

            if (!period) {
                alert('Please select a period.');
                return;
            }

            const url = window.FinancialPerformance.printUrl +
                '?period=' + period +
                '&compare=' + compare;

            window.open(url, '_blank');
        };

        document.addEventListener('DOMContentLoaded', function() {
            const nowDate = new Date();
            const todayStr = nowDate.toISOString().split('T')[0];
            const firstDay = new Date(
                nowDate.getFullYear(),
                nowDate.getMonth(), 1
            ).toISOString().split('T')[0];

            document.getElementById('date_from').value = firstDay;
            document.getElementById('date_to').value = todayStr;
            document.getElementById('oc_as_at').value = todayStr;

            // fp_period — current month in YYYY-MM format
            const yyyy = nowDate.getFullYear();
            const mm = String(nowDate.getMonth() + 1).padStart(2, '0');
            document.getElementById('fp_period').value = yyyy + '-' + mm;
        });
    </script>
@endpush
