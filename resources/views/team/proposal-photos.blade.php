{{--
    Manage photos for a proposal — see TeamController::photos()/uploadPhoto()/
    deletePhoto(). Only reachable by the proposal's own team member or admin.
    Redesigned (Oct 2026) to match the Add/Edit Proposal pages: header with the
    profile ID, a drag-or-click upload card with a live preview, and a grid of
    photo cards (main photo badge, large preview on click, delete). The forms
    and field names are unchanged (POST ...photos with `image`, DELETE ...photos/{id}).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Proposal Photos')
@section('main-content')
<style>
    .ur-ph { max-width: 1100px; font-family: 'Manrope', system-ui, sans-serif; }
    .ur-ph-back { display: inline-flex; align-items: center; gap: 6px; color: #6B7570; font-size: 13px; font-weight: 700; text-decoration: none; margin-bottom: 14px; }
    .ur-ph-back:hover { color: #123A2E; text-decoration: none; }
    .ur-ph-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .ur-ph-head h1 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; font-size: 26px; line-height: 1.2 !important; color: #123A2E; margin: 0 0 6px; }
    .ur-ph-head p { font-size: 14px; color: #4B5651; line-height: 1.5 !important; margin: 0; max-width: 620px; }
    .ur-ph-chip { display: inline-block; background: #F3F0E8; color: #123A2E; font-size: 11.5px; font-weight: 800; padding: 4px 11px; border-radius: 999px; vertical-align: middle; margin-left: 8px; letter-spacing: .02em; }
    .ur-ph-links { display: flex; gap: 8px; flex-wrap: wrap; }
    .ur-ph-link { display: inline-flex; align-items: center; gap: 7px; height: 40px; padding: 0 16px; border-radius: 12px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; font-size: 13px; font-weight: 800; text-decoration: none; }
    .ur-ph-link:hover { background: #F6F4EF; color: #123A2E; text-decoration: none; }

    .ur-ph-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 22px; padding: 22px 24px; margin-bottom: 18px; }
    .ur-ph-card__head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
    .ur-ph-card__head h2 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; font-size: 17px; line-height: 1.3 !important; color: #123A2E; margin: 0; }
    .ur-ph-count { background: #F3F0E8; color: #6B7570; font-size: 12px; font-weight: 800; padding: 5px 12px; border-radius: 999px; }

    /* upload */
    .ur-ph-drop { display: flex; align-items: center; gap: 18px; border: 1.5px dashed #D8D2C2; background: #F7F5EF; border-radius: 16px; padding: 18px; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
    .ur-ph-drop:hover, .ur-ph-drop.is-over { border-color: #123A2E; background: #EEF3EF; }
    .ur-ph-drop__preview { flex: 0 0 96px; width: 96px; height: 96px; border-radius: 14px; background: #fff; border: 1px solid #E7E2D6; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #9AA5A0; font-size: 26px; }
    .ur-ph-drop__preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ur-ph-drop__text b { display: block; font-size: 15px; color: #123A2E; line-height: 1.3 !important; }
    .ur-ph-drop__text small { display: block; font-size: 12px; color: #6B7570; line-height: 1.4 !important; margin-top: 3px; }
    .ur-ph-drop__file { font-size: 12.5px; color: #1C2321; font-weight: 700; margin-top: 6px; word-break: break-all; }
    .ur-ph-upload-actions { display: flex; align-items: center; gap: 10px; margin-top: 14px; }
    .ur-ph-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 44px; padding: 0 22px; border-radius: 12px; border: 1px solid #123A2E; background: #123A2E; color: #fff; font-size: 14px; font-weight: 800; cursor: pointer; box-shadow: 0 8px 20px rgba(15,46,36,.18); }
    .ur-ph-btn:hover { background: #0F2E24; }
    .ur-ph-btn[disabled] { opacity: .45; cursor: not-allowed; box-shadow: none; }
    .ur-ph-btn.is-loading { pointer-events: none; opacity: .85; }
    .ur-ph-spinner { display: inline-block; width: 15px; height: 15px; border-radius: 50%; border: 2px solid rgba(255,255,255,.45); border-top-color: #fff; animation: urPhSpin .8s linear infinite; }
    @keyframes urPhSpin { to { transform: rotate(360deg); } }
    .ur-ph-error { font-size: 12.5px; color: #B5674A; font-weight: 700; display: none; }

    /* grid */
    .ur-ph-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 14px; }
    .ur-ph-item { position: relative; border-radius: 16px; overflow: hidden; border: 1px solid #E7E2D6; background: #F3F0E8; }
    .ur-ph-item__img { display: block; width: 100%; aspect-ratio: 3 / 4; object-fit: cover; object-position: center 20%; cursor: zoom-in; }
    .ur-ph-item__badge { position: absolute; top: 10px; left: 10px; background: #123A2E; color: #fff; font-size: 10.5px; font-weight: 800; padding: 4px 10px; border-radius: 999px; }
    .ur-ph-item__num { position: absolute; left: 10px; bottom: 10px; background: rgba(15,46,36,.82); color: #fff; font-size: 11px; font-weight: 800; padding: 5px 10px; border-radius: 9px; }
    .ur-ph-item form { position: absolute; top: 8px; right: 8px; margin: 0; }
    .ur-ph-item__delete { width: 34px; height: 34px; border-radius: 10px; border: none; background: rgba(255,255,255,.95); color: #B5674A; font-size: 14px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,.18); }
    .ur-ph-item__delete:hover { background: #B5674A; color: #fff; }
    .ur-ph-empty { text-align: center; padding: 34px 10px; color: #6B7570; }
    .ur-ph-empty i { display: block; font-size: 30px; color: #C9C2AE; margin-bottom: 10px; }
    .ur-ph-empty b { display: block; color: #123A2E; font-size: 15px; margin-bottom: 4px; }
    /* delete confirmation */
    .ur-ph-modal { display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(15,46,36,.55); align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(2px); }
    .ur-ph-modal.is-open { display: flex; }
    .ur-ph-modal__box { width: 100%; max-width: 420px; background: #fff; border-radius: 22px; padding: 26px 26px 22px; text-align: center; box-shadow: 0 30px 70px rgba(0,0,0,.3); animation: urPhPop .16s ease-out; font-family: 'Manrope', system-ui, sans-serif; }
    @keyframes urPhPop { from { transform: scale(.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .ur-ph-modal__thumb { width: 92px; height: 112px; margin: 0 auto 14px; border-radius: 14px; overflow: hidden; border: 1px solid #E7E2D6; background: #F3F0E8; position: relative; }
    .ur-ph-modal__thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ur-ph-modal__thumb i { position: absolute; right: -8px; bottom: -8px; width: 34px; height: 34px; line-height: 34px; border-radius: 50%; background: #B5674A; color: #fff; font-size: 14px; box-shadow: 0 0 0 3px #fff; }
    .ur-ph-modal h3 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; font-size: 19px; line-height: 1.3 !important; color: #123A2E; margin: 0 0 8px; }
    .ur-ph-modal p { font-size: 13.5px; line-height: 1.55 !important; color: #4B5651; margin: 0 0 20px; }
    .ur-ph-modal__actions { display: flex; gap: 10px; }
    .ur-ph-modal__actions button { flex: 1; height: 46px; border-radius: 12px; font-size: 14px; font-weight: 800; cursor: pointer; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .ur-ph-modal__actions button:hover { background: #F6F4EF; }
    .ur-ph-modal__actions .is-danger { background: #B5674A; border-color: #B5674A; color: #fff; box-shadow: 0 8px 20px rgba(181,103,74,.28); }
    .ur-ph-modal__actions .is-danger:hover { background: #9E5539; }
    .ur-ph-modal__actions .is-danger.is-loading { pointer-events: none; opacity: .85; }
    /* the site stylesheet colours every <span>; keep button labels the button's own colour */
    .ur-ph-modal__actions button span, .ur-ph-btn span { color: inherit !important; }
    .ur-ph-modal__actions .is-danger, .ur-ph-modal__actions .is-danger i { color: #fff !important; }
    @media (max-width: 540px) { .ur-ph-card { padding: 18px 16px; } .ur-ph-drop { flex-direction: column; text-align: center; } .ur-ph-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>

<div class="ur-ph">
    <a href="{{ route('team.proposals.mine') }}" class="ur-ph-back"><i class="fa fa-arrow-left"></i> Back to My Clients</a>

    <div class="ur-ph-head">
        <div>
            <h1>Manage photos <span class="ur-ph-chip">{{ $proposal->dataid }}</span></h1>
            <p>Upload clear photos of the client. The first photo is the main one shown on cards. Photos are visible only to authorized matchmakers.</p>
        </div>
        <div class="ur-ph-links">
            <a class="ur-ph-link" href="{{ route('team.proposals.view', $proposal->dataid) }}"><i class="fa fa-eye"></i> View file</a>
            <a class="ur-ph-link" href="{{ route('team.proposals.edit', $proposal->dataid) }}"><i class="fa fa-pencil"></i> Edit proposal</a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:14px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Upload --}}
    <div class="ur-ph-card">
        <div class="ur-ph-card__head"><h2>Add a photo</h2></div>
        <form method="POST" action="{{ route('team.proposals.photos.store', $proposal->dataid) }}" enctype="multipart/form-data" id="ur_ph_form">
            @csrf
            <input type="file" name="image" id="ur_ph_input" accept="image/jpeg,image/png,image/webp,image/gif" hidden required>
            <div class="ur-ph-drop" id="ur_ph_drop" role="button" tabindex="0">
                <div class="ur-ph-drop__preview" id="ur_ph_preview"><i class="fa fa-camera"></i></div>
                <div class="ur-ph-drop__text">
                    <b>Click to choose a photo, or drop it here</b>
                    <small>JPG, PNG, WebP or GIF &middot; up to 5 MB</small>
                    <div class="ur-ph-drop__file" id="ur_ph_filename"></div>
                </div>
            </div>
            <div class="ur-ph-upload-actions">
                <button type="submit" class="ur-ph-btn" id="ur_ph_submit" disabled><i class="fa fa-upload"></i> <span style="color: white">Upload photo</span></button>
                <span class="ur-ph-error" id="ur_ph_error"></span>
            </div>
        </form>
    </div>

    {{-- Photos --}}
    <div class="ur-ph-card">
        <div class="ur-ph-card__head">
            <h2>Current photos</h2>
            <span class="ur-ph-count">{{ $images->count() }} {{ $images->count() == 1 ? 'photo' : 'photos' }}</span>
        </div>

        @if($images->isEmpty())
            <div class="ur-ph-empty">
                <i class="fa fa-picture-o"></i>
                <b>No photos uploaded yet</b>
                Use &ldquo;Add a photo&rdquo; above to upload the first one.
            </div>
        @else
            <div class="ur-ph-grid">
                @foreach($images as $i => $image)
                @php
                    $thumb = $image->thumb_path;
                    $full = $image->path;
                    $isMain = (bool) $image->is_main;
                @endphp
                <div class="ur-ph-item">
                    <img class="ur-ph-item__img" src="{{ url($thumb) }}" data-full="{{ url($full) }}" alt="Photo {{ $i + 1 }}" loading="lazy" onerror="this.onerror=null;this.src='{{ url($full) }}';">
                    @if($isMain)<span class="ur-ph-item__badge">Main photo</span>@endif
                    <span class="ur-ph-item__num">Photo {{ $i + 1 }}</span>
                    <form method="POST" action="{{ route('team.proposals.photos.destroy', [$proposal->dataid, $image->id]) }}" class="js-ur-ph-delete" data-photo-num="{{ $i + 1 }}" data-photo-src="{{ url($thumb) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ur-ph-item__delete" title="Delete photo" aria-label="Delete photo"><i class="fa fa-trash"></i></button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Delete confirmation (replaces the browser's plain confirm box) --}}
<div class="ur-ph-modal" id="ur_ph_confirm" role="dialog" aria-modal="true" aria-labelledby="ur_ph_confirm_title">
    <div class="ur-ph-modal__box">
        <div class="ur-ph-modal__thumb"><img src="" alt="" id="ur_ph_confirm_img"><i class="fa fa-trash"></i></div>
        <h3 id="ur_ph_confirm_title">Delete this photo?</h3>
        <p id="ur_ph_confirm_text">This photo will be permanently removed from the proposal. This can't be undone.</p>
        <div class="ur-ph-modal__actions">
            <button type="button" id="ur_ph_confirm_no">Cancel</button>
            <button type="button" class="is-danger" id="ur_ph_confirm_yes"><i class="fa fa-trash"></i> <span>Delete photo</span></button>
        </div>
    </div>
</div>
{{-- Large view --}}
<div id="ur_ph_lightbox" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.88); align-items:center; justify-content:center;">
    <button type="button" id="ur_ph_lb_close" aria-label="Close" style="position:absolute; top:16px; right:22px; background:none; border:0; color:#fff; font-size:34px; line-height:1; cursor:pointer;">&times;</button>
    <img id="ur_ph_lb_img" src="" alt="" style="max-width:92vw; max-height:90vh; object-fit:contain; border-radius:8px;">
</div>

<script>
(function () {
    var input = document.getElementById('ur_ph_input');
    var drop = document.getElementById('ur_ph_drop');
    var preview = document.getElementById('ur_ph_preview');
    var nameEl = document.getElementById('ur_ph_filename');
    var submit = document.getElementById('ur_ph_submit');
    var err = document.getElementById('ur_ph_error');
    var form = document.getElementById('ur_ph_form');
    var ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    var MAX = 5 * 1024 * 1024;

    function setError(msg) { err.textContent = msg || ''; err.style.display = msg ? 'inline' : 'none'; }
    function reset() { preview.innerHTML = '<i class="fa fa-camera"></i>'; nameEl.textContent = ''; submit.disabled = true; }

    function chosen() {
        setError('');
        var f = input.files && input.files[0];
        if (!f) { reset(); return; }
        if (ALLOWED.indexOf(f.type) === -1) { input.value = ''; reset(); setError('Please choose a JPG, PNG, WebP or GIF image.'); return; }
        if (f.size > MAX) { input.value = ''; reset(); setError('That image is larger than 5 MB.'); return; }
        var r = new FileReader();
        r.onload = function (e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="">'; };
        r.readAsDataURL(f);
        nameEl.textContent = f.name + ' · ' + (f.size / 1024 / 1024).toFixed(1) + ' MB';
        submit.disabled = false;
    }

    drop.addEventListener('click', function () { input.click(); });
    drop.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } });
    input.addEventListener('change', chosen);
    ['dragenter', 'dragover'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('is-over'); }); });
    drop.addEventListener('drop', function (e) {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            chosen();
        }
    });

    form.addEventListener('submit', function () {
        submit.classList.add('is-loading');
        submit.querySelector('i').outerHTML = '<span class="ur-ph-spinner"></span>';
        submit.querySelector('span:last-child').textContent = 'Uploading...';
    });
    window.addEventListener('pageshow', function () { submit.classList.remove('is-loading'); });

    // delete confirmation dialog
    var confirmBox = document.getElementById('ur_ph_confirm');
    var confirmImg = document.getElementById('ur_ph_confirm_img');
    var confirmText = document.getElementById('ur_ph_confirm_text');
    var confirmNo = document.getElementById('ur_ph_confirm_no');
    var confirmYes = document.getElementById('ur_ph_confirm_yes');
    var pendingForm = null;
    function openConfirm(formEl) {
        pendingForm = formEl;
        var n = formEl.getAttribute('data-photo-num') || '';
        confirmImg.src = formEl.getAttribute('data-photo-src') || '';
        confirmText.textContent = 'Photo ' + n + ' will be permanently removed from this proposal. This can\u2019t be undone.';
        confirmBox.classList.add('is-open');
        setTimeout(function () { confirmNo.focus(); }, 30);   // default focus = the safe choice
    }
    function closeConfirm() { confirmBox.classList.remove('is-open'); pendingForm = null; }
    document.querySelectorAll('form.js-ur-ph-delete').forEach(function (formEl) {
        formEl.addEventListener('submit', function (e) { e.preventDefault(); openConfirm(formEl); });
    });
    confirmNo.addEventListener('click', closeConfirm);
    confirmBox.addEventListener('click', function (e) { if (e.target === confirmBox) closeConfirm(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && confirmBox.classList.contains('is-open')) closeConfirm(); });
    confirmYes.addEventListener('click', function () {
        if (!pendingForm) return;
        confirmYes.classList.add('is-loading');
        confirmYes.querySelector('i').outerHTML = '<span class="ur-ph-spinner" style="border-color:rgba(255,255,255,.45);border-top-color:#fff;"></span>';
        confirmYes.querySelector('span:last-child').textContent = 'Deleting...';
        pendingForm.submit();    // native submit() does not re-fire the 'submit' listener above
    });
    // large view
    var box = document.getElementById('ur_ph_lightbox');
    var big = document.getElementById('ur_ph_lb_img');
    document.querySelectorAll('.ur-ph-item__img').forEach(function (img) {
        img.addEventListener('click', function () { big.src = img.getAttribute('data-full') || img.src; box.style.display = 'flex'; });
    });
    function hide() { box.style.display = 'none'; }
    document.getElementById('ur_ph_lb_close').onclick = hide;
    box.addEventListener('click', function (e) { if (e.target === box) hide(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); });
})();
</script>
@endsection
