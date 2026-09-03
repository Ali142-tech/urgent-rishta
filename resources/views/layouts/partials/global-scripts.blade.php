{{-- Shared global JS helpers (toasts, sweetalert confirms, interest actions, AJAX section
     editing) used by every layout. Anything in member/* views that calls sendInterest(),
     showAlert(), etc. depends on this being included on the page. --}}
<script type="text/javascript">
    window.Laravel = {'token': '{{ csrf_token() }}', 'root': '{{ url('/') }}'};
    @auth
    window.Laravel.userId='{{ Auth::user()->id }}';
    @endauth

    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        var header = document.getElementById("myHeader");
        if (!header) return;
        var sticky = header.offsetTop;
        if (window.pageYOffset > sticky) {
            header.classList.remove("sticky-header");
        } else {
            header.classList.remove("sticky-header");
        }
    }

    function register_request() {
        swal({
            'title': 'Register for Full Access',
            'text': 'Thanks for checking out our website. Kindly register to gain full access to the profiles and for complete interactions.',
            'icon': 'info',
        });
    }

    function swalConfirm(title, message, onConfirm) {
        swal({
            'title': title,
            'text': message,
            'icon': 'warning',
            'buttons': {
                cancel: true,
                confirm: true
            }
        }).then((isConfirm) => {
            if (isConfirm && onConfirm)
                onConfirm();
        });
    }

    function swalAlert(type, title, message, callback) {
        swal(title, message, type).then( callback );
    }

    function showAlert(type, message, timeout, code) {
        var stack = document.getElementById('message_alert');
        if (!stack) return;

        // Normalize the many type spellings used across the codebase
        // ('success', 'danger', 'error', 'warning', 'info', ...) to one
        // of our four toast variants.
        var variant = 'info';
        if (/success/i.test(type)) variant = 'success';
        else if (/danger|error/i.test(type)) variant = 'danger';
        else if (/warning/i.test(type)) variant = 'warning';

        var icons = {
            success: 'fa-check-circle',
            danger: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        var duration = timeout ? timeout : 10000;

        var toast = document.createElement('div');
        toast.className = 'ur-toast ur-toast--' + variant;
        toast.innerHTML =
            '<i class="fa ' + icons[variant] + ' ur-toast__icon" aria-hidden="true"></i>' +
            '<div class="ur-toast__body">' + message + '</div>' +
            '<button type="button" class="ur-toast__close" aria-label="Dismiss">&times;</button>' +
            '<div class="ur-toast__bar" style="animation-duration:' + duration + 'ms"></div>';

        stack.appendChild(toast);
        // Force layout before adding the "show" class so the slide-in transition runs.
        void toast.offsetWidth;
        toast.classList.add('ur-toast--show');

        var dismissed = false;
        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            toast.classList.remove('ur-toast--show');
            toast.classList.add('ur-toast--hide');
            setTimeout(function() {
                toast.remove();
                if (code) eval(code);
            }, 300);
        }

        toast.querySelector('.ur-toast__close').addEventListener('click', dismiss);
        var autoTimer = setTimeout(dismiss, duration);

        // Pause the countdown while the user is reading it.
        toast.addEventListener('mouseenter', function() {
            clearTimeout(autoTimer);
            toast.querySelector('.ur-toast__bar').style.animationPlayState = 'paused';
        });
        toast.addEventListener('mouseleave', function() {
            autoTimer = setTimeout(dismiss, 1500);
            toast.querySelector('.ur-toast__bar').style.animationPlayState = 'running';
        });
    }

    // highlight link with icon
    function clickHighlight(title_id, title, icon_tag, new_icon, new_label, isHighlight, updateAnchor, updatedOnClickCode, highlightClass) {

        if (!highlightClass) highlightClass = "c-base-1";

        if (title_id && title) // update title if needed
            title_id.html(title);

        if (icon_tag) { //  element containing the fa icon

            if (new_icon) // update if new icon and update to new label
                icon_tag.html('<i class="fa fa-'+new_icon+'"></i> '+new_label+' ');
            else { // just reinsert existing icon with new label
                var iconTag = icon_tag.children("i")[0];
                icon_tag.html("");
                icon_tag.append(iconTag, ' ' + new_label + ' ');
            }

            if (isHighlight) { // should highlight link
                icon_tag.addClass(highlightClass);
                icon_tag.siblings("span").addClass(highlightClass);
            } else {
                icon_tag.removeClass(highlightClass);
                icon_tag.siblings("span").removeClass(highlightClass);
            }

            if (updateAnchor) { // if anchor link should be updated
                var anchor = icon_tag.prop("tagName")=="A" ? icon_tag : icon_tag.parent("a");
                if (updatedOnClickCode) // new click code
                    anchor.attr("onclick", updatedOnClickCode);
                else anchor.removeAttr("onclick"); // remove on click option so link does not work anymore
            }
        }
    }

    function loadSelect(url, querystr, selElem, selectedId) {
        $.ajax({
            type: "get",
            url: url + "/" + querystr,
            data : {
                '_token': "{{ csrf_token() }}"
            },
            cache: false,
            success: function(result) {
                if (result.code=="200") {
                    if (result.options) {
                        selElem.empty();
                        selElem.append($("<option />").val(this.dataid).text("Choose one..."));
                        $.each(result.options, function() {
                            selElem.append($("<option />").val(this.dataid).text(this.name));
                        });
                        selElem.val(selectedId);
                        if (selElem.selectpicker)
                            selElem.selectpicker('refresh');
                    }
                }
            }
        });
    }

    function sendInterest(elem) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        var elemId = elem.attr("id");
        var splitId = elemId.split("_");
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/interest/send')}}" + "/" + splitId[1],
            data : {
                '_token': "{{ csrf_token() }}"
            },
            cache: false,
            timeout: 20000,
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message.split("|");
                if (result.code=="200") {
                    $("#status_"+splitId[1]).removeClass("btn-green");
                    $("#status_"+splitId[1]).removeClass("btn-red");
                    $("#status_"+splitId[1]).addClass("btn-base-1");
                    $("#status_"+splitId[1]).html("PENDING");
                    clickHighlight(null, null,
                        $(elem.children("span")[0]), null, "Interest Expressed", true, true,  "return withdrawInterest($(this), 's');");
                    showAlert(message[0], message[1], 7000);
                } else showAlert('danger', message, 5000);
            },
            error: function() {
                // Request itself failed/hung (network blip, timeout, session
                // expired, etc.) — never leave the button stuck on "Processing..".
                elem.html(oldHtml);
                elem.prop('disabled', false);
                showAlert('danger', 'Could not reach the server. Please check your connection and try again.', 6000);
            }
        });
    }

    function acceptInterest(elem) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        var elemId = elem.attr("id");
        var splitId = elemId.split("_");
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/interest/accept')}}" + '/' + splitId[1],
            data : {
                '_token': "{{ csrf_token() }}"
            },
            cache: false,
            timeout: 20000,
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message.split("|");
                if (result.code=="200") {
                    $("#interest_"+splitId[1]+"_d").hide();
                    $("#interest_"+splitId[1]+"_a").hide();
                    $("#interest_"+splitId[1]+"_w").show();
                    $("#status_"+splitId[1]).removeClass("btn-base-1");
                    $("#status_"+splitId[1]).removeClass("btn-red");
                    $("#status_"+splitId[1]).addClass("btn-green");
                    $("#status_"+splitId[1]).html("GRANTED");
                    showAlert(message[0], message[1], 7000);
                } else showAlert('danger', message, 5000);
            },
            error: function() {
                elem.html(oldHtml);
                elem.prop('disabled', false);
                showAlert('danger', 'Could not reach the server. Please check your connection and try again.', 6000);
            }
        });
    }

    function declineInterest(elem) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        var elemId = elem.attr("id");
        var splitId = elemId.split("_");
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/interest/decline')}}" + '/' + splitId[1],
            data : {
                '_token': "{{ csrf_token() }}"
            },
            cache: false,
            timeout: 20000,
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message.split("|");
                if (result.code=="200") {
                    $("#interest_"+splitId[1]+"_d").hide();
                    $("#interest_"+splitId[1]+"_a").hide();
                    $("#interest_"+splitId[1]+"_w").show();
                    $("#status_"+splitId[1]).removeClass("btn-green");
                    $("#status_"+splitId[1]).removeClass("btn-base-1");
                    $("#status_"+splitId[1]).addClass("btn-red");
                    $("#status_"+splitId[1]).html("DECLINED");
                    showAlert(message[0], message[1], 7000);
                } else showAlert('danger', message, 5000);
            },
            error: function() {
                elem.html(oldHtml);
                elem.prop('disabled', false);
                showAlert('danger', 'Could not reach the server. Please check your connection and try again.', 6000);
            }
        });
    }

    function withdrawInterest(elem, who) {
        swalConfirm("Withdraw Interest", "Are you sure you want to withdraw your interest?", ()=>{
            var oldHtml = elem.html();
            elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            elem.prop('disabled', true);

            var elemId = elem.attr("id");
            var splitId = elemId.split("_");
            $.ajax({
                type: "post",
                url: "{{ url('member/profile/interest/withdraw')}}" + "/" + splitId[1] + "/" + who,
                data : {
                    '_token': "{{ csrf_token() }}"
                },
                cache: false,
                timeout: 20000,
                success: function(result) {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);

                    var message = result.message.split("|");
                    if (result.code=="200") {
                        $("#interest_"+splitId[1]+"_w").hide();
                        if (who!="s") {
                            $("#interest_"+splitId[1]+"_a").show();
                            $("#interest_"+splitId[1]+"_d").show();
                            $("#status_"+splitId[1]).removeClass("btn-green");
                            $("#status_"+splitId[1]).removeClass("btn-red");
                            $("#status_"+splitId[1]).addClass("btn-base-1");
                            $("#status_"+splitId[1]).html("PENDING");
                        } else {
                            clickHighlight(null, null,
                                $(elem.children("span")[0]), null, "Express Interest", false, true,  "return sendInterest($(this));");
                            $("#block_sent_"+splitId[1]).remove();
                        }
                        showAlert(message[0], message[1], 7000);
                    } else showAlert('danger', message, 5000);
                },
                error: function() {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);
                    showAlert('danger', 'Could not reach the server. Please check your connection and try again.', 6000);
                }
            });
        });
    }

    function updateFiltered(elem, action) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
        elem.prop('disabled', true);

        var elemId = elem.attr("id");
        var splitId = elemId.split("_");
        var newLabel = null;
        if (action=="add") {
             newLabel = splitId[0][0].toUpperCase()+splitId[0].slice(1)+(splitId[0].charAt(splitId[0].length-1)=="e"?"d":"ed"); // append ed if last char not e otherwise just d
        } else {
            newLabel = splitId[0][0].toUpperCase()+splitId[0].slice(1);
        }
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/filtered')}}" + "/" + action + "/" + splitId[0] + "/" + splitId[1],
            data : {
                '_token': "{{ csrf_token() }}"
            },
            cache: false,
            success: function(result) {
                elem.html(oldHtml);
                elem.prop('disabled', false);

                var message = result.message.split("|");
                if (result.code=="200") {
                    clickHighlight(null, null,
                        $(elem.children("span")[0]), null, newLabel, (action=="add"), true, "return updateFiltered($(this), '"+(action=="add"?"remove":"add")+"');");
                    showAlert(message[0], message[1], 7000);
                } else showAlert('danger', message, 5000);
            }
        });
    }

    function showLightGallery(elem) {
        elem.lightGallery({
            cssEasing: 'cubic-bezier(0.680, -0.550, 0.265, 1.550)',
            dynamic: true,
            html: true,
            mobileSrc: true,
            showThumbByDefault: true,
            dynamicEl: @if(!empty($profile)) {!! $profile->getLightGalleryImages() !!} @else '' @endif
        });
    }

    function deleteAccount(elem) {
        swalConfirm("Delete Account?", "Are you sure you want to delete your account? You will not be able to revert this!", () => {
            // Intentionally not wired to member/profile/account/terminate yet —
            // left as a confirm-only prompt until real account deletion is implemented.
        });
    }

    // "Manage Pictures" page actions (Delete / Set as Display Picture) —
    // member/pictures.blade.php. Global since the display-pic change also needs
    // to refresh the topbar avatar, which lives outside that page's own content.
    function deleteImage(elem, dataid) {
        swalConfirm("Delete Image?", "Are you sure you want to delete this image? You will not be able to revert this!", () => {
            updateImage(elem, 'd', dataid);
        });
    }

    function updateImage(elem, action, dataid) {
        var oldHtml = elem.html();
        elem.html("<i class='fa fa-refresh fa-spin'></i>");
        elem.prop('disabled', true);
        $.ajax({
            type: "post",
            url: "{{ url('member/profile/images/update') }}" + "/" + action + "/" + dataid,
            data: {
                '_token': '{{ csrf_token() }}',
            },
            success: function(result) {
                var message = result.message.split("|");
                if (result.code == '200') {
                    showAlert(message[0], message[1], 3000);
                    setTimeout(function() { location.reload(); }, 700);
                } else {
                    elem.html(oldHtml);
                    elem.prop('disabled', false);
                    showAlert('danger', message, 5000);
                }
            }
        });
    }

    function renderPage(dataUrl, method, formFields, elem) {
        if (!elem) {
            showAlert('danger', "Rendering element is null. Cannot proceed.", 5000);
            return;
        }
        $.ajax({
            url: dataUrl,
            type: method,
            data: formFields?formFields:'',
            success: function(result){
                elem.html("<i class='fa fa-refresh fa-spin'></i> Retrieving some awesome records for you..");
                var message = result.message;
                if (result.code == '200') {
                    if (message)
                        showAlert('success', message, 3000);
                    if (result.html) {
                        elem.html(result.html);
                        $("body, html, .body-wrap").animate({ scrollTop: 0 }, "slow");
                    }
                } else {
                    if (message)
                        showAlert('danger', message, 5000);
                }
            }
        });
    }

    $(document).ready(function() {

        @if($errors->any() && !(request()->is('login') || request()->is('login/*') || request()->is('register') || request()->is('register/*')))
        showAlert("error", "{!! implode('', $errors->all('<div>:message</div>')) !!}")
        @endif

        @if(session('message') && !(request()->is('login') || request()->is('login/*') || request()->is('register') || request()->is('register/*')))
        var message = "{!! session('message') !!}".split("|");
        showAlert(message[0], message[1], message[2]);
        @endif

        $(".selectpicker").select2();
    });

</script>
