@php
    $senderAge = !empty($sender->birthday) ? date_diff(date_create($sender->birthday), date_create('now'))->y : null;
    $senderCity = $sender->profile()->lbl_city ?? null;
    $senderAvatarUrl = 'https://urgentrishta.co' . \App\Profile::defaultImage($sender->gender);
@endphp
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
        .ur-bg-note   { background-color:#FCF3E3 !important; }
        .ur-text-green { color:#123A2E !important; }
        .ur-text-gold  { color:#C9974D !important; }
        .ur-text-body  { color:#3A403C !important; }
        .ur-text-muted { color:#6B7570 !important; }
        .ur-text-faint { color:#9AA5A0 !important; }
        .ur-text-white { color:#ffffff !important; }
        .ur-text-note  { color:#7A5B23 !important; }
    }
    [data-ogsc] .ur-bg-page, [data-ogsb] .ur-bg-page { background-color:#F6F4EF !important; }
    [data-ogsc] .ur-bg-card, [data-ogsb] .ur-bg-card { background-color:#ffffff !important; }
    [data-ogsc] .ur-bg-header, [data-ogsb] .ur-bg-header { background-color:#123A2E !important; }
    [data-ogsc] .ur-text-green, [data-ogsb] .ur-text-green { color:#123A2E !important; }
</style>
</head>
<body style="margin:0; padding:0; background-color:#F6F4EF; font-family: Arial, Helvetica, sans-serif;">
<div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
    {{ $sender->getFullName() }} ({{ $senderAge ? $senderAge . ' yrs' : '' }}{{ $senderCity ? ', ' . $senderCity : '' }}) just sent you a rishta interest.
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
                <p class="ur-text-gold" style="margin:0 0 8px; font-size:11.5px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#C9974D;">New Rishta Interest</p>
                <h1 class="ur-h1 ur-text-green" style="margin:0; font-family: Georgia, 'Times New Roman', serif; font-size:24px; line-height:1.35; color:#123A2E; font-weight:700;">Someone Just Expressed Interest in You</h1>
            </td>
        </tr>

        {{-- Intro --}}
        <tr>
            <td class="ur-px" style="padding:12px 34px 22px;">
                <p class="ur-text-body" style="margin:0; font-size:14px; line-height:1.65; color:#3A403C;">Hello, you have just received a rishta interest. Here are their details:</p>
            </td>
        </tr>

        {{-- Sender card --}}
        <tr>
            <td class="ur-px" style="padding:0 34px 8px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#F6F4EF" class="ur-bg-tile" style="background:#F6F4EF; border-radius:12px;">
                    <tr>
                        <td width="88" valign="top" style="padding:18px 0 18px 18px;">
                            <img src="{{ $senderAvatarUrl }}" width="64" height="64" alt="{{ ucfirst($sender->gender) }} profile" style="display:block; width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid #C9974D;">
                        </td>
                        <td valign="middle" style="padding:18px 18px 18px 14px;">
                            <p class="ur-text-green" style="margin:0 0 4px; font-size:16px; font-weight:700; color:#123A2E; font-family: Georgia, 'Times New Roman', serif;">{{ $sender->getFullName() }}</p>
                            <p class="ur-text-faint" style="margin:0 0 3px; font-size:12px; color:#9AA5A0;">ID: {{ $sender->dataid }}</p>
                            <p class="ur-text-muted" style="margin:0; font-size:13px; color:#6B7570;">
                                {{ $senderAge ? $senderAge . ' yrs' : '' }}{{ ($senderAge && $senderCity) ? ' • ' : '' }}{{ $senderCity ?? '' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Primary CTA --}}
        <tr>
            <td class="ur-px" style="padding:26px 34px 10px;" align="center">
                <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        <td bgcolor="#C9974D" class="ur-bg-accent" style="border-radius:8px; background:#C9974D;">
                            <a href="https://urgentrishta.co/member/profile/listing/interests" class="ur-text-green" style="display:inline-block; padding:14px 34px; font-size:13.5px; font-weight:700; letter-spacing:.3px; color:#123A2E; text-decoration:none;">RESPOND TO INTEREST</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        @if(!empty($showUpgradeNote))
        {{-- Upgrade note --}}
        <tr>
            <td class="ur-px" style="padding:16px 34px 8px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FCF3E3" class="ur-bg-note" style="background:#FCF3E3; border-radius:8px;">
                    <tr>
                        <td style="padding:14px 16px; border-left:4px solid #C9974D;">
                            <p class="ur-text-note" style="margin:0; font-size:13px; line-height:1.6; color:#7A5B23;">
                                <strong>This interest came from one of our Royal members.</strong><br>
                                Upgrade your package to Royal to get the most out of a match like this.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif

        {{-- Footer --}}
        <tr>
            <td class="ur-px" style="padding:26px 34px 28px; border-top:1px solid #EFEAE0;">
                <p class="ur-text-muted" style="margin:0 0 10px; font-size:12px; line-height:1.8; color:#6B7570;">
                    Website: <a href="https://urgentrishta.co" class="ur-text-green" style="color:#123A2E;">urgentrishta.co</a><br>
                    WhatsApp: <a href="https://wa.me/923040227000" class="ur-text-green" style="color:#123A2E;">+92 304 0227000</a><br>
                    Email: <a href="mailto:urgentrishta.co@gmail.com" class="ur-text-green" style="color:#123A2E;">urgentrishta.co@gmail.com</a>
                </p>
                <p class="ur-text-faint" style="margin:0; font-size:11.5px; line-height:1.6; color:#9AA5A0;">
                    You're receiving this because you have an account with Urgent Rishta.
                </p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
