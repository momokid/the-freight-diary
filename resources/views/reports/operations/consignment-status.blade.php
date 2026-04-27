@extends('layouts.app')

@section('title', 'Operations Reports')

@section('content')

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <div>
            <h1 class="page-title">Operations Reports</h1>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">System Reports &rsaquo; Operations</p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.25rem; align-items:flex-start;">
            {{-- ── Card 1: Consignment Status Summary ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT STATUS SUMMARY
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="ss_date_from" class="form-input">
                    <input type="date" id="ss_date_to" class="form-input">
                    <select id="ss_status" class="form-input">
                        <option value="all">All Statuses</option>
                        <option value="1">Not Arrived</option>
                        <option value="2">Pending</option>
                        <option value="3">Gated Out</option>
                        <option value="0">Cleared</option>
                    </select>
                    <p id="ss_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewConsignmentStatus()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 2: Consignment Detail Report ── --}}
            <div class="card" style="padding:0;">
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
                    <p id="cd_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewConsignmentDetail()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 3: Consignment Carrier Report ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT CARRIER REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="cc_date_from" class="form-input" onchange="window.loadCarriers()">
                    <input type="date" id="cc_date_to" class="form-input" onchange="window.loadCarriers()">
                    <select id="cc_carrier" class="form-input">
                        <option value="">Loading carriers...</option>
                    </select>
                    <p id="cc_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewConsignmentCarrier()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 4: Port Aging Report ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        PORT AGING REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="pa_date_from" class="form-input">
                    <input type="date" id="pa_date_to" class="form-input">
                    <select id="pa_status" class="form-input">
                        <option value="all">All Statuses</option>
                        <option value="1">Not Arrived</option>
                        <option value="2">Pending</option>
                        <option value="3">Gated Out</option>
                        <option value="0">Cleared</option>
                    </select>
                    <p id="pa_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewPortAging()" class="btn-primary" style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 5: Pending Clearance Report ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        PENDING CLEARANCE REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="pc_date_from" class="form-input">
                    <input type="date" id="pc_date_to" class="form-input">
                    <select id="pc_status" class="form-input">
                        <option value="all">All Statuses</option>
                        <option value="1">Not Arrived</option>
                        <option value="2">Pending</option>
                        <option value="3">Gated Out</option>
                        <option value="0">Cleared</option>
                    </select>
                    <p id="pc_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewPendingClearance()" class="btn-primary"
                        style="width:100%; margin-top:0.25rem;">
                        View Report
                    </button>
                </div>
            </div>

            {{-- ── Card 6: Consignment Volume Report ── --}}
            <div class="card" style="padding:0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);">
                    <p style="font-size:0.8rem; font-weight:700; color:#185FA5; letter-spacing:0.04em;">
                        CONSIGNMENT VOLUME REPORT
                    </p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                    <input type="date" id="cv_date_from" class="form-input">
                    <input type="date" id="cv_date_to" class="form-input">
                    <p id="cv_error" style="display:none; font-size:0.75rem; color:#b91c1c; text-align:center;"></p>
                    <button onclick="window.viewConsignmentVolume()" class="btn-primary"
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
        // ── Route config ─────────────────────────────────────────────────────────
        window.ConsignmentStatusReport = {
            printUrl: '{{ route('reports.operations.consignment-status.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-status.export') }}',
        };

        window.ConsignmentDetailReport = {
            printUrl: '{{ route('reports.operations.consignment-detail.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-detail.export') }}',
        };

        window.ConsignmentCarrierReport = {
            printUrl: '{{ route('reports.operations.consignment-carrier.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-carrier.export') }}',
            carriersUrl: '{{ route('reports.operations.consignment-carrier.carriers') }}',
        };

        window.PortAgingReport = {
            printUrl: '{{ route('reports.operations.port-aging.print') }}',
            exportUrl: '{{ route('reports.operations.port-aging.export') }}',
        };

        window.PendingClearanceReport = {
            printUrl: '{{ route('reports.operations.pending-clearance.print') }}',
            exportUrl: '{{ route('reports.operations.pending-clearance.export') }}',
        };

        window.ConsignmentVolumeReport = {
            printUrl: '{{ route('reports.operations.consignment-volume.print') }}',
            exportUrl: '{{ route('reports.operations.consignment-volume.export') }}',
        };

        // ── Card 1: Consignment Status Summary ───────────────────────────────────
        window.viewConsignmentStatus = function() {
            const dateFrom = document.getElementById('ss_date_from').value;
            const dateTo = document.getElementById('ss_date_to').value;
            const status = document.getElementById('ss_status').value;

            const ssErr = document.getElementById('ss_error');
            if (!dateFrom || !dateTo) {
                ssErr.textContent = 'Please select both Date From and Date To.';
                ssErr.style.display = 'block';
                return;
            }
            ssErr.style.display = 'none';

            window.open(
                window.ConsignmentStatusReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                }),
                '_blank'
            );
        };

        // ── Card 2: Consignment Detail Report ────────────────────────────────────
        window.viewConsignmentDetail = function() {
            const dateFrom = document.getElementById('cd_date_from').value;
            const dateTo = document.getElementById('cd_date_to').value;
            const status = document.getElementById('cd_status').value;

            const cdErr = document.getElementById('cd_error');
            if (!dateFrom || !dateTo) {
                cdErr.textContent = 'Please select both Date From and Date To.';
                cdErr.style.display = 'block';
                return;
            }
            cdErr.style.display = 'none';

            window.open(
                window.ConsignmentDetailReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                }),
                '_blank'
            );
        };

        // ── Card 3: Consignment Carrier Report ───────────────────────────────────
        window.viewConsignmentCarrier = function() {
            const dateFrom = document.getElementById('cc_date_from').value;
            const dateTo = document.getElementById('cc_date_to').value;
            const carrierId = document.getElementById('cc_carrier').value;

            const ccErr = document.getElementById('cc_error');
            if (!dateFrom || !dateTo) {
                ccErr.textContent = 'Please select both Date From and Date To.';
                ccErr.style.display = 'block';
                return;
            }
            ccErr.style.display = 'none';

            const params = {
                date_from: dateFrom,
                date_to: dateTo
            };
            if (carrierId) params.carrier_id = carrierId;

            window.open(
                window.ConsignmentCarrierReport.printUrl + '?' +
                new URLSearchParams(params),
                '_blank'
            );
        };

        // ── Card 4: Port Aging Report ─────────────────────────────────────────────
        window.viewPortAging = function() {
            const dateFrom = document.getElementById('pa_date_from').value;
            const dateTo = document.getElementById('pa_date_to').value;
            const status = document.getElementById('pa_status').value;
            const paErr = document.getElementById('pa_error');

            if (!dateFrom || !dateTo) {
                paErr.textContent = 'Please select both Date From and Date To.';
                paErr.style.display = 'block';
                return;
            }

            paErr.style.display = 'none';

            window.open(
                window.PortAgingReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                }),
                '_blank'
            );
        };

        // ── Card 5: Pending Clearance Report ─────────────────────────────────────
        window.viewPendingClearance = function() {
            const dateFrom = document.getElementById('pc_date_from').value;
            const dateTo = document.getElementById('pc_date_to').value;
            const status = document.getElementById('pc_status').value;
            const pcErr = document.getElementById('pc_error');

            if (!dateFrom || !dateTo) {
                pcErr.textContent = 'Please select both Date From and Date To.';
                pcErr.style.display = 'block';
                return;
            }

            pcErr.style.display = 'none';

            window.open(
                window.PendingClearanceReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo,
                    status
                }),
                '_blank'
            );
        };

        // ── Card 6: Consignment Volume Report ────────────────────────────────────
        window.viewConsignmentVolume = function() {
            const dateFrom = document.getElementById('cv_date_from').value;
            const dateTo = document.getElementById('cv_date_to').value;
            const cvErr = document.getElementById('cv_error');

            if (!dateFrom || !dateTo) {
                cvErr.textContent = 'Please select both Date From and Date To.';
                cvErr.style.display = 'block';
                return;
            }

            cvErr.style.display = 'none';

            window.open(
                window.ConsignmentVolumeReport.printUrl + '?' +
                new URLSearchParams({
                    date_from: dateFrom,
                    date_to: dateTo
                }),
                '_blank'
            );
        };

        // ── Load carriers based on selected dates ────────────────────────────────
        // Fires when either date changes — only fetches if both dates are filled.
        window.loadCarriers = function() {
            const dateFrom = document.getElementById('cc_date_from').value;
            const dateTo = document.getElementById('cc_date_to').value;
            const sel = document.getElementById('cc_carrier');

            if (!dateFrom || !dateTo) return;

            sel.innerHTML = '<option value="">Loading...</option>';

            fetch(window.ConsignmentCarrierReport.carriersUrl + '?' +
                    new URLSearchParams({
                        date_from: dateFrom,
                        date_to: dateTo
                    }), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(res => res.json())
                .then(data => {
                    sel.innerHTML = '<option value="">All Carriers</option>';
                    if (data.length === 0) {
                        sel.innerHTML = '<option value="">No carriers found</option>';
                        return;
                    }
                    data.forEach(function(c) {
                        const opt = document.createElement('option');
                        opt.value = c.CarrierID;
                        opt.textContent = c.CarrierName;
                        sel.appendChild(opt);
                    });
                })
                .catch(function() {
                    sel.innerHTML = '<option value="">All Carriers</option>';
                });
        };
    </script>
@endpush
