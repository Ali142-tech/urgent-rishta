{{-- Currency picker shown above the "Monthly income range" dropdown. It only changes how the ranges are LABELLED (live rate from PKR);
     the saved value stays the PKR text, and this select has no name so it is never submitted. --}}
@php($__ic = app(\App\Services\CurrencyService::class)->incomeCurrencyOptions(request()))
@if(count($__ic['currencies']) > 1)
    <div class="ur-income-cur" style="margin-bottom:8px;">
        <div style="font-size:12px;opacity:.75;margin-bottom:4px;">Currency / Country</div>
        <select id="income_currency" class="{{ $selectClass ?? '' }}" style="width:100%;">
            @foreach($__ic['currencies'] as $code => $c)
                <option value="{{ $code }}" {{ $code === $__ic['default'] ? 'selected' : '' }}>{{ $c['label'] }}</option>
            @endforeach
        </select>
    </div>
    <script>
    (function () {
        var CFG = @json($__ic);
        function init() {
        var cur = document.getElementById('income_currency');
        var sal = document.querySelector('select[name="salary"]');
        if (!cur || !sal) { return; }

        function fmt(pkr, c) {
            var v = pkr * c.rate;
            var step = v >= 100000 ? 1000 : (v >= 1000 ? 100 : 10);
            return c.symbol + Math.round(Math.round(v / step) * step).toLocaleString('en-US');
        }
        function label(value, code) {
            var c = CFG.currencies[code];
            if (!c || code === 'PKR' || !/PKR/i.test(value)) { return value; }
            var m = value.match(/\d[\d,]*/g);
            if (!m) { return value; }
            var lo = parseFloat(m[0].replace(/,/g, '')), hi = m[1] ? parseFloat(m[1].replace(/,/g, '')) : null;
            if (hi !== null) { return '≈ ' + fmt(lo, c) + ' – ' + fmt(hi, c); }
            return (/below|under/i.test(value) ? '≈ below ' : '≈ above ') + fmt(lo, c);
        }
        function apply() {
            Array.prototype.forEach.call(sal.options, function (o) {
                if (!o.value) { return; }
                if (!o.hasAttribute('data-pkr')) { o.setAttribute('data-pkr', o.value); }
                var base = o.value.replace(/\s*\(current\)\s*$/, '');
                var tail = / \(current\)$/.test(o.textContent) ? ' (current)' : '';
                o.textContent = label(base, cur.value) + tail;
            });
            if (window.jQuery && jQuery.fn.selectpicker) { jQuery(sal).selectpicker('refresh'); }
        }
        cur.addEventListener('change', apply);
        apply();
        }
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
    })();
    </script>
@endif
