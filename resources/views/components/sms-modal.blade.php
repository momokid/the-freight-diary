{{-- SMS Notification Modal Component --}}
{{-- Usage: <x-sms-modal :route="route('consignments.send-notification')" /> --}}

<div id="sms-modal"
    style="display:none; position:fixed; inset:0; z-index:2000;
       align-items:center; justify-content:center;
       background:rgba(0,0,0,0.5);">
    <div class="card" style="width:100%; max-width:440px; margin:1rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
            <div>
                <p class="form-title">Notify Client</p>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                    Send message to client via SMS
                </p>
            </div>
            <button onclick="closeSmsModal()"
                style="background:none; border:none; cursor:pointer;
                   color:var(--text-muted); font-size:1.2rem;">✕</button>
        </div>

        <div class="form-group">
            <label class="form-label">Consignee</label>
            <p id="sms-consignee-name"
                style="font-size:0.85rem; font-weight:600;
                  color:var(--text-primary); padding:8px 0;">
                —</p>
        </div>

        <div class="form-group">
            <label class="form-label">Message Type <span style="color:#ef4444;">*</span></label>
            <select id="sms-event" class="form-input" onchange="onSmsEventChange()">
                <option value="registration">Consignment Registration</option>
                <option value="eta_change">ETA Updated</option>
                <option value="gate_out">Gate-Out Release</option>
                <option value="invoice_payment">Payment Received</option>
                <option value="manual">Custom Message</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number <span style="color:#ef4444;">*</span></label>
            <input type="tel" id="sms-phone" class="form-input" placeholder="e.g. 0244000000" maxlength="15"
                oninput="updateSmsPreview()">
            <p id="sms-phone-error" class="form-error"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Message Preview</label>
            <textarea id="sms-preview" rows="5" class="form-input"
                style="resize:none; font-size:0.78rem;
                   color:var(--text-muted); background:var(--content-bg);"
                oninput="onSmsPreviewEdit()"></textarea>
            <p id="sms-preview-hint" style="font-size:0.72rem; color:var(--text-muted); margin-top:3px;">
                Auto-generated. Edit only for custom messages.
            </p>
        </div>

        <p id="sms-send-error" class="form-error" style="margin-bottom:8px;"></p>

        <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
            <button onclick="closeSmsModal()" class="btn-secondary" style="flex:1;">Skip</button>
            <button onclick="sendSmsNotification()" id="sms-send-btn" class="btn-primary" style="flex:1;">
                Send SMS
            </button>
        </div>
    </div>
</div>
