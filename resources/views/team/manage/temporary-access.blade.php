{{--
    Admin ▸ Temporary Access — short-visit accounts for the Team Dashboard. The admin enters a name + email and picks 15 or 30
    minutes; the page shows the sign-in details ONCE (only the password's hash is kept) with a ready-to-send message. The person's
    clock starts at their first sign-in; when it ends the account can never sign in again. They can only browse the shared
    proposals: no photos, no contact details, no sharing, no editing (see EnsureUserIsTeamMember + the proposal views).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Temporary Access')
@section('main-content')
<style>
    .ur-ta { max-width: 1100px; }
    .ur-ta-eyebrow { font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #C9974D; margin: 0 0 6px; }
    .ur-ta h1 { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: #123A2E; margin: 0 0 6px; }
    .ur-ta-sub { color: #6B7570; font-size: 14px; margin: 0 0 22px; max-width: 680px; line-height: 1.6; }
    .ur-ta-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 22px; margin-bottom: 20px; }
    .ur-ta-card h2 { font-size: 16px; font-weight: 800; color: #123A2E; margin: 0 0 4px; }
    .ur-ta-card p.hint { margin: 0 0 16px; font-size: 13px; color: #6B7570; }
    .ur-ta-form { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto auto; gap: 12px; align-items: end; }
    .ur-ta-field { min-width: 0; }
    .ur-ta-field label { display: block; margin: 0 0 6px; font-size: 11.5px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #6B7570; }
    .ur-ta-field input[type=text], .ur-ta-field input[type=email] { width: 100%; height: 46px; padding: 0 14px; border: 1px solid #E7E2D6; border-radius: 12px; background: #FBF7EF; font-size: 14px; color: #1C2321; }
    .ur-ta-field input:focus { outline: none; border-color: #C9974D; background: #fff; }
    .ur-ta-mins { display: inline-flex; gap: 8px; }
    .ur-ta-mins label { margin: 0; text-transform: none; letter-spacing: 0; cursor: pointer; }
    .ur-ta-mins input { position: absolute; opacity: 0; pointer-events: none; }
    .ur-ta-mins span { display: inline-flex; align-items: center; justify-content: center; height: 46px; min-width: 84px; padding: 0 16px; border: 1.5px solid #E7E2D6; border-radius: 12px; background: #fff; font-size: 14px; font-weight: 700; color: #123A2E; }
    .ur-ta-mins input:checked + span { background: #123A2E; border-color: #123A2E; color: #fff; }
    .ur-ta-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 46px; padding: 0 22px; border: 0; border-radius: 999px; background: #123A2E; color: #fff !important; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none !important; white-space: nowrap; }
    .ur-ta-btn:hover { background: #0D2C23; }
    /* the site's global span/icon colours must not override the button text */
    .ur-ta-btn i, .ur-ta-btn span { color: inherit !important; }
    .ur-ta-btn--wa { background: #25D366; }
    .ur-ta-btn--wa:hover { background: #1DA851; }
    .ur-ta-btn--light { background: #fff; color: #123A2E !important; border: 1.5px solid #123A2E; }
    .ur-ta-btn--light:hover { background: #F3F9F5; }
    .ur-ta-btn--sm { height: 34px; padding: 0 14px; font-size: 12.5px; }
    .ur-ta-btn--danger { background: #fff; color: #B5413A !important; border: 1.5px solid #E4A8A2; }
    .ur-ta-btn--danger:hover { background: #FDECEA; }
    .ur-ta-error { color: #B5413A; font-size: 12.5px; margin-top: 6px; }

    .ur-ta-created { border: 2px solid #C9974D; background: linear-gradient(135deg, #FFFBF0, #fff); }
    .ur-ta-created__box { background: #FBF7EF; border: 1px dashed #D9C79B; border-radius: 12px; padding: 14px 16px; margin: 14px 0; }
    .ur-ta-created__row { display: flex; gap: 10px; padding: 4px 0; font-size: 14px; }
    .ur-ta-created__row span { width: 90px; color: #6B7570; font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; padding-top: 2px; }
    .ur-ta-created__row b { color: #123A2E; word-break: break-all; font-family: 'Courier New', monospace; font-size: 15px; }
    .ur-ta-created textarea { width: 100%; min-height: 150px; padding: 12px 14px; border: 1px solid #E7E2D6; border-radius: 12px; background: #fff; font-family: inherit; font-size: 13.5px; line-height: 1.6; color: #1C2321; resize: vertical; }
    .ur-ta-created__actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
    .ur-ta-warn { margin: 12px 0 0; font-size: 12.5px; color: #8A5A12; }

    .ur-ta-list { display: grid; gap: 12px; }
    .ur-ta-item { display: grid; grid-template-columns: minmax(0, 1.4fr) auto minmax(0, 1fr); gap: 14px; align-items: center; padding: 16px 18px; border: 1px solid #E7E2D6; border-radius: 14px; background: #fff; }
    .ur-ta-item__name { font-weight: 800; color: #123A2E; font-size: 15px; }
    .ur-ta-item__meta { font-size: 12.5px; color: #6B7570; margin-top: 3px; word-break: break-all; }
    .ur-ta-pill { display: inline-flex; align-items: center; gap: 7px; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; white-space: nowrap; }
    .ur-ta-pill--waiting { background: #EAF0FB; color: #2F4F8F; }
    .ur-ta-pill--active { background: #E3F4EA; color: #17734B; }
    .ur-ta-pill--expired { background: #F1EEE6; color: #6B7570; }
    .ur-ta-pill--revoked { background: #FDECEA; color: #A3281E; }
    .ur-ta-item__actions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
    .ur-ta-item__actions form { margin: 0; }
    .ur-ta-empty { text-align: center; padding: 34px 16px; color: #6B7570; font-size: 14px; border: 1px dashed #DDD5C2; border-radius: 14px; }
    @media (max-width: 900px) {
        .ur-ta-form { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .ur-ta-form .ur-ta-btn { grid-column: 1 / -1; width: 100%; }
        .ur-ta-item { grid-template-columns: minmax(0, 1fr); }
        .ur-ta-item__actions { justify-content: flex-start; }
    }
    @media (max-width: 560px) { .ur-ta-form { grid-template-columns: minmax(0, 1fr); } .ur-ta-mins span { min-width: 0; flex: 1; } .ur-ta-mins { display: flex; } .ur-ta-mins label { flex: 1; } }
</style>

<div class="ur-ta">
    <div class="ur-ta-eyebrow">Admin &middot; Team</div>
    <h1>Temporary Access</h1>
    <p class="ur-ta-sub">Give someone a short look at the team portal. They sign in with the email and password you send, and their 15 or 30 minutes start at their <b>first sign-in</b>. When time is up the account can never sign in again. They can only browse the shared proposals &mdash; no photos, no phone numbers or emails, no sharing and no editing.</p>

    @if(session('temp_created'))
        @php
            $c = session('temp_created');
            $msg = "Hello " . $c['name'] . ",\n\nHere is your temporary access to the team portal.\n\nLogin: " . $loginUrl . "\nEmail: " . $c['email'] . "\nPassword: " . $c['password'] . "\n\nYou have " . $c['minutes'] . " minutes, starting the moment you sign in. After that the access ends and cannot be used again. Please do not share these details.";
        @endphp
        <div class="ur-ta-card ur-ta-created">
            <h2><i class="fa fa-check-circle" style="color:#17734B;"></i> Access created for {{ $c['name'] }}</h2>
            <p class="hint" style="margin-bottom:0;">Send these details now. The password is shown only this once.</p>
            <div class="ur-ta-created__box">
                <div class="ur-ta-created__row"><span>Login</span><b>{{ $loginUrl }}</b></div>
                <div class="ur-ta-created__row"><span>Email</span><b>{{ $c['email'] }}</b></div>
                <div class="ur-ta-created__row"><span>Password</span><b>{{ $c['password'] }}</b></div>
                <div class="ur-ta-created__row"><span>Time</span><b style="font-family:inherit;">{{ $c['minutes'] }} minutes from the first sign-in</b></div>
            </div>
            <textarea id="ta_msg" readonly>{{ $msg }}</textarea>
            <div class="ur-ta-created__actions">
                <button type="button" class="ur-ta-btn" id="ta_copy"><i class="fa fa-copy"></i> <span>Copy message</span></button>
                <a class="ur-ta-btn ur-ta-btn--wa" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($msg) }}"><i class="fa fa-whatsapp"></i> Send on WhatsApp</a>
            </div>
            <p class="ur-ta-warn"><i class="fa fa-exclamation-triangle"></i> If you lose this message you cannot see the password again &mdash; create a new access instead.</p>
        </div>
    @endif

    <div class="ur-ta-card">
        <h2>Create temporary access</h2>
        <p class="hint">If they never sign in, the details stop working after 24 hours.</p>
        <form method="POST" action="{{ route('team.manage.temporary.store') }}" class="ur-ta-form" autocomplete="off">
            @csrf
            <div class="ur-ta-field">
                <label for="ta_name">Name</label>
                <input type="text" id="ta_name" name="name" value="{{ old('name') }}" maxlength="100" placeholder="e.g. Ali Raza" required>
                @error('name')<div class="ur-ta-error">{{ $message }}</div>@enderror
            </div>
            <div class="ur-ta-field">
                <label for="ta_email">Email</label>
                <input type="email" id="ta_email" name="email" value="{{ old('email') }}" maxlength="190" placeholder="name@example.com" required>
                @error('email')<div class="ur-ta-error">{{ $message }}</div>@enderror
            </div>
            <div class="ur-ta-field">
                <label>Time</label>
                <div class="ur-ta-mins">
                    <label><input type="radio" name="minutes" value="15" {{ old('minutes', '15') == '15' ? 'checked' : '' }}><span>15 min</span></label>
                    <label><input type="radio" name="minutes" value="30" {{ old('minutes') == '30' ? 'checked' : '' }}><span>30 min</span></label>
                </div>
            </div>
            <button type="submit" class="ur-ta-btn"><i class="fa fa-plus"></i> Create access</button>
        </form>
    </div>

    <div class="ur-ta-card">
        <h2>All temporary accounts</h2>
        <p class="hint">{{ $accounts->count() }} account{{ $accounts->count() === 1 ? '' : 's' }}</p>
        @if($accounts->isEmpty())
            <div class="ur-ta-empty"><i class="fa fa-clock-o" style="font-size:26px; color:#C9974D; display:block; margin-bottom:8px;"></i>No temporary access has been created yet.</div>
        @else
            <div class="ur-ta-list">
                @foreach($accounts as $a)
                    @php $state = $a->tempState(); $left = $a->tempSecondsLeft(); @endphp
                    <div class="ur-ta-item">
                        <div>
                            <div class="ur-ta-item__name">{{ trim($a->first_name . ' ' . $a->last_name) }}</div>
                            <div class="ur-ta-item__meta">{{ $a->email }} &middot; {{ $a->temp_minutes }} min &middot; created {{ $a->created_at->diffForHumans() }}</div>
                        </div>
                        <div>
                            @if($state === 'active')
                                <span class="ur-ta-pill ur-ta-pill--active"><i class="fa fa-circle" style="font-size:8px;"></i> Active &middot; <span class="js-ta-left" data-left="{{ $left }}">--:--</span> left</span>
                            @elseif($state === 'waiting')
                                <span class="ur-ta-pill ur-ta-pill--waiting"><i class="fa fa-hourglass-half"></i> Not signed in yet</span>
                            @elseif($state === 'revoked')
                                <span class="ur-ta-pill ur-ta-pill--revoked"><i class="fa fa-ban"></i> Ended by admin</span>
                            @else
                                <span class="ur-ta-pill ur-ta-pill--expired"><i class="fa fa-clock-o"></i> Expired</span>
                            @endif
                        </div>
                        <div class="ur-ta-item__actions">
                            @foreach([15, 30] as $m)
                            <form method="POST" action="{{ route('team.manage.temporary.extend', $a->dataid) }}">
                                @csrf
                                <input type="hidden" name="minutes" value="{{ $m }}">
                                <button type="submit" class="ur-ta-btn ur-ta-btn--light ur-ta-btn--sm" title="Give {{ $m }} more minutes starting now">+{{ $m }} min</button>
                            </form>
                            @endforeach
                            @if($state === 'active' || $state === 'waiting')
                            <form method="POST" action="{{ route('team.manage.temporary.revoke', $a->dataid) }}" class="js-ta-confirm" data-title="End this access now?" data-text="{{ trim($a->first_name . ' ' . $a->last_name) }} will be signed out and can never sign in again.">
                                @csrf
                                <button type="submit" class="ur-ta-btn ur-ta-btn--danger ur-ta-btn--sm"><i class="fa fa-ban"></i> End now</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('team.manage.temporary.destroy', $a->dataid) }}" class="js-ta-confirm" data-title="Delete this account?" data-text="It is removed completely.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ur-ta-btn ur-ta-btn--danger ur-ta-btn--sm" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        // copy the ready-to-send message
        var copy = document.getElementById('ta_copy'), box = document.getElementById('ta_msg');
        if (copy && box) {
            copy.addEventListener('click', function () {
                var done = function () { var s = copy.querySelector('span'); s.textContent = 'Copied!'; setTimeout(function () { s.textContent = 'Copy message'; }, 1800); };
                if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(box.value).then(done);
                else { box.select(); document.execCommand('copy'); done(); }
            });
        }
        // live "time left" on running accounts
        var timers = [].slice.call(document.querySelectorAll('.js-ta-left'));
        var now = Date.now();
        timers.forEach(function (el) { el._end = now + parseInt(el.getAttribute('data-left'), 10) * 1000; });
        function tick() {
            var reload = false;
            timers.forEach(function (el) {
                var left = Math.max(0, Math.round((el._end - Date.now()) / 1000)), m = Math.floor(left / 60), s = left % 60;
                el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                if (left === 0 && !el._done) { el._done = true; reload = true; }
            });
            if (reload) setTimeout(function () { location.reload(); }, 800);
        }
        if (timers.length) { tick(); setInterval(tick, 1000); }
        // confirm before ending / deleting
        document.querySelectorAll('form.js-ta-confirm').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                if (f.dataset.ok === '1') return;
                e.preventDefault();
                var go = function () { f.dataset.ok = '1'; f.submit(); };
                if (typeof swalConfirm === 'function') swalConfirm(f.dataset.title, f.dataset.text, go); else if (confirm(f.dataset.title + '\n' + f.dataset.text)) go();
            });
        });
    })();
</script>
@endsection
