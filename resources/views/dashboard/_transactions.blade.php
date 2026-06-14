<div class="card" style="padding:0;">

    {{-- ── Widget header ── --}}
    <div
        style="padding:1rem 1.25rem; border-bottom:1px solid var(--border-color);
                display:flex; align-items:center; justify-content:space-between;">
        <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0;">
            Recent Transactions
            <span
                style="font-size:11px; font-weight:400; color:var(--text-muted);
                         margin-left:6px;">Last
                10 entries</span>
        </p>
        <button onclick="window.DashboardApp.loadWidget('transactions')"
            style="background:none; border:0.5px solid var(--border-color);
                       border-radius:6px; padding:3px 10px; font-size:11px;
                       color:var(--text-muted); cursor:pointer;">
            ↻ Refresh
        </button>
    </div>

    @if (count($rows) > 0)

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#185FA5; color:#fff;">
                        <th
                            style="padding:6px 12px; font-size:11px; text-align:left;
                                   font-weight:600; letter-spacing:0.3px; white-space:nowrap;">
                            Date
                        </th>
                        <th
                            style="padding:6px 8px; font-size:11px; text-align:left;
                                   font-weight:600; letter-spacing:0.3px; white-space:nowrap;">
                            Receipt No
                        </th>
                        <th
                            style="padding:6px 8px; font-size:11px; text-align:left;
                                   font-weight:600; letter-spacing:0.3px;">
                            Account
                        </th>
                        <th
                            style="padding:6px 8px; font-size:11px; text-align:left;
                                   font-weight:600; letter-spacing:0.3px;">
                            Description
                        </th>
                        <th
                            style="padding:6px 8px; font-size:11px; text-align:right;
                                   font-weight:600; letter-spacing:0.3px; white-space:nowrap;">
                            Dr (GHS)
                        </th>
                        <th
                            style="padding:6px 12px; font-size:11px; text-align:right;
                                   font-weight:600; letter-spacing:0.3px; white-space:nowrap;">
                            Cr (GHS)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr style="border-bottom:0.5px solid var(--border-color);">

                            {{-- Date --}}
                            <td
                                style="padding:6px 12px; font-size:12px;
                                       color:var(--text-muted); white-space:nowrap;">
                                {{ date('d M Y', strtotime($row->Date)) }}
                            </td>

                            {{-- Receipt No --}}
                            <td style="padding:6px 8px;">
                                <span
                                    style="font-family:monospace; font-size:12px;
                                           color:var(--text-primary);">
                                    {{ $row->ReceiptNo ?? '—' }}
                                </span>
                            </td>

                            {{-- Account --}}
                            <td
                                style="padding:6px 8px; font-size:12px;
                                       color:var(--text-primary);">
                                {{ Str::limit($row->AccountName, 24) }}
                            </td>

                            {{-- Description --}}
                            <td
                                style="padding:6px 8px; font-size:11px;
                                       color:var(--text-muted);">
                                {{ Str::limit($row->Description, 28) }}
                            </td>

                            {{-- Dr --}}
                            <td
                                style="padding:6px 8px; font-size:12px; text-align:right;
                                       white-space:nowrap;
                                       {{ (float) $row->Dr > 0 ? 'color:#A32D2D; font-weight:600;' : 'color:var(--text-muted);' }}">
                                {{ (float) $row->Dr > 0 ? number_format($row->Dr, 2) : '—' }}
                            </td>

                            {{-- Cr --}}
                            <td
                                style="padding:6px 12px; font-size:12px; text-align:right;
                                       white-space:nowrap;
                                       {{ (float) $row->Cr > 0 ? 'color:#3B6D11; font-weight:600;' : 'color:var(--text-muted);' }}">
                                {{ (float) $row->Cr > 0 ? number_format($row->Cr, 2) : '—' }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        {{-- ── Empty state ── --}}
        <div style="padding:24px; text-align:center;">
            <p style="font-size:12px; color:var(--text-muted); margin:0;">
                No transactions recorded yet.
            </p>
        </div>

    @endif

</div>
