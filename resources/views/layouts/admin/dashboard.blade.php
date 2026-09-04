<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.head-assets')
    <link rel="stylesheet" href="/css/ur-dashboard.css?2">
    <link rel="stylesheet" href="/css/ur-admin.css?v={{ filemtime(public_path('css/ur-admin.css')) }}">
    {{-- member.interestdata (the "View Interests" listing modal opened from admin/profiles)
         @push('styles')'s its own /css/ur-interests.css — but the admin modal only ever
         reuses that view's rendered main-content SECTION (see
         AdminController::showListingModal), never the full page around it, so that push
         never reaches this <head>. Load it directly here instead. --}}
    <link rel="stylesheet" href="/css/ur-interests.css?1">
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

        /* Guard against a Bootstrap modal ever rendering fully opaque instead of the
           standard translucent backdrop. */
        .modal-backdrop { background-color: #000; }
        .modal-backdrop.show { opacity: .5 !important; }

        .pace .pace-progress { background: #C9974D !important; }
        .pace .pace-activity { border-top-color: #C9974D !important; border-left-color: #C9974D !important; }
        .pace .pace-progress-inner { box-shadow: 0 0 10px #C9974D, 0 0 5px #C9974D !important; }
    </style>

    <div class="ur-dash-shell">
        @include('layouts.partials.admin-sidebar')

        <div class="ur-dash-main">
            @include('layouts.partials.admin-topbar')

            <div class="ur-dash-content">
                <div id="message_alert" class="ur-toast-stack"></div>
                <div id="main-content">
                    @yield('main-content')
                </div>
            </div>
        </div>
    </div>

    <div id="modal_dialog"></div>

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
    </script>

    <script type="text/javascript">
        /**
         * Every admin AJAX modal (profile listing, package add/edit, package
         * assignment) replaces #modal_dialog's innerHTML with a fresh
         * <div id="active_modal" class="modal">...</div> and then calls
         * .modal("toggle"). Because the element is destroyed and recreated
         * each time, Bootstrap's own backdrop cleanup on hide never runs for
         * the *previous* modal instance — its <div class="modal-backdrop">
         * (appended to <body>, not inside #modal_dialog) is orphaned. Opening
         * a second, third, ... modal stacks another 50%-opacity backdrop on
         * top of the leftover ones each time, so the overlay gets darker and
         * darker until it looks solid black.
         *
         * openAdminModal() clears any stray backdrops (and the body classes
         * Bootstrap adds while a modal is open) before injecting + showing
         * the new modal, so only ever one backdrop exists at a time.
         */
        function openAdminModal(html) {
            $('#active_modal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');

            $('#modal_dialog').html(html);
            $('#active_modal').modal('show');
        }
    </script>

    @include('layouts.partials.global-scripts')
    @include('layouts.partials.app-scripts')
</body>
</html>
