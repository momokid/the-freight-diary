{{-- Arrival SMS Queue Modal --}}
{{-- Included globally in layouts/app.blade.php --}}
{{-- Appears on page load when pending arrival SMS rows exist --}}

<div id="arrival-sms-modal"
    style="display:none; position:fixed; inset:0; z-index:60;
           align-items:center; justify-content:center;
           background:rgba(0,0,0,0.55);">

    <div class="card"
        style="width:100%; max-width:680px; margin:1rem;
               max-height:90vh; overflow-y:auto; padding:0;">

        {{-- Header --}}
        <div
            style="padding:1.25rem 1.25rem 1rem; border-bottom:1px solid var(--border-color);
                    display:flex; align-items:center; justify-content:space-between;">
            <div>
                <p class="form-title" style="font-size:0.95rem;">
                    📦 Arrival SMS Queue
                </p>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                    Consignments arriving today — review and send SMS to each client.
                </p>
            </div>
            <button onclick="closeArrivalSmsModal()"
                style="background:none; border:none; cursor:pointer;
                       color:var(--text-muted); font-size:1.2rem;">✕</button>
        </div>

        {{-- Row list --}}
        <div id="arrival-sms-list" style="padding:1rem 1.25rem; display:flex; flex-direction:column; gap:1rem;">
            {{-- Rows injected by JS --}}
        </div>

        {{-- Empty state --}}
        <div id="arrival-sms-empty"
            style="display:none; padding:2rem; text-align:center; color:var(--text-muted); font-size:0.85rem;">
            ✅ All arrival SMS messages have been sent for today.
        </div>

        {{-- Footer --}}
        <div id="arrival-sms-footer"
            style="padding:1rem 1.25rem; border-top:1px solid var(--border-color);
                   display:flex; align-items:center; justify-content:space-between; gap:1rem;">
            <p id="arrival-sms-status" style="font-size:0.78rem; color:var(--text-muted); flex:1;"></p>
            <button onclick="closeArrivalSmsModal()" class="btn-secondary"
                style="width:auto; padding:8px 20px; font-size:0.82rem;">
                Close
            </button>
            <button id="arrival-send-all-btn" onclick="sendAllArrivalSms()" class="btn-primary"
                style="width:auto; padding:8px 20px; font-size:0.82rem; background:#185FA5;">
                Send All
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
            // ── State ──────────────────────────────────────────────────────────
            let _arrivalRows = [];

            // ── Init on page load ──────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function() {
                    @auth
                    @if (auth()->user() &&
                            \App\Models\UserAuth::where('Username', auth()->user()->ID)->first()
                                ?->hasPermission('SendArrivalSms'))
                        fetchArrivalQueue();
                    @endif
                @endauth
            });

        // ── Fetch pending rows ─────────────────────────────────────────────
        function fetchArrivalQueue() {
            fetch('{{ route('arrival-sms.pending') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.count > 0) {
                        _arrivalRows = data.rows;
                        renderArrivalRows();
                        document.getElementById('arrival-sms-modal').style.display = 'flex';
                    }
                })
                .catch(() => {});
        }

        // ── Render rows ────────────────────────────────────────────────────
        function renderArrivalRows() {
            const list = document.getElementById('arrival-sms-list');
            list.innerHTML = '';

            const pending = _arrivalRows.filter(r => !r._sent);

            if (pending.length === 0) {
                list.style.display = 'none';
                document.getElementById('arrival-sms-empty').style.display = 'block';
                document.getElementById('arrival-send-all-btn').style.display = 'none';
                return;
            }

            pending.forEach(function(row) {
                const card = document.createElement('div');
                card.id = 'arrival-row-' + row.id;
                card.style.cssText = 'border:1px solid var(--border-color); border-radius:10px; padding:1rem;';

                card.innerHTML = `
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:0.75rem; gap:1rem;">
                        <div>
                            <p style="font-size:0.82rem; font-weight:700; color:var(--text-primary); font-family:monospace;">
                                ${escHtml(row.BL)}
                            </p>
                            <p style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">
                                ${escHtml(row.ConsigneeName)}
                                &nbsp;·&nbsp; ${row.ContainerCount} container${row.ContainerCount !== 1 ? 's' : ''}
                                &nbsp;·&nbsp; ETA: ${escHtml(row.ETA)}
                            </p>
                        </div>
                        <button
                            onclick="sendOneArrivalSms(${row.id})"
                            id="arrival-send-btn-${row.id}"
                            style="flex-shrink:0; padding:6px 16px; border-radius:8px; border:none;
                                   background:#185FA5; color:white; font-size:0.78rem;
                                   font-weight:600; cursor:pointer; white-space:nowrap;">
                            Send
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom:0.5rem;">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" id="arrival-phone-${row.id}"
                            value="${escHtml(row.Phone)}"
                            maxlength="15" class="form-input"
                            style="font-size:0.82rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Message</label>
                        <textarea id="arrival-message-${row.id}" rows="3"
                            class="form-input"
                            style="resize:none; font-size:0.78rem; color:var(--text-muted);">${escHtml(row.Message)}</textarea>
                    </div>
                    <p id="arrival-row-error-${row.id}"
                        style="font-size:0.75rem; color:#ef4444; margin-top:4px; display:none;"></p>
                `;

                list.appendChild(card);
            });
        }

        // ── Send one ───────────────────────────────────────────────────────
        window.sendOneArrivalSms = function(id) {
            const phone = document.getElementById('arrival-phone-' + id)?.value.trim();
            const message = document.getElementById('arrival-message-' + id)?.value.trim();
            const errEl = document.getElementById('arrival-row-error-' + id);
            const btn = document.getElementById('arrival-send-btn-' + id);

            errEl.style.display = 'none';

            if (!phone || !message) {
                errEl.textContent = 'Phone and message are required.';
                errEl.style.display = 'block';
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Sending...';

            fetch('{{ route('arrival-sms.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        id,
                        phone,
                        message
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const row = _arrivalRows.find(r => r.ID === id);
                        if (row) row._sent = true;

                        const card = document.getElementById('arrival-row-' + id);
                        if (card) {
                            card.style.opacity = '0.4';
                            card.style.pointerEvents = 'none';
                            btn.textContent = '✓ Sent';
                            btn.style.background = '#16a34a';
                        }

                        updateArrivalStatus();
                    } else {
                        btn.disabled = false;
                        btn.textContent = 'Send';
                        errEl.textContent = data.message ?? 'Failed to send. Try again.';
                        errEl.style.display = 'block';
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = 'Send';
                    errEl.textContent = 'Something went wrong. Try again.';
                    errEl.style.display = 'block';
                });
        };

        // ── Send all ───────────────────────────────────────────────────────
        window.sendAllArrivalSms = function() {
            const pending = _arrivalRows.filter(r => !r._sent);
            if (pending.length === 0) return;

            const btn = document.getElementById('arrival-send-all-btn');
            btn.disabled = true;
            btn.textContent = 'Sending...';

            const rows = pending.map(row => ({
                id: row.id,
                phone: document.getElementById('arrival-phone-' + row.id)?.value.trim() ?? row.Phone,
                message: document.getElementById('arrival-message-' + row.id)?.value.trim() ?? row.Message,
            }));

            fetch('{{ route('arrival-sms.send-all') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        rows
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        _arrivalRows.forEach(r => r._sent = true);
                        renderArrivalRows();
                        document.getElementById('arrival-sms-status').textContent = data.message;
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = 'Send All';
                    document.getElementById('arrival-sms-status').textContent = 'Something went wrong. Try again.';
                });
        };

        // ── Status updater ─────────────────────────────────────────────────
        function updateArrivalStatus() {
            const total = _arrivalRows.length;
            const sent = _arrivalRows.filter(r => r._sent).length;
            const pending = total - sent;

            document.getElementById('arrival-sms-status').textContent =
                `${sent} of ${total} sent`;

            if (pending === 0) {
                document.getElementById('arrival-sms-list').style.display = 'none';
                document.getElementById('arrival-sms-empty').style.display = 'block';
                document.getElementById('arrival-send-all-btn').style.display = 'none';
            }
        }

        // ── Close ──────────────────────────────────────────────────────────
        window.closeArrivalSmsModal = function() {
            document.getElementById('arrival-sms-modal').style.display = 'none';
        };

        // ── Escape helper ──────────────────────────────────────────────────
        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    })();
</script>
