<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f5; padding: 24px; margin: 0;">
    <div
        style="max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb;">

        <div style="background: #dc2626; padding: 16px 24px;">
            <p style="color: #ffffff; font-size: 0.95rem; font-weight: 700; margin: 0;">⚠ New Error Ticket</p>
        </div>

        <div style="padding: 24px;">
            <p style="font-size: 0.85rem; color: #374151; margin: 0 0 6px;">
                <strong>Type:</strong> {{ class_basename($error['ExceptionClass']) }}
            </p>
            <p style="font-size: 0.85rem; color: #374151; margin: 0 0 6px;">
                <strong>Message:</strong> {{ \Illuminate\Support\Str::limit($error['Message'], 150) }}
            </p>
            <p style="font-size: 0.85rem; color: #374151; margin: 0 0 6px;">
                <strong>Route:</strong> {{ $error['Route'] ?? 'N/A' }}
            </p>
            <p style="font-size: 0.85rem; color: #374151; margin: 0 0 20px;">
                <strong>Time:</strong> {{ now()->format('d M Y, h:i A') }}
            </p>

            <a href="{{ url('/settings/error-log') }}"
                style="display: inline-block; padding: 10px 20px; background: #16a34a; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                View Full Details →
            </a>
        </div>

    </div>
</body>

</html>
