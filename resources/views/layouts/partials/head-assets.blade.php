{{-- Shared <head> assets (meta tags, CSS, core JS libraries) used by every layout:
     layouts/master.blade.php (public site) and layouts/dashboard.blade.php (member area). --}}
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="urgentrishta.com is a Global matrimonial website.">
<meta name="keywords" content="matrimonial,urgentrishta.com">
<meta name="author" content="urgentrishta.com">
<meta name="revisit-after" content="2 day(s)">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="icon" type="image/png" href="/images/header_logo2.png?1">
<link rel="apple-touch-icon" href="/images/header_logo2.png?1">

<link rel="stylesheet" href="/css/app.css?2"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/green/pace-theme-minimal.min.css" type="text/css" />
<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" type="text/css" />
<!-- Plugins -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.1.3/hamburgers.min.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.9.0/css/lightgallery.min.css" type="text/css" />
<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" type="text/css" />
<!-- Global style (main) -->
<link id="stylesheet" type="text/css" href="/css/global-style-pink.css?3" rel="stylesheet" media="screen" />
<!-- Custom style - Remove if not necessary -->
<link type="text/css" href="/css/custom-style.css?v={{ filemtime(public_path('css/custom-style.css')) }}" rel="stylesheet" />
<link type="text/css" href="/css/new-theme.css?2" rel="stylesheet" />
<link type="text/css" href="/css/new-animate.min.css?2" rel="stylesheet" />
<link type="text/css" href="/css/ur-navbar.css?16" rel="stylesheet" />
<!-- SCRIPTS -->
<!-- Core -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
<!-- Plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-viewport-checker/1.8.8/jquery.viewportchecker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>
<!-- Light Gallery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.9.0/js/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lg-thumbnail/1.2.1/lg-thumbnail.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lg-video/1.3.0/lg-video.min.js"></script>


<!-- Google Analytics -->
<script async="" src="/js/analytics.js"></script>

<script>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '/js/analytics.js', 'ga');
    ga('create', " ", 'auto');
    ga('send', 'pageview');
</script>
<!-- End Google Analytics -->
<title>Urgent Rishta</title>
