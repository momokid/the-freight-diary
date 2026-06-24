@extends('layouts.app')

@section('title', 'Messaging Center')
@section('page-title', 'Messaging Center')

@section('content')

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ── Send Message Panel ── --}}
        <div class="card">
            <p class="form-title">Send Message</p>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">
                Search a consignment and send a notification to the client.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: end;">

                {{-- BL Search --}}
                <div class="form-group" style="margin-bottom: 0; position: relative;">
                    <label class="form-label">Search BL# <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="mc-bl-input" class="form-input" placeholder="Type BL number..."
                        autocomplete="off" style="text-transform: uppercase;">
                    <div id="mc-bl-dropdown"
                        style="display: none; position: absolute; z-index: 100;
                           background: var(--card-bg); border: 1px solid var(--border-color);
                           border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                           width: 100%; top: calc(100% + 4px); max-height: 220px; overflow-y: auto;">
                    </div>
                    <input type="hidden" id="mc-bl-value">
                    <input type="hidden" id="mc-consignee-id">
                    <input type="hidden" id="mc-client-code">
                    <p id="mc-bl-error" class="form-error"></p>
                </div>

                {{-- Message Type --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Message Type <span style="color: #ef4444;">*</span></label>
                    <select id="mc-event" class="form-input">
                        <option value="registration">Consignment Registration</option>
                        <option value="gate_out">Gate-Out Release</option>
                        <option value="invoice_payment">Payment Received</option>
                        <option value="manual">Custom Message</option>
                    </select>
                </div>

                {{-- Send Button --}}
                <div>
                    <button onclick="openMcSmsModal()"
                        style="width: 100%; padding: 10px; border-radius: 8px; border: none;
                           background: #16a34a; color: white; font-size: 0.875rem;
                           font-weight: 600; cursor: pointer;">
                        Compose &amp; Send
                    </button>
                </div>
            </div>

            {{-- Consignment meta --}}
            <div id="mc-consignment-meta"
                style="display: none; margin-top: 1rem; padding: 0.6rem 0.75rem;
                   border-radius: 6px; background: var(--content-bg);">
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">Consignee</p>
                        <p id="mc-meta-consignee" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);">
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">Vessel</p>
                        <p id="mc-meta-vessel" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);">
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">ETA</p>
                        <p id="mc-meta-eta" style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary);"></p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; color: var(--text-muted);">Client Code</p>
                        <p id="mc-meta-code"
                            style="font-size: 0.82rem; font-weight: 700; color: #16a34a; font-family: monospace;"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Filters ── --}}
        <div class="card">
            <form method="GET" action="{{ route('messaging.index') }}"
                style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">

                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
                    <label class="form-label">Filter by BL#</label>
                    <input type="text" name="bl" value="{{ request('bl') }}" class="form-input"
                        placeholder="e.g. MSCU123..." style="text-transform: uppercase;">
                </div>

                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
                    <label class="form-label">Message Type</label>
                    <select name="event" class="form-input">
                        <option value="">All types</option>
                        <option value="registration" {{ request('event') === 'registration' ? 'selected' : '' }}>
                            Registration</option>
                        <option value="gate_out" {{ request('event') === 'gate_out' ? 'selected' : '' }}>
                            Gate-Out</option>
                        <option value="invoice_payment" {{ request('event') === 'invoice_payment' ? 'selected' : '' }}>
                            Payment</option>
                        <option value="manual" {{ request('event') === 'manual' ? 'selected' : '' }}>
                            Manual</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn-primary" style="padding: 8px 16px; font-size: 0.8rem;">
                        Filter
                    </button>
                    <a href="{{ route('messaging.index') }}"
                        style="padding: 8px 16px; font-size: 0.8rem; border-radius: 6px;
                           border: 1px solid var(--border-color); background: var(--card-bg);
                           color: var(--text-primary); text-decoration: none;">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- ── Messages Table ── --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <p class="form-title">Message History</p>
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    {{ $messages->total() }} message(s) found
                </p>
            </div>

            <table class="data-table" style="font-size: 0.8rem;">
                <thead>
                    <tr>
                        <th>BL#</th>
                        <th>Consignee</th>
                        <th>Type</th>
                        <th>Channel</th>
                        <th>Phone</th>
                        <th>Sent By</th>
                        <th>Date</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $msg)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">{{ $msg->BL }}</td>
                            <td style="font-size: 0.78rem;">{{ $msg->ConsigneeName ?? '—' }}</td>
                            <td>
                                @php
                                    $eventLabels = [
                                        'registration' => ['label' => 'Registration', 'color' => '#16a34a'],
                                        'gate_out' => ['label' => 'Gate-Out', 'color' => '#d97706'],
                                        'invoice_payment' => ['label' => 'Payment', 'color' => '#2563eb'],
                                        'manual' => ['label' => 'Manual', 'color' => '#7c3aed'],
                                    ];
                                    $ev = $eventLabels[$msg->event] ?? ['label' => $msg->event, 'color' => '#64748b'];
                                @endphp
                                <span
                                    style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px;
                                    border-radius: 10px; color: {{ $ev['color'] }};
                                    background: {{ $ev['color'] }}1a;">
                                    {{ $ev['label'] }}
                                </span>
                            </td>
                            <td style="text-transform: uppercase; font-size: 0.75rem;">{{ $msg->channel }}</td>
                            <td style="font-family: monospace;">{{ $msg->phone }}</td>
                            <td style="font-size: 0.75rem;">{{ $msg->sent_by ?? '—' }}</td>
                            <td style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('d M Y H:i') }}
                            </td>
                            <td style="text-align: center;">
                                @if ($msg->status === 'sent')
                                    <span
                                        style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px;
                                        border-radius: 10px; background: rgba(22,163,74,0.12); color: #16a34a;">
                                        ✓ Sent
                                    </span>
                                @else
                                    <span
                                        style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px;
                                        border-radius: 10px; background: rgba(220,38,38,0.12); color: #dc2626;">
                                        ✗ Failed
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button onclick="viewMessage('{{ addslashes($msg->message) }}')"
                                    style="padding: 3px 10px; font-size: 0.72rem; border-radius: 5px;
                                       border: 1px solid var(--border-color); background: var(--card-bg);
                                       color: var(--text-primary); cursor: pointer;">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($messages->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- SMS Modal component --}}
    <x-sms-modal />

    {{-- Message Preview Modal --}}
    <div id="message-preview-modal"
        style="display: none; position: fixed; inset: 0; z-index: 50;
           align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
        <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <p class="form-title">Message Content</p>
                <button onclick="document.getElementById('message-preview-modal').style.display='none'"
                    style="background: none; border: none; cursor: pointer;
                       color: var(--text-muted); font-size: 1.2rem;">✕</button>
            </div>
            <p id="message-preview-content"
                style="font-size: 0.82rem; color: var(--text-primary);
                   white-space: pre-wrap; line-height: 1.6;">
            </p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let _mcSearchTimer = null;

        // ── BL Typeahead ─────────────────────────────────────────────────────────
        document.getElementById('mc-bl-input').addEventListener('input', function() {
            clearTimeout(_mcSearchTimer);
            const q = this.value.trim();
            if (q.length < 2) {
                document.getElementById('mc-bl-dropdown').style.display = 'none';
                return;
            }
            _mcSearchTimer = setTimeout(() => searchMcBL(q), 300);
        });

        function searchMcBL(q) {
            fetch(`{{ route('disbursement.analysis.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(results => {
                    const dropdown = document.getElementById('mc-bl-dropdown');
                    if (!results.length) {
                        dropdown.style.display = 'none';
                        return;
                    }
                    dropdown.innerHTML = results.map(r => `
                    <div onclick="selectMcBL('${r.BL}', '${r.ConsigneeName}', '${r.VesselName}', '${r.ETA}')"
                        style="padding: 10px 14px; cursor: pointer; font-size: 0.8rem;
                               border-bottom: 1px solid var(--border-color);"
                        onmouseover="this.style.background='var(--content-bg)'"
                        onmouseout="this.style.background=''">
                        <div style="font-weight: 600; font-family: monospace;">${r.BL}</div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;">${r.ConsigneeName}</div>
                    </div>`).join('');
                    dropdown.style.display = 'block';
                });
        }

        function selectMcBL(bl, consigneeName, vessel, eta) {
            document.getElementById('mc-bl-input').value = bl;
            document.getElementById('mc-bl-value').value = bl;
            document.getElementById('mc-bl-dropdown').style.display = 'none';

            // Fetch consignment details to get ConsigneeID and ClientCode
            fetch(`{{ route('disbursement.analysis.search') }}?q=${encodeURIComponent(bl)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(results => {
                    const match = results.find(r => r.BL === bl);
                    if (match) {
                        document.getElementById('mc-consignee-id').value = match.ConsignmentID ?? 0;
                    }
                });

            // Fetch ClientCode from container_main
            fetch(`/consignments/client-code/${bl}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('mc-client-code').value = data.client_code ?? '';
                        document.getElementById('mc-meta-code').textContent = data.client_code ?? '—';
                        document.getElementById('mc-consignee-id').value = data.consignee_id ?? 0;
                    }
                })
                .catch(() => {});

            document.getElementById('mc-meta-consignee').textContent = consigneeName;
            document.getElementById('mc-meta-vessel').textContent = vessel;
            document.getElementById('mc-meta-eta').textContent = eta;
            document.getElementById('mc-consignment-meta').style.display = 'block';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#mc-bl-input') && !e.target.closest('#mc-bl-dropdown')) {
                document.getElementById('mc-bl-dropdown').style.display = 'none';
            }
        });

        // ── Open SMS Modal from Messaging Center ──────────────────────────────────
        function openMcSmsModal() {
            const bl = document.getElementById('mc-bl-value').value;
            const consigneeId = parseInt(document.getElementById('mc-consignee-id').value) || 0;
            const clientCode = document.getElementById('mc-client-code').value;
            const event = document.getElementById('mc-event').value;

            if (!bl) {
                document.getElementById('mc-bl-error').textContent = 'Please search and select a BL first.';
                document.getElementById('mc-bl-error').classList.add('visible');
                return;
            }

            document.getElementById('mc-bl-error').classList.remove('visible');
            openSmsModal(bl, clientCode, consigneeId, '{{ route('messaging.send') }}', event);
        }

        // ── View message content ──────────────────────────────────────────────────
        function viewMessage(message) {
            document.getElementById('message-preview-content').textContent = message;
            document.getElementById('message-preview-modal').style.display = 'flex';
        }

        // ── Auto-reload message history on send ──────────────────────────────────
        window.onSmsSent = function() {
            loadMessageHistory();
        };

        function loadMessageHistory() {
            const params = new URLSearchParams(window.location.search);

            fetch(`{{ route('messaging.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.card:last-of-type');
                    const currentTable = document.querySelector('.card:last-of-type');
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }
                })
                .catch(() => {});
        }
    </script>
@endpush
