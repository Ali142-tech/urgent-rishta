<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#F6F4EF; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F4EF; padding:30px 0;">
<tr>
<td align="center">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background:#ffffff; border-radius:14px; overflow:hidden;">
        <tr>
            <td style="background:#123A2E; padding:26px 30px; text-align:center;">
                <img src="{{ url('/images/header_logo2.png') }}" alt="Urgent Rishta" height="44" style="display:inline-block;">
            </td>
        </tr>
        <tr>
            <td style="padding:34px 30px 10px;">
                <p style="margin:0 0 4px; font-size:13px; color:#6B7570;">Hi {{ $firstName }},</p>
                <h1 style="margin:0 0 16px; font-size:20px; line-height:1.35; color:#123A2E; font-weight:700;">{{ $heading }}</h1>
                <p style="margin:0 0 26px; font-size:14px; line-height:1.6; color:#3A403C;">{{ $body }}</p>
                <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="border-radius:8px; background:#C9974D;">
                            <a href="{{ $ctaUrl }}" style="display:inline-block; padding:13px 28px; font-size:13.5px; font-weight:700; color:#123A2E; text-decoration:none;">{{ $ctaText }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:30px 30px 26px; border-top:1px solid #EFEAE0;">
                <p style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0;">
                    You're receiving this because you have an account with Urgent Rishta.
                    If you'd rather not get these emails, <a href="{{ $unsubscribeUrl ?? 'https://urgentrishta.com/contact-us' }}" style="color:#9AA5A0;">let us know</a> and we'll take you off this list.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
