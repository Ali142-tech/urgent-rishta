<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.head-assets')
    <link rel="stylesheet" href="/css/ur-dashboard.css?v={{ filemtime(public_path('css/ur-dashboard.css')) }}">
    @stack('styles')
</head>

<body class="ur-dash-body pace-done">
    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <style>
        .ur-premium-badge { display: inline-flex; align-items: center; gap: 4px; vertical-align: middle; margin-left: 6px; padding: 2px 9px; border-radius: 999px; background: linear-gradient(135deg, #E9C27A, #C9974D); color: #3B2A0E !important; font-size: 10.5px; font-weight: 800; letter-spacing: .02em; line-height: 1.5; white-space: nowrap; text-transform: none; }
        .ur-premium-badge i { color: #7A4F0C !important; font-size: 10px; margin: 0 !important; }
        /* ---------- Toast notifications (#message_alert / showAlert()) ---------- */
        .ur-toast-stack {
            position: fixed; top: 90px; right: 20px; z-index: 99999;
            display: flex; flex-direction: column; gap: 12px;
            max-width: min(380px, calc(100vw - 40px)); pointer-events: none;
        }
        .ur-toast {
            position: relative; display: flex; align-items: flex-start; gap: 12px;
            background: #fff; border-radius: 12px; padding: 16px 40px 18px 16px;
            box-shadow: 0 16px 36px rgba(15,46,36,.18), 0 2px 8px rgba(15,46,36,.08);
            border-left: 4px solid #C9974D; overflow: hidden; opacity: 0;
            transform: translateX(24px); transition: opacity .3s ease, transform .3s ease;
            pointer-events: auto;
        }
        .ur-toast--show { opacity: 1; transform: translateX(0); }
        .ur-toast--hide { opacity: 0; transform: translateX(24px); }
        .ur-toast--success { border-left-color: #123A2E; }
        .ur-toast--success .ur-toast__icon { color: #123A2E; }
        .ur-toast--danger { border-left-color: #B5674A; }
        .ur-toast--danger .ur-toast__icon { color: #B5674A; }
        .ur-toast--warning { border-left-color: #C9974D; }
        .ur-toast--warning .ur-toast__icon { color: #C9974D; }
        .ur-toast--info { border-left-color: #5B6560; }
        .ur-toast--info .ur-toast__icon { color: #5B6560; }
        .ur-toast__icon { font-size: 20px; line-height: 1.4; flex-shrink: 0; }
        .ur-toast__body { font-family: 'Manrope', system-ui, sans-serif; font-size: 13.5px; line-height: 1.55; color: #1C2321; word-break: break-word; }
        .ur-toast__body div { margin-bottom: 4px; }
        .ur-toast__body div:last-child { margin-bottom: 0; }
        .ur-toast__close { position: absolute; top: 8px; right: 10px; border: none; background: transparent; font-size: 18px; line-height: 1; color: #9AA5A0; cursor: pointer; padding: 4px; }
        .ur-toast__close:hover { color: #1C2321; }
        .ur-toast__bar { position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: currentColor; color: #C9974D; opacity: .35; transform-origin: left; animation: urToastShrink linear forwards; }
        .ur-toast--success .ur-toast__bar { color: #123A2E; }
        .ur-toast--danger .ur-toast__bar { color: #B5674A; }
        .ur-toast--info .ur-toast__bar { color: #5B6560; }
        @keyframes urToastShrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }
        @media (max-width: 500px) { .ur-toast-stack { top: 70px; right: 10px; left: 10px; max-width: none; } }

        /* ---------- "Complete Client File" modal (openClientFile()) ---------- */
        .ur-cf-overlay { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(15,46,36,.55); align-items: flex-start; justify-content: center; padding: 40px 16px; overflow-y: auto; }
        .ur-cf-overlay.is-visible { display: flex; }
        .ur-cf-modal { background: #fff; border-radius: 18px; max-width: 980px; width: 100%; box-shadow: 0 30px 70px rgba(0,0,0,.3); }
        .ur-cf-loading { padding: 60px 20px; text-align: center; color: #6B7570; font-size: 14px; }

        .ur-cf-header { display: flex; align-items: flex-start; flex-wrap: wrap; gap: 10px 14px; padding: 22px 24px; border-bottom: 1px solid #E7E2D6; }
        .ur-cf-header__icon { width: 40px; height: 40px; border-radius: 10px; background: #F6F4EF; color: #123A2E; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .ur-cf-header__text { flex: 1; min-width: 0; }
        .ur-cf-header h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 19px; color: #123A2E; margin: 0; }
        .ur-cf-header p { font-size: 12.5px; color: #6B7570; margin: 2px 0 0; }
        .ur-cf-matches-pill { background: #F6F4EF; color: #6B7570; font-size: 11.5px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap; margin-top: 2px; }
        .ur-cf-close { width: 34px; height: 34px; border-radius: 8px; border: 1px solid #E7E2D6; background: #fff; color: #6B7570; cursor: pointer; flex-shrink: 0; }
        .ur-cf-close:hover { background: #F6F4EF; color: #1C2321; }
        @media (max-width: 560px) {
            .ur-cf-header { position: relative; flex-wrap: wrap; padding: 16px 52px 14px 16px; gap: 8px 12px; }
            .ur-cf-header__text { flex: 1 1 0; min-width: 0; }
            .ur-cf-header h2 { font-size: 17px; line-height: 1.25; }
            .ur-cf-header p { line-height: 1.4; }
            .ur-cf-close { position: absolute; top: 14px; right: 14px; }
            .ur-cf-matches-pill { order: 3; margin: 0 0 0 52px; }
        }

        {{-- align-items: start is load-bearing — grid's default "stretch"
             was matching the photo column's height to the (much taller)
             info column, and since the <img> inside fills its slot with
             object-fit: cover, that stretched a normal photo into a huge,
             heavily-zoomed one pushing everything else down the page. This
             row now only holds the photo + identity header — the tabs and
             their content live in .ur-cf-tabbed below, full modal width,
             instead of being squeezed into this row's right column (which
             left a tall empty gap once the photos ended, well before the
             much taller field grid did). --}}
        .ur-cf-top { display: grid; grid-template-columns: minmax(240px, 380px) 1fr; align-items: start; gap: 0; }
        .ur-cf-photos { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 22px 0 22px 24px; }
        .ur-cf-photo-slot { position: relative; height: 270px; overflow: hidden; background: #F3F0E8; border-radius: 14px; border: 1px solid #E7E2D6; }
        .ur-cf-photo-slot img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; position: absolute; inset: 0; }
        .ur-cf-photo-slot__footer { position: absolute; left: 8px; right: 8px; bottom: 8px; background: rgba(15,46,36,.82); color: #fff; font-size: 11px; font-weight: 700; line-height: 1.2 !important; padding: 7px 10px; text-align: center; border-radius: 9px; }
        .ur-cf-photo-slot--empty { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 16px 10px 44px; gap: 6px; border: 1.5px dashed #D8D2C2; background: #F7F5EF; color: #6B7570; }
        .ur-cf-photo-slot--empty > i { width: 44px; height: 44px; line-height: 44px; border-radius: 50%; background: #fff; color: #9AA5A0; font-size: 18px; box-shadow: 0 1px 3px rgba(15,46,36,.12); }
        .ur-cf-photo-slot__caption { font-size: 12.5px; font-weight: 700; color: #4B5651; line-height: 1.3 !important; }
        .ur-cf-photo-slot__id { font-size: 11px; color: #9AA5A0; line-height: 1.3 !important; }
        .ur-cf-info { padding: 24px 24px 22px; min-width: 0; }
        .ur-cf-verified { display: inline-flex; align-items: center; gap: 6px; background: #E7F3EC; color: #205C3F; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; margin-bottom: 10px; }
        .ur-cf-badges { margin-bottom: 8px; }
        .ur-cf-badge { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin: 0 6px 6px 0; }
        .ur-cf-badge--premium { background: #FBF0DA; color: #8A6218; }
        .ur-cf-badge--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
        .ur-cf-badge--abroad { background: #E4EEF7; color: #2C5F8A; }
        .ur-cf-badge--highlight { background: #FBE4E9; color: #A23B57; }
        .ur-cf-info h3 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; font-size: 21px; line-height: 1.25 !important; color: #123A2E; margin: 0 0 6px; }
        .ur-cf-summary { font-size: 13.5px; color: #1C2321; margin: 0 0 4px; }
        .ur-cf-owner { font-size: 12.5px; color: #6B7570; margin: 0 0 16px; }
        .ur-cf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 16px; }
        .ur-cf-stats div { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 10px 12px; }
        .ur-cf-stats span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; color: #9AA5A0; text-transform: uppercase; }
        .ur-cf-stats b { display: block; font-size: 13.5px; color: #1C2321; margin-top: 3px; }
        .ur-cf-notice { position: relative; background: #F6F4EF; border-radius: 12px; padding: 14px 40px 14px 14px; margin-bottom: 18px; }
        .ur-cf-notice p { margin: 0; font-size: 12.5px; color: #6B7570; line-height: 1.5; }
        .ur-cf-notice i { position: absolute; right: 14px; top: 14px; color: #2E7D5B; }

        .ur-cf-tabbed { padding: 18px 24px 22px; border-top: 1px solid #EFEBE0; }
        .ur-cf-tabs { display: flex; gap: 4px; background: #F6F4EF; border-radius: 999px; padding: 4px; margin-bottom: 16px; flex-wrap: wrap; width: fit-content; }
        .ur-cf-tabs button { border: none; background: transparent; padding: 8px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; color: #6B7570; cursor: pointer; white-space: nowrap; }
        .ur-cf-tabs button.is-active { background: #123A2E; color: #fff; }
        .ur-cf-panel { display: none; }
        .ur-cf-panel.is-active { display: block; }
        .ur-cf-fields-head { font-size: 15px; font-weight: 800; color: #123A2E; margin: 0 0 12px; line-height: 1.3 !important; }
        .ur-cf-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
        .ur-cf-grid div { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 12px 14px; min-width: 0; }
        .ur-cf-grid div span { display: block; font-size: 11.5px; font-weight: 500; letter-spacing: 0; text-transform: none; color: #6B7570; margin: 0 0 4px; line-height: 1.3 !important; }
        .ur-cf-grid div b { display: block; color: #1C2321; font-size: 14.5px; font-weight: 700; line-height: 1.35 !important; overflow-wrap: anywhere; }
        @media (max-width: 760px) { .ur-cf-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 440px) { .ur-cf-grid { grid-template-columns: 1fr; } }
        .ur-cf-description { font-size: 13px; color: #1C2321; line-height: 1.6; margin: 14px 0 0; }
        .ur-cf-raw-text-toolbar { display: flex; justify-content: flex-end; margin-bottom: 8px; }
        .ur-cf-raw-text { white-space: pre-wrap; font-family: inherit; font-size: 12.5px; color: #1C2321; background: #F6F4EF; border-radius: 10px; padding: 16px; max-height: 420px; overflow: auto; margin: 0; }
        .ur-cf-copy-btn { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 12px; border-radius: 999px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-size: 11.5px; font-weight: 700; cursor: pointer; }
        .ur-cf-copy-btn:hover { background: #F6F4EF; }
        .ur-cf-synthesized-note { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #8A6218; background: #FCF3E3; border-radius: 8px; padding: 10px 12px; margin: 0 0 12px; }
        .ur-cf-empty-note { color: #9AA5A0; font-size: 13px; }

        .ur-cf-footer { border-top: 1px solid #E7E2D6; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .ur-cf-footer p { font-size: 11.5px; color: #9AA5A0; margin: 0; flex: 1 1 220px; }
        .ur-cf-footer__actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .ur-cf-footer__actions button, .ur-cf-footer__actions a { display: inline-flex; align-items: center; gap: 6px; height: 38px; padding: 0 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; cursor: pointer; text-decoration: none; white-space: nowrap; }
        .ur-cf-footer__actions button:hover, .ur-cf-footer__actions a:hover { background: #F6F4EF; }
        .ur-cf-footer__actions .is-gold { background: #C9974D; border-color: #C9974D; color: #1C2321; }
        .ur-cf-footer__actions .is-gold:hover { background: #B07C3D; }
        .ur-cf-footer__actions .is-dark { background: #123A2E; border-color: #123A2E; color: #fff; }
        .ur-cf-footer__actions .is-dark:hover { background: #0F2E24; }
        .ur-cf-footer__actions button:disabled { opacity: .6; cursor: default; }

        @media (max-width: 767px) {
            .ur-cf-top { grid-template-columns: 1fr; }
            .ur-cf-photos { padding: 16px 16px 4px; }
            .ur-cf-photo-slot { height: 220px; }
            .ur-cf-tabbed { padding-left: 16px; padding-right: 16px; }
        }
    </style>

    <div class="ur-dash-shell">
        @include('layouts.partials.team-sidebar')

        <div class="ur-dash-main">
            @include('layouts.partials.team-topbar')

            <div class="ur-dash-content">
                <div id="message_alert" class="ur-toast-stack"></div>
                <div id="main-content">
                    @yield('main-content')
                </div>
            </div>
        </div>
    </div>

    {{-- "Complete Client File" modal shell — openClientFile(dataid) fetches
         team.proposals.file and injects it here. Global (not per-page) so
         every Team Dashboard page's "View Match"/"View file" can use it. --}}
    <div class="ur-cf-overlay" id="ur_client_file_overlay">
        <div class="ur-cf-modal" role="dialog" aria-modal="true" aria-label="Complete Client File">
            <div id="ur_client_file_body"></div>
        </div>
    </div>

    <script type="text/javascript">
        (function() {
            var sidebar = document.getElementById('ur_dash_sidebar');
            var backdrop = document.getElementById('ur_dash_backdrop');
            var toggler = document.getElementById('ur_dash_toggler');
            var closeBtn = document.getElementById('ur_dash_sidebar_close');

            function openSidebar() {
                sidebar.classList.add('is-open');
                backdrop.classList.add('is-visible');
            }
            function closeSidebar() {
                sidebar.classList.remove('is-open');
                backdrop.classList.remove('is-visible');
            }
            if (toggler) toggler.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        })();

        (function () {
            var overlay = document.getElementById('ur_client_file_overlay');
            var body = document.getElementById('ur_client_file_body');

            window.openClientFile = function (dataid) {
                body.innerHTML = '<div class="ur-cf-loading">Loading client file&hellip;</div>';
                overlay.classList.add('is-visible');
                document.body.style.overflow = 'hidden';
                fetch('/team/proposals/' + encodeURIComponent(dataid) + '/file', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        body.innerHTML = res.code === '200' ? res.html : ('<div class="ur-cf-loading">' + (res.message || 'Could not load this profile.') + '</div>');
                    })
                    .catch(function () {
                        body.innerHTML = '<div class="ur-cf-loading">Something went wrong loading this profile.</div>';
                    });
            };

            window.closeClientFile = function () {
                overlay.classList.remove('is-visible');
                document.body.style.overflow = '';
            };

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) window.closeClientFile();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') window.closeClientFile();
            });

            // Delegated (not bound inside the fetched fragment itself) —
            // a <script> tag injected via body.innerHTML never executes in
            // any browser, so team/partials/proposal-file-modal.blade.php's
            // tab-switching/copy JS silently did nothing. Binding once here,
            // on the stable #ur_client_file_body container, works no matter
            // how many times its content gets replaced.
            // Status and Visibility (Public / Private) dropdowns in the file window: saved as soon as they change.
            body.addEventListener('change', function (e) {
                var sel = e.target.closest('select.js-profile-status, select.js-proposal-privacy');
                if (!sel) return;
                var previous = sel.dataset.current || '';
                if (sel.classList.contains('js-proposal-privacy') && typeof swal === 'function') {
                    // Making a proposal private / public is an admin decision with wide effect: confirm it first.
                    var toPrivate = sel.value === '1';
                    swal({
                        title: toPrivate ? 'Make this proposal private?' : 'Make this proposal public?',
                        text: toPrivate
                            ? 'Only admins, the team member who added it and members you allowed will be able to see it. Everyone else will no longer see it anywhere, including AI matches and share links.'
                            : 'Every team member will be able to see this proposal again.',
                        icon: 'warning',
                        buttons: { cancel: 'Cancel', confirm: toPrivate ? 'Yes, make private' : 'Yes, make public' }
                    }).then(function (ok) {
                        if (ok) { urSaveSelect(sel, previous); } else { sel.value = previous; }
                    });
                    return;
                }
                urSaveSelect(sel, previous);
            });

            function urSaveSelect(sel, previous) {
                var form = new FormData();
                form.append('_token', '{{ csrf_token() }}');
                form.append(sel.classList.contains('js-proposal-privacy') ? 'private' : 'profile_status', sel.value);
                sel.disabled = true;
                fetch(sel.dataset.url, { method: 'POST', body: form, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        sel.disabled = false;
                        if (String(res.code) === '200') {
                            sel.dataset.current = sel.value;
                            if (sel.classList.contains('js-proposal-privacy')) {
                                var pill = body.querySelector('.ur-cf-private-pill');
                                if (pill) pill.style.display = sel.value === '1' ? '' : 'none';
                            }
                            if (typeof showAlert === 'function') showAlert('success', res.message || 'Saved.', 3000);
                        } else {
                            sel.value = previous;
                            if (typeof showAlert === 'function') showAlert('danger', res.message || 'Could not save.', 5000);
                        }
                    })
                    .catch(function () { sel.disabled = false; sel.value = previous; if (typeof showAlert === 'function') showAlert('danger', 'Something went wrong. Please try again.', 5000); });
            }

            body.addEventListener('click', function (e) {
                var tabBtn = e.target.closest('.ur-cf-tabs button[data-tab]');
                if (tabBtn) {
                    body.querySelectorAll('.ur-cf-tabs button').forEach(function (b) { b.classList.toggle('is-active', b === tabBtn); });
                    body.querySelectorAll('.ur-cf-panel').forEach(function (p) { p.classList.toggle('is-active', p.dataset.panel === tabBtn.dataset.tab); });
                    return;
                }

                var copyBtn = e.target.closest('#cf_copy_btn');
                if (copyBtn) {
                    var text = copyBtn.dataset.text || '';
                    var done = function () {
                        var original = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fa fa-check"></i> Copied';
                        setTimeout(function () { copyBtn.innerHTML = original; }, 1800);
                    };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(done);
                    } else {
                        var raw = document.getElementById('cf_raw_text');
                        if (raw) {
                            var range = document.createRange();
                            range.selectNodeContents(raw);
                            var sel = window.getSelection();
                            sel.removeAllRanges();
                            sel.addRange(range);
                            document.execCommand('copy');
                            sel.removeAllRanges();
                        }
                        done();
                    }
                    return;
                }

                var shareBtn = e.target.closest('#cf_share_photos_btn');
                if (shareBtn) {
                    shareTeamProposalPhotos(shareBtn);
                }
            });
        })();

        // "Form + N photos" (Complete Client File modal) — tries the native
        // OS/browser share sheet with the real photo FILES attached (the
        // same "attach to any app" picker Windows/Android/iOS show for a
        // native Share action), so WhatsApp/Telegram/etc. get actual
        // images, not just a link. wa.me links can't attach files at all,
        // so this only works where navigator.share() with files is
        // supported (most mobile browsers, Edge/Chrome on Windows) — every
        // other browser falls back to the existing WhatsApp text+link
        // redirect, same as before.
        // Step two of a long share: the photos are already in WhatsApp, now offer
        // the client form text as its own share (needs a fresh tap, so a bar).
        // Step two of a photo share: the photos are already in WhatsApp, now offer
        // the client form (the pasted original) as its own share. The pending form is
        // also kept in sessionStorage because, after WhatsApp opens, a phone with
        // little memory often reloads this tab on return and a plain in-page bar
        // would be lost. The text is copied to the clipboard as well, so it can
        // simply be pasted into the chat.
        var UR_PENDING_KEY = 'ur_pending_form_share';
        function rememberPendingFormShare(p) {
            try { sessionStorage.setItem(UR_PENDING_KEY, JSON.stringify({ title: p.title || '', text: p.text || '', fallback: p.fallback || '', ts: Date.now() })); } catch (e) {}
        }
        function clearPendingFormShare() {
            try { sessionStorage.removeItem(UR_PENDING_KEY); } catch (e) {}
        }
        function copyFormText(text) {
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(text).catch(function () {});
            } catch (e) {}
        }
        function offerFormTextShare(p) {
            var old = document.getElementById('ur_share_text_bar');
            if (old) old.remove();
            var bar = document.createElement('div');
            bar.id = 'ur_share_text_bar';
            bar.style.cssText = 'position:fixed;left:12px;right:12px;bottom:14px;z-index:100001;background:#123A2E;color:#fff;border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;box-shadow:0 12px 32px rgba(0,0,0,.35);font-size:13px;';
            bar.innerHTML = '<span style="flex:1;line-height:1.35;"><b>Photos shared.</b> Now send the client form (' + (p.title || 'profile') + ') &mdash; it is also copied, so you can paste it in the chat.</span>'
                + '<button type="button" id="ur_share_text_go" style="background:#25D366;color:#fff;border:0;border-radius:10px;padding:9px 14px;font-weight:700;">Send form</button>'
                + '<button type="button" id="ur_share_text_x" aria-label="Dismiss" style="background:transparent;color:#fff;border:0;font-size:20px;line-height:1;">&times;</button>';
            document.body.appendChild(bar);
            document.getElementById('ur_share_text_x').onclick = function () { bar.remove(); clearPendingFormShare(); };
            document.getElementById('ur_share_text_go').onclick = function () {
                var payload = { title: p.title || '', text: p.text || '' };
                bar.remove();
                clearPendingFormShare();
                if (navigator.share && (!navigator.canShare || navigator.canShare(payload))) {
                    navigator.share(payload).catch(function (err) {
                        if (!err || err.name !== 'AbortError') window.open(p.fallback, '_blank');
                    });
                } else {
                    window.open(p.fallback, '_blank');
                }
            };
        }
        // Back from WhatsApp after a reload? Put the "Send form" bar back (15 min window).
        document.addEventListener('DOMContentLoaded', function () {
            try {
                var raw = sessionStorage.getItem(UR_PENDING_KEY);
                if (!raw) return;
                var p = JSON.parse(raw);
                if (!p || !p.text || Date.now() - (p.ts || 0) > 15 * 60 * 1000) { clearPendingFormShare(); return; }
                offerFormTextShare(p);
            } catch (e) {}
        });
        // Does this device need photos and form sent as two shares?
        // Default: no (single share). Yes for Samsung Galaxy A0x (Chrome hides the
        // model in the user-agent, so ask userAgentData). Per-browser override for
        // support: localStorage.ur_share_two_step = '1' (force) or '0' (never).
        function shareNeedsTwoStep() {
            try {
                var o = localStorage.getItem('ur_share_two_step');
                if (o === '1') return Promise.resolve(true);
                if (o === '0') return Promise.resolve(false);
            } catch (e) {}
            var A0X = /SM-A0\d{2}/i;
            if (A0X.test(navigator.userAgent || '')) return Promise.resolve(true);
            if (navigator.userAgentData && navigator.userAgentData.getHighEntropyValues) {
                return navigator.userAgentData.getHighEntropyValues(['model'])
                    .then(function (v) { return A0X.test((v && v.model) || ''); })
                    .catch(function () { return false; });
            }
            return Promise.resolve(false);
        }
        // WhatsApp shows "N items could not be sent" for originals that are too
        // large or whose real format doesn't match the extension. Re-draw every
        // photo into a plain JPEG (longest side <= 1600px) so what we hand to
        // the share sheet is always a valid, small image. If the browser can't
        // decode it, send the original bytes with a type taken from the response.
        function shareNormalizeImage(blob, i, ext) {
            var MAX = 1600;
            function asRaw() {
                // Not an image at all (e.g. an HTML error/login page served with 200)? Don't
                // dress it up as a .jpg — fail so the caller falls back to the text share.
                if (!blob.type || blob.type.indexOf('image/') !== 0) throw new Error('not an image: ' + blob.type);
                var type = blob.type;
                var outExt = type === 'image/png' ? 'png' : (type === 'image/webp' ? 'webp' : (type === 'image/gif' ? 'gif' : 'jpg'));
                return new File([blob], 'photo-' + (i + 1) + '.' + outExt, { type: type });
            }
            if (!window.createImageBitmap) return new Promise(function (resolve) { resolve(asRaw()); });
            return createImageBitmap(blob).then(function (bmp) {
                var scale = Math.min(1, MAX / Math.max(bmp.width, bmp.height));
                var w = Math.max(1, Math.round(bmp.width * scale));
                var h = Math.max(1, Math.round(bmp.height * scale));
                var canvas = document.createElement('canvas');
                canvas.width = w; canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';            // JPEG has no alpha
                ctx.fillRect(0, 0, w, h);
                ctx.drawImage(bmp, 0, 0, w, h);
                if (bmp.close) bmp.close();
                return new Promise(function (resolve) {
                    canvas.toBlob(function (out) {
                        resolve(out ? new File([out], 'photo-' + (i + 1) + '.jpg', { type: 'image/jpeg' }) : asRaw());
                    }, 'image/jpeg', 0.88);
                });
            }).catch(function () { return asRaw(); });
        }
        // Share button on search/Team Proposals cards: fetch the same "form +
        // photos" payload as the Complete Client File modal and open the
        // native share sheet with the real image FILES attached. Browsers
        // without file sharing (or a profile with no photos) fall back to the
        // card link's normal WhatsApp text share.
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a.js-share-proposal');
            if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) return;
            e.preventDefault();
            if (a.dataset.busy === '1') return;
            a.dataset.busy = '1';
            var original = a.innerHTML;
            a.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Preparing…';

            function done() { a.dataset.busy = ''; a.innerHTML = original; }
            function openLink(url) {
                var w = window.open(url, '_blank');
                if (!w) window.location.href = url;   // popup blocked after the async work
            }
            var MIME_BY_EXT = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', webp: 'image/webp', gif: 'image/gif' };
            function extFromUrl(url) {
                var m = url.split(/[?#]/)[0].match(/\.([a-zA-Z0-9]+)$/);
                return m ? m[1].toLowerCase() : 'jpg';
            }

            fetch(a.dataset.payloadUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error('payload ' + r.status); return r.json(); })
                .then(function (p) {
                    // Someone else's proposal: open the owner's own chat (the sharing sheet can't pick a contact).
                    if (p.direct || !p.photos || !p.photos.length || !navigator.share || !navigator.canShare) {
                        openLink(p.fallback || a.href);
                        return;
                    }
                    return Promise.all(p.photos.map(function (url, i) {
                        return fetch(url).then(function (r) {
                            if (!r.ok) throw new Error('photo ' + r.status);
                            return r.blob();
                        }).then(function (blob) {
                            return shareNormalizeImage(blob, i, extFromUrl(url));
                        });
                    })).then(function (files) {
                        // ONE share by default: photos + client form together (title + text).
                        // The only exception is a phone known to make WhatsApp reject
                        // "photos + text" in a single share (Samsung Galaxy A0x, e.g. A07:
                        // "N items couldn't be sent"); there the photos go first and the
                        // form follows via the bar. See shareNeedsTwoStep().
                        return shareNeedsTwoStep().then(function (deviceTwoStep) {
                            var twoStep = deviceTwoStep || !!p.two_step;   // p.two_step: long payloads (e.g. both forms)
                            var data = twoStep ? { files: files } : { files: files, title: p.title || '', text: p.text || '' };
                            if (!navigator.canShare(data)) { openLink(p.fallback || a.href); return; }
                            if (twoStep) { rememberPendingFormShare(p); copyFormText(p.text || ''); }
                            return navigator.share(data).then(function () {
                                if (twoStep) offerFormTextShare(p);
                            }).catch(function (err) {
                                if (err && err.name === 'AbortError') { clearPendingFormShare(); return; }          // user closed the sheet
                                if (typeof showAlert === 'function') showAlert('danger', 'Could not share the photos (' + (err && err.name || 'error') + '). Opening WhatsApp with the text instead.', 5000);
                                openLink(p.fallback || a.href);
                            });
                        });
                    }).catch(function (err) {
                        if (typeof showAlert === 'function') showAlert('danger', 'Could not prepare the photos: ' + (err && err.message || 'unknown error'), 5000);
                        openLink(p.fallback || a.href);
                    });
                })
                .catch(function () { openLink(a.href); })
                .finally(done);
        });
        function shareTeamProposalPhotos(btn) {
            var label = document.getElementById('cf_share_photos_label');
            var originalLabel = label ? label.textContent : '';
            var fallback = btn.dataset.fallback;
            var photoUrls = [];
            try { photoUrls = JSON.parse(btn.dataset.photos || '[]'); } catch (e) { photoUrls = []; }

            function goFallback() {
                window.open(fallback, '_blank');
            }

            if (!navigator.share || !navigator.canShare || !photoUrls.length) {
                goFallback();
                return;
            }

            btn.disabled = true;
            if (label) label.textContent = 'Preparing photos…';

            // Naming the File from the response's own Content-Type (blob.type)
            // was the actual bug — a server that sends back a generic/missing
            // type (common for these thumbnail paths on shared hosting) built
            // a filename like "photo-1.octet-stream", which WhatsApp then
            // rejected as a corrupt/unrecognized file. These URLs already end
            // in a real image extension (.jpg/.png/etc — see
            // Profile::getCardImages()), so derive both the filename AND the
            // File's MIME type from THAT instead of trusting the response
            // header. Also reject a non-OK response outright rather than
            // packaging a fetched error page as if it were a photo.
            var MIME_BY_EXT = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', webp: 'image/webp', gif: 'image/gif' };
            function extFromUrl(url) {
                var match = url.split(/[?#]/)[0].match(/\.([a-zA-Z0-9]+)$/);
                return match ? match[1].toLowerCase() : 'jpg';
            }

            Promise.all(photoUrls.map(function (url, i) {
                var ext = extFromUrl(url);
                var mime = MIME_BY_EXT[ext] || 'image/jpeg';
                return fetch(url).then(function (r) {
                    if (!r.ok) throw new Error('Photo fetch failed: ' + r.status);
                    return r.blob();
                }).then(function (blob) {
                    return new File([blob], 'photo-' + (i + 1) + '.' + ext, { type: mime });
                });
            })).then(function (files) {
                var shareData = { files: files, title: btn.dataset.title || '', text: btn.dataset.text || '' };
                if (!navigator.canShare(shareData)) {
                    goFallback();
                    return;
                }
                return navigator.share(shareData).catch(function (err) {
                    // AbortError = the user closed the share sheet themselves —
                    // not a failure, don't fall back to WhatsApp behind their back.
                    if (err && err.name !== 'AbortError') goFallback();
                });
            }).catch(function () {
                goFallback();
            }).finally(function () {
                btn.disabled = false;
                if (label) label.textContent = originalLabel;
            });
        }
    </script>

    <form id="delete-proposal-form" method="POST" style="display:none;">@csrf @method('DELETE')</form>
    <script>
        function deleteProposal(ref) {
            swalConfirm('Delete proposal ' + ref + '?', 'This removes the proposal from every list. This cannot be undone from the dashboard.', function () {
                var f = document.getElementById('delete-proposal-form');
                f.action = '{{ url('team/proposals') }}/' + ref;
                f.submit();
            });
            return false;
        }
    </script>
    @include('layouts.partials.global-scripts')
    @include('layouts.partials.app-scripts')
</body>
</html>
