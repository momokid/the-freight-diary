<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSIL Daily ETA Digest</title>
</head>

<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;font-size:14px;color:#1a1a1a;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;">

                    {{-- ── Header ── --}}
                    <tr>
                        <td style="background:#185FA5;padding:20px 28px;">
                            <p style="margin:0;color:#ffffff;font-size:18px;font-weight:bold;">
                                {{ $company->InstName ?? 'PSIL' }} | The Freight Diary
                            </p>
                            <p style="margin:4px 0 0;color:#cce0f5;font-size:13px;">
                                Daily ETA Digest &mdash; {{ now()->format('d M Y') }}
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="padding:24px 28px;">

                            @php
                                $hasActivity =
                                    !empty($digest['arriving_today']) ||
                                    !empty($digest['upcoming']) ||
                                    !empty($digest['eta_changed']);
                            @endphp

                            @if (!$hasActivity)

                                <p style="color:#6b7280;text-align:center;padding:32px 0;margin:0;">
                                    No ETA activity to report today.
                                </p>
                            @else
                                {{-- ── Arriving Today ── --}}
                                <p
                                    style="margin:0 0 8px;font-size:15px;font-weight:bold;color:#185FA5;
                       border-bottom:2px solid #185FA5;padding-bottom:6px;">
                                    Arriving Today ({{ count($digest['arriving_today']) }})
                                </p>

                                @if (empty($digest['arriving_today']))
                                    <p style="color:#6b7280;margin:0 0 24px;">None today.</p>
                                @else
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                        style="margin-bottom:28px;border-collapse:collapse;">
                                        <thead>
                                            <tr style="background:#185FA5;color:#ffffff;">
                                                <th style="padding:8px 10px;text-align:left;">BL No</th>
                                                <th style="padding:8px 10px;text-align:left;">Consignee</th>
                                                <th style="padding:8px 10px;text-align:left;">ETA</th>
                                                <th style="padding:8px 10px;text-align:center;">Containers</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($digest['arriving_today'] as $row)
                                                <tr
                                                    style="background:{{ $loop->index % 2 === 0 ? '#f9fafb' : '#ffffff' }};">
                                                    <td style="padding:8px 10px;font-family:monospace;">
                                                        {{ $row->BL }}</td>
                                                    <td style="padding:8px 10px;">{{ $row->FullName }}</td>
                                                    <td style="padding:8px 10px;">
                                                        {{ \Carbon\Carbon::parse($row->ETA)->format('d M Y') }}
                                                    </td>
                                                    <td style="padding:8px 10px;text-align:center;font-weight:bold;">
                                                        {{ $row->ContainerCount ?: '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                {{-- ── Upcoming — Next 7 Days ── --}}
                                <p
                                    style="margin:0 0 8px;font-size:15px;font-weight:bold;color:#185FA5;
                       border-bottom:2px solid #185FA5;padding-bottom:6px;">
                                    Upcoming &mdash; Next 7 Days ({{ count($digest['upcoming']) }})
                                </p>

                                @if (empty($digest['upcoming']))
                                    <p style="color:#6b7280;margin:0 0 24px;">None in the next 7 days.</p>
                                @else
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                        style="margin-bottom:28px;border-collapse:collapse;">
                                        <thead>
                                            <tr style="background:#185FA5;color:#ffffff;">
                                                <th style="padding:8px 10px;text-align:left;">BL No</th>
                                                <th style="padding:8px 10px;text-align:left;">Consignee</th>
                                                <th style="padding:8px 10px;text-align:left;">ETA</th>
                                                <th style="padding:8px 10px;text-align:center;">Days</th>
                                                <th style="padding:8px 10px;text-align:center;">Containers</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (collect($digest['upcoming'])->sortBy('ETADays') as $row)
                                                <tr
                                                    style="background:{{ $loop->index % 2 === 0 ? '#f9fafb' : '#ffffff' }};">
                                                    <td style="padding:8px 10px;font-family:monospace;">
                                                        {{ $row->BL }}</td>
                                                    <td style="padding:8px 10px;">{{ $row->FullName }}</td>
                                                    <td style="padding:8px 10px;">
                                                        {{ \Carbon\Carbon::parse($row->ETA)->format('d M Y') }}
                                                    </td>
                                                    <td
                                                        style="padding:8px 10px;text-align:center;font-weight:bold;
                                                        color:{{ (int) $row->ETADays <= 2 ? '#b91c1c' : '#185FA5' }};">
                                                        {{ $row->ETADays }}d
                                                    </td>
                                                    <td style="padding:8px 10px;text-align:center;font-weight:bold;">
                                                        {{ $row->ContainerCount ?: '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                {{-- ── ETA Changes Today ── --}}
                                <p
                                    style="margin:0 0 8px;font-size:15px;font-weight:bold;color:#185FA5;
                       border-bottom:2px solid #185FA5;padding-bottom:6px;">
                                    ETA Changes Today ({{ count($digest['eta_changed']) }})
                                </p>

                                @if (empty($digest['eta_changed']))
                                    <p style="color:#6b7280;margin:0 0 8px;">No ETA changes today.</p>
                                @else
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                        style="border-collapse:collapse;">
                                        <thead>
                                            <tr style="background:#185FA5;color:#ffffff;">
                                                <th style="padding:8px 10px;text-align:left;">BL No</th>
                                                <th style="padding:8px 10px;text-align:left;">Consignee</th>
                                                <th style="padding:8px 10px;text-align:left;">New ETA</th>
                                                <th style="padding:8px 10px;text-align:center;">Containers</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($digest['eta_changed'] as $row)
                                                <tr
                                                    style="background:{{ $loop->index % 2 === 0 ? '#f9fafb' : '#ffffff' }};">
                                                    <td style="padding:8px 10px;font-family:monospace;">
                                                        {{ $row->BL }}</td>
                                                    <td style="padding:8px 10px;">{{ $row->FullName }}</td>
                                                    <td style="padding:8px 10px;font-weight:bold;">
                                                        {{ \Carbon\Carbon::parse($row->ETA)->format('d M Y') }}
                                                    </td>
                                                    <td style="padding:8px 10px;text-align:center;font-weight:bold;">
                                                        {{ $row->ContainerCount ?: '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                            @endif

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#f4f6f8;padding:14px 28px;border-top:1px solid #e5e7eb;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="color:#9ca3af;font-size:11px;">
                                        Confidential &mdash; PSIL internal use only.
                                    </td>
                                    <td align="right" style="color:#9ca3af;font-size:11px;white-space:nowrap;">
                                        Freight Diary v2.0 &middot; {{ now()->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
