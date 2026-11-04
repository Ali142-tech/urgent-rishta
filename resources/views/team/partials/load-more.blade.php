{{--
    Shared "Load more" button for the team pages that show a long list in batches (AI Match, Matchmakers).
    Params: $lmGrid (id of the element the next batch is appended to), $lmUrl (JSON endpoint for the next batch),
    $lmShown / $lmTotal (counts so far), $lmNoun ("matches", "matchmakers" ...).
    The endpoint answers { html, count, has_more, next_url } — see TeamController::matchCenterResponse() / matchmakers().
--}}
@once
<style>
    .ur-lm { display: flex; flex-direction: column; align-items: center; gap: 10px; margin: 26px 0 8px; }
    .ur-lm__btn { display: inline-flex; align-items: center; gap: 12px; height: 48px; padding: 0 12px 0 24px; border: 0; border-radius: 999px; background: #123A2E; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 20px rgba(15, 46, 36, .2); transition: transform .15s ease, background .15s ease; }
    .ur-lm__btn:hover { background: #0D2C23; transform: translateY(-1px); }
    .ur-lm__btn:disabled { opacity: .7; cursor: wait; transform: none; }
    .ur-lm__btn i, .ur-lm__btn span { color: inherit !important; }
    .ur-lm__btn .ur-lm__left { background: #C9974D; color: #1C2321 !important; border-radius: 999px; padding: 5px 13px; font-size: 12px; font-weight: 700; }
    .ur-lm__count { font-size: 12.5px; color: #6B7570; }
    .ur-lm__spin { display: inline-block; width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgba(255,255,255,.35); border-top-color: #fff; animation: urLmSpin .7s linear infinite; }
    @keyframes urLmSpin { to { transform: rotate(360deg); } }
</style>
@endonce
<div class="ur-lm">
    <button type="button" class="ur-lm__btn" data-grid="{{ $lmGrid }}" data-url="{{ $lmUrl }}" data-shown="{{ $lmShown }}" data-total="{{ $lmTotal }}" data-noun="{{ $lmNoun }}">
        <span class="ur-lm__label"><i class="fa fa-plus-circle"></i> Load more {{ $lmNoun }}</span>
        <span class="ur-lm__left">{{ $lmTotal - $lmShown }} more</span>
    </button>
    <div class="ur-lm__count">Showing {{ $lmShown }} of {{ $lmTotal }}</div>
</div>
@once
<script>
    (function () {
        document.querySelectorAll('.ur-lm__btn').forEach(function (btn) {
            var grid = document.getElementById(btn.getAttribute('data-grid')), count = btn.parentNode.querySelector('.ur-lm__count');
            if (!grid) return;
            btn.addEventListener('click', function () {
                var label = btn.querySelector('.ur-lm__label'), left = btn.querySelector('.ur-lm__left'), original = label.innerHTML;
                btn.disabled = true;
                label.innerHTML = '<span class="ur-lm__spin"></span> Loading...';
                fetch(btn.getAttribute('data-url'), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function (r) { if (!r.ok) throw new Error('bad'); return r.json(); })
                    .then(function (res) {
                        grid.insertAdjacentHTML('beforeend', res.html);
                        var shown = parseInt(btn.getAttribute('data-shown'), 10) + res.count, total = parseInt(btn.getAttribute('data-total'), 10);
                        btn.setAttribute('data-shown', shown);
                        count.textContent = 'Showing ' + shown + ' of ' + total;
                        if (!res.has_more) { btn.parentNode.removeChild(btn); return; }
                        btn.setAttribute('data-url', res.next_url);
                        left.textContent = (total - shown) + ' more';
                        label.innerHTML = original;
                        btn.disabled = false;
                    })
                    .catch(function () {
                        label.innerHTML = original;
                        btn.disabled = false;
                        alert('Could not load more ' + btn.getAttribute('data-noun') + '. Please try again.');
                    });
            });
        });
    })();
</script>
@endonce
