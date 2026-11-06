<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
    :root { color-scheme: light; }
    @media only screen and (max-width: 480px) { .ur-px { padding-left:20px !important; padding-right:20px !important; } .ur-h1 { font-size:20px !important; } }
    @media (prefers-color-scheme: dark) {
        .ur-bg-page { background-color:#F6F4EF !important; } .ur-bg-card { background-color:#ffffff !important; }
        .ur-bg-header { background-color:#123A2E !important; } .ur-bg-accent { background-color:#C9974D !important; }
    }
</style>
</head>
<body style="margin:0; padding:0; background-color:#F6F4EF; font-family: Arial, Helvetica, sans-serif;">
<div style="display:none; max-height:0; overflow:hidden; opacity:0;">Hi {{ $firstName }}, complete your profile to 100% and enjoy one week of free access.</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#F6F4EF" class="ur-bg-page" style="background:#F6F4EF; padding:30px 0;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="ur-bg-card" style="max-width:560px; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 4px 18px rgba(18,58,46,0.08);">
    <tr><td bgcolor="#123A2E" class="ur-bg-header" style="background:#123A2E; padding:26px 30px; text-align:center;">
        <img src="https://urgentrishta.com/images/header_logo2.png" alt="Urgent Rishta" height="40" style="display:inline-block;">
    </td></tr>

    <tr><td class="ur-px" style="padding:32px 34px 6px; text-align:center;">
        <p style="margin:0 0 8px; font-size:11.5px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#C9974D;">Complete Your Profile</p>
        <h1 class="ur-h1" style="margin:0; font-family: Georgia, 'Times New Roman', serif; font-size:24px; line-height:1.35; color:#123A2E; font-weight:700;">Reach 100% &amp; get 1 week FREE access</h1>
    </td></tr>

    <tr><td class="ur-px" style="padding:18px 34px 6px;">
        <p style="margin:0 0 14px; font-size:14px; line-height:1.65; color:#3A403C;">Dear {{ $firstName }},</p>
        <p style="margin:0 0 14px; font-size:14px; line-height:1.65; color:#3A403C;">Your profile is currently <b style="color:#123A2E;">{{ $percent }}% complete</b>. A complete profile gets you far better, more compatible match results.</p>
        <p style="margin:0; font-size:14px; line-height:1.65; color:#3A403C;"><b style="color:#123A2E;">Special offer:</b> update the missing details below and, as soon as your profile reaches <b>100%</b>, we will give you <b>one week of free access</b> to the site.</p>
    </td></tr>

    <tr><td class="ur-px" style="padding:22px 34px 6px;">
        <p style="margin:0 0 12px; font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#123A2E; border-top:1px solid #EFEAE0; padding-top:22px;">What is still missing</p>
        @foreach($groups as $title => $labels)
            <p style="margin:0 0 4px; font-size:13.5px; font-weight:700; color:#123A2E;">{{ $title }}</p>
            <p style="margin:0 0 12px; font-size:13px; line-height:1.7; color:#6B7570;">
                @foreach($labels as $label)&bull; {{ $label }}<br>@endforeach
            </p>
        @endforeach
        @if($moreCount > 0)
            <p style="margin:0; font-size:13px; color:#6B7570;">&hellip; and {{ $moreCount }} more.</p>
        @endif
    </td></tr>

    <tr><td class="ur-px" style="padding:14px 34px 30px;" align="center">
        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
            <td bgcolor="#C9974D" class="ur-bg-accent" style="border-radius:8px; background:#C9974D;">
                <a href="{{ $completeUrl }}" style="display:inline-block; padding:14px 30px; font-size:13.5px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:none;">COMPLETE MY PROFILE</a>
            </td>
        </tr></table>
        <p style="margin:14px 0 0; font-size:12px; line-height:1.6; color:#9AA5A0;">Please log in first if asked.</p>
    </td></tr>

    <tr><td class="ur-px" style="padding:0 34px 26px;">
        <p style="margin:0; font-size:13px; line-height:1.6; color:#3A403C;">Warm regards,<br><b style="color:#123A2E;">Urgent Rishta Team</b></p>
    </td></tr>

    <tr><td class="ur-px" style="padding:22px 34px 28px; border-top:1px solid #EFEAE0;">
        <p style="margin:0 0 10px; font-size:12px; line-height:1.8; color:#6B7570;">
            Website: <a href="https://www.urgentrishta.com" style="color:#123A2E;">urgentrishta.com</a><br>
            WhatsApp: <a href="https://wa.me/923040227000" style="color:#123A2E;">+92 304 0227000</a>
        </p>
        <p style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0;">You're receiving this because you have an account with Urgent Rishta. If you'd rather not get these emails, <a href="https://www.urgentrishta.com/contact-us" style="color:#9AA5A0;">let us know</a>.</p>
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
