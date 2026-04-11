@extends('layouts.app')

@section('title', 'Customer Waybill')
@section('page-title', 'Customer Waybill')

@section('content')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">

        {{-- ── Left Column ── --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- House BL Search --}}
            <div class="card">
                <p class="form-title">House BL Search</p>
                <p class="form-subtitle">Search existing waybill by consignee or vehicle number</p>
                <div style="display: flex; gap: 8px; margin-top: 0.75rem;">
                    <input type="text" id="search-input" placeholder="Enter Consignee or Vehicle #" class="form-input"
                        oninput="debounceSearch()">
                </div>
                <div id="search-results"
                    style="display: none; margin-top: 8px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; max-height: 200px; overflow-y: auto;">
                </div>
            </div>

            {{-- Waybill Details --}}
            <div class="card">
                <p class="form-title">Waybill Details</p>
                <p class="form-subtitle">Fill in the waybill information</p>

                <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Consignee Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="consignee" class="form-input" placeholder="Full name of consignee">
                        <p id="consignee-error" class="form-error"></p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Vehicle No. <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="vehicle-no" class="form-input" style="text-transform: uppercase;">
                            <p id="vehicle-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Driver's Name <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="driver-name" class="form-input">
                            <p id="driver-name-error" class="form-error"></p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Port <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="port" class="form-input">
                            <p id="port-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Driver's License No. <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="driver-license" class="form-input" style="text-transform: uppercase;">
                            <p id="driver-license-error" class="form-error"></p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Package <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="package" class="form-input" placeholder="e.g. Carton, Box">
                            <p id="package-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Description <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="description" class="form-input">
                            <p id="description-error" class="form-error"></p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Quantity <span style="color: #ef4444;">*</span></label>
                            <input type="number" id="quantity" min="1" class="form-input">
                            <p id="quantity-error" class="form-error"></p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Date <span style="color: #ef4444;">*</span></label>
                            <input type="date" id="waybill-date" class="form-input"
                                value="{{ now()->toDateString() }}">
                            <p id="date-error" class="form-error"></p>
                        </div>
                    </div>

                    <p id="submit-error" class="form-error" style="text-align: center;"></p>
                    <p id="submit-success" class="form-success" style="text-align: center;"></p>

                    <button onclick="saveWaybill()" id="save-btn"
                        style="width: 100%; padding: 14px; border-radius: 10px; border: none; background: #16a34a; color: white; font-size: 0.925rem; font-weight: 600; cursor: pointer;">
                        Add New Waybill
                    </button>
                </div>
            </div>

        </div>

        {{-- ── Right Column ── --}}
        <div class="card">
            <p class="form-title">Existing Waybill</p>
            <p class="form-subtitle">Recent waybills matching your search</p>

            <div id="existing-waybills" style="margin-top: 0.75rem;">
                <div
                    style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
                    Search by consignee name or vehicle number to find existing waybills.
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let searchTimer = null;

        function debounceSearch() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(doSearch, 400);
        }

        function doSearch() {
            const q = document.getElementById('search-input').value.trim();
            if (!q || q.length < 2) return;

            fetch(`{{ route('invoice.waybill.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const existing = document.getElementById('existing-waybills');

                    if (!data.length) {
                        existing.innerHTML =
                            `<div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">No waybills found.</div>`;
                        return;
                    }

                    existing.innerHTML = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Consignee</th>
                        <th>Vehicle</th>
                        <th>Date</th>
                        <th style="text-align: center;">Print</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map(w => `
                            <tr>
                                <td class="td-muted">${w.id}</td>
                                <td style="font-weight: 500; color: var(--text-primary); font-size: 0.8rem;">${w.Consignee}</td>
                                <td class="td-mono">${w.VehicleNo}</td>
                                <td class="td-muted">${w.WaybillDate}</td>
                                <td style="text-align: center;">
                                    <button onclick="window.open('{{ url('invoice/waybill/report') }}/${w.id}', '_blank')"
                                        class="btn-icon" title="Print"
                                        style="background: rgba(22,163,74,0.1); color: #16a34a; border: 1px solid rgba(22,163,74,0.2);">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>`).join('')}
                </tbody>
            </table>`;
                });
        }

        function saveWaybill() {
            const btn = document.getElementById('save-btn');
            const errorEl = document.getElementById('submit-error');
            const successEl = document.getElementById('submit-success');

            errorEl.classList.remove('visible');
            successEl.classList.remove('visible');

            const fields = {
                Consignee: document.getElementById('consignee').value.trim(),
                VehicleNo: document.getElementById('vehicle-no').value.trim().toUpperCase(),
                DriverName: document.getElementById('driver-name').value.trim(),
                Port: document.getElementById('port').value.trim(),
                DriverLicense: document.getElementById('driver-license').value.trim().toUpperCase(),
                Package: document.getElementById('package').value.trim(),
                Description: document.getElementById('description').value.trim(),
                Quantity: document.getElementById('quantity').value,
                WaybillDate: document.getElementById('waybill-date').value,
            };

            // Validate
            let valid = true;
            const checks = [
                ['consignee-error', !fields.Consignee, 'Consignee name is required.'],
                ['vehicle-error', !fields.VehicleNo, 'Vehicle number is required.'],
                ['driver-name-error', !fields.DriverName, "Driver's name is required."],
                ['port-error', !fields.Port, 'Port is required.'],
                ['driver-license-error', !fields.DriverLicense, "Driver's license is required."],
                ['package-error', !fields.Package, 'Package is required.'],
                ['description-error', !fields.Description, 'Description is required.'],
                ['quantity-error', !fields.Quantity || parseInt(fields.Quantity) < 1, 'Quantity must be at least 1.'],
                ['date-error', !fields.WaybillDate, 'Date is required.'],
            ];

            checks.forEach(([errId, condition, msg]) => {
                const el = document.getElementById(errId);
                el.classList.remove('visible');
                if (condition) {
                    el.textContent = msg;
                    el.classList.add('visible');
                    valid = false;
                }
            });

            if (!valid) return;

            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch('{{ route('invoice.waybill.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(fields),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        successEl.textContent = data.message;
                        successEl.classList.add('visible');
                        setTimeout(() => {
                            window.open(`{{ url('invoice/waybill/report') }}/${data.id}`, '_blank');
                            // Reset form
                            ['consignee', 'vehicle-no', 'driver-name', 'port', 'driver-license', 'package',
                                'description', 'quantity'
                            ].forEach(id => {
                                document.getElementById(id).value = '';
                            });
                            document.getElementById('waybill-date').value = '{{ now()->toDateString() }}';
                            successEl.classList.remove('visible');
                        }, 1500);
                    } else {
                        errorEl.textContent = data.message ?? 'Failed to save waybill.';
                        errorEl.classList.add('visible');
                    }
                })
                .catch(() => {
                    errorEl.textContent = 'Something went wrong.';
                    errorEl.classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Add New Waybill';
                    btn.disabled = false;
                });
        }
    </script>
@endpush
