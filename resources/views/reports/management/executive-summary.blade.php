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

        <div style="display:flex; flex-wrap:wrap; gap:1.25rem; align-items:flex-start;">

            {{-- Executive Summary card --}}
            <div class="card" style="padding:0; width:320px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        EXECUTIVE SUMMARY
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="date_from" class="form-input">
                    <input type="date" id="date_to" class="form-input">
                    <button onclick="window.viewReport()" class="btn btn-primary" style="width:100%; margin-top:0.25rem;">
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

        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const firstDay = new Date(
                new Date().getFullYear(),
                new Date().getMonth(), 1
            ).toISOString().split('T')[0];

            document.getElementById('date_from').value = firstDay;
            document.getElementById('date_to').value = today;
        });
    </script>
@endpush
