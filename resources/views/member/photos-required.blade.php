@extends('layouts.master')

@section('main-content')
<style>
    .pg-page {
        --pg-green: #123A2E;
        --pg-green-deep: #0F2E24;
        --pg-gold: #C9974D;
        --pg-cream: #FBF7EF;
        --pg-line: #F0EADD;
        --pg-text: #5B6560;
        --pg-ink: #1C2321;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--pg-cream);
        min-height: calc(100vh - 220px);
        padding: 56px 20px;
    }
    .pg-card {
        max-width: 560px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid var(--pg-line);
        border-radius: 18px;
        padding: 40px 36px;
        box-shadow: 0 12px 36px rgba(15,46,36,.08);
        text-align: center;
    }
    .pg-icon {
        width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 18px;
        background: #EFE7D6; color: var(--pg-green); display: flex; align-items: center;
        justify-content: center; font-size: 24px;
    }
    .pg-card h2 { font-size: 22px; font-weight: 700; color: var(--pg-ink); margin: 0 0 8px; }
    .pg-card p.sub { color: var(--pg-text); font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
    .pg-progress { display: flex; gap: 8px; justify-content: center; margin-bottom: 22px; }
    .pg-progress span {
        width: 46px; height: 46px; border-radius: 10px; border: 1.5px dashed var(--pg-line);
        display: flex; align-items: center; justify-content: center; color: var(--pg-text); font-size: 18px;
        overflow: hidden; background: var(--pg-cream);
    }
    .pg-progress span.filled { border-style: solid; border-color: var(--pg-green); color: var(--pg-green); background: #fff; }
    .pg-progress span img { width: 100%; height: 100%; object-fit: cover; }
    .pg-dropzone {
        border: 2px dashed var(--pg-line); border-radius: 14px; padding: 26px 16px;
        cursor: pointer; transition: border-color .15s ease, background .15s ease; margin-bottom: 16px;
    }
    .pg-dropzone:hover, .pg-dropzone.dragover { border-color: var(--pg-gold); background: #FBF3E3; }
    .pg-dropzone i { font-size: 26px; color: var(--pg-gold); margin-bottom: 8px; display: block; }
    .pg-dropzone span { font-size: 13.5px; color: var(--pg-text); }
    .pg-status { font-size: 13px; color: var(--pg-text); margin-bottom: 6px; min-height: 18px; }
    .pg-note {
        background: #EFE7D6; color: var(--pg-ink); font-size: 12.5px; line-height: 1.6;
        border-radius: 10px; padding: 12px 14px; margin-top: 20px; text-align: left;
    }
    .pg-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 14px; border-radius: 10px; border: 0; font-weight: 700; font-size: 15px;
        background: var(--pg-green); color: var(--pg-cream); cursor: pointer; transition: .2s ease;
    }
    .pg-btn:hover { background: var(--pg-green-deep); }
    .pg-btn:disabled { background: #cfd6d2; cursor: not-allowed; }
    .pg-selfie {
        border-top: 1px solid var(--pg-line); margin-top: 26px; padding-top: 24px; text-align: left;
    }
    .pg-selfie__title { font-size: 14.5px; font-weight: 700; color: var(--pg-ink); margin: 0 0 4px; }
    .pg-selfie__desc { font-size: 12.5px; color: var(--pg-text); margin: 0 0 14px; line-height: 1.55; }
    .pg-selfie__stage {
        position: relative; width: 100%; max-width: 280px; margin: 0 auto 14px; border-radius: 14px;
        overflow: hidden; background: #111; aspect-ratio: 4/3;
    }
    .pg-selfie__stage video, .pg-selfie__stage canvas, .pg-selfie__stage img {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .pg-selfie__done {
        display: flex; align-items: center; gap: 10px; justify-content: center;
        color: var(--pg-green); font-size: 13.5px; font-weight: 600; margin-bottom: 10px;
    }
    .pg-selfie__actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .pg-btn--outline {
        background: transparent; border: 1.5px solid var(--pg-line); color: var(--pg-ink);
        padding: 10px 18px; border-radius: 9px; font-weight: 600; font-size: 13.5px; cursor: pointer;
    }
    .pg-btn--outline:hover { border-color: var(--pg-gold); }
    .pg-btn--small { width: auto; padding: 10px 20px; font-size: 13.5px; }
    .pg-required-tag {
        display: inline-block; background: var(--pg-terracotta, #B5674A); color: #fff; font-size: 10px;
        font-weight: 800; letter-spacing: .04em; text-transform: uppercase; padding: 2px 8px;
        border-radius: 99px; margin-left: 6px; vertical-align: middle;
    }
</style>

<div class="pg-page">
    <div class="pg-card">
        <div class="pg-icon"><i class="fa fa-camera" aria-hidden="true"></i></div>
        <h2>Add your photos to complete registration</h2>
        <p class="sub">
            Upload at least {{ $required }} recent photographs — one clear face photo and one recent
            lifestyle/full-length photo — and take a live selfie below. Only your own, unedited
            photographs are permitted. Once submitted, our team reviews everything before your account
            is activated; you'll be able to log in as soon as it's approved.
        </p>

        <div class="pg-progress" id="pg_progress">
            @for ($i = 0; $i < $required; $i++)
                <span><i class="fa fa-user" aria-hidden="true"></i></span>
            @endfor
        </div>

        <form id="pg_upload_form" enctype="multipart/form-data">
            @csrf
            <label class="pg-dropzone" for="pg_images" id="pg_dropzone">
                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                <span>Click to choose photos, or drag them here (JPG/PNG, max 5 MB each)</span>
            </label>
            <input type="file" id="pg_images" name="images[]" accept="image/png,image/x-png,image/gif,image/jpeg" multiple style="display:none;">
        </form>

        <div class="pg-status" id="pg_status">
            {{ $count }} of {{ $required }} photo{{ $required == 1 ? '' : 's' }} uploaded
        </div>

        <div class="pg-selfie">
            <p class="pg-selfie__title">
                <i class="fa fa-shield" aria-hidden="true"></i> Live verification selfie
                <span class="pg-required-tag">Required</span>
            </p>
            <p class="pg-selfie__desc">
                A live selfie is required so our team can confirm you're a real person before activating
                your account. Not stored publicly on your profile.
            </p>

            <div id="pg_selfie_idle" @if($hasSelfie ?? false) style="display:none;" @endif>
                <div class="pg-selfie__stage" id="pg_selfie_stage">
                    <video id="pg_selfie_video" autoplay playsinline muted></video>
                </div>
                <canvas id="pg_selfie_canvas" style="display:none;"></canvas>
                <div class="pg-selfie__actions">
                    <button type="button" class="pg-btn--outline" id="pg_selfie_start" onclick="pgStartCamera()">
                        <i class="fa fa-video-camera" aria-hidden="true"></i> Turn on camera
                    </button>
                    <button type="button" class="pg-btn--outline pg-btn--small" id="pg_selfie_capture" style="display:none;" onclick="pgCaptureSelfie()">
                        <i class="fa fa-circle" aria-hidden="true"></i> Capture
                    </button>
                </div>
            </div>

            <div id="pg_selfie_done" @if(!($hasSelfie ?? false)) style="display:none;" @endif>
                <div class="pg-selfie__done"><i class="fa fa-check-circle" aria-hidden="true"></i> Selfie captured</div>
                <div style="text-align:center;">
                    <button type="button" class="pg-btn--outline pg-btn--small" onclick="pgRetakeSelfie()">Retake</button>
                </div>
            </div>
        </div>

        <button type="button" class="pg-btn" id="pg_continue_btn" disabled onclick="pgContinue()" style="margin-top:20px;">
            <i class="fa fa-paper-plane" aria-hidden="true"></i> Submit for Review
        </button>

        <div class="pg-note">
            <i class="fa fa-shield" aria-hidden="true"></i>
            Fake, AI-generated, heavily edited or third-party photographs are not permitted and will be
            rejected during review. After submitting, you'll be logged out and can log back in once your
            account is approved.
        </div>
    </div>
</div>

<script>
(function() {
    var required = {{ (int) $required }};
    var currentCount = {{ (int) $count }};
    var hasSelfieCaptured = {{ ($hasSelfie ?? false) ? 'true' : 'false' }};
    var uploading = false;

    function updateContinueState() {
        document.getElementById('pg_continue_btn').disabled = currentCount < required || !hasSelfieCaptured;
    }

    function updateProgress(count) {
        currentCount = count;
        var spans = document.querySelectorAll('#pg_progress span');
        spans.forEach(function(span, i) {
            span.classList.toggle('filled', i < count);
        });
        document.getElementById('pg_status').textContent = count + ' of ' + required + ' photo' + (required === 1 ? '' : 's') + ' uploaded';
        updateContinueState();
    }

    function pollStatus() {
        fetch("{{ route('member.photos.required.status') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                hasSelfieCaptured = !!data.has_selfie;
                updateProgress(data.count || 0);
            })
            .catch(function() {});
    }

    window.pgContinue = function() {
        window.location.href = "{{ route('member.photos.required') }}";
    };

    document.getElementById('pg_images').addEventListener('change', function(e) {
        if (uploading || !e.target.files.length) return;
        uploading = true;

        var dropzone = document.getElementById('pg_dropzone');
        var oldText = dropzone.querySelector('span').textContent;
        dropzone.querySelector('span').textContent = 'Uploading…';

        var formData = new FormData(document.getElementById('pg_upload_form'));
        fetch("{{ url('member/profile/update/images/upload') }}", {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
            .then(function(r) { return r.json(); })
            .then(function(result) {
                uploading = false;
                dropzone.querySelector('span').textContent = oldText;
                document.getElementById('pg_images').value = '';
                if (result.message) {
                    var parts = result.message.split('|');
                    if (typeof showAlert === 'function') showAlert(parts[0], parts[1], parts[2]);
                }
                pollStatus();
            })
            .catch(function() {
                uploading = false;
                dropzone.querySelector('span').textContent = oldText;
                if (typeof showAlert === 'function') showAlert('danger', 'Upload failed. Please try again.');
            });
    });

    // Drag & drop convenience — the input itself still handles the actual upload.
    var dz = document.getElementById('pg_dropzone');
    ['dragenter', 'dragover'].forEach(function(evt) {
        dz.addEventListener(evt, function(e) { e.preventDefault(); dz.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
        dz.addEventListener(evt, function(e) { e.preventDefault(); dz.classList.remove('dragover'); });
    });
    dz.addEventListener('drop', function(e) {
        var files = e.dataTransfer.files;
        if (files && files.length) {
            document.getElementById('pg_images').files = files;
            document.getElementById('pg_images').dispatchEvent(new Event('change'));
        }
    });

    updateProgress(currentCount);

    // ---- Verification selfie (optional) ----
    var stream = null;

    window.pgStartCamera = function() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (typeof showAlert === 'function') showAlert('danger', 'Your browser does not support camera capture. Please try again from a different browser or device — this step is required to continue.');
            return;
        }
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
            .then(function(s) {
                stream = s;
                var video = document.getElementById('pg_selfie_video');
                video.srcObject = stream;
                document.getElementById('pg_selfie_start').style.display = 'none';
                document.getElementById('pg_selfie_capture').style.display = 'inline-flex';
            })
            .catch(function() {
                if (typeof showAlert === 'function') showAlert('danger', 'Could not access your camera. Please allow camera access in your browser settings — this step is required to continue.');
            });
    };

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(function(t) { t.stop(); });
            stream = null;
        }
    }

    window.pgCaptureSelfie = function() {
        var video = document.getElementById('pg_selfie_video');
        var canvas = document.getElementById('pg_selfie_canvas');
        canvas.width = video.videoWidth || 480;
        canvas.height = video.videoHeight || 360;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function(blob) {
            if (!blob) return;
            stopCamera();

            var formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('selfie', blob, 'selfie.jpg');

            fetch("{{ route('member.photos.selfie') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(function(r) { return r.json(); })
                .then(function(result) {
                    var parts = (result.message || '').split('|');
                    if (typeof showAlert === 'function' && parts[1]) showAlert(parts[0], parts[1]);
                    if (result.code === '200') {
                        document.getElementById('pg_selfie_idle').style.display = 'none';
                        document.getElementById('pg_selfie_done').style.display = 'block';
                        hasSelfieCaptured = true;
                        updateContinueState();
                    }
                })
                .catch(function() {
                    if (typeof showAlert === 'function') showAlert('danger', 'Could not save selfie. Please try again.');
                });
        }, 'image/jpeg', 0.9);
    };

    window.pgRetakeSelfie = function() {
        document.getElementById('pg_selfie_done').style.display = 'none';
        document.getElementById('pg_selfie_idle').style.display = 'block';
        document.getElementById('pg_selfie_capture').style.display = 'none';
        document.getElementById('pg_selfie_start').style.display = 'inline-flex';
    };

    window.addEventListener('beforeunload', stopCamera);
})();
</script>
@endsection
