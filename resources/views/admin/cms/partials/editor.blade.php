{{--
    Rich-text box (Quill) for the CMS: headings, bold / italic / underline, lists, quotes and links. Expects $name (the form
    field), $value (existing HTML) and $height (box height in px, default 360). On submit the text is copied into a hidden
    field; the server cleans it again (App\Services\HtmlSanitizer) before saving.
--}}
@once
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <style>
        .cms-editor { background: #fff; border-radius: 12px; }
        .cms-editor .ql-toolbar.ql-snow { border: 1px solid #E7E2D6; border-radius: 12px 12px 0 0; background: #FAF8F3; position: sticky; top: 0; z-index: 6; padding: 10px 12px; }
        .cms-editor .ql-container.ql-snow { border: 1px solid #E7E2D6; border-top: 0; border-radius: 0 0 12px 12px; font-size: 15.5px; font-family: inherit; height: auto; }
        .cms-editor .ql-editor { height: auto; overflow-y: visible; padding: 22px 26px; line-height: 1.75; color: #1C2321 !important; font-size: 15.5px; }
        .cms-editor .ql-editor p, .cms-editor .ql-editor li, .cms-editor .ql-editor strong, .cms-editor .ql-editor b { color: #1C2321; }
        .cms-editor .ql-editor h2, .cms-editor .ql-editor h3 { margin: 18px 0 8px; color: #123A2E; }
        .cms-editor .ql-editor ol, .cms-editor .ql-editor ul { padding-left: 1.4em; }
        .cms-editor .ql-editor.ql-blank::before { color: #9AA5A0; font-style: normal; left: 26px; }
    </style>
    @endpush
@endonce

<div class="cms-editor">
    <div id="ed_{{ $name }}" data-min="{{ $height ?? 360 }}"></div>
</div>
<textarea name="{{ $name }}" id="in_{{ $name }}" style="display:none;">{{ $value }}</textarea>

<script>
    (function () {
        // Quill itself is loaded once, on first use (this layout has no script stack to push it onto).
        if (!window.Quill && !document.getElementById('quill-js')) {
            var tag = document.createElement('script'); tag.id = 'quill-js'; tag.src = 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js'; document.head.appendChild(tag);
        }
        function boot() {
            if (!window.Quill) { return setTimeout(boot, 80); }
            var input = document.getElementById('in_{{ $name }}');
            var quill = new Quill('#ed_{{ $name }}', {
                theme: 'snow',
                modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['clean']] }
            });
            quill.root.style.minHeight = (document.getElementById('ed_{{ $name }}').dataset.min || 360) + 'px';
            quill.clipboard.dangerouslyPasteHTML(input.value || '');
            input.form.addEventListener('submit', function () { input.value = quill.root.innerHTML; });
        }
        boot();
    })();
</script>
