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

            {{-- Outstanding Collections card --}}
            <div class="card" style="padding:0; width:320px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        OUTSTANDING COLLECTIONS
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="as_at" class="form-input">
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
        window.OutstandingCollections = {
            printUrl: '{{ route('reports.management.outstanding-collections.print') }}'
        };

        window.viewReport = function() {
            const asAt = document.getElementById('as_at').value;

            if (!asAt) {
                alert('Please select an As At date.');
                return;
            }

            const url = window.OutstandingCollections.printUrl + '?as_at=' + asAt;
            window.open(url, '_blank');
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('as_at').value = new Date().toISOString().split('T')[0];
        });
    </script>
@endpush
