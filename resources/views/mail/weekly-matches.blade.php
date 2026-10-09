<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
    :root { color-scheme: light; }

    @media only screen and (max-width: 480px) {
        .ur-px { padding-left:20px !important; padding-right:20px !important; }
        .ur-h1 { font-size:20px !important; }
    }

    @media (prefers-color-scheme: dark) {
        .ur-bg-page   { background-color:#F6F4EF !important; }
        .ur-bg-card   { background-color:#ffffff !important; }
        .ur-bg-header { background-color:#123A2E !important; }
        .ur-bg-accent { background-color:#C9974D !important; }
        .ur-bg-tile   { background-color:#F6F4EF !important; }
        .ur-text-green { color:#123A2E !important; }
        .ur-text-gold  { color:#C9974D !important; }
        .ur-text-body  { color:#3A403C !important; }
        .ur-text-muted { color:#6B7570 !important; }
        .ur-text-faint { color:#9AA5A0 !important; }
        .ur-text-white { color:#ffffff !important; }
    }
    [data-ogsc] .ur-bg-page, [data-ogsb] .ur-bg-page { background-color:#F6F4EF !important; }
    [data-ogsc] .ur-bg-card, [data-ogsb] .ur-bg-card { background-color:#ffffff !important; }
    [data-ogsc] .ur-bg-header, [data-ogsb] .ur-bg-header { background-color:#123A2E !important; }
    [data-ogsc] .ur-text-green, [data-ogsb] .ur-text-green { color:#123A2E !important; }
</style>
</head>
<body style="margin:0; padding:0; background-color:#F6F4EF; font-family: Arial, Helvetica, sans-serif;">
<div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
    Hi {{ $firstName }}, {{ $profiles->count() }} new matches are waiting for you this week.
</div>
<div style="display:none; max-height:0; overflow:hidden;">&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#F6F4EF" class="ur-bg-page" style="background:#F6F4EF; padding:30px 0; font-family: Arial, Helvetica, sans-serif;">
<tr>
<td align="center">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="ur-bg-card" style="max-width:560px; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 4px 18px rgba(18,58,46,0.08);">

        {{-- Header --}}
        <tr>
            <td bgcolor="#123A2E" class="ur-bg-header" style="background:#123A2E; padding:26px 30px; text-align:center;">
                <img src="https://urgentrishta.co/images/header_logo2.png" alt="Urgent Rishta" height="40" style="display:inline-block;">
            </td>
        </tr>

        {{-- Eyebrow + heading --}}
        <tr>
            <td class="ur-px" style="padding:32px 34px 6px; text-align:center;">
                <p class="ur-text-gold" style="margin:0 0 8px; font-size:11.5px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#C9974D;">Weekly Matches</p>
                <h1 class="ur-h1 ur-text-green" style="margin:0; font-family: Georgia, 'Times New Roman', serif; font-size:24px; line-height:1.35; color:#123A2E; font-weight:700;">Your Weekly Matches</h1>
            </td>
        </tr>

        {{-- Intro --}}
        <tr>
            <td class="ur-px" style="padding:12px 34px 26px;">
                <p class="ur-text-body" style="margin:0; font-size:14px; line-height:1.65; color:#3A403C;">Dear {{ $firstName }}, based on your partner preferences, here are {{ $profiles->count() }} {{ $profiles->count() === 1 ? 'profile' : 'profiles' }} we think you'll want to meet this week.</p>
            </td>
        </tr>

        {{-- Profile cards --}}
        @foreach($profiles as $profile)
        <tr>
            <td class="ur-px" style="padding:0 34px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#F6F4EF" class="ur-bg-tile" style="background:#F6F4EF; border-radius:12px;">
                    <tr>
                        <td width="90" valign="top" style="padding:16px 0 16px 16px;">
                            <img src="{{ $profile->image_url }}" width="72" height="72" alt="" style="display:block; width:72px; height:72px; border-radius:8px; object-fit:cover;">
                        </td>
                        <td valign="top" style="padding:16px 16px 16px 14px;">
                            <p class="ur-text-green" style="margin:0 0 4px; font-size:15px; font-weight:700; color:#123A2E;">{{ $profile->name_line }}</p>
                            @if($profile->location_line)
                            <p class="ur-text-muted" style="margin:0 0 2px; font-size:12.5px; line-height:1.5; color:#6B7570;">{{ $profile->location_line }}</p>
                            @endif
                            @if($profile->profession)
                            <p class="ur-text-muted" style="margin:0 0 10px; font-size:12.5px; line-height:1.5; color:#6B7570;">{{ $profile->profession }}</p>
                            @endif
                            <a href="{{ $profile->profile_url }}" class="ur-text-green" style="display:inline-block; font-size:12px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:underline;">View Profile &rarr;</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @endforeach

        {{-- Primary CTA --}}
        <tr>
            <td class="ur-px" style="padding:8px 34px 34px;" align="center">
                <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        <td bgcolor="#C9974D" class="ur-bg-accent" style="border-radius:8px; background:#C9974D;">
                            <a href="https://urgentrishta.co/member/recommended-matches" class="ur-text-green" style="display:inline-block; padding:14px 30px; font-size:13.5px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:none;">VIEW ALL YOUR MATCHES</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td class="ur-px" style="padding:22px 34px 28px; border-top:1px solid #EFEAE0;">
                <p class="ur-text-muted" style="margin:0 0 10px; font-size:12px; line-height:1.8; color:#6B7570;">
                    Website: <a href="https://urgentrishta.co" class="ur-text-green" style="color:#123A2E;">urgentrishta.co</a><br>
                    WhatsApp: <a href="https://wa.me/923040227000" class="ur-text-green" style="color:#123A2E;">+92 304 0227000</a><br>
                    Email: <a href="mailto:urgentrishta.co@gmail.com" class="ur-text-green" style="color:#123A2E;">urgentrishta.co@gmail.com</a>
                </p>
                <p class="ur-text-faint" style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0;">
                    You're receiving this because you have an account with Urgent Rishta and preferences saved on your profile.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
