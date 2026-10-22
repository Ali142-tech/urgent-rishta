{{--
    Admin-configurable weight per compatibility factor — see
    Profile::compatibilityWith() and MatchWeightSetting::current(). Every
    factor defaults to 1 (equal weight), reproducing the old flat-average
    score exactly until an admin changes something here.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'AI Match Weights')
@section('main-content')
@php
    $factors = [
        'age_weight' => ['Age', 'Age', 'fa-birthday-cake', 'Is the age inside the preferred range? Closer to the range scores higher.'],
        'location_weight' => ['Location', 'Location', 'fa-map-marker', 'Country and city of residence against the preferred location.'],
        'religion_weight' => ['Religion', 'Religion', 'fa-moon-o', 'Whether the religion matches what the member asked for.'],
        'caste_weight' => ['Caste', 'Caste', 'fa-users', 'Caste / community against the preferred caste.'],
        'marital_status_weight' => ['Marital Status', 'Marital Status', 'fa-heart-o', 'Never married, divorced, widowed… against the preference.'],
        'education_weight' => ['Education', 'Education', 'fa-graduation-cap', 'Education level against the minimum education wanted.'],
        'mother_tongue_weight' => ['Mother Tongue', 'Mother Tongue', 'fa-comments-o', 'Mother tongue against the preferred language.'],
        'children_weight' => ['Children', 'Children', 'fa-child', 'Whether children are acceptable to the other side.'],
    ];
@endphp
<style>
    .mw { max-width: 1080px; }
    .mw-hero { background: linear-gradient(135deg, #123A2E 0%, #1C5A47 100%); border-radius: 20px; padding: 26px 30px; color: #fff; position: relative; overflow: hidden; margin-bottom: 22px; }
    .mw-hero::after { content: ""; position: absolute; right: -50px; top: -70px; width: 250px; height: 250px; border-radius: 50%; background: radial-gradient(circle, rgba(201,151,77,.35), transparent 70%); }
    .mw-hero h1 { font-family: 'Playfair Display', serif; font-size: 27px; margin: 0 0 8px; color: #fff; position: relative; }
    .mw-hero p { margin: 0; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.65; max-width: 720px; position: relative; }
    .mw-tips { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; position: relative; }
    .mw-tip { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); border-radius: 999px; padding: 6px 13px; font-size: 12px; font-weight: 700; color: #fff; display: inline-flex; gap: 7px; align-items: center; }
    .mw-tip i { color: #E9C98F; }

    .mw-card { background: #fff; border: 1px solid #EADFCB; border-radius: 18px; box-shadow: 0 10px 28px rgba(15,46,36,.06); padding: 20px 22px; margin-bottom: 20px; }
    .mw-card__title { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
    .mw-card__title b { font-size: 15px; color: #123A2E; }
    .mw-card__title span { font-size: 12.5px; color: #6B7570; }
    .mw-stack { display: flex; height: 22px; border-radius: 999px; overflow: hidden; background: #F0E8D6; }
    .mw-stack > div { height: 100%; transition: width .3s; min-width: 0; }
    .mw-legend { display: flex; flex-wrap: wrap; gap: 8px 16px; margin-top: 14px; }
    .mw-legend span { display: inline-flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; color: #3B4540; }
    .mw-legend i { width: 11px; height: 11px; border-radius: 3px; display: inline-block; }
    .mw-legend em { font-style: normal; color: #8A938E; font-weight: 600; }

    .mw-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .mw-f { background: #fff; border: 1px solid #EADFCB; border-radius: 16px; padding: 16px 18px 14px; transition: .15s; position: relative; }
    .mw-f:hover { box-shadow: 0 10px 24px rgba(15,46,36,.09); border-color: #D9CBA8; }
    .mw-f.is-off { background: #FAF8F2; }
    .mw-f.is-off .mw-f__name, .mw-f.is-off .mw-f__ic { opacity: .5; }
    .mw-f__top { display: flex; align-items: center; gap: 13px; }
    .mw-f__ic { width: 42px; height: 42px; border-radius: 13px; display: grid; place-items: center; color: #fff; font-size: 17px; flex: none; }
    .mw-f__name { font-size: 15.5px; font-weight: 800; color: #123A2E; line-height: 1.2; }
    .mw-f__share { margin-left: auto; text-align: right; }
    .mw-f__share b { font-size: 22px; font-weight: 800; color: #1C9A6C; display: block; line-height: 1; }
    .mw-f__share small { font-size: 10.5px; letter-spacing: .08em; font-weight: 800; color: #9AA39E; text-transform: uppercase; }
    .mw-f__desc { font-size: 12.5px; color: #6B7570; line-height: 1.5; margin: 10px 0 12px; min-height: 36px; }
    .mw-f__ctl { display: flex; align-items: center; gap: 14px; }
    .mw-f input[type=range] { flex: 1; -webkit-appearance: none; appearance: none; height: 8px; border-radius: 999px; background: linear-gradient(90deg, var(--c) var(--p), #EDE5D2 var(--p)); outline: none; cursor: pointer; }
    .mw-f input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 4px solid var(--c); box-shadow: 0 2px 8px rgba(0,0,0,.2); cursor: pointer; }
    .mw-f input[type=range]::-moz-range-thumb { width: 16px; height: 16px; border-radius: 50%; background: #fff; border: 4px solid var(--c); box-shadow: 0 2px 8px rgba(0,0,0,.2); cursor: pointer; }
    .mw-f input[type=number] { width: 78px; height: 40px; border: 1px solid #DDD3BE; border-radius: 11px; padding: 0 10px; text-align: center; font-weight: 800; font-size: 15px; color: #123A2E; background: #fff; }
    .mw-f input[type=number]:focus { outline: 0; border-color: #123A2E; box-shadow: 0 0 0 3px rgba(18,58,46,.10); }
    .mw-f__ticks { display: flex; justify-content: space-between; font-size: 10.5px; color: #A39A7F; font-weight: 700; margin: 6px 92px 0 0; }

    .mw-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
    .mw-btn { border: 0; border-radius: 12px; padding: 12px 20px; font: inherit; font-weight: 800; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 9px; transition: .15s; }
    .mw-btn--ghost { background: #fff; border: 1px solid #EADFCB; color: #123A2E; }
    .mw-btn--ghost:hover { border-color: #123A2E; }
    .mw-btn--save { background: linear-gradient(180deg, #D8A85C, #C9974D); color: #fff; box-shadow: 0 10px 20px rgba(201,151,77,.32); padding: 13px 28px; }
    .mw-btn--save:hover { filter: brightness(1.06); }
    .mw-presets { display: flex; gap: 10px; flex-wrap: wrap; }
    .mw-note { font-size: 12.5px; color: #6B7570; margin-top: 14px; }
    @media (max-width: 760px) { .mw-grid { grid-template-columns: 1fr; } .mw-hero { padding: 22px 20px; } .mw-actions { flex-direction: column-reverse; align-items: stretch; } .mw-btn { justify-content: center; } .mw-f__ticks { margin-right: 92px; } }
</style>

<div class="mw">
    <div class="mw-hero">
        <h1>AI Match Weights</h1>
        <p>Decide how much each factor counts when we work out the <b>“% Match”</b> that members and the team see. A higher weight means that factor moves the score more. The numbers do not have to add up to 100 — they are balanced automatically.</p>
        <div class="mw-tips">
            <span class="mw-tip"><i class="fa fa-sliders"></i> 0 – 100 per factor</span>
            <span class="mw-tip"><i class="fa fa-ban"></i> 0 removes a factor from the score</span>
            <span class="mw-tip"><i class="fa fa-balance-scale"></i> Equal numbers = equal importance</span>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.match-weights.update') }}" id="mw_form">
        @csrf

        <div class="mw-card">
            <div class="mw-card__title"><b>How the score is made up</b><span id="mw_total_note">Share of the final score, by factor</span></div>
            <div class="mw-stack" id="mw_stack"></div>
            <div class="mw-legend" id="mw_legend"></div>
        </div>

        <div class="mw-grid">
            @foreach($factors as $field => $meta)
                @php($value = old($field, $weights[$meta[1]] ?? 1))
                <div class="mw-f" data-field="{{ $field }}" data-label="{{ $meta[0] }}">
                    <div class="mw-f__top">
                        <span class="mw-f__ic"><i class="fa {{ $meta[2] }}"></i></span>
                        <div class="mw-f__name">{{ $meta[0] }}</div>
                        <div class="mw-f__share"><b class="js-share">0%</b><small>of score</small></div>
                    </div>
                    <div class="mw-f__desc">{{ $meta[3] }}</div>
                    <div class="mw-f__ctl">
                        <input type="range" min="0" max="100" step="1" value="{{ $value }}" aria-label="{{ $meta[0] }} weight" class="js-range">
                        <input type="number" min="0" max="100" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" class="js-num">
                    </div>
                    <div class="mw-f__ticks"><span>0</span><span>25</span><span>50</span><span>75</span><span>100</span></div>
                </div>
            @endforeach
        </div>

        <div class="mw-note"><i class="fa fa-info-circle"></i> Saving applies the new weights to every AI Match shown across the site straight away.</div>

        <div class="mw-actions">
            <div class="mw-presets">
                <button type="button" class="mw-btn mw-btn--ghost" id="mw_equal"><i class="fa fa-balance-scale"></i> Make all equal</button>
                <button type="button" class="mw-btn mw-btn--ghost" id="mw_reset"><i class="fa fa-undo"></i> Undo my changes</button>
            </div>
            <button type="submit" class="mw-btn mw-btn--save"><i class="fa fa-check"></i> Save Weights</button>
        </div>
    </form>
</div>

<script>
(function () {
    var COLORS = ['#1C9A6C', '#C9974D', '#3B7DD8', '#B5674A', '#8B5CF6', '#E0709A', '#0EA5A5', '#7A8B2F'];
    var cards = Array.prototype.slice.call(document.querySelectorAll('.mw-f'));
    var stack = document.getElementById('mw_stack'), legend = document.getElementById('mw_legend');
    var initial = cards.map(function (c) { return c.querySelector('.js-num').value; });

    cards.forEach(function (card, i) {
        var color = COLORS[i % COLORS.length];
        card.style.setProperty('--c', color);
        card.querySelector('.mw-f__ic').style.background = color;
        var range = card.querySelector('.js-range'), num = card.querySelector('.js-num');
        range.addEventListener('input', function () { num.value = range.value; update(); });
        num.addEventListener('input', function () {
            var v = Math.max(0, Math.min(100, parseInt(num.value || '0', 10) || 0));
            range.value = v; update();
        });
        num.addEventListener('blur', function () { if (num.value === '') { num.value = 0; range.value = 0; update(); } });
    });

    function update() {
        var vals = cards.map(function (c) { return Math.max(0, Math.min(100, parseInt(c.querySelector('.js-num').value || '0', 10) || 0)); });
        var total = vals.reduce(function (a, b) { return a + b; }, 0);
        stack.innerHTML = ''; legend.innerHTML = '';
        cards.forEach(function (card, i) {
            var share = total > 0 ? vals[i] / total * 100 : 0;
            var color = COLORS[i % COLORS.length];
            card.querySelector('.js-share').textContent = share.toFixed(share >= 10 || share === 0 ? 0 : 1) + '%';
            card.classList.toggle('is-off', vals[i] === 0);
            card.querySelector('.js-range').style.setProperty('--p', vals[i] + '%');
            if (share > 0) {
                var seg = document.createElement('div'); seg.style.width = share + '%'; seg.style.background = color; seg.title = card.dataset.label + ' ' + share.toFixed(1) + '%'; stack.appendChild(seg);
            }
            var li = document.createElement('span');
            li.innerHTML = '<i style="background:' + color + '"></i>' + card.dataset.label + ' <em>' + (share ? share.toFixed(share >= 10 ? 0 : 1) + '%' : 'off') + '</em>';
            legend.appendChild(li);
        });
        document.getElementById('mw_total_note').textContent = total > 0 ? 'Share of the final score, by factor' : 'All factors are off — the score would be empty';
    }

    document.getElementById('mw_equal').addEventListener('click', function () {
        cards.forEach(function (c) { c.querySelector('.js-num').value = 10; c.querySelector('.js-range').value = 10; }); update();
    });
    document.getElementById('mw_reset').addEventListener('click', function () {
        cards.forEach(function (c, i) { c.querySelector('.js-num').value = initial[i]; c.querySelector('.js-range').value = initial[i]; }); update();
    });
    update();
})();
</script>
@endsection
