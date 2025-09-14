@php
    if (!CheckZrights('AdminPanel') && !Auth::check()) {
        return redirect('login')->send();
    } elseif (!CheckZRights('AdminPanel') && Request::path() != 'no-rights') {
        return redirect('/no-rights')->send();
        // echo Request::path();
    }
    $subdomain = SD(); // z.B. "foo", "bar"
    $pagen = SD("pn");
    $favicon = "/images/_{$subdomain}/web/alogo.png";
    $ahost = $_SERVER['HTTP_HOST'];
@endphp

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Bereich</title>
    <link rel="icon" href="/favicon_{{ $subdomain }}.png" type="image/png">
    <link type='text/css' href='/css/app.css' rel='stylesheet'>
    <script src='/js/app.js' type='module'></script>
    <link type='text/css' href='/css/bootstrap.css' rel='stylesheet'>
    <link type='text/css' href='/css/user.css' rel='stylesheet'>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/jquery-ui.css">
    <script>
        window.subdomain = "{{ $subdomain }}";
        window.subdomain_alt = "{{ $sd_alt }}";
        window.pagename = "{{ $pagen }}";
        window.ahost = "{{ $ahost }}";
        window.sd = "{{ SD() }}";
        </script>

    <!-- jQuery -->
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>

    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="/css/jquery-ui.css">

    <!-- jQuery UI DateTimePicker Addon CSS -->
    <link rel="stylesheet"
        href="/css/jquery-ui-timepicker-addon.min.css">
    <script src="/js/Sortable.min.js"></script>

    <!-- jQuery UI DateTimePicker Addon JavaScript -->
    <script src="/js/jquery-ui-timepicker-addon.min.js">
    </script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/users.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<!-- ab/admin -->

