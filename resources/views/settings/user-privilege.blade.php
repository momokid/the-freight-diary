@extends('layouts.app')

@section('title', 'User Privilege')
@section('page-title', 'User Privilege')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 120px);">

    {{-- ── Left Panel: Users List ── --}}
    <div class="flex-shrink-0" style="width: 280px;">
        <div class="card h-full flex flex-col">

            <h2 class="text-sm font-semibold mb-3" style="color: var(--text-primary);">
                System Users
            </h2>

            {{-- Search --}}
            <div class="relative mb-3">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color: var(--text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    id="user-search"
                    placeholder="Search users from list"
                    oninput="filterUsers()"
                    class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm outline-none transition-all"
                    style="padding:0.5rem 0.75rem 0.5rem 2.25rem; border: 1px solid #16a34a;"
                    onfocus="this.style.borderColor='#16a34a'; this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.12)'"
                    onblur="this.style.borderColor=''; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
            </div>

            {{-- Users list — scrolls internally --}}
            <div id="users-list" class="flex-1 overflow-y-auto space-y-1" style="min-height: 0;">
                @foreach($users as $user)
                <button
                    onclick="loadUserPermissions('{{ $user->ID }}', '{{ addslashes($user->FullName) }}')"
                    data-name="{{ strtolower($user->FullName) }}"
                    data-userid="{{ $user->ID }}"
                    class="user-item w-full text-left px-3 py-2.5 rounded-lg transition-all"
                    style="border: 1px solid transparent;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                            style="background: #16a34a; color: white;">
                            {{ strtoupper(substr($user->FullName, 0, 1)) }}{{ strtoupper(substr($user->FullName, strrpos($user->FullName, ' ') + 1, 1)) }}
                        </div>
                       
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium truncate" style="color: var(--text-primary);">
                                    {{ $user->FullName }}
                                </div>
                                {{-- badge for pending reset request --}}
                                @if($user->reset_requested)
                                    <span class="flex-shrink-0 text-xs px-1.5 py-0.5 rounded-full font-medium"
                                        style="background: rgba(239,68,68,0.1); color: #ef4444; font-size: 0.6rem;">
                                        RESET
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs" style="color: var(--text-muted);">
                                {{ $user->ID }} {{ $user->Nature }}
                            </div>
                        </div>

                    </div>
                </button>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ── Right Panel: Permissions ── --}}
    <div class="flex-1" style="min-width: 0;border: 0px solid red; padding: 0px;">
        <div class="card h-full flex flex-col">

            {{-- Default state — no user selected --}}
            <div id="panel-empty" class="flex-1 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                    style="background: var(--content-bg);">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">No user selected</p>
                <p class="text-xs" style="color: var(--text-muted);">
                    Select a user from the left to manage their permissions
                </p>
            </div>

            {{-- Initialise prompt --}}
            <div id="panel-initialise" class="hidden flex-1 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                    style="background: rgba(245,158,11,0.1);">
                    <svg class="w-8 h-8" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold mb-1" style="color: var(--text-primary);">
                    No permissions found for <span id="initialise-username" class="text-green-600"></span>
                </p>
                <p class="text-xs mb-5" style="color: var(--text-muted);">
                    All permissions will be set to OFF by default
                </p>
                <button onclick="initialiseUser()"
                    class="px-5 py-5 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
                    style="background: #16a34a; padding: 0.625rem 1.25rem;">
                    Initialise Access Permissions
                </button>
            </div>

            {{-- Permissions panel --}}
            <div id="panel-permissions" class="hidden flex-1 flex flex-col" style="min-height: 0;">

                {{-- Header --}}
                {{-- CHANGED: header now includes reset password button --}}
                <div class="flex items-center justify-between pb-4 flex-shrink-0"
                    style="border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h2 class="text-sm font-semibold" style="color: var(--text-primary);">
                            Permissions for
                            <span id="permissions-username" class="text-green-600"></span>
                        </h2>
                        <p class="text-xs mt-0.5" style="color: var(--text-muted);">
                            Changes are saved instantly
                        </p>
                    </div>

                    {{-- ADDED: reset password button — only visible when user has pending request --}}
                    <button
                        id="reset-password-btn"
                        onclick="resetPassword()"
                        class="hidden items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-white transition hover:opacity-90"
                        style="background: #ef4444; padding: 8px 12px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset Password
                    </button>
                </div>

                
                {{-- Scrollable permissions list --}}
                <div class="flex-1 overflow-y-auto pt-4 space-y-6" style="min-height: 0;padding: 10px;">
                    @foreach($permissionGroups as $groupName => $permissions)
                    <div style="padding: 5px;">

                        {{-- Group label --}}
                        <p class="text-xs font-semibold uppercase tracking-widest mb-3"
                            style="font-size: 16px;color: white; letter-spacing: 0.1em; background-color:var(--text-muted)">
                            {{ $groupName }}
                        </p>

                        {{-- Permission rows --}}
                        <div class="rounded-xl overflow-hidden"
                            style="border: 1px solid var(--border-color);">
                            @foreach($permissions as $key => $label)
                            <div class="flex items-center justify-between px-4 py-3
                                {{ !$loop->last ? 'border-b' : '' }}"
                                style="padding: 5px; {{ !$loop->last ? 'border-color: var(--border-color);' : '' }}
                                       background: var(--card-bg);">

                                <span class="text-sm" style="color: var(--text-primary);">
                                    {{ $label }}
                                </span>

                                {{-- Toggle switch --}}
                                <button
                                    type="button"
                                    id="toggle-{{ $key }}"
                                    onclick="togglePermission('{{ $key }}')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    style="background: #d1d5db;"
                                    data-permission="{{ $key }}"
                                    data-enabled="false"
                                    role="switch"
                                    aria-checked="false">
                                    <span
                                        class="toggle-knob pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg transition duration-200 ease-in-out"
                                        style="transform: translateX(0px);">
                                    </span>
                                </button>

                            </div>
                            @endforeach
                        </div>

                    </div>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

