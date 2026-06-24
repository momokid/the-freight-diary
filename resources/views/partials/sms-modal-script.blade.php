<script>
    // ── SMS Modal global state ─────────────────────────────────────────────
    let _smsBL = '';
    let _smsCode = '';
    let _smsConsigneeId = 0;
    let _smsRoute = '';
    let _smsManualEdit = false;

    // ── Open modal ────────────────────────────────────────────────────────
    function openSmsModal(bl, clientCode, consigneeId, route, event = 'registration') {
        _smsBL = bl;
        _smsCode = clientCode;
        _smsConsigneeId = consigneeId;
        _smsRoute = route;
        _smsManualEdit = false;

        const modal = document.getElementById('sms-modal');
        if (!modal) return;

        modal.style.display = 'flex';

        document.getElementById('sms-event').value = event;
        document.getElementById('sms-phone').value = '';
        document.getElementById('sms-consignee-name').textContent = 'Loading...';
        document.getElementById('sms-preview').value = '';
        document.getElementById('sms-preview').readOnly = true;
        document.getElementById('sms-phone-error').classList.remove('visible');
        document.getElementById('sms-send-error').classList.remove('visible');

        if (consigneeId && consigneeId > 0) {
            fetch(`/master-data/consignees/${consigneeId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('sms-consignee-name').textContent = data.consignee.FullName ?? '—';
                        document.getElementById('sms-phone').value = data.consignee.TelNo ?? '';
                        updateSmsPreview();
                    }
                })
                .catch(() => {
                    document.getElementById('sms-consignee-name').textContent = '—';
                });
        } else {
            document.getElementById('sms-consignee-name').textContent = 'No consignee linked';
            updateSmsPreview();
        }
    }

    // ── Build preview from event type ─────────────────────────────────────
    function updateSmsPreview() {
        if (_smsManualEdit) return;

        const event = document.getElementById('sms-event').value;
        const preview = document.getElementById('sms-preview');

        const messages = {
            registration: `Dear Client, your consignment BL# ${_smsBL} has been registered with PSIL. Your access code is ${_smsCode}.`,
            gate_out: `Dear Client, your consignment BL# ${_smsBL} has been released for gate-out by PSIL. Please arrange collection at your earliest convenience.`,
            invoice_payment: `Dear Client, your payment for consignment BL# ${_smsBL} has been recorded by PSIL. Thank you for your payment.`,
            manual: '',
        };

        preview.value = messages[event] ?? '';
        preview.readOnly = event !== 'manual';

        document.getElementById('sms-preview-hint').textContent = event === 'manual' ?
            'Enter your custom message above.' :
            'Auto-generated. Edit only for custom messages.';
    }

    // ── Event type change ─────────────────────────────────────────────────
    function onSmsEventChange() {
        _smsManualEdit = false;
        updateSmsPreview();
    }

    // ── Manual edit detection ─────────────────────────────────────────────
    function onSmsPreviewEdit() {
        _smsManualEdit = document.getElementById('sms-event').value === 'manual';
    }

    // ── Close modal ───────────────────────────────────────────────────────
    function closeSmsModal() {
        const modal = document.getElementById('sms-modal');
        if (modal) modal.style.display = 'none';
    }

    // ── Send ──────────────────────────────────────────────────────────────
    function sendSmsNotification() {
        const phone = document.getElementById('sms-phone').value.trim();
        const event = document.getElementById('sms-event').value;
        const message = document.getElementById('sms-preview').value.trim();
        const phoneErr = document.getElementById('sms-phone-error');
        const sendErr = document.getElementById('sms-send-error');
        const btn = document.getElementById('sms-send-btn');

        phoneErr.classList.remove('visible');
        sendErr.classList.remove('visible');

        if (!phone) {
            phoneErr.textContent = 'Phone number is required.';
            phoneErr.classList.add('visible');
            return;
        }

        if (!message) {
            sendErr.textContent = 'Message cannot be empty.';
            sendErr.classList.add('visible');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Sending...';

        fetch(_smsRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    bl: _smsBL,
                    client_code: _smsCode,
                    consignee_id: _smsConsigneeId,
                    event: event,
                    phone: phone,
                    message: message,
                }),
            })
            .then(r => r.json()).then(data => {
                if (data.success) {
                    closeSmsModal();
                    if (typeof window.onSmsSent === 'function') {
                        window.onSmsSent();
                    }
                } else {
                    sendErr.textContent = data.message ?? 'Failed to send. Try again.';
                    sendErr.classList.add('visible');
                }
            })
            .catch(() => {
                sendErr.textContent = 'Something went wrong. Please try again.';
                sendErr.classList.add('visible');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Send SMS';
            });
    }
</script>
