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
                        <td width="64" height="64" align="center" valign="middle" style="background:#EAF1EE; border-radius:50%; font-size:30px; line-height:64px; color:#123A2E;">&#10003;</td>
                    </tr>
                </table>
                <h1 style="margin:0 0 12px; font-size:21px; line-height:1.35; color:#123A2E; font-weight:700;">Your profile is verified!</h1>
                <p style="margin:0 0 4px; font-size:14px; line-height:1.6; color:#3A403C; text-align:left;">Hi {{ $user->first_name }},</p>
                <p style="margin:0 0 28px; font-size:14px; line-height:1.6; color:#3A403C; text-align:left;">
                    Good news — our team has reviewed your profile and photos, and everything checks out.
                    Your account is now <b style="color:#123A2E;">fully active</b> and visible to other verified members.
                </p>
                <table role="presentation" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <td style="border-radius:8px; background:#C9974D;">
                            <a href="{{ url('login') }}" style="display:inline-block; padding:13px 30px; font-size:13.5px; font-weight:700; color:#123A2E; text-decoration:none;">Log In to Get Started</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:30px 30px 26px; border-top:1px solid #EFEAE0;">
                <p style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0; text-align:center;">
                    Need help? Contact us anytime at <a href="{{ url('contact-us') }}" style="color:#9AA5A0;">urgentrishta.com/contact-us</a>.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
