/**
 * Urgent Rishta — reusable navbar behavior
 * Layout is rendered correctly in HTML; JS only marks the active link.
 */
(function ($) {
    'use strict';

    function markActiveNav() {
        var path = window.location.pathname.replace(/\/+$/, '') || '/';
        $('#myHeader #navbar_main .nav-link').each(function () {
            var href = ($(this).attr('href') || '').replace(/\/+$/, '') || '/';
            var active = href === path || (path === '/home' && href === '/');
            $(this).toggleClass('is-active', active);
        });
    }

    $(markActiveNav);
})(jQuery);
