/*
 * Searchable dropdowns that work with a finger.
 *
 * Turns a plain <select> with a long list (country, caste, education …) into a button that opens a sheet with a search box:
 * a bottom sheet on a phone, a centred panel on a computer. The original <select> stays in the page — hidden, with its name,
 * value, `required` and change events untouched — so existing forms and scripts keep working exactly as before, including
 * lists that are reloaded by script (state, city, caste).
 *
 *   URSearchableSelect.init(document, { minOptions: 12 });
 *   <select data-no-search>   is skipped;   <select data-search>   is always enhanced.
 */
(function () {
    'use strict';
    if (window.URSearchableSelect) return;

    var registry = [];
    var openState = null;

    var CSS = '' +
        '.ur-ss-wrap{position:relative;display:block;width:100%}' +
        '.ur-ss-native{position:absolute!important;left:0;top:0;width:100%!important;height:100%!important;opacity:0!important;pointer-events:none!important;margin:0!important}' +
        '.ur-ss__btn{display:block;width:100%;text-align:left;cursor:pointer;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;-webkit-appearance:none;appearance:none;margin:0}' +
        '.ur-ss__btn[disabled]{opacity:.6;cursor:not-allowed}' +
        '.ur-ss__btn:focus{outline:0}' +
        '.ur-ss__btn:focus-visible{outline:2px solid #123A2E;outline-offset:2px}' +
        '.ur-ss-modal{position:fixed;inset:0;z-index:2147483000;background:rgba(10,24,19,.5);display:flex;align-items:flex-end;justify-content:center;font-family:inherit;-webkit-tap-highlight-color:transparent}' +
        '.ur-ss-panel{background:#fff;width:100%;max-width:480px;max-height:80vh;max-height:80dvh;border-radius:20px 20px 0 0;display:flex;flex-direction:column;box-shadow:0 -14px 40px rgba(0,0,0,.25);animation:ur-ss-up .22s ease;overflow:hidden}' +
        '@keyframes ur-ss-up{from{transform:translateY(24px);opacity:.4}to{transform:none;opacity:1}}' +
        '.ur-ss-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:16px 18px 6px}' +
        '.ur-ss-title{font-size:16px;font-weight:800;color:#123A2E;line-height:1.3}' +
        '.ur-ss-x{border:0;background:#F1EEE5;color:#123A2E;width:34px;height:34px;border-radius:50%;font-size:20px;line-height:1;cursor:pointer;flex:none}' +
        '.ur-ss-searchrow{padding:8px 16px 10px;position:relative}' +
        '.ur-ss-search{width:100%;height:46px;border:1px solid #DDD3BE;border-radius:12px;padding:0 14px 0 40px;font-size:16px;font-family:inherit;color:#1C2321;background:#fff;box-sizing:border-box;-webkit-appearance:none}' +
        '.ur-ss-search:focus{outline:0;border-color:#123A2E;box-shadow:0 0 0 3px rgba(18,58,46,.12)}' +
        '.ur-ss-searchrow svg{position:absolute;left:30px;top:50%;transform:translateY(-50%);width:16px;height:16px;fill:none;stroke:#8A938E;stroke-width:2;pointer-events:none}' +
        '.ur-ss-list{list-style:none;margin:0;padding:2px 8px 14px;overflow-y:auto;-webkit-overflow-scrolling:touch;overscroll-behavior:contain;flex:1}' +
        '.ur-ss-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 12px;border-radius:12px;font-size:15.5px;color:#1C2321;cursor:pointer}' +
        '.ur-ss-item:hover,.ur-ss-item.is-active{background:#F2F7F4}' +
        '.ur-ss-item.is-selected{background:#E3F1EA;color:#0F5B43;font-weight:800}' +
        '.ur-ss-item.is-selected::after{content:"\\2713";font-weight:800;color:#1C9A6C}' +
        '.ur-ss-empty{padding:26px 16px;text-align:center;color:#8A938E;font-size:14px}' +
        '.ur-ss-count{padding:0 22px 8px;font-size:12px;color:#8A938E}' +
        '@media (min-width:640px){.ur-ss-modal{align-items:center}.ur-ss-panel{border-radius:20px;max-height:70vh}}';

    function injectCss() {
        if (document.getElementById('ur-ss-css')) return;
        var s = document.createElement('style');
        s.id = 'ur-ss-css';
        s.textContent = CSS;
        document.head.appendChild(s);
    }

    function selectedText(sel) {
        var o = sel.options[sel.selectedIndex];
        return o ? o.textContent.trim() : '';
    }
    function hasValue(sel) { return sel.value !== '' && sel.selectedIndex > -1; }

    function titleFor(sel) {
        var field = sel.closest('.reg-field-outline, .field, .form-group, .reg-field');
        var t = '';
        if (field) {
            var f = field.querySelector('.float-label, label');
            if (f) t = f.textContent.replace(/\*|Required|Optional/g, '').trim();
        }
        if (!t && sel.id) {
            var l = document.querySelector('label[for="' + sel.id + '"]');
            if (l) t = l.textContent.replace(/\*/g, '').trim();
        }
        return t || (sel.options[0] ? sel.options[0].textContent.trim() : 'Select');
    }

    /** Copies how the select looks onto the button, once the select is actually on screen (a hidden step has no size yet). */
    function syncStyle(item) {
        var sel = item.select, btn = item.btn;
        if (!sel.getClientRects().length) return;
        var cs = window.getComputedStyle(sel);
        ['fontFamily', 'fontSize', 'fontWeight', 'color', 'lineHeight', 'letterSpacing',
         'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
         'borderTopWidth', 'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth',
         'borderTopStyle', 'borderRightStyle', 'borderBottomStyle', 'borderLeftStyle',
         'borderTopColor', 'borderRightColor', 'borderBottomColor', 'borderLeftColor',
         'borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomLeftRadius', 'borderBottomRightRadius',
         'backgroundColor', 'backgroundImage', 'backgroundRepeat', 'backgroundPosition', 'backgroundSize', 'boxShadow'
        ].forEach(function (p) { btn.style[p] = cs[p]; });
        btn.style.boxSizing = cs.boxSizing;
        btn.style.height = sel.offsetHeight ? sel.offsetHeight + 'px' : cs.height;
        item.synced = true;
    }

    function refresh(item) {
        var sel = item.select, btn = item.btn;
        var text = selectedText(sel);
        if (btn.textContent !== text) btn.textContent = text;
        if (btn.disabled !== sel.disabled) btn.disabled = sel.disabled;
        item.sig = sel.value + '|' + sel.options.length + '|' + sel.disabled;
        if (!item.synced) syncStyle(item);
    }

    function enhance(sel) {
        if (sel.dataset.urss === '1' || sel.multiple) return;
        sel.dataset.urss = '1';
        var wrap = document.createElement('div');
        wrap.className = 'ur-ss-wrap';
        sel.parentNode.insertBefore(wrap, sel);
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ur-ss__btn';
        btn.setAttribute('aria-haspopup', 'listbox');
        wrap.appendChild(btn);
        wrap.appendChild(sel);
        sel.classList.add('ur-ss-native');
        sel.tabIndex = -1;
        var item = { select: sel, btn: btn, synced: false, sig: '' };
        registry.push(item);
        btn.addEventListener('click', function () { if (!sel.disabled) open(item); });
        // the native select can still be focused by form validation (required): send the focus to the button
        sel.addEventListener('focus', function () { btn.focus(); });
        refresh(item);
    }

    function listOf(sel) {
        var out = [];
        Array.prototype.forEach.call(sel.options, function (o, i) {
            if (o.value === '' || o.disabled) return;
            out.push({ value: o.value, text: o.textContent.trim(), index: i, pinned: o.hasAttribute('data-pinned') });
        });
        return out;
    }

    function close() {
        if (!openState) return;
        document.removeEventListener('keydown', openState.onKey, true);
        if (window.visualViewport) window.visualViewport.removeEventListener('resize', openState.onResize);
        openState.modal.remove();
        document.body.style.overflow = openState.prevOverflow;
        var btn = openState.item.btn;
        openState = null;
        try { btn.focus({ preventScroll: true }); } catch (e) { btn.focus(); }
    }

    function open(item) {
        close();
        var sel = item.select, all = listOf(sel), current = sel.value, shown = all.slice(), active = -1;

        var modal = document.createElement('div');
        modal.className = 'ur-ss-modal';
        modal.innerHTML =
            '<div class="ur-ss-panel" role="dialog" aria-modal="true">' +
            '<div class="ur-ss-head"><div class="ur-ss-title"></div><button type="button" class="ur-ss-x" aria-label="Close">&times;</button></div>' +
            '<div class="ur-ss-searchrow"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>' +
            '<input type="search" class="ur-ss-search" placeholder="Type to search" autocomplete="off" autocapitalize="off" spellcheck="false"></div>' +
            '<div class="ur-ss-count"></div><ul class="ur-ss-list" role="listbox"></ul></div>';
        document.body.appendChild(modal);

        var panel = modal.querySelector('.ur-ss-panel'), list = modal.querySelector('.ur-ss-list'), input = modal.querySelector('.ur-ss-search'), count = modal.querySelector('.ur-ss-count');
        modal.querySelector('.ur-ss-title').textContent = titleFor(sel);

        function render() {
            list.innerHTML = '';
            if (!shown.length || shown.every(function (o) { return o.pinned; })) {
                var e = document.createElement('li'); e.className = 'ur-ss-empty'; e.textContent = 'No match. Try another spelling' + (shown.length ? ', or choose the option below.' : '.'); list.appendChild(e);
            }
            shown.forEach(function (o, i) {
                var li = document.createElement('li');
                li.className = 'ur-ss-item' + (String(o.value) === String(current) ? ' is-selected' : '') + (i === active ? ' is-active' : '');
                li.setAttribute('role', 'option');
                li.textContent = o.text;
                li.addEventListener('click', function () { choose(o); });
                list.appendChild(li);
            });
            count.textContent = shown.length === all.length ? all.length + ' options' : shown.length + ' of ' + all.length;
        }

        function choose(o) {
            sel.value = o.value;
            close();
            sel.dispatchEvent(new Event('input', { bubbles: true }));
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            refresh(item);
        }

        function filter() {
            var q = input.value.trim().toLowerCase();
            if (!q) { shown = all.slice(); }
            else {
                var starts = [], contains = [];
                all.forEach(function (o) { var t = o.text.toLowerCase(); var i = t.indexOf(q); if (i === 0) starts.push(o); else if (i > 0) contains.push(o); });
                shown = starts.concat(contains);
                // "Other"-style options stay available whatever was typed
                all.forEach(function (o) { if (o.pinned && shown.indexOf(o) === -1) shown.push(o); });
            }
            active = shown.length ? 0 : -1;
            render();
            list.scrollTop = 0;
        }

        input.addEventListener('input', filter);
        modal.querySelector('.ur-ss-x').addEventListener('click', close);
        modal.addEventListener('mousedown', function (e) { if (e.target === modal) close(); });
        modal.addEventListener('touchstart', function (e) { if (e.target === modal) close(); }, { passive: true });

        var onKey = function (e) {
            if (e.key === 'Escape') { e.preventDefault(); close(); return; }
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!shown.length) return;
                active = (active + (e.key === 'ArrowDown' ? 1 : -1) + shown.length) % shown.length;
                render();
                var el = list.children[active]; if (el && el.scrollIntoView) el.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (shown[active]) choose(shown[active]);
            }
        };
        document.addEventListener('keydown', onKey, true);

        // on a phone the keyboard shrinks the visible area: keep the sheet inside it
        var onResize = function () { if (window.visualViewport) panel.style.maxHeight = Math.round(window.visualViewport.height * 0.9) + 'px'; };
        if (window.visualViewport) { window.visualViewport.addEventListener('resize', onResize); onResize(); }

        openState = { item: item, modal: modal, onKey: onKey, onResize: onResize, prevOverflow: document.body.style.overflow };
        document.body.style.overflow = 'hidden';
        active = Math.max(0, shown.findIndex(function (o) { return String(o.value) === String(current); }));
        render();
        var sel0 = list.querySelector('.is-selected');
        if (sel0 && sel0.scrollIntoView) sel0.scrollIntoView({ block: 'center' });
        // desktop: ready to type; phone: leave the keyboard down until the member taps the search box
        if (window.matchMedia && window.matchMedia('(min-width: 640px)').matches) input.focus();
    }

    function init(root, opts) {
        opts = opts || {};
        var min = opts.minOptions == null ? 12 : opts.minOptions;
        injectCss();
        Array.prototype.forEach.call((root || document).querySelectorAll('select'), function (sel) {
            if (sel.multiple || sel.hasAttribute('data-no-search') || sel.dataset.urss === '1') return;
            if (sel.hasAttribute('data-search') || sel.options.length > min) enhance(sel);
        });
    }

    // keep labels, disabled state and style in step with scripts that change a select directly (value = …, new options, a step becoming visible)
    setInterval(function () {
        registry.forEach(function (item) {
            if (!document.body.contains(item.select)) return;
            var sig = item.select.value + '|' + item.select.options.length + '|' + item.select.disabled;
            if (sig !== item.sig || !item.synced) refresh(item);
        });
    }, 350);

    window.URSearchableSelect = { init: init, enhance: enhance, refresh: function () { registry.forEach(refresh); } };
})();
