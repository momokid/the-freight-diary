@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')

    <div style="display: flex; justify-content: flex-end; margin-bottom: 1.25rem;">
        <button onclick="openAddSettingModal()"
            style="padding: 10px 18px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
            + Add New Setting
        </button>
    </div>

    @forelse ($settings as $groupName => $groupSettings)
        <div class="card" style="margin-bottom: 1.25rem;">
            <p class="form-title" style="text-transform: uppercase; letter-spacing: 0.02em;">{{ $groupName ?: 'Ungrouped' }}
            </p>

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                @foreach ($groupSettings as $setting)
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ $setting->label ?: $setting->key }}</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="setting-{{ $setting->key }}" class="form-input"
                                value="{{ $setting->value }}" placeholder="Not set"
                                onchange="saveSetting('{{ $setting->key }}')">
                            <span id="setting-status-{{ $setting->key }}"
                                style="font-size: 0.75rem; color: #16a34a; align-self: center; opacity: 0;">✓ Saved</span>
                        </div>
                        <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 3px;">Key: {{ $setting->key }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; padding: 2rem; color: var(--text-muted);">
            No settings configured yet. Click "+ Add New Setting" to create one.
        </div>
    @endforelse

    {{-- Add Setting Modal --}}
    <div id="modal-add-setting"
        style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
        <div class="card" style="width: 100%; max-width: 420px; margin: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <p class="form-title">Add New Setting</p>
                <button onclick="closeAddSettingModal()"
                    style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem;">✕</button>
            </div>

            <div class="form-group">
                <label class="form-label">Key <span style="color: #ef4444;">*</span></label>
                <input type="text" id="as-key" class="form-input" placeholder="e.g. error_alert_email"
                    style="text-transform: lowercase;">
                <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 3px;">Lowercase letters, numbers, and
                    underscores only</p>
                <p id="as-key-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Label <span style="color: #ef4444;">*</span></label>
                <input type="text" id="as-label" class="form-input" placeholder="e.g. Error Alert Email">
                <p id="as-label-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Group <span style="color: #ef4444;">*</span></label>
                <input type="text" id="as-group" class="form-input" placeholder="e.g. error_log">
                <p id="as-group-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Value <span style="color: var(--text-muted);">optional</span></label>
                <input type="text" id="as-value" class="form-input" placeholder="Leave empty to set later">
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <button onclick="closeAddSettingModal()" class="btn-secondary" style="flex: 1;">Cancel</button>
                <button onclick="saveNewSetting()" id="as-save-btn" class="btn-primary" style="flex: 1;">Save</button>
            </div>
            <p id="as-error" class="form-error" style="margin-top: 8px; text-align: center;"></p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';

        function saveSetting(key) {
            const input = document.getElementById('setting-' + key);
            const status = document.getElementById('setting-status-' + key);

            fetch(`/settings/system-settings/${key}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        value: input.value
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        status.style.opacity = '1';
                        setTimeout(() => status.style.opacity = '0', 2000);
                    }
                });
        }

        function openAddSettingModal() {
            document.getElementById('modal-add-setting').style.display = 'flex';
        }

        function closeAddSettingModal() {
            document.getElementById('modal-add-setting').style.display = 'none';
            ['as-key', 'as-label', 'as-group', 'as-value'].forEach(id => document.getElementById(id).value = '');
            ['as-key-error', 'as-label-error', 'as-group-error', 'as-error'].forEach(id => {
                document.getElementById(id).classList.remove('visible');
            });
        }

        document.getElementById('modal-add-setting').addEventListener('click', function(e) {
            if (e.target === this) closeAddSettingModal();
        });

        function saveNewSetting() {
            const btn = document.getElementById('as-save-btn');
            const key = document.getElementById('as-key').value.trim().toLowerCase();
            const label = document.getElementById('as-label').value.trim();
            const group = document.getElementById('as-group').value.trim();
            const value = document.getElementById('as-value').value.trim();
            const generalError = document.getElementById('as-error');

            let valid = true;
            const checks = [
                [!key || !/^[a-z0-9_]+$/.test(key), 'as-key-error',
                    'Key is required (lowercase, numbers, underscores only).'
                ],
                [!label, 'as-label-error', 'Label is required.'],
                [!group, 'as-group-error', 'Group is required.'],
            ];

            checks.forEach(([condition, errorId, message]) => {
                const el = document.getElementById(errorId);
                el.classList.remove('visible');
                if (condition) {
                    el.textContent = message;
                    el.classList.add('visible');
                    valid = false;
                }
            });

            generalError.classList.remove('visible');
            if (!valid) return;

            btn.textContent = 'Saving...';
            btn.disabled = true;

            fetch('{{ route('settings.system-settings.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        key,
                        label,
                        group,
                        value
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        generalError.textContent = data.message ?? 'Failed to save.';
                        generalError.classList.add('visible');
                    }
                })
                .catch(() => {
                    generalError.textContent = 'Something went wrong.';
                    generalError.classList.add('visible');
                })
                .finally(() => {
                    btn.textContent = 'Save';
                    btn.disabled = false;
                });
        }
    </script>
@endpush
