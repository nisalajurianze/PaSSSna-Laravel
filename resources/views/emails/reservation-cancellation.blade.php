<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reservation Update - PaSSSna Restaurant</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3A8A; color: white; padding: 22px; border-radius: 12px 12px 0 0; }
        .content { background: #f9fafb; padding: 22px; border-radius: 0 0 12px 12px; }
        .card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin: 14px 0; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        .value { color: #111827; font-size: 14px; font-weight: 600; margin-top: 4px; }
        .cta { display: inline-block; background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; }
        .footer { color: #6b7280; font-size: 12px; margin-top: 18px; text-align: center; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge-red { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
    @php
        $tableNumbers = $reservation->tables?->pluck('table_number')->values()->all() ?? [];
        if (empty($tableNumbers) && is_array($reservation->table_numbers)) {
            $tableNumbers = $reservation->table_numbers;
        }
        $statusLabel = ucfirst(str_replace('_', ' ', $reservation->status));
    @endphp

    <div class="container">
        <div class="header">
            <h1 style="margin:0; font-size: 22px;">Reservation Update</h1>
            <p style="margin:6px 0 0; opacity: .95;">PaSSSna Restaurant</p>
        </div>

        <div class="content">
            <p style="margin-top:0;">Dear {{ $reservation->customer_name }},</p>

            <p>
                Your reservation status has been updated:
                <span class="badge badge-red">{{ $statusLabel }}</span>
            </p>

            <div class="card">
                <div style="margin-bottom: 10px;">
                    <div class="label">Reservation Number</div>
                    <div class="value">{{ $reservation->reservation_number }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <div class="label">Date</div>
                        <div class="value">{{ optional($reservation->reservation_date)->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="label">Time</div>
                        <div class="value">{{ $reservation->reservation_time }}</div>
                    </div>
                    <div>
                        <div class="label">Guests</div>
                        <div class="value">{{ $reservation->number_of_people }}</div>
                    </div>
                    <div>
                        <div class="label">Table{{ count($tableNumbers) > 1 ? 's' : '' }}</div>
                        <div class="value">{{ !empty($tableNumbers) ? implode(', ', $tableNumbers) : '—' }}</div>
                    </div>
                </div>
            </div>

            @if($reservation->cancellation_reason)
                <div class="card">
                    <div class="label">Message from PaSSSna</div>
                    <div class="value" style="font-weight: 400;">{!! nl2br(e($reservation->cancellation_reason)) !!}</div>
                </div>
            @endif

            <p style="margin: 18px 0 0;">
                <a class="cta" href="{{ url('/reservation') }}">Book Another Table</a>
            </p>

            <div class="footer">
                <p style="margin: 10px 0 0;">Generated on {{ now()->format('F d, Y') }}</p>
                <p style="margin: 6px 0 0;">&copy; {{ date('Y') }} PaSSSna Restaurant. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>


