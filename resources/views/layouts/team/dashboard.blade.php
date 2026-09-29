<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.head-assets')
    <link rel="stylesheet" href="/css/ur-dashboard.css?2">
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
        .ur-cf-top { display: grid; grid-template-columns: minmax(220px, 330px) 1fr; align-items: start; gap: 0; }
        .ur-cf-photos { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #E7E2D6; }
        .ur-cf-photo-slot { position: relative; height: 320px; overflow: hidden; background: #F6F4EF; }
        .ur-cf-photo-slot img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
        .ur-cf-photo-slot__footer { position: absolute; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,.65); color: #fff; font-size: 11px; font-weight: 700; padding: 8px 10px; text-align: center; }
        .ur-cf-photo-slot--empty { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 16px; color: #fff; gap: 4px; }
        .ur-cf-photo-slot--a { background: linear-gradient(160deg, #17493A, #0F2E24); }
        .ur-cf-photo-slot--b { background: linear-gradient(160deg, #C9974D, #A97B36); }
        .ur-cf-photo-slot__letter { font-family: 'Playfair Display', serif; font-size: 46px; font-weight: 700; opacity: .9; }
        .ur-cf-photo-slot__id { font-weight: 700; font-size: 14px; margin-top: 6px; }
        .ur-cf-photo-slot__caption { font-size: 12px; opacity: .85; margin-top: 2px; }
        .ur-cf-photo-slot__tags { font-size: 10.5px; opacity: .75; margin-top: 10px; }

        .ur-cf-info { padding: 22px 24px; min-width: 0; }
        .ur-cf-verified { display: inline-flex; align-items: center; gap: 6px; background: #E7F3EC; color: #205C3F; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; margin-bottom: 10px; }
        .ur-cf-badges { margin-bottom: 8px; }
        .ur-cf-badge { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin: 0 6px 6px 0; }
        .ur-cf-badge--premium { background: #FBF0DA; color: #8A6218; }
        .ur-cf-badge--royal { background: linear-gradient(135deg, #9B59B6, #5B2C6F); color: #fff; }
        .ur-cf-badge--abroad { background: #E4EEF7; color: #2C5F8A; }
        .ur-cf-badge--highlight { background: #FBE4E9; color: #A23B57; }
        .ur-cf-info h3 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 20px; color: #123A2E; margin: 0 0 4px; }
        .ur-cf-summary { font-size: 13.5px; color: #1C2321; margin: 0 0 4px; }
        .ur-cf-owner { font-size: 12.5px; color: #6B7570; margin: 0 0 16px; }
        .ur-cf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 16px; }
        .ur-cf-stats div { background: #F6F4EF; border-radius: 10px; padding: 10px 12px; }
        .ur-cf-stats span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; color: #9AA5A0; text-transform: uppercase; }
        .ur-cf-stats b { display: block; font-size: 13.5px; color: #1C2321; margin-top: 3px; }
        .ur-cf-notice { position: relative; background: #F6F4EF; border-radius: 12px; padding: 14px 40px 14px 14px; margin-bottom: 18px; }
        .ur-cf-notice p { margin: 0; font-size: 12.5px; color: #6B7570; line-height: 1.5; }
        .ur-cf-notice i { position: absolute; right: 14px; top: 14px; color: #2E7D5B; }

        .ur-cf-tabbed { padding: 4px 24px 22px; }
        .ur-cf-tabs { display: flex; gap: 4px; background: #F6F4EF; border-radius: 999px; padding: 4px; margin-bottom: 16px; flex-wrap: wrap; width: fit-content; }
        .ur-cf-tabs button { border: none; background: transparent; padding: 8px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; color: #6B7570; cursor: pointer; white-space: nowrap; }
        .ur-cf-tabs button.is-active { background: #123A2E; color: #fff; }
        .ur-cf-panel { display: none; }
        .ur-cf-panel.is-active { display: block; }
        .ur-cf-fields-head { font-size: 13.5px; font-weight: 700; color: #123A2E; margin-bottom: 12px; }
        .ur-cf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
        .ur-cf-grid div { background: #F6F4EF; border-radius: 10px; padding: 10px 12px; }
        .ur-cf-grid div span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #9AA5A0; margin-bottom: 2px; }
        .ur-cf-grid div b { color: #1C2321; font-size: 13px; font-weight: 600; }
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
        .ur-cf-footer__actions a.is-gold { background: #C9974D; border-color: #C9974D; color: #1C2321; }
        .ur-cf-footer__actions a.is-gold:hover { background: #B07C3D; }
        .ur-cf-footer__actions a.is-dark { background: #123A2E; border-color: #123A2E; color: #fff; }
        .ur-cf-footer__actions a.is-dark:hover { background: #0F2E24; }

        @media (max-width: 767px) {
            .ur-cf-top { grid-template-columns: 1fr; }
            .ur-cf-photo-slot { height: 200px; }
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
                }
            });
        })();
    </script>

    @include('layouts.partials.global-scripts')
    @include('layouts.partials.app-scripts')
</body>
</html>