<body class="{{ session('dark_mode', 'dark') }}">
    <input type='hidden' name='csrf' id='csrf' value='{{ csrf_token() }}'>
    <!-- Topnav -->
    <div id='app'>
        @include('layouts.navigation')

        <div class="container-fluid">
            <div class="row">
                <!-- Leftnav -->
                <nav class="col-md-2 col-lg-2 d-md-block bg-{{ $dm }} sidebar t200">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/admin/dashboard">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tables.index', 'admin_table') }}">
                                    Admintabelle
                                </a>
                            </li>
                            <li class="nav-item">
                                <div class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="tablesDropdown"
                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Verwalte Tabellen
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="tablesDropdown">
                                        <!-- Dynamisch generierte Tabellenlinks -->
                                        @foreach ($tables as $field => $tablez)
                                            @php
                                                // var_dump($tablez);
                                                // exit();
                                            @endphp
                                            @if (CheckRights(Auth::id(), $tablez->name, 'view'))
                                                <li><a class="dropdown-item"
                                                        href="{{ route('tables.index', ['table' => $tablez->name]) }}">{{ ucf($tablez->name) }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.rightsTable', compact('tables')) }}">
                                    Rechteverwaltung
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-12">




                    @yield('content')
                </main>

            </div>
        </div>
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


            // document.addEventListener("DOMContentLoaded", function() {


            document.getElementById('uploadForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Verhindert das Standard-Formular-Submit
                $j('#pb').show(); // Zeigt das Fortschrittsbalken-Element an
                let form = document.getElementById('uploadForm');
                let formData = new FormData(form);
                let xhr = new XMLHttpRequest();

                xhr.open('POST', form.action, true);

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        let percentComplete = (e.loaded / e.total) * 100;
                        let progressBar = document.getElementById('progressBar');
                        progressBar.style.width = percentComplete + '%';
                        progressBar.setAttribute('aria-valuenow', percentComplete);
                        progressBar.textContent = Math.round(percentComplete) + '%';
                    }
                });

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            // Erfolgreicher Upload
                            localStorage.setItem('successMessage',
                                'Der Beitrag wurde erfolgreich gesichert!');
                            // window.location.href = "{{ route('profile.update', 'success=Profil erfolgreich bearbeitet') }}";
                            $j('#uploadForm').submit();
                        } else {
                            // Fehlerhandling
                            alert('Es ist ein Fehler beim Hochladen aufgetreten.');
                        }
                    }
                };

                xhr.send(formData);
            });
        }
    </script>
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script>
        var $j = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden
    </script>
    <script>
        $j(document).ready(function() {
            let lastTap = 0;

            // Funktion, die aufgerufen wird, um die Zelle editierbar zu machen
            function makeCellEditable($cell) {
                var originalValue = $cell.text(); // Speichere den ursprünglichen Wert
                var field = $cell.data('field'); // Hole den Feldnamen
                var id = $cell.closest('tr').data('id'); // Hole die ID der Zeile
                var table2 = $j('#editableTable').data('table');

                // Ersetze den Zelleninhalt mit einem Textarea
                $cell.html("<textarea rows='4' cols='45'>" + originalValue + '</textarea>');
                var $textarea = $cell.find('textarea');

                // Fokussiere das Textarea und wähle den gesamten Text
                $textarea.focus().select();

                // Wenn der Benutzer die Eingabetaste drückt oder das Textarea verlässt
                $textarea.on('blur keydown', function(e) {
                    if (e.type === 'blur' || (e.key === 'Enter' && !e.shiftKey)) {
                        e.preventDefault(); // Verhindere den Zeilenumbruch bei "Enter"

                        var newValue = $textarea.val();

                        // Wenn sich der Wert geändert hat, führe AJAX aus
                        if (newValue !== originalValue) {
                            $j.ajax({
                                url: '/update-table', // Die Route zum Speichern der Daten
                                method: 'POST',
                                data: {
                                    id: id,
                                    table: table2,
                                    field: field,
                                    value: newValue,
                                    _token: '{{ csrf_token() }}' // CSRF-Token für Laravel
                                },
                                success: function(response) {
                                    $cell.text(
                                        newValue); // Aktualisiere die Zelle mit dem neuen Wert
                                    console.info('Wert erfolgreich gespeichert!');
                                },
                                error: function() {
                                    $cell.text(
                                        originalValue
                                    ); // Setze den alten Wert zurück bei Fehler
                                    console.info('Fehler beim Speichern des Wertes.');
                                }
                            });
                        } else {
                            $cell.text(
                                originalValue); // Setze den ursprünglichen Wert zurück, wenn keine Änderung
                        }
                    }
                });
            }

            // Event-Handler für Desktop-Doppelklick
            $j('.editable').on('dblclick', function() {
                makeCellEditable($j(this));
            });

            // Event-Handler für Doppeltipp auf Smartphones
            $j('.editable').on('touchstart', function(e) {
                var currentTime = new Date().getTime();
                var tapLength = currentTime - lastTap;

                if (tapLength < 500 && tapLength > 0) { // Doppeltipp innerhalb von 500ms
                    e.preventDefault();
                    makeCellEditable($j(this));
                }

                lastTap = currentTime;
            });
        });
        document.addEventListener('DOMContentLoaded', function() {

            const csrfToken = document.getElementById("csrf").value;

            var el = document.querySelector('#editableTable tbody');
            if (typeof el === 'undefined') {
                return;
            }
            // Initialize SortableJS
            var sortable = new Sortable(el, {
                onEnd: function(evt) {
                    // Get the new order of rows
                    var sortedIDs = [];
                    document.querySelectorAll('#editableTable tbody tr').forEach(function(row) {
                        sortedIDs.push(row.getAttribute('data-id'));
                    });

                    // The ID of the moved item
                    var movedItemId = evt.item.getAttribute('data-id'); // Get the ID of the moved row
                    // var id = movedItemId;
                    var newPositions = {};
                    sortedIDs.forEach(function(id, index) {
                        newPositions[id] = index; // Set the new position based on the index
                    });
                    var t = document.getElementById('editableTable');
                    var table2 = t.getAttribute('data-table');
                    // Send the new order to the server via AJAX
                    fetch('/admin_table/update-positions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken // Use the token from the meta tag
                            },
                            body: JSON.stringify({
                                sorted_ids: sortedIDs,
                                _token: csrfToken,
                                table: table2,
                                id: movedItemId, // Pass the moved item's ID
                                new_positions: newPositions,
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok ' + response
                                    .statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            // console.log(data.message);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating positions.');
                        });
                }
            });
        });
    </script>


    <script>
        // SELECT FIELD DBLCLICK
        $j(document).ready(function() {
            var lastTap = 0; // Für Double-Tap auf Mobilgeräten
            // Event-Listener für Doppelklick (nur für Desktop)
            $j(".selectable").on("dblclick", function() {
                var td = $j(this); // Das TD-Element
                var span = td.find("span"); // Der Text im TD-Element
                var select = td.find("select"); // Das Select-Feld im TD-Element
                var id = td.closest('tr').data('id');
                var table2 = $j('#editableTable').data('table');
                var field = span.data('field');
                // Zeige das Select-Feld und verstecke den Text
                span.hide();
                select.show().focus();

                // Wenn der Benutzer eine Auswahl trifft
                select.change(function() {
                    var newValue = $j(this).val(); // Neuer Wert des Selects
                    // AJAX-Anfrage zum Speichern der neuen Auswahl
                    $j.ajax({
                        url: '{{ route('table.updatez') }}', // Route zum Speichern
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF Token für Sicherheit in Laravel
                            id: id,
                            value: newValue,
                            field: field,
                            table: table2,
                        },
                        success: function(response) {
                            // Bei Erfolg: Text aktualisieren und Select-Feld verstecken
                            if (response.status === 'success') {
                                span.text(select.find('option:selected').text());
                                span.show();
                                select.hide();
                            } else {
                                alert("Fehler: " + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Fehlerbehandlung
                            alert(error);
                            span.show();
                            select.hide();
                        }
                    });
                });

                // Wenn das Select-Feld den Fokus verliert, verstecke es wieder
                select.blur(function() {
                    span.show();
                    select.hide();
                });
            });
            // SELECT FOR TOUCHEND
            // Event-Listener für Double-Tap (nur für Mobilgeräte)
            $j(".selectable").on("touchend", function(event) {
                var currentTime = new Date().getTime();
                var tapLength = currentTime - lastTap;

                if (tapLength < 300 && tapLength > 0) { // Zeitfenster für Double-Tap
                    var td = $j(this); // Das TD-Element
                    var span = td.find("span"); // Der Text im TD-Element
                    var select = td.find("select"); // Das Select-Feld im TD-Element
                    var id = td.closest('tr').data('id');
                    var table2 = $j('#editableTable').data('table');

                    // Zeige das Select-Feld und verstecke den Text
                    span.hide();
                    select.show().focus();

                    // Wenn der Benutzer eine Auswahl trifft
                    select.change(function() {
                        var newValue = $j(this).val(); // Neuer Wert des Selects

                        // AJAX-Anfrage zum Speichern der neuen Auswahl
                        $j.ajax({
                            url: '{{ route('table.updatez') }}', // Route zum Speichern
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}', // CSRF Token für Sicherheit in Laravel
                                id: id,
                                value: newValue,
                                field: "author_id",
                                table: table2,
                            },
                            success: function(response) {
                                // Bei Erfolg: Text aktualisieren und Select-Feld verstecken
                                if (response.status === 'success') {
                                    span.text(select.find('option:selected').text());
                                    span.show();
                                    select.hide();
                                } else {
                                    alert("Fehler: " + response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                // Fehlerbehandlung
                                alert(error);
                                span.show();
                                select.hide();
                            }
                        });
                    });

                    // Wenn das Select-Feld den Fokus verliert, verstecke es wieder
                    select.blur(function() {
                        span.show();
                        select.hide();
                    });
                }

                lastTap = currentTime; // Aktualisiere den letzten Tap-Zeitpunkt für Double-Tap-Erkennung
            });
        });
    </script>


    <script>
        // SORTABLE POSITION
        $j(document).ready(function() {
            var tableBody = document.querySelector("#editableTable tbody");

            new Sortable(tableBody, {
                animation: 150,
                handle: '.handle', // Nur Elemente mit der Klasse "handle" können gezogen werden
                onEnd: function(evt) {
                    var order = [];
                    $j('#editableTable tbody tr').each(function(index, element) {
                        order.push($j(element).data('id'));
                    });

                    // Hier den data-Attribut-Wert von der Tabelle holen
                    var table2 = $j('#editableTable').data('table');
                    var csrfToken = $j('meta[name="csrf-token"]').attr('content');

                    $j.ajax({
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken // CSRF-Token im Header senden
                        },
                        url: "{{ route('tables.updateOrder') }}",
                        method: "POST",
                        data: JSON.stringify({
                            table: table2,
                            order: order,
                        }),
                        success: function(response) {
                            // console.log(
                            //     "Sortierreihenfolge wurde erfolgreich gespeichert.");
                        },
                        error: function(xhr) {
                            console.error("Fehler bei der Anfrage:", xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
    <script>
        var $y = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden
    </script>

    <script language='javascript'>
        // Scroll UP BUTTON
        $y(document).ready(function() {

            // Der Button wird mit JavaScript erzeugt und vor dem Ende des body eingebunden.
            var back_to_top_button = [
                    '<a href="#top" class="back-to-top"><i class="fas fa-scroll-up"></i></a>'
                ]
                .join("");
            $y("body").append(back_to_top_button)

            // Der Button wird ausgeblendet
            $y(".back-to-top").hide();

            // Funktion für das Scroll-Verhalten
            $y(function() {
                $y(window).scroll(function() {
                    if ($y(this).scrollTop() > 100) { // Wenn 100 Pixel gescrolled wurde
                        $y('.back-to-top').fadeIn();

                    } else {
                        $y('.back-to-top').fadeOut();
                    }
                });

                $y('.back-to-top').click(function() { // Klick auf den Button
                    $y('body,html').animate({
                        scrollTop: 0
                    }, 900, 'swing');
                    return false;
                });
            });

        });
    </script>
</body>

</html>
