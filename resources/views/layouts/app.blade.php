@php
    use App\Http\Controllers\GlobalController;
    App::setLocale('de');
    setlocale(LC_ALL, 'deu_deu.1252');
    // GlobalController::SetDomain();

    use App\Http\Controllers\DarkModeController;
    if (!session_id()) {
        session_start();
    }
    $subdomain = SD(); // z.B. "foo", "bar"
    $pagen = SD("pn");

    $favicon = "/images/_{$subdomain}/web/alogo.png";
    $ahost = $_SERVER['HTTP_HOST'];
    $_SESSION['comment_ids'] = [];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
    window.subdomain = "{{ $subdomain }}";
    window.subdomain_alt = "{{ $sd_alt }}";
    window.pagename = "{{ $pagen }}";
    window.ahost = "{{ $ahost }}";
    window.app_name = "{{ $pagen }}"
</script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    <?php
    // Subdomain extrahieren (erster Teil des Hosts)
    $subdomain = SD();
    ?>
    <link rel="icon" href="_<?php echo $subdomain; ?>/web/alogo.png" type="image/png">
    <link type='text/css' href='/css/app.css' rel='stylesheet'>
    <script src='/js/app.js' type='module'></script>
    <link rel="stylesheet" href="/photoswipe/photoswipe.css">
    <link rel="stylesheet" href="/css/all.min.css">

    <!-- java scripts for datepicker-->
    <!-- Moment.js (abhängig vom datetimepicker) -->
    <script src="/js/moment.min.js"></script>

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css') }}" />

    <!-- Datetimepicker JS -->
    <link rel="stylesheet" type="text/css" href="/css/shariff.complete.css">
    <link rel="stylesheet" type="text/css" href="/css/tailw/{{$subdomain}}.css">


    <link type='text/css' href='{{ asset('css/bootstrap.css') }}' rel='stylesheet'>
    <link type='text/css' href='{{ asset('css/user.css') }}' rel='stylesheet'>
    {{-- <script src="/js/jquery.min.js"></script> --}}
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/shariff.complete.js') }}"></script>

    {{-- <script async src="/js/shariff.complete.js"></script> --}}
    {{-- <script src="/js/bootstrap.min.js"></script> --}}
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/users.js') }}"></script>


    {{-- <script src="/js/users.js"></script> --}}


</head>
<!-- layouts/app -->
@php

    if (empty($_SESSION['dm'])) {
        DarkModeController::setDarkMode('dark');
        $_SESSION['dm'] = 'dark';
    }
    $dm = $_SESSION['dm'] ?? 'dark';

@endphp

<body class="{{ $dm }}">
    <input type='hidden' id="csrf-token" value="{{ csrf_token() }}">
    <div id="app">
        @php
            $subdomain = SD();
        @endphp

        <!-- Navigation basierend auf der Subdomain laden -->
        @if ($subdomain == 'hm')
            @include('layouts.hm.hm_navigation') <!-- Spezifische Navigation für Subdomain "hm" -->
        @else
            @include('layouts.navigation') <!-- Standardnavigation -->
        @endif
        <img src="{{ route('countpixel', [
            'url' => urlencode(request()->fullUrl()),
            'route' => Route::currentRouteName() ?? 'unknown'
        ]) }}"
            alt="" width="1" height="1" style="display:none;">

        <main class="py-4 w10">
            @yield('content')
        </main>
        @if ($subdomain == 'hm')
            @include('layouts.hm.hm_subfooter') <!-- Spezifische Navigation für Subdomain "hm" -->
        @else
            @include('layouts.subfooter')
        @endif

    </div>
    <script>
        if (document.getElementById('uploadForm')) {
            // document.addEventListener("DOMContentLoaded", function() {
            // Überprüfen, ob eine Success Message in Local Storage existiert
            let successMessage = localStorage.getItem('successMessage');

            if (successMessage) {
                // Anzeigen der Nachricht
                //alert(successMessage); // oder eine andere Methode, z.B. toastr

                // Nach der Anzeige die Nachricht aus Local Storage löschen
                localStorage.removeItem('successMessage');
            }


            document.addEventListener("DOMContentLoaded", function() {
                // Funktion, um das Twitter-Icon zu ersetzen
                function replaceTwitterIcon() {
                    // Suche alle Twitter-Buttons
                    const twitterButtons = document.querySelectorAll(".twitter");

                    twitterButtons.forEach(button => {
                        // Entferne das vorhandene Icon (SVG oder Bild)
                        const icon = button.querySelector("img, svg");
                        if (icon) {
                            alert("jaaa");
                            icon.remove();
                        }

                        // Neues X-Icon mit Font Awesome hinzufügen
                        const xIcon = document.createElement("i");
                        xIcon.classList.add("fab",
                            "fa-x-twitter"); // Verwende die gewünschte Klasse von Font Awesome
                        xIcon.style.fontSize = "1em"; // Stil anpassen, falls gewünscht
                        button.prepend(xIcon); // Icon am Anfang des Buttons hinzufügen
                    });
                }

                // Überprüfe periodisch, ob die Shariff-Buttons erstellt wurden
                const checkShariffLoaded = setInterval(function() {
                    if (document.querySelector(".shariff-button-twitter")) {
                        clearInterval(checkShariffLoaded); // Überwachung stoppen
                        replaceTwitterIcon(); // Icons ersetzen
                    }
                }, 300); // Alle 300ms prüfen
            });




        }
    </script>
    <script>
        var $y = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden
    </script>
    <a href="#top" class="back-to-top">
        <i class="fas fa-scroll-up"></i>
    </a>
    <script language='javascript'>
        $y(document).ready(function() {
            // Der Button wird ausgeblendet
            $y(".back-to-top").hide();

            // Funktion für das Scroll-Verhalten
            $y(window).scroll(function() {
                if ($y(this).scrollTop() > 100) {
                    $y(".back-to-top").fadeIn();
                } else {
                    $y(".back-to-top").fadeOut();
                }
            });

            $y(".back-to-top").click(function() {
                $y("body, html").animate({
                    scrollTop: 0
                }, 900, "swing");
                return false;
            });
        });
    </script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

</body>

</html>
