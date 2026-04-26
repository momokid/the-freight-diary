@extends('layouts.app')

@section('title', 'System Reports')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">System Reports</h1>
            <p class="page-subtitle">Select a report group to get started</p>
        </div>
    </div>

    @if (empty($visibleGroups))
        <div class="empty-state">
            <p>You do not have permission to view any reports. Contact your administrator.</p>
        </div>
    @else
        <div class="report-groups-grid">
            @foreach ($visibleGroups as $group)
                <div class="report-group-card">
                    <div class="report-group-header">
                        <div class="report-group-icon">
                            @if ($group['icon'] === 'ship')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 17l1.5-6h15L21 17M3 17H2m1 0h18m0 0h1M12 3v8M8 11V7m8 4V7M5 20h14" />
                                </svg>
                            @elseif($group['icon'] === 'users')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="width:22px;height:22px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                </svg>
                            @elseif($group['icon'] === 'cash')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="width:22px;height:22px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            @elseif($group['icon'] === 'book')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="width:22px;height:22px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="width:22px;height:22px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="report-group-title">{{ $group['title'] }}</h3>
                            <p class="report-group-count">{{ count($group['reports']) }} reports</p>
                        </div>
                    </div>
                    <p class="report-group-desc">{{ $group['description'] }}</p>
                    <ul class="report-group-list">
                        @foreach ($group['reports'] as $report)
                            <li>{{ $report }}</li>
                        @endforeach
                    </ul>
                    <div class="report-group-footer">
                        <a href="{{ $group['route'] }}" class="btn-report-open">
                            Open reports
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .report-groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .report-group-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .report-group-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .report-group-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: var(--color-primary-subtle);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .report-group-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }

        .report-group-count {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            margin: 0;
        }

        .report-group-desc {
            font-size: 0.82rem;
            color: var(--color-text-muted);
            line-height: 1.5;
            margin: 0;
        }

        .report-group-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .report-group-list li {
            font-size: 0.80rem;
            color: var(--color-text-secondary);
            padding: 3px 0 3px 14px;
            position: relative;
        }

        .report-group-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--color-primary);
            opacity: 0.4;
        }

        .report-group-footer {
            border-top: 1px solid var(--color-border);
            padding-top: 0.75rem;
            margin-top: auto;
        }

        .btn-report-open {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--color-primary);
            text-decoration: none;
        }

        .btn-report-open:hover {
            text-decoration: underline;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }
    </style>
@endpush
