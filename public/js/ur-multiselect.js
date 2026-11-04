/* Turns every <select multiple class="ur-multi"> into the searchable multi-select (see ur-multiselect.css).
   Options on the select (data-*): data-placeholder (default "Any"), data-dropdown-parent (a selector — needed inside a Bootstrap modal).
   window.URMulti.init(root) can be called again after a list is rebuilt (e.g. cities after the country changes). */
(function (w, $) {
    if (!$ || !$.fn || !$.fn.select2) return;

    function badge($el) {
        var n = ($el.val() || []).length;
        var box = $el.next('.select2-container').find('.select2-selection--multiple');
        if (n > 1) box.attr('data-extra', '+' + (n - 1)); else box.removeAttr('data-extra');
    }

    function init(root) {
        $(root || document).find('select.ur-multi').each(function () {
            var $el = $(this);
            if ($el.data('urMultiReady')) { $el.trigger('change.select2'); badge($el); return; }
            var opts = {
                width: '100%',
                placeholder: $el.data('placeholder') || 'Any',
                closeOnSelect: false
            };
            var parent = $el.data('dropdownParent');
            if (parent) opts.dropdownParent = $(parent);
            $el.select2(opts);
            // select2 4.0 has no containerCssClass option: tag the closed box and the open menu ourselves (the CSS hangs on these classes)
            var inst = $el.data('select2');
            if (inst) { inst.$container.addClass('ur-ms'); inst.$dropdown.addClass('ur-ms-drop'); }
            $el.data('urMultiReady', true);
            $el.on('change select2:select select2:unselect select2:clear', function () { badge($el); });
            badge($el);
        });
    }

    /** Clears a multi-select (and tells select2 / any change listener). */
    function clear($el) { $el.val(null).trigger('change'); }

    w.URMulti = { init: init, badge: badge, clear: clear };
    $(function () { init(document); });
})(window, window.jQuery);
