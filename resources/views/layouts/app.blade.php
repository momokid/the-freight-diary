<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Freight Diary</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite('resources/css/app.css')
    <style>
        /* ── Dark theme (default) ── */
        [data-theme="dark"] {
            --sidebar-bg:      #1a3d26;
            --topbar-bg:       #1a3d26;         /* CHANGED: was #1f2937 dark gray, now matches sidebar */
            --content-bg:      #f3f4f6;
            --card-bg:         #ffffff;
            --border-color:    rgba(0,0,0,0.08);
            --text-primary:    #111827;
            --text-muted:      rgba(0,0,0,0.4);
            --sidebar-text:    #f0fdf4;
            --sidebar-muted:   rgba(255,255,255,0.45);
            --accent:          #16a34a;
            --accent-hover:    #15803d;
            --nav-hover:       rgba(255,255,255,0.08);
            --nav-active:      rgba(255,255,255,0.15);
            --nav-active-text: #ffffff;
            --tooltip-bg:      #0f2419;
            --tooltip-text:    #f0fdf4;
            --overlay:         rgba(0,0,0,0.6);
            --topbar-text:     #f0fdf4;         /* CHANGED: was #f9fafb, now pure white to match sidebar */
            --topbar-muted:    rgba(255,255,255,0.45);
            --topbar-border:   rgba(255,255,255,0.08); /* CHANGED: added topbar-specific border */
            --topbar-btn-bg:    rgba(255,255,255,0.08);  /* subtle white on green */
            --topbar-btn-border: rgba(255,255,255,0.12);
        }

       /* CHANGED: light theme now has a visually distinct topbar */
[data-theme="light"] {
    --sidebar-bg:      #1f2937;    /* CHANGED: dark gray */
    --topbar-bg:       #1f2937;         /* CHANGED: white topbar in light mode */
    --content-bg:      #f3f4f6;
    --card-bg:         #ffffff;
    --border-color:    rgba(0,0,0,0.08);
    --text-primary:    #111827;
    --text-muted:      rgba(0,0,0,0.4);
    --sidebar-text:    #f0fdf4;
    --sidebar-muted:   rgba(255,255,255,0.45);
    --accent:          #16a34a;
    --accent-hover:    #15803d;
    --nav-hover:       rgba(255,255,255,0.08);
    --nav-active:      rgba(255,255,255,0.15);
    --nav-active-text: #ffffff;
    --tooltip-bg:      #0f2419;
    --tooltip-text:    #f0fdf4;
    --overlay:         rgba(0,0,0,0.4);
    --topbar-text:     #ffffff;         /* CHANGED: dark text for white topbar */
    --topbar-muted:    rgba(0,0,0,0.4); /* CHANGED: dark muted for white topbar */
    --topbar-border:   rgba(0,0,0,0.08); /* CHANGED: dark border for white topbar */
    --topbar-btn-bg:    rgba(0,0,0,0.05);        /* CHANGED: subtle dark on white */
    --topbar-btn-border: rgba(0,0,0,0.1);

}

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--content-bg);
            color: var(--text-primary);
            font-family: ui-sans-serif, system-ui, sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid rgba(255,255,255,0.08); /* CHANGED: white border on green bg */
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: width 0.3s ease, transform 0.3s ease;
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: 64px;
        }

        /* Mobile — hidden off-screen by default */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
                width: 260px !important;
            }
            #sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        /* ── Overlay ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 40;
        }

        @media (max-width: 1023px) {
            #sidebar-overlay.active {
                display: block;
            }
        }

        /* ── Main wrapper ── */
        #main-wrapper {
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #main-wrapper.sidebar-collapsed {
            margin-left: 64px;
        }

        @media (max-width: 1023px) {
            #main-wrapper {
                margin-left: 0 !important;
            }
        }

        /* ── Topbar ── */
        #topbar {
            position: sticky;
            top: 0;
            height: 60px;
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border); /* CHANGED: uses topbar-specific border */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            z-index: 30;
        }

        /* ── Nav links ── */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 0px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--sidebar-text);
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            width: 100%;
            cursor: pointer;
            background: none;
            border: none;
            text-decoration: none;
            text-align: left;
        }

        .nav-link:hover { background: var(--nav-hover); }
        .nav-link.active { background: var(--nav-active); color: var(--nav-active-text); }

        /* ── Nav section labels ── */
        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--sidebar-muted);
            padding: 16px 12px 4px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }

        /* ── Collapsed sidebar — hide labels ── */
        #sidebar.collapsed .nav-label,
        #sidebar.collapsed .nav-section-label,
        #sidebar.collapsed .nav-arrow,
        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .user-info,
        #sidebar.collapsed .submenu {
            display: none !important;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 8px;
        }

        #sidebar.collapsed .logo-area {
            justify-content: center;
            padding: 0;
        }

        /* ── Tooltip ── */
        .nav-item-wrapper {
            position: relative;
        }

        .nav-item-wrapper .tooltip {
            display: none;
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--tooltip-bg);
            color: var(--tooltip-text);
            font-size: 0.75rem;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 100;
            pointer-events: none;
            border: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar.collapsed .nav-item-wrapper:hover .tooltip {
            display: block;
        }

        /* ── Submenu ── */
        .submenu {
            overflow: hidden;
            transition: max-height 0.25s ease;
        }

        .submenu.open  { max-height: 500px; }
        .submenu.closed { max-height: 0; }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 40px;
            border-radius: 0px;
            font-size: 0.8rem;
            color: var(--sidebar-muted); /* CHANGED: was --text-muted, now sidebar-muted for green bg */
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .submenu-link:hover {
            background: var(--nav-hover);
            color: var(--sidebar-text); /* CHANGED: was --text-primary */
        }

        .submenu-link.active {
            color: var(--nav-active-text);
            background: var(--nav-active);
        }

        /* ── Cards ── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
        }

        /* ── Theme toggle button ── */
        #theme-toggle {
            width: 36px; height: 36px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.15); /* CHANGED: white border on green topbar */
            background: rgba(255,255,255,0.08);       /* CHANGED: subtle white tint on green */
            color: var(--topbar-text);                /* CHANGED: was --text-primary */
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background 0.15s;
        }

        #theme-toggle:hover { background: rgba(255,255,255,0.15); }

        /* ── Avatar ── */
        .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #15803d;   /* CHANGED: slightly darker green for contrast on topbar */
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.2); /* CHANGED: added subtle border for definition */
        }

        /* ── User dropdown menu ── */
        /* CHANGED: added dedicated styles for dropdown so it looks clean on white card background */
        #user-menu {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        #user-menu .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }

        #user-menu .dropdown-header p:first-child {
            font-size: 0.8rem;
            font-weight: 600;
            color: #111827;
        }

        #user-menu .dropdown-header p:last-child {
            font-size: 0.72rem;
            color: rgba(0,0,0,0.4);
            margin-top: 1px;
        }

        #user-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            font-size: 0.82rem;
            font-weight: 500;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
            color: #374151;
        }

        #user-menu .dropdown-item:hover {
            background: #f3f4f6;
        }

        #user-menu .dropdown-item.danger {
            color: #ef4444;
        }

        #user-menu .dropdown-item.danger:hover {
            background: #fef2f2;
        }
    </style>
