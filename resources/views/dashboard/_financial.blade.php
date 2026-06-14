@php
    // Bar chart scale
    $barData = [
        'labels' => collect($trend)->map(fn($t) => Str::before($t->month_label, ' '))->toArray(),
        'revenue' => collect($trend)->map(fn($t) => round((float) $t->revenue, 2))->toArray(),
        'expenditure' => collect($trend)->map(fn($t) => round((float) $t->expenditure, 2))->toArray(),
    ];

    // Donut chart — assets only, converted to positive
    $donutColors = ['#185FA5', '#3B6D11', '#EF9F27', '#A32D2D', '#6B4EAE', '#0C7070'];
    $accountsWithAssets = collect($accounts)->filter(fn($a) => (float) $a->balance != 0);
    $donutLabels = $accountsWithAssets->pluck('AccountName')->values()->toArray();
    $donutData = $accountsWithAssets->map(fn($a) => abs((float) $a->balance))->values()->toArray();
    $donutFills = array_slice($donutColors, 0, count($donutLabels));
    $donutHasData = array_sum($donutData) > 0;
@endphp

<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
            Financial Performance
            <span
                style="font-size:11px; font-weight:400; color:var(--text-muted);
                         margin-left:6px;">{{ $monthLabel }}
                MTD</span>
        </p>
        <button onclick="window.DashboardApp.loadWidget('financial')"
            style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:11px;
                       color:var(--text-muted); cursor:pointer;">
            ↻ Refresh
        </button>
    </div>

    <div style="padding:1rem 1.25rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            {{-- ── Left: MTD stat cards + bar chart ── --}}
            <div>

                {{-- MTD stat cards --}}
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px;">

                    <div style="background:#EAF3DE; border-radius:8px; padding:10px 12px;">
                        <div style="font-size:14px; font-weight:600; color:#3B6D11; margin-bottom:2px;">
                            GHS {{ number_format($revenue, 2) }}
                        </div>
                        <div
                            style="font-size:11px; color:#3B6D11; text-transform:uppercase;
                                    letter-spacing:0.4px;">
                            Revenue
                        </div>
                    </div>

                    <div style="background:#FCEBEB; border-radius:8px; padding:10px 12px;">
                        <div style="font-size:14px; font-weight:600; color:#A32D2D; margin-bottom:2px;">
                            GHS {{ number_format($expenditure, 2) }}
                        </div>
                        <div
                            style="font-size:11px; color:#A32D2D; text-transform:uppercase;
                                    letter-spacing:0.4px;">
                            Expenditure
                        </div>
                    </div>

                    <div style="background:#E6F1FB; border-radius:8px; padding:10px 12px;">
                        @if ($net >= 0)
                            <div style="font-size:14px; font-weight:600; color:#0C447C; margin-bottom:2px;">
                                GHS {{ number_format($net, 2) }}
                            </div>
                            <div
                                style="font-size:11px; color:#0C447C; text-transform:uppercase;
                                        letter-spacing:0.4px;">
                                Net Surplus
                            </div>
                        @else
                            <div style="font-size:14px; font-weight:600; color:#A32D2D; margin-bottom:2px;">
                                GHS {{ number_format(abs($net), 2) }}
                            </div>
                            <div
                                style="font-size:11px; color:#A32D2D; text-transform:uppercase;
                                        letter-spacing:0.4px;">
                                Net Deficit
                            </div>
                        @endif
                    </div>

                </div>

                {{-- 3-month bar chart — taller bars --}}
                @if (count($trend) > 0)
                    <div style="position:relative; height:180px; width:100%; margin-top:8px;">
                        <canvas data-bar="{{ json_encode($barData) }}"></canvas>
                    </div>
                @else
                    <p style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                        No trend data available.
                    </p>
                @endif

            </div>{{-- end left --}}

            {{-- ── Right: Cash position donut chart ── --}}
            <div style="border-left:0.5px solid var(--border-color); padding-left:16px;">

                <p
                    style="font-size:12px; font-weight:600; color:var(--text-muted);
                          text-transform:uppercase; letter-spacing:0.6px; margin:0 0 10px;">
                    Cash Position
                </p>

                @if (count($accounts) > 0 && $donutHasData)

                    <div style="position:relative; height:200px; width:100%;">
                        <canvas
                            data-donut="{{ json_encode([
                                'labels' => $donutLabels,
                                'data' => $donutData,
                                'colors' => $donutFills,
                            ]) }}">
                        </canvas>
                    </div>
                @elseif (count($accounts) > 0)
                    {{-- All balances zero or negative — fall back to cards --}}
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach ($accounts as $acct)
                            <div
                                style="border:0.5px solid var(--border-color); border-radius:8px;
                                       padding:10px 12px;">
                                <div style="font-size:12px; color:var(--text-muted); margin-bottom:3px;">
                                    {{ $acct->AccountName }}
                                </div>
                                <div style="font-size:15px; font-weight:600; color:#185FA5;">
                                    GHS {{ number_format($acct->balance, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:12px; color:var(--text-muted);">
                        No active cash accounts configured.
                    </p>
                @endif

            </div>{{-- end right --}}

        </div>
    </div>

</div>
