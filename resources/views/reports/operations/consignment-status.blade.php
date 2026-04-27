@extends('layouts.app')

@section('title', 'Operations Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Operations Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">System Reports &rsaquo; Operations</p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:1.25rem; align-items:flex-start;">

            {{-- ── Consignment Status Summary ── --}}
            <div class="card" style="padding:0; width:320px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT SUMMARY REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="ss_date_from" class="form-input"> {{-- RENAMED: date_from → ss_date_from --}}
                    <input type="date" id="ss_date_to" class="form-input"> {{-- RENAMED: date_to → ss_date_to --}}
                    <select id="ss_status" class="form-input"> {{-- RENAMED: status_filter → ss_status --}}
                        <option value="all">All Statuses</option>
                        <option value="1">Not Arrived</option>
                        <option value="2">Pending</option>
                        <option value="3">Gated Out</option>
                        <option value="0">Cleared</option>
                    </select>
                    <button onclick="window.viewConsignmentStatus()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Consignment Detail Report — NEW CARD ── --}}
            <div class="card" style="padding:0; width:320px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT DETAIL REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="cd_date_from" class="form-input">
                    <input type="date" id="cd_date_to" class="form-input">
                    <select id="cd_status" class="form-input">
                        <option value="all">All Statuses</option>
                        <option value="1">Not Arrived</option>
                        <option value="2">Pending</option>
                        <option value="3">Gated Out</option>
                        <option value="0">Cleared</option>
                    </select>
                    <button onclick="window.viewConsignmentDetail()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

        </div> {{-- END of flex row --}}
    </div>

    </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.ConsignmentStatusReport = {
            printUrl: '{{ route('reports.operations.consignment-status.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-status.export') }}',
        };

        window.ConsignmentDetailReport = {
            printUrl: '{{ route('reports.operations.consignment-detail.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-detail.export') }}',
        };

        window.viewConsignmentStatus = function() {
            const dateFrom = document.getElementById('ss_date_from').value;
            const dateTo = document.getElementById('ss_date_to').value;
            const status = document.getElementById('ss_status').value;

            if (!dateFrom || !dateTo) {
                alert('Please select both Date From and Date To.');
                return;
            }

            const url = window.ConsignmentStatusReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                });

            window.open(url, '_blank');
        };

        window.viewConsignmentDetail = function() {
            const dateFrom = document.getElementById('cd_date_from').value;
            const dateTo = document.getElementById('cd_date_to').value;
            const status = document.getElementById('cd_status').value;

            if (!dateFrom || !dateTo) {
                alert('Please select both Date From and Date To.');
                return;
            }

            const url = window.ConsignmentDetailReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                });

            window.open(url, '_blank');
        };
    </script>
@endpush