</div>

{{-- ADDED: temporary password modal --}}
<div id="reset-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center"
    style="background: rgba(0,0,0,0.5);">
    <div class="rounded-2xl p-6 w-full max-w-sm mx-4"
        style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 1.5rem;">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                style="background: rgba(16,185,129,0.1);">
                <svg class="w-5 h-5" fill="none" stroke="#10b981" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--text-primary);">
                    Password Reset Successful
                </h3>
                <p class="text-xs" style="color: var(--text-muted);">
                    Share this temporary password with the user
                </p>
            </div>
        </div>

        {{-- Temporary password display --}}
        <div class="rounded-xl p-4 mb-4 text-center"
            style="background: var(--content-bg); border: 1px solid var(--border-color);">
            <p class="text-xs mb-1" style="color: var(--text-muted);">Temporary Password</p>
            <p id="temp-password-display"
                class="text-2xl font-bold tracking-widest"
                style="color: var(--text-primary); letter-spacing: 0.2em;">
            </p>
        </div>

        <p class="text-xs text-center mb-4" style="color: var(--text-muted);">
            This password will not be shown again. The user must change it on next login.
        </p>

        {{-- Copy button --}}
        <button onclick="copyTempPassword()"
            id="copy-btn"
            class="w-full py-2.5 rounded-lg text-sm font-medium mb-2 transition"
            style="background: #16a34a; color: white; padding: 0.625rem 1.25rem; margin-bottom: 1rem;">
            Copy Password
        </button>

        <button onclick="closeResetModal()"
            class="w-full py-2.5 rounded-lg text-sm font-medium transition"
            style="background: var(--content-bg); color: var(--text-muted); border: 1px solid var(--border-color);padding: 0.625rem 1.25rem;">
            Close
        </button>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let selectedUser = null;

    function filterUsers() {
        const query = document.getElementById('user-search').value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    }

    function setActiveUser(userId) {
        document.querySelectorAll('.user-item').forEach(item => {
            const isActive = item.getAttribute('data-userid') === userId;
            item.style.background  = isActive ? 'rgba(22,163,74,0.08)' : '';
            item.style.borderColor = isActive ? 'rgba(22,163,74,0.25)' : 'transparent';
        });
    }

    function loadUserPermissions(userId, fullName) {
        selectedUser = userId;
        setActiveUser(userId);
        showPanel('empty');

        fetch(`/settings/user-privilege/${userId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
    if (data.initialised === false) {
        document.getElementById('initialise-username').textContent = fullName;
        showPanel('initialise');
    } else {
        document.getElementById('permissions-username').textContent = fullName;
        populateToggles(data.permissions);
        showPanel('permissions');

        // ADDED: show/hide reset button based on reset_requested flag
        const resetBtn = document.getElementById('reset-password-btn');
        if (data.reset_requested) {
            resetBtn.classList.remove('hidden');
            resetBtn.classList.add('flex');
        } else {
            resetBtn.classList.add('hidden');
            resetBtn.classList.remove('flex');
        }
    }
})
        .catch(() => {
            alert('Failed to load permissions. Please try again.');
        });
    }

    function populateToggles(permissions) {
        Object.entries(permissions).forEach(([key, value]) => {
            const toggle = document.getElementById('toggle-' + key);
            if (toggle) setToggleState(toggle, value);
        });
    }

    function setToggleState(toggle, enabled) {
        const knob = toggle.querySelector('.toggle-knob');
        toggle.setAttribute('data-enabled', enabled);
        toggle.setAttribute('aria-checked', enabled);
        toggle.style.background = enabled ? '#16a34a' : '#d1d5db';
        knob.style.transform    = enabled ? 'translateX(20px)' : 'translateX(0px)';
    }

    function togglePermission(permission) {
        if (!selectedUser) return;

        const toggle   = document.getElementById('toggle-' + permission);
        const current  = toggle.getAttribute('data-enabled') === 'true';
        const newValue = !current;

        setToggleState(toggle, newValue);

        fetch('{{ route("settings.user-privilege.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                username:   selectedUser,
                permission: permission,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                setToggleState(toggle, current);
            }
        })
        .catch(() => {
            setToggleState(toggle, current);
        });
    }

    function initialiseUser() {
        if (!selectedUser) return;

        fetch('{{ route("settings.user-privilege.initialise") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ username: selectedUser }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const nameEl = document.getElementById('initialise-username');
                loadUserPermissions(selectedUser, nameEl.textContent);
            } else {
                alert(data.message);
            }
        })
        .catch(() => {
            alert('Failed to initialise permissions. Please try again.');
        });
    }

    function showPanel(panel) {
        ['empty', 'initialise', 'permissions'].forEach(p => {
            const el = document.getElementById('panel-' + p);
            el.classList.add('hidden');
            el.classList.remove('flex-1', 'flex');
        });

        const active = document.getElementById('panel-' + panel);
        active.classList.remove('hidden');
        active.classList.add('flex-1', 'flex');
    }

    // ADDED: reset password via AJAX
function resetPassword() {
    if (!selectedUser) return;

    if (!confirm('Reset password for this user? A temporary password will be generated.')) return;

    fetch('{{ route("settings.user-privilege.reset-password") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ username: selectedUser }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show modal with temporary password
            document.getElementById('temp-password-display').textContent = data.temp_password;
            document.getElementById('reset-modal').classList.remove('hidden');

            // Hide the reset button since request is now cleared
            document.getElementById('reset-password-btn').classList.add('hidden');
            document.getElementById('reset-password-btn').classList.remove('flex');

            // ADDED: update the user list badge — remove RESET badge for this user
            const userItem = document.querySelector(`[data-userid="${selectedUser}"]`);
            if (userItem) {
                const badge = userItem.querySelector('span');
                if (badge) badge.remove();
            }
        } else {
            alert('Failed to reset password. Please try again.');
        }
    })
    .catch(() => {
        alert('Failed to reset password. Please try again.');
    });
}

// ADDED: copy temporary password to clipboard
function copyTempPassword() {
    const password = document.getElementById('temp-password-display').textContent;
    navigator.clipboard.writeText(password).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.textContent = 'Copied!';
        setTimeout(() => { btn.textContent = 'Copy Password'; }, 2000);
    });
}

// ADDED: close the reset modal
function closeResetModal() {
    document.getElementById('reset-modal').classList.add('hidden');
    document.getElementById('temp-password-display').textContent = '';
}
</script>
@endpush