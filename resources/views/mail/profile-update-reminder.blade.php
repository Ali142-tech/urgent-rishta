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
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:14px; overflow:hidden;">

        {{-- Header --}}
        <tr>
            <td style="background:#123A2E; padding:26px 30px; text-align:center;">
                <img src="{{ url('/images/header_logo2.png') }}" alt="Urgent Rishta" height="40" style="display:inline-block;">
            </td>
        </tr>

        {{-- Hero image --}}
        <tr>
            <td>
                <img src="{{ url('/images/couples/second-marriage-couple.jpg') }}" alt="" width="560" style="display:block; width:100%; max-width:560px; height:auto;">
            </td>
        </tr>

        {{-- Eyebrow + heading --}}
        <tr>
            <td style="padding:32px 34px 6px; text-align:center;">
                <p style="margin:0 0 8px; font-size:11.5px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#C9974D;">Complete Your Profile</p>
                <h1 style="margin:0; font-size:23px; line-height:1.3; color:#123A2E; font-weight:700;">Improve Your Match Results</h1>
            </td>
        </tr>

        {{-- Intro --}}
        <tr>
            <td style="padding:18px 34px 6px;">
                <p style="margin:0 0 14px; font-size:14px; line-height:1.65; color:#3A403C;">Dear {{ $firstName }},</p>
                <p style="margin:0 0 14px; font-size:14px; line-height:1.65; color:#3A403C;">We have recently upgraded the Urgent Rishta website and introduced several new features to make your matchmaking experience more accurate, private and convenient.</p>
                <p style="margin:0; font-size:14px; line-height:1.65; color:#3A403C;">Following these updates, some information in existing profiles may now appear incomplete or missing. We kindly request you to review and update your profile so our system can provide you with more relevant and compatible match results.</p>
            </td>
        </tr>

        {{-- Steps section --}}
        <tr>
            <td style="padding:28px 34px 6px;">
                <p style="margin:0 0 18px; font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#123A2E; border-top:1px solid #EFEAE0; padding-top:24px;">Your Quick Profile Update Guide</p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="40" valign="top" style="padding-bottom:20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"><tr><td width="30" height="30" align="center" valign="middle" style="background:#123A2E; border-radius:50%; color:#ffffff; font-size:13px; font-weight:700;">01</td></tr></table>
                        </td>
                        <td valign="top" style="padding:0 0 20px 14px;">
                            <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#123A2E;">Log In to Your Account</p>
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#6B7570;">Visit UrgentRishta.com and select &ldquo;Log In.&rdquo;</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="40" valign="top" style="padding-bottom:20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"><tr><td width="30" height="30" align="center" valign="middle" style="background:#123A2E; border-radius:50%; color:#ffffff; font-size:13px; font-weight:700;">02</td></tr></table>
                        </td>
                        <td valign="top" style="padding:0 0 20px 14px;">
                            <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#123A2E;">Open and Complete Your Profile</p>
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#6B7570;">Go to &ldquo;My Profile&rdquo; or &ldquo;Edit Profile&rdquo; and complete all missing personal, educational, professional, family and partner-preference fields. A complete and accurate profile significantly improves the quality of your match results.</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="40" valign="top" style="padding-bottom:20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"><tr><td width="30" height="30" align="center" valign="middle" style="background:#123A2E; border-radius:50%; color:#ffffff; font-size:13px; font-weight:700;">03</td></tr></table>
                        </td>
                        <td valign="top" style="padding:0 0 20px 14px;">
                            <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#123A2E;">Choose Your Photo Privacy</p>
                            <p style="margin:0 0 8px; font-size:13px; line-height:1.6; color:#6B7570;">You are now fully in control of your photographs.</p>
                            <p style="margin:0; font-size:13px; line-height:1.8; color:#6B7570;">
                                &bull; Make your profile photo visible<br>
                                &bull; Keep your photo hidden for privacy<br>
                                &bull; Change your photo visibility whenever you prefer
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="40" valign="top">
                            <table role="presentation" cellpadding="0" cellspacing="0"><tr><td width="30" height="30" align="center" valign="middle" style="background:#123A2E; border-radius:50%; color:#ffffff; font-size:13px; font-weight:700;">04</td></tr></table>
                        </td>
                        <td valign="top" style="padding:0 0 0 14px;">
                            <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#123A2E;">Review and Save</p>
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#6B7570;">Check that all information is correct, upload recent photographs and select &ldquo;Save Changes.&rdquo;</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Primary CTA --}}
        <tr>
            <td style="padding:8px 34px 30px;" align="center">
                <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="border-radius:8px; background:#C9974D;">
                            <a href="https://www.urgentrishta.com/login" style="display:inline-block; padding:14px 30px; font-size:13.5px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:none;">LOG IN &amp; UPDATE MY PROFILE</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Personalized service section --}}
        <tr>
            <td style="padding:0 34px 30px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F4EF; border-radius:12px;">
                    <tr>
                        <td style="padding:26px 26px 24px;">
                            <p style="margin:0 0 10px; font-size:15px; font-weight:700; color:#123A2E;">Prefer a Personally Managed Search?</p>
                            <p style="margin:0 0 14px; font-size:13px; line-height:1.6; color:#3A403C;">Our Personalized Confidential Matchmaking Service is also available for clients who would like an experienced specialist matchmaker to manage their search. Your dedicated matchmaker will:</p>
                            <p style="margin:0 0 18px; font-size:13px; line-height:1.9; color:#3A403C;">
                                &bull; Understand your background and requirements<br>
                                &bull; Review suitable and compatible profiles<br>
                                &bull; Present selected proposals personally<br>
                                &bull; Coordinate introductions privately and professionally
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:8px; border:1.5px solid #123A2E;">
                                        <a href="https://www.urgentrishta.com/packages?type=personalized" style="display:inline-block; padding:11px 22px; font-size:12.5px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:none;">EXPLORE PERSONALIZED SERVICE</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Closing note --}}
        <tr>
            <td style="padding:0 34px 26px;">
                <p style="margin:0 0 22px; font-size:13px; line-height:1.6; color:#6B7570;">Your privacy, preferences and personal information remain important to us throughout the matchmaking process.</p>
                <p style="margin:0; font-size:13px; line-height:1.6; color:#3A403C;">
                    Warm regards,<br>
                    <b style="color:#123A2E;">Urgent Rishta Team</b><br>
                    <span style="color:#C9974D; font-size:12px;">Private &bull; Verified &bull; Professional Matchmaking</span><br>
                    <span style="color:#6B7570; font-size:12px;">16+ Years of Trust &amp; Service</span>
                </p>
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td style="padding:22px 34px 28px; border-top:1px solid #EFEAE0;">
                <p style="margin:0 0 10px; font-size:12px; line-height:1.8; color:#6B7570;">
                    Website: <a href="https://www.urgentrishta.com" style="color:#123A2E;">urgentrishta.com</a><br>
                    WhatsApp: <a href="https://wa.me/923040227000" style="color:#123A2E;">+92 304 0227000</a><br>
                    Email: <a href="mailto:urgentrishta.co@gmail.com" style="color:#123A2E;">urgentrishta.co@gmail.com</a>
                </p>
                <p style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0;">
                    You're receiving this because you have an account with Urgent Rishta.
                    If you'd rather not get these emails, <a href="{{ $unsubscribeUrl ?? 'https://www.urgentrishta.com/contact-us' }}" style="color:#9AA5A0;">let us know</a> and we'll take you off this list.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
