@extends('layouts.app')

@section('title', 'Operations Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Operations Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">System Reports &rsaquo; Operations</p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:1.25rem; align-items:flex-start;">

            {{-- Consignment Status Summary --}}
            <div class="card" style="padding:0; width:320px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT STATUS SUMMARY
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="date_from" class="form-input">
                    <input type="date" id="date_to" class="form-input">
                    <select id="status_filter" class="form-input">
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

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.ConsignmentStatusReport = {
            printUrl: '{{ route('reports.operations.consignment-status.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-status.export') }}',
        };

        window.viewConsignmentStatus = function() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const status = document.getElementById('status_filter').value;

            if (!dateFrom || !dateTo) {
                alert('Please select both Date From and Date To.');
                return;
            }

            const url = window.ConsignmentStatusReport.printUrl +
                '?' + new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                });

            window.open(url, '_blank');
        };
    </script>
@endpush