</head>
<body>

{{-- Overlay (mobile only) --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar">

    {{-- Logo --}}
    {{-- CHANGED: logo text now uses sidebar-text (white) instead of text-primary (dark) --}}
    <div class="logo-area flex items-center gap-3 px-4 py-3"
        style="border-bottom: 1px solid rgba(255,255,255,0.08); height: 60px;">
        <img src="/favicon.svg" alt="Logo" class="w-8 h-8 flex-shrink-0">
        <div class="logo-text">
            <div class="text-sm font-semibold" style="color: var(--sidebar-text);">Freight Diary</div>
            <div class="text-xs" style="color: var(--sidebar-muted);">v2.0 Prime Rebuild</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

        {{-- Dashboard --}}
        <div class="nav-item-wrapper">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-label">Dashboard</span>
            </a>
            <div class="tooltip">Dashboard</div>
        </div>

        {{-- Setup & Config --}}
{{-- Section label only shows if user has at least one setup permission --}}
@if(isset($userAuth) && $userAuth->hasPermission('BasicConfig'))
<div class="nav-section-label">Setup & Config</div>

<div class="nav-item-wrapper">
    <button class="nav-link" onclick="toggleSubmenu('setup')">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="nav-label flex-1 text-left">Basic Setup</span>
        <svg class="nav-arrow w-3 h-3 transition-transform duration-200" id="arrow-setup"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div class="tooltip">Basic Setup</div>
    <div id="submenu-setup" class="submenu closed">
        @if(isset($userAuth) && $userAuth->hasPermission('UserPrivilege'))
        <a href="{{ route('settings.user-privilege.index') }}" class="submenu-link
            {{ request()->routeIs('settings.user-privilege.*') ? 'active' : '' }}">
            User Privilege
        </a>
        @endif
    </div>
</div>
@endif

        {{-- Consignment Utilities --}}
        <div class="nav-section-label">Consignment Utilities</div>

        <div class="nav-item-wrapper">
            <button class="nav-link" onclick="toggleSubmenu('consignment')">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="nav-label flex-1 text-left">Consignment Register</span>
                <svg class="nav-arrow w-3 h-3 transition-transform duration-200" id="arrow-consignment"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="tooltip">Consignment Register</div>
            <div id="submenu-consignment" class="submenu closed">
                <span class="submenu-link italic">Coming soon</span>
            </div>
        </div>

        {{-- General Transactions --}}
        <div class="nav-section-label">General Transactions</div>

        @php
        $transactionMenus = [
            ['key' => 'invoice',      'label' => 'Generate Invoice',       'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['key' => 'payment',      'label' => 'Payment Transactions',   'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['key' => 'accounting',   'label' => 'Accounting Transaction', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            ['key' => 'disbursement', 'label' => 'Disbursement Analysis',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ];
        @endphp

        @foreach($transactionMenus as $item)
        <div class="nav-item-wrapper">
            <button class="nav-link" onclick="toggleSubmenu('{{ $item['key'] }}')">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="nav-label flex-1 text-left">{{ $item['label'] }}</span>
                <svg class="nav-arrow w-3 h-3 transition-transform duration-200" id="arrow-{{ $item['key'] }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="tooltip">{{ $item['label'] }}</div>
            <div id="submenu-{{ $item['key'] }}" class="submenu closed">
                <span class="submenu-link italic">Coming soon</span>
            </div>
        </div>
        @endforeach

        {{-- Edit Panel --}}
        <div class="nav-section-label">Edit Panel</div>

        <div class="nav-item-wrapper">
            <button class="nav-link" onclick="toggleSubmenu('edit')">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="nav-label flex-1 text-left">Edit Data</span>
                <svg class="nav-arrow w-3 h-3 transition-transform duration-200" id="arrow-edit"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="tooltip">Edit Data</div>
            <div id="submenu-edit" class="submenu closed">
                <span class="submenu-link italic">Coming soon</span>
            </div>
        </div>

        {{-- System Report --}}
        <div class="nav-section-label">System Report</div>

        <div class="nav-item-wrapper">
            <button class="nav-link" onclick="toggleSubmenu('reports')">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="nav-label flex-1 text-left">Report Viewer</span>
                <svg class="nav-arrow w-3 h-3 transition-transform duration-200" id="arrow-reports"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="tooltip">Report Viewer</div>
            <div id="submenu-reports" class="submenu closed">
                <span class="submenu-link italic">Coming soon</span>
            </div>
        </div>

    </nav>

    {{-- User info at bottom of sidebar --}}
    <div class="flex items-center gap-3 px-3 py-3 flex-shrink-0"
        style="border-top: 1px solid rgba(255,255,255,0.08);">
        <div class="avatar flex-shrink-0">
            {{ strtoupper(substr(Auth::user()->FullName, 0, 1)) }}{{ strtoupper(substr(Auth::user()->FullName, strrpos(Auth::user()->FullName, ' ') + 1, 1)) }}
        </div>
        <div class="user-info min-w-0">
            <div class="text-sm font-medium truncate" style="color: var(--sidebar-text);">
                {{ Auth::user()->FullName }}
            </div>
            <div class="text-xs truncate" style="color: var(--sidebar-muted);">
                {{ Auth::user()->Nature }}
            </div>
        </div>
    </div>

</aside>

{{-- Main wrapper --}}
<div id="main-wrapper">

    {{-- Topbar --}}
    <header id="topbar">
        <div class="flex items-center gap-3">

            {{-- Sidebar toggle --}}
  <button onclick="toggleSidebar()"
    class="w-9 h-9 flex items-center justify-center rounded-lg transition"
    style="color: var(--topbar-text); background: var(--topbar-btn-bg); border: 1px solid var(--topbar-btn-border);">


                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <h1 class="text-base font-semibold" style="color: var(--topbar-text);">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

        <div class="flex items-center gap-2">

            {{-- Theme toggle --}}
            <button id="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                <svg id="icon-dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg id="icon-light" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>

            {{-- Notifications --}}
            {{-- CHANGED: notification bell now shows pending reset count badge --}}
            <div class="relative">
                <button class="w-9 h-9 flex items-center justify-center rounded-lg"
                    style="color: var(--topbar-text); background: var(--topbar-btn-bg); border: 1px solid var(--topbar-btn-border);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
                {{-- Badge — only show if there are pending requests --}}
                @if(is_callable($pendingResetCount) ? $pendingResetCount() : $pendingResetCount > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-white"
                        style="background: #ef4444; font-size: 0.6rem; font-weight: 700;">
                        {{ is_callable($pendingResetCount) ? $pendingResetCount() : $pendingResetCount }}
                    </span>
                @endif
            </div>

            {{-- User dropdown --}}
            {{-- CHANGED: wrapped in dedicated div with id for cleaner dropdown targeting --}}
            <div class="relative" id="user-dropdown-wrapper">
                <button onclick="toggleUserMenu()"
    class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition"
    style="background: var(--topbar-btn-bg); border: 1px solid var(--topbar-btn-border);">
                    <div class="avatar" style="width:26px; height:26px; font-size:0.65rem;">
                        {{ strtoupper(substr(Auth::user()->FullName, 0, 1)) }}{{ strtoupper(substr(Auth::user()->FullName, strrpos(Auth::user()->FullName, ' ') + 1, 1)) }}
                    </div>
                    <span class="text-sm font-medium hidden sm:block" style="color: var(--topbar-text);">
                        {{ Auth::user()->FullName }}
                    </span>
                    {{-- CHANGED: added id to chevron so we can rotate it when menu opens --}}
                    <svg id="user-menu-chevron" class="w-3 h-3 hidden sm:block transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--topbar-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- CHANGED: dropdown rebuilt with dedicated CSS classes instead of inline styles --}}
                <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 z-50" style="min-width: 180px;">
                    <div class="dropdown-header">
                        <p>{{ Auth::user()->FullName }}</p>
                        <p>{{ Auth::user()->Nature }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-6 sm:p-8" style="padding: 1rem;">
        @yield('content')
    </main>

</div>

<script>
    const sidebar     = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('main-wrapper');
    const overlay     = document.getElementById('sidebar-overlay');
    const html        = document.documentElement;

    // ── Theme ──
    const savedTheme = localStorage.getItem('fd-theme') || 'dark';
    applyTheme(savedTheme);

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('fd-theme', theme);
        document.getElementById('icon-dark').classList.toggle('hidden', theme === 'light');
        document.getElementById('icon-light').classList.toggle('hidden', theme === 'dark');
    }

    function toggleTheme() {
        const current = html.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // ── Sidebar toggle ──
    const isDesktop = () => window.innerWidth >= 1024;
    const savedCollapsed = localStorage.getItem('fd-sidebar') === 'collapsed';
    if (isDesktop() && savedCollapsed) {
        sidebar.classList.add('collapsed');
        mainWrapper.classList.add('sidebar-collapsed');
    }

    function toggleSidebar() {
        if (isDesktop()) {
            const collapsed = sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed', collapsed);
            localStorage.setItem('fd-sidebar', collapsed ? 'collapsed' : 'expanded');
        } else {
            const open = sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active', open);
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }

    // ── Submenus ──
    function toggleSubmenu(key) {
        const submenu = document.getElementById('submenu-' + key);
        const arrow   = document.getElementById('arrow-' + key);
        if (!submenu) return;
        const isOpen = submenu.classList.contains('open');
        submenu.classList.toggle('open', !isOpen);
        submenu.classList.toggle('closed', isOpen);
        if (arrow) arrow.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    // ── User dropdown ──
    // CHANGED: use a flag to prevent the click-outside listener from
    // immediately closing the menu the same moment it opens
    let userMenuJustOpened = false;

    function toggleUserMenu() {
        const menu    = document.getElementById('user-menu');
        const chevron = document.getElementById('user-menu-chevron');
        const isHidden = menu.classList.contains('hidden');

        menu.classList.toggle('hidden');
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';

        if (isHidden) {
            userMenuJustOpened = true; // CHANGED: flag prevents immediate close
        }
    }

    // CHANGED: click-outside now respects the just-opened flag
    document.addEventListener('click', function(e) {
        if (userMenuJustOpened) {
            userMenuJustOpened = false;
            return;
        }
        const menu    = document.getElementById('user-menu');
        const wrapper = document.getElementById('user-dropdown-wrapper');
        if (!wrapper.contains(e.target)) {
            menu.classList.add('hidden');
            const chevron = document.getElementById('user-menu-chevron');
            if (chevron) chevron.style.transform = '';
        }
    });

    // ── Close sidebar on resize to desktop ──
    window.addEventListener('resize', () => {
        if (isDesktop()) closeSidebar();
    });
</script>

@stack('scripts')
</body>
</html>