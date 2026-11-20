@extends('layouts.dashboard')
@section('dashboard-title', 'Messages')

@push('styles')
<style>
    .ur-chat { display:flex; height:calc(100vh - 190px); min-height:480px; background:#fff; border:1px solid #e6dfd1; border-radius:14px; overflow:hidden; }
    .ur-chat__list { width:320px; flex:0 0 320px; border-right:1px solid #eee6d6; display:flex; flex-direction:column; background:#fbf8f1; }
    .ur-chat__head { padding:14px 16px; font-weight:700; color:#123A2E; border-bottom:1px solid #eee6d6; display:flex; align-items:center; justify-content:space-between; }
    .ur-chat__live { font-size:11px; font-weight:600; color:#8a8a8a; }
    .ur-chat__live i { color:#c9c9c9; margin-right:4px; font-size:9px; }
    .ur-chat__live.is-on { color:#1f7a4d; } .ur-chat__live.is-on i { color:#1f7a4d; }
    .ur-chat__convs { overflow-y:auto; flex:1; }
    .ur-chat__conv { display:flex; gap:10px; padding:12px 14px; cursor:pointer; border-bottom:1px solid #f1ebdd; align-items:center; }
    .ur-chat__conv:hover { background:#f5efe2; }
    .ur-chat__conv.is-active { background:#efe6d2; }
    .ur-chat__av { width:44px; height:44px; border-radius:50%; background:#ddd center/cover no-repeat; flex:0 0 44px; }
    .ur-chat__cmeta { min-width:0; flex:1; }
    .ur-chat__cname { font-weight:700; color:#123A2E; font-size:14px; display:flex; justify-content:space-between; gap:6px; }
    .ur-chat__cname small { font-weight:400; color:#999; font-size:11px; white-space:nowrap; }
    .ur-chat__clast { font-size:12.5px; color:#777; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .ur-chat__badge { background:#C9974D; color:#fff; border-radius:10px; font-size:11px; font-weight:700; padding:1px 7px; margin-left:6px; }
    .ur-chat__empty { padding:28px 18px; color:#888; font-size:13.5px; text-align:center; line-height:1.6; }
    .ur-chat__main { flex:1; display:flex; flex-direction:column; min-width:0; }
    .ur-chat__top { padding:12px 16px; border-bottom:1px solid #eee6d6; display:flex; align-items:center; gap:10px; background:#fff; }
    .ur-chat__top b { color:#123A2E; } .ur-chat__top a { font-size:12px; margin-left:auto; color:#9a6a22; }
    .ur-chat__back { display:none; border:0; background:none; font-size:18px; color:#123A2E; padding:0 6px 0 0; }
    .ur-chat__msgs { flex:1; overflow-y:auto; padding:16px; background:#faf7ef; display:flex; flex-direction:column; gap:6px; }
    .ur-chat__b { max-width:72%; padding:8px 12px; border-radius:14px; font-size:14px; line-height:1.45; white-space:pre-wrap; word-wrap:break-word; overflow-wrap:anywhere; }
    .ur-chat__b small { display:block; font-size:10.5px; opacity:.65; margin-top:3px; text-align:right; }
    .ur-chat__b--me small, .ur-chat__b--me .ur-chat__seen { color:#cfeedd; opacity:.9; }
    .ur-chat__b--them { background:#fff; border:1px solid #eadfca; align-self:flex-start; border-bottom-left-radius:4px; color:#222; }
    .ur-chat__b--me { background:#123A2E; color:#fff; align-self:flex-end; border-bottom-right-radius:4px; }
    .ur-chat__form { display:flex; gap:8px; padding:12px; border-top:1px solid #eee6d6; background:#fff; align-items:flex-end; }
    .ur-chat__form textarea { flex:1; resize:none; border:1px solid #d9ceb6; border-radius:12px; padding:9px 12px; max-height:110px; font-size:14px; outline:none; }
    .ur-chat__form textarea:focus { border-color:#C9974D; }
    .ur-chat__form button { border:0; background:#C9974D; color:#fff; border-radius:12px; padding:0 18px; height:40px; font-weight:700; }
    .ur-chat__form button:disabled { opacity:.5; }
    .ur-chat__blank { margin:auto; text-align:center; color:#8a8a8a; padding:30px; }
    .ur-chat__locked { padding:12px 16px; background:#fff5e0; color:#7a5a1a; font-size:13px; text-align:center; border-top:1px solid #eee6d6; }
    @media (max-width: 767px) {
        .ur-chat { height:calc(100vh - 150px); }
        .ur-chat__list { width:100%; flex:1 1 100%; }
        .ur-chat__main { display:none; width:100%; }
        .ur-chat.is-thread .ur-chat__list { display:none; }
        .ur-chat.is-thread .ur-chat__main { display:flex; }
        .ur-chat__back { display:block; }
        .ur-chat__b { max-width:86%; }
    }
</style>
@endpush

@section('main-content')
<section class="slice sct-color-1">
    <div class="container">
        <div class="ur-chat" id="urChat">
            <div class="ur-chat__list">
                <div class="ur-chat__head">
                    <span><i class="fa fa-comments"></i> Messages</span>
                    <span class="ur-chat__live" id="urChatLive" title="Messages arrive instantly when connected"><i class="fa fa-circle"></i><span>Connecting…</span></span>
                </div>
                <div class="ur-chat__convs" id="urChatConvs"><div class="ur-chat__empty">Loading…</div></div>
            </div>
            <div class="ur-chat__main">
                <div class="ur-chat__top" id="urChatTop" style="display:none;">
                    <button type="button" class="ur-chat__back" id="urChatBack" aria-label="Back"><i class="fa fa-chevron-left"></i></button>
                    <div class="ur-chat__av" id="urChatTopAv" style="width:36px;height:36px;flex-basis:36px;"></div>
                    <b id="urChatTopName"></b>
                    <a id="urChatTopLink" href="#" target="_blank">View profile</a>
                </div>
                <div class="ur-chat__msgs" id="urChatMsgs">
                    <div class="ur-chat__blank"><i class="fa fa-comments-o" style="font-size:42px;"></i><p style="margin-top:10px;">Select a conversation to start chatting.</p></div>
                </div>
                <div class="ur-chat__locked" id="urChatLocked" style="display:none;">Chat is available once one of you has accepted the other's interest.</div>
                <form class="ur-chat__form" id="urChatForm" style="display:none;" autocomplete="off">
                    <textarea id="urChatInput" rows="1" maxlength="1000" placeholder="Type a message…"></textarea>
                    <button type="submit" id="urChatSend"><i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>

@if($realtime)
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
@endif
<script>
(function () {
    var ME = @json($meDataid), ME_ID = @json($meId), OPEN = @json($open), RT = @json($realtime);
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var $ = function (id) { return document.getElementById(id); };
    var root = $('urChat'), convsEl = $('urChatConvs'), msgsEl = $('urChatMsgs'), form = $('urChatForm'), input = $('urChatInput');
    var active = null, lastId = 0, peerReadUpTo = 0, seen = {}, connected = false, convCache = [];

    function api(url, opts) {
        opts = opts || {};
        opts.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }, opts.headers || {});
        opts.credentials = 'same-origin';
        return fetch(url, opts).then(function (r) { return r.json().then(function (j) { j._status = r.status; return j; }); });
    }
    function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; }
    function timeOf(iso) { var d = new Date(iso); return isNaN(d) ? '' : d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); }
    function shortWhen(iso) {
        if (!iso) return '';
        var d = new Date(iso), n = new Date();
        return d.toDateString() === n.toDateString() ? timeOf(iso) : d.toLocaleDateString([], { day: 'numeric', month: 'short' });
    }
    function avatar(url) { var a = el('div', 'ur-chat__av'); if (url) a.style.backgroundImage = 'url("' + String(url).replace(/"/g, '%22') + '")'; return a; }

    /* ---------- conversation list ---------- */
    function renderConvs(list) {
        convCache = list;
        convsEl.textContent = '';
        if (!list.length) {
            var e = el('div', 'ur-chat__empty');
            e.appendChild(document.createTextNode('No conversations yet. Chat opens when one of you accepts the other\'s interest.'));
            e.appendChild(document.createElement('br'));
            var a = el('a', null, 'Go to My Interests'); a.href = '/member/profile/listing/interests'; e.appendChild(a);
            convsEl.appendChild(e); return;
        }
        list.forEach(function (c) {
            var row = el('div', 'ur-chat__conv' + (active === c.dataid ? ' is-active' : ''));
            row.appendChild(avatar(c.avatar));
            var meta = el('div', 'ur-chat__cmeta');
            var name = el('div', 'ur-chat__cname'); name.appendChild(el('span', null, c.name || c.dataid));
            var right = el('small', null, shortWhen(c.at));
            if (c.unread) right.appendChild(el('span', 'ur-chat__badge', c.unread));
            name.appendChild(right);
            meta.appendChild(name); meta.appendChild(el('div', 'ur-chat__clast', c.last));
            row.appendChild(meta);
            row.addEventListener('click', function () { openThread(c.dataid); });
            convsEl.appendChild(row);
        });
        var total = list.reduce(function (s, c) { return s + (c.unread || 0); }, 0);
        document.querySelectorAll('.ur-chat-unread').forEach(function (b) { b.textContent = total; b.style.display = total ? '' : 'none'; });
    }
    function loadConvs() { return api('/member/chat/conversations').then(function (j) { if (j.conversations) renderConvs(j.conversations); }); }

    /* ---------- thread ---------- */
    function bubble(m) {
        var b = el('div', 'ur-chat__b ur-chat__b--' + (m.mine ? 'me' : 'them'));
        b.dataset.id = m.id;
        b.appendChild(document.createTextNode(m.body));
        var s = el('small', null, timeOf(m.at)); s.className = 'ur-chat__meta'; b.appendChild(s);
        return b;
    }
    function paintSeen() {
        msgsEl.querySelectorAll('.ur-chat__seen').forEach(function (n) { n.remove(); });
        var mine = Array.prototype.filter.call(msgsEl.querySelectorAll('.ur-chat__b--me'), function (n) { return +n.dataset.id <= peerReadUpTo; });
        var last = mine[mine.length - 1];
        if (last) { var t = el('span', 'ur-chat__seen', ' · Seen'); last.querySelector('small').appendChild(t); }
    }
    function addMessages(arr) {
        var stick = msgsEl.scrollHeight - msgsEl.scrollTop - msgsEl.clientHeight < 80;
        arr.forEach(function (m) {
            if (seen[m.id]) return;
            seen[m.id] = true; lastId = Math.max(lastId, m.id);
            msgsEl.appendChild(bubble(m));
        });
        paintSeen();
        if (stick || arr.some(function (m) { return m.mine; })) msgsEl.scrollTop = msgsEl.scrollHeight;
    }
    function setThreadUI(data) {
        $('urChatTop').style.display = 'flex';
        $('urChatTopName').textContent = data.member.name || data.member.dataid;
        $('urChatTopAv').style.backgroundImage = data.member.avatar ? 'url("' + String(data.member.avatar).replace(/"/g, '%22') + '")' : '';
        $('urChatTopLink').href = '/member/profile/' + encodeURIComponent(data.member.dataid);
        form.style.display = data.can_chat ? 'flex' : 'none';
        $('urChatLocked').style.display = data.can_chat ? 'none' : 'block';
    }
    function openThread(dataid) {
        active = dataid; lastId = 0; seen = {}; peerReadUpTo = 0;
        root.classList.add('is-thread');
        msgsEl.textContent = '';
        api('/member/chat/' + encodeURIComponent(dataid) + '/messages').then(function (j) {
            if (j._status !== 200) { msgsEl.appendChild(el('div', 'ur-chat__blank', j.message || 'Could not open this chat.')); return; }
            peerReadUpTo = j.peer_read_up_to || 0;
            setThreadUI(j);
            if (!j.messages.length) msgsEl.appendChild(el('div', 'ur-chat__blank', 'No messages yet. Say salaam!'));
            addMessages(j.messages);
            msgsEl.scrollTop = msgsEl.scrollHeight;
            if (j.can_chat) input.focus();
            loadConvs();
        });
        try { history.replaceState(null, '', '/member/chat?with=' + encodeURIComponent(dataid)); } catch (e) {}
        renderConvs(convCache);
    }
    function pollThread() {
        if (!active) return;
        api('/member/chat/' + encodeURIComponent(active) + '/messages?after=' + lastId).then(function (j) {
            if (j._status !== 200) return;
            if (j.messages.length) { var blank = msgsEl.querySelector('.ur-chat__blank'); if (blank) blank.remove(); addMessages(j.messages); loadConvs(); }
            if (j.peer_read_up_to !== peerReadUpTo) { peerReadUpTo = j.peer_read_up_to; paintSeen(); }
        });
    }

    /* ---------- sending ---------- */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = input.value.trim();
        if (!text || !active) return;
        var btn = $('urChatSend'); btn.disabled = true;
        // clear the box straight away so the member can keep typing; the text comes back if the send fails
        input.value = ''; input.style.height = 'auto';
        function restore() { if (!input.value) { input.value = text; } }
        api('/member/chat/' + encodeURIComponent(active) + '/send', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ body: text })
        }).then(function (j) {
            btn.disabled = false;
            if (j._status === 200 && j.message) {
                var blank = msgsEl.querySelector('.ur-chat__blank'); if (blank) blank.remove();
                addMessages([j.message]); loadConvs();
            } else {
                restore();
                alert(j.message || 'Message could not be sent. Please try again.');
            }
            input.focus();
        }).catch(function () { btn.disabled = false; restore(); alert('Network problem. Please try again.'); });
    });
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); } });
    input.addEventListener('input', function () { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 110) + 'px'; });
    $('urChatBack').addEventListener('click', function () { root.classList.remove('is-thread'); active = null; renderConvs(convCache); });

    /* ---------- realtime (Pusher) with polling as the safety net ---------- */
    function setLive(on) {
        connected = on;
        var b = $('urChatLive'); b.classList.toggle('is-on', on);
        b.querySelector('span').textContent = on ? 'Live' : (RT ? 'Reconnecting…' : 'Auto-refresh');
    }
    setLive(false);
    if (RT && window.Pusher) {
        try {
            var opts = {
                cluster: RT.cluster,
                // the layout already loads an older pusher-js that only knows authEndpoint/auth, so give both spellings
                authEndpoint: '/member/chat/pusher-auth',
                auth: { headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } },
                channelAuthorization: { endpoint: '/member/chat/pusher-auth', transport: 'ajax', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }
            };
            if (RT.driver === 'reverb') {   // our own Reverb server instead of Pusher's cloud
                opts.wsHost = RT.wsHost; opts.wsPort = RT.wsPort; opts.wssPort = RT.wsPort; opts.forceTLS = RT.forceTLS;
                opts.enabledTransports = ['ws', 'wss']; opts.disableStats = true;
            }
            var pusher = new Pusher(RT.key, opts);
            pusher.connection.bind('disconnected', function () { setLive(false); });
            pusher.connection.bind('unavailable', function () { setLive(false); });
            var ch = pusher.subscribe('private-chat-user-' + ME_ID);
            // "Live" only once the private channel is really subscribed (a failed auth must not look connected)
            ch.bind('pusher:subscription_succeeded', function () { setLive(true); });
            ch.bind('pusher:subscription_error', function () { setLive(false); });
            window.__urPusher = pusher;
            ch.bind('chat.message', function (d) {
                if (active && d.from === active) {
                    var blank = msgsEl.querySelector('.ur-chat__blank'); if (blank) blank.remove();
                    addMessages([d.message]);
                    if (!d.message.mine) pollThread();   // marks it read and refreshes the "Seen" state
                }
                loadConvs();
            });
            ch.bind('chat.read', function (d) { if (active && d.by === active) pollThread(); });
        } catch (e) { setLive(false); }
    } else {
        $('urChatLive').querySelector('span').textContent = 'Auto-refresh';
    }
    var tick = 0;
    setInterval(function () {
        if (document.hidden) return;
        tick++;
        if (connected && tick % 4) return;   // realtime is connected: polling is only a slow safety net
        pollThread(); loadConvs();
    }, 5000);

    loadConvs().then(function () { if (OPEN) openThread(OPEN); });
})();
</script>
@endsection
