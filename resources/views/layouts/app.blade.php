<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Freight Diary</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <style>
        body {
            visibility: hidden;
        }
    </style>
    {{-- NProgress loading bar --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/layout.js'])
</head>

<body>

    {{-- Overlay (mobile only) --}}
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- ── Sidebar ── --}}
    <aside id="sidebar">

        {{-- Logo --}}
        <div class="logo-area"
            style="display: flex; align-items: center; gap: 12px; padding: 0 16px; border-bottom: 1px solid rgba(255,255,255,0.08); height: 60px;">
            <img src="/favicon.svg" alt="Logo" style="width: 32px; height: 32px; flex-shrink: 0;">
            <div class="logo-text">
                <div style="font-size: 0.875rem; font-weight: 600; color: var(--sidebar-text);">Freight Diary</div>
                <div style="font-size: 0.75rem; color: var(--sidebar-muted);">v2.0 Prime Rebuild</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav style="flex: 1; overflow-y: auto; padding: 12px 8px;">

            {{-- Dashboard --}}
            <div class="nav-item-wrapper">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="nav-label">Dashboard</span>
                </a>
                <div class="tooltip">Dashboard</div>
            </div>

            {{-- Setup & Config --}}
            @if (isset($userAuth) && $userAuth->hasPermission('BasicConfig'))
                <div class="nav-section-label">Setup & Config</div>
                <div class="nav-item-wrapper">
                    <button class="nav-link" onclick="toggleSubmenu('setup')">
                        <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="nav-label" style="flex: 1; text-align: left;">Basic Setup</span>
                        <svg class="nav-arrow" id="arrow-setup"
                            style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="tooltip">Basic Setup</div>
                    <div id="submenu-setup" class="submenu closed">
                        @if (isset($userAuth) && $userAuth->hasPermission('BasicConfig'))
                            <a href="{{ route('settings.ledger-control.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.ledger-control.*') ? 'active' : '' }}">
                                Ledger Control
                            </a>
                            <a href="{{ route('settings.ledger-category.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.ledger-category.*') ? 'active' : '' }}">
                                Ledger Category
                            </a>
                            <a href="{{ route('settings.ledger-account.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.ledger-account.*') ? 'active' : '' }}">
                                Ledger Account
                            </a>
                            <a href="{{ route('settings.active-accounts.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.active-accounts.*') ? 'active' : '' }}">
                                Configure Active Accounts
                            </a>
                            <a href="{{ route('settings.handling-charge.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.handling-charge.*') ? 'active' : '' }}">
                                Handling Charges
                            </a>
                            <a href="{{ route('settings.disbursement-account.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.disbursement-account.*') ? 'active' : '' }}">
                                Disbursement Setup
                            </a>
                        @endif
                        @if (isset($userAuth) && $userAuth->hasPermission('UserPrivilege'))
                            <a href="{{ route('settings.user-privilege.index') }}"
                                class="submenu-link {{ request()->routeIs('settings.user-privilege.*') ? 'active' : '' }}">
                                User Privilege
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Master Data --}}
            @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
                <div class="nav-section-label">Master Data</div>
                <div class="nav-item-wrapper">
                    <button class="nav-link" onclick="toggleSubmenu('masterdata')">
                        <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4 8 4m0 0c4.418 0 8-1.79 8-4" />
                        </svg>
                        <span class="nav-label" style="flex: 1; text-align: left;">Master Data</span>
                        <svg class="nav-arrow" id="arrow-masterdata"
                            style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="tooltip">Master Data</div>
                    <div id="submenu-masterdata" class="submenu closed">
                        <a href="{{ route('master-data.consignees.index') }}"
                            class="submenu-link {{ request()->routeIs('master-data.consignees.*') ? 'active' : '' }}">
                            Consignee Management
                        </a>
                        <a href="{{ route('master-data.shippers.index') }}"
                            class="submenu-link {{ request()->routeIs('master-data.shippers.*') ? 'active' : '' }}">
                            Shippers Management
                        </a>
                        <a href="{{ route('master-data.carriers.index') }}"
                            class="submenu-link {{ request()->routeIs('master-data.carriers.*') ? 'active' : '' }}">
                            Carriers Management
                        </a>
                        <a href="{{ route('master-data.ports.index') }}"
                            class="submenu-link {{ request()->routeIs('master-data.ports.*') ? 'active' : '' }}">
                            POL / POD Management
                        </a>
                        <a href="{{ route('master-data.commodities.index') }}"
                            class="submenu-link {{ request()->routeIs('master-data.commodities.*') ? 'active' : '' }}">
                            Commodity Types
                        </a>
                    </div>
                </div>
            @endif

            {{-- Consignment Utilities --}}
            <div class="nav-section-label">Consignment Utilities</div>
            <div class="nav-item-wrapper">
                <button class="nav-link" onclick="toggleSubmenu('consignment')">
                    <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="nav-label" style="flex: 1; text-align: left;">Consignment Register</span>
                    <svg class="nav-arrow" id="arrow-consignment"
                        style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Consignment Register --}}
                <div class="tooltip">Consignment Register</div>
                <div id="submenu-consignment" class="submenu closed">
                    @if (isset($userAuth) && $userAuth->hasPermission('ConsignmentRegister'))
                        <a href="{{ route('consignments.create') }}"
                            class="submenu-link {{ request()->routeIs('consignments.create') ? 'active' : '' }}">
                            New Consignment
                        </a>

                        <a href="{{ route('manifest.index') }}"
                            class="submenu-link {{ request()->routeIs('manifest.*') ? 'active' : '' }}">
                            Cargo Manifest
                        </a>
                        <a href="{{ route('cmdts.index') }}"
                            class="submenu-link {{ request()->routeIs('cmdts.*') ? 'active' : '' }}">
                            Consignment Cmdts
                        </a>
                    @endif

                </div>
            </div>

            {{-- General Transactions --}}
            <div class="nav-section-label">General Transactions</div>

            @php
                $transactionMenus = [
                    [
                        'key' => 'invoice',
                        'label' => 'Generate Invoice',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'key' => 'payment',
                        'label' => 'Payment Transactions',
                        'icon' =>
                            'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                    ],
                    [
                        'key' => 'accounting',
                        'label' => 'Accounting Transaction',
                        'icon' =>
                            'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    ],
                    [
                        'key' => 'disbursement',
                        'label' => 'Disbursement Analysis',
                        'icon' =>
                            'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    ],
                ];
            @endphp

            @foreach ($transactionMenus as $item)
                <div class="nav-item-wrapper">
                    <button class="nav-link" onclick="toggleSubmenu('{{ $item['key'] }}')">
                        <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $item['icon'] }}" />
                        </svg>
                        <span class="nav-label" style="flex: 1; text-align: left;">{{ $item['label'] }}</span>
                        <svg class="nav-arrow" id="arrow-{{ $item['key'] }}"
                            style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="tooltip">{{ $item['label'] }}</div>
                    <div id="submenu-{{ $item['key'] }}" class="submenu closed">

                        {{-- Invoice submenu --}}
                        @if ($item['key'] === 'invoice')
                            @if (isset($userAuth) && $userAuth->hasPermission('GenerateInvoice'))
                                <a href="{{ route('invoice.hbl.index') }}"
                                    class="submenu-link {{ request()->routeIs('invoice.hbl.*') ? 'active' : '' }}">
                                    House BL Invoice
                                </a>
                                <a href="{{ route('invoice.waybill.index') }}"
                                    class="submenu-link {{ request()->routeIs('invoice.waybill.*') ? 'active' : '' }}">
                                    Customer Waybill
                                </a>
                                <a href="{{ route('invoice.other-invoice.index') }}"
                                    class="submenu-link {{ request()->routeIs('invoice.other-invoice.*') ? 'active' : '' }}">
                                    Other Serv. Invoice
                                </a>
                                <a href="{{ route('invoice.non-manifest.index') }}"
                                    class="submenu-link {{ request()->routeIs('invoice.non-manifest.*') ? 'active' : '' }}">
                                    Non-Manifest Invoice
                                </a>
                            @endif


                            {{-- Payment submenu --}}
                        @elseif($item['key'] === 'payment')
                            @if (isset($userAuth) && $userAuth->hasPermission('PaymentTransaction'))
                                <a href="{{ route('payment.declaration.index') }}"
                                    class="submenu-link {{ request()->routeIs('payment.declaration.*') ? 'active' : '' }}">
                                    Process Declaration
                                </a>
                                <a href="{{ route('payment.handl-charge.index') }}"
                                    class="submenu-link {{ request()->routeIs('payment.handl-charge.*') ? 'active' : '' }}">
                                    Receive Handl. Charge
                                </a>
                                <a href="{{ route('payment.serv-charge.index') }}"
                                    class="submenu-link {{ request()->routeIs('payment.serv-charge.*') ? 'active' : '' }}">
                                    Receive Service Charge
                                </a>
                                <a href="{{ route('payment.handling-charge-expense.index') }}"
                                    class="submenu-link {{ request()->routeIs('payment.handling-charge-expense.*') ? 'active' : '' }}">
                                    Handl. Charge Expense
                                </a>
                            @endif

                            {{-- Accounting submenu --}}
                        @elseif($item['key'] === 'accounting')
                            @if (isset($userAuth) && $userAuth->hasPermission('GLTransaction'))
                                <a href="{{ route('accounting.transaction.index') }}"
                                    class="submenu-link {{ request()->routeIs('accounting.transaction.*') ? 'active' : '' }}">
                                    Accounting Transaction
                                </a>
                            @endif

                            {{-- Disbursement submenu --}}
                        @elseif($item['key'] === 'disbursement')
                            <span class="submenu-link" style="font-style: italic; opacity: 0.5;">Coming soon</span>
                        @endif

                    </div>
                </div>
            @endforeach

            {{-- Edit Panel --}}
            <div class="nav-section-label">Edit Panel</div>
            <div class="nav-item-wrapper">
                <button class="nav-link" onclick="toggleSubmenu('edit')">
                    <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="nav-label" style="flex: 1; text-align: left;">Edit Data</span>
                    <svg class="nav-arrow" id="arrow-edit"
                        style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="tooltip">Edit Data</div>
                <div id="submenu-edit" class="submenu closed">
                    <span class="submenu-link" style="font-style: italic;">Coming soon</span>
                </div>
            </div>

            {{-- System Report --}}
            <div class="nav-section-label">System Report</div>
            <div class="nav-item-wrapper">
                <button class="nav-link" onclick="toggleSubmenu('reports')">
                    <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="nav-label" style="flex: 1; text-align: left;">Report Viewer</span>
                    <svg class="nav-arrow" id="arrow-reports"
                        style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="tooltip">Report Viewer</div>
                <div id="submenu-reports" class="submenu closed">
                    <span class="submenu-link" style="font-style: italic;">Coming soon</span>
                </div>
            </div>

        </nav>

        {{-- User info at bottom of sidebar --}}
        <div class="user-info-bar"
            style="display: flex; align-items: center; gap: 12px; padding: 12px; border-top: 1px solid rgba(255,255,255,0.08); flex-shrink: 0;">
            <div class="avatar" style="flex-shrink: 0;">
                {{ strtoupper(substr(Auth::user()->FullName, 0, 1)) }}{{ strtoupper(substr(Auth::user()->FullName, strrpos(Auth::user()->FullName, ' ') + 1, 1)) }}
            </div>
            <div class="user-info" style="min-width: 0;">
                <div
                    style="font-size: 0.875rem; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--sidebar-text);">
                    {{ Auth::user()->FullName }}
                </div>
                <div
                    style="font-size: 0.75rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--sidebar-muted);">
                    {{ Auth::user()->Nature }}
                </div>
            </div>
        </div>

    </aside>

    {{-- ── Main wrapper ── --}}
    <div id="main-wrapper">

        {{-- Topbar --}}
        <header id="topbar">

            {{-- Left side --}}
            <div style="display: flex; align-items: center; gap: 12px;">

                {{-- Sidebar toggle --}}
                <button onclick="toggleSidebar()"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--topbar-btn-border); background: var(--topbar-btn-bg); color: var(--topbar-text); cursor: pointer;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Page title --}}
                <h1 style="font-size: 1rem; font-weight: 600; color: var(--topbar-text);">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            {{-- Right side --}}
            <div style="display: flex; align-items: center; gap: 8px;">

                {{-- Theme toggle --}}
                <button id="theme-toggle" onclick="toggleTheme()" title="Toggle theme"
                    style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.08); color: var(--topbar-text); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <svg id="icon-dark" style="width: 16px; height: 16px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg id="icon-light" style="width: 16px; height: 16px; display: none;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                {{-- Notifications --}}
                <div style="position: relative;">
                    <button
                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--topbar-btn-border); background: var(--topbar-btn-bg); color: var(--topbar-text); cursor: pointer;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                    @if (isset($pendingResetCount) &&
                            $pendingResetCount > 0 &&
                            isset($userAuth) &&
                            $userAuth->hasPermission('UserPrivilege'))
                        <span
                            style="position: absolute; top: -4px; right: -4px; width: 16px; height: 16px; border-radius: 50%; background: #ef4444; color: white; font-size: 0.6rem; font-weight: 700; display: flex; align-items: center; justify-content: center;">
                            {{ $pendingResetCount }}
                        </span>
                    @endif
                </div>

                {{-- User dropdown --}}
                <div style="position: relative;" id="user-dropdown-wrapper">
                    <button onclick="toggleUserMenu()"
                        style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; border: 1px solid var(--topbar-btn-border); background: var(--topbar-btn-bg); cursor: pointer;">
                        <div class="avatar" style="width: 26px; height: 26px; font-size: 0.65rem;">
                            {{ strtoupper(substr(Auth::user()->FullName, 0, 1)) }}{{ strtoupper(substr(Auth::user()->FullName, strrpos(Auth::user()->FullName, ' ') + 1, 1)) }}
                        </div>
                        <span style="font-size: 0.875rem; font-weight: 500; color: var(--topbar-text);">
                            {{ Auth::user()->FullName }}
                        </span>
                        <svg id="user-menu-chevron"
                            style="width: 12px; height: 12px; color: var(--topbar-muted); transition: transform 0.2s;"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- CHANGED: display:none added back correctly --}}
                    <div id="user-menu"
                        style="display: none; position: absolute; right: 0; margin-top: 8px; width: 180px; z-index: 50;">
                        <div class="dropdown-header">
                            <p>{{ Auth::user()->FullName }}</p>
                            <p>{{ Auth::user()->Nature }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Page content --}}
        <main style="flex: 1; padding: 1.5rem;">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
</body>

</html>
