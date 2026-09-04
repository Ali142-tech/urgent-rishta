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
            <td style="padding:36px 30px 8px; text-align:center;">
                <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto 20px;">
                    <tr>
                        <td width="64" height="64" align="center" valign="middle" style="background:#F7EBE3; border-radius:50%; font-size:28px; line-height:64px; color:#B5674A;">&#33;</td>
                    </tr>
                </table>
                <h1 style="margin:0 0 12px; font-size:21px; line-height:1.35; color:#123A2E; font-weight:700;">We need a couple new photos</h1>
                <p style="margin:0 0 4px; font-size:14px; line-height:1.6; color:#3A403C; text-align:left;">Hi {{ $user->first_name }},</p>
                <p style="margin:0 0 18px; font-size:14px; line-height:1.6; color:#3A403C; text-align:left;">
                    We've reviewed your profile photos and, unfortunately, couldn't approve them this time.
                </p>
                @if(!empty($reason))
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px; background:#FBF7EF; border:1px solid #EFEAE0; border-radius:10px;">
                    <tr>
                        <td style="padding:14px 16px;">
                            <p style="margin:0 0 4px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#9AA5A0;">Reason from our team</p>
                            <p style="margin:0; font-size:13.5px; line-height:1.55; color:#3A403C;">{{ $reason }}</p>
                        </td>
                    </tr>
                </table>
                @endif
                <p style="margin:0 0 28px; font-size:14px; line-height:1.6; color:#3A403C; text-align:left;">
                    For security, your account is on hold until our team reopens it for a fresh upload — please reach out and we'll get you sorted right away.
                </p>
                <table role="presentation" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <td style="border-radius:8px; background:#C9974D;">
                            <a href="{{ url('contact-us') }}" style="display:inline-block; padding:13px 30px; font-size:13.5px; font-weight:700; color:#123A2E; text-decoration:none;">Contact Support</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:30px 30px 26px; border-top:1px solid #EFEAE0;">
                <p style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0; text-align:center;">
                    You can also call us directly at 0304-0227000.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
