<!doctype html>
<html>
<body style="margin:0; padding:24px; background:#F6F4EF; font-family:Arial, Helvetica, sans-serif; color:#1C2321;">
    <div style="max-width:520px; margin:0 auto; background:#fff; border:1px solid #E7E2D6; border-radius:14px; padding:26px;">
        <h2 style="margin:0 0 6px; color:#123A2E; font-size:20px;">
            {{ $cancel ? 'Consultation cancelled' : 'Your consultation is scheduled' }}
        </h2>
        <p style="margin:0 0 18px; color:#6B7570; font-size:14px;">
            @if($cancel)
                This consultation with Urgent Rishta has been cancelled. It has been removed from your calendar.
            @else
                Hello {{ $appointment->contact_name ?: 'there' }}, your consultation with Urgent Rishta is confirmed.
            @endif
        </p>

        <table cellpadding="0" cellspacing="0" style="width:100%; font-size:14px; border-collapse:collapse;">
            <tr>
                <td style="padding:8px 0; color:#6B7570; width:90px;">Date</td>
                <td style="padding:8px 0; font-weight:bold;">{{ $start->format('l, d F Y') }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#6B7570;">Time</td>
                <td style="padding:8px 0; font-weight:bold;">{{ $start->format('g:i A') }} – {{ $end->format('g:i A') }} ({{ $start->getTimezone()->getName() }})</td>
            </tr>
            @if($appointment->subject)
            <tr>
                <td style="padding:8px 0; color:#6B7570;">Reason</td>
                <td style="padding:8px 0;">{{ $appointment->subject }}</td>
            </tr>
            @endif
        </table>

        @unless($cancel)
        <p style="margin:20px 0 0; font-size:13px; color:#6B7570;">
            Open the attached <b>invite.ics</b> (or use the Yes / Add to calendar button your email app shows)
            to add it to Google Calendar, Outlook or Apple Calendar. If the time changes we will send an updated invite.
        </p>
        @endunless
    </div>
</body>
</html>
