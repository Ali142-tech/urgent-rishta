{{--
    Full-detail read view for a single proposal — rendered inside the Team
    Dashboard shell (layouts.team.dashboard), unlike member/profile.blade.php
    (layouts.dashboard, the Member Dashboard shell). See TeamController::
    viewProposal(). Linked from every Team Dashboard card's "View Full
    Profile"/"View file" action (member-card.blade.php, search-result-
    card.blade.php) instead of member/profile/{dataid}, so a team member
    stays inside the Team Dashboard instead of being dropped into the
    Member Dashboard's own navigation.

    Design: the SAME "Complete Client File" layout as the popup (photos +
    identity header, then tabs — Original pasted form first, Profile details
    second — with the 3-column field cards), via
    team/partials/proposal-file-modal.blade.php in page mode. Contact
    information, About and Partner Requirements follow underneath as cards
    in the same style.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', trim($member->first_name . ' ' . $member->last_name) ?: $member->dataid)
@section('main-content')
<style>
    .ur-pv-back { display: inline-flex; align-items: center; gap: 6px; color: #6B7570; font-size: 13px; font-weight: 700; text-decoration: none; margin-bottom: 14px; }
    .ur-pv-back:hover { color: #123A2E; text-decoration: none; }

    /* The popup's card, used as a page: no overlay shadow, full width. */
    .ur-cf-page.ur-cf-modal { max-width: 1100px; box-shadow: 0 1px 3px rgba(15,46,36,.08); border: 1px solid #E7E2D6; margin-bottom: 18px; }
    .ur-cf-page .ur-cf-photo-slot img { cursor: zoom-in; }

    .ur-pv-section { max-width: 1100px; background: #fff; border: 1px solid #E7E2D6; border-radius: 18px; padding: 20px 24px; margin-bottom: 18px; }
    .ur-pv-section h2 { font-family: 'Manrope', system-ui, sans-serif; font-weight: 800; color: #123A2E; font-size: 15px; line-height: 1.3 !important; margin: 0 0 12px; }
    .ur-pv-section--soft { background: #FCF6EA; border-color: #F0DDB0; }
    .ur-pv-section .ur-cf-grid div { background: #fff; }
    .ur-pv-description { font-size: 13.5px; color: #1C2321; line-height: 1.6; white-space: pre-line; margin: 0; }
    .ur-pv-private { float: right; font-size: 10.5px; font-weight: 700; color: #8A6218; background: #FBF0DA; padding: 3px 10px; border-radius: 999px; }
    .ur-pv-gallery { display: flex; flex-wrap: wrap; gap: 10px; }
    .ur-pv-gallery img { width: 110px; height: 110px; object-fit: cover; border-radius: 12px; border: 1px solid #E7E2D6; cursor: zoom-in; }
</style>

<a href="{{ url()->previous() }}" class="ur-pv-back"><i class="fa fa-arrow-left"></i> Back to results</a>

<div class="ur-cf-modal ur-cf-page" id="ur_cf_page">
    @include('team.partials.proposal-file-modal', ['asPage' => true])
</div>


@if(!empty($member->profile_description))
<div class="ur-pv-section">
    <h2>About</h2>
    <p class="ur-pv-description">{{ $member->profile_description }}</p>
</div>
@endif

@if($preference)
<div class="ur-pv-section ur-pv-section--soft">
    <h2>Partner Requirements</h2>
    <div class="ur-cf-grid">
        @if(!empty($preference->marital_statuses))<div><span>Marital status</span><b>{{ implode(', ', $preference->marital_statuses) }}</b></div>@endif
        @if(!empty($preference->age_min) || !empty($preference->age_max))<div><span>Age</span><b>{{ $preference->age_min }} - {{ $preference->age_max }}</b></div>@endif
        @if(!empty($preference->pref_height))<div><span>Height</span><b>{{ $preference->pref_height }}</b></div>@endif
        @if(!empty($preference->educations))<div><span>Education</span><b>{{ implode(', ', $preference->educations) }}</b></div>@endif
        @if(!empty($preference->professions))<div><span>Profession</span><b>{{ implode(', ', $preference->professions) }}</b></div>@endif
        @if(!empty($preference->castes))<div><span>Caste</span><b>{{ implode(', ', $preference->castes) }}</b></div>@endif
        @if(!empty($preference->pref_city))<div><span>City</span><b>{{ $preference->pref_city }}</b></div>@endif
        @if(!empty($preference->nationalities))<div><span>Nationality</span><b>{{ implode(', ', $preference->nationalities) }}</b></div>@endif
    </div>
    @if(!empty($preference->general_requirement))
        <p class="ur-pv-description" style="margin-top:14px;">{{ $preference->general_requirement }}</p>
    @endif
</div>
@endif

@php
    // Full-size sources for the lightbox (thumbnails only for the strip).
    $galleryFull = json_decode($member->getLightGalleryImages(false), true) ?: [];
    $galleryFull = array_values(array_filter($galleryFull, fn ($g) => file_exists(public_path($g['src']))));
@endphp
@if(count($galleryFull) > 2)
<div class="ur-pv-section">
    <h2>All photos</h2>
    <div class="ur-pv-gallery">
        @foreach($galleryFull as $gi => $g)
            <img src="{{ file_exists(public_path($g['thumb'])) ? $g['thumb'] : $g['src'] }}" alt="" loading="lazy" data-full="{{ $g['src'] }}" data-index="{{ $gi }}">
        @endforeach
    </div>
</div>
@endif

<div id="urPvLightbox" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.88); align-items:center; justify-content:center;">
    <button type="button" id="urPvLbClose" aria-label="Close" style="position:absolute; top:16px; right:22px; background:none; border:0; color:#fff; font-size:34px; line-height:1; cursor:pointer;">&times;</button>
    <button type="button" id="urPvLbPrev" aria-label="Previous" style="position:absolute; left:18px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:0; color:#fff; font-size:26px; width:44px; height:44px; border-radius:50%; cursor:pointer;">&#8249;</button>
    <img id="urPvLbImg" src="" alt="" style="max-width:92vw; max-height:90vh; object-fit:contain; border-radius:8px;">
    <button type="button" id="urPvLbNext" aria-label="Next" style="position:absolute; right:18px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:0; color:#fff; font-size:26px; width:44px; height:44px; border-radius:50%; cursor:pointer;">&#8250;</button>
</div>
<script>
(function () {
    var page = document.getElementById('ur_cf_page');

    // ---- tabs, copy and "Form + photos" — the popup wires these on its own
    // container (#ur_client_file_body); on this page they live in #ur_cf_page.
    if (page) {
        page.addEventListener('click', function (e) {
            var tabBtn = e.target.closest('.ur-cf-tabs button[data-tab]');
            if (tabBtn) {
                page.querySelectorAll('.ur-cf-tabs button').forEach(function (b) { b.classList.toggle('is-active', b === tabBtn); });
                page.querySelectorAll('.ur-cf-panel').forEach(function (p) { p.classList.toggle('is-active', p.dataset.panel === tabBtn.dataset.tab); });
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
                        var range = document.createRange(); range.selectNodeContents(raw);
                        var sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range);
                        document.execCommand('copy'); sel.removeAllRanges();
                    }
                    done();
                }
                return;
            }
            var shareBtn = e.target.closest('#cf_share_photos_btn');
            if (shareBtn && typeof shareTeamProposalPhotos === 'function') shareTeamProposalPhotos(shareBtn);
        });
    }

    // ---- photo lightbox (header photos + the "All photos" strip, if shown)
    var box = document.getElementById('urPvLightbox');
    var img = document.getElementById('urPvLbImg');
    var full = @json(array_map(fn ($g) => $g['src'], $galleryFull));
    var cur = 0;
    function show(i) {
        if (!full.length) return;
        cur = (i + full.length) % full.length;
        img.src = full[cur];
        box.style.display = 'flex';
    }
    function hide() { box.style.display = 'none'; }
    document.querySelectorAll('.ur-cf-page .ur-cf-photo-slot img').forEach(function (el, i) { el.addEventListener('click', function () { show(i); }); });
    document.querySelectorAll('.ur-pv-gallery img').forEach(function (el) { el.addEventListener('click', function () { show(parseInt(el.getAttribute('data-index'), 10) || 0); }); });
    document.getElementById('urPvLbClose').onclick = hide;
    document.getElementById('urPvLbPrev').onclick = function (e) { e.stopPropagation(); show(cur - 1); };
    document.getElementById('urPvLbNext').onclick = function (e) { e.stopPropagation(); show(cur + 1); };
    box.addEventListener('click', function (e) { if (e.target === box) hide(); });
    document.addEventListener('keydown', function (e) {
        if (box.style.display === 'none') return;
        if (e.key === 'Escape') hide();
        if (e.key === 'ArrowLeft') show(cur - 1);
        if (e.key === 'ArrowRight') show(cur + 1);
    });
})();
</script>
@endsection
