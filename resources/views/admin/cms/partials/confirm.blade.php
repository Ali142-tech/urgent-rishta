{{--
    Delete confirmation for the CMS lists: a form with class "js-confirm" and data-title / data-text / data-button opens a proper
    confirmation box (SweetAlert, already on every admin page) and is only submitted when the admin confirms. Falls back to the
    browser's own confirm box if SweetAlert is not available.
--}}
<script>
    (function () {
        document.querySelectorAll('form.js-confirm').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed === '1') return;
                e.preventDefault();
                var title = form.dataset.title || 'Are you sure?', text = form.dataset.text || '', button = form.dataset.button || 'Yes, delete';
                if (typeof swal === 'function') {
                    swal({
                        title: title, text: text, icon: 'warning', dangerMode: true,
                        buttons: { cancel: { text: 'Cancel', visible: true }, confirm: { text: button, visible: true } }
                    }).then(function (ok) { if (ok) { form.dataset.confirmed = '1'; form.submit(); } });
                } else if (window.confirm(title + '\n\n' + text)) {
                    form.dataset.confirmed = '1'; form.submit();
                }
            });
        });
    })();
</script>
